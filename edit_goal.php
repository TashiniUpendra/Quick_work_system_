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

// validate id
if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = $_GET['id'];

/* =========================
   FETCH GOAL (OWNER CHECK)
========================= */
$stmt = $conn->prepare("
    SELECT * FROM future_goals 
    WHERE id=? AND user_id=?
");

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Goal not found!");
}

$goal = $result->fetch_assoc();

/* =========================
   UPDATE GOAL
========================= */
if (isset($_POST['update_goal'])) {

    $title = trim($_POST['goal_title']);
    $description = trim($_POST['description']);
    $target_date = $_POST['target_date'];
    $progress = $_POST['progress'];
    $status = $_POST['status'];

    if (empty($title)) {
        $message = "Goal title required!";
    } else {

        $stmt = $conn->prepare("
            UPDATE future_goals 
            SET goal_title=?, description=?, target_date=?, progress=?, status=?
            WHERE id=? AND user_id=?
        ");

        $stmt->bind_param(
            "sssissi",
            $title,
            $description,
            $target_date,
            $progress,
            $status,
            $id,
            $user_id
        );

        if ($stmt->execute()) {
            $success = "Goal updated successfully ✅";
        } else {
            $message = "Update failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Goal</title>

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
    width:600px;
    background:#111827;
    padding:25px;
    border-radius:15px;
    border:1px solid #1f2937;
}

h2{
    text-align:center;
}

input, textarea, select{
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
    height:100px;
    resize:none;
}

/* progress style */
input[type="range"]{
    width:100%;
}

/* button */
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

    <h2>✏ Edit Goal</h2>

    <?php if($message!=""){ ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <?php if($success!=""){ ?>
        <div class="success"><?php echo $success; ?></div>
    <?php } ?>

    <form method="POST">

        <input type="text" name="goal_title"
               value="<?php echo htmlspecialchars($goal['goal_title']); ?>"
               required>

        <textarea name="description"><?php echo htmlspecialchars($goal['description']); ?></textarea>

        <input type="date" name="target_date"
               value="<?php echo $goal['target_date']; ?>">

        <!-- PROGRESS -->
        <label>Progress: <span id="pval"><?php echo $goal['progress']; ?></span>%</label>
        <input type="range" name="progress" min="0" max="100"
               value="<?php echo $goal['progress']; ?>"
               oninput="pval.innerText=this.value">

        <!-- STATUS -->
        <select name="status">
            <option value="Pending" <?php if($goal['status']=="Pending") echo "selected"; ?>>Pending</option>
            <option value="In Progress" <?php if($goal['status']=="In Progress") echo "selected"; ?>>In Progress</option>
            <option value="Completed" <?php if($goal['status']=="Completed") echo "selected"; ?>>Completed</option>
        </select>

        <button type="submit" name="update_goal">Update Goal</button>

    </form>

    <div class="back">
        <a href="goals.php">← Back to Goals</a>
    </div>

</div>

</body>
</html>