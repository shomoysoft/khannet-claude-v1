<?php

namespace App\Controllers;

use Framework\Database\DB;
use Framework\Http\Request;
use App\Models\User;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('super_admin');
    }

    public function index(): void
    {
        $users = User::allOrdered();
        $this->view('users', compact('users'));
    }

    public function create(Request $request): void
    {
        $this->view('user_form', ['user' => null, 'editing' => false]);
    }

    public function store(Request $request): void
    {
        if (!csrf()->verify($request->input('csrf_token', ''))) {
            flash('error', 'Invalid security token.');
            redirect('/admin/users/create');
        }

        if (!$request->validate([
            'name'     => 'required|max:100',
            'username' => 'required|max:50',
            'email'    => 'nullable|email|max:150',
            'password' => 'required|min:8|max:100',
            'role'     => 'required|in:super_admin,admin,viewer',
        ])) {
            flash('error', $request->firstError());
            redirect('/admin/users/create');
        }

        if (User::findByUsername($request->input('username'))) {
            flash('error', 'That username is already taken.');
            redirect('/admin/users/create');
        }

        $data              = $request->validated();
        $data['is_active'] = 1;
        User::create($data);

        flash('success', 'User created successfully.');
        redirect('/admin/users');
    }

    public function edit(Request $request): void
    {
        $user = $this->findUserOr404((int) $request->input('id', 0));
        $this->view('user_form', ['user' => $user, 'editing' => true]);
    }

    public function update(Request $request): void
    {
        if (!csrf()->verify($request->input('csrf_token', ''))) {
            flash('error', 'Invalid security token.');
            redirect('/admin/users');
        }

        $user = $this->findUserOr404((int) $request->input('id', 0));

        $rules = [
            'name'  => 'required|max:100',
            'email' => 'nullable|email|max:150',
            'role'  => 'required|in:super_admin,admin,viewer',
        ];

        $newPassword = $request->input('password', '');
        if ($newPassword !== '') {
            $rules['password'] = 'min:8|max:100';
        }

        if (!$request->validate($rules)) {
            flash('error', $request->firstError());
            redirect('/admin/users/edit?id=' . $user->id);
        }

        $data = array_intersect_key($request->validated(), array_flip(['name', 'email', 'role']));

        if ($newPassword !== '') {
            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        // Prevent demoting the last super_admin
        if ($user->role === 'super_admin' && $data['role'] !== 'super_admin') {
            $superAdmins = count(array_filter(
                User::allOrdered(),
                fn($u) => $u->role === 'super_admin' && (bool) $u->is_active
            ));
            if ($superAdmins <= 1) {
                flash('error', 'Cannot demote the only super admin.');
                redirect('/admin/users/edit?id=' . $user->id);
            }
        }

        DB::table('users')->where('id', (int) $user->id)->update($data);

        flash('success', 'User updated successfully.');
        redirect('/admin/users');
    }

    public function toggle(Request $request): void
    {
        if (!csrf()->verify($request->input('csrf_token', ''))) {
            abort(403, 'Forbidden');
        }

        $user = $this->findUserOr404((int) $request->input('id', 0));

        if ((int) $user->id === auth()->id()) {
            flash('error', 'You cannot deactivate your own account.');
            redirect('/admin/users');
        }

        $newState = $user->is_active ? 0 : 1;
        DB::table('users')->where('id', (int) $user->id)->update(['is_active' => $newState]);

        flash('success', $newState ? 'User reactivated.' : 'User deactivated.');
        redirect('/admin/users');
    }

    private function findUserOr404(int $id): User
    {
        $user = User::find($id);
        if (!$user) {
            abort(404, 'User not found');
        }
        return $user;
    }
}
