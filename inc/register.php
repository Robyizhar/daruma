<?php
// For debugging any hidden PHP errors:
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../inc/design/head.php");
include("../inc/design/header.php");
include("../inc/design/nav.php");
include("./sql/db.php");

session_start();
$Model = new Model();

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic checks
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

    // Server-Side Password Validation:
    if (!empty($password)) {
        // At least 8 chars, 1 letter, 1 digit
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
            $errors[] = "Password must be at least 8 characters, including both letters and digits.";
        }
    }

    // If no errors so far, attempt registration
    if (empty($errors)) {
        $result = $Model->registerUser($fname, $lname, $email, $password);
        $success = $result['success'];
        $errors = $result['errors'];
    }
}
?>

<div class="container">
    <?php if ($success): ?>
        <h1 class="text-center">Registration Successful!</h1>
        <p class="text-center">
            You can now <a href="account.php" class="login-link">log in</a>.
        </p>
    <?php else: ?>
        <h1 class="text-center">Create Your Account</h1>
        <p class="text-center">Register now!</p>

        <?php if (!empty($errors)): ?>
            <ul class="error-message">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="register.php" id="registrationForm">
                <div class="form-row">
                    <label for="fname">First Name:</label>
                    <input type="text" id="fname" name="fname" 
                           value="<?= isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : '' ?>">
                </div>

                <div class="form-row">
                    <label for="lname">Last Name (Required):</label>
                    <input type="text" id="lname" name="lname" required 
                           value="<?= isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : '' ?>">
                </div>

                <div class="form-row">
                    <label for="email">Email (Required):</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
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

<!-- Make sure you have SweetAlert2 included somewhere. 
     E.g., from CDN (below) or your own local copy. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const registrationForm = document.getElementById("registrationForm");
    registrationForm.addEventListener("submit", function(e) {
        const password = document.getElementById("password").value.trim();

        // Regex: at least one letter, one digit, total length >= 8
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

        if (!passwordRegex.test(password)) {
            // Show a sweet alert message and prevent form submission
            e.preventDefault();
            Swal.fire({
                icon: "warning",
                title: "Password Error",
                text: "Password must be at least 8 characters, including letters and digits."
            });
        }
    });
});
</script>

<?php include("../inc/design/footer.php"); ?>
