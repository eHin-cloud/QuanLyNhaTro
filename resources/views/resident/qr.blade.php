<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartRoom - QR Thanh Toan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-[#080b11] text-slate-100 flex items-center justify-center p-4">
    <main class="w-full max-w-md rounded-2xl bg-slate-900/80 border border-slate-800 p-6 text-center">
        <a href="{{ route('smartroom.resident') }}" class="inline-flex items-center gap-2 text-xs text-slate-400 hover:text-slate-200 mb-5">
            <i class="fa-solid fa-arrow-left"></i> Quay lai
        </a>

        <h1 class="text-xl font-black">QR thanh toan</h1>
        <p class="text-xs text-slate-500 mt-1">Hoa don {{ $bill->billing_month }} - {{ $resident->name }}</p>

        <div class="mt-6 rounded-2xl bg-white p-4">
            <img src="{{ $qrUrl }}" alt="VietQR" class="w-full aspect-square object-contain" onerror="this.alt='Khong tai duoc ma QR'; this.classList.add('hidden'); document.getElementById('qr-fallback').classList.remove('hidden');">
            <div id="qr-fallback" class="hidden text-slate-900 text-sm font-bold py-20">Khong tai duoc ma QR</div>
        </div>

        <div class="mt-5 rounded-xl bg-slate-950/60 border border-slate-800 p-4 text-left text-sm">
            <div class="flex justify-between gap-3">
                <span class="text-slate-500">So tien</span>
                <strong>{{ number_format($bill->total_amount) }} VND</strong>
            </div>
            <div class="flex justify-between gap-3 mt-2">
                <span class="text-slate-500">Trang thai</span>
                <strong>{{ $bill->status_label }}</strong>
            </div>
        </div>

        <a href="{{ $qrUrl }}" download class="mt-5 inline-flex w-full items-center justify-center gap-2 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold">
            <i class="fa-solid fa-download"></i> Tai ma QR
        </a>
    </main>
</body>
</html>
