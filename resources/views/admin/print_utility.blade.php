<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Tiền Phòng - Phòng {{ $record->room->room_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 40px;
            font-size: 13px;
            line-height: 1.6;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-section h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: -0.5px;
        }
        .logo-section p {
            margin: 0;
            color: #64748b;
            font-size: 12px;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }
        .invoice-details p {
            margin: 2px 0;
            color: #64748b;
            font-size: 12px;
        }
        .grid-info {
            display: grid;
            grid-template-cols: 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }
        .info-col h3 {
            margin: 0 0 10px 0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            font-weight: 700;
        }
        .info-col p {
            margin: 3px 0;
            font-size: 13px;
        }
        .info-col strong {
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-bottom: 30px;
        }
        table th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
            padding: 12px;
            font-size: 11px;
            text-transform: uppercase;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 20px;
        }
        .payment-instructions {
            max-width: 400px;
        }
        .payment-instructions h4 {
            margin: 0 0 10px 0;
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }
        .payment-instructions p {
            margin: 4px 0;
            color: #64748b;
            font-size: 11px;
        }
        .qr-code {
            margin-top: 15px;
            width: 130px;
            height: 130px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px;
        }
        .totals-table {
            width: 300px;
            margin-bottom: 0;
        }
        .totals-table td {
            padding: 8px 12px;
            border: none;
        }
        .totals-table tr.grand-total td {
            font-size: 16px;
            font-weight: 800;
            color: #4f46e5;
            border-top: 2px solid #e2e8f0;
            padding-top: 12px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
            color: #94a3b8;
            font-size: 11px;
        }
        .no-print-btn {
            display: inline-block;
            background: #4f46e5;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            margin-bottom: 20px;
            transition: background 0.2s;
        }
        .no-print-btn:hover {
            background: #4338ca;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div style="max-width: 800px; margin: auto;" class="no-print">
        <button onclick="window.print()" class="no-print-btn">🖨️ In Hóa Đơn / Xuất PDF</button>
        <button onclick="window.close()" class="no-print-btn" style="background: #64748b; margin-left: 10px;">Đóng Trang</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo-section">
                <h1>SmartRoom</h1>
                <p>Hệ Thống Quản Lý Trọ Thông Minh</p>
            </div>
            <div class="invoice-details">
                <h2>HÓA ĐƠN TIỀN NHÀ</h2>
                <p><strong>Số hóa đơn:</strong> #SR-{{ $record->id }}-{{ date('Ymd') }}</p>
                <p><strong>Tháng thanh toán:</strong> Tháng {{ explode('-', $record->billing_month)[1] }}/{{ explode('-', $record->billing_month)[0] }}</p>
                <p><strong>Ngày lập hóa đơn:</strong> {{ $record->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="grid-info">
            <div class="info-col">
                <h3>Đơn vị cho thuê</h3>
                <p><strong>SmartRoom Cầu Giấy</strong></p>
                <p>Địa chỉ: Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy, Hà Nội</p>
                <p>Hotline: 0912.345.678</p>
            </div>
            <div class="info-col">
                <h3>Khách hàng</h3>
                <p><strong>Cư dân: {{ $resident ? $resident->name : 'N/A' }}</strong></p>
                <p>Phòng: <strong>{{ $record->room->room_number }}</strong></p>
                <p>Số điện thoại: {{ $resident ? $resident->phone : 'N/A' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Hạng mục dịch vụ</th>
                    <th>Chỉ số cũ</th>
                    <th>Chỉ số mới</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. Room Price -->
                <tr>
                    <td>1</td>
                    <td><strong>Tiền phòng tháng {{ explode('-', $record->billing_month)[1] }}</strong></td>
                    <td>-</td>
                    <td>-</td>
                    <td>1 tháng</td>
                    <td>{{ number_format($record->room->price) }}đ</td>
                    <td class="text-right"><strong>{{ number_format($record->room->price) }}đ</strong></td>
                </tr>
                <!-- 2. Electricity -->
                @php
                    $elecUsed = $record->new_electricity - $record->old_electricity;
                @endphp
                <tr>
                    <td>2</td>
                    <td>Tiền điện</td>
                    <td>{{ $record->old_electricity }}</td>
                    <td>{{ $record->new_electricity }}</td>
                    <td>{{ $elecUsed }} kWh</td>
                    <td>{{ number_format($record->electricity_price) }}đ</td>
                    <td class="text-right"><strong>{{ number_format($elecUsed * $record->electricity_price) }}đ</strong></td>
                </tr>
                <!-- 3. Water -->
                @php
                    $waterUsed = $record->new_water - $record->old_water;
                @endphp
                <tr>
                    <td>3</td>
                    <td>Tiền nước</td>
                    <td>{{ $record->old_water }}</td>
                    <td>{{ $record->new_water }}</td>
                    <td>{{ $waterUsed }} m3</td>
                    <td>{{ number_format($record->water_price) }}đ</td>
                    <td class="text-right"><strong>{{ number_format($waterUsed * $record->water_price) }}đ</strong></td>
                </tr>
                <!-- 4. Default services -->
                <tr>
                    <td>4</td>
                    <td>Dịch vụ chung (Vệ sinh, mạng internet, rác)</td>
                    <td>-</td>
                    <td>-</td>
                    <td>Cố định</td>
                    <td>150,000đ</td>
                    <td class="text-right"><strong>150,000đ</strong></td>
                </tr>
            </tbody>
        </table>

        @php
            $grandTotal = $record->room->price + ($elecUsed * $record->electricity_price) + ($waterUsed * $record->water_price) + 150000;
        @endphp

        <div class="total-section">
            <div class="payment-instructions">
                <h4>Hướng dẫn thanh toán</h4>
                <p>Quý khách vui lòng quét mã VietQR bên dưới hoặc chuyển khoản trực tiếp qua ngân hàng.</p>
                <p><strong>Ngân hàng:</strong> MB Bank</p>
                <p><strong>Số tài khoản:</strong> 1234567890</p>
                <p><strong>Chủ tài khoản:</strong> NGUYEN THANH HIEN</p>
                
                <img class="qr-code" src="https://img.vietqr.io/image/970422-1234567890-compact2.jpg?amount={{ $grandTotal }}&addInfo=Thanh%20toan%20tien%20phong%20{{ $record->room->room_number }}&accountName=NGUYEN%20THANH%20HIEN" alt="VietQR Code">
            </div>
            
            <div>
                <table class="totals-table">
                    <tr>
                        <td>Cộng tiền dịch vụ:</td>
                        <td class="text-right">{{ number_format(($elecUsed * $record->electricity_price) + ($waterUsed * $record->water_price) + 150000) }}đ</td>
                    </tr>
                    <tr>
                        <td>Cộng tiền phòng:</td>
                        <td class="text-right">{{ number_format($record->room->price) }}đ</td>
                    </tr>
                    <tr class="grand-total">
                        <td>TỔNG CỘNG:</td>
                        <td class="text-right">{{ number_format($grandTotal) }}đ</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>Cảm ơn quý khách đã tin tưởng và đồng hành cùng SmartRoom!</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Automatically open printer dialogue
            window.print();
        }
    </script>
</body>
</html>
