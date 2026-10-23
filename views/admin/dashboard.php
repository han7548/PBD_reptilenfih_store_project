<?php 

require_once 'views/layouts/header.php'; 
?>

<h2>Admin Control Panel</h2>
<p>Welcome to the main dashboard. From here, you have full control over the master data, and you can also access Manager and Cashier modules.</p>

<div class="card-container">
    <div class="card">
        <h3>Inventory & Barang</h3>
        <p>Add new livestock, equipment, and feeds. Manage prices, update statuses, and upload product images to the catalog.</p>
        <a href="index.php?page=master_barang" class="card-link">Manage Barang &rarr;</a>
    </div>
    
    <div class="card">
        <h3>User Management</h3>
        <p>Register new cashiers or managers, update their passwords, and assign system roles.</p>
        <a href="index.php?page=master_user" class="card-link">Manage Users &rarr;</a>
    </div>
    
    <div class="card">
        <h3>Vendors & Units</h3>
        <p>Configure supplier data (Vendors) and measurement units (Satuan) for your inventory.</p>
        <a href="#" class="card-link">Manage Settings &rarr;</a>
    </div>
</div>

<?php 
require_once 'views/layouts/footer.php'; 
?>