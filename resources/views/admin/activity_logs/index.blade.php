<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Vận Hành - SmartRoom</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .panel { background: rgba(13, 18, 31, 0.72); border: 1px solid rgba(30, 41, 59, 0.86); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #080b11; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen flex selection:bg-indigo-500 selection:text-white overflow-hidden">
    <aside class="w-64 bg-[#0d121f] border-r border-slate-900 flex flex-col justify-between h-screen shrink-0">
        <div>
            <div class="p-6 border-b border-slate-900">
                <a href="{{ route('smartroom.admin') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid fa-hotel text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-extrabold tracking-tight text-slate-100">SmartRoom</span>
                </a>
            </div>

            <nav class="p-4 space-y-1">
                <a href="{{ route('smartroom.admin') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                    <i class="fa-solid fa-chart-pie text-lg"></i>
                    <span>Tổng Quan</span>
                </a>
                <a href="{{ route('admin.rooms.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                    <i class="fa-solid fa-door-open text-lg"></i>
                    <span>Quản Lý Phòng</span>
                </a>
                <a href="{{ route('admin.payments.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                    <i class="fa-solid fa-money-check-dollar text-lg"></i>
                    <span>Thanh Toán</span>
                </a>
                <a href="{{ route('admin.equipment.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    <span>Thiết Bị</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                    <i class="fa-solid fa-chart-column text-lg"></i>
                    <span>Báo Cáo</span>
                </a>
                <a href="{{ route('admin.activity_logs.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-indigo-400 bg-indigo-500/10 border border-indigo-500/10 transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    <span>Lịch Sử Vận Hành</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-900">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/50 border border-slate-800/40">
                <div class="w-9 h-9 rounded-lg bg-indigo-900/50 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400 text-sm">
                    {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-xs font-bold text-slate-200 truncate">{{ Auth::user()->name ?? 'Người dùng' }}</h4>
                    <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->roleName() }}</p>
                </div>
            </div>
            <a href="{{ route('signout') }}" class="mt-3 w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất
            </a>
        </div>
    </aside>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div>
                <h1 class="text-lg font-bold text-slate-100">Lịch Sử Vận Hành</h1>
                <p class="text-xs text-slate-500 mt-0.5">Tra cứu các sự kiện đã xảy ra với phòng, cư dân, hóa đơn, hợp đồng và thiết bị.</p>
            </div>
            <div class="text-xs font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-4 py-2 rounded-xl">
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </header>

        <div class="p-8 space-y-6">
            <section class="panel rounded-xl p-6">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 text-xs font-bold border border-indigo-500/20">
                            <i class="fa-solid fa-shield-halved"></i>
                            Nhật ký vận hành nhà trọ
                        </div>
                        <h2 class="mt-4 text-2xl font-extrabold text-slate-100">Những việc đã phát sinh trong nhà trọ</h2>
                        <p class="mt-2 text-sm text-slate-400 max-w-2xl">Dùng để xem lại lịch sử của phòng/cư dân: chốt điện nước, thanh toán, hợp đồng, bàn giao thiết bị, thêm hoặc xóa dữ liệu.</p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 min-w-full xl:min-w-[520px]">
                        <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-4">
                            <div class="text-[11px] text-slate-500 font-bold uppercase">Sự kiện</div>
                            <div class="mt-1 text-2xl font-extrabold">{{ number_format($summary['total']) }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-4">
                            <div class="text-[11px] text-slate-500 font-bold uppercase">Hôm nay</div>
                            <div class="mt-1 text-2xl font-extrabold text-indigo-300">{{ number_format($summary['today']) }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-4">
                            <div class="text-[11px] text-slate-500 font-bold uppercase">Điều chỉnh</div>
                            <div class="mt-1 text-2xl font-extrabold text-amber-300">{{ number_format($summary['updates']) }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-4">
                            <div class="text-[11px] text-slate-500 font-bold uppercase">Đã hủy/xóa</div>
                            <div class="mt-1 text-2xl font-extrabold text-rose-300">{{ number_format($summary['deletes']) }}</div>
                        </div>
                    </div>
                </div>
                @if($moduleSummary->isNotEmpty())
                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ route('admin.activity_logs.index', request()->except('module', 'page')) }}" class="px-3 py-2 rounded-lg text-xs font-bold border {{ empty($filters['module']) ? 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:text-slate-100' }}">
                            Tất cả nhóm
                        </a>
                        @foreach($moduleSummary as $item)
                            <a href="{{ route('admin.activity_logs.index', array_merge(request()->except('page'), ['module' => $item['module']])) }}" class="px-3 py-2 rounded-lg text-xs font-bold border {{ $filters['module'] === $item['module'] ? 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:text-slate-100' }}">
                                {{ $item['label'] }} · {{ $item['count'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-[320px_minmax(0,1fr)] gap-6 items-start">
                <aside class="panel rounded-xl p-5 sticky top-24">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-extrabold text-slate-100">Bộ lọc</h3>
                        <a href="{{ route('admin.activity_logs.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-100">Xóa lọc</a>
                    </div>

                    <form method="GET" class="mt-5 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tìm phòng/cư dân</label>
                            <input name="q" value="{{ $filters['q'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="VD: phòng 101, tên cư dân">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Lọc theo phòng</label>
                            <select name="room_number" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="">Tất cả phòng</option>
                                @foreach($roomOptions as $roomNumber)
                                    <option value="{{ $roomNumber }}" @selected($filters['room_number'] === $roomNumber)>Phòng {{ $roomNumber }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Loại sự kiện</label>
                            <select name="action" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="">Tất cả sự kiện</option>
                                @foreach($actionLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['action'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nhóm dữ liệu</label>
                            <select name="module" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="">Tất cả nhóm</option>
                                @foreach($moduleLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['module'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Từ ngày</label>
                                <input type="date" name="from_date" value="{{ $filters['from_date'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Đến ngày</label>
                                <input type="date" name="to_date" value="{{ $filters['to_date'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>
                        <button class="w-full px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold">Áp dụng bộ lọc</button>
                    </form>
                </aside>

                <section class="space-y-3">
                    @forelse($logs as $log)
                        @php
                            $meta = $log->metadata ?? [];
                            $roomNumber = $meta['room_number'] ?? null;
                            $residentName = $meta['resident_name'] ?? null;
                            $billingMonth = $meta['billing_month'] ?? null;
                            $equipmentName = $meta['equipment_name'] ?? null;
                            $paymentMethod = $meta['payment_method'] ?? null;
                            $actionMeta = match ($log->action) {
                                'delete' => ['class' => 'bg-rose-500/10 text-rose-300 border-rose-500/20', 'icon' => 'fa-trash'],
                                'update', 'payment' => ['class' => 'bg-amber-500/10 text-amber-300 border-amber-500/20', 'icon' => 'fa-pen-to-square'],
                                'create' => ['class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20', 'icon' => 'fa-plus'],
                                'allocate', 'recover' => ['class' => 'bg-sky-500/10 text-sky-300 border-sky-500/20', 'icon' => 'fa-right-left'],
                                default => ['class' => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20', 'icon' => 'fa-bell'],
                            };
                        @endphp
                        <article class="panel rounded-xl p-5 hover:border-indigo-500/40 transition-all">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex gap-4 min-w-0">
                                    <div class="w-11 h-11 rounded-xl border {{ $actionMeta['class'] }} flex items-center justify-center shrink-0">
                                        <i class="fa-solid {{ $actionMeta['icon'] }}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($roomNumber)
                                                <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 text-xs font-bold">Phòng {{ $roomNumber }}</span>
                                            @endif
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 text-xs font-bold">{{ $moduleLabels[$log->module] ?? $log->module }}</span>
                                            <span class="px-2.5 py-1 rounded-lg border text-xs font-bold {{ $actionMeta['class'] }}">{{ $actionLabels[$log->action] ?? $log->action }}</span>
                                        </div>
                                        <h3 class="mt-3 text-base font-extrabold text-slate-100 leading-snug">{{ $log->description }}</h3>
                                        @if($residentName || $billingMonth || $equipmentName || $paymentMethod)
                                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                                @if($residentName)
                                                    <span class="px-2.5 py-1 rounded-lg bg-slate-950/70 border border-slate-800 text-slate-300">Cư dân: {{ $residentName }}</span>
                                                @endif
                                                @if($billingMonth)
                                                    <span class="px-2.5 py-1 rounded-lg bg-slate-950/70 border border-slate-800 text-slate-300">Kỳ: {{ $billingMonth }}</span>
                                                @endif
                                                @if($equipmentName)
                                                    <span class="px-2.5 py-1 rounded-lg bg-slate-950/70 border border-slate-800 text-slate-300">Thiết bị: {{ $equipmentName }}</span>
                                                @endif
                                                @if($paymentMethod)
                                                    <span class="px-2.5 py-1 rounded-lg bg-slate-950/70 border border-slate-800 text-slate-300">Thanh toán: {{ strtoupper($paymentMethod) }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                            <span><i class="fa-solid fa-user-gear mr-1 text-slate-600"></i>Người cập nhật: {{ $log->user_name ?? 'Hệ thống' }}</span>
                                            <span><i class="fa-regular fa-clock mr-1 text-slate-600"></i>{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                            @if($log->ip_address)
                                                <span><i class="fa-solid fa-location-dot mr-1 text-slate-600"></i>{{ $log->ip_address }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-slate-500 lg:text-right shrink-0">
                                    <div>{{ $log->created_at->diffForHumans() }}</div>
                                    @if($log->subject_id)
                                        <div class="mt-1 font-mono">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="panel rounded-xl p-12 text-center">
                            <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500">
                                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                            </div>
                            <h3 class="mt-5 text-lg font-extrabold text-slate-100">Chưa có lịch sử vận hành</h3>
                            <p class="mt-2 text-sm text-slate-500">Khi phát sinh chốt điện nước, thanh toán, hợp đồng, cư dân hoặc thiết bị, hệ thống sẽ ghi lại tại đây.</p>
                        </div>
                    @endforelse

                    <div class="pt-2">
                        {{ $logs->links() }}
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
