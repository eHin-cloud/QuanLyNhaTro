<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Quản Trị Viên - SmartRoom & Renty</title>
    
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
        <div class="flex items-center gap-4">
            <a href="{{ route('smartroom.admin') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-500/25 transition-all">
                <i class="fa-solid fa-gauge mr-1.5"></i> Dashboard Admin
            </a>
            <a href="{{ route('signout') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-rose-400 rounded-xl text-xs font-semibold transition-all">
                Đăng Xuất
            </a>
        </div>
    </header>

    <!-- Main -->
    <main class="container mx-auto px-6 py-12 flex-grow relative z-10">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Quản Trị Viên</h1>
                    <p class="text-xs text-slate-400 mt-1">Danh sách người dùng được cấp quyền quản lý trong hệ thống</p>
                </div>
                <a href="{{ route('user.createUser') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-emerald-500/20 transition-all">
                    <i class="fa-solid fa-user-plus mr-1.5"></i> Thêm Admin Mới
                </a>
            </div>

            <!-- Users Table -->
            <div class="bg-slate-900/40 backdrop-blur-xl border border-slate-800 rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.3)]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-900/60 text-xs font-semibold text-slate-400 tracking-wider">
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Tên</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Mô Tả / Sở Thích</th>
                                <th class="px-6 py-4 text-right">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-200">
                            @foreach($users as $user)
                            <tr class="hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 text-xs font-semibold text-slate-400">#{{ $user->id }}</td>
                                <td class="px-6 py-4 font-bold text-slate-200">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-slate-300">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-slate-400 text-xs italic">{{ $user->like }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('user.readUser', ['id' => $user->id]) }}" class="px-3 py-1.5 bg-slate-950 hover:bg-slate-900 border border-slate-800 rounded-lg text-[10px] font-semibold text-slate-300 transition-all">
                                            Chi Tiết
                                        </a>
                                        <a href="{{ route('user.updateUser', ['id' => $user->id]) }}" class="px-3 py-1.5 bg-slate-950 hover:bg-slate-900 border border-slate-800 rounded-lg text-[10px] font-semibold text-indigo-400 transition-all">
                                            Sửa
                                        </a>
                                        <a href="{{ route('user.deleteUser', ['id' => $user->id]) }}" onclick="return confirm('Bạn có chắc chắn muốn xóa admin này?');" class="px-3 py-1.5 bg-rose-550/10 hover:bg-rose-550/20 border border-rose-500/20 rounded-lg text-[10px] font-semibold text-rose-400 transition-all">
                                            Xóa
                                        </a>
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

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-600 relative z-10 border-t border-slate-950">
        © 2026 SmartRoom & Renty. Tất cả quyền được bảo lưu.
    </footer>
</body>
</html>
