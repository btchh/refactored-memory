// Comprehensive Tour System for Admin and User sides
import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

class TourSystem {
    constructor() {
        this.driverInstance = null;
        this.isInitialized = false;
        this.autoStarted = false;
        this.init();
    }

    init() {
        try {
            console.log('🎯 Initializing tour system...');
            
            this.driverInstance = driver({
                showProgress: true,
                nextBtnText: 'Next →',
                prevBtnText: '← Previous', 
                doneBtnText: 'Got it!',
                closeBtnText: '×',
                allowClose: true,
                overlayClickNext: false,
                smoothScroll: true,
                animate: true,
                overlayOpacity: 0.75,
                stagePadding: 4,
                stageRadius: 8,
                onDestroyed: () => {
                    console.log('Tour completed');
                    if (this.driverInstance && this.driverInstance.tourKey) {
                        this.markTourAsCompleted(this.driverInstance.tourKey);
                        // Clean up demo if it was a booking form tour
                        if (this.driverInstance.tourKey.includes('booking-form') || this.driverInstance.tourKey.includes('admin-booking-form')) {
                            setTimeout(() => this.cleanupDemo(), 1000);
                        }
                    }
                }
            });

            // Make available globally
            window.tour = {
                // Admin tours
                startAdminDashboard: () => this.startAdminDashboardTour(),
                startBookingManagement: () => this.startBookingManagementTour(),
                startAdminBookingForm: () => this.startAdminBookingFormTour(),
                startAdminUsers: () => this.startAdminUsersManagementTour(),
                startAdminPricing: () => this.startAdminPricingTour(),
                startAdminAnalytics: () => this.startAdminAnalyticsTour(),
                // User tours
                startUserDashboard: () => this.startUserDashboardTour(),
                startUserBookingForm: () => this.startUserBookingFormTour(),
                startUserHistory: () => this.startUserHistoryTour(),
                // Utilities
                reset: this.resetTours,
                isCompleted: this.isCompleted
            };

            this.isInitialized = true;
            this.autoStartTours();
            console.log('✅ Tour system initialized successfully');
        } catch (error) {
            console.error('❌ Tour initialization failed:', error);
        }
    }

    autoStartTours() {
        const currentPath = window.location.pathname;
        console.log('Current path:', currentPath);
        
        // Prevent multiple auto-starts
        if (this.autoStarted) {
            console.log('Auto-start already executed, skipping');
            return;
        }
        this.autoStarted = true;
        
        setTimeout(() => {
            // Admin tours
            if (currentPath.includes('/admin/dashboard')) {
                if (!this.isCompleted('admin-dashboard')) {
                    console.log('Auto-starting admin dashboard tour');
                    this.startAdminDashboardTour();
                }
            } else if (currentPath.includes('/admin/bookings/manage')) {
                if (!this.isCompleted('booking-management')) {
                    console.log('Auto-starting booking management tour');
                    this.startBookingManagementTour();
                }
            } else if (currentPath.includes('/admin/bookings') && !currentPath.includes('/manage')) {
                if (!this.isCompleted('admin-booking-form')) {
                    console.log('Auto-starting admin booking form tour');
                    this.startAdminBookingFormTour();
                }
            } else if (currentPath.includes('/admin/users')) {
                if (!this.isCompleted('admin-users')) {
                    console.log('Auto-starting admin users tour');
                    this.startAdminUsersManagementTour();
                }
            } else if (currentPath.includes('/admin/pricing')) {
                if (!this.isCompleted('admin-pricing')) {
                    console.log('Auto-starting admin pricing tour');
                    this.startAdminPricingTour();
                }
            } else if (currentPath.includes('/admin/analytics')) {
                if (!this.isCompleted('admin-analytics')) {
                    console.log('Auto-starting admin analytics tour');
                    this.startAdminAnalyticsTour();
                }
            }
            // User tours
            else if (currentPath.includes('/user/dashboard')) {
                if (!this.isCompleted('user-dashboard')) {
                    console.log('Auto-starting user dashboard tour');
                    this.startUserDashboardTour();
                }
            } else if (currentPath.includes('/user/booking')) {
                if (!this.isCompleted('user-booking-form')) {
                    console.log('Auto-starting user booking form tour');
                    this.startUserBookingFormTour();
                }
            } else if (currentPath.includes('/user/history')) {
                if (!this.isCompleted('user-history')) {
                    console.log('Auto-starting user history tour');
                    this.startUserHistoryTour();
                }
            }
        }, 2000);
    }

