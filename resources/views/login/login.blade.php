@php
    $page = $page ?? 'login';
    $titles = [
        'login' => 'Đăng Nhập - SmartRoom & Renty',
        'create' => 'Đăng Ký - SmartRoom & Renty',
        'list' => 'Danh Sách Quản Trị Viên - SmartRoom & Renty',
        'read' => 'Chi Tiết Quản Trị Viên - SmartRoom & Renty',
        'update' => 'Sửa Thông Tin Quản Trị Viên - SmartRoom & Renty',
    ];
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng nhập hệ thống quản lý SmartRoom & Renty.">
    <title>{{ $titles[$page] ?? $titles['login'] }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        .toast-container{position:fixed;top:1.5rem;right:1.5rem;z-index:9999;display:flex;flex-direction:column;gap:.75rem;max-width:400px;width:calc(100% - 3rem)}
        .toast-card{background:rgba(15,23,42,.6);backdrop-filter:blur(16px) saturate(180%);-webkit-backdrop-filter:blur(16px) saturate(180%);border:1px solid rgba(255,255,255,.08);border-left-width:4px;padding:1rem 1.25rem;border-radius:1rem;box-shadow:0 10px 30px -5px rgba(0,0,0,.3);display:flex;align-items:flex-start;gap:.75rem;transform:translateX(120%);transition:all .4s cubic-bezier(.16,1,.3,1)}
        .toast-card.show{transform:translateX(0)}
        .toast-success{border-left-color:#10b981}.toast-error{border-left-color:#ef4444}.toast-info{border-left-color:#3b82f6}
    </style>
</head>
<body class="bg-[#0b0f19] text-slate-100 min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-indigo-500 selection:text-white">
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-600/10 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-violet-600/10 blur-[120px] pointer-events-none"></div>
    <div id="toast-container" class="toast-container"></div>

    <header class="container mx-auto px-6 py-6 flex justify-between items-center relative z-10 {{ in_array($page, ['list', 'read', 'update']) ? 'border-b border-slate-900' : '' }}">
        <a href="{{ route('smartroom.portal') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition-all">
                <i class="fa-solid fa-hotel text-white text-lg"></i>
            </div>
            <span class="login-brand-lockup text-xl font-extrabold tracking-tight">
                <span class="login-brand-word login-brand-smart">SmartRoom</span>
                <span class="login-brand-word login-brand-renty">&amp; Renty</span>
            </span>
        </a>
        <div class="flex items-center gap-4">
            <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                <i class="fa-solid fa-moon" data-theme-icon></i>
            </button>
            @if($page === 'list')
                <a href="{{ route('admin.verifications.index') }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                    <i class="fa-solid fa-shield-halved mr-1.5"></i> Giám Sát & Bảo Mật
                </a>
                <a href="{{ route('smartroom.admin') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-500/25 transition-all">
                    <i class="fa-solid fa-gauge mr-1.5"></i> Dashboard Admin
                </a>
                <a href="{{ route('signout') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-rose-400 rounded-xl text-xs font-semibold transition-all">
                    Đăng Xuất
                </a>
            @endif
        </div>
    </header>

    @if($page === 'list')
        <main class="container mx-auto px-6 py-12 flex-grow relative z-10">
            <div class="max-w-7xl mx-auto">
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Quản Trị Hệ Thống</h1>
                        <p class="text-xs text-slate-400 mt-1">Danh sách người dùng và phân quyền quản lý trong hệ thống SmartRoom</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.verifications.index') }}" class="px-4 py-2.5 bg-sky-600/90 hover:bg-sky-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-sky-500/20 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved"></i> Giám Sát & Bảo Mật
                        </a>
                        <a href="{{ route('user.createUser') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-emerald-500/20 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-1.5">
                            <i class="fa-solid fa-user-plus"></i> Thêm Admin Mới
                        </a>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
                    <!-- Total members -->
                    <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/80 rounded-2xl p-5 flex items-center justify-between shadow-md">
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Tổng thành viên</span>
                            <h3 class="text-2xl font-black text-slate-100 mt-1">{{ $users->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shadow-inner">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                    </div>
                    <!-- Admin count -->
                    <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/80 rounded-2xl p-5 flex items-center justify-between shadow-md">
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Quản trị viên</span>
                            <h3 class="text-2xl font-black text-rose-450 mt-1">{{ $users->filter(fn($u) => $u->roleSlug() === 'admin')->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center shadow-inner">
                            <i class="fa-solid fa-user-shield text-lg"></i>
                        </div>
                    </div>
                    <!-- Landlord count -->
                    <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/80 rounded-2xl p-5 flex items-center justify-between shadow-md">
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Chủ nhà trọ</span>
                            <h3 class="text-2xl font-black text-sky-400 mt-1">{{ $users->filter(fn($u) => in_array($u->roleSlug(), ['landlord', 'unverified_landlord']))->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center shadow-inner">
                            <i class="fa-solid fa-house-user text-lg"></i>
                        </div>
                    </div>
                    <!-- Resident count -->
                    <div class="bg-slate-900/30 backdrop-blur-md border border-slate-800/80 rounded-2xl p-5 flex items-center justify-between shadow-md">
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Cư dân thuê trọ</span>
                            <h3 class="text-2xl font-black text-emerald-450 mt-1">{{ $users->filter(fn($u) => $u->roleSlug() === 'resident')->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-450 flex items-center justify-center shadow-inner">
                            <i class="fa-solid fa-door-closed text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Filters & Search -->
                <div class="flex flex-col md:flex-row gap-3 justify-between items-center mb-6">
                    <div class="relative w-full md:w-80">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-550 pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" id="user-search" onkeyup="filterUserList()" placeholder="Tìm tên, email, số điện thoại..." class="w-full bg-slate-950/60 border border-slate-800 hover:border-slate-700/60 focus:border-indigo-550 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:outline-none transition-all text-slate-200 placeholder-slate-500">
                    </div>
                    <div class="flex gap-2 w-full md:w-auto justify-end">
                        <select id="role-filter" onchange="filterUserList()" class="bg-slate-950/60 border border-slate-800 rounded-xl px-4 py-2.5 text-xs font-semibold focus:outline-none focus:border-indigo-555 transition-all text-slate-300">
                            <option value="all">Tất cả vai trò</option>
                            <option value="admin">Quản trị viên</option>
                            <option value="landlord">Chủ trọ</option>
                            <option value="unverified_landlord">Chủ trọ chưa xác minh</option>
                            <option value="manager">Nhân viên quản lý</option>
                            <option value="resident">Cư dân</option>
                            <option value="guest">Khách xem phòng</option>
                        </select>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="bg-slate-900/35 backdrop-blur-xl border border-slate-800 rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.35)]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="users-table">
                            <thead>
                                <tr class="border-b border-slate-800 bg-slate-900/50 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="px-6 py-4.5">ID</th>
                                    <th class="px-6 py-4.5">Thành Viên</th>
                                    <th class="px-6 py-4.5">Liên Hệ</th>
                                    <th class="px-6 py-4.5">Vai Trò &amp; Đối Tác</th>
                                    <th class="px-6 py-4.5">Ghi Chú</th>
                                    <th class="px-6 py-4.5 text-center">Phân Quyền</th>
                                    <th class="px-6 py-4.5 text-right">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40 text-slate-200">
                                @foreach($users as $userItem)
                                    <tr class="hover:bg-slate-800/15 transition-colors user-row"
                                        data-name="{{ strtolower($userItem->name) }}"
                                        data-email="{{ strtolower($userItem->email) }}"
                                        data-phone="{{ strtolower($userItem->phone) }}"
                                        data-role="{{ $userItem->roleSlug() }}">
                                        <td class="px-6 py-5 text-xs font-semibold text-slate-500">#{{ $userItem->id }}</td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <!-- Letter Avatar -->
                                                <div class="w-8.5 h-8.5 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-xs font-extrabold text-white shadow-md shadow-indigo-500/10">
                                                    {{ mb_strtoupper(mb_substr($userItem->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-200 text-sm block leading-tight">{{ $userItem->name }}</span>
                                                    <span class="text-[10px] text-slate-500">@<span>{{ $userItem->username }}</span></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-xs text-slate-350 block">{{ $userItem->email ?: 'Chưa cập nhật email' }}</span>
                                            <span class="text-[10px] text-slate-550 flex items-center gap-1 mt-0.5">
                                                <i class="fa-solid fa-phone text-[9px] text-slate-600"></i>
                                                {{ $userItem->phone }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5">
                                            @php
                                                $roleColor = match ($userItem->roleSlug()) {
                                                    'admin' => 'bg-rose-500/10 text-rose-300 border-rose-500/15',
                                                    'landlord' => 'bg-blue-500/10 text-blue-300 border-blue-500/15',
                                                    'unverified_landlord' => 'bg-amber-500/10 text-amber-300 border-amber-500/15',
                                                    'manager' => 'bg-teal-500/10 text-teal-300 border-teal-500/15',
                                                    'resident' => 'bg-emerald-500/10 text-emerald-350 border-emerald-500/15',
                                                    default => 'bg-slate-500/10 text-slate-300 border-slate-500/15',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border {{ $roleColor }} inline-block uppercase tracking-wider">
                                                {{ $userItem->roleName() }}
                                            </span>
                                            @if($userItem->tenant)
                                                <span class="text-[10px] text-slate-400 block mt-1.5 flex items-center gap-1">
                                                    <i class="fa-solid fa-hotel text-[9px] text-indigo-400"></i>
                                                    {{ $userItem->tenant->name }}
                                                </span>
                                            @else
                                                <span class="text-[10px] text-slate-600 block mt-1.5 italic">Không gắn đối tác</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5 text-slate-450 text-xs italic">{{ Str::limit($userItem->like, 25) }}</td>
                                        <td class="px-6 py-5 text-center">
                                            <button type="button"
                                                    onclick="openAssignRoleModal({{ $userItem->id }}, '{{ addslashes($userItem->name) }}', '{{ $userItem->roleSlug() }}', '{{ $userItem->tenant_id }}')"
                                                    class="mx-auto px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 rounded-xl text-xs font-semibold border border-indigo-500/20 transition-all flex items-center justify-center gap-1.5 shadow-md shadow-indigo-500/5 active:scale-[0.97]">
                                                <i class="fa-solid fa-user-gear"></i> Phân quyền
                                            </button>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="inline-flex gap-2">
                                                <a href="{{ route('user.readUser', ['id' => $userItem->id]) }}"
                                                   class="p-2 bg-slate-950 hover:bg-slate-900 border border-slate-800 hover:border-slate-700 rounded-xl text-slate-300 hover:text-white transition-all flex items-center justify-center shadow"
                                                   title="Xem chi tiết">
                                                    <i class="fa-regular fa-eye text-xs"></i>
                                                </a>
                                                <a href="{{ route('user.updateUser', ['id' => $userItem->id]) }}"
                                                   class="p-2 bg-slate-950 hover:bg-slate-900 border border-slate-800 hover:border-indigo-550 rounded-xl text-indigo-400 hover:text-indigo-300 transition-all flex items-center justify-center shadow"
                                                   title="Chỉnh sửa">
                                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                                </a>
                                                <a href="{{ route('user.deleteUser', ['id' => $userItem->id]) }}"
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');"
                                                   class="p-2 bg-rose-500/5 hover:bg-rose-500/20 border border-rose-500/10 hover:border-rose-500/30 rounded-xl text-rose-400 transition-all flex items-center justify-center shadow"
                                                   title="Xóa">
                                                    <i class="fa-regular fa-trash-can text-xs"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($users->hasPages())
                        <div class="mt-6 flex justify-center pb-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal Phân Quyền Tài Khoản -->
            <div id="assign-role-modal" class="fixed inset-0 z-50 items-center justify-center hidden transition-opacity duration-300">
                <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-sm" onclick="closeAssignRoleModal()"></div>
                <div class="bg-[#0f172a] border border-slate-800/80 rounded-3xl p-6 w-full max-w-md relative z-10 mx-4 shadow-2xl transition-all transform scale-95 duration-350">
                    <div class="flex items-center justify-between mb-5 border-b border-slate-800/60 pb-3">
                        <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-user-shield text-indigo-400"></i> Phân Quyền Thành Viên
                        </h3>
                        <button onclick="closeAssignRoleModal()" class="w-8 h-8 rounded-lg hover:bg-slate-800/60 text-slate-400 hover:text-slate-100 flex items-center justify-center transition-all">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    
                    <form id="assign-role-form" method="POST" action="{{ route('user.updateRole') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="user_id" id="modal-user-id">
                        
                        <div>
                            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Tên thành viên</label>
                            <input type="text" id="modal-user-name" readonly class="w-full rounded-xl border border-slate-850 bg-slate-950 px-3.5 py-2.5 text-xs text-slate-400 outline-none cursor-not-allowed font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Vai Trò</label>
                            <select name="role_slug" id="modal-role-select" onchange="toggleTenantSelect()" class="w-full rounded-xl border border-slate-850 bg-slate-950 px-3.5 py-2.5 text-xs text-slate-200 outline-none focus:border-indigo-500/80 transition-all font-semibold font-sans">
                                @foreach($roles as $role)
                                    <option value="{{ $role->slug }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="modal-tenant-container">
                            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Gán nhà trọ / Tenant</label>
                            <select name="tenant_id" id="modal-tenant-select" class="w-full rounded-xl border border-slate-850 bg-slate-950 px-3.5 py-2.5 text-xs text-slate-200 outline-none focus:border-indigo-500/80 transition-all font-semibold font-sans">
                                <option value="">Không gán nhà trọ</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-500 mt-2 leading-relaxed italic">
                                * Chủ trọ hoặc Nhân viên quản lý bắt buộc phải được gán nhà trọ để quản trị đúng phạm vi.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-slate-800/60">
                            <button type="button" onclick="closeAssignRoleModal()" class="flex-1 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-350 hover:bg-slate-850 transition-all text-center">
                                Hủy Bỏ
                            </button>
                            <button type="submit" class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-550 text-xs font-semibold text-white shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 transition-all text-center active:scale-[0.98]">
                                Cập Nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // Modal role assign functions
                function openAssignRoleModal(id, name, roleSlug, tenantId) {
                    document.getElementById('modal-user-id').value = id;
                    document.getElementById('modal-user-name').value = name;
                    document.getElementById('modal-role-select').value = roleSlug;
                    document.getElementById('modal-tenant-select').value = tenantId || '';
                    
                    toggleTenantSelect();
                    
                    const modal = document.getElementById('assign-role-modal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    
                    // Subtle pop animation
                    setTimeout(() => {
                        modal.querySelector('.bg-\\[\\#0f172a\\]').classList.remove('scale-95');
                        modal.querySelector('.bg-\\[\\#0f172a\\]').classList.add('scale-100');
                    }, 10);
                }

                function closeAssignRoleModal() {
                    const modal = document.getElementById('assign-role-modal');
                    modal.querySelector('.bg-\\[\\#0f172a\\]').classList.remove('scale-100');
                    modal.querySelector('.bg-\\[\\#0f172a\\]').classList.add('scale-95');
                    
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 150);
                }

                function toggleTenantSelect() {
                    const roleSelect = document.getElementById('modal-role-select').value;
                    const tenantContainer = document.getElementById('modal-tenant-container');
                    // If the role needs a tenant assignment
                    if (['landlord', 'unverified_landlord', 'manager'].includes(roleSelect)) {
                        tenantContainer.style.display = 'block';
                    } else {
                        tenantContainer.style.display = 'none';
                    }
                }

                function filterUserList() {
                    const searchQuery = document.getElementById('user-search').value.toLowerCase().trim();
                    const roleFilter = document.getElementById('role-filter').value;
                    const rows = document.querySelectorAll('.user-row');

                    rows.forEach(row => {
                        const name = row.getAttribute('data-name');
                        const email = row.getAttribute('data-email');
                        const phone = row.getAttribute('data-phone');
                        const role = row.getAttribute('data-role');

                        const matchesSearch = name.includes(searchQuery) || email.includes(searchQuery) || phone.includes(searchQuery);
                        const matchesRole = roleFilter === 'all' || role === roleFilter;

                        if (matchesSearch && matchesRole) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }
            </script>
        </main>
    @else
        <main class="flex-grow flex items-center justify-center px-4 py-8 relative z-10 {{ $page === 'login' ? 'login-main-stage' : '' }}">
            <div class="{{ $page === 'login' ? 'login-hero-shell' : (($page === 'create' || $page === 'update') ? 'register-card-upgraded' : 'w-full max-w-md bg-slate-900/40 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-[0_20px_50px_rgba(0,0,0,0.3)] hover:border-slate-700/60 transition-all duration-300') }}">
                @if($page === 'login')
                    <section class="login-visual-panel">
                        <div class="login-visual-copy">
                            <p class="login-eyebrow">SmartRoom &amp; Renty</p>
                            <h1>Quản lý nhà trọ rõ ràng hơn từ lần đăng nhập đầu tiên</h1>
                            <p>Theo dõi phòng, đánh giá, hóa đơn và trải nghiệm thuê trọ trong một giao diện tối hiện đại.</p>
                        </div>
                        <div id="login-house-3d" class="login-house-3d" aria-hidden="true"></div>
                        <div class="login-visual-stats" aria-label="Thông tin nổi bật">
                            <span><strong>24/7</strong> Theo dõi</span>
                            <span><strong>3D</strong> Trực quan</span>
                            <span><strong>Renty</strong> Review</span>
                        </div>
                    </section>
                    <section class="login-card-upgraded">
                        <div class="text-center mb-8">
                            <p class="login-form-kicker">Đăng nhập hệ thống</p>
                            <h2 class="text-3xl font-extrabold tracking-tight text-slate-50 mb-2">Chào Mừng Trở Lại</h2>
                            <p class="text-xs text-slate-400">Đăng nhập tài khoản quản lý nhà trọ của bạn</p>
                        </div>
                        <form action="{{ route('user.authUser') }}" method="POST" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-2" for="login">Tên đăng nhập hoặc Số điện thoại</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" name="login" id="login" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="Nhập tên đăng nhập hoặc số điện thoại">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-2" for="password">Mật khẩu</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" name="password" id="password" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="••••••••">
                                </div>
                            </div>
                            <button type="submit" class="login-submit-button w-full py-3 px-4 rounded-xl text-white font-semibold text-sm transform active:scale-95 transition-all duration-200">Đăng Nhập Hệ Thống</button>
                        </form>
                        <div class="mt-8 pt-6 border-t border-slate-800/60 text-center space-y-3">
                            <p class="text-xs text-slate-400">Chưa có tài khoản quản lý?</p>
                            <a href="{{ route('landlord.register') }}" class="login-register-link inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold transition-all">Đăng ký chủ trọ mới</a>
                            <a href="{{ route('user.createUser') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-slate-200 transition-all">Đăng ký tài khoản khách</a>
                        </div>
                    </section>
                @elseif($page === 'create' || $page === 'update')
                    @php $editing = $page === 'update'; @endphp
                    <div class="register-card-header">
                        <div>
                            <p class="login-form-kicker">{{ $editing ? 'Cập nhật hồ sơ' : (Auth::check() ? 'Thêm thành viên mới' : 'Đăng ký tài khoản khách') }}</p>
                            <h1>{{ $editing ? 'Chỉnh Sửa Tài Khoản' : (Auth::check() ? 'Thêm Thành Viên' : 'Đăng Ký Tài Khoản Khách') }}</h1>
                            <p>{{ $editing ? 'Cập nhật thông tin quản trị viên #' . $user->id : (Auth::check() ? 'Tạo tài khoản mới và gán vai trò quản trị.' : 'Đăng ký tài khoản khách để tìm phòng và gửi liên hệ.') }}</p>
                        </div>
                        <div class="register-mini-badge" aria-hidden="true">
                            <i class="fa-solid fa-user-tag"></i>
                            <span>{{ $editing ? 'Admin' : (Auth::check() ? 'Thêm Mới' : 'Khách') }}</span>
                        </div>
                    </div>
                    <form action="{{ $editing ? route('user.postUpdateUser') : route('user.postUser') }}" method="POST" class="register-form-grid">
                        @csrf
                        @if($editing)
                            <input type="hidden" name="id" value="{{ $user->id }}">
                        @endif
                        <div class="register-field">
                            <label class="block text-xs font-semibold text-slate-300 mb-2" for="name">Họ và Tên</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="name" id="name" value="{{ old('name', $editing ? $user->name : '') }}" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="Nguyễn Văn A">
                            </div>
                        </div>
                        <div class="register-field">
                            <label class="block text-xs font-semibold text-slate-300 mb-2" for="username">Tên đăng nhập</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-user-tag"></i></span>
                                <input type="text" name="username" id="username" value="{{ old('username', $editing ? $user->username : '') }}" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="username_vi_viet">
                            </div>
                        </div>
                        <div class="register-field">
                            <label class="block text-xs font-semibold text-slate-300 mb-2" for="phone">Số điện thoại</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-phone"></i></span>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone', $editing ? $user->phone : '') }}" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="0901234567">
                            </div>
                        </div>
                        <div class="register-field">
                            <label class="block text-xs font-semibold text-slate-300 mb-2" for="email">Địa chỉ Email (Không bắt buộc)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" name="email" id="email" value="{{ old('email', $editing ? $user->email : '') }}" class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="admin@example.com (tùy chọn)">
                            </div>
                        </div>
                        <div class="register-field register-field-wide">
                            <label class="block text-xs font-semibold text-slate-300 mb-2" for="like">Ghi chú / Mô tả</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-heart"></i></span>
                                <input type="text" name="like" id="like" value="{{ old('like', $editing ? $user->like : '') }}" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="{{ Auth::check() ? 'Ghi chú công việc hoặc vai trò' : 'Ví dụ: Tìm phòng trọ khu vực Quận 10' }}">
                            </div>
                        </div>
                        <div class="register-field register-field-wide">
                            <label class="block text-xs font-semibold text-slate-300 mb-2" for="password">{{ $editing ? 'Mật khẩu mới' : 'Mật khẩu' }}</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" id="password" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="{{ $editing ? 'Nhập mật khẩu mới hoặc cũ để xác nhận' : '•••••••• (tối thiểu 6 ký tự)' }}">
                            </div>
                        </div>
                        <div class="register-actions">
                            <a href="{{ $editing ? route('user.list') : route('login') }}">{{ $editing ? 'Hủy' : 'Đăng Nhập Ngay' }}</a>
                            <button type="submit">{{ $editing ? 'Cập Nhật' : 'Đăng Ký Tài Khoản' }}</button>
                        </div>
                    </form>
                @elseif($page === 'read')
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 mx-auto rounded-2xl bg-indigo-950/60 border border-indigo-500/20 flex items-center justify-center text-3xl text-indigo-400 mb-4">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <h1 class="text-2xl font-extrabold text-slate-200">{{ $messi->name }}</h1>
                        <p class="text-xs text-indigo-400 mt-1">ID Admin: #{{ $messi->id }}</p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Email liên hệ</label>
                            <p class="text-slate-200 mt-1 text-sm bg-slate-950/45 px-3 py-2.5 rounded-xl border border-slate-800/40">{{ $messi->email }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Mô tả / Sở thích</label>
                            <p class="text-slate-200 mt-1 text-sm bg-slate-950/45 px-3 py-2.5 rounded-xl border border-slate-800/40">{{ $messi->like }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 tracking-wider">Ngày đăng ký</label>
                            <p class="text-slate-200 mt-1 text-sm bg-slate-950/45 px-3 py-2.5 rounded-xl border border-slate-800/40">{{ $messi->created_at ? $messi->created_at->format('d/m/Y H:i') : 'Chưa rõ' }}</p>
                        </div>
                    </div>
                    <div class="mt-8 flex gap-3">
                        <a href="{{ route('user.list') }}" class="flex-1 py-2.5 px-4 text-center rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 transition-all">Quay Lại Danh Sách</a>
                        <a href="{{ route('user.updateUser', ['id' => $messi->id]) }}" class="flex-1 py-2.5 px-4 text-center rounded-xl bg-indigo-650 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-500/25 transition-all">Chỉnh Sửa</a>
                    </div>
                @endif
            </div>
        </main>
    @endif

    <footer class="py-6 text-center text-xs text-slate-600 relative z-10 border-t border-slate-950">
        © 2026 SmartRoom & Renty. Tất cả quyền được bảo lưu.
    </footer>

    <script>
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const card = document.createElement('div');
            card.className = `toast-card toast-${type}`;
            let icon = 'fa-circle-info';
            if (type === 'success') icon = 'fa-circle-check';
            if (type === 'error') icon = 'fa-circle-exclamation';
            card.innerHTML = `<i class="fa-solid ${icon} mt-0.5 text-lg ${type === 'success' ? 'text-emerald-400' : (type === 'error' ? 'text-red-400' : 'text-blue-400')}"></i><div class="flex-grow"><p class="text-xs font-medium text-slate-200 leading-relaxed">${message}</p></div>`;
            container.appendChild(card);
            setTimeout(() => card.classList.add('show'), 10);
            setTimeout(() => {
                card.classList.remove('show');
                setTimeout(() => card.remove(), 400);
            }, 4500);
        }

        @if(session('success'))
            showToast(@json(session('success')), "success");
        @endif
        @if(session('error'))
            showToast(@json(session('error')), "error");
        @endif
        @if($errors->any())
            showToast(@json($errors->first()), "error");
        @endif
    </script>
</body>
</html>
