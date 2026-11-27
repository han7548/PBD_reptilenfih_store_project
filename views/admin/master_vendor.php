<?php
$db = new Database();
$conn = $db->getConnection();

$allVendors = $conn->query("SELECT `no` AS idvendor, nama_vendor, badan_hukum, status FROM view_vendor ORDER BY `no` ASC")->fetchAll();
$activeVendors = array_filter($allVendors, fn($v) => $v['status'] === 'Active');
$inactiveVendors = array_filter($allVendors, fn($v) => $v['status'] !== 'Active');

$editVendor = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    foreach ($allVendors as $v) {
        if ((int)$v['idvendor'] === $editId) {
            $editVendor = $v;
            break;
        }
    }
}

require_once 'views/layouts/header.php';
?>

<h2 style="margin-top:0; color: var(--black-forest);">Master Vendor</h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php elseif (isset($_GET['err'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
<?php endif; ?>

<div class="card" id="vendor-form">
    <div class="card-header"><?= $editVendor ? 'Edit Vendor #' . (int)$editVendor['idvendor'] : 'Add New Vendor' ?></div>
    <form action="index.php?action=<?= $editVendor ? 'update_vendor' : 'create_vendor' ?>" method="POST">
        <?php if ($editVendor): ?>
            <input type="hidden" name="idvendor" value="<?= (int)$editVendor['idvendor'] ?>">
            <input type="hidden" name="status" value="<?= htmlspecialchars($editVendor['status']) ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Nama Vendor</label>
                <input type="text" name="nama_vendor" value="<?= htmlspecialchars($editVendor['nama_vendor'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Badan Hukum</label>
                <?php $curBadan = $editVendor['badan_hukum'] ?? 'P'; ?>
                <select name="badan_hukum" required>
                    <option value="P" <?= $curBadan === 'P' ? 'selected' : '' ?>>PT / Perseorangan (P)</option>
                    <option value="C" <?= $curBadan === 'C' ? 'selected' : '' ?>>CV (C)</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><?= $editVendor ? 'Update Vendor' : 'Save Vendor' ?></button>
        <?php if ($editVendor): ?>
            <a href="index.php?page=master_vendor" class="btn btn-warning">Cancel Edit</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="card-header">Active Vendors (<?= count($activeVendors) ?>)</div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Vendor</th>
                    <th>Badan Hukum</th>
                    <th>Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activeVendors)): ?>
                    <tr><td colspan="5" style="text-align:center;">No active vendors.</td></tr>
                <?php else: ?>
                    <?php foreach ($activeVendors as $v): ?>
                        <tr>
                            <td>#<?= (int)$v['idvendor'] ?></td>
                            <td><strong><?= htmlspecialchars($v['nama_vendor']) ?></strong></td>
                            <td><?= htmlspecialchars($v['badan_hukum']) ?></td>
                            <td><span class="status-text status-active">Active</span></td>
                            <td style="text-align:center; white-space:nowrap;">
                                <a href="index.php?page=master_vendor&edit_id=<?= (int)$v['idvendor'] ?>#vendor-form" class="btn btn-warning btn-sm">Edit</a>
                                <a href="index.php?action=toggle_vendor_status&id=<?= (int)$v['idvendor'] ?>&status=I" class="btn btn-danger btn-sm">Deactivate</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Inactive Vendors (<?= count($inactiveVendors) ?>)</div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Vendor</th>
                    <th>Badan Hukum</th>
                    <th>Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inactiveVendors)): ?>
                    <tr><td colspan="5" style="text-align:center;">No inactive vendors.</td></tr>
                <?php else: ?>
                    <?php foreach ($inactiveVendors as $v): ?>
                        <tr>
                            <td>#<?= (int)$v['idvendor'] ?></td>
                            <td><?= htmlspecialchars($v['nama_vendor']) ?></td>
                            <td><?= htmlspecialchars($v['badan_hukum']) ?></td>
                            <td><span class="status-text status-inactive">Inactive</span></td>
                            <td style="text-align:center; white-space:nowrap;">
                                <a href="index.php?action=toggle_vendor_status&id=<?= (int)$v['idvendor'] ?>&status=A" class="btn btn-success btn-sm">Activate</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>