    // Admin Booking Management Tour
    startBookingManagementTour() {
        if (!this.isInitialized) return;

        const steps = [
            {
                element: '.hero-header',
                popover: {
                    title: 'Welcome to Booking Management! 👋',
                    description: 'This is your central hub for managing all laundry bookings. Let\'s take a quick tour!',
                    side: 'bottom'
                }
            },
            {
                element: '.stats-grid',
                popover: {
                    title: 'Booking Statistics 📊',
                    description: 'These cards show booking counts by status (All, Pending, In Progress, Completed, Cancelled). Click any card to filter.',
                    side: 'bottom'
                }
            },
            {
                element: '.bookings-table',
                popover: {
                    title: 'Bookings Table 📋',
                    description: 'View all bookings with customer details, dates, services, and status. Use the dropdown to change status or action buttons to manage bookings.',
                    side: 'top'
                }
            }
        ];

        // Add filter panel if it exists
        const filterPanel = document.querySelector('.filter-panel');
        if (filterPanel) {
            steps.splice(2, 0, {
                element: '.filter-panel',
                popover: {
                    title: 'Date Filters 🔍',
                    description: 'Filter bookings by date ranges - today, tomorrow, this week, this month, or custom dates. You can also search by customer name.',
                    side: 'bottom'
                }
            });
        }

        this.startTour(steps, 'booking-management');
    }

