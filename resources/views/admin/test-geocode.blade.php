<x-layout title="Test Geocoding">
    <x-nav type="admin" />
    <div class="min-h-screen bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <p class="font-bold">Debug Mode Only</p>
                    <p class="text-sm">This page is only available when APP_DEBUG=true</p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h1 class="text-3xl font-bold mb-6">Test Geocoding</h1>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Address to Geocode
                            </label>
                            <input 
                                type="text" 
                                id="address-input" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="e.g., Bagong Pook, Cupi Rosario Batangas"
                                value="Bagong Pook, Cupi Rosario Batangas"
                            />
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="fresh-checkbox" class="mr-2" />
                                <span class="text-sm text-gray-700">Force fresh geocode (clear cache)</span>
                            </label>
                        </div>

                        <div class="flex gap-3">
                            <button 
                                onclick="testGeocode()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Test Geocode
                            </button>
                            <button 
                                onclick="clearResults()" 
                                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                            >
                                Clear Results
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div id="results-container" class="hidden">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Results</h2>
                        <div id="results-content"></div>
                    </div>

                    <!-- Map -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold mb-4">Map Preview</h2>
                        <div id="map" style="height: 400px; width: 100%;" class="rounded-lg"></div>
                    </div>
                </div>

                <!-- Loading -->
                <div id="loading" class="hidden bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-lg">Geocoding address...</span>
                    </div>
                </div>

                <!-- Example Addresses -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Example Philippine Addresses</h2>
                    <div class="space-y-2">
                        <button onclick="setAddress('Bagong Pook, Cupi Rosario Batangas')" class="block w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded">
                            Bagong Pook, Cupi Rosario Batangas
                        </button>
                        <button onclick="setAddress('Barangay San Jose, Batangas City, Batangas')" class="block w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded">
                            Barangay San Jose, Batangas City, Batangas
                        </button>
                        <button onclick="setAddress('Poblacion, Rosario, Batangas')" class="block w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded">
                            Poblacion, Rosario, Batangas
                        </button>
                        <button onclick="setAddress('Makati City, Metro Manila')" class="block w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded">
                            Makati City, Metro Manila
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let map = null;
        let marker = null;
        const GEOAPIFY_API_KEY = '{{ config('services.geoapify.api_key') }}';

        function setAddress(address) {
            document.getElementById('address-input').value = address;
        }

        function clearResults() {
            document.getElementById('results-container').classList.add('hidden');
            if (marker && map) {
                map.removeLayer(marker);
                marker = null;
            }
        }

        function testGeocode() {
            const address = document.getElementById('address-input').value.trim();
            const fresh = document.getElementById('fresh-checkbox').checked;

            if (!address) {
                alert('Please enter an address');
                return;
            }

            // Show loading
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('results-container').classList.add('hidden');

            // Make request
            fetch('{{ route('admin.test-geocode') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ address, fresh })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading').classList.add('hidden');

                if (data.success) {
                    displayResults(data.result, data.cached);
                } else {
                    alert('Geocoding failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                document.getElementById('loading').classList.add('hidden');
                alert('Error: ' + error.message);
            });
        }

        function displayResults(result, cached) {
            const resultsContent = document.getElementById('results-content');
            
            resultsContent.innerHTML = `
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-sm ${cached ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                            ${cached ? 'Cached Result' : 'Fresh Result'}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Latitude</p>
                            <p class="text-lg font-semibold">${result.latitude}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Longitude</p>
                            <p class="text-lg font-semibold">${result.longitude}</p>
                        </div>
                    </div>
                    ${result.formatted_address ? `
                        <div>
                            <p class="text-sm text-gray-600">Formatted Address</p>
                            <p class="font-semibold">${result.formatted_address}</p>
                        </div>
                    ` : ''}
                    ${result.result_type || result.confidence || result.total_score ? `
                        <div class="grid grid-cols-3 gap-4">
                            ${result.result_type ? `
                                <div>
                                    <p class="text-sm text-gray-600">Result Type</p>
                                    <p class="font-semibold">${result.result_type}</p>
                                </div>
                            ` : ''}
                            ${result.confidence ? `
                                <div>
                                    <p class="text-sm text-gray-600">Confidence</p>
                                    <p class="font-semibold">${result.confidence.toFixed(2)}</p>
                                </div>
                            ` : ''}
                            ${result.total_score ? `
                                <div>
                                    <p class="text-sm text-gray-600">Total Score</p>
                                    <p class="font-semibold text-green-600">${result.total_score.toFixed(2)}</p>
                                </div>
                            ` : ''}
                        </div>
                    ` : ''}
                    <div class="pt-3 border-t">
                        <p class="text-sm text-gray-600 mb-2">Google Maps Link</p>
                        <a href="https://www.google.com/maps?q=${result.latitude},${result.longitude}" 
                           target="_blank"
                           class="text-blue-600 hover:underline">
                            Open in Google Maps →
                        </a>
                    </div>
                </div>
            `;

            document.getElementById('results-container').classList.remove('hidden');

            // Initialize or update map
            if (!map) {
                map = L.map('map').setView([result.latitude, result.longitude], 15);
                L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${GEOAPIFY_API_KEY}`, {
                    attribution: '© Geoapify | © OpenStreetMap contributors',
                    maxZoom: 20
                }).addTo(map);
            } else {
                map.setView([result.latitude, result.longitude], 15);
            }

            // Add or update marker
            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([result.latitude, result.longitude]).addTo(map)
                .bindPopup(result.formatted_address || 'Location')
                .openPopup();
        }
    </script>
</x-layout>
