/**
 * Admin Pricing Management
 */

// Tab Switching
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.pricing-tab');
    const panels = document.querySelectorAll('.pricing-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetPanel = tab.dataset.tab;
            
            // Update tabs
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            // Update panels
            panels.forEach(panel => {
                if (panel.id === `${targetPanel}-panel`) {
                    panel.classList.remove('hidden');
                } else {
                    panel.classList.add('hidden');
                }
            });
        });
    });

    // Initialize forms
    initServiceForm();
    initProductForm();
});

// Service Functions
function initServiceForm() {
    const form = document.getElementById('service-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('service-id').value;
        const data = {
            service_name: document.getElementById('service-name').value,
            item_type: document.getElementById('service-item-type').value,
            price: document.getElementById('service-price').value,
            description: document.getElementById('service-description').value
        };

        const url = id 
            ? window.pricingRoutes.services.update.replace('__ID__', id)
            : window.pricingRoutes.services.store;
        
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.pricingRoutes.csrf
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showAlert('success', result.message);
                document.getElementById('service-modal').close();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert('error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'Failed to save service');
        }
    });
}

window.openServiceModal = (service = null) => {
    const modal = document.getElementById('service-modal');
    const title = document.getElementById('service-modal-title');
    const form = document.getElementById('service-form');
    
    if (service) {
        title.textContent = 'Edit Service';
        document.getElementById('service-id').value = service.id;
        document.getElementById('service-name').value = service.service_name;
        document.getElementById('service-item-type').value = service.item_type;
        document.getElementById('service-price').value = service.price;
        document.getElementById('service-description').value = service.description || '';
    } else {
        title.textContent = 'Add Service';
        form.reset();
        document.getElementById('service-id').value = '';
    }
    
    modal.showModal();
};

window.editService = (service) => {
    window.openServiceModal(service);
};

window.deleteService = async (id, name) => {
    if (!confirm(`Delete "${name}"?`)) return;

    try {
        const response = await fetch(window.pricingRoutes.services.delete.replace('__ID__', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.pricingRoutes.csrf,
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showAlert('success', result.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Failed to delete service');
    }
};

// Product Functions
function initProductForm() {
    const form = document.getElementById('product-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('product-id').value;
        const data = {
            product_name: document.getElementById('product-name').value,
            item_type: document.getElementById('product-item-type').value,
            price: document.getElementById('product-price').value,
            description: document.getElementById('product-description').value
        };

        const url = id 
            ? window.pricingRoutes.products.update.replace('__ID__', id)
            : window.pricingRoutes.products.store;
        
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.pricingRoutes.csrf
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showAlert('success', result.message);
                document.getElementById('product-modal').close();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert('error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'Failed to save product');
        }
    });
}

window.openProductModal = (product = null) => {
    const modal = document.getElementById('product-modal');
    const title = document.getElementById('product-modal-title');
    const form = document.getElementById('product-form');
    
    if (product) {
        title.textContent = 'Edit Product';
        document.getElementById('product-id').value = product.id;
        document.getElementById('product-name').value = product.product_name;
        document.getElementById('product-item-type').value = product.item_type;
        document.getElementById('product-price').value = product.price;
        document.getElementById('product-description').value = product.description || '';
    } else {
        title.textContent = 'Add Product';
        form.reset();
        document.getElementById('product-id').value = '';
    }
    
    modal.showModal();
};

window.editProduct = (product) => {
    window.openProductModal(product);
};

window.deleteProduct = async (id, name) => {
    if (!confirm(`Delete "${name}"?`)) return;

    try {
        const response = await fetch(window.pricingRoutes.products.delete.replace('__ID__', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.pricingRoutes.csrf,
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showAlert('success', result.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Failed to delete product');
    }
};

// Alert Helper
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
