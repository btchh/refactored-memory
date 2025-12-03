/**
 * Admin User Management
 */

let currentUserId = null;
let currentUserStatus = null;

// Toggle Status Modal Functions
window.openToggleStatusModal = function(userId, currentStatus) {
    currentUserId = userId;
    currentUserStatus = currentStatus;
    
    const row = document.getElementById(`user-row-${userId}`);
    const userName = row?.dataset.userName || 'this user';
    const action = currentStatus === 'active' ? 'disable' : 'enable';
    const actionTitle = currentStatus === 'active' ? 'Disable User' : 'Enable User';
    
    const modal = document.getElementById('toggle-status-modal');
    const iconContainer = document.getElementById('toggle-icon-container');
    const title = document.getElementById('toggle-modal-title');
    const message = document.getElementById('toggle-modal-message');
    const confirmBtn = document.getElementById('confirm-toggle-btn');
    
    // Set icon and colors based on action
    if (currentStatus === 'active') {
        iconContainer.className = 'w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center';
        iconContainer.innerHTML = `
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        `;
        confirmBtn.className = 'flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-xl transition-colors';
        confirmBtn.textContent = 'Disable User';
    } else {
        iconContainer.className = 'w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center';
        iconContainer.innerHTML = `
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        `;
        confirmBtn.className = 'flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors';
        confirmBtn.textContent = 'Enable User';
    }
    
    title.textContent = actionTitle;
    message.innerHTML = `Are you sure you want to ${action} <strong class="text-gray-900">${userName}</strong>? ${currentStatus === 'active' ? 'They will no longer be able to access their account.' : 'They will be able to access their account again.'}`;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
};

window.closeToggleStatusModal = function() {
    const modal = document.getElementById('toggle-status-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    currentUserId = null;
    currentUserStatus = null;
};

window.confirmToggleStatus = async function() {
    if (!currentUserId) return;
    
    const confirmBtn = document.getElementById('confirm-toggle-btn');
    const originalText = confirmBtn.textContent;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    try {
        const response = await fetch(window.userRoutes.toggleStatus.replace('__ID__', currentUserId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.userRoutes.csrf,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            closeToggleStatusModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('error', data.message);
            confirmBtn.disabled = false;
            confirmBtn.textContent = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Failed to update user status');
        confirmBtn.disabled = false;
        confirmBtn.textContent = originalText;
    }
};

// Delete User Modal Functions
window.openDeleteModal = function(userId) {
    currentUserId = userId;
    
    const row = document.getElementById(`user-row-${userId}`);
    const userName = row?.dataset.userName || 'this user';
    
    const modal = document.getElementById('delete-user-modal');
    const userNameSpan = document.getElementById('delete-user-name');
    
    userNameSpan.textContent = userName;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
};

window.closeDeleteModal = function() {
    const modal = document.getElementById('delete-user-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    currentUserId = null;
};

window.confirmDelete = async function() {
    if (!currentUserId) return;
    
    const confirmBtn = document.getElementById('confirm-delete-btn');
    const originalText = confirmBtn.textContent;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    try {
        const response = await fetch(window.userRoutes.delete.replace('__ID__', currentUserId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.userRoutes.csrf,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            closeDeleteModal();
            
            // Remove row with animation
            const row = document.getElementById(`user-row-${currentUserId}`);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => row.remove(), 300);
            }
        } else {
            showAlert('error', data.message);
            confirmBtn.disabled = false;
            confirmBtn.textContent = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Failed to delete user');
        confirmBtn.disabled = false;
        confirmBtn.textContent = originalText;
    }
};

// Restore User Modal Functions
window.openRestoreModal = function(userId) {
    currentUserId = userId;
    
    const row = document.getElementById(`user-row-${userId}`);
    const userName = row?.dataset.userName || 'this user';
    
    const modal = document.getElementById('restore-user-modal');
    const userNameSpan = document.getElementById('restore-user-name');
    
    userNameSpan.textContent = userName;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
};

window.closeRestoreModal = function() {
    const modal = document.getElementById('restore-user-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    currentUserId = null;
};

window.confirmRestore = async function() {
    if (!currentUserId) return;
    
    const confirmBtn = document.getElementById('confirm-restore-btn');
    const originalText = confirmBtn.textContent;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    try {
        const response = await fetch(window.userRoutes.restore.replace('__ID__', currentUserId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.userRoutes.csrf,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            closeRestoreModal();
            
            // Remove row with animation
            const row = document.getElementById(`user-row-${currentUserId}`);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => row.remove(), 300);
            }
        } else {
            showAlert('error', data.message);
            confirmBtn.disabled = false;
            confirmBtn.textContent = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Failed to restore user');
        confirmBtn.disabled = false;
        confirmBtn.textContent = originalText;
    }
};

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status buttons
    document.querySelectorAll('.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const status = this.dataset.userStatus;
            openToggleStatusModal(userId, status);
        });
    });
    
    // Delete buttons
    document.querySelectorAll('.delete-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            openDeleteModal(userId);
        });
    });
    
    // Restore buttons
    document.querySelectorAll('.restore-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            openRestoreModal(userId);
        });
    });
    
    // Confirm buttons
    document.getElementById('confirm-toggle-btn')?.addEventListener('click', confirmToggleStatus);
    document.getElementById('confirm-delete-btn')?.addEventListener('click', confirmDelete);
    document.getElementById('confirm-restore-btn')?.addEventListener('click', confirmRestore);
    
    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeToggleStatusModal();
            closeDeleteModal();
            closeRestoreModal();
        }
    });
});

function showAlert(type, message) {
    const container = document.getElementById('alert-container');
    if (!container) return;

    const alertClass = type === 'success' 
        ? 'bg-emerald-50 text-emerald-800 border-emerald-200' 
        : 'bg-rose-50 text-rose-800 border-rose-200';
    const iconPath = type === 'success' 
        ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';

    const alert = document.createElement('div');
    alert.className = `flex items-center gap-3 p-4 rounded-xl border ${alertClass} mb-4 shadow-sm transition-all`;
    alert.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}" />
        </svg>
        <span class="flex-1 font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="p-1 hover:bg-black/5 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;

    container.appendChild(alert);

    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}
