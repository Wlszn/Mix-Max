<form method="post" action="<?= $basePath ?>/events" class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-3xl font-bold mb-6">Create an Event</h1>

    <input name="title" placeholder="Event title" class="border p-2 w-full mb-4" required>

    <input name="artist" placeholder="Artist / Organizer" class="border p-2 w-full mb-4" required>

    <textarea name="description" placeholder="Description" class="border p-2 w-full mb-4"></textarea>

    <input name="venueId" placeholder="Venue ID" class="border p-2 w-full mb-4" required>

    <input type="date" name="date" class="border p-2 w-full mb-4" required>

    <input type="time" name="startTime" class="border p-2 w-full mb-4" required>

    <input type="time" name="endTime" class="border p-2 w-full mb-4" required>

    <input name="imageUrl" placeholder="Image URL" class="border p-2 w-full mb-4">

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Submit Event
    </button>
</form>