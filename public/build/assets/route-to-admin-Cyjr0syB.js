class l{constructor(e={}){this.mapId=e.mapId||"map",this.apiKey=e.apiKey||"",this.fetchUrl=e.fetchUrl||"",this.refreshInterval=e.refreshInterval||3e4,this.map=null,this.markers=[],this.userLocation=null,this.intervalId=null,this.admins=[],this.routeLayer=null,this.init()}init(){if(typeof L>"u"){console.error("Leaflet library not loaded");return}const e=[14.5995,120.9842];this.map=L.map(this.mapId).setView(e,13),L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${this.apiKey}`,{attribution:'© <a href="https://www.geoapify.com/">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',maxZoom:20}).addTo(this.map),this.loadAdminLocations(),this.startAutoRefresh()}async loadAdminLocations(){try{const t=await(await fetch(this.fetchUrl)).json();t.success?(this.userLocation=t.user_location,this.updateMap(t.admins,this.userLocation),this.updateAdminList(t.admins)):this.showError("Failed to load admin locations")}catch(e){console.error("Error loading admin locations:",e),this.showError("Failed to load admin locations: "+e.message)}}updateMap(e,t){if(this.markers.forEach(o=>this.map.removeLayer(o)),this.markers=[],t){const o=L.marker([t.latitude,t.longitude],{icon:L.divIcon({className:"custom-marker-user",html:`
                        <div style="position: relative;">
                            <div style="
                                background: linear-gradient(135deg, #10B981 0%, #059669 100%);
                                width: 40px;
                                height: 40px;
                                border-radius: 50% 50% 50% 0;
                                transform: rotate(-45deg);
                                border: 4px solid white;
                                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <svg style="transform: rotate(45deg); width: 20px; height: 20px; color: white;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div style="
                                position: absolute;
                                bottom: -8px;
                                left: 50%;
                                transform: translateX(-50%);
                                width: 12px;
                                height: 12px;
                                background: rgba(16, 185, 129, 0.3);
                                border-radius: 50%;
                                animation: pulse 2s infinite;
                            "></div>
                        </div>
                    `,iconSize:[40,40],iconAnchor:[20,40],popupAnchor:[0,-40]}),zIndexOffset:1e3}).addTo(this.map);this.markers.push(o)}if(e.length!==0&&(e.forEach(o=>{const r=L.marker([o.latitude,o.longitude],{icon:L.divIcon({className:"custom-marker-shop",html:`
                        <div style="position: relative;">
                            <div style="
                                background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
                                width: 40px;
                                height: 40px;
                                border-radius: 50% 50% 50% 0;
                                transform: rotate(-45deg);
                                border: 4px solid white;
                                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <svg style="transform: rotate(45deg); width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div style="
                                position: absolute;
                                bottom: -8px;
                                left: 50%;
                                transform: translateX(-50%);
                                width: 12px;
                                height: 12px;
                                background: rgba(59, 130, 246, 0.3);
                                border-radius: 50%;
                            "></div>
                        </div>
                    `,iconSize:[40,40],iconAnchor:[20,40],popupAnchor:[0,-40]}),zIndexOffset:500}).addTo(this.map);this.markers.push(r)}),this.markers.length>0)){const o=L.featureGroup(this.markers);this.map.fitBounds(o.getBounds().pad(.1))}}updateAdminList(e){const t=document.getElementById("admin-list");if(!t)return;if(e.length===0){t.innerHTML=`
                <div class="rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">No Branches Found</h3>
                    <p class="text-gray-500">No branch locations are currently available</p>
                </div>
            `,this.updateStatCards(0,"-");return}e[0].distance_km!==void 0&&e.sort((r,i)=>r.distance_km-i.distance_km),this.admins=e;const o=e.length>0&&e[0].distance_km?`${e[0].distance_km} km`:"-";this.updateStatCards(e.length,o),t.innerHTML=e.map((r,i)=>`
            <button 
                onclick="window.adminTracker.selectAdmin(${r.id})"
                class="admin-item group w-full text-left bg-white rounded-xl border-2 border-gray-200 hover:border-wash hover:shadow-lg transition-all"
                data-admin-id="${r.id}"
            >
                <div class="flex items-center gap-3 p-3">
                    <!-- Branch Number Badge -->
                    <div class="w-10 h-10 bg-gradient-to-br from-wash to-wash-dark rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md flex-shrink-0">
                        ${i+1}
                    </div>
                    
                    <!-- Branch Name -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 group-hover:text-wash transition-colors truncate">${r.branch_name}</h3>
                    </div>
                    
                    <!-- Arrow Icon -->
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-wash transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </button>
        `).join("")}async selectAdmin(e){const t=this.admins.find(o=>o.id===e);if(!t||!this.userLocation){console.error("Admin or user location not found",{adminId:e,admin:t,userLocation:this.userLocation});return}this.map.eachLayer(o=>{(o instanceof L.Polyline||o instanceof L.GeoJSON)&&(o instanceof L.TileLayer||this.markers.includes(o)||this.map.removeLayer(o))}),this.routeLayer&&(this.map.removeLayer(this.routeLayer),this.routeLayer=null);try{console.log("Fetching route for admin:",t.name);const o=await this.fetchRoute(this.userLocation.latitude,this.userLocation.longitude,t.latitude,t.longitude);if(o&&o.features&&o.features.length>0&&o.features[0].geometry){console.log("Drawing route from API data",o.features[0].geometry);try{this.routeLayer=L.geoJSON(o.features[0].geometry,{style:{color:"#3B82F6",weight:6,opacity:.9,lineJoin:"round",lineCap:"round"}}).addTo(this.map),setTimeout(()=>{this.map.fitBounds(this.routeLayer.getBounds(),{padding:[50,50],maxZoom:14})},100),console.log("Route drawn successfully")}catch(r){console.error("Error drawing route:",r),this.drawStraightLine(t)}}else console.warn("Route data invalid or empty, falling back to straight line",o),this.drawStraightLine(t)}catch(o){console.error("Error fetching route:",o),this.drawStraightLine(t)}this.updateRouteInfo(t),this.highlightSelectedAdmin(e)}updateRouteInfo(e){const t=document.getElementById("route-info");if(!t)return;t.classList.remove("hidden"),t.classList.add("flex"),document.getElementById("distance").textContent=e.distance_km?`${e.distance_km} km`:"-",document.getElementById("travel-time").textContent=e.eta_minutes?`${e.eta_minutes} min`:"-",document.getElementById("eta").textContent=e.eta||"-",document.getElementById("shop-name").textContent=e.branch_name||"-";const o=document.getElementById("shop-address");o&&(o.textContent=e.address||"-");const r=document.getElementById("google-maps-link");if(r&&this.userLocation){const i=`https://www.google.com/maps/dir/?api=1&origin=${this.userLocation.latitude},${this.userLocation.longitude}&destination=${e.latitude},${e.longitude}`;r.href=i}}async fetchRoute(e,t,o,r){if(!this.apiKey)return console.error("Geoapify API key is missing"),null;try{const i=`https://api.geoapify.com/v1/routing?waypoints=${e},${t}|${o},${r}&mode=drive&apiKey=${this.apiKey}`;console.log("Fetching route from Geoapify API..."),console.log("Start:",e,t),console.log("End:",o,r);const a=await fetch(i);if(!a.ok){const d=await a.text();throw console.error("Routing API error:",a.status,d),new Error(`Routing API request failed: ${a.status} - ${d}`)}const s=await a.json();return console.log("Route data received successfully:",s),!s.features||s.features.length===0?(console.error("No route features in response:",s),null):s.features[0].geometry?(console.log("Route geometry type:",s.features[0].geometry.type),console.log("Route coordinates count:",s.features[0].geometry.coordinates?.length),s):(console.error("No geometry in route feature:",s.features[0]),null)}catch(i){return console.error("Error fetching route from Geoapify:",i),null}}drawStraightLine(e){console.warn("Drawing straight line fallback (routing API failed or unavailable)"),this.routeLayer&&(this.map.removeLayer(this.routeLayer),this.routeLayer=null),this.routeLayer=L.polyline([[this.userLocation.latitude,this.userLocation.longitude],[e.latitude,e.longitude]],{color:"#EF4444",weight:4,opacity:.7,dashArray:"10, 10"}).addTo(this.map);const t=L.latLngBounds([[this.userLocation.latitude,this.userLocation.longitude],[e.latitude,e.longitude]]);this.map.fitBounds(t,{padding:[80,80]})}highlightSelectedAdmin(e){document.querySelectorAll(".admin-item").forEach(o=>{o.classList.remove("border-wash","bg-wash/5","shadow-lg"),o.classList.add("border-gray-200")});const t=document.querySelector(`[data-admin-id="${e}"]`);t&&(t.classList.remove("border-gray-200"),t.classList.add("border-wash","bg-wash/5","shadow-lg"),t.scrollIntoView({behavior:"smooth",block:"nearest"}))}focusAdmin(e,t){this.map&&this.map.setView([e,t],16)}updateStatCards(e,t){const o=document.getElementById("stat-branches");o&&(o.textContent=e);const r=document.getElementById("stat-nearest");r&&(r.textContent=t)}showError(e){const t=document.getElementById("admin-list");t&&(t.innerHTML=`<p class="text-red-500">${e}</p>`)}startAutoRefresh(){this.intervalId=setInterval(()=>{this.loadAdminLocations()},this.refreshInterval)}stopAutoRefresh(){this.intervalId&&(clearInterval(this.intervalId),this.intervalId=null)}destroy(){this.stopAutoRefresh(),this.map&&this.map.remove()}}document.addEventListener("DOMContentLoaded",()=>{const n=window.geoapifyApiKey||"",e=window.routes?.adminLocation||"/user/api/admins",t=new l({mapId:"map",apiKey:n,fetchUrl:e,refreshInterval:3e4});window.adminTracker=t});
