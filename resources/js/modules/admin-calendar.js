/**
 * Admin Calendar Module
 * Handles calendar rendering with full date access (past and future)
 * for managing all bookings including historical ones
 */

export class AdminCalendar {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.currentDate = new Date();
        this.onDateSelect = options.onDateSelect || (() => {});
        this.selectedDate = null;
        this.bookingCounts = {};
        this.countsUrl = options.countsUrl || null;
        this.allowPastDates = options.allowPastDates !== false; // Default true for admin
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
            const isToday = date.getTime() === today.getTime();
            const bookingCount = this.bookingCounts[dateStr] || 0;
            
            const dayEl = document.createElement('div');
            
            // Admin calendar: all dates are clickable, past dates have different styling
            let dayClass = 'calendar-day font-semibold relative cursor-pointer ';
            if (isToday) {
                dayClass += 'today ring-2 ring-wash ring-offset-1 ';
            }
            if (isPast) {
                dayClass += 'past bg-gray-100 text-gray-600 hover:bg-gray-200 ';
            } else {
                dayClass += 'available ';
            }
            
            dayEl.className = dayClass;
            
            // Add badge if there are bookings
            if (bookingCount > 0) {
                const badgeColor = isPast ? 'bg-gray-500' : 'bg-primary-600';
                dayEl.innerHTML = `
                    <span>${day}</span>
                    <span class="absolute top-1 right-1 ${badgeColor} text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">${bookingCount}</span>
                `;
            } else {
                dayEl.textContent = day;
            }
            
            dayEl.dataset.date = dateStr;
            dayEl.dataset.day = day;
            dayEl.dataset.count = bookingCount;
            dayEl.dataset.isPast = isPast;
            
            // Admin can click any date
            dayEl.onclick = () => this.selectDate(dateStr, dayEl, day, isPast);
            
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

    selectDate(dateStr, element, day, isPast = false) {
        this.selectedDate = dateStr;
        this.selectedIsPast = isPast;
        
        // Highlight selected date
        const grid = document.getElementById('calendar-grid');
        if (grid) {
            grid.querySelectorAll('.calendar-day').forEach(el => {
                el.classList.remove('selected');
            });
        }
        
        element.classList.add('selected');
        
        // Trigger callback with isPast flag
        this.onDateSelect(dateStr, isPast);
    }

    navigateMonth(direction) {
        const newDate = new Date(this.currentDate);
        newDate.setMonth(newDate.getMonth() + direction);
        
        // Admin can navigate to any month (no restrictions)
        this.currentDate = newDate;
        this.render();
    }

    setupNavigation() {
        const prevBtn = document.getElementById('prev-month');
        const nextBtn = document.getElementById('next-month');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                this.navigateMonth(-1);
            });
            // Remove disabled state for admin - can always go back
            prevBtn.disabled = false;
            prevBtn.classList.remove('btn-disabled', 'opacity-50', 'cursor-not-allowed');
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                this.navigateMonth(1);
            });
        }
    }

    reset() {
        this.selectedDate = null;
        this.selectedIsPast = false;
        const grid = document.getElementById('calendar-grid');
        if (grid) {
            grid.querySelectorAll('.calendar-day').forEach(el => {
                el.classList.remove('selected');
            });
        }
    }

    init() {
        this.render();
        this.setupNavigation();
    }
}
