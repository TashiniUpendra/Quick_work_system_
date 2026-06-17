<?php
session_start();
include("db.php");

// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// check id
if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = $_GET['id'];

// verify journal belongs to user
$stmt = $conn->prepare("
    SELECT id FROM journals 
    WHERE id=? AND user_id=? AND is_deleted=0
");

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Journal not found or already deleted!");
}

// soft delete (recommended for SaaS)
$stmt = $conn->prepare("
    UPDATE journals 
    SET is_deleted=1, updated_at=NOW()
    WHERE id=? AND user_id=?
");

$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    header("Location: dashboard.php?msg=deleted");
    exit();
} else {
    echo "Delete failed!";
}
?>