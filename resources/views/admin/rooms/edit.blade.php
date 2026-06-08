<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Chỉnh sửa thông tin phòng trọ - SmartRoom.">
    <title>Chỉnh Sửa Phòng Trọ - SmartRoom</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .glass-card {
            background: rgba(13, 18, 31, 0.45);
            backdrop-filter: blur(16px);
            border: 1px border-slate-800/80;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen flex selection:bg-indigo-500 selection:text-white overflow-hidden">

    <!-- Decorative glows -->
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] rounded-full bg-indigo-600/5 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] rounded-full bg-emerald-600/5 blur-[100px] pointer-events-none"></div>

    <!-- SIDEBAR -->
    <aside class="w-64 bg-[#0d121f] border-r border-slate-900 flex flex-col justify-between h-screen shrink-0 relative z-20">
        <div>
            <div class="p-6 border-b border-slate-900 flex items-center justify-between">
                <a href="{{ route('smartroom.admin') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid fa-hotel text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-extrabold tracking-tight bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">SmartRoom</span>
                </a>
            </div>
            
            <nav class="p-4 space-y-1">
                <a href="{{ route('smartroom.admin') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-chart-pie text-lg"></i>
                    <span>Tổng Quan</span>
                </a>
                <a href="{{ route('admin.rooms.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-indigo-400 bg-indigo-500/10 border border-indigo-500/10 transition-all duration-200">
                    <i class="fa-solid fa-door-open text-lg"></i>
                    <span>Quản Lý Phòng</span>
                </a>
                <a href="{{ route('admin.equipment.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    <span>Thiết Bị</span>
                </a>
                <a href="{{ route('admin.activity_logs.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 border border-transparent hover:border-slate-800 transition-all duration-200">
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
            <a href="{{ route('signout') }}" class="mt-3 w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 transition-all duration-200">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất
            </a>
        </div>
    </aside>

    <!-- MAIN APP WRAPPER -->
    <div class="flex-grow flex flex-col h-screen overflow-y-auto relative z-10">
        
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-slate-100">Chỉnh Sửa Thông Tin Phòng Trọ</h2>
            </div>
        </header>

        <main class="p-8 flex-grow overflow-y-auto">

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Container -->
            <div class="glass-card rounded-2xl p-8 border border-slate-800/40 relative max-w-3xl mx-auto">
                <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                
                <h3 class="text-lg font-bold text-slate-200 mb-6 flex items-center gap-2">
                    <i class="fa-regular fa-pen-to-square text-indigo-400"></i> Chỉnh Sửa Phòng Trọ P.{{ $room->room_number }}
                </h3>

                <form id="edit-room-form" action="{{ route('admin.rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- OPTIMISTIC LOCKING: Trường ẩn version -->
                    <input type="hidden" name="version" value="{{ $room->version }}">

                    <!-- Tòa nhà và số phòng -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tòa Nhà <span class="text-rose-500">*</span></label>
                            <select name="building_id" id="building_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}" {{ $room->building_id == $building->id ? 'selected' : '' }}>{{ $building->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-building"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số Phòng <span class="text-rose-500">*</span></label>
                            <input type="text" name="room_number" id="room_number" required 
                                   value="{{ old('room_number', $room->room_number) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                   placeholder="Ví dụ: 101, 202" onblur="validateRoomNumber()">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-room_number"></span>
                        </div>
                    </div>

                    <!-- Tầng, Diện tích, Trạng thái, Loại phòng -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tầng <span class="text-rose-500">*</span></label>
                            <input type="text" name="floor" id="floor" required 
                                   value="{{ old('floor', $room->floor) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                   placeholder="Ví dụ: 1, 2" oninput="sanitizeNumberInput(this)" onblur="validateFloor()">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-floor"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Diện Tích (m²) <span class="text-rose-500">*</span></label>
                            <input type="text" name="area" id="area" required 
                                   value="{{ old('area', $room->area) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                   placeholder="Ví dụ: 25" oninput="sanitizeNumberInput(this)" onblur="validateArea()">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-area"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Trạng Thái <span class="text-rose-500">*</span></label>
                            <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="empty" {{ $room->status === 'empty' ? 'selected' : '' }}>Trống</option>
                                <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>Đầy (Đang thuê)</option>
                                <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Đang sửa chữa</option>
                                <option value="overdue" {{ $room->status === 'overdue' ? 'selected' : '' }}>Nợ tiền</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Loại Phòng <span class="text-rose-500">*</span></label>
                            <select name="room_type" id="room_type" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="normal" {{ ($room->room_type ?? 'normal') === 'normal' ? 'selected' : '' }}>Thường</option>
                                <option value="vip" {{ ($room->room_type ?? 'normal') === 'vip' ? 'selected' : '' }}>VIP</option>
                            </select>
                        </div>
                    </div>

                    <!-- Giá và hình ảnh -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Giá Thuê / Tháng (VND) <span class="text-rose-500">*</span></label>
                            <input type="text" name="price" id="price" required 
                                   value="{{ old('price', $room->price) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                   placeholder="Ví dụ: 3000000" oninput="sanitizeNumberInput(this)" onblur="validatePrice()">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-price"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hình Ảnh (Giữ trống nếu không thay đổi)</label>
                            <input type="file" name="images[]" id="images" accept="image/*" multiple
                                   class="w-full px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none"
                                   onchange="previewImages(this)">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-image"></span>
                            
                            <!-- Hiển thị ảnh cũ hoặc preview ảnh mới, tự động cấu hình onerror placeholder -->
                            <div class="mt-3" id="preview-box">
                                <span class="block text-[10px] text-slate-500 mb-1">Hình ảnh hiện tại / Preview:</span>
                                @php
                                    $roomImages = is_array($room->images) ? $room->images : [];
                                    if ($room->image && !in_array($room->image, $roomImages)) {
                                        array_unshift($roomImages, $room->image);
                                    }
                                @endphp
                                <div id="preview-list" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @forelse($roomImages as $roomImage)
                                        <img src="{{ asset('storage/' . $roomImage) }}" class="h-24 w-full object-cover rounded-lg border border-slate-800"
                                             onerror="this.onerror=null; this.src='https://placehold.co/100x80/0f172a/6366f1?text=Image+Broken';">
                                    @empty
                                        <img src="https://placehold.co/100x80/0f172a/6366f1?text=No+Image" class="h-24 w-full object-cover rounded-lg border border-slate-800">
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Video giới thiệu phòng</label>
                        <input type="file" name="video" id="video" accept="video/mp4,video/webm,video/quicktime"
                               class="w-full px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none"
                               onchange="previewVideo(this)">
                        <span class="text-xs text-rose-400 mt-1 hidden" id="err-video"></span>
                        <div class="mt-3 {{ $room->video ? '' : 'hidden' }}" id="video-preview-box">
                            <video id="preview-video" class="w-full max-h-64 rounded-lg border border-slate-800 bg-black" controls
                                   @if($room->video) src="{{ asset('storage/' . $room->video) }}" @endif></video>
                        </div>
                    </div>

                    <!-- Tiện ích (Checkbox) -->
                    @php
                        $roomAmenities = is_array($room->amenities) ? $room->amenities : [];
                    @endphp
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Tiện Ích Đi Kèm</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="gác lửng" {{ in_array('gác lửng', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Gác lửng</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="điều hòa" {{ in_array('điều hòa', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Điều hòa</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="nước nóng" {{ in_array('nước nóng', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Bình nóng lạnh</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="máy giặt" {{ in_array('máy giặt', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Máy giặt riêng</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="tủ lạnh" {{ in_array('tủ lạnh', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Tủ lạnh</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="ban công" {{ in_array('ban công', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Ban công thoáng</span>
                            </label>
                        </div>
                    </div>

                    <!-- Mô tả -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mô Tả Chi Tiết (Không chứa mã HTML)</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                  placeholder="Nhập thông tin mô tả chi tiết phòng trọ..." onblur="validateDescription()">{{ old('description', $room->description) }}</textarea>
                        <span class="text-xs text-rose-400 mt-1 hidden" id="err-description"></span>
                    </div>

                    <!-- Buttons -->
                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-900">
                        <a href="{{ route('admin.rooms.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                            Hủy bỏ
                        </a>
                        <button type="submit" id="submit-btn" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> Cập Nhật Phòng
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- JS VALIDATION, CHỐNG F12 & CHỐNG SPAM CLICK -->
    <script>
        function cleanString(str) {
            if (!str) return '';
            let cleaned = str.replace(/　/g, ' ');
            const fullWidth = ['０', '１', '２', '３', '４', '５', '６', '７', '８', '９'];
            const halfWidth = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            for (let i = 0; i < 10; i++) {
                cleaned = cleaned.replace(new RegExp(fullWidth[i], 'g'), halfWidth[i]);
            }
            return cleaned.trim();
        }

        function sanitizeNumberInput(input) {
            input.value = cleanString(input.value).replace(/[^0-9]/g, '');
        }

        function validateRoomNumber() {
            const input = document.getElementById('room_number');
            input.value = cleanString(input.value);
            const errSpan = document.getElementById('err-room_number');
            if (input.value === '') {
                errSpan.textContent = 'Số phòng không được bỏ trống.';
                errSpan.classList.remove('hidden');
                return false;
            }
            errSpan.classList.add('hidden');
            return true;
        }

        function validateFloor() {
            const input = document.getElementById('floor');
            input.value = cleanString(input.value);
            const errSpan = document.getElementById('err-floor');
            if (input.value === '' || isNaN(input.value) || parseInt(input.value) <= 0) {
                errSpan.textContent = 'Số tầng phải là số nguyên dương hợp lệ.';
                errSpan.classList.remove('hidden');
                return false;
            }
            errSpan.classList.add('hidden');
            return true;
        }

        function validateArea() {
            const input = document.getElementById('area');
            input.value = cleanString(input.value);
            const errSpan = document.getElementById('err-area');
            if (input.value === '' || isNaN(input.value) || parseInt(input.value) <= 0) {
                errSpan.textContent = 'Diện tích phải là số nguyên dương hợp lệ.';
                errSpan.classList.remove('hidden');
                return false;
            }
            errSpan.classList.add('hidden');
            return true;
        }

        function validatePrice() {
            const input = document.getElementById('price');
            input.value = cleanString(input.value);
            const errSpan = document.getElementById('err-price');
            if (input.value === '' || isNaN(input.value) || parseInt(input.value) < 0) {
                errSpan.textContent = 'Giá thuê phải là số nguyên dương hợp lệ.';
                errSpan.classList.remove('hidden');
                return false;
            }
            errSpan.classList.add('hidden');
            return true;
        }

        function validateDescription() {
            const input = document.getElementById('description');
            const errSpan = document.getElementById('err-description');
            if (input.value.length > 1000) {
                errSpan.textContent = 'Mô tả không được vượt quá 1000 ký tự.';
                errSpan.classList.remove('hidden');
                return false;
            }
            input.value = input.value.replace(/<\/?[^>]+(>|$)/g, "");
            errSpan.classList.add('hidden');
            return true;
        }

        function previewImages(input) {
            const errSpan = document.getElementById('err-image');
            const previewList = document.getElementById('preview-list');
            const files = Array.from(input.files || []);

            if (files.length === 0) {
                return;
            }

            if (files.length > 10) {
                errSpan.textContent = 'Chi duoc chon toi da 10 hinh anh.';
                errSpan.classList.remove('hidden');
                input.value = '';
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            for (const file of files) {
                if (!allowedTypes.includes(file.type)) {
                    errSpan.textContent = 'Chi chap nhan dinh dang hinh anh jpg, png, webp, gif.';
                    errSpan.classList.remove('hidden');
                    input.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    errSpan.textContent = 'Moi hinh anh khong duoc vuot qua 2MB.';
                    errSpan.classList.remove('hidden');
                    input.value = '';
                    return;
                }
            }

            errSpan.classList.add('hidden');
            previewList.innerHTML = '';

            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'h-24 w-full object-cover rounded-lg border border-slate-800';
                    previewList.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        function previewVideo(input) {
            const file = input.files[0];
            const errSpan = document.getElementById('err-video');
            const previewBox = document.getElementById('video-preview-box');
            const previewVideo = document.getElementById('preview-video');

            if (!file) {
                return;
            }

            const allowedTypes = ['video/mp4', 'video/webm', 'video/quicktime'];
            if (!allowedTypes.includes(file.type)) {
                errSpan.textContent = 'Video phai co dinh dang mp4, webm hoac mov.';
                errSpan.classList.remove('hidden');
                input.value = '';
                return;
            }

            if (file.size > 50 * 1024 * 1024) {
                errSpan.textContent = 'Dung luong video khong duoc vuot qua 50MB.';
                errSpan.classList.remove('hidden');
                input.value = '';
                return;
            }

            errSpan.classList.add('hidden');
            previewVideo.src = URL.createObjectURL(file);
            previewBox.classList.remove('hidden');
        }
        const form = document.getElementById('edit-room-form');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const isOk = validateRoomNumber() && validateFloor() && validateArea() && validatePrice() && validateDescription();
            if (!isOk) {
                alert('Vui lòng kiểm tra lại thông tin form.');
                return false;
            }

            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang lưu...';
            
            form.submit();
        });

        // 2. Chặn các phím tắt mở DevTools
        document.addEventListener('keydown', function (e) {
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                alert('Hành động can thiệp hệ thống bị chặn!');
                return false;
            }
            if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C' || e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) {
                e.preventDefault();
                alert('Hành động can thiệp hệ thống bị chặn!');
                return false;
            }
            if (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.keyCode === 85)) {
                e.preventDefault();
                alert('Hành động can thiệp hệ thống bị chặn!');
                return false;
            }
        });

        // 3. Chặn chuột phải trên toàn trang
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            alert('Chuột phải đã bị vô hiệu hóa để bảo mật!');
            return false;
        });

        // 4. MutationObserver giám sát việc hack sửa thuộc tính DOM
        const targetBtn = document.getElementById('submit-btn');
        const observer = new MutationObserver((mutationsList) => {
            for (let mutation of mutationsList) {
                if (mutation.type === 'attributes') {
                    alert('Phát hiện hành vi can thiệp hệ thống!');
                    window.location.reload();
                }
            }
        });
        observer.observe(targetBtn, { attributes: true });
    </script>
</body>
</html>
