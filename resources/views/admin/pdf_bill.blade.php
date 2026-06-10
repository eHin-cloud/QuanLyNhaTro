<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hóa đơn tiền nhà phòng {{ $bill->room->room_number }} - {{ $bill->billing_month }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            color: #0056b3;
        }
        .header p {
            margin: 0;
            color: #666;
        }
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-table td.label {
            font-weight: bold;
            width: 120px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        .details-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .details-table td.amount {
            text-align: right;
        }
        .total-box {
            text-align: right;
            margin-top: 15px;
            margin-bottom: 25px;
            font-size: 15px;
        }
        .total-box span.amount {
            font-size: 18px;
            font-weight: bold;
            color: #d9534f;
        }
        .qr-section {
            margin-top: 20px;
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }
        .qr-section img {
            width: 180px;
            height: 180px;
        }
        .qr-section p {
            margin-top: 5px;
            font-size: 12px;
            color: #555;
            font-style: italic;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $tenant->name }}</h2>
        <p>Địa chỉ: {{ $bill->room->building->address }}</p>
        <p>Hotline: {{ $tenant->phone ?? 'N/A' }} | Email: {{ $tenant->email }}</p>
    </div>

    <div class="invoice-title">
        Hóa Đơn Tiền Nhà & Dịch Vụ
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Phòng trọ:</td>
            <td>Phòng {{ $bill->room->room_number }} (Tầng {{ $bill->room->floor }})</td>
            <td class="label">Tháng hóa đơn:</td>
            <td>Tháng {{ explode('-', $bill->billing_month)[1] }}/{{ explode('-', $bill->billing_month)[0] }}</td>
        </tr>
        <tr>
            <td class="label">Khách thuê:</td>
            <td>{{ $resident ? $resident->name : 'N/A' }}</td>
            <td class="label">Ngày lập:</td>
            <td>{{ $bill->created_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Số điện thoại:</td>
            <td>{{ $resident ? $resident->phone : 'N/A' }}</td>
            <td class="label">Trạng thái:</td>
            <td style="font-weight: bold; color: {{ $bill->status === 'paid' ? '#5cb85c' : '#d9534f' }}">
                {{ $bill->status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
            </td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Khoản mục</th>
                <th>Chỉ số cũ</th>
                <th>Chỉ số mới</th>
                <th>Tiêu thụ</th>
                <th>Đơn giá</th>
                <th style="text-align: right;">Thành tiền (VND)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Tiền phòng -->
            <tr>
                <td>Tiền thuê phòng</td>
                <td>-</td>
                <td>-</td>
                <td>1 tháng</td>
                <td>{{ number_format($bill->room_price) }} đ</td>
                <td class="amount">{{ number_format($bill->room_price) }} đ</td>
            </tr>
            <!-- Tiền điện -->
            @if($bill->electricWaterLog)
            <tr>
                <td>Tiền điện</td>
                <td>{{ $bill->electricWaterLog->old_electricity }}</td>
                <td>{{ $bill->electricWaterLog->new_electricity }}</td>
                <td>{{ $bill->electricity_usage }} kWh</td>
                <td>{{ number_format($bill->electricWaterLog->electricity_price) }} đ</td>
                <td class="amount">{{ number_format($bill->electricity_cost) }} đ</td>
            </tr>
            <!-- Tiền nước -->
            <tr>
                <td>Tiền nước</td>
                <td>{{ $bill->electricWaterLog->old_water }}</td>
                <td>{{ $bill->electricWaterLog->new_water }}</td>
                <td>{{ $bill->water_usage }} m³</td>
                <td>{{ number_format($bill->electricWaterLog->water_price) }} đ</td>
                <td class="amount">{{ number_format($bill->water_cost) }} đ</td>
            </tr>
            @endif
            <!-- Dịch vụ khác -->
            <tr>
                <td>Phí dịch vụ chung (Vệ sinh, rác, wifi...)</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td class="amount">{{ number_format($bill->service_cost) }} đ</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        Tổng số tiền cần thanh toán: <span class="amount">{{ number_format($bill->total_amount) }} VND</span>
    </div>

    @if($bill->status !== 'paid' && $bill->vietqr_url)
    <div class="qr-section">
        <p><strong>Quét mã VietQR dưới đây để thanh toán chuyển khoản nhanh:</strong></p>
        <img src="{{ $bill->vietqr_url }}" alt="VietQR Payment">
        <p>Ngân hàng nhận: {{ $tenant->bank_name }} | Số tài khoản: {{ $tenant->bank_account_no }}<br>Chủ tài khoản: {{ $tenant->bank_account_name }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Cảm ơn quý khách đã tin tưởng dịch vụ của chúng tôi!</p>
        <p>Mọi thắc mắc xin vui lòng liên hệ hotline ban quản lý.</p>
    </div>

</body>
</html>
