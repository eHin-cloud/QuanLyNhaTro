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

// 3D House State Variables
let targetWallColor = null;
let targetRoofColor = null;
let targetAmbientIntensity = 0.22; // Start in night mode for cozy neon look
let targetKeyLightIntensity = 0.35;
let targetRimIntensity = 2.2;
let targetEmissiveIntensity = 1.8;
let targetEmissiveColor = null;
let autoRotate3D = true;
let targetCameraPos = null;

function setWallTheme(theme) {
    if (!window.THREE) return;
    const colors = {
        slate: { wall: 0x1e293b, roof: 0x4f46e5 },
        terracotta: { wall: 0xc2410c, roof: 0x78350f },
        moss: { wall: 0x0f766e, roof: 0x115e59 },
        gold: { wall: 0xd97706, roof: 0x1e293b }
    };
    const t = colors[theme] || colors.slate;
    if (targetWallColor) targetWallColor.setHex(t.wall);
    if (targetRoofColor) targetRoofColor.setHex(t.roof);

    document.querySelectorAll('.login-3d-controls-panel button[onclick^="setWallTheme"]').forEach(btn => {
        if (btn.getAttribute('onclick').includes(theme)) {
            btn.style.borderColor = 'rgba(255, 255, 255, 0.8)';
        } else {
            btn.style.borderColor = 'transparent';
        }
    });
}
window.setWallTheme = setWallTheme;

function set3DLightMode(mode) {
    if (!window.THREE) return;
    if (mode === 'day') {
        targetAmbientIntensity = 0.95;
        targetKeyLightIntensity = 1.4;
        targetRimIntensity = 0.35;
        targetEmissiveIntensity = 0.0;
        if (targetEmissiveColor) targetEmissiveColor.setHex(0x38bdf8);
        
        const btnDay = document.getElementById('btn-light-day');
        const btnNight = document.getElementById('btn-light-night');
        if (btnDay && btnNight) {
            btnDay.className = "px-2.5 py-1.5 rounded-lg bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 hover:text-white transition-all flex items-center justify-center gap-1.5";
            btnNight.className = "px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-all flex items-center justify-center gap-1.5";
        }
    } else {
        targetAmbientIntensity = 0.22;
        targetKeyLightIntensity = 0.35;
        targetRimIntensity = 2.2;
        targetEmissiveIntensity = 1.8;
        if (targetEmissiveColor) targetEmissiveColor.setHex(0xeab308); // warm golden interior glow
        
        const btnDay = document.getElementById('btn-light-day');
        const btnNight = document.getElementById('btn-light-night');
        if (btnDay && btnNight) {
            btnDay.className = "px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-all flex items-center justify-center gap-1.5";
            btnNight.className = "px-2.5 py-1.5 rounded-lg bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 hover:text-white transition-all flex items-center justify-center gap-1.5";
        }
    }
}
window.set3DLightMode = set3DLightMode;

function set3DCameraPreset(preset) {
    if (!targetCameraPos) return;
    const positions = {
        front: { x: 0, y: 1.1, z: 7.2 },
        iso: { x: 4.5, y: 3.2, z: 6.4 },
        top: { x: 0.1, y: 7.2, z: 2.5 }
    };
    const pos = positions[preset] || positions.iso;
    targetCameraPos.set(pos.x, pos.y, pos.z);

    document.querySelectorAll('.login-3d-controls-panel button[onclick^="set3DCameraPreset"]').forEach(btn => {
        if (btn.getAttribute('onclick').includes(preset)) {
            btn.className = "p-1.5 rounded-lg bg-indigo-500/20 border border-indigo-550/30 text-indigo-300 hover:text-white transition-all text-[10px] font-semibold flex-1";
        } else {
            btn.className = "p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-all text-[10px] font-semibold flex-1";
        }
    });
}
window.set3DCameraPreset = set3DCameraPreset;

function toggle3DRotation(checkbox) {
    autoRotate3D = checkbox.checked;
}
window.toggle3DRotation = toggle3DRotation;

