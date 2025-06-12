<?php
include 'includes/config.php';
include 'includes/database.php';
include 'includes/admin-header.php';

$db = new Database();

// Get all posts
$posts = $db->query(
    "SELECT post_id as id, post_name as title, post_slug as slug, 
            CASE WHEN post_status = 1 THEN 'published' ELSE 'draft' END as status,
            post_status as status_id
     FROM post 
     ORDER BY post_date DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .media-picker-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .media-picker-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .media-picker-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close-media-picker {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }

        .media-picker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            padding: 15px;
        }

        .media-item {
            cursor: pointer;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .media-item:hover {
            border-color: #007bff;
            transform: scale(1.05);
        }

        .media-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 3px;
        }

        .media-item p {
            margin: 5px 0 0;
            font-size: 12px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .selected-image-preview img {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <main>
        <div class="header-actions">
            <h1>Posts</h1>
            <a href="edit-post.php" class="btn-primary">
                <i class="fas fa-plus"></i> Create a new post
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $post['status']; ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button onclick="editPost(<?php echo $post['id']; ?>)" class="btn-edit">
                                    Edit
                                </button>
                                <button onclick="deletePost(<?php echo $post['id']; ?>)" class="btn-delete">
                                    Delete
                                </button>
                                <a href="../page.php?slug=<?php echo $post['slug']; ?>" target="_blank" class="btn-view">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Modal functionality
        const postModal = document.getElementById('postModal');
        const span = document.getElementsByClassName('close')[0];

        function editPost(postId) {
            window.location.href = `edit-post.php?id=${postId}`;
        }

        // Close modal when clicking the X
        span.onclick = function() {
            postModal.style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == postModal) {
                postModal.style.display = 'none';
            }
        }

        // Auto-generate slug from title
        document.getElementById('title').addEventListener('input', function() {
            const title = this.value;
            const slug = title.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g, '');
            document.getElementById('slug').value = slug;
        });

        function deletePost(postId) {
            if (confirm('Weet je zeker dat je dit bericht wilt verwijderen?')) {
                fetch(`includes/post_actions.php?action=delete&id=${postId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting post');
                    });
            }
        }

        // Media Picker functionality
        function openMediaPicker(button) {
            const mediaPicker = document.createElement('div');
            mediaPicker.className = 'media-picker-modal';
            const originalBlock = button.closest('.form-group');

            // Create modal
            mediaPicker.innerHTML = `
                <div class="media-picker-content">
                    <div class="media-picker-header">
                        <h3>Select Media</h3>
                        <button type="button" class="close-media-picker">×</button>
                    </div>
                    <div class="media-picker-grid">
                        <div class="loading">Loading media...</div>
                    </div>
                </div>
            `;

            document.body.appendChild(mediaPicker);

            // Add event listener for close button
            const closeButton = mediaPicker.querySelector('.close-media-picker');
            closeButton.addEventListener('click', function() {
                mediaPicker.remove();
            });

            // Close modal when clicking outside
            mediaPicker.addEventListener('click', function(e) {
                if (e.target === mediaPicker) {
                    mediaPicker.remove();
                }
            });

            // Fetch media from the server
            fetch('includes/get_media.php')
                .then(response => response.json())
                .then(media => {
                    const mediaGrid = mediaPicker.querySelector('.media-picker-grid');
                    mediaGrid.innerHTML = media.map(item => `
                        <div class="media-item" data-url="${item.filepath}">
                            <img src="${item.filepath}" alt="${item.filename}">
                            <p>${item.filename}</p>
                        </div>
                    `).join('');

                    // Add event listeners for media items
                    mediaPicker.querySelectorAll('.media-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const imageUrl = this.dataset.url;
                            const hiddenInput = originalBlock.querySelector('.selected-image');
                            const preview = originalBlock.querySelector('.selected-image-preview');

                            hiddenInput.value = imageUrl;
                            preview.innerHTML = `<img src="${imageUrl}" alt="Selected image">`;
                            mediaPicker.remove();
                        });
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    const mediaGrid = mediaPicker.querySelector('.media-picker-grid');
                    mediaGrid.innerHTML = `<div class="error">Error loading media. Please try again.</div>`;
                });
        }

        // Add event listener for media button
        document.querySelector('.select-media').addEventListener('click', function(e) {
            e.preventDefault();
            openMediaPicker(this);
        });
    </script>
</body>
</html>