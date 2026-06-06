<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Resident;
use App\Models\UtilityRecord;
use App\Models\Review;
use App\Models\Contract;
use Carbon\Carbon;

class SmartRoomSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Rooms
        $roomData = [
            // Floor 1
            ['room_number' => '101', 'floor' => 1, 'status' => 'occupied', 'price' => 3200000],
            ['room_number' => '102', 'floor' => 1, 'status' => 'occupied', 'price' => 3200000],
            ['room_number' => '103', 'floor' => 1, 'status' => 'overdue',  'price' => 3500000],
            ['room_number' => '104', 'floor' => 1, 'status' => 'empty',    'price' => 3500000],

            // Floor 2
            ['room_number' => '201', 'floor' => 2, 'status' => 'occupied', 'price' => 3300000],
            ['room_number' => '202', 'floor' => 2, 'status' => 'occupied', 'price' => 3300000],
            ['room_number' => '203', 'floor' => 2, 'status' => 'overdue',  'price' => 3600000],
            ['room_number' => '204', 'floor' => 2, 'status' => 'empty',    'price' => 3600000],

            // Floor 3
            ['room_number' => '301', 'floor' => 3, 'status' => 'occupied', 'price' => 3500000],
            ['room_number' => '302', 'floor' => 3, 'status' => 'occupied', 'price' => 3500000],
            ['room_number' => '303', 'floor' => 3, 'status' => 'overdue',  'price' => 3800000],
            ['room_number' => '304', 'floor' => 3, 'status' => 'empty',    'price' => 3800000],
        ];

        $rooms = [];
        foreach ($roomData as $data) {
            $rooms[$data['room_number']] = Room::create($data);
        }

        // 2. Create Residents for occupied & overdue rooms
        $residentsData = [
            '101' => ['name' => 'Trần Thanh Hùng', 'phone' => '0912345678', 'email' => 'hung.tran@gmail.com', 'start_date' => '2026-01-15'],
            '102' => ['name' => 'Nguyễn Thị Lan',  'phone' => '0987654321', 'email' => 'lan.nguyen@gmail.com', 'start_date' => '2026-02-10'],
            '103' => ['name' => 'Lê Hoàng Nam',    'phone' => '0905123456', 'email' => 'nam.le@gmail.com',     'start_date' => '2026-01-05'],
            
            '201' => ['name' => 'Phạm Minh Tuấn', 'phone' => '0933999888', 'email' => 'tuan.pham@gmail.com', 'start_date' => '2026-03-01'],
            '202' => ['name' => 'Vũ Thu Trang',   'phone' => '0944888777', 'email' => 'trang.vu@gmail.com',   'start_date' => '2026-03-15'],
            '203' => ['name' => 'Đặng Anh Đức',   'phone' => '0911777666', 'email' => 'duc.dang@gmail.com',   'start_date' => '2026-02-20'],
            
            '301' => ['name' => 'Hoàng Quốc Việt', 'phone' => '0977666555', 'email' => 'viet.hoang@gmail.com', 'start_date' => '2026-04-01'],
            '302' => ['name' => 'Bùi Phương Thảo', 'phone' => '0966555444', 'email' => 'thao.bui@gmail.com',   'start_date' => '2026-04-10'],
            '303' => ['name' => 'Ngô Tiến Đạt',    'phone' => '0955444333', 'email' => 'dat.ngo@gmail.com',     'start_date' => '2026-03-25'],
        ];

        foreach ($residentsData as $roomNum => $res) {
            Resident::create([
                'room_id' => $rooms[$roomNum]->id,
                'name' => $res['name'],
                'phone' => $res['phone'],
                'email' => $res['email'],
                'start_date' => $res['start_date'],
                'status' => 'active'
            ]);
        }

        // 3. Generate historical utility records (March, April, May, June 2026)
        $months = ['2026-03', '2026-04', '2026-05', '2026-06'];
        
        // Base meter readings starting in Feb 2026
        $meters = [
            '101' => ['elec' => 100, 'water' => 10],
            '102' => ['elec' => 150, 'water' => 15],
            '103' => ['elec' => 120, 'water' => 12],
            '201' => ['elec' => 80,  'water' => 8],
            '202' => ['elec' => 110, 'water' => 11],
            '203' => ['elec' => 95,  'water' => 9],
            '301' => ['elec' => 70,  'water' => 7],
            '302' => ['elec' => 130, 'water' => 13],
            '303' => ['elec' => 140, 'water' => 14],
        ];

        foreach ($months as $month) {
            foreach ($meters as $roomNum => &$meter) {
                // Determine if this room was rented in this month
                $room = $rooms[$roomNum];
                $resident = Resident::where('room_id', $room->id)->first();
                $startDate = Carbon::parse($resident->start_date);
                $monthDate = Carbon::parse($month . '-01');
                
                if ($monthDate->lt($startDate->startOfMonth())) {
                    continue; // Resident hadn't moved in yet
                }

                $oldElec = $meter['elec'];
                $oldWater = $meter['water'];
                
                // Add random usage
                $elecUsage = rand(80, 150); // kWh
                $waterUsage = rand(4, 10);   // m3
                
                $newElec = $oldElec + $elecUsage;
                $newWater = $oldWater + $waterUsage;
                
                // Save current for next month
                $meter['elec'] = $newElec;
                $meter['water'] = $newWater;

                // Determine invoice status for this month
                $status = 'paid';
                if ($month === '2026-06') {
                    // For June, overdue rooms are sent but unpaid
                    if ($room->status === 'overdue') {
                        $status = 'sent';
                    }
                }

                UtilityRecord::create([
                    'room_id' => $room->id,
                    'billing_month' => $month,
                    'old_electricity' => $oldElec,
                    'new_electricity' => $newElec,
                    'old_water' => $oldWater,
                    'new_water' => $newWater,
                    'electricity_price' => 3500,
                    'water_price' => 15000,
                    'status' => $status
                ]);
            }
        }

        // 4. Create Reviews
        $reviewsData = [
            '101' => [
                ['author_name' => 'Nguyễn Minh Anh', 'rating' => 5, 'comment' => 'Phòng sạch sẽ thoáng mát, chủ nhà rất thân thiện và nhiệt tình hỗ trợ khi có sự cố.'],
                ['author_name' => 'Lê Thanh Bình', 'rating' => 4, 'comment' => 'An ninh tốt, khóa vân tay an toàn. Giá điện nước hơi cao một chút nhưng dịch vụ ổn.']
            ],
            '102' => [
                ['author_name' => 'Phạm Lan Anh', 'rating' => 4, 'comment' => 'Phòng đầy đủ tiện nghi, có ban công phơi đồ rất tiện lợi. Khu vực yên tĩnh.']
            ],
            '103' => [
                ['author_name' => 'Trần Văn Cường', 'rating' => 3, 'comment' => 'Phòng hơi chật so với ảnh, nhưng bù lại an ninh tốt và gần trạm xe buýt.']
            ],
            '201' => [
                ['author_name' => 'Ngô Quốc Khánh', 'rating' => 5, 'comment' => 'Mọi thứ đều tuyệt vời! Phòng có gác lửng thiết kế đẹp, tiện ích đầy đủ.']
            ],
            '202' => [
                ['author_name' => 'Đỗ Phương Thảo', 'rating' => 5, 'comment' => 'Chủ trọ hỗ trợ nhanh chóng. Giờ giấc tự do, không chung chủ. Rất hài lòng!']
            ]
        ];

        foreach ($reviewsData as $roomNum => $reviews) {
            if (isset($rooms[$roomNum])) {
                foreach ($reviews as $rev) {
                    Review::create([
                        'room_id' => $rooms[$roomNum]->id,
                        'rating' => $rev['rating'],
                        'comment' => $rev['comment'],
                        'author_name' => $rev['author_name']
                    ]);
                }
            }
        }

        // 5. Create Contracts for active residents
        foreach ($residentsData as $roomNum => $res) {
            $room = $rooms[$roomNum];
            $resident = Resident::where('room_id', $room->id)->first();
            if ($resident) {
                Contract::create([
                    'room_id' => $room->id,
                    'resident_id' => $resident->id,
                    'contract_code' => 'HĐ-' . $room->room_number . '-' . date('Ymd', strtotime($resident->start_date)),
                    'start_date' => $resident->start_date,
                    'end_date' => Carbon::parse($resident->start_date)->addYear()->toDateString(),
                    'deposit' => $room->price,
                    'status' => 'active',
                    'terms' => "ĐIỀU KHOẢN HỢP ĐỒNG THUÊ PHÒNG\n\n"
                             . "Điều 1: Bên A (Bên cho thuê) đồng ý cho Bên B (Bên thuê) thuê phòng số " . $room->room_number . " tại địa chỉ SmartRoom Cầu Giấy.\n"
                             . "Điều 2: Giá thuê là " . number_format($room->price) . " VNĐ/tháng (Thanh toán định kỳ trước ngày 10 hàng tháng).\n"
                             . "Điều 3: Tiền đặt cọc bảo đảm là " . number_format($room->price) . " VNĐ. Tiền cọc sẽ được hoàn trả đầy đủ sau khi thanh lý hợp đồng.\n"
                             . "Điều 4: Bên thuê cam kết chấp hành nghiêm chỉnh nội quy phòng trọ, phòng chống cháy nổ và khai báo tạm trú đầy đủ.",
                    'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
                ]);
            }
        }
    }
}
