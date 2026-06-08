<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Renty Review - Nền tảng tìm kiếm và đánh giá phòng trọ chân thực.">
    <title>Renty Review - Tìm Phòng Trọ & Đánh Giá Không Gian Sống</title>
    
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
<body class="bg-[#080b11] text-slate-100 min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-emerald-500 selection:text-white">

    <!-- Decorative glows -->
    <div class="absolute top-[-15%] left-[-10%] w-[500px] h-[500px] rounded-full bg-emerald-600/5 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-600/5 blur-[120px] pointer-events-none"></div>

    <!-- NAVBAR -->
    <header class="h-20 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md sticky top-0 z-40 flex items-center">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('smartroom.portal') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fa-solid fa-magnifying-glass-location text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Renty Review</span>
                </a>
            </div>
            
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-400">
                <a href="#" class="text-emerald-400 hover:text-emerald-300">Khám Phá Phòng</a>
                <a href="javascript:void(0)" onclick="openHotAreasModal()" class="hover:text-slate-205 transition-colors">Khu Vực Hot</a>
                <a href="javascript:void(0)" onclick="openNewReviewsModal()" class="hover:text-slate-205 transition-colors">Đánh Giá Mới</a>
                @auth
                    <div class="flex items-center gap-3 bg-slate-900/60 border border-slate-800/80 px-3.5 py-1.5 rounded-xl">
                        <span class="text-xs font-bold text-emerald-400">
                            <i class="fa-solid fa-user-circle mr-1"></i> {{ Auth::user()->name }}
                        </span>
                        <a href="{{ route('signout') }}" class="text-xs font-semibold text-rose-400 hover:text-rose-300 transition-colors">
                            Đăng xuất
                        </a>
                    </div>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('smartroom.admin') }}" class="px-4 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white shadow-lg shadow-indigo-600/15 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-gauge"></i> Cổng Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-slate-100 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket text-emerald-400"></i> Đăng Nhập
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- HERO SEARCH & ADVANCED FILTERS -->
    <section class="container mx-auto px-6 pt-12 pb-6 max-w-6xl relative z-10">
        <div class="max-w-3xl mx-auto text-center mb-8">
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-4 leading-tight">
                Tìm Trọ Đúng Nghĩa - <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">Xem Review Thật</span>
            </h1>
            <p class="text-slate-400 text-xs md:text-sm">
                Tránh bẫy "ảnh mạng một đằng thực tế một nẻo". Xem đánh giá điểm số chủ nhà, an ninh, điện nước trước khi cọc.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-4xl mx-auto bg-slate-900/60 backdrop-blur-xl border border-slate-800 p-4 rounded-3xl shadow-xl shadow-slate-950/20 mb-8">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-grow relative">
                    <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                    <input type="text" id="search-input" onkeyup="filterItems()" class="w-full pl-12 pr-4 py-3 bg-[#0a0e17] border border-slate-800 rounded-2xl text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none text-sm font-semibold" placeholder="Tìm trọ gần: Đại học Bách Khoa, Cầu Giấy, Quận 10...">
                </div>
                <div class="flex gap-2">
                    <button onclick="toggleFilterDrawer()" class="px-4 py-3 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-slate-100 rounded-2xl text-sm font-bold flex items-center gap-2 transition-all">
                        <i class="fa-solid fa-sliders text-emerald-400"></i> Bộ Lọc
                    </button>
                    <button onclick="filterItems()" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl text-sm font-bold shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-2 shrink-0">
                        <i class="fa-solid fa-magnifying-glass"></i> Tìm Kiếm
                    </button>
                </div>
            </div>

            <!-- Expandable Filters -->
            <div id="filter-drawer" class="hidden mt-4 pt-4 border-t border-slate-800/80 grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in">
                <!-- Price Range -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Giá phòng tối đa</label>
                    <select id="filter-price" onchange="filterItems()" class="w-full px-4 py-2 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none">
                        <option value="all">Tất cả khoảng giá</option>
                        <option value="3000000">Dưới 3.000.000đ</option>
                        <option value="4000000">Dưới 4.000.000đ</option>
                        <option value="5000000">Dưới 5.000.000đ</option>
                    </select>
                </div>
                
                <!-- Ratings -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Điểm đánh giá tối thiểu</label>
                    <select id="filter-rating" onchange="filterItems()" class="w-full px-4 py-2 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none">
                        <option value="all">Mọi điểm số</option>
                        <option value="4.5">Từ 4.5⭐ trở lên</option>
                        <option value="4.0">Từ 4.0⭐ trở lên</option>
                        <option value="3.5">Từ 3.5⭐ trở lên</option>
                    </select>
                </div>

                <!-- Utilities -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Tiện ích đặc biệt</label>
                    <div class="flex flex-wrap gap-2">
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700">
                            <input type="checkbox" id="tag-pets" onchange="filterItems()" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> Nuôi thú cưng
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700">
                            <input type="checkbox" id="tag-loft" onchange="filterItems()" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> Có gác lửng
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700">
                            <input type="checkbox" id="tag-balcony" onchange="filterItems()" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> Ban công
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LIST OF ROOMS -->
    <main class="container mx-auto px-6 py-6 max-w-6xl flex-grow relative z-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-slate-200" id="results-count">Danh sách phòng trọ ({{ count($rooms) }} kết quả)</h2>
            <p class="text-xs text-slate-500">Tích chọn tối đa 3 phòng để so sánh</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16" id="rooms-grid">
            @foreach($rooms as $room)
            <div class="room-item-card glass-card rounded-2xl overflow-hidden group flex flex-col justify-between" 
                 data-price="{{ $room['price'] }}" 
                 data-rating="{{ $room['rating'] }}" 
                 data-pets="{{ $room['pets'] }}" 
                 data-loft="{{ $room['loft'] }}" 
                 data-balcony="{{ $room['balcony'] }}" 
                 data-distance="{{ $room['distance'] }}" 
                 data-title="{{ $room['title'] }}">
                <div>
                    <!-- Room photo -->
                    <div class="h-48 bg-slate-950 relative overflow-hidden border-b border-slate-900 group">
                        <img src="{{ $room['cover_image'] }}" alt="Ảnh phòng {{ $room['room_number'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/10 to-transparent"></div>

                        @if($room['status'] === 'empty')
                            <span class="absolute top-4 left-4 px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Sẵn sàng
                            </span>
                        @else
                            <span class="absolute top-4 left-4 px-2.5 py-1 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                Đã thuê
                            </span>
                        @endif

                        <div class="absolute left-4 right-4 bottom-4 z-10 flex items-end justify-between gap-3">
                            <div>
                                <span class="block text-[10px] font-extrabold text-white uppercase tracking-widest drop-shadow">Ảnh thực tế phòng</span>
                                <span class="block text-[10px] font-semibold text-slate-300 mt-0.5">Phòng {{ $room['room_number'] }} · {{ count($room['image_urls']) }} ảnh</span>
                            </div>
                            <span class="w-9 h-9 rounded-xl bg-slate-950/70 border border-white/10 backdrop-blur flex items-center justify-center text-emerald-300">
                                <i class="fa-solid fa-images"></i>
                            </span>
                        </div>
                    </div>
                    <!-- Details -->
                    <div class="p-5 flex-grow flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2 gap-2">
                                <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-all line-clamp-1">{{ $room['title'] }}</h3>
                                <div class="flex items-center gap-1 text-xs text-amber-400 font-bold shrink-0">
                                    <i class="fa-solid fa-star text-[10px]"></i> <span>{{ $room['rating'] }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 mb-4 flex items-center gap-1"><i class="fa-solid fa-map-marker-alt text-[10px] text-slate-650"></i> {{ $room['address'] }}</p>
                            <!-- Prices and tags -->
                            <div class="flex items-baseline gap-1.5 mb-4">
                                <span class="text-xl font-extrabold text-emerald-400">{{ number_format($room['price'], 0, ',', '.') }}đ</span>
                                <span class="text-[10px] text-slate-500 font-semibold">/ tháng</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @if($room['loft'] === 'true')
                                <span class="px-2.5 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 text-[9px] font-bold border border-indigo-500/20">Có gác lửng</span>
                            @endif
                            @if($room['pets'] === 'true')
                                <span class="px-2.5 py-0.5 rounded-md bg-teal-500/10 text-teal-400 text-[9px] font-bold border border-teal-500/20">Nuôi thú cưng</span>
                            @endif
                            @if($room['balcony'] === 'true')
                                <span class="px-2.5 py-0.5 rounded-md bg-sky-500/10 text-sky-400 text-[9px] font-bold border border-sky-500/20">Ban công</span>
                            @endif
                            <span class="px-2.5 py-0.5 rounded-md bg-slate-900 text-slate-500 text-[9px] font-bold border border-slate-800/40">WC khép kín</span>
                        </div>
                    </div>
                </div>
                <!-- Card footer action -->
                <div class="px-5 pb-5 pt-3 border-t border-slate-900/50 flex justify-between items-center bg-slate-950/20">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer hover:text-slate-355 transition-colors">
                        <input type="checkbox" onchange="toggleCompare('{{ $room['id'] }}', this)" class="compare-checkbox w-4 h-4 rounded border-slate-800 bg-slate-900 text-emerald-600 focus:ring-0 focus:ring-offset-0">
                        <span>So sánh</span>
                    </label>
                    <button onclick="openRoomDetailModal('{{ $room['id'] }}')" class="text-xs text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                        <span>Chi tiết review</span> <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </main>

    <!-- FLOATING COMPARE BOX / DOCK (HIDDEN BY DEFAULT) -->
    <div id="compare-dock" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-[#0c111e]/90 backdrop-blur-md border border-slate-800 px-6 py-4 rounded-2xl shadow-2xl flex items-center justify-between gap-8 max-w-lg w-11/12 hidden animate-fade-in">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                <i class="fa-solid fa-code-compare"></i>
            </div>
            <div>
                <strong class="text-xs block text-slate-200" id="compare-count-label">Đang chọn 1 phòng trọ</strong>
                <span class="text-[10px] text-slate-500">So sánh các thông số trọ trực quan</span>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <button onclick="clearCompare()" class="px-3 py-1.5 text-xs text-slate-400 hover:text-slate-200 font-semibold transition-all">
                Hủy
            </button>
            <button onclick="openCompareModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/20 transition-all">
                So Sánh Ngay
            </button>
        </div>
    </div>

    <!-- ROOM COMPARISON MODAL -->
    <div id="compare-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-5xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto animate-fade-in">
            <button onclick="closeCompareModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-xl font-bold mb-6 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-code-compare text-emerald-400"></i> Bảng So Sánh Các Phòng Trọ Đã Chọn
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <!-- Left: Radar Chart (5/12 cols) -->
                <div class="lg:col-span-5 p-6 rounded-2xl bg-[#0e1424]/60 border border-slate-800/80 flex flex-col justify-between items-center min-h-[360px]">
                    <div class="w-full text-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Biểu Đồ Radar Chỉ Số</span>
                        <span class="text-[9px] text-slate-500 block leading-tight">So sánh trực quan thang điểm 10 (càng xa tâm càng tốt)</span>
                    </div>
                    <div class="w-full h-72 flex items-center justify-center mt-4 relative">
                        <canvas id="compareRadarChart"></canvas>
                    </div>
                </div>

                <!-- Right: Detailed Table (7/12 cols) -->
                <div class="lg:col-span-7 flex flex-col justify-between">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300 border-collapse">
                            <thead>
                                <tr class="border-b border-slate-900 pb-2">
                                    <th class="px-3 py-3 text-[10px] font-bold text-slate-500 uppercase">Tiêu chí</th>
                                    <th class="px-3 py-3 font-bold text-xs text-emerald-400 max-w-[120px] truncate" id="compare-col-1-title">Phòng 1</th>
                                    <th class="px-3 py-3 font-bold text-xs text-indigo-400 max-w-[120px] truncate" id="compare-col-2-title">Phòng 2</th>
                                    <th class="px-3 py-3 font-bold text-xs text-cyan-400 max-w-[120px] truncate" id="compare-col-3-title">Phòng 3</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900/60">
                                <!-- Rent price -->
                                <tr>
                                    <td class="px-3 py-3 font-bold text-slate-400">Giá thuê / tháng</td>
                                    <td class="px-3 py-3 font-extrabold text-slate-200" id="compare-val-1-price">-</td>
                                    <td class="px-3 py-3 font-extrabold text-slate-200" id="compare-val-2-price">-</td>
                                    <td class="px-3 py-3 font-extrabold text-slate-200" id="compare-val-3-price">-</td>
                                </tr>
                                <!-- Distance to campus -->
                                <tr>
                                    <td class="px-3 py-3 font-bold text-slate-400">Khoảng cách trường</td>
                                    <td class="px-3 py-3 text-slate-300" id="compare-val-1-dist">-</td>
                                    <td class="px-3 py-3 text-slate-300" id="compare-val-2-dist">-</td>
                                    <td class="px-3 py-3 text-slate-300" id="compare-val-3-dist">-</td>
                                </tr>
                                <!-- Overall score -->
                                <tr>
                                    <td class="px-3 py-3 font-bold text-slate-400">Đánh giá chung</td>
                                    <td class="px-3 py-3 font-bold text-amber-400" id="compare-val-1-rating">-</td>
                                    <td class="px-3 py-3 font-bold text-amber-400" id="compare-val-2-rating">-</td>
                                    <td class="px-3 py-3 font-bold text-amber-400" id="compare-val-3-rating">-</td>
                                </tr>
                                <!-- Owner Score -->
                                <tr>
                                    <td class="px-3 py-3 font-bold text-slate-400">Điểm chủ nhà</td>
                                    <td class="px-3 py-3 text-slate-300" id="compare-val-1-owner">-</td>
                                    <td class="px-3 py-3 text-slate-300" id="compare-val-2-owner">-</td>
                                    <td class="px-3 py-3 text-slate-300" id="compare-val-3-owner">-</td>
                                </tr>
                                <!-- Security Score -->
                                <tr>
                                    <td class="px-3 py-3 font-bold text-slate-400">An ninh & Khóa</td>
                                    <td class="px-3 py-3 text-slate-300" id="compare-val-1-sec">-</td>
                                    <td class="px-3 py-3 text-slate-300" id="compare-val-2-sec">-</td>
                                    <td class="px-3 py-3 text-slate-300" id="compare-val-3-sec">-</td>
                                </tr>
                                <!-- Pets allowed -->
                                <tr>
                                    <td class="px-3 py-3 font-bold text-slate-400">Cho nuôi thú cưng</td>
                                    <td class="px-3 py-3" id="compare-val-1-pets">-</td>
                                    <td class="px-3 py-3" id="compare-val-2-pets">-</td>
                                    <td class="px-3 py-3" id="compare-val-3-pets">-</td>
                                </tr>
                                <!-- Has loft -->
                                <tr>
                                    <td class="px-3 py-3 font-bold text-slate-400">Có gác lửng</td>
                                    <td class="px-3 py-3" id="compare-val-1-loft">-</td>
                                    <td class="px-3 py-3" id="compare-val-2-loft">-</td>
                                    <td class="px-3 py-3" id="compare-val-3-loft">-</td>
                                </tr>
                                <!-- Has balcony -->
                                <tr>
                                    <td class="px-3 py-3 font-bold text-slate-400">Ban công thoáng</td>
                                    <td class="px-3 py-3" id="compare-val-1-balcony">-</td>
                                    <td class="px-3 py-3" id="compare-val-2-balcony">-</td>
                                    <td class="px-3 py-3" id="compare-val-3-balcony">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-slate-900 flex justify-end">
                <button onclick="closeCompareModal()" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-700 transition-all">
                    Đóng bảng so sánh
                </button>
            </div>
        </div>
    </div>

    <!-- ROOM DETAIL & REVIEWS MODAL -->
    <div id="room-detail-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto animate-fade-in">
            <button onclick="closeRoomDetailModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-xl font-bold mb-2 text-slate-100" id="detail-room-title">SmartRoom Cầu Giấy - Phòng 101</h2>
            <p class="text-xs text-slate-500 mb-6 flex items-center gap-1">
                <i class="fa-solid fa-location-dot text-slate-600"></i> <span id="detail-room-address">Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy</span>
            </p>

            <div class="mb-6">
                <div class="relative h-72 rounded-2xl overflow-hidden bg-slate-950 border border-slate-800">
                    <img id="detail-main-image" src="" alt="Ảnh phòng trọ" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent pointer-events-none"></div>
                    <div class="absolute left-4 bottom-4">
                        <span class="px-3 py-1.5 rounded-lg bg-slate-950/70 border border-white/10 backdrop-blur text-xs font-bold text-slate-100">
                            <i class="fa-solid fa-camera text-emerald-300 mr-1.5"></i><span id="detail-image-count">Ảnh phòng</span>
                        </span>
                    </div>
                </div>
                <div id="detail-image-thumbs" class="mt-3 grid grid-cols-3 gap-3">
                </div>
            </div>

            <div class="mb-6 p-5 rounded-2xl bg-slate-900/45 border border-slate-800/60">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h3 class="text-sm font-extrabold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-emerald-400"></i>
                        Mô tả nơi ở & không gian
                    </h3>
                    <span class="px-3 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-bold" id="detail-room-area">0 m²</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-[#070b13] border border-slate-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Địa chỉ & khu vực</span>
                        <p class="mt-2 text-xs text-slate-300 leading-relaxed" id="detail-location-description"></p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#070b13] border border-slate-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Khung cảnh</span>
                        <p class="mt-2 text-xs text-slate-300 leading-relaxed" id="detail-scenery-description"></p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#070b13] border border-slate-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Không gian phòng</span>
                        <p class="mt-2 text-xs text-slate-300 leading-relaxed" id="detail-space-description"></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Info Left -->
                <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-800/40 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Giá thuê:</span>
                        <strong class="text-emerald-400 font-extrabold" id="detail-room-price">3.200.000đ/tháng</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Đánh giá trung bình:</span>
                        <strong class="text-amber-400 font-bold" id="detail-room-rating">4.5 ⭐</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Chủ nhà & Dịch vụ:</span>
                        <span class="text-slate-300 text-xs" id="detail-room-owner">⭐⭐⭐⭐⭐</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">An ninh & Khóa:</span>
                        <span class="text-slate-300 text-xs" id="detail-room-sec">⭐⭐⭐⭐⭐</span>
                    </div>
                </div>

                <!-- Info Right -->
                <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-800/40 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Nuôi thú cưng:</span>
                        <strong class="text-slate-200" id="detail-room-pets">Có</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Có gác lửng:</span>
                        <strong class="text-slate-200" id="detail-room-loft">Có</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Có ban công:</span>
                        <strong class="text-slate-200" id="detail-room-balcony">Có</strong>
                    </div>
                </div>
            </div>

            <!-- Contact landlord and request consultation -->
            <div class="border-t border-slate-800/60 pt-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-300 mb-3"><i class="fa-solid fa-phone-volume text-emerald-400 mr-1.5"></i>Liên hệ Chủ nhà</h3>
                    <div class="p-4 rounded-xl bg-[#070b13] border border-slate-800/60 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 font-bold">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-200 block">Chủ trọ SmartRoom</span>
                                <span class="text-[10px] text-slate-400">Hỗ trợ tư vấn, xem phòng trực tiếp</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="tel:0987654321" class="flex-1 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all">
                                <i class="fa-solid fa-phone"></i> Gọi điện
                            </a>
                            <a href="https://zalo.me/0987654321" target="_blank" class="flex-1 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all">
                                <i class="fa-solid fa-comments"></i> Chat Zalo
                            </a>
                        </div>
                    </div>

                    <!-- Consultation Form -->
                    <h3 class="text-sm font-bold text-slate-300 mt-6 mb-3"><i class="fa-solid fa-calendar-check text-indigo-400 mr-1.5"></i>Đăng ký Tư vấn & Xem phòng</h3>
                    <form action="{{ route('renty.contact_request.store') }}" method="POST" class="p-4 rounded-xl bg-[#070b13] border border-slate-800/60 space-y-3.5">
                        @csrf
                        <input type="hidden" name="room_id" id="contact-room-id" value="">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Họ tên của bạn</label>
                            @auth
                                <input type="text" name="name" value="{{ Auth::user()->name }}" readonly class="w-full px-3 py-2 rounded-lg bg-[#0c1222] border border-slate-850 text-slate-400 text-xs cursor-not-allowed">
                            @else
                                <input type="text" name="name" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nguyễn Văn A">
                            @endauth
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Số điện thoại liên hệ</label>
                            @auth
                                <input type="tel" name="phone" value="{{ Auth::user()->phone }}" readonly class="w-full px-3 py-2 rounded-lg bg-[#0c1222] border border-slate-855 text-slate-400 text-xs cursor-not-allowed">
                            @else
                                <input type="tel" name="phone" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="0901234567">
                            @endauth
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Lời nhắn (Ví dụ: Thời gian muốn xem phòng...)</label>
                            <textarea name="message" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Tôi muốn xem phòng vào tối nay lúc 19h..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold transition-all shadow-md shadow-indigo-500/10">
                            Gửi Yêu Cầu Tư Vấn
                        </button>
                    </form>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-slate-300 mb-3"><i class="fa-solid fa-comments text-amber-400 mr-1.5"></i>Đánh giá thực tế từ người ở trước</h3>
                    <div class="space-y-4 max-h-[460px] overflow-y-auto pr-2" id="detail-reviews-container">
                        <!-- Dynamic list of reviews -->
                    </div>
                </div>
            </div>

            <!-- Write Review Form -->
            <div class="border-t border-slate-800/60 pt-6">
                <h3 class="text-sm font-bold text-slate-300 mb-3">Gửi đánh giá của bạn</h3>
                <form id="write-review-form" action="" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tên của bạn</label>
                            @auth
                                <input type="text" name="author_name" value="{{ Auth::user()->name }}" readonly class="w-full px-4 py-2.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-slate-400 text-xs cursor-not-allowed">
                            @else
                                <input type="text" name="author_name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none" placeholder="Nguyễn Văn A">
                            @endauth
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Điểm đánh giá (1-5)</label>
                            <select name="rating" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none">
                                <option value="5">5 ⭐⭐⭐⭐⭐ (Xuất sắc)</option>
                                <option value="4">4 ⭐⭐⭐⭐ (Tốt)</option>
                                <option value="3">3 ⭐⭐⭐ (Trung bình)</option>
                                <option value="2">2 ⭐⭐ (Kém)</option>
                                <option value="1">1 ⭐ (Rất kém)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Bình luận trải nghiệm thực tế</label>
                        <textarea name="comment" required rows="3" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none" placeholder="Hãy chia sẻ trải nghiệm về chủ nhà, an ninh, phòng ốc..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeRoomDetailModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-600/20 transition-all">
                            Gửi Đánh Giá
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="border-t border-slate-900 bg-slate-950/80 py-8 mt-12 relative z-10">
        <div class="container mx-auto px-6 max-w-6xl flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <div>
                © 2026 Renty Review. Hệ thống đánh giá không gian sống sinh viên Việt Nam.
            </div>

        </div>
    </footer>

    <!-- JS LOGIC -->
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

        // Show session success or error toasts
        @if(session('success'))
            window.addEventListener('DOMContentLoaded', () => {
                alert("{{ session('success') }}");
            });
        @endif
        @if(session('error'))
            window.addEventListener('DOMContentLoaded', () => {
                alert("{{ session('error') }}");
            });
        @endif

        // Toggle advanced filters
        function toggleFilterDrawer() {
            const drawer = document.getElementById('filter-drawer');
            drawer.classList.toggle('hidden');
        }

        // Room details modal
        function openRoomDetailModal(roomId) {
            const data = mockRooms[roomId];
            if (!data) return;

            document.getElementById('detail-room-title').textContent = data.title;
            document.getElementById('detail-room-address').textContent = data.address;
            document.getElementById('detail-room-price').textContent = data.price.toLocaleString('vi-VN') + "đ/tháng";
            document.getElementById('detail-room-rating').textContent = data.rating + " ⭐";
            document.getElementById('detail-room-owner').textContent = data.owner;
            document.getElementById('detail-room-sec').textContent = data.sec;
            document.getElementById('detail-room-pets').textContent = data.pets_txt;
            document.getElementById('detail-room-loft').textContent = data.loft_txt;
            document.getElementById('detail-room-balcony').textContent = data.balcony_txt;
            document.getElementById('detail-room-area').textContent = data.area_text;
            document.getElementById('detail-location-description').textContent = data.location_description;
            document.getElementById('detail-scenery-description').textContent = data.scenery_description;
            document.getElementById('detail-space-description').textContent = data.space_description;

            const images = Array.isArray(data.image_urls) && data.image_urls.length > 0 ? data.image_urls : [data.cover_image];
            const mainImage = document.getElementById('detail-main-image');
            const imageCount = document.getElementById('detail-image-count');
            const thumbs = document.getElementById('detail-image-thumbs');

            mainImage.src = images[0];
            mainImage.alt = `Ảnh phòng ${data.room_number}`;
            imageCount.textContent = `${images.length} ảnh phòng ${data.room_number}`;
            thumbs.innerHTML = '';

            images.slice(0, 6).forEach((src, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'h-20 rounded-xl overflow-hidden border border-slate-800 hover:border-emerald-500/70 transition-all focus:outline-none focus:border-emerald-400';
                button.innerHTML = `<img src="${src}" alt="Ảnh phòng ${data.room_number} ${index + 1}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">`;
                button.addEventListener('click', () => {
                    mainImage.src = src;
                    document.querySelectorAll('#detail-image-thumbs button').forEach(btn => btn.classList.remove('border-emerald-400'));
                    button.classList.add('border-emerald-400');
                });
                if (index === 0) {
                    button.classList.add('border-emerald-400');
                }
                thumbs.appendChild(button);
            });

            // Set form action route
            const form = document.getElementById('write-review-form');
            form.action = `/renty/room/${roomId}/review`;

            // Set contact request hidden input
            document.getElementById('contact-room-id').value = roomId;

            // Populate reviews
            const container = document.getElementById('detail-reviews-container');
            container.innerHTML = '';

            if (data.reviews && data.reviews.length > 0) {
                data.reviews.forEach(rev => {
                    const stars = '⭐'.repeat(rev.rating) + '☆'.repeat(5 - rev.rating);
                    const item = document.createElement('div');
                    item.className = 'p-3 rounded-xl bg-slate-900/60 border border-slate-800/50 space-y-1.5';
                    item.innerHTML = `
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-300">${rev.author_name}</span>
                            <span class="text-amber-400 font-semibold">${stars}</span>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">${rev.comment}</p>
                        <span class="block text-[9px] text-slate-600">${rev.created_at}</span>
                    `;
                    container.appendChild(item);
                });
            } else {
                container.innerHTML = `
                    <div class="py-4 text-center text-xs text-slate-500 italic">
                        Chưa có đánh giá thực tế nào cho phòng này. Hãy là người đầu tiên đánh giá!
                    </div>
                `;
            }

            document.getElementById('room-detail-modal').classList.remove('hidden');
        }

        function closeRoomDetailModal() {
            document.getElementById('room-detail-modal').classList.add('hidden');
        }

        // Room lists mock database object for comparison
        const mockRooms = {!! json_encode($rooms->keyBy('id')) !!};

        // Track selected comparison rooms
        let selectedRooms = [];

        function toggleCompare(roomId, checkbox) {
            if (checkbox.checked) {
                if (selectedRooms.length >= 3) {
                    alert('Chỉ được so sánh tối đa 3 phòng trọ cùng lúc!');
                    checkbox.checked = false;
                    return;
                }
                selectedRooms.push(roomId);
            } else {
                selectedRooms = selectedRooms.filter(id => id !== roomId);
            }

            updateCompareDock();
        }

        function updateCompareDock() {
            const dock = document.getElementById('compare-dock');
            const label = document.getElementById('compare-count-label');

            if (selectedRooms.length > 0) {
                dock.classList.remove('hidden');
                label.textContent = `Đang chọn ${selectedRooms.length} phòng trọ`;
            } else {
                dock.classList.add('hidden');
            }
        }

        function clearCompare() {
            selectedRooms = [];
            document.querySelectorAll('.compare-checkbox').forEach(cb => cb.checked = false);
            updateCompareDock();
        }

        // Track radar chart instance
        let compareChartInstance = null;

        function openCompareModal() {
            if (selectedRooms.length === 0) return;
            
            const modal = document.getElementById('compare-modal');
            
            // Pre-clear table values
            for (let col = 1; col <= 3; col++) {
                document.getElementById(`compare-col-${col}-title`).textContent = "-";
                document.getElementById(`compare-val-${col}-price`).textContent = "-";
                document.getElementById(`compare-val-${col}-dist`).textContent = "-";
                document.getElementById(`compare-val-${col}-rating`).textContent = "-";
                document.getElementById(`compare-val-${col}-owner`).textContent = "-";
                document.getElementById(`compare-val-${col}-sec`).textContent = "-";
                document.getElementById(`compare-val-${col}-pets`).textContent = "-";
                document.getElementById(`compare-val-${col}-loft`).textContent = "-";
                document.getElementById(`compare-val-${col}-balcony`).textContent = "-";
            }

            // Construct query string with selected IDs
            const queryParams = selectedRooms.map(id => `ids[]=${id}`).join('&');
            
            fetch(`/api/rooms/compare?${queryParams}`)
                .then(res => res.json())
                .then(response => {
                    if (!response.success || !response.data) return;
                    
                    const roomsData = response.data;
                    const datasets = [];
                    const colors = [
                        { stroke: '#10b981', fill: 'rgba(16, 185, 129, 0.15)' }, // Emerald
                        { stroke: '#6366f1', fill: 'rgba(99, 102, 241, 0.15)' },  // Indigo
                        { stroke: '#06b6d4', fill: 'rgba(6, 182, 212, 0.15)' }    // Cyan
                    ];

                    roomsData.forEach((room, idx) => {
                        const col = idx + 1;
                        
                        // Populate Table
                        document.getElementById(`compare-col-${col}-title`).textContent = `Phòng ${room.room_number}`;
                        document.getElementById(`compare-val-${col}-price`).textContent = room.price_formatted;
                        document.getElementById(`compare-val-${col}-dist`).textContent = room.distance + " km";
                        document.getElementById(`compare-val-${col}-rating`).textContent = room.rating + " ⭐";
                        document.getElementById(`compare-val-${col}-owner`).textContent = "⭐".repeat(room.owner_stars) + "☆".repeat(5 - room.owner_stars) + ` (${room.owner_stars}/5)`;
                        document.getElementById(`compare-val-${col}-sec`).textContent = "⭐".repeat(room.security_stars) + "☆".repeat(5 - room.security_stars) + ` (${room.security_stars}/5)`;
                        
                        const petsElem = document.getElementById(`compare-val-${col}-pets`);
                        petsElem.textContent = room.pets;
                        petsElem.className = room.pets === 'Có' ? 'px-3 py-3 text-xs font-bold text-emerald-400' : 'px-3 py-3 text-xs text-slate-500';

                        const loftElem = document.getElementById(`compare-val-${col}-loft`);
                        loftElem.textContent = room.loft;
                        loftElem.className = room.loft === 'Có' ? 'px-3 py-3 text-xs font-bold text-emerald-400' : 'px-3 py-3 text-xs text-slate-500';

                        const balconyElem = document.getElementById(`compare-val-${col}-balcony`);
                        balconyElem.textContent = room.balcony;
                        balconyElem.className = room.balcony === 'Có' ? 'px-3 py-3 text-xs font-bold text-emerald-400' : 'px-3 py-3 text-xs text-slate-500';

                        // Add to Radar Datasets
                        datasets.push({
                            label: `Phòng ${room.room_number}`,
                            data: [
                                room.scores.price,
                                room.scores.distance,
                                room.scores.security,
                                room.scores.owner
                            ],
                            borderColor: colors[idx].stroke,
                            backgroundColor: colors[idx].fill,
                            borderWidth: 2.5,
                            pointBackgroundColor: colors[idx].stroke,
                            pointBorderColor: '#0a0f1d',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: colors[idx].stroke,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        });
                    });

                    // Render Radar Chart
                    const ctxRadar = document.getElementById('compareRadarChart').getContext('2d');
                    
                    if (compareChartInstance) {
                        compareChartInstance.destroy();
                    }

                    compareChartInstance = new Chart(ctxRadar, {
                        type: 'radar',
                        data: {
                            labels: ['Giá cả', 'Khoảng cách', 'An ninh', 'Chủ nhà'],
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#cbd5e1',
                                        font: { size: 10, weight: 'bold' }
                                    }
                                }
                            },
                            scales: {
                                r: {
                                    angleLines: {
                                        color: 'rgba(255, 255, 255, 0.08)'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.08)'
                                    },
                                    pointLabels: {
                                        color: '#94a3b8',
                                        font: { size: 10, weight: 'bold' }
                                    },
                                    ticks: {
                                        display: false,
                                        stepSize: 2
                                    },
                                    min: 0,
                                    max: 10
                                }
                            }
                        }
                    });
                })
                .catch(err => console.error("Error loading comparison details:", err));

            modal.classList.remove('hidden');
        }

        function closeCompareModal() {
            document.getElementById('compare-modal').classList.add('hidden');
        }

        // Search and filter function
        function filterItems() {
            const query = document.getElementById('search-input').value.toLowerCase();
            const filterPrice = document.getElementById('filter-price').value;
            const filterRating = document.getElementById('filter-rating').value;
            
            const petChecked = document.getElementById('tag-pets').checked;
            const loftChecked = document.getElementById('tag-loft').checked;
            const balconyChecked = document.getElementById('tag-balcony').checked;

            let matchesCount = 0;

            document.querySelectorAll('.room-item-card').forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                const price = parseInt(card.getAttribute('data-price'));
                const rating = parseFloat(card.getAttribute('data-rating'));
                const pets = card.getAttribute('data-pets') === 'true';
                const loft = card.getAttribute('data-loft') === 'true';
                const balcony = card.getAttribute('data-balcony') === 'true';

                let matchesQuery = title.includes(query) || card.textContent.toLowerCase().includes(query);
                
                let matchesPrice = true;
                if (filterPrice !== 'all') {
                    matchesPrice = price <= parseInt(filterPrice);
                }

                let matchesRating = true;
                if (filterRating !== 'all') {
                    matchesRating = rating >= parseFloat(filterRating);
                }

                let matchesTags = true;
                if (petChecked && !pets) matchesTags = false;
                if (loftChecked && !loft) matchesTags = false;
                if (balconyChecked && !balcony) matchesTags = false;

                if (matchesQuery && matchesPrice && matchesRating && matchesTags) {
                    card.classList.remove('hidden');
                    matchesCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            document.getElementById('results-count').textContent = `Danh sách phòng trọ (${matchesCount} kết quả)`;
        }

        function openHotAreasModal() {
            document.getElementById('hot-areas-modal').classList.remove('hidden');
        }

        function closeHotAreasModal() {
            document.getElementById('hot-areas-modal').classList.add('hidden');
        }

        function selectHotArea(areaQuery) {
            document.getElementById('search-input').value = areaQuery;
            filterItems();
            closeHotAreasModal();
            // Highlight search input briefly
            const input = document.getElementById('search-input');
            input.focus();
            input.classList.add('ring-2', 'ring-emerald-500');
            setTimeout(() => {
                input.classList.remove('ring-2', 'ring-emerald-500');
            }, 1000);
        }

        function openNewReviewsModal() {
            document.getElementById('new-reviews-modal').classList.remove('hidden');
        }

        function closeNewReviewsModal() {
            document.getElementById('new-reviews-modal').classList.add('hidden');
        }
    </script>

    <!-- HOT AREAS MODAL -->
    <div id="hot-areas-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-8 shadow-2xl relative max-h-[85vh] overflow-y-auto animate-fade-in">
            <button onclick="closeHotAreasModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-2 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-fire text-amber-500 animate-pulse"></i> Khu Vực Tìm Kiếm Hot
            </h2>
            <p class="text-xs text-slate-500 mb-6 font-semibold">Chọn nhanh khu vực để lọc danh sách phòng trọ tốt nhất quanh các khu đại học lớn.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Area 1 -->
                <div onclick="selectHotArea('Sư Phạm')" class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all hover:-translate-y-0.5 group relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-colors">ĐH Sư Phạm Hà Nội</h3>
                    <p class="text-[11px] text-slate-505 mt-1 leading-relaxed">Khu vực Xuân Thủy đông đúc sinh viên, đi bộ ra trạm tàu điện Nhổn - Cát Linh rất gần.</p>
                    <div class="mt-4 flex justify-between items-center text-[10px] text-slate-400 font-bold">
                        <span>Bán kính: ~0.5 km</span>
                        <span class="text-emerald-400 flex items-center gap-1">Lọc nhanh <i class="fa-solid fa-arrow-right text-[8px]"></i></span>
                    </div>
                </div>
                
                <!-- Area 2 -->
                <div onclick="selectHotArea('Quốc Gia')" class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all hover:-translate-y-0.5 group relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-colors">ĐH Quốc Gia Hà Nội</h3>
                    <p class="text-[11px] text-slate-505 mt-1 leading-relaxed">Trung tâm Cầu Giấy, các ngõ rộng thoáng khí, an ninh an toàn và nhiều phòng trọ cao cấp.</p>
                    <div class="mt-4 flex justify-between items-center text-[10px] text-slate-400 font-bold">
                        <span>Bán kính: ~0.8 km</span>
                        <span class="text-emerald-400 flex items-center gap-1">Lọc nhanh <i class="fa-solid fa-arrow-right text-[8px]"></i></span>
                    </div>
                </div>

                <!-- Area 3 -->
                <div onclick="selectHotArea('Cầu Giấy')" class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all hover:-translate-y-0.5 group relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-colors">Quận Cầu Giấy</h3>
                    <p class="text-[11px] text-slate-505 mt-1 leading-relaxed">Khu vực trung tâm đắc địa tập trung nhiều trường đại học lớn, di chuyển cực kì nhanh chóng.</p>
                    <div class="mt-4 flex justify-between items-center text-[10px] text-slate-400 font-bold">
                        <span>Bán kính: Mọi khoảng cách</span>
                        <span class="text-emerald-400 flex items-center gap-1">Lọc nhanh <i class="fa-solid fa-arrow-right text-[8px]"></i></span>
                    </div>
                </div>

                <!-- Area 4 -->
                <div onclick="selectHotArea('Xuân Thủy')" class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all hover:-translate-y-0.5 group relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-colors">Đường Xuân Thủy</h3>
                    <p class="text-[11px] text-slate-505 mt-1 leading-relaxed">Trục đường chính sầm uất, di chuyển thuận tiện tới các trường đại học xung quanh.</p>
                    <div class="mt-4 flex justify-between items-center text-[10px] text-slate-400 font-bold">
                        <span>Bán kính: ~0.2 km</span>
                        <span class="text-emerald-400 flex items-center gap-1">Lọc nhanh <i class="fa-solid fa-arrow-right text-[8px]"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW REVIEWS MODAL -->
    <div id="new-reviews-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-8 shadow-2xl relative max-h-[85vh] overflow-y-auto animate-fade-in">
            <button onclick="closeNewReviewsModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-2 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-comments text-emerald-400 animate-pulse"></i> Đánh Giá Mới Nhất
            </h2>
            <p class="text-xs text-slate-500 mb-6 font-semibold">Tổng hợp 5 bình luận đánh giá không gian sống thực tế mới được cập nhật trên hệ thống.</p>
            
            <div class="space-y-4">
                @forelse($recentReviews as $rev)
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800/80 space-y-2 relative overflow-hidden group">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold text-slate-200 block"><i class="fa-solid fa-user-circle text-emerald-400 mr-1.5"></i>{{ $rev->author_name }}</span>
                            <span class="text-[9px] text-indigo-400 font-extrabold uppercase mt-0.5 tracking-wider block">Phòng {{ $rev->room ? $rev->room->room_number : 'N/A' }}</span>
                        </div>
                        <span class="text-xs text-amber-400 font-bold flex items-center gap-1 bg-amber-500/5 border border-amber-500/10 px-2 py-0.5 rounded-lg shrink-0">
                            <i class="fa-solid fa-star text-[10px]"></i> {{ $rev->rating }}/5
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 italic pl-1 leading-relaxed border-l-2 border-slate-800">"{{ $rev->comment }}"</p>
                    <span class="text-[9px] text-slate-600 block text-right"><i class="fa-regular fa-clock mr-1"></i>{{ $rev->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @empty
                <div class="text-center text-xs text-slate-500 py-8">
                    <i class="fa-solid fa-comment-slash text-2xl mb-2 text-slate-700 block"></i>
                    <span>Chưa có đánh giá thực tế nào trên hệ thống.</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
