<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thanh Toán - SmartRoom</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        .panel {
            background: rgba(13, 18, 31, 0.72);
            border: 1px solid rgba(30, 41, 59, 0.86);
        }
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
                <a href="{{ route('admin.payments.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-indigo-400 bg-indigo-500/10 border border-indigo-500/10 transition-all">
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
            </nav>
        </div>

        <div class="p-4 border-t border-slate-900">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/50 border border-slate-800/40">
                <div class="w-9 h-9 rounded-lg bg-indigo-900/50 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400 text-sm">
                    {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-xs font-bold text-slate-200 truncate">{{ Auth::user()->name ?? 'Người dùng' }}</h4>
                    <p class="text-[10px] text-slate-500 truncate">{{ optional(Auth::user()->role()->first())->name ?? 'Quản trị viên' }}</p>
                </div>
            </div>
            <a href="{{ route('signout') }}" class="mt-3 w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất
            </a>
        </div>
    </aside>

    <div class="flex-grow flex flex-col h-screen overflow-y-auto">
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div>
                <h1 class="text-lg font-bold text-slate-100">Quản Lý Thanh Toán</h1>
                <p class="text-xs text-slate-500 mt-0.5">Theo dõi trạng thái hóa đơn, lịch sử phòng và doanh thu.</p>
            </div>
            <a href="{{ route('admin.payments.export', request()->query()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-file-export"></i> Xuất CSV
            </a>
        </header>

        <main class="p-8 flex-grow overflow-y-auto space-y-6">
            @if(session('success'))
                <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-4 py-3 text-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 px-4 py-3 text-sm font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Chưa gửi</div>
                    <div class="mt-2 text-3xl font-extrabold text-slate-100">{{ number_format($summary['draft_count']) }}</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Đã gửi</div>
                    <div class="mt-2 text-3xl font-extrabold text-indigo-300">{{ number_format($summary['sent_count']) }}</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Đã thanh toán</div>
                    <div class="mt-2 text-3xl font-extrabold text-emerald-300">{{ number_format($summary['paid_count']) }}</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Quá hạn</div>
                    <div class="mt-2 text-3xl font-extrabold text-rose-300">{{ number_format($summary['overdue_count']) }}</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Đã thu</div>
                    <div class="mt-2 text-2xl font-extrabold text-emerald-300">{{ number_format($summary['paid_total']) }}đ</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Chưa thu</div>
                    <div class="mt-2 text-2xl font-extrabold text-amber-300">{{ number_format($summary['unpaid_total']) }}đ</div>
                </div>
            </section>

            <section class="panel glass-card rounded-2xl p-6">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-8 gap-3">
                    <input type="month" name="billing_month" value="{{ $filters['billing_month'] }}" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                    <input type="month" name="from_month" value="{{ $filters['from_month'] }}" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500" title="Từ tháng">
                    <input type="month" name="to_month" value="{{ $filters['to_month'] }}" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500" title="Đến tháng">
                    <select name="status" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="">Tất cả trạng thái</option>
                        @foreach($statusLabels as $value => $meta)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                    <select name="payment_method" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="">Mọi phương thức</option>
                        @foreach($paymentMethods as $value => $label)
                            <option value="{{ $value }}" @selected($filters['payment_method'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="room_id" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="">Tất cả phòng</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" @selected($filters['room_id'] === $room->id)>Phòng {{ $room->room_number }}</option>
                        @endforeach
                    </select>
                    <select name="report_period" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="month" @selected($filters['report_period'] === 'month')>Theo tháng</option>
                        <option value="quarter" @selected($filters['report_period'] === 'quarter')>Theo quý</option>
                        <option value="year" @selected($filters['report_period'] === 'year')>Theo năm</option>
                    </select>
                    <div class="flex gap-2">
                        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Phòng, tên, SĐT" class="min-w-0 flex-1 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                    </div>
                </form>
            </section>

            <section class="panel glass-card rounded-2xl p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-indigo-400"></i> Danh Sách Thanh Toán
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Cập nhật trạng thái, ngày thanh toán và phương thức nhận tiền.</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-900">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                            <tr>
                                <th class="px-4 py-4 font-bold">Tháng</th>
                                <th class="px-4 py-4 font-bold">Phòng</th>
                                <th class="px-4 py-4 font-bold">Người thuê</th>
                                <th class="px-4 py-4 font-bold">Chi phí</th>
                                <th class="px-4 py-4 font-bold">Tổng tiền</th>
                                <th class="px-4 py-4 font-bold">Trạng thái</th>
                                <th class="px-4 py-4 font-bold">Thanh toán</th>
                                <th class="px-4 py-4 font-bold">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                            @forelse($paymentRows as $row)
                                <tr class="hover:bg-slate-900/40 transition-all">
                                    <td class="px-4 py-4 font-bold text-indigo-300">{{ $row->billing_month }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-slate-200">Phòng {{ $row->room_number }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $row->building_name }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-400">{{ $row->resident_name }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-400">
                                        <div>Phòng: {{ number_format($row->room_amount) }}đ</div>
                                        <div>Điện: {{ number_format($row->electricity_amount) }}đ / {{ number_format($row->electricity_usage) }} kWh</div>
                                        <div>Nước: {{ number_format($row->water_amount) }}đ / {{ number_format($row->water_usage) }} m3</div>
                                        <div>Dịch vụ: {{ number_format($row->service_amount) }}đ</div>
                                    </td>
                                    <td class="px-4 py-4 font-extrabold text-slate-100">{{ number_format($row->total_amount) }}đ</td>
                                    <td class="px-4 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $row->status_class }}">{{ $row->status_label }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-400">
                                        @if($row->payment_date)
                                            <div class="font-bold text-emerald-300">{{ $row->payment_date->format('d/m/Y') }}</div>
                                            <div>{{ $paymentMethods[$row->payment_method] ?? 'Khác' }}</div>
                                        @else
                                            <span class="text-slate-500">Chưa ghi nhận</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 min-w-[260px]">
                                        @if($row->id)
                                            <form method="POST" action="{{ route('admin.payments.update', $row->id) }}" class="grid grid-cols-2 gap-2">
                                                @csrf
                                                <select name="status" class="col-span-2 px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                                                    <option value="sent" @selected($row->status === 'sent')>Đã gửi</option>
                                                    <option value="paid" @selected($row->status === 'paid')>Đã thanh toán</option>
                                                    <option value="overdue" @selected($row->status === 'overdue')>Quá hạn</option>
                                                </select>
                                                <input type="date" name="payment_date" value="{{ $row->payment_date ? $row->payment_date->format('Y-m-d') : now()->format('Y-m-d') }}" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                                                <select name="payment_method" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                                                    @foreach($paymentMethods as $value => $label)
                                                        <option value="{{ $value }}" @selected($row->payment_method === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="col-span-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold">
                                                    Cập nhật
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('smartroom.admin', ['tab' => 'utility-section']) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-bold">
                                                <i class="fa-solid fa-paper-plane"></i> Gửi hóa đơn
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-xs text-slate-500">Không có dữ liệu thanh toán phù hợp bộ lọc.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="panel glass-card rounded-2xl p-6 xl:col-span-1">
                    <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-emerald-400"></i> Doanh Thu
                    </h2>
                    <div class="mt-5 space-y-3 max-h-[480px] overflow-y-auto pr-1">
                        @forelse($revenueReports as $row)
                            <div class="rounded-xl bg-slate-950/50 border border-slate-800 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-bold text-slate-200">{{ $row['label'] }}</div>
                                        <div class="text-[11px] text-slate-500">{{ number_format($row['paid_count']) }} hóa đơn đã thanh toán</div>
                                    </div>
                                    <div class="text-sm font-extrabold text-emerald-300">{{ number_format($row['total_revenue']) }}đ</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500">Chưa có doanh thu trong phạm vi đang xem.</div>
                        @endforelse
                    </div>
                </div>

                <div class="panel glass-card rounded-2xl p-6 xl:col-span-2">
                    <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-amber-400"></i> Lịch Sử Thanh Toán Theo Phòng
                    </h2>
                    <div class="mt-5 space-y-4 max-h-[520px] overflow-y-auto pr-1">
                        @forelse($histories as $roomHistory)
                            <div class="rounded-xl bg-slate-950/50 border border-slate-800 p-4">
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <div class="text-sm font-extrabold text-slate-200">Phòng {{ $roomHistory->first()->room_number }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $roomHistory->first()->building_name }}</div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs text-slate-400">
                                        <thead class="text-[10px] uppercase text-slate-500 border-b border-slate-800">
                                            <tr>
                                                <th class="py-2 pr-3">Tháng</th>
                                                <th class="py-2 pr-3">Tổng tiền</th>
                                                <th class="py-2 pr-3">Trạng thái</th>
                                                <th class="py-2 pr-3">Ngày thanh toán</th>
                                                <th class="py-2 pr-3">Phương thức</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-900">
                                            @foreach($roomHistory as $item)
                                                <tr>
                                                    <td class="py-2 pr-3 font-bold text-indigo-300">{{ $item->billing_month }}</td>
                                                    <td class="py-2 pr-3 font-bold text-slate-200">{{ number_format($item->total_amount) }}đ</td>
                                                    <td class="py-2 pr-3">
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $item->status_class }}">{{ $item->status_label }}</span>
                                                    </td>
                                                    <td class="py-2 pr-3">{{ $item->payment_date ? $item->payment_date->format('d/m/Y') : '-' }}</td>
                                                    <td class="py-2 pr-3">{{ $paymentMethods[$item->payment_method] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500">Chưa có lịch sử thanh toán.</div>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