    // Admin Booking Form Tour - More helpful like user version
    startAdminBookingFormTour() {
        if (!this.isInitialized) return;

        const steps = [
            {
                element: 'body',
                popover: {
                    title: 'Admin Booking Creation 📝',
                    description: 'Let me show you how to create bookings for walk-in customers or phone orders. This is a step-by-step process that requires specific information in order.',
                    side: 'bottom'
                }
            }
        ];

        // Check what elements exist and build helpful steps
        const serviceSelection = document.querySelector('.service-selection, .pickup-delivery-options, .admin-service-cards');
        const customerSelection = document.querySelector('.user-search, .customer-selection, .booking-type-selection');
        const branchSelection = document.querySelector('.branch-selector, .branch-selection');
        const dateTimeSelection = document.querySelector('.calendar-picker, .date-time-picker, .schedule-selection');
        const servicePricing = document.querySelector('.service-pricing, .item-service-selection, .pricing-section');

        // Step 1: Service Type - Always important
        if (serviceSelection) {
            steps.push({
                element: serviceSelection.className.split(' ')[0],
                popover: {
                    title: 'Step 1: Choose Service Type 🚚',
                    description: 'IMPORTANT: Start by selecting the pickup and delivery method. This affects pricing and availability. Choose "Full Service" for complete pickup and delivery, or other options based on customer needs.',
                    side: 'bottom',
                    onNextClick: () => this.demoSelectService()
                }
            });
        }

        // Step 2: Customer Type - Critical decision point
        if (customerSelection) {
            steps.push({
                element: customerSelection.className.split(' ')[0],
                popover: {
                    title: 'Step 2: Customer Type Selection 👤',
                    description: 'CRITICAL: Choose "Online Booking" for existing customers (you can search their info) or "Walk-in" for new customers (you\'ll need to collect their details). This determines the next steps!',
                    side: 'bottom',
                    onNextClick: () => this.demoSelectCustomerType()
                }
            });
        }

        // Step 3: Branch Selection - If applicable
        if (branchSelection && !branchSelection.classList.contains('hidden')) {
            steps.push({
                element: branchSelection.className.split(' ')[0],
                popover: {
                    title: 'Step 3: Select Branch 🏪',
                    description: 'Choose which branch will handle this booking. Each branch has different services, pricing, and availability. Select the one closest to the customer or with the required services.',
                    side: 'bottom',
                    onNextClick: () => this.demoSelectBranch()
                }
            });
        }

        // Step 4: Date/Time - Always required
        if (dateTimeSelection) {
            steps.push({
                element: dateTimeSelection.className.split(' ')[0],
                popover: {
                    title: 'Step 4: Schedule the Booking 📅',
                    description: 'Select pickup/delivery date and time. Green dates are available. Time slots depend on branch capacity and service type. Choose a convenient time for the customer.',
                    side: 'bottom',
                    onNextClick: () => this.demoSelectDateTime()
                }
            });
        }

        // Step 5: Services - The main selection
        if (servicePricing) {
            steps.push({
                element: servicePricing.className.split(' ')[0],
                popover: {
                    title: 'Step 5: Select Items & Services 🧺',
                    description: 'Now the important part: Choose item type (Clothes, Comforter, Shoes) first, then select services needed. Watch the price update automatically. Ask the customer about special requirements!',
                    side: 'top',
                    onNextClick: () => this.demoSelectServices()
                }
            });
        }

        // Step 6: Customer Info - For walk-ins
        const customerInfo = document.querySelector('.customer-info, .walk-in-form, .customer-details');
        if (customerInfo) {
            steps.push({
                element: customerInfo.className.split(' ')[0],
                popover: {
                    title: 'Step 6: Customer Information 📋',
                    description: 'For walk-in customers, collect: Full Name, Phone Number, and Email (optional). This info is needed for pickup notifications and order tracking.',
                    side: 'bottom'
                }
            });
        }

        // Final step: Review and submit
        const bookingSummary = document.querySelector('.booking-summary, .order-summary, .final-review');
        if (bookingSummary) {
            steps.push({
                element: bookingSummary.className.split(' ')[0],
                popover: {
                    title: 'Final Step: Review & Create 💰',
                    description: 'Review all details carefully: customer info, services, date/time, and total cost. Add any special notes from the customer, then click "Create Booking" to complete.',
                    side: 'top'
                }
            });
        } else {
            steps.push({
                element: 'body',
                popover: {
                    title: 'Final Step: Complete the Booking 💰',
                    description: 'Once all information is filled correctly, submit the booking. The customer will receive SMS/email confirmation with pickup details and order number.',
                    side: 'bottom'
                }
            });
        }

        // Add helpful tips if form is mostly empty
        if (steps.length <= 2) {
            steps.push({
                element: 'body',
                popover: {
                    title: 'Admin Booking Tips 💡',
                    description: 'Remember: Always confirm customer details, explain pricing clearly, and provide the order number for tracking. Walk-in customers need phone numbers for pickup notifications!',
                    side: 'bottom'
                }
            });
        }

        this.startTour(steps, 'admin-booking-form');
    }

    // Enhanced demo interaction methods for admin booking form
    demoSelectService() {
        setTimeout(() => {
            console.log('🎯 Demo: Selecting service type...');
            
            // Try different possible service selection elements with priority
            const serviceOptions = [
                { selector: '.admin-service-card[data-pickup="branch_pickup"][data-delivery="branch_delivery"]', name: 'Full Service Card' },
                { selector: '.service-card.full-service', name: 'Full Service Option' },
                { selector: 'input[name="pickup_type"][value="branch_pickup"]', name: 'Branch Pickup Radio' },
                { selector: '.service-option:first-child', name: 'First Service Option' },
                { selector: '.pickup-delivery-card:first-child', name: 'First Pickup Card' }
            ];

            let selected = false;
            for (const option of serviceOptions) {
                const element = document.querySelector(option.selector);
                if (element && !selected) {
                    element.click();
                    console.log(`✅ Demo selected: ${option.name}`);
                    
                    // Add visual feedback
                    element.style.border = '2px solid #3b82f6';
                    setTimeout(() => {
                        if (element.style) element.style.border = '';
                    }, 2000);
                    
                    selected = true;
                    break;
                }
            }
            
            if (!selected) {
                console.log('⚠️ No service selection elements found for demo');
            }
        }, 500);
    }

