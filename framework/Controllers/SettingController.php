<?php

namespace Framework\Controllers;

class SettingController
{
    public function setPassword()
    {

        // Verify CSRF
        if (!csrf()->verify($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid security token');
            back();
        }

        if (auth()->check()) {
            $user = auth()->user();
            $errors = $this->validatePassword($_POST, $user);
            if (empty($errors)) {
                $user->password = password_hash($_POST['new-password'], PASSWORD_DEFAULT);
                $user->save();

                flash('success', 'Congratulations! You have successfully set your password.');
                back();
            }
            flash('errors', $errors);
            back();
        } else {
            flash('error', 'You are not loggen. Please login and try setting password.');
            redirect('/login.php');
        }
    }


    protected function  validatePassword($data, $user)
    {
        $errors = [];
        $password = $data['new-password'];
        if(!$password){
            $errors['new-password'] = 'Password field cannot be empty!';
        }

        if (!password_verify($data['geekkey'], $user->password)) {
            $errors['geekkey'] = 'Sorry your geekey does not match!';
        }

        if ($data['new-password'] !== $data['confirm-password']) {
            $errors['password_confirmation'] = 'Sorry password confirmation does not match!';
        }
        return $errors;
    }
}
