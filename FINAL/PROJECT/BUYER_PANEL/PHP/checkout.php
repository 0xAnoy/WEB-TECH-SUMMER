<?php

require_once "config.php";
require_once "email.php"; 
session_start();
ob_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user_id'];

// fetch cart
$q = $conn->prepare("SELECT cart.id AS cart_id, products.id AS product_id, products.name, products.price, cart.quantity
                    FROM cart JOIN products ON cart.product_id = products.id
                    WHERE cart.user_id = ?");
$q->bind_param("i", $user);
$q->execute();
$res = $q->get_result();

$items = [];
$grand = 0;
while ($r = $res->fetch_assoc()) {
    $items[] = $r;
    $grand += $r['price'] * $r['quantity'];
}

$errors = [];
$values = [
  'full_name' => '',
  'email' => '',
  'phone' => '',
  'street' => '',
  'city' => '',
  'zip' => '',
  'payment_method' => 'cod'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (empty($items)) {
    $errors[] = "Cart is empty.";
  } else {

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'cod';

    $values['full_name'] = $full_name;
    $values['email'] = $email;
    $values['phone'] = $phone;
    $values['street'] = $street;
    $values['city'] = $city;
    $values['zip'] = $zip;
    $values['payment_method'] = $payment_method;

    // from validation
    $invalid = false;
    if (empty($full_name)) {
      echo "<p class='form-error'>Full name is required!</p>";
      $invalid = true;
    }
    if (empty($email)) {
      echo "<p class='form-error'>Email is required!</p>";
      $invalid = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "<p class='form-error'>Email format is invalid!</p>";
      $invalid = true;
    }
    if (empty($street)) {
      echo "<p class='form-error'>Street address is required!</p>";
      $invalid = true;
    }
    if (empty($phone)) {
      echo "<p class='form-error'>Phone is required!</p>";
      $invalid = true;
    }
    if (empty($city)) {
      echo "<p class='form-error'>City is required!</p>";
      $invalid = true;
    }
    if (empty($zip)) {
      echo "<p class='form-error'>ZIP code is required!</p>";
      $invalid = true;
    }

  if (empty($errors) && !$invalid) {
      $hasPaymentCol = false;
      $colRes = $conn->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");
      if ($colRes && $colRes->num_rows > 0) { $hasPaymentCol = true; }

      if ($hasPaymentCol) {
        $ord = $conn->prepare("INSERT INTO orders (user_id, total, payment_method, full_name, email, phone, address, city, zip) VALUES (?,?,?,?,?,?,?,?,?)");
        $addr = $values['street'];
        $ord->bind_param("idsssssss", $user, $grand, $values['payment_method'], $values['full_name'], $values['email'], $values['phone'], $addr, $values['city'], $values['zip']);
      } else {
        $ord = $conn->prepare("INSERT INTO orders (user_id, total, full_name, email, phone, address, city, zip) VALUES (?,?,?,?,?,?,?,?)");
        $addr = $values['street'];
        $ord->bind_param("idssssss", $user, $grand, $values['full_name'], $values['email'], $values['phone'], $addr, $values['city'], $values['zip']);
      }
      if ($ord->execute()) {
        $order_id = $ord->insert_id;
        // insert order items 
        $tblRes = $conn->query("SHOW TABLES LIKE 'order_items'");
        if ($tblRes && $tblRes->num_rows > 0) {
          foreach ($items as $it) {
            $ins = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
            $ins->bind_param("iiid", $order_id, $it['product_id'], $it['quantity'], $it['price']);
            $ins->execute();
            $ins->close();
          }
        } else {
          $errors[] = 'Note: order_items table missing; items were not recorded separately.';
        }
        // Prepare order data for email
        $orderData = [
          'id' => $order_id,
          'total' => $grand,
          'items' => array_map(function($it){
              return [
                'name' => $it['name'],
                'price' => $it['price'],
                'quantity' => $it['quantity']
              ];
            }, $items),
          'billing' => [
            'full_name' => $values['full_name'],
            'email' => $values['email'],
            'phone' => $values['phone'],
            'street' => $values['street'],
            'city' => $values['city'],
            'zip' => $values['zip']
          ],
          'payment_method' => $values['payment_method']
        ];

        // Attempt to send receipt 
        $emailResult = send_order_receipt($SMTP_CONFIG, $values['email'], $values['full_name'] ?: 'Customer', $orderData);
        if (!$emailResult['ok']) {
          $_SESSION['email_failed'] = 1;
          $_SESSION['email_error'] = substr($emailResult['error'],0,200);
        } else {
          $_SESSION['email_failed'] = 0;
        }

        // clear cart
        $clr = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clr->bind_param("i", $user);
        $clr->execute();

        header("Location: order_success.php?order_id=" . intval($order_id));
        exit;
      } else {
        $errors[] = "Failed to create order: " . $conn->error;
      }
    }
  }
}


include "main.php";
?>

<div class="card-centered">
  <h2 class="heading-page">Checkout</h2>
  <?php if (!empty($errors)): ?>
    <div class="message-error">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?=htmlspecialchars($err)?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (empty($items)): ?>
  <p>Your cart is empty. <a href="products.php" class="link-primary">Shop now</a></p>
  <?php else: ?>
  <ul class="mb-4 list-plain">
      <?php foreach ($items as $it): ?>
  <li class="order-line">
          <div>
            <div class="text-semibold">
              <?=htmlspecialchars($it['name'])?>
            </div>
            <div class="small-muted">Qty: <?=intval($it['quantity'])?></div>
          </div>
          <div>$<?=number_format($it['price'] * $it['quantity'],2)?></div>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="mt-4">
  <div class="total-amount mb-4">Grand Total: $<?=number_format($grand,2)?></div>

      <form method="post">
  <h3 class="heading-billing">Billing Information</h3>
  <div class="field-grid">
          <div>
            <label class="field-label">Full Name</label>
            <input name="full_name" value="<?=htmlspecialchars($values['full_name'] ?? '')?>" class="form-input" />

          </div>
          <div>
            <label class="field-label">Email Address</label>
            <input name="email" type="email" value="<?=htmlspecialchars($values['email'] ?? '')?>" class="form-input" />

          </div>
          <div>
            <label class="field-label">Phone Number</label>
            <input name="phone" value="<?=htmlspecialchars($values['phone'] ?? '')?>" class="form-input" />
          </div>
        </div>
  <h4 class="heading-sub">Shipping Address</h4>
  <div class="address-grid">
          <div>
            <label class="field-label">Street Address</label>
            <input name="street" value="<?=htmlspecialchars($values['street'] ?? '')?>" class="form-input" />
          </div>
          <div class="responsive-mini-grid">
            <div>
              <label class="field-label">City</label>
              <input name="city" value="<?=htmlspecialchars($values['city'] ?? '')?>" class="form-input" />
            </div>
            <div>
              <label class="field-label">ZIP Code</label>
              <input name="zip" value="<?=htmlspecialchars($values['zip'] ?? '')?>" class="form-input" />
            </div>
          </div>
        </div>

  <div class="form-footer-row form-footer-flex">
          <div class="pay-method-box">
            <h4 class="heading-sub mb-0">Payment Method</h4>
            <label class="inline-option pay-method-option">
              <input type="radio" name="payment_method" value="cod" <?=($values['payment_method'] === 'cod' ? 'checked' : '')?> />
              <span>Cash On Delivery</span>
            </label>
          </div>
          <div class="submit-box">
            <button type="submit" class="btn btn-primary">Place Order</button>
          </div>
        </div>
      </form>
    </div>
</div>

</main>

<?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>

