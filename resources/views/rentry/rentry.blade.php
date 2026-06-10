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

    <!-- Leaflet Map Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Custom CSS -->
    <!-- Pass Laravel variables to Global JS context -->
    <script>
        window.rentyRoomsData = {!! json_encode($rooms->keyBy('id')) !!};
        window.rentySessionSuccess = {!! json_encode(session('success')) !!};
        window.rentySessionError = {!! json_encode(session('error')) !!};
    </script>

    <!-- Custom CSS & JS -->
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-emerald-500 selection:text-white">
    <div id="theme-flip-wash" class="theme-flip-wash" aria-hidden="true"></div>

    <!-- Decorative glows -->
    <div class="absolute top-[-15%] left-[-10%] w-[500px] h-[500px] rounded-full bg-emerald-600/5 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-600/5 blur-[120px] pointer-events-none"></div>

    <div id="renty-search-backdrop" class="renty-search-backdrop" onclick="blurRentySearch()"></div>

    <!-- NAVBAR -->
    <header class="h-20 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md sticky top-0 z-40 flex items-center">
        <div class="container mx-auto px-6 flex justify-between items-center gap-4">
            <!-- Left: Logo and Nav Links -->
            <div class="flex items-center gap-6 shrink-0">
                <a href="{{ route('smartroom.portal') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fa-solid fa-magnifying-glass-location text-white text-lg"></i>
                    </div>
                    <span class="renty-brand-text text-xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Renty</span>
                </a>

                <nav class="hidden md:flex items-center gap-5 text-xs font-semibold text-slate-400">
                    <a href="#" class="hover:text-emerald-400 transition-colors">Khám phá</a>
                    <a href="javascript:void(0)" onclick="openHotAreasModal()" class="hover:text-emerald-400 transition-colors">Khu vực</a>
                    <a href="javascript:void(0)" onclick="setViewMode('map')" class="hover:text-emerald-400 transition-colors flex items-center gap-1.5">
                        Bản đồ
                        <span class="px-1.5 py-0.5 text-[8px] font-black bg-emerald-500 text-white rounded-md uppercase tracking-wider animate-pulse">🆕</span>
                    </a>
                </nav>
            </div>
            
            <!-- Middle: Search Bar (Glassmorphism Renty search panel) -->
            <div id="renty-search-panel" class="renty-search-panel flex-grow max-w-md mx-4 relative hidden lg:block">
                <div class="relative w-full renty-search-shell">
                    <div class="renty-search-focus-ring"></div>
                    <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 renty-search-icon"></i>
                    <input type="text" id="search-input" onkeyup="filterItems()" onfocus="openRentySearchSuggestions()" class="renty-search-input w-full pl-11 pr-4 py-2.5 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none text-xs font-semibold" placeholder="Tìm kiếm trọ, khu vực, tiện ích...">
                    
                    <div id="renty-search-suggestions" class="renty-search-suggestions">
                        <div class="flex items-center justify-between gap-3 mb-2.5">
                            <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500">Gợi ý nhanh</span>
                            <span class="text-[9px] font-bold text-emerald-400">Nhấn để tìm ngay</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="applySearchSuggestion('Cầu Giấy')" class="renty-suggestion-chip text-[10px] px-2.5 py-1">
                                <i class="fa-solid fa-location-dot text-[9px]"></i> Cầu Giấy
                            </button>
                            <button type="button" onclick="applySearchSuggestion('Bách Khoa')" class="renty-suggestion-chip text-[10px] px-2.5 py-1">
                                <i class="fa-solid fa-graduation-cap text-[9px]"></i> Bách Khoa
                            </button>
                            <button type="button" onclick="applySearchSuggestion('phòng dưới 3 triệu')" class="renty-suggestion-chip text-[10px] px-2.5 py-1">
                                <i class="fa-solid fa-tags text-[9px]"></i> Dưới 3 triệu
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Admin Profile and Theme Mode Switch -->
            <div class="flex items-center gap-3 shrink-0">
                <nav class="flex items-center gap-3 text-xs font-semibold">
                    @auth
                        <div class="flex items-center gap-3 bg-slate-900/40 border border-slate-800/80 px-3.5 py-1.5 rounded-xl">
                            <span class="font-bold text-slate-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-user-circle text-emerald-400"></i> {{ Auth::user()->name }}
                            </span>
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('smartroom.admin') }}" class="px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 hover:bg-indigo-500/20 transition-all font-semibold" title="Cổng Admin">
                                    Admin
                                </a>
                            @endif
                            <span class="w-[1px] h-3 bg-slate-800"></span>
                            <a href="{{ route('signout') }}" class="font-semibold text-rose-450 hover:text-rose-400 transition-colors">
                                Đăng xuất
                            </a>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 transition-all font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-right-to-bracket text-emerald-400"></i> Đăng Nhập
                        </a>
                    @endauth
                    
                    <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button renty-theme-switch" aria-label="Chuyển chế độ sáng tối" data-theme-switch>
                        <span class="theme-switch-track">
                            <span class="theme-switch-knob">
                                <i class="fa-solid fa-moon theme-switch-icon theme-switch-moon"></i>
                                <i class="fa-solid fa-sun theme-switch-icon theme-switch-sun"></i>
                            </span>
                        </span>
                    </button>
                </nav>
            </div>
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

        <!-- Visual & Advanced Filters Panel -->
        <div class="max-w-4xl mx-auto bg-slate-900/35 border border-slate-800/80 p-5 rounded-3xl mb-8 backdrop-blur-sm">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 pb-4 border-b border-slate-800/50 mb-4">
                <div class="text-left w-full md:w-auto">
                    <span class="block text-xs font-bold text-slate-350 uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fa-solid fa-wand-magic-sparkles text-teal-400"></i>
                        Bộ lọc nhanh trực quan
                    </span>
                    <span class="block text-[10px] text-slate-500 mt-0.5">Click nhanh để lọc phòng theo các tiêu chí phổ biến</span>
                </div>
                
                <div class="flex gap-2 w-full md:w-auto justify-end">
                    <button onclick="toggleFilterDrawer()" class="px-4 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-slate-100 rounded-xl text-xs font-bold flex items-center gap-2 transition-all">
                        <i class="fa-solid fa-sliders text-emerald-400"></i> Bộ Lọc Nâng Cao
                      </button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 w-full md:w-auto md:flex md:flex-row justify-center">
                        <!-- Nuôi thú cưng -->
                        <button type="button" id="vbtn-pets" onclick="toggleVisualFilter('pets')" class="vfilter-btn flex flex-col items-center justify-center p-3 rounded-2xl transition-all duration-300 relative group overflow-hidden">
                            <div class="vfilter-glow-element absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <span class="vfilter-icon-box flex items-center justify-center w-9 h-9 rounded-xl mb-1.5 transition-all duration-300">
                                <i class="fa-solid fa-cat text-sm"></i>
                            </span>
                            <span class="vfilter-text text-[10px] font-bold tracking-wide">Nuôi thú cưng</span>
                        </button>
                        
                        <!-- WC khép kín -->
                        <button type="button" id="vbtn-wc" onclick="toggleVisualFilter('wc')" class="vfilter-btn flex flex-col items-center justify-center p-3 rounded-2xl transition-all duration-300 relative group overflow-hidden">
                            <div class="vfilter-glow-element absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <span class="vfilter-icon-box flex items-center justify-center w-9 h-9 rounded-xl mb-1.5 transition-all duration-300">
                                <i class="fa-solid fa-door-closed text-sm"></i>
                            </span>
                            <span class="vfilter-text text-[10px] font-bold tracking-wide">WC khép kín</span>
                        </button>
                        
                        <!-- Có ban công -->
                        <button type="button" id="vbtn-balcony" onclick="toggleVisualFilter('balcony')" class="vfilter-btn flex flex-col items-center justify-center p-3 rounded-2xl transition-all duration-300 relative group overflow-hidden">
                            <div class="vfilter-glow-element absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <span class="vfilter-icon-box flex items-center justify-center w-9 h-9 rounded-xl mb-1.5 transition-all duration-300">
                                <i class="fa-solid fa-cloud-sun text-sm"></i>
                            </span>
                            <span class="vfilter-text text-[10px] font-bold tracking-wide">Có ban công</span>
                        </button>
                    </div>
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
                            <input type="checkbox" id="tag-pets" onchange="syncFromCheckbox('pets')" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> Nuôi thú cưng
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700">
                            <input type="checkbox" id="tag-loft" onchange="filterItems()" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> Có gác lửng
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700">
                            <input type="checkbox" id="tag-balcony" onchange="syncFromCheckbox('balcony')" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> Ban công
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700">
                            <input type="checkbox" id="tag-wc" onchange="syncFromCheckbox('wc')" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> WC khép kín
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN WORKSPACE -->
    <main class="container mx-auto px-6 py-6 max-w-6xl flex-grow flex flex-col relative z-10">
        <!-- Interactive Map Pane (Left 50% in map mode) -->
        <div class="renty-split-left">
            <div id="renty-interactive-map" class="rounded-3xl border border-slate-800/80 overflow-hidden shadow-2xl"></div>
        </div>

        <!-- Room Cards List Pane (Right 50% in map mode) -->
        <div class="renty-split-right">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-slate-900 pb-4">
            <h2 class="text-lg font-bold text-slate-200" id="results-count">Tìm thấy {{ count($rooms) }} phòng</h2>
            
            <div class="flex flex-wrap items-center gap-3 self-start sm:self-auto">
                <!-- View Mode Toggle (Map vs Grid) -->
                <div class="view-mode-toggle">
                    <button type="button" onclick="setViewMode('map')" id="view-mode-map-btn">
                        <i class="fa-solid fa-map-location-dot"></i> <span class="hidden sm:inline">Bản đồ</span>
                    </button>
                    <button type="button" onclick="setViewMode('grid')" id="view-mode-grid-btn" class="active">
                        <i class="fa-solid fa-table-cells-large"></i> <span class="hidden sm:inline">Lưới</span>
                    </button>
                </div>

                <div class="flex items-center gap-3 bg-slate-950/45 px-4 py-2 rounded-2xl border border-slate-900/60 backdrop-blur-sm">
                    <span class="text-xs text-slate-400 font-bold select-none">Ẩn phòng đã thuê</span>
                    <label class="ios-switch">
                        <input type="checkbox" id="hide-rented-toggle" onchange="filterItems()">
                        <span class="ios-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16" id="rooms-grid">
            @foreach($rooms as $room)
            <div class="room-item-card glass-card rounded-2xl overflow-hidden group flex flex-col justify-between relative" 
                 data-room-id="{{ $room['id'] }}"
                 data-price="{{ $room['price'] }}" 
                 data-rating="{{ $room['rating'] }}" 
                 data-pets="{{ $room['pets'] }}" 
                 data-loft="{{ $room['loft'] }}" 
                 data-balcony="{{ $room['balcony'] }}" 
                 data-wc="{{ $room['wc'] }}"
                 data-distance="{{ $room['distance'] }}" 
                 data-area-name="{{ $room['area_name'] }}"
                 data-status="{{ $room['status'] }}"
                 data-viewed="false"
                 data-title="{{ $room['title'] }}">
                <div class="room-card-skeleton" aria-hidden="true">
                    <div class="skeleton-media"></div>
                    <div class="skeleton-body">
                        <div class="skeleton-row skeleton-row-title"></div>
                        <div class="skeleton-row skeleton-row-rating"></div>
                        <div class="skeleton-row skeleton-row-address"></div>
                        <div class="skeleton-row skeleton-row-price"></div>
                        <div class="skeleton-tags">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="skeleton-footer">
                        <div class="skeleton-check"></div>
                        <div class="skeleton-action"></div>
                    </div>
                </div>
                <div class="room-card-content">
                    @php
                        $cardImages = collect($room['image_urls'] ?? [])
                            ->filter()
                            ->prepend($room['cover_image'])
                            ->unique()
                            ->take(4)
                            ->values();
                    @endphp
                    <!-- Room photo -->
                    <div class="room-card-media h-48 bg-slate-950 relative overflow-hidden border-b border-slate-900 group">
                        <a href="{{ route('renty.room.show', $room['id']) }}" class="absolute inset-0 z-0 renty-card-carousel" aria-label="Xem chi tiết phòng {{ $room['room_number'] }}">
                            @foreach($cardImages as $imageIndex => $imageUrl)
                                <img src="{{ $imageUrl }}" alt="Ảnh {{ $imageIndex + 1 }} phòng {{ $room['room_number'] }}" class="renty-card-carousel-image" style="--carousel-delay: {{ $imageIndex * 2 }}s;" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                            @endforeach
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/10 to-transparent"></div>
                        </a>

                        @if($room['status'] === 'empty')
                            <span class="room-status-badge room-status-empty absolute top-4 left-4 px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Sẵn sàng
                            </span>
                        @else
                            <span class="room-status-badge room-status-rented absolute top-4 left-4 px-2.5 py-1 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                Đã thuê
                            </span>
                            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-[0.5px] z-10"></div>
                            <div class="absolute inset-0 flex items-center justify-center z-20">
                                <button type="button" onclick="subscribeEmptyNotification(event, '{{ $room['id'] }}', '{{ $room['title'] }}')" class="py-2 px-3.5 rounded-xl bg-slate-900/95 hover:bg-slate-900 border border-slate-800 text-slate-200 hover:text-white flex items-center gap-2 text-[10px] font-extrabold shadow-lg transition-all renty-notify-btn group/bell-btn">
                                    <i class="fa-solid fa-bell text-teal-400 text-xs"></i>
                                    Chuông báo khi trống phòng
                                </button>
                            </div>
                        @endif

                        <div class="absolute top-14 right-4 z-10 flex flex-col items-end gap-1.5">
                            @if($room['price_warning'])
                                <span class="px-2.5 py-1 bg-amber-500/10 text-amber-300 border border-amber-500/25 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm flex items-center gap-1.5" title="{{ $room['price_warning']['message'] }}">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    {{ $room['price_warning']['type'] === 'low' ? 'Giá quá rẻ' : 'Giá cao' }}
                                </span>
                            @endif

                            <span class="px-2.5 py-1 border rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm flex items-center gap-1.5 {{ $room['trust_badge']['class'] }}">
                                <i class="fa-solid {{ $room['trust_badge']['icon'] }}"></i>
                                {{ $room['trust_badge']['label'] }}
                            </span>
                        </div>

                        <button type="button" onclick="openQuickRoomPreview(event, '{{ $room['id'] }}')" class="absolute top-4 right-4 px-3 py-1.5 rounded-xl bg-slate-950/82 border border-white/10 text-[10px] font-extrabold text-slate-100 backdrop-blur z-20 flex items-center gap-1.5 hover:border-emerald-400/60 hover:text-emerald-200 quick-eye-button" title="Xem nhanh thông tin phòng">
                            <i class="fa-solid fa-eye text-slate-300"></i>
                            <span class="quick-eye-text">Xem nhanh</span>
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
                            <div class="viewed-room-strip" aria-hidden="true">
                                <span class="viewed-room-strip-icon">
                                    <i class="fa-solid fa-eye"></i>
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <span>ĐÃ XEM</span>
                            </div>
                            <div class="flex justify-between items-start mb-2 gap-2">
                                <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-all line-clamp-1">
                                    <a href="{{ route('renty.room.show', $room['id']) }}">{{ $room['title'] }}</a>
                                </h3>
                                <div class="relative group/rating flex items-center gap-1 text-xs text-amber-400 font-bold shrink-0 cursor-pointer py-1 px-1.5 rounded-lg hover:bg-amber-500/10 transition-colors">
                                    <i class="fa-solid fa-star text-[10px]"></i> <span>{{ $room['rating'] }}</span>
                                    
                                    <!-- Popover Rating Breakdown -->
                                    <div class="rating-popover absolute bottom-full right-0 mb-3 w-64 bg-[#0b0f19]/95 border border-slate-800 p-4 rounded-2xl shadow-2xl opacity-0 invisible group-hover/rating:opacity-100 group-hover/rating:visible transition-all duration-300 transform translate-y-2 group-hover/rating:translate-y-0 z-30 pointer-events-none text-left">
                                        <div class="flex items-center justify-between border-b border-slate-800/60 pb-2 mb-3">
                                            <h4 class="text-[10px] font-extrabold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                                                <i class="fa-solid fa-chart-simple text-teal-400"></i>
                                                Điểm đánh giá thực tế
                                            </h4>
                                            <span class="text-[10px] text-slate-500 font-bold">{{ $room['rating'] }}/5</span>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <!-- An ninh -->
                                            <div>
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-[10px] text-slate-400 font-bold">An ninh & Trật tự</span>
                                                    <div class="flex items-center gap-0.5 text-[8px] text-teal-400">
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-regular fa-star text-slate-700"></i>
                                                    </div>
                                                </div>
                                                <div class="h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                                                    <div class="h-full bg-teal-500 rounded-full" style="width: 80%"></div>
                                                </div>
                                            </div>
                                            
                                            <!-- Chủ nhà -->
                                            <div>
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-[10px] text-slate-400 font-bold">Chủ nhà & Hỗ trợ</span>
                                                    <div class="flex items-center gap-0.5 text-[8px] text-teal-400">
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                    </div>
                                                </div>
                                                <div class="h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                                                    <div class="h-full bg-teal-500 rounded-full" style="width: 100%"></div>
                                                </div>
                                            </div>
                                            
                                            <!-- Giá cả & Điện nước -->
                                            <div>
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-[10px] text-slate-400 font-bold">Giá cả & Điện nước</span>
                                                    <div class="flex items-center gap-0.5 text-[8px] text-teal-400">
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star-half-stroke"></i>
                                                    </div>
                                                </div>
                                                <div class="h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                                                    <div class="h-full bg-teal-500 rounded-full" style="width: 90%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Arrow Pointing Down -->
                                        <div class="absolute top-full right-4 -mt-1.5 w-3 h-3 bg-[#0b0f19] border-r border-b border-slate-800 transform rotate-45"></div>
                                    </div>
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
                            @if($room['wc'] === 'true')
                                <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 text-[9px] font-bold border border-emerald-500/20">WC khép kín</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-md bg-slate-900/60 text-slate-500 text-[9px] font-bold border border-slate-800/40">WC chung</span>
                            @endif
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
                        <button type="button" onclick="openReportModal('{{ $room['id'] }}', '{{ e($room['title']) }}')" class="room-report-button text-xs text-rose-400 hover:text-rose-300 font-bold flex items-center gap-1">
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

        <!-- Pagination Controls -->
        <div id="renty-pagination" class="flex justify-center items-center gap-2 mb-12 flex-wrap"></div>

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

        <!-- COMMUNITY Q&A SECTION -->
        <section class="qa-section mt-12 rounded-3xl border border-slate-800/60 overflow-hidden" id="community-qa">
            @php
                $qaCards = [
                    [
                        'question' => 'Khu ngõ 105 Xuân Thủy đợt này có hay mất nước không ạ? Nghe bảo hay bị mất nước đột ngột vào mùa hè.',
                        'area' => 'Cầu Giấy', 'areaColor' => 'teal', 'time' => '2 giờ trước',
                        'votes' => 32, 'comments' => 14, 'tags' => ['Nước sinh hoạt', 'Mùa hè'],
                        'reply_author' => 'Hoàng Anh', 'reply_school' => 'Sinh viên Sư Phạm',
                        'reply_text' => 'Khu này bể ngầm hơi nhỏ nên nếu mất nước chung thì cúp tầm nửa ngày thôi bạn, chủ nhà có bể dự phòng nhé.',
                        'is_hot' => true,
                    ],
                    [
                        'question' => 'Có bác nào ở chung chủ ngõ 119 Chùa Láng không? Cho em xin review chủ nhà có khó tính không ạ?',
                        'area' => 'Đống Đa', 'areaColor' => 'violet', 'time' => '5 giờ trước',
                        'votes' => 18, 'comments' => 8, 'tags' => ['Review chủ nhà'],
                        'reply_author' => 'Khánh Linh', 'reply_school' => 'Ngoại Thương',
                        'reply_text' => 'Chủ nhà ngõ này hiền lắm, giữ xe free mà 11h đêm khóa cổng thôi. Không chung đụng gì nhiều đâu em.',
                        'is_hot' => false,
                    ],
                    [
                        'question' => 'Trọ gần Bách Khoa ngõ Tự Do tầm giá 3M5 đợt này có phòng nào có ban công thoáng không mọi người?',
                        'area' => 'Hai Bà Trưng', 'areaColor' => 'amber', 'time' => '1 ngày trước',
                        'votes' => 45, 'comments' => 21, 'tags' => ['Tìm phòng', 'Ban công'],
                        'reply_author' => 'Minh Đức', 'reply_school' => 'Bách Khoa',
                        'reply_text' => 'Tầm giá này ở ngõ Tự Do hơi hiếm ban công rộng, bạn chịu khó lùi ra Trần Đại Nghĩa hoặc Lê Thanh Nghị thì nhiều phòng đẹp hơn nha.',
                        'is_hot' => true,
                    ],
                    [
                        'question' => 'Ngõ 20 Hồ Tùng Mậu an ninh thế nào ạ? Em thấy ngõ hơi sâu, con gái đi học tối về có an toàn không?',
                        'area' => 'Cầu Giấy', 'areaColor' => 'teal', 'time' => '3 ngày trước',
                        'votes' => 27, 'comments' => 12, 'tags' => ['An ninh', 'Con gái'],
                        'reply_author' => 'Thu Trang', 'reply_school' => 'Báo Chí',
                        'reply_text' => 'Đầu ngõ có chốt dân phòng với đèn đường sáng trưng tới sáng luôn bạn, yên tâm cực kỳ nha.',
                        'is_hot' => false,
                    ],
                ];
                $areaColorMap = [
                    'teal'   => 'bg-teal-500/10 text-teal-400 border-teal-500/20',
                    'violet' => 'bg-violet-500/10 text-violet-400 border-violet-500/20',
                    'amber'  => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                    'rose'   => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                ];
            @endphp

            <!-- Section Hero Header -->
            <div class="qa-section-header px-6 pt-7 pb-5" style="background: linear-gradient(145deg, #121214 0%, #161620 100%);">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-600 to-cyan-500 flex items-center justify-center shadow-lg shadow-teal-500/20">
                            <i class="fa-solid fa-comments text-white text-base"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-100 tracking-tight">Hỏi Đáp Cộng Đồng</h2>
                            <p class="text-[10px] text-slate-500 mt-0.5">Chia sẻ thắc mắc ẩn danh &bull; Không lộ danh tính</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-extrabold flex items-center gap-1.5 bg-teal-500/8 text-teal-400 border border-teal-500/15">
                            <i class="fa-solid fa-shield-halved text-[9px]"></i> 100% Ẩn danh
                        </span>
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-extrabold flex items-center gap-1.5 bg-slate-800/60 text-slate-400 border border-slate-700/40">
                            <i class="fa-solid fa-fire text-orange-400 text-[9px]"></i> {{ count($qaCards) }} câu hỏi hot
                        </span>
                    </div>
                </div>

                <!-- Input Box -->
                <div class="relative w-full">
                    <input type="text" id="qa-input-field" onkeyup="updateQaCharCount()" placeholder="Hỏi ẩn danh về khu vực hoặc chủ nhà tại đây..." maxlength="200" class="w-full pl-12 pr-28 py-4 bg-slate-900/60 border border-slate-800/80 rounded-2xl text-slate-200 placeholder-slate-550 text-xs focus:outline-none transition-all duration-300" />
                    <i class="fa-solid fa-user-secret absolute left-4.5 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-2 z-[1]">
                        <span id="qa-char-count" class="text-[9px] font-bold text-slate-600 tabular-nums">0/200</span>
                        <button type="button" onclick="submitQaQuestion()" class="qa-submit-btn px-3.5 py-2 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-500 hover:to-cyan-500 text-white rounded-lg text-[10px] font-extrabold transition-all shadow-md shadow-teal-600/15 uppercase tracking-wider">
                            <i class="fa-solid fa-paper-plane mr-1"></i> Gửi
                        </button>
                    </div>
                </div>

                <!-- Trending Area Tags -->
                <div class="flex flex-wrap items-center gap-2 mt-4">
                    <span class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mr-1">Xu hướng:</span>
                    @foreach(['Cầu Giấy' => 'teal', 'Đống Đa' => 'violet', 'Hai Bà Trưng' => 'amber', 'Thanh Xuân' => 'rose'] as $areaName => $color)
                        <button type="button" class="px-2.5 py-1 rounded-full text-[9px] font-bold border transition-all hover:scale-105 {{ $areaColorMap[$color] }}">
                            {{ $areaName }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Q&A Cards Grid -->
            <div id="qa-grid" class="px-6 pb-6 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4" style="background-color: #121214;">
                @foreach($qaCards as $qaIndex => $qa)
                    <div class="qa-card rounded-2xl border border-slate-800/50 flex flex-col justify-between transition-all duration-300 hover:border-slate-700/60 group/card overflow-hidden" style="background-color: #1a1a20; animation-delay: {{ $qaIndex * 0.08 }}s;">
                        <div class="p-5 pb-0">
                            <!-- Meta Row -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-800/80 flex items-center justify-center border border-slate-700/60">
                                        <i class="fa-solid fa-user-secret text-xs text-teal-400"></i>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-extrabold text-slate-300">Người dùng ẩn danh</span>
                                        <span class="block text-[8px] text-slate-600 font-bold mt-0.5">{{ $qa['time'] }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border uppercase tracking-wider {{ $areaColorMap[$qa['areaColor']] }}">
                                    {{ $qa['area'] }}
                                </span>
                            </div>

                            <!-- Question Title -->
                            <h3 class="text-xs font-bold text-slate-200 leading-relaxed group-hover/card:text-teal-400 transition-colors mb-2.5 flex items-start gap-1.5">
                                @if($qa['is_hot'])
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-rose-500/10 text-rose-400 text-[8px] border border-rose-500/20 font-bold uppercase tracking-wider scale-95 origin-left" title="Câu hỏi tiêu biểu">
                                        <i class="fa-solid fa-fire text-[8px]"></i> HOT
                                    </span>
                                @endif
                                {{ $qa['question'] }}
                            </h3>

                            <!-- Tags -->
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($qa['tags'] as $tag)
                                    <span class="px-2 py-0.5 rounded-md bg-slate-800/60 text-slate-500 text-[9px] font-bold border border-slate-800/40">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Bottom Section -->
                        <div class="px-5 pb-4 pt-3 mt-auto border-t border-slate-800/40">
                            <!-- Interaction Row -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-0.5 bg-slate-900/50 border border-slate-800/60 rounded-lg overflow-hidden">
                                    <button type="button" onclick="voteQa(this, 'up')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-emerald-400 hover:bg-emerald-500/8 transition-all text-xs" aria-label="Upvote">
                                        <i class="fa-solid fa-arrow-up"></i>
                                    </button>
                                    <span class="px-2 text-[11px] font-extrabold text-slate-300 tabular-nums qa-vote-count select-none">{{ $qa['votes'] }}</span>
                                    <button type="button" onclick="voteQa(this, 'down')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-rose-400 hover:bg-rose-500/8 transition-all text-xs" aria-label="Downvote">
                                        <i class="fa-solid fa-arrow-down"></i>
                                    </button>
                                </div>
                                <button type="button" onclick="openQaCommentsModal(this)" class="qa-comment-btn flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-slate-300 transition-colors" data-qa-index="{{ $qaIndex }}" data-qa-question="{{ e($qa['question']) }}" data-qa-area="{{ $qa['area'] }}" data-qa-time="{{ $qa['time'] }}">
                                    <i class="fa-regular fa-message text-[10px]"></i>
                                    <span class="qa-comment-count">{{ $qa['comments'] }} bình luận</span>
                                </button>
                            </div>

                            <!-- Best Reply -->
                            <div class="qa-best-reply rounded-xl p-3 flex flex-col gap-1.5 bg-slate-900/40 border border-slate-800/30">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-bold text-slate-400">{{ $qa['reply_author'] }} ({{ $qa['reply_school'] }})</span>
                                        <span class="w-3.5 h-3.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[7px] border border-emerald-500/15 inline-flex items-center justify-center" title="Đã xác minh">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                    </div>
                                    <span class="text-[8px] text-teal-500/70 font-bold uppercase tracking-wider">Best</span>
                                </div>
                                <p class="text-[11px] text-slate-400 leading-relaxed italic">
                                    "{{ $qa['reply_text'] }}"
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Load More Bar -->
            <div class="qa-load-more-bar px-6 py-4 border-t border-slate-800/40 flex items-center justify-center" style="background-color: #121214;">
                <button type="button" onclick="loadMoreQaQuestions(this)" class="qa-load-more px-6 py-2.5 rounded-xl bg-slate-800/40 border border-slate-700/40 text-xs font-bold text-slate-400 hover:text-teal-400 hover:border-teal-500/30 hover:bg-teal-500/5 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-angles-down text-[10px]"></i> Xem thêm câu hỏi
                </button>
            </div>
        </section>

        <!-- FOOTER -->
        @include('footer.footer')
        </div> <!-- End of .renty-split-right -->
    </main>

    <!-- FLOATING COMPARE BAR -->
    <div id="compare-dock" class="compare-floating-bar hidden">
        <div class="compare-floating-copy">
            <div class="compare-floating-icon">
                <i class="fa-solid fa-code-compare"></i>
            </div>
            <div class="hidden sm:block">
                <strong class="text-xs font-bold text-slate-200">So sánh phòng</strong>
                <span class="text-[10px] text-slate-400">Chọn tối đa 3 phòng</span>
            </div>
        </div>

        <!-- Dynamic Room Thumbnails List -->
        <div id="compare-thumbnails" class="compare-floating-thumbnails flex items-center gap-2"></div>

        <div class="compare-floating-actions flex items-center gap-2">
            <button type="button" onclick="clearCompare()" class="compare-clear-btn px-3 py-1.5 rounded-xl text-[11px] font-bold">
                Hủy
            </button>
            <button type="button" onclick="openCompareModal()" id="compare-btn-submit" class="compare-submit-btn px-4 py-2 rounded-xl text-xs font-extrabold flex items-center gap-1">
                So sánh ngay (0)
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

    <!-- JS LOGIC EXTRACTED TO resources/js/rentry.js -->

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

    <!-- Q&A COMMENTS MODAL -->
    <div id="qa-comments-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-lg bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl relative max-h-[85vh] flex flex-col animate-fade-in">
            <button onclick="closeQaCommentsModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-base font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-regular fa-comments text-teal-400 animate-pulse"></i> Bình Luận Cộng Đồng
            </h2>
            
            <!-- Question Content Header -->
            <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-800/80 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border uppercase tracking-wider bg-teal-500/10 text-teal-400 border-teal-500/20" id="qa-modal-area">Khu vực</span>
                    <span class="text-[9px] text-slate-500 font-semibold" id="qa-modal-time">Thời gian</span>
                </div>
                <p class="text-xs font-bold text-slate-200 leading-relaxed" id="qa-modal-question">Nội dung câu hỏi</p>
            </div>
            
            <!-- Scrollable Comments List -->
            <div class="flex-grow overflow-y-auto mb-4 space-y-3 pr-1 scrollbar-thin max-h-[40vh]" id="qa-modal-comments-list">
                <!-- Comments dynamically populated here -->
            </div>
            
            <!-- Reply input form -->
            <form id="qa-reply-form" onsubmit="submitQaReply(event)" class="mt-auto pt-3 border-t border-slate-800/80">
                <div class="relative w-full">
                    <input type="text" id="qa-reply-input" placeholder="Viết phản hồi ẩn danh của bạn..." required class="w-full pl-4 pr-16 py-3 bg-slate-900/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-550 text-xs focus:outline-none focus:border-teal-500 transition-colors" />
                    <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-500 hover:to-cyan-500 text-white rounded-lg text-[10px] font-extrabold transition-all">
                        Gửi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- NOTIFY SUBSCRIBE MODAL -->
    <div id="notify-subscribe-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <button onclick="closeNotifySubscribeModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400">
                    <i class="fa-solid fa-bell text-lg"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-100">Đăng ký chuông báo</h2>
                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Nhận thông báo ngay khi phòng trống</p>
                </div>
            </div>
            
            <form id="notify-subscribe-form" onsubmit="handleNotifySubscribeSubmit(event)">
                <input type="hidden" id="notify-room-id">
                
                <div class="space-y-4">
                    <div>
                        <span class="block text-[10px] text-slate-500 font-bold uppercase mb-1.5">Tên phòng trọ</span>
                        <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-900 text-xs font-bold text-slate-300" id="notify-room-title-display"></div>
                    </div>
                    
                    <div>
                        <label for="notify-contact-input" class="block text-[10px] text-slate-500 font-bold uppercase mb-1.5">Email hoặc Số điện thoại</label>
                        <input type="text" id="notify-contact-input" required placeholder="nhap-email@example.com hoặc 09xxxxxx" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-950/80 border border-slate-800 text-slate-200 focus:border-teal-500 focus:ring-0 focus:outline-none placeholder-slate-600 transition-colors">
                    </div>
                    
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-teal-500/5 border border-teal-500/10">
                        <i class="fa-solid fa-circle-info text-teal-400 text-xs mt-0.5 shrink-0"></i>
                        <p class="text-[10px] text-teal-300/85 leading-normal font-semibold">Chúng tôi sẽ tự động gửi thông báo qua Email hoặc SMS ngay khi phòng trọ này được cập nhật trạng thái "SẴN SÀNG".</p>
                    </div>
                    
                    <div class="flex items-center gap-2 pt-2">
                        <button type="button" onclick="closeNotifySubscribeModal()" class="w-1/2 py-2 px-3 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs font-bold text-slate-400 hover:text-slate-200 transition-colors">
                            Hủy
                        </button>
                        <button type="submit" class="w-1/2 py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-xs font-extrabold text-white shadow-lg shadow-teal-600/15 transition-all">
                            Đăng ký ngay
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- CUSTOM ALERT MODAL -->
    <div id="custom-alert-modal" class="fixed inset-0 z-[60] bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="w-full max-w-sm bg-[#0a0f1d] border border-slate-800 rounded-3xl p-5 shadow-2xl relative text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mx-auto mb-3">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-100 mb-1.5" id="custom-alert-title">Thành công!</h3>
            <p class="text-[11px] text-slate-400 leading-normal mb-4" id="custom-alert-message"></p>
            <button onclick="closeCustomAlert()" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-extrabold text-white transition-colors">
                Xác nhận
            </button>
        </div>
    </div>
</body>
</html>
