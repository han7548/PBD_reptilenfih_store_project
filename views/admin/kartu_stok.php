<?php


require_once 'classes/KartuStok.php';

$db = new Database();
$conn = $db->getConnection();
$kartuStokModel = new KartuStok($conn);

$filterBarangId = (int)($_GET['filter_barang'] ?? 0);

$logs = $kartuStokModel->getLogs($filterBarangId);
$barangs = $kartuStokModel->getBarangList();

require_once 'views/layouts/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 style="color: var(--black-forest); margin-bottom: 5px;">Kartu Stok (Audit Log Mutasi)</h2>
        
    </div>

    <!-- Filter Form by Barang -->
    <form action="index.php" method="GET" style="display: flex; gap: 10px; align-items: center;">
        <input type="hidden" name="page" value="kartu_stok">
        <select name="filter_barang" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-weight: bold; background: var(--white);">
            <option value="0">-- All Items Ledger --</option>
            <?php foreach ($barangs as $b): ?>
                <option value="<?= $b['idbarang'] ?>" <?= $filterBarangId === (int)$b['idbarang'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['nama']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($filterBarangId > 0): ?>
            <a href="index.php?page=kartu_stok" style="color: #d9534f; text-decoration: none; font-size: 0.9rem; font-weight: bold;">Reset Filter</a>
        <?php endif; ?>
    </form>
</div>

<!-- Stock Movement Audit Table -->
<div style="overflow-x: auto; background: var(--white); border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: var(--black-forest); color: var(--cornsilk);">
                <th style="padding: 12px; text-align: left;">ID Log</th>
                <th style="padding: 12px; text-align: left;">Timestamp</th>
                <th style="padding: 12px; text-align: left;">Nama Barang</th>
                <th style="padding: 12px; text-align: center;">Jenis Transaksi</th>
                <th style="padding: 12px; text-align: right;">Masuk (+)</th>
                <th style="padding: 12px; text-align: right;">Keluar (-)</th>
                <th style="padding: 12px; text-align: right;">Saldo Stok</th>
                <th style="padding: 12px; text-align: center;">Ref. Transaksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $row): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; color: #777;">#<?= htmlspecialchars($row['no']) ?></td>
                        <td style="padding: 10px; color: #555;"><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td style="padding: 10px; font-weight: bold; color: var(--black-forest);"><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: <?= $row['jenis_transaksi'] === 'I' ? '#28a745' : '#dc3545' ?>; color: white; padding: 3px 10px; border-radius: 12px; font-weight: bold; font-size: 0.8rem;">
                                <?= htmlspecialchars($row['label_transaksi']) ?>
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: right; color: #28a745; font-weight: bold;">
                            <?= $row['masuk'] > 0 ? '+' . $row['masuk'] : '-' ?>
                        </td>
                        <td style="padding: 10px; text-align: right; color: #dc3545; font-weight: bold;">
                            <?= $row['keluar'] > 0 ? '-' . $row['keluar'] : '-' ?>
                        </td>
                        <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 1.05rem; color: var(--olive-leaf);">
                            <?= htmlspecialchars($row['saldo_stok']) ?>
                        </td>
                        <td style="padding: 10px; text-align: center; color: #666; font-size: 0.9rem;">
                            #<?= htmlspecialchars($row['no_transaksi_ref']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="padding: 20px; text-align: center; color: #888;">
                        No movement log entries found for this selection.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'views/layouts/footer.php'; ?>