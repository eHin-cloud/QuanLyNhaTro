// Project-specific JavaScript for QuanLyNhaTro.

const MAX_COMPARE_ROOMS = 3;
const selectedCompareRoomIds = [];

function getRentyRooms() {
    return window.rentyRooms || {};
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function formatCurrency(value) {
    return Number(value || 0).toLocaleString('vi-VN') + 'đ';
}

function getSelectedCompareRooms() {
    const rooms = getRentyRooms();

    return selectedCompareRoomIds
        .map((id) => rooms[id])
        .filter(Boolean);
}

function normalizeCompareRoom(room) {
    const securityText = room.sec || 'Đang cập nhật';
    const securityMatch = String(securityText).match(/\((\d)\/5\)/);
    const securityScore = securityMatch ? `${securityMatch[1]}/5` : securityText;

    return {
        title: room.room_number ? `Phòng ${room.room_number}` : room.title,
        price: formatCurrency(room.price),
        area: room.area_text || `${room.area || '-'} m²`,
        rating: `${room.rating || '-'} sao`,
        loft: room.loft_txt || (room.loft === 'true' ? 'Có' : 'Không'),
        security: securityScore
    };
}

function removeCompareRoom(roomId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const normalizedId = String(roomId);
    
    // Find checkbox and uncheck it
    const checkboxes = document.querySelectorAll('.compare-checkbox');
    checkboxes.forEach((cb) => {
        // Match string ID parameters from dynamic attributes/onchange calls
        if (cb.outerHTML.includes(normalizedId) || cb.getAttribute('onchange')?.includes(normalizedId)) {
            cb.checked = false;
        }
    });

    const index = selectedCompareRoomIds.indexOf(normalizedId);
    if (index !== -1) {
        selectedCompareRoomIds.splice(index, 1);
    }
    updateCompareDock();
}
window.removeCompareRoom = removeCompareRoom;

function updateCompareDock() {
    const dock = document.getElementById('compare-dock');
    const container = document.getElementById('compare-thumbnails');
    const submitBtn = document.getElementById('compare-btn-submit');

    if (!dock) return;

    if (selectedCompareRoomIds.length === 0) {
        dock.classList.add('hidden');
        return;
    }

    dock.classList.remove('hidden');

    if (container) {
        const selectedRooms = getSelectedCompareRooms();
        container.innerHTML = selectedRooms
            .map((room) => {
                const coverImage = room.cover_image || 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';
                const displayName = room.room_number ? `P.${room.room_number}` : 'Phòng';
                return `
                    <div class="relative w-10 h-10 rounded-xl overflow-hidden border border-slate-700/60 shrink-0 group/thumb">
                        <img src="${coverImage}" alt="${displayName}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-slate-950/40 flex items-center justify-center">
                            <span class="text-[8px] font-extrabold text-white uppercase tracking-wider drop-shadow-sm">${displayName}</span>
                        </div>
                        <button type="button" onclick="removeCompareRoom('${room.id}', event)" class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-rose-500 hover:bg-rose-600 text-white rounded-full flex items-center justify-center text-[7px] border border-slate-900 shadow transition-all z-10">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                `;
            })
            .join('');
    }

    if (submitBtn) {
        submitBtn.innerHTML = `<i class="fa-solid fa-code-compare mr-1"></i> So sánh ngay (${selectedCompareRoomIds.length})`;
    }
}

function toggleCompare(roomId, checkbox) {
    const normalizedId = String(roomId);
    const exists = selectedCompareRoomIds.includes(normalizedId);

    if (checkbox.checked) {
        if (exists) return;

        if (selectedCompareRoomIds.length >= MAX_COMPARE_ROOMS) {
            checkbox.checked = false;
            alert('Chỉ được so sánh tối đa 3 phòng trọ cùng lúc.');
            return;
        }

        selectedCompareRoomIds.push(normalizedId);
    } else {
        const index = selectedCompareRoomIds.indexOf(normalizedId);
        if (index !== -1) {
            selectedCompareRoomIds.splice(index, 1);
        }
    }

    updateCompareDock();
}

function clearCompare() {
    selectedCompareRoomIds.splice(0, selectedCompareRoomIds.length);

    document.querySelectorAll('.compare-checkbox').forEach((checkbox) => {
        checkbox.checked = false;
    });

    updateCompareDock();
    closeCompareModal();
}

function renderCompareTable() {
    const head = document.getElementById('compare-table-head');
    const body = document.getElementById('compare-table-body');
    const selectedRooms = getSelectedCompareRooms().map(normalizeCompareRoom);

    if (!head || !body) return;

    head.innerHTML = `
        <tr>
            <th>Tiêu chí</th>
            ${selectedRooms.map((room) => `<th>${escapeHtml(room.title)}</th>`).join('')}
        </tr>
    `;

    const rows = [
        ['Giá', 'price'],
        ['Diện tích', 'area'],
        ['Đánh giá', 'rating'],
        ['Có gác lửng', 'loft'],
        ['An ninh', 'security']
    ];

    body.innerHTML = rows.map(([label, key]) => `
        <tr>
            <td>${label}</td>
            ${selectedRooms.map((room) => `<td>${escapeHtml(room[key])}</td>`).join('')}
        </tr>
    `).join('');
}

function openCompareModal() {
    const modal = document.getElementById('compare-modal');

    if (!modal || selectedCompareRoomIds.length === 0) return;

    renderCompareTable();
    modal.classList.remove('hidden');
}

function closeCompareModal() {
    const modal = document.getElementById('compare-modal');

    if (!modal) return;
    modal.classList.add('hidden');
}

window.toggleCompare = toggleCompare;
window.clearCompare = clearCompare;
window.openCompareModal = openCompareModal;
window.closeCompareModal = closeCompareModal;

window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeCompareModal();
    }
});

