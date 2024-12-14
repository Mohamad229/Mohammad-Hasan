<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- إضافة رابط Bootstrap من CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">User Registration</h2>
        <form method="POST" action="register.php" class="col-md-6 mx-auto">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
        </form>

        <!-- عرض الرسائل في حالة وجود خطأ -->
        <?php
        if (isset($_POST['register'])) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

            try {
                // التحقق من وجود البريد الإلكتروني مسبقًا
                $check_email_sql = "SELECT COUNT(*) FROM users WHERE email = ?";
                $stmt = $conn->prepare($check_email_sql);
                $stmt->execute([$email]);
                $email_exists = $stmt->fetchColumn();

                if ($email_exists > 0) {
                    echo "<div class='alert alert-danger mt-3' role='alert'>
                            Error: Email already exists. Please use a different email.
                            </div>";
                } else {
                    // إدخال المستخدم الجديد إذا لم يكن البريد الإلكتروني موجودًا
                    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$username, $email, $password]);

                    // التوجيه إلى صفحة تسجيل الدخول
                    header("Location: login.php");
                    exit;
                }
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger mt-3' role='alert'>
                        Error: " . $e->getMessage() . "
                        </div>";
            }
        }
        ?>
    </div>

    <!-- إضافة Bootstrap JS و Popper.js من CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>