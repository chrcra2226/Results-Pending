<?php
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/validation.php';
require_once '../src/Database.php';

$errors = [];
$success = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    $errors = validateRegistration($name, $email, $password, $confirm_password);

    if (empty($errors)) {
        try {
            $db = Database::getInstance()->getConnection();

            // Check if email already exists
            $stmt = $db->prepare('SELECT user_id FROM users WHERE email = :email');
            $stmt->execute([':email' => $email]);

            if ($stmt->fetch()) {
                $errors[] = 'An account with that email address already exists.';
            } else {
                // Hash the password
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                // Insert new user
                $stmt = $db->prepare('
                    INSERT INTO users (name, email, password_hash, role) 
                    VALUES (:name, :email, :password_hash, :role)
                ');
                $stmt->execute([
                    ':name'          => $name,
                    ':email'         => $email,
                    ':password_hash' => $password_hash,
                    ':role'          => 'customer'
                ]);

                $success = 'Account created successfully! You can now <a href="login.php">login here</a>.';
                $name = '';
                $email = '';
            }
        } catch (PDOException $e) {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: 40px auto;">
        <h2 style="color: #1B3A6B; margin-bottom: 5px;">Create an Account</h2>
        <p style="color: #555555; margin-bottom: 25px;">Join Vent Then Validate and start submitting complaints today.</p>

        <?php displayErrors($errors); ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name"
                    value="<?php echo $name; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address"
                    value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                    placeholder="Min 8 characters, at least one letter and number" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                    placeholder="Confirm your password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
        </form>

        <p style="text-align: center; margin-top: 20px; color: #555555;">
            Already have an account? <a href="login.php" style="color: #2E6DB4;">Login here</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>