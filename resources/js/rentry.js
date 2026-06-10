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
            <div class="custom-toast-close"><i class="fa-solid fa-xmark"></i></div>
        `;
        
        // Inline close click handler
        toast.querySelector('.custom-toast-close').addEventListener('click', () => {
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 400);
        });

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

// Show session success or error toasts
function showSessionToasts() {
    if (window.rentySessionSuccess) {
        alert(window.rentySessionSuccess);
    }
    if (window.rentySessionError) {
        alert(window.rentySessionError);
    }
}
if (document.readyState === 'loading') {
    window.addEventListener('DOMContentLoaded', showSessionToasts);
} else {
    showSessionToasts();
}

// Toggle advanced filters
function toggleFilterDrawer() {
    const drawer = document.getElementById('filter-drawer');
    drawer.classList.toggle('hidden');
}

function applyThemeMode(mode) {
    const isLight = mode === 'light';
    document.documentElement.classList.toggle('theme-light', isLight);
    document.body.classList.toggle('theme-light', isLight);
    document.querySelectorAll('[data-theme-icon], #theme-toggle-icon').forEach(icon => {
        if (!icon.classList.contains('theme-switch-icon')) {
            icon.classList.toggle('fa-sun', isLight);
            icon.classList.toggle('fa-moon', !isLight);
        }
    });
    document.querySelectorAll('[data-theme-switch]').forEach(button => {
        button.classList.toggle('is-light', isLight);
        button.setAttribute('aria-pressed', isLight ? 'true' : 'false');
    });
}

function toggleThemeMode() {
    const nextMode = document.body.classList.contains('theme-light') ? 'dark' : 'light';
    localStorage.setItem('renty_theme_mode', nextMode);
    document.body.classList.remove('theme-flipping');
    void document.body.offsetWidth;
    document.body.classList.add('theme-flipping');
    document.querySelectorAll('[data-theme-switch]').forEach(button => {
        button.classList.remove('is-animating');
        void button.offsetWidth;
        button.classList.add('is-animating');
    });
    applyThemeMode(nextMode);
}

// Read localStorage theme on run
applyThemeMode(localStorage.getItem('renty_theme_mode') || 'dark');

function initThemeAnimations() {
    document.getElementById('theme-flip-wash')?.addEventListener('animationend', () => {
        document.body.classList.remove('theme-flipping');
    });

    document.querySelectorAll('[data-theme-switch]').forEach(button => {
        button.addEventListener('animationend', () => button.classList.remove('is-animating'));
    });
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemeAnimations);
} else {
    initThemeAnimations();
}

let currentDetailRoomId = null;
let activeRoomReviews = [];
let activeRoomImages = [];
let activeRoomImageIndex = 0;
const MOVE_IN_MAX_PEOPLE = 5;
let activeRoomCost = {
    room: 0,
    people: 2,
    vehicles: 1
};

function formatCurrency(value) {
    return Number(value || 0).toLocaleString('vi-VN') + 'đ';
}

function formatShortPrice(value) {
    const millions = Number(value || 0) / 1000000;
    return `${millions.toFixed(millions % 1 === 0 ? 0 : 1)}tr/tháng`;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function openQuickRoomPreview(event, roomId) {
    event.preventDefault();
    event.stopPropagation();

    const mockRooms = window.rentyRoomsData || {};
    const data = mockRooms[roomId];
    if (!data) return;

    const modal = document.getElementById('quick-room-preview');
    document.getElementById('quick-preview-image').src = data.cover_image;
    document.getElementById('quick-preview-media-label').textContent = `${data.media_source_label || 'Ảnh phòng'} · ${Array.isArray(data.image_urls) ? data.image_urls.length : 1} ảnh`;
    document.getElementById('quick-preview-title').textContent = data.title;
    document.getElementById('quick-preview-price').textContent = formatCurrency(data.price);
    document.getElementById('quick-preview-rating').textContent = `${data.rating} ⭐`;
    document.getElementById('quick-preview-area').textContent = data.area_text || `${data.area || 0} m²`;
    document.getElementById('quick-preview-location').textContent = data.area_name || 'Khu vực trung tâm';
    document.getElementById('quick-preview-video').textContent = data.video_url ? 'Có video tour' : 'Chưa có video tour';
    document.getElementById('quick-preview-detail').href = `/renty/room/${data.id}`;

    const tags = [
        data.loft_txt === 'Có' ? 'Có gác lửng' : 'Không gác lửng',
        data.balcony_txt === 'Có' ? 'Ban công/cửa sổ' : 'Không ban công',
        data.pets_txt === 'Có' ? 'Cho nuôi thú cưng' : 'Không thú cưng',
    ];
    document.getElementById('quick-preview-tags').innerHTML = tags
        .map(tag => `<span>${escapeHtml(tag)}</span>`)
        .join('');

    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeQuickRoomPreview() {
    const modal = document.getElementById('quick-room-preview');
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openReportModal(roomId, title) {
    const modal = document.getElementById('room-report-modal');
    const form = document.getElementById('room-report-form');
    form.action = `/renty/room/${roomId}/report`;
    document.getElementById('report-room-title').textContent = title || 'Renty Review sẽ kiểm tra báo cáo này.';
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeReportModal() {
    document.getElementById('room-report-modal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function updateMoveInCost(type, delta) {
    if (type === 'people') {
        activeRoomCost.people = Math.min(MOVE_IN_MAX_PEOPLE, Math.max(1, activeRoomCost.people + delta));
    }

    if (type === 'vehicles') {
        activeRoomCost.vehicles = Math.max(0, activeRoomCost.vehicles + delta);
    }

    const room = Number(activeRoomCost.room || 0);
    const deposit = room;
    const electric = 350000;
    const water = activeRoomCost.people * 20000;
    const service = 100000 + activeRoomCost.vehicles * 50000;
    const total = room + deposit + electric + water + service;

    document.getElementById('cost-people').textContent = activeRoomCost.people;
    document.getElementById('cost-vehicles').textContent = activeRoomCost.vehicles;
    document.getElementById('cost-room').textContent = formatCurrency(room);
    document.getElementById('cost-deposit').textContent = formatCurrency(deposit);
    document.getElementById('cost-electric').textContent = formatCurrency(electric);
    document.getElementById('cost-water').textContent = formatCurrency(water);
    document.getElementById('cost-service').textContent = formatCurrency(service);
    document.getElementById('cost-total').textContent = formatCurrency(total);

    const peopleMinus = document.getElementById('cost-people-minus');
    const peoplePlus = document.getElementById('cost-people-plus');
    peopleMinus.disabled = activeRoomCost.people <= 1;
    peoplePlus.disabled = activeRoomCost.people >= MOVE_IN_MAX_PEOPLE;
    peopleMinus.classList.toggle('opacity-40', peopleMinus.disabled);
    peoplePlus.classList.toggle('opacity-40', peoplePlus.disabled);
    peopleMinus.classList.toggle('cursor-not-allowed', peopleMinus.disabled);
    peoplePlus.classList.toggle('cursor-not-allowed', peoplePlus.disabled);
}

function toggleDetailDescription(button) {
    const description = document.getElementById('detail-full-description');
    description.classList.toggle('detail-description-clamped');
    button.textContent = description.classList.contains('detail-description-clamped') ? 'Xem thêm' : 'Thu gọn';
}

function renderReviewSummary(data) {
    const average = Number(data.rating || 0);
    const reviewCount = Array.isArray(data.reviews) ? data.reviews.length : 0;
    const criteria = [
        ['Sạch sẽ', Math.min(5, average + 0.1)],
        ['Vị trí', Math.max(3.5, average - 0.1)],
        ['Chủ nhà', Math.min(5, average + 0.05)],
        ['Giá cả', Math.max(3.5, average - 0.2)]
    ];

    document.getElementById('review-average-score').textContent = average.toFixed(1);
    document.getElementById('review-average-stars').textContent = '★'.repeat(Math.round(average)) + '☆'.repeat(5 - Math.round(average));
    document.getElementById('review-count-label').textContent = reviewCount > 0 ? `${reviewCount} đánh giá` : 'Chưa có đánh giá';
    document.getElementById('review-score-bars').innerHTML = criteria.map(([label, score]) => `
        <div class="review-score-row">
            <span>${label}</span>
            <div><i style="width: ${(score / 5) * 100}%"></i></div>
            <strong>${score.toFixed(1)}</strong>
        </div>
    `).join('');
}

function renderReviews(showAll = false) {
    const container = document.getElementById('detail-reviews-container');
    const button = document.getElementById('show-all-reviews-btn');
    container.innerHTML = '';

    if (!activeRoomReviews.length) {
        container.innerHTML = `
            <div class="py-4 text-center text-xs text-slate-500 italic">
                Chưa có đánh giá thực tế nào cho phòng này. Hãy là người đầu tiên đánh giá!
            </div>
        `;
        button.classList.add('hidden');
        return;
    }

    activeRoomReviews.slice(0, showAll ? activeRoomReviews.length : 2).forEach(rev => {
        const rating = Math.max(1, Math.min(5, Number(rev.rating || 5)));
        const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
        const item = document.createElement('div');
        item.className = 'p-3 rounded-xl bg-slate-900/60 border border-slate-800/50 space-y-1.5';
        item.innerHTML = `
            <div class="flex justify-between items-center text-xs gap-3">
                <span class="font-bold text-slate-300">${escapeHtml(rev.author_name)}</span>
                <span class="text-amber-400 font-semibold whitespace-nowrap">${stars}</span>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">${escapeHtml(rev.comment)}</p>
            <span class="block text-[9px] text-slate-600">${escapeHtml(rev.created_at)}</span>
        `;
        container.appendChild(item);
    });

    if (activeRoomReviews.length > 2) {
        button.classList.remove('hidden');
        button.textContent = showAll ? 'Thu gọn đánh giá' : 'Xem tất cả đánh giá';
        button.dataset.expanded = showAll ? 'true' : 'false';
    } else {
        button.classList.add('hidden');
    }
}

function toggleAllReviews() {
    const button = document.getElementById('show-all-reviews-btn');
    renderReviews(button.dataset.expanded !== 'true');
}

function normalizeRoomImages(data) {
    const rawAngles = Array.isArray(data.image_angles) ? data.image_angles : [];
    const rawUrls = Array.isArray(data.image_urls) && data.image_urls.length > 0 ? data.image_urls : [data.cover_image];

    return rawUrls.filter(Boolean).map((url, index) => {
        const angle = rawAngles[index] || {};
        return {
            url,
            label: angle.label || `Ảnh thực tế ${index + 1}`
        };
    });
}

function setActiveRoomImage(index) {
    if (!activeRoomImages.length) return;

    activeRoomImageIndex = Math.max(0, Math.min(activeRoomImages.length - 1, index));
    const image = activeRoomImages[activeRoomImageIndex];
    const mainImage = document.getElementById('detail-main-image');
    const angle = document.getElementById('detail-image-angle');

    mainImage.src = image.url;
    angle.textContent = image.label;
    document.querySelectorAll('#detail-image-thumbs button').forEach((btn, btnIndex) => {
        btn.classList.toggle('border-emerald-400', btnIndex === activeRoomImageIndex);
    });
}

function openImageZoom() {
    if (!activeRoomImages.length) return;

    const modal = document.getElementById('image-zoom-modal');
    modal.classList.remove('hidden');
    renderZoomImage();
}

function renderZoomImage() {
    const image = activeRoomImages[activeRoomImageIndex];
    if (!image) return;

    document.getElementById('zoom-main-image').src = image.url;
    document.getElementById('zoom-image-label').textContent = image.label;
    document.getElementById('zoom-image-count').textContent = `${activeRoomImageIndex + 1}/${activeRoomImages.length}`;
}

function changeZoomImage(delta) {
    if (!activeRoomImages.length) return;

    activeRoomImageIndex = (activeRoomImageIndex + delta + activeRoomImages.length) % activeRoomImages.length;
    setActiveRoomImage(activeRoomImageIndex);
    renderZoomImage();
}

function closeImageZoom() {
    document.getElementById('image-zoom-modal').classList.add('hidden');
}

function getViewedRoomIds() {
    try {
        return JSON.parse(localStorage.getItem('renty_viewed_rooms') || '[]');
    } catch (error) {
        return [];
    }
}

// Save room ID viewed in localStorage
function saveViewedRoom(roomId) {
    const normalizedId = String(roomId);
    const viewedIds = getViewedRoomIds().filter(id => id !== normalizedId);
    viewedIds.unshift(normalizedId);
    localStorage.setItem('renty_viewed_rooms', JSON.stringify(viewedIds.slice(0, 6)));
    renderViewedRooms();
}

function clearViewedRooms() {
    localStorage.removeItem('renty_viewed_rooms');
    document.querySelectorAll('.room-item-card').forEach(card => {
        card.dataset.viewed = 'false';
        card.classList.remove('room-card-viewed');
    });
    renderViewedRooms();
}

function renderViewedRooms() {
    const section = document.getElementById('viewed-rooms-section');
    const list = document.getElementById('viewed-rooms-list');
    const viewedIds = getViewedRoomIds();
    const mockRooms = window.rentyRoomsData || {};

    document.querySelectorAll('.room-item-card').forEach(card => {
        const isViewed = viewedIds.includes(String(card.dataset.roomId));
        card.dataset.viewed = isViewed ? 'true' : 'false';
        card.classList.toggle('room-card-viewed', isViewed);
    });

    if (!section || !list) return;

    const viewedRooms = viewedIds.map(id => mockRooms[id]).filter(Boolean);
    if (!viewedRooms.length) {
        section.classList.add('hidden');
        list.innerHTML = '';
        return;
    }

    section.classList.remove('hidden');
    list.innerHTML = viewedRooms.map(room => `
        <a href="/renty/room/${room.id}" class="viewed-room-chip">
            <img src="${room.cover_image}" alt="Phòng ${room.room_number}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
            <span>
                <strong>${escapeHtml(room.title)}</strong>
                <small>${Number(room.price || 0).toLocaleString('vi-VN')}đ/tháng · ${escapeHtml(room.area_text || '')}</small>
            </span>
        </a>
    `).join('');
}

function parseNaturalSearch(query) {
    const normalized = query
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd');

    const parsed = {
        maxPrice: null,
        keywords: normalized.split(/\s+/).filter(Boolean),
        locations: [],
        amenities: {
            pets: normalized.includes('thu cung') || normalized.includes('pet'),
            loft: normalized.includes('gac') || normalized.includes('gac lung'),
            balcony: normalized.includes('ban cong'),
            wc: normalized.includes('khep kin') || normalized.includes('wc') || normalized.includes('ve sinh')
        },
        near: []
    };

    const priceMatch = normalized.match(/(?:duoi|nho hon|toi da|<=?)\s*(\d+(?:[.,]\d+)?)\s*(trieu|tr|m|000000)?/);
    if (priceMatch) {
        const amount = parseFloat(priceMatch[1].replace(',', '.'));
        parsed.maxPrice = amount < 100000 ? amount * 1000000 : amount;
    }

    const locationAliases = [
        ['cau giay', 'cầu giấy'],
        ['thanh xuan', 'thanh xuân'],
        ['quan 10', 'quận 10'],
        ['bach khoa', 'bách khoa'],
        ['dai hoc bach khoa', 'đại học bách khoa'],
        ['su pham', 'sư phạm'],
        ['quoc gia', 'quốc gia'],
        ['xuan thuy', 'xuân thủy']
    ];

    locationAliases.forEach(([plain, label]) => {
        if (normalized.includes(plain)) {
            parsed.locations.push(plain);
            parsed.near.push(label);
        }
    });

    return parsed;
}

function normalizeText(value) {
    return String(value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd');
}

// Room details modal
function openRoomDetailModal(roomId) {
    const mockRooms = window.rentyRoomsData || {};
    const data = mockRooms[roomId];
    if (!data) return;

    currentDetailRoomId = roomId;
    const summaryBox = document.getElementById('review-summary-box');
    summaryBox.classList.add('hidden');
    summaryBox.innerHTML = '';

    document.getElementById('detail-room-title').textContent = data.title;
    document.getElementById('detail-room-address').textContent = data.address;
    document.getElementById('detail-media-note').textContent = data.media_source_note || 'Ưu tiên ảnh thật theo từng góc, xem rõ trước khi liên hệ đặt lịch.';
    document.getElementById('detail-room-price').textContent = data.price.toLocaleString('vi-VN') + "đ/tháng";
    document.getElementById('sticky-room-price').textContent = formatShortPrice(data.price);
    document.getElementById('detail-room-rating').textContent = data.rating + " ⭐";
    document.getElementById('detail-room-owner').textContent = data.owner;
    document.getElementById('detail-room-sec').textContent = data.sec;
    document.getElementById('detail-room-pets').textContent = data.pets_txt;
    document.getElementById('detail-room-loft').textContent = data.loft_txt;
    document.getElementById('detail-room-balcony').textContent = data.balcony_txt;
    document.getElementById('detail-room-area').textContent = data.area_text;
    document.getElementById('detail-room-area-name').textContent = (data.address || '').split('(')[0].trim() || 'Khu vực trung tâm';

    const fullDescription = [
        data.location_description,
        data.scenery_description,
        data.space_description,
        `Tiện ích nổi bật: ${data.loft_txt === 'Có' ? 'có gác lửng' : 'không gác lửng'}, ${data.balcony_txt === 'Có' ? 'có ban công' : 'không ban công'}, ${data.pets_txt === 'Có' ? 'có thể nuôi thú cưng' : 'không nuôi thú cưng'}.`
    ].filter(Boolean).join(' ');
    const description = document.getElementById('detail-full-description');
    description.textContent = fullDescription;
    description.classList.add('detail-description-clamped');
    const descButton = description.nextElementSibling;
    if (descButton) descButton.textContent = 'Xem thêm';

    activeRoomCost = {
        room: Number(data.price || 0),
        people: 2,
        vehicles: 1
    };
    updateMoveInCost();

    const images = Array.isArray(data.image_urls) && data.image_urls.length > 0 ? data.image_urls : [data.cover_image];
    const mainImage = document.getElementById('detail-main-image');
    const imageCount = document.getElementById('detail-image-count');
    const thumbs = document.getElementById('detail-image-thumbs');
    const videoSection = document.getElementById('detail-video-section');
    const videoEmpty = document.getElementById('detail-video-empty');
    const roomVideo = document.getElementById('detail-room-video');

    activeRoomImages = normalizeRoomImages(data);
    activeRoomImageIndex = 0;
    mainImage.alt = `Ảnh phòng ${data.room_number}`;
    imageCount.textContent = `${images.length} ảnh phòng ${data.room_number}`;
    thumbs.innerHTML = '';
    setActiveRoomImage(0);

    activeRoomImages.slice(0, 6).forEach((image, index) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'relative h-20 rounded-xl overflow-hidden border border-slate-800 hover:border-emerald-500/70 transition-all focus:outline-none focus:border-emerald-400';
        button.innerHTML = `
            <img src="${image.url}" alt="${escapeHtml(image.label)} phòng ${data.room_number}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
            <span class="absolute left-1.5 right-1.5 bottom-1.5 rounded-md bg-slate-950/75 px-1.5 py-0.5 text-[8px] font-extrabold text-slate-100 truncate">${escapeHtml(image.label)}</span>
        `;
        button.addEventListener('click', () => {
            setActiveRoomImage(index);
        });
        if (index === 0) {
            button.classList.add('border-emerald-400');
        }
        thumbs.appendChild(button);
    });

    if (data.video_url) {
        roomVideo.src = data.video_url;
        videoSection.classList.remove('hidden');
        videoEmpty.classList.add('hidden');
    } else {
        roomVideo.removeAttribute('src');
        roomVideo.load();
        videoSection.classList.add('hidden');
        videoEmpty.classList.remove('hidden');
    }

    // Set form action route
    const form = document.getElementById('write-review-form');
    form.action = `/renty/room/${roomId}/review`;

    // Set contact request hidden input
    document.getElementById('contact-room-id').value = roomId;

    activeRoomReviews = Array.isArray(data.reviews) ? data.reviews : [];
    renderReviewSummary(data);
    renderReviews(false);

    const warningBox = document.getElementById('detail-price-warning');
    if (data.price_warning) {
        document.getElementById('detail-price-warning-title').textContent = data.price_warning.label;
        document.getElementById('detail-price-warning-message').textContent = data.price_warning.message;
        warningBox.classList.remove('hidden');
    } else {
        warningBox.classList.add('hidden');
    }

    saveViewedRoom(roomId);

    document.getElementById('room-detail-modal').classList.remove('hidden');
}

function loadReviewSummary(btn) {
    if (!currentDetailRoomId) return;

    const box = document.getElementById('review-summary-box');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang tóm tắt...';
    box.classList.remove('hidden');
    box.textContent = 'AI đang đọc các review...';

    fetch(`/api/renty/rooms/${currentDetailRoomId}/reviews/summary`)
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = original;

            if (!data.success) {
                box.textContent = 'Không thể tóm tắt review.';
                return;
            }

            const summary = data.summary;
            const pros = (summary.pros || []).map(item => `<li>${escapeHtml(item)}</li>`).join('');
            const cons = (summary.cons || []).map(item => `<li>${escapeHtml(item)}</li>`).join('');
            box.innerHTML = `
                <div class="font-bold text-slate-200">${escapeHtml(summary.summary || '')}</div>
                ${pros ? `<div class="mt-2 text-emerald-300 font-bold">Ưu điểm</div><ul class="list-disc pl-5">${pros}</ul>` : ''}
                ${cons ? `<div class="mt-2 text-amber-300 font-bold">Cần lưu ý</div><ul class="list-disc pl-5">${cons}</ul>` : ''}
            `;
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = original;
            box.textContent = 'Không thể kết nối AI để tóm tắt review.';
        });
}

function closeRoomDetailModal() {
    document.getElementById('room-detail-modal').classList.add('hidden');
    closeImageZoom();
}

// Interactive Map Variables
let rentyMap = null;
let rentyMarkers = {};

function initRentyMap() {
    if (rentyMap) return;

    const mockRooms = window.rentyRoomsData || {};

    // Center of Cầu Giấy area in Hà Nội
    const center = [21.036, 105.790];
    
    // Create map
    rentyMap = L.map('renty-interactive-map', {
        zoomControl: true,
        attributionControl: false
    }).setView(center, 14);

    // Add colorful road map tile layer from Google Maps
    L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        attribution: 'Map data &copy; Google'
    }).addTo(rentyMap);

    // Add markers
    Object.values(mockRooms).forEach(room => {
        // Generate a deterministic coordinate in Cầu Giấy based on room ID
        const offsetLat = Math.sin(room.id * 1.7) * 0.006;
        const offsetLng = Math.cos(room.id * 2.3) * 0.006;
        const lat = 21.036 + offsetLat;
        const lng = 105.790 + offsetLng;

        const shortPrice = (function(price) {
            if (price >= 1000000) {
                return (price / 1000000).toFixed(1).replace('.0', '') + 'M';
            }
            return (price / 1000).toFixed(0) + 'K';
        })(room.price);

        // Custom glowing pin HTML
        const customIcon = L.divIcon({
            className: 'custom-map-pin',
            html: `<div class="glowing-teal-pin" id="map-pin-${room.id}">${shortPrice}</div>`,
            iconSize: [50, 30],
            iconAnchor: [25, 15]
        });

        const marker = L.marker([lat, lng], { icon: customIcon }).addTo(rentyMap);

        // Add beautiful floating preview card inside popup
        const popupContent = `
            <div class="map-preview-card">
                <img class="map-preview-card-img" src="${room.cover_image}" alt="Phòng ${room.room_number}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                <div class="map-preview-card-body">
                    <h4 class="map-preview-card-title">${escapeHtml(room.title)}</h4>
                    <div class="map-preview-card-price-row">
                        <span class="map-preview-card-price">${Number(room.price).toLocaleString('vi-VN')}đ</span>
                        <span class="map-preview-card-rating">
                            <i class="fa-solid fa-star text-amber-400"></i> ${room.rating}
                        </span>
                    </div>
                    <a href="javascript:void(0)" class="map-preview-card-btn" id="map-card-detail-btn-${room.id}">Xem chi tiết</a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent, {
            closeButton: false,
            offset: L.point(0, -10)
        });

        // Add custom detail link click handler after popup opens
        marker.on('popupopen', () => {
            const btn = document.getElementById(`map-card-detail-btn-${room.id}`);
            if (btn) {
                btn.addEventListener('click', () => {
                    openRoomDetailModal(room.id);
                });
            }
        });

        // Marker click effect and active state toggle
        marker.on('click', function () {
            // Remove active state from all other pins
            document.querySelectorAll('.glowing-teal-pin').forEach(pin => {
                pin.classList.remove('active');
            });
            
            // Add active state to this pin
            const pinEl = document.getElementById(`map-pin-${room.id}`);
            if (pinEl) {
                pinEl.classList.add('active');
            }

            // Smooth pan to marker
            rentyMap.panTo(marker.getLatLng());

            // Find and highlight matching card in scrollable right panel
            const roomCard = document.querySelector(`.room-item-card[data-room-id="${room.id}"]`);
            if (roomCard) {
                roomCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                roomCard.classList.add('ring-2', 'ring-emerald-500');
                setTimeout(() => {
                    roomCard.classList.remove('ring-2', 'ring-emerald-500');
                }, 2000);
            }
        });

        marker.on('popupclose', function () {
            const pinEl = document.getElementById(`map-pin-${room.id}`);
            if (pinEl) {
                pinEl.classList.remove('active');
            }
        });

        rentyMarkers[room.id] = marker;
    });
}

