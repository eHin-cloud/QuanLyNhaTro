<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hợp đồng thuê nhà trọ - {{ $contract->contract_code }}</title>
    <style>
        @page {
            margin: 24mm 20mm 22mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.55;
        }

        .meta {
            color: #475569;
            font-size: 10px;
            margin-bottom: 12px;
            text-align: right;
        }

        .terms {
            white-space: pre-line;
            text-align: justify;
        }

        .signatures {
            margin-top: 42px;
            width: 100%;
            table-layout: fixed;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-weight: 700;
        }

        .hint {
            display: block;
            margin-top: 4px;
            font-weight: 400;
            font-style: italic;
            color: #475569;
        }

        .signature-image {
            margin-top: 18px;
            max-width: 190px;
            max-height: 90px;
        }

        .name-line {
            margin-top: 76px;
            font-weight: 400;
        }

        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -12mm;
            color: #94a3b8;
            font-size: 9px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="meta">
        Mã hợp đồng: {{ $contract->contract_code }}
    </div>

    <div class="terms">{{ $contract->terms }}</div>

    <table class="signatures">
        <tr>
            <td>
                BÊN THUÊ NHÀ
                <span class="hint">(Ký, ghi rõ họ tên)</span>
                @if($contract->signature && function_exists('imagecreatefrompng'))
                    <img src="{{ $contract->signature }}" class="signature-image" alt="Chữ ký bên thuê">
                @elseif($contract->signature)
                    <div class="name-line">Đã ký điện tử</div>
                @else
                    <div class="name-line">........................................</div>
                @endif
            </td>
            <td>
                BÊN CHO THUÊ NHÀ
                <span class="hint">(Ký, ghi rõ họ tên)</span>
                <div class="name-line">........................................</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        SmartRoom - Bản in hợp đồng điện tử
    </div>
</body>
</html>
