<?php
// views/kasir/pos.php
require_once 'classes/Penjualan.php';
$db = new Database();
$conn = $db->getConnection();
$penjualanModel = new Penjualan($conn);

$items = $penjualanModel->getAvailableBarang();
$activeMargin = $penjualanModel->getActiveMargin();
$cart = $_SESSION['pos_cart'] ?? [];

require_once 'views/layouts/header.php';
?>

<h2 style="margin-top:0; color: var(--black-forest);">Point of Sale (POS)</h2>
<p style="color: #666; margin-bottom: 20px;">Build your order in the cart. Stock is deducted only upon final checkout.</p>


<div style="background: var(--cornsilk); border-left: 4px solid var(--olive-leaf); padding: 12px 15px; margin-bottom: 25px; border-radius: 4px;">
    <strong style="color: var(--black-forest);">Active Profit Margin: <?= htmlspecialchars($activeMargin['persen'] ?? 0) ?>%</strong>
    <span style="color: #555; margin-left: 10px; font-size: 0.9rem;">(All item prices below have been automatically marked up by this percentage)</span>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php elseif (isset($_GET['err'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
<?php endif; ?>

<div style="display: flex; gap: 20px; align-items: flex-start;">
    
    <!-- LEFT SIDE: Catalog & Add to Cart Form -->
    <div class="card" style="flex: 1;">
        <div class="card-header">Select Item</div>
        
        <form action="index.php?action=add_to_cart" method="POST">
            <div class="form-group">
                <label>Pilih Barang</label>
                <!-- JS Auto-fill triggers on change -->
                <select name="idbarang" id="itemSelect" onchange="autoFillItemDetails()" required>
                    <option value="">-- Choose Item --</option>
                    <?php foreach ($items as $item): ?>
                        <?php if($item['stock'] > 0): ?>
                            <option value="<?= $item['idbarang'] ?>" 
                                    data-nama="<?= htmlspecialchars($item['nama']) ?>" 
                                    data-harga="<?= $item['harga_jual'] ?>" 
                                    data-stock="<?= $item['stock'] ?>">
                                <?= htmlspecialchars($item['nama']) ?> (Stok: <?= $item['stock'] ?>) - Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Hidden fields to pass data to controller -->
            <input type="hidden" name="nama_barang" id="hiddenNama">
            <input type="hidden" name="max_stock" id="hiddenStock">

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Harga Satuan (Auto-filled)</label>
                    <input type="number" name="harga" id="inputHarga" required readonly style="background: #eee;">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Qty</label>
                    <input type="number" name="qty" id="inputQty" min="1" value="1" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">+ Add to Cart</button>
        </form>
    </div>

    <!-- RIGHT SIDE: Active Session Cart -->
    <div class="card" style="flex: 1.2;">
        <div class="card-header">Current Cart</div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Subtotal</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    if (empty($cart)): ?>
                        <tr><td colspan="4" style="text-align:center; color:#999;">Cart is empty</td></tr>
                    <?php else: ?>
                        <?php foreach ($cart as $id => $c): 
                            $sub = $c['qty'] * $c['harga'];
                            $grandTotal += $sub;
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($c['nama']) ?></strong><br>
                                <small>Rp <?= number_format($c['harga'], 0, ',', '.') ?></small>
                            </td>
                            <td style="text-align:center;"><?= $c['qty'] ?></td>
                            <td style="text-align:right;">Rp <?= number_format($sub, 0, ',', '.') ?></td>
                            <td style="text-align:center;">
                                <a href="index.php?action=remove_from_cart&id=<?= $id ?>" style="color: var(--danger); text-decoration:none; font-weight:bold;">&times; Remove</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($cart)): 
            $ppn = round($grandTotal * 0.11);
            $totalPay = $grandTotal + $ppn;
        ?>
        <div style="background: var(--bg-light); padding: 15px; border-radius: 5px; margin-top: 20px;">
            <div style="display: flex; justify-content: space-between;"><span>Subtotal:</span> <strong>Rp <?= number_format($grandTotal, 0, ',', '.') ?></strong></div>
            <div style="display: flex; justify-content: space-between;"><span>PPN (11%):</span> <strong>Rp <?= number_format($ppn, 0, ',', '.') ?></strong></div>
            <hr>
            <div style="display: flex; justify-content: space-between; font-size: 1.2rem; color: var(--black-forest);">
                <span><strong>Total:</strong></span> <strong>Rp <?= number_format($totalPay, 0, ',', '.') ?></strong>
            </div>
        </div>

        <form action="index.php?action=checkout_pos" method="POST" style="margin-top: 15px;">
            <input type="hidden" name="idmargin" value="<?= $activeMargin['idmargin_penjualan'] ?? 0 ?>">
            <button type="submit" class="btn btn-success" style="width: 100%;">Complete Checkout</button>
        </form>
        <?php endif; ?>

    </div>
</div>

<script>
// Auto-fill logic for the Price and hidden fields
function autoFillItemDetails() {
    const select = document.getElementById('itemSelect');
    const option = select.options[select.selectedIndex];
    
    if(option.value !== "") {
        document.getElementById('inputHarga').value = option.getAttribute('data-harga');
        document.getElementById('hiddenNama').value = option.getAttribute('data-nama');
        document.getElementById('hiddenStock').value = option.getAttribute('data-stock');
        document.getElementById('inputQty').max = option.getAttribute('data-stock');
    } else {
        document.getElementById('inputHarga').value = '';
    }
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>