// Helper to control marker visibility during filtering
function showMarker(id) {
    if (rentyMarkers[id] && rentyMap) {
        if (!rentyMap.hasLayer(rentyMarkers[id])) {
            rentyMap.addLayer(rentyMarkers[id]);
        }
    }
}

function hideMarker(id) {
    if (rentyMarkers[id] && rentyMap) {
        if (rentyMap.hasLayer(rentyMarkers[id])) {
            rentyMap.removeLayer(rentyMarkers[id]);
        }
    }
}

function setViewMode(mode) {
    const mapBtn = document.getElementById('view-mode-map-btn');
    const gridBtn = document.getElementById('view-mode-grid-btn');

    if (mode === 'map') {
        document.body.classList.add('renty-map-mode');
        if (mapBtn) mapBtn.classList.add('active');
        if (gridBtn) gridBtn.classList.remove('active');
        localStorage.setItem('rentry_view_mode', 'map');

        // Initialize map if not already done
        setTimeout(() => {
            initRentyMap();
            if (rentyMap) {
                rentyMap.invalidateSize();
            }
        }, 100);
    } else {
        document.body.classList.remove('renty-map-mode');
        if (mapBtn) mapBtn.classList.remove('active');
        if (gridBtn) gridBtn.classList.add('active');
        localStorage.setItem('rentry_view_mode', 'grid');
    }
}

