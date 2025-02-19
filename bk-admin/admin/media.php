<?php
include '../includes/config.php';
include '../includes/database.php';
include '../includes/header.php';

$db = new Database();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    $uploadDir = '../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['media'];
    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $fileUrl = '/Fontys/Personal%20project/CMS/bk-admin/uploads/' . $fileName;
        $db->query(
            "INSERT INTO media (file_name, file_url, file_type) VALUES (?, ?, ?)",
            [$fileName, $fileUrl, $file['type']]
        );
    }
}

// Get all media
$media = $db->query("SELECT * FROM media ORDER BY uploaded_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Media Library</title>
    <link rel="stylesheet" href="../public/css/style-admin.css">
</head>
<body>
    <main>
        <h1>Media Library</h1>

        <form class="upload-form" method="POST" enctype="multipart/form-data">
            <input type="file" name="media" accept="image/*" required>
            <button type="submit">Upload</button>
        </form>

        <div class="media-grid">
            <?php foreach ($media as $item): ?>
                <div class="media-item" data-url="<?php echo htmlspecialchars($item['file_url']); ?>">
                    <img src="<?php echo htmlspecialchars($item['file_url']); ?>" 
                         alt="<?php echo htmlspecialchars($item['file_name']); ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Modal for image selection -->
        <div id="imageModal">
            <div class="modal-content">
                <h2>Select Image</h2>
                <div class="media-grid">
                    <?php foreach ($media as $item): ?>
                        <div class="media-item" data-url="<?php echo htmlspecialchars($item['file_url']); ?>">
                            <img src="<?php echo htmlspecialchars($item['file_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['file_name']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button id="selectImage">Select</button>
                <button id="cancelSelection">Cancel</button>
            </div>
        </div>
    </main>

    <script>
        // Function to create an image selector
        function createImageSelector(callback) {
            const modal = document.getElementById('imageModal');
            const mediaItems = modal.querySelectorAll('.media-item');
            const selectBtn = document.getElementById('selectImage');
            const cancelBtn = document.getElementById('cancelSelection');
            let selectedUrl = null;

            // Show modal
            modal.style.display = 'block';

            // Handle image selection
            mediaItems.forEach(item => {
                item.addEventListener('click', () => {
                    mediaItems.forEach(i => i.classList.remove('selected'));
                    item.classList.add('selected');
                    selectedUrl = item.dataset.url;
                });
            });

            // Handle select button
            selectBtn.onclick = () => {
                modal.style.display = 'none';
                if (selectedUrl && callback) {
                    callback(selectedUrl);
                }
            };

            // Handle cancel button
            cancelBtn.onclick = () => {
                modal.style.display = 'none';
            };
        }

        // Example of how to use the selector
        window.selectImage = function(inputId) {
            createImageSelector(url => {
                document.getElementById(inputId).value = url;
            });
        };
    </script>
</body>
</html>
