<?php
$db = new Database();
$conn = $db->getConnection();

$penjualans = $conn->query("SELECT * FROM view_penjualan_riwayat")->fetchAll();
$detailRows = $conn->query("SELECT * FROM view_detail_penjualan")->fetchAll();

$itemsBySale = [];
foreach ($detailRows as $d) {
    $itemsBySale[(int)$d['no_penjualan']][] = $d;
}

require_once 'views/layouts/header.php';
?>

<h2 style="margin-top:0; color: var(--black-forest);">Daftar Penjualan</h2>
<p style="color: var(--text-muted); margin-bottom: 20px;">All recorded sales transactions and their itemized breakdown.</p>

<div class="card">
    <div class="card-header">Riwayat Penjualan (<?= count($penjualans) ?>)</div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Items Sold</th>
                    <th style="text-align:right;">Margin</th>
                    <th style="text-align:right;">Subtotal</th>
                    <th style="text-align:right;">PPN</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($penjualans)): ?>
                    <tr><td colspan="8" style="text-align:center;">No sales recorded yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($penjualans as $row):
                        $saleId = (int)$row['no_penjualan'];
                        $saleItems = $itemsBySale[$saleId] ?? [];
                    ?>
                        <tr>
                            <td><strong>#<?= $saleId ?></strong></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars($row['kasir']) ?></td>
                            <td>
                                <?php if (empty($saleItems)): ?>
                                    <span style="color: var(--text-muted);">-</span>
                                <?php else: ?>
                                    <ul style="margin:0; padding-left:16px; font-size:0.86rem;">
                                        <?php foreach ($saleItems as $it): ?>
                                            <li>
                                                <strong><?= htmlspecialchars($it['nama_barang']) ?></strong>
                                                &times; <?= (int)$it['jumlah'] ?>
                                                (@ Rp <?= number_format((int)$it['harga_satuan'], 0, ',', '.') ?>)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;"><?= htmlspecialchars($row['margin_persen']) ?>%</td>
                            <td style="text-align:right;">Rp <?= number_format((int)$row['subtotal_nilai'], 0, ',', '.') ?></td>
                            <td style="text-align:right;">Rp <?= number_format((int)$row['ppn'], 0, ',', '.') ?></td>
                            <td style="text-align:right;"><strong>Rp <?= number_format((int)$row['total_nilai'], 0, ',', '.') ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>