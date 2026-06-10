@php
    $currentTab = request()->query('tab', 'dashboard-section');
    $isDashboardRoute = request()->routeIs('smartroom.admin');
    $isLandlord = Auth::user()?->isLandlord();
    $sidebarContactCount = \App\Models\ContactRequest::where('status', 'pending')->count();
    $userInitials = '';
    $userName = '';
    $userRoleLabel = 'Quản trị viên';
    
    if (Auth::check()) {
        $userName = Auth::user()->name;
        $userRoleLabel = $isLandlord ? 'Chủ chung cư mini' : 'Nhân viên vận hành';
        $words = explode(' ', trim($userName));
        if (count($words) >= 2) {
            $userInitials = mb_substr($words[count($words) - 2], 0, 1) . mb_substr($words[count($words) - 1], 0, 1);
        } else {
            $userInitials = mb_substr($userName, 0, 2);
        }
        $userInitials = mb_strtoupper($userInitials);
    }
@endphp

<!-- SIDEBAR -->
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#0d121f] border-r border-slate-900 flex flex-col justify-between h-screen z-30 transition-[width] duration-200 shrink-0">
    <div>
        <!-- Sidebar Header -->
        <div class="p-6 border-b border-slate-900 flex items-center justify-between">
            <a href="{{ route('smartroom.portal') }}" class="sidebar-brand flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-hotel text-white text-sm"></i>
                </div>
                <span class="text-lg font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">SmartRoom</span>
            </a>
            <button type="button" id="sidebar-toggle" class="w-8 h-8 rounded-lg border border-slate-800 text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 transition-all" title="Thu gọn/mở rộng sidebar">
                <i class="fa-solid fa-angles-left transition-transform"></i>
            </button>
        </div>
        
        <!-- Navigation Links -->
        <nav class="p-4 space-y-1">
            <a href="{{ route('smartroom.admin') }}?tab=dashboard-section" 
               data-section="dashboard-section" 
               class="sidebar-nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ ($isDashboardRoute && $currentTab === 'dashboard-section') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-chart-pie text-lg"></i>
                <span>Tổng Quan</span>
            </a>
            
            <a href="{{ route('smartroom.admin') }}?tab=profile-section" 
               data-section="profile-section" 
               class="sidebar-nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ ($isDashboardRoute && $currentTab === 'profile-section') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-address-card text-lg"></i>
                <span>Hồ Sơ</span>
            </a>
            
            <a href="{{ route('smartroom.admin') }}?tab=room-map-section" 
               data-section="room-map-section" 
               class="sidebar-nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ ($isDashboardRoute && $currentTab === 'room-map-section') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-cubes text-lg"></i>
                <span>Sơ Đồ Phòng</span>
            </a>
            
            <a href="{{ route('admin.rooms.index') }}" 
               class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.rooms.*') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-door-open text-lg"></i>
                <span>Cấu hình phòng</span>
            </a>
            
            <a href="{{ route('admin.equipment.index') }}" 
               class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.equipment.*') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                <span>Thiết Bị</span>
            </a>
            
            @if($isLandlord)
                <a href="{{ route('admin.reports.index') }}" 
                   class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                    <i class="fa-solid fa-chart-column text-lg"></i>
                    <span>Báo Cáo</span>
                </a>
            @endif
            
            <a href="{{ route('admin.payments.index') }}" 
               class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.payments.*') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-money-check-dollar text-lg"></i>
                <span>Thanh Toán</span>
            </a>
            
            @if($isLandlord)
                <a href="{{ route('admin.activity_logs.index') }}" 
                   class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.activity_logs.*') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Lịch Sử Vận Hành</span>
                </a>
            @endif

            @if(Auth::user()?->isAdmin())
                <a href="{{ route('admin.verifications.index') }}" 
                   class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ (request()->routeIs('admin.verifications.*') || request()->routeIs('admin.audit-logs') || request()->routeIs('admin.settings') || request()->routeIs('admin.analytics')) ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                    <i class="fa-solid fa-shield-halved text-lg"></i>
                    <span>Giám Sát & Bảo Mật</span>
                </a>
            @endif
            
            <a href="{{ route('smartroom.admin') }}?tab=utility-section" 
               data-section="utility-section" 
               class="sidebar-nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ ($isDashboardRoute && $currentTab === 'utility-section') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-bolt text-lg"></i>
                <span>Chốt Điện Nước</span>
            </a>
            
            <a href="{{ route('smartroom.admin') }}?tab=resident-section" 
               data-section="resident-section" 
               class="sidebar-nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ ($isDashboardRoute && $currentTab === 'resident-section') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-users text-lg"></i>
                <span>Quản Lý Cư Dân</span>
            </a>
            
            <a href="{{ route('smartroom.admin') }}?tab=contract-section" 
               data-section="contract-section" 
               class="sidebar-nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ ($isDashboardRoute && $currentTab === 'contract-section') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-file-signature text-lg"></i>
                <span>Hợp Đồng Online</span>
            </a>
            
            <a href="{{ route('smartroom.admin') }}?tab=contact-section" 
               data-section="contact-section" 
               class="sidebar-nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ ($isDashboardRoute && $currentTab === 'contact-section') ? 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800' }}">
                <i class="fa-solid fa-phone-volume text-lg"></i>
                <span>Yêu Cầu Tư Vấn</span>
                @if($sidebarContactCount > 0)
                    <span class="sidebar-badge ml-auto bg-rose-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full animate-pulse">
                        {{ $sidebarContactCount }}
                    </span>
                @endif
            </a>
        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer p-4 border-t border-slate-900">
        <div class="sidebar-user flex items-center gap-3 p-2 rounded-xl bg-slate-900/50 border border-slate-800/40">
            <div class="w-9 h-9 rounded-lg bg-indigo-900/50 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400 text-sm">
                {{ $userInitials ?: 'AD' }}
            </div>
            <div class="sidebar-profile overflow-hidden">
                <h4 class="text-xs font-bold text-slate-200 truncate">{{ $userName ?: 'Quản Trị Viên' }}</h4>
                <p class="text-[10px] text-slate-500 truncate">{{ $userRoleLabel }}</p>
            </div>
        </div>
        <a href="{{ route('signout') }}" class="sidebar-logout mt-3 w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 transition-all duration-200">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Đăng Xuất (Thoát Admin)</span>
        </a>
    </div>
</aside>