    demoSelectCustomerType() {
        setTimeout(() => {
            console.log('🎯 Demo: Selecting customer type (Online Booking)...');
            
            const customerOptions = [
                { selector: 'input[name="booking_type"][value="online"]', name: 'Online Booking Radio' },
                { selector: 'input[name="customer_type"][value="existing"]', name: 'Existing Customer Radio' },
                { selector: '.customer-type-online', name: 'Online Customer Button' },
                { selector: '.booking-type-existing', name: 'Existing Booking Type' }
            ];

            let selected = false;
            for (const option of customerOptions) {
                const element = document.querySelector(option.selector);
                if (element && !selected) {
                    element.checked = true;
                    element.dispatchEvent(new Event('change'));
                    console.log(`✅ Demo selected: ${option.name}`);
                    
                    // Add visual feedback
                    const parent = element.closest('.form-group, .radio-group, .option-card') || element.parentElement;
                    if (parent) {
                        parent.style.backgroundColor = '#eff6ff';
                        setTimeout(() => {
                            if (parent.style) parent.style.backgroundColor = '';
                        }, 2000);
                    }
                    
                    selected = true;
                    break;
                }
            }
            
            if (!selected) {
                console.log('⚠️ No customer type selection elements found for demo');
            }
        }, 500);
    }

    demoSelectBranch() {
        setTimeout(() => {
            console.log('🎯 Demo: Selecting branch...');
            
            const branchOptions = [
                { selector: 'select[name="branch_id"] option:nth-child(2)', name: 'Branch Dropdown' },
                { selector: '.branch-card:first-child', name: 'First Branch Card' },
                { selector: 'input[name="branch"]:first-of-type', name: 'First Branch Radio' }
            ];

            let selected = false;
            for (const option of branchOptions) {
                const element = document.querySelector(option.selector);
                if (element && !selected) {
                    if (element.tagName === 'OPTION') {
                        element.selected = true;
                        element.parentElement.dispatchEvent(new Event('change'));
                        console.log(`✅ Demo selected: ${option.name} - ${element.textContent}`);
                    } else {
                        element.click();
                        console.log(`✅ Demo selected: ${option.name}`);
                    }
                    selected = true;
                    break;
                }
            }
            
            if (!selected) {
                console.log('⚠️ No branch selection elements found for demo');
            }
        }, 500);
    }

    demoSelectDateTime() {
        setTimeout(() => {
            console.log('🎯 Demo: Selecting date and time...');
            
            // First try to select a date
            const dateOptions = [
                { selector: '.calendar-day.available:first-child', name: 'Available Calendar Day' },
                { selector: '.calendar-day[data-date]:first-child', name: 'Calendar Day with Date' },
                { selector: '.date-option.available:first-child', name: 'Available Date Option' },
                { selector: 'input[type="date"]', name: 'Date Input Field' }
            ];

            let dateSelected = false;
            for (const option of dateOptions) {
                const element = document.querySelector(option.selector);
                if (element && !dateSelected) {
                    if (element.tagName === 'INPUT') {
                        const today = new Date().toISOString().split('T')[0];
                        element.value = today;
                        element.dispatchEvent(new Event('change'));
                        console.log(`✅ Demo selected date: ${today} in ${option.name}`);
                    } else {
                        element.click();
                        console.log(`✅ Demo selected: ${option.name}`);
                        
                        // Add visual feedback
                        element.style.backgroundColor = '#3b82f6';
                        element.style.color = 'white';
                        setTimeout(() => {
                            if (element.style) {
                                element.style.backgroundColor = '';
                                element.style.color = '';
                            }
                        }, 2000);
                    }
                    dateSelected = true;
                    break;
                }
            }

            // Then try to select a time slot
            setTimeout(() => {
                const timeSlot = document.querySelector('.time-slot.available:first-child, .time-option:first-child, .time-picker option:nth-child(2)');
                if (timeSlot) {
                    if (timeSlot.tagName === 'OPTION') {
                        timeSlot.selected = true;
                        timeSlot.parentElement.dispatchEvent(new Event('change'));
                        console.log(`✅ Demo selected time: ${timeSlot.textContent}`);
                    } else {
                        timeSlot.click();
                        console.log('✅ Demo selected time slot');
                        
                        // Add visual feedback
                        timeSlot.style.backgroundColor = '#10b981';
                        timeSlot.style.color = 'white';
                        setTimeout(() => {
                            if (timeSlot.style) {
                                timeSlot.style.backgroundColor = '';
                                timeSlot.style.color = '';
                            }
                        }, 2000);
                    }
                }
            }, 1000);
            
            if (!dateSelected) {
                console.log('⚠️ No date selection elements found for demo');
            }
        }, 500);
    }

