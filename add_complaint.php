<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

redirectIfNotLoggedIn();

$user = getCurrentUser($mysqli);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = trim($_POST['subject']);
    $description = trim($_POST['description']);
    
    if (empty($subject) || empty($description)) {
        $error = 'جميع الحقول مطلوبة';
    } else {
        $stmt = $mysqli->prepare("INSERT INTO complaints (user_id, subject, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $subject, $description);
        
        if ($stmt->execute()) {
            $success = 'تم إرسال الشكوى بنجاح';
            header("refresh:2;url=student_dashboard.php");
        } else {
            $error = 'حدث خطأ أثناء إرسال الشكوى';
        }
    }
}
?>

<div class="container mt-5">
    <h2>إضافة شكوى جديدة</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">موضوع الشكوى</label>
            <input type="text" name="subject" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">وصف الشكوى</label>
            <textarea name="description" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">إرسال الشكوى</button>
        <a href="student_dashboard.php" class="btn btn-secondary">إلغاء</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>