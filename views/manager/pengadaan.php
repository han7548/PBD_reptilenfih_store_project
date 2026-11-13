<?php
// views/manager/pengadaan.php

require_once 'classes/Pengadaan.php';

$db = new Database();
$conn = $db->getConnection();
$pengadaanModel = new Pengadaan($conn);

$vendors = $pengadaanModel->getActiveVendors();
$barangs = $pengadaanModel->getActiveBarang();
$pendingPOs = $pengadaanModel->getPendingPengadaan();
$penerimaanDetails = $pengadaanModel->getPenerimaanDetailsForRetur();

$poCart = $_SESSION['po_cart'] ?? [];
$rcvCart = $_SESSION['rcv_cart'] ?? [];
$returCart = $_SESSION['retur_cart'] ?? [];

require_once 'views/layouts/header.php';
?>

<h2 style="margin-top:0; color: var(--black-forest);">Inventory Management</h2>
<p style="color: #666; margin-bottom: 25px;">Build Procurement, Receiving, and Retur transactions using temporary session carts.</p>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php elseif (isset($_GET['err'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
<?php endif; ?>

<!-- ========================================== -->
<!-- MODULE 1: PENGADAAN (Order to Vendor)      -->
<!-- ========================================== -->
<div class="card" style="border-top: 4px solid var(--olive-leaf);">
    <div class="card-header">1. Pengadaan (Order to Vendor)</div>
    
    <div style="display: flex; gap: 20px;">
        <!-- Add to Cart Form -->
        <div style="flex: 1;">
            <form action="index.php?action=add_po_cart" method="POST">
                <div class="form-group">
                    <label>Pilih Vendor</label>
                    <select name="vendor_id" required>
                        <option value="">-- Choose Vendor --</option>
                        <?php foreach ($vendors as $v): ?>
                            <option value="<?= $v['idvendor'] ?>" <?= (isset($_SESSION['active_vendor_id']) && $_SESSION['active_vendor_id'] == $v['idvendor']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['nama_vendor']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Pilih Barang</label>
                    <select name="idbarang" id="poItemSelect" onchange="autoFillPoPrice()" required>
                        <option value="">-- Choose Item --</option>
                        <?php foreach ($barangs as $b): ?>
                            <option value="<?= $b['idbarang'] ?>" data-nama="<?= htmlspecialchars($b['nama']) ?>" data-harga="<?= $b['harga'] ?>">
                                <?= htmlspecialchars($b['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="nama_barang" id="poHiddenNama">
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Satuan</label>
                        <input type="number" name="harga" id="poInputHarga" required readonly style="background: #eee;">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Qty Order</label>
                        <input type="number" name="qty" min="1" value="1" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">+ Add to PO Cart</button>
            </form>
        </div>

        <!-- PO Session Cart -->
        <div style="flex: 1.5; background: var(--bg-light); padding: 15px; border-radius: 8px;">
            <strong>Pengadaan Cart</strong>
            <table style="margin-top: 10px; font-size: 0.9rem;">
                <thead><tr><th>Item</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr></thead>
                <tbody>
                    <?php 
                    $poSub = 0;
                    if(empty($poCart)) echo '<tr><td colspan="4" style="text-align:center;">Cart empty</td></tr>';
                    foreach($poCart as $id => $c): 
                        $total = $c['qty'] * $c['harga']; $poSub += $total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nama']) ?></td>
                            <td><?= $c['qty'] ?></td>
                            <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
                            <td><a href="index.php?action=remove_po_cart&id=<?= $id ?>" style="color:var(--danger); text-decoration:none;">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if(!empty($poCart)): ?>
                <div style="margin-top:15px; text-align:right;">
                    <strong>Total Nilai: Rp <?= number_format($poSub + round($poSub*0.11), 0, ',', '.') ?></strong> (Inc. PPN)
                </div>
                <form action="index.php?action=checkout_po" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="vendor_id" value="<?= $_SESSION['active_vendor_id'] ?>">
                    <button type="submit" class="btn btn-success" style="width:100%;">Submit Pengadaan Database</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODULE 2: PENERIMAAN (Receive Shipment)    -->
<!-- ========================================== -->
<div class="card" style="border-top: 4px solid var(--black-forest);">
    <div class="card-header">2. Penerimaan (Receive & Auto-Fill Cart)</div>
    
    <div style="display: flex; gap: 20px;">
        <!-- Load PO Form -->
        <div style="flex: 1;">
            <form action="index.php?action=load_rcv_cart" method="POST">
                <div class="form-group">
                    <label>Pilih Pending Order untuk di-Terima</label>
                    <select name="idpengadaan" required>
                        <option value="">-- Pending Orders --</option>
                        <?php foreach ($pendingPOs as $po): ?>
                            <option value="<?= $po['idpengadaan'] ?>" <?= (isset($_SESSION['active_pengadaan_id']) && $_SESSION['active_pengadaan_id'] == $po['idpengadaan']) ? 'selected' : '' ?>>
                                PO #<?= $po['idpengadaan'] ?> - <?= htmlspecialchars($po['nama_vendor']) ?> (<?= $po['status'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Load Items to Receiving Cart >></button>
            </form>
        </div>

        <!-- RCV Session Cart (Editable) -->
        <div style="flex: 1.5; background: var(--bg-light); padding: 15px; border-radius: 8px;">
            <strong>Penerimaan Cart (Loaded from PO)</strong>
            <table style="margin-top: 10px; font-size: 0.9rem;">
                <thead><tr><th>Item</th><th>Expected</th><th>Received Qty</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if(empty($rcvCart)) echo '<tr><td colspan="4" style="text-align:center;">Load a PO first</td></tr>';
                    foreach($rcvCart as $id => $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nama']) ?></td>
                            <td style="color:#666;">Max: <?= $c['max_qty'] ?></td>
                            <td>
                                <!-- Form to update received qty -->
                                <form action="index.php?action=update_rcv_qty" method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="idbarang" value="<?= $id ?>">
                                    <input type="number" name="qty" value="<?= $c['qty'] ?>" min="1" max="<?= $c['max_qty'] ?>" style="width:60px; padding:4px;">
                                    <button type="submit" style="padding:4px 8px;">Update</button>
                                </form>
                            </td>
                            <td><a href="index.php?action=remove_rcv_cart&id=<?= $id ?>" style="color:var(--danger); text-decoration:none;">Drop</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if(!empty($rcvCart)): ?>
                <form action="index.php?action=checkout_rcv" method="POST" style="margin-top: 15px;">
                    <button type="submit" class="btn btn-success" style="width:100%;">Confirm Receipt (Add to Stock)</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODULE 3: RETUR (Damaged Goods)            -->
<!-- ========================================== -->
<div class="card" style="border-top: 4px solid var(--danger);">
    <div class="card-header" style="color: var(--danger);">3. Retur (Kembalikan Barang Rusak)</div>
    
    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;">
            <form action="index.php?action=add_retur_cart" method="POST">
                <div class="form-group">
                    <label>Pilih Barang yang Sudah Diterima</label>
                    <select name="iddetail_penerimaan" id="returSelect" onchange="autoFillRetur()" required>
                        <option value="">-- Choose Received Item --</option>
                        <?php foreach ($penerimaanDetails as $pd): ?>
                            <option value="<?= $pd['no_detail_terima'] ?>" 
                                    data-idpenerimaan="<?= $pd['no_penerimaan'] ?>" 
                                    data-nama="<?= htmlspecialchars($pd['nama_barang']) ?>"
                                    data-max="<?= $pd['jumlah_terima'] ?>">
                                Penerimaan #<?= $pd['no_penerimaan'] ?> - <?= htmlspecialchars($pd['nama_barang']) ?> (Rcv: <?= $pd['jumlah_terima'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <input type="hidden" name="idpenerimaan" id="returIdPenerimaan">
                <input type="hidden" name="nama_barang" id="returNama">

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Qty Retur</label>
                        <input type="number" name="qty" id="returQty" min="1" required>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label>Alasan (Cacat/Mati)</label>
                        <input type="text" name="alasan" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-danger" style="width:100%;">+ Add to Retur Cart</button>
            </form>
        </div>

        <div style="flex: 1.5; background: var(--bg-light); padding: 15px; border-radius: 8px;">
            <strong>Retur Cart</strong>
            <table style="margin-top: 10px; font-size: 0.9rem;">
                <thead><tr><th>Item</th><th>Qty</th><th>Alasan</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if(empty($returCart)) echo '<tr><td colspan="4" style="text-align:center;">Cart empty</td></tr>';
                    foreach($returCart as $id => $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nama']) ?></td>
                            <td><?= $c['qty'] ?></td>
                            <td><?= htmlspecialchars($c['alasan']) ?></td>
                            <td><a href="index.php?action=remove_retur_cart&id=<?= $id ?>" style="color:var(--danger); text-decoration:none;">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if(!empty($returCart)): ?>
                <form action="index.php?action=checkout_retur" method="POST" style="margin-top: 15px;">
                    <button type="submit" class="btn btn-danger" style="width:100%;">Process Retur (Deduct Stock)</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function autoFillPoPrice() {
    const opt = document.getElementById('poItemSelect').options[document.getElementById('poItemSelect').selectedIndex];
    if(opt.value) {
        document.getElementById('poInputHarga').value = opt.getAttribute('data-harga');
        document.getElementById('poHiddenNama').value = opt.getAttribute('data-nama');
    }
}

function autoFillRetur() {
    const opt = document.getElementById('returSelect').options[document.getElementById('returSelect').selectedIndex];
    if(opt.value) {
        document.getElementById('returIdPenerimaan').value = opt.getAttribute('data-idpenerimaan');
        document.getElementById('returNama').value = opt.getAttribute('data-nama');
        document.getElementById('returQty').max = opt.getAttribute('data-max');
    }
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>