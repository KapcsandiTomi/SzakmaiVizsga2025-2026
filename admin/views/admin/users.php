<?php
if (!isset($users)) {
    $users = [];
}
?>


<?php
$headerPath = __DIR__ . '/../templates/header.php';
$navbarPath = __DIR__ . '/../templates/navbar.php';
$footerPath = __DIR__ . '/../templates/footer.php';

require_once $headerPath;
require_once $navbarPath;
?>

<div class="admin-container">
    

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-container">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-container">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="admin-card">
        <h2><i class="fas fa-users"></i> Users Management</h2>
        
        <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if ($user['is_admin']): ?>
                                <span class="badge badge-success">Admin</span>
                            <?php else: ?>
                                <span class="badge badge-primary">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($user['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                                    <?php if (!$user['is_admin']): ?>
                                        <a href="index.php?page=users&action=make_admin&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-user-shield"></i> Grant Admin
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?page=users&action=remove_admin&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-user-minus"></i> Remove Admin
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="index.php?page=users&action=delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-user"></i> Current User</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <h4>No Users Found</h4>
                <p>There are no users in the database.</p>
            </div>
        <?php endif; ?>
    </div>
    
</div>

<?php require_once $footerPath; ?>