    demoSelectServices() {
        setTimeout(() => {
            console.log('🎯 Demo: Selecting item type and services...');
            
            // Select item type first (prefer clothes)
            const itemTypeOptions = [
                { selector: 'input[name="item_type"][value="clothes"]', name: 'Clothes Item Type' },
                { selector: '.item-type-clothes input', name: 'Clothes Category Input' },
                { selector: '.item-category:first-child input', name: 'First Item Category' }
            ];

            let itemSelected = false;
            for (const option of itemTypeOptions) {
                const element = document.querySelector(option.selector);
                if (element && !itemSelected) {
                    element.checked = true;
                    element.dispatchEvent(new Event('change'));
                    console.log(`✅ Demo selected: ${option.name}`);
                    
                    // Add visual feedback to parent
                    const parent = element.closest('.item-type-card, .category-card, .form-group') || element.parentElement;
                    if (parent) {
                        parent.style.border = '2px solid #10b981';
                        setTimeout(() => {
                            if (parent.style) parent.style.border = '';
                        }, 2000);
                    }
                    
                    itemSelected = true;
                    break;
                }
            }

            // Select some services after item type
            setTimeout(() => {
                const serviceCheckboxes = document.querySelectorAll('#services-container input[type="checkbox"], .service-options input[type="checkbox"], .services-list input[type="checkbox"]');
                
                if (serviceCheckboxes.length > 0) {
                    // Select first service
                    serviceCheckboxes[0].checked = true;
                    serviceCheckboxes[0].dispatchEvent(new Event('change'));
                    console.log(`✅ Demo selected first service: ${this.getServiceName(serviceCheckboxes[0])}`);
                    
                    // Visual feedback
                    const parent1 = serviceCheckboxes[0].closest('.service-item, .checkbox-group') || serviceCheckboxes[0].parentElement;
                    if (parent1) {
                        parent1.style.backgroundColor = '#f0f9ff';
                        setTimeout(() => {
                            if (parent1.style) parent1.style.backgroundColor = '';
                        }, 2000);
                    }
                    
                    // Select second service if available
                    if (serviceCheckboxes.length > 1) {
                        setTimeout(() => {
                            serviceCheckboxes[1].checked = true;
                            serviceCheckboxes[1].dispatchEvent(new Event('change'));
                            console.log(`✅ Demo selected second service: ${this.getServiceName(serviceCheckboxes[1])}`);
                            
                            // Visual feedback
                            const parent2 = serviceCheckboxes[1].closest('.service-item, .checkbox-group') || serviceCheckboxes[1].parentElement;
                            if (parent2) {
                                parent2.style.backgroundColor = '#f0f9ff';
                                setTimeout(() => {
                                    if (parent2.style) parent2.style.backgroundColor = '';
                                }, 2000);
                            }
                        }, 500);
                    }
                } else {
                    console.log('⚠️ No service checkboxes found for demo');
                }
            }, 1000);
            
            if (!itemSelected) {
                console.log('⚠️ No item type selection elements found for demo');
            }
        }, 500);
    }

    // Helper method to get service name for logging
    getServiceName(serviceElement) {
        const label = serviceElement.closest('label') || document.querySelector(`label[for="${serviceElement.id}"]`);
        if (label) {
            return label.textContent.trim();
        }
        return serviceElement.value || 'Unknown Service';
    }

