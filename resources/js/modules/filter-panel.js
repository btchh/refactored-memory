/**
 * Filter Panel Module
 * Provides consistent filter/search functionality across admin and user interfaces
 */

export class FilterPanel {
    constructor(options = {}) {
        this.form = options.form || document.querySelector('[data-filter-panel]');
        this.startDateInput = options.startDateInput || this.form?.querySelector('[data-start-date]');
        this.endDateInput = options.endDateInput || this.form?.querySelector('[data-end-date]');
        this.autoSubmit = options.autoSubmit ?? false;
        
        this.init();
    }

    init() {
        if (!this.form) return;
        
        this.bindQuickRangeButtons();
        this.bindAutoSubmit();
    }

    bindQuickRangeButtons() {
        document.querySelectorAll('[data-quick-range]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const days = parseInt(button.dataset.quickRange);
                this.setDateRange(days);
            });
        });
    }

    bindAutoSubmit() {
        if (!this.autoSubmit) return;
        
        this.form.querySelectorAll('select, input[type="date"]').forEach(input => {
            input.addEventListener('change', () => this.form.submit());
        });
    }

    /**
     * Set date range based on number of days back from today
     */
    setDateRange(days) {
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - days);
        
        if (this.startDateInput) {
            this.startDateInput.value = this.formatDate(startDate);
        }
        if (this.endDateInput) {
            this.endDateInput.value = this.formatDate(endDate);
        }
        
        if (this.autoSubmit) {
            this.form.submit();
        }
    }

    /**
     * Set specific date range
     */
    setCustomRange(startDate, endDate) {
        if (this.startDateInput && startDate) {
            this.startDateInput.value = this.formatDate(new Date(startDate));
        }
        if (this.endDateInput && endDate) {
            this.endDateInput.value = this.formatDate(new Date(endDate));
        }
    }

    /**
     * Clear all filters
     */
    clearFilters() {
        this.form.querySelectorAll('input:not([type="hidden"]), select').forEach(input => {
            if (input.type === 'text' || input.type === 'date') {
                input.value = '';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
    }

    /**
     * Format date to YYYY-MM-DD
     */
    formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    /**
     * Get current filter values
     */
    getValues() {
        const formData = new FormData(this.form);
        const values = {};
        for (const [key, value] of formData.entries()) {
            values[key] = value;
        }
        return values;
    }
}

/**
 * Quick date range presets
 */
export const DatePresets = {
    today: () => {
        const today = new Date();
        return { start: today, end: today };
    },
    yesterday: () => {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        return { start: yesterday, end: yesterday };
    },
    thisWeek: () => {
        const today = new Date();
        const start = new Date(today);
        start.setDate(today.getDate() - today.getDay());
        return { start, end: today };
    },
    lastWeek: () => {
        const today = new Date();
        const end = new Date(today);
        end.setDate(today.getDate() - today.getDay() - 1);
        const start = new Date(end);
        start.setDate(end.getDate() - 6);
        return { start, end };
    },
    thisMonth: () => {
        const today = new Date();
        const start = new Date(today.getFullYear(), today.getMonth(), 1);
        return { start, end: today };
    },
    lastMonth: () => {
        const today = new Date();
        const start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const end = new Date(today.getFullYear(), today.getMonth(), 0);
        return { start, end };
    },
    thisYear: () => {
        const today = new Date();
        const start = new Date(today.getFullYear(), 0, 1);
        return { start, end: today };
    },
    last7Days: () => {
        const today = new Date();
        const start = new Date(today);
        start.setDate(today.getDate() - 7);
        return { start, end: today };
    },
    last30Days: () => {
        const today = new Date();
        const start = new Date(today);
        start.setDate(today.getDate() - 30);
        return { start, end: today };
    },
    last90Days: () => {
        const today = new Date();
        const start = new Date(today);
        start.setDate(today.getDate() - 90);
        return { start, end: today };
    }
};

/**
 * Initialize filter panel with default options
 */
export function initFilterPanel(selector = '[data-filter-panel]', options = {}) {
    const form = document.querySelector(selector);
    if (form) {
        return new FilterPanel({ form, ...options });
    }
    return null;
}

export default FilterPanel;