function initModalListeners() {
    document.getElementById('image-zoom-modal')?.addEventListener('click', (event) => {
        if (event.target.id === 'image-zoom-modal') {
            closeImageZoom();
        }
    });

    document.getElementById('quick-room-preview')?.addEventListener('click', (event) => {
        if (event.target.id === 'quick-room-preview') {
            closeQuickRoomPreview();
        }
    });

    document.getElementById('room-report-modal')?.addEventListener('click', (event) => {
        if (event.target.id === 'room-report-modal') {
            closeReportModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeImageZoom();
            closeQuickRoomPreview();
            closeReportModal();
        }

        if (!document.getElementById('image-zoom-modal')?.classList.contains('hidden')) {
            if (event.key === 'ArrowLeft') changeZoomImage(-1);
            if (event.key === 'ArrowRight') changeZoomImage(1);
        }
    });
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModalListeners);
} else {
    initModalListeners();
}

let rentySearchSkeletonTimer = null;

function setSearchSkeletonLoading(isLoading) {
    const resultsCount = document.getElementById('results-count');

    document.querySelectorAll('.room-item-card').forEach(card => {
        if (isLoading) {
            card.classList.remove('hidden');
        }
        card.classList.toggle('is-search-loading', isLoading);
    });

    if (isLoading && resultsCount) {
        resultsCount.textContent = 'Đang tìm phòng phù hợp...';
    }
}

