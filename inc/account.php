<?php 
    session_start();
    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 

    // Check if the user is already logged in
    if (!isset($_SESSION['user_id'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($email) || empty($password)) {
                $error = "Please enter both email and password.";
            } else {
                $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");

                if ($stmt === false)
                    die("MySQL prepare error: " . $conn->error);

                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->bind_result($user_id, $fname, $hashed_password, $role);
                $stmt->fetch();

                if ($user_id) {
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['fname'] = $fname;
                        $_SESSION['role'] = $role;
                        header("Location: account.php");
                        exit();
                    } else {
                        $error = "Incorrect password.";
                    }
                } else {
                    $error = "No account found with that email.";
                }
                $stmt->close();
            }
        }
    }
    $conn->close();
?>

<div class="container">
    <?php if (!isset($_SESSION['user_id'])): ?>
        <h1>Your Account</h1>
        <p>Please log in to view your account details and orders.</p>
        
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="account.php">
                <div class="form-row">
                    <label for="email">Email (Required):</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-row">
                    <label for="password">Password (Required):</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-row">
                    <button type="submit" name="login_submit">Log In</button>
                </div>
            </form>
        </div>
        <p>Don't have an account? <a href="register.php" class="login-link">Register here</a>.</p>
        
    <?php else: ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['fname']); ?>!</h1>
        <p>You are now logged in. Here you can view your account details and order history.</p>
        <!-- Replace the line below with your actual order and account details -->
        <p>[Your order details and account information will appear here]</p>
        <!-- Optional: Add a logout link -->
        <p><a href="logout.php" class="login-link">Log Out</a></p>
    <?php endif; ?>
</div>

<?php include("../inc/design/footer.php"); ?>
