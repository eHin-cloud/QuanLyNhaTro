<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        Contract::query()->delete();

        $residents = Resident::with(['room.building.tenant'])
            ->where('status', 'active')
            ->whereNotNull('room_id')
            ->orderBy('id')
            ->get();

        foreach ($residents as $index => $resident) {
            $room = $resident->room;
            $building = $room?->building;
            $tenant = $building?->tenant ?? $resident->tenant;

            if (!$room || !$tenant) {
                continue;
            }

            $startDate = $resident->start_date
                ? Carbon::parse($resident->start_date)
                : Carbon::now()->subMonths(2);
            $endDate = $startDate->copy()->addYears(3);
            $status = $index % 4 === 0 ? 'pending' : 'active';

            Contract::create([
                'tenant_id' => $resident->tenant_id,
                'room_id' => $room->id,
                'resident_id' => $resident->id,
                'contract_code' => 'HĐ-' . $room->room_number . '-' . $startDate->format('Ymd') . '-' . $resident->id,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'deposit' => (int) $room->price,
                'status' => $status,
                'terms' => $this->terms($tenant, $building, $room, $resident, $startDate, $endDate),
                'signature' => $status === 'active' ? $this->signatureDataUrl($resident->name) : null,
                'lessor_signature' => $status === 'active' ? $this->signatureDataUrl($tenant?->bank_account_name ?: $tenant?->name ?: 'Chủ nhà') : null,
            ]);
        }
    }

    private function signatureDataUrl(string $name): string
    {
        $parts = preg_split('/\s+/u', trim($name));
        $lastName = $parts ? end($parts) : 'Tenant';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="460" height="170" viewBox="0 0 460 170">'
            . '<rect width="460" height="170" fill="white"/>'
            . '<path d="M62 103 C104 42, 137 42, 116 98 C101 137, 162 112, 196 73 C218 48, 235 57, 218 96 C207 124, 247 118, 278 82 C301 55, 319 63, 303 104 C294 130, 338 117, 381 76" fill="none" stroke="#111827" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>'
            . '<path d="M92 119 C156 139, 260 136, 386 119" fill="none" stroke="#111827" stroke-width="4" stroke-linecap="round"/>'
            . '<text x="238" y="95" text-anchor="middle" font-family="DejaVu Serif, serif" font-size="34" font-style="italic" fill="#111827">' . e($lastName) . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private function terms($tenant, $building, $room, $resident, Carbon $startDate, Carbon $endDate): string
    {
        $rentPrice = (int) $room->price;
        $cycle = 3;
        $cycleAmount = $rentPrice * $cycle;
        $address = trim(($building?->address ?: '..........................................................') . ' - Phòng ' . $room->room_number);
        $amenities = collect($room->amenities ?? [])
            ->filter()
            ->map(fn ($item) => '- ' . $item)
            ->implode("\n") ?: '..........................................................................................................................';

        $city = (strpos($building?->address ?? '', 'Hồ Chí Minh') !== false || strpos($building?->address ?? '', 'HCM') !== false || strpos($building?->address ?? '', 'Bình Thạnh') !== false) ? 'TP. Hồ Chí Minh' : 'Hà Nội';

        return implode("\n", [
            'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM',
            'Độc lập – Tự do – Hạnh phúc',
            $city . ', ngày ' . $startDate->day . ' tháng ' . $startDate->month . ' năm ' . $startDate->year,
            '',
            'HỢP ĐỒNG THUÊ NHÀ TRỌ',
            '_______',
            '- Căn cứ vào các quy định pháp luật có liên quan,',
            'Tại ' . $address . '. Chúng tôi gồm:',
            '',
            'I. BÊN CHO THUÊ NHÀ (Sau đây gọi tắt là bên A):',
            'Ông/Bà: ' . ($tenant?->bank_account_name ?: $tenant?->name ?: 'Chủ nhà') . '     CMTND/CCCD số: ........................',
            'HKTT/Chỗ ở hiện tại: ' . ($building?->address ?: '..........................................................'),
            'Điện thoại liên hệ: ' . ($tenant?->phone ?: '........................'),
            '',
            'II. BÊN THUÊ NHÀ Ở (Sau đây gọi tắt là bên B):',
            'Ông/Bà: ' . $resident->name . '     Số CMND/CCCD số: ' . ($resident->cccd ?: '........................'),
            'HKTT: ' . ($resident->hometown ?: '..........................................................'),
            'Chỗ ở hiện tại: ' . $address,
            'Điện thoại liên hệ: ' . ($resident->phone ?: '........................'),
            '',
            'Hai bên thống nhất ký kết Hợp đồng cho thuê nhà để ở với các nội dung sau:',
            '',
            'ĐIỀU 1: NỘI DUNG HỢP ĐỒNG',
            '1.1 Nhà cho thuê',
            '- Địa chỉ: ' . $address . '.',
            '- Diện tích cho thuê: Phòng ' . $room->room_number . ', diện tích ' . ($room->area ?: '........') . ' m².',
            '- Trang thiết bị kèm theo:',
            $amenities,
            '',
            '1.2 Mục đích thuê nhà:',
            '- Bên thuê nhà thuê nhà để ở.',
            '- Số lượng người ở: 1',
            '',
            '1.3 Giá cho thuê: ' . number_format($rentPrice, 0, ',', '.') . ' VND/01 tháng.',
            'Các khoản phí như điện, nước, phí vệ sinh bên A sẽ phải tự thanh toán theo hóa đơn của đơn vị cung cấp, trừ khi hai bên có thỏa thuận khác bằng văn bản.',
            '',
            '1.4 Thời hạn cho thuê: 03 năm bắt đầu từ ngày ' . $startDate->format('d/m/Y') . ' đến ' . $endDate->format('d/m/Y') . '.',
            'Trong trường hợp gia hạn Hợp đồng thuê, hai bên sẽ cùng nhau thoả thuận về việc gia hạn. Trong bất cứ trường hợp nào, đề xuất về việc gia hạn sẽ được đưa ra trước 30 (ba mươi) ngày trước khi hết hạn.',
            '',
            '1.5 Hình thức thanh toán',
            '- Số tiền thanh toán: Bên B thanh toán cho bên A số tiền 03 tháng/01 lần tương đương ' . number_format($cycleAmount, 0, ',', '.') . ' VND trong khoảng từ mồng 10 đến ngày 15 tháng đầu tiên của kỳ thanh toán tiền nhà.',
            '- Thời điểm thanh toán lần đầu: ' . $startDate->format('d/m/Y'),
            '- Hình thức thanh toán: Chuyển khoản hoặc tiền mặt theo thỏa thuận của hai bên.',
            '- Tiền đặt cọc: ' . number_format($rentPrice, 0, ',', '.') . ' VND.',
            '',
            'ĐIỀU 2: QUYỀN VÀ NGHĨA VỤ CÁC BÊN',
            '1. Quyền và nghĩa vụ của Bên cho thuê:',
            '- Yêu cầu Bên thuê trả đủ tiền thuê nhà đúng thời hạn ghi trong Hợp đồng;',
            '- Yêu cầu Bên thuê có trách nhiệm trong việc sửa chữa phần hư hỏng, bồi thường thiệt hại do lỗi của Bên thuê gây ra ngay tại thời điểm phát hiện;',
            '- Đơn phương chấm dứt thực hiện Hợp đồng thuê nhà khi Bên thuê nhà vi phạm nghiêm trọng nghĩa vụ trong Hợp đồng hoặc vi phạm quy định về an ninh trật tự;',
            '- Bảo trì nhà ở; cải tạo nhà ở khi được Bên thuê đồng ý;',
            '- Nhận lại nhà trong các trường hợp chấm dứt Hợp đồng thuê nhà ở quy định tại Hợp đồng này;',
            '- Kiểm tra tình trạng nhà, trang thiết bị nhà sau khi đã thông báo với Bên thuê nhà.',
            '',
            '2. Quyền và nghĩa vụ của Bên thuê:',
            '- Nhận nhà ở và trang thiết bị (nếu có) theo đúng ngày quy định tại Điều 1 Hợp đồng này;',
            '- Bảo quản nhà và các trang thiết bị sử dụng;',
            '- Thanh toán tiền nhà đúng thời hạn;',
            '- Không được cho bên thứ ba thuê lại nhà;',
            '- Chịu trách nhiệm đền bù những hư hỏng, mất mát các đồ đạc, trang thiết bị nội thất tại địa điểm thuê không phải do hao mòn tự nhiên trong quá trình sử dụng gây ra;',
            '- Đảm bảo vệ sinh, an ninh trật tự trong suốt quá trình thuê nhà.',
            '',
            'ĐIỀU 3: CHẤM DỨT HỢP ĐỒNG',
            '1. Hợp đồng này chấm dứt khi hết thời hạn tại Điều 1 hoặc hai bên thỏa thuận chấm dứt Hợp đồng;',
            '2. Các bên khi đơn phương chấm dứt Hợp đồng phải thông báo trước 02 tháng và thực hiện đầy đủ các nghĩa vụ ghi nhận tại Hợp đồng;',
            '3. Hợp đồng chấm dứt khi nhà ở cho thuê phải sửa chữa do bị hư hỏng nặng hoặc do thực hiện quy hoạch xây dựng của Nhà nước.',
            '',
            'ĐIỀU 4: CAM KẾT CỦA CÁC BÊN',
            '1. Hai bên cùng cam kết thực hiện đúng các nội dung đã ký. Trong quá trình thực hiện nếu phát hiện thấy những vấn đề cần thoả thuận thì hai bên có thể lập thêm phụ lục hợp đồng. Nội dung Hợp đồng phụ có giá trị pháp lý như hợp đồng chính.',
            '2. Hợp đồng được lập thành 03 trang, 02 bản và có giá trị như nhau. Mỗi bên giữ 01 bản, 01 bản./.',
        ]);
    }
}
