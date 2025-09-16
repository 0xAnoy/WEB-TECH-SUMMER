<?php
require_once "config.php";
require_once "email.php";
session_start();

// Auto-create password_resets table if missing
$conn->query("CREATE TABLE IF NOT EXISTS password_resets (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, token_hash CHAR(64) NOT NULL, expires_at INT NOT NULL, used TINYINT(1) DEFAULT 0, created_at INT NOT NULL, INDEX(user_id), INDEX(token_hash)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email !== '') {
        $stmt = $conn->prepare("SELECT id,name FROM users WHERE email = ?");
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($uid,$name);
            $stmt->fetch();
            // generate token
            $raw = bin2hex(random_bytes(16));
            $hash = hash('sha256', $raw);
            $expires = time() + 1800; // 30m
            $created = time();
            // store
            $ins = $conn->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at, created_at) VALUES (?,?,?,?)");
            $ins->bind_param('ssii', $uid, $hash, $expires, $created);
            $ins->execute();
            $ins->close();
            // link
            $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
            $link = rtrim($base,'/').'/reset_password.php?token='.$raw;
            $send = send_password_reset_email($SMTP_CONFIG, $email, $name ?: 'User', $link, 30);
            if (!$send['ok']) {
                echo "<p class='form-error'>Failed to send reset email: ".htmlspecialchars(substr($send['error'],0,120))."</p>";
            }
        }
        $submitted = true; // always show generic confirmation
        $stmt->close();
    } else {
        echo "<p class='form-error'>Email is required!</p>";
    }
}

include "main.php";
?>
<div class="card form-card">
  <h2>Forgot Password</h2>
  <?php if ($submitted): ?>
    <p>If an account with that email exists, a password reset link has been sent.</p>
    <p><a href="login.php" class="link-primary">Return to login</a></p>
  <?php else: ?>
    <form method="post" autocomplete="off">
      <input name="email" type="email" class="form-input" placeholder="Your account email" required autofocus>
      <div class="form-actions"><button class="btn btn-primary btn-block">Send Reset Link</button></div>
    </form>
    <p class="mt-3 text-sm"><a href="login.php" class="text-blue-600">Back to login</a></p>
  <?php endif; ?>
</div>
</main>
<?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>
