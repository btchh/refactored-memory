class d{constructor(t={}){this.mapId=t.mapId||"map",this.apiKey=t.apiKey||"",this.fetchUrl=t.fetchUrl||"",this.refreshInterval=t.refreshInterval||3e4,this.map=null,this.markers=[],this.userLocation=null,this.intervalId=null,this.admins=[],this.routeLayer=null,this.init()}init(){if(typeof L>"u"){console.error("Leaflet library not loaded");return}const t=[14.5995,120.9842];this.map=L.map(this.mapId).setView(t,13),L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${this.apiKey}`,{attribution:'© <a href="https://www.geoapify.com/">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',maxZoom:20}).addTo(this.map),this.loadAdminLocations(),this.startAutoRefresh()}async loadAdminLocations(){try{const e=await(await fetch(this.fetchUrl)).json();e.success?(this.userLocation=e.user_location,this.updateMap(e.admins,this.userLocation),this.updateAdminList(e.admins)):this.showError("Failed to load admin locations")}catch(t){console.error("Error loading admin locations:",t),this.showError("Failed to load admin locations: "+t.message)}}updateMap(t,e){if(this.markers.forEach(o=>this.map.removeLayer(o)),this.markers=[],e){const o=L.marker([e.latitude,e.longitude],{icon:L.divIcon({className:"custom-marker-user",html:`
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
                    `,iconSize:[40,40],iconAnchor:[20,40],popupAnchor:[0,-40]}),zIndexOffset:1e3}).addTo(this.map);this.markers.push(o)}if(t.length!==0&&(t.forEach(o=>{const r=L.marker([o.latitude,o.longitude],{icon:L.divIcon({className:"custom-marker-shop",html:`
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
                    `,iconSize:[40,40],iconAnchor:[20,40],popupAnchor:[0,-40]}),zIndexOffset:500}).addTo(this.map);this.markers.push(r)}),this.markers.length>0)){const o=L.featureGroup(this.markers);this.map.fitBounds(o.getBounds().pad(.1))}}updateAdminList(t){const e=document.getElementById("admin-list");if(!e)return;if(t.length===0){e.innerHTML=`
                <div class="rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">No Branches Found</h3>
                    <p class="text-gray-500">No branch locations are currently available</p>
                </div>
            `,this.updateStatCards(0,"-");return}t[0].distance_km!==void 0&&t.sort((r,s)=>r.distance_km-s.distance_km),this.admins=t;const o=t.length>0&&t[0].distance_km?`${t[0].distance_km} km`:"-";this.updateStatCards(t.length,o),e.innerHTML=t.map((r,s)=>`
            <button 
                onclick="window.adminTracker.selectAdmin(${r.id})"
                class="admin-item group w-full text-left bg-white rounded-xl border-2 border-gray-200 hover:border-wash hover:shadow-lg transition-all"
                data-admin-id="${r.id}"
            >
                <div class="flex items-center gap-3 p-3">
                    <!-- Branch Number Badge -->
                    <div class="w-10 h-10 bg-gradient-to-br from-wash to-wash-dark rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md flex-shrink-0">
                        ${s+1}
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
        `).join("")}async selectAdmin(t){const e=this.admins.find(o=>o.id===t);if(!e||!this.userLocation){console.error("Admin or user location not found",{adminId:t,admin:e,userLocation:this.userLocation});return}this.map.eachLayer(o=>{(o instanceof L.Polyline||o instanceof L.GeoJSON)&&(o instanceof L.TileLayer||this.markers.includes(o)||this.map.removeLayer(o))}),this.routeLayer&&(this.map.removeLayer(this.routeLayer),this.routeLayer=null);try{console.log("Fetching route for admin:",e.name);const o=await this.fetchRoute(this.userLocation.latitude,this.userLocation.longitude,e.latitude,e.longitude);if(o&&o.features&&o.features.length>0&&o.features[0].geometry){console.log("Drawing route from API data",o.features[0].geometry);try{this.routeLayer=L.geoJSON(o.features[0].geometry,{style:{color:"#3B82F6",weight:6,opacity:.9,lineJoin:"round",lineCap:"round"}}).addTo(this.map),setTimeout(()=>{this.map.fitBounds(this.routeLayer.getBounds(),{padding:[50,50],maxZoom:14})},100),console.log("Route drawn successfully")}catch(r){console.error("Error drawing route:",r),this.drawStraightLine(e)}}else console.warn("Route data invalid or empty, falling back to straight line",o),this.drawStraightLine(e)}catch(o){console.error("Error fetching route:",o),this.drawStraightLine(e)}this.updateRouteInfo(e),this.highlightSelectedAdmin(t)}updateRouteInfo(t){const e=document.getElementById("route-info");if(!e)return;e.classList.remove("hidden"),document.getElementById("distance").textContent=t.distance_km?`${t.distance_km} km`:"-",document.getElementById("travel-time").textContent=t.eta_minutes?`${t.eta_minutes} min`:"-",document.getElementById("eta").textContent=t.eta||"-",document.getElementById("shop-name").textContent=t.branch_name||"-";const o=document.getElementById("google-maps-link");if(o&&this.userLocation){const r=`https://www.google.com/maps/dir/?api=1&origin=${this.userLocation.latitude},${this.userLocation.longitude}&destination=${t.latitude},${t.longitude}`;o.href=r}}async fetchRoute(t,e,o,r){if(!this.apiKey)return console.error("Geoapify API key is missing"),null;try{const s=`https://api.geoapify.com/v1/routing?waypoints=${t},${e}|${o},${r}&mode=drive&apiKey=${this.apiKey}`;console.log("Fetching route from Geoapify API..."),console.log("Start:",t,e),console.log("End:",o,r);const a=await fetch(s);if(!a.ok){const l=await a.text();throw console.error("Routing API error:",a.status,l),new Error(`Routing API request failed: ${a.status} - ${l}`)}const i=await a.json();return console.log("Route data received successfully:",i),!i.features||i.features.length===0?(console.error("No route features in response:",i),null):i.features[0].geometry?(console.log("Route geometry type:",i.features[0].geometry.type),console.log("Route coordinates count:",i.features[0].geometry.coordinates?.length),i):(console.error("No geometry in route feature:",i.features[0]),null)}catch(s){return console.error("Error fetching route from Geoapify:",s),null}}drawStraightLine(t){console.warn("Drawing straight line fallback (routing API failed or unavailable)"),this.routeLayer&&(this.map.removeLayer(this.routeLayer),this.routeLayer=null),this.routeLayer=L.polyline([[this.userLocation.latitude,this.userLocation.longitude],[t.latitude,t.longitude]],{color:"#EF4444",weight:4,opacity:.7,dashArray:"10, 10"}).addTo(this.map);const e=L.latLngBounds([[this.userLocation.latitude,this.userLocation.longitude],[t.latitude,t.longitude]]);this.map.fitBounds(e,{padding:[80,80]})}highlightSelectedAdmin(t){document.querySelectorAll(".admin-item").forEach(o=>{o.classList.remove("border-wash","bg-wash/5","shadow-lg"),o.classList.add("border-gray-200")});const e=document.querySelector(`[data-admin-id="${t}"]`);e&&(e.classList.remove("border-gray-200"),e.classList.add("border-wash","bg-wash/5","shadow-lg"),e.scrollIntoView({behavior:"smooth",block:"nearest"}))}focusAdmin(t,e){this.map&&this.map.setView([t,e],16)}updateStatCards(t,e){const o=document.getElementById("stat-branches");o&&(o.textContent=t);const r=document.getElementById("stat-nearest");r&&(r.textContent=e)}showError(t){const e=document.getElementById("admin-list");e&&(e.innerHTML=`<p class="text-red-500">${t}</p>`)}startAutoRefresh(){this.intervalId=setInterval(()=>{this.loadAdminLocations()},this.refreshInterval)}stopAutoRefresh(){this.intervalId&&(clearInterval(this.intervalId),this.intervalId=null)}destroy(){this.stopAutoRefresh(),this.map&&this.map.remove()}}document.addEventListener("DOMContentLoaded",()=>{const n=window.geoapifyApiKey||"",t=window.routes?.adminLocation||"/user/api/admins",e=new d({mapId:"map",apiKey:n,fetchUrl:t,refreshInterval:3e4});window.adminTracker=e});
