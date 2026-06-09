<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hợp đồng thuê nhà trọ - {{ $contract->contract_code }}</title>
    <style>
        @page {
            margin: 15mm 18mm 16mm;
        }

        body {
            font-family: DejaVu Serif, serif;
            color: #000;
            font-size: 9.6px;
            line-height: 1.55;
        }

        .page-corner {
            position: fixed;
            width: 18px;
            height: 18px;
            border-color: #9ca3af;
            border-style: solid;
        }

        .corner-tl { top: -8mm; left: -10mm; border-width: 1px 0 0 1px; }
        .corner-tr { top: -8mm; right: -10mm; border-width: 1px 1px 0 0; }
        .corner-bl { bottom: -9mm; left: -10mm; border-width: 0 0 1px 1px; }
        .corner-br { bottom: -9mm; right: -10mm; border-width: 0 1px 1px 0; }

        .meta {
            color: #334155;
            font-size: 8.8px;
            margin-bottom: 6px;
            text-align: right;
        }

        .document {
            max-width: 168mm;
            margin: 0 auto;
        }

        .line {
            margin: 0 0 3px;
            text-align: left;
            word-spacing: normal;
            letter-spacing: normal;
        }

        .center { text-align: center; }

        .national {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.3px;
            margin-bottom: 1px;
        }

        .motto {
            font-weight: 700;
            font-size: 9px;
            margin-bottom: 1px;
        }

        .date-line {
            text-align: right;
            font-style: italic;
            font-size: 10px;
            margin-bottom: 10px;
            padding-right: 22mm;
        }

        .title {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            margin-top: 8px;
            margin-bottom: 1px;
        }

        .underline {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .section {
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        .article {
            font-weight: 700;
            margin-top: 8px;
            margin-bottom: 3px;
        }

        .indent { padding-left: 13px; }
        .blank { height: 5px; }

        .dot-line {
            border-bottom: 1px dotted #64748b;
            height: 11px;
            margin: 1px 0;
        }

        .signatures {
            margin-top: 28px;
            width: 100%;
            table-layout: fixed;
            page-break-inside: avoid;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-weight: 700;
            font-size: 10px;
        }

        .hint {
            display: block;
            margin-top: 4px;
            font-weight: 400;
            font-style: italic;
            color: #334155;
        }

        .signature-image {
            display: block;
            margin: 14px auto 0;
            max-width: 170px;
            max-height: 78px;
        }

        .signed-mark {
            display: inline-block;
            margin-top: 18px;
            padding: 5px 11px;
            border: 1px solid #94a3b8;
            color: #0f172a;
            font-size: 9px;
            font-weight: 700;
        }

        .name-line {
            margin-top: 62px;
            font-weight: 400;
        }

        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -10mm;
            color: #94a3b8;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="page-corner corner-tl"></div>
    <div class="page-corner corner-tr"></div>
    <div class="page-corner corner-bl"></div>
    <div class="page-corner corner-br"></div>

    @php
        $lines = preg_split('/\R/u', (string) $contract->terms);
    @endphp

    <div class="document">
        <div class="meta">Mã hợp đồng: {{ $contract->contract_code }}</div>

        @foreach($lines as $index => $line)
            @php
                $text = trim($line);
                $class = 'line';

                if ($text === '') {
                    $class .= ' blank';
                } elseif ($index === 0) {
                    $class .= ' center national';
                } elseif ($index === 1) {
                    $class .= ' center motto';
                } elseif ($index === 2) {
                    $class .= ' date-line';
                } elseif ($text === 'HỢP ĐỒNG THUÊ NHÀ TRỌ') {
                    $class .= ' center title';
                } elseif ($text === '_______') {
                    $class .= ' center underline';
                } elseif (preg_match('/^(I\.|II\.)/u', $text)) {
                    $class .= ' section';
                } elseif (preg_match('/^ĐIỀU\s+\d+/u', $text)) {
                    $class .= ' article';
                } elseif (preg_match('/^\d+\.\d/u', $text)) {
                    $class .= ' article indent';
                } elseif (str_starts_with($text, '-')) {
                    $class .= ' indent';
                }
            @endphp

            @if($text === '')
                <div class="{{ $class }}"></div>
            @elseif(str_starts_with($text, '......'))
                <div class="dot-line"></div>
            @else
                <div class="{{ $class }}">{{ $text }}</div>
            @endif
        @endforeach

        <table class="signatures">
            <tr>
                <td>
                    BÊN THUÊ NHÀ
                    <span class="hint">(Ký, ghi rõ họ tên)</span>
                    @if($contract->signature && str_contains($contract->signature, 'image/svg+xml'))
                        <img src="{{ $contract->signature }}" class="signature-image" alt="Chữ ký bên thuê">
                    @elseif($contract->signature && function_exists('imagecreatefrompng'))
                        <img src="{{ $contract->signature }}" class="signature-image" alt="Chữ ký bên thuê">
                    @elseif($contract->signature)
                        <div class="signed-mark">Đã ký điện tử</div>
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
    </div>

    <div class="footer">
        SmartRoom - Bản in hợp đồng điện tử
    </div>
</body>
</html>
