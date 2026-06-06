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
                <a href="#" class="hover:text-slate-200">Khu Vực Hot</a>
                <a href="#" class="hover:text-slate-200">Đánh Giá Mới</a>
                <a href="{{ route('smartroom.admin') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-slate-100 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-indigo-400"></i> Chủ Trọ Đăng Nhập
                </a>
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
            <h2 class="text-lg font-bold text-slate-200" id="results-count">Danh sách phòng trọ (4 kết quả)</h2>
            <p class="text-xs text-slate-500">Tích chọn tối đa 3 phòng để so sánh</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16" id="rooms-grid">
            <!-- Room 1 -->
            <div class="room-item-card glass-card rounded-2xl overflow-hidden group flex flex-col justify-between" 
                 data-price="3500000" data-rating="4.6" data-pets="true" data-loft="true" data-balcony="false" data-distance="0.8" data-title="SmartRoom Cầu Giấy - Phòng 301">
                <div>
                    <!-- Thumbnail image (CSS gradient placeholder) -->
                    <div class="h-44 bg-gradient-to-tr from-indigo-900 to-indigo-950 relative overflow-hidden flex items-center justify-center border-b border-slate-900">
                        <span class="absolute top-4 left-4 px-2 py-1 bg-emerald-500 text-white rounded text-[10px] font-extrabold uppercase shadow">Sẵn sàng</span>
                        <div class="text-center">
                            <i class="fa-solid fa-home text-slate-600 text-4xl mb-2 group-hover:scale-110 transition-all duration-300"></i>
                            <span class="block text-xs font-bold text-slate-400">SmartRoom Cầu Giấy</span>
                        </div>
                    </div>
                    <!-- Details -->
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-all">SmartRoom Cầu Giấy - P. 301</h3>
                            <div class="flex items-center gap-1 text-xs text-amber-400 font-bold">
                                <i class="fa-solid fa-star"></i> 4.6
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mb-4 flex items-center gap-1"><i class="fa-solid fa-location-dot text-slate-600"></i> Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy (Cách ĐH Sư Phạm 0.8km)</p>
                        <!-- Prices and tags -->
                        <div class="flex items-baseline gap-1.5 mb-4">
                            <span class="text-lg font-extrabold text-emerald-400">3.500.000đ</span>
                            <span class="text-[10px] text-slate-500 font-semibold">/ tháng</span>
                        </div>
                        <div class="flex flex-wrap gap-1 mb-4">
                            <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-400 text-[10px] font-bold">Có gác lửng</span>
                            <span class="px-2 py-0.5 rounded bg-teal-500/10 text-teal-400 text-[10px] font-bold">Nuôi thú cưng</span>
                            <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-500 text-[10px] font-bold">Vệ sinh khép kín</span>
                        </div>
                    </div>
                </div>
                <!-- Card footer action -->
                <div class="px-5 pb-5 pt-3 border-t border-slate-900/50 flex justify-between items-center bg-slate-950/20">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer">
                        <input type="checkbox" onchange="toggleCompare('room1', this)" class="compare-checkbox w-4.5 h-4.5 rounded border-slate-800 text-emerald-600 focus:ring-0">
                        <span>So sánh</span>
                    </label>
                    <button class="text-xs text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1">
                        Xem chi tiết <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>

            <!-- Room 2 -->
            <div class="room-item-card glass-card rounded-2xl overflow-hidden group flex flex-col justify-between" 
                 data-price="3800000" data-rating="4.2" data-pets="false" data-loft="true" data-balcony="true" data-distance="1.2" data-title="Chung Cư Mini Trần Thái Tông - Phòng 201">
                <div>
                    <div class="h-44 bg-gradient-to-tr from-slate-900 to-slate-950 relative overflow-hidden flex items-center justify-center border-b border-slate-900">
                        <span class="absolute top-4 left-4 px-2 py-1 bg-emerald-500 text-white rounded text-[10px] font-extrabold uppercase shadow">Sẵn sàng</span>
                        <div class="text-center">
                            <i class="fa-solid fa-home text-slate-600 text-4xl mb-2 group-hover:scale-110 transition-all duration-300"></i>
                            <span class="block text-xs font-bold text-slate-400">CCMN Trần Thái Tông</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-all">CCMN Trần Thái Tông - P. 201</h3>
                            <div class="flex items-center gap-1 text-xs text-amber-400 font-bold">
                                <i class="fa-solid fa-star"></i> 4.2
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mb-4 flex items-center gap-1"><i class="fa-solid fa-location-dot text-slate-600"></i> Số 5 Ngõ 45 Trần Thái Tông, Cầu Giấy (Cách ĐH Bách Khoa 1.2km)</p>
                        <div class="flex items-baseline gap-1.5 mb-4">
                            <span class="text-lg font-extrabold text-emerald-400">3.800.000đ</span>
                            <span class="text-[10px] text-slate-500 font-semibold">/ tháng</span>
                        </div>
                        <div class="flex flex-wrap gap-1 mb-4">
                            <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-400 text-[10px] font-bold">Có gác lửng</span>
                            <span class="px-2 py-0.5 rounded bg-sky-500/10 text-sky-400 text-[10px] font-bold">Ban công</span>
                            <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-500 text-[10px] font-bold">Khoá vân tay</span>
                        </div>
                    </div>
                </div>
                <div class="px-5 pb-5 pt-3 border-t border-slate-900/50 flex justify-between items-center bg-slate-950/20">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer">
                        <input type="checkbox" onchange="toggleCompare('room2', this)" class="compare-checkbox w-4.5 h-4.5 rounded border-slate-800 text-emerald-600 focus:ring-0">
                        <span>So sánh</span>
                    </label>
                    <button class="text-xs text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1">
                        Xem chi tiết <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>

            <!-- Room 3 -->
            <div class="room-item-card glass-card rounded-2xl overflow-hidden group flex flex-col justify-between" 
                 data-price="4200000" data-rating="4.8" data-pets="true" data-loft="false" data-balcony="true" data-distance="0.5" data-title="Căn Hộ Dịch Vụ Hồ Tùng Mậu - Phòng 104">
                <div>
                    <div class="h-44 bg-gradient-to-tr from-teal-950 to-slate-950 relative overflow-hidden flex items-center justify-center border-b border-slate-900">
                        <span class="absolute top-4 left-4 px-2 py-1 bg-emerald-500 text-white rounded text-[10px] font-extrabold uppercase shadow">Sẵn sàng</span>
                        <div class="text-center">
                            <i class="fa-solid fa-home text-slate-600 text-4xl mb-2 group-hover:scale-110 transition-all duration-300"></i>
                            <span class="block text-xs font-bold text-slate-400">CHDV Hồ Tùng Mậu</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-all">CHDV Hồ Tùng Mậu - P. 104</h3>
                            <div class="flex items-center gap-1 text-xs text-amber-400 font-bold">
                                <i class="fa-solid fa-star"></i> 4.8
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mb-4 flex items-center gap-1"><i class="fa-solid fa-location-dot text-slate-600"></i> Số 88 Hồ Tùng Mậu, Cầu Giấy (Cách ĐH Thương Mại 0.5km)</p>
                        <div class="flex items-baseline gap-1.5 mb-4">
                            <span class="text-lg font-extrabold text-emerald-400">4.200.000đ</span>
                            <span class="text-[10px] text-slate-500 font-semibold">/ tháng</span>
                        </div>
                        <div class="flex flex-wrap gap-1 mb-4">
                            <span class="px-2 py-0.5 rounded bg-sky-500/10 text-sky-400 text-[10px] font-bold">Ban công</span>
                            <span class="px-2 py-0.5 rounded bg-teal-500/10 text-teal-400 text-[10px] font-bold">Nuôi thú cưng</span>
                            <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-400 text-[10px] font-bold">Đủ nội thất</span>
                        </div>
                    </div>
                </div>
                <div class="px-5 pb-5 pt-3 border-t border-slate-900/50 flex justify-between items-center bg-slate-950/20">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer">
                        <input type="checkbox" onchange="toggleCompare('room3', this)" class="compare-checkbox w-4.5 h-4.5 rounded border-slate-800 text-emerald-600 focus:ring-0">
                        <span>So sánh</span>
                    </label>
                    <button class="text-xs text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1">
                        Xem chi tiết <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>

            <!-- Room 4 -->
            <div class="room-item-card glass-card rounded-2xl overflow-hidden group flex flex-col justify-between" 
                 data-price="2800000" data-rating="3.9" data-pets="false" data-loft="false" data-balcony="false" data-distance="1.5" data-title="Phòng Trọ Giá Rẻ Phạm Văn Đồng">
                <div>
                    <div class="h-44 bg-gradient-to-tr from-slate-900 to-slate-950 relative overflow-hidden flex items-center justify-center border-b border-slate-900">
                        <span class="absolute top-4 left-4 px-2 py-1 bg-emerald-500 text-white rounded text-[10px] font-extrabold uppercase shadow">Sẵn sàng</span>
                        <div class="text-center">
                            <i class="fa-solid fa-home text-slate-600 text-4xl mb-2 group-hover:scale-110 transition-all duration-300"></i>
                            <span class="block text-xs font-bold text-slate-400">Nhà trọ Phạm Văn Đồng</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-all">Nhà Trọ Phạm Văn Đồng</h3>
                            <div class="flex items-center gap-1 text-xs text-amber-400 font-bold">
                                <i class="fa-solid fa-star"></i> 3.9
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mb-4 flex items-center gap-1"><i class="fa-solid fa-location-dot text-slate-600"></i> Ngõ 23 Phạm Văn Đồng, Bắc Từ Liêm (Cách ĐH Ngoại Ngữ 1.5km)</p>
                        <div class="flex items-baseline gap-1.5 mb-4">
                            <span class="text-lg font-extrabold text-emerald-400">2.800.000đ</span>
                            <span class="text-[10px] text-slate-500 font-semibold">/ tháng</span>
                        </div>
                        <div class="flex flex-wrap gap-1 mb-4">
                            <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-500 text-[10px] font-bold">Tự do giờ giấc</span>
                            <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-500 text-[10px] font-bold">Vệ sinh khép kín</span>
                        </div>
                    </div>
                </div>
                <div class="px-5 pb-5 pt-3 border-t border-slate-900/50 flex justify-between items-center bg-slate-950/20">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer">
                        <input type="checkbox" onchange="toggleCompare('room4', this)" class="compare-checkbox w-4.5 h-4.5 rounded border-slate-800 text-emerald-600 focus:ring-0">
                        <span>So sánh</span>
                    </label>
                    <button class="text-xs text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1">
                        Xem chi tiết <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>
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
        <div class="w-full max-w-4xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto animate-fade-in">
            <button onclick="closeCompareModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-xl font-bold mb-6 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-code-compare text-emerald-400"></i> Bảng So Sánh Các Phòng Trọ Đã Chọn
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300 border-collapse">
                    <thead>
                        <tr class="border-b border-slate-900">
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tiêu chí</th>
                            <th class="px-4 py-3 font-bold text-xs text-emerald-400" id="compare-col-1-title">Phòng 1</th>
                            <th class="px-4 py-3 font-bold text-xs text-emerald-400" id="compare-col-2-title">Phòng 2</th>
                            <th class="px-4 py-3 font-bold text-xs text-emerald-400" id="compare-col-3-title">Phòng 3</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900">
                        <!-- Rent price -->
                        <tr>
                            <td class="px-4 py-4 text-xs font-semibold text-slate-500">Giá thuê tháng</td>
                            <td class="px-4 py-4 text-sm font-extrabold text-slate-200" id="compare-val-1-price">-</td>
                            <td class="px-4 py-4 text-sm font-extrabold text-slate-200" id="compare-val-2-price">-</td>
                            <td class="px-4 py-4 text-sm font-extrabold text-slate-200" id="compare-val-3-price">-</td>
                        </tr>
                        <!-- Distance to campus -->
                        <tr>
                            <td class="px-4 py-4 text-xs font-semibold text-slate-500">Khoảng cách trường</td>
                            <td class="px-4 py-4 text-xs text-slate-300" id="compare-val-1-dist">-</td>
                            <td class="px-4 py-4 text-xs text-slate-300" id="compare-val-2-dist">-</td>
                            <td class="px-4 py-4 text-xs text-slate-300" id="compare-val-3-dist">-</td>
                        </tr>
                        <!-- Overall score -->
                        <tr>
                            <td class="px-4 py-4 text-xs font-semibold text-slate-500">Đánh giá chung</td>
                            <td class="px-4 py-4 text-xs font-bold text-amber-400" id="compare-val-1-rating">-</td>
                            <td class="px-4 py-4 text-xs font-bold text-amber-400" id="compare-val-2-rating">-</td>
                            <td class="px-4 py-4 text-xs font-bold text-amber-400" id="compare-val-3-rating">-</td>
                        </tr>
                        <!-- Owner Score -->
                        <tr>
                            <td class="px-4 py-4 text-xs font-semibold text-slate-500">Điểm chủ nhà</td>
                            <td class="px-4 py-4 text-xs text-slate-300" id="compare-val-1-owner">⭐⭐⭐⭐⭐ (5/5)</td>
                            <td class="px-4 py-4 text-xs text-slate-300" id="compare-val-2-owner">⭐⭐⭐⭐☆ (4/5)</td>
                            <td class="px-4 py-4 text-xs text-slate-300" id="compare-val-3-owner">⭐⭐⭐⭐⭐ (5/5)</td>
                        </tr>
                        <!-- Security Score -->
                        <tr>
                            <td class="px-4 py-4 text-xs font-semibold text-slate-500">An ninh & Khóa</td>
                            <td class="px-4 py-4 text-xs text-slate-300" id="compare-val-1-sec">⭐⭐⭐⭐☆ (4/5)</td>
                            <td class="px-4 py-4 text-xs text-slate-300" id="compare-val-2-sec">⭐⭐⭐⭐⭐ (5/5)</td>
                            <td class="px-4 py-4 text-xs text-slate-300" id="compare-val-3-sec">⭐⭐⭐⭐⭐ (5/5)</td>
                        </tr>
                        <!-- Pets allowed -->
                        <tr>
                            <td class="px-4 py-4 text-xs font-semibold text-slate-500">Cho nuôi thú cưng</td>
                            <td class="px-4 py-4 text-xs" id="compare-val-1-pets">-</td>
                            <td class="px-4 py-4 text-xs" id="compare-val-2-pets">-</td>
                            <td class="px-4 py-4 text-xs" id="compare-val-3-pets">-</td>
                        </tr>
                        <!-- Has loft -->
                        <tr>
                            <td class="px-4 py-4 text-xs font-semibold text-slate-500">Có gác lửng</td>
                            <td class="px-4 py-4 text-xs" id="compare-val-1-loft">-</td>
                            <td class="px-4 py-4 text-xs" id="compare-val-2-loft">-</td>
                            <td class="px-4 py-4 text-xs" id="compare-val-3-loft">-</td>
                        </tr>
                        <!-- Has balcony -->
                        <tr>
                            <td class="px-4 py-4 text-xs font-semibold text-slate-500">Có ban công thoáng</td>
                            <td class="px-4 py-4 text-xs" id="compare-val-1-balcony">-</td>
                            <td class="px-4 py-4 text-xs" id="compare-val-2-balcony">-</td>
                            <td class="px-4 py-4 text-xs" id="compare-val-3-balcony">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 pt-4 flex justify-end">
                <button onclick="closeCompareModal()" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-700 transition-all">
                    Đóng bảng so sánh
                </button>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="border-t border-slate-900 bg-slate-950/80 py-8 mt-12 relative z-10">
        <div class="container mx-auto px-6 max-w-6xl flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <div>
                © 2026 Renty Review. Hệ thống đánh giá không gian sống sinh viên Việt Nam.
            </div>
            <div class="flex items-center gap-4">
                <span>Thực hiện bởi: <strong class="text-slate-400">Thành viên 1</strong></span>
            </div>
        </div>
    </footer>

    <!-- JS LOGIC -->
    <script>
        // Toggle advanced filters
        function toggleFilterDrawer() {
            const drawer = document.getElementById('filter-drawer');
            drawer.classList.toggle('hidden');
        }

        // Room lists mock database object for comparison
        const mockRooms = {
            room1: {
                title: "SmartRoom Cầu Giấy - Phòng 301",
                price: 3500000,
                rating: 4.6,
                distance: "0.8km",
                pets: "Có",
                loft: "Có",
                balcony: "Không",
                owner: "⭐⭐⭐⭐⭐ (5/5)",
                sec: "⭐⭐⭐⭐☆ (4/5)"
            },
            room2: {
                title: "CCMN Trần Thái Tông - Phòng 201",
                price: 3800000,
                rating: 4.2,
                distance: "1.2km",
                pets: "Không",
                loft: "Có",
                balcony: "Có",
                owner: "⭐⭐⭐⭐☆ (4/5)",
                sec: "⭐⭐⭐⭐⭐ (5/5)"
            },
            room3: {
                title: "CHDV Hồ Tùng Mậu - Phòng 104",
                price: 4200000,
                rating: 4.8,
                distance: "0.5km",
                pets: "Có",
                loft: "Không",
                balcony: "Có",
                owner: "⭐⭐⭐⭐⭐ (5/5)",
                sec: "⭐⭐⭐⭐⭐ (5/5)"
            },
            room4: {
                title: "Nhà trọ giá rẻ Phạm Văn Đồng",
                price: 2800000,
                rating: 3.9,
                distance: "1.5km",
                pets: "Không",
                loft: "Không",
                balcony: "Không",
                owner: "⭐⭐⭐☆☆ (3/5)",
                sec: "⭐⭐⭐⭐☆ (4/5)"
            }
        };

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

        function openCompareModal() {
            if(selectedRooms.length === 0) return;
            
            const modal = document.getElementById('compare-modal');
            
            // Pre-clear table values
            for(let col=1; col<=3; col++) {
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

            // Fill table values from selectedRooms
            selectedRooms.forEach((roomId, idx) => {
                const col = idx + 1;
                const data = mockRooms[roomId];
                
                document.getElementById(`compare-col-${col}-title`).textContent = data.title;
                document.getElementById(`compare-val-${col}-price`).textContent = data.price.toLocaleString('vi-VN') + "đ";
                document.getElementById(`compare-val-${col}-dist`).textContent = data.distance;
                document.getElementById(`compare-val-${col}-rating`).textContent = data.rating + " ⭐";
                document.getElementById(`compare-val-${col}-owner`).textContent = data.owner;
                document.getElementById(`compare-val-${col}-sec`).textContent = data.sec;
                
                // Set green text for positive checkmarks
                const petsElem = document.getElementById(`compare-val-${col}-pets`);
                petsElem.textContent = data.pets;
                petsElem.className = data.pets === 'Có' ? 'px-4 py-4 text-xs font-bold text-emerald-400' : 'px-4 py-4 text-xs text-slate-500';

                const loftElem = document.getElementById(`compare-val-${col}-loft`);
                loftElem.textContent = data.loft;
                loftElem.className = data.loft === 'Có' ? 'px-4 py-4 text-xs font-bold text-emerald-400' : 'px-4 py-4 text-xs text-slate-500';

                const balconyElem = document.getElementById(`compare-val-${col}-balcony`);
                balconyElem.textContent = data.balcony;
                balconyElem.className = data.balcony === 'Có' ? 'px-4 py-4 text-xs font-bold text-emerald-400' : 'px-4 py-4 text-xs text-slate-500';
            });

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
    </script>
</body>
</html>
