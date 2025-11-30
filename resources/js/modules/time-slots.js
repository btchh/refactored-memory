/**
 * Time Slots Module
 * Handles loading and displaying available time slots from CalAPI
 */

import { api } from './api.js';

export class TimeSlots {
    constructor(options = {}) {
        this.selectElement = document.getElementById(options.selectId || 'booking_time');
        this.slotsUrl = options.slotsUrl || '/api/calendar/slots';
    }

    async loadSlots(date) {
        if (!this.selectElement) return;

        // Show loading state
        this.selectElement.innerHTML = '<option value="">Loading slots...</option>';
        this.selectElement.disabled = true;

        try {
            const data = await api.get(`${this.slotsUrl}?date=${date}`);
            
            this.selectElement.innerHTML = '<option value="">Select time slot</option>';
            
            if (data.slots && data.slots.length > 0) {
                const availableSlots = data.slots.filter(slot => slot.available);
                
                if (availableSlots.length === 0) {
                    this.selectElement.innerHTML = '<option value="">No slots available</option>';
                } else {
                    availableSlots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.time;
                        option.textContent = slot.formatted || slot.time;
                        this.selectElement.appendChild(option);
                    });
                }
            } else {
                this.selectElement.innerHTML = '<option value="">No slots available</option>';
            }
        } catch (error) {
            console.error('Error loading time slots:', error);
            this.selectElement.innerHTML = '<option value="">Error loading slots. Please try again.</option>';
        } finally {
            this.selectElement.disabled = false;
        }
    }

    clear() {
        if (this.selectElement) {
            this.selectElement.innerHTML = '<option value="">Select time slot</option>';
        }
    }
}
