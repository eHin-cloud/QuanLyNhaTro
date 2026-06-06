<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    /**
     * Khởi tạo và yêu cầu đăng nhập đối với tất cả hành động
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước.');
            }
            
            // Chỉ cho phép Landlord (chủ trọ) quản lý phòng trọ
            $user = Auth::user();
            if (!$user->isLandlord()) {
                abort(403, 'Bạn không có quyền truy cập chức năng này.');
            }
            
            return $next($request);
        });
    }

    /**
     * Danh sách phòng trọ có phân trang
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // Xử lý tham số page không hợp lệ hoặc quá lớn
        $page = $request->query('page');
        if ($page !== null && (!is_numeric($page) || intval($page) <= 0)) {
            return redirect()->route('admin.rooms.index', ['page' => 1]);
        }

        $perPage = 5;
        $rooms = Room::with('building')
            ->where('tenant_id', $tenantId)
            ->orderBy('room_number')
            ->paginate($perPage);

        // Nếu page vượt quá tổng số trang, tự động quay lại page 1
        if ($rooms->currentPage() > $rooms->lastPage() && $rooms->lastPage() > 0) {
            return redirect()->route('admin.rooms.index', ['page' => 1]);
        }

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Màn hình tạo mới phòng trọ
     */
    public function create()
    {
        $user = Auth::user();
        $buildings = Building::where('tenant_id', $user->tenant_id)->get();
        return view('admin.rooms.create', compact('buildings'));
    }

    /**
     * Lưu thông tin phòng trọ mới vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // 1. Chuyển đổi số Full-width thành Half-width chuẩn & trim khoảng trắng (bao gồm khoảng trắng Nhật/Trung)
        $input = $request->all();
        
        if (isset($input['room_number'])) {
            $input['room_number'] = $this->cleanWhitespace($input['room_number']);
        }
        if (isset($input['floor'])) {
            $input['floor'] = $this->toHalfWidth($this->cleanWhitespace($input['floor']));
        }
        if (isset($input['price'])) {
            $input['price'] = $this->toHalfWidth($this->cleanWhitespace($input['price']));
        }
        if (isset($input['area'])) {
            $input['area'] = $this->toHalfWidth($this->cleanWhitespace($input['area']));
        }
        if (isset($input['description'])) {
            // Sanitize / Escape HTML để phòng chống tấn công XSS và tránh vỡ giao diện
            $input['description'] = strip_tags($input['description']);
        }

        // Cập nhật lại request data sau khi đã chuẩn hóa
        $request->merge($input);

        // 2. Validate dữ liệu Backend nghiêm ngặt
        $request->validate([
            'building_id' => [
                'required',
                'integer',
                // Đảm bảo building_id tồn tại và phải thuộc quyền sở hữu của tenant hiện tại (Chống F12 sửa Option)
                function ($attribute, $value, $fail) use ($tenantId) {
                    $building = Building::where('id', $value)->where('tenant_id', $tenantId)->first();
                    if (!$building) {
                        $fail('Tòa nhà được chọn không hợp lệ hoặc không thuộc quyền quản lý của bạn.');
                    }
                }
            ],
            'room_number' => [
                'required',
                'string',
                'max:50',
                // Đảm bảo số phòng là duy nhất trong tòa nhà được chọn
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Room::where('building_id', $request->building_id)
                        ->where('room_number', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Số phòng này đã tồn tại trong tòa nhà được chọn.');
                    }
                }
            ],
            'floor' => 'required|integer|min:1|max:100',
            'status' => 'required|in:empty,occupied,overdue',
            'price' => 'required|integer|min:0|max:1000000000',
            'area' => 'required|integer|min:1|max:1000',
            'amenities' => 'nullable|array',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048' // Chặn file lạ (PDF, exe...), dung lượng max 2MB
        ], [
            'room_number.required' => 'Vui lòng nhập số phòng.',
            'floor.required' => 'Vui lòng nhập tầng.',
            'floor.integer' => 'Tầng phải là số nguyên hợp lệ.',
            'price.required' => 'Vui lòng nhập giá phòng.',
            'price.integer' => 'Giá phòng phải là số nguyên hợp lệ.',
            'area.required' => 'Vui lòng nhập diện tích.',
            'area.integer' => 'Diện tích phải là số nguyên hợp lệ.',
            'image.image' => 'Chỉ chấp nhận định dạng hình ảnh (jpg, png, webp).',
            'image.mimes' => 'Hình ảnh phải có đuôi mở rộng: jpeg, jpg, png, webp.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 2MB.'
        ]);

        // 3. Xử lý lưu ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('rooms', 'public');
        }

        // 4. Tạo phòng trọ mới
        Room::create([
            'building_id' => $request->building_id,
            'tenant_id' => $tenantId,
            'room_number' => $request->room_number,
            'floor' => $request->floor,
            'status' => $request->status,
            'price' => $request->price,
            'area' => $request->area,
            'amenities' => $request->amenities ?? [],
            'description' => $request->description,
            'image' => $imagePath,
            'version' => 1 // Khởi tạo phiên bản đầu tiên cho Optimistic Locking
        ]);

        return redirect()->route('admin.rooms.index')->with('success', 'Thêm phòng trọ mới thành công.');
    }

    /**
     * Màn hình chỉnh sửa thông tin phòng trọ
     */
    public function edit(Request $request, $id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // 1. Kiểm tra ID định dạng số
        if (!is_numeric($id) || intval($id) <= 0 || intval($id) > 9999999999) {
            abort(404, 'Phòng trọ không tồn tại.');
        }

        // 2. Tìm phòng trọ và kiểm tra quyền sở hữu (BOLA protection)
        $room = Room::where('id', $id)->where('tenant_id', $tenantId)->first();
        if (!$room) {
            abort(404, 'Phòng trọ không tồn tại hoặc bạn không có quyền sửa.');
        }

        $buildings = Building::where('tenant_id', $tenantId)->get();
        return view('admin.rooms.edit', compact('room', 'buildings'));
    }

    /**
     * Cập nhật thông tin phòng trọ (Xử lý đồng thời Optimistic Locking)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // 1. Kiểm tra ID định dạng số
        if (!is_numeric($id) || intval($id) <= 0) {
            abort(404, 'Phòng trọ không tồn tại.');
        }

        // 2. Tìm phòng trọ và kiểm tra quyền sở hữu (BOLA)
        $room = Room::where('id', $id)->where('tenant_id', $tenantId)->first();
        if (!$room) {
            abort(404, 'Phòng trọ không tồn tại hoặc bạn không có quyền sửa.');
        }

        // 3. XỬ LÝ ĐỒNG THỜI & TRÙNG LẶP (Optimistic Locking)
        // Nếu version gửi lên khác version trong database, tức là đã có tab khác hoặc người khác cập nhật trước
        $submittedVersion = $request->input('version');
        if ($submittedVersion != $room->version) {
            return back()->withInput()->with('error', 'Dữ liệu phòng trọ này đã được cập nhật bởi một phiên làm việc khác. Vui lòng tải lại trang trước khi lưu.');
        }

        // 4. Chuẩn hóa dữ liệu đầu vào
        $input = $request->all();
        if (isset($input['room_number'])) {
            $input['room_number'] = $this->cleanWhitespace($input['room_number']);
        }
        if (isset($input['floor'])) {
            $input['floor'] = $this->toHalfWidth($this->cleanWhitespace($input['floor']));
        }
        if (isset($input['price'])) {
            $input['price'] = $this->toHalfWidth($this->cleanWhitespace($input['price']));
        }
        if (isset($input['area'])) {
            $input['area'] = $this->toHalfWidth($this->cleanWhitespace($input['area']));
        }
        if (isset($input['description'])) {
            $input['description'] = strip_tags($input['description']);
        }

        $request->merge($input);

        // 5. Validate dữ liệu Backend
        $request->validate([
            'building_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($tenantId) {
                    $building = Building::where('id', $value)->where('tenant_id', $tenantId)->first();
                    if (!$building) {
                        $fail('Tòa nhà được chọn không hợp lệ.');
                    }
                }
            ],
            'room_number' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request, $id) {
                    $exists = Room::where('building_id', $request->building_id)
                        ->where('room_number', $value)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('Số phòng này đã tồn tại trong tòa nhà được chọn.');
                    }
                }
            ],
            'floor' => 'required|integer|min:1|max:100',
            'status' => 'required|in:empty,occupied,overdue',
            'price' => 'required|integer|min:0|max:1000000000',
            'area' => 'required|integer|min:1|max:1000',
            'amenities' => 'nullable|array',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048'
        ], [
            'room_number.required' => 'Vui lòng nhập số phòng.',
            'floor.required' => 'Vui lòng nhập tầng.',
            'price.required' => 'Vui lòng nhập giá phòng.',
            'area.required' => 'Vui lòng nhập diện tích.',
            'image.image' => 'Chỉ chấp nhận định dạng hình ảnh (jpg, png, webp).',
            'image.mimes' => 'Hình ảnh phải có đuôi mở rộng: jpeg, jpg, png, webp.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 2MB.'
        ]);

        // 6. Xử lý ảnh: Giữ nguyên ảnh cũ nếu không chọn ảnh mới (Update Persistence)
        $imagePath = $room->image;
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có để tiết kiệm dung lượng
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $imagePath = $request->file('image')->store('rooms', 'public');
        }

        // 7. Cập nhật dữ liệu phòng trọ và tăng version lên 1
        $room->update([
            'building_id' => $request->building_id,
            'room_number' => $request->room_number,
            'floor' => $request->floor,
            'status' => $request->status,
            'price' => $request->price,
            'area' => $request->area,
            'amenities' => $request->amenities ?? [],
            'description' => $request->description,
            'image' => $imagePath,
            'version' => $room->version + 1 // Tăng version để phòng chống cập nhật đè (Optimistic Locking)
        ]);

        return redirect()->route('admin.rooms.index')->with('success', 'Cập nhật phòng trọ thành công.');
    }

    /**
     * Xóa phòng trọ (Chống xóa trực tiếp qua URL GET, chống xóa trùng)
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // 1. Chống xóa trực tiếp qua URL (Chỉ chấp nhận DELETE hoặc POST, không được dùng GET)
        if (!$request->isMethod('delete') && !$request->isMethod('post')) {
            abort(405, 'Phương thức không hợp lệ. Hành động xóa chỉ được thực thi thông qua yêu cầu POST/DELETE an toàn.');
        }

        // 2. Kiểm tra ID định dạng số
        if (!is_numeric($id) || intval($id) <= 0) {
            abort(404, 'Phòng trọ không hợp lệ.');
        }

        // 3. Xử lý xóa trùng (Concurrency delete): Nếu phòng trọ đã bị xóa trước đó
        $room = Room::where('id', $id)->first();
        if (!$room) {
            return redirect()->route('admin.rooms.index')->with('error', 'Phòng trọ này đã bị xóa trước đó hoặc không tồn tại.');
        }

        // 4. Kiểm tra phân quyền (BOLA protection)
        if ($room->tenant_id !== $tenantId) {
            abort(403, 'Bạn không có quyền xóa phòng trọ này.');
        }

        // Xóa ảnh trước khi xóa bản ghi
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Xóa phòng trọ thành công.');
    }

    /**
     * Chuẩn hóa khoảng trắng 2-bytes (Nhật/Trung) và trim sạch sẽ
     */
    private function cleanWhitespace($string)
    {
        if (is_null($string)) {
            return '';
        }
        // Thay thế khoảng trắng toàn sừng (full-width ideographic space '　') thành khoảng trắng thường
        $cleaned = str_replace('　', ' ', $string);
        return trim($cleaned);
    }

    /**
     * Chuyển đổi số Full-width (０-９) sang Half-width (0-9) chuẩn
     */
    private function toHalfWidth($string)
    {
        if (is_null($string)) {
            return '';
        }
        
        $fullWidth = ['０', '１', '２', '３', '４', '５', '６', '７', '８', '９'];
        $halfWidth = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($fullWidth, $halfWidth, $string);
    }
}
