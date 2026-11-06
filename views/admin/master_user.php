<?php
// views/admin/master_user.php

require_once 'classes/User.php';

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);

$users = $userModel->getAll();
$roles = $userModel->getRoles();

$editUser = null;
if (isset($_GET['edit_id'])) {
    $editUser = $userModel->getById((int)$_GET['edit_id']);
}

require_once 'views/layouts/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="color: var(--black-forest); margin: 0;">Master Data User</h2>
    <a href="index.php?page=master_user#user-form" class="btn-primary" style="text-decoration: none; font-size: 0.9rem;">+ Add New User</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
        User action completed successfully!
    </div>
<?php elseif (isset($_GET['err'])): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
        <?php 
            if ($_GET['err'] === 'self_delete') echo "Security Error: You cannot delete your own logged-in account!";
            elseif ($_GET['err'] === 'fk_constraint') echo "Cannot delete user: Account has linked transaction records.";
            else echo "Invalid input data provided.";
        ?>
    </div>
<?php endif; ?>

<!-- Users Data Table -->
<div style="overflow-x: auto; background: var(--white); border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: var(--olive-leaf); color: var(--white);">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Username</th>
                <th style="padding: 12px; text-align: left;">Role</th>
                <th style="padding: 12px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $row): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?= htmlspecialchars($row['no']) ?></td>
                        <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($row['username']) ?></td>
                        <td style="padding: 10px;">
                            <span style="background: var(--black-forest); color: var(--cornsilk); padding: 3px 8px; border-radius: 12px; font-size: 0.8rem;">
                                <?= htmlspecialchars($row['nama_role']) ?>
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <a href="index.php?page=master_user&edit_id=<?= $row['no'] ?>#user-form" style="color: var(--olive-leaf); margin-right: 10px; font-weight: bold;">Edit</a>
                            <?php if ($row['no'] != $_SESSION['user_id']): ?>
                                <a href="index.php?action=delete_user&id=<?= $row['no'] ?>" onclick="return confirm('Delete user account <?= htmlspecialchars($row['username']) ?>?');" style="color: #d9534f; font-weight: bold;">Delete</a>
                            <?php else: ?>
                                <span style="color: #ccc; font-size: 0.85rem;">(Current User)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="padding: 15px; text-align: center;">No user accounts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add / Edit Form Card -->
<div id="user-form" style="background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 500px;">
    <h3 style="color: var(--black-forest); margin-top: 0; border-bottom: 2px solid var(--cornsilk); padding-bottom: 10px;">
        <?= $editUser ? 'Edit User (ID: ' . $editUser['iduser'] . ')' : 'Add New User Account' ?>
    </h3>

    <form action="index.php?action=<?= $editUser ? 'update_user' : 'create_user' ?>" method="POST">
        <?php if ($editUser): ?>
            <input type="hidden" name="iduser" value="<?= $editUser['iduser'] ?>">
        <?php endif; ?>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($editUser['username'] ?? '') ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Password</label>
            <input type="password" name="password" <?= $editUser ? '' : 'required' ?> placeholder="<?= $editUser ? 'Leave blank to keep existing password' : 'Enter password' ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Role</label>
            <select name="idrole" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="">-- Select Role --</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['idrole'] ?>" <?= (isset($editUser['idrole']) && $editUser['idrole'] == $role['idrole']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['nama_role']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="padding: 10px 20px; font-weight: bold;">
                <?= $editUser ? 'Update User' : 'Save User' ?>
            </button>
            <?php if ($editUser): ?>
                <a href="index.php?page=master_user" style="margin-left: 10px; color: #666; text-decoration: none;">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php require_once 'views/layouts/footer.php'; ?>