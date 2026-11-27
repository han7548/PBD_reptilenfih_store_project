<?php


require_once 'classes/Barang.php';


$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT * FROM view_barang ORDER BY `no` ASC");
$stmt->execute();
$allBarangs = $stmt->fetchAll();

$activeBarangs = [];
$inactiveBarangs = [];
foreach ($allBarangs as $b) {
    if (strtoupper((string)$b['status']) === 'ACTIVE') {
        $activeBarangs[] = $b;
    } else {
        $inactiveBarangs[] = $b;
    }
}

$editItem = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    foreach ($allBarangs as $b) {
        if ((int)$b['no'] === $editId) {
            $editItem = $b;
            break;
        }
    }
}

$satuans = $conn->query("SELECT idsatuan, nama_satuan FROM satuan ORDER BY nama_satuan ASC")->fetchAll();

require_once 'views/layouts/header.php';
?>

<h2 style="margin-top:0; color: var(--black-forest);">Master Barang</h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php elseif (isset($_GET['err'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
<?php endif; ?>

<div class="card" id="barang-form">
    <div class="card-header"><?= $editItem ? 'Edit Barang #' . (int)$editItem['no'] : 'Add New Barang' ?></div>
    <form action="index.php?action=<?= $editItem ? 'update_barang' : 'create_barang' ?>" method="POST">
        <?php if ($editItem): ?>
            <input type="hidden" name="idbarang" value="<?= (int)$editItem['no'] ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Jenis</label>
                <select name="jenis" required>
                    <?php $curJenis = $editItem['jenis'] ?? 'P'; ?>
                    <option value="H" <?= $curJenis === 'H' ? 'selected' : '' ?>>Hewan (H)</option>
                    <option value="F" <?= $curJenis === 'F' ? 'selected' : '' ?>>Makanan (F)</option>
                    <option value="P" <?= $curJenis === 'P' ? 'selected' : '' ?>>Perlengkapan (P)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($editItem['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Satuan</label>
                <select name="idsatuan" required>
                    <?php foreach ($satuans as $s): ?>
                        <option value="<?= (int)$s['idsatuan'] ?>" <?= (isset($editItem['nama_satuan']) && $editItem['nama_satuan'] === $s['nama_satuan']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nama_satuan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Harga Dasar (Rp)</label>
                <input type="number" name="harga" min="0" value="<?= htmlspecialchars($editItem['harga'] ?? '') ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><?= $editItem ? 'Update Barang' : 'Save Barang' ?></button>
        <?php if ($editItem): ?>
            <a href="index.php?page=master_barang" class="btn btn-warning">Cancel Edit</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table 1: Active Barang -->
<div class="card">
    <div class="card-header">Active Barang (<?= count($activeBarangs) ?>)</div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th style="text-align:right;">Harga</th>
                    <th style="text-align:center;">Stok</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activeBarangs)): ?>
                    <tr><td colspan="8" style="text-align:center;">No active items.</td></tr>
                <?php else: ?>
                    <?php foreach ($activeBarangs as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row['no']) ?></td>
                            <td><?= htmlspecialchars($row['jenis']) ?></td>
                            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nama_satuan']) ?></td>
                            <td style="text-align:right;">Rp <?= number_format((int)$row['harga'], 0, ',', '.') ?></td>
                            <td style="text-align:center;"><strong><?= htmlspecialchars($row['stok']) ?></strong></td>
                            <td style="text-align:center;"><span class="status-text status-active">Active</span></td>
                            <td style="text-align:center; white-space:nowrap;">
                                <a href="index.php?page=master_barang&edit_id=<?= (int)$row['no'] ?>#barang-form" class="btn btn-warning btn-sm">Edit</a>
                                <a href="index.php?action=toggle_barang_status&id=<?= (int)$row['no'] ?>&status=0" class="btn btn-danger btn-sm">Deactivate</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Table 2: Inactive Barang (Same Tab) -->
<div class="card">
    <div class="card-header">Inactive Barang (<?= count($inactiveBarangs) ?>)</div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th style="text-align:right;">Harga</th>
                    <th style="text-align:center;">Stok</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inactiveBarangs)): ?>
                    <tr><td colspan="8" style="text-align:center;">No inactive items.</td></tr>
                <?php else: ?>
                    <?php foreach ($inactiveBarangs as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row['no']) ?></td>
                            <td><?= htmlspecialchars($row['jenis']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['nama_satuan']) ?></td>
                            <td style="text-align:right;">Rp <?= number_format((int)$row['harga'], 0, ',', '.') ?></td>
                            <td style="text-align:center;"><?= htmlspecialchars($row['stok']) ?></td>
                            <td style="text-align:center;"><span class="status-text status-inactive">Inactive</span></td>
                            <td style="text-align:center; white-space:nowrap;">
                                <a href="index.php?action=toggle_barang_status&id=<?= (int)$row['no'] ?>&status=1" class="btn btn-success btn-sm">Activate</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

