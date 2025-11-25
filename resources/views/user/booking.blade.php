<x-layout>
    <x-slot name="title">Booking</x-slot>

    <div class="max-w-6xl mx-auto px-4 py-6 space-y-6">
        <h1 class="text-3xl font-bold text-blue-600 text-center">Laundry Booking</h1>
        <p class="text-center text-gray-600">Select your item and services, pin your location, and review your receipt before submitting.</p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Booking Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card bg-white rounded-xl shadow-md border border-gray-200 p-6 space-y-6">

                    <!-- Customer -->
                    <div>
                        <label class="block font-medium text-gray-700">Customer</label>
                        <input type="text" value="{{ Auth::user()->fname }} {{ Auth::user()->lname }}" readonly class="input input-bordered w-full bg-gray-100">
                    </div>

                    <!-- Item Selector -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">Select Item Type</label>
                        <select id="itemType" class="select select-bordered w-full" onchange="loadServices(this.value)">
                            <option value="">-- Choose Item --</option>
                            <option value="clothes">Clothes</option>
                            <option value="comforter">Comforter</option>
                            <option value="shoes">Shoes</option>
                        </select>
                    </div>

                    <!-- Services -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">Select Services</label>
                        <div id="serviceList" class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>
                    </div>

                    <!-- Schedule -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="date" class="block font-medium text-gray-700">Booking Date</label>
                            <input type="date" id="date" name="date" class="input input-bordered w-full" required>
                        </div>
                        <div>
                            <label for="time" class="block font-medium text-gray-700">Booking Time</label>
                            <input type="time" id="time" name="time" class="input input-bordered w-full" required>
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block font-medium text-gray-700">Pickup Address</label>
                        <input type="text" id="address" name="address" class="input input-bordered w-full" placeholder="Enter your location or click on the map" required>
                    </div>

                    <!-- Map -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">Pin Your Location</label>
                        <div id="map" class="w-full h-64 rounded-lg border border-gray-300"></div>
                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="lng" name="lng">
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center pt-4">
                        <button type="button" class="btn btn-outline btn-info" onclick="generateSummary()">Preview Summary</button>
                        <form action="{{ route('user.booking') }}" method="POST">
                            @csrf
                            <input type="hidden" name="payload" id="payload">
                            <button type="submit" class="btn btn-primary">Submit Booking</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Receipt -->
            <div>
                <div class="card bg-white rounded-xl shadow-md border border-gray-200 p-6 space-y-4">
                    <h2 class="text-xl font-semibold text-blue-600">Receipt Summary</h2>
                    <ul id="summaryList" class="text-sm text-gray-700 space-y-1"></ul>
                    <div class="pt-2 border-t border-gray-300">
                        <p class="font-bold text-blue-600">Total: ₱<span id="totalAmount">0</span></p>
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
                btn.className = 'btn btn-outline btn-sm';
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
                li.textContent = `${item.name} - ₱${item.price}`;
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
