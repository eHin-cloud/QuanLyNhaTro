<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhật Ký Audit - SmartRoom Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #030712; /* slate-950 */
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(51, 65, 85, 0.5);
        }
        .glass-panel:hover {
            border-color: rgba(99, 102, 241, 0.4);
        }
        .glow-circle {
            filter: blur(140px);
            opacity: 0.12;
            pointer-events: none;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #090d16;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">
    {{-- @include('admin.partials.sidebar') --}}

    <div id="admin-shell" class="min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        <!-- Background Elements -->
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-indigo-600 glow-circle"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-sky-600 glow-circle"></div>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 py-10 relative z-10 flex-grow">
        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 pb-6 border-b border-slate-900">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-widest bg-indigo-500/10 text-indigo-400 rounded-lg border border-indigo-500/20">
                        SmartRoom Console
                    </span>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white mt-2 flex items-center gap-3">
                    <i class="fa-solid fa-shield-halved text-indigo-400"></i> Giám Sát & Bảo Mật
                </h1>
                <p class="text-xs text-slate-400">
                    Giám sát lịch sử mở khóa và giải mã các tài liệu bảo mật nhạy cảm của các quản trị viên.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('user.list') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-900/60 backdrop-blur px-4 py-2.5 text-xs font-bold text-slate-300 hover:text-white hover:border-indigo-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fa-solid fa-users-gear text-indigo-400"></i>
                    Quản lý thành viên
                </a>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap items-center gap-2 mb-8 bg-slate-900/40 p-1.5 rounded-xl border border-slate-800/80 max-w-max">
            <a href="{{ route('admin.verifications.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.verifications.index') ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <i class="fa-solid fa-file-signature text-sky-400"></i> Duyệt Hồ Sơ
            </a>
            <a href="{{ route('admin.audit-logs') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.audit-logs') ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <i class="fa-solid fa-shield-halved text-indigo-400"></i> Nhật Ký Audit
            </a>
            <a href="{{ route('admin.analytics') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request()->routeIs('admin.analytics') ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <i class="fa-solid fa-chart-pie text-pink-400"></i> Thống Kê Hệ Thống
            </a>
        </div>

        <!-- Filter Panel -->
        <div class="glass-panel rounded-2xl p-6 mb-8 transition-all duration-300">
            <form action="{{ route('admin.audit-logs') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                <div class="flex-1 w-full space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tìm kiếm nhanh</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo lý do, IP, loại tài liệu..." class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-xs font-medium text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div class="w-full md:w-64 space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Quản trị viên</label>
                    <select name="admin_id" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 px-4 text-xs font-semibold text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-all">
                        <option value="">-- Tất cả Admin --</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }} ({{ $admin->username }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit" class="flex-1 md:flex-none inline-flex justify-center items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-500 px-5 py-2.5 text-xs font-bold text-white transition-all">
                        <i class="fa-solid fa-filter"></i> Lọc
                    </button>
                    @if(request()->anyFilled(['search', 'admin_id']))
                        <a href="{{ route('admin.audit-logs') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-800 hover:border-slate-700 bg-slate-900/60 px-5 py-2.5 text-xs font-bold text-slate-400 hover:text-white transition-all">
                            Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="glass-panel rounded-2xl overflow-hidden border border-slate-800/80 transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-800/80 bg-slate-950/40 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-6">Quản trị viên</th>
                            <th class="py-4 px-6">Chủ trọ mục tiêu</th>
                            <th class="py-4 px-6">Thông tin truy cập</th>
                            <th class="py-4 px-6">Lý do giải mã</th>
                            <th class="py-4 px-6">IP & Thiết bị</th>
                            <th class="py-4 px-6">Thời gian</th>
                            <th class="py-4 px-6 text-center">Bảo mật Row</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-xs">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-900/25 transition-all">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($log->admin->name ?? 'AD', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-200">{{ $log->admin->name ?? 'Đã xóa Admin' }}</p>
                                            <p class="text-[10px] text-slate-500 font-medium">ID: {{ $log->admin_user_id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p class="font-bold text-slate-300">{{ $log->targetLandlord->name ?? 'Chưa xác định' }}</p>
                                            <p class="text-[10px] text-slate-500">Chủ trọ</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 text-[9px] font-extrabold rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 uppercase">
                                                {{ $log->access_type }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                {{ $log->document_type }}
                                            </span>
                                        </div>
                                        @if($log->presigned_url_expires_at)
                                            <p class="text-[10px] text-slate-500">
                                                Hạn link: {{ $log->presigned_url_expires_at->format('H:i:s d/m/Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 max-w-xs">
                                    <p class="text-slate-350 italic line-clamp-2" title="{{ $log->reason }}">
                                        "{{ $log->reason }}"
                                    </p>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-0.5">
                                        <p class="font-mono text-slate-300 text-[11px]">{{ $log->ip_address }}</p>
                                        <p class="text-[10px] text-slate-500 truncate max-w-[150px]" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent }}
                                        </p>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-slate-400">
                                    {{ $log->created_at->format('H:i:s d/m/Y') }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" title="Dòng log đã ký số băm row_hash: {{ $log->row_hash }}">
                                        <i class="fa-solid fa-lock text-[9px]"></i> ĐÃ KÝ
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 font-medium">
                                    <i class="fa-solid fa-shield-halved text-2xl mb-3 block opacity-30"></i>
                                    Chưa có nhật ký truy cập tài liệu bảo mật nào được ghi nhận.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="py-4 px-6 border-t border-slate-800 bg-slate-950/20">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
        </main>
    </div>
    {{-- <script src="{{ asset('js/admin-sidebar.js') }}"></script> --}}
</body>
</html>
