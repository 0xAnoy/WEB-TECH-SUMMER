<?php
// cart.php (single block version)
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user = (int) $_SESSION['user_id'];

// Handle update/remove
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_id'])) {
        $cid = (int) $_POST['update_id'];
        $qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
        if ($qty < 1) { $qty = 1; }
        $up = $conn->prepare('UPDATE cart SET quantity=? WHERE id=? AND user_id=?');
        $up->bind_param('iii', $qty, $cid, $user);
        $up->execute();
        $up->close();
    } elseif (isset($_POST['remove_id'])) {
        $cid = (int) $_POST['remove_id'];
        $rm = $conn->prepare('DELETE FROM cart WHERE id=? AND user_id=?');
        $rm->bind_param('ii', $cid, $user);
        $rm->execute();
        $rm->close();
    }
}

// Fetch cart items
$q = $conn->prepare('SELECT cart.id AS cart_id, products.id AS product_id, products.name, products.price, products.image, cart.quantity
                      FROM cart JOIN products ON cart.product_id = products.id
                      WHERE cart.user_id = ?');
$q->bind_param('i', $user);
$q->execute();
$res = $q->get_result();

include "main.php";
?>

<div class="card">
  <h2 class="heading-page">Your Cart</h2>
  <?php if ($res->num_rows === 0): ?>
    <p>Your cart is empty. <a href="products.php" class="link-primary">Shop now</a></p>
  <?php else: ?>
  <table class="table">
      <thead>
        <tr class="text-left">
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Total</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $grand = 0;
        while ($r = $res->fetch_assoc()):
          $total = $r['price'] * $r['quantity'];
          $grand += $total;
        ?>
          <tr>
            <td>
              <div class="cart-line">
                <img src="<?=htmlspecialchars($r['image']?:'../assets/placeholder.png')?>" class="img-thumb-sm">
                <div><?=htmlspecialchars($r['name'])?></div>
              </div>
            </td>
            <td>$<?=number_format($r['price'],2)?></td>
            <td>
              <form method="post" class="inline-form">
                <input type="hidden" name="update_id" value="<?=intval($r['cart_id'])?>">
                <input type="number" name="quantity" value="<?=intval($r['quantity'])?>" min="1" class="qty-input">
                <button class="btn btn-secondary btn-sm">Update</button>
              </form>
            </td>
            <td>$<?=number_format($total,2)?></td>
            <td>
              <form method="post">
                <input type="hidden" name="remove_id" value="<?=intval($r['cart_id'])?>">
                <button class="btn btn-danger btn-sm">Remove</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

  <div class="mt-section row-between-wrap">
      <div class="total-amount">Grand Total: $<?=number_format($grand,2)?></div>
      <div class="btn-row">
        <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
      </div>
    </div>
  <?php endif; ?>
</div>
<!-- content ends; layout (header/footer) handled by main.php -->

</main>

<?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>
