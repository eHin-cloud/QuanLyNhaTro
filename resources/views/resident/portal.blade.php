<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartRoom - Trang Cu Dan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .panel { background: rgba(15, 23, 42, .72); border: 1px solid rgba(51, 65, 85, .8); }
    </style>
</head>
<body class="min-h-screen bg-[#080b11] text-slate-100">
    <div class="min-h-screen">
        <header class="sticky top-0 z-20 border-b border-slate-900 bg-[#080b11]/90 backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
                <a href="{{ route('smartroom.portal') }}" class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white">
                        <i class="fa-solid fa-hotel"></i>
                    </span>
                    <span class="font-black tracking-tight">SmartRoom Resident</span>
                </a>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline text-xs text-slate-400">{{ Auth::user()->name ?? 'Resident' }}</span>
                    <a href="{{ route('signout') }}" class="px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-bold">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">
            @if(session('success'))
                <div class="panel rounded-xl p-4 text-sm text-emerald-300 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="panel rounded-xl p-4 text-sm text-rose-300 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="panel rounded-xl p-4 text-sm text-rose-300">
                    <div class="font-bold mb-1">Du lieu chua hop le</div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(!$room)
                <section class="panel rounded-2xl p-8 text-center">
                    <i class="fa-solid fa-user-lock text-4xl text-amber-300 mb-4"></i>
                    <h1 class="text-xl font-black">Tai khoan chua duoc gan phong</h1>
                    <p class="text-sm text-slate-400 mt-2">Vui long lien he ban quan ly de kich hoat ho so cu dan.</p>
                </section>
            @else
                <section class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                    <div class="panel rounded-2xl p-5">
                        <div class="text-xs font-bold uppercase text-slate-500">Cu dan</div>
                        <div class="mt-2 text-xl font-black">{{ $resident->name }}</div>
                        <div class="mt-1 text-xs text-slate-400">{{ $resident->phone }}</div>
                    </div>
                    <div class="panel rounded-2xl p-5">
                        <div class="text-xs font-bold uppercase text-slate-500">Phong</div>
                        <div class="mt-2 text-xl font-black text-indigo-300">P. {{ $room->room_number }}</div>
                        <div class="mt-1 text-xs text-slate-400">{{ $room->building->name ?? 'Chua co toa nha' }}</div>
                    </div>
                    <div class="panel rounded-2xl p-5">
                        <div class="text-xs font-bold uppercase text-slate-500">No can thanh toan</div>
                        <div class="mt-2 text-xl font-black text-amber-300">{{ number_format($unpaidTotal) }} VND</div>
                        <div class="mt-1 text-xs text-slate-400">{{ $bills->where('status', '!=', 'paid')->count() }} hoa don</div>
                    </div>
                    <div class="panel rounded-2xl p-5">
                        <div class="text-xs font-bold uppercase text-slate-500">Su co dang mo</div>
                        <div class="mt-2 text-xl font-black text-cyan-300">{{ $tickets->where('status', '!=', 'resolved')->count() }}</div>
                        <div class="mt-1 text-xs text-slate-400">Bao tri / sua chua</div>
                    </div>
                </section>

                <section class="panel rounded-2xl p-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="switchResidentTab('bills')" class="resident-tab px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold">Hoa don</button>
                        <button type="button" onclick="switchResidentTab('contract')" class="resident-tab px-4 py-2 rounded-xl bg-slate-900 text-slate-300 text-xs font-bold border border-slate-800">Hop dong</button>
                        <button type="button" onclick="switchResidentTab('tickets')" class="resident-tab px-4 py-2 rounded-xl bg-slate-900 text-slate-300 text-xs font-bold border border-slate-800">Su co / Bao tri</button>
                    </div>
                </section>

                <section id="resident-tab-bills" class="resident-section panel rounded-2xl p-6">
                    <div class="flex items-center justify-between gap-4 mb-5">
                        <div>
                            <h2 class="text-lg font-black">Hoa don cua toi</h2>
                            <p class="text-xs text-slate-500 mt-1">Xem chi tiet tien phong, dien, nuoc va ma QR thanh toan.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-950 text-slate-500 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Thang</th>
                                    <th class="px-4 py-3">Chi tiet</th>
                                    <th class="px-4 py-3">Tong</th>
                                    <th class="px-4 py-3">Trang thai</th>
                                    <th class="px-4 py-3 text-right">QR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900">
                                @forelse($bills as $bill)
                                    <tr class="hover:bg-slate-900/30">
                                        <td class="px-4 py-4 font-bold text-indigo-300">{{ $bill->billing_month }}</td>
                                        <td class="px-4 py-4 text-xs text-slate-400">
                                            <div>Phong: {{ number_format($bill->room_amount) }} VND</div>
                                            <div>Dien: {{ number_format($bill->electricity_amount) }} VND / {{ $bill->electricity_usage }} kWh</div>
                                            <div>Nuoc: {{ number_format($bill->water_amount) }} VND / {{ $bill->water_usage }} m3</div>
                                            <div>Dich vu: {{ number_format($bill->service_amount) }} VND</div>
                                        </td>
                                        <td class="px-4 py-4 font-black">{{ number_format($bill->total_amount) }} VND</td>
                                        <td class="px-4 py-4">
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $bill->status === 'paid' ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20' : 'bg-amber-500/10 text-amber-300 border-amber-500/20' }}">
                                                {{ $bill->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('smartroom.resident.bills.qr', $bill->id) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold">
                                                <i class="fa-solid fa-qrcode"></i> QR
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-xs text-slate-500">Chua co hoa don.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="resident-tab-contract" class="resident-section panel rounded-2xl p-6 hidden">
                    <h2 class="text-lg font-black mb-5">Hop dong cua toi</h2>
                    @if($contract)
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="rounded-xl bg-slate-950/40 border border-slate-800 p-4">
                                <div class="text-xs text-slate-500 font-bold uppercase">Ma hop dong</div>
                                <div class="mt-2 font-black">{{ $contract->contract_code }}</div>
                            </div>
                            <div class="rounded-xl bg-slate-950/40 border border-slate-800 p-4">
                                <div class="text-xs text-slate-500 font-bold uppercase">Thoi han</div>
                                <div class="mt-2 font-black">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}</div>
                            </div>
                            <div class="rounded-xl bg-slate-950/40 border border-slate-800 p-4">
                                <div class="text-xs text-slate-500 font-bold uppercase">Dat coc</div>
                                <div class="mt-2 font-black">{{ number_format($contract->deposit) }} VND</div>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl bg-slate-950/40 border border-slate-800 p-4">
                            <div class="text-xs text-slate-500 font-bold uppercase mb-2">Dieu khoan</div>
                            <div class="text-sm text-slate-300 whitespace-pre-line">{{ $contract->terms }}</div>
                        </div>
                        <a href="{{ route('smartroom.contract.sign_view', $contract->id) }}" target="_blank" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem / ky hop dong
                        </a>
                    @else
                        <div class="text-sm text-slate-500">Chua co hop dong.</div>
                    @endif
                </section>

                <section id="resident-tab-tickets" class="resident-section panel rounded-2xl p-6 hidden">
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        <form method="POST" action="{{ route('smartroom.resident.tickets.store') }}" enctype="multipart/form-data" class="xl:col-span-1 rounded-xl bg-slate-950/40 border border-slate-800 p-4 space-y-3" onsubmit="return disableSubmit(this)">
                            @csrf
                            <h2 class="text-lg font-black">Gui yeu cau sua chua</h2>
                            <input name="title" maxlength="150" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm focus:outline-none focus:border-indigo-500" placeholder="Tieu de">
                            <select name="category" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm focus:outline-none focus:border-indigo-500">
                                <option value="electric">Dien</option>
                                <option value="water">Nuoc</option>
                                <option value="furniture">Noi that</option>
                                <option value="maintenance">Bao tri</option>
                                <option value="other">Khac</option>
                            </select>
                            <textarea name="description" maxlength="1000" required rows="5" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm focus:outline-none focus:border-indigo-500" placeholder="Mo ta chi tiet"></textarea>
                            <input name="image" type="file" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-400 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:text-xs file:font-bold">
                            <button type="submit" class="submit-btn w-full px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold">
                                <i class="fa-solid fa-paper-plane"></i> Gui yeu cau
                            </button>
                        </form>

                        <div class="xl:col-span-2 overflow-x-auto rounded-xl border border-slate-900">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-950 text-slate-500 uppercase text-xs">
                                    <tr>
                                        <th class="px-4 py-3">Ngay</th>
                                        <th class="px-4 py-3">Noi dung</th>
                                        <th class="px-4 py-3">Trang thai</th>
                                        <th class="px-4 py-3">Phu trach</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-900">
                                    @forelse($tickets as $ticket)
                                        <tr class="hover:bg-slate-900/30">
                                            <td class="px-4 py-4 text-xs text-slate-500">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-4">
                                                <div class="font-bold text-slate-200">{{ $ticket->title }}</div>
                                                <div class="text-xs text-slate-500 mt-1">{{ $ticket->description }}</div>
                                                @if($ticket->image_path)
                                                    <a href="{{ $ticket->image_path }}" target="_blank" class="text-xs text-indigo-300 mt-1 inline-block">Xem anh</a>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $ticket->status === 'resolved' ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20' : 'bg-amber-500/10 text-amber-300 border-amber-500/20' }}">
                                                    {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-xs text-slate-400">{{ $ticket->assigned_to ?? 'Chua phan cong' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-xs text-slate-500">Chua co yeu cau sua chua.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endif
        </main>
    </div>

    <script>
        function switchResidentTab(tab) {
            document.querySelectorAll('.resident-section').forEach(section => section.classList.add('hidden'));
            document.getElementById(`resident-tab-${tab}`)?.classList.remove('hidden');
            document.querySelectorAll('.resident-tab').forEach(button => {
                button.className = 'resident-tab px-4 py-2 rounded-xl bg-slate-900 text-slate-300 text-xs font-bold border border-slate-800';
            });
            event.currentTarget.className = 'resident-tab px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold';
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        }

        function disableSubmit(form) {
            const btn = form.querySelector('.submit-btn');
            if (btn.disabled) return false;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Dang gui...';
            return true;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tab = new URLSearchParams(window.location.search).get('tab');
            if (tab && document.getElementById(`resident-tab-${tab}`)) {
                const buttons = Array.from(document.querySelectorAll('.resident-tab'));
                const index = ['bills', 'contract', 'tickets'].indexOf(tab);
                if (buttons[index]) buttons[index].click();
            }
        });
    </script>
</body>
</html>
