<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SmartRoom & Renty Review - Hệ thống số hóa quản lý phòng trọ và tìm kiếm đánh giá nhà trọ hàng đầu.">
    <title>SmartRoom & Renty Review - Hệ Thống Quản Lý & Tìm Kiếm Phòng Trọ</title>
    <script>
        if (localStorage.getItem('renty_theme_mode') === 'light') {
            document.documentElement.classList.add('theme-light');
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0b0f19] text-slate-100 min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-indigo-500 selection:text-white">
    
    <!-- Decorative background elements -->
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-600/10 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-emerald-600/10 blur-[120px] pointer-events-none"></div>

    <!-- Header / Navbar -->
    <header class="container mx-auto px-6 py-6 flex justify-between items-center relative z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <i class="fa-solid fa-hotel text-white text-lg"></i>
            </div>
            <span class="app-brand-text text-xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">SmartRoom <span class="text-indigo-400">&</span> Renty</span>
        </div>
        <div class="flex items-center gap-4">
            <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                <i class="fa-solid fa-moon" data-theme-icon></i>
            </button>
            @auth
                <div class="flex items-center gap-3 bg-slate-900/40 backdrop-blur-xl border border-slate-800/80 px-3.5 py-1.5 rounded-full text-xs">
                    <span class="font-bold text-indigo-400">
                        <i class="fa-solid fa-user-circle mr-1"></i> {{ Auth::user()->name }} ({{ Auth::user()->role === 'admin' ? 'Chủ trọ' : 'Người thuê' }})
                    </span>
                    <a href="{{ route('signout') }}" class="font-semibold text-rose-400 hover:text-rose-300 transition-colors">
                        Đăng xuất
                    </a>
                </div>
            @else
                <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-full bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 hover:text-slate-100 transition-all">
                    Đăng Nhập
                </a>
            @endauth
            <span class="text-xs px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 font-semibold uppercase tracking-wider hidden sm:inline-block">Phiên bản 1.0</span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12 flex-grow flex flex-col items-center justify-center relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16 animate-fade-in">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 leading-tight">
                Giải Pháp <span class="bg-gradient-to-r from-indigo-400 via-violet-400 to-emerald-400 bg-clip-text text-transparent">Số Hóa & Đánh Giá</span> Không Gian Sống
            </h1>
            <p class="text-slate-400 text-base md:text-lg leading-relaxed max-w-2xl mx-auto">
                Hệ thống tích hợp tối ưu cho cả Chủ nhà trọ quản lý chung cư mini (SmartRoom) và Người thuê nhà tìm kiếm, đánh giá không gian sống (Renty Review).
            </p>
        </div>

        <!-- Portals Grid -->
        <div class="grid md:grid-cols-2 gap-8 max-w-5xl w-full mx-auto px-4">
            
            <!-- PORTAL 1: ADMIN (SMARTROOM) -->
            <a href="{{ route('smartroom.admin') }}" id="portal-admin" class="group relative bg-slate-900/40 backdrop-blur-xl border border-slate-800 hover:border-indigo-500/50 rounded-3xl p-8 transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_50px_rgba(99,102,241,0.15)] flex flex-col justify-between overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-600/10 rounded-full blur-2xl group-hover:bg-indigo-600/20 transition-all duration-500"></div>
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-all duration-300">
                        <i class="fa-solid fa-chart-line text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-3 group-hover:text-indigo-400 transition-colors duration-300">SmartRoom Admin</h2>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6">
                        Dành cho Chủ nhà / Quản lý tòa nhà. Quản lý sơ đồ phòng trực quan, biểu đồ doanh thu chi tiết, tự động chốt số điện nước và quản lý thông tin cư dân thông minh.
                    </p>
                    <ul class="space-y-2.5 text-xs text-slate-400 mb-8">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-indigo-400 text-[10px]"></i> Sơ đồ phòng màu sắc trực quan (Trống, Đã thuê, Nợ tiền)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-indigo-400 text-[10px]"></i> Nhập điện nước tự động tính tiền thông minh</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-indigo-400 text-[10px]"></i> Biểu đồ doanh thu trực quan, quản lý cư dân chuyên nghiệp</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-indigo-400 text-[10px]"></i> Ký hợp đồng điện tử E-Contract bằng chữ ký tay trực tuyến</li>
                    </ul>
                </div>
                <div class="flex items-center gap-2 text-indigo-400 font-semibold text-sm group-hover:gap-4 transition-all duration-300">
                    Truy cập Dashboard Admin <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- PORTAL 2: USER (RENTY REVIEW) -->
            <a href="{{ route('renty.user') }}" id="portal-user" class="group relative bg-slate-900/40 backdrop-blur-xl border border-slate-800 hover:border-emerald-500/50 rounded-3xl p-8 transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_50px_rgba(16,185,129,0.15)] flex flex-col justify-between overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-600/10 rounded-full blur-2xl group-hover:bg-emerald-600/20 transition-all duration-500"></div>
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-all duration-300">
                        <i class="fa-solid fa-magnifying-glass-location text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-3 group-hover:text-emerald-400 transition-colors duration-300">Renty Tenant Hub</h2>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6">
                        Dành cho Khách thuê / Người tìm trọ. Tìm kiếm thông tin phòng trọ quanh trường đại học, bộ lọc tìm kiếm nâng cao, so sánh phòng trọ đa chiều và đánh giá thực tế từ người dùng cũ.
                    </p>
                    <ul class="space-y-2.5 text-xs text-slate-400 mb-8">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i> Bộ lọc tìm kiếm nâng cao thông minh</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i> Đánh giá đa chiều (Chủ nhà, An ninh, Điện nước, Tiện ích)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i> So sánh so kèo phòng trọ trực quan, tiện lợi</li>
                    </ul>
                </div>
                <div class="flex items-center gap-2 text-emerald-400 font-semibold text-sm group-hover:gap-4 transition-all duration-300">
                    Khám phá Cổng Tìm Kiếm <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 bg-slate-950/50 py-6 relative z-10">
        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <div>
                © 2026 SmartRoom & Renty Review. Đồ án xây dựng hệ thống quản lý & số hóa nhà trọ.
            </div>

        </div>
    </footer>

</body>
</html>
