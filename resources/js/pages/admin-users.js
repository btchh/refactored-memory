/**
 * Admin User Management
 */

window.toggleUserStatus = async function(userId, currentStatus) {
    const action = currentStatus === 'active' ? 'disable' : 'enable';
    
    if (!confirm(`Are you sure you want to ${action} this user?`)) {
        return;
    }

    try {
        const response = await fetch(window.userRoutes.toggleStatus.replace('__ID__', userId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.userRoutes.csrf,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            
            // Update badge
            const badge = document.getElementById(`status-badge-${userId}`);
            if (badge) {
                badge.className = `badge ${data.status === 'active' ? 'badge-success' : 'badge-error'}`;
                badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            }
            
            // Update button icon
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('error', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Failed to update user status');
    }
};

window.deleteUser = async function(userId, userName) {
    if (!confirm(`Are you sure you want to delete "${userName}"?\n\nThis action cannot be undone.`)) {
        return;
    }

    try {
        const response = await fetch(window.userRoutes.delete.replace('__ID__', userId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.userRoutes.csrf,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            
            // Remove row
            const row = document.getElementById(`user-row-${userId}`);
            if (row) {
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
        } else {
            showAlert('error', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Failed to delete user');
    }
};

function showAlert(type, message) {
    const container = document.getElementById('alert-container');
    if (!container) return;

    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const iconPath = type === 'success' 
        ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';

    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} mb-4`;
    alert.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}" />
        </svg>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;

    container.appendChild(alert);

    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}
