<?php
$basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== ''
    ? '/' . APP_ROOT_DIR_NAME
    : '';
?>

<?php require __DIR__ . '/../common/header.php'; ?>

<main class="bg-slate-50 min-h-screen">

    <section class="bg-slate-950 text-white border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 py-12 grid lg:grid-cols-2 gap-8 items-center">
            <div>
                <p class="text-blue-400 font-semibold mb-3">Create an event</p>
                <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                    Host your event on Mix Max
                </h1>
                <p class="text-slate-300 mt-4 max-w-xl">
                    Submit your event details, venue information, and ticket pricing.
                    Our team will review it before it becomes public.
                </p>
            </div>

            <div class="bg-white/10 border border-white/10 rounded-2xl p-6">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-bold text-blue-400">24-48h</p>
                        <p class="text-sm text-slate-300">Review time</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-400">Free</p>
                        <p class="text-sm text-slate-300">To submit</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-pink-400">Easy</p>
                        <p class="text-sm text-slate-300">Ticket setup</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form method="POST" action="<?= $basePath ?>/events" enctype="multipart/form-data" class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid lg:grid-cols-[1fr_360px] gap-8 items-start">

            <div class="space-y-6">

                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-5">Event Details</h2>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Event Title *</label>
                            <input name="title" required type="text" class="w-full form-input"
                                placeholder="e.g., Summer Music Festival 2026">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Artist / Organizer *</label>
                            <input name="artist" required type="text" class="w-full form-input"
                                placeholder="e.g., Arctic Wolves">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Category *</label>
                            <select name="category" required class="w-full form-input">
                                <option value="concert">Concert</option>
                                <option value="sports">Sports</option>
                                <option value="theater">Theater</option>
                                <option value="comedy">Comedy</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Description *</label>
                            <textarea name="description" required rows="4" class="w-full form-input"
                                placeholder="Describe your event..."></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Event Image</label>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-slate-500 mb-1">Upload from device</p>
                                    <input name="eventImageFile" type="file" accept="image/*" class="w-full form-input">
                                </div>

                                <div>
                                    <p class="text-xs text-slate-500 mb-1">Or paste image URL</p>
                                    <input name="imageUrl" type="url" class="w-full form-input"
                                        placeholder="https://example.com/event-image.jpg">
                                </div>
                            </div>

                            <p class="text-xs text-slate-500 mt-2">
                                Optional. You can upload an image, paste a link, or leave it empty.
                            </p>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-5">Date & Time</h2>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Date *</label>
                            <input name="date" required type="date" class="w-full form-input">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Start Time *</label>
                            <input name="startTime" required type="time" class="w-full form-input">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">End Time *</label>
                            <input name="endTime" required type="time" class="w-full form-input">
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-5">Venue Information</h2>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Venue Name *</label>
                            <input name="venueName" required type="text" class="w-full form-input"
                                placeholder="Bell Centre">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Address *</label>
                            <input name="address" required type="text" class="w-full form-input"
                                placeholder="1909 Avenue des Canadiens-de-Montréal">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">City *</label>
                            <input name="city" required type="text" class="w-full form-input" placeholder="Montreal">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Capacity *</label>
                            <input name="capacity" required type="number" min="1" class="w-full form-input"
                                placeholder="500">
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-5">Tickets</h2>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Section *</label>
                            <input name="ticketSection" required type="text" class="w-full form-input"
                                placeholder="General">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Price *</label>
                            <input name="ticketPrice" required type="number" min="0" step="0.01"
                                class="w-full form-input" placeholder="50.00">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Quantity *</label>
                            <input name="ticketQuantity" required type="number" min="1" class="w-full form-input"
                                placeholder="100">
                        </div>
                    </div>
                </section>

            </div>

            <aside class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sticky top-24">
                <h2 class="text-xl font-bold mb-3">Submit for Review</h2>
                <p class="text-sm text-slate-600 mb-5">
                    Your event will be marked as pending until an admin approves it.
                </p>

                <label class="flex items-start gap-3 text-sm text-slate-700 mb-5">
                    <input type="checkbox" required class="mt-1">
                    <span>I confirm that all event information is accurate.</span>
                </label>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl">
                    Submit Event
                </button>
            </aside>

        </div>
    </form>

</main>

<?php require __DIR__ . '/../common/js-scripts.php'; ?>
<?php require __DIR__ . '/../common/footer.php'; ?>