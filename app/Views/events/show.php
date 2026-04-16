<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title'] ?? 'Event Details') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <a href="/events" class="btn btn-secondary mb-3">Back to Events</a>

    <div class="card shadow-sm">
        <?php if (!empty($event['imageUrl'])): ?>
            <img src="<?= htmlspecialchars($event['imageUrl']) ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>">
        <?php endif; ?>

        <div class="card-body">
            <h1><?= htmlspecialchars($event['title']) ?></h1>
            <p><strong>Artist:</strong> <?= htmlspecialchars($event['artist']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($event['date']) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($event['startTime']) ?> - <?= htmlspecialchars($event['endTime']) ?></p>

            <?php if (!empty($event['venueName'])): ?>
                <p><strong>Venue:</strong> <?= htmlspecialchars($event['venueName']) ?></p>
            <?php endif; ?>

            <?php if (!empty($event['address'])): ?>
                <p><strong>Address:</strong> <?= htmlspecialchars($event['address']) ?></p>
            <?php endif; ?>

            <?php if (!empty($event['city'])): ?>
                <p><strong>City:</strong> <?= htmlspecialchars($event['city']) ?></p>
            <?php endif; ?>

            <?php if (!empty($event['description'])): ?>
                <hr>
                <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>