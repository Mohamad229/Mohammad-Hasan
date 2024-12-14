<?php
require 'db.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = [];

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $profile_picture = null;

    // معالجة رفع الصورة
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = 'photo_' . time() . '_' . basename($_FILES['profile_picture']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = $target_file;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to upload profile picture: ' . $_FILES['profile_picture']['error'];
            echo json_encode($response);
            exit;
        }
    }

    // إدخال البيانات
    $sql = "INSERT INTO students (name, email, phone, address, profile_picture) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$name, $email, $phone, $address, $profile_picture])) {
        $response['status'] = 'success';
        $response['message'] = 'Student added successfully!';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Database error: ' . $conn->errorInfo()[2];
    }

    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <!-- إضافة رابط Bootstrap من CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Add Student</h2>

        <form id="studentForm" enctype="multipart/form-data" method="POST" class="col-md-6 mx-auto border p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone:</label>
                <input type="text" id="phone" name="phone" class="form-control">
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address:</label>
                <textarea id="address" name="address" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100">Add Student</button>
        </form>

        <div id="response" class="mt-3 text-center"></div>
    </div>

    <!-- إضافة Bootstrap JS و Popper.js من CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#studentForm').on('submit', function(e) {
                e.preventDefault(); // منع إعادة تحميل الصفحة

                var formData = new FormData(this);

                $.ajax({
                    url: 'addStudent.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        var result = JSON.parse(response);
                        $('#response').text(result.message);

                        if (result.status === 'success') {
                            $('#studentForm')[0].reset(); // إعادة تعيين النموذج
                            $('#response').removeClass('text-danger').addClass('text-success');
                        } else {
                            $('#response').removeClass('text-success').addClass('text-danger');
                        }
                    },
                    error: function() {
                        $('#response').text('An error occurred.').removeClass('text-success').addClass('text-danger');
                    }
                });
            });
        });
    </script>
</body>

</html>