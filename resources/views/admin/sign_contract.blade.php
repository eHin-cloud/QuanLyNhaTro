<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ký Hợp Đồng Điện Tử - {{ $contract->contract_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #030712;
            overflow-x: hidden;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        /* Custom Scrollbar for terms */
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 99px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.3);
            border-radius: 99px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.6);
        }
        /* Grid pattern backdrops */
        .bg-grid {
            background-size: 30px 30px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
        }
        .signature-paper {
            background: #fff;
            background-image:
                linear-gradient(to bottom, rgba(15, 23, 42, 0.04) 1px, transparent 1px);
            background-size: 100% 32px;
        }
        .signature-preview-img {
            filter: grayscale(1) invert(1) contrast(3.2) brightness(1.08);
            mix-blend-mode: multiply;
        }
    </style>
</head>
<body class="text-slate-100 min-h-screen py-12 px-4 bg-grid relative flex flex-col items-center justify-center">
    @php
        $signedInUser = Auth::user();
        $canLessorSign = $signedInUser
            && (
                (int) $signedInUser->tenant_id === (int) $contract->tenant_id
                || (!$signedInUser->tenant_id && $signedInUser->role === 'admin')
            );
        $showTenantSignaturePad = !$canLessorSign && $contract->status !== 'active';
    @endphp

    <!-- Ambient blur blobs -->
    <div class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] -z-10 animate-pulse" style="animation-duration: 8s;"></div>
    <div class="absolute bottom-1/4 right-1/4 translate-x-1/2 translate-y-1/2 w-[400px] h-[400px] bg-fuchsia-600/10 rounded-full blur-[100px] -z-10 animate-pulse" style="animation-duration: 12s;"></div>

    <div class="w-full max-w-3xl space-y-6 z-10">
        
        <!-- Back to Portal Header -->
        <div class="flex items-center justify-between px-2">
            <a href="{{ route('smartroom.portal') }}" class="group flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 transition-all">
                <span class="w-7 h-7 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center group-hover:border-slate-700 transition-all">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
                Quay lại Trang Chủ
            </a>
            <div class="flex items-center gap-3 text-right">
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-500">Hệ thống hợp đồng online</span>
                <a href="{{ route('smartroom.contract.pdf', $contract->id) }}" target="_blank" class="h-9 px-3 rounded-xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 text-xs font-bold text-slate-300 hover:text-white transition-all flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf text-rose-400"></i>
                    In/PDF
                </a>
            </div>
        </div>

        <!-- Success notification toast -->
        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center gap-3 animate-bounce">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-300">
                <i class="fa-solid fa-circle-check text-lg"></i>
            </div>
            <div>
                <strong class="block text-sm font-bold">Ký kết thành công!</strong>
                <span class="text-xs text-emerald-400/80">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <!-- Error notification toast -->
        @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center gap-3 animate-pulse">
            <div class="w-10 h-10 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-300">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
            </div>
            <div>
                <strong class="block text-sm font-bold">Lỗi ký hợp đồng!</strong>
                <span class="text-xs text-rose-400/80">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Main legal document container -->
        <div class="glass-card rounded-[32px] p-8 md:p-10 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/5 rounded-full blur-3xl -z-10"></div>

            <!-- Decorative corner lights -->
            <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-indigo-500/50 to-transparent"></div>

            <!-- Document Title Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-800/80 pb-6 mb-8 gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold tracking-wider uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            E-Contract
                        </span>
                        <span class="text-xs text-slate-400 font-medium">Hợp đồng thuê căn hộ dịch vụ</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">
                        Số: <span class="bg-gradient-to-r from-indigo-200 to-slate-200 bg-clip-text text-transparent">{{ $contract->contract_code }}</span>
                    </h1>
                </div>
                <div>
                    @if($contract->status === 'active')
                        <span class="px-4 py-2 rounded-2xl text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> 
                            <span>Đã Ký Hiệu Lực</span>
                        </span>
                    @else
                        <span class="px-4 py-2 rounded-2xl text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> 
                            <span>Chờ Cư Dân Ký</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Fast-glance details ribbon -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 bg-slate-900/60 p-6 rounded-2xl border border-slate-800/60 mb-8">
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">Căn Hộ</span>
                    <strong class="text-slate-200 text-sm font-semibold flex items-center gap-1.5">
                        <i class="fa-solid fa-door-open text-indigo-400"></i> Phòng {{ $contract->room->room_number }}
                    </strong>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">Người Thuê</span>
                    <strong class="text-slate-200 text-sm font-semibold flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-indigo-400"></i> {{ $contract->resident->name }}
                    </strong>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">Đặt Cọc</span>
                    <strong class="text-emerald-400 text-sm font-bold">
                        {{ number_format($contract->deposit) }}đ
                    </strong>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">Thời Hạn Thuê</span>
                    <strong class="text-slate-300 text-xs font-semibold block">
                        {{ date('d/m/Y', strtotime($contract->start_date)) }} - {{ date('d/m/Y', strtotime($contract->end_date)) }}
                    </strong>
                </div>
            </div>

            <!-- Document terms viewer -->
            <div class="space-y-3 mb-8">
                <label class="text-xs font-bold uppercase text-slate-400 tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-indigo-400"></i> Nội dung điều khoản chi tiết
                </label>
                <div class="bg-slate-950/80 border border-slate-900 rounded-2xl p-6 h-96 overflow-y-auto text-sm text-slate-300 whitespace-pre-wrap leading-relaxed custom-scroll">
{{ $contract->terms }}
                </div>
            </div>

            <!-- Signing area with signature pad guides -->
            <div class="border-t border-slate-800/80 pt-8">
                @if($contract->status === 'active')
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <span class="text-xs text-slate-500 uppercase font-bold tracking-wider">Chữ ký bên thuê</span>
                        <div class="signature-paper border border-slate-700 rounded-2xl p-4 w-full max-w-sm flex items-center justify-center h-40 relative shadow-inner overflow-hidden">
                            @if($contract->signature)
                                <img src="{{ $contract->signature }}" alt="Tenant Signature" class="{{ str_contains($contract->signature, 'image/svg+xml') ? '' : 'signature-preview-img' }} max-h-28 max-w-full opacity-100 transition-all hover:scale-105 duration-300">
                            @endif
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[10px] font-extrabold uppercase tracking-wider shadow-sm">
                                Đã ký điện tử
                            </span>
                            <!-- Background watermark -->
                            <div class="absolute bottom-2 right-4 text-[9px] text-slate-400 font-bold uppercase tracking-widest pointer-events-none">
                                Verified Secure
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-emerald-400 animate-pulse"></i> 
                            Bên thuê đã ký vào lúc: {{ $contract->updated_at->format('H:i d/m/Y') }}
                        </p>
                    </div>
                @elseif($showTenantSignaturePad)
                    <form id="sign-form" action="{{ route('smartroom.contract.sign', $contract->id) }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="signature" id="signature-input">

                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold uppercase text-slate-400 tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-signature text-indigo-400"></i> Vẽ chữ ký điện tử của bạn
                                </span>
                                <button type="button" onclick="clearSignature()" class="text-xs text-rose-400 hover:text-rose-300 font-bold flex items-center gap-1 transition-all bg-rose-500/5 hover:bg-rose-500/10 px-3 py-1 rounded-xl border border-rose-500/10">
                                    <i class="fa-solid fa-eraser"></i> Xóa vẽ lại
                                </button>
                            </div>
                            
                            <!-- Drawing Pad Canvas with interactive helpers -->
                            <div class="bg-slate-950 border border-slate-800/80 rounded-2xl overflow-hidden relative group" style="height: 220px;">
                                <!-- Sign baseline guide line -->
                                <div class="absolute bottom-12 left-8 right-8 h-[1px] border-b border-dashed border-slate-800/60 pointer-events-none"></div>
                                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-[10px] text-slate-600 uppercase tracking-widest font-bold pointer-events-none select-none">
                                    Vẽ chữ ký của bạn vào khung này
                                </div>
                                <canvas id="signature-pad" class="w-full h-full cursor-crosshair z-10 relative"></canvas>
                            </div>
                        </div>

                        <!-- OTP Verification Card -->
                        <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-2xl space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold uppercase text-slate-400 tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-key text-indigo-400"></i> Xác thực chữ ký bằng OTP
                                </label>
                                <button type="button" id="btn-send-otp" onclick="sendOtp()" class="text-xs text-indigo-400 hover:text-indigo-300 font-bold flex items-center gap-1.5 transition-all bg-indigo-500/5 hover:bg-indigo-500/10 px-3 py-1.5 rounded-xl border border-indigo-500/10">
                                    <i class="fa-solid fa-paper-plane"></i> Gửi mã OTP qua SMS/Zalo
                                </button>
                            </div>
                            <div class="relative">
                                <input type="text" name="otp_code" id="otp-input" placeholder="Nhập mã OTP 6 chữ số" required maxlength="6"
                                    class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all text-center tracking-[0.5em] font-mono font-bold">
                            </div>
                            <p id="otp-status" class="text-[11px] text-slate-500 text-center hidden flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-400"></i> Mã OTP đã được gửi thành công!
                            </p>
                        </div>

                        <div class="pt-4">
                            <button type="submit" onclick="submitSignature(event)" class="w-full py-4 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-500 font-bold text-white shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/40 transition-all duration-300 flex items-center justify-center gap-2 hover:-translate-y-0.5">
                                <i class="fa-solid fa-pen-nib text-sm"></i>
                                <span>Bên thuê xác nhận ký hợp đồng</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 text-center text-xs font-semibold text-slate-500">
                        Bên thuê chưa ký. Hãy gửi liên kết này cho người thuê để họ ký hợp đồng.
                    </div>
                @endif

                <div class="mt-8 pt-8 border-t border-slate-800/80">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <span class="text-xs text-slate-500 uppercase font-bold tracking-wider">Chữ ký bên cho thuê</span>
                        @if($contract->lessor_signature)
                            <div class="signature-paper border border-slate-700 rounded-2xl p-4 w-full max-w-sm flex items-center justify-center h-40 relative shadow-inner overflow-hidden">
                                <img src="{{ $contract->lessor_signature }}" alt="Lessor Signature" class="{{ str_contains($contract->lessor_signature, 'image/svg+xml') ? '' : 'signature-preview-img' }} max-h-28 max-w-full opacity-100 transition-all hover:scale-105 duration-300">
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-cyan-600 text-white text-[10px] font-extrabold uppercase tracking-wider shadow-sm">
                                    Chủ trọ đã ký
                                </span>
                                <div class="absolute bottom-2 right-4 text-[9px] text-slate-400 font-bold uppercase tracking-widest pointer-events-none">
                                    Lessor Verified
                                </div>
                            </div>
                        @elseif($canLessorSign)
                            <form id="lessor-sign-form" action="{{ route('smartroom.contract.lessor_sign', $contract->id) }}" method="POST" class="w-full max-w-sm space-y-4">
                                @csrf
                                <input type="hidden" name="lessor_signature" id="lessor-signature-input">
                                <div class="bg-slate-950 border border-slate-800/80 rounded-2xl overflow-hidden relative group" style="height: 180px;">
                                    <div class="absolute bottom-10 left-8 right-8 h-[1px] border-b border-dashed border-slate-800/60 pointer-events-none"></div>
                                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-[10px] text-slate-600 uppercase tracking-widest font-bold pointer-events-none select-none">
                                        Chủ trọ ký vào khung này
                                    </div>
                                    <canvas id="lessor-signature-pad" class="w-full h-full cursor-crosshair z-10 relative"></canvas>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <button type="button" onclick="clearLessorSignature()" class="py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-bold text-slate-300 transition-all">
                                        Xóa vẽ lại
                                    </button>
                                    <button type="submit" onclick="submitLessorSignature(event)" class="py-3 rounded-2xl bg-cyan-600 hover:bg-cyan-500 text-xs font-bold text-white shadow-lg shadow-cyan-600/25 transition-all">
                                        Chủ trọ xác nhận ký
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="w-full max-w-sm rounded-2xl border border-slate-800 bg-slate-950/70 p-5 text-center text-xs font-semibold text-slate-500">
                                Bên cho thuê chưa ký online.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if($showTenantSignaturePad)
    <script>
        // Setup custom toast notification override for alert()
        (function() {
            const toastStyle = document.createElement('style');
            toastStyle.innerHTML = `
                .custom-toast-container {
                    position: fixed;
                    top: 24px;
                    right: 24px;
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    pointer-events: none;
                }
                .custom-toast {
                    min-width: 320px;
                    max-width: 450px;
                    background: rgba(15, 23, 42, 0.9);
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 16px;
                    padding: 16px 20px;
                    color: #f1f5f9;
                    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3), 0 0 1px 1px rgba(255, 255, 255, 0.05);
                    display: flex;
                    align-items: flex-start;
                    gap: 14px;
                    pointer-events: auto;
                    transform: translateX(120%);
                    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                .custom-toast.show {
                    transform: translateX(0);
                }
                .custom-toast.hide {
                    transform: translateX(120%);
                    opacity: 0;
                    margin-top: -60px;
                }
                .custom-toast-icon {
                    flex-shrink: 0;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 13px;
                }
                .custom-toast-success .custom-toast-icon {
                    background: rgba(16, 185, 129, 0.15);
                    color: #10b981;
                    border: 1px solid rgba(16, 185, 129, 0.2);
                }
                .custom-toast-warning .custom-toast-icon {
                    background: rgba(245, 158, 11, 0.15);
                    color: #f59e0b;
                    border: 1px solid rgba(245, 158, 11, 0.2);
                }
                .custom-toast-error .custom-toast-icon {
                    background: rgba(239, 68, 68, 0.15);
                    color: #ef4444;
                    border: 1px solid rgba(239, 68, 68, 0.2);
                }
                .custom-toast-info .custom-toast-icon {
                    background: rgba(59, 130, 246, 0.15);
                    color: #3b82f6;
                    border: 1px solid rgba(59, 130, 246, 0.2);
                }
                .custom-toast-content {
                    flex-grow: 1;
                }
                .custom-toast-title {
                    font-size: 13px;
                    font-weight: 700;
                    margin-bottom: 3px;
                    letter-spacing: 0.3px;
                }
                .custom-toast-message {
                    font-size: 12px;
                    color: #94a3b8;
                    line-height: 1.5;
                    white-space: pre-wrap;
                }
                .custom-toast-close {
                    color: #64748b;
                    cursor: pointer;
                    font-size: 14px;
                    transition: color 0.2s;
                    margin-top: 1px;
                }
                .custom-toast-close:hover {
                    color: #94a3b8;
                }
            `;
            document.head.appendChild(toastStyle);

            window.alert = function(message) {
                let type = 'success';
                let title = 'Thông Báo';
                
                const lowerMsg = message.toLowerCase();
                if (lowerMsg.includes('lỗi') || 
                    lowerMsg.includes('không thể') || 
                    lowerMsg.includes('thất bại') || 
                    lowerMsg.includes('chưa') || 
                    lowerMsg.includes('không được') || 
                    lowerMsg.includes('chỉ được') || 
                    lowerMsg.includes('nhỏ hơn') ||
                    lowerMsg.includes('vui lòng')) {
                    type = 'warning';
                    title = 'Cảnh Báo';
                } else if (lowerMsg.includes('thành công') || 
                           lowerMsg.includes('tuyệt vời') || 
                           lowerMsg.includes('đã') || 
                           lowerMsg.includes('sao chép')) {
                    type = 'success';
                    title = 'Thành Công';
                } else {
                    type = 'info';
                    title = 'Thông Tin';
                }
                
                let container = document.querySelector('.custom-toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'custom-toast-container';
                    document.body.appendChild(container);
                }
                
                const toast = document.createElement('div');
                toast.className = `custom-toast custom-toast-${type}`;
                
                let iconHtml = '';
                if (type === 'success') iconHtml = '<i class="fa-solid fa-check"></i>';
                else if (type === 'warning') iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
                else if (type === 'error') iconHtml = '<i class="fa-solid fa-circle-xmark"></i>';
                else iconHtml = '<i class="fa-solid fa-info"></i>';
                
                toast.innerHTML = `
                    <div class="custom-toast-icon">${iconHtml}</div>
                    <div class="custom-toast-content">
                        <div class="custom-toast-title">${title}</div>
                        <div class="custom-toast-message">${message}</div>
                    </div>
                    <div class="custom-toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 400);"><i class="fa-solid fa-xmark"></i></div>
                `;
                
                container.appendChild(toast);
                
                setTimeout(() => toast.classList.add('show'), 10);
                
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.classList.remove('show');
                        toast.classList.add('hide');
                        setTimeout(() => toast.remove(), 400);
                    }
                }, 4500);
            };
        })();

        const canvas = document.getElementById('signature-pad');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;

        function resizeCanvas() {
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;
            ctx.lineWidth = 3.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#ffffff';
        }

        window.addEventListener('resize', resizeCanvas);
        // Wait briefly for container layouts
        setTimeout(resizeCanvas, 100);

        // Coordinates corrector for custom DPI scale
        function getCoords(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        function startDrawing(e) {
            isDrawing = true;
            const coords = getCoords(e);
            ctx.beginPath();
            ctx.moveTo(coords.x, coords.y);
            e.preventDefault();
        }

        function draw(e) {
            if (!isDrawing) return;
            const coords = getCoords(e);
            ctx.lineTo(coords.x, coords.y);
            ctx.stroke();
            e.preventDefault();
        }

        function stopDrawing() {
            isDrawing = false;
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseleave', stopDrawing);

        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function submitSignature(e) {
            e.preventDefault();
            
            // Check image data buffer contents
            const buffer = new Uint32Array(ctx.getImageData(0, 0, canvas.width, canvas.height).data.buffer);
            const isCanvasEmpty = !buffer.some(color => color !== 0);

            if (isCanvasEmpty) {
                alert('Vui lòng vẽ chữ ký của bạn trước khi xác nhận ký kết!');
                return;
            }

            const dataURL = canvas.toDataURL('image/png');
            document.getElementById('signature-input').value = dataURL;
            document.getElementById('sign-form').submit();
        }

        function sendOtp() {
            const btn = document.getElementById('btn-send-otp');
            const status = document.getElementById('otp-status');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang gửi...';

            fetch("{{ route('smartroom.contract.send_otp', $contract->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    status.classList.remove('hidden');
                    let timeLeft = 60;
                    const interval = setInterval(() => {
                        timeLeft--;
                        if (timeLeft <= 0) {
                            clearInterval(interval);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Gửi lại mã OTP';
                        } else {
                            btn.innerHTML = `<i class="fa-solid fa-clock"></i> Gửi lại sau (${timeLeft}s)`;
                        }
                    }, 1000);
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể gửi mã OTP.'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Gửi mã OTP qua SMS/Zalo';
                }
            })
            .catch(error => {
                console.error(error);
                alert('Có lỗi xảy ra khi gửi mã OTP!');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Gửi mã OTP qua SMS/Zalo';
            });
        }
    </script>
    @endif
    @if($canLessorSign && !$contract->lessor_signature)
    <script>
        const lessorCanvas = document.getElementById('lessor-signature-pad');
        const lessorCtx = lessorCanvas.getContext('2d');
        let isDrawingLessor = false;

        function resizeLessorCanvas() {
            const rect = lessorCanvas.getBoundingClientRect();
            lessorCanvas.width = rect.width;
            lessorCanvas.height = rect.height;
            lessorCtx.lineWidth = 3.5;
            lessorCtx.lineCap = 'round';
            lessorCtx.lineJoin = 'round';
            lessorCtx.strokeStyle = '#ffffff';
        }

        function getLessorCoords(e) {
            const rect = lessorCanvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        function startLessorDrawing(e) {
            isDrawingLessor = true;
            const coords = getLessorCoords(e);
            lessorCtx.beginPath();
            lessorCtx.moveTo(coords.x, coords.y);
            e.preventDefault();
        }

        function drawLessor(e) {
            if (!isDrawingLessor) return;
            const coords = getLessorCoords(e);
            lessorCtx.lineTo(coords.x, coords.y);
            lessorCtx.stroke();
            e.preventDefault();
        }

        function stopLessorDrawing() {
            isDrawingLessor = false;
        }

        function clearLessorSignature() {
            lessorCtx.clearRect(0, 0, lessorCanvas.width, lessorCanvas.height);
        }

        function submitLessorSignature(e) {
            e.preventDefault();
            const buffer = new Uint32Array(lessorCtx.getImageData(0, 0, lessorCanvas.width, lessorCanvas.height).data.buffer);
            const isCanvasEmpty = !buffer.some(color => color !== 0);

            if (isCanvasEmpty) {
                alert('Vui lòng vẽ chữ ký bên cho thuê trước khi xác nhận.');
                return;
            }

            document.getElementById('lessor-signature-input').value = lessorCanvas.toDataURL('image/png');
            document.getElementById('lessor-sign-form').submit();
        }

        lessorCanvas.addEventListener('mousedown', startLessorDrawing);
        lessorCanvas.addEventListener('mousemove', drawLessor);
        lessorCanvas.addEventListener('mouseup', stopLessorDrawing);
        lessorCanvas.addEventListener('mouseleave', stopLessorDrawing);
        lessorCanvas.addEventListener('touchstart', startLessorDrawing);
        lessorCanvas.addEventListener('touchmove', drawLessor);
        lessorCanvas.addEventListener('touchend', stopLessorDrawing);
        window.addEventListener('resize', resizeLessorCanvas);
        setTimeout(resizeLessorCanvas, 100);
    </script>
    @endif
</body>
</html>
