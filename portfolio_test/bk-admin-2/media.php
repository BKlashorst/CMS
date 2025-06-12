<?php
include 'includes/config.php';
include 'includes/database.php';
include 'includes/admin-header.php';

$db = new Database();

// Get all media
$media = $db->query(
    "SELECT medi_id as id, medi_name as file_name, medi_url as file_url, medi_type as file_type, medi_uploaded as uploaded_at 
     FROM media 
     ORDER BY medi_uploaded DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Media Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <base href="/bk-admin-2/">
</head>
<body>
    <main>
        <h1>Media Library</h1>

        <div class="upload-container">
            <form id="mediaForm" action="includes/media_actions.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload">
                <div class="upload-zone" id="dropZone">
                    <div class="upload-content">
                        <svg class="upload-icon" viewBox="0 0 24 24" width="48" height="48">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        <p>Sleep je bestanden hierheen of</p>
                        <label for="fileInput" class="btn-primary">Kies een bestand</label>
                        <input type="file" name="media_file" id="fileInput" accept="image/*" required hidden>
                    </div>
                    <div class="upload-preview" id="uploadPreview" style="display: none;">
                        <img id="previewImage" src="/uploads/" alt="Preview">
                        <div class="upload-info">
                            <p id="fileName"></p>
                            <div class="upload-progress">
                                <div class="progress-bar" id="progressBar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="media-grid">
            <?php foreach ($media as $item): ?>
                <div class="media-item" data-id="<?php echo htmlspecialchars($item['id']); ?>" data-url="<?php echo htmlspecialchars($item['file_url']); ?>">
                    <img src="<?php echo htmlspecialchars($item['file_url']); ?>" 
                         alt="<?php echo htmlspecialchars($item['file_name']); ?>">
                    <button onclick="deleteMedia(<?php echo $item['id']; ?>)" class="btn-delete">
                        Delete
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            const uploadForm = document.getElementById('mediaForm');
            const uploadPreview = document.getElementById('uploadPreview');
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');
            const progressBar = document.getElementById('progressBar');

            // Drag and drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropZone.classList.add('dragover');
            }

            function unhighlight(e) {
                dropZone.classList.remove('dragover');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }

            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });

            function handleFiles(files) {
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        // Show preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.result;
                            fileName.textContent = file.name;
                            uploadPreview.style.display = 'flex';
                        }
                        reader.readAsDataURL(file);

                        // Simulate upload progress
                        let progress = 0;
                        const interval = setInterval(() => {
                            progress += 5;
                            progressBar.style.width = progress + '%';
                            if (progress >= 100) {
                                clearInterval(interval);
                                // Submit the form
                                uploadForm.submit();
                            }
                        }, 100);
                    } else {
                        alert('Alleen afbeeldingen zijn toegestaan.');
                    }
                }
            }
        });

        function deleteMedia(mediaId) {
            if (confirm('Are you sure you want to delete this media?')) {
                window.location.href = `includes/media_actions.php?action=delete&id=${mediaId}`;
            }
        }
    </script>
</body>
</html>
