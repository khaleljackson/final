<?php
require_once '../config/database.php';
require_once '../includes/admin_auth.php';
require_once '../includes/admin_header.php';

if (!isset($_GET['id'])) {
    header("Location: complaints.php");
    exit();
}

$complaint_id = (int)$_GET['id'];

// جلب بيانات الشكوى مع بيانات المستخدم
$stmt = $mysqli->prepare("SELECT c.*, u.full_name, u.email FROM complaints c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();

if (!$complaint) {
    header("Location: complaints.php");
    exit();
}

$error = '';
$success = '';

// تحديث الحالة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $allowed_statuses = ['pending', 'in_progress', 'resolved', 'rejected'];
    
    if (in_array($status, $allowed_statuses)) {
        $stmt = $mysqli->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $complaint_id);
        
        if ($stmt->execute()) {
            $success = 'تم تحديث حالة الشكوى بنجاح';
            // تحديث البيانات
            $complaint['status'] = $status;
        } else {
            $error = 'حدث خطأ أثناء تحديث الحالة';
        }
    } else {
        $error = 'حالة غير صالحة';
    }
}

$status_text = '';
switch ($complaint['status']) {
    case 'pending': $status_text = 'قيد الانتظار'; break;
    case 'in_progress': $status_text = 'قيد المعالجة'; break;
    case 'resolved': $status_text = 'تم الحل'; break;
    case 'rejected': $status_text = 'مرفوض'; break;
}
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>تفاصيل الشكوى #<?= $complaint['id'] ?></h4>
            </div>
            <div class="card-body">
                <h5><?= htmlspecialchars($complaint['subject']) ?></h5>
                <p class="mt-3"><?= nl2br(htmlspecialchars($complaint['description'])) ?></p>
                <hr>
                <p><strong>الحالة الحالية:</strong> 
                    <span class="badge bg-<?= $complaint['status'] == 'pending' ? 'warning' : ($complaint['status'] == 'in_progress' ? 'info' : ($complaint['status'] == 'resolved' ? 'success' : 'danger')) ?>">
                        <?= $status_text ?>
                    </span>
                </p>
                <p><strong>تاريخ الإرسال:</strong> <?= date('Y-m-d H:i', strtotime($complaint['created_at'])) ?></p>
                <?php if ($complaint['updated_at'] != $complaint['created_at']): ?>
                <p><strong>آخر تحديث:</strong> <?= date('Y-m-d H:i', strtotime($complaint['updated_at'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>معلومات المستخدم</h4>
            </div>
            <div class="card-body">
                <p><strong>الاسم:</strong> <?= htmlspecialchars($complaint['full_name']) ?></p>
                <p><strong>البريد:</strong> <?= htmlspecialchars($complaint['email']) ?></p>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h4>تحديث الحالة</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            <option value="pending" <?= $complaint['status'] == 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                            <option value="in_progress" <?= $complaint['status'] == 'in_progress' ? 'selected' : '' ?>>قيد المعالجة</option>
                            <option value="resolved" <?= $complaint['status'] == 'resolved' ? 'selected' : '' ?>>تم الحل</option>
                            <option value="rejected" <?= $complaint['status'] == 'rejected' ? 'selected' : '' ?>>مرفوض</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">تحديث الحالة</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="complaints.php" class="btn btn-secondary">العودة للشكاوى</a>
</div>

<?php include '../includes/footer.php'; ?>