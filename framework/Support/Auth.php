<?php
namespace Framework\Support;

use Framework\Models\User;

class Auth {
    
    /**
     * Check if user is authenticated
     */
    public static function check() {
        return Session::has('user_id');
    }
    
    /**
     * Check if user is guest (not authenticated)
     */
    public static function guest() {
        return !self::check();
    }
    
    /**
     * Get authenticated user ID
     */
    public static function id() {
        return Session::get('user_id');
    }
    
    /**
     * Get authenticated user object
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        $userId = self::id();
        return User::find($userId);
    }
    
    /**
     * Login user
     */
    public static function login(User $user, $remember = false) {
        // Regenerate session ID to prevent session fixation
        Session::regenerate();
        
        // Store user information
        Session::set('user_id', $user->id);
        Session::set('user_name', $user->name);
        Session::set('user_email', $user->email);
        
        // Remember me functionality (optional)
        if ($remember) {
            self::setRememberToken($user);
        }
        
        return true;
    }
    
    /**
     * Login user by ID
     */
    public static function loginById($userId, $remember = false) {
        $user = User::find($userId);
        
        if ($user) {
            return self::login($user, $remember);
        }
        
        return false;
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        // Remove remember me cookie
        self::clearRememberToken();
        
        // Destroy session
        Session::destroy();
    }
    
    /**
     * Attempt login with credentials
     */
    public static function attempt(array $credentials, $remember = false) {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;
        
        if (!$email || !$password) {
            return false;
        }
        
        // Find user by email
        $user = User::first('email', $email);
        
        if (!$user) {
            return false;
        }
        
        // Verify password
        if (!password_verify($password, $user->password)) {
            return false;
        }
        
        // Login user
        return self::login($user, $remember);
    }
    
    /**
     * Require authentication (middleware-like)
     */
    public static function requireAuth($redirectTo = '/login.php') {
        if (self::guest()) {
            Session::flash('error', 'Please login to continue');
            Session::flash('intended_url', $_SERVER['REQUEST_URI']);
            header("Location: $redirectTo");
            exit;
        }
    }
    
    /**
     * Require guest (redirect if authenticated)
     */
    public static function requireGuest($redirectTo = '/dashboard.php') {
        if (self::check()) {
            header("Location: $redirectTo");
            exit;
        }
    }
    
    /**
     * Get intended URL (where user wanted to go before login)
     */
    public static function intended($default = '/dashboard.php') {
        $intended = Session::getFlash('intended_url', $default);
        return $intended;
    }
    
    /**
     * Set remember token (optional feature)
     */
    private static function setRememberToken(User $user) {
        $token = bin2hex(random_bytes(32));
        
        // Store token in database (you'd need to add remember_token column)
        // $user->remember_token = hash('sha256', $token);
        // $user->save();
        
        // Set cookie (30 days)
        setcookie(
            'remember_token',
            $token,
            time() + (30 * 24 * 60 * 60),
            '/',
            '',
            false,
            true
        );
    }
    
    /**
     * Clear remember token
     */
    private static function clearRememberToken() {
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
}