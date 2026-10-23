<?php
// views/admin/all_views.php
require_once 'classes/MasterData.php';

$db = new Database();
$conn = $db->getConnection();
$masterData = new MasterData($conn);

// Fetch all views
$roles   = $masterData->getViewData('view_role');
$satuans = $masterData->getViewData('view_satuan');
$vendors = $masterData->getViewData('view_vendor');
$users   = $masterData->getViewData('view_user');
$barangs = $masterData->getViewData('view_barang');
$margins = $masterData->getViewData('view_margin');

require_once 'views/layouts/header.php'; 
?>

<div style="margin-bottom: 30px;">
    <h2 style="color: var(--black-forest); border-bottom: 3px solid var(--olive-leaf); padding-bottom: 10px;">Master Data Overview TESTINGGGG BUAT RESPONSI</h2>
    <p style="color: var(--black-forest);">Displaying all integrated database views.</p>
</div>

<!-- Render Tables -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 30px; align-items: start; margin-top: 20px;">
<?php 
$tables = [
    'Role' => $roles,
    'Satuan' => $satuans,
    'Vendor' => $vendors,
    'User' => $users,
    'Barang' => $barangs,
    'Margin Penjualan' => $margins
];

foreach ($tables as $title => $data): 
?>
    <div>
        <h3 style="color: var(--olive-leaf); margin-top: 0; border-bottom: 2px solid var(--cornsilk); padding-bottom: 5px;"><?= $title ?></h3>
        <div style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 6px;">
            <table style="width: 100%; border-collapse: collapse; background-color: var(--white); white-space: nowrap;">
                <thead>
                    <tr>
                        <?php if (!empty($data)): ?>
                            <?php foreach (array_keys($data[0]) as $header): ?>
                                <th style="background-color: var(--olive-leaf); color: var(--white); padding: 12px; text-align: left; text-transform: capitalize; border: 1px solid #ddd;">
                                    <?= htmlspecialchars(str_replace('_', ' ', $header)) ?>
                                </th>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <th style="background-color: var(--olive-leaf); color: var(--white); padding: 12px;">No Data Available</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($row as $cell): ?>
                                <td style="padding: 10px; border: 1px solid #ddd; color: var(--black-forest);">
                                    <?= htmlspecialchars($cell ?? '') ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php require_once 'views/layouts/footer.php'; ?>