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

if (!$complaint || $complaint['status'] != 'pending') {
    header("Location: student_dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = trim($_POST['subject']);
    $description = trim($_POST['description']);
    
    if (empty($subject) || empty($description)) {
        $error = 'جميع الحقول مطلوبة';
    } else {
        $stmt = $mysqli->prepare("UPDATE complaints SET subject = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $subject, $description, $complaint_id, $user['id']);
        
        if ($stmt->execute()) {
            $success = 'تم تحديث الشكوى بنجاح';
            header("refresh:2;url=complaint_details.php?id=$complaint_id");
        } else {
            $error = 'حدث خطأ أثناء تحديث الشكوى';
        }
    }
}
?>

<div class="container mt-5">
    <h2>تعديل شكوى</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">موضوع الشكوى</label>
            <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($complaint['subject']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">وصف الشكوى</label>
            <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($complaint['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">تحديث الشكوى</button>
        <a href="complaint_details.php?id=<?= $complaint['id'] ?>" class="btn btn-secondary">إلغاء</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>