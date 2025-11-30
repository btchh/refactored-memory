/**
 * Shop Location Map
 * Displays shop locations on an interactive Leaflet map
 */

export class ShopMap {
    constructor(options = {}) {
        this.mapId = options.mapId || 'shopMap';
        this.shops = options.shops || [];
        this.defaultCenter = options.defaultCenter || [13.941, 121.163];
        this.defaultZoom = options.defaultZoom || 13;
        this.map = null;
        
        this.init();
    }

    init() {
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            return;
        }

        // Initialize map
        this.map = L.map(this.mapId).setView(this.defaultCenter, this.defaultZoom);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18,
        }).addTo(this.map);

        // Add shop markers
        this.addShopMarkers();
    }

    addShopMarkers() {
        if (!this.shops || this.shops.length === 0) {
            // Use default shops if none provided
            this.shops = [
                { name: 'WashHour Main', lat: 13.941, lng: 121.163 },
                { name: 'WashHour North', lat: 13.951, lng: 121.173 },
                { name: 'WashHour South', lat: 13.931, lng: 121.153 }
            ];
        }

        this.shops.forEach(shop => {
            L.marker([shop.lat, shop.lng])
                .addTo(this.map)
                .bindPopup(`<b>${shop.name}</b><br>Click for directions`);
        });
    }

    setCenter(lat, lng, zoom = 15) {
        if (this.map) {
            this.map.setView([lat, lng], zoom);
        }
    }

    addMarker(lat, lng, popupText) {
        if (this.map) {
            return L.marker([lat, lng])
                .addTo(this.map)
                .bindPopup(popupText);
        }
    }
}
