<?php
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../src/Database.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /vent-then-validate/public/login.php');
    exit();
}

// Fetch complaints for logged in user
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('
        SELECT c.*, cat.name AS category_name 
        FROM complaints c
        JOIN categories cat ON c.category_id = cat.category_id
        WHERE c.user_id = :user_id
        ORDER BY c.created_at DESC
    ');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $complaints = $stmt->fetchAll();
} catch (PDOException $e) {
    $complaints = [];
}

// Badge helper
function getStatusBadge($status)
{
    $badges = [
        'Open'      => 'badge-open',
        'In Review' => 'badge-review',
        'Resolved'  => 'badge-resolved',
        'Closed'    => 'badge-closed'
    ];
    $class = isset($badges[$status]) ? $badges[$status] : 'badge-open';
    return '<span class="badge ' . $class . '">' . $status . '</span>';
}
?>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h2 style="color: #1B3A6B; margin-bottom: 5px;">My Complaints</h2>
                <p style="color: #555555;">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </div>
            <a href="submit-complaint.php" class="btn btn-primary">+ New Complaint</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($complaints)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #555555; padding: 30px;">
                            No complaints submitted yet.
                            <a href="submit-complaint.php" style="color: #2E6DB4;">Submit one now.</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?php echo $complaint['complaint_id']; ?></td>
                            <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['category_name']); ?></td>
                            <td><?php echo getStatusBadge($complaint['status']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>