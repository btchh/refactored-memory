<x-layout>
    <x-slot:title>My Bookings</x-slot:title>
    <x-nav type="user" />
    
    <div class="min-h-screen bg-gray-100">
        <main class="container mx-auto p-4">
            <!-- Page Header -->
            <div class="flex justify-between items-center my-6">
                <h1 class="text-3xl font-bold text-gray-800">My Bookings</h1>
                <x-modules.button variant="success" onclick="showModal('createBookingModal')">
                    + Create Booking
                </x-modules.button>
            </div>

            <!-- Bookings List -->
            <x-modules.card>
                <div id="bookingsContainer">
                    <div class="text-center py-8">
                        <p class="text-gray-500">Loading bookings...</p>
                    </div>
                </div>
            </x-modules.card>
        </main>
    </div>

    <!-- Create Booking Modal -->
    <x-modules.modal id="createBookingModal" title="Create New Booking" size="lg">
        <form id="createBookingForm">
            @csrf
            <div class="space-y-4">
                <x-modules.input 
                    type="text" 
                    name="title" 
                    label="Title" 
                    placeholder="Enter booking title"
                    required
                />

                <x-modules.textarea 
                    name="description" 
                    label="Description" 
                    placeholder="Enter booking description"
                    rows="3"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-modules.input 
                        type="datetime-local" 
                        name="start_time" 
                        label="Start Time" 
                        required
                    />

                    <x-modules.input 
                        type="datetime-local" 
                        name="end_time" 
                        label="End Time" 
                        required
                    />
                </div>

                <x-modules.input 
                    type="text" 
                    name="location" 
                    label="Location" 
                    placeholder="Enter location"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-modules.input 
                        type="text" 
                        name="attendee_name" 
                        label="Attendee Name" 
                        placeholder="Enter attendee name"
                    />

                    <x-modules.input 
                        type="email" 
                        name="attendee_email" 
                        label="Attendee Email" 
                        placeholder="Enter attendee email"
                    />
                </div>

                <x-modules.input 
                    type="tel" 
                    name="attendee_phone" 
                    label="Attendee Phone" 
                    placeholder="Enter attendee phone"
                />

                <x-modules.textarea 
                    name="notes" 
                    label="Notes" 
                    placeholder="Additional notes"
                    rows="3"
                />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-modules.button type="button" variant="secondary" onclick="hideModal('createBookingModal')">
                    Cancel
                </x-modules.button>
                <x-modules.button type="submit" variant="success">
                    Create Booking
                </x-modules.button>
            </div>
        </form>
    </x-modules.modal>

    <!-- Edit Booking Modal -->
    <x-modules.modal id="editBookingModal" title="Edit Booking" size="lg">
        <form id="editBookingForm">
            @csrf
            <input type="hidden" id="edit_booking_id" name="booking_id">
            
            <div class="space-y-4">
                <x-modules.input 
                    type="text" 
                    name="title" 
                    id="edit_title"
                    label="Title" 
                    placeholder="Enter booking title"
                    required
                />

                <x-modules.textarea 
                    name="description" 
                    id="edit_description"
                    label="Description" 
                    placeholder="Enter booking description"
                    rows="3"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-modules.input 
                        type="datetime-local" 
                        name="start_time" 
                        id="edit_start_time"
                        label="Start Time" 
                        required
                    />

                    <x-modules.input 
                        type="datetime-local" 
                        name="end_time" 
                        id="edit_end_time"
                        label="End Time" 
                        required
                    />
                </div>

                <x-modules.input 
                    type="text" 
                    name="location" 
                    id="edit_location"
                    label="Location" 
                    placeholder="Enter location"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-modules.input 
                        type="text" 
                        name="attendee_name" 
                        id="edit_attendee_name"
                        label="Attendee Name" 
                        placeholder="Enter attendee name"
                    />

                    <x-modules.input 
                        type="email" 
                        name="attendee_email" 
                        id="edit_attendee_email"
                        label="Attendee Email" 
                        placeholder="Enter attendee email"
                    />
                </div>

                <x-modules.input 
                    type="tel" 
                    name="attendee_phone" 
                    id="edit_attendee_phone"
                    label="Attendee Phone" 
                    placeholder="Enter attendee phone"
                />

                <x-modules.textarea 
                    name="notes" 
                    id="edit_notes"
                    label="Notes" 
                    placeholder="Additional notes"
                    rows="3"
                />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-modules.button type="button" variant="secondary" onclick="hideModal('editBookingModal')">
                    Cancel
                </x-modules.button>
                <x-modules.button type="submit" variant="primary">
                    Update Booking
                </x-modules.button>
            </div>
        </form>
    </x-modules.modal>

    <!-- Cancel Booking Confirmation Modal -->
    <x-modules.modal id="cancelBookingModal" title="Cancel Booking" size="md">
        <div>
            <p class="text-gray-700 mb-4">Are you sure you want to cancel this booking?</p>
            <p class="text-sm text-gray-600 mb-2"><strong>Title:</strong> <span id="cancel_booking_title"></span></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Date:</strong> <span id="cancel_booking_date"></span></p>
            
            <input type="hidden" id="cancel_booking_id">
            
            <div class="mt-6 flex justify-end space-x-3">
                <x-modules.button type="button" variant="secondary" onclick="hideModal('cancelBookingModal')">
                    No, Keep It
                </x-modules.button>
                <x-modules.button type="button" variant="danger" onclick="confirmCancelBooking()">
                    Yes, Cancel Booking
                </x-modules.button>
            </div>
        </div>
    </x-modules.modal>

    <x-notifications />
</x-layout>
