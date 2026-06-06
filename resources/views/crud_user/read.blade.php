<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Quản Trị Viên - SmartRoom & Renty</title>
    
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
                <a href="{{ route('user.list') }}" class="flex-1 py-2.5 px-4 text-center rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 transition-all">
                    Quay Lại Danh Sách
                </a>
                <a href="{{ route('user.updateUser', ['id' => $messi->id]) }}" class="flex-1 py-2.5 px-4 text-center rounded-xl bg-indigo-650 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-500/25 transition-all">
                    Chỉnh Sửa
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-600 relative z-10 border-t border-slate-950">
        © 2026 SmartRoom & Renty. Tất cả quyền được bảo lưu.
    </footer>
</body>
</html>
