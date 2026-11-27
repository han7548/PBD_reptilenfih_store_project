<?php
require_once 'classes/Margin.php';

$db = new Database();
$conn = $db->getConnection();
$marginModel = new Margin($conn);

$margins = $marginModel->getAllMargins();
$activeMargin = $marginModel->getActiveMargin();

require_once 'views/layouts/header.php';
?>

<h2 style="margin-top:0; color: var(--black-forest);">Margin Settings</h2>
<p style="color: var(--text-muted); margin-bottom: 20px;">Only one profit margin can be active at a time. POS Penjualan automatically uses the active margin.</p>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php elseif (isset($_GET['err'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['err']) ?></div>
<?php endif; ?>

<div class="card">
    <strong>Current Active Margin:</strong>
    <?php if ($activeMargin): ?>
        <span class="status-text status-active"><?= htmlspecialchars($activeMargin['persen']) ?>% (ID #<?= htmlspecialchars($activeMargin['idmargin_penjualan']) ?>)</span>
    <?php else: ?>
        <span class="status-text status-inactive">None (0% markup)</span>
    <?php endif; ?>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">Add New Margin</div>
        <form action="index.php?action=create_margin" method="POST">
            <div class="form-group">
                <label>Margin Percentage (%)</label>
                <input type="number" step="0.01" min="0" name="persen" required placeholder="e.g. 10 or 15.5">
            </div>
            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; font-weight:500;">
                    <input type="checkbox" name="set_active" value="1" style="width:auto;">
                    Set as the single active margin immediately
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Save Margin</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">Select Active Margin</div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Margin (%)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($margins)): ?>
                        <tr><td colspan="4" style="text-align:center;">No margins configured.</td></tr>
                    <?php else: ?>
                        <?php foreach ($margins as $m): $isActive = (strtoupper((string)($m['status'] ?? '')) === 'ACTIVE'); ?>
                            <tr>
                                <td>#<?= htmlspecialchars($m['idmargin_penjualan']) ?></td>
                                <td><strong><?= htmlspecialchars($m['persen']) ?>%</strong></td>
                                <td>
                                    <span class="status-text <?= $isActive ? 'status-active' : 'status-inactive' ?>">
                                        <?= $isActive ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!$isActive): ?>
                                        <a href="index.php?action=activate_margin&id=<?= (int)$m['idmargin_penjualan'] ?>" class="btn btn-success btn-sm">Set Active</a>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.85rem;">Selected</span>
                                    <?php endif; ?>
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