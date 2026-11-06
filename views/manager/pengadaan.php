<?php
// views/manager/pengadaan.php

require_once 'classes/Pengadaan.php';

$db = new Database();
$conn = $db->getConnection();
$pengadaanModel = new Pengadaan($conn);

$pengadaanList   = $pengadaanModel->getAllPengadaan();
$pengadaanDetails= $pengadaanModel->getPengadaanDetails();
$penerimaanList  = $pengadaanModel->getAllPenerimaan();
$penerimaanDetails = $pengadaanModel->getPenerimaanDetails();
$vendors         = $pengadaanModel->getActiveVendors();
$barangs         = $pengadaanModel->getActiveBarang();

// Group detail items by Pengadaan ID
$detailMap = [];
foreach ($pengadaanDetails as $d) {
    $detailMap[$d['no_pengadaan']][] = $d;
}

require_once 'views/layouts/header.php';
?>

<div style="margin-bottom: 25px;">
    <h2 style="color: var(--black-forest); margin-bottom: 5px;">Management Pengadaan & Penerimaan (Multi-Item Transactions)</h2>
    <p style="color: #666; margin: 0;">Order and receive multiple items under a single header transaction with JSON procedure processing.</p>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
        <?php 
            if ($_GET['msg'] === 'pengadaan_created') echo "Multi-item Pengadaan created successfully!";
            elseif ($_GET['msg'] === 'penerimaan_processed') echo "Multi-item Penerimaan processed! Stock IN triggers fired for all items.";
            elseif ($_GET['msg'] === 'retur_processed') echo "Retur executed successfully!";
        ?>
    </div>
