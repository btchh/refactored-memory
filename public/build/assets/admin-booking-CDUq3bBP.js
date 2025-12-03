class F{constructor(e,t={}){this.container=document.getElementById(e),this.currentDate=new Date,this.onDateSelect=t.onDateSelect||(()=>{}),this.selectedDate=null,this.bookingCounts={},this.countsUrl=t.countsUrl||null}async render(){if(!this.container)return;const e=this.currentDate.getFullYear(),t=this.currentDate.getMonth();this.countsUrl&&await this.loadBookingCounts(e,t+1);const s=document.getElementById("current-month");s&&(s.textContent=new Date(e,t).toLocaleDateString("en-US",{month:"long",year:"numeric"}));const o=new Date(e,t,1).getDay(),a=new Date(e,t+1,0).getDate(),g=new Date;g.setHours(0,0,0,0);const m=document.getElementById("calendar-grid");if(m){m.innerHTML="",["Sun","Mon","Tue","Wed","Thu","Fri","Sat"].forEach(d=>{const f=document.createElement("div");f.className="text-center font-bold text-sm text-gray-500 py-2",f.textContent=d,m.appendChild(f)});for(let d=0;d<o;d++)m.appendChild(document.createElement("div"));for(let d=1;d<=a;d++){const f=new Date(e,t,d);f.setHours(0,0,0,0);const k=`${e}-${String(t+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`,B=f<g,w=this.bookingCounts[k]||0,h=document.createElement("div");h.className=`calendar-day font-semibold relative ${B?"disabled":"available"}`,w>0?h.innerHTML=`
                    <span>${d}</span>
                    <span class="absolute top-1 right-1 bg-primary-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">${w}</span>
                `:h.textContent=d,h.dataset.date=k,h.dataset.day=d,h.dataset.count=w,B||(h.onclick=()=>this.selectDate(k,h,d)),m.appendChild(h)}}}async loadBookingCounts(e,t){if(this.countsUrl)try{const o=await(await fetch(`${this.countsUrl}?year=${e}&month=${t}`)).json();o.success&&(this.bookingCounts=o.counts)}catch(s){console.error("Failed to load booking counts:",s)}}selectDate(e,t,s){this.selectedDate=e,console.log("Date selected:",{dateStr:e,day:s,elementText:t.textContent});const o=document.getElementById("calendar-grid");o&&o.querySelectorAll(".calendar-day").forEach(a=>{a.classList.remove("selected")}),t.classList.add("selected"),this.onDateSelect(e)}navigateMonth(e){const t=new Date(this.currentDate);t.setMonth(t.getMonth()+e);const s=new Date;s.setDate(1),s.setHours(0,0,0,0),(t>=s||e>0)&&(this.currentDate=t,this.render())}setupNavigation(){const e=document.getElementById("prev-month"),t=document.getElementById("next-month");e&&e.addEventListener("click",()=>{this.navigateMonth(-1),this.updateNavigationButtons()}),t&&t.addEventListener("click",()=>{this.navigateMonth(1),this.updateNavigationButtons()}),this.updateNavigationButtons()}updateNavigationButtons(){const e=document.getElementById("prev-month"),t=new Date;t.setDate(1),t.setHours(0,0,0,0);const s=new Date(this.currentDate);s.setDate(1),s.setHours(0,0,0,0),e&&(s<=t?(e.disabled=!0,e.classList.add("btn-disabled","opacity-50","cursor-not-allowed")):(e.disabled=!1,e.classList.remove("btn-disabled","opacity-50","cursor-not-allowed")))}reset(){this.selectedDate=null;const e=document.getElementById("calendar-grid");e&&e.querySelectorAll("div").forEach(t=>{t.classList.remove("bg-blue-500","text-white","ring-2","ring-blue-400")})}init(){this.render(),this.setupNavigation()}}class P{constructor(e={}){this.servicesData=e.servicesData||{},this.productsData=e.productsData||{},this.selectedServices=[],this.selectedProducts=[],this.onTotalChange=e.onTotalChange||(()=>{})}loadServices(e){const t=document.getElementById("services-container");if(!t)return;const s=this.servicesData[e]||[];if(s.length===0){t.innerHTML='<p class="text-gray-500 col-span-2">No services available</p>';return}t.innerHTML=s.map(o=>`
            <label class="group flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:border-blue-400 hover:shadow-md transition-all duration-300 transform hover:scale-105">
                <input type="checkbox" name="services[]" value="${o.id}" 
                    class="checkbox checkbox-primary checkbox-lg" data-price="${o.price}" data-name="${o.service_name}">
                <div class="flex-1">
                    <span class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">${o.service_name}</span>
                    <span class="block text-xs font-bold text-green-600 mt-1">₱${o.price}</span>
                </div>
            </label>
        `).join(""),t.querySelectorAll('input[type="checkbox"]').forEach(o=>{o.addEventListener("change",a=>{this.toggleService(parseInt(a.target.value),parseFloat(a.target.dataset.price),a.target.dataset.name,a.target.checked)})})}loadProducts(e){const t=document.getElementById("products-container");if(!t)return;const s=this.productsData[e]||[];if(s.length===0){t.innerHTML='<p class="text-gray-400 col-span-2 text-sm text-center py-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">No products available</p>';return}t.innerHTML=s.map(o=>`
            <label class="group flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 hover:border-purple-400 hover:shadow-md transition-all duration-300 transform hover:scale-105">
                <input type="checkbox" name="products[]" value="${o.id}" 
                    class="checkbox checkbox-secondary checkbox-lg" data-price="${o.price}" data-name="${o.product_name}">
                <div class="flex-1">
                    <span class="text-sm font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">${o.product_name}</span>
                    <span class="block text-xs font-bold text-green-600 mt-1">₱${o.price}</span>
                </div>
            </label>
        `).join(""),t.querySelectorAll('input[type="checkbox"]').forEach(o=>{o.addEventListener("change",a=>{this.toggleProduct(parseInt(a.target.value),parseFloat(a.target.dataset.price),a.target.dataset.name,a.target.checked)})})}toggleService(e,t,s,o){o?this.selectedServices.push({id:e,price:t,name:s}):this.selectedServices=this.selectedServices.filter(a=>a.id!==e),this.calculateTotal()}toggleProduct(e,t,s,o){o?this.selectedProducts.push({id:e,price:t,name:s}):this.selectedProducts=this.selectedProducts.filter(a=>a.id!==e),this.calculateTotal()}calculateTotal(){const e=this.selectedServices.reduce((s,o)=>s+o.price,0)+this.selectedProducts.reduce((s,o)=>s+o.price,0),t=document.getElementById("total-price");return t&&(t.textContent=e.toFixed(2)),this.onTotalChange(e),e}reset(){this.selectedServices=[],this.selectedProducts=[],this.calculateTotal(),document.querySelectorAll('input[name="services[]"], input[name="products[]"]').forEach(e=>{e.checked=!1})}setupItemTypeListener(){const e=document.getElementById("item_type");e&&e.tagName==="SELECT"&&e.addEventListener("change",s=>{this.handleItemTypeChange(s.target.value)});const t=document.querySelectorAll('input[name="item_type"]');t.length>0&&t.forEach(s=>{s.addEventListener("change",o=>{this.handleItemTypeChange(o.target.value);const a=document.getElementById("item_type");a&&a.tagName==="SELECT"&&(a.value=o.target.value)})})}handleItemTypeChange(e){if(this.selectedServices=[],this.selectedProducts=[],!e){document.getElementById("services-container").innerHTML='<p class="text-gray-400 col-span-2 text-sm text-center py-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">Select an item type first</p>',document.getElementById("products-container").innerHTML='<p class="text-gray-400 col-span-2 text-sm text-center py-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">Select an item type first</p>';return}this.loadServices(e),this.loadProducts(e),this.calculateTotal()}init(){this.setupItemTypeListener(),this.calculateTotal()}}class A{constructor(e={}){this.searchInput=document.getElementById(e.inputId||"user-search"),this.resultsContainer=document.getElementById(e.resultsId||"user-search-results"),this.searchUrl=e.searchUrl||"/admin/api/users/search",this.onUserSelect=e.onUserSelect||(()=>{}),this.searchTimeout=null,this.selectedUser=null}async search(e){if(e.length<2){this.hideResults();return}try{const s=await(await fetch(`${this.searchUrl}?q=${encodeURIComponent(e)}`)).json();this.displayResults(s)}catch(t){console.error("Error searching users:",t),this.displayResults([])}}displayResults(e){if(this.resultsContainer){if(e.length===0){this.resultsContainer.innerHTML='<div class="p-3 text-gray-500">No users found</div>',this.resultsContainer.classList.remove("hidden");return}this.resultsContainer.innerHTML=e.map(t=>`
            <div class="p-3 hover:bg-gray-100 cursor-pointer border-b last:border-b-0" data-user='${JSON.stringify(t)}'>
                <p class="font-semibold">${t.name}</p>
                <p class="text-sm text-gray-600">${t.email}</p>
            </div>
        `).join(""),this.resultsContainer.querySelectorAll("[data-user]").forEach(t=>{t.addEventListener("click",()=>{const s=JSON.parse(t.dataset.user);this.selectUser(s)})}),this.resultsContainer.classList.remove("hidden")}}selectUser(e){this.selectedUser=e,this.searchInput&&(this.searchInput.value=""),this.hideResults();const t=document.getElementById("user_id");t&&(t.value=e.id);const s={"selected-user-name":e.name,"selected-user-email":e.email,"selected-user-phone":e.phone,"selected-user-address":e.address,pickup_address:e.address,latitude:e.latitude||"",longitude:e.longitude||""};Object.entries(s).forEach(([a,g])=>{const m=document.getElementById(a);m&&(m.tagName==="INPUT"?m.value=g:m.textContent=g)});const o=document.getElementById("selected-user-info");o&&o.classList.remove("hidden"),this.onUserSelect(e)}clearSelection(){this.selectedUser=null;const e=document.getElementById("user_id");e&&(e.value="");const t=document.getElementById("selected-user-info");t&&t.classList.add("hidden")}hideResults(){this.resultsContainer&&this.resultsContainer.classList.add("hidden")}setupListeners(){if(!this.searchInput)return;this.searchInput.addEventListener("input",t=>{clearTimeout(this.searchTimeout);const s=t.target.value;this.searchTimeout=setTimeout(()=>{this.search(s)},300)});const e=document.getElementById("clear-user");e&&e.addEventListener("click",()=>this.clearSelection()),document.addEventListener("click",t=>{!t.target.closest("#user-search")&&!t.target.closest("#user-search-results")&&this.hideResults()})}init(){this.setupListeners()}}class N{constructor(){this.csrfToken=document.querySelector('meta[name="csrf-token"]')?.content||""}async get(e){try{const t=await fetch(e,{method:"GET",headers:{Accept:"application/json","X-CSRF-TOKEN":this.csrfToken}});if(!t.ok)throw new Error(`HTTP error! status: ${t.status}`);return await t.json()}catch(t){throw console.error("API GET error:",t),t}}async post(e,t){try{const s=await fetch(e,{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json","X-CSRF-TOKEN":this.csrfToken},body:JSON.stringify(t)});if(!s.ok)throw new Error(`HTTP error! status: ${s.status}`);return await s.json()}catch(s){throw console.error("API POST error:",s),s}}async put(e,t){try{const s=await fetch(e,{method:"PUT",headers:{"Content-Type":"application/json",Accept:"application/json","X-CSRF-TOKEN":this.csrfToken},body:JSON.stringify(t)});if(!s.ok)throw new Error(`HTTP error! status: ${s.status}`);return await s.json()}catch(s){throw console.error("API PUT error:",s),s}}async delete(e,t={}){try{const s=await fetch(e,{method:"DELETE",headers:{"Content-Type":"application/json",Accept:"application/json","X-CSRF-TOKEN":this.csrfToken},body:JSON.stringify(t)});if(!s.ok)throw new Error(`HTTP error! status: ${s.status}`);return await s.json()}catch(s){throw console.error("API DELETE error:",s),s}}async patch(e,t){try{const s=await fetch(e,{method:"PATCH",headers:{"Content-Type":"application/json",Accept:"application/json","X-CSRF-TOKEN":this.csrfToken},body:JSON.stringify(t)});if(!s.ok)throw new Error(`HTTP error! status: ${s.status}`);return await s.json()}catch(s){throw console.error("API PATCH error:",s),s}}}const E=new N;class R{constructor(e={}){this.userId=e.userId||null,this.onBookingUpdate=e.onBookingUpdate||(()=>{})}async loadUserBookings(e){this.userId=e;try{const t=await E.get(`/admin/api/bookings/user/${e}`);t.success&&t.bookings.length>0?this.displayBookings(t.bookings):this.displayEmptyState()}catch(t){console.error("Error loading bookings:",t),this.displayEmptyState()}}displayBookings(e){const t=document.getElementById("user-bookings-list"),s=document.getElementById("user-bookings-section");!t||!s||(t.innerHTML=e.map(o=>`
            <div class="border border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition-shadow ${o.status==="cancelled"?"opacity-60":""}">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-2">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">${o.datetime}</p>
                        <p class="text-sm text-gray-600 mt-1">${o.item_type} - ₱${o.total}</p>
                        <p class="text-sm text-gray-600">${o.services}</p>
                        ${o.products?`<p class="text-sm text-gray-600">${o.products}</p>`:""}
                    </div>
                    <span class="badge ${this.getStatusBadgeClass(o.status)} self-start">${o.status.replace("_"," ")}</span>
                </div>
                ${o.is_upcoming&&o.status!=="cancelled"?`
                    <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                        <button onclick="window.bookingManager.openRescheduleModal(${o.id})" class="btn btn-sm btn-outline flex-1 sm:flex-none">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Reschedule
                        </button>
                        <button onclick="window.bookingManager.cancelBooking(${o.id})" class="btn btn-sm btn-outline text-error border-error hover:bg-error hover:text-white flex-1 sm:flex-none">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </button>
                        <select onchange="window.bookingManager.changeStatus(${o.id}, this.value)" class="select select-sm select-bordered flex-1 sm:flex-none">
                            <option value="">Change Status</option>
                            <option value="pending" ${o.status==="pending"?"selected":""}>Pending</option>
                            <option value="in_progress" ${o.status==="in_progress"?"selected":""}>In Progress</option>
                            <option value="completed" ${o.status==="completed"?"selected":""}>Completed</option>
                        </select>
                    </div>
                `:""}
            </div>
        `).join(""),s.classList.remove("hidden"))}displayEmptyState(){const e=document.getElementById("user-bookings-list"),t=document.getElementById("user-bookings-section");e&&(e.innerHTML='<p class="text-gray-500 text-center py-4">No bookings found</p>'),t&&t.classList.add("hidden")}getStatusBadgeClass(e){return{pending:"badge-warning",in_progress:"badge-info",completed:"badge-success",cancelled:"badge-error"}[e]||"badge-neutral"}async cancelBooking(e){if(!confirm("Are you sure you want to cancel this booking?"))return;const t=prompt("Cancellation reason (optional):");try{(await E.delete(`/admin/bookings/${e}`,{reason:t})).success&&(window.Toast?.success("Booking cancelled successfully"),this.loadUserBookings(this.userId),this.onBookingUpdate())}catch(s){console.error("Error cancelling booking:",s),window.Toast?.error("Failed to cancel booking")}}async changeStatus(e,t){if(t)try{(await E.patch(`/admin/bookings/${e}/status`,{status:t})).success&&(window.Toast?.success("Status updated successfully"),this.loadUserBookings(this.userId),this.onBookingUpdate())}catch(s){console.error("Error updating status:",s),window.Toast?.error("Failed to update status")}}openRescheduleModal(e){const t=document.getElementById("reschedule-modal"),s=document.getElementById("reschedule-booking-id"),o=document.getElementById("reschedule-date"),a=document.getElementById("reschedule-time");s&&(s.value=e),o&&(o.value=""),a&&(a.innerHTML='<option value="">Select a date first</option>'),t&&t.showModal&&t.showModal()}async submitReschedule(e,t,s){try{if((await E.post(`/admin/bookings/${e}/reschedule`,{booking_date:t,booking_time:s})).success){window.Toast?.success("Booking rescheduled successfully");const a=document.getElementById("reschedule-modal");a&&a.close&&a.close(),this.loadUserBookings(this.userId),this.onBookingUpdate()}}catch(o){console.error("Error rescheduling:",o),window.Toast?.error("Failed to reschedule booking")}}setupRescheduleForm(){const e=document.getElementById("reschedule-form");if(!e)return;const t=document.getElementById("reschedule-date");t&&t.addEventListener("change",async s=>{await this.loadTimeSlots(s.target.value)}),e.addEventListener("submit",async s=>{s.preventDefault();const o=document.getElementById("reschedule-booking-id")?.value,a=document.getElementById("reschedule-date")?.value,g=document.getElementById("reschedule-time")?.value;o&&a&&g&&await this.submitReschedule(o,a,g)})}async loadTimeSlots(e){const t=document.getElementById("reschedule-time");if(t){if(!e){t.innerHTML='<option value="">Select a date first</option>';return}t.innerHTML='<option value="">Loading...</option>';try{const s=window.bookingData?.routes?.slots||"/admin/api/calendar/slots",o=await E.get(`${s}?date=${e}`);o.success&&o.slots&&o.slots.length>0?t.innerHTML='<option value="">Select time slot</option>'+o.slots.map(a=>`<option value="${a.time}">${a.display}</option>`).join(""):t.innerHTML='<option value="">No available slots</option>'}catch(s){console.error("Error loading time slots:",s),t.innerHTML='<option value="">Error loading slots</option>'}}}init(){this.setupRescheduleForm(),window.bookingManager=this}}class q{constructor(e={}){this.selectElement=document.getElementById(e.selectId||"booking_time"),this.slotsUrl=e.slotsUrl||"/api/calendar/slots"}async loadSlots(e){if(this.selectElement){this.selectElement.innerHTML='<option value="">Loading slots...</option>',this.selectElement.disabled=!0;try{const t=await E.get(`${this.slotsUrl}?date=${e}`);if(this.selectElement.innerHTML='<option value="">Select time slot</option>',t.slots&&t.slots.length>0){const s=t.slots.filter(o=>o.available);s.length===0?this.selectElement.innerHTML='<option value="">No slots available</option>':s.forEach(o=>{const a=document.createElement("option");a.value=o.time,a.textContent=o.formatted||o.time,this.selectElement.appendChild(a)})}else this.selectElement.innerHTML='<option value="">No slots available</option>'}catch(t){console.error("Error loading time slots:",t),this.selectElement.innerHTML='<option value="">Error loading slots. Please try again.</option>'}finally{this.selectElement.disabled=!1}}}clear(){this.selectElement&&(this.selectElement.innerHTML='<option value="">Select time slot</option>')}}document.addEventListener("DOMContentLoaded",()=>{const y=window.bookingData;if(!y){console.error("Booking data not found");return}const{services:e,products:t,routes:s,csrf:o}=y,a=new q({selectId:"booking_time",slotsUrl:s.slots}),g=new R({bookingsUrl:s.bookings,userBookingsUrl:s.userBookings,csrf:o,onBookingUpdate:()=>{}});g.init();const m=new F("calendar-container",{countsUrl:s.bookingCounts,onDateSelect:async n=>{const r=document.getElementById("booking_date");r&&(r.value=n),await d(n),a.loadSlots(n)}});m.init();async function d(n){const r=document.getElementById("date-bookings-section"),c=document.getElementById("date-bookings-list"),i=document.getElementById("selected-date-display"),l=document.getElementById("booking-form"),u=document.getElementById("empty-state"),v=document.getElementById("bookings-count-badge");c&&(c.innerHTML=`
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                    <span class="ml-3 text-gray-600">Loading bookings...</span>
                </div>
            `);try{const b=await(await fetch(`${s.bookingsByDate}?date=${n}`)).json();if(!b.success||!r||!c)return;if(u&&(u.style.opacity="0",setTimeout(()=>u.classList.add("hidden"),300)),i){const p=new Date(n+"T00:00:00");i.textContent=p.toLocaleDateString("en-US",{weekday:"long",year:"numeric",month:"long",day:"numeric"})}v&&(v.textContent=b.bookings.length),r.classList.remove("hidden"),setTimeout(()=>{r.style.opacity="1"},10),l&&l.classList.add("hidden"),b.bookings.length===0?c.innerHTML=`
                    <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">No Bookings Yet</h4>
                        <p class="text-gray-600 mb-4">This date is available for new bookings</p>
                        <button onclick="showBookingForm()" class="btn btn-primary btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create First Booking
                        </button>
                    </div>
                `:c.innerHTML=b.bookings.map((p,H)=>`
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-primary-300 transition-all duration-200 bg-white animate-slide-in" style="animation-delay: ${H*50}ms">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex items-center gap-2 bg-gray-100 px-3 py-1 rounded-full">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span class="font-bold text-gray-900 text-sm">#${p.id}</span>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full ${f(p.status)} shadow-sm">
                                        ${p.status.replace("_"," ").toUpperCase()}
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">${p.user.name}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">${p.time}</span>
                                        <span class="text-gray-400">•</span>
                                        <span class="text-sm text-gray-700 capitalize">${p.item_type}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <span class="text-sm text-gray-600">${p.services||"No services"}</span>
                                    </div>
                                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                                        <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        <span class="text-base font-bold text-success">₱${parseFloat(p.total).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button onclick="viewBooking(${p.id})" class="btn btn-sm btn-info">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </button>
                                ${p.is_upcoming?`
                                    <button onclick="rescheduleBooking(${p.id})" class="btn btn-sm btn-primary">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Reschedule
                                    </button>
                                    <button onclick="cancelBooking(${p.id})" class="btn btn-sm btn-error">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cancel
                                    </button>
                                `:""}
                            </div>
                        </div>
                    </div>
                `).join("")}catch(T){console.error("Failed to load date bookings:",T)}}function f(n){return{pending:"bg-yellow-100 text-yellow-700",in_progress:"bg-blue-100 text-blue-700",completed:"bg-green-100 text-green-700",cancelled:"bg-red-100 text-red-700"}[n]||"bg-gray-100 text-gray-700"}function k(){const n=document.getElementById("booking-form"),r=document.getElementById("date-bookings-section");n&&(n.classList.remove("hidden"),setTimeout(()=>{n.scrollIntoView({behavior:"smooth",block:"start"})},100)),r&&(r.style.opacity="0",setTimeout(()=>{r.classList.add("hidden")},300))}window.showBookingForm=k;const B=document.getElementById("show-add-form-btn");B&&B.addEventListener("click",k),window.viewBooking=async n=>{try{const c=await(await fetch(s.bookingDetails.replace("__ID__",n))).json();if(c.success){const i=c.booking,l=document.getElementById("view-booking-content");l.innerHTML=`
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Booking ID</p>
                            <p class="text-base font-semibold text-gray-900">#${i.id}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Status</p>
                            <p class="text-base font-semibold text-gray-900">${i.status}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600">Customer</p>
                            <p class="text-base font-semibold text-gray-900">${i.customer.name}</p>
                            <p class="text-sm text-gray-500">${i.customer.email}</p>
                            <p class="text-sm text-gray-500">${i.customer.phone}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Date</p>
                            <p class="text-base font-semibold text-gray-900">${i.booking_date}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Time</p>
                            <p class="text-base font-semibold text-gray-900">${i.booking_time}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600">Pickup Address</p>
                            <p class="text-base text-gray-900">${i.pickup_address}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Item Type</p>
                            <p class="text-base font-semibold text-gray-900">${i.item_type}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Weight</p>
                            <p class="text-base font-semibold text-gray-900">${i.weight}</p>
                        </div>
                        ${i.services.length>0?`
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600 mb-2">Services</p>
                            <ul class="space-y-1">
                                ${i.services.map(u=>`<li class="text-sm text-gray-900">• ${u.name} - ${u.price}</li>`).join("")}
                            </ul>
                        </div>
                        `:""}
                        ${i.products.length>0?`
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600 mb-2">Products</p>
                            <ul class="space-y-1">
                                ${i.products.map(u=>`<li class="text-sm text-gray-900">• ${u.name} - ${u.price}</li>`).join("")}
                            </ul>
                        </div>
                        `:""}
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600">Notes</p>
                            <p class="text-base text-gray-900">${i.notes}</p>
                        </div>
                        <div class="col-span-2 pt-4 border-t">
                            <p class="text-sm font-medium text-gray-600">Total Price</p>
                            <p class="text-2xl font-bold text-success">${i.total_price}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500">Created: ${i.created_at}</p>
                        </div>
                        <div class="col-span-2 pt-4 border-t">
                            <button onclick="editWeightFromView(${i.id}, '${i.weight}')" class="btn btn-warning w-full">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                                Update Weight
                            </button>
                        </div>
                    </div>
                `,document.getElementById("view-booking-modal").showModal()}else window.Toast?.error("Failed to load booking details")}catch(r){console.error("Error:",r),window.Toast?.error("Failed to load booking details")}},window.rescheduleBooking=n=>{g.openRescheduleModal(n)},window.editWeightFromView=(n,r)=>{document.getElementById("view-booking-modal").close(),document.getElementById("edit-weight-booking-id").value=n,document.getElementById("edit-booking-weight").value=r.replace(" kg","")||"",document.getElementById("edit-weight-modal").showModal()};const w=document.getElementById("edit-weight-form");w&&w.addEventListener("submit",async n=>{n.preventDefault();const r=document.getElementById("edit-weight-booking-id").value,c=document.getElementById("edit-booking-weight").value;try{const l=await(await fetch(s.updateWeight.replace("__ID__",r),{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":bookingData.csrf},body:JSON.stringify({weight:c})})).json();l.success?(window.Toast?.success(l.message),document.getElementById("edit-weight-modal").close(),selectedDate&&loadBookingsByDate(selectedDate)):window.Toast?.error(l.message||"Failed to update weight")}catch(i){console.error("Error:",i),window.Toast?.error("Failed to update weight")}}),window.cancelBooking=async n=>{if(confirm("Are you sure you want to cancel this booking?"))try{const c=await(await fetch(`${s.bookings}/${n}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":o,"Content-Type":"application/json"}})).json();if(c.success){window.Toast?.success("Booking cancelled successfully");const i=document.getElementById("booking_date");i&&i.value&&await d(i.value),m.render()}else window.Toast?.error("Failed to cancel booking: "+c.message)}catch(r){console.error("Failed to cancel booking:",r),window.Toast?.error("Failed to cancel booking")}};const h=new P({servicesData:e,productsData:t});h.init(),new A({inputId:"user-search",resultsId:"user-search-results",searchUrl:s.userSearch,onUserSelect:n=>{const r=document.getElementById("user_id"),c=document.getElementById("pickup_address"),i=document.getElementById("latitude"),l=document.getElementById("longitude");r&&(r.value=n.id),c&&(c.value=n.address),i&&(i.value=n.latitude||""),l&&(l.value=n.longitude||"");const u=document.getElementById("selected-user-info");u&&(document.getElementById("selected-user-name").textContent=n.name,document.getElementById("selected-user-email").textContent=n.email,document.getElementById("selected-user-phone").textContent=n.phone,document.getElementById("selected-user-address").textContent=n.address,u.classList.remove("hidden")),g.loadUserBookings(n.id);const v=document.getElementById("bookings-user-name");v&&(v.textContent=n.name)}}).init();const M=document.getElementById("clear-user");M&&M.addEventListener("click",()=>{const n=document.getElementById("user_id"),r=document.getElementById("selected-user-info"),c=document.getElementById("user-bookings-section");n&&(n.value=""),r&&r.classList.add("hidden"),c&&c.classList.add("hidden")});const U=document.querySelectorAll('input[name="service_type"]'),I=document.getElementById("pickup-address-section"),x=document.getElementById("pickup_address");function L(){const n=document.querySelector('input[name="service_type"]:checked')?.value;I&&(n==="dropoff"?(I.classList.add("hidden"),x&&x.removeAttribute("required")):(I.classList.remove("hidden"),x&&x.setAttribute("required","required")))}U.forEach(n=>{n.addEventListener("change",L)}),L();const j=document.querySelectorAll('input[name="booking_type"]'),$=document.getElementById("customer-selection-section"),S=document.getElementById("user_id");function D(){const n=document.querySelector('input[name="booking_type"]:checked')?.value;if($&&S)if(n==="walkin"){$.classList.add("hidden"),S.removeAttribute("required"),S.value="",I&&(I.classList.add("hidden"),x&&x.removeAttribute("required"));const r=document.querySelector('input[name="service_type"][value="dropoff"]');r&&(r.checked=!0,r.closest(".bg-white").classList.add("hidden"))}else{$.classList.remove("hidden"),S.setAttribute("required","required");const r=document.querySelector('input[name="service_type"]')?.closest(".bg-white");r&&r.classList.remove("hidden"),L()}}j.forEach(n=>{n.addEventListener("change",D)}),D();const _=document.getElementById("clear-form");_&&_.addEventListener("click",()=>{const n=document.getElementById("booking-form");n&&n.reset(),h.reset();const r=document.getElementById("booking_date"),c=r?r.value:null;r&&(r.value=""),a.clear();const i=document.getElementById("booking-form"),l=document.getElementById("date-bookings-section"),u=document.getElementById("empty-state"),v=document.getElementById("user-bookings-section");i&&i.classList.add("hidden"),c&&l?(l.classList.remove("hidden"),l.style.opacity="1"):(l&&l.classList.add("hidden"),u&&(u.classList.remove("hidden"),u.style.opacity="1")),v&&v.classList.add("hidden");const T=document.getElementById("user_id"),b=document.getElementById("selected-user-info");T&&(T.value=""),b&&b.classList.add("hidden"),c||m.reset()})});let C=!1;window.handleBookingSubmit=function(y){if(C)return y.preventDefault(),!1;C=!0;const e=y.target.querySelector('button[type="submit"]');return e&&(e.disabled=!0,e.innerHTML='<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Submitting...'),setTimeout(()=>{C=!1,e&&(e.disabled=!1,e.innerHTML="Submit Booking")},3e3),!0};
