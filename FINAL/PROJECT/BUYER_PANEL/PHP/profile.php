<?php
// profile.php
require_once "config.php";
session_start();
if (!isset($_SESSION['user_id'])) {
    if (!empty($_COOKIE['remember_user_id']) && ctype_digit($_COOKIE['remember_user_id'])) {
        $_SESSION['user_id'] = (int)$_COOKIE['remember_user_id'];
        if (!empty($_COOKIE['remember_username'])) {
            $_SESSION['username'] = $_COOKIE['remember_username'];
        }
    }
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
include "main.php";

// fetch user info (optional)
$stmt = $conn->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($name, $email, $created_at);
$stmt->fetch();
$stmt->close();

// Fetch recent orders (simple history)
$orders = [];
$orderItemsMap = [];
$uid = (int)$_SESSION['user_id'];
// Detect optional columns
$hasPaymentCol = false; $hasCreatedCol = false;
$colRes = $conn->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");
if ($colRes && $colRes->num_rows > 0) { $hasPaymentCol = true; }
$colRes2 = $conn->query("SHOW COLUMNS FROM orders LIKE 'created_at'");
if ($colRes2 && $colRes2->num_rows > 0) { $hasCreatedCol = true; }

$selectCols = 'id,total';
if ($hasPaymentCol) { $selectCols .= ',payment_method'; }
if ($hasCreatedCol) { $selectCols .= ',created_at'; }

$limit = 10; // recent 10 orders
$ordStmt = $conn->prepare("SELECT $selectCols FROM orders WHERE user_id=? ORDER BY id DESC LIMIT ?");
$ordStmt->bind_param('ii', $uid, $limit);
if ($ordStmt->execute()) {
    $res = $ordStmt->get_result();
    while ($row = $res->fetch_assoc()) { $orders[] = $row; }
}
$ordStmt->close();

// Attempt to load item names if order_items table exists
// Defensive: only attempt item names join if both tables exist
$hasOrderItems = false; $hasProductsTable = false;
$tblRes = $conn->query("SHOW TABLES LIKE 'order_items'");
if ($tblRes && $tblRes->num_rows > 0) { $hasOrderItems = true; }
$tblRes2 = $conn->query("SHOW TABLES LIKE 'products'");
if ($tblRes2 && $tblRes2->num_rows > 0) { $hasProductsTable = true; }
if ($hasOrderItems && $hasProductsTable && !empty($orders)) {
    $ids = array_map(function($o){ return (int)$o['id']; }, $orders);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $sqlItems = "SELECT oi.order_id, p.name, oi.quantity FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id IN ($placeholders)";
    $stmtItems = $conn->prepare($sqlItems);
    if ($stmtItems) {
        $stmtItems->bind_param($types, ...$ids);
        if ($stmtItems->execute()) {
            $resItems = $stmtItems->get_result();
            while ($ir = $resItems->fetch_assoc()) {
                $oid = (int)$ir['order_id'];
                if (!isset($orderItemsMap[$oid])) $orderItemsMap[$oid] = [];
                $orderItemsMap[$oid][] = $ir['name'] . ( ($ir['quantity']>1)?' x'.$ir['quantity']:'' );
            }
        }
        $stmtItems->close();
    }
} elseif ($hasOrderItems && !empty($orders) && !$hasProductsTable) {
    echo "<p class='form-error'>Products table missing. Item names unavailable.</p>";
}
?>

<div class="card-centered">
  <h2 class="heading-page">Your Profile</h2>
  <p><strong>Name:</strong> <?=htmlspecialchars($name)?></p>
  <p><strong>Email:</strong> <?=htmlspecialchars($email)?></p>
  <p><strong>Member since:</strong> <?=htmlspecialchars($created_at)?></p>
  <div class="mt-4">
  <a href="products.php" class="btn btn-primary btn-sm">Shop</a>
  <a href="cart.php" class="btn btn-secondary btn-sm ml-sm">View Cart</a>
  </div>
</div>
<div class="card space-top-lg">
    <h3 class="heading-sub">Recent Orders</h3>
    <?php if (empty($orders)): ?>
        <p class="small-muted">You have no orders yet.</p>
    <?php else: ?>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Total</th>
                    <?php if ($hasPaymentCol): ?><th>Payment</th><?php endif; ?>
                    <?php if ($hasCreatedCol): ?><th>Date</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><a href="order_success.php?order_id=<?=intval($o['id'])?>" class="link-primary">#<?=intval($o['id'])?></a></td>
                        <td>$<?=number_format($o['total'],2)?></td>
                        <?php if ($hasPaymentCol): ?>
                            <td><?=($o['payment_method']==='cod'?'Cash On Delivery':htmlspecialchars($o['payment_method']))?></td>
                        <?php endif; ?>
                        <?php if ($hasCreatedCol): ?>
                            <td><?=htmlspecialchars($o['created_at'])?></td>
                        <?php endif; ?>
                    </tr>
                    <?php if (!empty($orderItemsMap[(int)$o['id']])): ?>
                    <tr class="order-items-row">
                        <td colspan="<?= 2 + ($hasPaymentCol?1:0) + ($hasCreatedCol?1:0) ?>" class="small-muted">
                            Items: <?=htmlspecialchars(implode(', ', $orderItemsMap[(int)$o['id']]))?>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</main>

<?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>
