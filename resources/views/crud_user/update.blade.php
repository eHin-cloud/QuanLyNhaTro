<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Quản Trị Viên - SmartRoom & Renty</title>
    
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
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-[#0b0f19] text-slate-100 min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-indigo-500 selection:text-white">
    
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-600/10 blur-[120px] pointer-events-none"></div>

    <!-- Header -->
    <header class="container mx-auto px-6 py-6 flex justify-between items-center relative z-10 border-b border-slate-900">
        <a href="{{ route('smartroom.portal') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center">
                <i class="fa-solid fa-hotel text-white text-lg"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">SmartRoom</span>
        </a>
    </header>

    <!-- Main -->
    <main class="flex-grow flex items-center justify-center px-4 py-8 relative z-10">
        <div class="w-full max-w-md bg-slate-900/40 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-[0_20px_50px_rgba(0,0,0,0.3)] hover:border-slate-700/60 transition-all duration-300">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent mb-2">Chỉnh Sửa Tài Khoản</h1>
                <p class="text-xs text-slate-400">Cập nhật thông tin quản trị viên #{{ $user->id }}</p>
            </div>

            <form action="{{ route('user.postUpdateUser') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-2" for="name">Họ và Tên</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="name" id="name" value="{{ $user->name }}" required class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800/80 rounded-xl text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all" placeholder="Nguyễn Văn A">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-2" for="username">Tên đăng nhập</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-user-tag"></i>
                        </span>
                        <input type="text" name="username" id="username" value="{{ $user->username }}" required class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800/80 rounded-xl text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all" placeholder="username_vi_viet">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-2" for="phone">Số điện thoại</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <input type="tel" name="phone" id="phone" value="{{ $user->phone }}" required class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800/80 rounded-xl text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all" placeholder="0901234567">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-2" for="email">Địa chỉ Email (Không bắt buộc)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ $user->email }}" class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800/80 rounded-xl text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all" placeholder="admin@example.com (tùy chọn)">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-2" for="like">Ghi chú / Mô tả (Like)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-heart"></i>
                        </span>
                        <input type="text" name="like" id="like" value="{{ $user->like }}" required class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800/80 rounded-xl text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all" placeholder="Quản lý chung cư mini">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-2" for="password">Mật khẩu mới</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800/80 rounded-xl text-slate-200 text-sm focus:border-indigo-500 focus:outline-none transition-all" placeholder="Nhập mật khẩu mới hoặc cũ để xác nhận">
                    </div>
                </div>

                <div class="pt-2 flex gap-3">
                    <a href="{{ route('user.list') }}" class="flex-1 py-2.5 px-4 text-center rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 transition-all">
                        Hủy
                    </a>
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold text-sm shadow-lg shadow-indigo-500/20 transform active:scale-95 transition-all">
                        Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-600 relative z-10 border-t border-slate-950">
        © 2026 SmartRoom & Renty. Tất cả quyền được bảo lưu.
    </footer>
</body>
</html>
