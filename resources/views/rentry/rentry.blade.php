<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Renty Review - Nền tảng tìm kiếm và đánh giá phòng trọ chân thực.">
    <title>Renty Review - Tìm Phòng Trọ & Đánh Giá Không Gian Sống</title>
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
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
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
                    <span class="renty-brand-text text-xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Renty Review</span>
                </a>
            </div>
            
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-400">
                <a href="#" class="text-emerald-400 hover:text-emerald-300">Khám Phá Phòng</a>
                <a href="javascript:void(0)" onclick="openHotAreasModal()" class="hover:text-slate-205 transition-colors">Khu Vực Hot</a>
                <a href="javascript:void(0)" onclick="openNewReviewsModal()" class="hover:text-slate-205 transition-colors">Đánh Giá Mới</a>
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" id="theme-toggle-icon"></i>
                </button>
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
                    <input type="text" id="search-input" onkeyup="filterItems()" class="w-full pl-12 pr-4 py-3 bg-[#0a0e17] border border-slate-800 rounded-2xl text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none text-sm font-semibold" placeholder="VD: Tìm phòng dưới 3 triệu ở Cầu Giấy, gần đại học Bách Khoa...">
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
                 data-room-id="{{ $room['id'] }}"
                 data-price="{{ $room['price'] }}" 
                 data-rating="{{ $room['rating'] }}" 
                 data-pets="{{ $room['pets'] }}" 
                 data-loft="{{ $room['loft'] }}" 
                 data-balcony="{{ $room['balcony'] }}" 
                 data-distance="{{ $room['distance'] }}" 
                 data-area-name="{{ $room['area_name'] }}"
                 data-viewed="false"
                 data-title="{{ $room['title'] }}">
                <div>
                    <!-- Room photo -->
                    <div class="h-48 bg-slate-950 relative overflow-hidden border-b border-slate-900 group">
                        <a href="{{ route('renty.room.show', $room['id']) }}" class="absolute inset-0 z-0">
                            <img src="{{ $room['cover_image'] }}" alt="Ảnh phòng {{ $room['room_number'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/10 to-transparent"></div>
                        </a>

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

                        @if($room['price_warning'])
                            <span class="absolute top-14 right-4 px-2.5 py-1 bg-amber-500/10 text-amber-300 border border-amber-500/25 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5" title="{{ $room['price_warning']['message'] }}">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                {{ $room['price_warning']['type'] === 'low' ? 'Giá quá rẻ' : 'Giá cao' }}
                            </span>
                        @endif

                        <span class="absolute {{ $room['price_warning'] ? 'top-24' : 'top-14' }} right-4 px-2.5 py-1 border rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5 {{ $room['trust_badge']['class'] }}">
                            <i class="fa-solid {{ $room['trust_badge']['icon'] }}"></i>
                            {{ $room['trust_badge']['label'] }}
                        </span>

                        <button type="button" onclick="openQuickRoomPreview(event, '{{ $room['id'] }}')" class="absolute top-4 right-4 px-3 py-1.5 rounded-xl bg-slate-950/82 border border-white/10 text-[10px] font-extrabold text-slate-100 backdrop-blur z-20 flex items-center gap-1.5 hover:border-emerald-400/60 hover:text-emerald-200 quick-eye-button" title="Xem nhanh thông tin phòng">
                            <i class="fa-solid fa-eye text-slate-300"></i>
                            Xem nhanh
                        </button>

                        <div class="absolute left-4 right-4 bottom-4 z-10 flex items-end justify-between gap-3">
                            <div>
                                <span class="block text-[10px] font-extrabold text-white uppercase tracking-widest drop-shadow">{{ $room['media_source_label'] }} phòng</span>
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
                                <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-all line-clamp-1">
                                    <a href="{{ route('renty.room.show', $room['id']) }}">{{ $room['title'] }}</a>
                                </h3>
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
                            @if($room['price_warning'])
                                <div class="mb-3 px-3 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[10px] text-amber-200 font-bold flex items-start gap-2">
                                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                                    <span>{{ $room['price_warning']['label'] }}</span>
                                </div>
                            @endif
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
                <div class="px-5 pb-5 pt-3 border-t border-slate-900/50 flex justify-between items-center bg-slate-950/20 gap-3">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer hover:text-slate-355 transition-colors">
                        <input type="checkbox" onchange="toggleCompare('{{ $room['id'] }}', this)" class="compare-checkbox w-4 h-4 rounded border-slate-800 bg-slate-900 text-emerald-600 focus:ring-0 focus:ring-offset-0">
                        <span>So sánh</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="openReportModal('{{ $room['id'] }}', '{{ e($room['title']) }}')" class="text-xs text-rose-400 hover:text-rose-300 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-flag"></i> Báo cáo
                        </button>
                        <a href="{{ route('renty.room.show', $room['id']) }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            <span>Chi tiết review</span> <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <section id="viewed-rooms-section" class="hidden mb-16">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-400"></i>
                        Phòng bạn đã xem gần đây
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Lưu nhanh trên trình duyệt để tránh mở nhầm lại cùng một phòng.</p>
                </div>
                <button type="button" onclick="clearViewedRooms()" class="text-xs font-bold text-slate-500 hover:text-slate-200">
                    Xóa lịch sử
                </button>
            </div>
            <div id="viewed-rooms-list" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
        </section>
    </main>

    <!-- FLOATING COMPARE BAR -->
    <div id="compare-dock" class="compare-floating-bar hidden">
        <div class="compare-floating-copy">
            <div class="compare-floating-icon">
                <i class="fa-solid fa-code-compare"></i>
            </div>
            <div>
                <strong id="compare-count-label">Đang chọn 1 phòng trọ</strong>
                <span>Tối đa 3 phòng để so sánh</span>
            </div>
        </div>

        <div class="compare-floating-actions">
            <button type="button" onclick="clearCompare()">
                Hủy
            </button>
            <button type="button" onclick="openCompareModal()">
                So sánh ngay
            </button>
        </div>
    </div>

    <!-- ROOM COMPARISON MODAL -->
    <div id="compare-modal" class="compare-modal hidden">
        <div class="compare-panel animate-fade-in">
            <button type="button" onclick="closeCompareModal()" class="compare-close">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="compare-panel-header">
                <h2><i class="fa-solid fa-code-compare text-emerald-400"></i> So sánh phòng đã chọn</h2>
                <p>Vuốt ngang trên điện thoại để xem đủ các phòng.</p>
            </div>

            <div class="compare-table-wrap">
                <table class="compare-table">
                    <thead id="compare-table-head"></thead>
                    <tbody id="compare-table-body"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- QUICK ROOM PREVIEW -->
    <div id="quick-room-preview" class="quick-preview-overlay hidden" aria-hidden="true">
        <div class="quick-preview-panel" role="dialog" aria-modal="true" aria-labelledby="quick-preview-title">
            <button type="button" onclick="closeQuickRoomPreview()" class="quick-preview-close" aria-label="Đóng xem nhanh">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="quick-preview-media">
                <img id="quick-preview-image" src="" alt="Ảnh phòng xem nhanh" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                <span id="quick-preview-media-label">Ảnh thực tế</span>
            </div>

            <div class="quick-preview-content">
                <span class="quick-preview-brand">Renty Review</span>
                <h2 id="quick-preview-title">Phòng trọ</h2>
                <div class="quick-preview-price-row">
                    <strong id="quick-preview-price">0đ</strong>
                    <span id="quick-preview-rating">4.0 ⭐</span>
                </div>

                <div class="quick-preview-facts">
                    <div>
                        <i class="fa-solid fa-ruler-combined"></i>
                        <span id="quick-preview-area">25 m²</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-location-dot"></i>
                        <span id="quick-preview-location">Cầu Giấy</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-video"></i>
                        <span id="quick-preview-video">Có video tour</span>
                    </div>
                </div>

                <div class="quick-preview-tags" id="quick-preview-tags"></div>

                <div class="quick-preview-actions">
                    <a href="tel:0987654321" class="quick-preview-call">
                        <i class="fa-solid fa-phone"></i> Gọi ngay
                    </a>
                    <a href="#" id="quick-preview-detail" class="quick-preview-detail">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ROOM REPORT MODAL -->
    <div id="room-report-modal" class="fixed inset-0 z-[75] bg-[#02040a]/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-3xl bg-[#0a0f1d] border border-slate-800 p-5 md:p-6 shadow-2xl animate-fade-in">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[10px] font-extrabold uppercase tracking-wider">
                        <i class="fa-solid fa-shield-halved"></i> Báo cáo phòng
                    </span>
                    <h2 class="mt-3 text-xl font-extrabold text-slate-100">Nghi ngờ thông tin không an toàn?</h2>
                    <p class="mt-1 text-xs text-slate-500" id="report-room-title">Renty Review sẽ kiểm tra báo cáo này.</p>
                </div>
                <button type="button" onclick="closeReportModal()" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="room-report-form" method="POST" action="" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tên của bạn</label>
                        <input type="text" name="reporter_name" value="{{ Auth::user()->name ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none" placeholder="Có thể bỏ trống">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Số điện thoại</label>
                        <input type="tel" name="reporter_phone" value="{{ Auth::user()->phone ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none" placeholder="Để liên hệ xác minh">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Lý do báo cáo</label>
                    <select name="reason" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none">
                        <option value="scam">Nghi lừa đảo / yêu cầu cọc bất thường</option>
                        <option value="fake_images">Ảnh không đúng thực tế</option>
                        <option value="wrong_price">Giá hoặc phí phát sinh sai</option>
                        <option value="unsafe">Vấn đề an toàn / an ninh</option>
                        <option value="spam">Tin đăng spam / trùng lặp</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mô tả chi tiết</label>
                    <textarea name="description" required minlength="10" rows="4" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none" placeholder="Ví dụ: chủ trọ yêu cầu chuyển cọc trước khi xem phòng, ảnh không giống lúc đến xem..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 rounded-xl border border-slate-800 bg-slate-900 text-slate-300 text-xs font-bold">Hủy</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-extrabold shadow-lg shadow-rose-600/20">
                        <i class="fa-solid fa-flag mr-1.5"></i> Gửi báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ROOM DETAIL & REVIEWS MODAL -->
    <div id="room-detail-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="room-detail-panel w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-5 md:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto animate-fade-in">
            <button onclick="closeRoomDetailModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-xl font-bold mb-2 text-slate-100" id="detail-room-title">SmartRoom Cầu Giấy - Phòng 101</h2>
            <p class="text-xs text-slate-500 mb-6 flex items-center gap-1">
                <i class="fa-solid fa-location-dot text-slate-600"></i> <span id="detail-room-address">Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy</span>
            </p>

            <div class="mb-6">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-images text-emerald-400"></i>
                            Nội dung hình ảnh
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1" id="detail-media-note">Ưu tiên ảnh thật theo từng góc, xem rõ trước khi liên hệ đặt lịch.</p>
                    </div>
                    <button type="button" onclick="openImageZoom()" class="shrink-0 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500/70 text-[11px] font-extrabold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-expand"></i> Zoom
                    </button>
                </div>
                <div class="relative h-72 rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 group">
                    <button type="button" onclick="openImageZoom()" class="absolute inset-0 z-10" aria-label="Phóng to ảnh phòng"></button>
                    <img id="detail-main-image" src="" alt="Ảnh phòng trọ" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.02]" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent pointer-events-none"></div>
                    <div class="absolute left-4 bottom-4">
                        <span class="px-3 py-1.5 rounded-lg bg-slate-950/70 border border-white/10 backdrop-blur text-xs font-bold text-slate-100">
                            <i class="fa-solid fa-camera text-emerald-300 mr-1.5"></i><span id="detail-image-count">Ảnh phòng</span>
                        </span>
                    </div>
                    <div class="absolute right-4 bottom-4 z-20">
                        <span id="detail-image-angle" class="px-3 py-1.5 rounded-lg bg-emerald-500/15 border border-emerald-400/20 backdrop-blur text-xs font-extrabold text-emerald-100">View toàn phòng</span>
                    </div>
                </div>
                <div id="detail-image-thumbs" class="mt-3 grid grid-cols-3 gap-3">
                </div>
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-[10px] font-bold text-slate-400">
                    <span class="rounded-xl border border-slate-800 bg-slate-900/50 px-3 py-2"><i class="fa-solid fa-border-all text-emerald-400 mr-1.5"></i> Toàn phòng</span>
                    <span class="rounded-xl border border-slate-800 bg-slate-900/50 px-3 py-2"><i class="fa-solid fa-shower text-cyan-300 mr-1.5"></i> Nhà vệ sinh</span>
                    <span class="rounded-xl border border-slate-800 bg-slate-900/50 px-3 py-2"><i class="fa-solid fa-kitchen-set text-amber-300 mr-1.5"></i> Bếp</span>
                    <span class="rounded-xl border border-slate-800 bg-slate-900/50 px-3 py-2"><i class="fa-solid fa-sun text-sky-300 mr-1.5"></i> Ban công/cửa sổ</span>
                </div>
            </div>

            <div id="detail-video-section" class="hidden mb-6 p-4 rounded-2xl bg-[#070b13] border border-slate-800/70">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-video text-rose-300"></i>
                            Video / Virtual Tour
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1">Video quay nhanh từ cửa vào phòng giúp cảm nhận diện tích và luồng di chuyển thực tế.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-[9px] font-extrabold text-rose-200 uppercase tracking-wider">Điểm cộng</span>
                </div>
                <video id="detail-room-video" class="w-full max-h-80 rounded-xl border border-slate-800 bg-black" controls preload="metadata"></video>
            </div>

            <div id="detail-video-empty" class="mb-6 p-4 rounded-2xl bg-slate-900/35 border border-dashed border-slate-800 text-xs text-slate-400 flex items-start gap-3">
                <i class="fa-solid fa-mobile-screen-button text-slate-500 mt-0.5"></i>
                <span>Chưa có video tour cho phòng này. Khi chủ trọ thêm video quay từ cửa vào phòng, mục này sẽ hiển thị ngay tại đây.</span>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="text-sm font-extrabold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-table-cells-large text-emerald-400"></i>
                        Thông tin phòng
                    </h3>
                    <strong class="text-amber-400 text-xs" id="detail-room-rating">4.5 sao</strong>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-tag"></i></span>
                        <small>Giá</small>
                        <strong id="detail-room-price">3.200.000đ/tháng</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-ruler-combined"></i></span>
                        <small>Diện tích</small>
                        <strong id="detail-room-area">0 m²</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-location-dot"></i></span>
                        <small>Địa chỉ</small>
                        <strong id="detail-room-area-name">Khu vực trung tâm</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-shield-halved"></i></span>
                        <small>Cọc</small>
                        <strong>1 tháng</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-bolt"></i></span>
                        <small>Điện</small>
                        <strong>3.5k/kWh</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-droplet"></i></span>
                        <small>Nước</small>
                        <strong>20k/người</strong>
                    </div>
                </div>
            </div>

            <div id="detail-price-warning" class="hidden mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-100 text-xs leading-relaxed">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-300 mt-0.5"></i>
                    <div>
                        <strong class="block text-amber-200 mb-1" id="detail-price-warning-title">Cảnh báo giá</strong>
                        <span id="detail-price-warning-message"></span>
                    </div>
                </div>
            </div>

            <div class="mb-6 p-5 rounded-2xl bg-slate-900/45 border border-slate-800/60">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="text-sm font-extrabold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-align-left text-emerald-400"></i>
                        Mô tả chi tiết
                    </h3>
                    <div class="hidden">
                        <span id="detail-room-owner"></span>
                        <span id="detail-room-sec"></span>
                        <span id="detail-room-pets"></span>
                        <span id="detail-room-loft"></span>
                        <span id="detail-room-balcony"></span>
                    </div>
                </div>
                <p class="detail-description detail-description-clamped text-xs text-slate-300 leading-relaxed" id="detail-full-description"></p>
                <button type="button" onclick="toggleDetailDescription(this)" class="mt-3 text-xs font-extrabold text-emerald-400 hover:text-emerald-300">
                    Xem thêm
                </button>
            </div>

            <div class="mb-6 p-5 rounded-2xl bg-[#070b13] border border-slate-800/70">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-calculator text-cyan-300"></i>
                            Chi phí dự kiến khi vào ở
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1">Ước tính nhanh, có thể điều chỉnh theo số người và số xe.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    <div class="cost-stepper">
                        <span>Số người ở</span>
                        <div>
                            <button type="button" id="cost-people-minus" onclick="updateMoveInCost('people', -1)">-</button>
                            <strong id="cost-people">2</strong>
                            <button type="button" id="cost-people-plus" onclick="updateMoveInCost('people', 1)">+</button>
                        </div>
                    </div>
                    <div class="cost-stepper">
                        <span>Số lượng xe</span>
                        <div>
                            <button type="button" onclick="updateMoveInCost('vehicles', -1)">-</button>
                            <strong id="cost-vehicles">1</strong>
                            <button type="button" onclick="updateMoveInCost('vehicles', 1)">+</button>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="cost-row"><span>Tiền phòng</span><strong id="cost-room">3.200.000đ</strong></div>
                    <div class="cost-row"><span>Cọc</span><strong id="cost-deposit">3.200.000đ</strong></div>
                    <div class="cost-row"><span>Điện dự kiến</span><strong id="cost-electric">350.000đ</strong></div>
                    <div class="cost-row"><span>Nước</span><strong id="cost-water">40.000đ</strong></div>
                    <div class="cost-row"><span>Phí dịch vụ / xe</span><strong id="cost-service">150.000đ</strong></div>
                    <div class="cost-row cost-total"><span>Tổng ban đầu</span><strong id="cost-total">6.940.000đ</strong></div>
                </div>
            </div>

            <!-- Contact landlord and request consultation -->
            <div class="border-t border-slate-800/60 pt-6 mb-6">
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

            </div>

            <!-- Reviews: summary, list, and write form in one place -->
            <div class="reviews-end-section border-t border-slate-800/60 pt-6">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="text-sm font-bold text-slate-300"><i class="fa-solid fa-comments text-amber-400 mr-1.5"></i>Đánh giá thực tế</h3>
                    <button type="button" id="review-summary-btn" onclick="loadReviewSummary(this)" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> AI tóm tắt
                    </button>
                </div>
                <div id="review-summary-box" class="hidden mb-3 rounded-xl bg-slate-900/70 border border-slate-800 p-3 text-xs text-slate-300"></div>
                <div class="p-4 rounded-xl bg-[#070b13] border border-slate-800/60 mb-4">
                    <div class="flex flex-col md:flex-row md:items-start gap-4 mb-4">
                        <div class="min-w-20">
                            <strong class="block text-4xl leading-none text-slate-100" id="review-average-score">4.8</strong>
                            <span class="block text-amber-400 text-xs mt-1" id="review-average-stars">★★★★★</span>
                            <small class="block text-[10px] text-slate-500 mt-1" id="review-count-label">24 đánh giá</small>
                        </div>
                        <div class="flex-1 space-y-2" id="review-score-bars"></div>
                    </div>
                    <a href="https://zalo.me/0987654321" target="_blank" class="w-full min-h-10 rounded-lg bg-blue-600/20 border border-blue-500/30 text-blue-200 text-xs font-extrabold flex items-center justify-center gap-2 hover:bg-blue-600/30 transition-all">
                        <i class="fa-solid fa-comments"></i> Chat Zalo để hỏi phòng
                    </a>
                </div>
                <div class="space-y-3" id="detail-reviews-container">
                    <!-- Dynamic list of reviews -->
                </div>
                <button type="button" id="show-all-reviews-btn" onclick="toggleAllReviews()" class="hidden mt-3 w-full min-h-11 rounded-xl border border-slate-800 bg-slate-900/50 text-slate-200 text-xs font-extrabold hover:border-slate-700">
                    Xem tất cả đánh giá
                </button>

                <div class="mt-6 pt-6 border-t border-slate-800/60">
                    <h3 class="text-sm font-bold text-slate-300 mb-3">Gửi đánh giá của bạn</h3>
                    <form id="write-review-form" action="" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

            <div class="sticky-contact-bar">
                <div class="sticky-price">
                    <span>Giá thuê</span>
                    <strong id="sticky-room-price">3.2tr/tháng</strong>
                </div>
                <div class="sticky-actions">
                    <a href="tel:0987654321" class="sticky-call-button"><i class="fa-solid fa-phone"></i> Gọi điện</a>
                    <a href="https://zalo.me/0987654321" target="_blank" class="sticky-zalo-button"><i class="fa-solid fa-comments"></i> Chat Zalo</a>
                </div>
            </div>
        </div>
    </div>

    <!-- IMAGE ZOOM MODAL -->
    <div id="image-zoom-modal" class="fixed inset-0 z-[60] bg-[#02040a]/95 backdrop-blur-md hidden flex items-center justify-center p-4">
        <button type="button" onclick="closeImageZoom()" class="absolute top-5 right-5 w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-300 hover:text-white">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <button type="button" onclick="changeZoomImage(-1)" class="absolute left-4 md:left-8 w-11 h-11 rounded-full bg-slate-900/80 border border-slate-800 hover:border-emerald-500/70 text-slate-200 flex items-center justify-center">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <img id="zoom-main-image" src="" alt="Ảnh phòng phóng to" class="max-w-full max-h-[84vh] rounded-2xl object-contain border border-slate-800 shadow-2xl">
        <button type="button" onclick="changeZoomImage(1)" class="absolute right-4 md:right-8 w-11 h-11 rounded-full bg-slate-900/80 border border-slate-800 hover:border-emerald-500/70 text-slate-200 flex items-center justify-center">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
        <div class="absolute left-1/2 -translate-x-1/2 bottom-5 px-4 py-2 rounded-xl bg-slate-950/80 border border-white/10 text-xs font-bold text-slate-200">
            <span id="zoom-image-label">View toàn phòng</span>
            <span class="text-slate-500 mx-2">·</span>
            <span id="zoom-image-count">1/1</span>
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

        function applyThemeMode(mode) {
            const isLight = mode === 'light';
            document.documentElement.classList.toggle('theme-light', isLight);
            document.body.classList.toggle('theme-light', isLight);
            document.querySelectorAll('#theme-toggle-icon').forEach(icon => {
                icon.classList.toggle('fa-sun', isLight);
                icon.classList.toggle('fa-moon', !isLight);
            });
        }

        function toggleThemeMode() {
            const nextMode = document.body.classList.contains('theme-light') ? 'dark' : 'light';
            localStorage.setItem('renty_theme_mode', nextMode);
            applyThemeMode(nextMode);
        }

        applyThemeMode(localStorage.getItem('renty_theme_mode') || 'dark');

        let currentDetailRoomId = null;
        let activeRoomReviews = [];
        let activeRoomImages = [];
        let activeRoomImageIndex = 0;
        const MOVE_IN_MAX_PEOPLE = 5;
        let activeRoomCost = {
            room: 0,
            people: 2,
            vehicles: 1
        };

        function formatCurrency(value) {
            return Number(value || 0).toLocaleString('vi-VN') + 'đ';
        }

        function formatShortPrice(value) {
            const millions = Number(value || 0) / 1000000;
            return `${millions.toFixed(millions % 1 === 0 ? 0 : 1)}tr/tháng`;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function openQuickRoomPreview(event, roomId) {
            event.preventDefault();
            event.stopPropagation();

            const data = mockRooms[roomId];
            if (!data) return;

            const modal = document.getElementById('quick-room-preview');
            document.getElementById('quick-preview-image').src = data.cover_image;
            document.getElementById('quick-preview-media-label').textContent = `${data.media_source_label || 'Ảnh phòng'} · ${Array.isArray(data.image_urls) ? data.image_urls.length : 1} ảnh`;
            document.getElementById('quick-preview-title').textContent = data.title;
            document.getElementById('quick-preview-price').textContent = formatCurrency(data.price);
            document.getElementById('quick-preview-rating').textContent = `${data.rating} ⭐`;
            document.getElementById('quick-preview-area').textContent = data.area_text || `${data.area || 0} m²`;
            document.getElementById('quick-preview-location').textContent = data.area_name || 'Khu vực trung tâm';
            document.getElementById('quick-preview-video').textContent = data.video_url ? 'Có video tour' : 'Chưa có video tour';
            document.getElementById('quick-preview-detail').href = `/renty/room/${data.id}`;

            const tags = [
                data.loft_txt === 'Có' ? 'Có gác lửng' : 'Không gác lửng',
                data.balcony_txt === 'Có' ? 'Ban công/cửa sổ' : 'Không ban công',
                data.pets_txt === 'Có' ? 'Cho nuôi thú cưng' : 'Không thú cưng',
            ];
            document.getElementById('quick-preview-tags').innerHTML = tags
                .map(tag => `<span>${escapeHtml(tag)}</span>`)
                .join('');

            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }

        function closeQuickRoomPreview() {
            const modal = document.getElementById('quick-room-preview');
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        function openReportModal(roomId, title) {
            const modal = document.getElementById('room-report-modal');
            const form = document.getElementById('room-report-form');
            form.action = `/renty/room/${roomId}/report`;
            document.getElementById('report-room-title').textContent = title || 'Renty Review sẽ kiểm tra báo cáo này.';
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeReportModal() {
            document.getElementById('room-report-modal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function updateMoveInCost(type, delta) {
            if (type === 'people') {
                activeRoomCost.people = Math.min(MOVE_IN_MAX_PEOPLE, Math.max(1, activeRoomCost.people + delta));
            }

            if (type === 'vehicles') {
                activeRoomCost.vehicles = Math.max(0, activeRoomCost.vehicles + delta);
            }

            const room = Number(activeRoomCost.room || 0);
            const deposit = room;
            const electric = 350000;
            const water = activeRoomCost.people * 20000;
            const service = 100000 + activeRoomCost.vehicles * 50000;
            const total = room + deposit + electric + water + service;

            document.getElementById('cost-people').textContent = activeRoomCost.people;
            document.getElementById('cost-vehicles').textContent = activeRoomCost.vehicles;
            document.getElementById('cost-room').textContent = formatCurrency(room);
            document.getElementById('cost-deposit').textContent = formatCurrency(deposit);
            document.getElementById('cost-electric').textContent = formatCurrency(electric);
            document.getElementById('cost-water').textContent = formatCurrency(water);
            document.getElementById('cost-service').textContent = formatCurrency(service);
            document.getElementById('cost-total').textContent = formatCurrency(total);

            const peopleMinus = document.getElementById('cost-people-minus');
            const peoplePlus = document.getElementById('cost-people-plus');
            peopleMinus.disabled = activeRoomCost.people <= 1;
            peoplePlus.disabled = activeRoomCost.people >= MOVE_IN_MAX_PEOPLE;
            peopleMinus.classList.toggle('opacity-40', peopleMinus.disabled);
            peoplePlus.classList.toggle('opacity-40', peoplePlus.disabled);
            peopleMinus.classList.toggle('cursor-not-allowed', peopleMinus.disabled);
            peoplePlus.classList.toggle('cursor-not-allowed', peoplePlus.disabled);
        }

        function toggleDetailDescription(button) {
            const description = document.getElementById('detail-full-description');
            description.classList.toggle('detail-description-clamped');
            button.textContent = description.classList.contains('detail-description-clamped') ? 'Xem thêm' : 'Thu gọn';
        }

        function renderReviewSummary(data) {
            const average = Number(data.rating || 0);
            const reviewCount = Array.isArray(data.reviews) ? data.reviews.length : 0;
            const criteria = [
                ['Sạch sẽ', Math.min(5, average + 0.1)],
                ['Vị trí', Math.max(3.5, average - 0.1)],
                ['Chủ nhà', Math.min(5, average + 0.05)],
                ['Giá cả', Math.max(3.5, average - 0.2)]
            ];

            document.getElementById('review-average-score').textContent = average.toFixed(1);
            document.getElementById('review-average-stars').textContent = '★'.repeat(Math.round(average)) + '☆'.repeat(5 - Math.round(average));
            document.getElementById('review-count-label').textContent = reviewCount > 0 ? `${reviewCount} đánh giá` : 'Chưa có đánh giá';
            document.getElementById('review-score-bars').innerHTML = criteria.map(([label, score]) => `
                <div class="review-score-row">
                    <span>${label}</span>
                    <div><i style="width: ${(score / 5) * 100}%"></i></div>
                    <strong>${score.toFixed(1)}</strong>
                </div>
            `).join('');
        }

        function renderReviews(showAll = false) {
            const container = document.getElementById('detail-reviews-container');
            const button = document.getElementById('show-all-reviews-btn');
            container.innerHTML = '';

            if (!activeRoomReviews.length) {
                container.innerHTML = `
                    <div class="py-4 text-center text-xs text-slate-500 italic">
                        Chưa có đánh giá thực tế nào cho phòng này. Hãy là người đầu tiên đánh giá!
                    </div>
                `;
                button.classList.add('hidden');
                return;
            }

            activeRoomReviews.slice(0, showAll ? activeRoomReviews.length : 2).forEach(rev => {
                const rating = Math.max(1, Math.min(5, Number(rev.rating || 5)));
                const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
                const item = document.createElement('div');
                item.className = 'p-3 rounded-xl bg-slate-900/60 border border-slate-800/50 space-y-1.5';
                item.innerHTML = `
                    <div class="flex justify-between items-center text-xs gap-3">
                        <span class="font-bold text-slate-300">${escapeHtml(rev.author_name)}</span>
                        <span class="text-amber-400 font-semibold whitespace-nowrap">${stars}</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">${escapeHtml(rev.comment)}</p>
                    <span class="block text-[9px] text-slate-600">${escapeHtml(rev.created_at)}</span>
                `;
                container.appendChild(item);
            });

            if (activeRoomReviews.length > 2) {
                button.classList.remove('hidden');
                button.textContent = showAll ? 'Thu gọn đánh giá' : 'Xem tất cả đánh giá';
                button.dataset.expanded = showAll ? 'true' : 'false';
            } else {
                button.classList.add('hidden');
            }
        }

        function toggleAllReviews() {
            const button = document.getElementById('show-all-reviews-btn');
            renderReviews(button.dataset.expanded !== 'true');
        }

        function normalizeRoomImages(data) {
            const rawAngles = Array.isArray(data.image_angles) ? data.image_angles : [];
            const rawUrls = Array.isArray(data.image_urls) && data.image_urls.length > 0 ? data.image_urls : [data.cover_image];

            return rawUrls.filter(Boolean).map((url, index) => {
                const angle = rawAngles[index] || {};
                return {
                    url,
                    label: angle.label || `Ảnh thực tế ${index + 1}`
                };
            });
        }

        function setActiveRoomImage(index) {
            if (!activeRoomImages.length) return;

            activeRoomImageIndex = Math.max(0, Math.min(activeRoomImages.length - 1, index));
            const image = activeRoomImages[activeRoomImageIndex];
            const mainImage = document.getElementById('detail-main-image');
            const angle = document.getElementById('detail-image-angle');

            mainImage.src = image.url;
            angle.textContent = image.label;
            document.querySelectorAll('#detail-image-thumbs button').forEach((btn, btnIndex) => {
                btn.classList.toggle('border-emerald-400', btnIndex === activeRoomImageIndex);
            });
        }

        function openImageZoom() {
            if (!activeRoomImages.length) return;

            const modal = document.getElementById('image-zoom-modal');
            modal.classList.remove('hidden');
            renderZoomImage();
        }

        function renderZoomImage() {
            const image = activeRoomImages[activeRoomImageIndex];
            if (!image) return;

            document.getElementById('zoom-main-image').src = image.url;
            document.getElementById('zoom-image-label').textContent = image.label;
            document.getElementById('zoom-image-count').textContent = `${activeRoomImageIndex + 1}/${activeRoomImages.length}`;
        }

        function changeZoomImage(delta) {
            if (!activeRoomImages.length) return;

            activeRoomImageIndex = (activeRoomImageIndex + delta + activeRoomImages.length) % activeRoomImages.length;
            setActiveRoomImage(activeRoomImageIndex);
            renderZoomImage();
        }

        function closeImageZoom() {
            document.getElementById('image-zoom-modal').classList.add('hidden');
        }

        function getViewedRoomIds() {
            try {
                return JSON.parse(localStorage.getItem('renty_viewed_rooms') || '[]');
            } catch (error) {
                return [];
            }
        }

        function saveViewedRoom(roomId) {
            const normalizedId = String(roomId);
            const viewedIds = getViewedRoomIds().filter(id => id !== normalizedId);
            viewedIds.unshift(normalizedId);
            localStorage.setItem('renty_viewed_rooms', JSON.stringify(viewedIds.slice(0, 6)));
            renderViewedRooms();
        }

        function clearViewedRooms() {
            localStorage.removeItem('renty_viewed_rooms');
            document.querySelectorAll('.room-item-card').forEach(card => {
                card.dataset.viewed = 'false';
                card.classList.remove('room-card-viewed');
                card.querySelector('.viewed-room-badge')?.remove();
            });
            renderViewedRooms();
        }

        function renderViewedRooms() {
            const section = document.getElementById('viewed-rooms-section');
            const list = document.getElementById('viewed-rooms-list');
            const viewedIds = getViewedRoomIds();

            document.querySelectorAll('.room-item-card').forEach(card => {
                const isViewed = viewedIds.includes(String(card.dataset.roomId));
                card.dataset.viewed = isViewed ? 'true' : 'false';
                card.classList.toggle('room-card-viewed', isViewed);

                if (isViewed && !card.querySelector('.viewed-room-badge')) {
                    const badge = document.createElement('span');
                    badge.className = 'viewed-room-badge absolute left-4 top-14 z-10';
                    badge.innerHTML = '<i class="fa-solid fa-eye mr-1"></i> Đã xem';
                    card.querySelector('.h-48')?.appendChild(badge);
                }

                if (!isViewed) {
                    card.querySelector('.viewed-room-badge')?.remove();
                }
            });

            if (!section || !list) return;

            const viewedRooms = viewedIds.map(id => mockRooms[id]).filter(Boolean);
            if (!viewedRooms.length) {
                section.classList.add('hidden');
                list.innerHTML = '';
                return;
            }

            section.classList.remove('hidden');
            list.innerHTML = viewedRooms.map(room => `
                <a href="/renty/room/${room.id}" class="viewed-room-chip">
                    <img src="${room.cover_image}" alt="Phòng ${room.room_number}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                    <span>
                        <strong>${escapeHtml(room.title)}</strong>
                        <small>${Number(room.price || 0).toLocaleString('vi-VN')}đ/tháng · ${escapeHtml(room.area_text || '')}</small>
                    </span>
                </a>
            `).join('');
        }

        function parseNaturalSearch(query) {
            const normalized = query
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd');

            const parsed = {
                maxPrice: null,
                keywords: normalized.split(/\s+/).filter(Boolean),
                locations: [],
                amenities: {
                    pets: normalized.includes('thu cung') || normalized.includes('pet'),
                    loft: normalized.includes('gac') || normalized.includes('gac lung'),
                    balcony: normalized.includes('ban cong')
                },
                near: []
            };

            const priceMatch = normalized.match(/(?:duoi|nho hon|toi da|<=?)\s*(\d+(?:[.,]\d+)?)\s*(trieu|tr|m|000000)?/);
            if (priceMatch) {
                const amount = parseFloat(priceMatch[1].replace(',', '.'));
                parsed.maxPrice = amount < 100000 ? amount * 1000000 : amount;
            }

            const locationAliases = [
                ['cau giay', 'cầu giấy'],
                ['thanh xuan', 'thanh xuân'],
                ['quan 10', 'quận 10'],
                ['bach khoa', 'bách khoa'],
                ['dai hoc bach khoa', 'đại học bách khoa'],
                ['su pham', 'sư phạm'],
                ['quoc gia', 'quốc gia'],
                ['xuan thuy', 'xuân thủy']
            ];

            locationAliases.forEach(([plain, label]) => {
                if (normalized.includes(plain)) {
                    parsed.locations.push(plain);
                    parsed.near.push(label);
                }
            });

            return parsed;
        }

        function normalizeText(value) {
            return String(value || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd');
        }

        // Room details modal
        function openRoomDetailModal(roomId) {
            const data = mockRooms[roomId];
            if (!data) return;

            currentDetailRoomId = roomId;
            const summaryBox = document.getElementById('review-summary-box');
            summaryBox.classList.add('hidden');
            summaryBox.innerHTML = '';

            document.getElementById('detail-room-title').textContent = data.title;
            document.getElementById('detail-room-address').textContent = data.address;
            document.getElementById('detail-media-note').textContent = data.media_source_note || 'Ưu tiên ảnh thật theo từng góc, xem rõ trước khi liên hệ đặt lịch.';
            document.getElementById('detail-room-price').textContent = data.price.toLocaleString('vi-VN') + "đ/tháng";
            document.getElementById('sticky-room-price').textContent = formatShortPrice(data.price);
            document.getElementById('detail-room-rating').textContent = data.rating + " ⭐";
            document.getElementById('detail-room-owner').textContent = data.owner;
            document.getElementById('detail-room-sec').textContent = data.sec;
            document.getElementById('detail-room-pets').textContent = data.pets_txt;
            document.getElementById('detail-room-loft').textContent = data.loft_txt;
            document.getElementById('detail-room-balcony').textContent = data.balcony_txt;
            document.getElementById('detail-room-area').textContent = data.area_text;
            document.getElementById('detail-room-area-name').textContent = (data.address || '').split('(')[0].trim() || 'Khu vực trung tâm';

            const fullDescription = [
                data.location_description,
                data.scenery_description,
                data.space_description,
                `Tiện ích nổi bật: ${data.loft_txt === 'Có' ? 'có gác lửng' : 'không gác lửng'}, ${data.balcony_txt === 'Có' ? 'có ban công' : 'không ban công'}, ${data.pets_txt === 'Có' ? 'có thể nuôi thú cưng' : 'không nuôi thú cưng'}.`
            ].filter(Boolean).join(' ');
            const description = document.getElementById('detail-full-description');
            description.textContent = fullDescription;
            description.classList.add('detail-description-clamped');
            const descButton = description.nextElementSibling;
            if (descButton) descButton.textContent = 'Xem thêm';

            activeRoomCost = {
                room: Number(data.price || 0),
                people: 2,
                vehicles: 1
            };
            updateMoveInCost();

            const images = Array.isArray(data.image_urls) && data.image_urls.length > 0 ? data.image_urls : [data.cover_image];
            const mainImage = document.getElementById('detail-main-image');
            const imageCount = document.getElementById('detail-image-count');
            const thumbs = document.getElementById('detail-image-thumbs');
            const videoSection = document.getElementById('detail-video-section');
            const videoEmpty = document.getElementById('detail-video-empty');
            const roomVideo = document.getElementById('detail-room-video');

            activeRoomImages = normalizeRoomImages(data);
            activeRoomImageIndex = 0;
            mainImage.alt = `Ảnh phòng ${data.room_number}`;
            imageCount.textContent = `${images.length} ảnh phòng ${data.room_number}`;
            thumbs.innerHTML = '';
            setActiveRoomImage(0);

            activeRoomImages.slice(0, 6).forEach((image, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'relative h-20 rounded-xl overflow-hidden border border-slate-800 hover:border-emerald-500/70 transition-all focus:outline-none focus:border-emerald-400';
                button.innerHTML = `
                    <img src="${image.url}" alt="${escapeHtml(image.label)} phòng ${data.room_number}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                    <span class="absolute left-1.5 right-1.5 bottom-1.5 rounded-md bg-slate-950/75 px-1.5 py-0.5 text-[8px] font-extrabold text-slate-100 truncate">${escapeHtml(image.label)}</span>
                `;
                button.addEventListener('click', () => {
                    setActiveRoomImage(index);
                });
                if (index === 0) {
                    button.classList.add('border-emerald-400');
                }
                thumbs.appendChild(button);
            });

            if (data.video_url) {
                roomVideo.src = data.video_url;
                videoSection.classList.remove('hidden');
                videoEmpty.classList.add('hidden');
            } else {
                roomVideo.removeAttribute('src');
                roomVideo.load();
                videoSection.classList.add('hidden');
                videoEmpty.classList.remove('hidden');
            }

            // Set form action route
            const form = document.getElementById('write-review-form');
            form.action = `/renty/room/${roomId}/review`;

            // Set contact request hidden input
            document.getElementById('contact-room-id').value = roomId;

            activeRoomReviews = Array.isArray(data.reviews) ? data.reviews : [];
            renderReviewSummary(data);
            renderReviews(false);

            const warningBox = document.getElementById('detail-price-warning');
            if (data.price_warning) {
                document.getElementById('detail-price-warning-title').textContent = data.price_warning.label;
                document.getElementById('detail-price-warning-message').textContent = data.price_warning.message;
                warningBox.classList.remove('hidden');
            } else {
                warningBox.classList.add('hidden');
            }

            saveViewedRoom(roomId);

            document.getElementById('room-detail-modal').classList.remove('hidden');
        }

        function loadReviewSummary(btn) {
            if (!currentDetailRoomId) return;

            const box = document.getElementById('review-summary-box');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang tóm tắt...';
            box.classList.remove('hidden');
            box.textContent = 'AI đang đọc các review...';

            fetch(`/api/renty/rooms/${currentDetailRoomId}/reviews/summary`)
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = original;

                    if (!data.success) {
                        box.textContent = 'Không thể tóm tắt review.';
                        return;
                    }

                    const summary = data.summary;
                    const pros = (summary.pros || []).map(item => `<li>${escapeHtml(item)}</li>`).join('');
                    const cons = (summary.cons || []).map(item => `<li>${escapeHtml(item)}</li>`).join('');
                    box.innerHTML = `
                        <div class="font-bold text-slate-200">${escapeHtml(summary.summary || '')}</div>
                        ${pros ? `<div class="mt-2 text-emerald-300 font-bold">Ưu điểm</div><ul class="list-disc pl-5">${pros}</ul>` : ''}
                        ${cons ? `<div class="mt-2 text-amber-300 font-bold">Cần lưu ý</div><ul class="list-disc pl-5">${cons}</ul>` : ''}
                    `;
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = original;
                    box.textContent = 'Không thể kết nối AI để tóm tắt review.';
                });
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function closeRoomDetailModal() {
            document.getElementById('room-detail-modal').classList.add('hidden');
            closeImageZoom();
        }

        // Room lists mock database object for detail and comparison.
        const mockRooms = {!! json_encode($rooms->keyBy('id')) !!};
        window.rentyRooms = mockRooms;

        document.getElementById('image-zoom-modal')?.addEventListener('click', (event) => {
            if (event.target.id === 'image-zoom-modal') {
                closeImageZoom();
            }
        });

        document.getElementById('quick-room-preview')?.addEventListener('click', (event) => {
            if (event.target.id === 'quick-room-preview') {
                closeQuickRoomPreview();
            }
        });

        document.getElementById('room-report-modal')?.addEventListener('click', (event) => {
            if (event.target.id === 'room-report-modal') {
                closeReportModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeImageZoom();
                closeQuickRoomPreview();
                closeReportModal();
            }

            if (!document.getElementById('image-zoom-modal')?.classList.contains('hidden')) {
                if (event.key === 'ArrowLeft') changeZoomImage(-1);
                if (event.key === 'ArrowRight') changeZoomImage(1);
            }
        });

        // Search and filter function
        function filterItems() {
            const query = document.getElementById('search-input').value;
            const parsedSearch = parseNaturalSearch(query);
            const normalizedQuery = normalizeText(query);
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
                const searchableText = normalizeText(`${card.getAttribute('data-title')} ${card.getAttribute('data-area-name')} ${card.textContent}`);

                let matchesQuery = true;
                if (normalizedQuery.trim() !== '') {
                    const importantTerms = parsedSearch.keywords.filter(term => !['tim', 'phong', 'tro', 'duoi', 'o', 'gan', 'dai', 'hoc', 'trieu', 'tr', 'gia'].includes(term));
                    matchesQuery = importantTerms.length === 0 || importantTerms.some(term => searchableText.includes(term));
                }
                
                let matchesPrice = true;
                if (filterPrice !== 'all') {
                    matchesPrice = price <= parseInt(filterPrice);
                }
                if (parsedSearch.maxPrice) {
                    matchesPrice = matchesPrice && price <= parsedSearch.maxPrice;
                }

                let matchesRating = true;
                if (filterRating !== 'all') {
                    matchesRating = rating >= parseFloat(filterRating);
                }

                let matchesTags = true;
                if (petChecked && !pets) matchesTags = false;
                if (loftChecked && !loft) matchesTags = false;
                if (balconyChecked && !balcony) matchesTags = false;
                if (parsedSearch.amenities.pets && !pets) matchesTags = false;
                if (parsedSearch.amenities.loft && !loft) matchesTags = false;
                if (parsedSearch.amenities.balcony && !balcony) matchesTags = false;

                let matchesLocation = true;
                if (parsedSearch.locations.length > 0) {
                    matchesLocation = parsedSearch.locations.some(location => searchableText.includes(location));
                }

                if (matchesQuery && matchesPrice && matchesRating && matchesTags && matchesLocation) {
                    card.classList.remove('hidden');
                    matchesCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            document.getElementById('results-count').textContent = `Danh sách phòng trọ (${matchesCount} kết quả)`;
        }

        window.addEventListener('DOMContentLoaded', renderViewedRooms);

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
