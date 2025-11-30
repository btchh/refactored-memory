/**
 * User Search Module
 * Handles user search with autocomplete for admin booking
 */

export class UserSearch {
    constructor(options = {}) {
        this.searchInput = document.getElementById(options.inputId || 'user-search');
        this.resultsContainer = document.getElementById(options.resultsId || 'user-search-results');
        this.searchUrl = options.searchUrl || '/admin/api/users/search';
        this.onUserSelect = options.onUserSelect || (() => {});
        this.searchTimeout = null;
        this.selectedUser = null;
    }

    async search(query) {
        if (query.length < 2) {
            this.hideResults();
            return;
        }

        try {
            const response = await fetch(`${this.searchUrl}?q=${encodeURIComponent(query)}`);
            const users = await response.json();
            this.displayResults(users);
        } catch (error) {
            console.error('Error searching users:', error);
            this.displayResults([]);
        }
    }

    displayResults(users) {
        if (!this.resultsContainer) return;

        if (users.length === 0) {
            this.resultsContainer.innerHTML = '<div class="p-3 text-gray-500">No users found</div>';
            this.resultsContainer.classList.remove('hidden');
            return;
        }

        this.resultsContainer.innerHTML = users.map(user => `
            <div class="p-3 hover:bg-gray-100 cursor-pointer border-b last:border-b-0" data-user='${JSON.stringify(user)}'>
                <p class="font-semibold">${user.name}</p>
                <p class="text-sm text-gray-600">${user.email}</p>
            </div>
        `).join('');

        // Attach click listeners
        this.resultsContainer.querySelectorAll('[data-user]').forEach(el => {
            el.addEventListener('click', () => {
                const user = JSON.parse(el.dataset.user);
                this.selectUser(user);
            });
        });

        this.resultsContainer.classList.remove('hidden');
    }

    selectUser(user) {
        this.selectedUser = user;
        
        // Clear search input
        if (this.searchInput) {
            this.searchInput.value = '';
        }
        
        // Hide results
        this.hideResults();
        
        // Update hidden input
        const userIdInput = document.getElementById('user_id');
        if (userIdInput) {
            userIdInput.value = user.id;
        }
        
        // Update display
        const displayElements = {
            'selected-user-name': user.name,
            'selected-user-email': user.email,
            'selected-user-phone': user.phone,
            'selected-user-address': user.address,
            'pickup_address': user.address,
            'latitude': user.latitude || '',
            'longitude': user.longitude || ''
        };

        Object.entries(displayElements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                if (element.tagName === 'INPUT') {
                    element.value = value;
                } else {
                    element.textContent = value;
                }
            }
        });

        // Show selected user info
        const selectedInfo = document.getElementById('selected-user-info');
        if (selectedInfo) {
            selectedInfo.classList.remove('hidden');
        }

        // Trigger callback
        this.onUserSelect(user);
    }

    clearSelection() {
        this.selectedUser = null;
        
        const userIdInput = document.getElementById('user_id');
        if (userIdInput) {
            userIdInput.value = '';
        }

        const selectedInfo = document.getElementById('selected-user-info');
        if (selectedInfo) {
            selectedInfo.classList.add('hidden');
        }
    }

    hideResults() {
        if (this.resultsContainer) {
            this.resultsContainer.classList.add('hidden');
        }
    }

    setupListeners() {
        if (!this.searchInput) return;

        // Search on input
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            const query = e.target.value;
            
            this.searchTimeout = setTimeout(() => {
                this.search(query);
            }, 300);
        });

        // Clear button
        const clearBtn = document.getElementById('clear-user');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.clearSelection());
        }

        // Close results when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#user-search') && !e.target.closest('#user-search-results')) {
                this.hideResults();
            }
        });
    }

    init() {
        this.setupListeners();
    }
}
