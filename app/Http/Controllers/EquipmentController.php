<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Room;
use App\Models\RoomEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước.');
            }

            if (!Auth::user()->isLandlord()) {
                abort(403, 'Bạn không có quyền truy cập chức năng này.');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $page = $request->query('page');
        if ($page !== null && (!ctype_digit((string) $page) || (int) $page <= 0)) {
            return redirect()->route('admin.equipment.index', ['page' => 1]);
        }

        $equipment = Equipment::where('tenant_id', $tenantId)
            ->with(['roomAllocations.room.building'])
            ->orderBy('name')
            ->paginate(8);

        if ($equipment->currentPage() > $equipment->lastPage() && $equipment->lastPage() > 0) {
            return redirect()->route('admin.equipment.index', ['page' => 1]);
        }

        $rooms = Room::with('building')
            ->where('tenant_id', $tenantId)
            ->orderBy('room_number')
            ->get();

        $allocations = RoomEquipment::with(['room.building', 'equipment'])
            ->where('tenant_id', $tenantId)
            ->where('quantity', '>', 0)
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.equipment.index', compact('equipment', 'rooms', 'allocations'));
    }

    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $input = $this->normalizeEquipmentInput($request);
        $request->merge($input);

        $request->validate($this->equipmentRules(), $this->messages());

        DB::transaction(function () use ($request, $tenantId) {
            $equipment = Equipment::where('tenant_id', $tenantId)
                ->where('code', $request->code)
                ->lockForUpdate()
                ->first();

            if ($equipment) {
                $equipment->update([
                    'name' => $request->name,
                    'unit' => $request->unit,
                    'total_quantity' => $equipment->total_quantity + (int) $request->quantity,
                    'description' => $request->description,
                    'version' => $equipment->version + 1,
                ]);
                return;
            }

            Equipment::create([
                'tenant_id' => $tenantId,
                'code' => $request->code,
                'name' => $request->name,
                'unit' => $request->unit,
                'total_quantity' => (int) $request->quantity,
                'allocated_quantity' => 0,
                'description' => $request->description,
                'version' => 1,
            ]);
        });

        return redirect()->route('admin.equipment.index')->with('success', 'Đã nhập thiết bị vào danh mục.');
    }

    public function update(Request $request, $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $equipment = $this->findTenantEquipment($id, $tenantId);

        $input = $this->normalizeEquipmentInput($request);
        $request->merge($input);

        if ((int) $request->input('version') !== (int) $equipment->version) {
            return back()->withInput()->with('error', 'Dữ liệu thiết bị đã thay đổi, vui lòng tải lại trang trước khi cập nhật.');
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:30',
            'total_quantity' => 'required|integer|min:' . $equipment->allocated_quantity . '|max:1000000',
            'description' => 'nullable|string|max:1000',
        ], $this->messages());

        $duplicate = Equipment::where('tenant_id', $tenantId)
            ->where('code', $request->code)
            ->where('id', '!=', $equipment->id)
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'Mã thiết bị đã tồn tại trong danh mục.');
        }

        $equipment->update([
            'code' => $request->code,
            'name' => $request->name,
            'unit' => $request->unit,
            'total_quantity' => (int) $request->total_quantity,
            'description' => $request->description,
            'version' => $equipment->version + 1,
        ]);

        return redirect()->route('admin.equipment.index')->with('success', 'Đã cập nhật thông tin thiết bị.');
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->isMethod('delete')) {
            abort(405, 'Hành động xóa chỉ được thực hiện bằng yêu cầu DELETE an toàn.');
        }

        $tenantId = Auth::user()->tenant_id;
        $equipment = $this->findTenantEquipment($id, $tenantId);

        if ($equipment->allocated_quantity > 0) {
            return back()->with('error', 'Không thể xóa thiết bị đang được phân bổ cho phòng. Hãy thu hồi trước.');
        }

        $equipment->delete();

        return redirect()->route('admin.equipment.index')->with('success', 'Đã xóa thiết bị khỏi danh mục.');
    }

    public function allocate(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $input = $this->normalizeQuantityInput($request);
        $request->merge($input);

        $request->validate($this->movementRules($tenantId), $this->messages());

        try {
            DB::transaction(function () use ($request, $tenantId) {
                $equipment = Equipment::where('tenant_id', $tenantId)
                    ->where('id', $request->equipment_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $room = Room::where('tenant_id', $tenantId)
                    ->where('id', $request->room_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = (int) $request->quantity;
                $stock = $equipment->total_quantity - $equipment->allocated_quantity;

                // Khóa bản ghi thiết bị trong transaction để hai tab không thể cùng trừ vượt tồn kho.
                if ($quantity > $stock) {
                    throw new \RuntimeException('Số lượng tồn không đủ để bàn giao/lắp đặt cho phòng này.');
                }

                $allocation = RoomEquipment::firstOrNew([
                    'tenant_id' => $tenantId,
                    'room_id' => $room->id,
                    'equipment_id' => $equipment->id,
                ]);

                $allocation->quantity = (int) $allocation->quantity + $quantity;
                $allocation->last_allocated_at = now();
                $allocation->save();

                $equipment->allocated_quantity += $quantity;
                $equipment->version += 1;
                $equipment->save();
            });
        } catch (\RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.equipment.index')->with('success', 'Đã bàn giao/lắp đặt thiết bị cho phòng.');
    }

    public function recover(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $input = $this->normalizeQuantityInput($request);
        $request->merge($input);

        $request->validate($this->movementRules($tenantId), $this->messages());

        try {
            DB::transaction(function () use ($request, $tenantId) {
                $equipment = Equipment::where('tenant_id', $tenantId)
                    ->where('id', $request->equipment_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $allocation = RoomEquipment::where('tenant_id', $tenantId)
                    ->where('room_id', $request->room_id)
                    ->where('equipment_id', $equipment->id)
                    ->lockForUpdate()
                    ->first();

                $quantity = (int) $request->quantity;

                if (!$allocation || $allocation->quantity < $quantity) {
                    throw new \RuntimeException('Phòng này không có đủ số lượng thiết bị để thu hồi.');
                }

                $allocation->quantity -= $quantity;
                $allocation->last_recovered_at = now();
                $allocation->save();

                $equipment->allocated_quantity = max(0, $equipment->allocated_quantity - $quantity);
                $equipment->version += 1;
                $equipment->save();
            });
        } catch (\RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.equipment.index')->with('success', 'Đã thu hồi thiết bị về tồn kho.');
    }

    private function findTenantEquipment($id, int $tenantId): Equipment
    {
        if (!ctype_digit((string) $id) || (int) $id <= 0 || (int) $id > 9999999999) {
            abort(404, 'Thiết bị không tồn tại.');
        }

        $equipment = Equipment::where('tenant_id', $tenantId)->where('id', $id)->first();
        if (!$equipment) {
            abort(404, 'Thiết bị không tồn tại hoặc không thuộc quyền quản lý của bạn.');
        }

        return $equipment;
    }

    private function normalizeEquipmentInput(Request $request): array
    {
        return [
            'code' => strtoupper($this->cleanText($request->input('code'))),
            'name' => $this->cleanText($request->input('name')),
            'unit' => $this->cleanText($request->input('unit', 'cai')),
            'quantity' => $this->toHalfWidth($this->cleanText($request->input('quantity'))),
            'total_quantity' => $this->toHalfWidth($this->cleanText($request->input('total_quantity'))),
            'description' => strip_tags($this->cleanText($request->input('description'))),
        ];
    }

    private function normalizeQuantityInput(Request $request): array
    {
        return [
            'equipment_id' => $this->toHalfWidth($this->cleanText($request->input('equipment_id'))),
            'room_id' => $this->toHalfWidth($this->cleanText($request->input('room_id'))),
            'quantity' => $this->toHalfWidth($this->cleanText($request->input('quantity'))),
        ];
    }

    private function equipmentRules(): array
    {
        return [
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:30',
            'quantity' => 'required|integer|min:1|max:1000000',
            'description' => 'nullable|string|max:1000',
        ];
    }

    private function movementRules(int $tenantId): array
    {
        return [
            'equipment_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($tenantId) {
                    if (!Equipment::where('tenant_id', $tenantId)->where('id', $value)->exists()) {
                        $fail('Thiết bị được chọn không hợp lệ.');
                    }
                },
            ],
            'room_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($tenantId) {
                    if (!Room::where('tenant_id', $tenantId)->where('id', $value)->exists()) {
                        $fail('Phòng được chọn không hợp lệ.');
                    }
                },
            ],
            'quantity' => 'required|integer|min:1|max:1000000',
        ];
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute không được bỏ trống.',
            'integer' => ':attribute phải là số nguyên hợp lệ.',
            'min' => ':attribute không hợp lệ hoặc nhỏ hơn mức cho phép.',
            'max' => ':attribute vượt quá giới hạn cho phép.',
            'string' => ':attribute phải là chuỗi hợp lệ.',
        ];
    }

    private function cleanText($value): string
    {
        $value = (string) ($value ?? '');
        $value = str_replace(["\u{3000}", 'ã€€'], ' ', $value);
        return trim($value);
    }

    private function toHalfWidth(string $value): string
    {
        return str_replace(
            ['０', '１', '２', '３', '４', '５', '６', '７', '８', '９', 'ï¼', 'ï¼‘', 'ï¼’', 'ï¼“', 'ï¼”', 'ï¼•', 'ï¼–', 'ï¼—', 'ï¼˜', 'ï¼™'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            $value
        );
    }
}
