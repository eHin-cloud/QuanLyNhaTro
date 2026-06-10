<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cấu Hình Bảo Mật - SmartRoom Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #030712; /* slate-950 */
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(51, 65, 85, 0.5);
        }
        .glass-panel:hover {
            border-color: rgba(16, 185, 129, 0.4);
        }
        .glow-circle {
            filter: blur(140px);
            opacity: 0.12;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">
    @include('admin.partials.sidebar')

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        <!-- Background Elements -->
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-emerald-600 glow-circle"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-teal-600 glow-circle"></div>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 relative z-10 flex-grow">
        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 pb-6 border-b border-slate-900">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/10 text-emerald-400 rounded-lg border border-emerald-500/20">
                        Security Control
                    </span>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white mt-2 flex items-center gap-3">
                    <i class="fa-solid fa-sliders text-emerald-400"></i> Cấu Hình Bảo Mật Hệ Thống
                </h1>
                <p class="text-xs text-slate-400">
                    Cấu hình tham số bảo mật JIT Unlock và cài đặt yêu cầu xác thực đa yếu tố đa thiết bị.
                </p>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap items-center gap-2 mb-8 bg-slate-900/40 p-1.5 rounded-xl border border-slate-800/80 max-w-max">
            <a href="{{ route('admin.verifications.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.verifications.index') ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <i class="fa-solid fa-file-signature text-sky-400"></i> Duyệt Hồ Sơ
            </a>
            <a href="{{ route('admin.audit-logs') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.audit-logs') ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <i class="fa-solid fa-shield-halved text-indigo-400"></i> Nhật Ký Audit
            </a>
            <a href="{{ route('admin.settings') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.settings') ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <i class="fa-solid fa-sliders text-emerald-400"></i> Cấu Hình Bảo Mật
            </a>
            <a href="{{ route('admin.analytics') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.analytics') ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <i class="fa-solid fa-chart-pie text-pink-400"></i> Thống Kê Hệ Thống
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-base"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8">
            <!-- Form Config -->
            <div class="glass-panel rounded-2xl p-8 transition-all duration-300">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Setting 1: TTL -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-hourglass-end text-emerald-400"></i> Thời gian sống của liên kết mở khóa (TTL)
                                </h3>
                                <p class="text-xs text-slate-400 mt-1">
                                    Số giây mà liên kết giải mã tài liệu nhạy cảm (presigned URL) còn hiệu lực sau khi được phê duyệt JIT Unlock.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 max-w-xs">
                            <input type="number" name="presigned_url_ttl_seconds" value="{{ $settings['presigned_url_ttl_seconds'] ?? 300 }}" min="10" max="86400" required class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-3 px-4 text-xs font-mono font-bold text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                            <span class="text-xs font-bold text-slate-400 uppercase">Giây</span>
                        </div>
                        @error('presigned_url_ttl_seconds')
                            <p class="text-[11px] text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-slate-800/80">

                    <!-- Setting 2: Passkey Require Toggle -->
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-6">
                            <div class="space-y-1">
                                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-fingerprint text-emerald-400"></i> Bắt buộc bảo mật sinh trắc học (Passkey)
                                </h3>
                                <p class="text-xs text-slate-400">
                                    Nếu bật, **tất cả** Admin bắt buộc phải đăng ký và xác thực bằng vân tay/khuôn mặt trên thiết bị để thực hiện JIT Unlock. Nếu tắt, Admin chưa đăng ký Passkey vẫn có thể mở khóa bằng cách cung cấp lý do.
                                </p>
                            </div>
                            <div class="relative inline-flex items-center cursor-pointer select-none">
                                <select name="require_passkey" class="bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 px-4 text-xs font-bold text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                                    <option value="1" {{ ($settings['require_passkey'] ?? true) ? 'selected' : '' }}>Bật (Bắt buộc)</option>
                                    <option value="0" {{ !($settings['require_passkey'] ?? true) ? 'selected' : '' }}>Tắt (Tùy chọn)</option>
                                </select>
                            </div>
                        </div>
                        @error('require_passkey')
                            <p class="text-[11px] text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-800/60 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-6 py-3 text-xs font-bold text-white transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu cấu hình bảo mật
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Advisory Banner -->
            <div class="p-6 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-350 space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-sm text-amber-400"></i> Khuyến nghị bảo mật hệ thống
                </h4>
                <ul class="list-disc list-inside text-[11px] space-y-2 leading-relaxed">
                    <li>Thời gian TTL của liên kết giải mã tài liệu nên được thiết lập ngắn (từ **60 đến 300 giây**) để tránh rò rỉ khi admin sao chép URL hoặc chia sẻ link ra ngoài.</li>
                    <li>Nên **Bật chế độ bắt buộc Passkey** trên môi trường Production để ngăn chặn hoàn toàn nguy cơ tài khoản admin bị hack/brute-force mật khẩu rồi đọc lén dữ liệu KYC.</li>
                    <li>Tất cả các thay đổi cấu hình bảo mật tại đây sẽ được áp dụng ngay lập tức mà không cần khởi động lại máy chủ.</li>
                </ul>
            </div>
        </div>
        </main>
    </div>
    <script src="{{ asset('js/admin-sidebar.js') }}"></script>
</body>
</html>
