<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Báo cáo thống kê thiết bị, tồn kho và công nợ phòng trọ - SmartRoom.">
    <title>Báo Cáo - Thống Kê - SmartRoom</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                <a href="{{ route('admin.equipment.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    <span>Thiết Bị</span>
                </a>
                <a href="{{ route('admin.payments.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
                    <i class="fa-solid fa-money-check-dollar text-lg"></i>
                    <span>Thanh Toán</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-indigo-400 bg-indigo-500/10 border border-indigo-500/10 transition-all">
                    <i class="fa-solid fa-chart-column text-lg"></i>
                    <span>Báo Cáo</span>
                </a>
                <a href="{{ route('admin.activity_logs.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all">
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

    <div class="flex-grow flex flex-col h-screen overflow-y-auto">
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div>
                <h1 class="text-lg font-bold text-slate-100">Báo Cáo - Thống Kê</h1>
                <p class="text-xs text-slate-500 mt-0.5">Tổng hợp thiết bị, phân bổ và phòng còn nợ tiền điện nước, dịch vụ.</p>
            </div>
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2">
                <input type="month" name="billing_month" value="{{ $billingMonth }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i> Lọc
                </button>
                @if($billingMonth)
                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-bold">Tất cả</a>
                @endif
            </form>
        </header>

        <main class="p-8 flex-grow overflow-y-auto space-y-6">
            <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Tổng thiết bị</div>
                    <div class="mt-2 text-3xl font-extrabold text-slate-100">{{ number_format($summary['equipment_total']) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500">Số lượng đang quản lý</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Đã phân bổ</div>
                    <div class="mt-2 text-3xl font-extrabold text-amber-400">{{ number_format($summary['equipment_allocated']) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500">Thiết bị đang ở phòng</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Còn tồn</div>
                    <div class="mt-2 text-3xl font-extrabold text-emerald-400">{{ number_format($summary['equipment_stock']) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500">Có thể bàn giao tiếp</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Phòng còn nợ</div>
                    <div class="mt-2 text-3xl font-extrabold text-rose-400">{{ number_format($summary['unpaid_room_count']) }}</div>
                    <div class="mt-1 text-[11px] text-slate-500">Theo hóa đơn chưa thanh toán</div>
                </div>
                <div class="panel glass-card rounded-2xl p-5">
                    <div class="text-xs font-bold text-slate-500 uppercase">Tổng công nợ</div>
                    <div class="mt-2 text-2xl font-extrabold text-indigo-300">{{ number_format($summary['unpaid_total_amount']) }}đ</div>
                    <div class="mt-1 text-[11px] text-slate-500">Gồm phòng, điện, nước, dịch vụ</div>
                </div>
            </section>

            <section class="panel glass-card rounded-2xl p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-indigo-400"></i> Tình Hình Thiết Bị
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Theo dõi tổng số lượng, đã phân bổ và số còn tồn của từng thiết bị.</p>
                    </div>
                    <a href="{{ route('admin.equipment.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-bold flex items-center gap-2">
                        <i class="fa-solid fa-screwdriver-wrench"></i> Quản lý thiết bị
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-900">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                            <tr>
                                <th class="px-4 py-4 font-bold">Mã</th>
                                <th class="px-4 py-4 font-bold">Tên thiết bị</th>
                                <th class="px-4 py-4 font-bold">Tổng</th>
                                <th class="px-4 py-4 font-bold">Đã phân bổ</th>
                                <th class="px-4 py-4 font-bold">Còn tồn</th>
                                <th class="px-4 py-4 font-bold">Số phòng dùng</th>
                                <th class="px-4 py-4 font-bold">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                            @forelse($equipmentReports as $item)
                                <tr class="hover:bg-slate-900/40 transition-all">
                                    <td class="px-4 py-4 font-bold text-indigo-400">{{ $item['code'] }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-slate-200">{{ $item['name'] }}</div>
                                        <div class="text-[11px] text-slate-500">Đơn vị: {{ $item['unit'] }}</div>
                                    </td>
                                    <td class="px-4 py-4 font-bold">{{ number_format($item['total_quantity']) }}</td>
                                    <td class="px-4 py-4 font-bold text-amber-400">{{ number_format($item['allocated_quantity']) }}</td>
                                    <td class="px-4 py-4 font-bold text-emerald-400">{{ number_format($item['stock_quantity']) }}</td>
                                    <td class="px-4 py-4">{{ number_format($item['using_rooms_count']) }}</td>
                                    <td class="px-4 py-4">
                                        @if($item['stock_quantity'] > 0)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Còn tồn</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Hết tồn</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">Chưa có thiết bị để thống kê.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="panel glass-card rounded-2xl p-6 xl:col-span-1">
                    <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-right-left text-emerald-400"></i> Phân Bổ Theo Phòng
                    </h2>
                    <div class="mt-5 space-y-3 max-h-[520px] overflow-y-auto pr-1">
                        @forelse($allocationReports as $allocation)
                            <div class="glass-card rounded-xl bg-slate-950/50 border border-slate-800 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-bold text-slate-200">Phòng {{ $allocation->room->room_number ?? 'N/A' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $allocation->room->building->name ?? 'Chưa có tòa nhà' }}</div>
                                    </div>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ number_format($allocation->quantity) }} {{ $allocation->equipment->unit ?? '' }}
                                    </span>
                                </div>
                                <div class="mt-3 text-xs font-bold text-indigo-300">{{ $allocation->equipment->name ?? 'Thiết bị' }}</div>
                                <div class="mt-1 text-[11px] text-slate-500">Mã: {{ $allocation->equipment->code ?? 'N/A' }}</div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500">Chưa có thiết bị nào được phân bổ cho phòng.</div>
                        @endforelse
                    </div>
                </div>

                <div class="panel glass-card rounded-2xl p-6 xl:col-span-2">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <h2 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-file-invoice-dollar text-rose-400"></i> Phòng Còn Nợ Điện Nước Và Dịch Vụ
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">
                                @if($billingMonth)
                                    Đang xem công nợ tháng {{ $billingMonth }}.
                                @else
                                    Đang xem toàn bộ hóa đơn chưa thanh toán.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                                <tr>
                                    <th class="px-4 py-4 font-bold">Tháng</th>
                                    <th class="px-4 py-4 font-bold">Phòng</th>
                                    <th class="px-4 py-4 font-bold">Người thuê</th>
                                    <th class="px-4 py-4 font-bold">Điện</th>
                                    <th class="px-4 py-4 font-bold">Nước</th>
                                    <th class="px-4 py-4 font-bold">Dịch vụ</th>
                                    <th class="px-4 py-4 font-bold">Tổng nợ</th>
                                    <th class="px-4 py-4 font-bold">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                                @forelse($unpaidUtilities as $record)
                                    <tr class="hover:bg-slate-900/40 transition-all">
                                        <td class="px-4 py-4 font-bold text-indigo-300">{{ $record->billing_month }}</td>
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-slate-200">Phòng {{ $record->room->room_number ?? 'N/A' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $record->room->building->name ?? 'Chưa có tòa nhà' }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-xs text-slate-400">
                                            {{ optional($record->room->residents->first())->name ?? 'Chưa gán cư dân' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-slate-200">{{ number_format($record->electricity_amount) }}đ</div>
                                            <div class="text-[11px] text-slate-500">{{ number_format($record->electricity_usage) }} kWh</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-slate-200">{{ number_format($record->water_amount) }}đ</div>
                                            <div class="text-[11px] text-slate-500">{{ number_format($record->water_usage) }} m3</div>
                                        </td>
                                        <td class="px-4 py-4 font-bold">{{ number_format($record->service_amount) }}đ</td>
                                        <td class="px-4 py-4 font-extrabold text-rose-400">{{ number_format($record->total_amount) }}đ</td>
                                        <td class="px-4 py-4">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                {{ $record->status === 'overdue' ? 'Quá hạn' : 'Đã gửi' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-xs text-slate-500">Không có phòng còn nợ trong phạm vi đang xem.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
