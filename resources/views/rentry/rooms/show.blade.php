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
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
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
        <div class="mb-6">
            <a href="{{ route('renty.user') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-slate-100 text-xs font-bold">
                <i class="fa-solid fa-arrow-left"></i> Danh sách phòng
            </a>
        </div>

        <section class="mb-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="px-3 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-extrabold text-emerald-300 uppercase tracking-wider">{{ $room['media_source_label'] }}</span>
                        <span class="px-3 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[10px] font-extrabold text-slate-300 uppercase tracking-wider">Phòng {{ $room['room_number'] }}</span>
                    </div>
                    <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight text-slate-100">{{ $room['title'] }}</h1>
                    <p class="mt-3 text-sm text-slate-500 flex items-start gap-2">
                        <i class="fa-solid fa-location-dot text-slate-600 mt-1"></i>
                        <span>{{ $room['address'] }}</span>
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-extrabold text-emerald-400">{{ number_format($room['price'], 0, ',', '.') }}đ</div>
                    <div class="text-[11px] font-bold text-slate-500 mt-1">/ tháng · {{ $room['rating'] }} ⭐ · {{ $reviewCount }} đánh giá</div>
                    <button type="button" onclick="openReportModal()" class="mt-3 inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 hover:text-rose-200 text-xs font-extrabold">
                        <i class="fa-solid fa-flag"></i> Báo cáo tin đăng
                    </button>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 items-start">
            <div class="space-y-6">
                <div class="rounded-2xl overflow-hidden border border-slate-800 bg-slate-950">
                    <button type="button" onclick="openZoom()" class="relative block w-full h-[360px] md:h-[520px] group text-left">
                        <img id="main-image" src="{{ $images[0]['url'] ?? $room['cover_image'] }}" alt="{{ $images[0]['label'] ?? 'Ảnh phòng' }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.02]" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                        <span class="media-overlay-label absolute left-4 bottom-4">
                            <i class="fa-solid fa-camera text-emerald-300 mr-1.5"></i><span id="main-image-label">{{ $images[0]['label'] ?? 'View toàn phòng' }}</span>
                        </span>
                        <span class="absolute right-4 bottom-4 w-10 h-10 rounded-xl bg-slate-950/75 border border-white/10 text-emerald-300 flex items-center justify-center backdrop-blur">
                            <i class="fa-solid fa-expand"></i>
                        </span>
                    </button>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($images as $index => $image)
                        <button type="button" onclick="selectImage({{ $index }})" class="thumb-button relative h-24 rounded-xl overflow-hidden border {{ $index === 0 ? 'border-emerald-400' : 'border-slate-800' }} hover:border-emerald-500/70 bg-slate-950">
                            <img src="{{ $image['url'] }}" alt="{{ $image['label'] }}" class="w-full h-full object-cover" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                            <span class="media-overlay-label media-overlay-label-small absolute left-1.5 right-1.5 bottom-1.5 truncate">{{ $image['label'] }}</span>
                        </button>
                    @endforeach
                </div>

                <p class="rounded-2xl border border-slate-800 bg-slate-900/40 px-4 py-3 text-xs leading-relaxed text-slate-400">
                    <i class="fa-solid fa-circle-info text-emerald-400 mr-1.5"></i>{{ $room['media_source_note'] }}
                </p>

                @if($room['video_url'])
                    <section class="p-4 rounded-2xl bg-[#070b13] border border-slate-800/70">
                        <h2 class="text-sm font-extrabold text-slate-100 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-video text-rose-300"></i> Video / Virtual Tour
                        </h2>
                        <video class="w-full max-h-[420px] rounded-xl border border-slate-800 bg-black" src="{{ $room['video_url'] }}" controls preload="metadata"></video>
                    </section>
                @else
                    <section class="p-4 rounded-2xl bg-slate-900/35 border border-dashed border-slate-800 text-xs text-slate-400 flex items-start gap-3">
                        <i class="fa-solid fa-mobile-screen-button text-slate-500 mt-0.5"></i>
                        <span>Chưa có video tour cho phòng này. Nên yêu cầu chủ trọ gửi video quay từ cửa vào phòng trước khi đặt cọc.</span>
                    </section>
                @endif

                <section class="p-5 rounded-2xl bg-slate-900/45 border border-slate-800/60">
                    <h2 class="text-sm font-extrabold text-slate-100 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-align-left text-emerald-400"></i> Mô tả chi tiết
                    </h2>
                    <p class="text-sm text-slate-300 leading-relaxed">{{ $fullDescription }}</p>
                </section>

                <section class="p-5 rounded-2xl bg-[#070b13] border border-slate-800/70">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h2 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-comments text-amber-400"></i> Đánh giá thực tế
                        </h2>
                        <button type="button" onclick="loadReviewSummary(this)" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> AI tóm tắt
                        </button>
                    </div>
                    <div id="review-summary-box" class="hidden mb-4 rounded-xl bg-slate-900/70 border border-slate-800 p-3 text-xs text-slate-300"></div>
                    <div class="space-y-3">
                        @forelse($room['reviews'] as $review)
                            <article class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/50">
                                <div class="flex justify-between items-center gap-3 text-xs">
                                    <strong class="text-slate-200">{{ $review['author_name'] }}</strong>
                                    <span class="text-amber-400 font-bold">{{ str_repeat('★', (int) $review['rating']) }}{{ str_repeat('☆', 5 - (int) $review['rating']) }}</span>
                                </div>
                                <p class="text-xs text-slate-400 leading-relaxed mt-2">{{ $review['comment'] }}</p>
                                <small class="block text-[9px] text-slate-600 mt-2">{{ $review['created_at'] }}</small>
                            </article>
                        @empty
                            <div class="py-4 text-center text-xs text-slate-500 italic">Chưa có đánh giá thực tế nào cho phòng này.</div>
                        @endforelse
                    </div>

                    <form action="{{ route('renty.room.review.store', $room['id']) }}" method="POST" class="mt-6 pt-6 border-t border-slate-800/60 space-y-4">
                        @csrf
                        <h3 class="text-sm font-bold text-slate-300">Gửi đánh giá của bạn</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="author_name" required value="{{ Auth::user()->name ?? '' }}" placeholder="Tên của bạn" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none">
                            <select name="rating" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none">
                                <option value="5">5 sao - Xuất sắc</option>
                                <option value="4">4 sao - Tốt</option>
                                <option value="3">3 sao - Trung bình</option>
                                <option value="2">2 sao - Kém</option>
                                <option value="1">1 sao - Rất kém</option>
                            </select>
                        </div>
                        <textarea name="comment" required rows="3" placeholder="Chia sẻ trải nghiệm thực tế về phòng, chủ nhà, an ninh..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none"></textarea>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-600/20">
                            Gửi đánh giá
                        </button>
                    </form>
                </section>
            </div>

            <aside class="lg:sticky lg:top-24 space-y-4">
                <section class="p-5 rounded-2xl bg-[#070b13] border border-slate-800/70">
                    <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-table-cells-large text-emerald-400"></i> Thông tin phòng
                    </h2>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="rounded-xl bg-slate-900/60 border border-slate-800 p-3"><small class="block text-slate-500 mb-1">Diện tích</small><strong>{{ $room['area_text'] }}</strong></div>
                        <div class="rounded-xl bg-slate-900/60 border border-slate-800 p-3"><small class="block text-slate-500 mb-1">Khu vực</small><strong>{{ $room['area_name'] }}</strong></div>
                        <div class="rounded-xl bg-slate-900/60 border border-slate-800 p-3"><small class="block text-slate-500 mb-1">Gác lửng</small><strong>{{ $room['loft_txt'] }}</strong></div>
                        <div class="rounded-xl bg-slate-900/60 border border-slate-800 p-3"><small class="block text-slate-500 mb-1">Ban công</small><strong>{{ $room['balcony_txt'] }}</strong></div>
                        <div class="rounded-xl bg-slate-900/60 border border-slate-800 p-3"><small class="block text-slate-500 mb-1">Thú cưng</small><strong>{{ $room['pets_txt'] }}</strong></div>
                        <div class="rounded-xl bg-slate-900/60 border border-slate-800 p-3"><small class="block text-slate-500 mb-1">Cọc</small><strong>1 tháng</strong></div>
                    </div>
                </section>

                @if($room['price_warning'])
                    <section class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-100">
                        <strong class="block text-amber-200 mb-1"><i class="fa-solid fa-triangle-exclamation mr-1.5"></i>{{ $room['price_warning']['label'] }}</strong>
                        <span>{{ $room['price_warning']['message'] }}</span>
                    </section>
                @endif

                <section class="p-5 rounded-2xl bg-[#070b13] border border-slate-800/70">
                    <h2 class="text-sm font-extrabold text-slate-100 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-cyan-300"></i> Chi phí dự kiến
                    </h2>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between"><span class="text-slate-500">Tiền phòng</span><strong>{{ number_format($room['price'], 0, ',', '.') }}đ</strong></div>
                        <div class="flex justify-between"><span class="text-slate-500">Cọc</span><strong>{{ number_format($room['price'], 0, ',', '.') }}đ</strong></div>
                        <div class="flex justify-between"><span class="text-slate-500">Điện dự kiến</span><strong>350.000đ</strong></div>
                        <div class="flex justify-between"><span class="text-slate-500">Nước 2 người</span><strong>40.000đ</strong></div>
                        <div class="flex justify-between"><span class="text-slate-500">Dịch vụ / xe</span><strong>150.000đ</strong></div>
                        <div class="flex justify-between border-t border-slate-800 pt-3 mt-3 text-emerald-300"><span>Tổng ban đầu</span><strong>{{ number_format($initialCost, 0, ',', '.') }}đ</strong></div>
                    </div>
                </section>

                <section class="p-5 rounded-2xl bg-[#070b13] border border-slate-800/70">
                    <h2 class="text-sm font-extrabold text-slate-100 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-phone-volume text-emerald-400"></i> Liên hệ xem phòng
                    </h2>
                    <div class="flex gap-2 mb-4">
                        <a href="tel:0987654321" class="flex-1 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold text-center"><i class="fa-solid fa-phone"></i> Gọi</a>
                        <a href="https://zalo.me/0987654321" target="_blank" class="flex-1 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold text-center"><i class="fa-solid fa-comments"></i> Zalo</a>
                    </div>
                    <form action="{{ route('renty.contact_request.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room['id'] }}">
                        <input type="text" name="name" required value="{{ Auth::user()->name ?? '' }}" placeholder="Họ tên" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none">
                        <input type="tel" name="phone" required value="{{ Auth::user()->phone ?? '' }}" placeholder="Số điện thoại" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none">
                        <textarea name="message" rows="2" placeholder="Thời gian muốn xem phòng..." class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none"></textarea>
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold">
                            Gửi yêu cầu tư vấn
                        </button>
                    </form>
                </section>
            </aside>
        </section>
    </main>

    <div id="zoom-modal" class="fixed inset-0 z-50 hidden bg-[#02040a]/95 backdrop-blur-md p-4 items-center justify-center">
        <button type="button" onclick="closeZoom()" class="absolute top-5 right-5 w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 text-slate-200"><i class="fa-solid fa-xmark"></i></button>
        <button type="button" onclick="changeZoom(-1)" class="absolute left-4 md:left-8 w-11 h-11 rounded-full bg-slate-900/80 border border-slate-800 text-slate-200"><i class="fa-solid fa-chevron-left"></i></button>
        <img id="zoom-image" src="" alt="Ảnh phòng phóng to" class="max-w-full max-h-[84vh] object-contain rounded-2xl border border-slate-800">
        <button type="button" onclick="changeZoom(1)" class="absolute right-4 md:right-8 w-11 h-11 rounded-full bg-slate-900/80 border border-slate-800 text-slate-200"><i class="fa-solid fa-chevron-right"></i></button>
        <div class="absolute left-1/2 -translate-x-1/2 bottom-5 px-4 py-2 rounded-xl bg-slate-950/80 border border-white/10 text-xs font-bold text-slate-200">
            <span id="zoom-label">{{ $images[0]['label'] ?? 'View toàn phòng' }}</span>
            <span class="text-slate-500 mx-2">·</span>
            <span id="zoom-count">1/{{ count($images) }}</span>
        </div>
    </div>

    <div id="room-report-modal" class="fixed inset-0 z-[60] hidden bg-[#02040a]/80 backdrop-blur-md p-4 items-center justify-center">
        <div class="w-full max-w-lg rounded-3xl bg-[#0a0f1d] border border-slate-800 p-5 md:p-6 shadow-2xl animate-fade-in">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[10px] font-extrabold uppercase tracking-wider">
                        <i class="fa-solid fa-shield-halved"></i> Báo cáo phòng
                    </span>
                    <h2 class="mt-3 text-xl font-extrabold text-slate-100">Báo cáo tin đăng đáng ngờ</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ $room['title'] }}</p>
                </div>
                <button type="button" onclick="closeReportModal()" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('renty.room.report.store', $room['id']) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" name="reporter_name" value="{{ Auth::user()->name ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none" placeholder="Tên của bạn">
                    <input type="tel" name="reporter_phone" value="{{ Auth::user()->phone ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none" placeholder="Số điện thoại">
                </div>
                <select name="reason" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none">
                    <option value="scam">Nghi lừa đảo / yêu cầu cọc bất thường</option>
                    <option value="fake_images">Ảnh không đúng thực tế</option>
                    <option value="wrong_price">Giá hoặc phí phát sinh sai</option>
                    <option value="unsafe">Vấn đề an toàn / an ninh</option>
                    <option value="spam">Tin đăng spam / trùng lặp</option>
                    <option value="other">Khác</option>
                </select>
                <textarea name="description" required minlength="10" rows="4" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none" placeholder="Mô tả điều bạn thấy đáng ngờ..."></textarea>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 rounded-xl border border-slate-800 bg-slate-900 text-slate-300 text-xs font-bold">Hủy</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-extrabold">
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
            document.getElementById('main-image').src = image.url;
            document.getElementById('main-image-label').textContent = image.label;
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
            button.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang tóm tắt...';
            box.classList.remove('hidden');
            box.textContent = 'AI đang đọc các review...';

            fetch('/api/renty/rooms/{{ $room['id'] }}/reviews/summary')
                .then(response => response.json())
                .then(data => {
                    button.disabled = false;
                    button.innerHTML = original;
                    if (!data.success) {
                        box.textContent = 'Không thể tóm tắt review.';
                        return;
                    }
                    const summary = data.summary || {};
                    const pros = (summary.pros || []).map(item => `<li>${escapeHtml(item)}</li>`).join('');
                    const cons = (summary.cons || []).map(item => `<li>${escapeHtml(item)}</li>`).join('');
                    box.innerHTML = `
                        <div class="font-bold text-slate-200">${escapeHtml(summary.summary || '')}</div>
                        ${pros ? `<div class="mt-2 text-emerald-300 font-bold">Ưu điểm</div><ul class="list-disc pl-5">${pros}</ul>` : ''}
                        ${cons ? `<div class="mt-2 text-amber-300 font-bold">Cần lưu ý</div><ul class="list-disc pl-5">${cons}</ul>` : ''}
                    `;
                })
                .catch(() => {
                    button.disabled = false;
                    button.innerHTML = original;
                    box.textContent = 'Không thể kết nối AI để tóm tắt review.';
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
                // Local storage is optional for this page.
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
