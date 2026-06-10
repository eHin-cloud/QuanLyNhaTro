<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyệt Hồ Sơ Xác Minh - SmartRoom & Renty</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        /* Toast notification */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 380px;
            width: 100%;
        }
        .toast-card {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .toast-card.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast-success { border-left: 4px solid #10b981; }
        .toast-error { border-left: 4px solid #f43f5e; }
        .toast-info { border-left: 4px solid #3b82f6; }
    </style>
</head>
<body class="min-h-screen text-slate-100 relative overflow-x-hidden pb-12">
    <!-- Background Elements -->
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-indigo-600 glow-circle"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-sky-600 glow-circle"></div>

    <div id="toast-container"></div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-10 relative z-10">
        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 pb-6 border-b border-slate-900">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-widest bg-sky-500/10 text-sky-400 rounded-lg border border-sky-500/20">
                        SmartRoom Console
                    </span>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white mt-2 flex items-center gap-3">
                    <i class="fa-solid fa-shield-halved text-sky-400"></i> Giám Sát & Bảo Mật
                </h1>
                <p class="text-xs text-slate-400">
                    Phê duyệt KYC để kích hoạt thanh toán hoặc cấp tích xanh hỗ trợ nâng cao uy tín cho chủ trọ trên Renty.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Nút Đăng ký thiết bị bảo mật (Passkey) -->
                <button id="btnRegisterPasskey" onclick="registerBiometricDevice()" class="inline-flex items-center gap-2 rounded-xl border border-sky-800 bg-sky-950/40 backdrop-blur px-4 py-2.5 text-xs font-bold text-sky-300 hover:text-white hover:border-sky-500 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i id="passkeyIcon" class="fa-solid fa-fingerprint text-sky-400"></i>
                    <span id="passkeyBtnText">Đăng ký Passkey</span>
                </button>

                <a href="{{ route('user.list') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-900/60 backdrop-blur px-4 py-2.5 text-xs font-bold text-slate-350 hover:text-white hover:border-indigo-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
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

        <!-- Quick Stats Dashboard -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Stats -->
            <div class="glass-panel rounded-2xl p-4 flex items-center gap-4 transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 text-base">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Tất Cả Hồ Sơ</p>
                    <p class="text-xl font-bold text-slate-100 mt-0.5">{{ $stats['total'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Pending Stats -->
            <div class="glass-panel rounded-2xl p-4 flex items-center gap-4 transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20 text-base">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Đang Chờ Duyệt</p>
                    <p class="text-xl font-bold text-amber-400 mt-0.5">{{ $stats['pending'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Approved Stats -->
            <div class="glass-panel rounded-2xl p-4 flex items-center gap-4 transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 text-base">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Đã Phê Duyệt</p>
                    <p class="text-xl font-bold text-emerald-400 mt-0.5">{{ $stats['approved'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Rejected Stats -->
            <div class="glass-panel rounded-2xl p-4 flex items-center gap-4 transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20 text-base">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Đã Từ Chối</p>
                    <p class="text-xl font-bold text-rose-400 mt-0.5">{{ $stats['rejected'] ?? 0 }}</p>
                </div>
            </div>
        </section>

        <!-- Filters Section -->
        <section class="glass-panel rounded-2xl p-5 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="verification-search" oninput="filterRequests()" placeholder="Tìm theo tên nhà trọ, chủ trọ, số điện thoại..." class="w-full rounded-xl border border-slate-800 bg-slate-950/60 pl-10 pr-4 py-2.5 text-xs text-slate-200 outline-none focus:border-indigo-500/80 transition-all font-semibold placeholder-slate-500">
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <select id="status-filter" onchange="filterRequests()" class="w-full md:w-40 rounded-xl border border-slate-800 bg-slate-950/60 px-3.5 py-2.5 text-xs text-slate-300 outline-none focus:border-indigo-500/80 transition-all font-semibold font-sans">
                    <option value="all">Trạng thái (Tất cả)</option>
                    <option value="pending">Đang chờ duyệt</option>
                    <option value="approved">Đã phê duyệt</option>
                    <option value="rejected">Đã từ chối</option>
                    <option value="superseded">Đã bị thay thế</option>
                </select>

                <select id="type-filter" onchange="filterRequests()" class="w-full md:w-40 rounded-xl border border-slate-800 bg-slate-950/60 px-3.5 py-2.5 text-xs text-slate-300 outline-none focus:border-indigo-500/80 transition-all font-semibold font-sans">
                    <option value="all">Loại xác minh (Tất cả)</option>
                    <option value="kyc">KYC nhận tiền</option>
                    <option value="premium">Tích xanh Premium</option>
                </select>
            </div>
        </section>

        <!-- Requests List -->
        <section class="space-y-5" id="requests-container">
            @forelse($requests as $request)
                @php
                    $typeLabel = $request->type === 'premium' ? 'Tích xanh' : 'KYC nhận tiền';
                    $statusClass = match ($request->status) {
                        'approved' => 'border-emerald-500/25 bg-emerald-500/10 text-emerald-350',
                        'rejected' => 'border-rose-500/25 bg-rose-500/10 text-rose-350',
                        'superseded' => 'border-slate-800 bg-slate-900/60 text-slate-400',
                        default => 'border-amber-500/25 bg-amber-500/10 text-amber-350',
                    };
                    $statusName = match ($request->status) {
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        'superseded' => 'Thay thế',
                        default => 'Chờ duyệt',
                    };
                    $senderName = $request->user?->name ?? 'Không rõ';
                    $initial = mb_substr($senderName, 0, 1);
                @endphp

                <article class="glass-panel rounded-3xl p-6 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-950/10 user-request-card"
                         data-tenant="{{ strtolower($request->tenant?->name ?? '') }}"
                         data-user="{{ strtolower($senderName) }}"
                         data-phone="{{ strtolower($request->user?->phone ?? '') }}"
                         data-status="{{ $request->status }}"
                         data-type="{{ $request->type }}">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                        <!-- Left Details Content -->
                        <div class="flex-grow space-y-4 min-w-0">
                            <!-- Labels & Badge Info -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg border px-2.5 py-1 text-[10px] font-extrabold tracking-wider {{ $statusClass }}">
                                    {{ strtoupper($statusName) }}
                                </span>
                                <span class="rounded-lg border border-sky-500/20 bg-sky-500/10 px-2.5 py-1 text-[10px] font-extrabold tracking-wider text-sky-300">
                                    {{ $typeLabel }}
                                </span>
                                <span class="text-xs text-slate-500 font-medium ml-1">
                                    #{{ $request->id }} • {{ $request->created_at?->format('d/m/Y H:i') }}
                                </span>
                            </div>

                            <!-- Title Business / Tenant -->
                            <div>
                                <h2 class="text-xl font-bold text-white tracking-tight leading-snug">
                                    {{ $request->tenant?->name ?? 'Không gán cơ sở kinh doanh' }}
                                </h2>
                                
                                <!-- Sender Account Detail Card -->
                                <div class="flex items-center gap-3 mt-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 text-xs font-bold text-slate-350 flex items-center justify-center uppercase select-none">
                                        {{ $initial }}
                                    </div>
                                    <div class="text-xs">
                                        <p class="font-semibold text-slate-200">{{ $senderName }}</p>
                                        <p class="text-slate-400 mt-0.5">{{ $request->user?->phone ?? 'N/A' }} • {{ $request->user?->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Rejection Alert box -->
                            @if($request->reject_reason)
                                <div class="rounded-2xl border border-rose-500/10 bg-rose-500/5 p-4 text-xs leading-relaxed text-rose-200">
                                    <p class="font-semibold text-rose-450 flex items-center gap-1.5 mb-1">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Lý do từ chối trước đó:
                                    </p>
                                    <p class="text-slate-300 italic">"{{ $request->reject_reason }}"</p>
                                </div>
                            @endif

                            <!-- Documents Attached -->
                            <div class="space-y-2">
                                <h4 class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                    <i class="fa-solid fa-paperclip"></i> Tài liệu đính kèm
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($request->documents as $document)
                                        @if($request->allowsDefaultAdminDocumentReview())
                                            <a href="{{ route('admin.verification-documents.show', $document) }}" target="_blank" class="flex items-center justify-between rounded-xl border border-slate-850 bg-slate-950/65 px-4 py-3 text-xs font-bold text-slate-200 hover:border-sky-500/40 hover:bg-slate-900/40 transition-all group shadow-inner">
                                                <span class="flex items-center gap-2 truncate pr-2">
                                                    <i class="fa-regular fa-file-pdf text-rose-400 group-hover:scale-110 transition-transform text-sm"></i>
                                                    <span class="truncate">{{ str_replace('_', ' ', strtoupper($document->document_type)) }}</span>
                                                </span>
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500 group-hover:text-sky-400"></i>
                                            </a>
                                        @else
                                            <div class="rounded-xl border border-slate-850 bg-slate-950/40 p-3.5 shadow-inner">
                                                <div class="flex items-center gap-2 font-bold text-slate-350 text-xs">
                                                    <i class="fa-solid fa-lock text-amber-500/80"></i>
                                                    {{ str_replace('_', ' ', strtoupper($document->document_type)) }}
                                                </div>
                                                
                                                <form method="POST" action="{{ route('admin.verification-documents.unlock', $document) }}" onsubmit="handleJitUnlockSubmit(event, this)" class="mt-2.5 space-y-2">
                                                    @csrf
                                                    <textarea name="reason" required minlength="12" rows="2" placeholder="Lý do mở khóa dữ liệu bảo mật JIT..." class="w-full rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1.5 text-[11px] text-slate-200 outline-none focus:border-amber-500/80 transition-all placeholder-slate-600 font-sans"></textarea>
                                                    <button class="w-full py-1.5 rounded-lg border border-amber-500/30 bg-amber-500/10 text-[10px] font-extrabold text-amber-300 hover:bg-amber-500/20 active:scale-[0.98] transition-all">
                                                        Unlock & Request Access
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions Panel -->
                        @if($request->status === 'pending')
                            <div class="w-full lg:w-80 flex-shrink-0 bg-slate-950/50 rounded-2xl p-4 border border-slate-850 space-y-3 shadow-md self-stretch flex flex-col justify-center">
                                <h3 class="text-xs font-bold text-slate-400 pb-2 border-b border-slate-850 flex items-center gap-1.5 mb-1">
                                    <i class="fa-solid fa-sliders text-indigo-400"></i> Thao Tác Quyết Định
                                </h3>

                                <form method="POST" action="{{ route('admin.verifications.approve', $request) }}">
                                    @csrf
                                    <button class="w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-550 hover:to-teal-550 text-xs font-bold text-white shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/20 transition-all duration-200 active:scale-[0.97]">
                                        <i class="fa-solid fa-square-check mr-1.5 text-base align-middle"></i> Phê Duyệt Hồ Sơ
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.verifications.reject', $request) }}" class="space-y-2">
                                    @csrf
                                    <textarea name="reject_reason" required rows="3" placeholder="Nhập lý do từ chối cụ thể..." class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200 outline-none focus:border-rose-500/70 transition-all font-sans placeholder-slate-600" style="resize: none;"></textarea>
                                    <button class="w-full py-2 rounded-xl border border-rose-500/20 bg-rose-500/10 text-xs font-bold text-rose-350 hover:bg-rose-500/20 hover:border-rose-500/30 transition-all active:scale-[0.97]">
                                        <i class="fa-solid fa-rectangle-xmark mr-1.5 text-base align-middle"></i> Từ Chối Yêu Cầu
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-slate-850 bg-slate-900/20 backdrop-blur-md p-12 text-center text-slate-400 flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 text-slate-500 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-folder-closed"></i>
                    </div>
                    <p class="font-bold text-sm text-slate-300">Chưa Có Hồ Sơ Xác Minh Nào</p>
                    <p class="text-xs text-slate-500 mt-1">Hệ thống hiện tại chưa ghi nhận yêu cầu phê duyệt nào từ chủ trọ.</p>
                </div>
            @endforelse
        </section>

        <!-- Pagination -->
        @if($requests->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $requests->links() }}
            </div>
        @endif
    </main>

    <script>
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const card = document.createElement('div');
            card.className = `toast-card toast-${type}`;
            let icon = 'fa-circle-info';
            if (type === 'success') icon = 'fa-circle-check';
            if (type === 'error') icon = 'fa-circle-exclamation';
            
            card.innerHTML = `
                <i class="fa-solid ${icon} mt-0.5 text-lg ${type === 'success' ? 'text-emerald-400' : (type === 'error' ? 'text-rose-400' : 'text-blue-400')}"></i>
                <div class="flex-grow">
                    <p class="text-xs font-semibold text-slate-200 leading-relaxed">${message}</p>
                </div>
            `;
            container.appendChild(card);
            setTimeout(() => card.classList.add('show'), 10);
            setTimeout(() => {
                card.classList.remove('show');
                setTimeout(() => card.remove(), 400);
            }, 4500);
        }

        function filterRequests() {
            const searchQuery = document.getElementById('verification-search').value.toLowerCase().trim();
            const statusFilter = document.getElementById('status-filter').value;
            const typeFilter = document.getElementById('type-filter').value;
            const cards = document.querySelectorAll('.user-request-card');
            
            let visibleCount = 0;

            cards.forEach(card => {
                const tenant = card.getAttribute('data-tenant');
                const user = card.getAttribute('data-user');
                const phone = card.getAttribute('data-phone');
                const status = card.getAttribute('data-status');
                const type = card.getAttribute('data-type');

                const matchesSearch = tenant.includes(searchQuery) || user.includes(searchQuery) || phone.includes(searchQuery);
                const matchesStatus = statusFilter === 'all' || status === statusFilter;
                const matchesType = typeFilter === 'all' || type === typeFilter;

                if (matchesSearch && matchesStatus && matchesType) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
        }

        @if(session('success'))
            showToast(@json(session('success')), "success");
        @endif
        @if(session('error'))
            showToast(@json(session('error')), "error");
        @endif
        @if($errors->any())
            showToast(@json($errors->first()), "error");
        @endif

        // ================= WebAuthn FIDO2 Biometric Security =================
        window.hasPasskey = false;

        function base64UrlToUint8Array(base64Url) {
            const padding = '='.repeat((4 - base64Url.length % 4) % 4);
            const base64 = (base64Url + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        function bufferToBase64(buffer) {
            let binary = '';
            const bytes = new Uint8Array(buffer);
            const len = bytes.byteLength;
            for (let i = 0; i < len; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }

        function updatePasskeyButtonState() {
            const btn = document.getElementById('btnRegisterPasskey');
            const icon = document.getElementById('passkeyIcon');
            const text = document.getElementById('passkeyBtnText');
            if (window.hasPasskey) {
                btn.className = "inline-flex items-center gap-2 rounded-xl border border-emerald-800 bg-emerald-950/40 backdrop-blur px-4 py-2.5 text-xs font-bold text-emerald-300 hover:text-white hover:border-emerald-500 transition-all hover:scale-[1.02] active:scale-[0.98]";
                icon.className = "fa-solid fa-shield-halved text-emerald-400";
                text.innerText = "Passkey: Đã bảo vệ";
            } else {
                btn.className = "inline-flex items-center gap-2 rounded-xl border border-sky-800 bg-sky-950/40 backdrop-blur px-4 py-2.5 text-xs font-bold text-sky-300 hover:text-white hover:border-sky-500 transition-all hover:scale-[1.02] active:scale-[0.98]";
                icon.className = "fa-solid fa-fingerprint text-sky-400";
                text.innerText = "Đăng ký Passkey";
            }
        }

        async function checkPasskeyStatus() {
            try {
                const res = await fetch('{{ route("admin.verifications.has-passkey") }}');
                const data = await res.json();
                window.hasPasskey = data.has_passkey;
                updatePasskeyButtonState();
            } catch (e) {
                console.error("Lỗi check passkey status:", e);
            }
        }

        async function registerBiometricDevice() {
            if (typeof PublicKeyCredential === "undefined") {
                showToast("Trình duyệt hoặc thiết bị của bạn không hỗ trợ WebAuthn/Passkeys.", "error");
                return;
            }

            try {
                showToast("Đang kết nối máy chủ để khởi tạo Passkey...", "info");
                
                const optionsResponse = await fetch('/webauthn/register/options', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                });
                
                if (!optionsResponse.ok) {
                    throw new Error("Không thể lấy cấu hình đăng ký từ máy chủ.");
                }
                
                const options = await optionsResponse.json();
                options.challenge = base64UrlToUint8Array(options.challenge);
                options.user.id = base64UrlToUint8Array(options.user.id);
                
                if (options.excludeCredentials) {
                    options.excludeCredentials = options.excludeCredentials.map(cred => ({
                        ...cred,
                        id: base64UrlToUint8Array(cred.id)
                    }));
                }
                
                showToast("Vui lòng hoàn tất quét vân tay/khuôn mặt trên thiết bị của bạn...", "info");
                const credential = await navigator.credentials.create({
                    publicKey: options
                });
                
                const attestationResponse = {
                    id: credential.id,
                    rawId: bufferToBase64(credential.rawId),
                    type: credential.type,
                    response: {
                        clientDataJSON: bufferToBase64(credential.response.clientDataJSON),
                        attestationObject: bufferToBase64(credential.response.attestationObject)
                    }
                };
                
                const saveResponse = await fetch('/webauthn/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(attestationResponse)
                });
                
                if (saveResponse.ok) {
                    showToast("Đăng ký thiết bị bảo mật (Passkey) thành công!", "success");
                    window.hasPasskey = true;
                    updatePasskeyButtonState();
                } else {
                    const errData = await saveResponse.json();
                    throw new Error(errData.message || "Không thể lưu thiết bị bảo mật.");
                }
                
            } catch (error) {
                console.error(error);
                showToast(error.message || "Đăng ký Passkey thất bại hoặc bị hủy bỏ.", "error");
            }
        }

        async function handleJitUnlockSubmit(event, form) {
            event.preventDefault();
            
            if (!window.hasPasskey) {
                showToast("Bảo mật bắt buộc: Vui lòng click nút 'Đăng ký Passkey' ở trên đầu trang trước khi mở khóa tài liệu nhạy cảm.", "error");
                return;
            }
            
            const reasonTextarea = form.querySelector('textarea[name="reason"]');
            const reason = reasonTextarea.value.trim();
            if (reason.length < 12) {
                showToast("Lý do mở khóa phải chứa ít nhất 12 ký tự.", "error");
                return;
            }
            
            try {
                showToast("Đang chuẩn bị xác thực sinh trắc học...", "info");
                
                const optionsResponse = await fetch('/webauthn/login/options', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email: '{{ Auth::user()->email }}'
                    })
                });
                
                if (!optionsResponse.ok) {
                    throw new Error("Không thể khởi tạo cấu hình xác thực sinh trắc học.");
                }
                
                const options = await optionsResponse.json();
                options.challenge = base64UrlToUint8Array(options.challenge);
                if (options.allowCredentials) {
                    options.allowCredentials = options.allowCredentials.map(cred => ({
                        ...cred,
                        id: base64UrlToUint8Array(cred.id)
                    }));
                }
                
                showToast("Vui lòng quét vân tay hoặc khuôn mặt để mở khóa...", "info");
                const assertion = await navigator.credentials.get({
                    publicKey: options
                });
                
                const payload = {
                    id: assertion.id,
                    rawId: bufferToBase64(assertion.rawId),
                    type: assertion.type,
                    response: {
                        clientDataJSON: bufferToBase64(assertion.response.clientDataJSON),
                        authenticatorData: bufferToBase64(assertion.response.authenticatorData),
                        signature: bufferToBase64(assertion.response.signature),
                        userHandle: assertion.response.userHandle ? bufferToBase64(assertion.response.userHandle) : null
                    },
                    reason: reason
                };
                
                showToast("Đang gửi yêu cầu giải mã lên máy chủ...", "info");
                const unlockResponse = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                
                const result = await unlockResponse.json();
                
                if (unlockResponse.ok && result.success) {
                    showToast("Xác thực thành công! Đang mở tài liệu...", "success");
                    window.open(result.url, '_blank');
                    reasonTextarea.value = '';
                } else {
                    throw new Error(result.message || "Xác thực Passkey không thành công.");
                }
                
            } catch (error) {
                console.error(error);
                showToast(error.message || "Xác thực sinh trắc học thất bại hoặc bị hủy.", "error");
            }
        }

        // Tự động kiểm tra trạng thái Passkey khi vào trang
        document.addEventListener('DOMContentLoaded', checkPasskeyStatus);
    </script>
</body>
</html>
