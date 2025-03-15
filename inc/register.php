<?php include("../inc/design/head.php"); ?>
<?php include("../inc/design/header.php"); ?>
<?php include("../inc/design/nav.php"); ?>

<?php

// $host = "localhost";
// $user = "inf1005-sqldev";
// $pass = "r2Qr3YjS";
// $dbname = "daruma_db";

// $conn = new mysqli($host, $user, $pass, $dbname);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($lname)) {
        $errors[] = "Last name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $full_name = trim($fname . ' ' . $lname);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $full_name, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = true;
        } else {
            if ($stmt->errno == 1062) {
                $errors[] = "Error: Email already exists.";
            } else {
                $errors[] = "Error: " . $stmt->error;
            }
        }

        $stmt->close();
    }

}

$conn->close();
?>

<div class="container">
    <?php if ($success): ?>
        <h1 class="text-center">Registration Successful!</h1>
        <p class="text-center">Registration Successful. You can now <a href="account.php" class="login-link">log in</a>.</p>
    <?php else: ?>
        <h1 class="text-center">Create Your Account</h1>
        <p class="text-center">Register now and enjoy exclusive promotions.</p>

        <?php if (!empty($errors)): ?>
            <ul class="error-message">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="register.php">
                <div class="form-row">
                    <label for="fname">First Name:</label>
                    <input type="text" id="fname" name="fname">
                </div>

                <div class="form-row">
                    <label for="lname">Last Name (Required):</label>
                    <input type="text" id="lname" name="lname" required>
                </div>

                <div class="form-row">
                    <label for="email">Email (Required):</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-row">
                    <label for="password">Password (Required):</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-row">
                    <button type="submit">Register</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include("../inc/design/footer.php"); ?>
