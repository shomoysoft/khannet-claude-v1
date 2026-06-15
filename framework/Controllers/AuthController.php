<?php

namespace Framework\Controllers;

use Framework\Mail\PHPMailerMailer;
use Framework\Mail\SendGridMailer;
use Framework\Mail\WelcomeMail;
use Framework\Models\User;

class AuthController
{

    /**
     * Handle registration display logic
     */
    public function registerView()
    {
        if (auth()->check()) {
            redirect('dashboard.php');
        }

        $step = session()->get('step');

        // Handle policy agreement
        if ($step === 'welcome') {
            return [
                'step' => $step,
                'username' => flash('username'),
                'password' => flash('password'),
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agree_policy'])) {
            session()->set('step', 'form');
            $step = 'form';
        }

        return [
            'step' => $step,
            'errors' => flash('errors') ?? [],
            'old' => flash('old') ?? []
        ];
    }

    /**
     * Handle registration submission
     */
    public function registerSubmit()
    {
        if (!csrf()->verify($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid security token');
            redirect('/register.php');
        }

        $errors = $this->validateRegistration($_POST);

        if (!empty($errors)) {
            flash('errors', $errors);
            flash('old', $_POST);
            session()->set('step', 'form');
            redirect('/register.php');
        }

        try {
            $password = $this->generate_token();
            $user = new User([
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $user->save();

            $mailer = (new WelcomeMail(
                [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => $password,
                    'subject' => "DuaneCook - Welcome",
                ],
                new PHPMailerMailer()
            ))->sendMail();
            // echo 'sdfsdf'; return;
            flash('username', $_POST['username']);
            flash('password', $password);
            session()->set('step', 'welcome');
            redirect('/register.php');
        } catch (\Exception $e) {
            flash('error', 'Registration failed. Please try again.');
            flash('old', $_POST);
            session()->set('step', 'form');
            redirect('/register.php');
        }
    }

    /**
     * Validate registration data
     */
    private function validateRegistration($data)
    {
        $errors = [];

        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');

        if (empty($username)) {
            $errors['username'] = 'username is required';
        } else {
            $existingUser = User::first('username', $username);
            if ($existingUser) {
                $errors['username'] = 'Username already registered';
            }
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        } else {
            $existingUser = User::first('email', $email);
            if ($existingUser) {
                $errors['email'] = 'Email already registered';
            }
        }

        return $errors;
    }

    /**
     * Handle login display
     */
    public function loginView()
    {
        if (auth()->check()) {
            redirect('dashboard.php');
        }

        return [
            'error' => flash('error'),
            'success' => flash('success'),
            'old' => flash('old') ?? []
        ];
    }

    /**
     * Handle login submission
     */
    public function loginSubmit()
    {
        // Verify CSRF
        if (!csrf()->verify($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid security token');
            redirect('/login.php');
        }

        $remember = isset($_POST['remember']);

        if (auth()->attempt($_POST, $remember)) {
            flash('success', 'Welcome back!');
            redirect('/dashboard.php');
        } else {
            flash('error', 'Invalid email or password');
            flash('old', ['email' => $_POST['email'] ?? '']);
            redirect('/login.php');
        }
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        auth()->logout();
        flash('success', 'You have been logged out successfully.');
        redirect('login.php');
    }

    public function generate_token($length = 32)
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}
