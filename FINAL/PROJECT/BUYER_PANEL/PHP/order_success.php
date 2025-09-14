<?php
// Simple version without alternative syntax (no endif;, else:)
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
$email_failed_flag = isset($_SESSION['email_failed']) ? $_SESSION['email_failed'] : null;
$email_error_msg = isset($_SESSION['email_error']) ? $_SESSION['email_error'] : '';
unset($_SESSION['email_failed'], $_SESSION['email_error']);

include 'main.php'; // header + open <main>

echo '<div class="card-centered">';
echo '<h2 class="heading-page">Order Confirmed</h2>';

if ($order_id) {
    echo '<p>Thank you — your order #' . $order_id . ' has been placed successfully.</p>';

    // attempt to show payment method if column exists
    $showPay = false; $payLabel = '';
    $colRes = $conn->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");
    if ($colRes && $colRes->num_rows > 0) {
        $stmtPm = $conn->prepare("SELECT payment_method FROM orders WHERE id = ? LIMIT 1");
        $stmtPm->bind_param('i', $order_id);
        if ($stmtPm->execute()) {
            $pmRes = $stmtPm->get_result();
            if ($rowPm = $pmRes->fetch_assoc()) {
                if (!empty($rowPm['payment_method'])) {
                    $showPay = true;
                    $pmVal = strtolower($rowPm['payment_method']);
                    if ($pmVal === 'cod') { $payLabel = 'Cash On Delivery'; }
                    else { $payLabel = htmlspecialchars($rowPm['payment_method']); }
                }
            }
        }
        $stmtPm->close();
    }
    if ($showPay) {
        echo '<div class="status-block status-neutral">Payment Method: ' . $payLabel . '</div>';
    }

    if ($email_failed_flag !== null) {
        if ($email_failed_flag) {
            $msg = htmlspecialchars($email_error_msg ?: 'error');
            echo '<div class="status-block status-error">Receipt email could not be sent (' . $msg . ').</div>';
        } else {
            echo '<div class="status-block status-success">A receipt has been emailed to you.</div>';
        }
    }

    echo '<p class="para-spaced">'
        . '<a href="products.php" class="link-primary">Continue shopping</a> or '
        . '<a href="profile.php" class="link-primary">view profile</a>.'
        . '</p>';
} else {
    echo '<p>No order information found.</p>';
    echo '<p class="para-spaced"><a href="products.php" class="link-primary">Return to products</a></p>';
}

echo '</div>';

include 'footer.php';
?>
</main>
<script src="../js/script.js"></script>
</body>
</html>
