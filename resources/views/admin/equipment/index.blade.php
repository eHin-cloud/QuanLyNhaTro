<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quản lý thiết bị, tồn kho và phân bổ theo phòng - SmartRoom.">
    <title>Quản Lý Thiết Bị - SmartRoom</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])

    <style>
        .glass-card {
            background: rgba(13, 18, 31, 0.45);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(30, 41, 59, 0.8);
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #080b11; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #4f46e5; }
        body.sidebar-collapsed #admin-sidebar { width: 5rem; }
        body.sidebar-collapsed #admin-shell { margin-left: 5rem; }
        body.sidebar-collapsed #admin-sidebar .sidebar-label,
        body.sidebar-collapsed #admin-sidebar .sidebar-profile { display: none; }
        body.sidebar-collapsed #admin-sidebar > div:first-child > div:first-child { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }
        body.sidebar-collapsed #admin-sidebar .sidebar-brand { display: none; }
        body.sidebar-collapsed #admin-sidebar .sidebar-link { justify-content: center; padding-left: 0; padding-right: 0; }
        body.sidebar-collapsed #admin-sidebar .sidebar-footer { padding-left: 0.75rem; padding-right: 0.75rem; }
        body.sidebar-collapsed #sidebar-toggle i { transform: rotate(180deg); }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">
    @php
        $isLandlord = Auth::user()?->isLandlord();
    @endphp
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] rounded-full bg-indigo-600/5 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] rounded-full bg-emerald-600/5 blur-[100px] pointer-events-none"></div>

    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#0d121f] border-r border-slate-900 flex flex-col justify-between h-screen z-30 transition-[width] duration-200">
        <div>
            <div class="p-6 border-b border-slate-900 flex items-center justify-between gap-2">
                <a href="{{ route('smartroom.admin') }}" class="sidebar-brand sidebar-link flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid fa-hotel text-white text-sm"></i>
                    </div>
                    <span class="sidebar-label text-lg font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">SmartRoom</span>
                </a>
                <button type="button" id="sidebar-toggle" class="w-8 h-8 rounded-lg border border-slate-800 text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 transition-all" title="Thu gọn/mở rộng sidebar">
                    <i class="fa-solid fa-angles-left transition-transform"></i>
                </button>
            </div>

            <nav class="p-4 space-y-1">
                <a href="{{ route('smartroom.admin') }}" class="sidebar-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                    <i class="fa-solid fa-chart-pie text-lg"></i>
                    <span class="sidebar-label">Tổng Quan</span>
                </a>
                <a href="{{ route('admin.rooms.index') }}" class="sidebar-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                    <i class="fa-solid fa-door-open text-lg"></i>
                    <span class="sidebar-label">Quản Lý Phòng</span>
                </a>
                <a href="{{ route('admin.equipment.index') }}" class="sidebar-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-indigo-400 bg-indigo-500/10 border border-indigo-500/10 transition-all">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    <span class="sidebar-label">Thiết Bị</span>
                </a>
                @if($isLandlord)
                    <a href="{{ route('admin.reports.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                        <i class="fa-solid fa-chart-column text-lg"></i>
                        <span>Báo Cáo</span>
                    </a>
                    <a href="{{ route('admin.activity_logs.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                        <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                        <span>Lịch Sử Vận Hành</span>
                    </a>
                @endif
            </nav>
        </div>

        <div class="sidebar-footer p-4 border-t border-slate-900">
            <div class="sidebar-link flex items-center gap-3 p-2 rounded-xl bg-slate-900/50 border border-slate-800/40">
                <div class="w-9 h-9 rounded-lg bg-indigo-900/50 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400 text-sm">
                    {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                </div>
                <div class="sidebar-profile overflow-hidden">
                    <h4 class="text-xs font-bold text-slate-200 truncate">{{ Auth::user()->name ?? 'Người dùng' }}</h4>
                    <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->roleName() }}</p>
                </div>
            </div>
            <a href="{{ route('signout') }}" class="sidebar-link mt-3 w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> <span class="sidebar-label">Đăng Xuất</span>
            </a>
        </div>
    </aside>

    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <h2 class="text-lg font-bold text-slate-100">Quản Lý Thông Tin Thiết Bị</h2>
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <div class="text-sm font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-4 py-2 rounded-xl flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-indigo-400"></i>
                    <span>{{ date('d/m/Y') }}</span>
                </div>
            </div>
        </header>

        <main class="p-8 flex-grow overflow-y-auto space-y-6">
            <div id="toast-stack" class="fixed top-5 right-5 z-50 w-[min(360px,calc(100vw-2rem))] space-y-3 pointer-events-none"></div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <section class="glass-card rounded-2xl p-6 xl:col-span-1">
                    <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-indigo-400"></i> Nhập Thiết Bị
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Nhập cùng mã thiết bị sẽ cộng thêm số lượng vào danh mục hiện có.</p>

                    <form action="{{ route('admin.equipment.store') }}" method="POST" class="secure-form mt-5 space-y-4" data-watch="equipment-create">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Mã thiết bị *</label>
                                <input name="code" data-text required maxlength="50" value="{{ old('code') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="VD: MAYGIAT01">
                                <span class="field-error hidden text-xs text-rose-400 mt-1"></span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Đơn vị *</label>
                                <input name="unit" data-text required maxlength="30" value="{{ old('unit', 'cai') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                                <span class="field-error hidden text-xs text-rose-400 mt-1"></span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tên thiết bị *</label>
                            <input name="name" data-text required maxlength="150" value="{{ old('name') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="Máy giặt, điều hòa, giường...">
                            <span class="field-error hidden text-xs text-rose-400 mt-1"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Số lượng nhập *</label>
                            <input name="quantity" data-number required maxlength="7" value="{{ old('quantity') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="VD: 10">
                            <span class="field-error hidden text-xs text-rose-400 mt-1"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Ghi chú</label>
                            <textarea name="description" data-text maxlength="1000" rows="3" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 resize-none">{{ old('description') }}</textarea>
                            <span class="field-error hidden text-xs text-rose-400 mt-1"></span>
                        </div>
                        <button type="submit" class="submit-btn w-full py-3 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu Thiết Bị
                        </button>
                    </form>
                </section>

                <section class="glass-card rounded-2xl p-6 xl:col-span-2">
                    <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-right-left text-emerald-400"></i> Bàn Giao - Thu Hồi Theo Phòng
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                        <form action="{{ route('admin.equipment.allocate') }}" method="POST" class="secure-form rounded-xl bg-slate-950/40 border border-slate-800 p-5 space-y-4" data-watch="equipment-allocate">
                            @csrf
                            <h4 class="text-sm font-bold text-emerald-400">Bàn giao / lắp đặt</h4>
                            @include('admin.equipment.partials.movement-fields', ['equipment' => $equipment, 'rooms' => $rooms, 'mode' => 'allocate'])
                            <button type="submit" class="submit-btn w-full py-3 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-screwdriver-wrench"></i> Bàn Giao
                            </button>
                        </form>

                        <form action="{{ route('admin.equipment.recover') }}" method="POST" class="secure-form rounded-xl bg-slate-950/40 border border-slate-800 p-5 space-y-4" data-watch="equipment-recover">
                            @csrf
                            <h4 class="text-sm font-bold text-amber-400">Thu hồi về kho</h4>
                            @include('admin.equipment.partials.movement-fields', ['equipment' => $equipment, 'rooms' => $rooms, 'mode' => 'recover'])
                            <button type="submit" class="submit-btn w-full py-3 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-rotate-left"></i> Thu Hồi
                            </button>
                        </form>
                    </div>
                </section>
            </div>

            <section class="glass-card rounded-2xl p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-base font-bold text-slate-200">Danh Mục Và Tồn Kho</h3>
                        <p class="text-xs text-slate-500 mt-1">Số tồn = tổng số lượng trong danh mục - số đang phân bổ ở các phòng.</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-900">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                            <tr>
                                <th class="px-4 py-4 font-bold">Mã</th>
                                <th class="px-4 py-4 font-bold">Tên thiết bị</th>
                                <th class="px-4 py-4 font-bold">Tổng</th>
                                <th class="px-4 py-4 font-bold">Đã phân bổ</th>
                                <th class="px-4 py-4 font-bold">Tồn</th>
                                <th class="px-4 py-4 font-bold">Phòng đang dùng</th>
                                <th class="px-4 py-4 font-bold text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                            @forelse($equipment as $item)
                                <tr class="hover:bg-slate-900/40 transition-all align-top">
                                    <td class="px-4 py-4 font-bold text-indigo-400">{{ $item->code }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-slate-200">{{ $item->name }}</div>
                                        <div class="text-[11px] text-slate-500 mt-1">Đơn vị: {{ $item->unit }}</div>
                                        @if($item->description)
                                            <div class="text-[11px] text-slate-500 mt-1 max-w-xs break-words">{{ $item->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 font-bold">{{ $item->total_quantity }}</td>
                                    <td class="px-4 py-4 text-amber-400 font-bold">{{ $item->allocated_quantity }}</td>
                                    <td class="px-4 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $item->stock_quantity > 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                            {{ $item->stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 min-w-[240px]">
                                        @forelse($item->roomAllocations->where('quantity', '>', 0) as $allocation)
                                            <div class="mb-1 text-[11px] text-slate-400">
                                                <span class="font-bold text-slate-200">P.{{ $allocation->room->room_number ?? 'N/A' }}</span>
                                                <span class="text-slate-500">({{ $allocation->room->building->name ?? 'N/A' }})</span>:
                                                <span class="text-emerald-400 font-bold">{{ $allocation->quantity }} {{ $item->unit }}</span>
                                            </div>
                                        @empty
                                            <span class="text-xs text-slate-600">Chưa phân bổ</span>
                                        @endforelse
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <details class="text-left">
                                            <summary class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white text-xs font-bold border border-indigo-500/20">
                                                <i class="fa-regular fa-pen-to-square"></i> Sửa
                                            </summary>
                                            <form action="{{ route('admin.equipment.update', $item->id) }}" method="POST" class="secure-form mt-3 p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3 min-w-[320px]" data-watch="equipment-update-{{ $item->id }}">
                                                @csrf
                                                <input type="hidden" name="version" value="{{ $item->version }}">
                                                <div class="grid grid-cols-2 gap-3">
                                                    <input name="code" data-text required maxlength="50" value="{{ $item->code }}" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200">
                                                    <input name="unit" data-text required maxlength="30" value="{{ $item->unit }}" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200">
                                                </div>
                                                <input name="name" data-text required maxlength="150" value="{{ $item->name }}" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200">
                                                <input name="total_quantity" data-number required maxlength="7" value="{{ $item->total_quantity }}" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200" title="Không được nhỏ hơn số đã phân bổ">
                                                <textarea name="description" data-text maxlength="1000" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 resize-none">{{ $item->description }}</textarea>
                                                <div class="flex gap-2">
                                                    <button type="submit" class="submit-btn flex-1 px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold">Cập nhật</button>
                                                </div>
                                            </form>
                                            @if($isLandlord)
                                                <form action="{{ route('admin.equipment.destroy', $item->id) }}" method="POST" class="secure-form mt-2" data-watch="equipment-delete-{{ $item->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="submit-btn w-full px-3 py-2 rounded-lg bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white text-xs font-bold" data-confirm="Xóa thiết bị {{ $item->name }} khỏi danh mục?">
                                                        Xóa thiết bị
                                                    </button>
                                                </form>
                                            @endif
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">
                                        <div class="flex flex-col items-center gap-3">
                                            <i class="fa-solid fa-box-open text-2xl text-slate-700"></i>
                                            <span>Chưa có thiết bị nào trong danh mục.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $equipment->links() }}
                </div>
            </section>

            <section class="glass-card rounded-2xl p-6">
                <h3 class="text-base font-bold text-slate-200 mb-4">Chi Tiết Phân Bổ Gần Đây</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse($allocations as $allocation)
                        <div class="rounded-xl bg-slate-950/40 border border-slate-800 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-bold text-slate-200">P.{{ $allocation->room->room_number ?? 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $allocation->room->building->name ?? 'N/A' }}</div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-1 rounded-full">
                                    {{ $allocation->quantity }} {{ $allocation->equipment->unit ?? '' }}
                                </span>
                            </div>
                            <div class="mt-3 text-xs text-indigo-300 font-bold">{{ $allocation->equipment->name ?? 'Thiết bị' }}</div>
                            <div class="mt-1 text-[11px] text-slate-500">Mã: {{ $allocation->equipment->code ?? 'N/A' }}</div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-500">Chưa có thiết bị nào được phân bổ cho phòng.</div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>

    <div id="confirm-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/70 backdrop-blur-sm px-4">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-[#0d121f] p-6 shadow-2xl shadow-black/30">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-100">Xác nhận thao tác</h3>
                    <p id="confirm-message" class="mt-2 text-sm leading-6 text-slate-400"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="confirm-cancel" class="px-4 py-2 rounded-xl border border-slate-700 text-xs font-bold text-slate-300 hover:bg-slate-800 transition-all">
                    Hủy
                </button>
                <button type="button" id="confirm-accept" class="px-4 py-2 rounded-xl bg-rose-600 text-xs font-bold text-white hover:bg-rose-500 transition-all">
                    Xóa
                </button>
            </div>
        </div>
    </div>

    <script>
        window.__trustedSubmit = false;
        let pendingConfirmForm = null;

        function notify(message, type = 'info') {
            const stack = document.getElementById('toast-stack');
            if (!stack) return;

            const styles = {
                success: ['fa-circle-check', 'text-emerald-300', 'bg-emerald-500/10', 'border-emerald-500/20'],
                warning: ['fa-triangle-exclamation', 'text-amber-300', 'bg-amber-500/10', 'border-amber-500/20'],
                error: ['fa-circle-exclamation', 'text-rose-300', 'bg-rose-500/10', 'border-rose-500/20'],
                info: ['fa-circle-info', 'text-indigo-300', 'bg-indigo-500/10', 'border-indigo-500/20'],
            };
            const [icon, color, bg, border] = styles[type] || styles.info;
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-start gap-3 rounded-2xl border ${border} ${bg} bg-[#0d121f]/95 p-4 text-sm text-slate-200 shadow-xl shadow-black/20 backdrop-blur transition-all duration-200`;
            toast.innerHTML = `
                <i class="fa-solid ${icon} ${color} mt-0.5"></i>
                <div class="flex-1 leading-5"></div>
                <button type="button" class="text-slate-500 hover:text-slate-200 transition-colors" aria-label="Đóng thông báo">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            toast.querySelector('div').textContent = message;

            const close = () => {
                toast.classList.add('opacity-0', 'translate-x-3');
                setTimeout(() => toast.remove(), 180);
            };
            toast.querySelector('button').addEventListener('click', close);
            stack.appendChild(toast);
            setTimeout(close, 4200);
        }

        function openConfirm(message, form) {
            pendingConfirmForm = form;
            document.getElementById('confirm-message').textContent = message;
            document.getElementById('confirm-modal').classList.remove('hidden');
            document.getElementById('confirm-modal').classList.add('flex');
        }

        function closeConfirm() {
            pendingConfirmForm = null;
            document.getElementById('confirm-modal').classList.add('hidden');
            document.getElementById('confirm-modal').classList.remove('flex');
        }

        function normalizeSpaces(value, trimValue = true) {
            const normalized = (value || '').replace(/\u3000/g, ' ').replace(/ã€€/g, ' ');
            return trimValue ? normalized.trim() : normalized;
        }

        function toHalfWidthNumber(value) {
            const full = '０１２３４５６７８９ï¼ï¼‘ï¼’ï¼“ï¼”ï¼•ï¼–ï¼—ï¼˜ï¼™';
            const half = '01234567890123456789';
            return normalizeSpaces(value).replace(/[０-９ï¼-ï¼™]/g, char => half[full.indexOf(char)] || char);
        }

        function stripTags(value) {
            return value.replace(/<\/?[^>]+(>|$)/g, '');
        }

        function showFieldError(input, message) {
            const error = input.parentElement.querySelector('.field-error');
            if (!error) return;
            error.textContent = message;
            error.classList.remove('hidden');
        }

        function clearFieldError(input) {
            const error = input.parentElement.querySelector('.field-error');
            if (error) error.classList.add('hidden');
        }

        function validateInput(input, options = {}) {
            const shouldTrim = options.trim !== false;

            if (input.dataset.number !== undefined) {
                input.value = toHalfWidthNumber(input.value).replace(/[^0-9]/g, '');
                if (input.required && input.value === '') {
                    showFieldError(input, 'Vui lòng nhập số lượng hợp lệ.');
                    return false;
                }
                clearFieldError(input);
                return true;
            }

            if (input.dataset.text !== undefined) {
                input.value = stripTags(normalizeSpaces(input.value, shouldTrim));
                if (input.required && input.value.trim() === '') {
                    showFieldError(input, 'Trường này không được bỏ trống.');
                    return false;
                }
                if (input.maxLength > 0 && input.value.length > input.maxLength) {
                    showFieldError(input, `Không được vượt quá ${input.maxLength} ký tự.`);
                    return false;
                }
                clearFieldError(input);
                return true;
            }

            return true;
        }

        document.querySelectorAll('[data-number], [data-text]').forEach(input => {
            input.addEventListener('input', () => validateInput(input, { trim: false }));
            input.addEventListener('blur', () => validateInput(input));
        });

        document.querySelectorAll('.secure-form').forEach(form => {
            form.addEventListener('submit', event => {
                const confirmButton = event.submitter?.dataset.confirm ? event.submitter : null;
                if (confirmButton && !form.dataset.confirmed) {
                    event.preventDefault();
                    openConfirm(confirmButton.dataset.confirm, form);
                    return false;
                }

                let isValid = true;
                form.querySelectorAll('[data-number], [data-text]').forEach(input => {
                    isValid = validateInput(input) && isValid;
                });

                if (!isValid) {
                    event.preventDefault();
                    notify('Vui lòng kiểm tra lại dữ liệu trước khi lưu.', 'warning');
                    return false;
                }

                // Chống spam click: khóa nút ngay khi form hợp lệ để không tạo giao dịch trùng.
                window.__trustedSubmit = true;
                form.querySelectorAll('.submit-btn').forEach(button => {
                    button.disabled = true;
                    button.classList.add('opacity-60', 'cursor-not-allowed');
                    button.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang xử lý...';
                });
            });

            // Chỉ cảnh báo thay đổi nhạy cảm trên input ẩn; spinner/toast và trạng thái nút là cập nhật UI hợp lệ.
            const observer = new MutationObserver(mutations => {
                if (window.__trustedSubmit) return;
                const tampered = mutations.some(mutation => {
                    if (mutation.type !== 'attributes') return false;
                    const target = mutation.target;
                    return target.matches('input[type="hidden"]') && ['name', 'value', 'type'].includes(mutation.attributeName);
                });
                if (tampered) {
                    notify('Phát hiện thay đổi bất thường trên dữ liệu ẩn. Vui lòng tải lại trang trước khi thao tác tiếp.', 'error');
                }
            });
            observer.observe(form, { attributes: true, subtree: true });
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'F12' || event.keyCode === 123) {
                event.preventDefault();
                notify('Phím tắt này đã được tắt trong khu vực quản lý thiết bị.', 'warning');
            }
            if (event.ctrlKey && event.shiftKey && ['I', 'J', 'C'].includes(event.key.toUpperCase())) {
                event.preventDefault();
                notify('Phím tắt này đã được tắt trong khu vực quản lý thiết bị.', 'warning');
            }
            if (event.ctrlKey && event.key.toUpperCase() === 'U') {
                event.preventDefault();
                notify('Phím tắt này đã được tắt trong khu vực quản lý thiết bị.', 'warning');
            }
        });

        document.addEventListener('contextmenu', event => {
            event.preventDefault();
            notify('Chuột phải đã được tắt trong khu vực quản lý thiết bị.', 'info');
        });

        document.getElementById('confirm-cancel').addEventListener('click', closeConfirm);
        document.getElementById('confirm-modal').addEventListener('click', event => {
            if (event.target.id === 'confirm-modal') closeConfirm();
        });
        document.getElementById('confirm-accept').addEventListener('click', () => {
            if (!pendingConfirmForm) return;
            pendingConfirmForm.dataset.confirmed = 'true';
            pendingConfirmForm.requestSubmit();
        });

        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarCollapsedKey = 'smartroom.sidebar.collapsed';

        function setSidebarCollapsed(collapsed) {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            localStorage.setItem(sidebarCollapsedKey, collapsed ? '1' : '0');
        }

        setSidebarCollapsed(localStorage.getItem(sidebarCollapsedKey) === '1');
        sidebarToggle?.addEventListener('click', () => {
            setSidebarCollapsed(!document.body.classList.contains('sidebar-collapsed'));
        });
    </script>
</body>
</html>
