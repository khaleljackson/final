<?php
require_once '../config/database.php';
require_once '../includes/admin_auth.php';
require_once '../includes/admin_header.php';

// جلب جميع المستخدمين
$result = $mysqli->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>إدارة المستخدمين</h2>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم الكامل</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ التسجيل</th>
                        <th>عدد الشكاوى</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php
                            $stmt_count = $mysqli->prepare("SELECT COUNT(*) as count FROM complaints WHERE user_id = ?");
                            $stmt_count->bind_param("i", $user['id']);
                            $stmt_count->execute();
                            $count_result = $stmt_count->get_result();
                            echo $count_result->fetch_assoc()['count'];
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>