<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hệ thống quản lý SmartRoom - Trang quản trị nhà trọ.">
    <title>SmartRoom - Quản Trị Nhà Trọ Cao Cấp</title>
    
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
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen flex selection:bg-indigo-500 selection:text-white overflow-hidden">

    <!-- Decorative glows -->
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] rounded-full bg-indigo-600/5 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] rounded-full bg-emerald-600/5 blur-[100px] pointer-events-none"></div>

    <!-- SIDEBAR -->
    <aside class="w-64 bg-[#0d121f] border-r border-slate-900 flex flex-col justify-between h-screen shrink-0 relative z-20">
        <div>
            <!-- Sidebar Header -->
            <div class="p-6 border-b border-slate-900 flex items-center justify-between">
                <a href="{{ route('smartroom.portal') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid fa-hotel text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">SmartRoom</span>
                </a>
            </div>
            
            <!-- Navigation Links -->
            <nav class="p-4 space-y-1">
                <button onclick="switchTab('dashboard-section', this)" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-indigo-400 bg-indigo-500/10 border border-indigo-500/10 transition-all duration-200">
                    <i class="fa-solid fa-chart-pie text-lg"></i>
                    <span>Tổng Quan</span>
                </button>
                <button onclick="switchTab('room-map-section', this)" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-cubes text-lg"></i>
                    <span>Sơ Đồ Phòng</span>
                </button>
                <button onclick="switchTab('utility-section', this)" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-bolt text-lg"></i>
                    <span>Chốt Điện Nước</span>
                </button>
                <button onclick="switchTab('resident-section', this)" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-users text-lg"></i>
                    <span>Quản Lý Cư Dân</span>
                </button>
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-900">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/50 border border-slate-800/40">
                <div class="w-9 h-9 rounded-lg bg-indigo-900/50 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400 text-sm">
                    AD
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-xs font-bold text-slate-200 truncate">Nguyễn Thành Hiền</h4>
                    <p class="text-[10px] text-slate-500 truncate">Chủ chung cư mini</p>
                </div>
            </div>
            <a href="{{ route('smartroom.portal') }}" class="mt-3 w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 transition-all duration-200">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Thoát Cổng Admin
            </a>
        </div>
    </aside>

    <!-- MAIN APP WRAPPER -->
    <div class="flex-grow flex flex-col h-screen overflow-y-auto relative z-10">
        
        <!-- TOP NAVBAR -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-2">
                <h2 id="section-title" class="text-lg font-bold text-slate-100">Tổng Quan Hệ Thống</h2>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- Notifications -->
                <div class="relative">
                    <button class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                        <i class="fa-regular fa-bell"></i>
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-indigo-500"></span>
                    </button>
                </div>
                
                <!-- Quick Date -->
                <div class="text-sm font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-4 py-2 rounded-xl flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-indigo-400"></i>
                    <span>Tháng 06 / 2026</span>
                </div>
            </div>
        </header>

        <!-- CONTENT PANEL -->
        <main class="p-8 flex-grow overflow-y-auto">

            <!-- SECTION 1: DASHBOARD OVERVIEW -->
            <section id="dashboard-section" class="tab-content space-y-8 animate-fade-in">
                <!-- Stats ribbon -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tổng số phòng</span>
                            <h3 class="text-3xl font-extrabold text-slate-100 mt-2">12</h3>
                            <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-arrow-up"></i> 100% Khai thác
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center">
                            <i class="fa-solid fa-door-open text-xl"></i>
                        </div>
                    </div>
                    <!-- Occupied Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-red-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Đang thuê</span>
                            <h3 class="text-3xl font-extrabold text-red-400 mt-2">9</h3>
                            <span class="text-[10px] text-slate-400 font-semibold flex items-center gap-1 mt-1">
                                Tỉ lệ lấp đầy: 75%
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center">
                            <i class="fa-solid fa-user-check text-xl"></i>
                        </div>
                    </div>
                    <!-- Empty Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Phòng trống</span>
                            <h3 class="text-3xl font-extrabold text-emerald-400 mt-2">2</h3>
                            <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-1">
                                Sẵn sàng đón khách
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <i class="fa-solid fa-circle-plus text-xl"></i>
                        </div>
                    </div>
                    <!-- Overdue Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Chưa đóng tiền</span>
                            <h3 class="text-3xl font-extrabold text-amber-400 mt-2">1</h3>
                            <span class="text-[10px] text-amber-400 font-semibold flex items-center gap-1 mt-1">
                                Cần nhắc nhở đóng phí
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Revenue Line Chart -->
                    <div class="glass-card rounded-2xl p-6 lg:col-span-2 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-200">Xu Hướng Doanh Thu</h3>
                                <p class="text-xs text-slate-500">So sánh doanh thu các tháng gần nhất</p>
                            </div>
                            <span class="text-xs px-3 py-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 font-semibold">Doanh thu phòng trọ (VND)</span>
                        </div>
                        <div class="h-64 w-full">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    <!-- Doughnut Room Status Chart -->
                    <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                        <div class="mb-4">
                            <h3 class="text-base font-bold text-slate-200">Trạng Thái Phòng Trọ</h3>
                            <p class="text-xs text-slate-500">Tỉ lệ phần trăm trạng thái phòng</p>
                        </div>
                        <div class="h-48 w-full flex items-center justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                            <div class="p-2 rounded-xl bg-emerald-500/5 border border-emerald-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Trống</span>
                                <strong class="text-sm text-emerald-400">16.7%</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-red-500/5 border border-red-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Thuê</span>
                                <strong class="text-sm text-red-400">75%</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-amber-500/5 border border-amber-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Nợ</span>
                                <strong class="text-sm text-amber-400">8.3%</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Events Table -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Hoạt Động Gần Đây</h3>
                            <p class="text-xs text-slate-500">Các giao dịch và cập nhật mới nhất trong tháng</p>
                        </div>
                        <button class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                            Xem tất cả <i class="fa-solid fa-angle-right"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Cư dân / Phòng</th>
                                    <th class="px-6 py-4 font-bold">Loại giao dịch</th>
                                    <th class="px-6 py-4 font-bold">Số tiền</th>
                                    <th class="px-6 py-4 font-bold">Thời gian</th>
                                    <th class="px-6 py-4 font-bold">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900">
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 font-bold text-xs">
                                            101
                                        </div>
                                        <div>
                                            <strong class="text-slate-200 text-xs block">Nguyễn Văn An</strong>
                                            <span class="text-[10px] text-slate-500">Phòng 101</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-xs text-slate-400">Thanh toán hóa đơn tháng 05</td>
                                    <td class="px-6 py-4 text-emerald-400 font-bold text-xs">4,150,000đ</td>
                                    <td class="px-6 py-4 text-xs text-slate-500">Hôm nay, 08:32</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Hoàn tất (VietQR)
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-500 font-bold text-xs">
                                            202
                                        </div>
                                        <div>
                                            <strong class="text-slate-200 text-xs block">Lê Thị Bình</strong>
                                            <span class="text-[10px] text-slate-500">Phòng 202</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-xs text-slate-400">Nhập chỉ số điện nước tháng 06</td>
                                    <td class="px-6 py-4 text-slate-400 font-bold text-xs">Chốt số mới</td>
                                    <td class="px-6 py-4 text-xs text-slate-500">Hôm qua, 18:15</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                            Đã cập nhật
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500 font-bold text-xs">
                                            302
                                        </div>
                                        <div>
                                            <strong class="text-slate-200 text-xs block">Trần Văn Cường</strong>
                                            <span class="text-[10px] text-slate-500">Phòng 302</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-xs text-slate-400">Báo cáo sự cố: Nghẹt đường thoát nước máy giặt</td>
                                    <td class="px-6 py-4 text-slate-500 text-xs">Sửa chữa</td>
                                    <td class="px-6 py-4 text-xs text-slate-500">05/06/2026, 10:20</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            Mới tiếp nhận
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: VISUAL ROOM MAP -->
            <section id="room-map-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Filter buttons and color legend -->
                <div class="flex flex-wrap items-center justify-between gap-4 bg-slate-900/40 border border-slate-800/80 p-4 rounded-2xl">
                    <div class="flex items-center gap-2">
                        <button onclick="filterRooms('all')" class="room-filter-btn px-4 py-2 text-xs font-bold rounded-xl bg-indigo-600 text-white transition-all">
                            Tất cả (12)
                        </button>
                        <button onclick="filterRooms('empty')" class="room-filter-btn px-4 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Trống (2)
                        </button>
                        <button onclick="filterRooms('occupied')" class="room-filter-btn px-4 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Đã thuê (9)
                        </button>
                        <button onclick="filterRooms('overdue')" class="room-filter-btn px-4 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Nợ phí (1)
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-6 text-xs font-bold text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-emerald-500 border border-emerald-400/30"></span>
                            <span>Trống</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-red-500 border border-red-400/30"></span>
                            <span>Đã thuê</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-amber-500 border border-amber-400/30"></span>
                            <span>Nợ tiền</span>
                        </div>
                    </div>
                </div>

                <!-- Floors and room grid -->
                <div class="space-y-8">
                    <!-- Floor 3 -->
                    <div class="floor-group">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-indigo-400"></i> Tầng 3
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div onclick="openRoomDetail('301', 'occupied', 'Nguyễn Huy Hoàng', '0988777123', '3.500.000đ', '125 kWh', '40 m3')" class="room-card room-occupied glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 301</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20">Đã thuê</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Nguyễn Huy Hoàng</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 3.500.000đ</p>
                            </div>
                            <div onclick="openRoomDetail('302', 'occupied', 'Trần Văn Cường', '0901234567', '3.500.000đ', '180 kWh', '52 m3')" class="room-card room-occupied glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 302</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20">Đã thuê</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Trần Văn Cường</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 3.500.000đ</p>
                            </div>
                            <div onclick="openRoomDetail('303', 'empty', '', '', '3.500.000đ', '', '')" class="room-card room-empty glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 303</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Trống</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-500 italic mb-1">Chưa có cư dân</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 3.500.000đ</p>
                            </div>
                            <div onclick="openRoomDetail('304', 'occupied', 'Phạm Minh Hải', '0912345987', '3.500.000đ', '110 kWh', '36 m3')" class="room-card room-occupied glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 304</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20">Đã thuê</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Phạm Minh Hải</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 3.500.000đ</p>
                            </div>
                        </div>
                    </div>

                    <!-- Floor 2 -->
                    <div class="floor-group">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-indigo-400"></i> Tầng 2
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div onclick="openRoomDetail('201', 'occupied', 'Nguyễn Thị Minh', '0933355577', '3.800.000đ', '95 kWh', '30 m3')" class="room-card room-occupied glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 201</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20">Đã thuê</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Nguyễn Thị Minh</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 3.800.000đ</p>
                            </div>
                            <div onclick="openRoomDetail('202', 'overdue', 'Lê Thị Bình', '0901234567', '3.800.000đ', '210 kWh', '58 m3')" class="room-card room-overdue glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 202</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20">Nợ phí</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Lê Thị Bình</h4>
                                <p class="text-[10px] text-slate-500">Nợ tháng: 05/2026</p>
                            </div>
                            <div onclick="openRoomDetail('203', 'occupied', 'Võ Hoàng Nam', '0977888999', '3.800.000đ', '140 kWh', '45 m3')" class="room-card room-occupied glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 203</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20">Đã thuê</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Võ Hoàng Nam</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 3.800.000đ</p>
                            </div>
                            <div onclick="openRoomDetail('204', 'occupied', 'Đặng Thùy Dương', '0909090909', '3.800.000đ', '88 kWh', '28 m3')" class="room-card room-occupied glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 204</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20">Đã thuê</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Đặng Thùy Dương</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 3.800.000đ</p>
                            </div>
                        </div>
                    </div>

                    <!-- Floor 1 -->
                    <div class="floor-group">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-indigo-400"></i> Tầng 1
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div onclick="openRoomDetail('101', 'occupied', 'Nguyễn Văn An', '0912111222', '4.000.000đ', '130 kWh', '42 m3')" class="room-card room-occupied glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 101</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20">Đã thuê</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Nguyễn Văn An</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 4.000.000đ</p>
                            </div>
                            <div onclick="openRoomDetail('102', 'occupied', 'Nguyễn Thị Bích', '0944455566', '4.000.000đ', '102 kWh', '31 m3')" class="room-card room-occupied glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 102</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20">Đã thuê</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Nguyễn Thị Bích</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 4.000.000đ</p>
                            </div>
                            <div onclick="openRoomDetail('103', 'empty', '', '', '4.000.000đ', '', '')" class="room-card room-empty glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 103</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Trống</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-500 italic mb-1">Chưa có cư dân</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 4.000.000đ</p>
                            </div>
                            <div onclick="openRoomDetail('104', 'occupied', 'Nguyễn Minh Quân', '0933333333', '4.200.000đ', '165 kWh', '48 m3')" class="room-card room-occupied glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. 104</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20">Đã thuê</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: Nguyễn Minh Quân</h4>
                                <p class="text-[10px] text-slate-500">Giá phòng: 4.200.000đ</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ROOM DETAIL DRAWER/MODAL (HIDDEN BY DEFAULT) -->
                <div id="room-detail-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex justify-end items-stretch transition-opacity duration-300">
                    <div class="w-full max-w-md bg-[#0a0f1d] border-l border-slate-800 p-8 flex flex-col justify-between h-full shadow-2xl relative animate-slide-in">
                        <button onclick="closeRoomDetail()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        
                        <div class="space-y-6">
                            <!-- Room Head -->
                            <div>
                                <span class="text-xs px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-bold uppercase" id="modal-room-status-badge">Đã thuê</span>
                                <h2 class="text-2xl font-extrabold text-slate-100 mt-2" id="modal-room-title">Phòng 202</h2>
                            </div>

                            <hr class="border-slate-900">

                            <!-- Resident details -->
                            <div class="space-y-3" id="modal-resident-details">
                                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Thông tin cư dân</h3>
                                <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-800/40 space-y-2">
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Họ tên:</span> <strong class="text-slate-200" id="modal-resident-name">Lê Thị Bình</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Số điện thoại:</span> <strong class="text-slate-200" id="modal-resident-phone">0901234567</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Bắt đầu ở:</span> <strong class="text-slate-200">01/03/2025</strong></div>
                                </div>
                            </div>

                            <!-- Billing summary -->
                            <div class="space-y-3" id="modal-billing-details">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hóa đơn tháng 05/2026</h3>
                                    <span class="text-[10px] text-amber-400 font-bold" id="modal-bill-status">Chưa thanh toán</span>
                                </div>
                                <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-800/40 space-y-2">
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Tiền thuê phòng:</span> <strong class="text-slate-200" id="modal-bill-rent">3.800.000đ</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Tiền điện:</span> <strong class="text-slate-200" id="modal-bill-electric">385.000đ (110 kWh)</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Tiền nước:</span> <strong class="text-slate-200" id="modal-bill-water">120.000đ (8 m3)</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Dịch vụ (Mạng, vệ sinh):</span> <strong class="text-slate-200">150.000đ</strong></div>
                                    <hr class="border-slate-800/40 my-2">
                                    <div class="flex justify-between text-sm"><strong class="text-indigo-400">Tổng thanh toán:</strong> <strong class="text-indigo-400 font-extrabold" id="modal-bill-total">4.455.000đ</strong></div>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="space-y-3 mt-6">
                            <button id="modal-btn-action" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition-all">
                                <i class="fa-solid fa-bell-slash"></i> Gửi nhắc nợ qua Zalo/Mail
                            </button>
                            <button onclick="window.open('https://img.vietqr.io/image/970422-1234567890-compact2.jpg?amount=4455000&addInfo=Thanh%20toan%20tien%20phong%20202%20Thang%2005&accountName=NGUYEN%20THANH%20HIEN', '_blank')" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-qrcode text-indigo-400"></i> Xem mã VietQR hóa đơn
                            </button>
                            <button onclick="closeRoomDetail()" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                                Đóng lại
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 3: UTILITY RECORD INPUT -->
            <section id="utility-section" class="tab-content hidden space-y-8 animate-fade-in">
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Chốt Điện Nước Cuối Tháng</h3>
                            <p class="text-xs text-slate-500">Nhập chỉ số điện nước tháng 06/2026. Đơn giá: Điện 3.500đ/kWh, Nước 15.000đ/m3.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="submitAllUtilities()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-check-double"></i> Lưu & Xuất Hóa Đơn PDF
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Phòng</th>
                                    <th class="px-6 py-4 font-bold">Điện cũ</th>
                                    <th class="px-6 py-4 font-bold">Điện mới (kWh)</th>
                                    <th class="px-6 py-4 font-bold">Nước cũ</th>
                                    <th class="px-6 py-4 font-bold">Nước mới (m3)</th>
                                    <th class="px-6 py-4 font-bold">Số lượng xài</th>
                                    <th class="px-6 py-4 font-bold">Thành tiền tạm tính</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900" id="utility-table-body">
                                <!-- Room 101 row -->
                                <tr class="hover:bg-slate-900/10 transition-all" data-room="101" data-price="4000000">
                                    <td class="px-6 py-4 font-bold text-slate-200 flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> 101 (Văn An)
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500" data-field="old-elec">1250</td>
                                    <td class="px-6 py-4">
                                        <input type="number" oninput="calculateRowCost(this)" class="w-28 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nhập số mới">
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500" data-field="old-water">142</td>
                                    <td class="px-6 py-4">
                                        <input type="number" oninput="calculateRowCost(this)" class="w-28 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nhập số mới">
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-400">
                                        <div>⚡ Điện: <strong data-field="used-elec">0</strong> kWh</div>
                                        <div>💧 Nước: <strong data-field="used-water">0</strong> m3</div>
                                    </td>
                                    <td class="px-6 py-4 text-indigo-400 font-bold text-xs" data-field="cost-total">0đ</td>
                                </tr>
                                <!-- Room 202 row -->
                                <tr class="hover:bg-slate-900/10 transition-all" data-room="202" data-price="3800000">
                                    <td class="px-6 py-4 font-bold text-slate-200 flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> 202 (Thị Bình)
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500" data-field="old-elec">3420</td>
                                    <td class="px-6 py-4">
                                        <input type="number" oninput="calculateRowCost(this)" class="w-28 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nhập số mới">
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500" data-field="old-water">285</td>
                                    <td class="px-6 py-4">
                                        <input type="number" oninput="calculateRowCost(this)" class="w-28 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nhập số mới">
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-400">
                                        <div>⚡ Điện: <strong data-field="used-elec">0</strong> kWh</div>
                                        <div>💧 Nước: <strong data-field="used-water">0</strong> m3</div>
                                    </td>
                                    <td class="px-6 py-4 text-indigo-400 font-bold text-xs" data-field="cost-total">0đ</td>
                                </tr>
                                <!-- Room 302 row -->
                                <tr class="hover:bg-slate-900/10 transition-all" data-room="302" data-price="3500000">
                                    <td class="px-6 py-4 font-bold text-slate-200 flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> 302 (Văn Cường)
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500" data-field="old-elec">1900</td>
                                    <td class="px-6 py-4">
                                        <input type="number" oninput="calculateRowCost(this)" class="w-28 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nhập số mới">
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500" data-field="old-water">198</td>
                                    <td class="px-6 py-4">
                                        <input type="number" oninput="calculateRowCost(this)" class="w-28 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nhập số mới">
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-400">
                                        <div>⚡ Điện: <strong data-field="used-elec">0</strong> kWh</div>
                                        <div>💧 Nước: <strong data-field="used-water">0</strong> m3</div>
                                    </td>
                                    <td class="px-6 py-4 text-indigo-400 font-bold text-xs" data-field="cost-total">0đ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- SECTION 4: RESIDENT MANAGEMENT -->
            <section id="resident-section" class="tab-content hidden space-y-8 animate-fade-in">
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Quản Lý Danh Sách Cư Dân</h3>
                            <p class="text-xs text-slate-500">Thêm mới, cập nhật thông tin và quản lý hợp đồng thuê phòng trọ.</p>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="resident-search-input" onkeyup="searchResidentTable()" class="px-4 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:outline-none" placeholder="Tìm tên cư dân / số phòng...">
                            <button onclick="toggleAddResidentModal(true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                                <i class="fa-solid fa-plus"></i> Thêm Cư Dân
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Họ tên</th>
                                    <th class="px-6 py-4 font-bold">Phòng</th>
                                    <th class="px-6 py-4 font-bold">Số điện thoại</th>
                                    <th class="px-6 py-4 font-bold">Ngày bắt đầu ở</th>
                                    <th class="px-6 py-4 font-bold">Trạng thái hợp đồng</th>
                                    <th class="px-6 py-4 font-bold text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900" id="resident-table-body">
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-6 py-4 font-bold text-slate-200">Nguyễn Văn An</td>
                                    <td class="px-6 py-4 text-xs font-semibold text-indigo-400">P. 101</td>
                                    <td class="px-6 py-4 text-xs text-slate-400">0912111222</td>
                                    <td class="px-6 py-4 text-xs text-slate-500">01/01/2024</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Đang hoạt động</span>
                                    </td>
                                    <td class="px-6 py-4 flex gap-2 justify-center">
                                        <button class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-xs font-bold text-indigo-400 transition-all"><i class="fa-regular fa-pen-to-square"></i> Sửa</button>
                                        <button onclick="deleteRow(this)" class="px-2.5 py-1.5 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 rounded-lg text-xs font-bold text-rose-400 transition-all"><i class="fa-regular fa-trash-can"></i> Xóa</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-6 py-4 font-bold text-slate-200">Lê Thị Bình</td>
                                    <td class="px-6 py-4 text-xs font-semibold text-indigo-400">P. 202</td>
                                    <td class="px-6 py-4 text-xs text-slate-400">0901234567</td>
                                    <td class="px-6 py-4 text-xs text-slate-500">15/03/2025</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Đang hoạt động</span>
                                    </td>
                                    <td class="px-6 py-4 flex gap-2 justify-center">
                                        <button class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-xs font-bold text-indigo-400 transition-all"><i class="fa-regular fa-pen-to-square"></i> Sửa</button>
                                        <button onclick="deleteRow(this)" class="px-2.5 py-1.5 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 rounded-lg text-xs font-bold text-rose-400 transition-all"><i class="fa-regular fa-trash-can"></i> Xóa</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-6 py-4 font-bold text-slate-200">Trần Văn Cường</td>
                                    <td class="px-6 py-4 text-xs font-semibold text-indigo-400">P. 302</td>
                                    <td class="px-6 py-4 text-xs text-slate-400">0901234567</td>
                                    <td class="px-6 py-4 text-xs text-slate-500">10/10/2025</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Đang hoạt động</span>
                                    </td>
                                    <td class="px-6 py-4 flex gap-2 justify-center">
                                        <button class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-xs font-bold text-indigo-400 transition-all"><i class="fa-regular fa-pen-to-square"></i> Sửa</button>
                                        <button onclick="deleteRow(this)" class="px-2.5 py-1.5 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 rounded-lg text-xs font-bold text-rose-400 transition-all"><i class="fa-regular fa-trash-can"></i> Xóa</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- ADD RESIDENT MODAL (POPUP) -->
    <div id="add-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-lg bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4">
            <button onclick="toggleAddResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Cư Dân Mới
            </h2>
            <form onsubmit="submitAddResident(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Họ và tên</label>
                        <input type="text" id="add-res-name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="Nguyễn Văn A">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại</label>
                        <input type="tel" id="add-res-phone" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="09xxxxxxxx">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chọn phòng trọ</label>
                        <select id="add-res-room" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="103">Phòng 103 (Trống)</option>
                            <option value="303">Phòng 303 (Trống)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày bắt đầu ở</label>
                        <input type="date" id="add-res-date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleAddResidentModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Xác Nhận Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        // Tab switching
        function switchTab(tabId, btn) {
            // Hide all sections
            document.querySelectorAll('.tab-content').forEach(section => {
                section.classList.add('hidden');
            });
            // Show active section
            document.getElementById(tabId).classList.remove('hidden');
            
            // Remove active style from all nav buttons
            document.querySelectorAll('.nav-btn').forEach(button => {
                button.classList.remove('text-indigo-400', 'bg-indigo-500/10', 'border-indigo-500/10');
                button.classList.add('text-slate-400', 'hover:text-slate-100', 'hover:bg-slate-800/50', 'border-transparent', 'hover:border-slate-800');
            });
            
            // Add active style to current button
            btn.classList.remove('text-slate-400', 'hover:text-slate-100', 'hover:bg-slate-800/50', 'border-transparent', 'hover:border-slate-800');
            btn.classList.add('text-indigo-400', 'bg-indigo-500/10', 'border-indigo-500/10');
            
            // Set header title
            let title = "SmartRoom Dashboard";
            if(tabId === 'dashboard-section') title = "Tổng Quan Hệ Thống";
            else if(tabId === 'room-map-section') title = "Sơ Đồ Phòng Trực Quan";
            else if(tabId === 'utility-section') title = "Chốt Chỉ Số Điện Nước";
            else if(tabId === 'resident-section') title = "Quản Lý Cư Dân";
            document.getElementById('section-title').textContent = title;
        }

        // Room filter function
        function filterRooms(status) {
            // Set active button style
            document.querySelectorAll('.room-filter-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('bg-slate-900', 'text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800');
            });
            event.currentTarget.classList.remove('bg-slate-900', 'text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800');
            event.currentTarget.classList.add('bg-indigo-600', 'text-white');

            // Show/hide cards
            document.querySelectorAll('.room-card').forEach(card => {
                if (status === 'all') {
                    card.classList.remove('hidden');
                } else {
                    if (card.classList.contains('room-' + status)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                }
            });
        }

        // Room Detail modal triggers
        function openRoomDetail(roomNum, status, name, phone, rent, elec, water) {
            const modal = document.getElementById('room-detail-modal');
            const title = document.getElementById('modal-room-title');
            const badge = document.getElementById('modal-room-status-badge');
            
            title.textContent = "Phòng " + roomNum;
            badge.textContent = status === 'empty' ? 'Trống' : (status === 'overdue' ? 'Nợ phí' : 'Đã thuê');
            
            // Set badge classes
            badge.className = "text-xs px-2.5 py-1 rounded-md font-bold uppercase border ";
            if(status === 'empty') badge.className += "bg-emerald-500/10 text-emerald-400 border-emerald-500/20";
            else if(status === 'occupied') badge.className += "bg-red-500/10 text-red-400 border-red-500/20";
            else badge.className += "bg-amber-500/10 text-amber-400 border-amber-500/20";

            // If empty, hide resident and billing details
            const resDetails = document.getElementById('modal-resident-details');
            const billDetails = document.getElementById('modal-billing-details');
            const actionBtn = document.getElementById('modal-btn-action');
            
            if(status === 'empty') {
                resDetails.classList.add('hidden');
                billDetails.classList.add('hidden');
                actionBtn.classList.add('hidden');
            } else {
                resDetails.classList.remove('hidden');
                billDetails.classList.remove('hidden');
                actionBtn.classList.remove('hidden');
                
                document.getElementById('modal-resident-name').textContent = name;
                document.getElementById('modal-resident-phone').textContent = phone;
                document.getElementById('modal-bill-rent').textContent = rent;
                document.getElementById('modal-bill-electric').textContent = status === 'overdue' ? '385.000đ (110 kWh)' : '350.000đ (100 kWh)';
                document.getElementById('modal-bill-water').textContent = status === 'overdue' ? '120.000đ (8 m3)' : '105.000đ (7 m3)';
                
                const billStatusBadge = document.getElementById('modal-bill-status');
                if(status === 'overdue') {
                    billStatusBadge.textContent = 'Chưa thanh toán';
                    billStatusBadge.className = 'text-[10px] text-amber-400 font-bold px-2 py-0.5 bg-amber-500/10 border border-amber-500/20 rounded';
                    actionBtn.innerHTML = '<i class="fa-solid fa-bell"></i> Gửi nhắc nợ Zalo & SMS';
                    actionBtn.className = "w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-600/30 transition-all";
                } else {
                    billStatusBadge.textContent = 'Đã thanh toán';
                    billStatusBadge.className = 'text-[10px] text-emerald-400 font-bold px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 rounded';
                    actionBtn.innerHTML = '<i class="fa-solid fa-check"></i> Đã đóng tiền tháng này';
                    actionBtn.className = "w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-slate-500 bg-slate-900 border border-slate-800 cursor-not-allowed";
                }
            }

            modal.classList.remove('hidden');
        }

        function closeRoomDetail() {
            document.getElementById('room-detail-modal').classList.add('hidden');
        }

        // Live calculation for electricity & water utility row
        function calculateRowCost(input) {
            const row = input.closest('tr');
            const roomPrice = parseInt(row.getAttribute('data-price'));
            
            const oldElec = parseInt(row.querySelector('[data-field="old-elec"]').textContent);
            const newElecInput = row.querySelectorAll('input')[0].value;
            const newElec = newElecInput ? parseInt(newElecInput) : 0;
            
            const oldWater = parseInt(row.querySelector('[data-field="old-water"]').textContent);
            const newWaterInput = row.querySelectorAll('input')[1].value;
            const newWater = newWaterInput ? parseInt(newWaterInput) : 0;

            let elecUsed = 0;
            let waterUsed = 0;
            
            if(newElec > oldElec) elecUsed = newElec - oldElec;
            if(newWater > oldWater) waterUsed = newWater - oldWater;

            row.querySelector('[data-field="used-elec"]').textContent = elecUsed;
            row.querySelector('[data-field="used-water"]').textContent = waterUsed;

            // Compute costs
            const elecCost = elecUsed * 3500;
            const waterCost = waterUsed * 15000;
            const serviceCost = 150000; // default service fee
            const total = roomPrice + elecCost + waterCost + serviceCost;

            row.querySelector('[data-field="cost-total"]').textContent = total.toLocaleString('vi-VN') + "đ";
        }

        function submitAllUtilities() {
            alert('Đã cập nhật chỉ số điện nước thành công! Hệ thống đã gửi tin nhắn thông báo hóa đơn kèm mã VietQR tới từng phòng.');
        }

        // Resident search
        function searchResidentTable() {
            const query = document.getElementById('resident-search-input').value.toLowerCase();
            const rows = document.getElementById('resident-table-body').querySelectorAll('tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if(text.includes(query)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        // Resident delete simulation
        function deleteRow(btn) {
            if(confirm('Bạn có chắc chắn muốn xóa cư dân này ra khỏi phòng trọ?')) {
                btn.closest('tr').remove();
            }
        }

        // Resident add modal triggers
        function toggleAddResidentModal(show) {
            const modal = document.getElementById('add-resident-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function submitAddResident(e) {
            e.preventDefault();
            const name = document.getElementById('add-res-name').value;
            const phone = document.getElementById('add-res-phone').value;
            const room = document.getElementById('add-res-room').value;
            const dateStr = document.getElementById('add-res-date').value;
            
            const date = new Date(dateStr);
            const formattedDate = date.getDate().toString().padStart(2, '0') + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getFullYear();

            // Insert row to resident table
            const tbody = document.getElementById('resident-table-body');
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-900/30 transition-all";
            tr.innerHTML = `
                <td class="px-6 py-4 font-bold text-slate-200">${name}</td>
                <td class="px-6 py-4 text-xs font-semibold text-indigo-400">P. ${room}</td>
                <td class="px-6 py-4 text-xs text-slate-400">${phone}</td>
                <td class="px-6 py-4 text-xs text-slate-500">${formattedDate}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Đang hoạt động</span>
                </td>
                <td class="px-6 py-4 flex gap-2 justify-center">
                    <button class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-xs font-bold text-indigo-400 transition-all"><i class="fa-regular fa-pen-to-square"></i> Sửa</button>
                    <button onclick="deleteRow(this)" class="px-2.5 py-1.5 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 rounded-lg text-xs font-bold text-rose-400 transition-all"><i class="fa-regular fa-trash-can"></i> Xóa</button>
                </td>
            `;
            tbody.appendChild(tr);
            
            // Update the room card status in Sơ đồ phòng
            const roomCards = document.querySelectorAll('.room-card');
            roomCards.forEach(card => {
                if (card.querySelector('span').textContent.trim() === 'P. ' + room) {
                    // Update card design
                    card.classList.remove('room-empty');
                    card.classList.add('room-occupied');
                    
                    const badge = card.querySelector('span:nth-child(2)');
                    badge.textContent = 'Đã thuê';
                    badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20';
                    
                    card.querySelector('h4').textContent = 'Cư dân: ' + name;
                    card.querySelector('h4').className = 'text-xs font-bold text-slate-400 truncate mb-1';
                    
                    // Attach click handler with new details
                    card.onclick = function() {
                        openRoomDetail(room, 'occupied', name, phone, '4.000.000đ', '0 kWh', '0 m3');
                    };
                }
            });

            // Close modal
            toggleAddResidentModal(false);
            alert(`Đã thêm thành công cư dân ${name} vào phòng ${room}!`);
            
            // Clear inputs
            document.getElementById('add-res-name').value = '';
            document.getElementById('add-res-phone').value = '';
            document.getElementById('add-res-date').value = '';
        }

        // CHARTS INITIALIZATION
        window.addEventListener('DOMContentLoaded', () => {
            // Revenue Chart
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            const gradRev = ctxRevenue.createLinearGradient(0, 0, 0, 300);
            gradRev.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
            gradRev.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: ['Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6 (Dự tính)'],
                    datasets: [{
                        label: 'Doanh thu phòng trọ (VND)',
                        data: [31500000, 34200000, 33900000, 38100000],
                        borderColor: '#6366f1',
                        borderWidth: 3,
                        pointBackgroundColor: '#818cf8',
                        pointBorderColor: '#6366f1',
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        fill: true,
                        backgroundColor: gradRev,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.03)' },
                            ticks: {
                                color: '#64748b',
                                font: { size: 10, weight: 'bold' },
                                callback: function(value) { return (value / 1000000) + 'M'; }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#64748b',
                                font: { size: 10, weight: 'bold' }
                            }
                        }
                    }
                }
            });

            // Status Pie Chart
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Trống', 'Đang thuê', 'Nợ tiền'],
                    datasets: [{
                        data: [2, 9, 1],
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                        borderColor: '#0a0f1d',
                        borderWidth: 4,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
</body>
</html>