function initLoginHouse3D() {
    const mount = document.getElementById('login-house-3d');

    if (!mount || !window.THREE) return;

    const THREE = window.THREE;
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(38, 1, 0.1, 100);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    const house = new THREE.Group();
    const accentBlue = new THREE.Color('#38bdf8');
    const accentIndigo = new THREE.Color('#6366f1');

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.outputEncoding = THREE.sRGBEncoding;
    mount.appendChild(renderer.domElement);

    camera.position.set(4.5, 3.2, 6.4);
    camera.lookAt(0, 0.65, 0);

    scene.add(new THREE.AmbientLight(0xdbeafe, 0.68));

    const keyLight = new THREE.DirectionalLight(0x7dd3fc, 1.15);
    keyLight.position.set(4, 6, 4);
    scene.add(keyLight);

    const rimLight = new THREE.PointLight(0x6366f1, 1.8, 12);
    rimLight.position.set(-3.2, 2.4, 2.8);
    scene.add(rimLight);

    const wallMaterial = new THREE.MeshStandardMaterial({
        color: 0x1e293b,
        roughness: 0.48,
        metalness: 0.18
    });
    const sideMaterial = new THREE.MeshStandardMaterial({
        color: 0x0f172a,
        roughness: 0.56,
        metalness: 0.16
    });
    const roofMaterial = new THREE.MeshStandardMaterial({
        color: 0x4f46e5,
        roughness: 0.38,
        metalness: 0.22
    });
    const glowMaterial = new THREE.MeshStandardMaterial({
        color: 0x38bdf8,
        emissive: accentBlue,
        emissiveIntensity: 0.58,
        roughness: 0.26,
        metalness: 0.12
    });
    const doorMaterial = new THREE.MeshStandardMaterial({
        color: 0x020617,
        roughness: 0.4,
        metalness: 0.32
    });

    const body = new THREE.Mesh(new THREE.BoxGeometry(2.8, 1.9, 2.25), wallMaterial);
    body.position.y = 0.95;
    house.add(body);

    const rightWing = new THREE.Mesh(new THREE.BoxGeometry(1.18, 1.44, 1.72), sideMaterial);
    rightWing.position.set(1.72, 0.72, -0.1);
    house.add(rightWing);

    const roof = new THREE.Mesh(new THREE.ConeGeometry(2.25, 1.12, 4), roofMaterial);
    roof.position.set(0, 2.32, 0);
    roof.rotation.y = Math.PI / 4;
    roof.scale.z = 0.9;
    house.add(roof);

    const wingRoof = new THREE.Mesh(new THREE.ConeGeometry(1.18, 0.76, 4), roofMaterial);
    wingRoof.position.set(1.72, 1.62, -0.1);
    wingRoof.rotation.y = Math.PI / 4;
    wingRoof.scale.z = 0.82;
    house.add(wingRoof);

    const door = new THREE.Mesh(new THREE.BoxGeometry(0.54, 0.98, 0.08), doorMaterial);
    door.position.set(-0.46, 0.49, 1.17);
    house.add(door);

    const knob = new THREE.Mesh(new THREE.SphereGeometry(0.045, 16, 16), glowMaterial);
    knob.position.set(-0.27, 0.48, 1.23);
    house.add(knob);

    const windowGeometry = new THREE.BoxGeometry(0.54, 0.42, 0.08);
    [
        [-1.1, 1.08, 1.17],
        [0.56, 1.08, 1.17],
        [1.72, 0.9, 0.79]
    ].forEach(([x, y, z]) => {
        const windowMesh = new THREE.Mesh(windowGeometry, glowMaterial);
        windowMesh.position.set(x, y, z);
        house.add(windowMesh);
    });

    const base = new THREE.Mesh(
        new THREE.CylinderGeometry(2.85, 3.15, 0.16, 48),
        new THREE.MeshStandardMaterial({
            color: 0x111827,
            roughness: 0.55,
            metalness: 0.22
        })
    );
    base.position.y = -0.08;
    house.add(base);

    const ring = new THREE.Mesh(
        new THREE.TorusGeometry(2.85, 0.018, 8, 96),
        new THREE.MeshBasicMaterial({ color: accentIndigo })
    );
    ring.position.y = 0.04;
    ring.rotation.x = Math.PI / 2;
    house.add(ring);

    house.rotation.y = -0.42;
    house.position.y = -0.2;
    scene.add(house);

    function resizeRenderer() {
        const width = mount.clientWidth || 320;
        const height = mount.clientHeight || 260;

        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
    }

    const resizeObserver = new ResizeObserver(resizeRenderer);
    resizeObserver.observe(mount);
    resizeRenderer();

    let frameId = 0;
    const clock = new THREE.Clock();

    function animate() {
        const elapsed = clock.getElapsedTime();

        house.rotation.y = -0.42 + Math.sin(elapsed * 0.55) * 0.17;
        house.position.y = -0.2 + Math.sin(elapsed * 1.2) * 0.055;
        ring.rotation.z = elapsed * 0.22;
        renderer.render(scene, camera);
        frameId = window.requestAnimationFrame(animate);
    }

    animate();

    window.addEventListener('beforeunload', () => {
        window.cancelAnimationFrame(frameId);
        resizeObserver.disconnect();
        renderer.dispose();
    }, { once: true });
}

document.addEventListener('DOMContentLoaded', initLoginHouse3D);
