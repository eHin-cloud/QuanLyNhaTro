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
                <a href="{{ route('renty.user') }}" onclick="handleExploreClick(event)" class="hover:text-emerald-400 transition-colors">Khám phá</a>
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
                <input type="text" id="search-input" onkeyup="handleSearchInput(event)" onfocus="openRentySearchSuggestions()" class="renty-search-input w-full pl-11 pr-10 py-2.5 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none text-xs font-semibold" placeholder="Tìm kiếm trọ, khu vực, tiện ích...">
                <button type="button" onclick="triggerRentySearch()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-400 transition-colors w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-800/40" title="Tìm kiếm" aria-label="Tìm kiếm">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
                
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
                    <!-- Notification Bell -->
                    <div class="relative" id="renty-notification-wrapper">
                        <button type="button" id="renty-notification-bell" onclick="toggleNotificationDropdown(event)" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 flex items-center justify-center relative transition-all duration-200" title="Thông báo">
                            <i class="fa-solid fa-bell text-sm"></i>
                            <span id="renty-notification-badge" class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-rose-500 rounded-full border border-slate-900 hidden animate-pulse"></span>
                        </button>
                        
                        <!-- Dropdown Tray -->
                        <div id="renty-notification-dropdown" class="absolute right-0 mt-3 w-80 bg-slate-950/95 border border-slate-800/90 rounded-2xl shadow-2xl p-4 hidden z-50 backdrop-blur-xl transition-all">
                            <div class="flex items-center justify-between border-b border-slate-800/60 pb-2 mb-3">
                                <h4 class="text-xs font-extrabold text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-bell text-emerald-450"></i>
                                    Thông báo
                                </h4>
                                <button type="button" onclick="markAllNotificationsRead(event)" class="text-[10px] text-slate-400 hover:text-emerald-450 font-bold transition-colors">
                                    Đánh dấu đã đọc
                                </button>
                            </div>
                            
                            <!-- Notification list -->
                            <div id="renty-notification-list" class="max-h-64 overflow-y-auto space-y-2.5 pr-1 text-left scrollbar-thin">
                                <div class="text-center py-6 text-slate-500 text-xs font-semibold">
                                    <i class="fa-solid fa-spinner animate-spin mr-1.5"></i> Đang tải thông báo...
                                </div>
                            </div>
                        </div>
                    </div>

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
                        <a href="{{ route('signout') }}" class="font-semibold text-rose-455 hover:text-rose-400 transition-colors">
                            Đăng xuất
                        </a>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-355 hover:text-slate-100 transition-all font-bold flex items-center gap-1.5">
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

<script>
let rentyNotifications = [];

function toggleNotificationDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('renty-notification-dropdown');
    if (!dropdown) return;
    
    const isHidden = dropdown.classList.contains('hidden');
    
    if (isHidden) {
        dropdown.classList.remove('hidden');
        fetchNotifications();
    } else {
        dropdown.classList.add('hidden');
    }
}

function fetchNotifications() {
    const listContainer = document.getElementById('renty-notification-list');
    if (!listContainer) return;
    
    fetch('{{ route("renty.notifications") }}')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.notifications) {
                rentyNotifications = data.notifications;
                renderNotifications(rentyNotifications);
                
                // Show badge if there are unread items
                const badge = document.getElementById('renty-notification-badge');
                const lastReadId = localStorage.getItem('renty_last_read_notification');
                const hasUnread = rentyNotifications.length > 0 && 
                                  (!lastReadId || rentyNotifications[0].id !== lastReadId);
                                  
                if (badge) {
                    if (hasUnread) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            }
        })
        .catch(err => {
            console.error('Error fetching notifications:', err);
            listContainer.innerHTML = '<div class="text-center py-4 text-rose-450 text-xs">Không thể tải thông báo.</div>';
        });
}

function renderNotifications(items) {
    const listContainer = document.getElementById('renty-notification-list');
    if (!listContainer) return;
    
    if (items.length === 0) {
        listContainer.innerHTML = '<div class="text-center py-6 text-slate-500 text-xs">Không có thông báo nào.</div>';
        return;
    }
    
    listContainer.innerHTML = items.map(item => `
        <a href="${item.link}" class="block p-2.5 rounded-xl hover:bg-slate-900/60 border border-transparent hover:border-slate-800/80 transition-all duration-200 group">
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center shrink-0 ${item.color}">
                    <i class="fa-solid ${item.icon} text-xs"></i>
                </div>
                <div class="flex-grow min-w-0">
                    <h5 class="text-[11px] font-extrabold text-slate-200 line-clamp-1 group-hover:text-emerald-450 transition-colors">${item.title}</h5>
                    <p class="text-[10px] text-slate-400 mt-0.5 leading-relaxed break-words">${item.message}</p>
                    <span class="block text-[8px] text-slate-500 font-medium mt-1">${item.time}</span>
                </div>
            </div>
        </a>
    `).join('');
}

function markAllNotificationsRead(event) {
    if (event) event.stopPropagation();
    const badge = document.getElementById('renty-notification-badge');
    if (badge) badge.classList.add('hidden');
    
    if (rentyNotifications.length > 0) {
        localStorage.setItem('renty_last_read_notification', rentyNotifications[0].id);
    }
}

// Close dropdown on click outside
document.addEventListener('click', (e) => {
    const wrapper = document.getElementById('renty-notification-wrapper');
    const dropdown = document.getElementById('renty-notification-dropdown');
    if (dropdown && wrapper && !wrapper.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

// Pre-fetch on load to check for badge
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('renty-notification-bell')) {
        fetchNotifications();
    }
});
</script>


<script>
function handleExploreClick(event) {
    localStorage.setItem('rentry_view_mode', 'grid');
    const path = window.location.pathname.replace(/\/$/, '');
    if (path === '/renty') {
        event.preventDefault();
        window.location.href = '/renty';
    }
}
</script>
