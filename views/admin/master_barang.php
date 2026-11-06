<?php
// views/admin/master_barang.php

require_once 'classes/Barang.php';

$db = new Database();
$conn = $db->getConnection();
$barangModel = new Barang($conn);

$barangs = $barangModel->getAll();
$satuans = $barangModel->getSatuans();

// Pre-fill edit mode if requested
$editItem = null;
if (isset($_GET['edit_id'])) {
    $editItem = $barangModel->getById((int)$_GET['edit_id']);
}

require_once 'views/layouts/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="color: var(--black-forest); margin: 0;">Master Data Barang</h2>
    <a href="index.php?page=master_barang#barang-form" class="btn-primary" style="text-decoration: none; font-size: 0.9rem;">+ Add New Item</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
        Action completed successfully!
    </div>
<?php elseif (isset($_GET['err']) && $_GET['err'] === 'fk_constraint'): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
        Cannot delete item: It is already referenced in transaction logs (Pengadaan / Penjualan).
    </div>
<?php endif; ?>

<!-- Data Table -->
<div style="overflow-x: auto; background: var(--white); border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: var(--olive-leaf); color: var(--white);">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Jenis</th>
                <th style="padding: 12px; text-align: left;">Nama Barang</th>
                <th style="padding: 12px; text-align: left;">Satuan</th>
                <th style="padding: 12px; text-align: right;">Harga</th>
                <th style="padding: 12px; text-align: center;">Stok</th>
                <th style="padding: 12px; text-align: center;">Status</th>
                <th style="padding: 12px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($barangs)): ?>
                <?php foreach ($barangs as $row): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?= htmlspecialchars($row['no']) ?></td>
                        
                        <td style="padding: 10px;">
                            <?php 
                                $jenisLabel = ['H' => 'Hewan', 'F' => 'Makanan', 'P' => 'Perlengkapan'];
                                echo htmlspecialchars($jenisLabel[$row['jenis']] ?? $row['jenis']);
                            ?>
                        </td>
                        <td style="padding: 10px; font-weight: bold; color: var(--black-forest);"><?= htmlspecialchars($row['nama']) ?></td>
                        <td style="padding: 10px;"><?= htmlspecialchars($row['nama_satuan']) ?></td>
                        <td style="padding: 10px; text-align: right;">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        
                        <!-- NEW STOK COLUMN -->
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: <?= $row['stok'] > 0 ? '#28a745' : '#dc3545' ?>; color: white; padding: 3px 8px; border-radius: 12px; font-weight: bold; font-size: 0.85rem;">
                                <?= htmlspecialchars($row['stok']) ?>
                            </span>
                        </td>
                        
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: <?= $row['status'] === 'Active' ? '#17a2b8' : '#6c757d' ?>; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.8rem;">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        
                        <td style="padding: 10px; text-align: center;">
                            <a href="index.php?page=master_barang&edit_id=<?= $row['no'] ?>#barang-form" style="color: var(--olive-leaf); margin-right: 10px; font-weight: bold; text-decoration: none;">Edit</a>
                            <a href="index.php?action=delete_barang&id=<?= $row['no'] ?>" onclick="return confirm('Delete this item?');" style="color: #d9534f; font-weight: bold; text-decoration: none;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="padding: 15px; text-align: center;">No barang records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add / Edit Form Card -->
<div id="barang-form" style="background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px;">
    <h3 style="color: var(--black-forest); margin-top: 0; border-bottom: 2px solid var(--cornsilk); padding-bottom: 10px;">
        <?= $editItem ? 'Edit Barang (ID: ' . $editItem['idbarrang'] . ')' : 'Add New Barang' ?>
    </h3>

    <form action="index.php?action=<?= $editItem ? 'update_barang' : 'create_barang' ?>" method="POST">
        <?php if ($editItem): ?>
            <input type="hidden" name="idbarrang" value="<?= $editItem['idbarrang'] ?>">
        <?php endif; ?>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Nama Barang</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($editItem['nama'] ?? '') ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Jenis</label>
                <select name="jenis" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="H" <?= (isset($editItem['jenis']) && $editItem['jenis'] === 'H') ? 'selected' : '' ?>>Hewan (H)</option>
                    <option value="F" <?= (isset($editItem['jenis']) && $editItem['jenis'] === 'F') ? 'selected' : '' ?>>Makanan (F)</option>
                    <option value="P" <?= (isset($editItem['jenis']) && $editItem['jenis'] === 'P') ? 'selected' : '' ?>>Perlengkapan (P)</option>
                </select>
            </div>
            
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Satuan</label>
                <select name="idsatuan" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <?php foreach ($satuans as $satuan): ?>
                        <option value="<?= $satuan['idsatuan'] ?>" <?= (isset($editItem['idsatuan']) && $editItem['idsatuan'] == $satuan['idsatuan']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($satuan['nama_satuan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Harga (Rp)</label>
                <input type="number" name="harga" value="<?= htmlspecialchars($editItem['harga'] ?? 0) ?>" required min="0" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Status</label>
                <select name="status" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="1" <?= (!isset($editItem) || $editItem['STATUS'] == 1) ? 'selected' : '' ?>>Active (1)</option>
                    <option value="0" <?= (isset($editItem) && $editItem['STATUS'] == 0) ? 'selected' : '' ?>>Inactive (0)</option>
                </select>
            </div>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="padding: 10px 20px; font-weight: bold;">
                <?= $editItem ? 'Update Barang' : 'Save Barang' ?>
            </button>
            <?php if ($editItem): ?>
                <a href="index.php?page=master_barang" style="margin-left: 10px; color: #666; text-decoration: none;">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php require_once 'views/layouts/footer.php'; ?>