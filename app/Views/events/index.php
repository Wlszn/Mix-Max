<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Events') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h1 class="mb-4">Upcoming Events</h1>

    <form method="get" action="/events" class="mb-4">
        <div class="row g-2">
            <div class="col-md-8">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by title, artist, or description"
                    value="<?= htmlspecialchars($search ?? '') ?>"
                >
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <?php if (empty($events)): ?>
        <div class="alert alert-warning">
            No events found.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($event['imageUrl'])): ?>
                            <img src="<?= htmlspecialchars($event['imageUrl']) ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>">
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                            <p class="card-text mb-1"><strong>Artist:</strong> <?= htmlspecialchars($event['artist']) ?></p>

                            <?php if (!empty($event['venueName'])): ?>
                                <p class="card-text mb-1"><strong>Venue:</strong> <?= htmlspecialchars($event['venueName']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($event['city'])): ?>
                                <p class="card-text mb-1"><strong>City:</strong> <?= htmlspecialchars($event['city']) ?></p>
                            <?php endif; ?>

                            <p class="card-text mb-1"><strong>Date:</strong> <?= htmlspecialchars($event['date']) ?></p>
                            <p class="card-text mb-2"><strong>Time:</strong> <?= htmlspecialchars($event['startTime']) ?> - <?= htmlspecialchars($event['endTime']) ?></p>

                            <?php if (!empty($event['description'])): ?>
                                <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer bg-white border-0">
                            <a href="/events/<?= (int)$event['eventId'] ?>" class="btn btn-outline-primary w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>