<?php elseif (isset($_GET['err'])): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
        <?= htmlspecialchars(urldecode($_GET['err'])) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; align-items: start; margin-bottom: 35px;">
    
    <!-- MULTI-ITEM FORM 1: Create Pengadaan -->
    <div style="background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="color: var(--olive-leaf); margin-top: 0; border-bottom: 2px solid var(--cornsilk); padding-bottom: 10px;">1. Buat Multi-Item Pengadaan Order</h3>
        
        <form action="index.php?action=create_pengadaan" method="POST" onsubmit="return submitPengadaanForm()">
            <input type="hidden" name="items_json" id="pengadaan_items_json" value="[]">

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Vendor Supplier</label>
                <select name="vendor_id" id="po_vendor" onchange="resetPengadaanCart()" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">-- Select Vendor --</option>
                    <?php foreach ($vendors as $v): ?>
                        <option value="<?= $v['idvendor'] ?>"><?= htmlspecialchars($v['nama_vendor']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Item Picker to Add to Cart -->
            <div style="background: var(--cornsilk); padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                <strong style="display: block; margin-bottom: 8px; color: var(--black-forest);">Add Item to Order List:</strong>
                <select id="po_item_select" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px;">
                    <option value="">-- Select Item --</option>
                    <?php foreach ($barangs as $b): ?>
                        <option value="<?= $b['idbarang'] ?>" data-nama="<?= htmlspecialchars($b['nama']) ?>" data-harga="<?= $b['harga'] ?>">
                            <?= htmlspecialchars($b['nama']) ?> (Default: Rp <?= number_format($b['harga'], 0, ',', '.') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="number" id="po_qty" min="1" placeholder="Qty" style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <input type="number" id="po_price" min="1" placeholder="Cost/unit" style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <button type="button" onclick="addPengadaanItem()" style="background: var(--olive-leaf); color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">+ Add</button>
                </div>
            </div>

            <!-- Cart Table -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;" id="po_cart_table">
                <thead>
                    <tr style="background: var(--black-forest); color: var(--cornsilk); font-size: 0.85rem;">
                        <th style="padding: 6px; text-align: left;">Item</th>
                        <th style="padding: 6px; text-align: center;">Qty</th>
                        <th style="padding: 6px; text-align: right;">Cost</th>
                        <th style="padding: 6px; text-align: right;">Subtotal</th>
                        <th style="padding: 6px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5" style="text-align: center; padding: 10px; color: #999;">No items added yet</td></tr>
                </tbody>
            </table>

            <div style="text-align: right; margin-bottom: 15px; font-weight: bold; color: var(--black-forest);">
                Grand Subtotal: <span id="po_grand_subtotal">Rp 0</span>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 10px; font-weight: bold;">Submit Single Multi-Item Order</button>
        </form>
    </div>

    <!-- MULTI-ITEM FORM 2: Process Penerimaan -->
    <div style="background: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="color: var(--black-forest); margin-top: 0; border-bottom: 2px solid var(--cornsilk); padding-bottom: 10px;">2. Terima Multi-Item Shipment</h3>
        
        <form action="index.php?action=proses_penerimaan" method="POST" onsubmit="return submitPenerimaanForm()">
            <input type="hidden" name="items_json" id="penerimaan_items_json" value="[]">

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Pilih No. Pengadaan (Pending)</label>
                <select name="idpengadaan" id="rcv_pengadaan_select" onchange="loadPendingOrderDetails()" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">-- Select Pending Order --</option>
                    <?php foreach ($pengadaanList as $p): ?>
                        <?php if ($p['status'] === 'Pending'): ?>
                            <option value="<?= $p['no'] ?>">Order #<?= $p['no'] ?> - Vendor: <?= htmlspecialchars($p['nama_vendor']) ?> (Total: Rp <?= number_format($p['total_nilai'], 0, ',', '.') ?>)</option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Auto-populated Items Table to Confirm Receipts -->
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Ordered Items to Receive:</label>
                <table style="width: 100%; border-collapse: collapse;" id="rcv_items_table">
                    <thead>
                        <tr style="background: var(--olive-leaf); color: white; font-size: 0.85rem;">
                            <th style="padding: 6px; text-align: left;">Item</th>
                            <th style="padding: 6px; text-align: center;">Ordered</th>
                            <th style="padding: 6px; text-align: center;">Received Qty</th>
                            <th style="padding: 6px; text-align: right;">Unit Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="4" style="text-align: center; padding: 10px; color: #999;">Select a pending order to load items</td></tr>
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 10px; font-weight: bold; background-color: var(--black-forest);">Confirm Receipt & Receive All Stock</button>
        </form>
    </div>
</div>

<!-- History Table showing Nested Details -->
<h3 style="color: var(--black-forest); border-bottom: 2px solid var(--olive-leaf); padding-bottom: 8px;">History Orders & Multi-Item Details (view_pengadaan + view_detail_pengadaan)</h3>
<div style="overflow-x: auto; background: var(--white); border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: var(--olive-leaf); color: var(--white);">
                <th style="padding: 10px;">ID Order</th>
                <th style="padding: 10px;">Tanggal</th>
                <th style="padding: 10px;">Vendor</th>
                <th style="padding: 10px;">Items in Transaction</th>
                <th style="padding: 10px;">Subtotal</th>
                <th style="padding: 10px;">PPN</th>
                <th style="padding: 10px;">Total Nilai</th>
                <th style="padding: 10px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pengadaanList)): ?>
                <?php foreach ($pengadaanList as $row): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; font-weight: bold;">#<?= htmlspecialchars($row['no']) ?></td>
                        <td style="padding: 10px;"><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($row['nama_vendor']) ?></td>
                        <td style="padding: 10px;">
                            <?php if (!empty($detailMap[$row['no']])): ?>
                                <?php foreach ($detailMap[$row['no']] as $item): ?>
                                    <div style="background: var(--cornsilk); padding: 4px 8px; border-radius: 4px; margin-bottom: 4px; font-size: 0.85rem;">
                                        <strong><?= htmlspecialchars($item['nama_barang']) ?></strong> &times; <?= $item['jumlah_order'] ?> 
                                        <span style="color: #666;">(Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?>/unit = Rp <?= number_format($item['sub_total'], 0, ',', '.') ?>)</span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <em style="color: #999;">No detail items</em>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 10px;">Rp <?= number_format($row['subtotal_nilai'], 0, ',', '.') ?></td>
                        <td style="padding: 10px;">Rp <?= number_format($row['ppn'], 0, ',', '.') ?></td>
                        <td style="padding: 10px; font-weight: bold; color: var(--black-forest);">Rp <?= number_format($row['total_nilai'], 0, ',', '.') ?></td>
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: <?= $row['status'] === 'Finished' ? '#28a745' : ($row['status'] === 'Pending' ? '#ffc107' : '#dc3545') ?>; color: <?= $row['status'] === 'Pending' ? '#000' : '#fff' ?>; padding: 3px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" style="padding: 15px; text-align: center;">No pengadaan records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// JSON Details Map loaded directly from PHP
const pengadaanDetailsMap = <?= json_encode($detailMap) ?>;

// --- 1. PENGADAAN CART JS ---
let poCart = [];

function addPengadaanItem() {
    const itemSelect = document.getElementById('po_item_select');
    const selectedOpt = itemSelect.options[itemSelect.selectedIndex];
    const qtyInput = document.getElementById('po_qty');
    const priceInput = document.getElementById('po_price');

    const idbarang = parseInt(itemSelect.value);
    const nama = selectedOpt.getAttribute('data-nama');
    const jumlah = parseInt(qtyInput.value);
    const harga = parseInt(priceInput.value);

    if (!idbarang || !jumlah || !harga) {
        alert("Please select an item, quantity, and price.");
        return;
    }

    poCart.push({ idbarang, nama, jumlah, harga });
    renderPoCart();

    itemSelect.value = '';
    qtyInput.value = '';
    priceInput.value = '';
}

function removePoItem(index) {
    poCart.splice(index, 1);
    renderPoCart();
}

function resetPengadaanCart() {
    poCart = [];
    renderPoCart();
}

function renderPoCart() {
    const tbody = document.querySelector('#po_cart_table tbody');
    tbody.innerHTML = '';
    let grandSubtotal = 0;

    if (poCart.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 10px; color: #999;">No items added yet</td></tr>';
    } else {
        poCart.forEach((item, index) => {
            const subtotal = item.jumlah * item.harga;
            grandSubtotal += subtotal;
            tbody.innerHTML += `
                <tr style="border-bottom: 1px solid #eee; font-size: 0.9rem;">
                    <td style="padding: 6px;">${item.nama}</td>
                    <td style="padding: 6px; text-align: center;">${item.jumlah}</td>
                    <td style="padding: 6px; text-align: right;">Rp ${item.harga.toLocaleString('id-ID')}</td>
                    <td style="padding: 6px; text-align: right; font-weight: bold;">Rp ${subtotal.toLocaleString('id-ID')}</td>
                    <td style="padding: 6px; text-align: center;"><button type="button" onclick="removePoItem(${index})" style="color: red; border: none; background: none; cursor: pointer; font-weight: bold;">&times;</button></td>
                </tr>
            `;
        });
    }
    document.getElementById('po_grand_subtotal').innerText = 'Rp ' + grandSubtotal.toLocaleString('id-ID');
}

function submitPengadaanForm() {
    if (poCart.length === 0) {
        alert("Cannot submit order with an empty item list!");
        return false;
    }
    document.getElementById('pengadaan_items_json').value = JSON.stringify(poCart);
    return true;
}

// --- 2. PENERIMAAN RECEIPT LOAD JS ---
function loadPendingOrderDetails() {
    const orderId = document.getElementById('rcv_pengadaan_select').value;
    const tbody = document.querySelector('#rcv_items_table tbody');
    tbody.innerHTML = '';

    if (!orderId || !pengadaanDetailsMap[orderId]) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 10px; color: #999;">Select a pending order to load items</td></tr>';
        return;
    }

    const items = pengadaanDetailsMap[orderId];
    items.forEach((item, idx) => {
        tbody.innerHTML += `
            <tr style="border-bottom: 1px solid #eee; font-size: 0.9rem;">
                <td style="padding: 6px; font-weight: bold;">
                    ${item.nama_barang}
                    <input type="hidden" class="rcv_idbarang" value="${item.idbarang}">
                </td>
                <td style="padding: 6px; text-align: center;">${item.jumlah_order}</td>
                <td style="padding: 6px; text-align: center;">
                    <input type="number" class="rcv_jumlah" min="1" max="${item.jumlah_order}" value="${item.jumlah_order}" required style="width: 70px; padding: 4px; border: 1px solid #ccc; border-radius: 4px; text-align: center;">
                </td>
                <td style="padding: 6px; text-align: right;">
                    <input type="number" class="rcv_harga" value="${item.harga_satuan}" required style="width: 90px; padding: 4px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                </td>
            </tr>
        `;
    });
}

function submitPenerimaanForm() {
    const rows = document.querySelectorAll('#rcv_items_table tbody tr');
    let rcvItems = [];

    rows.forEach(row => {
        const idbarang = parseInt(row.querySelector('.rcv_idbarang')?.value);
        const jumlah_terima = parseInt(row.querySelector('.rcv_jumlah')?.value);
        const harga = parseInt(row.querySelector('.rcv_harga')?.value);

        if (idbarang && jumlah_terima && harga) {
            rcvItems.push({ idbarang, jumlah_terima, harga });
        }
    });

    if (rcvItems.length === 0) {
        alert("No valid items to receive!");
        return false;
    }

    document.getElementById('penerimaan_items_json').value = JSON.stringify(rcvItems);
    return true;
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>