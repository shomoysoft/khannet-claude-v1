<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in — KhanNet Admin</title>
  <link rel="stylesheet" href="/assets/css/admin.css?v=2">
</head>
<body>
<div class="login-page">

  <!-- ── Left brand panel ── -->
  <div class="login-left">
    <div class="login-brand-mark">
      <div class="login-brand-icon">KN</div>
      KhanNet ISP
    </div>

    <div class="login-tagline">
      <h2>Manage your<br>network from<br>one place</h2>
      <p>Connection requests, quote management,<br>and customer tracking — all in one panel.</p>
    </div>

    <div class="login-features">
      <div class="login-feature"><span class="login-feature-dot"></span>Connection requests</div>
      <div class="login-feature"><span class="login-feature-dot"></span>Quote management</div>
      <div class="login-feature"><span class="login-feature-dot"></span>Customer tracking</div>
    </div>
  </div>

  <!-- ── Right form panel ── -->
  <div class="login-right">
    <div class="login-box">

      <div class="login-heading">
        <h1>Sign in</h1>
        <p>Enter your credentials to access the admin panel.</p>
      </div>

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
                 value="<?= e(input('username', '')) ?>"
                 placeholder="admin" autocomplete="username">
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <input type="password" id="password" name="password" required
                   placeholder="••••••••" autocomplete="current-password">
            <button type="button" class="input-eye" id="togglePwd" aria-label="Show password">
              <svg id="eyeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="login-btn">Sign in &rarr;</button>
      </form>

      <div class="login-foot">
        KhanNet ISP &copy; <?= date('Y') ?> &middot; Admin Panel
      </div>

    </div>
  </div>

</div>
<script>
  const pwd  = document.getElementById('password');
  const btn  = document.getElementById('togglePwd');
  const icon = document.getElementById('eyeIcon');
  const open  = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  const slash = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
  btn.addEventListener('click', () => {
    const show = pwd.type === 'password';
    pwd.type   = show ? 'text' : 'password';
    icon.innerHTML = show ? slash : open;
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
  });
</script>
</body>
</html>
