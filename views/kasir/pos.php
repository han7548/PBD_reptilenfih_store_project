<?php
// views/kasir/pos.php

require_once 'classes/Penjualan.php';

$db = new Database();
$conn = $db->getConnection();
$penjualanModel = new Penjualan($conn);

$items = $penjualanModel->getAvailableBarang();
$activeMargin = $penjualanModel->getActiveMargin();

require_once 'views/layouts/header.php';
?>

<div style="margin-bottom: 25px;">
    <h2 style="color: var(--black-forest); margin-bottom: 5px;">Kasir Point of Sale (Multi-Item Checkout)</h2>
    <p style="color: #666; margin: 0;">Add multiple items to a single transaction receipt. Stock cards will update automatically for all items.</p>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
        Multi-item transaction processed successfully! Stock card updated automatically for all items.
    </div>
<?php elseif (isset($_GET['err'])): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
        Transaction Failed: <?= htmlspecialchars(urldecode($_GET['err'])) ?>
    </div>
<?php endif; ?>

<?php if (!$activeMargin): ?>
    <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <strong>Warning:</strong> No active margin setting found in `margin_penjualan`.
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 420px; gap: 25px; align-items: start;">
    
    <!-- Available Inventory List -->
    <div style="background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="color: var(--olive-leaf); margin-top: 0; border-bottom: 2px solid var(--cornsilk); padding-bottom: 10px;">Available Inventory</h3>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: var(--olive-leaf); color: var(--white);">
                        <th style="padding: 10px; text-align: left;">ID</th>
                        <th style="padding: 10px; text-align: left;">Item Name</th>
                        <th style="padding: 10px; text-align: left;">Unit</th>
                        <th style="padding: 10px; text-align: right;">Price</th>
                        <th style="padding: 10px; text-align: center;">Stock</th>
                        <th style="padding: 10px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;"><?= htmlspecialchars($item['idbarang']) ?></td>
                                <td style="padding: 10px; font-weight: bold; color: var(--black-forest);"><?= htmlspecialchars($item['nama']) ?></td>
                                <td style="padding: 10px;"><?= htmlspecialchars($item['nama_satuan']) ?></td>
                                <td style="padding: 10px; text-align: right;">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                <td style="padding: 10px; text-align: center;">
                                    <span style="background: <?= $item['stock'] > 0 ? '#28a745' : '#dc3545' ?>; color: white; padding: 2px 8px; border-radius: 10px; font-weight: bold; font-size: 0.85rem;">
                                        <?= htmlspecialchars($item['stock']) ?>
                                    </span>
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <?php if ($item['stock'] > 0 && $activeMargin): ?>
                                        <button type="button" 
                                                onclick="addToPosCart(<?= $item['idbarang'] ?>, '<?= htmlspecialchars(addslashes($item['nama'])) ?>', <?= $item['harga'] ?>, <?= $item['stock'] ?>)"
                                                style="background: var(--olive-leaf); color: white; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                            + Add
                                        </button>
                                    <?php else: ?>
                                        <button disabled style="background: #ccc; color: #666; border: none; padding: 5px 10px; border-radius: 4px; cursor: not-allowed;">
                                            Out of Stock
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="padding: 15px; text-align: center;">No active inventory available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Multi-Item Cart & Receipt -->
    <div style="background: var(--white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); position: sticky; top: 20px;">
        <h3 style="color: var(--black-forest); margin-top: 0; border-bottom: 2px solid var(--cornsilk); padding-bottom: 10px;">Transaction Receipt</h3>
        
        <form action="index.php?action=checkout_pos" method="POST" onsubmit="return validateCheckout()">
            <input type="hidden" name="idmargin" value="<?= htmlspecialchars($activeMargin['idmargin_penjualan'] ?? '') ?>">
            <input type="hidden" name="items_json" id="pos_items_json" value="[]">

            <!-- Cart Table -->
            <div style="max-height: 280px; overflow-y: auto; margin-bottom: 15px;">
                <table style="width: 100%; border-collapse: collapse;" id="pos_cart_table">
                    <thead>
                        <tr style="background: var(--black-forest); color: var(--cornsilk); font-size: 0.85rem;">
                            <th style="padding: 8px; text-align: left;">Item</th>
                            <th style="padding: 8px; text-align: center;">Qty</th>
                            <th style="padding: 8px; text-align: right;">Subtotal</th>
                            <th style="padding: 8px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="4" style="text-align: center; padding: 15px; color: #999;">Cart is empty. Click "+ Add" on items to build receipt.</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Total Calculation Breakdown -->
            <div style="background: var(--cornsilk); padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Grand Subtotal:</span>
                    <strong id="lbl_pos_subtotal">Rp 0</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>PPN (11%):</span>
                    <strong id="lbl_pos_ppn">Rp 0</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--olive-leaf); padding-top: 8px; font-size: 1.15rem; color: var(--black-forest);">
                    <strong>Total Pay:</strong>
                    <strong id="lbl_pos_total">Rp 0</strong>
                </div>
            </div>

            <button type="submit" id="btnPosCheckout" disabled class="btn-primary" 
                    style="width: 100%; padding: 12px; font-size: 1rem; font-weight: bold; opacity: 0.5; cursor: not-allowed;">
                Complete Multi-Item Checkout
            </button>
        </form>
    </div>
