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

// get current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// update profile
if (isset($_POST['update'])) {

    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $phone      = $_POST['phone'];
    $address    = $_POST['address'];
    $about_me   = $_POST['about_me'];
    $gender     = $_POST['gender'];
    $birth_date = $_POST['birth_date'];

    // check email already used by another user
    $check = $conn->prepare("SELECT id FROM users WHERE email=? AND id != ?");
    $check->bind_param("si", $email, $user_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $message = "Email already in use!";
    } else {

        $stmt = $conn->prepare("
            UPDATE users 
            SET username=?, email=?, first_name=?, last_name=?, phone=?, address=?, about_me=?, gender=?, birth_date=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "sssssssssi",
            $username,
            $email,
            $first_name,
            $last_name,
            $phone,
            $address,
            $about_me,
            $gender,
            $birth_date,
            $user_id
        );

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
        } else {
            $message = "Update failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>

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
}

h2{
    text-align:center;
    margin-bottom:10px;
}

input, select, textarea{
    width:100%;
    padding:12px;
    margin:8px 0;
    border:none;
    border-radius:10px;
    background:#1f2937;
    color:white;
    outline:none;
}

textarea{
    height:80px;
    resize:none;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#3b82f6;
    color:white;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#2563eb;
}

.message{
    background:#ef4444;
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;
    text-align:center;
}

.success{
    background:#16a34a;
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;
    text-align:center;
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

    <h2>✏ Edit Profile</h2>

    <?php if($message!=""){ ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <?php if($success!=""){ ?>
        <div class="success"><?php echo $success; ?></div>
    <?php } ?>

    <form method="POST">

        <input type="text" name="username"
               value="<?php echo $user['username']; ?>"
               required>

        <input type="email" name="email"
               value="<?php echo $user['email']; ?>"
               required>

        <input type="text" name="first_name"
               value="<?php echo $user['first_name']; ?>">

        <input type="text" name="last_name"
               value="<?php echo $user['last_name']; ?>">

        <input type="text" name="phone"
               value="<?php echo $user['phone']; ?>">

        <input type="text" name="address"
               value="<?php echo $user['address']; ?>">

        <textarea name="about_me"><?php echo $user['about_me']; ?></textarea>

        <select name="gender">
            <option value="">Select Gender</option>
            <option value="Male" <?php if($user['gender']=="Male") echo "selected"; ?>>Male</option>
            <option value="Female" <?php if($user['gender']=="Female") echo "selected"; ?>>Female</option>
            <option value="Other" <?php if($user['gender']=="Other") echo "selected"; ?>>Other</option>
        </select>

        <input type="date" name="birth_date"
               value="<?php echo $user['birth_date']; ?>">

        <button type="submit" name="update">Save Changes</button>

    </form>

    <div class="back">
        <a href="profile.php">← Back to Profile</a>
    </div>

</div>

</body>
</html>