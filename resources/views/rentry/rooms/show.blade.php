@php
    $images = $room['image_angles'] ?? collect($room['image_urls'] ?? [])->map(fn ($url, $index) => [
        'url' => $url,
        'label' => 'Ảnh thực tế ' . ($index + 1),
    ])->all();
    $fullDescription = collect([
        $room['location_description'] ?? null,
        $room['scenery_description'] ?? null,
        $room['space_description'] ?? null,
        'Tiện ích nổi bật: ' . (($room['loft_txt'] ?? 'Không') === 'Có' ? 'có gác lửng' : 'không gác lửng') . ', ' . (($room['balcony_txt'] ?? 'Không') === 'Có' ? 'có ban công' : 'không ban công') . ', ' . (($room['pets_txt'] ?? 'Không') === 'Có' ? 'có thể nuôi thú cưng' : 'không nuôi thú cưng') . '.',
    ])->filter()->join(' ');
    $reviewCount = count($room['reviews'] ?? []);
    $initialCost = ($room['price'] * 2) + 350000 + (2 * 20000) + 150000;
@endphp
<!-- DESIGN READ: Premium, bento-inspired dark mode detail page calibrated with Emerald / Teal accent hues, tactile interactive inputs, micro-glows, dynamic asymmetrical media grids, and optimized for smooth viewport-height fluidity without slop. -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $room['title'] }} - chi tiết hình ảnh, video tour, chi phí và đánh giá thực tế.">
    <title>{{ $room['title'] }} - Renty Review</title>
    <script>
        if (localStorage.getItem('renty_theme_mode') === 'light') {
            document.documentElement.classList.add('theme-light');
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        /* Glassmorphism custom styling for premium feel */
        .premium-card {
            background: linear-gradient(145deg, rgba(30, 41, 59, 0.45), rgba(15, 23, 42, 0.35));
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(148, 163, 184, 0.08);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03), 0 20px 50px rgba(2, 6, 23, 0.15);
        }
        .theme-light .premium-card {
            background: rgba(255, 255, 255, 0.85);
            border-color: rgba(203, 213, 225, 0.7);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.05);
        }
        .hover-lift {
            transition: transform 0.25s cubic-bezier(0.2, 1, 0.3, 1), border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            border-color: rgba(16, 185, 129, 0.3);
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.25);
        }
        /* Custom scrollbar for gallery list */
        .custom-scroll::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.4);
            border-radius: 99px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.2);
            border-radius: 99px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen overflow-x-hidden selection:bg-emerald-500 selection:text-white">
    <header class="h-20 border-b border-slate-900 bg-[#080b11]/85 backdrop-blur-md sticky top-0 z-40 flex items-center">
        <div class="container mx-auto px-6 max-w-6xl flex justify-between items-center">
            <a href="{{ route('renty.user') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fa-solid fa-magnifying-glass-location text-white text-lg"></i>
                </div>
                <span class="renty-brand-text text-xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Renty Review</span>
            </a>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" id="theme-toggle-icon"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-6 py-8 max-w-6xl">
        <!-- Back navigation link -->
        <div class="mb-6">
            <a href="{{ route('renty.user') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/30 text-slate-350 hover:text-emerald-300 text-xs font-bold transition-all active:scale-[0.98]">
                <i class="fa-solid fa-chevron-left text-[10px]"></i> Quay lại danh sách
            </a>
        </div>

        <!-- Room Header Details -->
        <section class="mb-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div>
                    <!-- Trust badges row -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-extrabold text-emerald-300 uppercase tracking-widest">{{ $room['media_source_label'] }}</span>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[9px] font-extrabold text-slate-300 uppercase tracking-widest">Phòng {{ $room['room_number'] }}</span>
                        <span class="px-2.5 py-1 rounded-lg border text-[9px] font-extrabold uppercase tracking-widest flex items-center gap-1.5 shadow-sm {{ $room['trust_badge']['class'] }}">
                            <i class="fa-solid {{ $room['trust_badge']['icon'] }}"></i>
                            <span>{{ $room['trust_badge']['label'] }}</span>
                        </span>
                    </div>
                    <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight text-slate-100">{{ $room['title'] }}</h1>
                    <p class="mt-3 text-xs md:text-sm text-slate-400 flex items-start gap-2 max-w-2xl leading-relaxed">
                        <i class="fa-solid fa-location-dot text-emerald-500/80 mt-0.5 shrink-0 text-sm"></i>
                        <span>{{ $room['address'] }}</span>
                    </p>
                </div>
                <div class="text-left md:text-right shrink-0">
                    <div class="text-3xl md:text-4xl font-black text-emerald-400 drop-shadow-[0_0_15px_rgba(52,211,153,0.1)]">{{ number_format($room['price'], 0, ',', '.') }}đ</div>
                    <div class="text-[11px] font-bold text-slate-500 mt-1.5">/ tháng · {{ $room['rating'] }} ⭐ · {{ $reviewCount }} đánh giá</div>
                    <button type="button" onclick="openReportModal()" class="mt-3.5 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-350 hover:text-rose-200 text-xs font-bold transition-colors active:scale-95">
                        <i class="fa-solid fa-flag text-[10px]"></i>
                        <span>Báo cáo tin đăng</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Main Workspace: Left content, Right sticky cards -->
        <section class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8 items-start">
            <div class="space-y-6">
                <!-- Bento Gallery Grid (Airbnb Style) -->
                <div class="relative rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 p-1.5 shadow-2xl shadow-slate-950/50">
                    @php
                        $imgCount = count($images);
                    @endphp
                    
                    @if($imgCount === 1)
                        <!-- 1 ảnh duy nhất -->
                        <button type="button" onclick="openZoomWithIndex(0)" class="relative block w-full h-[320px] md:h-[480px] rounded-xl overflow-hidden group text-left">
                            <img id="main-image" src="{{ $images[0]['url'] ?? $room['cover_image'] }}" alt="{{ $images[0]['label'] ?? 'Ảnh phòng' }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.015]" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                            <span class="media-overlay-label absolute left-4 bottom-4 z-10">
                                <i class="fa-solid fa-camera text-emerald-300 mr-1.5"></i><span id="main-image-label">{{ $images[0]['label'] ?? 'View toàn phòng' }}</span>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 to-transparent z-0"></div>
                            <span class="absolute right-4 bottom-4 w-10 h-10 rounded-xl bg-slate-950/75 border border-white/10 text-emerald-300 flex items-center justify-center backdrop-blur z-10">
                                <i class="fa-solid fa-expand"></i>
                            </span>
                        </button>
                    @elseif($imgCount === 2)
                        <!-- 2 ảnh chia đôi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 h-[320px] md:h-[480px]">
                            @foreach(array_slice($images, 0, 2) as $idx => $img)
                                <button type="button" onclick="openZoomWithIndex({{ $idx }})" class="relative w-full h-full rounded-xl overflow-hidden group text-left">
                                    <img src="{{ $img['url'] }}" alt="{{ $img['label'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.02]" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/30 to-transparent"></div>
                                    <span class="media-overlay-label absolute left-4 bottom-4 text-[10px]">{{ $img['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    @elseif($imgCount === 3)
                        <!-- 3 ảnh: 1 lớn bên trái, 2 nhỏ dọc bên phải -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 h-[320px] md:h-[480px]">
                            <button type="button" onclick="openZoomWithIndex(0)" class="relative md:col-span-2 w-full h-full rounded-xl overflow-hidden group text-left">
                                <img src="{{ $images[0]['url'] }}" alt="{{ $images[0]['label'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.015]" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 to-transparent"></div>
                                <span class="media-overlay-label absolute left-4 bottom-4">
                                    <i class="fa-solid fa-camera text-emerald-300 mr-1.5"></i><span>{{ $images[0]['label'] }}</span>
                                </span>
                            </button>
                            <div class="hidden md:grid grid-rows-2 gap-2 h-full">
                                @foreach(array_slice($images, 1, 2) as $subIdx => $img)
                                    <button type="button" onclick="openZoomWithIndex({{ $subIdx + 1 }})" class="relative w-full h-full rounded-xl overflow-hidden group text-left">
                                        <img src="{{ $img['url'] }}" alt="{{ $img['label'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.02]" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/30 to-transparent"></div>
                                        <span class="media-overlay-label absolute left-3 bottom-3 text-[10px]">{{ $img['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- 4 hoặc nhiều ảnh hơn: Bento Grid 5 ô (ảnh 1 to bên trái, 4 ảnh nhỏ bên phải) -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2.5 h-[340px] md:h-[480px]">
                            <!-- Ảnh chính -->
                            <button type="button" onclick="openZoomWithIndex(0)" class="relative md:col-span-2 w-full h-full rounded-xl overflow-hidden group text-left">
                                <img src="{{ $images[0]['url'] }}" alt="{{ $images[0]['label'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.015]" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 to-transparent"></div>
                                <span class="media-overlay-label absolute left-4 bottom-4">
                                    <i class="fa-solid fa-camera text-emerald-300 mr-1.5"></i><span>{{ $images[0]['label'] }}</span>
                                </span>
                            </button>
                            <!-- 4 ảnh phụ -->
                            <div class="hidden md:grid md:col-span-2 grid-cols-2 grid-rows-2 gap-2.5 h-full">
                                @for($i = 1; $i <= 4; $i++)
                                    @if(isset($images[$i]))
                                        <button type="button" onclick="openZoomWithIndex({{ $i }})" class="relative w-full h-full rounded-xl overflow-hidden group text-left">
                                            <img src="{{ $images[$i]['url'] }}" alt="{{ $images[$i]['label'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.02]" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/30 to-transparent"></div>
                                            <span class="media-overlay-label absolute left-3 bottom-3 text-[10px] truncate max-w-full block">{{ $images[$i]['label'] }}</span>
                                            @if($i === 4 && $imgCount > 5)
                                                <div class="absolute inset-0 bg-slate-950/65 flex flex-col items-center justify-center backdrop-blur-[2px] transition-colors group-hover:bg-slate-950/55">
                                                    <span class="text-white text-base font-extrabold">+{{ $imgCount - 5 }}</span>
                                                    <span class="text-[9px] text-emerald-300 font-bold tracking-wider uppercase mt-1">Góc khác</span>
                                                </div>
                                            @endif
                                        </button>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    @endif

                    <!-- Nút xem đầy đủ ở góc -->
                    <button type="button" onclick="openZoom()" class="absolute right-4 bottom-4 px-3.5 py-2 rounded-xl bg-slate-950/80 border border-white/10 text-emerald-355 hover:text-white flex items-center gap-2 text-xs font-bold backdrop-blur transition-all active:scale-95 shadow-md">
                        <i class="fa-solid fa-images text-emerald-400"></i>
                        <span>Xem tất cả {{ $imgCount }} ảnh</span>
                    </button>
                </div>

                <!-- Thư viện ảnh dạng danh sách Thumbnail trượt chọn -->
                <div class="flex gap-2.5 overflow-x-auto py-1.5 px-0.5 custom-scroll">
                    @foreach($images as $index => $image)
                        <button type="button" onclick="selectImage({{ $index }})" class="thumb-button shrink-0 relative w-24 h-16 rounded-xl overflow-hidden border {{ $index === 0 ? 'border-emerald-400' : 'border-slate-800' }} hover:border-emerald-500/70 bg-slate-950 transition-all">
                            <img src="{{ $image['url'] }}" alt="{{ $image['label'] }}" class="w-full h-full object-cover" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                            <span class="absolute inset-x-0 bottom-0 bg-slate-950/80 text-[8px] text-slate-300 py-0.5 px-1 truncate text-center">{{ $image['label'] }}</span>
                        </button>
                    @endforeach
                </div>

                <p class="rounded-2xl border border-slate-850 bg-slate-900/25 px-4 py-3.5 text-[11px] leading-relaxed text-slate-400">
                    <i class="fa-solid fa-circle-info text-emerald-400 mr-2 text-xs shrink-0"></i>{{ $room['media_source_note'] }}
                </p>

                <!-- Video / Virtual Tour Section -->
                @if($room['video_url'])
                    <section class="p-5 rounded-2xl bg-slate-900/20 border border-slate-800/80 shadow-[inset_0_1px_0_rgba(255,255,255,0.02)]">
                        <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-video text-rose-400"></i> Video / Virtual Tour thực tế
                        </h2>
                        <div class="relative rounded-xl overflow-hidden border border-slate-800 bg-black max-h-[420px] shadow-lg">
                            <video class="w-full h-full max-h-[420px]" src="{{ $room['video_url'] }}" controls preload="metadata"></video>
                        </div>
                    </section>
                @else
                    <section class="p-5 rounded-2xl bg-slate-900/10 border border-dashed border-slate-800/80 text-xs text-slate-400 flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-mobile-screen-button text-slate-500"></i>
                        </div>
                        <div class="space-y-1">
                            <strong class="text-slate-300 block font-bold">Chưa có video tour cho phòng này</strong>
                            <p class="leading-relaxed">Bạn nên yêu cầu chủ trọ gửi video quay cận cảnh (không cắt ghép) từ cửa vào phòng trước khi đặt cọc để kiểm tra chính xác hiện trạng.</p>
                        </div>
                    </section>
                @endif

                <!-- Mô tả chi tiết -->
                <section class="p-6 rounded-2xl bg-slate-900/20 border border-slate-800/80 shadow-[inset_0_1px_0_rgba(255,255,255,0.02)]">
                    <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-align-left text-emerald-400"></i> Mô tả không gian sống
                    </h2>
                    <p class="text-xs md:text-sm text-slate-350 leading-relaxed whitespace-pre-line">{{ $fullDescription }}</p>
                </section>

                <!-- Đánh giá thực tế từ người dùng -->
                <section class="p-6 rounded-2xl bg-slate-900/20 border border-slate-800/80 shadow-[inset_0_1px_0_rgba(255,255,255,0.02)]">
                    <div class="flex items-center justify-between gap-4 mb-5 pb-3 border-b border-slate-800/50">
                        <h2 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-comments text-amber-400"></i> Đánh giá khách quan
                        </h2>
                        <button type="button" onclick="loadReviewSummary(this)" class="px-3 py-2 rounded-xl bg-emerald-600/90 hover:bg-emerald-500 text-white text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1.5 shadow-md shadow-emerald-600/10 active:scale-95 transition-all">
                            <i class="fa-solid fa-wand-magic-sparkles text-[11px]"></i>
                            <span>AI Tóm tắt</span>
                        </button>
                    </div>

                    <!-- AI Summary Box container -->
                    <div id="review-summary-box" class="hidden mb-5 rounded-2xl bg-slate-950/60 border border-emerald-500/20 p-5 text-xs text-slate-300 shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>
                    </div>

                    <!-- Reviews list -->
                    <div class="space-y-4">
                        @forelse($room['reviews'] as $review)
                            @php
                                $firstLetter = mb_substr($review['author_name'] ?? 'U', 0, 1, 'UTF-8');
                                $bgColors = [
                                    'bg-emerald-500/10 text-emerald-355 border-emerald-500/20',
                                    'bg-cyan-500/10 text-cyan-355 border-cyan-500/20',
                                    'bg-teal-500/10 text-teal-355 border-teal-500/20',
                                    'bg-indigo-500/10 text-indigo-355 border-indigo-500/20'
                                ];
                                $colorIdx = (ord(strtolower($firstLetter)) % count($bgColors));
                                $avatarClass = $bgColors[$colorIdx];
                            @endphp
                            <article class="p-4 rounded-xl bg-slate-900/30 border border-slate-800/50 hover:border-slate-800 transition-all relative group shadow-[inset_0_1px_0_rgba(255,255,255,0.01)]">
                                <!-- Background Quote Decors -->
                                <i class="fa-solid fa-quote-right absolute right-4 top-4 text-slate-800/10 text-3xl pointer-events-none group-hover:text-slate-800/20 transition-colors"></i>
                                
                                <div class="flex justify-between items-start gap-3 text-xs relative z-10">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs border {{ $avatarClass }}">
                                            {{ mb_strtoupper($firstLetter, 'UTF-8') }}
                                        </div>
                                        <div>
                                            <strong class="text-slate-200 text-xs font-bold block">{{ $review['author_name'] }}</strong>
                                            <small class="text-[9px] text-slate-500 block mt-0.5">{{ $review['created_at'] }}</small>
                                        </div>
                                    </div>
                                    <span class="text-amber-400 text-[10px] bg-amber-500/5 px-2.5 py-1 rounded-lg border border-amber-500/10 font-bold shrink-0">
                                        {{ str_repeat('★', (int) $review['rating']) }}{{ str_repeat('☆', 5 - (int) $review['rating']) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 leading-relaxed mt-3 pl-11 relative z-10 select-all">{{ $review['comment'] }}</p>
                            </article>
                        @empty
                            <div class="py-6 text-center text-xs text-slate-500 italic rounded-xl bg-slate-900/10 border border-dashed border-slate-800/60">
                                <i class="fa-solid fa-comments text-slate-650 block text-lg mb-2"></i>
                                Chưa có đánh giá thực tế nào cho phòng này.
                            </div>
                        @endforelse
                    </div>

                    <!-- Gửi đánh giá form -->
                    <form action="{{ route('renty.room.review.store', $room['id']) }}" method="POST" class="mt-8 pt-6 border-t border-slate-800/60 space-y-4">
                        @csrf
                        <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-pen-to-square text-emerald-400"></i> Chia sẻ trải nghiệm trọ
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="text" name="author_name" required value="{{ Auth::user()->name ?? '' }}" placeholder="Tên hiển thị của bạn" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none placeholder-slate-600 transition-colors">
                            </div>
                            <div>
                                <select name="rating" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-colors">
                                    <option value="5">5 sao - Xuất sắc</option>
                                    <option value="4">4 sao - Tốt</option>
                                    <option value="3">3 sao - Trung bình</option>
                                    <option value="2">2 sao - Kém</option>
                                    <option value="1">1 sao - Rất kém</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <textarea name="comment" required rows="3.5" placeholder="Bạn thấy phòng như thế nào? An ninh có đảm bảo? Chủ trọ có nhiệt tình? Chi phí phát sinh có hợp lý không?..." class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none placeholder-slate-600 transition-colors resize-none"></textarea>
                        </div>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-extrabold uppercase tracking-wider text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-600/10 hover:shadow-emerald-600/20 active:scale-95 transition-all">
                            Gửi đánh giá
                        </button>
                    </form>
                </section>
            </div>

            <!-- Right Sidebar Columns (Sticky cards) -->
            <aside class="lg:sticky lg:top-24 space-y-5">
                <!-- Bento box: Thông số phòng -->
                <section class="p-6 rounded-2xl bg-slate-900/30 border border-slate-800/80 backdrop-blur-md relative overflow-hidden shadow-[inset_0_1px_0_rgba(255,255,255,0.03)]">
                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-emerald-500/30 to-transparent"></div>
                    
                    <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-cubes text-emerald-400"></i> Thông số phòng trọ
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Diện tích</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">{{ $room['area_text'] }}</strong>
                                <i class="fa-solid fa-ruler-combined text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>
                        
                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Khu vực</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200 truncate max-w-[80px]" title="{{ $room['area_name'] }}">{{ $room['area_name'] }}</strong>
                                <i class="fa-solid fa-map-pin text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Gác lửng</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">{{ $room['loft_txt'] }}</strong>
                                <i class="fa-solid fa-stairs text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Ban công</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">{{ $room['balcony_txt'] }}</strong>
                                <i class="fa-solid fa-door-open text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Thú cưng</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">{{ $room['pets_txt'] }}</strong>
                                <i class="fa-solid fa-paw text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Tiền cọc</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">1 tháng</strong>
                                <i class="fa-solid fa-wallet text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Warning box if available -->
                @if($room['price_warning'])
                    <section class="p-4.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-200 shadow-sm relative overflow-hidden">
                        <strong class="block text-amber-300 mb-1 font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
                            <span>{{ $room['price_warning']['label'] }}</span>
                        </strong>
                        <p class="leading-relaxed text-[11px] text-amber-305">{{ $room['price_warning']['message'] }}</p>
                    </section>
                @endif

                <!-- Glassmorphic Receipt Box: Chi phí dự kiến -->
                <section class="p-6 rounded-2xl bg-slate-900/30 border border-slate-800/80 backdrop-blur-md relative overflow-hidden shadow-[inset_0_1px_0_rgba(255,255,255,0.03)]">
                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-cyan-500/30 to-transparent"></div>
                    
                    <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice-dollar text-cyan-400"></i> Chi phí ước tính
                    </h2>
                    
                    <div class="space-y-3.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Tiền phòng</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">{{ number_format($room['price'], 0, ',', '.') }}đ</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Tiền cọc phòng</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">{{ number_format($room['price'], 0, ',', '.') }}đ</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Điện (ước lượng)</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">350.000đ</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Nước (2 người)</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">40.000đ</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Dịch vụ & Gửi xe</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">150.000đ</strong>
                        </div>
                        
                        <!-- Divider receipt -->
                        <div class="border-t border-dashed border-slate-800 pt-4 mt-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-300">Tổng ban đầu</span>
                                <strong class="text-lg font-black text-emerald-400 drop-shadow-[0_0_12px_rgba(52,211,153,0.15)]">{{ number_format($initialCost, 0, ',', '.') }}đ</strong>
                            </div>
                            <span class="block text-[9px] text-slate-500 mt-2 leading-relaxed text-right">
                                * Ước lượng 1 tháng thuê đầu tiên kèm cọc & các dịch vụ cơ bản.
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Liên hệ & Hẹn lịch xem trọ -->
                <section class="p-6 rounded-2xl bg-slate-900/30 border border-slate-800/80 backdrop-blur-md relative overflow-hidden shadow-[inset_0_1px_0_rgba(255,255,255,0.03)]">
                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-teal-500/30 to-transparent"></div>
                    
                    <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-phone-volume text-teal-400"></i> Liên hệ xem phòng
                    </h2>
                    
                    <div class="flex gap-2.5 mb-4">
                        <a href="tel:0987654321" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 hover:shadow-lg hover:shadow-emerald-600/15 text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all active:scale-[0.98]">
                            <i class="fa-solid fa-phone"></i>
                            <span>Gọi điện</span>
                        </a>
                        <a href="https://zalo.me/0987654321" target="_blank" class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 hover:shadow-lg hover:shadow-blue-600/15 text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all active:scale-[0.98]">
                            <i class="fa-solid fa-comments"></i>
                            <span>Nhắn Zalo</span>
                        </a>
                    </div>
                    
                    <form action="{{ route('renty.contact_request.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room['id'] }}">
                        <div>
                            <input type="text" name="name" required value="{{ Auth::user()->name ?? '' }}" placeholder="Họ tên của bạn" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none placeholder-slate-655 transition-colors">
                        </div>
                        <div>
                            <input type="tel" name="phone" required value="{{ Auth::user()->phone ?? '' }}" placeholder="Số điện thoại liên hệ" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none placeholder-slate-655 transition-colors">
                        </div>
                        <div>
                            <textarea name="message" rows="2.5" placeholder="Ghi chú thời gian muốn hẹn xem phòng..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none placeholder-slate-655 transition-colors resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white text-xs font-extrabold uppercase tracking-wider shadow-lg shadow-emerald-600/10 hover:shadow-emerald-600/20 transition-all active:scale-[0.98] mt-1.5">
                            Gửi yêu cầu tư vấn
                        </button>
                    </form>
                </section>
            </aside>
        </section>
    </main>

    <!-- Overlay Zoom Modal (Full screen image browser) -->
    <div id="zoom-modal" class="fixed inset-0 z-50 hidden bg-[#02040a]/95 backdrop-blur-md p-4 items-center justify-center">
        <button type="button" onclick="closeZoom()" class="absolute top-5 right-5 w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 hover:text-white flex items-center justify-center transition-all shadow-lg active:scale-95"><i class="fa-solid fa-xmark"></i></button>
        <button type="button" onclick="changeZoom(-1)" class="absolute left-4 md:left-8 w-12 h-12 rounded-full bg-slate-900/80 border border-slate-800 text-slate-200 hover:text-white flex items-center justify-center transition-all shadow-md active:scale-90"><i class="fa-solid fa-chevron-left"></i></button>
        <img id="zoom-image" src="" alt="Ảnh phòng phóng to" class="max-w-full max-h-[82vh] object-contain rounded-2xl border border-slate-800 shadow-2xl">
        <button type="button" onclick="changeZoom(1)" class="absolute right-4 md:right-8 w-12 h-12 rounded-full bg-slate-900/80 border border-slate-800 text-slate-200 hover:text-white flex items-center justify-center transition-all shadow-md active:scale-90"><i class="fa-solid fa-chevron-right"></i></button>
        <div class="absolute left-1/2 -translate-x-1/2 bottom-5 px-4.5 py-2.5 rounded-xl bg-slate-950/85 border border-white/10 text-xs font-bold text-slate-200 flex items-center gap-2 shadow-lg backdrop-blur">
            <span id="zoom-label">{{ $images[0]['label'] ?? 'View toàn phòng' }}</span>
            <span class="text-slate-650 font-normal">|</span>
            <span id="zoom-count" class="text-emerald-400 tabular-nums">1/{{ count($images) }}</span>
        </div>
    </div>

    <!-- Overlay Report Modal -->
    <div id="room-report-modal" class="fixed inset-0 z-[60] hidden bg-[#02040a]/80 backdrop-blur-md p-4 items-center justify-center">
        <div class="w-full max-w-lg rounded-3xl bg-[#0a0f1d] border border-slate-800 p-5 md:p-6 shadow-2xl animate-fade-in relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-rose-500/30 to-transparent"></div>
            
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-350 text-[10px] font-extrabold uppercase tracking-widest shadow-sm">
                        <i class="fa-solid fa-shield-halved text-[9px]"></i> Báo cáo phòng
                    </span>
                    <h2 class="mt-3 text-xl font-extrabold text-slate-100">Báo cáo tin đăng đáng ngờ</h2>
                    <p class="mt-1 text-xs text-slate-500 truncate max-w-[320px]">{{ $room['title'] }}</p>
                </div>
                <button type="button" onclick="closeReportModal()" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-slate-355 hover:text-white flex items-center justify-center transition-all active:scale-95 shadow-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('renty.room.report.store', $room['id']) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" name="reporter_name" value="{{ Auth::user()->name ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none placeholder-slate-600 transition-colors" placeholder="Họ tên của bạn">
                    <input type="tel" name="reporter_phone" value="{{ Auth::user()->phone ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none placeholder-slate-600 transition-colors" placeholder="Số điện thoại của bạn">
                </div>
                <select name="reason" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none transition-colors">
                    <option value="scam">Nghi ngờ lừa đảo / yêu cầu cọc bất thường</option>
                    <option value="fake_images">Ảnh chụp không đúng thực tế phòng</option>
                    <option value="wrong_price">Giá hoặc phí phát sinh sai khác nhiều</option>
                    <option value="unsafe">Vấn đề an toàn / an ninh khu trọ</option>
                    <option value="spam">Tin đăng spam / trùng lặp / hết phòng từ lâu</option>
                    <option value="other">Lý do khác</option>
                </select>
                <textarea name="description" required minlength="10" rows="4" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none placeholder-slate-655 transition-colors resize-none" placeholder="Vui lòng mô tả chi tiết điều bạn thấy bất thường để quản trị viên xác minh..."></textarea>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeReportModal()" class="px-4.5 py-2.5 rounded-xl border border-slate-800 bg-slate-900 hover:bg-slate-850 hover:text-slate-100 text-slate-355 text-xs font-bold transition-all active:scale-95">Hủy</button>
                    <button type="submit" class="px-5.5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold uppercase tracking-wider transition-all active:scale-95 shadow-lg shadow-rose-600/10 hover:shadow-rose-600/20">
                        Gửi báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const roomImages = @json($images);
        let activeImageIndex = 0;

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

        function selectImage(index) {
            activeImageIndex = Math.max(0, Math.min(roomImages.length - 1, index));
            const image = roomImages[activeImageIndex];
            const mainImg = document.getElementById('main-image');
            if (mainImg) {
                mainImg.src = image.url;
                mainImg.alt = image.label;
            }
            const labelEl = document.getElementById('main-image-label');
            if (labelEl) {
                labelEl.textContent = image.label;
            }
            document.querySelectorAll('.thumb-button').forEach((button, buttonIndex) => {
                button.classList.toggle('border-emerald-400', buttonIndex === activeImageIndex);
                button.classList.toggle('border-slate-800', buttonIndex !== activeImageIndex);
            });
        }

        function openZoom() {
            document.getElementById('zoom-modal').classList.remove('hidden');
            document.getElementById('zoom-modal').classList.add('flex');
            renderZoom();
        }

        function openZoomWithIndex(index) {
            activeImageIndex = Math.max(0, Math.min(roomImages.length - 1, index));
            selectImage(activeImageIndex);
            openZoom();
        }

        function renderZoom() {
            const image = roomImages[activeImageIndex];
            document.getElementById('zoom-image').src = image.url;
            document.getElementById('zoom-label').textContent = image.label;
            document.getElementById('zoom-count').textContent = `${activeImageIndex + 1}/${roomImages.length}`;
        }

        function changeZoom(delta) {
            activeImageIndex = (activeImageIndex + delta + roomImages.length) % roomImages.length;
            selectImage(activeImageIndex);
            renderZoom();
        }

        function closeZoom() {
            document.getElementById('zoom-modal').classList.add('hidden');
            document.getElementById('zoom-modal').classList.remove('flex');
        }

        function openReportModal() {
            document.getElementById('room-report-modal').classList.remove('hidden');
            document.getElementById('room-report-modal').classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeReportModal() {
            document.getElementById('room-report-modal').classList.add('hidden');
            document.getElementById('room-report-modal').classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function loadReviewSummary(button) {
            const box = document.getElementById('review-summary-box');
            const original = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Đang tóm tắt...';
            box.classList.remove('hidden');
            box.innerHTML = `
                <div class="flex items-center gap-2 text-slate-400">
                    <i class="fa-solid fa-circle-notch animate-spin text-emerald-400 text-xs"></i>
                    <span>AI đang phân tích các đánh giá thực tế...</span>
                </div>
            `;

            fetch('/api/renty/rooms/{{ $room['id'] }}/reviews/summary')
                .then(response => response.json())
                .then(data => {
                    button.disabled = false;
                    button.innerHTML = original;
                    if (!data.success) {
                        box.innerHTML = '<span class="text-rose-450"><i class="fa-solid fa-circle-exclamation mr-1"></i> Không thể tóm tắt lúc này.</span>';
                        return;
                    }
                    const summary = data.summary || {};
                    const pros = (summary.pros || []).map(item => `
                        <li class="flex items-start gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-emerald-400 mt-1 shrink-0 text-[10px]"></i>
                            <span>${escapeHtml(item)}</span>
                        </li>
                    `).join('');
                    const cons = (summary.cons || []).map(item => `
                        <li class="flex items-start gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-exclamation text-amber-400 mt-1 shrink-0 text-[10px]"></i>
                            <span>${escapeHtml(item)}</span>
                        </li>
                    `).join('');
                    
                    box.innerHTML = `
                        <div class="space-y-4">
                            <div class="font-bold text-slate-200 text-xs border-b border-slate-800 pb-2 flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-emerald-400"></i>
                                <span>AI PHÂN TÍCH REVIEW</span>
                            </div>
                            <p class="text-slate-350 leading-relaxed text-xs">${escapeHtml(summary.summary || '')}</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-900/60">
                                ${pros ? `
                                    <div>
                                        <div class="text-emerald-400 font-extrabold text-[10px] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <i class="fa-solid fa-thumbs-up"></i> Ưu điểm
                                        </div>
                                        <ul class="space-y-2 text-[11px]">${pros}</ul>
                                    </div>
                                ` : ''}
                                ${cons ? `
                                    <div>
                                        <div class="text-amber-400 font-extrabold text-[10px] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Cần lưu ý
                                        </div>
                                        <ul class="space-y-2 text-[11px]">${cons}</ul>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                })
                .catch(() => {
                    button.disabled = false;
                    button.innerHTML = original;
                    box.innerHTML = '<span class="text-rose-450"><i class="fa-solid fa-circle-exclamation mr-1"></i> Lỗi kết nối máy chủ AI. Vui lòng thử lại.</span>';
                });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        (function saveViewedRoom() {
            try {
                const roomId = String({{ $room['id'] }});
                const viewedIds = JSON.parse(localStorage.getItem('renty_viewed_rooms') || '[]').filter(id => id !== roomId);
                viewedIds.unshift(roomId);
                localStorage.setItem('renty_viewed_rooms', JSON.stringify(viewedIds.slice(0, 6)));
            } catch (error) {
                // Local storage is optional
            }
        })();

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeZoom();
                closeReportModal();
            }
            if (!document.getElementById('zoom-modal').classList.contains('hidden')) {
                if (event.key === 'ArrowLeft') changeZoom(-1);
                if (event.key === 'ArrowRight') changeZoom(1);
            }
        });
    </script>
</body>
</html>
