<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dang ky chu tro - SmartRoom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-[#080b11] text-slate-100 selection:bg-indigo-500 selection:text-white">
    <main class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
        <section class="hidden lg:flex flex-col justify-between p-10 bg-slate-950 border-r border-slate-900">
            <a href="{{ route('smartroom.portal') }}" class="inline-flex items-center gap-3 text-sm font-bold text-slate-200">
                <span class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-hotel"></i>
                </span>
                SmartRoom
            </a>

            <div class="max-w-xl">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-300">Onboarding cho chu tro</p>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-white">Vao dashboard truoc, xac minh sau khi can nhan tien.</h1>
                <p class="mt-5 text-sm leading-7 text-slate-400">
                    Ban chi can so dien thoai, ten nha tro va dia chi de bat dau them phong. Ho so CCCD, ngan hang, PCCC va giay phep se duoc nhac dung thoi diem, khong lam ban bi ngop ngay tu dau.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-3 text-xs">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                    <strong class="block text-emerald-300">Step 1</strong>
                    <span class="text-slate-500">Tao nha tro</span>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                    <strong class="block text-amber-300">Step 2</strong>
                    <span class="text-slate-500">KYC khi co tien</span>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                    <strong class="block text-sky-300">Step 3</strong>
                    <span class="text-slate-500">Tich xanh</span>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center px-5 py-10">
            <div class="w-full max-w-xl">
                <div class="mb-8">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-300">Dang ky nhanh</p>
                    <h2 class="mt-3 text-2xl font-black text-white">Tao tai khoan chu tro</h2>
                    <p class="mt-2 text-sm text-slate-500">OTP demo hien tai: <strong class="text-slate-300">123456</strong></p>
                </div>

                @if($errors->any())
                    <div class="mb-5 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4 text-sm text-rose-200">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('landlord.register.store') }}" class="space-y-4 rounded-3xl border border-slate-800 bg-slate-900/45 p-6 shadow-2xl shadow-black/20">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="block">
                            <span class="block text-xs font-bold text-slate-400 mb-2">So dien thoai</span>
                            <input name="phone" value="{{ old('phone') }}" required placeholder="0988123456" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none focus:border-indigo-500">
                        </label>
                        <label class="block">
                            <span class="block text-xs font-bold text-slate-400 mb-2">OTP</span>
                            <input name="otp" value="{{ old('otp', '123456') }}" required maxlength="6" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none focus:border-indigo-500">
                        </label>
                    </div>

                    <label class="block">
                        <span class="block text-xs font-bold text-slate-400 mb-2">Ho va ten</span>
                        <input name="full_name" value="{{ old('full_name') }}" required maxlength="120" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none focus:border-indigo-500">
                    </label>

                    <label class="block">
                        <span class="block text-xs font-bold text-slate-400 mb-2">Ten nha tro mong muon</span>
                        <input name="property_name" value="{{ old('property_name') }}" required maxlength="160" placeholder="VD: Nha tro Minh Anh" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none focus:border-indigo-500">
                    </label>

                    <label class="block">
                        <span class="block text-xs font-bold text-slate-400 mb-2">Dia chi nha tro</span>
                        <textarea name="property_address" required maxlength="500" rows="3" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none focus:border-indigo-500 resize-none">{{ old('property_address') }}</textarea>
                    </label>

                    <label class="block">
                        <span class="block text-xs font-bold text-slate-400 mb-2">Mat khau</span>
                        <input type="password" name="password" required minlength="6" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none focus:border-indigo-500">
                    </label>

                    <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4 text-xs leading-6 text-amber-100">
                        Phong ban dang se co nhan "Chua xac minh" tren Renty. Khi can nhan tien tu dong, he thong moi yeu cau CCCD va tai khoan ngan hang.
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white hover:bg-indigo-500 active:scale-[0.99] transition">
                        Tao dashboard chu tro
                    </button>

                    <div class="text-center text-xs text-slate-500">
                        Da co tai khoan? <a href="{{ route('login') }}" class="font-bold text-indigo-300 hover:text-indigo-200">Dang nhap</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