function runSearchWithSkeleton() {
    clearTimeout(rentySearchSkeletonTimer);
    setSearchSkeletonLoading(true);

    rentySearchSkeletonTimer = setTimeout(() => {
        filterItems({ keepSkeleton: true });
        setSearchSkeletonLoading(false);
    }, 680);
}

// Search and filter function
function filterItems(options = {}) {
    clearTimeout(rentySearchSkeletonTimer);
    if (!options.keepSkeleton) {
        setSearchSkeletonLoading(false);
    }
    const query = document.getElementById('search-input').value;
    const parsedSearch = parseNaturalSearch(query);
    const normalizedQuery = normalizeText(query);
    const filterPrice = document.getElementById('filter-price').value;
    const filterRating = document.getElementById('filter-rating').value;
    
    const petChecked = document.getElementById('tag-pets').checked;
    const loftChecked = document.getElementById('tag-loft').checked;
    const balconyChecked = document.getElementById('tag-balcony').checked;
    const wcChecked = document.getElementById('tag-wc') ? document.getElementById('tag-wc').checked : false;
    const hideRented = document.getElementById('hide-rented-toggle') ? document.getElementById('hide-rented-toggle').checked : false;

    let matchesCount = 0;

    document.querySelectorAll('.room-item-card').forEach(card => {
        const title = card.getAttribute('data-title').toLowerCase();
        const price = parseInt(card.getAttribute('data-price'));
        const rating = parseFloat(card.getAttribute('data-rating'));
        const pets = card.getAttribute('data-pets') === 'true';
        const loft = card.getAttribute('data-loft') === 'true';
        const balcony = card.getAttribute('data-balcony') === 'true';
        const wc = card.getAttribute('data-wc') === 'true';
        const status = card.getAttribute('data-status');
        const searchableText = normalizeText(`${card.getAttribute('data-title')} ${card.getAttribute('data-area-name')} ${card.textContent}`);

        let matchesQuery = true;
        if (normalizedQuery.trim() !== '') {
            const importantTerms = parsedSearch.keywords.filter(term => !['tim', 'phong', 'tro', 'duoi', 'o', 'gan', 'dai', 'hoc', 'trieu', 'tr', 'gia'].includes(term));
            matchesQuery = importantTerms.length === 0 || importantTerms.some(term => searchableText.includes(term));
        }
        
        let matchesPrice = true;
        if (filterPrice !== 'all') {
            matchesPrice = price <= parseInt(filterPrice);
        }
        if (parsedSearch.maxPrice) {
            matchesPrice = matchesPrice && price <= parsedSearch.maxPrice;
        }

        let matchesRating = true;
        if (filterRating !== 'all') {
            matchesRating = rating >= parseFloat(filterRating);
        }

        let matchesTags = true;
        if (petChecked && !pets) matchesTags = false;
        if (loftChecked && !loft) matchesTags = false;
        if (balconyChecked && !balcony) matchesTags = false;
        if (wcChecked && !wc) matchesTags = false;
        if (parsedSearch.amenities.pets && !pets) matchesTags = false;
        if (parsedSearch.amenities.loft && !loft) matchesTags = false;
        if (parsedSearch.amenities.balcony && !balcony) matchesTags = false;
        if (parsedSearch.amenities.wc && !wc) matchesTags = false;

        let matchesLocation = true;
        if (parsedSearch.locations.length > 0) {
            matchesLocation = parsedSearch.locations.some(location => searchableText.includes(location));
        }

        let matchesStatus = true;
        if (hideRented && status !== 'empty') {
            matchesStatus = false;
        }

        if (matchesQuery && matchesPrice && matchesRating && matchesTags && matchesLocation && matchesStatus) {
            card.classList.remove('hidden');
            matchesCount++;
            showMarker(card.getAttribute('data-room-id'));
        } else {
            card.classList.add('hidden');
            hideMarker(card.getAttribute('data-room-id'));
        }
    });

    document.getElementById('results-count').textContent = `Tìm thấy ${matchesCount} phòng`;
}

