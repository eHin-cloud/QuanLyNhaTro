<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mẫu CT01 - Tờ khai thay đổi thông tin cư trú - {{ $resident->name }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header .national-title {
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
        }
        .header .national-subtitle {
            font-weight: bold;
            font-size: 13pt;
            text-decoration: underline;
        }
        .header .form-id {
            text-align: right;
            font-style: italic;
            font-size: 11pt;
            margin-bottom: 10px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            margin-top: 20px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            font-style: italic;
            margin-bottom: 25px;
        }
        .field-group {
            margin-bottom: 12px;
            display: flex;
            flex-wrap: wrap;
        }
        .field-label {
            font-weight: normal;
        }
        .field-value {
            border-bottom: 1px dotted #000;
            flex-grow: 1;
            padding-left: 5px;
            min-height: 20px;
        }
        .row {
            display: flex;
            width: 100%;
            margin-bottom: 10px;
        }
        .col-6 {
            width: 50%;
            display: flex;
        }
        .col-4 {
            width: 33.33%;
            display: flex;
        }
        .col-8 {
            width: 66.66%;
            display: flex;
        }
        .col-3 {
            width: 25%;
            display: flex;
        }
        .col-9 {
            width: 75%;
            display: flex;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
            font-size: 11pt;
        }
        .footer-sign {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .footer-sign .col {
            width: 45%;
        }
        .no-print-btn {
            background-color: #1e293b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .no-print-btn:hover {
            background-color: #0f172a;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="no-print" style="text-align: right;">
        <button class="no-print-btn" onclick="window.print()">
            🖨️ In tờ khai (PDF / Bản cứng)
        </button>
    </div>

    <div class="header">
        <div class="form-id"><b>Mẫu CT01</b> (Ban hành theo TT số 56/2021/TT-BCA ngày 15/5/2021)</div>
        <div class="national-title">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
        <div class="national-subtitle">Độc lập - Tự do - Hạnh phúc</div>
    </div>

    <div class="title">TỜ KHAI THAY ĐỔI THÔNG TIN CƯ TRÚ</div>
    <div class="subtitle">(Dùng cho đăng ký tạm trú, thông báo lưu trú)</div>

    <div class="row">
        <div class="field-label">Kính gửi:</div>
        <div class="field-value">Công an xã/phường/thị trấn: ............................................................................</div>
    </div>

    <div class="row">
        <div class="col-8">
            <div class="field-label">1. Họ, chữ đệm và tên:</div>
            <div class="field-value"><b>{{ mb_strtoupper($resident->name) }}</b></div>
        </div>
        <div class="col-4">
            <div class="field-label">2. Ngày, tháng, năm sinh:</div>
            <div class="field-value">{{ $resident->dob ? $resident->dob->format('d/m/Y') : '......................' }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <div class="field-label">3. Giới tính:</div>
            <div class="field-value">Nam [  ]  Nữ [  ]</div>
        </div>
        <div class="col-8">
            <div class="field-label">4. Số định danh cá nhân/CCCD:</div>
            <div class="field-value">{{ $resident->cccd ?? '................................................' }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="field-label">5. Số điện thoại liên hệ:</div>
            <div class="field-value">{{ $resident->phone ?? '...................................' }}</div>
        </div>
        <div class="col-6">
            <div class="field-label">6. Địa chỉ Email:</div>
            <div class="field-value">{{ $resident->email ?? '...................................' }}</div>
        </div>
    </div>

    <div class="row">
        <div class="field-label">7. Nơi thường trú:</div>
        <div class="field-value">{{ $resident->hometown ?? '.................................................................................................................................' }}</div>
    </div>

    <div class="row">
        <div class="field-label">8. Nơi tạm trú hiện tại:</div>
        <div class="field-value">Phòng {{ $resident->room->room_number ?? '...' }}, {{ $resident->room->building->address ?? '.................................................................................................................' }}</div>
    </div>

    <div class="row">
        <div class="field-label">9. Nơi ở hiện tại:</div>
        <div class="field-value">Phòng {{ $resident->room->room_number ?? '...' }}, {{ $resident->room->building->address ?? '.................................................................................................................' }}</div>
    </div>

    <div class="row">
        <div class="field-label">10. Nghề nghiệp, nơi làm việc:</div>
        <div class="field-value">Tự do / Học sinh - Sinh viên / Nhân viên văn phòng</div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="field-label">11. Họ tên chủ hộ trọ:</div>
            <div class="field-value">{{ $resident->tenant->bank_account_name ?? '...................................' }}</div>
        </div>
        <div class="col-6">
            <div class="field-label">12. Số điện thoại chủ hộ:</div>
            <div class="field-value">{{ $resident->tenant->phone ?? '...................................' }}</div>
        </div>
    </div>

    <div class="row">
        <div class="field-label">13. Nội dung đề nghị:</div>
        <div class="field-value">Đăng ký tạm trú cho công dân tại căn hộ cho thuê diện tích {{ $resident->room->area ?? '...' }}m² từ ngày {{ $resident->start_date ? $resident->start_date->format('d/m/Y') : now()->format('d/m/Y') }}.</div>
    </div>

    <div class="row">
        <div class="field-label">14. Những thành viên cùng thay đổi cư trú:</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">STT</th>
                <th style="width: 25%;">Họ và tên</th>
                <th style="width: 15%; text-align: center;">Ngày sinh</th>
                <th style="width: 10%; text-align: center;">Giới tính</th>
                <th style="width: 20%;">Số định danh cá nhân / CCCD</th>
                <th style="width: 25%;">Quan hệ với người khai báo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resident->relatives as $index => $relative)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $relative->name }}</td>
                    <td style="text-align: center;">{{ $relative->dob ? \Carbon\Carbon::parse($relative->dob)->format('d/m/Y') : '' }}</td>
                    <td style="text-align: center;">{{ $relative->gender ?? '' }}</td>
                    <td>{{ $relative->cccd ?? '' }}</td>
                    <td>{{ $relative->relationship }}</td>
                </tr>
            @empty
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>...........................................................</td>
                    <td style="text-align: center;">....../....../..........</td>
                    <td style="text-align: center;">..........</td>
                    <td>............................................</td>
                    <td>............................................</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="col">
            <b>Ý KIẾN CỦA CHỦ HỘ / CHỦ SỞ HỮU HỢP PHÁP</b><br>
            <i>(Ký, ghi rõ họ tên và ngày tháng năm)</i>
            <div style="margin-top: 60px;">
                <b>{{ $resident->tenant->bank_account_name ?? '' }}</b>
            </div>
        </div>
        <div class="col">
            <i>Ngày ...... tháng ...... năm 20...</i><br>
            <b>NGƯỜI VIẾT PHIẾU KHAI BÁO</b><br>
            <i>(Ký, ghi rõ họ tên)</i>
            <div style="margin-top: 60px;">
                <b>{{ $resident->name }}</b>
            </div>
        </div>
    </div>
</div>

</body>
</html>