    // User Dashboard Tour
    startUserDashboardTour() {
        if (!this.isInitialized) return;

        const steps = [
            {
                element: 'body',
                popover: {
                    title: 'Welcome to Your Dashboard! 🏠',
                    description: 'This is your personal laundry management center. From here you can book services, track orders, and view your history.',
                    side: 'bottom'
                }
            }
        ];

        // Add specific elements if they exist
        const dashboardHeader = document.querySelector('.dashboard-header');
        const quickActions = document.querySelector('.quick-actions');
        const recentBookings = document.querySelector('.recent-bookings');
        
        if (dashboardHeader) {
            steps[0].element = '.dashboard-header';
        }

        if (quickActions) {
            steps.push({
                element: '.quick-actions',
                popover: {
                    title: 'Quick Actions ⚡',
                    description: 'Use these buttons for common tasks like booking new services or checking your order status.',
                    side: 'bottom'
                }
            });
        }

        if (recentBookings) {
            steps.push({
                element: '.recent-bookings',
                popover: {
                    title: 'Recent Bookings 📋',
                    description: 'Your latest bookings are shown here. You can track status, view details, or cancel pending orders.',
                    side: 'top'
                }
            });
        }

        this.startTour(steps, 'user-dashboard');
    }

    // User Booking Form Tour
    startUserBookingFormTour() {
        if (!this.isInitialized) return;

        const steps = [
            {
                element: 'body',
                popover: {
                    title: 'Book Your Laundry Service 🧺',
                    description: 'Let me show you how to book laundry service step by step! Note: The booking form only appears after you select a date.',
                    side: 'bottom'
                }
            },
            {
                element: '.date-time-picker',
                popover: {
                    title: 'Step 1: Choose Date First 📅',
                    description: 'IMPORTANT: You must select a date first! The booking form will appear after you choose an available date (shown in green).',
                    side: 'bottom'
                }
            }
        ];

        // Add conditional steps that only show if form is visible
        const bookingForm = document.getElementById('booking-form');
        if (bookingForm && !bookingForm.classList.contains('hidden')) {
            steps.push(
                {
                    element: '.pickup-delivery-options',
                    popover: {
                        title: 'Step 2: Choose Service Type 🚚',
                        description: 'Great! Now that you\'ve selected a date, choose how you want pickup and delivery handled.',
                        side: 'bottom'
                    }
                },
                {
                    element: '.branch-selector',
                    popover: {
                        title: 'Step 3: Select Your Branch 🏪',
                        description: 'Choose the laundry branch closest to you. Each branch has its own services and pricing.',
                        side: 'bottom'
                    }
                },
                {
                    element: '.service-selector',
                    popover: {
                        title: 'Step 4: Select Services 🧺',
                        description: 'Choose your item type (clothes, comforter, shoes) and select the services you need. Prices update automatically.',
                        side: 'top'
                    }
                },
                {
                    element: '.booking-summary',
                    popover: {
                        title: 'Step 5: Review & Submit 💰',
                        description: 'Review your booking details and total cost. Add any special instructions, then submit your booking!',
                        side: 'top'
                    }
                }
            );
        }

        this.startTour(steps, 'user-booking-form');
    }

    // User History Tour
    startUserHistoryTour() {
        if (!this.isInitialized) return;

        const steps = [
            {
                element: '.history-header',
                popover: {
                    title: 'Your Booking History 📚',
                    description: 'View all your past and current laundry bookings. Track your order history and spending.',
                    side: 'bottom'
                }
            },
            {
                element: '.history-filters',
                popover: {
                    title: 'Filter Your History 🔍',
                    description: 'Filter by date ranges (All Time, Today, This Week, This Month, Custom) or search by order details.',
                    side: 'bottom'
                }
            },
            {
                element: '.booking-cards',
                popover: {
                    title: 'Booking Cards 📋',
                    description: 'Each card shows booking details: date, status, services, total cost, and branch information.',
                    side: 'top'
                }
            }
        ];

        this.startTour(steps, 'user-history');
    }

