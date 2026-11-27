<?php
$db = new Database();
$conn = $db->getConnection();

$roles = $conn->query("SELECT `no` AS idrole, nama_role, total_users FROM view_role ORDER BY `no` ASC")->fetchAll();

$editRole = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    foreach ($roles as $r) {
        if ((int)$r['idrole'] === $editId) {
            $editRole = $r;
            break;
        }
    }
}

require_once 'views/layouts/header.php';
?>

<h2 style="margin-top:0; color: var(--black-forest);">Master Role</h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php elseif (isset($_GET['err'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
<?php endif; ?>

<div class="grid-2">
    <div class="card" id="role-form">
        <div class="card-header"><?= $editRole ? 'Edit Role #' . (int)$editRole['idrole'] : 'Add New Role' ?></div>
        <form action="index.php?action=<?= $editRole ? 'update_role' : 'create_role' ?>" method="POST">
            <?php if ($editRole): ?>
                <input type="hidden" name="idrole" value="<?= (int)$editRole['idrole'] ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>Nama Role</label>
                <input type="text" name="nama_role" value="<?= htmlspecialchars($editRole['nama_role'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?= $editRole ? 'Update Role' : 'Save Role' ?></button>
            <?php if ($editRole): ?>
                <a href="index.php?page=master_role" class="btn btn-warning">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <div class="card-header">Registered Roles (<?= count($roles) ?>)</div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Role</th>
                        <th style="text-align:center;">Assigned Users</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $r): ?>
                        <tr>
                            <td>#<?= (int)$r['idrole'] ?></td>
                            <td><strong><?= htmlspecialchars($r['nama_role']) ?></strong></td>
                            <td style="text-align:center;"><?= (int)$r['total_users'] ?></td>
                            <td style="text-align:center;">
                                <a href="index.php?page=master_role&edit_id=<?= (int)$r['idrole'] ?>#role-form" class="btn btn-warning btn-sm">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>