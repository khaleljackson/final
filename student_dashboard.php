<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

redirectIfNotLoggedIn();

$user = getCurrentUser($mysqli);
$stmt = $mysqli->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$complaints = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>مرحباً بك، <?= htmlspecialchars($user['full_name']) ?></h2>
        <a href="logout.php" class="btn btn-outline-danger">تسجيل الخروج</a>
    </div>
    
    <a href="add_complaint.php" class="btn btn-primary mb-3">إضافة شكوى جديدة</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>الموضوع</th>
                <th>الحالة</th>
                <th>التاريخ</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($complaints as $complaint): ?>
            <tr>
                <td><?= $complaint['id'] ?></td>
                <td><?= htmlspecialchars($complaint['subject']) ?></td>
                <td>
                    <?php
                    $status_class = '';
                    $status_text = '';
                    switch ($complaint['status']) {
                        case 'pending': $status_class = 'warning'; $status_text = 'قيد الانتظار'; break;
                        case 'in_progress': $status_class = 'info'; $status_text = 'قيد المعالجة'; break;
                        case 'resolved': $status_class = 'success'; $status_text = 'تم الحل'; break;
                        case 'rejected': $status_class = 'danger'; $status_text = 'مرفوض'; break;
                    }
                    ?>
                    <span class="badge bg-<?= $status_class ?>"><?= $status_text ?></span>
                </td>
                <td><?= date('Y-m-d', strtotime($complaint['created_at'])) ?></td>
                <td>
                    <a href="complaint_details.php?id=<?= $complaint['id'] ?>" class="btn btn-sm btn-info">عرض</a>
                    <?php if ($complaint['status'] == 'pending'): ?>
                    <a href="edit_complaint.php?id=<?= $complaint['id'] ?>" class="btn btn-sm btn-warning">تعديل</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php if (empty($complaints)): ?>
    <div class="text-center mt-5">
        <p>لا توجد شكاوى حالياً.</p>
        <a href="add_complaint.php" class="btn btn-primary">أضف شكوى جديدة</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>