function openRentySearchSuggestions() {
    document.getElementById('renty-search-panel')?.classList.add('is-search-active');
    document.getElementById('renty-search-backdrop')?.classList.add('is-active');
}

function blurRentySearch() {
    document.getElementById('renty-search-panel')?.classList.remove('is-search-active');
    document.getElementById('renty-search-backdrop')?.classList.remove('is-active');
    document.getElementById('search-input')?.blur();
}

function applySearchSuggestion(query) {
    const input = document.getElementById('search-input');
    input.value = query;
    input.focus();
    openRentySearchSuggestions();
    filterItems();
}

function initSearchListeners() {
    document.addEventListener('click', (event) => {
        const panel = document.getElementById('renty-search-panel');
        if (panel && panel.classList.contains('is-search-active') && !panel.contains(event.target)) {
            blurRentySearch();
        }
    });
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSearchListeners);
} else {
    initSearchListeners();
}

function subscribeEmptyNotification(event, roomId, roomTitle) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    document.getElementById('notify-room-id').value = roomId;
    document.getElementById('notify-room-title-display').textContent = roomTitle;
    document.getElementById('notify-contact-input').value = '';
    
    const modal = document.getElementById('notify-subscribe-modal');
    modal.classList.remove('hidden');
}

function closeNotifySubscribeModal() {
    document.getElementById('notify-subscribe-modal').classList.add('hidden');
}

