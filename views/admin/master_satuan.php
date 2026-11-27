<?php
$db = new Database();
$conn = $db->getConnection();

$allSatuans = $conn->query("SELECT `no` AS idsatuan, nama AS nama_satuan, status FROM view_satuan ORDER BY `no` ASC")->fetchAll();
$activeSatuans = array_filter($allSatuans, fn($s) => $s['status'] === 'Active');
$inactiveSatuans = array_filter($allSatuans, fn($s) => $s['status'] !== 'Active');

$editSatuan = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    foreach ($allSatuans as $s) {
        if ((int)$s['idsatuan'] === $editId) {
            $editSatuan = $s;
            break;
        }
    }
}

require_once 'views/layouts/header.php';
?>

<h2 style="margin-top:0; color: var(--black-forest);">Master Satuan</h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php elseif (isset($_GET['err'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
<?php endif; ?>

<div class="card" id="satuan-form">
    <div class="card-header"><?= $editSatuan ? 'Edit Satuan #' . (int)$editSatuan['idsatuan'] : 'Add New Satuan' ?></div>
    <form action="index.php?action=<?= $editSatuan ? 'update_satuan' : 'create_satuan' ?>" method="POST">
        <?php if ($editSatuan): ?>
            <input type="hidden" name="idsatuan" value="<?= (int)$editSatuan['idsatuan'] ?>">
        <?php endif; ?>
        <div class="form-group" style="max-width: 420px;">
            <label>Nama Satuan (e.g. Ekor, Pcs, Pack, Gram, Liter)</label>
            <input type="text" name="nama_satuan" value="<?= htmlspecialchars($editSatuan['nama_satuan'] ?? '') ?>" required>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editSatuan ? 'Update Satuan' : 'Save Satuan' ?></button>
        <?php if ($editSatuan): ?>
            <a href="index.php?page=master_satuan" class="btn btn-warning">Cancel Edit</a>
        <?php endif; ?>
    </form>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">Active Satuan (<?= count($activeSatuans) ?>)</div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Satuan</th>
                        <th>Status</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activeSatuans)): ?>
                        <tr><td colspan="4" style="text-align:center;">No active satuan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($activeSatuans as $s): ?>
                            <tr>
                                <td>#<?= (int)$s['idsatuan'] ?></td>
                                <td><strong><?= htmlspecialchars($s['nama_satuan']) ?></strong></td>
                                <td><span class="status-text status-active">Active</span></td>
                                <td style="text-align:center; white-space:nowrap;">
                                    <a href="index.php?page=master_satuan&edit_id=<?= (int)$s['idsatuan'] ?>#satuan-form" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="index.php?action=toggle_satuan_status&id=<?= (int)$s['idsatuan'] ?>&status=0" class="btn btn-danger btn-sm">Deactivate</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Inactive Satuan (<?= count($inactiveSatuans) ?>)</div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Satuan</th>
                        <th>Status</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inactiveSatuans)): ?>
                        <tr><td colspan="4" style="text-align:center;">No inactive satuan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inactiveSatuans as $s): ?>
                            <tr>
                                <td>#<?= (int)$s['idsatuan'] ?></td>
                                <td><?= htmlspecialchars($s['nama_satuan']) ?></td>
                                <td><span class="status-text status-inactive">Inactive</span></td>
                                <td style="text-align:center; white-space:nowrap;">
                                    <a href="index.php?action=toggle_satuan_status&id=<?= (int)$s['idsatuan'] ?>&status=1" class="btn btn-success btn-sm">Activate</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>