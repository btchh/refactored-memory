/**
 * User Booking Page
 * Main entry point for user booking interface
 */

import { Calendar } from '../modules/calendar.js';
import { BookingForm } from '../modules/booking-form.js';
import { TimeSlots } from '../modules/time-slots.js';

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Get data from window object
    const data = window.bookingData;
    if (!data) {
        console.error('Booking data not found');
        return;
    }

    const { services, products, routes } = data;

    // Initialize Time Slots first (needed by calendar)
    const timeSlots = new TimeSlots({
        selectId: 'booking_time',
        slotsUrl: routes.slots
    });

    // Initialize Calendar
    const calendar = new Calendar('calendar-container', {
        onDateSelect: (date) => {
            console.log('Calendar date selected:', date);
            
            // Show form, hide empty state
            const emptyState = document.getElementById('empty-state');
            const bookingForm = document.getElementById('booking-form');
            
            if (emptyState) emptyState.classList.add('hidden');
            if (bookingForm) bookingForm.classList.remove('hidden');
            
            // Set date
            const dateInput = document.getElementById('booking_date');
            if (dateInput) {
                dateInput.value = date;
                console.log('Date input set to:', dateInput.value);
                
                // Show formatted date
                const dateDisplay = document.getElementById('date-display');
                if (dateDisplay) {
                    const dateObj = new Date(date + 'T00:00:00');
                    const formatted = dateObj.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    dateDisplay.textContent = `Selected: ${formatted}`;
                }
            }
            
            // Load time slots
            console.log('Loading time slots for:', date);
            timeSlots.loadSlots(date);
        }
    });
    calendar.init();

    // Initialize Booking Form
    const bookingForm = new BookingForm({
        servicesData: services,
        productsData: products
    });
    bookingForm.init();

    // Clear selection button
    const clearBtn = document.getElementById('clear-selection');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            // Reset form
            bookingForm.reset();
            
            // Reset calendar
            calendar.reset();
            
            // Clear form fields
            const itemType = document.getElementById('item_type');
            const notes = document.getElementById('notes');
            const bookingDate = document.getElementById('booking_date');
            const bookingTime = document.getElementById('booking_time');
            const servicesContainer = document.getElementById('services-container');
            const productsContainer = document.getElementById('products-container');
            
            if (itemType) itemType.value = '';
            if (notes) notes.value = '';
            if (bookingDate) bookingDate.value = '';
            if (bookingTime) bookingTime.innerHTML = '<option value="">Select time slot</option>';
            if (servicesContainer) servicesContainer.innerHTML = '<p class="text-gray-500 col-span-2">Select an item type first</p>';
            if (productsContainer) productsContainer.innerHTML = '<p class="text-gray-500 col-span-2">Select an item type first</p>';
            
            // Hide form, show empty state
            const emptyState = document.getElementById('empty-state');
            const form = document.getElementById('booking-form');
            if (emptyState) emptyState.classList.remove('hidden');
            if (form) form.classList.add('hidden');
        });
    }

    // Form validation before submission
    const form = document.getElementById('booking-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            const bookingDate = document.getElementById('booking_date').value;
            const bookingTime = document.getElementById('booking_time').value;
            const itemType = document.querySelector('input[name="item_type"]:checked')?.value || document.getElementById('item_type')?.value;
            const totalPrice = parseFloat(document.getElementById('total-price').textContent);

            if (!bookingDate) {
                e.preventDefault();
                window.Toast?.warning('Please select a date from the calendar');
                return false;
            }

            if (!bookingTime) {
                e.preventDefault();
                window.Toast?.warning('Please select a time slot');
                return false;
            }

            if (!itemType) {
                e.preventDefault();
                window.Toast?.warning('Please select an item type');
                return false;
            }

            if (totalPrice <= 0) {
                e.preventDefault();
                window.Toast?.warning('Please select at least one service or product');
                return false;
            }

            // Show confirmation modal instead of native confirm
            e.preventDefault();
            showBookingConfirmation(bookingDate, bookingTime, totalPrice, form);
        });
    }

    // Booking confirmation modal
    function showBookingConfirmation(date, time, total, formElement) {
        // Create modal if it doesn't exist
        let modal = document.getElementById('booking-confirm-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'booking-confirm-modal';
            modal.className = 'fixed inset-0 z-[300] flex items-center justify-center p-4 hidden';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeBookingModal()"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform scale-95 opacity-0 transition-all duration-300" id="booking-modal-content">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Confirm Booking</h3>
                        <p class="text-gray-600 mb-4" id="booking-confirm-details"></p>
                        <div class="bg-green-50 rounded-xl p-4 mb-6">
                            <p class="text-sm text-gray-600">Total Amount</p>
                            <p class="text-3xl font-bold text-green-600" id="booking-confirm-total"></p>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeBookingModal()" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                                Cancel
                            </button>
                            <button type="button" onclick="submitBookingForm()" class="flex-1 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors">
                                Confirm Booking
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // Format date nicely
        const dateObj = new Date(date + 'T00:00:00');
        const formattedDate = dateObj.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });

        // Update modal content
        document.getElementById('booking-confirm-details').textContent = `${formattedDate} at ${time}`;
        document.getElementById('booking-confirm-total').textContent = `₱${total.toFixed(2)}`;

        // Store form reference
        window._bookingForm = formElement;

        // Show modal with animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            const content = document.getElementById('booking-modal-content');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // Global functions for modal
    window.closeBookingModal = function() {
        const modal = document.getElementById('booking-confirm-modal');
        const content = document.getElementById('booking-modal-content');
        if (modal && content) {
            content.classList.add('scale-95', 'opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    };

    window.submitBookingForm = function() {
        closeBookingModal();
        if (window._bookingForm) {
            // Remove the submit event listener temporarily and submit
            const form = window._bookingForm;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Submitting...';
            }
            // Use HTMLFormElement.submit() to bypass event listeners
            HTMLFormElement.prototype.submit.call(form);
        }
    };

    // Branch card selection handler
    const branchCards = document.querySelectorAll('.branch-card');
    branchCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected state from all cards
            branchCards.forEach(c => {
                c.classList.remove('border-purple-600', 'bg-purple-50');
                c.classList.add('border-gray-200');
                c.querySelector('.branch-check')?.classList.add('hidden');
            });
            
            // Add selected state to clicked card
            this.classList.remove('border-gray-200');
            this.classList.add('border-purple-600', 'bg-purple-50');
            this.querySelector('.branch-check')?.classList.remove('hidden');
            
            // Update hidden select
            const branchId = this.dataset.branchId;
            const adminSelect = document.getElementById('admin_id');
            if (adminSelect) {
                adminSelect.value = branchId;
                // Trigger change event to load pricing
                adminSelect.dispatchEvent(new Event('change'));
            }
        });
    });

    // Branch selection handler (for hidden select)
    const adminSelect = document.getElementById('admin_id');
    if (adminSelect) {
        adminSelect.addEventListener('change', async function() {
            const selectedOption = this.options[this.selectedIndex];
            const branchInfo = document.getElementById('branch-info');
            const branchAddress = document.getElementById('branch-address');
            const branchPhone = document.getElementById('branch-phone');
            
            if (this.value && selectedOption) {
                const address = selectedOption.getAttribute('data-address');
                const phone = selectedOption.getAttribute('data-phone');
                
                if (branchAddress) branchAddress.textContent = `📍 ${address}`;
                if (branchPhone) branchPhone.textContent = `📞 ${phone}`;
                if (branchInfo) branchInfo.classList.remove('hidden');

                // Load branch-specific pricing
                try {
                    const response = await fetch(`${routes.branchPricing}?admin_id=${this.value}`);
                    const result = await response.json();
                    
                    if (result.success) {
                        // Update booking form with new services/products
                        bookingForm.servicesData = result.services;
                        bookingForm.productsData = result.products;
                        
                        // Reload current item type if selected
                        const currentItemType = document.querySelector('input[name="item_type"]:checked')?.value;
                        if (currentItemType) {
                            bookingForm.handleItemTypeChange(currentItemType);
                        }
                    }
                } catch (error) {
                    console.error('Failed to load branch pricing:', error);
                    window.Toast?.error('Failed to load branch pricing');
                }
            } else {
                if (branchInfo) branchInfo.classList.add('hidden');
                // Clear services/products
                bookingForm.servicesData = {};
                bookingForm.productsData = {};
                document.getElementById('services-container').innerHTML = '<p class="text-gray-400 col-span-2 text-sm text-center py-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">Select a branch first</p>';
                document.getElementById('products-container').innerHTML = '<p class="text-gray-400 col-span-2 text-sm text-center py-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">Select a branch first</p>';
            }
        });
    }

    // Service type toggle (pickup vs dropoff)
    const serviceTypeRadios = document.querySelectorAll('input[name="service_type"]');
    const pickupAddressSection = document.getElementById('pickup-address-section');
    const pickupAddressInput = document.getElementById('pickup_address');

    function updateServiceTypeUI() {
        const selectedType = document.querySelector('input[name="service_type"]:checked')?.value;
        
        if (pickupAddressSection) {
            if (selectedType === 'dropoff') {
                pickupAddressSection.classList.add('hidden');
                if (pickupAddressInput) pickupAddressInput.removeAttribute('required');
            } else {
                pickupAddressSection.classList.remove('hidden');
                if (pickupAddressInput) pickupAddressInput.setAttribute('required', 'required');
            }
        }
    }

    serviceTypeRadios.forEach(radio => {
        radio.addEventListener('change', updateServiceTypeUI);
    });

    // Initialize on load
    updateServiceTypeUI();
});
