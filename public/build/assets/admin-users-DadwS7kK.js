window.toggleUserStatus=async function(s,a){if(confirm(`Are you sure you want to ${a==="active"?"disable":"enable"} this user?`))try{const e=await(await fetch(window.userRoutes.toggleStatus.replace("__ID__",s),{method:"POST",headers:{"X-CSRF-TOKEN":window.userRoutes.csrf,"Content-Type":"application/json"}})).json();if(e.success){n("success",e.message);const t=document.getElementById(`status-badge-${s}`);t&&(t.className=`badge ${e.status==="active"?"badge-success":"badge-error"}`,t.textContent=e.status.charAt(0).toUpperCase()+e.status.slice(1)),setTimeout(()=>window.location.reload(),1e3)}else n("error",e.message)}catch(o){console.error("Error:",o),n("error","Failed to update user status")}};window.deleteUser=async function(s,a){if(confirm(`Are you sure you want to delete "${a}"?

This action cannot be undone.`))try{const o=await(await fetch(window.userRoutes.delete.replace("__ID__",s),{method:"DELETE",headers:{"X-CSRF-TOKEN":window.userRoutes.csrf,"Content-Type":"application/json"}})).json();if(o.success){n("success",o.message);const e=document.getElementById(`user-row-${s}`);e&&(e.style.opacity="0",setTimeout(()=>e.remove(),300))}else n("error",o.message)}catch(r){console.error("Error:",r),n("error","Failed to delete user")}};function n(s,a){const r=document.getElementById("alert-container");if(!r)return;const o=s==="success"?"alert-success":"alert-error",e=s==="success"?"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z":"M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z",t=document.createElement("div");t.className=`alert ${o} mb-4`,t.innerHTML=`
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${e}" />
        </svg>
        <span>${a}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `,r.appendChild(t),setTimeout(()=>{t.style.opacity="0",setTimeout(()=>t.remove(),300)},5e3)}
