/**
 * Asset Viewer ES module for Feebas Elementor Widgets.
 */
import * as THREE from 'https://unpkg.com/three@0.152.2/build/three.module.js';
import { GLTFLoader } from 'https://unpkg.com/three@0.152.2/examples/jsm/loaders/GLTFLoader.js';
import { OrbitControls } from 'https://unpkg.com/three@0.152.2/examples/jsm/controls/OrbitControls.js';

/**
 * Initialize the Three.js scene and render the GLTF/GLB model.
 * @param {HTMLElement} container
 */
function initAssetViewer(container) {
    // Clean up any existing renderer and canvas to avoid multiple contexts
    if (container._feebasAnimationId) {
        cancelAnimationFrame(container._feebasAnimationId);
        delete container._feebasAnimationId;
    }
    // Remove any previous canvas
    const oldCanvas = container.querySelector('canvas');
    if (oldCanvas) {
        container.removeChild(oldCanvas);
    }
    const assetUrl = container.getAttribute('data-asset-url');
    if (!assetUrl) {
        return;
    }
    
    const scene = new THREE.Scene();
    const width = container.clientWidth;
    const height = container.clientHeight;
    const camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    container.appendChild(renderer.domElement);
    // Handle WebGL context loss/restoration
    renderer.domElement.addEventListener('webglcontextlost', function(event) {
        event.preventDefault();
        console.error('WebGL context lost on Asset Viewer canvas');
        if (container._feebasAnimationId) {
            cancelAnimationFrame(container._feebasAnimationId);
            delete container._feebasAnimationId;
        }
    }, false);
    renderer.domElement.addEventListener('webglcontextrestored', function(event) {
        console.log('WebGL context restored on Asset Viewer canvas');
        animate();
    }, false);
    // Add orbit controls for zoom and rotation
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.enableZoom = true;
    controls.enableRotate = true;

    // Zoom limits from attributes
    const minZoomAttr = parseFloat(container.getAttribute('data-min-zoom'));
    const maxZoomAttr = parseFloat(container.getAttribute('data-max-zoom'));
    if (!isNaN(minZoomAttr) && minZoomAttr > 0) {
        controls.minDistance = minZoomAttr;
    }
    if (!isNaN(maxZoomAttr) && maxZoomAttr > 0) {
        controls.maxDistance = maxZoomAttr;
    }
    // Auto-rotate options
    let currentModel = null;
    let prevTime = performance.now();
    const autoRotate = container.getAttribute('data-auto-rotate') === 'true';
    const autoRotateX = container.getAttribute('data-auto-rotate-x') === 'true';
    const autoRotateY = container.getAttribute('data-auto-rotate-y') === 'true';
    const autoRotateZ = container.getAttribute('data-auto-rotate-z') === 'true';
    const speedDeg = parseFloat(container.getAttribute('data-auto-rotate-speed'));
    const autoRotateSpeed = (!isNaN(speedDeg) ? speedDeg : 0) * Math.PI / 180;
    // Disable orbit controls if auto-rotate is enabled
    controls.enabled = !autoRotate;

    // Lights
    const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444);
    hemiLight.position.set(0, 200, 0);
    scene.add(hemiLight);
    
    const dirLight = new THREE.DirectionalLight(0xffffff);
    dirLight.position.set(0, 200, 100);
    scene.add(dirLight);
    
    // Load model
    const loader = new GLTFLoader();
    loader.load(
        assetUrl,
        gltf => {
            const model = gltf.scene;
            currentModel = model;
            scene.add(model);
            // Center model
            const box = new THREE.Box3().setFromObject(model);
            const center = box.getCenter(new THREE.Vector3());
            model.position.sub(center);
            // Fit camera
            const size = box.getSize(new THREE.Vector3()).length();
            const fov = (camera.fov * Math.PI) / 180;
            const cameraZ = Math.abs(size / Math.sin(fov / 2));
            camera.position.set(0, size / 2, cameraZ);
            camera.lookAt(new THREE.Vector3(0, 0, 0));
            // Update controls target and state
            controls.target.set(0, 0, 0);
            controls.update();
        },
        undefined,
        error => {
            console.error('Error loading 3D asset:', error);
        }
    );
    
    // Handle resize
    window.addEventListener('resize', () => {
        const w = container.clientWidth;
        const h = container.clientHeight;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    });
    
    // Animation loop
    function animate() {
        container._feebasAnimationId = requestAnimationFrame(animate);
        const gl = renderer.getContext();
        if (gl.isContextLost()) {
            console.warn('Asset Viewer: WebGL context lost, stopping animation loop');
            cancelAnimationFrame(container._feebasAnimationId);
            delete container._feebasAnimationId;
            return;
        }
        const currentTime = performance.now();
        const delta = (currentTime - prevTime) / 1000;
        prevTime = currentTime;
        if (autoRotate && currentModel) {
            if (autoRotateX) currentModel.rotation.x += autoRotateSpeed * delta;
            if (autoRotateY) currentModel.rotation.y += autoRotateSpeed * delta;
            if (autoRotateZ) currentModel.rotation.z += autoRotateSpeed * delta;
        } else {
            controls.update();
        }
        renderer.render(scene, camera);
    }
    animate();
}

// Fix: Properly wait for Elementor frontend to be initialized
jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction(
        'frontend/element_ready/feebas_3d_asset_viewer.default',
        function($scope) {
            const containerEl = $scope.find('.feebas-asset-viewer-container')[0];
            if (containerEl) {
                initAssetViewer(containerEl);
            }
        }
    );
});