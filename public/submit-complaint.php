<?php
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/validation.php';
require_once '../src/Database.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /vent-then-validate/public/login.php');
    exit();
}

$errors = [];
$success = '';
$title = '';
$description = '';
$category_id = '';

// Fetch categories from database
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT * FROM categories ORDER BY name ASC');
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $category_id = sanitize($_POST['category_id']);
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);

    // Validate inputs
    $errors = validateComplaint($category_id, $title, $description);

    if (empty($errors)) {
        try {
            $stmt = $db->prepare('
                INSERT INTO complaints (user_id, category_id, title, description, status)
                VALUES (:user_id, :category_id, :title, :description, :status)
            ');
            $stmt->execute([
                ':user_id'     => $_SESSION['user_id'],
                ':category_id' => $category_id,
                ':title'       => $title,
                ':description' => $description,
                ':status'      => 'Open'
            ]);

            $success = 'Your complaint has been submitted successfully! We will review it shortly.';
            $title = '';
            $description = '';
            $category_id = '';
        } catch (PDOException $e) {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}
?>

<div class="container">
    <div class="card" style="max-width: 700px; margin: 40px auto;">
        <h2 style="color: #1B3A6B; margin-bottom: 5px;">Submit a Complaint</h2>
        <p style="color: #555555; margin-bottom: 25px;">Fill out the form below and we will get back to you as soon as possible.</p>

        <?php displayErrors($errors); ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="submit-complaint.php">
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category_id" required>
                    <option value="">-- Select a Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>"
                            <?php echo ($category_id == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Complaint Title</label>
                <input type="text" id="title" name="title"
                    placeholder="Brief summary of your complaint"
                    value="<?php echo $title; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="6"
                    placeholder="Please describe your complaint in detail..."
                    required><?php echo $description; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Complaint</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>