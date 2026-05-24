<?php require_once '../../includes/header.php'; ?>
<?php require_once '../../includes/navbar.php'; ?>

<div class="container">
    <h2 style="color: #1B3A6B; margin-bottom: 20px;">Admin Dashboard</h2>

    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div class="card" style="flex: 1; text-align: center;">
            <h3 style="color: #1B3A6B; font-size: 2rem;">0</h3>
            <p style="color: #555555;">Total Complaints</p>
        </div>
        <div class="card" style="flex: 1; text-align: center;">
            <h3 style="color: #E8A020; font-size: 2rem;">0</h3>
            <p style="color: #555555;">Open</p>
        </div>
        <div class="card" style="flex: 1; text-align: center;">
            <h3 style="color: #2E6DB4; font-size: 2rem;">0</h3>
            <p style="color: #555555;">In Review</p>
        </div>
        <div class="card" style="flex: 1; text-align: center;">
            <h3 style="color: #2E7D32; font-size: 2rem;">0</h3>
            <p style="color: #555555;">Resolved</p>
        </div>
    </div>

    <div class="card">
        <h3 style="color: #1B3A6B; margin-bottom: 20px;">Recent Complaints</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Submitted By</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" style="text-align: center; color: #555555; padding: 30px;">
                        No complaints to display yet.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>