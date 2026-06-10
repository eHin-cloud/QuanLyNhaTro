// Setup custom toast notification override for alert()
(function () {
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

    window.alert = function (message) {
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

function getDynamicLocations() {
    const locations = new Set();
    
    // 1. Một số địa điểm mặc định phổ biến
    const defaults = [
        'cau giay', 'cầu giấy',
        'thanh xuan', 'thanh xuân',
        'quan 10', 'quận 10',
        'bach khoa', 'bách khoa',
        'dai hoc bach khoa', 'đại học bách khoa',
        'su pham', 'sư phạm',
        'quoc gia', 'quốc gia',
        'xuan thuy', 'xuân thủy',
        'thu duc', 'thủ đức',
        'quan 1', 'quận 1',
        'quan 3', 'quận 3',
        'quan 5', 'quận 5',
        'go vap', 'gò vấp',
        'binh thanh', 'bình thạnh'
    ];
    defaults.forEach(loc => locations.add(loc));

    // 2. Tự động quét từ các card phòng trọ trong trang
    document.querySelectorAll('.room-item-card').forEach(card => {
        const areaName = card.getAttribute('data-area-name');
        if (areaName) {
            const trimmed = areaName.trim().toLowerCase();
            locations.add(trimmed);
            locations.add(normalizeText(trimmed));
        }

        const title = card.getAttribute('data-title');
        if (title) {
            const schoolMatches = title.match(/(?:dai hoc|cao dang|truong)\s+[a-zA-Z0-9\sÀ-ỹ]+/gi);
            if (schoolMatches) {
                schoolMatches.forEach(match => {
                    const cleaned = match.replace(/[\,\.\-\;]/g, '').trim().toLowerCase();
                    if (cleaned.length > 5) {
                        locations.add(cleaned);
                        locations.add(normalizeText(cleaned));
                    }
                });
            }
        }
    });

    // Sắp xếp theo chiều dài giảm dần để ưu tiên so khớp các cụm từ dài nhất trước
    return Array.from(locations).sort((a, b) => b.length - a.length);
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
            loft: normalized.includes('gac') || normalized.includes('gac lung') || normalized.includes('gac xep'),
            balcony: normalized.includes('ban cong'),
            wc: normalized.includes('khep kin') || normalized.includes('wc') || normalized.includes('ve sinh') || normalized.includes('nha ve sinh')
        },
        near: []
    };

    const priceMatch = normalized.match(/(?:duoi|nho hon|toi da|<=?|tam|khoang)\s*(\d+(?:[.,]\d+)?)\s*(trieu|tr|m|000000)?/);
    if (priceMatch) {
        const amount = parseFloat(priceMatch[1].replace(',', '.'));
        parsed.maxPrice = amount < 100000 ? amount * 1000000 : amount;
    }

    const dynamicLocs = getDynamicLocations();
    let tempQuery = normalized;

    dynamicLocs.forEach(loc => {
        const index = tempQuery.indexOf(loc);
        if (index !== -1) {
            // Kiểm tra xem đây có phải là một từ độc lập hay không (tránh 'quan 1' khớp trong 'quan 10')
            const isWord = (index === 0 || /\s/.test(tempQuery[index - 1])) && 
                           (index + loc.length === tempQuery.length || /\s/.test(tempQuery[index + loc.length]));
            
            if (isWord) {
                parsed.locations.push(loc);
                parsed.near.push(loc);
                tempQuery = tempQuery.substring(0, index) + ' ' + tempQuery.substring(index + loc.length);
            }
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

    // Center of Cầu Giấy area in Hà Nội by default
    let center = [21.036, 105.790];
    
    // Check if the first room is in HCMC to center the map on HCMC initially
    const roomsArray = Object.values(mockRooms);
    if (roomsArray.length > 0) {
        const firstRoom = roomsArray[0];
        const firstRoomIsHcm = (firstRoom.address && (firstRoom.address.includes('Hồ Chí Minh') || firstRoom.address.includes('Bình Thạnh') || firstRoom.address.includes('Quận 10') || firstRoom.address.includes('HCM'))) || (firstRoom.area_name && (firstRoom.area_name.includes('Quan 10') || firstRoom.area_name.includes('Bình Thạnh') || firstRoom.area_name.includes('Hồ Chí Minh')));
        if (firstRoomIsHcm) {
            center = [10.798, 106.705];
        }
    }

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
        // Generate a deterministic coordinate based on room address/region
        const isHcm = (room.address && (room.address.includes('Hồ Chí Minh') || room.address.includes('Bình Thạnh') || room.address.includes('Quận 10') || room.address.includes('HCM'))) || (room.area_name && (room.area_name.includes('Quan 10') || room.area_name.includes('Bình Thạnh') || room.area_name.includes('Hồ Chí Minh')));
        const offsetLat = Math.sin(room.id * 1.7) * 0.006;
        const offsetLng = Math.cos(room.id * 2.3) * 0.006;
        const lat = isHcm ? (10.798 + offsetLat) : (21.036 + offsetLat);
        const lng = isHcm ? (106.705 + offsetLng) : (105.790 + offsetLng);

        const shortPrice = (function (price) {
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

    // Create coordinate indicator box on the map
    const coordBox = document.createElement('div');
    coordBox.id = 'map-coords-indicator';
    coordBox.className = 'absolute bottom-4 left-4 z-[1000] px-3 py-1.5 rounded-xl bg-slate-950/85 border border-white/10 text-[10px] font-extrabold text-slate-300 backdrop-blur pointer-events-none transition-opacity duration-300 opacity-0 flex items-center shadow-lg';
    coordBox.innerHTML = `<i class="fa-solid fa-location-crosshairs text-teal-400 mr-1.5"></i> 0.00000, 0.00000`;

    const mapContainer = document.getElementById('renty-interactive-map');
    if (mapContainer) {
        mapContainer.appendChild(coordBox);
    }

    // Update coordinate indicator when hovering and moving mouse over the map
    rentyMap.on('mousemove', function (e) {
        const coordEl = document.getElementById('map-coords-indicator');
        if (coordEl) {
            const lat = e.latlng.lat.toFixed(5);
            const lng = e.latlng.lng.toFixed(5);
            coordEl.innerHTML = `<i class="fa-solid fa-location-crosshairs text-teal-400 mr-1.5"></i> Tọa độ: ${lat}, ${lng}`;
            coordEl.style.opacity = '1';
        }
    });

    rentyMap.on('mouseout', function () {
        const coordEl = document.getElementById('map-coords-indicator');
        if (coordEl) {
            coordEl.style.opacity = '0';
        }
    });

    // Add hover listeners to room cards to open matching map marker
    document.querySelectorAll('.room-item-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            const roomId = card.getAttribute('data-room-id');
            if (rentyMarkers[roomId] && rentyMap) {
                // Highlight pin
                document.querySelectorAll('.glowing-teal-pin').forEach(pin => {
                    pin.classList.remove('active');
                });
                const pinEl = document.getElementById(`map-pin-${roomId}`);
                if (pinEl) pinEl.classList.add('active');

                // Open Leaflet popup
                rentyMarkers[roomId].openPopup();
                rentyMap.panTo(rentyMarkers[roomId].getLatLng());
            }
        });
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
        }, 450);
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

let rentyCurrentPage = 1;
const rentyItemsPerPage = 9;

// Dynamically render premium glassmorphism pagination controls
function renderPageButton(page, isActive) {
    return `
        <button type="button" 
                onclick="changeRentyPage(${page})" 
                class="w-9 h-9 rounded-xl font-extrabold text-xs transition-all border ${isActive ? 'bg-gradient-to-tr from-emerald-600 to-teal-500 text-white border-transparent shadow-lg shadow-emerald-500/20' : 'bg-slate-900/40 border-slate-800/80 text-slate-400 hover:border-emerald-500/30 hover:text-slate-200'}">
            ${page}
        </button>
    `;
}

function renderEllipsisButton(totalPages) {
    return `
        <div class="relative inline-block pagination-ellipsis-container">
            <button type="button" 
                    onclick="makePaginationInput(this, ${totalPages})" 
                    class="w-9 h-9 rounded-xl font-extrabold text-xs transition-all border bg-slate-900/40 border-slate-800/80 text-slate-400 hover:border-emerald-500/30 hover:text-slate-200 flex items-center justify-center cursor-pointer"
                    title="Click để nhập số trang nhanh">
                ...
            </button>
        </div>
    `;
}

function makePaginationInput(button, totalPages) {
    const container = button.parentElement;
    if (!container) return;

    container.innerHTML = `
        <input type="number" 
               min="1" 
               max="${totalPages}" 
               placeholder="..." 
               class="w-12 h-9 text-center rounded-xl bg-slate-950/90 border border-emerald-500/80 text-emerald-400 font-extrabold text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all shadow-lg shadow-emerald-500/10 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
               onkeydown="handlePaginationInputKey(event, this, ${totalPages})" 
               onblur="restorePaginationEllipsis(this, ${totalPages})">
    `;

    const input = container.querySelector('input');
    if (input) {
        input.focus();
        input.select();
    }
}

function handlePaginationInputKey(event, input, totalPages) {
    if (event.key === 'Enter') {
        const val = parseInt(input.value);
        if (!isNaN(val) && val >= 1 && val <= totalPages) {
            changeRentyPage(val);
        } else {
            input.classList.add('border-rose-500', 'text-rose-450');
            setTimeout(() => {
                input.classList.remove('border-rose-500', 'text-rose-455');
            }, 500);
        }
    } else if (event.key === 'Escape') {
        restorePaginationEllipsis(input, totalPages);
    }
}

function restorePaginationEllipsis(input, totalPages) {
    const container = input.parentElement;
    if (!container) return;

    setTimeout(() => {
        if (container.contains(input)) {
            container.innerHTML = `
                <button type="button" 
                        onclick="makePaginationInput(this, ${totalPages})" 
                        class="w-9 h-9 rounded-xl font-extrabold text-xs transition-all border bg-slate-900/40 border-slate-800/80 text-slate-400 hover:border-emerald-500/30 hover:text-slate-200 flex items-center justify-center cursor-pointer"
                        title="Click để nhập số trang nhanh">
                    ...
                </button>
            `;
        }
    }, 120);
}

// Make functions global
window.makePaginationInput = makePaginationInput;
window.handlePaginationInputKey = handlePaginationInputKey;
window.restorePaginationEllipsis = restorePaginationEllipsis;

function renderPaginationControls(totalPages) {
    const container = document.getElementById('renty-pagination');
    if (!container) return;

    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';

    // Previous Button
    html += `
        <button type="button" 
                onclick="changeRentyPage(${rentyCurrentPage - 1})" 
                class="px-4 py-2 rounded-xl bg-slate-900/40 border border-slate-800/85 hover:border-emerald-500/40 hover:text-emerald-400 transition-all font-bold text-xs flex items-center justify-center gap-1.5 ${rentyCurrentPage === 1 ? 'opacity-40 cursor-not-allowed' : ''}" 
                ${rentyCurrentPage === 1 ? 'disabled' : ''}>
            <i class="fa-solid fa-chevron-left text-[10px]"></i> Trước
        </button>
    `;

    // Page numbers algorithm
    const maxVisiblePages = 7;
    if (totalPages <= maxVisiblePages) {
        for (let i = 1; i <= totalPages; i++) {
            html += renderPageButton(i, i === rentyCurrentPage);
        }
    } else {
        // We have more than 7 pages, use ellipsis
        if (rentyCurrentPage <= 4) {
            // Near the start: 1 2 3 4 5 ... totalPages
            for (let i = 1; i <= 5; i++) {
                html += renderPageButton(i, i === rentyCurrentPage);
            }
            html += renderEllipsisButton(totalPages);
            html += renderPageButton(totalPages, false);
        } else if (rentyCurrentPage >= totalPages - 3) {
            // Near the end: 1 ... totalPages-4 totalPages-3 totalPages-2 totalPages-1 totalPages
            html += renderPageButton(1, false);
            html += renderEllipsisButton(totalPages);
            for (let i = totalPages - 4; i <= totalPages; i++) {
                html += renderPageButton(i, i === rentyCurrentPage);
            }
        } else {
            // In the middle: 1 ... current-1 current current+1 ... totalPages
            html += renderPageButton(1, false);
            html += renderEllipsisButton(totalPages);
            
            html += renderPageButton(rentyCurrentPage - 1, false);
            html += renderPageButton(rentyCurrentPage, true);
            html += renderPageButton(rentyCurrentPage + 1, false);
            
            html += renderEllipsisButton(totalPages);
            html += renderPageButton(totalPages, false);
        }
    }

    // Next Button
    html += `
        <button type="button" 
                onclick="changeRentyPage(${rentyCurrentPage + 1})" 
                class="px-4 py-2 rounded-xl bg-slate-900/40 border border-slate-800/85 hover:border-emerald-500/40 hover:text-emerald-400 transition-all font-bold text-xs flex items-center justify-center gap-1.5 ${rentyCurrentPage === totalPages ? 'opacity-40 cursor-not-allowed' : ''}" 
                ${rentyCurrentPage === totalPages ? 'disabled' : ''}>
            Sau <i class="fa-solid fa-chevron-right text-[10px]"></i>
        </button>
    `;

    container.innerHTML = html;
}

function changeRentyPage(page) {
    rentyCurrentPage = page;
    filterItems({ resetPage: false });
    
    // Smooth scroll to top of main workspace
    const resultsCount = document.getElementById('results-count');
    if (resultsCount) {
        resultsCount.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Make changeRentyPage global so inline event handler onclick works
window.changeRentyPage = changeRentyPage;

// Search and filter function
function filterItems(options = {}) {
    clearTimeout(rentySearchSkeletonTimer);
    if (!options.keepSkeleton) {
        setSearchSkeletonLoading(false);
    }
    
    // Reset page to 1 unless explicitly requested to keep page
    if (options.resetPage !== false) {
        rentyCurrentPage = 1;
    }
    
    const query = document.getElementById('search-input').value;
    const parsedSearch = parseNaturalSearch(query);
    const normalizedQuery = normalizeText(query);
    const filterPrice = document.getElementById('filter-price').value;
    const filterRating = document.getElementById('filter-rating').value;
    const distanceSlider = document.getElementById('distance-slider');
    const filterDistance = distanceSlider ? parseFloat(distanceSlider.value) : 3.0;

    const petEl = document.getElementById('tag-pets');
    const petChecked = petEl ? petEl.checked : false;
    const loftEl = document.getElementById('tag-loft');
    const loftChecked = loftEl ? loftEl.checked : false;
    const balconyEl = document.getElementById('tag-balcony');
    const balconyChecked = balconyEl ? balconyEl.checked : false;
    const wcChecked = document.getElementById('tag-wc') ? document.getElementById('tag-wc').checked : false;
    const hideRented = document.getElementById('hide-rented-toggle') ? document.getElementById('hide-rented-toggle').checked : false;

    let matches = [];

    document.querySelectorAll('.room-item-card').forEach(card => {
        const title = card.getAttribute('data-title').toLowerCase();
        const price = parseInt(card.getAttribute('data-price'));
        const rating = parseFloat(card.getAttribute('data-rating'));
        const distance = parseFloat(card.getAttribute('data-distance') || 0);
        const pets = card.getAttribute('data-pets') === 'true';
        const loft = card.getAttribute('data-loft') === 'true';
        const balcony = card.getAttribute('data-balcony') === 'true';
        const wc = card.getAttribute('data-wc') === 'true';
        const status = card.getAttribute('data-status');
        const searchableText = normalizeText(
            `${card.getAttribute('data-title')} ` +
            `${card.getAttribute('data-area-name')} ` +
            `${card.getAttribute('data-address') || ''} ` +
            `${card.getAttribute('data-location-desc') || ''} ` +
            `${card.getAttribute('data-space-desc') || ''} ` +
            `${card.getAttribute('data-scenery-desc') || ''} ` +
            `${card.textContent}`
        );

        let matchesQuery = true;
        if (normalizedQuery.trim() !== '') {
            // 1. Loại bỏ các từ quan hệ thừa ở đầu hoặc trong query để tìm kiếm cụm từ sạch
            const cleanQuery = normalizedQuery
                .replace(/^(gan|o|tim|cho thue|khu vuc|xung quanh)\s+/g, '')
                .replace(/\b(gan|o|tim|cho thue|khu vuc|xung quanh)\b/g, '')
                .replace(/\s+/g, ' ')
                .trim();

            const containsFullQuery = searchableText.includes(cleanQuery);

            // 2. Thử so khớp các từ khóa quan trọng với độ phủ cao (Ratio Matching)
            const stopWords = [
                'tim', 'phong', 'tro', 'duoi', 'o', 'gan', 'dai', 'hoc', 'trieu', 'tr', 'gia',
                'co', 'khong', 'cho', 'thue', 'can', 'ho', 'va', 'voi', 'trong', 'ngoai',
                'dep', 're', 'nha', 'chinh', 'chu', 'thang'
            ];

            // Loại bỏ từ khóa tiện ích đã được bóc tách
            const amenityWords = [];
            if (parsedSearch.amenities.pets) amenityWords.push('thu', 'cung', 'pet', 'pets');
            if (parsedSearch.amenities.loft) amenityWords.push('gac', 'lung', 'xep');
            if (parsedSearch.amenities.balcony) amenityWords.push('ban', 'cong');
            if (parsedSearch.amenities.wc) amenityWords.push('khep', 'kin', 'wc', 've', 'sinh', 'toilet');

            // Loại bỏ từ khóa địa danh đã được bóc tách
            const locationWords = [];
            parsedSearch.locations.forEach(loc => {
                locationWords.push(...loc.split(/\s+/));
            });

            const importantTerms = parsedSearch.keywords.filter(term => {
                return !stopWords.includes(term) && 
                       !amenityWords.includes(term) && 
                       !locationWords.includes(term);
            });

            // So khớp thông minh:
            let matchesAllTerms = false;
            if (importantTerms.length === 0) {
                matchesAllTerms = true;
            } else {
                const matchCount = importantTerms.filter(term => searchableText.includes(term)).length;
                const matchRatio = matchCount / importantTerms.length;
                
                if (importantTerms.length <= 2) {
                    // Nếu từ khóa ngắn (1-2 từ), yêu cầu khớp 100% (ví dụ: "metro", "thu duc")
                    matchesAllTerms = (matchRatio === 1);
                } else {
                    // Nếu từ khóa dài (từ 3 từ trở lên), cho phép khớp tối thiểu 70% số từ để hỗ trợ tìm tự do 
                    // (ví dụ: "truong cao dang thu duc" vẫn khớp nếu phòng chỉ có "cao dang thu duc")
                    matchesAllTerms = (matchRatio >= 0.7);
                }
            }

            matchesQuery = containsFullQuery || matchesAllTerms;
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

        let matchesDistance = true;
        if (distanceSlider) {
            matchesDistance = distance <= filterDistance;
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

        if (matchesQuery && matchesPrice && matchesRating && matchesDistance && matchesTags && matchesLocation && matchesStatus) {
            matches.push(card);
            showMarker(card.getAttribute('data-room-id'));
        } else {
            card.classList.add('hidden');
            hideMarker(card.getAttribute('data-room-id'));
        }
    });

    const matchesCount = matches.length;
    document.getElementById('results-count').textContent = `Tìm thấy ${matchesCount} phòng`;

    // Calculate total pages
    const totalPages = Math.ceil(matchesCount / rentyItemsPerPage);
    if (rentyCurrentPage > totalPages) {
        rentyCurrentPage = totalPages || 1;
    }
    if (rentyCurrentPage < 1) {
        rentyCurrentPage = 1;
    }

    const startIndex = (rentyCurrentPage - 1) * rentyItemsPerPage;
    const endIndex = startIndex + rentyItemsPerPage;

    matches.forEach((card, index) => {
        if (index >= startIndex && index < endIndex) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });

    renderPaginationControls(totalPages);

    // Fit map bounds to visible markers
    if (rentyMap && typeof L !== 'undefined') {
        const visibleLatLngs = [];
        Object.values(rentyMarkers).forEach(marker => {
            if (rentyMap.hasLayer(marker)) {
                visibleLatLngs.push(marker.getLatLng());
            }
        });
        if (visibleLatLngs.length > 0) {
            const bounds = L.latLngBounds(visibleLatLngs);
            rentyMap.fitBounds(bounds, { maxZoom: 14, padding: [30, 30] });
        }
    }
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
    if (input) {
        input.value = query;
        if (!document.getElementById('rooms-grid')) {
            window.location.href = '/renty?search=' + encodeURIComponent(query);
        } else {
            input.focus();
            openRentySearchSuggestions();
            filterItems();
        }
    }
}

function triggerRentySearch() {
    const input = document.getElementById('search-input');
    if (!input) return;
    const value = input.value;
    if (!document.getElementById('rooms-grid')) {
        window.location.href = '/renty?search=' + encodeURIComponent(value);
    } else {
        filterItems();
        blurRentySearch();
    }
}
window.triggerRentySearch = triggerRentySearch;

function handleSearchInput(e) {
    if (!document.getElementById('rooms-grid')) {
        if (e.key === 'Enter') {
            window.location.href = '/renty?search=' + encodeURIComponent(e.target.value);
        }
    } else {
        filterItems();
        if (e.key === 'Enter') {
            blurRentySearch();
        }
    }
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
    // Only initialize if we are on the Renty dashboard page (contains map container or mode toggle)
    if (!document.getElementById('renty-interactive-map') && !document.getElementById('view-mode-map-btn')) {
        return;
    }

    renderViewedRooms();

    // Set initial view mode, default to 'grid'
    const savedMode = localStorage.getItem('rentry_view_mode') || 'grid';
    setViewMode(savedMode);

    // Read search param from URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search');
    if (searchQuery) {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = searchQuery;
        }
    }

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

// Database of Q&A comments
const qaCommentsData = {
    0: [
        { author: 'Hoàng Anh', meta: 'Sinh viên Sư Phạm', text: 'Khu này bể ngầm hơi nhỏ nên nếu mất nước chung thì cúp tầm nửa ngày thôi bạn, chủ nhà có bể dự phòng nhé.', is_best: true, time: '2 giờ trước' },
        { author: 'Trần Nam', meta: 'Người dùng ẩn danh', text: 'Chính xác luôn, đợt năm ngoái nắng nóng cúp nước liên tục cơ mà nhà này vẫn có nước dùng tạm.', is_best: false, time: '1 giờ trước' },
        { author: 'Ngọc Mai', meta: 'Sinh viên Quốc Gia', text: 'Chủ nhà có báo trước lịch cắt nước không bạn ơi?', is_best: false, time: '30 phút trước' }
    ],
    1: [
        { author: 'Khánh Linh', meta: 'Ngoại Thương', text: 'Chủ nhà ngõ này hiền lắm, giữ xe free mà 11h đêm khóa cổng thôi. Không chung đụng gì nhiều đâu em.', is_best: true, time: '5 giờ trước' },
        { author: 'Duy Bách', meta: 'Người dùng ẩn danh', text: 'Có quy định giờ giấc nghiêm ngặt không chị? Bạn bè tới chơi có phải xin phép không?', is_best: false, time: '3 giờ trước' }
    ],
    2: [
        { author: 'Minh Đức', meta: 'Bách Khoa', text: 'Tầm giá này ở ngõ Tự Do hơi hiếm ban công rộng, bạn chịu khó lùi ra Trần Đại Nghĩa hoặc Lê Thanh Nghị thì nhiều phòng đẹp hơn nha.', is_best: true, time: '1 ngày trước' },
        { author: 'Văn Hải', meta: 'Xây Dựng', text: 'Ngõ Tự Do phòng bé tí mà đắt lắm, khuyên thật nên ra Lê Thanh Nghị tìm phòng rộng hơn.', is_best: false, time: '18 giờ trước' }
    ],
    3: [
        { author: 'Thu Trang', meta: 'Báo Chí', text: 'Đầu ngõ có chốt dân phòng với đèn đường sáng trưng tới sáng luôn bạn, yên tâm cực kỳ nha.', is_best: true, time: '3 ngày trước' },
        { author: 'Hương Giang', meta: 'Sư Phạm', text: 'Mình con gái ở đây 2 năm rồi, đi làm thêm về muộn 11h đêm suốt thấy an toàn lắm.', is_best: false, time: '2 ngày trước' }
    ],
    4: [
        { author: 'Hoàng Long', meta: 'ĐH Ngoại Thương', text: 'Mấy ngõ như ngõ 80 hoặc ngõ 157 Chùa Láng nhiều chung cư mini mới xây lắm bạn ơi. Có hầm xe rộng rãi nhưng nhớ hỏi kỹ xem có tính thêm phí gửi xe không nha.', is_best: true, time: '5 giờ trước' },
        { author: 'Quốc Anh', meta: 'Người dùng ẩn danh', text: 'Ngõ 80 Chùa Láng công nhận nhiều nhà đẹp thật, cơ mà đỗ xe oto hơi khó.', is_best: false, time: '4 giờ trước' }
    ],
    5: [
        { author: 'Thu Thảo', meta: 'Học viện Bưu chính', text: 'Đúng là mạn này thỉnh thoảng nước hơi yếu thật ấy, nhất là mấy khu tập thể cũ. Bạn nên mua thêm một đầu lọc thô lắp ở vòi lavabo với vòi tắm cho an tâm.', is_best: true, time: '1 ngày trước' },
        { author: 'Đức Huy', meta: 'Mật Mã', text: 'Khu Phùng Khoang nước sinh hoạt có vị hơi lợ, nên dùng máy lọc nước RO để nấu ăn nha mọi người.', is_best: false, time: '20 giờ trước' }
    ]
};

let activeQaIndex = null;
let activeQaButton = null;

function openQaCommentsModal(button) {
    activeQaButton = button;
    activeQaIndex = button.getAttribute('data-qa-index');
    const question = button.getAttribute('data-qa-question');
    const area = button.getAttribute('data-qa-area');
    const time = button.getAttribute('data-qa-time');

    // Populate modal fields
    document.getElementById('qa-modal-area').textContent = area;
    document.getElementById('qa-modal-time').textContent = time;
    document.getElementById('qa-modal-question').textContent = question;

    // Reset input
    document.getElementById('qa-reply-input').value = '';

    // Load comments
    renderQaComments();

    // Show modal
    document.getElementById('qa-comments-modal').classList.remove('hidden');
}

function closeQaCommentsModal() {
    document.getElementById('qa-comments-modal').classList.add('hidden');
}

function renderQaComments() {
    const list = document.getElementById('qa-modal-comments-list');
    if (!list) return;
    list.innerHTML = '';

    const comments = qaCommentsData[activeQaIndex] || [];
    comments.forEach(comment => {
        const card = document.createElement('div');
        const isBest = comment.is_best;
        const borderClass = isBest ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-slate-800/80 bg-slate-900/60';
        const badgeHtml = isBest ? '<span class="text-[8px] text-emerald-400 bg-emerald-500/10 border border-emerald-500/15 px-1.5 py-0.5 rounded font-extrabold uppercase tracking-wider">Best Reply</span>' : '';

        card.className = `p-4 rounded-2xl border ${borderClass} space-y-2 relative overflow-hidden transition-all duration-300`;
        card.innerHTML = `
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[10px] font-bold text-slate-200 block">
                        <i class="fa-solid fa-user-circle text-teal-400 mr-1.5"></i>${comment.author}
                    </span>
                    <span class="text-[8px] text-indigo-400 font-extrabold uppercase mt-0.5 tracking-wider block">
                        ${comment.meta}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    ${badgeHtml}
                    <span class="text-[8px] text-slate-650 font-bold shrink-0">
                        <i class="fa-regular fa-clock mr-1"></i>${comment.time}
                    </span>
                </div>
            </div>
            <p class="text-xs text-slate-350 italic pl-1 leading-relaxed border-l-2 border-slate-850">
                "${escapeHtml(comment.text)}"
            </p>
        `;
        list.appendChild(card);
    });
}

function submitQaReply(event) {
    event.preventDefault();
    const input = document.getElementById('qa-reply-input');
    if (!input) return;
    const text = input.value.trim();
    if (!text) return;

    if (!qaCommentsData[activeQaIndex]) {
        qaCommentsData[activeQaIndex] = [];
    }

    // Add comment to database
    const newComment = {
        author: 'Người dùng ẩn danh',
        meta: 'Thành viên cộng đồng',
        text: text,
        is_best: false,
        time: 'Vừa xong'
    };
    qaCommentsData[activeQaIndex].push(newComment);

    // Render new comment
    renderQaComments();

    // Scroll last comment into view
    const list = document.getElementById('qa-modal-comments-list');
    if (list && list.lastChild) {
        list.lastChild.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Clear input
    input.value = '';

    // Update comment count on Q&A card
    if (activeQaButton) {
        const countSpan = activeQaButton.querySelector('.qa-comment-count');
        if (countSpan) {
            const newCount = qaCommentsData[activeQaIndex].length;
            countSpan.textContent = `${newCount} bình luận`;
        }
    }

    // Show toast notice
    alert('Cảm ơn bạn đã gửi ý kiến');
}

function voteQa(button, direction) {
    const parent = button.parentElement;
    const countSpan = parent.querySelector('.qa-vote-count');
    if (!countSpan) return;

    let currentVotes = parseInt(countSpan.textContent) || 0;
    const activeUp = button.classList.contains('voted-up');
    const activeDown = button.classList.contains('voted-down');

    if (direction === 'up') {
        const downBtn = parent.querySelector('button[aria-label="Downvote"]');
        if (activeUp) {
            button.classList.remove('voted-up');
            countSpan.textContent = currentVotes - 1;
        } else {
            button.classList.add('voted-up');
            if (downBtn && downBtn.classList.contains('voted-down')) {
                downBtn.classList.remove('voted-down');
                countSpan.textContent = currentVotes + 2;
            } else {
                countSpan.textContent = currentVotes + 1;
            }
        }
    } else if (direction === 'down') {
        const upBtn = parent.querySelector('button[aria-label="Upvote"]');
        if (activeDown) {
            button.classList.remove('voted-down');
            countSpan.textContent = currentVotes + 1;
        } else {
            button.classList.add('voted-down');
            if (upBtn && upBtn.classList.contains('voted-up')) {
                upBtn.classList.remove('voted-up');
                countSpan.textContent = currentVotes - 2;
            } else {
                countSpan.textContent = currentVotes - 1;
            }
        }
    }
}

// ── Community Q&A Input Interactions ──
function updateQaCharCount() {
    const input = document.getElementById('qa-input-field');
    const counter = document.getElementById('qa-char-count');
    if (input && counter) {
        const len = input.value.length;
        counter.textContent = `${len}/200`;
        counter.style.color = len >= 180 ? '#f87171' : len >= 120 ? '#fbbf24' : '';
    }
}

function submitQaQuestion() {
    const input = document.getElementById('qa-input-field');
    if (!input) return;
    const question = input.value.trim();
    if (question.length === 0) {
        alert('Vui lòng nhập câu hỏi của bạn.');
        input.focus();
        return;
    }

    const grid = document.getElementById('qa-grid');
    if (!grid) return;

    // Animate submit button
    const submitBtn = document.querySelector('.qa-submit-btn');
    let originalHtml = '';
    if (submitBtn) {
        originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang gửi...';
        submitBtn.classList.add('opacity-80', 'pointer-events-none');
    }

    const newId = 'qa-' + Date.now();
    qaCommentsData[newId] = [
        {
            author: 'Renty Bot',
            meta: 'Hệ thống tự động',
            text: 'Chào bạn, câu hỏi của bạn đã được đăng thành công. Hệ thống sẽ tự động gửi thông báo đến các thành viên trong khu vực để phản hồi sớm nhất!',
            is_best: true,
            time: 'Vừa xong'
        }
    ];

    setTimeout(() => {
        // Create new element
        const newCard = document.createElement('div');
        newCard.className = 'qa-card rounded-2xl border border-slate-800/50 flex flex-col justify-between transition-all duration-300 hover:border-slate-700/60 group/card overflow-hidden animate-fade-in';
        newCard.style.backgroundColor = '#1a1a20';
        newCard.style.animationDelay = '0s';

        newCard.innerHTML = `
            <div class="p-5 pb-0">
                <!-- Meta Row -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-slate-800/80 flex items-center justify-center border border-slate-700/60">
                            <i class="fa-solid fa-user-secret text-xs text-teal-400"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-extrabold text-slate-300">Người dùng ẩn danh</span>
                            <span class="block text-[8px] text-slate-600 font-bold mt-0.5">Vừa xong</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border uppercase tracking-wider bg-teal-500/10 text-teal-400 border-teal-500/20">
                        Cầu Giấy
                    </span>
                </div>

                <!-- Question Title -->
                <h3 class="text-xs font-bold text-slate-200 leading-relaxed group-hover/card:text-teal-400 transition-colors mb-2.5 flex items-start gap-1.5">
                    ${escapeHtml(question)}
                </h3>

                <!-- Tags -->
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span class="px-2 py-0.5 rounded-md bg-slate-800/60 text-slate-500 text-[9px] font-bold border border-slate-800/40">#HỏiẨnDanh</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-800/60 text-slate-500 text-[9px] font-bold border border-slate-800/40">#RentyCommunity</span>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="px-5 pb-4 pt-3 mt-auto border-t border-slate-800/40">
                <!-- Interaction Row -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-0.5 bg-slate-900/50 border border-slate-800/60 rounded-lg overflow-hidden">
                        <button type="button" onclick="voteQa(this, 'up')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-emerald-400 hover:bg-emerald-500/8 transition-all text-xs" aria-label="Upvote">
                            <i class="fa-solid fa-arrow-up"></i>
                        </button>
                        <span class="px-2 text-[11px] font-extrabold text-slate-300 tabular-nums qa-vote-count select-none">1</span>
                        <button type="button" onclick="voteQa(this, 'down')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-rose-400 hover:bg-rose-500/8 transition-all text-xs" aria-label="Downvote">
                            <i class="fa-solid fa-arrow-down"></i>
                        </button>
                    </div>
                    <button type="button" onclick="openQaCommentsModal(this)" class="qa-comment-btn flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-slate-300 transition-colors" data-qa-index="${newId}" data-qa-question="${escapeHtml(question)}" data-qa-area="Cầu Giấy" data-qa-time="Vừa xong">
                        <i class="fa-regular fa-message text-[10px]"></i>
                        <span class="qa-comment-count">1 bình luận</span>
                    </button>
                </div>

                <!-- Best Reply -->
                <div class="qa-best-reply rounded-xl p-3 flex flex-col gap-1.5 bg-slate-900/40 border border-slate-800/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold text-slate-400">Renty Bot</span>
                            <span class="w-3.5 h-3.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[7px] border border-emerald-500/15 inline-flex items-center justify-center" title="Đã xác minh">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        </div>
                        <span class="text-[8px] text-teal-500/70 font-bold uppercase tracking-wider">Best</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed italic">
                        "Chào bạn, câu hỏi của bạn đã được đăng thành công. Hệ thống sẽ tự động gửi thông báo đến các thành viên trong khu vực để phản hồi sớm nhất!"
                    </p>
                </div>
            </div>
        `;

        grid.insertBefore(newCard, grid.firstChild);

        // Scroll the newly posted comment into view smoothly without page reload or jump
        newCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Show thank you toast notification
        alert('Cảm ơn bạn đã gửi ý kiến');

        input.value = '';
        updateQaCharCount();
        if (submitBtn) {
            submitBtn.innerHTML = originalHtml || '<i class="fa-solid fa-paper-plane mr-1"></i> Gửi';
            submitBtn.classList.remove('opacity-80', 'pointer-events-none');
        }
    }, 1000);
}

function loadMoreQaQuestions(button) {
    if (!button) return;
    const grid = document.getElementById('qa-grid');
    if (!grid) return;

    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Đang tải thêm câu hỏi...';
    button.classList.add('pointer-events-none', 'opacity-80');

    setTimeout(() => {
        const mockQuestions = [
            {
                time: '5 giờ trước',
                area: 'Đống Đa',
                areaClass: 'bg-violet-500/10 text-violet-400 border-violet-500/20',
                question: 'Khu vực Chùa Láng có nhà trọ nào tầm 3.5tr - 4tr mà có chỗ để xe máy tầng 1 rộng rãi không ạ? Nghe bảo khu này hay bị chật chỗ để xe.',
                tags: ['Tìm phòng', 'Chùa Láng', 'Chung cư mini'],
                votes: 19,
                comments: 7,
                reply_author: 'Hoàng Long',
                reply_school: 'ĐH Ngoại Thương',
                reply_text: 'Mấy ngõ như ngõ 80 hoặc ngõ 157 Chùa Láng nhiều chung cư mini mới xây lắm bạn ơi. Có hầm xe rộng rãi nhưng nhớ hỏi kỹ xem có tính thêm phí gửi xe không nha.'
            },
            {
                time: '1 ngày trước',
                area: 'Thanh Xuân',
                areaClass: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                question: 'Mọi người cho mình hỏi nước sinh hoạt ở mạn Phùng Khoang dạo này có ổn không ạ? Có bị cặn đen hay mất nước đột ngột không?',
                tags: ['Nước sinh hoạt', 'Phùng Khoang', 'Review'],
                votes: 8,
                comments: 3,
                reply_author: 'Thu Thảo',
                reply_school: 'Học viện Bưu chính',
                reply_text: 'Đúng là mạn này thỉnh thoảng nước hơi yếu thật ấy, nhất là mấy khu tập thể cũ. Bạn nên mua thêm một đầu lọc thô lắp ở vòi lavabo với vòi tắm cho an tâm.'
            }
        ];

        mockQuestions.forEach((qa, idx) => {
            const card = document.createElement('div');
            card.className = 'qa-card rounded-2xl border border-slate-800/50 flex flex-col justify-between transition-all duration-300 hover:border-slate-700/60 group/card overflow-hidden animate-fade-in';
            card.style.backgroundColor = '#1a1a20';
            card.style.animationDelay = `${idx * 0.1}s`;

            const tagsHtml = qa.tags.map(t => `<span class="px-2 py-0.5 rounded-md bg-slate-800/60 text-slate-500 text-[9px] font-bold border border-slate-800/40">#${t}</span>`).join('');

            card.innerHTML = `
                <div class="p-5 pb-0">
                    <!-- Meta Row -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-slate-800/80 flex items-center justify-center border border-slate-700/60">
                                <i class="fa-solid fa-user-secret text-xs text-teal-400"></i>
                            </div>
                            <div>
                                <span class="block text-[10px] font-extrabold text-slate-300">Người dùng ẩn danh</span>
                                <span class="block text-[8px] text-slate-600 font-bold mt-0.5">${qa.time}</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border uppercase tracking-wider ${qa.areaClass}">
                            ${qa.area}
                        </span>
                    </div>

                    <!-- Question Title -->
                    <h3 class="text-xs font-bold text-slate-200 leading-relaxed group-hover/card:text-teal-400 transition-colors mb-2.5">
                        ${qa.question}
                    </h3>

                    <!-- Tags -->
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        ${tagsHtml}
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="px-5 pb-4 pt-3 mt-auto border-t border-slate-800/40">
                    <!-- Interaction Row -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-0.5 bg-slate-900/50 border border-slate-800/60 rounded-lg overflow-hidden">
                            <button type="button" onclick="voteQa(this, 'up')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-emerald-400 hover:bg-emerald-500/8 transition-all text-xs" aria-label="Upvote">
                                <i class="fa-solid fa-arrow-up"></i>
                            </button>
                            <span class="px-2 text-[11px] font-extrabold text-slate-300 tabular-nums qa-vote-count select-none">${qa.votes}</span>
                            <button type="button" onclick="voteQa(this, 'down')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-rose-400 hover:bg-rose-500/8 transition-all text-xs" aria-label="Downvote">
                                <i class="fa-solid fa-arrow-down"></i>
                            </button>
                        </div>
                        <button type="button" onclick="openQaCommentsModal(this)" class="qa-comment-btn flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-slate-300 transition-colors" data-qa-index="${idx + 4}" data-qa-question="${escapeHtml(qa.question)}" data-qa-area="${qa.area}" data-qa-time="${qa.time}">
                            <i class="fa-regular fa-message text-[10px]"></i>
                            <span class="qa-comment-count">${qa.comments} bình luận</span>
                        </button>
                    </div>

                    <!-- Best Reply -->
                    <div class="qa-best-reply rounded-xl p-3 flex flex-col gap-1.5 bg-slate-900/40 border border-slate-800/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold text-slate-400">${qa.reply_author} (${qa.reply_school})</span>
                                <span class="w-3.5 h-3.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[7px] border border-emerald-500/15 inline-flex items-center justify-center" title="Đã xác minh">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </div>
                            <span class="text-[8px] text-teal-500/70 font-bold uppercase tracking-wider">Best</span>
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed italic">
                            "${qa.reply_text}"
                        </p>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });

        // Update button status
        button.innerHTML = '<i class="fa-solid fa-circle-check mr-1.5"></i> Đã tải hết câu hỏi';
        button.classList.remove('pointer-events-none', 'opacity-80');
        button.classList.add('pointer-events-none', 'opacity-60', 'bg-teal-500/10', 'text-teal-400', 'border-teal-500/20');
    }, 800);
}

function showCommentsAlert() {
    if (typeof showCustomAlert === 'function') {
        showCustomAlert('Hệ thống bình luận chi tiết đang được đồng bộ hóa, tính năng này sẽ khả dụng sớm nhất!', 'info');
    }
}

function subscribeNewsletter() {
    const emailInput = document.getElementById('footer-newsletter-email');
    if (!emailInput) return;
    const email = emailInput.value.trim();
    if (!email) {
        if (typeof showCustomAlert === 'function') {
            showCustomAlert('Vui lòng nhập địa chỉ email của bạn.', 'warning');
        }
        emailInput.focus();
        return;
    }
    // Simple email validation regex
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        if (typeof showCustomAlert === 'function') {
            showCustomAlert('Địa chỉ email không hợp lệ. Vui lòng kiểm tra lại.', 'warning');
        }
        emailInput.focus();
        return;
    }

    if (typeof showCustomAlert === 'function') {
        showCustomAlert('Đăng ký nhận tin tức phòng trọ mới thành công!', 'success');
    }
    emailInput.value = '';
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
window.handleSearchInput = handleSearchInput;
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
window.updateQaCharCount = updateQaCharCount;
window.loadMoreQaQuestions = loadMoreQaQuestions;
window.showCommentsAlert = showCommentsAlert;
window.subscribeNewsletter = subscribeNewsletter;
window.openQaCommentsModal = openQaCommentsModal;
window.closeQaCommentsModal = closeQaCommentsModal;
window.submitQaReply = submitQaReply;

function updateDistanceSlider(val) {
    const slider = document.getElementById('distance-slider');
    if (!slider) return;
    const min = parseFloat(slider.min) || 0;
    const max = parseFloat(slider.max) || 3;
    const percentage = ((parseFloat(val) - min) / (max - min)) * 100;
    slider.style.setProperty('--range-progress', `${percentage}%`);
    
    // Update feedback text
    const feedback = document.getElementById('distance-feedback');
    if (feedback) {
        feedback.innerText = `Tìm phòng trong bán kính dưới ${val}km từ Đại học Bách Khoa`;
    }
    
    // Update active state on ticks
    document.querySelectorAll('.tick-mark').forEach(tick => {
        const tickVal = parseFloat(tick.getAttribute('data-value'));
        if (parseFloat(val) >= tickVal) {
            tick.classList.add('active', 'text-teal-400');
        } else {
            tick.classList.remove('active', 'text-teal-400');
        }
    });
    
    // Run filtering
    filterItems();
}

function setSliderValue(val) {
    const slider = document.getElementById('distance-slider');
    if (slider) {
        slider.value = val;
        updateDistanceSlider(val);
    }
}

// Bind distance slider helpers to window
window.updateDistanceSlider = updateDistanceSlider;
window.setSliderValue = setSliderValue;

// Initialize the distance slider progress on DOM Content Loaded
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('distance-slider');
    if (slider) {
        updateDistanceSlider(slider.value);
    }
});

// ════════════════════════════════════════════════════════════════════════
// RENTY CHATBOT ENGINE
// ════════════════════════════════════════════════════════════════════════

let rentyChatbotOpen = false;
let rentyChatbotHistory = [];
let rentyChatbotInitialized = false;
let rentyChatbotSending = false;
const RENTY_CHATBOT_MAX_LENGTH = 300;

// ── Conversation Context (Ngữ cảnh hội thoại) ──────────────────────
let chatbotContext = {
    locations: [],       // Khu vực đã hỏi
    maxPrice: null,      // Giá tối đa
    minPrice: null,      // Giá tối thiểu
    amenities: { pets: false, loft: false, balcony: false, wc: false },
    lastResults: [],     // Kết quả lần trước
    lastQuery: '',       // Câu hỏi gốc lần trước
    turnCount: 0         // Số lượt hội thoại
};

function toggleRentyChatbot() {
    const panel = document.getElementById('renty-chatbot-panel');
    if (!panel) return;
    rentyChatbotOpen = !rentyChatbotOpen;
    panel.classList.toggle('is-open', rentyChatbotOpen);

    // Hide badge when opening
    if (rentyChatbotOpen) {
        const badge = document.getElementById('renty-chatbot-badge');
        if (badge) badge.classList.add('hidden');
    }

    // Show welcome message on first open
    if (rentyChatbotOpen && !rentyChatbotInitialized) {
        rentyChatbotInitialized = true;
        addBotMessage(
            `👋 Chào bạn! Mình là <strong>Renty AI</strong> - Trợ lý tìm kiếm phòng trọ, chung cư mini, căn hộ và review không gian sống thông minh. 🏠<br><br>` +
            `Mình có thể hỗ trợ bạn:<br>` +
            `• 📍 Tìm kiếm nơi ở theo khu vực, tuyến đường, địa danh<br>` +
            `• 💰 Lọc phòng theo khoảng giá và ngân sách phù hợp<br>` +
            `• 🏠 Lọc nhanh phòng có ban công, gác lửng, cho nuôi thú cưng...<br>` +
            `• 🛡️ Tư vấn mẹo thuê phòng an toàn và tránh các rủi ro đặt cọc<br><br>` +
            `Bạn cần mình tìm kiếm nơi ở tại khu vực nào hoặc có tiêu chuẩn cụ thể gì không? Nhắn cho mình biết nhé! 👇`
        );
    }
}
window.toggleRentyChatbot = toggleRentyChatbot;

function clearRentyChatbot() {
    const container = document.getElementById('renty-chatbot-messages');
    if (container) container.innerHTML = '';
    rentyChatbotHistory = [];
    rentyChatbotInitialized = false;
    chatbotContext = {
        locations: [], maxPrice: null, minPrice: null,
        amenities: { pets: false, loft: false, balcony: false, wc: false },
        lastResults: [], lastQuery: '', turnCount: 0
    };
    addBotMessage('Đã xoá lịch sử. Bạn muốn tìm phòng trọ như thế nào? 😊');
    rentyChatbotInitialized = true;
}
window.clearRentyChatbot = clearRentyChatbot;

function addBotMessage(html, roomCards) {
    const container = document.getElementById('renty-chatbot-messages');
    if (!container) return;

    let cardsHtml = '';
    if (roomCards && roomCards.length > 0) {
        cardsHtml = roomCards.map(room => {
            const price = Number(room.price || 0).toLocaleString('vi-VN');
            const amenities = [];
            if (room.balcony === 'true' || room.balcony_txt === 'Có') amenities.push('Ban công');
            if (room.pets === 'true' || room.pets_txt === 'Có') amenities.push('Thú cưng');
            if (room.loft === 'true' || room.loft_txt === 'Có') amenities.push('Gác lửng');
            if (room.wc === 'true' || room.wc_txt === 'Có') amenities.push('WC riêng');
            const amenityText = amenities.slice(0, 3).join(' · ') || 'Cơ bản';

            return `<a href="/renty/room/${room.id}" class="renty-cb-room-card" target="_blank">
                <img src="${escapeHtml(room.cover_image || '')}" alt="${escapeHtml(room.title || '')}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=400&q=60';">
                <div class="renty-cb-room-info">
                    <span class="room-title">${escapeHtml(room.title || 'Phòng trọ')}</span>
                    <span class="room-price">${price}đ/tháng</span>
                    <div class="room-meta">
                        <span><i class="fa-solid fa-ruler-combined"></i> ${room.area_text || room.area + 'm²'}</span>
                        <span><i class="fa-solid fa-star"></i> ${room.rating}</span>
                        <span>${amenityText}</span>
                    </div>
                </div>
            </a>`;
        }).join('');
    }

    const msgEl = document.createElement('div');
    msgEl.className = 'renty-cb-msg renty-cb-msg-bot';
    msgEl.innerHTML = `
        <div class="renty-cb-msg-icon"><i class="fa-solid fa-robot"></i></div>
        <div class="renty-cb-bubble">${html}${cardsHtml}</div>
    `;
    container.appendChild(msgEl);
    container.scrollTop = container.scrollHeight;

    rentyChatbotHistory.push({ role: 'bot', text: html });
}

function addUserMessage(text) {
    const container = document.getElementById('renty-chatbot-messages');
    if (!container) return;

    const msgEl = document.createElement('div');
    msgEl.className = 'renty-cb-msg renty-cb-msg-user';
    msgEl.innerHTML = `
        <div class="renty-cb-msg-icon"><i class="fa-solid fa-user"></i></div>
        <div class="renty-cb-bubble">${escapeHtml(text)}</div>
    `;
    container.appendChild(msgEl);
    container.scrollTop = container.scrollHeight;

    rentyChatbotHistory.push({ role: 'user', text });
}

function showTypingIndicator() {
    const container = document.getElementById('renty-chatbot-messages');
    if (!container) return;

    const typingEl = document.createElement('div');
    typingEl.className = 'renty-cb-msg renty-cb-msg-bot';
    typingEl.id = 'renty-cb-typing';
    typingEl.innerHTML = `
        <div class="renty-cb-msg-icon"><i class="fa-solid fa-robot"></i></div>
        <div class="renty-cb-bubble">
            <div class="renty-cb-typing"><span></span><span></span><span></span></div>
        </div>
    `;
    container.appendChild(typingEl);
    container.scrollTop = container.scrollHeight;
}

function removeTypingIndicator() {
    const el = document.getElementById('renty-cb-typing');
    if (el) el.remove();
}

function setRentyChatbotBusy(isBusy) {
    rentyChatbotSending = isBusy;

    const input = document.getElementById('renty-chatbot-input');
    const sendButton = document.querySelector('.renty-chatbot-send-btn');
    const quickButtons = document.querySelectorAll('#renty-chatbot-quick button');

    if (input) {
        input.disabled = isBusy;
        input.placeholder = isBusy ? 'Renty AI đang trả lời...' : 'Hỏi về phòng trọ, khu vực, giá cả...';
    }

    if (sendButton) {
        sendButton.disabled = isBusy;
        sendButton.classList.toggle('opacity-50', isBusy);
        sendButton.classList.toggle('cursor-not-allowed', isBusy);
        sendButton.setAttribute('aria-busy', isBusy ? 'true' : 'false');
    }

    quickButtons.forEach(button => {
        button.disabled = isBusy;
        button.classList.toggle('opacity-50', isBusy);
        button.classList.toggle('cursor-not-allowed', isBusy);
    });
}

// ── Core: Search rooms from rentyRoomsData ──────────────────────────
function chatbotSearchRooms(query) {
    const data = window.rentyRoomsData;
    if (!data) return [];

    const rooms = Object.values(data);
    const parsed = parseNaturalSearch(query);
    const norm = normalizeText(query);

    // Dọn dẹp từ khóa tìm kiếm thô
    const cleanQuery = norm
        .replace(/^(gan|o|tim|cho thue|khu vuc|xung quanh)\s+/g, '')
        .replace(/\b(gan|o|tim|cho thue|khu vuc|xung quanh)\b/g, '')
        .replace(/\s+/g, ' ')
        .trim();

    let results = rooms.filter(room => {
        // Build searchable text from room data
        const searchable = normalizeText(
            `${room.title || ''} ${room.area_name || ''} ${room.address || ''} ` +
            `${room.location_description || ''} ${room.space_description || ''} ` +
            `${room.scenery_description || ''}`
        );

        // Price filter
        if (parsed.maxPrice && room.price > parsed.maxPrice) return false;

        // Amenity filters
        if (parsed.amenities.pets && room.pets !== 'true' && room.pets_txt !== 'Có') return false;
        if (parsed.amenities.loft && room.loft !== 'true' && room.loft_txt !== 'Có') return false;
        if (parsed.amenities.balcony && room.balcony !== 'true' && room.balcony_txt !== 'Có') return false;
        if (parsed.amenities.wc && room.wc !== 'true' && room.wc_txt !== 'Có') return false;

        // Location filter (Nếu nhận diện được quận/địa danh cụ thể)
        if (parsed.locations.length > 0) {
            const locMatch = parsed.locations.some(loc => searchable.includes(loc));
            if (!locMatch) return false;
        }

        // Keyword match (Khớp từ khóa nghiêm ngặt bằng ranh giới từ AND)
        if (cleanQuery.length > 0) {
            // Nếu có khớp trực tiếp cụm từ nguyên bản
            if (searchable.includes(cleanQuery)) return true;

            const stopWords = ['tim', 'phong', 'tro', 'duoi', 'o', 'gan', 'dai', 'hoc', 'trieu', 'tr',
                'gia', 'co', 'khong', 'cho', 'thue', 'can', 'ho', 'va', 'voi', 'trong',
                'ngoai', 'dep', 're', 'nha', 'chinh', 'chu', 'thang', 'lam'];
            const words = cleanQuery.split(/\s+/).filter(w => !stopWords.includes(w) && w.length > 1);
            
            if (words.length > 0) {
                // TẤT CẢ các từ khóa quan trọng phải xuất hiện trong searchable dưới dạng từ nguyên gốc
                const allWordsMatch = words.every(w => {
                    // Tránh ký tự regex đặc biệt
                    const escapedWord = w.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    const regex = new RegExp('\\b' + escapedWord + '\\b', 'i');
                    return regex.test(searchable);
                });
                return allWordsMatch;
            }
        }

        // Nếu người dùng không nhập từ khóa mà chỉ chọn lọc tiện ích/giá
        return true;
    });

    // Sắp xếp: Ưu tiên phòng trống lên đầu, sau đó sắp xếp theo rating giảm dần, giá tăng dần
    results.sort((a, b) => {
        // Trạng thái trống (empty) lên trước
        const aEmpty = a.status === 'empty' ? 1 : 0;
        const bEmpty = b.status === 'empty' ? 1 : 0;
        if (aEmpty !== bEmpty) {
            return bEmpty - aEmpty; // 1 (empty) đứng trước 0 (khác)
        }

        // Cùng trạng thái trống thì xếp theo rating giảm dần
        const ratingDiff = parseFloat(b.rating || 0) - parseFloat(a.rating || 0);
        if (ratingDiff !== 0) return ratingDiff;

        // Cùng rating thì xếp theo giá tăng dần
        return a.price - b.price;
    });

    return results.slice(0, 5);
}

// ── Core: Generate response ─────────────────────────────────────────
// ── Follow-up detection ─────────────────────────────────────────────
function detectFollowUp(norm) {
    const patterns = {
        cheaper: /\b(re hon|gia re|re nhat|thap hon|it tien)\b/,
        expensive: /\b(dat hon|cao hon|sang hon|xin hon)\b/,
        bigger: /\b(rong hon|to hon|lon hon)\b/,
        other: /\b(phong khac|cai khac|xem them|con gi|co gi khac|khac khong)\b/,
        sameArea: /\b(o do|khu do|cho do|gan do|quanh do|khu vuc do)\b/,
        addBalcony: /\b(co ban cong|them ban cong|ban cong)\b/,
        addPets: /\b(nuoi thu cung|thu cung|co pet|nuoi cho|nuoi meo)\b/,
        addLoft: /\b(co gac|gac lung|gac xep|them gac)\b/,
        addWc: /\b(wc rieng|khep kin|ve sinh rieng)\b/,
        howMany: /\b(bao nhieu phong|co may phong|tong cong)\b/,
        cheapest: /\b(re nhat|thap nhat|gia nhat)\b/,
        best: /\b(tot nhat|diem cao nhat|danh gia cao)\b/
    };
    for (const [type, regex] of Object.entries(patterns)) {
        if (regex.test(norm)) return type;
    }
    return null;
}

// ── Core: Generate response ─────────────────────────────────────────

function generateChatbotResponse(userMsg) {
    const norm = normalizeText(userMsg);
    chatbotContext.turnCount++;

    // ── FAQ / Mẹo thuê trọ detection ──────────────────────────────────
    if (norm.includes('tip') || norm.includes('meo') || norm.includes('an toan') || norm.includes('luu y') || norm.includes('kinh nghiem')) {
        return {
            text: `🛡️ <strong>Mẹo thuê trọ an toàn từ Renty:</strong><br><br>` +
                `• <strong>Xem phòng trực tiếp</strong>: Không đặt cọc khi chưa đến xem thực tế.<br>` +
                `• <strong>Kiểm tra giấy tờ pháp lý</strong>: Yêu cầu xem sổ đỏ hoặc hợp đồng thuê gốc của chủ nhà.<br>` +
                `• <strong>Đọc kỹ hợp đồng</strong>: Chú ý kỹ các điều khoản hoàn cọc, chu kỳ tăng giá và chi phí dịch vụ phụ.<br>` +
                `• <strong>Kiểm tra an ninh xung quanh</strong>: Hệ thống camera, khóa cửa vân tay, hoặc có bảo vệ trông giữ.<br>` +
                `• <strong>Hỏi ý kiến cư dân cũ</strong>: Trò chuyện với người đang thuê xung quanh để biết thêm thông tin.<br>` +
                `• <strong>Chụp ảnh hiện trạng lúc bàn giao</strong>: Lưu lại hình ảnh thiết bị hư hỏng cũ để tránh tranh chấp khi trả phòng sau này.`,
            rooms: []
        };
    }
    if (norm.includes('xin chao') || norm.includes('hello') || norm === 'hi' || norm.includes('chao')) {
        return { text: `Chào bạn! 😄 Mình là Renty AI đây. Bạn muốn tìm phòng trọ, chung cư mini hay căn hộ ở khu vực nào, ngân sách tầm bao nhiêu để mình gợi ý cho bạn nhé?`, rooms: [] };
    }
    if (norm.includes('cam on') || norm.includes('thanks') || norm === 'tks') {
        return { text: `Không có gì nè! 🌟 Rất vui vì đã giúp được bạn. Cần tìm gì thêm cứ nhắn mình nhé!`, rooms: [] };
    }
    if (norm.includes('so sanh') || norm.includes('nen chon')) {
        return {
            text: `📊 <strong>Để so sánh nơi ở (phòng trọ, chung cư mini, căn hộ), bạn nên chú ý:</strong><br><br>` +
                `• <strong>Giá thuê / diện tích (m²)</strong> để tính đơn giá hợp lý.<br>` +
                `• <strong>Khoảng cách di chuyển</strong> đến nơi học tập, làm việc (đo bằng phút đi xe hoặc đi bộ).<br>` +
                `• <strong>Tiện ích sinh hoạt</strong>: Có WC riêng, máy giặt chung/riêng, chỗ phơi đồ đón nắng không.<br>` +
                `• <strong>Chi phí điện nước</strong>: Giá nhà nước hay giá kinh doanh tự phát.<br><br>` +
                `Bạn muốn mình tìm phòng theo tiêu chí nào trong số này?`,
            rooms: []
        };
    }

    // ── TRƯỜNG HỢP 2: Khu vực chưa có dữ liệu hoặc tỉnh thành khác (Ví dụ: Thủ Đức, Quận 9, TP.HCM...) ──
    const unsupportedLocations = [
        'thu duc', 'thu duoc', 'quan 9', 'q9', 'quan 1', 'q1', 'quan 7', 'q7', 'quan 2', 'q2', 'quan 3', 'q3',
        'quan 10', 'q10', 'quan 5', 'q5', 'quan 12', 'q12', 'quan 8', 'q8', 'binh thanh',
        'tan binh', 'tan phu', 'go vap', 'phu nhuan', 'binh tan', 'ho chi minh', 'hcm', 'sai gon', 'da nang', 'hai phong', 'can tho'
    ];
    const requestedUnsupported = unsupportedLocations.find(loc => norm.includes(loc));
    if (requestedUnsupported) {
        let matchedName = requestedUnsupported.toUpperCase();
        if (requestedUnsupported === 'hcm' || requestedUnsupported === 'ho chi minh' || requestedUnsupported === 'sai gon') matchedName = "TP. Hồ Chí Minh";
        if (requestedUnsupported === 'thu duc' || requestedUnsupported === 'thu duoc') matchedName = "Thủ Đức (TP.HCM)";
        if (requestedUnsupported.startsWith('q') && requestedUnsupported.length <= 3) {
            matchedName = `Quận ${requestedUnsupported.substring(1)} (TP.HCM)`;
        }
        
        return {
            text: `✨ <strong>Chào bạn thân mến,</strong><br><br>` +
                `Hiện tại Renty đang tập trung dữ liệu review và thông tin phòng xác thực tại khu vực <strong>Hà Nội</strong> (như Cầu Giấy, Đống Đa, Hai Bà Trưng, Thanh Xuân, Nam Từ Liêm...) nên thật tiếc là mình chưa có nhiều thông tin tại <strong>${matchedName}</strong>. 😢<br><br>` +
                `💡 <strong>Giải pháp thay thế cho bạn:</strong><br>` +
                `• Bạn có muốn tham khảo <strong>kinh nghiệm và mẹo thuê phòng an toàn</strong> áp dụng chung không?<br>` +
                `• Hoặc bạn có muốn tìm kiếm thử các phòng, căn hộ xác thực tại các khu vực thuộc <strong>Hà Nội</strong> không?<br><br>` +
                `Nhắn cho mình biết nếu bạn muốn chuyển hướng nhé!`,
            rooms: []
        };
    }

    // ── TRƯỜNG HỢP 3: Chỉ nhập từ khóa ngắn thiếu ngữ cảnh (Ví dụ: "thú cưng", "dưới 3 triệu", "ban công", "wc riêng") ──
    const shortKeywords = {
        pets: /\b(nuoi thu cung|thu cung|co pet|nuoi cho|nuoi meo|pet|thu cung)\b/,
        price: /\b(duoi 3 trieu|duoi 3tr|3 trieu|gia re|3tr|re)\b/,
        balcony: /\b(ban cong|co ban cong|thoang mat)\b/,
        wc: /\b(wc rieng|khep kin|ve sinh rieng|wc rieng)\b/,
        loft: /\b(co gac|gac lung|gac xep)\b/
    };

    let isOnlyShortKeyword = false;
    let shortType = '';
    
    // Nếu tin nhắn rất ngắn (< 15 ký tự) và khớp một trong các bộ lọc tiện ích/giá mà không có quận, trường học
    const hasLocationOrUni = norm.includes('cau giay') || norm.includes('dong da') || norm.includes('thanh xuan') || 
                             norm.includes('hai ba') || norm.includes('hoang mai') || norm.includes('ba dinh') ||
                             norm.includes('tay ho') || norm.includes('ha dong') || norm.includes('tu liem') || 
                             norm.includes('bach khoa') || norm.includes('ngoai thuong') || norm.includes('quoc gia') ||
                             norm.includes('su pham') || norm.includes('kinh te') || norm.includes('duong') || norm.includes('ngo');

    if (!hasLocationOrUni && userMsg.length < 25) {
        for (const [key, regex] of Object.entries(shortKeywords)) {
            if (regex.test(norm)) {
                isOnlyShortKeyword = true;
                shortType = key;
                break;
            }
        }
    }

    if (isOnlyShortKeyword) {
        let specName = '';
        if (shortType === 'pets') specName = 'cho phép nuôi thú cưng 🐾';
        if (shortType === 'price') specName = 'có giá dưới 3 triệu 💰';
        if (shortType === 'balcony') specName = 'có ban công thoáng mát 🌿';
        if (shortType === 'wc') specName = 'có vệ sinh khép kín riêng tư 🚿';
        if (shortType === 'loft') specName = 'có gác lửng tiện lợi 🏠';

        return {
            text: `🔍 <strong>Mình đã ghi nhận nhu cầu của bạn!</strong><br><br>` +
                `Mình thấy bạn đang tìm kiếm những nơi ở <strong>${specName}</strong>.<br><br>` +
                `👉 Để mình lọc chính xác nhất, bạn muốn tìm quanh <strong>khu vực quận nào</strong>, <strong>tuyến đường nào</strong> hoặc gần <strong>địa danh nào</strong> không? Nhắn cho mình biết nhé!`,
            rooms: []
        };
    }

    // ── Follow-up context handling ───────────────────────────────────
    const followUp = detectFollowUp(norm);
    if (followUp && chatbotContext.turnCount > 1) {
        return handleFollowUp(followUp, userMsg);
    }

    // ── TRƯỜNG HỢP 1: Thực hiện tìm kiếm phòng bình thường ─────────────────
    const parsed = parseNaturalSearch(userMsg);

    // Merge vào context để ghi nhớ
    if (parsed.locations.length > 0) chatbotContext.locations = parsed.locations;
    if (parsed.maxPrice) chatbotContext.maxPrice = parsed.maxPrice;
    if (parsed.amenities.pets) chatbotContext.amenities.pets = true;
    if (parsed.amenities.loft) chatbotContext.amenities.loft = true;
    if (parsed.amenities.balcony) chatbotContext.amenities.balcony = true;
    if (parsed.amenities.wc) chatbotContext.amenities.wc = true;
    chatbotContext.lastQuery = userMsg;

    const rooms = chatbotSearchRooms(userMsg);
    chatbotContext.lastResults = rooms;

    if (rooms.length > 0) {
        return buildRoomResponse(rooms, userMsg);
    }

    // Nếu hoàn toàn không có phòng nào khớp với khu vực Hà Nội được hỏi
    const locationPart = parsed.locations.length > 0 ? ` tại khu vực <strong>${parsed.locations.join(', ')}</strong>` : '';
    return {
        text: `😢 <strong>Renty AI chưa tìm thấy phòng phù hợp</strong>${locationPart} với đầy đủ các tiêu chí của bạn.<br><br>` +
            `💡 <strong>Gợi ý dành cho bạn:</strong><br>` +
            `• Thử nới rộng ngân sách hoặc giảm bớt một vài bộ lọc tiện ích (như ban công hoặc nuôi thú cưng) để xem thêm nhiều lựa chọn.<br>` +
            `• Hoặc thử tìm kiếm theo một số khu vực lân cận xem sao nhé!<br><br>` +
            `Nếu bạn cần tư vấn thêm về khu vực này, cứ nhắn cho mình!`,
        rooms: []
    };
}


function handleFollowUp(type, userMsg) {
    const data = window.rentyRoomsData ? Object.values(window.rentyRoomsData) : [];
    let results = [];
    let msg = '';

    switch (type) {
        case 'cheaper': {
            const maxP = chatbotContext.lastResults.length > 0
                ? Math.min(...chatbotContext.lastResults.map(r => r.price))
                : (chatbotContext.maxPrice || 5000000);
            results = data.filter(r => r.price < maxP && !chatbotContext.lastResults.some(lr => lr.id === r.id));
            if (chatbotContext.locations.length > 0) {
                results = results.filter(r => {
                    const s = normalizeText(`${r.area_name} ${r.address} ${r.location_description}`);
                    return chatbotContext.locations.some(l => s.includes(l));
                });
            }
            results.sort((a, b) => a.price - b.price);
            results = results.slice(0, 5);
            msg = results.length > 0
                ? `💰 Đây là các phòng <strong>rẻ hơn</strong> (dưới ${maxP.toLocaleString('vi-VN')}đ):`
                : `😅 Không tìm thấy phòng rẻ hơn${chatbotContext.locations.length ? ' ở ' + chatbotContext.locations.join(', ') : ''} rồi.`;
            break;
        }
        case 'expensive': {
            const minP = chatbotContext.lastResults.length > 0
                ? Math.max(...chatbotContext.lastResults.map(r => r.price))
                : 0;
            results = data.filter(r => r.price > minP);
            if (chatbotContext.locations.length > 0) {
                results = results.filter(r => {
                    const s = normalizeText(`${r.area_name} ${r.address}`);
                    return chatbotContext.locations.some(l => s.includes(l));
                });
            }
            results.sort((a, b) => a.price - b.price);
            results = results.slice(0, 5);
            msg = results.length > 0
                ? `✨ Phòng <strong>cao cấp hơn</strong> (trên ${minP.toLocaleString('vi-VN')}đ):`
                : `Không có phòng đắt hơn ở khu vực này rồi.`;
            break;
        }
        case 'other': {
            const excludeIds = chatbotContext.lastResults.map(r => r.id);
            results = data.filter(r => !excludeIds.includes(r.id));
            if (chatbotContext.locations.length > 0) {
                results = results.filter(r => {
                    const s = normalizeText(`${r.area_name} ${r.address}`);
                    return chatbotContext.locations.some(l => s.includes(l));
                });
            }
            if (chatbotContext.maxPrice) results = results.filter(r => r.price <= chatbotContext.maxPrice);
            results.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
            results = results.slice(0, 5);
            msg = results.length > 0
                ? `🔄 Đây là các phòng <strong>khác</strong> phù hợp:`
                : `Hết phòng khác ở khu vực này rồi. Thử khu vực mới nhé?`;
            break;
        }
        case 'sameArea': {
            if (chatbotContext.locations.length === 0) {
                return { text: `📍 Bạn chưa chọn khu vực nào trước đó. Hãy cho mình biết khu vực bạn muốn tìm!`, rooms: [] };
            }
            results = data.filter(r => {
                const s = normalizeText(`${r.area_name} ${r.address} ${r.location_description}`);
                return chatbotContext.locations.some(l => s.includes(l));
            });
            results.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
            results = results.slice(0, 5);
            msg = `📍 Tất cả phòng ở <strong>${chatbotContext.locations.join(', ')}</strong>:`;
            break;
        }
        case 'addBalcony': case 'addPets': case 'addLoft': case 'addWc': {
            const amenityMap = { addBalcony: 'balcony', addPets: 'pets', addLoft: 'loft', addWc: 'wc' };
            const amenityNames = { addBalcony: 'ban công', addPets: 'thú cưng', addLoft: 'gác lửng', addWc: 'WC riêng' };
            chatbotContext.amenities[amenityMap[type]] = true;
            results = chatbotSearchRooms(chatbotContext.lastQuery + ' ' + amenityNames[type]);
            msg = results.length > 0
                ? `🏠 Phòng có <strong>${amenityNames[type]}</strong>${chatbotContext.locations.length ? ' ở ' + chatbotContext.locations.join(', ') : ''}:`
                : `Không tìm thấy phòng có ${amenityNames[type]}${chatbotContext.locations.length ? ' ở ' + chatbotContext.locations.join(', ') : ''}.`;
            break;
        }
        case 'cheapest': {
            results = chatbotContext.lastResults.length > 0
                ? [...chatbotContext.lastResults].sort((a, b) => a.price - b.price).slice(0, 1)
                : data.sort((a, b) => a.price - b.price).slice(0, 3);
            msg = `💎 Phòng <strong>rẻ nhất</strong>:`;
            break;
        }
        case 'best': {
            results = chatbotContext.lastResults.length > 0
                ? [...chatbotContext.lastResults].sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating)).slice(0, 1)
                : data.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating)).slice(0, 3);
            msg = `⭐ Phòng <strong>đánh giá cao nhất</strong>:`;
            break;
        }
        default: {
            results = chatbotContext.lastResults;
            msg = `Mình hiểu bạn muốn tìm thêm. Hãy thử mô tả cụ thể hơn nhé!`;
        }
    }

    chatbotContext.lastResults = results;
    if (results.length > 0) {
        return buildRoomResponse(results, msg);
    }
    return { text: msg, rooms: [] };
}

function buildRoomResponse(rooms, customMsg) {
    const areaNames = [...new Set(rooms.map(r => r.area_name).filter(Boolean))].join(', ');
    const prices = rooms.map(r => r.price);
    const minP = Math.min(...prices).toLocaleString('vi-VN');
    const maxP = Math.max(...prices).toLocaleString('vi-VN');
    const available = rooms.filter(r => r.status === 'empty').length;

    let text = '';
    
    // Nếu customMsg chứa HTML sẵn thì dùng, không thì tự dựng tiêu đề chuyên nghiệp
    if (typeof customMsg === 'string' && (customMsg.includes('<') || customMsg.includes('rẻ hơn') || customMsg.includes('cao cấp') || customMsg.includes('khác'))) {
        text = customMsg;
    } else {
        text = `🔍 <strong>Renty AI tìm thấy ${rooms.length} nơi ở phù hợp</strong>`;
        if (areaNames) text += ` tại khu vực <strong>${areaNames}</strong>`;
        text += `:<br>💰 Giá từ <strong>${minP}đ</strong> đến <strong>${maxP}đ</strong>/tháng.`;
        if (available > 0) text += `<br>✅ Hiện có <strong>${available}</strong> nơi đang trống sẵn sàng chuyển đến.`;
    }

    text += `<br><br>📝 <strong>Chi tiết các lựa chọn tốt nhất:</strong><br>`;
    
    rooms.forEach((r, idx) => {
        const ratingVal = r.rating ? parseFloat(r.rating).toFixed(1) : 'Chưa có';
        const ratingStar = ratingVal !== 'Chưa có' ? `⭐ ${ratingVal}/5` : '📝 Chưa có đánh giá';
        
        // Ghi nhận tiện ích nổi bật
        let amList = [];
        if (r.has_wc === 1 || r.has_wc === '1') amList.push('WC khép kín');
        if (r.has_balcony === 1 || r.has_balcony === '1') amList.push('Có ban công');
        if (r.has_loft === 1 || r.has_loft === '1') amList.push('Có gác lửng');
        if (r.allow_pets === 1 || r.allow_pets === '1') amList.push('Cho nuôi pet');
        const amString = amList.length > 0 ? amList.join(', ') : 'Đầy đủ điện nước';

        const priceText = parseInt(r.price).toLocaleString('vi-VN') + 'đ';

        text += `${idx + 1}️⃣ <strong>${escapeHtml(r.title)}</strong><br>` +
                `• 📍 Địa chỉ: ${escapeHtml(r.address || r.area_name)}<br>` +
                `• 💰 Giá: <strong>${priceText}</strong>/tháng<br>` +
                `• 🌿 Tiện ích: ${amString}<br>` +
                `• ${ratingStar} (Đánh giá thực tế từ người thuê trước)<br><br>`;
    });

    text += `👇 Bạn có thể click vào các thẻ nơi ở bên dưới để xem hình ảnh thực tế và liên hệ chủ nhà nhé!`;
    return { text, rooms };
}



// ── Send message handler ────────────────────────────────────────────
// ── Profanity Filter ────────────────────────────────────────────────
const RENTY_PROFANITY_LIST = [
    // Tiếng Việt - từ tục tĩu, xúc phạm phổ biến
    'dm', 'dcm', 'dkm', 'dtm', 'dmm', 'đm', 'đcm', 'đkm', 'đtm', 'đmm',
    'dit', 'dit me', 'đít', 'địt', 'địt mẹ', 'deo me', 'đéo mẹ',
    'cc', 'cac', 'cặc', 'buoi', 'buồi', 'lon', 'lol', 'lồn',
    'du ma', 'duma', 'đụ má', 'đụ mẹ', 'du me',
    'ngu', 'ngu vl', 'ngu vcl', 'ngu lắm',
    'vl', 'vcl', 'vkl', 'vleu',
    'clgt', 'cl', 'cln',
    'deo', 'đéo', 'đậu mẹ', 'đậu má',
    'thang cho', 'thằng chó', 'con cho', 'con chó',
    'mat day', 'mất dạy', 'vo hoc', 'vô học',
    'chan cho', 'chán chó', 'nhu cho', 'như chó',
    'thang ngu', 'thằng ngu', 'con ngu',
    'do ngoc', 'đồ ngốc', 'do ngu', 'đồ ngu',
    'do dien', 'đồ điên', 'thang dien', 'thằng điên',
    'xam loz', 'xạm lồn',
    // Tiếng Anh
    'fuck', 'shit', 'bitch', 'ass', 'dick', 'pussy', 'bastard', 'damn',
    'wtf', 'stfu', 'idiot', 'stupid'
];

function containsProfanity(text) {
    const norm = text.toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/[^a-z0-9\s]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
    
    return RENTY_PROFANITY_LIST.some(word => {
        const normWord = word.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd');
        // Kiểm tra từ nguyên vẹn (word boundary) hoặc cụm từ
        const regex = new RegExp(`(?:^|\\s)${normWord.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}(?:\\s|$)`, 'i');
        return regex.test(norm) || norm === normWord;
    });
}

// ── Rate Limiter (localStorage) ─────────────────────────────────────
function getChatbotUsageToday() {
    const key = 'renty_chatbot_usage';
    const today = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
    try {
        const stored = JSON.parse(localStorage.getItem(key) || '{}');
        if (stored.date !== today) {
            // Reset cho ngày mới
            const fresh = { date: today, count: 0 };
            localStorage.setItem(key, JSON.stringify(fresh));
            return fresh;
        }
        return stored;
    } catch {
        const fresh = { date: today, count: 0 };
        localStorage.setItem(key, JSON.stringify(fresh));
        return fresh;
    }
}

function incrementChatbotUsage() {
    const usage = getChatbotUsageToday();
    usage.count += 1;
    localStorage.setItem('renty_chatbot_usage', JSON.stringify(usage));
    return usage.count;
}

function getChatbotDailyLimit() {
    return window.rentyIsAuthenticated ? 50 : 10;
}

function getRemainingChatbotQuota() {
    const usage = getChatbotUsageToday();
    const limit = getChatbotDailyLimit();
    return Math.max(0, limit - usage.count);
}

// ── Send message handler (with profanity + rate limit) ──────────────
function sendRentyChatbotMessage(presetMsg) {
    if (rentyChatbotSending) return;

    const input = document.getElementById('renty-chatbot-input');
    const text = presetMsg || (input ? input.value.trim() : '');
    if (!text) return;

    if (text.length > RENTY_CHATBOT_MAX_LENGTH) {
        addBotMessage(
            `⚠️ <strong>Tin nhắn quá dài</strong><br><br>` +
            `Bạn vui lòng rút gọn câu hỏi còn tối đa <strong>${RENTY_CHATBOT_MAX_LENGTH} ký tự</strong>. ` +
            `Ví dụ: <strong>"Cầu Giấy dưới 4 triệu có ban công"</strong>.`
        );
        return;
    }

    // Open panel if not open
    if (!rentyChatbotOpen) {
        toggleRentyChatbot();
    }

    // Clear input
    if (input && !presetMsg) input.value = '';

    // ── Kiểm tra từ ngữ tục tĩu ─────────────────────────────────────
    if (containsProfanity(text)) {
        addUserMessage(text);
        showTypingIndicator();
        setTimeout(() => {
            removeTypingIndicator();
            addBotMessage(
                `🚫 <strong>Tin nhắn bị chặn</strong><br><br>` +
                `Renty AI không hỗ trợ các tin nhắn chứa từ ngữ không phù hợp. ` +
                `Hãy sử dụng ngôn ngữ lịch sự để được hỗ trợ tốt nhất nhé! 🙏<br><br>` +
                `<em style="font-size:10px;color:#64748b;">Tin nhắn vi phạm sẽ không được xử lý và không tính vào lượt hỏi.</em>`
            );
        }, 300);
        return;
    }

    // ── Kiểm tra giới hạn lượt hỏi ──────────────────────────────────
    const remaining = getRemainingChatbotQuota();
    const limit = getChatbotDailyLimit();
    const isGuest = !window.rentyIsAuthenticated;

    if (remaining <= 0) {
        addUserMessage(text);
        showTypingIndicator();
        setTimeout(() => {
            removeTypingIndicator();
            const loginHint = isGuest
                ? `<br><br>💡 <strong>Mẹo:</strong> <a href="/login" style="color:#10b981;font-weight:700;text-decoration:underline;">Đăng nhập</a> để được nâng lên <strong>50 lượt/ngày</strong>!`
                : '';
            addBotMessage(
                `⏳ <strong>Đã hết lượt hỏi hôm nay</strong><br><br>` +
                `Bạn đã sử dụng hết <strong>${limit} lượt</strong> hỏi trong ngày. ` +
                `Giới hạn sẽ được đặt lại vào <strong>0:00 ngày mai</strong>.${loginHint}<br><br>` +
                `<em style="font-size:10px;color:#64748b;">Trong khi chờ, bạn vẫn có thể duyệt phòng trọ bằng bộ lọc ở trên!</em>`
            );
        }, 300);
        return;
    }

    // ── Tính lượt và gửi tin nhắn ────────────────────────────────────
    const currentCount = incrementChatbotUsage();
    const newRemaining = Math.max(0, limit - currentCount);

    // Add user message
    addUserMessage(text);

    // Show typing, then respond
    showTypingIndicator();
    setRentyChatbotBusy(true);

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    fetch('/renty/chatbot/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ prompt: text })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })).catch(() => ({
        ok: false,
        data: {}
    })))
    .then(({ ok, data }) => {
        removeTypingIndicator();
        setRentyChatbotBusy(false);
        
        let quotaNote = '';
        if (newRemaining <= 3 && newRemaining > 0) {
            quotaNote = `<br><br><em style="font-size:10px;color:#f59e0b;">⚠️ Còn ${newRemaining} lượt hỏi hôm nay${isGuest ? ' — <a href="/login" style="color:#10b981;font-weight:600;">đăng nhập</a> để có 50 lượt' : ''}.</em>`;
        }

        if (ok && data.success) {
            addBotMessage((data.response || '') + quotaNote, data.rooms || []);
        } else {
            addBotMessage(
                `⚠️ <strong>Renty AI gặp sự cố</strong><br><br>` +
                `Mình chưa thể xử lý câu hỏi này ngay lúc này. Bạn vui lòng thử lại sau ít phút hoặc dùng bộ lọc phòng ở phía trên nhé.` +
                quotaNote
            );
        }
    })
    .catch(err => {
        console.error('Chatbot API error:', err);
        removeTypingIndicator();
        setRentyChatbotBusy(false);
        
        let quotaNote = '';
        if (newRemaining <= 3 && newRemaining > 0) {
            quotaNote = `<br><br><em style="font-size:10px;color:#f59e0b;">⚠️ Còn ${newRemaining} lượt hỏi hôm nay${isGuest ? ' — <a href="/login" style="color:#10b981;font-weight:600;">đăng nhập</a> để có 50 lượt' : ''}.</em>`;
        }
        addBotMessage(`⚠️ <strong>Lỗi kết nối</strong><br><br>Không thể gửi tin nhắn đi. Vui lòng kiểm tra lại kết nối mạng!` + quotaNote);
    });
}
window.sendRentyChatbotMessage = sendRentyChatbotMessage;

