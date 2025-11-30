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
            const itemType = document.getElementById('item_type').value;
            const totalPrice = parseFloat(document.getElementById('total-price').textContent);

            if (!bookingDate) {
                e.preventDefault();
                alert('Please select a date from the calendar');
                return false;
            }

            if (!bookingTime) {
                e.preventDefault();
                alert('Please select a time slot');
                return false;
            }

            if (!itemType) {
                e.preventDefault();
                alert('Please select an item type');
                return false;
            }

            if (totalPrice <= 0) {
                e.preventDefault();
                alert('Please select at least one service or product');
                return false;
            }

            // Confirm booking
            if (!confirm(`Confirm booking for ${bookingDate} at ${bookingTime}?\nTotal: ₱${totalPrice.toFixed(2)}`)) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Branch selection handler
    const adminSelect = document.getElementById('admin_id');
    if (adminSelect) {
        adminSelect.addEventListener('change', function() {
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
            } else {
                if (branchInfo) branchInfo.classList.add('hidden');
            }
        });
    }
});
