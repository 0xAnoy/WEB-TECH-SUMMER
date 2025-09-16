<?php include("db.php"); ?>

<?php
if (!isset($conn) || !$conn) {
    die("<p style='color:red;'>Database connection failed. Please check db.php.</p>");
}
if (!function_exists('validateInput')) {
    function validateInput($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Seller Dashboard</title>
<style>
    body {font-family: 'Segoe UI', Arial, sans-serif;background: #f4f6fb;margin: 0;padding: 0;}
    h1 {background: #2d3e50;color: #fff;margin: 0;padding: 30px 0 20px 0;text-align: center;letter-spacing: 2px;font-size: 2.2em;box-shadow: 0 2px 8px rgba(0,0,0,0.04);}
    .menu {background: #fff;padding: 20px 0;text-align: center;box-shadow: 0 2px 8px rgba(0,0,0,0.04);margin-bottom: 30px;}
    .menu a {margin: 0 18px;text-decoration: none;font-weight: 600;color: #2d3e50;font-size: 1.1em;padding: 8px 18px;border-radius: 4px;transition: background 0.2s, color 0.2s;}
    .menu a:hover, .menu a:focus {background: #2d3e50;color: #fff;}
    .box {background: #fff;border-radius: 8px;box-shadow: 0 2px 12px rgba(0,0,0,0.07);padding: 30px 32px 24px 32px;margin: 30px auto;max-width: 700px;}
    h2 {color: #2d3e50;margin-top: 0;font-size: 1.5em;border-bottom: 1px solid #eaeaea;padding-bottom: 10px;margin-bottom: 25px;}
    table {width: 100%;border-collapse: collapse;margin-top: 10px;background: #fafbfc;border-radius: 6px;overflow: hidden;}
    th, td {padding: 12px 10px;text-align: left;}
    th {background: #2d3e50;color: #fff;font-weight: 600;}
    tr:nth-child(even) {background: #f4f6fb;}
    tr:hover {background: #e3eaf2;}
    input[type="text"], textarea {width: 95%;padding: 8px;border: 1px solid #cfd8dc;border-radius: 4px;margin-bottom: 8px;font-size: 1em;background: #f9fafb;transition: border 0.2s;}
    input[type="text"]:focus, textarea:focus {border: 1.5px solid #2d3e50;outline: none;}
    input[type="file"] {margin-top: 6px;margin-bottom: 12px;}
    button, input[type="submit"] {background: #2d3e50;color: #fff;border: none;padding: 9px 22px;border-radius: 4px;font-size: 1em;font-weight: 600;cursor: pointer;transition: background 0.2s;margin-top: 5px;}
    button:hover, input[type="submit"]:hover {background: #1a2533;}
    select {padding: 7px 10px;border-radius: 4px;border: 1px solid #cfd8dc;background: #f9fafb;font-size: 1em;}
    p {font-size: 1.08em;}
    @media (max-width: 800px) {.box {max-width: 98vw;padding: 16px 6vw;}table, th, td {font-size: 0.98em;}}
</style>
</head>
<body>
<h1>Seller Dashboard</h1>
<div class="menu">
<a href="?page=add">Add Product</a>
<a href="?page=manage">Edit/Delete Product</a>
<a href="?page=availability">Set Availability</a>
<a href="?page=orders">View Orders</a>
<a href="?page=sales">Sales Summary</a>
</div>
<?php
$page = $_GET['page'] ?? 'add';

// Ensure products.stock column exists (runtime migration)
$colStock = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
if($colStock && $colStock->num_rows === 0){
    if($conn->query("ALTER TABLE products ADD stock INT NOT NULL DEFAULT 10 AFTER price")){
        echo "<p style='color:green;text-align:center;'>Added missing products.stock column (default 10).</p>";
    } else {
        echo "<p style='color:red;text-align:center;'>Failed adding stock column: ".htmlspecialchars($conn->error)."</p>";
    }
}

// Handle order status update early
if(isset($_POST['update_order_status'], $_POST['order_id'], $_POST['new_status'])) {
    $oid = intval($_POST['order_id']);
    $newStatus = trim($_POST['new_status']);
    $allowed = ['Pending','Processing','Shipped','Completed','Cancelled'];
    if(in_array($newStatus, $allowed, true)) {
        $stmt = $conn->prepare('UPDATE orders SET status=? WHERE id=?');
        if($stmt){
            $stmt->bind_param('si', $newStatus, $oid);
            $stmt->execute();
            $stmt->close();
            echo '<p style="color:green;text-align:center;">Order #'.$oid.' status updated to '.htmlspecialchars($newStatus).'</p>';
        } else {
            echo '<p style="color:red;text-align:center;">Failed to prepare status update.</p>';
        }
    } else {
        echo '<p style="color:red;text-align:center;">Invalid status value.</p>';
    }
}

// Ensure orders.status column exists (lightweight migration)
$needStatus = false;
$colCheck = $conn->query("SHOW COLUMNS FROM orders LIKE 'status'");
if($colCheck && $colCheck->num_rows === 0){
    $needStatus = true;
}
if($needStatus){
    if($conn->query("ALTER TABLE orders ADD status VARCHAR(30) NOT NULL DEFAULT 'Pending' AFTER created_at")){
        echo '<p style="color:green;text-align:center;">Added missing orders.status column (default Pending).</p>';
    } else {
        echo '<p style="color:red;text-align:center;">Failed adding status column: '.htmlspecialchars($conn->error).'</p>';
    }
}


// ------------------ ADD PRODUCT ------------------
if ($page=="add") {
    if (isset($_POST['add'])) {
    $name  = validateInput($_POST['name']);
    $price = validateInput($_POST['price']);
    $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
        $desc  = validateInput($_POST['description']);
        $cat   = validateInput($_POST['category']);
        $image = $_FILES['image']['name'];

        if (empty($name) || empty($price) || empty($desc) || empty($cat) || empty($image)) {
            echo "<p style='color:red;'>All fields are required!</p>";
        } elseif (!is_numeric($price)) {
            echo "<p style='color:red;'>Price must be numeric!</p>";
        } elseif (!in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), ['jpg','jpeg','png'])) {
            echo "<p style='color:red;'>Only JPG, JPEG, PNG allowed!</p>";
        } else {
            if (!is_dir("uploads")) mkdir("uploads");
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);
        if($stock < 0) $stock = 0;
        $sql = "INSERT INTO products (name, price, stock, description, category, image) 
            VALUES ('$name','$price','$stock','$desc','$cat','$image')";
            echo ($conn->query($sql)) 
                ? "<p style='color:green;'>Product added!</p>" 
                : "<p style='color:red;'>Error: ".$conn->error."</p>";
        }
    }
?>
<div class="box">
<h2>Add Product</h2>
<form method="post" enctype="multipart/form-data">
Name: <input type="text" name="name"><br><br>
Price: <input type="text" name="price"><br><br>
Initial Stock: <input type="number" name="stock" value="10" min="0"><br><br>
Category:
<select name="category">
<option value="">--Select--</option>
<option>Laptops</option>
<option>Accessories</option>
<option>Monitors</option>
<option>Chairs</option>
<option>Tablets</option>
</select><br><br>
Description:<br>
<textarea name="description" rows="4"></textarea><br><br>
Image: <input type="file" name="image"><br><br>
<button type="submit" name="add">Add Product</button>
</form>
</div>
<?php }


// ------------------ MANAGE PRODUCT ------------------
if ($page=="manage") {
    if (isset($_GET['delete'])) {
        $id=intval($_GET['delete']);
        $conn->query("DELETE FROM products WHERE id=$id");
        echo "<p style='color:green;'>Product deleted!</p>";
    }
    if (isset($_POST['update'])) {
        $id=intval($_POST['id']);
        $name=validateInput($_POST['name']);
    $price=validateInput($_POST['price']);
    $stock=isset($_POST['stock'])?intval($_POST['stock']):0;
        $desc=validateInput($_POST['description']);
        $cat=validateInput($_POST['category']);
        if(empty($name)||empty($price)||empty($desc)||empty($cat)) echo "<p style='color:red;'>All fields required!</p>";
        elseif(!is_numeric($price)) echo "<p style='color:red;'>Price must be numeric!</p>";
        else { 
            if($stock < 0) $stock = 0;
            $conn->query("UPDATE products SET name='$name',price='$price',stock='$stock',description='$desc',category='$cat' WHERE id=$id"); 
            echo "<p style='color:green;'>Updated!</p>"; 
        }
    }
    $res=$conn->query("SELECT * FROM products");
?>
<div class="box">
<h2>Edit/Delete Products</h2>
<table border="1" cellpadding="8">
<tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Category</th><th>Description</th><th>Action</th></tr>
<?php while($r=$res->fetch_assoc()){ ?>
<tr>
<form method="post">
<td><?php echo $r['id']; ?></td>
<td><input type="text" name="name" value="<?php echo $r['name']; ?>"></td>
<td><input type="text" name="price" value="<?php echo $r['price']; ?>"></td>
<td><input type="number" name="stock" value="<?php echo isset($r['stock'])?intval($r['stock']):0; ?>" min="0" style="width:70px"></td>
<td>
<select name="category">
<option <?php if($r['category']=="Laptops") echo "selected"; ?>>Laptops</option>
<option <?php if($r['category']=="Accessories") echo "selected"; ?>>Accessories</option>
<option <?php if($r['category']=="Monitors") echo "selected"; ?>>Monitors</option>
<option <?php if($r['category']=="Chairs") echo "selected"; ?>>Chairs</option>
<option <?php if($r['category']=="Tablets") echo "selected"; ?>>Tablets</option>
</select>
</td>
<td><textarea name="description" rows="2"><?php echo $r['description']; ?></textarea></td>
<td>
<input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<button name="update">Update</button>
<a href="?page=manage&delete=<?php echo $r['id']; ?>" onclick="return confirm('Delete this product?')">Delete</a>
</td>
</form>
</tr>
<?php } ?>
</table>
</div>
<?php }


// ------------------ AVAILABILITY ------------------
if ($page=="availability") {
    // Single product status editor: In Stock / Out of Stock mapped to stock values
    $message = '';
    if(isset($_POST['set_status']) && isset($_POST['product_id']) && isset($_POST['status'])){
        $pid = intval($_POST['product_id']);
        $status = $_POST['status']==='In Stock' ? 'In Stock' : 'Out of Stock';
        $rowP = $conn->query("SELECT stock FROM products WHERE id=$pid");
        if($rowP && $rowP->num_rows){
            $cur = (int)$rowP->fetch_assoc()['stock'];
            if($status==='Out of Stock'){
                $ok = $conn->query("UPDATE products SET stock=0 WHERE id=$pid");
                $message = $ok? "<p style='color:green;'>Product #$pid marked Out of Stock.</p>":"<p style='color:red;'>Update failed: ".htmlspecialchars($conn->error)."</p>";
            } else { // In Stock
                // If current stock is 0, bump to default 10; otherwise leave as is
                if($cur<=0){
                    $ok = $conn->query("UPDATE products SET stock=10 WHERE id=$pid");
                    $message = $ok? "<p style='color:green;'>Product #$pid set In Stock (stock=10).</p>":"<p style='color:red;'>Update failed: ".htmlspecialchars($conn->error)."</p>";
                } else {
                    $message = "<p style='color:green;'>Product #$pid already In Stock (stock=$cur).</p>";
                }
            }
        } else {
            $message = "<p style='color:red;'>Product not found.</p>";
        }
    }
    $plist = $conn->query("SELECT id,name,stock FROM products ORDER BY name ASC");
?>
<div class="box">
<h2>Set Product Stock Status</h2>
<?php echo $message; ?>
<form method="post" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
    <div>
        <label>Product<br>
        <select name="product_id" required>
            <option value="">-- Select Product --</option>
            <?php if($plist){ while($p=$plist->fetch_assoc()){ echo '<option value="'.intval($p['id']).'">'.htmlspecialchars($p['name']).' (Stock: '.intval($p['stock']).')</option>'; } } ?>
        </select></label>
    </div>
    <div>
        <label>Status<br>
        <select name="status">
            <option>In Stock</option>
            <option>Out of Stock</option>
        </select></label>
    </div>
    <div>
        <button name="set_status">Apply</button>
    </div>
</form>
<hr style="margin:25px 0;">
<h3>Current Products</h3>
<table border="1" cellpadding="6" style="width:100%;">
<tr><th>ID</th><th>Name</th><th>Stock</th><th>Status</th></tr>
<?php 
    $all = $conn->query("SELECT id,name,stock FROM products ORDER BY id DESC");
    if($all && $all->num_rows){
        while($r=$all->fetch_assoc()){
            $st = ((int)$r['stock']>0)?'<span style="color:green;font-weight:600;">In Stock</span>':'<span style="color:red;font-weight:600;">Out of Stock</span>';
            echo '<tr><td>'.intval($r['id']).'</td><td>'.htmlspecialchars($r['name']).'</td><td>'.intval($r['stock']).'</td><td>'.$st.'</td></tr>';
        }
    } else {
        echo '<tr><td colspan="4"><em>No products.</em></td></tr>';
    }
?>
</table>
</div>
<?php }


// ------------------ ORDERS ------------------
if ($page=="orders") {
    // Decide whether status column exists now
    $hasStatus = true;
    $chk = $conn->query("SHOW COLUMNS FROM orders LIKE 'status'");
    if(!$chk || $chk->num_rows === 0){ $hasStatus = false; }
    $selectCols = "o.id, o.created_at, o.total, o.payment_method, o.full_name, o.email, o.phone, o.city" . ($hasStatus ? ", o.status" : "");
    $sql = "SELECT $selectCols FROM orders o ORDER BY o.id DESC";
        $res = $conn->query($sql);
        if(!$res){
                echo '<div class="box"><p style="color:red;">Query error: '.htmlspecialchars($conn->error).'</p></div>';
        } else {
?>
<div class="box">
<h2>All Orders</h2>
<table border="1" cellpadding="8">
<tr>
    <th>ID</th>
    <th>Date</th>
    <th>Customer</th>
    <th>Contact</th>
    <th>City</th>
    <th>Payment</th>
    <th>Total</th>
    <th>Items</th>
    <th>Action</th>
</tr>
<?php while($o=$res->fetch_assoc()){ 
        $oid = (int)$o['id'];
        // Load items for this order
        $items = [];
        $ir = $conn->query("SELECT oi.quantity, oi.price, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=".$oid);
        if($ir){
                while($row = $ir->fetch_assoc()) { $items[] = $row; }
        }
        $itemSummary = '';
        foreach($items as $it){
                $itemSummary .= htmlspecialchars($it['name']).' x'.$it['quantity'].' ($'.number_format($it['price'],2).')<br>'; 
        }
        if($itemSummary==='') $itemSummary = '<em>No items</em>';
?>
<tr>
    <td><?php echo $oid; ?></td>
    <td><?php echo htmlspecialchars($o['created_at']); ?></td>
    <td><?php echo htmlspecialchars($o['full_name']); ?></td>
    <td><?php echo htmlspecialchars($o['email']); ?><br><?php echo htmlspecialchars($o['phone']); ?></td>
    <td><?php echo htmlspecialchars($o['city']); ?></td>
    <td><?php echo htmlspecialchars($o['payment_method']); ?></td>
    <td>$<?php echo number_format($o['total'],2); ?></td>
    <td style="max-width:250px;line-height:1.3;"><?php echo $itemSummary; ?></td>
    <td>
        <form method="post" style="display:flex;flex-direction:column;gap:4px;">
            <input type="hidden" name="order_id" value="<?php echo $oid; ?>">
            <select name="new_status">
                <?php 
                    $statuses = ['Pending','Processing','Shipped','Completed','Cancelled'];
                    $current = $hasStatus ? ($o['status'] ?? 'Pending') : 'Pending';
                    foreach($statuses as $st){
                        $sel = ($st==$current)?'selected':'';
                        echo '<option '.$sel.'>'.htmlspecialchars($st).'</option>';
                    }
                ?>
            </select>
            <button name="update_order_status">Update</button>
        </form>
    </td>
</tr>
<?php } // end while orders ?>
</table>
</div>
<?php } // end if orders query success
} // end if page==orders


// ------------------ SALES SUMMARY ------------------
if ($page=="sales") {
    // Determine if status column exists
    $hasStatus = true; $chk = $conn->query("SHOW COLUMNS FROM orders LIKE 'status'");
    if(!$chk || $chk->num_rows===0) $hasStatus = false;
    $where = $hasStatus ? "WHERE o.status='Completed'" : "";
    $r1 = $conn->query("SELECT SUM(oi.quantity) AS sold, SUM(oi.quantity * oi.price) AS revenue FROM order_items oi JOIN orders o ON o.id=oi.order_id $where");
    $agg = $r1 ? $r1->fetch_assoc() : ['sold'=>0,'revenue'=>0];
    $r2 = $conn->query("SELECT p.category, SUM(oi.quantity) AS sold_qty, SUM(oi.quantity*oi.price) AS cat_revenue FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN products p ON p.id=oi.product_id $where GROUP BY p.category ORDER BY cat_revenue DESC");
    $r3 = $conn->query("SELECT id,name,stock FROM products ORDER BY stock ASC LIMIT 10");
?>
<div class="box">
<h2>Sales & Inventory Summary</h2>
<p><strong>Total Products Sold:</strong> <?php echo (int)($agg['sold'] ?? 0); ?></p>
<p><strong>Total Revenue:</strong> $<?php echo number_format($agg['revenue'] ?? 0, 2); ?></p>
<h3>By Category</h3>
<table border="1" cellpadding="6" style="margin-bottom:20px;">
<tr><th>Category</th><th>Units Sold</th><th>Revenue</th></tr>
<?php if($r2 && $r2->num_rows){ while($c=$r2->fetch_assoc()){ ?>
<tr><td><?php echo htmlspecialchars($c['category']); ?></td><td><?php echo (int)$c['sold_qty']; ?></td><td>$<?php echo number_format($c['cat_revenue'],2); ?></td></tr>
<?php }} else { echo '<tr><td colspan="3"><em>No data</em></td></tr>'; } ?>
</table>
<h3>Low Stock (<=10)</h3>
<table border="1" cellpadding="6">
<tr><th>ID</th><th>Name</th><th>Stock</th></tr>
<?php if($r3 && $r3->num_rows){ while($l=$r3->fetch_assoc()){ $cls = ($l['stock']<=5)?'style="color:red;"':''; ?>
<tr <?php echo $cls; ?>><td><?php echo $l['id']; ?></td><td><?php echo htmlspecialchars($l['name']); ?></td><td><?php echo intval($l['stock']); ?></td></tr>
<?php }} else { echo '<tr><td colspan="3"><em>No products</em></td></tr>'; } ?>
</table>
</div>
<?php } // end if page==sales ?>

</body>
</html>