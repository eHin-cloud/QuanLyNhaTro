<div class="room-item-card glass-card rounded-2xl overflow-hidden group flex flex-col justify-between relative" 
     data-room-id="{{ $room['id'] }}"
     data-price="{{ $room['price'] }}" 
     data-rating="{{ $room['rating'] }}" 
     data-pets="{{ $room['pets'] }}" 
     data-loft="{{ $room['loft'] }}" 
     data-balcony="{{ $room['balcony'] }}" 
     data-wc="{{ $room['wc'] }}"
     data-distance="{{ $room['distance'] }}" 
     data-area-name="{{ $room['area_name'] }}"
     data-status="{{ $room['status'] }}"
     data-viewed="false"
     data-title="{{ $room['title'] }}"
     data-address="{{ $room['address'] }}"
     data-location-desc="{{ $room['location_description'] }}"
     data-space-desc="{{ $room['space_description'] }}"
     data-scenery-desc="{{ $room['scenery_description'] }}">
   <div class="room-card-skeleton" aria-hidden="true">
       <div class="skeleton-media"></div>
       <div class="skeleton-body">
           <div class="skeleton-row skeleton-row-title"></div>
           <div class="skeleton-row skeleton-row-rating"></div>
           <div class="skeleton-row skeleton-row-address"></div>
           <div class="skeleton-row skeleton-row-price"></div>
           <div class="skeleton-tags">
               <span></span>
               <span></span>
               <span></span>
           </div>
       </div>
       <div class="skeleton-footer">
           <div class="skeleton-check"></div>
           <div class="skeleton-action"></div>
       </div>
   </div>
   <div class="room-card-content">
       @php
           $cardImages = collect($room['image_urls'] ?? [])
               ->filter()
               ->prepend($room['cover_image'])
               ->unique()
               ->take(4)
               ->values();
       @endphp
       <!-- Room photo -->
       <div class="room-card-media h-48 bg-slate-950 relative overflow-hidden border-b border-slate-900 group">
           <a href="{{ route('renty.room.show', $room['id']) }}" class="absolute inset-0 z-0 renty-card-carousel" aria-label="Xem chi tiết phòng {{ $room['room_number'] }}">
               @foreach($cardImages as $imageIndex => $imageUrl)
                   <img src="{{ $imageUrl }}" alt="Ảnh {{ $imageIndex + 1 }} phòng {{ $room['room_number'] }}" class="renty-card-carousel-image" style="--carousel-delay: {{ $imageIndex * 2 }}s;" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
               @endforeach
               <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/10 to-transparent"></div>
           </a>

           @if($room['status'] === 'empty')
               <span class="room-status-badge room-status-empty absolute top-4 left-4 px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5">
                   <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                   Sẵn sàng
               </span>
           @else
               <span class="room-status-badge room-status-rented absolute top-4 left-4 px-2.5 py-1 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5">
                   <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                   Đã thuê
               </span>
               <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-[0.5px] z-10"></div>
               <div class="absolute inset-0 flex items-center justify-center z-20">
                   <button type="button" onclick="subscribeEmptyNotification(event, '{{ $room['id'] }}', '{{ $room['title'] }}')" class="py-2 px-3.5 rounded-xl bg-slate-900/95 hover:bg-slate-900 border border-slate-800 text-slate-200 hover:text-white flex items-center gap-2 text-[10px] font-extrabold shadow-lg transition-all renty-notify-btn group/bell-btn">
                       <i class="fa-solid fa-bell text-teal-400 text-xs"></i>
                       Chuông báo khi trống phòng
                   </button>
               </div>
           @endif

           <div class="absolute top-14 right-4 z-10 flex flex-col items-end gap-1.5">
               @if($room['price_warning'])
                   <span class="px-2.5 py-1 bg-amber-500/10 text-amber-300 border border-amber-500/25 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm flex items-center gap-1.5" title="{{ $room['price_warning']['message'] }}">
                       <i class="fa-solid fa-triangle-exclamation"></i>
                       {{ $room['price_warning']['type'] === 'low' ? 'Giá quá rẻ' : 'Giá cao' }}
                   </span>
               @endif

               <span class="px-2.5 py-1 border rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm flex items-center gap-1.5 {{ $room['trust_badge']['class'] }}">
                   <i class="fa-solid {{ $room['trust_badge']['icon'] }}"></i>
                   {{ $room['trust_badge']['label'] }}
               </span>
           </div>

           <button type="button" onclick="openQuickRoomPreview(event, '{{ $room['id'] }}')" class="absolute top-4 right-4 px-3 py-1.5 rounded-xl bg-slate-950/82 border border-white/10 text-[10px] font-extrabold text-slate-100 backdrop-blur z-20 flex items-center gap-1.5 hover:border-emerald-400/60 hover:text-emerald-200 quick-eye-button" title="Xem nhanh thông tin phòng">
               <i class="fa-solid fa-eye text-slate-300"></i>
               <span class="quick-eye-text">Xem nhanh</span>
           </button>

           <div class="absolute left-4 right-4 bottom-4 z-10 flex items-end justify-between gap-3">
               <div>
                   <span class="block text-[10px] font-extrabold text-white uppercase tracking-widest drop-shadow">{{ $room['media_source_label'] }} phòng</span>
                   <span class="block text-[10px] font-semibold text-slate-300 mt-0.5">Phòng {{ $room['room_number'] }} · {{ count($room['image_urls']) }} ảnh</span>
               </div>
               <span class="w-9 h-9 rounded-xl bg-slate-950/70 border border-white/10 backdrop-blur flex items-center justify-center text-emerald-300">
                   <i class="fa-solid fa-images"></i>
               </span>
           </div>
       </div>
       <!-- Details -->
       <div class="p-5 flex-grow flex flex-col justify-between">
           <div>
               <div class="viewed-room-strip" aria-hidden="true">
                   <span class="viewed-room-strip-icon">
                       <i class="fa-solid fa-eye"></i>
                       <i class="fa-solid fa-check"></i>
                   </span>
                   <span>ĐÃ XEM</span>
               </div>
               <div class="flex justify-between items-start mb-2 gap-2">
                   <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-all line-clamp-1">
                       <a href="{{ route('renty.room.show', $room['id']) }}">{{ $room['title'] }}</a>
                   </h3>
                   <div class="relative group/rating flex items-center gap-1 text-xs text-amber-400 font-bold shrink-0 cursor-pointer py-1 px-1.5 rounded-lg hover:bg-amber-500/10 transition-colors">
                       <i class="fa-solid fa-star text-[10px]"></i> <span>{{ $room['rating'] }}</span>
                       
                       <!-- Popover Rating Breakdown -->
                       <div class="rating-popover absolute bottom-full right-0 mb-3 w-64 bg-[#0b0f19]/95 border border-slate-800 p-4 rounded-2xl shadow-2xl opacity-0 invisible group-hover/rating:opacity-100 group-hover/rating:visible transition-all duration-300 transform translate-y-2 group-hover/rating:translate-y-0 z-30 pointer-events-none text-left">
                           <div class="flex items-center justify-between border-b border-slate-800/60 pb-2 mb-3">
                               <h4 class="text-[10px] font-extrabold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                                   <i class="fa-solid fa-chart-simple text-teal-400"></i>
                                   Điểm đánh giá thực tế
                               </h4>
                               <span class="text-[10px] text-slate-500 font-bold">{{ $room['rating'] }}/5</span>
                           </div>
                           
                           <div class="space-y-3">
                               <!-- An ninh -->
                               <div>
                                   <div class="flex justify-between items-center mb-1">
                                       <span class="text-[10px] text-slate-400 font-bold">An ninh & Trật tự</span>
                                       <div class="flex items-center gap-0.5 text-[8px] text-teal-400">
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-regular fa-star text-slate-700"></i>
                                       </div>
                                   </div>
                                   <div class="h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                                       <div class="h-full bg-teal-500 rounded-full" style="width: 80%"></div>
                                   </div>
                               </div>
                               
                               <!-- Chủ nhà -->
                               <div>
                                   <div class="flex justify-between items-center mb-1">
                                       <span class="text-[10px] text-slate-400 font-bold">Chủ nhà & Hỗ trợ</span>
                                       <div class="flex items-center gap-0.5 text-[8px] text-teal-400">
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                       </div>
                                   </div>
                                   <div class="h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                                       <div class="h-full bg-teal-500 rounded-full" style="width: 100%"></div>
                                   </div>
                               </div>
                               
                               <!-- Giá cả & Điện nước -->
                               <div>
                                   <div class="flex justify-between items-center mb-1">
                                       <span class="text-[10px] text-slate-400 font-bold">Giá cả & Điện nước</span>
                                       <div class="flex items-center gap-0.5 text-[8px] text-teal-400">
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star"></i>
                                           <i class="fa-solid fa-star-half-stroke"></i>
                                       </div>
                                   </div>
                                   <div class="h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                                       <div class="h-full bg-teal-500 rounded-full" style="width: 90%"></div>
                                   </div>
                               </div>
                           </div>
                           
                           <!-- Arrow Pointing Down -->
                           <div class="absolute top-full right-4 -mt-1.5 w-3 h-3 bg-[#0b0f19] border-r border-b border-slate-800 transform rotate-45"></div>
                       </div>
                   </div>
               </div>
               <p class="text-xs text-slate-500 mb-4 flex items-center gap-1"><i class="fa-solid fa-map-marker-alt text-[10px] text-slate-650"></i> {{ $room['address'] }}</p>
               <!-- Prices and tags -->
               <div class="flex items-baseline gap-1.5 mb-4">
                   <span class="text-xl font-extrabold text-emerald-400">{{ number_format($room['price'], 0, ',', '.') }}đ</span>
                   <span class="text-[10px] text-slate-500 font-semibold">/ tháng</span>
               </div>
               @if($room['price_warning'])
                   <div class="mb-3 px-3 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[10px] text-amber-200 font-bold flex items-start gap-2">
                       <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                       <span>{{ $room['price_warning']['label'] }}</span>
                   </div>
               @endif
           </div>
           <div class="flex flex-wrap gap-1.5">
               @if($room['loft'] === 'true')
                   <span class="px-2.5 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 text-[9px] font-bold border border-indigo-500/20">Có gác lửng</span>
               @endif
               @if($room['pets'] === 'true')
                   <span class="px-2.5 py-0.5 rounded-md bg-teal-500/10 text-teal-400 text-[9px] font-bold border border-teal-500/20">Nuôi thú cưng</span>
               @endif
               @if($room['balcony'] === 'true')
                   <span class="px-2.5 py-0.5 rounded-md bg-sky-500/10 text-sky-400 text-[9px] font-bold border border-sky-500/20">Ban công</span>
               @endif
               @if($room['wc'] === 'true')
                   <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 text-[9px] font-bold border border-emerald-500/20">WC khép kín</span>
               @else
                   <span class="px-2.5 py-0.5 rounded-md bg-slate-900/60 text-slate-500 text-[9px] font-bold border border-slate-800/40">WC chung</span>
               @endif
           </div>
       </div>
   </div>
   <!-- Card footer action -->
   <div class="px-5 pb-5 pt-3 border-t border-slate-900/50 flex justify-between items-center bg-slate-950/20 gap-3">
       <label class="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer hover:text-slate-355 transition-colors">
           <input type="checkbox" onchange="toggleCompare('{{ $room['id'] }}', this)" class="compare-checkbox w-4 h-4 rounded border-slate-800 bg-slate-900 text-emerald-600 focus:ring-0 focus:ring-offset-0">
           <span>So sánh</span>
       </label>
       <div class="flex items-center gap-3">
           <button type="button" onclick="openReportModal('{{ $room['id'] }}', '{{ e($room['title']) }}')" class="room-report-button text-xs text-rose-400 hover:text-rose-300 font-bold flex items-center gap-1">
               <i class="fa-solid fa-flag"></i> Báo cáo
           </button>
           <a href="{{ route('renty.room.show', $room['id']) }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
               <span>Chi tiết review</span> <i class="fa-solid fa-angle-right"></i>
           </a>
       </div>
   </div>
</div>
