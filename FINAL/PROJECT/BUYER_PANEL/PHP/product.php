<?php

require_once 'config.php';
session_start();
include 'main.php';

// fetch products
$productId = (int)($_GET['id'] ?? 0);
if ($productId <= 0) {
    echo "<div class='card card-centered'>Invalid product.</div>";
    include 'footer.php';
    exit;
}

$stmt = $conn->prepare('SELECT id, name, description, price, stock, image FROM products WHERE id = ?');
$stmt->bind_param('i', $productId);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
if (!$product) {
    echo "<div class='card card-centered'>Product not found.</div>";
    include 'footer.php';
    exit;
}

// add to cart
$add_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product_id'])) {
    if (!isset($_SESSION['user_id'])) { // must be logged in
        header('Location: login.php');
        exit;
    }
    $rawId = $_POST['add_product_id'] ?? '';
    $rawQty = $_POST['quantity'] ?? '';

    if ($rawId === '' || !ctype_digit((string)$rawId)) {
        $add_error = 'Invalid product.';
    } else {
        $pid   = (int)$rawId;
        $qty   = max(1, (int)$rawQty ?: 1);
        $userId = (int)$_SESSION['user_id'];

        // Confirm product still exists
        $chk = $conn->prepare('SELECT id FROM products WHERE id = ?');
        $chk->bind_param('i', $pid);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows === 0) {
            $add_error = 'Product not found.';
        }
        $chk->close();

        if ($add_error === '') {
            // Upsert cart record
            $c = $conn->prepare('SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?');
            $c->bind_param('ii', $userId, $pid);
            $c->execute();
            $c->store_result();
            if ($c->num_rows > 0) {
                $c->bind_result($cartId, $existingQty);
                $c->fetch();
                $newQty = $existingQty + $qty;
                $up = $conn->prepare('UPDATE cart SET quantity = ? WHERE id = ?');
                $up->bind_param('ii', $newQty, $cartId);
                $up->execute();
                $up->close();
            } else {
                $ins = $conn->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?)');
                $ins->bind_param('iii', $userId, $pid, $qty);
                $ins->execute();
                $ins->close();
            }
            $c->close();

            header('Location: product.php?id=' . $pid . '&added=1');
            exit;
        }
    }
}

?>

<div class="card card-lg"<?php if (isset($_GET['added'])): ?> data-toast="Added to cart."<?php endif; ?>>
  <div class="grid-single">
    <div>
  <img src="<?=htmlspecialchars($product['image']?:'assets/placeholder.png')?>" alt="" class="img-fluid-rounded">
    </div>
    <div>
  <h1 class="heading-title"><?=htmlspecialchars($product['name'])?></h1>
  <div class="product-price-accent">$<?=number_format($product['price'],2)?></div>
  <p><strong>Stock:</strong> <?= isset($product['stock']) ? ( (int)$product['stock']>0 ? intval($product['stock']) : '<span style="color:red;">Out of Stock</span>' ) : 'N/A' ?></p>
  <p class="product-description"><?=nl2br(htmlspecialchars($product['description']))?></p>

      <?php if (!empty($add_error)): ?>
        <div class="product-error"><span><?=htmlspecialchars($add_error)?></span></div>
      <?php endif; ?>
  <?php $pStock = isset($product['stock'])?(int)$product['stock']:0; ?>
  <?php if($pStock>0): ?>
    <form method="post" class="product-add-form" id="addToCartForm">
      <input type="hidden" name="add_product_id" value="<?=intval($product['id'])?>">
      <label>Qty <input type="number" name="quantity" value="1" min="1" max="<?=$pStock?>" class="qty-input"></label>
      <button class="btn btn-primary" id="addToCartBtn">Add to Cart</button>
    </form>
  <?php else: ?>
    <div class="out-of-stock-label" style="margin-top:10px;display:inline-block;">Out of Stock</div>
  <?php endif; ?>

      <!-- Disclaimer modal -->
      <div id="disclaimerModal" class="modal-overlay">
        <div class="modal-dialog card card-lg product-modal-wide">
          <h3 class="modal-title">Important Notice</h3>
          <div class="modal-body">
            <ul class="list-disc pl-5 space-y-2">
              <li><strong>Stock Availability Is Subject To Change.</strong> Please confirm availability before shopping by calling us.</li>
              <li><strong>Product Image For Illustration Only.</strong> The actual product may vary in size, color, and layout. No claim will be accepted for an image mismatch.</li>
              <li><strong>Prices May Change At Any Moment.</strong> Tech Land BD can change product prices due to volatile market conditions.</li>
              <li><strong>Information May Not Be 100% Accurate.</strong> Tech Land BD is not responsible for results obtained from the use of this information.</li>
            </ul>
          </div>
          <div class="modal-footer">
            <button id="disclaimerClose" class="btn btn-secondary">Close</button>
            <button id="disclaimerAccept" class="btn btn-primary">I Understand</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</main>

<?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>
