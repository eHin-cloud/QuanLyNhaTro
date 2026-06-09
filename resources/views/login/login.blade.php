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
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        @if($page === 'list')
            <div class="flex items-center gap-4">
                <a href="{{ route('smartroom.admin') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-500/25 transition-all">
                    <i class="fa-solid fa-gauge mr-1.5"></i> Dashboard Admin
                </a>
                <a href="{{ route('signout') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-rose-400 rounded-xl text-xs font-semibold transition-all">
                    Đăng Xuất
                </a>
            </div>
        @endif
    </header>

    @if($page === 'list')
        <main class="container mx-auto px-6 py-12 flex-grow relative z-10">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Quản Trị Viên</h1>
                        <p class="text-xs text-slate-400 mt-1">Danh sách người dùng được cấp quyền quản lý trong hệ thống</p>
                    </div>
                    <a href="{{ route('user.createUser') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-emerald-500/20 transition-all">
                        <i class="fa-solid fa-user-plus mr-1.5"></i> Thêm Admin Mới
                    </a>
                </div>

                <div class="bg-slate-900/40 backdrop-blur-xl border border-slate-800 rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.3)]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800 bg-slate-900/60 text-xs font-semibold text-slate-400 tracking-wider">
                                    <th class="px-4 py-4">ID</th>
                                    <th class="px-4 py-4">Tên</th>
                                    <th class="px-4 py-4">Email</th>
                                    <th class="px-4 py-4">Vai tro</th>
                                    <th class="px-4 py-4">Cap quyen</th>
                                    <th class="px-4 py-4">Mô Tả / Sở Thích</th>
                                    <th class="px-4 py-4 text-right">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-200">
                                @foreach($users as $userItem)
                                    <tr class="hover:bg-slate-900/20 transition-colors">
                                        <td class="px-4 py-4 text-xs font-semibold text-slate-400">#{{ $userItem->id }}</td>
                                        <td class="px-4 py-4 font-bold text-slate-200">{{ $userItem->name }}</td>
                                        <td class="px-4 py-4 text-slate-300">{{ $userItem->email }}</td>
                                        <td class="px-4 py-4">
                                            <div class="text-xs font-bold text-indigo-300">{{ $userItem->roleName() }}</div>
                                            <div class="text-[10px] text-slate-500">{{ $userItem->roleSlug() }}</div>
                                            <div class="text-[10px] text-slate-500 mt-1">{{ $userItem->tenant->name ?? 'Khong gan tenant' }}</div>
                                        </td>
                                        <td class="px-4 py-4 min-w-[220px]">
                                            <form method="POST" action="{{ route('user.updateRole') }}" class="flex flex-col gap-1.5">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $userItem->id }}">
                                                <select name="role_slug" class="px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-[11px] text-slate-200 focus:outline-none focus:border-indigo-500">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->slug }}" @selected($userItem->roleSlug() === $role->slug)>{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                                <select name="tenant_id" class="px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-[11px] text-slate-200 focus:outline-none focus:border-indigo-500">
                                                    <option value="">Khong gan tenant</option>
                                                    @foreach($tenants as $tenant)
                                                        <option value="{{ $tenant->id }}" @selected($userItem->tenant_id === $tenant->id)>{{ $tenant->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-indigo-650 hover:bg-indigo-600 active:scale-[0.98] transition-all text-white text-[10px] font-bold">
                                                    Cap quyen
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-4 text-slate-400 text-xs italic">{{ $userItem->like }}</td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="inline-flex gap-1.5">
                                                <a href="{{ route('user.readUser', ['id' => $userItem->id]) }}" class="px-2.5 py-1.5 bg-slate-950 hover:bg-slate-900 border border-slate-800 rounded-lg text-[10px] font-semibold text-slate-300 transition-all">Chi Tiết</a>
                                                <a href="{{ route('user.updateUser', ['id' => $userItem->id]) }}" class="px-2.5 py-1.5 bg-slate-950 hover:bg-slate-900 border border-slate-800 rounded-lg text-[10px] font-semibold text-indigo-400 transition-all">Sửa</a>
                                                <a href="{{ route('user.deleteUser', ['id' => $userItem->id]) }}" onclick="return confirm('Bạn có chắc chắn muốn xóa admin này?');" class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 rounded-lg text-[10px] font-semibold text-rose-400 transition-all">Xóa</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                            <a href="{{ route('user.createUser') }}" class="login-register-link inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold transition-all">Đăng Ký Tài Khoản Mới</a>
                        </div>
                    </section>
                @elseif($page === 'create' || $page === 'update')
                    @php $editing = $page === 'update'; @endphp
                    <div class="register-card-header">
                        <div>
                            <p class="login-form-kicker">{{ $editing ? 'Cập nhật hồ sơ' : 'Tạo tài khoản quản lý' }}</p>
                            <h1>{{ $editing ? 'Chỉnh Sửa Tài Khoản' : 'Đăng Ký Tài Khoản' }}</h1>
                            <p>{{ $editing ? 'Cập nhật thông tin quản trị viên #' . $user->id : 'Tạo tài khoản SmartRoom & Renty để quản lý phòng, hóa đơn và đánh giá trong một nơi.' }}</p>
                        </div>
                        <div class="register-mini-badge" aria-hidden="true">
                            <i class="fa-solid fa-building-user"></i>
                            <span>{{ $editing ? 'Admin' : 'New Admin' }}</span>
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
                                <input type="text" name="like" id="like" value="{{ old('like', $editing ? $user->like : '') }}" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="Quản lý chung cư mini">
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
