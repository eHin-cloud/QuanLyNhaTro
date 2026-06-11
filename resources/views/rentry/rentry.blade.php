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

    @include('header.header')

    <!-- HERO BENTO GRID & ADVANCED FILTERS -->
    <section id="renty-hero-section" class="container mx-auto px-6 pt-8 pb-8 max-w-6xl relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Bento Box 1: Main Search & Visual Filters (Col span 2) -->
            <div class="lg:col-span-2 bg-gradient-to-br from-slate-900/40 to-slate-950/40 border border-slate-800/80 rounded-3xl p-6 md:p-8 backdrop-blur-md flex flex-col justify-between relative overflow-hidden group">
                <!-- Decorative absolute glow inside card -->
                <div class="absolute -top-24 -left-24 w-48 h-48 rounded-full bg-emerald-500/10 blur-[80px] pointer-events-none"></div>
                
                <div class="relative z-10 w-full">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Review phòng trọ thực tế
                    </span>
                    
                    <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-4 leading-tight text-slate-100">
                        Tìm Trọ Đúng Nghĩa - <br class="hidden md:inline"><span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">Xem Review Thật</span>
                    </h1>
                    
                    <p class="text-slate-400 text-xs md:text-sm max-w-xl mb-6 leading-relaxed">
                        Tránh bẫy "ảnh mạng một đằng thực tế một nẻo". Xem đánh giá điểm số chủ nhà, an ninh, điện nước trước khi cọc.
                    </p>
                    
                    <!-- Integrated Search Bar -->
                    <div class="relative w-full max-w-2xl mb-6 group/search">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl opacity-10 group-hover/search:opacity-25 blur-sm transition duration-300"></div>
                        <div class="relative flex items-center bg-slate-950/80 border border-slate-800/80 rounded-2xl overflow-hidden backdrop-blur-md">
                            <i class="fa-solid fa-location-dot pl-4 text-emerald-400"></i>
                            <input type="text" id="hero-search-input" class="w-full pl-3 pr-4 py-3.5 bg-transparent text-slate-250 placeholder-slate-500 focus:outline-none text-xs md:text-sm font-semibold" placeholder="Tìm kiếm theo địa chỉ, khu vực, trường học hoặc tiện ích...">
                        </div>
                    </div>
                </div>

                <!-- Visual Filters Inner Row -->
                <div class="relative z-10 pt-4 border-t border-slate-800/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fa-solid fa-wand-magic-sparkles text-teal-400"></i>
                            Bộ lọc nhanh trực quan
                        </span>
                        <span class="block text-[9px] text-slate-500 mt-0.5">Click nhanh để lọc nhanh phòng trọ phù hợp</span>
                    </div>
                    
                    <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto">
                        <!-- Nuôi thú cưng -->
                        <button type="button" id="vbtn-pets" onclick="toggleVisualFilter('pets')" class="vfilter-btn flex items-center gap-2 px-3 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 rounded-xl transition-all duration-300 text-[10px] font-bold">
                            <i class="fa-solid fa-cat text-teal-450"></i>
                            <span>Thú cưng</span>
                        </button>
                        
                        <!-- WC khép kín -->
                        <button type="button" id="vbtn-wc" onclick="toggleVisualFilter('wc')" class="vfilter-btn flex items-center gap-2 px-3 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 rounded-xl transition-all duration-300 text-[10px] font-bold">
                            <i class="fa-solid fa-door-closed text-teal-450"></i>
                            <span>WC khép kín</span>
                        </button>
                        
                        <!-- Có ban công -->
                        <button type="button" id="vbtn-balcony" onclick="toggleVisualFilter('balcony')" class="vfilter-btn flex items-center gap-2 px-3 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 rounded-xl transition-all duration-300 text-[10px] font-bold">
                            <i class="fa-solid fa-cloud-sun text-teal-450"></i>
                            <span>Có ban công</span>
                        </button>

                        <button type="button" onclick="toggleFilterDrawer()" class="px-3 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 rounded-xl text-[10px] font-extrabold flex items-center gap-1.5 transition-all">
                            <i class="fa-solid fa-sliders text-emerald-450"></i> Lọc nâng cao
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bento Box 2: Community Insights & Live Stats (Col span 1) -->
            <div class="bg-gradient-to-br from-slate-900/40 to-slate-950/40 border border-slate-800/80 rounded-3xl p-6 backdrop-blur-md flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -bottom-24 -right-24 w-48 h-48 rounded-full bg-teal-500/10 blur-[80px] pointer-events-none"></div>
                
                <div class="relative z-10">
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4">
                        <i class="fa-solid fa-chart-line text-emerald-400 mr-1.5"></i>
                        Thống kê cộng đồng
                    </span>
                    
                    <div class="space-y-4">
                        <!-- Stat 1 -->
                        <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-slate-950/40 border border-slate-900/60 transition-all hover:border-slate-800">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/15 flex items-center justify-center font-bold text-xs shrink-0 animate-pulse">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-extrabold text-slate-200">100% Xác thực</span>
                                <span class="block text-[9px] text-slate-500">Mọi đánh giá đều từ sinh viên ở thực tế</span>
                            </div>
                        </div>

                        <!-- Stat 2 -->
                        <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-slate-950/40 border border-slate-900/60 transition-all hover:border-slate-800">
                            <div class="w-9 h-9 rounded-xl bg-teal-500/10 text-teal-400 border border-teal-500/15 flex items-center justify-center font-bold text-xs shrink-0">
                                <i class="fa-solid fa-house-circle-check"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-extrabold text-slate-200">Tìm kiếm tối ưu</span>
                                <span class="block text-[9px] text-slate-500">Lọc theo khoảng cách & tiện ích trực quan</span>
                            </div>
                        </div>

                        <!-- Stat 3 -->
                        <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-slate-950/40 border border-slate-900/60 transition-all hover:border-slate-800">
                            <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/15 flex items-center justify-center font-bold text-xs shrink-0">
                                <i class="fa-solid fa-user-secret"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-extrabold text-slate-200">Ẩn danh tuyệt đối</span>
                                <span class="block text-[9px] text-slate-500">Đăng review và hỏi đáp không lo lộ danh tính</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 pt-4 border-t border-slate-800/50 flex items-center justify-between text-[9px] text-slate-550 font-bold uppercase tracking-wider">
                    <span>Cập nhật trực tiếp</span>
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Realtime</span>
                </div>
            </div>
        </div>

        <!-- Advanced Filters Dropdown (Expandable Filters) -->
        <div id="filter-drawer" class="hidden mt-6 bg-slate-900/35 border border-slate-800/80 p-5 rounded-3xl backdrop-blur-md animate-fade-in">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Price Range -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Giá phòng tối đa</label>
                    <select id="filter-price" onchange="filterItems()" class="w-full px-4 py-2.5 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-colors">
                        <option value="all">Tất cả khoảng giá</option>
                        <option value="3000000">Dưới 3.000.000đ</option>
                        <option value="4000000">Dưới 4.000.000đ</option>
                        <option value="5000000">Dưới 5.000.000đ</option>
                    </select>
                </div>
                
                <!-- Ratings -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Điểm đánh giá tối thiểu</label>
                    <select id="filter-rating" onchange="filterItems()" class="w-full px-4 py-2.5 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-colors">
                        <option value="all">Mọi điểm số</option>
                        <option value="4.5">Từ 4.5⭐ trở lên</option>
                        <option value="4.0">Từ 4.0⭐ trở lên</option>
                        <option value="3.5">Từ 3.5⭐ trở lên</option>
                    </select>
                </div>

                <!-- Distance Slider Section -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Khoảng cách đến Trường/Tiện ích</label>
                    <div class="slider-container relative mt-3 mb-2 px-1">
                        <input 
                            type="range" 
                            id="distance-slider" 
                            min="0" 
                            max="3" 
                            step="0.1" 
                            value="3.0" 
                            oninput="updateDistanceSlider(this.value)"
                            class="custom-range-slider"
                            style="--range-progress: 100%;"
                        >
                        <!-- Explicit numeric tick marks -->
                        <div class="slider-ticks flex justify-between px-1 mt-1 text-[10px] text-slate-500 font-semibold">
                            <span class="tick-mark cursor-pointer transition-colors" onclick="setSliderValue(0)" data-value="0">0 km</span>
                            <span class="tick-mark cursor-pointer transition-colors" onclick="setSliderValue(1)" data-value="1">1 km</span>
                            <span class="tick-mark cursor-pointer transition-colors" onclick="setSliderValue(2)" data-value="2">2 km</span>
                            <span class="tick-mark cursor-pointer transition-colors" onclick="setSliderValue(3)" data-value="3">3 km</span>
                        </div>
                    </div>
                    <!-- Dynamic Feedback Text Box -->
                    <div class="feedback-box mt-3 p-2.5 bg-slate-950/40 border border-slate-800/80 rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-teal-400 text-xs shrink-0 animate-pulse"></i>
                        <span id="distance-feedback" class="text-[10px] text-slate-400 leading-normal font-semibold">
                            Tìm phòng trong bán kính dưới 3.0km từ Đại học Bách Khoa
                        </span>
                    </div>
                </div>

                <!-- Utilities Checkbox Group -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Tiện ích đặc biệt</label>
                    <div class="flex flex-wrap gap-2">
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-pets" onchange="syncFromCheckbox('pets')" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> Nuôi thú cưng
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-loft" onchange="filterItems()" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> Có gác lửng
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-balcony" onchange="syncFromCheckbox('balcony')" class="rounded border-slate-800 text-emerald-600 focus:ring-0"> Ban công
                        </label>
                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0a0e17] border border-slate-800 text-[10px] font-bold text-slate-400 cursor-pointer hover:border-slate-700 transition-colors">
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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-slate-900 pb-4 renty-split-header">
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
                @include('rentry.partials.room_card', ['room' => $room])
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

        <!-- FEATURED ARTICLES SECTION -->
        <section class="mb-16">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-newspaper text-teal-400"></i>
                        Bài viết nổi bật
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Tổng hợp chia sẻ kinh nghiệm tìm phòng trọ và review khu vực từ cộng đồng sinh viên.</p>
                </div>
            </div>
            
            @php
            $featuredArticles = [
                [
                    'id' => 1,
                    'tag' => 'Review Phòng',
                    'tag_class' => 'bg-teal-500/10 text-teal-400 border border-teal-500/20',
                    'time' => '5 giờ trước',
                    'title' => 'Kinh nghiệm tìm nhà trọ quanh Đại học Bách Khoa Hà Nội',
                    'summary' => 'Tổng hợp danh sách các khu vực trọ an ninh tốt, giá hợp lý ở ngõ Tự Do, Trần Đại Nghĩa và Lê Thanh Nghị cho tân sinh viên khóa mới chuẩn bị nhập học.',
                    'author' => 'Hoàng Anh',
                    'border_color' => 'teal',
                    'shadow_color' => '20,184,166',
                    'text_color' => 'teal-400',
                    'text_hover_color' => 'teal-300',
                    'detail' => '
                        <p><strong class="text-slate-200">1. Ngõ Tự Do (Hai Bà Trưng):</strong> Khu vực yên tĩnh, cách cổng trường Bách Khoa chỉ 300m. Giá phòng dao động từ 2.5 – 4 triệu/tháng. An ninh tốt nhờ camera và bảo vệ khu phố. Nhược điểm: đường vào hẹp, hay ngập khi mưa lớn.</p>
                        <p><strong class="text-slate-200">2. Trần Đại Nghĩa:</strong> Nhiều lựa chọn phòng mới xây với đầy đủ tiện nghi (điều hòa, nóng lạnh, giặt miễn phí). Giá cao hơn khu Tự Do khoảng 500k–1tr. Gần quán ăn, tiện đi lại. Phù hợp bạn nào thích tiện nghi.</p>
                        <p><strong class="text-slate-200">3. Lê Thanh Nghị:</strong> Giá mềm hơn (2 – 3.5 triệu), nhưng phòng thường cũ hơn. Gần ga Hà Nội nên hơi ồn ban đêm. Bù lại, rất thuận tiện đi các tuyến xe buýt và đường sắt đô thị.</p>
                        <p><strong class="text-teal-400">💡 Mẹo:</strong> Nên đi xem phòng vào buổi tối để đánh giá tiếng ồn thực tế và kiểm tra ánh sáng hành lang, camera an ninh. Hỏi rõ về tiền điện nước trước khi đặt cọc.</p>
                    '
                ],
                [
                    'id' => 2,
                    'tag' => 'Khu Vực',
                    'tag_class' => 'bg-teal-500/10 text-teal-400 border border-teal-500/20',
                    'time' => '1 ngày trước',
                    'title' => 'Đánh giá an ninh ngõ 119 Chùa Láng đợt nắng nóng cao điểm',
                    'summary' => 'Phản hồi chân thực từ cộng đồng khách thuê về tình hình điện nước, camera giám sát và giờ giấc đóng mở cửa của các tòa nhà chung cư mini trong khu vực.',
                    'author' => 'Khánh Linh',
                    'border_color' => 'teal',
                    'shadow_color' => '20,184,166',
                    'text_color' => 'teal-400',
                    'text_hover_color' => 'teal-300',
                    'detail' => '
                        <p><strong class="text-slate-200">Tình hình điện nước:</strong> Đợt nắng nóng tháng 6, khu 119 Chùa Láng bị cúp nước 2–3 lần/tuần, mỗi lần kéo dài 4–6 tiếng. Điện ổn định hơn nhưng giá điện tại một số nhà trọ bị tính cao (4.000đ/kWh so với giá EVN).</p>
                        <p><strong class="text-slate-200">Camera an ninh:</strong> Khoảng 60% nhà trọ trong ngõ đã lắp camera ở cổng và hành lang. Tuy nhiên, vẫn có tình trạng mất trộm xe đạp điện tại các nhà chưa có bảo vệ trực đêm.</p>
                        <p><strong class="text-slate-200">Giờ giấc đóng cửa:</strong> Đa số nhà trọ khóa cổng lúc 23h00. Một số nhà linh hoạt hơn cho ra vào bằng vân tay đến 1h sáng. Sinh viên hay đi làm thêm ca tối nên cần hỏi kỹ trước khi thuê.</p>
                        <p><strong class="text-teal-400">⚠️ Lưu ý:</strong> Nên kiểm tra hợp đồng có ghi rõ giá điện nước không. Tham khảo thêm bảng giá EVN để so sánh. Nếu chủ nhà tính quá cao, có thể phản ánh qua Sở Công Thương.</p>
                    '
                ],
                [
                    'id' => 3,
                    'tag' => 'TP. HCM',
                    'tag_class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                    'time' => '3 giờ trước',
                    'title' => 'Top khu trọ giá rẻ quanh Làng Đại Học Thủ Đức cho tân sinh viên',
                    'summary' => 'Hướng dẫn chi tiết tìm phòng trọ gần ĐH Bách Khoa, KHTN, Nông Lâm TP.HCM với giá từ 2.5 – 4 triệu/tháng kèm review thực tế từ sinh viên.',
                    'author' => 'Minh Đức',
                    'border_color' => 'amber',
                    'shadow_color' => '245,158,11',
                    'text_color' => 'amber-400',
                    'text_hover_color' => 'amber-300',
                    'detail' => '
                        <p><strong class="text-slate-200">1. Khu Linh Trung – Linh Chiểu:</strong> Vị trí sát Làng Đại học, đi bộ 5–10 phút đến cổng trường. Giá phòng 2.5 – 3.5tr/tháng, đa số là nhà trọ dân xây. Ưu điểm: rẻ, gần trường. Nhược điểm: phòng nhỏ, ít tiện nghi cao cấp.</p>
                        <p><strong class="text-slate-200">2. Khu Xa lộ Hà Nội – Trường Thọ:</strong> Phòng mới xây hơn, nhiều căn hộ mini có thang máy. Giá 3.5 – 4.5tr/tháng. Gần trạm Metro số 1 (Bến Xe Miền Đông mới), đi Quận 1 chỉ 20 phút. Phù hợp bạn đi làm thêm ở trung tâm.</p>
                        <p><strong class="text-slate-200">3. Khu Đường số 7 – Khu phố 6:</strong> Khu trọ sinh viên lâu đời, giá mềm nhất (2 – 3tr). Nhà trọ đông đúc, vui, dễ tìm bạn ở ghép. Lưu ý: hay ngập khi mưa lớn, nên chọn phòng tầng 2 trở lên.</p>
                        <p><strong class="text-amber-400">💡 Mẹo:</strong> Đăng lên nhóm Facebook "Phòng trọ Thủ Đức – Làng ĐH" để tìm phòng nhanh. Luôn đến xem phòng trước khi cọc. Hỏi kỹ giá điện (trên 3.500đ/kWh là đắt), nước, wifi và có cho nấu ăn không.</p>
                    '
                ],
                [
                    'id' => 4,
                    'tag' => 'TP. HCM',
                    'tag_class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                    'time' => '2 ngày trước',
                    'title' => 'So sánh chi phí thuê trọ Quận 7 vs Gò Vấp – Đâu là lựa chọn tốt nhất?',
                    'summary' => 'Phân tích giá phòng, tiện ích xung quanh, giao thông và chất lượng sống giữa 2 khu vực hot nhất Sài Gòn cho dân văn phòng và sinh viên.',
                    'author' => 'Thanh Trúc',
                    'border_color' => 'amber',
                    'shadow_color' => '245,158,11',
                    'text_color' => 'amber-400',
                    'text_hover_color' => 'amber-300',
                    'detail' => '
                        <p><strong class="text-slate-200">Quận 7 – Phú Mỹ Hưng:</strong> Giá thuê cao hơn (5 – 8tr/tháng) nhưng đổi lại là môi trường sống sạch đẹp, an ninh tốt, gần Lotte Mart, SC VivoCity, bệnh viện FV. Phù hợp dân văn phòng thu nhập khá hoặc gia đình trẻ.</p>
                        <p><strong class="text-slate-200">Gò Vấp:</strong> Giá mềm hơn nhiều (2.5 – 4tr/tháng), tiện ích đa dạng: chợ, Emart, Công viên Gia Định. Giao thông thuận tiện đi Quận 1, Bình Thạnh. Gần ĐH Công nghiệp, Văn Lang. Nhược điểm: kẹt xe giờ cao điểm ở Nguyễn Oanh và Phan Văn Trị.</p>
                        <p><strong class="text-slate-200">Bình Thạnh – phương án trung gian:</strong> Giá 3.5 – 6tr, nằm giữa trung tâm và ngoại ô. Gần Ngã tư Hàng Xanh, Metro, nhiều trường ĐH (HUTECH, Ngoại Thương). Khu Nguyễn Gia Trí (D2 cũ) có nhiều studio đẹp, phù hợp freelancer.</p>
                        <p><strong class="text-amber-400">⚠️ Lưu ý:</strong> Ở HCM nên ưu tiên phòng có ban công (thoáng, đỡ nóng). Kiểm tra kỹ hệ thống thoát nước (hay ngập mùa mưa). Nên thuê gần trạm Metro nếu đi làm ở Quận 1 – tiết kiệm thời gian và chi phí.</p>
                    '
                ],
                [
                    'id' => 5,
                    'tag' => 'Cảnh Báo',
                    'tag_class' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                    'time' => '3 ngày trước',
                    'title' => 'Cảnh giác các chiêu trò lừa đảo đặt cọc phòng trọ mùa nhập học',
                    'summary' => 'Tổng hợp các mánh khóe lừa tiền cọc giữ chỗ phổ biến hiện nay từ những kẻ mạo danh chủ trọ trên mạng xã hội.',
                    'author' => 'Quốc Việt',
                    'border_color' => 'rose',
                    'shadow_color' => '244,63,94',
                    'text_color' => 'rose-400',
                    'text_hover_color' => 'rose-300',
                    'detail' => '
                        <p><strong class="text-slate-200">1. Đặt cọc giữ chỗ qua mạng không gặp mặt:</strong> Kẻ lừa đảo copy hình ảnh phòng đẹp từ các trang khác, rao giá cực rẻ rồi hối thúc khách chuyển khoản giữ chỗ qua tài khoản ảo ngân hàng.</p>
                        <p><strong class="text-slate-200">2. Chủ nhà giả mạo:</strong> Dẫn khách đi xem phòng nhưng thực ra là thuê AirBnb theo ngày hoặc giả danh quản lý để nhận tiền cọc của 5-10 người cùng một lúc rồi biến mất.</p>
                        <p><strong class="text-slate-200">3. Phát sinh phí khôn lường:</strong> Ghi hợp đồng mập mờ, sau khi chuyển vào thì bắt buộc đóng thêm phí dịch vụ cắt cổ như tiền giặt, tiền bảo trì thang máy, tiền rác gấp 5 lần bình thường.</p>
                        <p><strong class="text-rose-400">⚠️ Cách phòng tránh:</strong> Tuyệt đối không cọc khi chưa xem phòng trực tiếp, chưa gặp chủ nhà và kiểm tra giấy tờ chứng minh sở hữu căn nhà.</p>
                    '
                ],
                [
                    'id' => 6,
                    'tag' => 'Chia Sẻ',
                    'tag_class' => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20',
                    'time' => '4 ngày trước',
                    'title' => 'Kinh nghiệm ở ghép cùng người lạ: Làm sao để tránh xung đột?',
                    'summary' => 'Các nguyên tắc vàng để duy trì mối quan hệ hòa thuận, chia sẻ chi phí sòng phẳng khi thuê phòng ở ghép cùng người mới quen.',
                    'author' => 'Phương Thảo',
                    'border_color' => 'indigo',
                    'shadow_color' => '99,102,241',
                    'text_color' => 'indigo-400',
                    'text_hover_color' => 'indigo-300',
                    'detail' => '
                        <p><strong class="text-slate-200">1. Thống nhất quy tắc tài chính:</strong> Chia đều hóa đơn điện nước, mạng internet, ga ăn uống ngay từ đầu tháng. Tạo file Excel theo dõi minh bạch.</p>
                        <p><strong class="text-slate-200">2. Giờ giấc sinh hoạt & Dọn dẹp vệ sinh:</strong> Phân chia lịch trực nhật phòng tắm, nấu ăn cụ thể. Hạn chế dẫn bạn bè về phòng tụ tập khuya gây ảnh hưởng đến giấc ngủ của người khác.</p>
                        <p><strong class="text-slate-200">3. Tôn trọng không gian riêng tư:</strong> Không tự ý sử dụng đồ dùng cá nhân, mỹ phẩm hay quần áo của bạn cùng phòng khi chưa được sự đồng ý.</p>
                        <p><strong class="text-indigo-400">💡 Gợi ý:</strong> Nên có một buổi nói chuyện thẳng thắn mỗi tháng để cùng nhau tháo gỡ những điểm chưa hài lòng, tránh tích tụ bức xúc lâu ngày.</p>
                    '
                ],
                [
                    'id' => 7,
                    'tag' => 'Khu Vực',
                    'tag_class' => 'bg-teal-500/10 text-teal-400 border border-teal-500/20',
                    'time' => '5 ngày trước',
                    'title' => 'Đánh giá chi tiết ngõ 175 Xuân Thủy Cầu Giấy dưới góc nhìn sinh viên',
                    'summary' => 'Phân tích tiện ích xung quanh, tình hình giao thông, giá cả sinh hoạt tại ngõ trọ hot bậc nhất Cầu Giấy.',
                    'author' => 'Bảo Châu',
                    'border_color' => 'teal',
                    'shadow_color' => '20,184,166',
                    'text_color' => 'teal-400',
                    'text_hover_color' => 'teal-300',
                    'detail' => '
                        <p><strong class="text-slate-200">Ưu điểm:</strong> Nằm ngay trung tâm Cầu Giấy, gần các trường Đại học Sư Phạm, Quốc Gia, Báo Chí. Xung quanh ngõ có chợ dân sinh, siêu thị mini và vô vàn quán ăn giá rẻ cho sinh viên.</p>
                        <p><strong class="text-slate-200">Nhược điểm:</strong> Mật độ dân cư quá đông đúc, ngõ nhỏ hẹp thường xuyên xảy ra ùn tắc vào giờ cao điểm từ 17h00 - 19h00. Tiếng ồn từ các hàng quán mở muộn có thể ảnh hưởng đến học tập.</p>
                        <p><strong class="text-slate-200">Chi phí:</strong> Giá phòng ở đây dao động từ 2.8 - 4.5tr. Chi phí ăn uống cực kỳ rẻ nhờ cạnh tranh cao, trung bình chỉ 30k-35k cho một suất cơm trưa chất lượng.</p>
                    '
                ],
                [
                    'id' => 8,
                    'tag' => 'Tiện Ích',
                    'tag_class' => 'bg-violet-500/10 text-violet-400 border border-violet-500/20',
                    'time' => '1 tuần trước',
                    'title' => 'Những tiện ích bắt buộc phải có khi thuê căn hộ dịch vụ Quận 10',
                    'summary' => 'Danh sách các trang bị cơ bản giúp tối ưu cuộc sống bận rộn của người đi làm khi thuê phòng dịch vụ.',
                    'author' => 'Anh Tuấn',
                    'border_color' => 'violet',
                    'shadow_color' => '139,92,246',
                    'text_color' => 'violet-400',
                    'text_hover_color' => 'violet-300',
                    'detail' => '
                        <p><strong class="text-slate-200">1. Hệ thống giặt sấy chung/riêng:</strong> Giúp tiết kiệm thời gian phơi phóng quần áo, đặc biệt tiện lợi trong mùa mưa kéo dài ở Sài Gòn.</p>
                        <p><strong class="text-slate-200">2. Khóa vân tay tích hợp camera:</strong> Đảm bảo chỉ người trong tòa nhà được ra vào, hạn chế người lạ đột nhập trộm xe máy ở tầng trệt.</p>
                        <p><strong class="text-slate-200">3. Khu bếp nấu ăn thông thoáng:</strong> Phòng có cửa sổ lớn hoặc máy hút mùi giúp tránh ám mùi dầu mỡ vào giường ngủ.</p>
                        <p><strong class="text-violet-400">🔥 Lưu ý đặc biệt:</strong> Kiểm tra xem tòa nhà có trang bị thang thoát hiểm bên ngoài và hệ thống báo cháy tự động hoạt động tốt hay không.</p>
                    '
                ],
                [
                    'id' => 9,
                    'tag' => 'Khu Vực',
                    'tag_class' => 'bg-teal-500/10 text-teal-400 border border-teal-500/20',
                    'time' => '1 tuần trước',
                    'title' => 'Review khu vực trọ Phùng Khoang, Thanh Xuân – Thiên đường giá rẻ',
                    'summary' => 'Đánh giá chân thực về mức giá thuê phòng, chi phí ăn uống và những lưu ý an ninh tại khu chợ Phùng Khoang.',
                    'author' => 'Tiến Đạt',
                    'border_color' => 'teal',
                    'shadow_color' => '20,184,166',
                    'text_color' => 'teal-400',
                    'text_hover_color' => 'teal-300',
                    'detail' => '
                        <p><strong class="text-slate-200">Giá phòng:</strong> Rất rẻ, chỉ từ 1.8 - 2.5 triệu cho phòng khép kín cơ bản, phù hợp với các bạn sinh viên muốn tối ưu ngân sách chi tiêu hàng tháng.</p>
                        <p><strong class="text-slate-200">Ăn uống & Mua sắm:</strong> Chợ Phùng Khoang bán thực phẩm tươi sống với giá vô cùng hạt dẻ. Đồ ăn vặt, quần áo thời trang dọc ngõ trọ phong phú, phục vụ học sinh sinh viên.</p>
                        <p><strong class="text-slate-200">An ninh:</strong> Do lượng người qua lại lớn nên an ninh phức tạp hơn các khu khác. Cần chọn tòa nhà có bảo vệ hoặc camera giám sát bãi để xe 24/24.</p>
                    '
                ],
                [
                    'id' => 10,
                    'tag' => 'Cẩm Nang',
                    'tag_class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                    'time' => '2 tuần trước',
                    'title' => 'Check-list 10 điều bắt buộc phải kiểm tra trước khi đặt bút ký hợp đồng trọ',
                    'summary' => 'Cẩm nang pháp lý và thực tế giúp người thuê nhà bảo vệ quyền lợi, tránh các tranh chấp về sau.',
                    'author' => 'Lan Hương',
                    'border_color' => 'emerald',
                    'shadow_color' => '16,185,129',
                    'text_color' => 'emerald-400',
                    'text_hover_color' => 'emerald-300',
                    'detail' => '
                        <p><strong class="text-slate-200">1. Xác minh thông tin chủ nhà:</strong> Yêu cầu xem sổ hồng căn nhà hoặc hợp đồng ủy quyền quản lý hợp pháp của chủ nhà.</p>
                        <p><strong class="text-slate-200">2. Làm rõ giá dịch vụ:</strong> Ghi rõ giá điện, nước, internet, tiền rác, tiền xe trong hợp đồng; tránh việc tăng giá đột ngột sau vài tháng ở.</p>
                        <p><strong class="text-slate-200">3. Chụp ảnh tình trạng thiết bị:</strong> Lưu lại hình ảnh hiện trạng tường sơn, tủ lạnh, điều hòa khi bàn giao để đối chiếu khi chuyển đi, tránh bị trừ tiền cọc oan.</p>
                        <p><strong class="text-emerald-400">💡 Lời khuyên:</strong> Hãy thỏa thuận rõ về thời gian báo trước khi trả phòng (thường là 30 ngày) để nhận lại đầy đủ 100% tiền đặt cọc.</p>
                    '
                ],
                [
                    'id' => 11,
                    'tag' => 'Review Phòng',
                    'tag_class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                    'time' => '2 tuần trước',
                    'title' => 'Đánh giá chất lượng chung cư mini khu vực Quận Bình Thạnh',
                    'summary' => 'Trải nghiệm thực tế về không gian sống, dịch vụ vệ sinh và sự thuận tiện đi lại của các căn hộ studio Bình Thạnh.',
                    'author' => 'Tuấn Kiệt',
                    'border_color' => 'amber',
                    'shadow_color' => '245,158,11',
                    'text_color' => 'amber-400',
                    'text_hover_color' => 'amber-300',
                    'detail' => '
                        <p><strong class="text-slate-200">Vị trí đắc địa:</strong> Gần Ngã tư Hàng Xanh, dễ dàng kết nối sang Quận 1, Quận 2 và Thủ Đức. Khu Nguyễn Gia Trí nhộn nhịp, đầy đủ tiện ích mua sắm, ăn chơi giải trí.</p>
                        <p><strong class="text-slate-200">Chất lượng phòng:</strong> Hầu hết là chung cư mini mới xây từ 3-5 năm, thiết kế studio hiện đại có ban công lớn đón ánh sáng tự nhiên. Giá thuê tầm 5.5 - 7.5 triệu.</p>
                        <p><strong class="text-slate-200">Điểm trừ:</strong> Tình trạng ngập úng nhẹ ở các hẻm nhỏ dọc đường Nguyễn Hữu Cảnh, Xô Viết Nghệ Tĩnh khi triều cường lên cao hoặc mưa lớn liên tục.</p>
                    '
                ],
                [
                    'id' => 12,
                    'tag' => 'Khu Vực',
                    'tag_class' => 'bg-teal-500/10 text-teal-400 border border-teal-500/20',
                    'time' => '3 tuần trước',
                    'title' => 'Review trọ khu vực Tây Hồ: Sang xịn mịn, view hồ thoáng mát',
                    'summary' => 'Khảo sát giá thuê căn hộ studio, phong cách thiết kế và đối tượng người thuê chủ yếu tại khu vực ven Hồ Tây.',
                    'author' => 'Thu Trang',
                    'border_color' => 'teal',
                    'shadow_color' => '20,184,166',
                    'text_color' => 'teal-400',
                    'text_hover_color' => 'teal-300',
                    'detail' => '
                        <p><strong class="text-slate-200">Không gian sống:</strong> Không khí trong lành nhất Hà Nội, thoáng đãng, nhiều cây xanh. Phù hợp cho những ai yêu thích sự yên tĩnh, bình yên sau giờ học và làm việc căng thẳng.</p>
                        <p><strong class="text-slate-200">Giá cả:</strong> Khá cao, dao động từ 6 – 10 triệu/tháng cho căn hộ studio đầy đủ nội thất cao cấp. Đối tượng thuê chủ yếu là người nước ngoài và người đi làm thu nhập cao.</p>
                        <p><strong class="text-slate-200">Tiện ích:</strong> Nhiều quán cafe view hồ xinh xắn, phòng gym hiện đại, nhà hàng Tây Âu và siêu thị đồ nhập khẩu chất lượng cao.</p>
                    '
                ],
                [
                    'id' => 13,
                    'tag' => 'Chia Sẻ',
                    'tag_class' => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20',
                    'time' => '3 tuần trước',
                    'title' => 'Kinh nghiệm vàng giúp tiết kiệm tối đa chi phí sinh hoạt cho sinh viên ở trọ',
                    'summary' => 'Những mẹo thực tiễn giúp quản lý tài chính thông minh, giảm thiểu hóa đơn tiền điện nước hàng tháng hiệu quả.',
                    'author' => 'Minh Quân',
                    'border_color' => 'indigo',
                    'shadow_color' => '99,102,241',
                    'text_color' => 'indigo-400',
                    'text_hover_color' => 'indigo-300',
                    'detail' => '
                        <p><strong class="text-slate-200">1. Tự nấu ăn tại phòng:</strong> Hạn chế ăn ngoài hàng quán, chuẩn bị cơm hộp đi học giúp tiết kiệm đến 50% chi phí ăn uống mỗi tháng.</p>
                        <p><strong class="text-slate-200">2. Sử dụng điện nước tiết kiệm:</strong> Tắt điều hòa khi ra ngoài, tận dụng ánh sáng tự nhiên ban ngày, gom quần áo giặt chung máy giặt để tiết kiệm điện nước.</p>
                        <p><strong class="text-slate-200">3. Tìm bạn ở ghép:</strong> Chia sẻ không gian sống cùng 1-2 người bạn thân giúp giảm thiểu một nửa tiền phòng và các phí dịch vụ chung khác.</p>
                    '
                ],
                [
                    'id' => 14,
                    'tag' => 'An Toàn',
                    'tag_class' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                    'time' => '1 tháng trước',
                    'title' => 'Cẩm nang an toàn phòng cháy chữa cháy tại các khu nhà trọ nhiều tầng',
                    'summary' => 'Hướng dẫn kiểm tra lối thoát hiểm, cách sử dụng bình chữa cháy và xử lý tình huống khẩn cấp khi xảy ra hỏa hoạn.',
                    'author' => 'Đức Hùng',
                    'border_color' => 'rose',
                    'shadow_color' => '244,63,94',
                    'text_color' => 'rose-400',
                    'text_hover_color' => 'rose-300',
                    'detail' => '
                        <p><strong class="text-slate-200">1. Khảo sát lối thoát hiểm:</strong> Luôn xác định rõ các hướng thoát hiểm phụ như ban công, lối lên sân thượng của tòa nhà khi mới chuyển đến ở.</p>
                        <p><strong class="text-slate-200">2. Trang bị bình chữa cháy mini:</strong> Đề xuất chủ nhà trang bị đầy đủ bình chữa cháy dạng bột/khí CO2 tại khu vực cầu thang bộ mỗi tầng trệt.</p>
                        <p><strong class="text-slate-200">3. Quy tắc an toàn điện xe máy:</strong> Hạn chế sạc xe máy điện, xe đạp điện qua đêm ở tầng trệt khi không có hệ thống ngắt điện tự động an toàn.</p>
                    '
                ]
            ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                @foreach ($featuredArticles as $index => $article)
                    <!-- Article Card {{ $article['id'] }} -->
                    <article id="article-card-{{ $article['id'] }}" class="group bg-slate-900/40 backdrop-blur-md border border-slate-800 hover:border-{{ $article['border_color'] }}/40 rounded-2xl overflow-hidden hover:shadow-[0_20px_50px_rgba({{ $article['shadow_color'] }},0.12)] transition-all duration-300 flex flex-col justify-between {{ $index >= 4 ? 'article-extra hidden' : '' }}">
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $article['tag_class'] }} uppercase tracking-wider">
                                    {{ $article['tag'] }}
                                </span>
                                <span class="text-[11px] text-slate-500 font-bold">{{ $article['time'] }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-200 group-hover:text-{{ $article['text_color'] }} transition-colors mb-3">
                                {{ $article['title'] }}
                            </h3>
                            <p class="text-slate-400 text-[11px] leading-relaxed mb-4">
                                {{ $article['summary'] }}
                            </p>
                            <div id="article-detail-{{ $article['id'] }}" class="hidden mt-2 pt-4 border-t border-slate-800/30 space-y-3 text-[11px] text-slate-400 leading-relaxed animate-fadeIn">
                                {!! $article['detail'] !!}
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-3 border-t border-slate-800/40 flex justify-between items-center bg-slate-950/20">
                            <span class="text-[10px] text-slate-500 font-semibold">Tác giả: {{ $article['author'] }}</span>
                            <button type="button" onclick="toggleArticleDetail({{ $article['id'] }})" id="article-toggle-{{ $article['id'] }}" class="text-[11px] text-{{ $article['text_color'] }} hover:text-{{ $article['text_hover_color'] }} font-bold flex items-center gap-1 group-hover:translate-x-0.5 transition-all cursor-pointer">
                                Đọc tiếp <i class="fa-solid fa-angle-right text-[9px] transition-transform" id="article-icon-{{ $article['id'] }}"></i>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            @if (count($featuredArticles) > 4)
                <div class="flex justify-center mt-8" id="show-more-articles-btn-container">
                    <button type="button" onclick="toggleMoreArticles()" id="toggle-more-articles-btn" class="px-6 py-2.5 rounded-xl border border-slate-800 bg-slate-900/60 hover:bg-slate-900 hover:border-teal-500/40 text-teal-400 hover:text-teal-300 text-xs font-bold transition-all shadow-lg flex items-center gap-2 cursor-pointer">
                        <span id="toggle-more-articles-btn-text">Xem thêm bài viết</span>
                        <i id="toggle-more-articles-btn-icon" class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                </div>
            @endif

            <script>
            function toggleMoreArticles() {
                const extraArticles = document.querySelectorAll('.article-extra');
                const btnText = document.getElementById('toggle-more-articles-btn-text');
                const btnIcon = document.getElementById('toggle-more-articles-btn-icon');
                if (!btnText || !btnIcon || extraArticles.length === 0) return;

                const isHidden = extraArticles[0].classList.contains('hidden');

                if (isHidden) {
                    extraArticles.forEach(art => {
                        art.classList.remove('hidden');
                        art.style.animation = 'fadeSlideDown 0.4s ease-out forwards';
                    });
                    btnText.textContent = 'Thu gọn bài viết';
                    btnIcon.className = 'fa-solid fa-chevron-up text-[10px]';
                } else {
                    extraArticles.forEach(art => {
                        art.classList.add('hidden');
                    });
                    btnText.textContent = 'Xem thêm bài viết';
                    btnIcon.className = 'fa-solid fa-chevron-down text-[10px]';
                    
                    // Smoothly scroll back to the top of featured articles section
                    const sectionHeader = document.querySelector('.fa-newspaper').closest('h2');
                    if (sectionHeader) {
                        sectionHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            }

            function toggleArticleDetail(id) {
                const detail = document.getElementById('article-detail-' + id);
                const toggleBtn = document.getElementById('article-toggle-' + id);
                const icon = document.getElementById('article-icon-' + id);
                if (!detail || !toggleBtn) return;

                const isHidden = detail.classList.contains('hidden');
                detail.classList.toggle('hidden', !isHidden);
                
                if (isHidden) {
                    toggleBtn.innerHTML = 'Thu gọn <i class="fa-solid fa-angle-up text-[9px] transition-transform"></i>';
                    detail.style.animation = 'fadeSlideDown 0.3s ease-out forwards';
                } else {
                    toggleBtn.innerHTML = 'Đọc tiếp <i class="fa-solid fa-angle-right text-[9px] transition-transform"></i>';
                }
            }
            </script>
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
                    <a href="tel:0396519196" class="quick-preview-call">
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
                            <a href="tel:0396519196" class="flex-1 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all">
                                <i class="fa-solid fa-phone"></i> Gọi điện
                            </a>
                            <a href="https://zalo.me/0396519196" target="_blank" class="flex-1 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all">
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
                    <a href="https://zalo.me/0396519196" target="_blank" class="w-full min-h-10 rounded-lg bg-blue-600/20 border border-blue-500/30 text-blue-200 text-xs font-extrabold flex items-center justify-center gap-2 hover:bg-blue-600/30 transition-all">
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
                    <a href="tel:0396519196" class="sticky-call-button"><i class="fa-solid fa-phone"></i> Gọi điện</a>
                    <a href="https://zalo.me/0396519196" target="_blank" class="sticky-zalo-button"><i class="fa-solid fa-comments"></i> Chat Zalo</a>
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
    </div>

    <!-- Sync Hero Search Input with Navbar Search Input -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navbarInput = document.getElementById('search-input');
            const heroInput = document.getElementById('hero-search-input');

            function syncInputs(source, target) {
                if (source && target && target.value !== source.value) {
                    target.value = source.value;
                }
            }

            if (navbarInput && heroInput) {
                navbarInput.addEventListener('input', () => {
                    syncInputs(navbarInput, heroInput);
                });
                
                heroInput.addEventListener('input', () => {
                    syncInputs(heroInput, navbarInput);
                    if (typeof filterItems === 'function') {
                        filterItems();
                    }
                });

                navbarInput.addEventListener('change', () => {
                    syncInputs(navbarInput, heroInput);
                });

                // Periodic check for search suggestion updates
                setInterval(() => {
                    if (navbarInput.value !== heroInput.value) {
                        syncInputs(navbarInput, heroInput);
                    }
                }, 200);
            }
        });
    </script>
</body>
</html>