</div>

<script>
let posCart = [];

function addToPosCart(idbarang, nama, harga, maxStock) {
    const existingIndex = posCart.findIndex(item => item.idbarang === idbarang);
    
    if (existingIndex > -1) {
        if (posCart[existingIndex].jumlah < maxStock) {
            posCart[existingIndex].jumlah += 1;
        } else {
            alert(`Cannot add more. Stock limit for ${nama} is ${maxStock}.`);
        }
    } else {
        posCart.push({
            idbarang: idbarang,
            nama: nama,
            harga: harga,
            jumlah: 1,
            maxStock: maxStock
        });
    }
    
    renderPosCart();
}

function updateCartQty(index, newQty) {
    newQty = parseInt(newQty) || 1;
    if (newQty > posCart[index].maxStock) {
        alert(`Cannot exceed available stock (${posCart[index].maxStock})!`);
        posCart[index].jumlah = posCart[index].maxStock;
    } else if (newQty < 1) {
        posCart[index].jumlah = 1;
    } else {
        posCart[index].jumlah = newQty;
    }
    renderPosCart();
}

function removePosCartItem(index) {
    posCart.splice(index, 1);
    renderPosCart();
}

function renderPosCart() {
    const tbody = document.querySelector('#pos_cart_table tbody');
    tbody.innerHTML = '';
    let grandSubtotal = 0;

    if (posCart.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 15px; color: #999;">Cart is empty. Click "+ Add" on items to build receipt.</td></tr>';
        document.getElementById('btnPosCheckout').disabled = true;
        document.getElementById('btnPosCheckout').style.opacity = '0.5';
        document.getElementById('btnPosCheckout').style.cursor = 'not-allowed';
    } else {
        posCart.forEach((item, index) => {
            const itemSubtotal = item.harga * item.jumlah;
            grandSubtotal += itemSubtotal;

            tbody.innerHTML += `
                <tr style="border-bottom: 1px solid #eee; font-size: 0.9rem;">
                    <td style="padding: 8px;">
                        <strong>${item.nama}</strong><br>
                        <small style="color: #666;">Rp ${item.harga.toLocaleString('id-ID')}</small>
                    </td>
                    <td style="padding: 8px; text-align: center;">
                        <input type="number" min="1" max="${item.maxStock}" value="${item.jumlah}" 
                               onchange="updateCartQty(${index}, this.value)" 
                               style="width: 50px; padding: 4px; text-align: center; border: 1px solid #ccc; border-radius: 4px;">
                    </td>
                    <td style="padding: 8px; text-align: right; font-weight: bold;">
                        Rp ${itemSubtotal.toLocaleString('id-ID')}
                    </td>
                    <td style="padding: 8px; text-align: center;">
                        <button type="button" onclick="removePosCartItem(${index})" style="color: #d9534f; border: none; background: none; cursor: pointer; font-weight: bold; font-size: 1.1rem;">&times;</button>
                    </td>
                </tr>
            `;
        });

        const btn = document.getElementById('btnPosCheckout');
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    }

    const ppn = Math.round(grandSubtotal * 0.11);
    const grandTotal = grandSubtotal + ppn;

    document.getElementById('lbl_pos_subtotal').innerText = 'Rp ' + grandSubtotal.toLocaleString('id-ID');
    document.getElementById('lbl_pos_ppn').innerText = 'Rp ' + ppn.toLocaleString('id-ID');
    document.getElementById('lbl_pos_total').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
}

function validateCheckout() {
    if (posCart.length === 0) {
        alert("Cart is empty!");
        return false;
    }

    // Format array to match procedure expected JSON payload
    const payload = posCart.map(item => ({
        idbarang: item.idbarang,
        jumlah: item.jumlah,
        harga: item.harga
    }));

    document.getElementById('pos_items_json').value = JSON.stringify(payload);
    return true;
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>