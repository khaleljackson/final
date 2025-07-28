<?php
require_once '../config/database.php';
require_once '../includes/admin_auth.php';
require_once '../includes/admin_header.php';

// إحصائيات سريعة
$total_complaints = $mysqli->query("SELECT COUNT(*) as count FROM complaints")->fetch_assoc()['count'];
$pending_complaints = $mysqli->query("SELECT COUNT(*) as count FROM complaints WHERE status = 'pending'")->fetch_assoc()['count'];
$resolved_complaints = $mysqli->query("SELECT COUNT(*) as count FROM complaints WHERE status = 'resolved'")->fetch_assoc()['count'];
$total_users = $mysqli->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];

// أحدث الشكاوى
$result = $mysqli->query("SELECT c.*, u.full_name FROM complaints c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC LIMIT 5");
$recent_complaints = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>إجمالي الشكاوى</h5>
                <h2><?= $total_complaints ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>قيد الانتظار</h5>
                <h2><?= $pending_complaints ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>تم الحل</h5>
                <h2><?= $resolved_complaints ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>عدد المستخدمين</h5>
                <h2><?= $total_users ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>أحدث الشكاوى</h4>
            </div>
            <div class="card-body">
                <a href="complaints.php" class="btn btn-primary mb-3">عرض جميع الشكاوى</a>
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
                        <?php foreach ($recent_complaints as $complaint): ?>
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
</div>

<?php include '../includes/footer.php'; ?>