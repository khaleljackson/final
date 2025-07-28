<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

redirectIfNotLoggedIn();

if (!isset($_GET['id'])) {
    header("Location: student_dashboard.php");
    exit();
}

$user = getCurrentUser($mysqli);
$complaint_id = (int)$_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM complaints WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $complaint_id, $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();

if (!$complaint) {
    header("Location: student_dashboard.php");
    exit();
}

$status_text = '';
switch ($complaint['status']) {
    case 'pending': $status_text = 'قيد الانتظار'; break;
    case 'in_progress': $status_text = 'قيد المعالجة'; break;
    case 'resolved': $status_text = 'تم الحل'; break;
    case 'rejected': $status_text = 'مرفوض'; break;
}
?>

<div class="container mt-5">
    <h2>تفاصيل الشكوى</h2>
    <div class="card p-4">
        <h4><?= htmlspecialchars($complaint['subject']) ?></h4>
        <p class="mt-3"><?= nl2br(htmlspecialchars($complaint['description'])) ?></p>
        <p><strong>الحالة:</strong> 
            <span class="badge bg-<?= $complaint['status'] == 'pending' ? 'warning' : ($complaint['status'] == 'in_progress' ? 'info' : ($complaint['status'] == 'resolved' ? 'success' : 'danger')) ?>">
                <?= $status_text ?>
            </span>
        </p>
        <p><strong>تاريخ الإرسال:</strong> <?= date('Y-m-d H:i', strtotime($complaint['created_at'])) ?></p>
        <?php if ($complaint['updated_at'] != $complaint['created_at']): ?>
        <p><strong>آخر تحديث:</strong> <?= date('Y-m-d H:i', strtotime($complaint['updated_at'])) ?></p>
        <?php endif; ?>
    </div>
    <a href="student_dashboard.php" class="btn btn-primary mt-3">العودة للوحة التحكم</a>
</div>

<?php include 'includes/footer.php'; ?>