<!-- Decorative search backdrop -->
<div id="renty-search-backdrop" class="renty-search-backdrop" onclick="blurRentySearch()"></div>

<!-- NAVBAR -->
<header class="h-20 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md sticky top-0 z-40 flex items-center">
    <div class="container mx-auto px-4 md:px-6 flex justify-between items-center gap-2 md:gap-4">
        <!-- Left: Logo and Nav Links -->
        <div class="flex items-center gap-3 md:gap-6 shrink-0">
            <a href="{{ route('smartroom.portal') }}" class="flex items-center gap-2 md:gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fa-solid fa-magnifying-glass-location text-white text-lg"></i>
                </div>
                <span class="renty-brand-text text-xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Renty</span>
            </a>

            <nav class="flex items-center gap-3 md:gap-5 text-xs font-semibold text-slate-400">
                <a href="#" class="hover:text-emerald-400 transition-colors">Khám phá</a>
                <a href="javascript:void(0)" onclick="openHotAreasModal()" class="hover:text-emerald-400 transition-colors">Khu vực</a>
                <a href="javascript:void(0)" onclick="setViewMode('map')" class="hover:text-emerald-400 transition-colors flex items-center gap-1.5">
                    Bản đồ
                    <span class="px-1.5 py-0.5 text-[8px] font-black bg-emerald-500 text-white rounded-md uppercase tracking-wider animate-pulse">🆕</span>
                </a>
            </nav>
        </div>
        
        <!-- Middle: Search Bar (Glassmorphism Renty search panel) -->
        <div id="renty-search-panel" class="renty-search-panel flex-grow max-w-md mx-4 relative block">
            <div class="relative w-full renty-search-shell">
                <div class="renty-search-focus-ring"></div>
                <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 renty-search-icon"></i>
                <input type="text" id="search-input" onkeyup="handleSearchInput(event)" onfocus="openRentySearchSuggestions()" class="renty-search-input w-full pl-11 pr-4 py-2.5 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none text-xs font-semibold" placeholder="Tìm kiếm trọ, khu vực, tiện ích...">
                
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
