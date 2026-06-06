<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class VisitorController extends Controller
{
    // Tìm kiếm và lọc nâng cao phòng trọ
    public function index(Request $request)
    {
        $query = Room::with(['building', 'reviews']);

        // Bộ lọc theo Building
        if ($request->has('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        // Bộ lọc theo tầng
        if ($request->has('floor')) {
            $query->where('floor', $request->floor);
        }

        // Bộ lọc giá thuê
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Bộ lọc diện tích
        if ($request->has('area_min')) {
            $query->where('area', '>=', $request->area_min);
        }
        if ($request->has('area_max')) {
            $query->where('area', '<=', $request->area_max);
        }

        // Bộ lọc trạng thái (mặc định chỉ tìm phòng trống nếu không được chỉ định)
        $status = $request->input('status', 'empty');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Bộ lọc tiện ích (amenities) - ví dụ: ['gác lửng', 'cho nuôi thú cưng']
        if ($request->has('amenities')) {
            $amenities = $request->amenities;
            if (is_string($amenities)) {
                $amenities = explode(',', $amenities);
            }
            if (is_array($amenities) && count($amenities) > 0) {
                foreach ($amenities as $amenity) {
                    $query->whereJsonContains('amenities', trim($amenity));
                }
            }
        }

        $rooms = $query->get();

        // Mapped dữ liệu để trả về đẹp mắt cho frontend
        $result = $rooms->map(function ($room) {
            $ratingAvg = $room->reviews->avg('rating') ?: 0;
            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'status' => $room->status,
                'price' => $room->price,
                'area' => $room->area,
                'amenities' => $room->amenities,
                'description' => $room->description,
                'building' => [
                    'id' => $room->building->id,
                    'name' => $room->building->name,
                    'address' => $room->building->address,
                ],
                'rating_avg' => round($ratingAvg, 1),
                'reviews_count' => $room->reviews->count()
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $result->count(),
            'rooms' => $result
        ]);
    }

    // Bản đồ trực quan: trả về danh sách Marker phòng kèm tọa độ toà nhà (giả lập tọa độ địa lý)
    public function map(Request $request)
    {
        $rooms = Room::with('building')->where('status', 'empty')->get();

        $markers = $rooms->map(function ($room) {
            // Giả lập toạ độ dựa trên id của building để hiển thị trên bản đồ trực quan
            $lat = 21.036 + (($room->building->id * 7) % 100) / 10000;
            $lng = 105.782 + (($room->building->id * 13) % 100) / 10000;

            return [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'price' => $room->price,
                'area' => $room->area,
                'building_name' => $room->building->name,
                'address' => $room->building->address,
                'latitude' => $lat,
                'longitude' => $lng,
            ];
        });

        return response()->json([
            'success' => true,
            'markers' => $markers
        ]);
    }

    // Đọc đánh giá của một phòng trọ
    public function reviews($roomId)
    {
        $room = Room::with(['reviews' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->find($roomId);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Phòng không tồn tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'room_number' => $room->room_number,
            'rating_avg' => round($room->reviews->avg('rating') ?: 0, 1),
            'reviews_count' => $room->reviews->count(),
            'reviews' => $room->reviews
        ]);
    }

    // Viết đánh giá (Yêu cầu xác thực tài khoản)
    public function storeReview(Request $request, $roomId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5'
        ]);

        $room = Room::find($roomId);
        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Phòng không tồn tại'
            ], 404);
        }

        $user = Auth::user();

        $review = Review::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'author_name' => $user->name,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gửi đánh giá thành công',
            'review' => $review
        ], 201);
    }

    // So sánh phòng (Nhận tối đa 3 phòng để hiển thị so sánh)
    public function compare(Request $request)
    {
        $request->validate([
            'room_ids' => 'required|array|min:1|max:3',
            'room_ids.*' => 'exists:rooms,id'
        ]);

        $rooms = Room::with(['building', 'reviews'])->whereIn('id', $request->room_ids)->get();

        $comparison = $rooms->map(function ($room) {
            $ratingAvg = $room->reviews->avg('rating') ?: 0;
            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'status' => $room->status,
                'price' => $room->price,
                'area' => $room->area,
                'amenities' => $room->amenities,
                'description' => $room->description,
                'building_name' => $room->building->name,
                'address' => $room->building->address,
                'rating_avg' => round($ratingAvg, 1),
                'reviews_count' => $room->reviews->count()
            ];
        });

        return response()->json([
            'success' => true,
            'comparison' => $comparison
        ]);
    }
}