    // Admin Dashboard Tour
    startAdminDashboardTour() {
        if (!this.isInitialized) return;

        const steps = [
            {
                element: 'body',
                popover: {
                    title: 'Welcome to Admin Dashboard! 👋',
                    description: 'This is your main control center for managing the laundry business. Let me show you around!',
                    side: 'bottom'
                }
            },
            {
                element: '.stats-overview',
                popover: {
                    title: 'Business Overview 📊',
                    description: 'These cards show key metrics: total bookings, revenue, pending orders, and customer count.',
                    side: 'bottom'
                }
            },
            {
                element: '.quick-actions',
                popover: {
                    title: 'Quick Actions ⚡',
                    description: 'Access frequently used features like creating bookings, managing users, and viewing reports.',
                    side: 'bottom'
                }
            },
            {
                element: '.recent-activity',
                popover: {
                    title: 'Recent Activity 📋',
                    description: 'Monitor recent bookings, status changes, and customer activities in real-time.',
                    side: 'top'
                }
            }
        ];

        this.startTour(steps, 'admin-dashboard');
    }

    // Admin Users Management Tour
    startAdminUsersManagementTour() {
        if (!this.isInitialized) return;

        const steps = [
            {
                element: 'body',
                popover: {
                    title: 'User Management System 👥',
                    description: 'Here you can view, search, and manage all customer accounts. Let me show you the features!',
                    side: 'bottom'
                }
            },
            {
                element: '.users-stats',
                popover: {
                    title: 'User Statistics 📊',
                    description: 'Quick overview of total users, active customers, new registrations, and user activity.',
                    side: 'bottom'
                }
            },
            {
                element: '.users-filters',
                popover: {
                    title: 'Search & Filter Users 🔍',
                    description: 'Search users by name, email, or phone. Filter by registration date or activity status.',
                    side: 'bottom'
                }
            },
            {
                element: '.users-table',
                popover: {
                    title: 'Users Table 📋',
                    description: 'View all user details, booking history, and account status. Click on any user to see their full profile.',
                    side: 'top'
                }
            }
        ];

        this.startTour(steps, 'admin-users');
    }

    // Admin Pricing Management Tour
    startAdminPricingTour() {
        if (!this.isInitialized) return;

        const steps = [
            {
                element: 'body',
                popover: {
                    title: 'Pricing Management 💰',
                    description: 'Manage your laundry services and product pricing. Set different prices for different item types!',
                    side: 'bottom'
                }
            },
            {
                element: '.pricing-tabs',
                popover: {
                    title: 'Service Categories 📂',
                    description: 'Switch between different item types: Clothes, Comforters, and Shoes. Each has its own pricing structure.',
                    side: 'bottom'
                }
            },
            {
                element: '.services-section',
                popover: {
                    title: 'Services Management 🧺',
                    description: 'Add, edit, or remove laundry services. Set prices and descriptions for each service type.',
                    side: 'bottom'
                }
            },
            {
                element: '.products-section',
                popover: {
                    title: 'Products Management 🛍️',
                    description: 'Manage additional products like detergents, fabric softeners, and other add-ons.',
                    side: 'top'
                }
            }
        ];

        this.startTour(steps, 'admin-pricing');
    }

    // Admin Analytics Tour
    startAdminAnalyticsTour() {
        if (!this.isInitialized) return;

        const steps = [
            {
                element: 'body',
                popover: {
                    title: 'Business Analytics 📈',
                    description: 'Track your business performance with detailed analytics and reports. Make data-driven decisions!',
                    side: 'bottom'
                }
            },
            {
                element: '.analytics-filters',
                popover: {
                    title: 'Date Range Filters 📅',
                    description: 'Select different time periods to analyze: today, week, month, or custom date ranges.',
                    side: 'bottom'
                }
            },
            {
                element: '.revenue-chart',
                popover: {
                    title: 'Revenue Analytics 💰',
                    description: 'Track daily, weekly, and monthly revenue trends. See which services generate the most income.',
                    side: 'bottom'
                }
            },
            {
                element: '.booking-analytics',
                popover: {
                    title: 'Booking Analytics 📊',
                    description: 'Monitor booking volumes, completion rates, and customer behavior patterns.',
                    side: 'top'
                }
            }
        ];

        this.startTour(steps, 'admin-analytics');
    }

