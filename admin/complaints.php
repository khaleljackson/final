<?php
require_once '../config/database.php';
require_once '../includes/admin_auth.php';
require_once '../includes/admin_header.php';

// معالجة الفلاتر
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT c.*, u.full_name FROM complaints c JOIN users u ON c.user_id = u.id";
$params = [];
$types = "";

if ($status_filter) {
    $sql .= " WHERE c.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($search) {
    $sql .= $status_filter ? " AND " : " WHERE ";
    $sql .= "(c.subject LIKE ? OR u.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

$sql .= " ORDER BY c.created_at DESC";

if (!empty($params)) {
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $mysqli->query($sql);
}
$complaints = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>إدارة الشكاوى</h2>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                    <option value="in_progress" <?= $status_filter == 'in_progress' ? 'selected' : '' ?>>قيد المعالجة</option>
                    <option value="resolved" <?= $status_filter == 'resolved' ? 'selected' : '' ?>>تم الحل</option>
                    <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>مرفوض</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">بحث</label>
                <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث بالموضوع أو اسم المستخدم">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">بحث</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
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
                        <td><?= htmlspecialchars($complaint['full_name']) ?></td>
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
                            <a href="manage_complaint.php?id=<?= $complaint['id'] ?>" class="btn btn-sm btn-primary">إدارة</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>