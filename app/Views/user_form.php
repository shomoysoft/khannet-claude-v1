<?php layout_start($editing ? 'Edit User' : 'New User', 'users'); ?>

<?php if ($err = flash('error')): ?>
  <div class="alert alert-error" style="margin-bottom:1rem"><?= e($err) ?></div>
<?php endif; ?>

<div class="table-card" style="max-width:560px">
  <div class="toolbar">
    <span class="toolbar-title"><?= $editing ? 'Edit User' : 'New User' ?></span>
    <a href="/admin/users" class="btn btn-outline btn-sm">← Back</a>
  </div>

  <form method="POST" action="<?= $editing ? '/admin/users/edit' : '/admin/users/create' ?>" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
    <?= csrf_field() ?>
    <?php if ($editing): ?>
      <input type="hidden" name="id" value="<?= (int) $user->id ?>">
    <?php endif; ?>

    <div class="form-group" style="margin-bottom:0">
      <label for="uf-name">Full Name</label>
      <input type="text" id="uf-name" name="name" required maxlength="100"
             value="<?= e($user?->name ?? old('name', '')) ?>" placeholder="e.g. Md. Jubaer Hossain">
    </div>

    <?php if (!$editing): ?>
    <div class="form-group" style="margin-bottom:0">
      <label for="uf-username">Username</label>
      <input type="text" id="uf-username" name="username" required maxlength="50"
             value="<?= e(old('username', '')) ?>" placeholder="e.g. jubaer" autocomplete="off">
    </div>
    <?php else: ?>
    <div class="form-group" style="margin-bottom:0">
      <label>Username</label>
      <input type="text" value="<?= e($user->username) ?>" disabled style="opacity:.6;cursor:not-allowed">
    </div>
    <?php endif; ?>

    <div class="form-group" style="margin-bottom:0">
      <label for="uf-email">Email <span style="font-weight:400;color:var(--muted)">(optional)</span></label>
      <input type="email" id="uf-email" name="email" maxlength="150"
             value="<?= e($user?->email ?? old('email', '')) ?>" placeholder="e.g. admin@khannet.com">
    </div>

    <div class="form-group" style="margin-bottom:0">
      <label for="uf-password">
        Password
        <?php if ($editing): ?>
          <span style="font-weight:400;color:var(--muted)">(leave blank to keep current)</span>
        <?php endif; ?>
      </label>
      <div class="input-wrap">
        <input type="password" id="uf-password" name="password" maxlength="100"
               <?= !$editing ? 'required' : '' ?> placeholder="••••••••" minlength="8" autocomplete="new-password">
        <button type="button" class="input-eye" id="toggleUfPwd" aria-label="Show password">
          <svg id="ufEyeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="form-group" style="margin-bottom:0">
      <label for="uf-role">Role</label>
      <select id="uf-role" name="role" style="padding:.75rem 1rem;border:1.5px solid var(--border);border-radius:8px;font-size:.94rem;font-family:inherit;outline:none;background:var(--card);color:var(--text)">
        <?php foreach (\App\Models\User::ROLES as $r): ?>
          <option value="<?= $r ?>" <?= ($user?->role ?? 'viewer') === $r ? 'selected' : '' ?>>
            <?= \App\Models\User::ROLE_LABELS[$r] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:.5rem">
      <button type="submit" class="btn btn-primary">
        <?= $editing ? 'Save Changes' : 'Create User' ?>
      </button>
      <a href="/admin/users" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

<script>
  const ufPwd  = document.getElementById('uf-password');
  const ufBtn  = document.getElementById('toggleUfPwd');
  const ufIcon = document.getElementById('ufEyeIcon');
  const open   = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  const slash  = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
  ufBtn.addEventListener('click', () => {
    const show = ufPwd.type === 'password';
    ufPwd.type = show ? 'text' : 'password';
    ufIcon.innerHTML = show ? slash : open;
  });
</script>

<?php layout_end(); ?>
