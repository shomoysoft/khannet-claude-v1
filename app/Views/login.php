<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — KhanNet Admin</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-page">
  <div class="login-box">

    <div class="login-brand">
      <div class="login-brand-icon">KN</div>
      KhanNet Admin
    </div>

    <h1 class="login-title">Sign in</h1>
    <p class="login-sub">Admin access only</p>

    <?php if (isset($_GET['expired'])): ?>
      <div class="alert alert-info">Your session expired. Please sign in again.</div>
    <?php endif; ?>

    <?php if ($err = flash('error')): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus
               value="<?= e(input('username', '')) ?>" placeholder="admin">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit" class="login-btn">Sign in →</button>
    </form>

  </div>
</div>
</body>
</html>
