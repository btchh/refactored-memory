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
            tabs.forEach(t => {
                t.classList.remove('active', 'border-blue-600', 'text-blue-600', 'bg-white', '-mb-px');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            tab.classList.add('active', 'border-blue-600', 'text-blue-600', 'bg-white', '-mb-px');
            tab.classList.remove('border-transparent', 'text-gray-500');
            
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
        const itemType = document.querySelector('input[name="service_item_type"]:checked')?.value;
        
        if (!itemType) {
            showAlert('error', 'Please select an item type');
            return;
        }

        const data = {
            service_name: document.getElementById('service-name').value,
            item_type: itemType,
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
                closeServiceModal();
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

window.openServiceModal = () => {
    const modal = document.getElementById('service-modal');
    document.getElementById('service-modal-title').textContent = 'Add Service';
    document.getElementById('service-form').reset();
    document.getElementById('service-id').value = '';
    modal.classList.remove('hidden');
};

window.closeServiceModal = () => {
    document.getElementById('service-modal').classList.add('hidden');
};

window.editService = (service) => {
    const modal = document.getElementById('service-modal');
    document.getElementById('service-modal-title').textContent = 'Edit Service';
    document.getElementById('service-id').value = service.id;
    document.getElementById('service-name').value = service.service_name;
    document.getElementById('service-price').value = service.price;
    document.getElementById('service-description').value = service.description || '';
    
    // Set radio button
    const radio = document.querySelector(`input[name="service_item_type"][value="${service.item_type}"]`);
    if (radio) radio.checked = true;
    
    modal.classList.remove('hidden');
};

window.deleteService = async (id, name) => {
    if (!confirm(`Delete "${name}"? This action cannot be undone.`)) return;

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
        const itemType = document.querySelector('input[name="product_item_type"]:checked')?.value;
        
        if (!itemType) {
            showAlert('error', 'Please select an item type');
            return;
        }

        const data = {
            product_name: document.getElementById('product-name').value,
            item_type: itemType,
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
                closeProductModal();
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

window.openProductModal = () => {
    const modal = document.getElementById('product-modal');
    document.getElementById('product-modal-title').textContent = 'Add Product';
    document.getElementById('product-form').reset();
    document.getElementById('product-id').value = '';
    modal.classList.remove('hidden');
};

window.closeProductModal = () => {
    document.getElementById('product-modal').classList.add('hidden');
};

window.editProduct = (product) => {
    const modal = document.getElementById('product-modal');
    document.getElementById('product-modal-title').textContent = 'Edit Product';
    document.getElementById('product-id').value = product.id;
    document.getElementById('product-name').value = product.product_name;
    document.getElementById('product-price').value = product.price;
    document.getElementById('product-description').value = product.description || '';
    
    // Set radio button
    const radio = document.querySelector(`input[name="product_item_type"][value="${product.item_type}"]`);
    if (radio) radio.checked = true;
    
    modal.classList.remove('hidden');
};

window.deleteProduct = async (id, name) => {
    if (!confirm(`Delete "${name}"? This action cannot be undone.`)) return;

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

// Alert Helper - Use Toast system
function showAlert(type, message) {
    if (window.Toast) {
        window.Toast.show(message, type);
    }
}
