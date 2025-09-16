<?php
// products.php (expanded formatting)
require_once 'config.php';
session_start();
include 'main.php';

$add_message = '';

if (
  $_SERVER['REQUEST_METHOD'] === 'POST'
  && isset($_POST['add_product_id'])
  && isset($_SESSION['user_id'])
) {
  $pidIn = $_POST['add_product_id'];
  if (ctype_digit($pidIn)) {
    $pid = (int)$pidIn;
    $qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    if ($qty < 1) {
      $qty = 1;
    }
    $uid = (int) $_SESSION['user_id'];

    $p = $conn->prepare('SELECT id FROM products WHERE id=?');
    $p->bind_param('i', $pid);
    $p->execute();
    $p->store_result();
    if ($p->num_rows) {
      $c = $conn->prepare('SELECT id, quantity FROM cart WHERE user_id=? AND product_id=?');
      $c->bind_param('ii', $uid, $pid);
      $c->execute();
      $c->store_result();
      if ($c->num_rows) {
        $c->bind_result($cid, $oldQty);
        $c->fetch();
        $newQty = $oldQty + $qty;
        $u = $conn->prepare('UPDATE cart SET quantity=? WHERE id=?');
        $u->bind_param('ii', $newQty, $cid);
        $u->execute();
        $u->close();
      } else {
        $ins = $conn->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?)');
        $ins->bind_param('iii', $uid, $pid, $qty);
        $ins->execute();
        $ins->close();
      }
      $c->close();
      $add_message = 'Added to cart.';
    }
    $p->close();
  }
} elseif (
  $_SERVER['REQUEST_METHOD'] === 'POST'
  && !isset($_SESSION['user_id'])
) {
  header('Location: login.php');
  exit;
}

// Pagination
$perPage = 9;
$page = isset($_GET['page']) && ctype_digit((string) $_GET['page']) ? (int) $_GET['page'] : 1;
// Category filter
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
if ($category !== '' && strlen($category) > 50) { $category = substr($category,0,50); }
if ($page < 1) {
  $page = 1;
}
$offset = ($page - 1) * $perPage;
if ($category) {
  $countStmt = $conn->prepare('SELECT COUNT(*) AS c FROM products WHERE category=?');
  $countStmt->bind_param('s', $category);
  $countStmt->execute();
  $countRes = $countStmt->get_result();
  $countRow = $countRes->fetch_assoc();
  $total = (int) ($countRow['c'] ?? 0);
  $countStmt->close();
} else {
  $countQuery = $conn->query('SELECT COUNT(*) AS c FROM products');
  $countRow = $countQuery->fetch_assoc();
  $total = (int) $countRow['c'];
}
if ($total > 0) {
  $totalPages = (int) ceil($total / $perPage);
} else {
  $totalPages = 1;
}

$list = [];
if ($category) {
  $q = $conn->prepare('SELECT id, name, description, price, image, category FROM products WHERE category=? ORDER BY id DESC LIMIT ? OFFSET ?');
  $q->bind_param('sii', $category, $perPage, $offset);
} else {
  $q = $conn->prepare('SELECT id, name, description, price, image, category FROM products ORDER BY id DESC LIMIT ? OFFSET ?');
  $q->bind_param('ii', $perPage, $offset);
}
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $list[] = $row;
}
$q->close();

// Open grid container
echo '<div class="product-grid"';
if ($add_message) {
  echo ' data-toast="' . htmlspecialchars($add_message, ENT_QUOTES) . '"';
}
echo '>';

foreach ($list as $row) {
  $pid = (int) $row['id'];
  $img = htmlspecialchars($row['image'] ?: 'assets/placeholder.png');
  $name = htmlspecialchars($row['name']);
  $desc = htmlspecialchars(substr($row['description'], 0, 120)) . '...';
  $price = number_format($row['price'], 2);
?>
  <div class="card card-compact product-card">
    <a href="product.php?id=<?=$pid?>">
      <img src="<?=$img?>" alt="" class="img-card-md">
    </a>
    <h3 class="space-top-md">
      <a href="product.php?id=<?=$pid?>" class="product-card-title"><?=$name?></a>
    </h3>
    <p class="product-card-price space-top-sm"><?=$desc?></p>
    <div class="space-top-md row-between gap-xs">
      <div class="product-card-price price-sm">$<?=$price?></div>
      <form method="post" class="row-inline gap-xs">
        <input type="hidden" name="add_product_id" value="<?=$pid?>">
        <input type="number" name="quantity" value="1" min="1" class="qty-input">
        <button class="btn btn-primary btn-sm">Add</button>
      </form>
    </div>
  </div>
<?php
}

echo '</div>';

if ($totalPages > 1) {
  echo '<nav class="pager">';
  if ($page > 1) {
  $base = 'products.php';
  $queryCat = $category ? '&category=' . urlencode($category) : '';
  echo '<a href="' . $base . '?page=' . ($page - 1) . $queryCat . '">&laquo; Prev</a>';
  } else {
    echo '<span>&laquo; Prev</span>';
  }
  for ($p = 1; $p <= $totalPages; $p++) {
    if ($p == $page) {
      echo '<span class="active">' . $p . '</span>';
    } else {
  echo '<a href="products.php?page=' . $p . ($category ? '&category=' . urlencode($category) : '') . '">' . $p . '</a>';
    }
  }
  if ($page < $totalPages) {
  echo '<a href="products.php?page=' . ($page + 1) . ($category ? '&category=' . urlencode($category) : '') . '">Next &raquo;</a>';
  } else {
    echo '<span>Next &raquo;</span>';
  }
  echo '</nav>';
}

echo '</main>';
include 'footer.php';
echo '<script src="../js/script.js"></script>';
echo '</body>';
echo '</html>';
?>

