<?php
require_once(__DIR__ . '/../config/Database.php');

class User {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // 🔐 REGISTER
    public function register($name, $email, $phone, $password, $role) {

        $check = $this->conn->prepare("SELECT user_id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if($result->num_rows > 0){
            return "Email already exists";
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO users(name,email,phone,password,role)
            VALUES(?,?,?,?,?)
        ");

        $stmt->bind_param("sssss", $name, $email, $phone, $hashed, $role);

        return $stmt->execute();
    }

    // 🔐 LOGIN (status check included)
    public function login($email, $password) {

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $res = $stmt->get_result();

        if($res->num_rows == 1){
            $user = $res->fetch_assoc();

            if($user['status'] != "ACTIVE"){
                return "Account not active";
            }

            if(password_verify($password, $user['password'])){
                return $user;
            } else {
                return "Wrong password";
            }
        }

        return "User not found";
    }

}
?>
