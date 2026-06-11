<?php layout_start('Dashboard', 'dashboard'); ?>

<div class="stats">
  <div class="stat hl-blue">
    <div class="stat-label">New Connections</div>
    <div class="stat-num"><?= (int)($cr['new'] ?? 0) ?></div>
  </div>
  <div class="stat hl-green">
    <div class="stat-label">Connected</div>
    <div class="stat-num"><?= (int)($cr['connected'] ?? 0) ?></div>
  </div>
  <div class="stat hl-primary">
    <div class="stat-label">Total Requests</div>
    <div class="stat-num"><?= (int)($cr['total'] ?? 0) ?></div>
  </div>
  <div class="stat hl-yellow">
    <div class="stat-label">Today (ISP)</div>
    <div class="stat-num"><?= (int)($cr['today'] ?? 0) ?></div>
  </div>
  <div class="stat hl-blue">
    <div class="stat-label">New Quotes</div>
    <div class="stat-num"><?= (int)($sq['new'] ?? 0) ?></div>
  </div>
  <div class="stat hl-green">
    <div class="stat-label">Completed Quotes</div>
    <div class="stat-num"><?= (int)($sq['completed'] ?? 0) ?></div>
  </div>
  <div class="stat hl-primary">
    <div class="stat-label">Total Quotes</div>
    <div class="stat-num"><?= (int)($sq['total'] ?? 0) ?></div>
  </div>
  <div class="stat hl-yellow">
    <div class="stat-label">Today (Quotes)</div>
    <div class="stat-num"><?= (int)($sq['today'] ?? 0) ?></div>
  </div>
</div>

<div class="two-col">

  <div class="card">
    <div class="card-head">
      <div class="card-head-title">🔌 Recent Connection Requests</div>
      <a href="/admin/connections" class="card-head-link">View all →</a>
    </div>
    <?php if ($recent_cr): ?>
    <ul class="mini-list">
      <?php foreach ($recent_cr as $r): ?>
      <li class="mini-item">
        <div class="mini-item-info">
          <div class="mini-item-name"><?= e($r['name']) ?></div>
          <div class="mini-item-sub"><?= e($r['mobile']) ?> · <?= e($r['area']) ?></div>
        </div>
        <div class="mini-item-meta">
          <?= status_badge($r['status']) ?><br>
          <span style="font-size:.72rem;"><?= time_ago($r['created_at']) ?></span>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <div class="mini-empty">No requests yet.</div>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-head">
      <div class="card-head-title">💻 Recent Shomoysoft Quotes</div>
      <a href="/admin/quotes" class="card-head-link">View all →</a>
    </div>
    <?php if ($recent_sq): ?>
    <ul class="mini-list">
      <?php foreach ($recent_sq as $r): ?>
      <li class="mini-item">
        <div class="mini-item-info">
          <div class="mini-item-name"><?= e($r['name']) ?></div>
          <div class="mini-item-sub"><?= e($r['mobile']) ?> · <?= e($r['service']) ?></div>
        </div>
        <div class="mini-item-meta">
          <?= status_badge($r['status']) ?><br>
          <span style="font-size:.72rem;"><?= time_ago($r['created_at']) ?></span>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <div class="mini-empty">No quotes yet.</div>
    <?php endif; ?>
  </div>

</div>

<?php layout_end(); ?>
