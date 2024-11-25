<?php
// Configurations
$directory = "."; // Current directory
$files = array_diff(scandir($directory), array('.', '..')); // Exclude . and .. entries

// Filter files based on search query
$search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
if ($search) {
    $files = array_filter($files, function ($file) use ($search) {
        return stripos($file, $search) !== false;
    });
}

// Sorting logic
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
if ($sort === 'name') {
    natsort($files);
} elseif ($sort === 'size') {
    usort($files, function ($a, $b) use ($directory) {
        return filesize($directory . '/' . $a) - filesize($directory . '/' . $b);
    });
} elseif ($sort === 'date') {
    usort($files, function ($a, $b) use ($directory) {
        return filemtime($directory . '/' . $b) - filemtime($directory . '/' . $a);
    });
}

// Helper function to generate thumbnails or icons
function getThumbnail($file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    // File type categories
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
    $isoExtensions = ['iso'];
    $documentExtensions = ['xls', 'xlsx', 'ppt', 'pptx', 'pdf'];

    if (in_array($ext, $imageExtensions)) {
        // Image files
        return '<img src="images/image-icon.svg" alt="Image File" class="img-thumbnail" style="width: 50px; height: auto;">';
    } elseif (in_array($ext, $isoExtensions)) {
        // ISO files
        return '<img src="images/iso-icon.svg" alt="ISO File" class="img-thumbnail" style="width: 50px; height: auto;">';
    } elseif (in_array($ext, $documentExtensions)) {
        // Document files
        return '<img src="images/document-icon.svg" alt="Document File" class="img-thumbnail" style="width: 50px; height: auto;">';
    } else {
        // Other files
        return '<img src="images/file-icon.svg" alt="Generic File" class="img-thumbnail" style="width: 50px; height: auto;">';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directory Listing</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Directory Listing</h1>

        <!-- Search Bar -->
        <div class="input-group mb-3">
            <form class="d-flex w-100" method="GET" action="">
                <input type="text" name="search" class="form-control" placeholder="Search files..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
        </div>

        <!-- File Table -->
        <table class="table table-bordered table-striped table-responsive">
            <thead class="table-dark">
                <tr>
                    <th><a href="?sort=name" class="text-white">Name</a></th>
                    <th>Thumbnail</th>
                    <th><a href="?sort=size" class="text-white">Size</a></th>
                    <th><a href="?sort=date" class="text-white">Last Modified</a></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><a href="<?= $file ?>" target="_blank"><?= $file ?></a></td>
                        <td><?= getThumbnail($file) ?></td>
                        <td><?= is_file($directory . '/' . $file) ? filesize($directory . '/' . $file) . ' bytes' : '-' ?></td>
                        <td><?= date("Y-m-d H:i:s", filemtime($directory . '/' . $file)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="js/script.js"></script>
</body>
</html>