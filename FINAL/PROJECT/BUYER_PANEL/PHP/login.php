<?php

require_once "config.php";
require_once "email.php"; // for send_login_otp
session_start();

// If already logged in skip
if (!empty($_SESSION['user_id'])) {
  header('Location: profile.php');
  exit;
}

$otpStage = false; // flag to show OTP form
$now = time();

// Handle resend request (GET) if OTP pending
if (isset($_GET['resend']) && isset($_SESSION['pending_login']['user_id'])) {
  $pending = $_SESSION['pending_login'];
  if ($pending['expires'] > $now) {
    // regenerate OTP
    $otp = random_int(100000, 999999);
    $_SESSION['pending_login']['otp'] = $otp;
    $_SESSION['pending_login']['generated'] = $now;
    $_SESSION['pending_login']['expires'] = $now + 300; // 5 min
    $res = send_login_otp($SMTP_CONFIG, $pending['email'], $pending['name'] ?: 'User', (string)$otp);
    if (!$res['ok']) {
      echo "<p class='form-error'>Failed to resend OTP: " . htmlspecialchars(substr($res['error'],0,120)) . "</p>";
    } else {
      echo "<p class='text-green-600 mb-2'>New OTP sent.</p>";
    }
    $otpStage = true;
  } else {
    // expired, drop pending
    unset($_SESSION['pending_login']);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Stage 2: OTP submission
  if (isset($_POST['otp']) && isset($_SESSION['pending_login']['user_id'])) {
    $code = trim($_POST['otp']);
    $pending = $_SESSION['pending_login'];
    if ($pending['expires'] < $now) {
      echo "<p class='form-error'>OTP expired. Please login again.</p>";
      unset($_SESSION['pending_login']);
    } elseif ($code === '' ) {
      echo "<p class='form-error'>OTP is required!</p>";
      $otpStage = true;
    } elseif (!ctype_digit($code) || strlen($code) !== 6) {
      echo "<p class='form-error'>OTP must be 6 digits.</p>";
      $otpStage = true;
    } elseif ($code != $pending['otp']) {
      echo "<div class='form-error otp-error'>Incorrect OTP.</div>";
      $otpStage = true;
    } else {
      // Success: finalize login
      $_SESSION['user_id'] = $pending['user_id'];
      $_SESSION['username'] = $pending['name'];
      $remember = !empty($pending['remember']);
      if ($remember) {
        setcookie('remember_user_id', $pending['user_id'], time()+86400*30, '/');
        setcookie('remember_username', $pending['name'], time()+86400*30, '/');
        setcookie('remember_email', $pending['email'], time()+86400*30, '/');
      } else {
        setcookie('remember_user_id', '', time()-3600, '/');
        setcookie('remember_username', '', time()-3600, '/');
        setcookie('remember_email', '', time()-3600, '/');
      }
      unset($_SESSION['pending_login']);
      header('Location: profile.php');
      exit;
    }
  } else {
    // Stage 1: Credentials submission
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);
    $invalid = false;

    if (empty($email)) {
      echo "<p class='form-error'>Email is required!</p>";
      $invalid = true;
    }
    if (empty($password)) {
      echo "<p class='form-error'>Password is required!</p>";
      $invalid = true;
    }
    if (!$invalid) {
      $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $stmt->store_result();
      if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
          // Build pending login and send OTP
          $otp = random_int(100000, 999999);
          $_SESSION['pending_login'] = [
            'user_id' => $id,
            'name' => $name,
            'email' => $email,
            'otp' => $otp,
            'generated' => $now,
            'expires' => $now + 300, // 5 min
            'remember' => $remember
          ];
          $res = send_login_otp($SMTP_CONFIG, $email, $name ?: 'User', (string)$otp);
          if (!$res['ok']) {
            echo "<p class='form-error'>Failed to send OTP: " . htmlspecialchars(substr($res['error'],0,120)) . "</p>";
            unset($_SESSION['pending_login']);
          } else {
            echo "<p class='text-green-600 mb-2'>OTP sent to your email. Enter it below.</p>";
            $otpStage = true;
          }
        } else {
          echo "<p class='form-error'>Invalid credentials.</p>";
        }
      } else {
        echo "<p class='form-error'>No account with that email.</p>";
      }
      $stmt->close();
    }
  }
}

include "main.php";
?>

<div class="card form-card">
  <h2>Login</h2>
  <?php if (isset($_GET['registered'])): ?>
    <div class="mb-4 text-green-600">Registration successful. Please login.</div>
  <?php endif; ?>

  <?php if ($otpStage && isset($_SESSION['pending_login'])): ?>
    <form method="post" autocomplete="off">
      <input name="otp" placeholder="Enter 6-digit OTP" class="form-input" maxlength="6" autofocus>
      <div class="flex gap-2 mt-2">
        <button class="btn btn-primary btn-block">Verify OTP</button>
      </div>
      <div class="mt-3 text-sm">
        <a href="login.php?resend=1" class="text-blue-600">Resend OTP</a> |
        <a href="login.php" class="text-blue-600">Start over</a>
      </div>
    </form>
  <?php else: ?>
    <!-- Stage 1 credentials form -->
    <form method="post" autocomplete="off">
      <input name="email" placeholder="Email" type="email" class="form-input" value="<?= isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : '' ?>" autofocus>
      <input name="password" placeholder="Password" type="password" class="form-input">
      <label class="remember-option">
        <input type="checkbox" name="remember" value="1" class="remember-checkbox"> Remember me
      </label>
      <div class="form-actions"><button class="btn btn-primary btn-block">Login</button></div>
    </form>
    <p class="mt-3 text-sm">Don't have an account? <a href="register.php" class="text-blue-600">Register</a></p>
  <?php endif; ?>
</div>

</main>

<?php include 'footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>
