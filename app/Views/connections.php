<?php layout_start('Connection Requests', 'connections'); ?>

<?php if (input('saved')): ?>
<div class="alert alert-success" style="margin-bottom:1rem;">Record updated successfully.</div>
<?php endif; ?>

<div class="table-card">
  <div class="toolbar">
    <div class="toolbar-title">ISP Connection Requests
      <span style="font-size:.75rem;font-weight:400;color:var(--muted);margin-left:.5rem;"><?= $total ?> total</span>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
      <form method="GET" class="filter-row">
        <input type="text" name="search" placeholder="Name or mobile…" value="<?= e($search) ?>">
        <select name="status">
          <option value="">All statuses</option>
          <?php foreach (['new','contacted','connected','cancelled'] as $s): ?>
          <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Filter</button>
        <?php if ($search || $status): ?>
        <a href="connections.php" class="btn btn-outline btn-sm">Clear</a>
        <?php endif; ?>
      </form>
      <a href="export.php?type=connections" class="btn btn-outline btn-sm">⬇ CSV</a>
    </div>
  </div>

  <?php if ($rows): ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Name</th><th>Mobile</th><th>Area</th>
          <th>Plan</th><th>Status</th><th>Received</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <?php $did = 'cr-' . $r['id']; ?>
        <tr>
          <td class="muted"><?= $r['id'] ?></td>
          <td class="bold"><?= e($r['name']) ?></td>
          <td><?= e($r['mobile']) ?></td>
          <td><?= e($r['area'] ?: '—') ?></td>
          <td class="plan"><?= e($r['plan'] ?: '—') ?></td>
          <td><?= status_badge($r['status']) ?></td>
          <td class="muted"><?= time_ago($r['created_at']) ?></td>
          <td><button class="expand-btn" onclick="toggleRow(this,'<?= $did ?>')">▼</button></td>
        </tr>
        <tr id="<?= $did ?>" class="row-detail">
          <td colspan="8">
            <div class="detail-grid">
              <div><div class="detail-label">Address</div><div class="detail-val"><?= e($r['address'] ?: '—') ?></div></div>
              <div><div class="detail-label">Message</div><div class="detail-val"><?= e($r['message'] ?: '—') ?></div></div>
              <div><div class="detail-label">Notes</div><div class="detail-val"><?= e($r['notes'] ?: '—') ?></div></div>
              <div><div class="detail-label">Full Plan</div><div class="detail-val"><?= e($r['plan'] ?: '—') ?></div></div>
              <div><div class="detail-label">Received</div><div class="detail-val"><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></div></div>
              <div><div class="detail-label">IP</div><div class="detail-val"><?= e($r['ip'] ?: '—') ?></div></div>
            </div>
            <form method="POST" action="update-status.php" class="action-row">
              <?= csrf_field() ?>
              <input type="hidden" name="type"  value="connection">
              <input type="hidden" name="id"    value="<?= (int)$r['id'] ?>">
              <select name="status" class="status-sel">
                <?php foreach (['new','contacted','connected','cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= $r['status']===$s ? 'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
              <textarea name="notes" class="notes-ta" placeholder="Add notes…"><?= e($r['notes'] ?? '') ?></textarea>
              <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pages > 1): ?>
  <div class="pager">
    <span class="pager-info">Page <?= $page ?> of <?= $pages ?></span>
    <?php for ($p = 1; $p <= $pages; $p++): ?>
    <?php $q = http_build_query(['status' => $status, 'search' => $search, 'page' => $p]); ?>
    <?php if ($p === $page): ?><span class="cur"><?= $p ?></span>
    <?php else: ?><a href="connections.php?<?= $q ?>"><?= $p ?></a><?php endif; ?>
    <?php endfor; ?>
  </div>
  <?php endif; ?>

  <?php else: ?>
  <div class="empty">No records found<?= ($search || $status) ? ' for this filter' : '' ?>.</div>
  <?php endif; ?>
</div>

<?php layout_end(); ?>
