<?php layout_start('Users', 'users'); ?>

<?php if ($msg = flash('success')): ?>
  <div class="alert alert-success" style="margin-bottom:1rem"><?= e($msg) ?></div>
<?php endif; ?>
<?php if ($err = flash('error')): ?>
  <div class="alert alert-error" style="margin-bottom:1rem"><?= e($err) ?></div>
<?php endif; ?>

<div class="table-card">
  <div class="toolbar">
    <span class="toolbar-title">All Users</span>
    <a href="/admin/users/create" class="btn btn-primary btn-sm">+ New User</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Created</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($users)): ?>
          <tr><td colspan="8" class="empty">No users found.</td></tr>
        <?php else: foreach ($users as $u): ?>
          <tr>
            <td class="muted"><?= (int) $u->id ?></td>
            <td class="bold"><?= e($u->name) ?></td>
            <td class="muted"><?= e($u->username) ?></td>
            <td class="muted"><?= e($u->email ?: '—') ?></td>
            <td><?= role_badge($u->role, true) ?></td>
            <td><?= $u->is_active
                  ? '<span class="badge badge-connected">● Active</span>'
                  : '<span class="badge badge-inactive">● Inactive</span>' ?></td>
            <td class="muted"><?= date('d M Y', strtotime($u->created_at)) ?></td>
            <td>
              <div style="display:flex;gap:.4rem;justify-content:flex-end">
                <a href="/admin/users/edit?id=<?= (int) $u->id ?>" class="btn btn-outline btn-sm">Edit</a>
                <?php if ((int) $u->id !== auth()->id()): ?>
                  <form method="POST" action="/admin/users/toggle" onsubmit="return confirm('<?= $u->is_active ? 'Deactivate' : 'Reactivate' ?> this user?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $u->id ?>">
                    <button type="submit" class="btn btn-sm <?= $u->is_active ? 'btn-danger' : 'btn-green' ?>">
                      <?= $u->is_active ? 'Deactivate' : 'Reactivate' ?>
                    </button>
                  </form>
                <?php else: ?>
                  <span class="btn btn-outline btn-sm" style="opacity:.4;cursor:default">You</span>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php layout_end(); ?>
