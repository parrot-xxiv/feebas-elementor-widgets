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
    // Your existing initAssetViewer code remains unchanged
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
    // Add orbit controls for zoom and rotation
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.enableZoom = true;
    controls.enableRotate = true;
    
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
    (function animate() {
        requestAnimationFrame(animate);
        // Update controls for smooth interaction
        controls.update();
        renderer.render(scene, camera);
    })();
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