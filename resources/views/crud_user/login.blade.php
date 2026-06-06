<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng nhập hệ thống quản lý SmartRoom & Renty.">
    <title>Đăng Nhập - SmartRoom & Renty</title>
    
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
    
    <style>
        /* Glassmorphic Toast Notifications */
        .toast-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-width: 400px;
            width: calc(100% - 3rem);
        }
        .toast-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-left-width: 4px;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            transform: translateX(120%);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .toast-card.show {
            transform: translateX(0);
        }
        .toast-success { border-left-color: #10b981; }
        .toast-error { border-left-color: #ef4444; }
        .toast-info { border-left-color: #3b82f6; }
    </style>
</head>
<body class="bg-[#0b0f19] text-slate-100 min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-indigo-500 selection:text-white">
    
    <!-- Decorative background glow elements -->
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-600/10 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-violet-600/10 blur-[120px] pointer-events-none"></div>

    <!-- Toast container for notifications -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Header / Navbar -->
    <header class="container mx-auto px-6 py-6 flex justify-between items-center relative z-10">
        <a href="{{ route('smartroom.portal') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition-all">
                <i class="fa-solid fa-hotel text-white text-lg"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">SmartRoom</span>
        </a>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 py-8 relative z-10">
        <div class="w-full max-w-md bg-slate-900/40 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-[0_20px_50px_rgba(0,0,0,0.3)] hover:border-slate-700/60 transition-all duration-300">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent mb-2">Chào Mừng Trở Lại</h1>
                <p class="text-xs text-slate-400">Đăng nhập tài khoản quản lý nhà trọ của bạn</p>
            </div>

            <form action="{{ route('user.authUser') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-2" for="login">Tên đăng nhập hoặc Số điện thoại</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="login" id="login" required class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800/80 rounded-xl text-slate-200 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all" placeholder="Nhập tên đăng nhập hoặc số điện thoại">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-semibold text-slate-400" for="password">Mật khẩu</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800/80 rounded-xl text-slate-200 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold text-sm shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/35 transform active:scale-95 transition-all duration-200">
                    Đăng Nhập Hệ Thống
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-800/60 text-center space-y-3">
                <p class="text-xs text-slate-400">Chưa có tài khoản quản lý?</p>
                <a href="{{ route('user.createUser') }}" class="inline-block px-4 py-2 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-indigo-400 transition-all">
                    Đăng Ký Tài Khoản Mới
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-600 relative z-10 border-t border-slate-950">
        © 2026 SmartRoom & Renty. Tất cả quyền được bảo lưu.
    </footer>

    <!-- Global Glassmorphic Toast Script -->
    <script>
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const card = document.createElement('div');
            card.className = `toast-card toast-${type}`;
            
            let icon = 'fa-circle-info';
            if (type === 'success') icon = 'fa-circle-check';
            if (type === 'error') icon = 'fa-circle-exclamation';

            card.innerHTML = `
                <i class="fa-solid ${icon} mt-0.5 text-lg ${type === 'success' ? 'text-emerald-400' : (type === 'error' ? 'text-red-400' : 'text-blue-400')}"></i>
                <div class="flex-grow">
                    <p class="text-xs font-medium text-slate-200 leading-relaxed">${message}</p>
                </div>
            `;
            container.appendChild(card);
            
            // Trigger animation
            setTimeout(() => card.classList.add('show'), 10);
            
            // Auto remove after 4.5 seconds
            setTimeout(() => {
                card.classList.remove('show');
                setTimeout(() => card.remove(), 400);
            }, 4500);
        }

        // Display notifications from Laravel session if present
        @if(session('success'))
            showToast("{{ session('success') }}", "success");
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", "error");
        @endif

        @if($errors->any())
            showToast("{{ $errors->first() }}", "error");
        @endif
    </script>
</body>
</html>
