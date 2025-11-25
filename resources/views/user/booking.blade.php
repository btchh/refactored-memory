<x-layout>
    <x-slot name="title">Booking</x-slot>

    <div class="max-w-6xl mx-auto px-4 py-6 space-y-6">
        <!-- Header with gradient -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                🧺 Book Your Laundry
            </h1>
            <p class="text-gray-600">Select services, schedule pickup, and we'll handle the rest!</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Booking Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-lg border-2 border-blue-100 p-8 space-y-6 hover:shadow-xl transition-shadow duration-300">

                    <!-- Customer -->
                    <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                        <label class="block font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <span class="text-xl">👤</span> Customer
                        </label>
                        <input type="text" value="{{ Auth::user()->fname }} {{ Auth::user()->lname }}" readonly class="input input-bordered w-full bg-gray-50 font-medium">
                    </div>

                    <!-- Item Selector -->
                    <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500">
                        <label class="block font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <span class="text-xl">👕</span> Select Item Type
                        </label>
                        <select id="itemType" class="select select-bordered w-full text-lg hover:border-purple-400 focus:border-purple-500 transition-colors" onchange="loadServices(this.value)">
                            <option value="">-- Choose Item --</option>
                            <option value="clothes">👕 Clothes</option>
                            <option value="comforter">🛏️ Comforter</option>
                            <option value="shoes">👟 Shoes</option>
                        </select>
                    </div>

                    <!-- Services -->
                    <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                        <label class="block font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <span class="text-xl">✨</span> Select Services
                        </label>
                        <div id="serviceList" class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>
                    </div>

                    <!-- Schedule -->
                    <div class="bg-white rounded-lg p-4 border-l-4 border-orange-500">
                        <label class="block font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <span class="text-xl">📅</span> Schedule Pickup
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-600 mb-1">Date</label>
                                <input type="date" id="date" name="date" class="input input-bordered w-full hover:border-orange-400 focus:border-orange-500" required>
                            </div>
                            <div>
                                <label for="time" class="block text-sm font-medium text-gray-600 mb-1">Time</label>
                                <input type="time" id="time" name="time" class="input input-bordered w-full hover:border-orange-400 focus:border-orange-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Address & Map -->
                    <div class="bg-white rounded-lg p-4 border-l-4 border-red-500">
                        <label class="block font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <span class="text-xl">📍</span> Pickup Location
                        </label>
                        <input type="text" id="address" name="address" class="input input-bordered w-full mb-4 hover:border-red-400 focus:border-red-500" placeholder="Click on the map to set location" required>
                        <div id="map" class="w-full h-64 rounded-lg border-2 border-gray-300 shadow-inner relative" style="z-index: 1;"></div>
                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="lng" name="lng">
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6">
                        <button type="button" class="btn btn-outline btn-info w-full sm:w-auto hover:scale-105 transition-transform" onclick="generateSummary()">
                            👁️ Preview Summary
                        </button>
                        <form action="{{ route('user.booking') }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <input type="hidden" name="payload" id="payload">
                            <button type="submit" class="btn btn-primary w-full sm:w-auto bg-gradient-to-r from-blue-600 to-purple-600 border-none hover:scale-105 transition-transform">
                                🚀 Submit Booking
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Receipt -->
            <div class="lg:sticky lg:top-24">
                <div class="card bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-lg border-2 border-green-200 p-6 space-y-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-3xl">🧾</span>
                        <h2 class="text-2xl font-bold text-green-700">Receipt</h2>
                    </div>
                    <div class="bg-white rounded-lg p-4 min-h-[200px]">
                        <ul id="summaryList" class="text-sm text-gray-700 space-y-2"></ul>
                    </div>
                    <div class="pt-4 border-t-2 border-green-300">
                        <p class="text-2xl font-bold text-green-700 flex justify-between items-center">
                            <span>Total:</span>
                            <span class="text-3xl">₱<span id="totalAmount">0</span></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet + Geoapify -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const services = {
            clothes: [
                { name: 'Wash', price: 70 },
                { name: 'Dry', price: 70 },
                { name: 'Detergent', price: 15 },
                { name: 'Fabric Conditioner', price: 20 },
                { name: 'Fold', price: 20 },
                { name: 'Delivery', price: 20 },
                { name: 'Per Load (With Fold)', price: 165, bundle: ['Wash', 'Dry', 'Detergent', 'Fabric Conditioner', 'Fold'] },
                { name: 'Per Load (Without Fold)', price: 175, bundle: ['Wash', 'Dry', 'Detergent', 'Fabric Conditioner'] },
            ],
            comforter: [
                { name: 'Single Piece', price: 200 },
                { name: 'Safai', price: 15 },
                { name: 'Color Protection', price: 25 },
                { name: 'Packaging', price: 20 },
            ],
            shoes: [
                { name: 'Shoe Cleaning', price: 50 },
                { name: 'Polish', price: 20 },
                { name: 'Deodorize', price: 15 },
            ]
        };

        let selected = [];

        function loadServices(type) {
            const list = document.getElementById('serviceList');
            list.innerHTML = '';
            selected = [];
            updateSummary();

            if (!services[type]) return;

            services[type].forEach(svc => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline btn-sm hover:scale-105 transition-transform';
                btn.textContent = `${svc.name} (₱${svc.price})`;
                btn.onclick = () => {
                    if (svc.bundle) {
                        svc.bundle.forEach(b => {
                            const bundled = services[type].find(s => s.name === b);
                            if (bundled && !selected.find(s => s.name === bundled.name)) {
                                selected.push(bundled);
                            }
                        });
                    }
                    if (!selected.find(s => s.name === svc.name)) {
                        selected.push(svc);
                    }
                    btn.classList.add('btn-primary');
                    btn.classList.remove('btn-outline');
                    updateSummary();
                };
                list.appendChild(btn);
            });
        }

        function updateSummary() {
            const list = document.getElementById('summaryList');
            const total = document.getElementById('totalAmount');
            list.innerHTML = '';
            let sum = 0;

            selected.forEach(item => {
                sum += item.price;
                const li = document.createElement('li');
                li.className = 'flex justify-between items-center py-1 border-b border-gray-100';
                li.innerHTML = `<span class="font-medium">${item.name}</span><span class="text-green-600 font-semibold">₱${item.price}</span>`;
                list.appendChild(li);
            });

            total.textContent = sum;
        }

        function generateSummary() {
            const payload = {
                customer: `{{ Auth::user()->fname }} {{ Auth::user()->lname }}`,
                date: document.getElementById('date').value,
                time: document.getElementById('time').value,
                address: document.getElementById('address').value,
                lat: document.getElementById('lat').value,
                lng: document.getElementById('lng').value,
                services: selected,
                total: document.getElementById('totalAmount').textContent
            };
            document.getElementById('payload').value = JSON.stringify(payload);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('map').setView([13.941, 121.163], 13);
            L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-liberty/{z}/{x}/{y}.png?apiKey=YOUR_GEOAPIFY_API_KEY`, {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(map);

            let marker;
            map.on('click', function(e) {
                if (marker) map.removeLayer(marker);
                marker = L.marker(e.latlng).addTo(map);
                document.getElementById('lat').value = e.latlng.lat;
                document.getElementById('lng').value = e.latlng.lng;
                document.getElementById('address').value = `Lat: ${e.latlng.lat}, Lng: ${e.latlng.lng}`;
                document.getElementById('address').value = `Lat: ${e.latlng.lat}, Lng: ${e.latlng.lng}`;
            });
        });
    </script>
</x-layout>
