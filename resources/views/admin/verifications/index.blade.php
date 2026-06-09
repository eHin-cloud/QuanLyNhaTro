<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyet ho so xac minh - SmartRoom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.2em] text-sky-300">SmartRoom Admin</p>
                <h1 class="text-2xl font-black mt-2">Duyet ho so xac minh</h1>
                <p class="text-sm text-slate-400 mt-2">KYC mo khoa thanh toan. Tich xanh mo khoa badge va boost tren Renty.</p>
            </div>
            <a href="{{ route('user.list') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-900 px-4 py-2 text-xs font-bold text-slate-200 hover:border-sky-500/50">
                <i class="fa-solid fa-arrow-left"></i>
                Quan ly tai khoan
            </a>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm font-bold text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-sm font-bold text-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse($requests as $request)
                @php
                    $typeLabel = $request->type === 'premium' ? 'Tich xanh' : 'KYC nhan tien';
                    $statusClass = match ($request->status) {
                        'approved' => 'border-emerald-500/25 bg-emerald-500/10 text-emerald-200',
                        'rejected' => 'border-rose-500/25 bg-rose-500/10 text-rose-200',
                        'superseded' => 'border-slate-700 bg-slate-900 text-slate-400',
                        default => 'border-amber-500/25 bg-amber-500/10 text-amber-200',
                    };
                @endphp

                <article class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-5">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg border px-3 py-1 text-[10px] font-black {{ $statusClass }}">{{ strtoupper($request->status) }}</span>
                                <span class="rounded-lg border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-[10px] font-black text-sky-200">{{ $typeLabel }}</span>
                                <span class="text-xs text-slate-500">#{{ $request->id }} - {{ $request->created_at?->format('d/m/Y H:i') }}</span>
                            </div>

                            <h2 class="mt-3 text-lg font-black truncate">{{ $request->tenant?->name ?? 'Tenant khong ton tai' }}</h2>
                            <p class="mt-1 text-sm text-slate-400">
                                Gui boi: {{ $request->user?->name ?? 'N/A' }}
                                <span class="text-slate-600">/</span>
                                {{ $request->user?->phone ?? 'N/A' }}
                            </p>

                            @if($request->reject_reason)
                                <p class="mt-3 rounded-xl border border-rose-500/20 bg-rose-500/10 p-3 text-xs text-rose-100">{{ $request->reject_reason }}</p>
                            @endif

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                                @foreach($request->documents as $document)
                                    @if($request->allowsDefaultAdminDocumentReview())
                                        <a href="{{ route('admin.verification-documents.show', $document) }}" target="_blank" class="rounded-xl border border-slate-800 bg-slate-950 px-3 py-3 text-xs font-bold text-slate-200 hover:border-sky-500/50">
                                            <i class="fa-solid fa-file-shield text-sky-300 mr-2"></i>
                                            {{ str_replace('_', ' ', $document->document_type) }}
                                        </a>
                                    @else
                                        <form method="POST" action="{{ route('admin.verification-documents.unlock', $document) }}" target="_blank" class="rounded-xl border border-slate-800 bg-slate-950 p-3 text-xs text-slate-300">
                                            @csrf
                                            <div class="flex items-center gap-2 font-bold text-slate-200">
                                                <i class="fa-solid fa-lock text-amber-300"></i>
                                                {{ str_replace('_', ' ', $document->document_type) }}
                                            </div>
                                            <textarea name="reason" required minlength="12" rows="2" placeholder="Ly do mo khoa JIT..." class="mt-2 w-full rounded-lg border border-slate-800 bg-slate-900 px-2 py-2 text-[11px] text-slate-100 outline-none focus:border-amber-400"></textarea>
                                            <button class="mt-2 w-full rounded-lg border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-[10px] font-black text-amber-100 hover:bg-amber-500/20">
                                                Unlock & Request Access
                                            </button>
                                        </form>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        @if($request->status === 'pending')
                            <div class="w-full xl:w-96 space-y-3">
                                <form method="POST" action="{{ route('admin.verifications.approve', $request) }}">
                                    @csrf
                                    <button class="w-full rounded-xl bg-emerald-500 px-4 py-2 text-xs font-black text-slate-950 hover:bg-emerald-400">
                                        Duyet ho so
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.verifications.reject', $request) }}" class="space-y-2">
                                    @csrf
                                    <textarea name="reject_reason" required rows="3" placeholder="Ly do tu choi..." class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-100 outline-none focus:border-rose-400"></textarea>
                                    <button class="w-full rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-xs font-black text-rose-200 hover:bg-rose-500/20">
                                        Tu choi
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-8 text-center text-sm text-slate-400">
                    Chua co ho so xac minh nao.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    </main>
</body>
</html>
