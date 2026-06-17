<?php
session_start();
include("db.php");

// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$message = "";
$success = "";

// check id
if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = $_GET['id'];

// fetch journal (only owner)
$stmt = $conn->prepare("
    SELECT * FROM journals 
    WHERE id=? AND user_id=? AND is_deleted=0
");

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Journal not found!");
}

$row = $result->fetch_assoc();

// update journal
if (isset($_POST['update'])) {

    $title = trim($_POST['title']);
    $mood = $_POST['mood'];
    $content = trim($_POST['content']);
    $date = $_POST['journal_date'];

    if (empty($title) || empty($content)) {
        $message = "⚠ Title and Content required!";
    } else {

        $stmt = $conn->prepare("
            UPDATE journals 
            SET title=?, mood=?, content=?, journal_date=?, updated_at=NOW()
            WHERE id=? AND user_id=?
        ");

        $stmt->bind_param(
            "ssssii",
            $title,
            $mood,
            $content,
            $date,
            $id,
            $user_id
        );

        if ($stmt->execute()) {
            $success = "Journal updated successfully ✅";
        } else {
            $message = "Update failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Journal</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#0b1220;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.card{
    width:650px;
    background:#111827;
    padding:25px;
    border-radius:15px;
    border:1px solid #1f2937;
}

h2{
    text-align:center;
}

input, select, textarea{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:none;
    border-radius:10px;
    background:#1f2937;
    color:white;
    outline:none;
}

textarea{
    height:180px;
    resize:none;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#f59e0b;
    color:white;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#d97706;
}

.message{
    background:#ef4444;
    padding:10px;
    border-radius:8px;
    text-align:center;
    margin-bottom:10px;
}

.success{
    background:#16a34a;
    padding:10px;
    border-radius:8px;
    text-align:center;
    margin-bottom:10px;
}

.back{
    text-align:center;
    margin-top:10px;
}

.back a{
    color:#60a5fa;
    text-decoration:none;
}
</style>

</head>

<body>

<div class="card">

    <h2>✏ Edit Journal</h2>

    <?php if ($message != "") { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <?php if ($success != "") { ?>
        <div class="success"><?php echo $success; ?></div>
    <?php } ?>

    <form method="POST">

        <input type="text" name="title"
               value="<?php echo htmlspecialchars($row['title']); ?>"
               required>

        <select name="mood">
            <option value="happy" <?php if($row['mood']=="happy") echo "selected"; ?>>😊 Happy</option>
            <option value="sad" <?php if($row['mood']=="sad") echo "selected"; ?>>😔 Sad</option>
            <option value="neutral" <?php if($row['mood']=="neutral") echo "selected"; ?>>😐 Neutral</option>
            <option value="excited" <?php if($row['mood']=="excited") echo "selected"; ?>>🔥 Excited</option>
            <option value="stressed" <?php if($row['mood']=="stressed") echo "selected"; ?>>😫 Stressed</option>
        </select>

        <input type="date" name="journal_date"
               value="<?php echo $row['journal_date']; ?>"
               required>

        <textarea name="content" required><?php echo htmlspecialchars($row['content']); ?></textarea>

        <button type="submit" name="update">Update Journal</button>

    </form>

    <div class="back">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>

</div>

</body>
</html>