function initLoginHouse3D() {
    const mount = document.getElementById('login-house-3d');

    if (!mount || !window.THREE) return;

    const THREE = window.THREE;
    
    // Initialize target vectors with THREE instance
    if (!targetWallColor) targetWallColor = new THREE.Color(0x1e293b);
    if (!targetRoofColor) targetRoofColor = new THREE.Color(0x4f46e5);
    if (!targetEmissiveColor) targetEmissiveColor = new THREE.Color(0xeab308);
    if (!targetCameraPos) targetCameraPos = new THREE.Vector3(4.5, 3.2, 6.4);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(38, 1, 0.1, 100);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    
    // Shadow map setup
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.outputEncoding = THREE.sRGBEncoding;
    mount.appendChild(renderer.domElement);

    camera.position.copy(targetCameraPos);
    camera.lookAt(0, 0.65, 0);

    // Ambient light
    const ambientLight = new THREE.AmbientLight(0xdbeafe, targetAmbientIntensity);
    scene.add(ambientLight);

    // Directional sunlight
    const keyLight = new THREE.DirectionalLight(0x7dd3fc, targetKeyLightIntensity);
    keyLight.position.set(5, 7.5, 5);
    keyLight.castShadow = true;
    keyLight.shadow.mapSize.width = 1024;
    keyLight.shadow.mapSize.height = 1024;
    keyLight.shadow.camera.near = 0.5;
    keyLight.shadow.camera.far = 20;
    keyLight.shadow.camera.left = -4;
    keyLight.shadow.camera.right = 4;
    keyLight.shadow.camera.top = 4;
    keyLight.shadow.camera.bottom = -4;
    keyLight.shadow.bias = -0.001;
    scene.add(keyLight);

    // Rim neon point light
    const rimLight = new THREE.PointLight(0x6366f1, targetRimIntensity, 12);
    rimLight.position.set(-3.2, 2.4, 2.8);
    rimLight.castShadow = true;
    scene.add(rimLight);

    // Materials
    const wallMaterial = new THREE.MeshStandardMaterial({
        color: 0x1e293b,
        roughness: 0.45,
        metalness: 0.2
    });
    
    const sideMaterial = new THREE.MeshStandardMaterial({
        color: 0x0f172a,
        roughness: 0.5,
        metalness: 0.15
    });

    const roofMaterial = new THREE.MeshStandardMaterial({
        color: 0x4f46e5,
        roughness: 0.35,
        metalness: 0.25
    });

    const glowMaterial = new THREE.MeshStandardMaterial({
        color: 0x38bdf8,
        emissive: targetEmissiveColor,
        emissiveIntensity: targetEmissiveIntensity,
        roughness: 0.2,
        metalness: 0.1
    });

    const doorMaterial = new THREE.MeshStandardMaterial({
        color: 0x020617,
        roughness: 0.45,
        metalness: 0.3
    });

    const frameMaterial = new THREE.MeshStandardMaterial({
        color: 0x0f172a,
        roughness: 0.55,
        metalness: 0.35
    });

    const house = new THREE.Group();

    // Main house body
    const body = new THREE.Mesh(new THREE.BoxGeometry(2.8, 1.9, 2.25), wallMaterial);
    body.position.y = 0.95;
    body.castShadow = true;
    body.receiveShadow = true;
    house.add(body);

    // Right wing
    const rightWing = new THREE.Mesh(new THREE.BoxGeometry(1.18, 1.44, 1.72), sideMaterial);
    rightWing.position.set(1.72, 0.72, -0.1);
    rightWing.castShadow = true;
    rightWing.receiveShadow = true;
    house.add(rightWing);

    // Main Roof
    const roof = new THREE.Mesh(new THREE.ConeGeometry(2.25, 1.12, 4), roofMaterial);
    roof.position.set(0, 2.32, 0);
    roof.rotation.y = Math.PI / 4;
    roof.scale.z = 0.9;
    roof.castShadow = true;
    roof.receiveShadow = true;
    house.add(roof);

    // Wing Roof
    const wingRoof = new THREE.Mesh(new THREE.ConeGeometry(1.18, 0.76, 4), roofMaterial);
    wingRoof.position.set(1.72, 1.62, -0.1);
    wingRoof.rotation.y = Math.PI / 4;
    wingRoof.scale.z = 0.82;
    wingRoof.castShadow = true;
    wingRoof.receiveShadow = true;
    house.add(wingRoof);

    // Chimney
    const chimney = new THREE.Mesh(new THREE.BoxGeometry(0.3, 0.9, 0.3), wallMaterial);
    chimney.position.set(0.8, 2.2, -0.4);
    chimney.castShadow = true;
    chimney.receiveShadow = true;
    house.add(chimney);

    // Door
    const door = new THREE.Mesh(new THREE.BoxGeometry(0.54, 0.98, 0.08), doorMaterial);
    door.position.set(-0.46, 0.49, 1.17);
    door.castShadow = true;
    door.receiveShadow = true;
    house.add(door);

    // Knob
    const knob = new THREE.Mesh(new THREE.SphereGeometry(0.045, 16, 16), glowMaterial);
    knob.position.set(-0.27, 0.48, 1.23);
    house.add(knob);

    // Windows with Frames
    const windowFrameGeo = new THREE.BoxGeometry(0.64, 0.52, 0.04);
    const windowGlassGeo = new THREE.BoxGeometry(0.54, 0.42, 0.06);
    const windowPositions = [
        [-1.1, 1.08, 1.13],
        [0.56, 1.08, 1.13],
        [1.72, 0.9, 0.79]
    ];
    
    windowPositions.forEach(([x, y, z]) => {
        const frame = new THREE.Mesh(windowFrameGeo, frameMaterial);
        frame.position.set(x, y, z);
        frame.castShadow = true;
        frame.receiveShadow = true;
        
        const glass = new THREE.Mesh(windowGlassGeo, glowMaterial);
        glass.position.set(x, y, z + 0.02);
        
        house.add(frame);
        house.add(glass);
    });

    // Pine Trees
    function createPineTree(x, z, scale = 1) {
        const treeGroup = new THREE.Group();
        
        const trunkGeo = new THREE.CylinderGeometry(0.04 * scale, 0.06 * scale, 0.4 * scale, 8);
        const trunkMat = new THREE.MeshStandardMaterial({ color: 0x451a03, roughness: 0.9 });
        const trunk = new THREE.Mesh(trunkGeo, trunkMat);
        trunk.position.y = 0.2 * scale;
        trunk.castShadow = true;
        trunk.receiveShadow = true;
        treeGroup.add(trunk);
        
        const foliageMat = new THREE.MeshStandardMaterial({ color: 0x14532d, roughness: 0.8 });
        
        const l1 = new THREE.Mesh(new THREE.ConeGeometry(0.28 * scale, 0.55 * scale, 8), foliageMat);
        l1.position.y = 0.45 * scale;
        l1.castShadow = true;
        l1.receiveShadow = true;
        treeGroup.add(l1);
        
        const l2 = new THREE.Mesh(new THREE.ConeGeometry(0.20 * scale, 0.42 * scale, 8), foliageMat);
        l2.position.y = 0.75 * scale;
        l2.castShadow = true;
        l2.receiveShadow = true;
        treeGroup.add(l2);
        
        treeGroup.position.set(x, 0, z);
        treeGroup.userData = { l1, l2 };
        
        return treeGroup;
    }

    const t1 = createPineTree(-1.7, -0.7, 1.2);
    const t2 = createPineTree(1.8, -1.1, 0.9);
    const t3 = createPineTree(-1.5, 1.1, 0.8);
    
    house.add(t1);
    house.add(t2);
    house.add(t3);

    // Stone Pathway
    const pathMat = new THREE.MeshStandardMaterial({ color: 0x334155, roughness: 0.95 });
    const stoneGeo = new THREE.BoxGeometry(0.35, 0.02, 0.16);
    
    const s1 = new THREE.Mesh(stoneGeo, pathMat);
    s1.position.set(-0.46, 0.01, 1.35);
    s1.rotation.y = 0.08;
    s1.receiveShadow = true;
    house.add(s1);
    
    const s2 = new THREE.Mesh(stoneGeo, pathMat);
    s2.position.set(-0.54, 0.01, 1.62);
    s2.rotation.y = -0.05;
    s2.receiveShadow = true;
    house.add(s2);
    
    const s3 = new THREE.Mesh(stoneGeo, pathMat);
    s3.position.set(-0.48, 0.01, 1.9);
    s3.rotation.y = 0.12;
    s3.receiveShadow = true;
    house.add(s3);

    // Base Cylinder
    const base = new THREE.Mesh(
        new THREE.CylinderGeometry(2.85, 3.15, 0.16, 48),
        new THREE.MeshStandardMaterial({
            color: 0x111827,
            roughness: 0.55,
            metalness: 0.22
        })
    );
    base.position.y = -0.08;
    base.receiveShadow = true;
    house.add(base);

    // Neon Torus Ring
    const accentIndigo = new THREE.Color('#6366f1');
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

    // Smoke System Setup
    const smokeGroup = new THREE.Group();
    scene.add(smokeGroup);

    const smokeParticles = [];
    const maxSmokeParticles = 25;
    const smokeMaterial = new THREE.MeshBasicMaterial({
        color: 0x94a3b8,
        transparent: true,
        opacity: 0.0,
        depthWrite: false
    });
    const smokeGeo = new THREE.SphereGeometry(0.045, 6, 6);

    for (let i = 0; i < maxSmokeParticles; i++) {
        const p = new THREE.Mesh(smokeGeo, smokeMaterial.clone());
        p.position.set(0.8, 2.65, -0.4); // Chimney exit
        p.scale.setScalar(0.2 + Math.random() * 0.8);
        p.userData = {
            speedY: 0.007 + Math.random() * 0.005,
            speedX: (Math.random() - 0.5) * 0.003,
            speedZ: (Math.random() - 0.5) * 0.003,
            growth: 0.004 + Math.random() * 0.004,
            life: 0,
            maxLife: 120 + Math.random() * 100,
            delay: i * 10
        };
        smokeGroup.add(p);
        smokeParticles.push(p);
    }

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

        // Smooth color interpolation
        wallMaterial.color.lerp(targetWallColor, 0.08);
        roofMaterial.color.lerp(targetRoofColor, 0.08);
        glowMaterial.emissive.lerp(targetEmissiveColor, 0.08);
        
        // Smooth intensity interpolation
        ambientLight.intensity = THREE.MathUtils.lerp(ambientLight.intensity, targetAmbientIntensity, 0.08);
        keyLight.intensity = THREE.MathUtils.lerp(keyLight.intensity, targetKeyLightIntensity, 0.08);
        rimLight.intensity = THREE.MathUtils.lerp(rimLight.intensity, targetRimIntensity, 0.08);
        glowMaterial.emissiveIntensity = THREE.MathUtils.lerp(glowMaterial.emissiveIntensity, targetEmissiveIntensity, 0.08);
        
        // Smooth camera preset interpolation
        camera.position.lerp(targetCameraPos, 0.06);

        // Rotation & Floating effect
        if (autoRotate3D) {
            house.rotation.y = -0.42 + Math.sin(elapsed * 0.45) * 0.18;
            house.position.y = -0.2 + Math.sin(elapsed * 1.0) * 0.05;
        } else {
            // Gradually return to default base rotation
            house.rotation.y = THREE.MathUtils.lerp(house.rotation.y, -0.42, 0.05);
            house.position.y = THREE.MathUtils.lerp(house.position.y, -0.2, 0.05);
        }
        
        ring.rotation.z = elapsed * 0.2;

        // Sway pine trees in the wind
        [t1, t2, t3].forEach((tree, index) => {
            const wind = Math.sin(elapsed * 1.5 + index * 0.7) * 0.02;
            tree.userData.l1.rotation.z = wind;
            tree.userData.l2.rotation.z = wind * 1.4;
        });

        // Smoke animation
        smokeParticles.forEach((p) => {
            if (p.userData.delay > 0) {
                p.userData.delay--;
                return;
            }
            
            p.position.y += p.userData.speedY;
            p.position.x += p.userData.speedX + Math.sin(elapsed * 2 + p.userData.life * 0.05) * 0.001;
            p.position.z += p.userData.speedZ;
            p.scale.addScalar(p.userData.growth);
            p.userData.life++;
            
            const lifeRatio = p.userData.life / p.userData.maxLife;
            // Less visible smoke during the day
            const visibilityMultiplier = targetEmissiveIntensity > 0 ? 1.0 : 0.25;
            
            if (lifeRatio < 0.2) {
                p.material.opacity = (lifeRatio / 0.2) * 0.4 * visibilityMultiplier;
            } else {
                p.material.opacity = (1 - lifeRatio) * 0.4 * visibilityMultiplier;
            }
            
            if (p.userData.life >= p.userData.maxLife) {
                p.position.set(0.8, 2.65, -0.4);
                p.scale.setScalar(0.2 + Math.random() * 0.8);
                p.userData.life = 0;
                p.userData.maxLife = 120 + Math.random() * 100;
            }
        });

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
