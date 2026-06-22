<?php
require_once 'config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $conn-> real_escape_string(trim($_POST['username']));
    $email = $conn-> real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Empty field validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($confirm_password)) {
        $errors[] = "Confirm Password is required.";
    }

    // Username length
    if (!empty($username) && strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }

    // Email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Password length
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // Password match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }



    if (empty($errors)) {

        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE username = ? OR email = ?"
        );

        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Username or email already exists.";
        }

        $stmt->close();
    }


    
    if (empty($errors)) {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (username, email, password_hash)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "sss",
            $username,
            $email,
            $password_hash
        );

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Registration successful!</p>";
        } else {
            echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>


    <div class="register-container">


<h2>User Registration</h2>

<?php if(!empty($errors)): ?>
    <div class="error-box">
        <ul>
            <?php foreach($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if(!empty($success)): ?>
    <div class="success-box">
        <?= $success ?>
    </div>
<?php endif; ?>

<form method="POST" class="register-form">

    <label>Username</label>
    <input type="text" name="username">

    <label>Email</label>
    <input type="email" name="email">

    <label>Password</label>
    <input type="password" name="password">

    <label>Confirm Password</label>
    <input type="password" name="confirm_password">

    <button type="submit" class="register-btn">
        Register
    </button>

</form>


</div>



</body>
</html>