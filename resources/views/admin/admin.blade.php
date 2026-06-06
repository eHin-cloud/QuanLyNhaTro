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
                <a href="{{ route('admin.rooms.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-door-open text-lg"></i>
                    <span>Cấu hình phòng</span>
                </a>
                <button onclick="switchTab('utility-section', this)" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-bolt text-lg"></i>
                    <span>Chốt Điện Nước</span>
                </button>
                <button onclick="switchTab('resident-section', this)" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-users text-lg"></i>
                    <span>Quản Lý Cư Dân</span>
                </button>
                <button onclick="switchTab('contract-section', this)" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-file-signature text-lg"></i>
                    <span>Hợp Đồng Online</span>
                </button>
                <button onclick="switchTab('contact-section', this)" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-phone-volume text-lg"></i>
                    <span>Yêu Cầu Tư Vấn</span>
                    @if($contactRequests->where('status', 'pending')->count() > 0)
                        <span class="ml-auto bg-rose-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full animate-pulse">
                            {{ $contactRequests->where('status', 'pending')->count() }}
                        </span>
                    @endif
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
            <a href="{{ route('signout') }}" class="mt-3 w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 transition-all duration-200">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất (Thoát Admin)
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

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-semibold flex items-center gap-2 animate-fade-in">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center gap-2 animate-fade-in">
                    <i class="fa-solid fa-circle-exclamation text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- SECTION 1: DASHBOARD OVERVIEW -->
            <section id="dashboard-section" class="tab-content space-y-8 animate-fade-in">
                <!-- Stats ribbon -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tổng số phòng</span>
                            <h3 class="text-3xl font-extrabold text-slate-100 mt-2">{{ $totalRooms }}</h3>
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
                            <h3 class="text-3xl font-extrabold text-red-400 mt-2">{{ $occupiedRooms }}</h3>
                            <span class="text-[10px] text-slate-400 font-semibold flex items-center gap-1 mt-1">
                                Tỉ lệ lấp đầy: {{ $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0 }}%
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
                            <h3 class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $emptyRooms }}</h3>
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
                            <h3 class="text-3xl font-extrabold text-amber-400 mt-2">{{ $overdueRooms }}</h3>
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
                    <!-- Revenue Line & Doughnut Split Chart -->
                    <div class="glass-card rounded-2xl p-6 lg:col-span-2 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-900/60 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-200">Phân Tích Doanh Thu Hệ Thống</h3>
                                <p class="text-xs text-slate-500">Báo cáo xu hướng cột và cơ cấu nguồn thu từ điện, nước, phòng</p>
                            </div>
                            <span class="text-[10px] px-2.5 py-1 rounded bg-indigo-500/10 text-indigo-400 font-bold border border-indigo-500/20 uppercase tracking-wider">Doanh thu tháng 06</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                            <!-- Left: Trend Bar Chart -->
                            <div class="md:col-span-3 flex flex-col justify-between">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Xu Hướng Doanh Thu</span>
                                <div class="h-56 w-full">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Right: Doughnut Breakdown Chart -->
                            <div class="md:col-span-2 flex flex-col justify-between border-t md:border-t-0 md:border-l border-slate-800/50 pt-4 md:pt-0 md:pl-6">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Cơ Cấu Nguồn Thu</span>
                                <div class="h-36 w-full flex items-center justify-center relative">
                                    <canvas id="revenueBreakdownChart"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                                        <span class="text-[8px] text-slate-500 font-bold uppercase tracking-wider">Tổng thu</span>
                                        <span class="text-xs font-black text-indigo-400" id="breakdown-total-txt">--M</span>
                                    </div>
                                </div>
                                <div class="space-y-1.5 mt-3 text-[10px]">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>Tiền phòng</span>
                                        <strong class="text-slate-200" id="breakdown-room-pct">--%</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Tiền điện</span>
                                        <strong class="text-slate-200" id="breakdown-elec-pct">--%</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>Tiền nước</span>
                                        <strong class="text-slate-200" id="breakdown-water-pct">--%</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Dịch vụ</span>
                                        <strong class="text-slate-200" id="breakdown-service-pct">--%</strong>
                                    </div>
                                </div>
                            </div>
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
                                <strong class="text-sm text-emerald-400">{{ $totalRooms > 0 ? round(($emptyRooms / $totalRooms) * 100, 1) : 0 }}%</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-red-500/5 border border-red-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Thuê</span>
                                <strong class="text-sm text-red-400">{{ $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0 }}%</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-amber-500/5 border border-amber-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Nợ</span>
                                <strong class="text-sm text-amber-400">{{ $totalRooms > 0 ? round(($overdueRooms / $totalRooms) * 100, 1) : 0 }}%</strong>
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
                                @foreach($rooms->where('status', '!=', 'empty')->take(3) as $r)
                                @php
                                    $latestRecord = $r->utilityRecords->first();
                                    $resident = $r->residents->first();
                                @endphp
                                @if($resident)
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 font-bold text-xs">
                                            {{ $r->room_number }}
                                        </div>
                                        <div>
                                            <strong class="text-slate-200 text-xs block">{{ $resident->name }}</strong>
                                            <span class="text-[10px] text-slate-500">Phòng {{ $r->room_number }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-xs text-slate-400">
                                        {{ $r->status === 'overdue' ? 'Gửi hóa đơn tháng ' . ($latestRecord ? explode('-', $latestRecord->billing_month)[1] : '06') : 'Đã đóng hóa đơn tháng ' . ($latestRecord ? explode('-', $latestRecord->billing_month)[1] : '05') }}
                                    </td>
                                    <td class="px-6 py-4 {{ $r->status === 'overdue' ? 'text-amber-400' : 'text-emerald-400' }} font-bold text-xs">
                                        @if($latestRecord)
                                            {{ number_format($r->price + ($latestRecord->new_electricity - $latestRecord->old_electricity) * $latestRecord->electricity_price + ($latestRecord->new_water - $latestRecord->old_water) * $latestRecord->water_price + 150000) }}đ
                                        @else
                                            {{ number_format($r->price + 150000) }}đ
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $latestRecord ? $latestRecord->updated_at->diffForHumans() : 'Hôm nay' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($r->status === 'overdue')
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Chưa đóng</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Hoàn tất</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
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
                            Tất cả ({{ $totalRooms }})
                        </button>
                        <button onclick="filterRooms('empty')" class="room-filter-btn px-4 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Trống ({{ $emptyRooms }})
                        </button>
                        <button onclick="filterRooms('occupied')" class="room-filter-btn px-4 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Đã thuê ({{ $occupiedRooms }})
                        </button>
                        <button onclick="filterRooms('overdue')" class="room-filter-btn px-4 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Nợ phí ({{ $overdueRooms }})
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
                    @foreach($roomsByFloor as $floor => $floorRooms)
                    <div class="floor-group">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-indigo-400"></i> Tầng {{ $floor }}
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            @foreach($floorRooms as $room)
                            @php
                                $resident = $room->residents->first();
                                $latestBill = $room->utilityRecords->first();
                                $elecUsed = $latestBill ? ($latestBill->new_electricity - $latestBill->old_electricity) : 0;
                                $waterUsed = $latestBill ? ($latestBill->new_water - $latestBill->old_water) : 0;
                                $statusLabel = $room->status === 'empty' ? 'Trống' : ($room->status === 'overdue' ? 'Nợ phí' : 'Đã thuê');
                                $statusClass = $room->status === 'empty' ? 'room-empty border-emerald-500/20' : ($room->status === 'overdue' ? 'room-overdue border-amber-500/20' : 'room-occupied border-red-500/20');
                                $badgeClass = $room->status === 'empty' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : ($room->status === 'overdue' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20');
                                $totalBill = $latestBill ? ($room->price + ($elecUsed * $latestBill->electricity_price) + ($waterUsed * $latestBill->water_price) + 150000) : 0;
                            @endphp
                            <div onclick="openRoomDetail('{{ $room->room_number }}', '{{ $room->status }}', '{{ $resident ? $resident->name : '' }}', '{{ $resident ? $resident->phone : '' }}', '{{ number_format($room->price) }}đ', '{{ $elecUsed }} kWh', '{{ $waterUsed }} m3', '{{ number_format($totalBill) }}đ', '{{ $latestBill ? $latestBill->id : 'null' }}')" 
                                 class="room-card {{ $statusClass }} glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. {{ $room->room_number }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold border {{ $badgeClass }}">{{ $statusLabel }}</span>
                                </div>
                                @if($resident)
                                    <h4 class="text-xs font-bold text-slate-400 truncate mb-1">Cư dân: {{ $resident->name }}</h4>
                                    <p class="text-[10px] text-slate-500">Giá phòng: {{ number_format($room->price) }}đ</p>
                                @else
                                    <h4 class="text-xs font-bold text-slate-500 italic mb-1">Chưa có cư dân</h4>
                                    <p class="text-[10px] text-slate-500">Giá phòng: {{ number_format($room->price) }}đ</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
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
                            <form id="modal-pay-form" action="" method="POST" class="hidden">
                                @csrf
                            </form>
                            <form id="modal-notify-form" action="" method="POST" class="hidden">
                                @csrf
                            </form>
                            <button id="modal-btn-pay" onclick="submitModalPay()" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-600/30 transition-all hidden">
                                <i class="fa-solid fa-circle-check"></i> Xác nhận đã đóng tiền
                            </button>
                            <button id="modal-btn-action" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition-all">
                                <i class="fa-solid fa-bell-slash"></i> Gửi nhắc nợ qua Zalo/Mail
                            </button>
                            <button id="modal-btn-qr" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-qrcode text-indigo-400"></i> Xem mã VietQR hóa đơn
                            </button>
                            <button id="modal-btn-print" onclick="printModalInvoice()" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all hidden">
                                <i class="fa-solid fa-print text-indigo-400"></i> In hóa đơn / Xuất PDF
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
                            <button type="button" onclick="triggerAutoRemind(this)" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-rose-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-bell animate-bounce"></i> Tự Động Nhắc Nợ Zalo Hàng Loạt
                            </button>
                            <button type="submit" form="bulk-utility-form" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-check-double"></i> Lưu & Xuất Hóa Đơn Hàng Loạt
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <form id="bulk-utility-form" action="{{ route('smartroom.admin.utility.bulk_store') }}" method="POST">
                            @csrf
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
                                        <th class="px-6 py-4 font-bold text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-900" id="utility-table-body">
                                    @foreach($utilityRooms as $room)
                                    @php
                                        $resident = $room->residents->first();
                                        $latestBill = $room->utilityRecords->first();
                                        $currentMonth = \Carbon\Carbon::now()->format('Y-m');
                                        
                                        if ($latestBill && $latestBill->billing_month === $currentMonth) {
                                            $oldElec = $latestBill->old_electricity;
                                            $oldWater = $latestBill->old_water;
                                            $newElec = $latestBill->new_electricity;
                                            $newWater = $latestBill->new_water;
                                        } else {
                                            $oldElec = $latestBill ? $latestBill->new_electricity : 0;
                                            $oldWater = $latestBill ? $latestBill->new_water : 0;
                                            $newElec = '';
                                            $newWater = '';
                                        }
                                        $statusColor = $room->status === 'overdue' ? 'bg-amber-500' : ($room->status === 'empty' ? 'bg-emerald-500' : 'bg-red-500');
                                    @endphp
                                    <tr class="hover:bg-slate-900/10 transition-all" data-room="{{ $room->room_number }}" data-price="{{ $room->price }}">
                                        <td class="px-6 py-4 font-bold text-slate-200 flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full {{ $statusColor }}"></span> 
                                            {{ $room->room_number }} ({{ $resident ? $resident->name : 'N/A' }})
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500" data-field="old-elec">{{ $oldElec }}</td>
                                        <td class="px-6 py-4">
                                            <input type="number" name="utilities[{{ $room->id }}][new_electricity]" value="{{ $newElec }}" oninput="calculateRowCost(this)" class="new-elec-input w-28 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nhập số mới">
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500" data-field="old-water">{{ $oldWater }}</td>
                                        <td class="px-6 py-4">
                                            <input type="number" name="utilities[{{ $room->id }}][new_water]" value="{{ $newWater }}" oninput="calculateRowCost(this)" class="new-water-input w-28 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nhập số mới">
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-400">
                                            <div>⚡ Điện: <strong data-field="used-elec">0</strong> kWh</div>
                                            <div>💧 Nước: <strong data-field="used-water">0</strong> m3</div>
                                        </td>
                                        <td class="px-6 py-4 text-indigo-400 font-bold text-xs" data-field="cost-total">0đ</td>
                                        <td class="px-6 py-4 text-center">
                                             <div class="flex items-center justify-center gap-2">
                                                 @if($room->status === 'overdue' && $latestBill && $latestBill->status !== 'paid')
                                                     <form action="{{ route('smartroom.admin.utility.pay', $latestBill->id) }}" method="POST" class="inline">
                                                         @csrf
                                                         <button type="submit" class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded-lg text-xs font-bold border border-emerald-500/20 transition-all flex items-center gap-1">
                                                             <i class="fa-solid fa-check"></i> Xác nhận đóng
                                                         </button>
                                                     </form>
                                                     <a href="{{ route('smartroom.admin.utility.print', $latestBill->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-bold border border-slate-700 transition-all flex items-center gap-1" title="In hóa đơn">
                                                         <i class="fa-solid fa-print"></i> In
                                                     </a>
                                                 @else
                                                     <button type="button" onclick="saveSingleUtility('{{ $room->id }}', this)" class="px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white rounded-lg text-xs font-bold border border-indigo-500/20 transition-all">
                                                         <i class="fa-solid fa-save"></i> Lưu số
                                                     </button>
                                                 @endif
                                             </div>
                                         </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </section>

            <!-- SECTION 4: RESIDENT MANAGEMENT (Quản lý khách trọ) -->
            <section id="resident-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Stat Cards cho Trạng thái tạm trú -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tổng cư dân</p>
                                <h3 class="text-2xl font-extrabold text-white mt-1">{{ $residents->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400"><i class="fa-solid fa-users"></i></div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Đã đăng ký tạm trú</p>
                                <h3 class="text-2xl font-extrabold text-emerald-400 mt-1">{{ $residents->where('temporary_residence_status', 'registered')->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400"><i class="fa-solid fa-clipboard-check"></i></div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tạm vắng</p>
                                <h3 class="text-2xl font-extrabold text-amber-400 mt-1">{{ $residents->where('temporary_residence_status', 'absent')->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400"><i class="fa-solid fa-user-clock"></i></div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(239,68,68,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-rose-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Chưa đăng ký</p>
                                <h3 class="text-2xl font-extrabold text-rose-400 mt-1">{{ $residents->where('temporary_residence_status', 'none')->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400"><i class="fa-solid fa-user-xmark"></i></div>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Quản Lý Danh Sách Cư Dân</h3>
                            <p class="text-xs text-slate-500">Lưu trữ thông tin cá nhân, đăng ký tạm trú, quản lý người thân tạm trú.</p>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="resident-search-input" onkeyup="searchResidentTable()" class="px-4 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:outline-none" placeholder="Tìm tên / CCCD / phòng...">
                            <button onclick="toggleAddResidentModal(true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                                <i class="fa-solid fa-plus"></i> Thêm Cư Dân
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                <tr>
                                    <th class="px-4 py-4 font-bold">Họ tên</th>
                                    <th class="px-4 py-4 font-bold">Phòng</th>
                                    <th class="px-4 py-4 font-bold">SĐT</th>
                                    <th class="px-4 py-4 font-bold">CCCD</th>
                                    <th class="px-4 py-4 font-bold">Quê quán</th>
                                    <th class="px-4 py-4 font-bold">Tạm trú</th>
                                    <th class="px-4 py-4 font-bold">Ngày vào ở</th>
                                    <th class="px-4 py-4 font-bold text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900" id="resident-table-body">
                                @foreach($residents as $resident)
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-4 py-4 font-bold text-slate-200">{{ $resident->name }}</td>
                                    <td class="px-4 py-4 text-xs font-semibold text-indigo-400">P. {{ $resident->room ? $resident->room->room_number : 'N/A' }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-400 font-mono">{{ $resident->phone }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-400 font-mono">{{ $resident->cccd ?? '—' }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-500 max-w-[120px] truncate" title="{{ $resident->hometown }}">{{ $resident->hometown ?? '—' }}</td>
                                    <td class="px-4 py-4">
                                        @if($resident->temporary_residence_status === 'registered')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Đã đăng ký
                                            </span>
                                        @elseif($resident->temporary_residence_status === 'absent')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Tạm vắng
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Chưa ĐK
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-500">{{ \Carbon\Carbon::parse($resident->start_date)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex gap-1.5 justify-center flex-wrap">
                                            {{-- Nút Xem chi tiết --}}
                                            <button onclick="openViewResidentModal({{ $resident->id }}, '{{ addslashes($resident->name) }}', '{{ $resident->phone }}', '{{ $resident->email }}', '{{ $resident->room ? $resident->room->room_number : 'N/A' }}', '{{ \Carbon\Carbon::parse($resident->start_date)->format('d/m/Y') }}', '{{ $resident->status === 'active' ? 'Đang hoạt động' : 'Tạm ngưng' }}', '{{ $resident->dob }}', '{{ $resident->cccd }}', '{{ addslashes($resident->hometown) }}', '{{ $resident->temporary_residence_status }}')" class="px-2 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-[10px] font-bold text-emerald-400 transition-all" title="Xem chi tiết">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                            {{-- Nút Sửa thông tin --}}
                                            <button onclick="openEditResidentModal('{{ $resident->id }}', '{{ addslashes($resident->name) }}', '{{ $resident->phone }}', '{{ $resident->room_id }}', '{{ $resident->start_date }}', '{{ $resident->dob }}', '{{ $resident->cccd }}', '{{ addslashes($resident->hometown) }}', '{{ $resident->temporary_residence_status }}', '{{ $resident->version }}')" class="px-2 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-[10px] font-bold text-indigo-400 transition-all" title="Sửa thông tin">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </button>
                                            {{-- Nút Quản lý người thân --}}
                                            <button onclick="openRelativesModal({{ $resident->id }}, '{{ addslashes($resident->name) }}')" class="px-2 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-[10px] font-bold text-cyan-400 transition-all" title="Quản lý người thân tạm trú">
                                                <i class="fa-solid fa-people-roof"></i>
                                            </button>
                                            {{-- Nút Xóa cư dân - có chống spam click (disabled sau click) --}}
                                            <form action="{{ route('smartroom.admin.resident.delete', $resident->id) }}" method="POST" onsubmit="return confirmAndDisable(this, 'Bạn có chắc chắn muốn xóa cư dân này ra khỏi phòng trọ?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="anti-spam-btn px-2 py-1.5 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 rounded-lg text-[10px] font-bold text-rose-400 transition-all" title="Xóa cư dân">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- SECTION 5: CONTRACT MANAGEMENT -->
            <section id="contract-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Premium Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tổng số hợp đồng</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contracts->count() }}</h3>
                                <span class="text-[10px] text-indigo-400 font-semibold mt-1 block">Tất cả bản ghi</span>
                            </div>
                            <span class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-file-contract text-lg"></i>
                            </span>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Hợp đồng hiệu lực</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contracts->where('status', 'active')->count() }}</h3>
                                <span class="text-[10px] text-emerald-400 font-semibold mt-1 block">Đã có chữ ký</span>
                            </div>
                            <span class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                            </span>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Chờ cư dân ký</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contracts->where('status', 'pending')->count() }}</h3>
                                <span class="text-[10px] text-amber-400 font-semibold mt-1 block">Yêu cầu chữ ký</span>
                            </div>
                            <span class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-signature text-lg"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Contract List Glass Card -->
                <div class="glass-card rounded-2xl p-6 border border-slate-800/40 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-file-invoice-dollar text-indigo-400"></i> Danh Sách Hợp Đồng Online
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Khởi tạo hợp đồng thuê nhà điện tử và theo dõi trạng thái ký trực tuyến.</p>
                        </div>
                        <div>
                            <button onclick="toggleAddContractModal(true)" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-indigo-600/25 hover:-translate-y-0.5">
                                <i class="fa-solid fa-plus-circle"></i> Tạo Hợp Đồng Mới
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Mã Hợp Đồng</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Phòng Trọ</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Cư Dân Đại Diện</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tiền Cọc</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Thời Hạn Thuê</th>
                                    <th class="px-6 py-4 font-bold text-center tracking-wider">Trạng Thái</th>
                                    <th class="px-6 py-4 font-bold text-center tracking-wider">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                                @forelse($contracts as $c)
                                <tr class="hover:bg-slate-900/40 transition-all group">
                                    <td class="px-6 py-4 font-bold text-slate-200 group-hover:text-indigo-400 transition-colors">{{ $c->contract_code }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded bg-indigo-500/10 text-indigo-400 text-xs font-bold border border-indigo-500/20">
                                            Phòng {{ $c->room ? $c->room->room_number : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-200 text-xs">{{ $c->resident ? $c->resident->name : 'N/A' }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5"><i class="fa-solid fa-phone text-[8px] mr-1"></i>{{ $c->resident ? $c->resident->phone : '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold text-emerald-400">{{ number_format($c->deposit) }}đ</td>
                                    <td class="px-6 py-4 text-xs text-slate-400">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-calendar-alt text-[10px] text-slate-500"></i>
                                            <span>{{ date('d/m/Y', strtotime($c->start_date)) }} - {{ date('d/m/Y', strtotime($c->end_date)) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($c->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                Đã ký hiệu lực
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Chờ chữ ký
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($c->status === 'pending')
                                                <button onclick="copySignLink('{{ route('smartroom.contract.sign_view', $c->id, false) }}', this)" class="px-3 py-2 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white rounded-xl text-xs font-bold border border-indigo-500/20 transition-all flex items-center gap-1.5">
                                                    <i class="fa-solid fa-link"></i> Link ký
                                                </button>
                                                <button onclick="openSendMsgModal('{{ $c->resident ? $c->resident->phone : '' }}', '{{ $c->resident ? $c->resident->name : '' }}', 'Kính gửi anh/chị {{ $c->resident ? $c->resident->name : '' }}, vui lòng truy cập đường link sau để hoàn tất ký kết hợp đồng thuê phòng {{ $c->room ? $c->room->room_number : '' }}: ' + window.location.origin + '{{ route('smartroom.contract.sign_view', $c->id, false) }}', 'contract')" class="px-3 py-2 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded-xl text-xs font-bold border border-emerald-500/20 transition-all flex items-center gap-1.5">
                                                    <i class="fa-solid fa-paper-plane"></i> Gửi Zalo/SMS
                                                </button>
                                            @endif
                                            <a href="{{ route('smartroom.contract.sign_view', $c->id) }}" target="_blank" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold border border-slate-750 transition-all flex items-center gap-1.5">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem HĐ
                                            </a>
                                            <form action="{{ route('smartroom.admin.contract.delete', $c->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hợp đồng này không?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-2 bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white rounded-xl text-xs font-bold border border-rose-500/25 transition-all">
                                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <i class="fa-solid fa-folder-open text-2xl text-slate-700"></i>
                                            <span>Không tìm thấy hợp đồng nào. Hãy tạo hợp đồng mới!</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- SECTION 6: CONTACT REQUESTS -->
            <section id="contact-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tổng số yêu cầu</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contactRequests->count() }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                                <i class="fa-solid fa-phone-volume text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Chưa xử lý</p>
                                <h3 class="text-3xl font-extrabold text-amber-400 mt-2 tracking-tight">{{ $contactRequests->where('status', 'pending')->count() }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400">
                                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Đã liên hệ</p>
                                <h3 class="text-3xl font-extrabold text-indigo-400 mt-2 tracking-tight">{{ $contactRequests->where('status', 'processed')->count() }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                                <i class="fa-solid fa-square-check text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consultation Requests Table -->
                <div class="glass-card rounded-3xl border border-slate-900 overflow-hidden shadow-2xl">
                    <div class="p-6 border-b border-slate-900 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-200">Danh sách đăng ký tư vấn</h2>
                            <p class="text-xs text-slate-500 mt-1">Các yêu cầu xem phòng và đăng ký tư vấn từ khách thuê trên Renty Hub</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4">Khách hàng</th>
                                    <th class="px-6 py-4">Số điện thoại</th>
                                    <th class="px-6 py-4">Phòng quan tâm</th>
                                    <th class="px-6 py-4">Lời nhắn</th>
                                    <th class="px-6 py-4">Ngày đăng ký</th>
                                    <th class="px-6 py-4">Trạng thái</th>
                                    <th class="px-6 py-4 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900">
                                @forelse($contactRequests as $req)
                                <tr class="hover:bg-slate-900/40 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-250">{{ $req->name }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-indigo-300">{{ $req->phone }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-900 border border-slate-800 text-slate-300">
                                            Phòng {{ $req->room->room_number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs max-w-xs truncate" title="{{ $req->message }}">
                                        {{ $req->message ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        @if($req->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Chờ xử lý
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                Đã liên hệ
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="tel:{{ $req->phone }}" class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all text-xs" title="Gọi điện">
                                                <i class="fa-solid fa-phone"></i>
                                            </a>
                                            <a href="https://zalo.me/{{ $req->phone }}" target="_blank" class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all text-xs" title="Chat Zalo">
                                                <i class="fa-solid fa-comment-sms"></i>
                                            </a>
                                            <form action="{{ route('smartroom.admin.contact_request.status', $req->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $req->status === 'pending' ? 'processed' : 'pending' }}">
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all text-xs" title="Đổi trạng thái">
                                                    <i class="fa-solid {{ $req->status === 'pending' ? 'fa-check' : 'fa-rotate-left' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('smartroom.admin.contact_request.delete', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa yêu cầu tư vấn này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all text-xs" title="Xóa">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <i class="fa-solid fa-phone-slash text-2xl text-slate-700"></i>
                                            <span>Chưa có yêu cầu tư vấn hay đăng ký xem phòng nào.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- ADD RESIDENT MODAL (POPUP) - Mở rộng thêm thông tin cá nhân & tạm trú -->
    <div id="add-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleAddResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Cư Dân Mới
            </h2>
            <!-- Chống spam click: form submit sẽ disable nút sau lần click đầu tiên -->
            <form action="{{ route('smartroom.admin.resident.store') }}" method="POST" class="space-y-4" onsubmit="return antiSpamSubmit(this)">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Họ và tên *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="Nguyễn Văn A">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="09xxxxxxxx">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày sinh</label>
                        <input type="date" name="dob" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số CCCD</label>
                        <input type="text" name="cccd" maxlength="12" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="0xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Quê quán</label>
                        <input type="text" name="hometown" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="TP. Hồ Chí Minh">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chọn phòng *</label>
                        <select name="room_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            @foreach($emptyRoomsList as $room)
                                <option value="{{ $room->id }}">P. {{ $room->room_number }} (Trống)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày vào ở *</label>
                        <input type="date" name="start_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tạm trú *</label>
                        <select name="temporary_residence_status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="none">Chưa đăng ký</option>
                            <option value="registered">Đã đăng ký</option>
                            <option value="absent">Tạm vắng</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleAddResidentModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="anti-spam-btn px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Xác Nhận Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT RESIDENT MODAL - Có Optimistic Locking (version) để chặn ghi đè xung đột -->
    <div id="edit-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleEditResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-indigo-400"></i> Chỉnh Sửa Thông Tin Cư Dân
            </h2>
            <form id="edit-resident-form" action="" method="POST" class="space-y-4" onsubmit="return antiSpamSubmit(this)">
                @csrf
                @method('PUT')
                <!-- Optimistic Locking: trường version ẩn để server so sánh phiên bản trước khi cập nhật -->
                <input type="hidden" name="version" id="edit-version" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Họ và tên *</label>
                        <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại *</label>
                        <input type="tel" name="phone" id="edit-phone" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày sinh</label>
                        <input type="date" name="dob" id="edit-dob" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số CCCD</label>
                        <input type="text" name="cccd" id="edit-cccd" maxlength="12" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Quê quán</label>
                        <input type="text" name="hometown" id="edit-hometown" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Phòng trọ *</label>
                        <select name="room_id" id="edit-room-id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}">P. {{ $r->room_number }} ({{ $r->status === 'empty' ? 'Trống' : 'Đang thuê' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày vào ở *</label>
                        <input type="date" name="start_date" id="edit-start-date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tạm trú *</label>
                        <select name="temporary_residence_status" id="edit-temp-status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="none">Chưa đăng ký</option>
                            <option value="registered">Đã đăng ký</option>
                            <option value="absent">Tạm vắng</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleEditResidentModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="anti-spam-btn px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- VIEW RESIDENT MODAL (POPUP) - Hiển thị chi tiết cư dân & danh sách người thân đi cùng -->
    <div id="view-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleViewResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-address-card text-indigo-400"></i> Thông Tin Chi Tiết Cư Dân
            </h2>
            <div class="space-y-6">
                <!-- Thông tin cá nhân -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-indigo-400 text-[10px]"></i> Thông tin cá nhân
                    </h3>
                    <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-900/50 border border-slate-800/40">
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Họ và tên:</span>
                                <strong class="text-slate-200" id="view-name">Nguyễn Văn A</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Số điện thoại:</span>
                                <strong class="text-slate-200 font-mono" id="view-phone">09xxxxxxxx</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Email liên hệ:</span>
                                <strong class="text-slate-200" id="view-email">email@gmail.com</strong>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Ngày sinh:</span>
                                <strong class="text-slate-200 font-mono" id="view-dob">—</strong>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Phòng thuê:</span>
                                <strong class="text-indigo-400 font-bold" id="view-room">Phòng 101</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Số CCCD:</span>
                                <strong class="text-slate-200 font-mono" id="view-cccd">—</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Quê quán:</span>
                                <strong class="text-slate-200" id="view-hometown">—</strong>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Đăng ký tạm trú:</span>
                                <span id="view-temp-status" class="px-2 py-0.5 rounded text-[10px] font-bold">Chưa ĐK</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách người thân -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-people-group text-cyan-400 text-[10px]"></i> Người thân tạm trú cùng
                        </h3>
                        <button id="view-manage-relatives-btn" onclick="" class="text-[10px] font-bold text-cyan-400 hover:text-cyan-300 transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-cog"></i> Quản lý
                        </button>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-800/40 min-h-[80px]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-300 hidden" id="view-relatives-table">
                                <thead class="text-slate-500 uppercase font-bold text-[10px] border-b border-slate-800">
                                    <tr>
                                        <th class="pb-2">Họ tên</th>
                                        <th class="pb-2">Quan hệ</th>
                                        <th class="pb-2">CCCD</th>
                                        <th class="pb-2">Trạng thái tạm trú</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/40" id="view-relatives-tbody">
                                    <!-- AJAX insert relatives here -->
                                </tbody>
                            </table>
                            <div class="text-slate-500 text-center py-4" id="view-relatives-empty">
                                <i class="fa-solid fa-folder-open text-lg mb-1 block"></i>
                                Không có thông tin người thân tạm trú.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="toggleViewResidentModal(false)" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MANAGE RELATIVES MODAL (POPUP) - Quản lý người thân tạm trú qua AJAX -->
    <div id="relatives-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleRelativesModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-2 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-people-roof text-cyan-400"></i> Quản Lý Người Thân Tạm Trú
            </h2>
            <p class="text-xs text-slate-500 mb-6">Của cư dân: <strong class="text-slate-300 font-bold" id="relatives-modal-resident-name">—</strong></p>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Danh sách người thân hiện có -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-list text-cyan-400"></i> Danh sách người thân trọ cùng
                    </h3>
                    <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-800/60 min-h-[200px] max-h-[350px] overflow-y-auto space-y-2" id="relatives-list-container">
                        <!-- AJAX generated list here -->
                    </div>
                </div>

                <!-- Form Thêm / Sửa người thân -->
                <div class="lg:col-span-2 space-y-4 border-t lg:border-t-0 lg:border-l border-slate-800/80 pt-4 lg:pt-0 lg:pl-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1" id="relative-form-title">
                        <i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Người Thân Mới
                    </h3>
                    
                    <form id="relative-ajax-form" onsubmit="saveRelative(event)" class="space-y-3">
                        <input type="hidden" id="relative-id" value="">
                        <!-- version của relative cho Optimistic Locking -->
                        <input type="hidden" id="relative-version" value="1">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Họ và tên *</label>
                            <input type="text" id="relative-name" required class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nguyễn Văn B">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mối quan hệ *</label>
                                <input type="text" id="relative-relationship" required class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Bố, mẹ, vợ, con...">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Ngày sinh</label>
                                <input type="date" id="relative-dob" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Số CCCD</label>
                            <input type="text" id="relative-cccd" maxlength="12" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="12 số">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Quê quán</label>
                            <input type="text" id="relative-hometown" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Địa chỉ quê quán">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Trạng thái đăng ký tạm trú *</label>
                            <select id="relative-temp-status" required class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none">
                                <option value="none">Chưa đăng ký</option>
                                <option value="registered">Đã đăng ký</option>
                                <option value="absent">Tạm vắng</option>
                            </select>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" id="relative-reset-btn" onclick="resetRelativeForm()" class="hidden w-1/2 py-2 rounded-lg text-xs font-bold text-slate-400 bg-transparent hover:bg-slate-900 border border-slate-800 transition-all">
                                Hủy sửa
                            </button>
                            <button type="submit" id="relative-submit-btn" class="w-full py-2 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-save"></i> <span>Lưu Lại</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="pt-6 mt-6 border-t border-slate-900 flex justify-end">
                <button type="button" onclick="toggleRelativesModal(false)" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-300 bg-slate-900 border border-slate-800 hover:border-slate-700 transition-all">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- ADD CONTRACT MODAL (POPUP) -->
    <div id="add-contract-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4">
            <button onclick="toggleAddContractModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-indigo-400"></i> Tạo Hợp Đồng Thuê Nhà Mới
            </h2>
            <form action="{{ route('smartroom.admin.contract.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chọn Cư Dân Đại Diện</label>
                        <select name="resident_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            @foreach($residents as $res)
                                <option value="{{ $res->id }}">{{ $res->name }} (P. {{ $res->room ? $res->room->room_number : 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chọn Phòng Trọ</label>
                        <select name="room_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            @foreach($rooms as $r)
                                @if($r->status !== 'empty')
                                    <option value="{{ $r->id }}">Phòng {{ $r->room_number }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày Bắt Đầu</label>
                        <input type="date" name="start_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày Kết Thúc</label>
                        <input type="date" name="end_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tiền Đặt Cọc (VNĐ)</label>
                        <input type="number" name="deposit" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="3000000">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Điều Khoản Hợp Đồng</label>
                    <textarea name="terms" rows="6" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none resize-none">ĐIỀU KHOẢN HỢP ĐỒNG THUÊ PHÒNG

Điều 1: Bên A (Bên cho thuê) đồng ý cho Bên B (Bên thuê) thuê phòng trọ.
Điều 2: Tiền thuê phòng đóng định kỳ trước ngày 10 hàng tháng. Tiền đặt cọc bảo đảm nghĩa vụ thực hiện hợp đồng.
Điều 3: Bên thuê cam kết bảo quản tài sản phòng trọ, tuân thủ các quy định phòng chống cháy nổ và khai báo tạm trú theo quy định của pháp luật.</textarea>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleAddContractModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy Bỏ
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Tạo Hợp Đồng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        // Setup custom toast notification override for alert()
        (function() {
            const toastStyle = document.createElement('style');
            toastStyle.innerHTML = `
                .custom-toast-container {
                    position: fixed;
                    top: 24px;
                    right: 24px;
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    pointer-events: none;
                }
                .custom-toast {
                    min-width: 320px;
                    max-width: 450px;
                    background: rgba(15, 23, 42, 0.9);
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 16px;
                    padding: 16px 20px;
                    color: #f1f5f9;
                    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3), 0 0 1px 1px rgba(255, 255, 255, 0.05);
                    display: flex;
                    align-items: flex-start;
                    gap: 14px;
                    pointer-events: auto;
                    transform: translateX(120%);
                    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                .custom-toast.show {
                    transform: translateX(0);
                }
                .custom-toast.hide {
                    transform: translateX(120%);
                    opacity: 0;
                    margin-top: -60px;
                }
                .custom-toast-icon {
                    flex-shrink: 0;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 13px;
                }
                .custom-toast-success .custom-toast-icon {
                    background: rgba(16, 185, 129, 0.15);
                    color: #10b981;
                    border: 1px solid rgba(16, 185, 129, 0.2);
                }
                .custom-toast-warning .custom-toast-icon {
                    background: rgba(245, 158, 11, 0.15);
                    color: #f59e0b;
                    border: 1px solid rgba(245, 158, 11, 0.2);
                }
                .custom-toast-error .custom-toast-icon {
                    background: rgba(239, 68, 68, 0.15);
                    color: #ef4444;
                    border: 1px solid rgba(239, 68, 68, 0.2);
                }
                .custom-toast-info .custom-toast-icon {
                    background: rgba(59, 130, 246, 0.15);
                    color: #3b82f6;
                    border: 1px solid rgba(59, 130, 246, 0.2);
                }
                .custom-toast-content {
                    flex-grow: 1;
                }
                .custom-toast-title {
                    font-size: 13px;
                    font-weight: 700;
                    margin-bottom: 3px;
                    letter-spacing: 0.3px;
                }
                .custom-toast-message {
                    font-size: 12px;
                    color: #94a3b8;
                    line-height: 1.5;
                    white-space: pre-wrap;
                }
                .custom-toast-close {
                    color: #64748b;
                    cursor: pointer;
                    font-size: 14px;
                    transition: color 0.2s;
                    margin-top: 1px;
                }
                .custom-toast-close:hover {
                    color: #94a3b8;
                }
            `;
            document.head.appendChild(toastStyle);

            window.alert = function(message) {
                let type = 'success';
                let title = 'Thông Báo';
                
                const lowerMsg = message.toLowerCase();
                if (lowerMsg.includes('lỗi') || 
                    lowerMsg.includes('không thể') || 
                    lowerMsg.includes('thất bại') || 
                    lowerMsg.includes('chưa') || 
                    lowerMsg.includes('không được') || 
                    lowerMsg.includes('chỉ được') || 
                    lowerMsg.includes('nhỏ hơn') ||
                    lowerMsg.includes('vui lòng')) {
                    type = 'warning';
                    title = 'Cảnh Báo';
                } else if (lowerMsg.includes('thành công') || 
                           lowerMsg.includes('tuyệt vời') || 
                           lowerMsg.includes('đã') || 
                           lowerMsg.includes('sao chép')) {
                    type = 'success';
                    title = 'Thành Công';
                } else {
                    type = 'info';
                    title = 'Thông Tin';
                }
                
                let container = document.querySelector('.custom-toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'custom-toast-container';
                    document.body.appendChild(container);
                }
                
                const toast = document.createElement('div');
                toast.className = `custom-toast custom-toast-${type}`;
                
                let iconHtml = '';
                if (type === 'success') iconHtml = '<i class="fa-solid fa-check"></i>';
                else if (type === 'warning') iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
                else if (type === 'error') iconHtml = '<i class="fa-solid fa-circle-xmark"></i>';
                else iconHtml = '<i class="fa-solid fa-info"></i>';
                
                toast.innerHTML = `
                    <div class="custom-toast-icon">${iconHtml}</div>
                    <div class="custom-toast-content">
                        <div class="custom-toast-title">${title}</div>
                        <div class="custom-toast-message">${message}</div>
                    </div>
                    <div class="custom-toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 400);"><i class="fa-solid fa-xmark"></i></div>
                `;
                
                container.appendChild(toast);
                
                setTimeout(() => toast.classList.add('show'), 10);
                
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.classList.remove('show');
                        toast.classList.add('hide');
                        setTimeout(() => toast.remove(), 400);
                    }
                }, 4500);
            };
        })();

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
            else if(tabId === 'contract-section') title = "Quản Lý Hợp Đồng Online";
            else if(tabId === 'contact-section') title = "Yêu Cầu Tư Vấn & Xem Phòng";
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

        function toggleAddContractModal(show) {
            const modal = document.getElementById('add-contract-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function copySignLink(url, btn) {
            const fullUrl = url.startsWith('http') ? url : (window.location.origin + url);
            navigator.clipboard.writeText(fullUrl).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Đã sao chép!';
                btn.classList.remove('text-indigo-400', 'bg-indigo-600/20');
                btn.classList.add('text-emerald-400', 'bg-emerald-600/20');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('text-emerald-400', 'bg-emerald-600/20');
                    btn.classList.add('text-indigo-400', 'bg-indigo-600/20');
                }, 2000);
            }).catch(err => {
                console.error('Lỗi khi sao chép: ', err);
            });
        }

        let currentBillId = null;

        function printModalInvoice() {
            if (currentBillId) {
                window.open(`/smartroom/admin/utility/${currentBillId}/print`, '_blank');
            } else {
                alert('Không tìm thấy hóa đơn hợp lệ để in!');
            }
        }

        // Room Detail modal triggers
        function openRoomDetail(roomNum, status, name, phone, rent, elec, water, total, latestBillId) {
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
            const payBtn = document.getElementById('modal-btn-pay');
            const printBtn = document.getElementById('modal-btn-print');
            
            if(status === 'empty') {
                resDetails.classList.add('hidden');
                billDetails.classList.add('hidden');
                actionBtn.classList.add('hidden');
                payBtn.classList.add('hidden');
                printBtn.classList.add('hidden');
                currentBillId = null;
            } else {
                resDetails.classList.remove('hidden');
                billDetails.classList.remove('hidden');
                actionBtn.classList.remove('hidden');
                
                document.getElementById('modal-resident-name').textContent = name;
                document.getElementById('modal-resident-phone').textContent = phone;
                document.getElementById('modal-bill-rent').textContent = rent;
                document.getElementById('modal-bill-electric').textContent = elec;
                document.getElementById('modal-bill-water').textContent = water;
                document.getElementById('modal-bill-total').textContent = total || rent;
                
                const billStatusBadge = document.getElementById('modal-bill-status');
                const qrBtn = document.getElementById('modal-btn-qr');
                const rawAmount = (total || rent).replace(/\D/g, '');
                
                if (qrBtn) {
                    qrBtn.onclick = function() {
                        if (latestBillId && latestBillId !== 'null') {
                            showVietQR(latestBillId);
                        } else {
                            showVietQRFallback(roomNum, rawAmount);
                        }
                    };
                }

                if (latestBillId && latestBillId !== 'null') {
                    currentBillId = latestBillId;
                    printBtn.classList.remove('hidden');
                } else {
                    currentBillId = null;
                    printBtn.classList.add('hidden');
                }

                if(status === 'overdue') {
                    billStatusBadge.textContent = 'Chưa thanh toán';
                    billStatusBadge.className = 'text-[10px] text-amber-400 font-bold px-2 py-0.5 bg-amber-500/10 border border-amber-500/20 rounded';
                    actionBtn.innerHTML = '<i class="fa-solid fa-bell"></i> Gửi nhắc nợ Zalo & SMS';
                    actionBtn.className = "w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-600/30 transition-all cursor-pointer";
                    
                    if (latestBillId && latestBillId !== 'null') {
                        payBtn.classList.remove('hidden');
                        document.getElementById('modal-pay-form').action = `/smartroom/admin/utility/${latestBillId}/pay`;
                        document.getElementById('modal-notify-form').action = `/smartroom/admin/utility/${latestBillId}/notify`;
                        actionBtn.onclick = function() {
                            openSendMsgModal(phone, name, `Kính gửi anh/chị ${name}, ban quản lý thông báo hóa đơn dịch vụ tháng 06 phòng ${roomNum} chưa được thanh toán với tổng số tiền là ${total}. Vui lòng thanh toán sớm nhất có thể. Trân trọng!`, 'debt');
                        };
                    } else {
                        payBtn.classList.add('hidden');
                        actionBtn.onclick = null;
                    }
                } else {
                    billStatusBadge.textContent = 'Đã thanh toán';
                    billStatusBadge.className = 'text-[10px] text-emerald-400 font-bold px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 rounded';
                    actionBtn.innerHTML = '<i class="fa-solid fa-check"></i> Đã đóng tiền tháng này';
                    actionBtn.className = "w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-slate-500 bg-slate-900 border border-slate-800 cursor-not-allowed";
                    payBtn.classList.add('hidden');
                    actionBtn.onclick = null;
                }
            }

            modal.classList.remove('hidden');
        }

        function syncInputs(btn) {
            const row = btn.closest('tr');
            const newElecInput = row.querySelector('.new-elec-input');
            const newWaterInput = row.querySelector('.new-water-input');
            const newElecVal = newElecInput.value;
            const newWaterVal = newWaterInput.value;
            
            const oldElec = parseInt(row.querySelector('[data-field="old-elec"]').textContent);
            const oldWater = parseInt(row.querySelector('[data-field="old-water"]').textContent);
            
            if (!newElecVal || !newWaterVal) {
                alert('Vui lòng nhập đầy đủ số điện và nước mới!');
                event.preventDefault();
                return;
            }
            
            if (parseInt(newElecVal) < oldElec || parseInt(newWaterVal) < oldWater) {
                alert('Số mới không được nhỏ hơn số cũ!');
                event.preventDefault();
                return;
            }
            
            row.querySelector('.form-new-elec').value = newElecVal;
            row.querySelector('.form-new-water').value = newWaterVal;
        }

        function closeRoomDetail() {
            document.getElementById('room-detail-modal').classList.add('hidden');
        }

        function submitModalPay() {
            if (confirm('Xác nhận cư dân đã thanh toán hóa đơn này?')) {
                document.getElementById('modal-pay-form').submit();
            }
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

        function saveSingleUtility(roomId, btn) {
            const row = btn.closest('tr');
            const newElecInput = row.querySelector('.new-elec-input');
            const newWaterInput = row.querySelector('.new-water-input');
            const newElecVal = newElecInput.value;
            const newWaterVal = newWaterInput.value;
            
            const oldElec = parseInt(row.querySelector('[data-field="old-elec"]').textContent);
            const oldWater = parseInt(row.querySelector('[data-field="old-water"]').textContent);
            
            if (!newElecVal || !newWaterVal) {
                alert('Vui lòng nhập đầy đủ số điện và nước mới!');
                return;
            }
            
            if (parseInt(newElecVal) < oldElec || parseInt(newWaterVal) < oldWater) {
                alert('Số mới không được nhỏ hơn số cũ!');
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('smartroom.admin.utility.store') }}";
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            form.appendChild(csrf);
            
            const roomInp = document.createElement('input');
            roomInp.type = 'hidden';
            roomInp.name = 'room_id';
            roomInp.value = roomId;
            form.appendChild(roomInp);
            
            const elecInp = document.createElement('input');
            elecInp.type = 'hidden';
            elecInp.name = 'new_electricity';
            elecInp.value = newElecVal;
            form.appendChild(elecInp);
            
            const waterInp = document.createElement('input');
            waterInp.type = 'hidden';
            waterInp.name = 'new_water';
            waterInp.value = newWaterVal;
            form.appendChild(waterInp);
            
            document.body.appendChild(form);
            form.submit();
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

        // ==========================================
        // 1. CƠ CHẾ KHÓA ĐỒNG THỜI (OPTIMISTIC LOCKING)
        // Giải thích: Server và Client truyền tay nhau thuộc tính 'version' (phiên bản dữ liệu).
        // Khi Landlord sửa dữ liệu cư dân, giá trị version hiện tại sẽ được gửi kèm lên Server.
        // Server kiểm tra xem version đó có khớp với database không. Nếu một Landlord khác đã lưu trước đó,
        // version trong database sẽ tăng lên, dẫn đến xung đột phiên bản và Server sẽ chặn cập nhật (HTTP 409).
        // Dưới client, nếu nhận được HTTP 409 hoặc lỗi 422, ta sẽ hiển thị cảnh báo cho người dùng reload lại trang.
        // ==========================================

        // Edit resident modal triggers
        function toggleEditResidentModal(show) {
            const modal = document.getElementById('edit-resident-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function openEditResidentModal(id, name, phone, roomId, startDate, dob, cccd, hometown, tempStatus, version) {
            const form = document.getElementById('edit-resident-form');
            form.action = `/smartroom/admin/resident/${id}`;
            
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-room-id').value = roomId;
            document.getElementById('edit-start-date').value = startDate;
            
            // Điền thông tin cá nhân mở rộng
            document.getElementById('edit-dob').value = dob || '';
            document.getElementById('edit-cccd').value = cccd || '';
            document.getElementById('edit-hometown').value = hometown || '';
            document.getElementById('edit-temp-status').value = tempStatus || 'none';
            
            // Optimistic Locking: Gán version hiện tại của bản ghi
            document.getElementById('edit-version').value = version || 1;
            
            toggleEditResidentModal(true);
        }

        // View resident modal triggers
        function toggleViewResidentModal(show) {
            const modal = document.getElementById('view-resident-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function openViewResidentModal(id, name, phone, email, room, startDate, status, dob, cccd, hometown, tempStatus) {
            document.getElementById('view-name').textContent = name;
            document.getElementById('view-phone').textContent = phone;
            document.getElementById('view-email').textContent = email || 'Chưa cung cấp';
            document.getElementById('view-room').textContent = 'Phòng ' + room;
            
            // Gán dữ liệu mở rộng
            document.getElementById('view-dob').textContent = dob ? formatDateString(dob) : 'Chưa cập nhật';
            document.getElementById('view-cccd').textContent = cccd || 'Chưa cập nhật';
            document.getElementById('view-hometown').textContent = hometown || 'Chưa cập nhật';
            
            const tempBadge = document.getElementById('view-temp-status');
            if (tempStatus === 'registered') {
                tempBadge.textContent = 'Đã đăng ký';
                tempBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
            } else if (tempStatus === 'absent') {
                tempBadge.textContent = 'Tạm vắng';
                tempBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20';
            } else {
                tempBadge.textContent = 'Chưa ĐK';
                tempBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20';
            }

            // Gán nút cấu hình người thân đi cùng
            const manageBtn = document.getElementById('view-manage-relatives-btn');
            manageBtn.onclick = function() {
                toggleViewResidentModal(false);
                openRelativesModal(id, name);
            };

            // Tải danh sách người thân trọ cùng qua AJAX
            loadRelativesForView(id);
            
            toggleViewResidentModal(true);
        }

        function formatDateString(dateStr) {
            if (!dateStr) return '—';
            try {
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;
                return date.getDate().toString().padStart(2, '0') + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getFullYear();
            } catch(e) {
                return dateStr;
            }
        }

        // Tải danh sách người thân chỉ để hiển thị trong View Modal
        function loadRelativesForView(residentId) {
            const table = document.getElementById('view-relatives-table');
            const tbody = document.getElementById('view-relatives-tbody');
            const emptyDiv = document.getElementById('view-relatives-empty');

            tbody.innerHTML = '';
            table.classList.add('hidden');
            emptyDiv.classList.remove('hidden');

            fetch(`/smartroom/admin/resident/${residentId}/relatives`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.relatives.length > 0) {
                        data.relatives.forEach(relative => {
                            let tempBadgeHTML = '';
                            if (relative.temporary_residence_status === 'registered') {
                                tempBadgeHTML = '<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400">Đã ĐK</span>';
                            } else if (relative.temporary_residence_status === 'absent') {
                                tempBadgeHTML = '<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/10 text-amber-400">Tạm vắng</span>';
                            } else {
                                tempBadgeHTML = '<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500/10 text-rose-400">Chưa ĐK</span>';
                            }

                            const tr = document.createElement('tr');
                            tr.className = "hover:bg-slate-900/10 transition-all border-b border-slate-800 last:border-0";
                            tr.innerHTML = `
                                <td class="py-2 text-slate-200 font-semibold text-xs">${relative.name}</td>
                                <td class="py-2 text-slate-400 text-xs">${relative.relationship}</td>
                                <td class="py-2 text-slate-400 text-xs font-mono">${relative.cccd || '—'}</td>
                                <td class="py-2">${tempBadgeHTML}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                        table.classList.remove('hidden');
                        emptyDiv.classList.add('hidden');
                    }
                })
                .catch(err => console.error("Error fetching relatives for view:", err));
        }

        // ==========================================
        // RELATIVES AJAX CRUD LOGIC (Quản lý người thân tạm trú)
        // ==========================================
        let currentResidentId = null;

        function toggleRelativesModal(show) {
            const modal = document.getElementById('relatives-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
                resetRelativeForm();
            }
        }

        function openRelativesModal(residentId, residentName) {
            currentResidentId = residentId;
            document.getElementById('relatives-modal-resident-name').textContent = residentName;
            
            // Load danh sách người thân hiện tại
            loadRelativesList();
            toggleRelativesModal(true);
        }

        function loadRelativesList() {
            const container = document.getElementById('relatives-list-container');
            container.innerHTML = `
                <div class="flex items-center justify-center py-10">
                    <div class="w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            `;

            fetch(`/smartroom/admin/resident/${currentResidentId}/relatives`)
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = '';
                    if (data.success && data.relatives.length > 0) {
                        data.relatives.forEach(rel => {
                            let statusText = 'Chưa ĐK';
                            let statusClass = 'bg-rose-500/10 text-rose-400 border border-rose-500/20';
                            if (rel.temporary_residence_status === 'registered') {
                                statusText = 'Đã ĐK';
                                statusClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                            } else if (rel.temporary_residence_status === 'absent') {
                                statusText = 'Tạm vắng';
                                statusClass = 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
                            }

                            const card = document.createElement('div');
                            card.className = "flex items-center justify-between p-3 rounded-xl bg-slate-950/40 border border-slate-900 hover:border-slate-800 transition-all";
                            
                            // Tránh lỗi ký tự đặc biệt khi parse JSON trong inline onclick
                            const relEscaped = JSON.stringify(rel).replace(/'/g, "\\'").replace(/"/g, "&quot;");
                            
                            card.innerHTML = `
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-xs font-bold text-slate-200">${rel.name}</h4>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 font-semibold">${rel.relationship}</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded ${statusClass} font-bold">${statusText}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-1 flex gap-3">
                                        <span>CCCD: <strong class="font-mono text-slate-400">${rel.cccd || '—'}</strong></span>
                                        <span>Sinh: <strong class="font-mono text-slate-400">${rel.dob ? formatDateString(rel.dob) : '—'}</strong></span>
                                        <span class="truncate max-w-[150px]" title="${rel.hometown}">Quê: <strong class="text-slate-400">${rel.hometown || '—'}</strong></span>
                                    </div>
                                </div>
                                <div class="flex gap-1.5">
                                    <button onclick="editRelative(JSON.parse('${JSON.stringify(rel).replace(/'/g, "\\'")}'))" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 flex items-center justify-center text-cyan-400 transition-all" title="Sửa">
                                        <i class="fa-solid fa-pencil text-[10px]"></i>
                                    </button>
                                    <button onclick="deleteRelative(${rel.id})" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-rose-950/20 border border-slate-800 hover:border-rose-900/50 flex items-center justify-center text-rose-400 transition-all" title="Xóa">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                </div>
                            `;
                            container.appendChild(card);
                        });
                    } else {
                        container.innerHTML = `
                            <div class="flex flex-col items-center justify-center py-12 text-slate-500 text-xs">
                                <i class="fa-solid fa-people-arrows text-2xl mb-2 text-slate-600"></i>
                                <span>Chưa có người thân tạm trú nào đăng ký cùng.</span>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    container.innerHTML = `<p class="text-xs text-rose-400 py-4 text-center">Không thể tải danh sách người thân!</p>`;
                    console.error(err);
                });
        }

        function resetRelativeForm() {
            document.getElementById('relative-id').value = '';
            document.getElementById('relative-version').value = '1';
            document.getElementById('relative-name').value = '';
            document.getElementById('relative-relationship').value = '';
            document.getElementById('relative-dob').value = '';
            document.getElementById('relative-cccd').value = '';
            document.getElementById('relative-hometown').value = '';
            document.getElementById('relative-temp-status').value = 'none';

            document.getElementById('relative-form-title').innerHTML = '<i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Người Thân Mới';
            document.getElementById('relative-submit-btn').innerHTML = '<i class="fa-solid fa-save"></i> <span>Lưu Lại</span>';
            document.getElementById('relative-reset-btn').classList.add('hidden');
            document.getElementById('relative-submit-btn').className = "w-full py-2 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center gap-1.5";
        }

        function editRelative(rel) {
            document.getElementById('relative-id').value = rel.id;
            document.getElementById('relative-version').value = rel.version;
            document.getElementById('relative-name').value = rel.name;
            document.getElementById('relative-relationship').value = rel.relationship;
            document.getElementById('relative-dob').value = rel.dob || '';
            document.getElementById('relative-cccd').value = rel.cccd || '';
            document.getElementById('relative-hometown').value = rel.hometown || '';
            document.getElementById('relative-temp-status').value = rel.temporary_residence_status || 'none';

            document.getElementById('relative-form-title').innerHTML = '<i class="fa-solid fa-user-pen text-cyan-400"></i> Cập Nhật Người Thân';
            document.getElementById('relative-submit-btn').innerHTML = '<i class="fa-solid fa-save"></i> <span>Cập Nhật</span>';
            document.getElementById('relative-reset-btn').classList.remove('hidden');
            document.getElementById('relative-submit-btn').className = "w-1/2 py-2 rounded-lg text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 shadow-md shadow-cyan-600/20 transition-all flex items-center justify-center gap-1.5";
        }

        function saveRelative(e) {
            e.preventDefault();
            const relativeId = document.getElementById('relative-id').value;
            const submitBtn = document.getElementById('relative-submit-btn');

            const payload = {
                name: document.getElementById('relative-name').value,
                relationship: document.getElementById('relative-relationship').value,
                dob: document.getElementById('relative-dob').value,
                cccd: document.getElementById('relative-cccd').value,
                hometown: document.getElementById('relative-hometown').value,
                temporary_residence_status: document.getElementById('relative-temp-status').value,
                version: document.getElementById('relative-version').value
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const originalHTML = submitBtn.innerHTML;
            
            // Chống click liên tiếp
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';

            let url = `/smartroom/admin/resident/${currentResidentId}/relative`;
            let method = 'POST';

            if (relativeId) {
                url = `/smartroom/admin/relative/${relativeId}`;
                method = 'PUT';
            }

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(async res => {
                const isJson = res.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await res.json() : null;

                if (res.status === 409) {
                    // Xử lý xung đột phiên bản dữ liệu (Optimistic Locking)
                    alert("❌ Lỗi Xung Đột Phiên Bản (Optimistic Locking):\nDữ liệu của người thân này đã bị thay đổi bởi một quản trị viên khác. Vui lòng đóng và mở lại danh sách để nhận dữ liệu mới nhất.");
                    return false;
                }

                if (!res.ok) {
                    throw new Error(data?.message || "Lỗi xử lý API!");
                }

                return data;
            })
            .then(data => {
                if (data) {
                    alert(data.message || "Đã lưu thông tin người thân thành công!");
                    resetRelativeForm();
                    loadRelativesList();
                }
            })
            .catch(err => {
                alert("Lỗi: " + err.message);
                console.error(err);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            });
        }

        function deleteRelative(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa thông tin người thân này?')) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            fetch(`/smartroom/admin/relative/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || "Đã xóa thành công!");
                    loadRelativesList();
                } else {
                    alert("Có lỗi xảy ra: " + (data.message || "Không thể xóa!"));
                }
            })
            .catch(err => {
                alert("Không thể kết nối máy chủ để xóa!");
                console.error(err);
            });
        }

        // ==========================================
        // 2. CHỐNG SPAM CLICK (ANTI-SPAM frontend)
        // Giải thích: Vô hiệu hóa nút nhấn hoặc form submit ngay sau click đầu tiên,
        // ngăn chặn việc gửi trùng lặp nhiều request cùng lúc khi mạng chậm.
        // ==========================================
        function antiSpamSubmit(form) {
            const btn = form.querySelector('.anti-spam-btn') || form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                const origHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang xử lý...';
            }
            return true;
        }

        function confirmAndDisable(form, message) {
            if (confirm(message)) {
                const btn = form.querySelector('.anti-spam-btn') || form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';
                }
                return true;
            }
            return false;
        }

        // ==========================================
        // 3. CHỐNG MỞ F12 & CÔNG CỤ NHÀ PHÁT TRIỂN (ANTI-F12 / DEVTOOLS)
        // Giải thích: Hạn chế người dùng hoặc hacker táy máy thay đổi DOM, xem mã nguồn,
        // hoặc bắt các request API nhạy cảm bằng cách chặn phím tắt và theo dõi DevTools.
        // ==========================================
        (function() {
            // Chặn phím tắt mở Developer Tools
            window.addEventListener('keydown', function(e) {
                // F12
                if (e.keyCode === 123) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
                // Ctrl+Shift+I
                if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
                // Ctrl+Shift+J
                if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
                // Ctrl+U (Xem nguồn trang)
                if (e.ctrlKey && e.keyCode === 85) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
            });

            // Chặn chuột phải
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                showDevToolsWarning();
                return false;
            });

            // Phát hiện DevTools mở bằng sự khác biệt về kích thước màn hình
            const devtools = {
                isOpen: false,
                orientation: undefined
            };
            const threshold = 160;
            
            setInterval(function() {
                const widthThreshold = window.outerWidth - window.innerWidth > threshold;
                const heightThreshold = window.outerHeight - window.innerHeight > threshold;
                
                if (widthThreshold || heightThreshold) {
                    if (!devtools.isOpen) {
                        devtools.isOpen = true;
                        console.warn("Cảnh báo: Phát hiện bảng điều khiển DevTools!");
                    }
                } else {
                    devtools.isOpen = false;
                }
            }, 1000);

            function showDevToolsWarning() {
                console.warn("🔐 Chức năng F12 và chuột phải đã bị khóa để bảo mật trang Quản trị.");
            }
        })();

        // CHARTS INITIALIZATION
        window.addEventListener('DOMContentLoaded', () => {
            // Revenue Chart
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            const gradRev = ctxRevenue.createLinearGradient(0, 0, 0, 300);
            gradRev.addColorStop(0, '#6366f1');
            gradRev.addColorStop(1, '#4f46e5');

            new Chart(ctxRevenue, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartMonths) !!},
                    datasets: [{
                        label: 'Doanh thu (VND)',
                        data: {!! json_encode($chartRevenue) !!},
                        backgroundColor: gradRev,
                        hoverBackgroundColor: '#818cf8',
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 16
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

            // Revenue Breakdown Doughnut Chart
            const ctxBreakdown = document.getElementById('revenueBreakdownChart').getContext('2d');
            let breakdownChart = new Chart(ctxBreakdown, {
                type: 'doughnut',
                data: {
                    labels: ['Tiền phòng', 'Tiền điện', 'Tiền nước', 'Dịch vụ'],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: [
                            '#6366f1',
                            '#f59e0b',
                            '#06b6d4',
                            '#10b981'
                        ],
                        borderWidth: 2,
                        borderColor: '#0a0f1d',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            fetch('/api/revenue-breakdown')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const b = data.breakdown;
                        const p = data.percentages;
                        breakdownChart.data.datasets[0].data = [b.room, b.electric, b.water, b.service];
                        breakdownChart.update();
                        
                        document.getElementById('breakdown-total-txt').textContent = (data.total / 1000000).toFixed(1) + 'M';
                        document.getElementById('breakdown-room-pct').textContent = p.room + '%';
                        document.getElementById('breakdown-elec-pct').textContent = p.electric + '%';
                        document.getElementById('breakdown-water-pct').textContent = p.water + '%';
                        document.getElementById('breakdown-service-pct').textContent = p.service + '%';
                    }
                })
                .catch(err => console.error("Error loading revenue breakdown:", err));

            // Status Pie Chart
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Trống', 'Đang thuê', 'Nợ tiền'],
                    datasets: [{
                        data: [{{ $emptyRooms }}, {{ $occupiedRooms }}, {{ $overdueRooms }}],
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

            // Trigger calculation for any pre-populated utility inputs
            document.querySelectorAll('.new-elec-input').forEach(input => {
                if (input.value) {
                    calculateRowCost(input);
                }
            });

            // Auto-switch to tab from query param if provided (e.g. ?tab=utility-section)
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                const targetBtn = Array.from(document.querySelectorAll('.nav-btn')).find(btn => {
                    const onclickStr = btn.getAttribute('onclick') || '';
                    return onclickStr.includes(tabParam);
                });
                if (targetBtn) {
                    switchTab(tabParam, targetBtn);
                }
                // Clean the URL parameter without reloading the page
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({ path: newUrl }, '', newUrl);
            }
        });

        function showVietQR(billId) {
            const qrModal = document.getElementById('vietqr-modal');
            document.getElementById('qr-modal-loading').classList.remove('hidden');
            document.getElementById('qr-modal-content').classList.add('hidden');
            qrModal.classList.remove('hidden');

            fetch(`/api/utility-bill/${billId}/qr`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('qr-modal-room').textContent = `Phòng ${data.room_number}`;
                        document.getElementById('qr-modal-tenant').textContent = data.resident_name;
                        document.getElementById('qr-modal-amount').textContent = data.amount.toLocaleString('vi-VN') + "đ";
                        document.getElementById('qr-modal-bank').textContent = `${data.bank_id} - ${data.account_no}`;
                        document.getElementById('qr-modal-name').textContent = data.account_name;
                        document.getElementById('qr-modal-desc').textContent = decodeURIComponent(data.description);
                        document.getElementById('qr-modal-image').src = data.qr_url;
                        
                        document.getElementById('qr-modal-download').href = data.qr_url;
                        
                        document.getElementById('qr-modal-loading').classList.add('hidden');
                        document.getElementById('qr-modal-content').classList.remove('hidden');
                    } else {
                        alert("Không thể tải mã QR: " + (data.error || "Lỗi không xác định"));
                        qrModal.classList.add('hidden');
                    }
                })
                .catch(err => {
                    alert("Có lỗi xảy ra khi kết nối API!");
                    console.error(err);
                    qrModal.classList.add('hidden');
                });
        }

        function showVietQRFallback(roomNum, amount) {
            const qrModal = document.getElementById('vietqr-modal');
            document.getElementById('qr-modal-loading').classList.add('hidden');
            document.getElementById('qr-modal-content').classList.remove('hidden');
            qrModal.classList.remove('hidden');

            const amt = parseInt(amount) || 0;
            const bankId = 'MB';
            const accountNo = '9999888889999';
            const accountName = 'NGUYEN VAN CHU NHA';
            const desc = `Thanh toan Phong ${roomNum} coc hoac tien phong`;
            const qrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-compact.png?amount=${amt}&addInfo=${encodeURIComponent(desc)}&accountName=${encodeURIComponent(accountName)}`;

            document.getElementById('qr-modal-room').textContent = `Phòng ${roomNum}`;
            document.getElementById('qr-modal-tenant').textContent = 'Khách mới / Cư dân';
            document.getElementById('qr-modal-amount').textContent = amt.toLocaleString('vi-VN') + "đ";
            document.getElementById('qr-modal-bank').textContent = `${bankId} - ${accountNo}`;
            document.getElementById('qr-modal-name').textContent = accountName;
            document.getElementById('qr-modal-desc').textContent = desc;
            document.getElementById('qr-modal-image').src = qrUrl;
            document.getElementById('qr-modal-download').href = qrUrl;
        }

        let activeMessageType = 'zalo';
        let currentRecipientPhone = '';
        let currentRecipientName = '';
        let currentMsgType = 'contract'; // 'contract' or 'debt'
        
        function openSendMsgModal(phone, name, messageText, msgType) {
            currentRecipientPhone = phone;
            currentRecipientName = name;
            currentMsgType = msgType;
            activeMessageType = 'zalo'; 
            
            document.getElementById('msg-phone-input').value = phone;
            document.getElementById('msg-text-input').value = messageText;
            document.getElementById('phone-screen-title').textContent = name || 'Khách thuê';
            
            updateMessagePreview();
            switchMsgTypeTab('zalo');
            
            document.getElementById('msg-success-overlay').classList.add('hidden');
            document.getElementById('msg-send-btn').disabled = false;
            document.getElementById('msg-send-btn').innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay';
            
            document.getElementById('send-msg-modal').classList.remove('hidden');
        }
        
        function closeSendMsgModal() {
            document.getElementById('send-msg-modal').classList.add('hidden');
        }
        
        function switchMsgTypeTab(type) {
            activeMessageType = type;
            const tabZalo = document.getElementById('msg-tab-zalo');
            const tabSms = document.getElementById('msg-tab-sms');
            const previewContainer = document.getElementById('msg-preview-container');
            const phoneHeader = document.getElementById('phone-header');
            
            if (type === 'zalo') {
                tabZalo.className = "flex-1 py-2 text-center text-xs font-bold text-indigo-400 bg-indigo-500/10 border-b-2 border-indigo-500 transition-all cursor-pointer";
                tabSms.className = "flex-1 py-2 text-center text-xs font-medium text-slate-500 hover:text-slate-300 transition-all cursor-pointer";
                previewContainer.className = "flex-1 p-4 overflow-y-auto space-y-3 bg-[#0f172a] rounded-2xl border border-slate-800/80 flex flex-col justify-end";
                phoneHeader.className = "flex items-center justify-between px-4 py-3 bg-[#1e293b] border-b border-slate-800 rounded-t-3xl text-slate-200";
                document.getElementById('phone-app-name').textContent = "Zalo Messenger";
                document.getElementById('preview-bubble').className = "max-w-[85%] rounded-2xl px-4 py-2 text-xs bg-indigo-600 text-white self-end ml-auto shadow-md";
            } else {
                tabSms.className = "flex-1 py-2 text-center text-xs font-bold text-emerald-400 bg-emerald-500/10 border-b-2 border-emerald-500 transition-all cursor-pointer";
                tabZalo.className = "flex-1 py-2 text-center text-xs font-medium text-slate-500 hover:text-slate-300 transition-all cursor-pointer";
                previewContainer.className = "flex-1 p-4 overflow-y-auto space-y-3 bg-[#0b0f19] rounded-2xl border border-slate-800/80 flex flex-col justify-end";
                phoneHeader.className = "flex items-center justify-between px-4 py-3 bg-[#111827] border-b border-slate-800 rounded-t-3xl text-slate-200";
                document.getElementById('phone-app-name').textContent = "Tin nhắn SMS";
                document.getElementById('preview-bubble').className = "max-w-[85%] rounded-2xl px-4 py-2 text-xs bg-emerald-600 text-white self-end ml-auto shadow-md";
            }
        }
        
        function updateMessagePreview() {
            const val = document.getElementById('msg-text-input').value;
            document.getElementById('preview-bubble-text').textContent = val || "(Trống)";
        }
        
        function selectMsgTemplate(templateIndex) {
            let msg = '';
            if (currentMsgType === 'contract') {
                if (templateIndex === 1) {
                    msg = `Kính gửi anh/chị ${currentRecipientName}, hợp đồng thuê phòng của anh/chị đã được ban quản lý khởi tạo. Vui lòng ký trực tuyến tại đây: [Link ký]`;
                } else if (templateIndex === 2) {
                    msg = `Chào ${currentRecipientName}, vui lòng kiểm tra và thực hiện ký hợp đồng online sớm nhất để hoàn tất thủ tục nhận phòng nhé.`;
                } else {
                    msg = `Thông báo: Link ký hợp đồng thuê phòng của anh/chị đã sẵn sàng. Truy cập ngay: [Link ký]`;
                }
            } else {
                if (templateIndex === 1) {
                    msg = `Kính gửi anh/chị ${currentRecipientName}, ban quản lý thông báo tiền phòng tháng này chưa thanh toán. Vui lòng nộp trước ngày 10. Trân trọng!`;
                } else if (templateIndex === 2) {
                    msg = `Nhắc nhở: Phòng của anh/chị còn dư nợ hóa đơn dịch vụ điện nước. Vui lòng thanh toán qua ứng dụng SmartRoom.`;
                } else {
                    msg = `Ban quản lý nhà trọ thông báo nhắc nợ tiền phòng tháng này đối với anh/chị ${currentRecipientName}. Liên hệ chủ nhà để biết thêm chi tiết.`;
                }
            }
            
            const originalVal = document.getElementById('msg-text-input').value;
            const linkMatch = originalVal.match(/https?:\/\/[^\s]+/);
            if (linkMatch && linkMatch[0]) {
                msg = msg.replace('[Link ký]', linkMatch[0]);
            }
            
            document.getElementById('msg-text-input').value = msg;
            updateMessagePreview();
        }
        
        function triggerSendMessage() {
            const phone = document.getElementById('msg-phone-input').value;
            const messageText = document.getElementById('msg-text-input').value;
            const btn = document.getElementById('msg-send-btn');
            
            if (!phone || !messageText) {
                alert("Vui lòng điền đầy đủ số điện thoại và nội dung tin nhắn!");
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';
            
            fetch('/api/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    phone: phone,
                    message: messageText,
                    type: activeMessageType
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('msg-success-overlay').classList.remove('hidden');
                    
                    if (navigator.vibrate) {
                        navigator.vibrate([100, 50, 100]);
                    }
                    
                    setTimeout(() => {
                        closeSendMsgModal();
                        alert(`Đã gửi tin nhắn nhắc nhở đến ${data.phone} qua kênh ${data.type.toUpperCase()} thành công!`);
                    }, 2200);
                } else {
                    alert("Có lỗi xảy ra: " + (data.error || "Gửi tin thất bại"));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay';
                }
            })
            .catch(err => {
                alert("Không thể kết nối đến máy chủ!");
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay';
                console.error(err);
            });
        }

        function closeVietQRModal() {
            document.getElementById('vietqr-modal').classList.add('hidden');
        }

        function copyBankAccount() {
            navigator.clipboard.writeText('9999888889999').then(() => {
                const btn = document.getElementById('qr-modal-copy');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-emerald-450"></i> Đã sao chép!';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            });
        }

        function triggerAutoRemind(btn) {
            if (!confirm('Hệ thống sẽ tự động quét các hóa đơn chưa đóng trong tháng này và gửi tin nhắn nhắc nợ qua Zalo hàng loạt. Bạn có chắc chắn muốn thực hiện?')) {
                return;
            }

            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang tự động gửi...';

            fetch('/api/utility-bills/auto-remind', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;

                if (data.success) {
                    if (data.sent_count > 0) {
                        let roomList = data.sent_rooms.map(r => `Phòng ${r.room_number} (${r.resident_name}): ${r.total_amount_formatted}`).join('\n');
                        alert(`🚀 Tự động nhắc nợ thành công!\nĐã gửi tin nhắn nhắc nợ Zalo tới ${data.sent_count} phòng chưa đóng tiền:\n\n${roomList}`);
                    } else {
                        alert(`✨ Tuyệt vời! Tất cả các phòng đã hoàn thành đóng tiền trọ tháng này.`);
                    }
                } else {
                    alert("Có lỗi xảy ra khi gửi nhắc nợ tự động.");
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                alert("Không thể kết nối đến máy chủ để thực hiện gửi nhắc nợ hàng loạt!");
                console.error(err);
            });
        }
    </script>

    <!-- VIETQR POPUP MODAL -->
    <div id="vietqr-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl relative animate-fade-in">
            <button onclick="closeVietQRModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="text-center mb-4">
                <span class="text-xs px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-bold uppercase tracking-wider">Mã VietQR Hóa Đơn</span>
                <h3 class="text-lg font-bold text-slate-100 mt-2" id="qr-modal-room">Phòng 101</h3>
            </div>

            <!-- Loading Spinner -->
            <div id="qr-modal-loading" class="flex flex-col items-center justify-center py-12 gap-3">
                <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs text-slate-400 font-semibold">Đang tạo mã thanh toán...</span>
            </div>

            <!-- Content -->
            <div id="qr-modal-content" class="space-y-4 hidden">
                <!-- QR Image Display -->
                <div class="flex justify-center p-4 bg-white rounded-2xl relative overflow-hidden group">
                    <img id="qr-modal-image" class="w-60 h-60 object-contain transition-transform group-hover:scale-105 duration-300" src="" alt="Mã VietQR">
                </div>

                <!-- Payment Details -->
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 space-y-2.5 text-xs">
                    <div class="flex justify-between"><span class="text-slate-500">Khách thuê:</span> <strong class="text-slate-200" id="qr-modal-tenant">N/A</strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Số tiền cần đóng:</span> <strong class="text-emerald-400 font-bold text-sm" id="qr-modal-amount">0đ</strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Ngân hàng:</span> <strong class="text-slate-200" id="qr-modal-bank">MB - 9999888889999</strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Chủ tài khoản:</span> <strong class="text-slate-200" id="qr-modal-name">NGUYEN VAN CHU NHA</strong></div>
                    <div class="flex justify-between items-start"><span class="text-slate-500">Nội dung chuyển:</span> <strong class="text-slate-200 text-right shrink-0 max-w-[180px] break-words" id="qr-modal-desc">N/A</strong></div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button id="qr-modal-copy" onclick="copyBankAccount()" class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all">
                        <i class="fa-solid fa-copy text-indigo-400"></i> Sao chép STK
                    </button>
                    <a id="qr-modal-download" download="VietQR_Payment.png" target="_blank" class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        <i class="fa-solid fa-download"></i> Tải ảnh QR
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ZALO / SMS SEND MESSAGE PHONE MODAL -->
    <div id="send-msg-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 md:p-8 shadow-2xl relative animate-fade-in">
            <button onclick="closeSendMsgModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all z-30">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex flex-col md:flex-row gap-8 items-stretch justify-center">
                <!-- LEFT SIDE: PHONE SIMULATOR -->
                <div class="flex justify-center items-center shrink-0">
                    <div class="w-[280px] h-[480px] bg-slate-950 rounded-[36px] border-[5px] border-slate-800 relative shadow-2xl flex flex-col overflow-hidden">
                        <!-- iPhone Notch & Status Bar -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-28 h-4 bg-slate-950 rounded-b-xl z-20 flex justify-between items-center px-4 text-[7px] text-slate-500 font-bold">
                            <span>16:40</span>
                            <div class="w-8 h-1 bg-slate-800 rounded-full"></div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-wifi"></i>
                                <i class="fa-solid fa-battery-three-quarters"></i>
                            </div>
                        </div>

                        <!-- Chat Header -->
                        <div id="phone-header" class="flex items-center justify-between px-3 py-2 bg-[#1e293b] border-b border-slate-800 rounded-t-2xl text-slate-200 mt-3 shrink-0">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-[10px] text-indigo-400 font-bold">
                                    <i class="fa-solid fa-user text-[9px]"></i>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold" id="phone-screen-title">Khách thuê</div>
                                    <div class="text-[7px] text-emerald-400 font-bold" id="phone-app-name">Zalo Messenger</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-ellipsis-vertical text-[10px] text-slate-500"></i>
                        </div>

                        <!-- Chat Area -->
                        <div id="msg-preview-container" class="flex-1 p-3 overflow-y-auto space-y-3 bg-[#0f172a] flex flex-col justify-end">
                            <div class="max-w-[85%] rounded-2xl px-3 py-1.5 text-[10px] bg-slate-900 text-slate-400 border border-slate-800 self-start">
                                Xin chào ban quản lý. Tôi cần nhận thông báo phòng.
                            </div>
                            <!-- Live Preview Bubble -->
                            <div id="preview-bubble" class="max-w-[85%] rounded-2xl px-3 py-1.5 text-[10px] bg-indigo-600 text-white self-end ml-auto shadow-md">
                                <p id="preview-bubble-text" class="break-words leading-relaxed">(Trống)</p>
                            </div>
                        </div>

                        <!-- Message Input Mock -->
                        <div class="p-2 bg-slate-900/60 border-t border-slate-800 shrink-0 flex items-center gap-2">
                            <div class="flex-1 bg-slate-950 border border-slate-800 rounded-full px-3 py-1 text-[8px] text-slate-500">
                                Tin nhắn...
                            </div>
                            <div class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center text-white text-[8px]">
                                <i class="fa-solid fa-microphone"></i>
                            </div>
                        </div>

                        <!-- Success Broadcast Overlay -->
                        <div id="msg-success-overlay" class="absolute inset-0 bg-slate-950/95 flex flex-col items-center justify-center text-center space-y-4 p-4 z-10 hidden">
                            <div class="relative flex items-center justify-center">
                                <div class="absolute w-16 h-16 bg-emerald-500/10 rounded-full animate-ping"></div>
                                <div class="absolute w-24 h-24 bg-emerald-500/5 rounded-full animate-pulse"></div>
                                <div class="w-12 h-12 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-lg">
                                    <i class="fa-solid fa-paper-plane animate-bounce"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-200">ĐANG TRUYỀN TIN</h4>
                                <p class="text-[9px] text-slate-500 mt-1">Kết nối cổng Zalo & SMS API thành công...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: MESSAGE EDITING & CONTROL -->
                <div class="flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-envelope-open-text text-indigo-400 animate-pulse"></i> Trình Gửi Tin Nhắn Điện Tử
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Hệ thống tích hợp cổng API Zalo ZNS và SMS Brandname để tự động gửi thông báo đến cư dân.</p>
                    </div>

                    <!-- Channel Switch Tabs -->
                    <div class="flex border-b border-slate-800">
                        <div id="msg-tab-zalo" onclick="switchMsgTypeTab('zalo')" class="flex-1 py-2 text-center text-xs font-bold text-indigo-400 bg-indigo-500/10 border-b-2 border-indigo-500 transition-all cursor-pointer">
                            <i class="fa-solid fa-message mr-1.5"></i> Cổng Zalo ZNS
                        </div>
                        <div id="msg-tab-sms" onclick="switchMsgTypeTab('sms')" class="flex-1 py-2 text-center text-xs font-medium text-slate-500 hover:text-slate-300 transition-all cursor-pointer">
                            <i class="fa-solid fa-comment-sms mr-1.5"></i> Cổng SMS Brandname
                        </div>
                    </div>

                    <!-- Recipient details -->
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Số điện thoại nhận tin</label>
                        <input type="text" id="msg-phone-input" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold" placeholder="Nhập số điện thoại...">
                    </div>

                    <!-- Template Selectors -->
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Mẫu tin nhắn nhanh</label>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="selectMsgTemplate(1)" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-slate-300 hover:text-slate-100 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-paste text-slate-500 mr-1"></i> Mẫu 1
                            </button>
                            <button onclick="selectMsgTemplate(2)" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-slate-300 hover:text-slate-100 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-paste text-slate-500 mr-1"></i> Mẫu 2
                            </button>
                            <button onclick="selectMsgTemplate(3)" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-slate-300 hover:text-slate-100 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-paste text-slate-500 mr-1"></i> Mẫu 3
                            </button>
                        </div>
                    </div>

                    <!-- Message text input -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Nội dung tin nhắn</label>
                        <textarea id="msg-text-input" oninput="updateMessagePreview()" rows="4" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-slate-300 focus:outline-none focus:border-indigo-500 resize-none leading-relaxed" placeholder="Soạn nội dung tin nhắn..."></textarea>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center gap-3 pt-2">
                        <button onclick="closeSendMsgModal()" class="flex-1 py-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 rounded-xl text-xs font-bold transition-all">
                            Hủy bỏ
                        </button>
                        <button id="msg-send-btn" onclick="triggerSendMessage()" class="flex-1 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/25 transition-all flex items-center justify-center">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