function handleNotifySubscribeSubmit(event) {
    event.preventDefault();
    const roomTitle = document.getElementById('notify-room-title-display').textContent;
    const contactInput = document.getElementById('notify-contact-input').value.trim();
    
    if (!contactInput) return;
    
    closeNotifySubscribeModal();
    showCustomAlert('Đăng ký thành công!', `Đã kích hoạt chuông báo trống phòng thành công cho phòng "${roomTitle}". Chúng tôi sẽ gửi thông báo tới "${contactInput}" ngay khi phòng Sẵn sàng.`);
}

function showCustomAlert(title, message) {
    document.getElementById('custom-alert-title').textContent = title;
    document.getElementById('custom-alert-message').textContent = message;
    document.getElementById('custom-alert-modal').classList.remove('hidden');
}

function closeCustomAlert() {
    document.getElementById('custom-alert-modal').classList.add('hidden');
}

function toggleVisualFilter(key) {
    const btn = document.getElementById(`vbtn-${key}`);
    const checkbox = document.getElementById(`tag-${key}`);
    if (!btn || !checkbox) return;
    
    checkbox.checked = !checkbox.checked;
    btn.classList.toggle('active', checkbox.checked);
    filterItems();
}

function syncFromCheckbox(key) {
    const btn = document.getElementById(`vbtn-${key}`);
    const checkbox = document.getElementById(`tag-${key}`);
    if (!btn || !checkbox) return;
    
    btn.classList.toggle('active', checkbox.checked);
    filterItems();
}