    // Generic tour starter
    startTour(steps, tourKey) {
        if (!this.driverInstance) {
            console.warn('Driver not initialized');
            return;
        }

        // Filter out steps for elements that don't exist
        const validSteps = steps.filter(step => {
            if (step.element === 'body') return true;
            const element = document.querySelector(step.element);
            if (!element) {
                console.warn(`Element not found: ${step.element}`);
                return false;
            }
            return true;
        });

        if (validSteps.length === 0) {
            console.warn('No valid elements found for tour');
            // Fallback tour
            validSteps.push({
                element: 'body',
                popover: {
                    title: 'Welcome! 👋',
                    description: 'Welcome to the laundry management system. Explore the interface to get familiar with the features.',
                    side: 'bottom'
                }
            });
        }

        try {
            this.driverInstance.setSteps(validSteps);
            this.driverInstance.drive();
            
            // Store tour key for completion tracking
            this.driverInstance.tourKey = tourKey;
            
            console.log(`✅ ${tourKey} tour started`);
        } catch (error) {
            console.error(`❌ Failed to start ${tourKey} tour:`, error);
        }
    }

    // Tour completion tracking
    isCompleted(tourKey) {
        const completed = JSON.parse(localStorage.getItem('completedTours') || '[]');
        return completed.includes(tourKey);
    }

    markTourAsCompleted(tourKey) {
        const completed = JSON.parse(localStorage.getItem('completedTours') || '[]');
        if (!completed.includes(tourKey)) {
            completed.push(tourKey);
            localStorage.setItem('completedTours', JSON.stringify(completed));
            console.log(`✅ Tour ${tourKey} marked as completed`);
        }
    }

    resetTours() {
        localStorage.removeItem('completedTours');
        console.log('✅ All tours reset');
    }

    // Demo setup functions




    cleanupDemo() {
        try {
            console.log('🧹 Cleaning up demo...');
            
            // Reset form if there's a clear button
            const clearButtons = [
                '#clear-form',
                '#clear-selection', 
                '.clear-form-btn',
                '.reset-form',
                'button[data-action="clear"]'
            ];
            
            for (const selector of clearButtons) {
                const button = document.querySelector(selector);
                if (button) {
                    button.click();
                    console.log(`✅ Clicked clear button: ${selector}`);
                    break;
                }
            }
            
            // Hide booking forms if they were shown
            const formsToHide = [
                '#booking-form',
                '.booking-form-container',
                '.admin-booking-form',
                '.user-booking-form'
            ];
            
            formsToHide.forEach(selector => {
                const form = document.querySelector(selector);
                if (form && !form.classList.contains('hidden')) {
                    form.classList.add('hidden');
                    console.log(`✅ Hidden form: ${selector}`);
                }
            });
            
            // Reset any checked options that weren't default
            document.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked').forEach(input => {
                if (!input.defaultChecked) {
                    input.checked = false;
                    input.dispatchEvent(new Event('change'));
                }
            });
            
            // Reset select elements to first option
            document.querySelectorAll('select').forEach(select => {
                if (select.selectedIndex !== 0) {
                    select.selectedIndex = 0;
                    select.dispatchEvent(new Event('change'));
                }
            });
            
            // Clear any selected calendar dates
            document.querySelectorAll('.calendar-day.selected, .date-selected, .time-slot.selected').forEach(element => {
                element.classList.remove('selected');
            });
            
            // Clear any input fields that were filled during demo
            document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="date"], textarea').forEach(input => {
                if (input.value && !input.defaultValue) {
                    input.value = input.defaultValue || '';
                    input.dispatchEvent(new Event('change'));
                }
            });
            
            // Remove any demo-added classes
            document.querySelectorAll('.demo-selected, .tour-selected, .highlighted').forEach(element => {
                element.classList.remove('demo-selected', 'tour-selected', 'highlighted');
            });
            
            console.log('✅ Demo cleanup complete');
        } catch (error) {
            console.warn('Demo cleanup failed:', error);
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new TourSystem());
} else {
    new TourSystem();
}

export default TourSystem;