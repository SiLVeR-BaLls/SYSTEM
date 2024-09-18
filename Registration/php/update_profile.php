<?php
// Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'dlms';

// Create connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle file upload
$profile_photo = '';
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
    if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
        $profile_photo = $target_file;
    }
}

// Prepare the update statement
$query = "UPDATE users_info 
          SET Fname = ?, Sname = ?, Mname = ?, profile_photo = ?
          WHERE id = ?";

// Prepare and execute the update query
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssi", $_POST['first_name'], $_POST['last_name'], $_POST['middle_name'], $profile_photo, $user_id);

if ($stmt->execute()) {
    // Redirect or show success message
    header("Location: profile.php");
} else {
    echo "Error updating record: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
