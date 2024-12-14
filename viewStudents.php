<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// البحث
$search = $_GET['search'] ?? '';

// الترقيم الصفحي
$limit = 5; // عدد الطلاب لكل صفحة
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM students WHERE name LIKE :search OR email LIKE :search LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll();

$total_students_sql = "SELECT COUNT(*) FROM students WHERE name LIKE :search OR email LIKE :search";
$total_stmt = $conn->prepare($total_students_sql);
$total_stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$total_stmt->execute();
$total_students = $total_stmt->fetchColumn();
$total_pages = ceil($total_students / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <!-- إضافة رابط Bootstrap من CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Students</h2>

        <!-- نموذج البحث -->
        <form method="GET" class="mb-4 d-flex justify-content-center">
            <input type="text" name="search" placeholder="Search students" value="<?php echo htmlspecialchars($search); ?>" class="form-control w-50">
            <button type="submit" class="btn btn-primary ms-2">Search</button>
        </form>

        <!-- جدول الطلاب -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Profile Picture</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['phone']); ?></td>
                            <td><?php echo htmlspecialchars($student['address']); ?></td>
                            <td>
                                <?php if ($student['profile_picture'] && file_exists($student['profile_picture'])): ?>
                                    <img src="<?php echo htmlspecialchars($student['profile_picture']); ?>" width="50" height="50" class="rounded-circle">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="updateStudent.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="deleteStudent.php?id=<?php echo htmlspecialchars($student['id']); ?>" onclick="return confirm('Are you sure you want to delete this student?');" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No students found matching your search.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- ترقيم الصفحات -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a href="?search=<?php echo htmlspecialchars($search); ?>&page=<?php echo $page - 1; ?>" class="page-link">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item">
                        <a href="?search=<?php echo htmlspecialchars($search); ?>&page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a href="?search=<?php echo htmlspecialchars($search); ?>&page=<?php echo $page + 1; ?>" class="page-link">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <!-- إضافة Bootstrap JS و Popper.js من CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>