function initRentyDashboard() {
    renderViewedRooms();
    
    // Set initial view mode, default to 'map'
    const savedMode = localStorage.getItem('rentry_view_mode') || 'map';
    setViewMode(savedMode);

    filterItems(); // Run initial filter to apply checked state of pets & balcony
}
if (document.readyState === 'loading') {
    window.addEventListener('DOMContentLoaded', initRentyDashboard);
} else {
    initRentyDashboard();
}

function openHotAreasModal() {
    document.getElementById('hot-areas-modal').classList.remove('hidden');
}

function closeHotAreasModal() {
    document.getElementById('hot-areas-modal').classList.add('hidden');
}

function selectHotArea(areaQuery) {
    document.getElementById('search-input').value = areaQuery;
    filterItems();
    closeHotAreasModal();
    // Highlight search input briefly
    const input = document.getElementById('search-input');
    input.focus();
    input.classList.add('ring-2', 'ring-emerald-500');
    setTimeout(() => {
        input.classList.remove('ring-2', 'ring-emerald-500');
    }, 1000);
}

function openNewReviewsModal() {
    document.getElementById('new-reviews-modal').classList.remove('hidden');
}

function closeNewReviewsModal() {
    document.getElementById('new-reviews-modal').classList.add('hidden');
}

// Q&A INTERACTIONS
function submitQaQuestion() {
    const input = document.getElementById('qa-input-field');
    const text = input ? input.value.trim() : '';

    if (!text) {
        alert('Vui lòng nhập câu hỏi trước khi gửi!');
        return;
    }

    const grid = document.querySelector('.renty-qa-section .grid');
    if (!grid) return;

    const newCard = document.createElement('div');
    newCard.className = 'qa-card rounded-2xl p-5 border border-slate-800/60 flex flex-col justify-between transition-all duration-300 hover:border-slate-700/80 hover:shadow-xl hover:shadow-black/20 animate-fade-in';
    newCard.style.backgroundColor = '#1E1E24';

    newCard.innerHTML = `
        <div>
            <!-- Upper Meta-row -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 border border-slate-700">
                        <i class="fa-solid fa-user-secret text-sm text-teal-400"></i>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-200">Người dùng ẩn danh</span>
                        <span class="block text-[10px] text-slate-500">Vừa xong</span>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-teal-500/10 text-teal-400 text-[10px] font-extrabold uppercase border border-teal-500/20">
                    Hà Nội
                </span>
            </div>

            <!-- Question Text -->
            <h3 class="text-sm font-bold text-slate-200 leading-snug mb-4">
                ${escapeHtml(text)}
            </h3>
        </div>

        <div>
            <!-- Subtle horizontal separator line -->
            <div class="border-t border-slate-800/80 my-4"></div>

            <!-- Bottom Interaction Row -->
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <!-- Upvote/Downvote button counter -->
                    <div class="flex items-center bg-slate-900/40 border border-slate-800/80 rounded-lg p-1">
                        <button type="button" onclick="voteQa(this, 'up')" class="px-2 py-1 text-slate-500 hover:text-emerald-400 transition-colors text-xs">
                            <i class="fa-solid fa-chevron-up"></i>
                        </button>
                        <span class="px-2 text-xs font-extrabold text-slate-300 qa-vote-count">1</span>
                        <button type="button" onclick="voteQa(this, 'down')" class="px-2 py-1 text-slate-500 hover:text-rose-400 transition-colors text-xs">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                    
                    <!-- Comment icon & label -->
                    <button type="button" class="flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-slate-200 transition-colors">
                        <i class="fa-regular fa-comment text-slate-500"></i>
                        <span>0 bình luận</span>
                    </button>
                </div>

                <!-- Highlighted top reply -->
                <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800/50 flex flex-col gap-2 mt-2 text-slate-500">
                    <p class="text-xs italic">Chưa có câu trả lời nào. Hãy là người đầu tiên trả lời!</p>
                </div>
            </div>
        </div>
    `;

    grid.insertBefore(newCard, grid.firstChild);
    input.value = '';
}


