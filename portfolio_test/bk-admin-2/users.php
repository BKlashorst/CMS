<?php
include 'includes/config.php';
include 'includes/database.php';
include 'includes/admin-header.php';

// Create database instance
$database = new Database();

// Fetch all users
$users = $database->query(
    "SELECT u.*, r.role_name 
     FROM user u 
     LEFT JOIN role r ON u.role_id = r.role_id 
     ORDER BY u.user_id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

// Fetch all roles for the dropdown
$roles = $database->query(
    "SELECT * FROM role ORDER BY role_name"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    <link rel="stylesheet" href="public/css/style-admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <main>
        <div class="header-actions">
            <h1>Users</h1>
            <button class="btn-primary" onclick="openAddUserModal()">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_mail']); ?></td>
                        <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                        <td>
                            <button class="btn-edit" onclick="editUser(<?php echo $user['user_id']; ?>)">
                                Edit
                            </button>
                            <button class="btn-delete" onclick="deleteUser(<?php echo $user['user_id']; ?>)">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add User Modal -->
        <div id="userModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Add New User</h2>
                <form id="userForm" action="includes/user_actions.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="user_name">Username:</label>
                        <input type="text" id="user_name" name="user_name" required>
                    </div>
                    <div class="form-group">
                        <label for="user_mail">Email:</label>
                        <input type="email" id="user_mail" name="user_mail" required>
                    </div>
                    <div class="form-group">
                        <label for="user_password">Password:</label>
                        <input type="password" id="user_password" name="user_password" required>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Role:</label>
                        <select id="role_id" name="role_id" required>
                            <option value="">Select a role</option>
                            <?php foreach($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Modal functionality
        const modal = document.getElementById('userModal');
        const span = document.getElementsByClassName('close')[0];

        function openAddUserModal() {
            modal.style.display = 'block';
            document.getElementById('userForm').reset();
            document.getElementById('userForm').action = 'includes/user_actions.php';
            document.querySelector('input[name="action"]').value = 'add';
        }

        span.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function editUser(userId) {
            // Implement edit functionality
            console.log('Edit user:', userId);
        }

        function deleteUser(userId) {
            if(confirm('Are you sure you want to delete this user?')) {
                window.location.href = `includes/user_actions.php?action=delete&id=${userId}`;
            }
        }
    </script>
</body>
</html>
