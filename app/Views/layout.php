<?php

function layout_start(string $title, string $active): void
{
    $nc        = new_count('connection_requests');
    $nq        = new_count('shomoysoft_quotes');
    $total_new = $nc + $nq;
    $userRole  = session()->get('user_role', '');
    $userName  = session()->get('user_name', 'Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> — KhanNet Admin</title>
  <link rel="stylesheet" href="/assets/css/admin.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌐</text></svg>">
</head>
<body>
<div class="layout">

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">KN</div>
    KhanNet Admin
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Menu</div>
    <a href="/admin" class="nav-link <?= $active === 'dashboard'    ? 'active' : '' ?>">
      <span class="icon">📊</span> Dashboard
      <?php if ($total_new > 0): ?><span class="nav-badge"><?= $total_new ?></span><?php endif; ?>
    </a>
    <a href="/admin/connections" class="nav-link <?= $active === 'connections' ? 'active' : '' ?>">
      <span class="icon">🔌</span> Connections
      <?php if ($nc > 0): ?><span class="nav-badge"><?= $nc ?></span><?php endif; ?>
    </a>
    <a href="/admin/quotes" class="nav-link <?= $active === 'quotes' ? 'active' : '' ?>">
      <span class="icon">💻</span> Shomoysoft Quotes
      <?php if ($nq > 0): ?><span class="nav-badge"><?= $nq ?></span><?php endif; ?>
    </a>
    <?php if ($userRole === 'super_admin'): ?>
    <div class="nav-section" style="margin-top:1.25rem;">Management</div>
    <a href="/admin/users" class="nav-link <?= $active === 'users' ? 'active' : '' ?>">
      <span class="icon">👥</span> Users
    </a>
    <?php endif; ?>
    <div class="nav-section" style="margin-top:1.25rem;">Account</div>
    <a href="/admin/logout" class="nav-link">
      <span class="icon">🚪</span> Logout
    </a>
  </nav>
  <div class="sidebar-foot">KhanNet Admin · v1.0</div>
</aside>

<main class="main">
  <div class="topbar">
    <div class="topbar-title"><?= e($title) ?></div>
    <div style="display:flex;align-items:center;gap:1rem">
      <div class="topbar-meta"><?= date('d M Y, H:i') ?></div>
      <div style="display:flex;align-items:center;gap:.5rem;font-size:.8rem">
        <?= role_badge($userRole) ?>
        <span style="color:var(--muted)"><?= e($userName) ?></span>
      </div>
    </div>
  </div>
  <div class="page">
<?php
}

function layout_end(): void
{
?>
  </div>
</main>
</div>

<script>
function toggleRow(btn, id) {
  var row = document.getElementById(id);
  if (!row) return;
  var open = row.classList.toggle('open');
  btn.textContent = open ? '▲' : '▼';
}

document.addEventListener('change', function(e) {
  if (e.target.classList.contains('status-sel')) {
    e.target.closest('form').submit();
  }
});
</script>
</body>
</html>
<?php
}
