/**
 * Booking Form Module
 * Handles booking form logic, service/product selection, and price calculation
 */

export class BookingForm {
    constructor(options = {}) {
        this.servicesData = options.servicesData || {};
        this.productsData = options.productsData || {};
        this.selectedServices = [];
        this.selectedProducts = [];
        this.onTotalChange = options.onTotalChange || (() => {});
    }

    loadServices(itemType) {
        const container = document.getElementById('services-container');
        if (!container) return;

        const services = this.servicesData[itemType] || [];
        
        if (services.length === 0) {
            container.innerHTML = '<p class="text-gray-500 col-span-2">No services available</p>';
            return;
        }
        
        container.innerHTML = services.map(service => `
            <label class="group flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:border-blue-400 hover:shadow-md transition-all duration-300 transform hover:scale-105">
                <input type="checkbox" name="services[]" value="${service.id}" 
                    class="checkbox checkbox-primary checkbox-lg" data-price="${service.price}" data-name="${service.service_name}">
                <div class="flex-1">
                    <span class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">${service.service_name}</span>
                    <span class="block text-xs font-bold text-green-600 mt-1">₱${service.price}</span>
                </div>
            </label>
        `).join('');

        // Attach event listeners
        container.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.toggleService(
                    parseInt(e.target.value),
                    parseFloat(e.target.dataset.price),
                    e.target.dataset.name,
                    e.target.checked
                );
            });
        });
    }

    loadProducts(itemType) {
        const container = document.getElementById('products-container');
        if (!container) return;

        const products = this.productsData[itemType] || [];
        
        if (products.length === 0) {
            container.innerHTML = '<p class="text-gray-500 col-span-2">No products available</p>';
            return;
        }
        
        container.innerHTML = products.map(product => `
            <label class="flex items-center gap-2 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-purple-50 hover:border-purple-300 transition-all">
                <input type="checkbox" name="products[]" value="${product.id}" 
                    class="checkbox checkbox-secondary" data-price="${product.price}" data-name="${product.product_name}">
                <span class="text-sm font-medium">${product.product_name} <span class="text-green-600">(₱${product.price})</span></span>
            </label>
        `).join('');

        // Attach event listeners
        container.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.toggleProduct(
                    parseInt(e.target.value),
                    parseFloat(e.target.dataset.price),
                    e.target.dataset.name,
                    e.target.checked
                );
            });
        });
    }

    toggleService(id, price, name, checked) {
        if (checked) {
            this.selectedServices.push({ id, price, name });
        } else {
            this.selectedServices = this.selectedServices.filter(s => s.id !== id);
        }
        this.calculateTotal();
    }

    toggleProduct(id, price, name, checked) {
        if (checked) {
            this.selectedProducts.push({ id, price, name });
        } else {
            this.selectedProducts = this.selectedProducts.filter(p => p.id !== id);
        }
        this.calculateTotal();
    }

    calculateTotal() {
        const total = this.selectedServices.reduce((sum, s) => sum + s.price, 0) +
                     this.selectedProducts.reduce((sum, p) => sum + p.price, 0);
        
        const totalElement = document.getElementById('total-price');
        if (totalElement) {
            totalElement.textContent = total.toFixed(2);
        }

        this.onTotalChange(total);
        return total;
    }

    reset() {
        this.selectedServices = [];
        this.selectedProducts = [];
        this.calculateTotal();
        
        // Reset checkboxes
        document.querySelectorAll('input[name="services[]"], input[name="products[]"]').forEach(cb => {
            cb.checked = false;
        });
    }

    setupItemTypeListener() {
        const itemTypeSelect = document.getElementById('item_type');
        if (itemTypeSelect) {
            itemTypeSelect.addEventListener('change', (e) => {
                const itemType = e.target.value;
                this.selectedServices = [];
                this.selectedProducts = [];
                
                if (!itemType) {
                    document.getElementById('services-container').innerHTML = '<p class="text-gray-500 col-span-2">Select an item type first</p>';
                    document.getElementById('products-container').innerHTML = '<p class="text-gray-500 col-span-2">Select an item type first</p>';
                    return;
                }
                
                this.loadServices(itemType);
                this.loadProducts(itemType);
                this.calculateTotal();
            });
        }
    }

    init() {
        this.setupItemTypeListener();
        this.calculateTotal();
    }
}