function voteQa(button, direction) {
    const parent = button.parentElement;
    const countSpan = parent.querySelector('.qa-vote-count');
    if (!countSpan) return;

    let currentVotes = parseInt(countSpan.textContent) || 0;
    const activeUp = button.classList.contains('text-emerald-400');
    const activeDown = button.classList.contains('text-rose-400');

    if (direction === 'up') {
        const downBtn = parent.querySelector('button[onclick*="down"]');
        if (activeUp) {
            button.classList.remove('text-emerald-400');
            countSpan.textContent = currentVotes - 1;
        } else {
            button.classList.add('text-emerald-400');
            if (downBtn && downBtn.classList.contains('text-rose-400')) {
                downBtn.classList.remove('text-rose-400');
                countSpan.textContent = currentVotes + 2;
            } else {
                countSpan.textContent = currentVotes + 1;
            }
        }
    } else if (direction === 'down') {
        const upBtn = parent.querySelector('button[onclick*="up"]');
        if (activeDown) {
            button.classList.remove('text-rose-400');
            countSpan.textContent = currentVotes + 1;
        } else {
            button.classList.add('text-rose-400');
            if (upBtn && upBtn.classList.contains('text-emerald-400')) {
                upBtn.classList.remove('text-emerald-400');
                countSpan.textContent = currentVotes - 2;
            } else {
                countSpan.textContent = currentVotes - 1;
            }
        }
    }
}

// Bind functions to window so inline onclick event handlers can call them
window.toggleFilterDrawer = toggleFilterDrawer;
window.toggleThemeMode = toggleThemeMode;
window.openQuickRoomPreview = openQuickRoomPreview;
window.closeQuickRoomPreview = closeQuickRoomPreview;
window.openReportModal = openReportModal;
window.closeReportModal = closeReportModal;
window.updateMoveInCost = updateMoveInCost;
window.toggleDetailDescription = toggleDetailDescription;
window.renderReviewSummary = renderReviewSummary;
window.renderReviews = renderReviews;
window.toggleAllReviews = toggleAllReviews;
window.setActiveRoomImage = setActiveRoomImage;
window.openImageZoom = openImageZoom;
window.changeZoomImage = changeZoomImage;
window.closeImageZoom = closeImageZoom;
window.clearViewedRooms = clearViewedRooms;
window.openRoomDetailModal = openRoomDetailModal;
window.loadReviewSummary = loadReviewSummary;
window.closeRoomDetailModal = closeRoomDetailModal;
window.setViewMode = setViewMode;
window.runSearchWithSkeleton = runSearchWithSkeleton;
window.filterItems = filterItems;
window.openRentySearchSuggestions = openRentySearchSuggestions;
window.blurRentySearch = blurRentySearch;
window.applySearchSuggestion = applySearchSuggestion;
window.subscribeEmptyNotification = subscribeEmptyNotification;
window.closeNotifySubscribeModal = closeNotifySubscribeModal;
window.handleNotifySubscribeSubmit = handleNotifySubscribeSubmit;
window.showCustomAlert = showCustomAlert;
window.closeCustomAlert = closeCustomAlert;
window.toggleVisualFilter = toggleVisualFilter;
window.syncFromCheckbox = syncFromCheckbox;
window.openHotAreasModal = openHotAreasModal;
window.closeHotAreasModal = closeHotAreasModal;
window.selectHotArea = selectHotArea;
window.openNewReviewsModal = openNewReviewsModal;
window.closeNewReviewsModal = closeNewReviewsModal;
window.showMarker = showMarker;
window.hideMarker = hideMarker;
window.submitQaQuestion = submitQaQuestion;
window.voteQa = voteQa;
