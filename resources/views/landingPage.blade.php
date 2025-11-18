<x-layout>
    <x-slot:title>Landing Page</x-slot:title>

    <div class="min-h-screen flex flex-col">
        <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold">This is a placeholder for the views</h1>
        </header>

        <main class="grow container mx-auto p-4">
            <section class="text-center my-8">
                <h2 class="text-2xl font-semibold mb-4">Discover Our Features</h2>
                <p class="mb-6">Explore the amazing features we offer to make your experience unforgettable.</p>
                <a href="#features" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Learn More</a>
            </section>

            <section id="features" class="my-8">
                <h2 class="text-2xl font-semibold mb-4 text-center">Features</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="border p-4 rounded shadow">
                        <h3 class="font-bold mb-2">Feature One</h3>
                        <p>Detail about feature one that highlights its benefits and uses.</p>
                    </div>
                    <div class="border p-4 rounded shadow">
                        <h3 class="font-bold mb-2">Feature Two</h3>
                        <p>Detail about feature two that highlights its benefits and uses.</p>
                    </div>
                    <div class="border p-4 rounded shadow">
                        <h3 class="font-bold mb-2">Feature Three</h3>
                        <p>Detail about feature three that highlights its benefits and uses.</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-gray-800 text-white p-4 text-center">
            &copy; {{ date('Y') }} Your Company. All rights reserved.
        </footer>
    </div>
</x-layout>
