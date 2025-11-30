/**
 * Calendar Module
 * Handles calendar rendering and date selection
 */

export class Calendar {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.currentDate = new Date();
        this.onDateSelect = options.onDateSelect || (() => {});
        this.selectedDate = null;
        this.bookingCounts = {};
        this.countsUrl = options.countsUrl || null;
    }

    async render() {
        if (!this.container) return;

        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        
        // Load booking counts if URL is provided
        if (this.countsUrl) {
            await this.loadBookingCounts(year, month + 1);
        }
        
        // Update month display
        const monthDisplay = document.getElementById('current-month');
        if (monthDisplay) {
            monthDisplay.textContent = new Date(year, month).toLocaleDateString('en-US', { 
                month: 'long', 
                year: 'numeric' 
            });
        }
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const grid = document.getElementById('calendar-grid');
        if (!grid) return;
        
        grid.innerHTML = '';
        
        // Day headers
        ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(day => {
            const header = document.createElement('div');
            header.className = 'text-center font-bold text-sm text-gray-500 py-2';
            header.textContent = day;
            grid.appendChild(header);
        });
        
        // Empty cells
        for (let i = 0; i < firstDay; i++) {
            grid.appendChild(document.createElement('div'));
        }
        
        // Days
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            date.setHours(0, 0, 0, 0);
            
            // Format date as YYYY-MM-DD without timezone issues
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isPast = date < today;
            const bookingCount = this.bookingCounts[dateStr] || 0;
            
            const dayEl = document.createElement('div');
            dayEl.className = `calendar-day font-semibold relative ${
                isPast ? 'disabled' : 'available'
            }`;
            
            // Add badge if there are bookings
            if (bookingCount > 0) {
                dayEl.innerHTML = `
                    <span>${day}</span>
                    <span class="absolute top-1 right-1 bg-primary-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">${bookingCount}</span>
                `;
            } else {
                dayEl.textContent = day;
            }
            
            dayEl.dataset.date = dateStr;
            dayEl.dataset.day = day;
            dayEl.dataset.count = bookingCount;
            
            if (!isPast) {
                dayEl.onclick = () => this.selectDate(dateStr, dayEl, day);
            }
            
            grid.appendChild(dayEl);
        }
    }

    async loadBookingCounts(year, month) {
        if (!this.countsUrl) return;
        
        try {
            const response = await fetch(`${this.countsUrl}?year=${year}&month=${month}`);
            const data = await response.json();
            
            if (data.success) {
                this.bookingCounts = data.counts;
            }
        } catch (error) {
            console.error('Failed to load booking counts:', error);
        }
    }

    selectDate(dateStr, element, day) {
        this.selectedDate = dateStr;
        
        console.log('Date selected:', {
            dateStr: dateStr,
            day: day,
            elementText: element.textContent
        });
        
        // Highlight selected date
        const grid = document.getElementById('calendar-grid');
        if (grid) {
            grid.querySelectorAll('.calendar-day').forEach(el => {
                el.classList.remove('selected');
            });
        }
        
        element.classList.add('selected');
        
        // Trigger callback
        this.onDateSelect(dateStr);
    }

    navigateMonth(direction) {
        const newDate = new Date(this.currentDate);
        newDate.setMonth(newDate.getMonth() + direction);
        
        // Prevent navigating to past months
        const today = new Date();
        today.setDate(1);
        today.setHours(0, 0, 0, 0);
        
        if (newDate >= today || direction > 0) {
            this.currentDate = newDate;
            this.render();
        }
    }

    setupNavigation() {
        const prevBtn = document.getElementById('prev-month');
        const nextBtn = document.getElementById('next-month');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                this.navigateMonth(-1);
                this.updateNavigationButtons();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                this.navigateMonth(1);
                this.updateNavigationButtons();
            });
        }
        
        this.updateNavigationButtons();
    }

    updateNavigationButtons() {
        const prevBtn = document.getElementById('prev-month');
        const today = new Date();
        today.setDate(1);
        today.setHours(0, 0, 0, 0);
        
        const currentMonth = new Date(this.currentDate);
        currentMonth.setDate(1);
        currentMonth.setHours(0, 0, 0, 0);
        
        if (prevBtn) {
            if (currentMonth <= today) {
                prevBtn.disabled = true;
                prevBtn.classList.add('btn-disabled', 'opacity-50', 'cursor-not-allowed');
            } else {
                prevBtn.disabled = false;
                prevBtn.classList.remove('btn-disabled', 'opacity-50', 'cursor-not-allowed');
            }
        }
    }

    reset() {
        this.selectedDate = null;
        const grid = document.getElementById('calendar-grid');
        if (grid) {
            grid.querySelectorAll('div').forEach(el => {
                el.classList.remove('bg-blue-500', 'text-white', 'ring-2', 'ring-blue-400');
            });
        }
    }

    init() {
        this.render();
        this.setupNavigation();
    }
}
