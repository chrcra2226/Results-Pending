<?php
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/validation.php';
require_once '../src/Database.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    $errors = validateLogin($email, $password);

    if (empty($errors)) {
        try {
            $db = Database::getInstance()->getConnection();

            // Find user by email
            $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Store user info in session
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: /vent-then-validate/public/admin/dashboard.php');
                } else {
                    header('Location: /vent-then-validate/public/my-complaints.php');
                }
                exit();
            } else {
                $errors[] = 'Invalid email address or password. Please try again.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: 40px auto;">
        <h2 style="color: #1B3A6B; margin-bottom: 5px;">Welcome Back</h2>
        <p style="color: #555555; margin-bottom: 25px;">Login to your Vent Then Validate account.</p>

        <?php displayErrors($errors); ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address"
                    value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                    placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <p style="text-align: center; margin-top: 20px; color: #555555;">
            Don't have an account? <a href="register.php" style="color: #2E6DB4;">Register here</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>