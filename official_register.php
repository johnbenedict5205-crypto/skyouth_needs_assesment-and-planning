
<?php
session_start();
include 'database.php';

$message = "";

$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM sk_officials");
$count_row = mysqli_fetch_assoc($count_result);
$official_exists = $count_row['total'] > 0;

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$official_exists) {

    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($full_name === '' || $username === '' || strlen($password) < 8) {
        $message = "Complete all fields. Password must be at least 8 characters.";

    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn,
            "INSERT INTO sk_officials (full_name, username, password)
             VALUES (?, ?, ?)");

        mysqli_stmt_bind_param(
            $stmt, "sss",
            $full_name, $username, $hashed_password
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "SK Official account created successfully!";
            $official_exists = true;
        } else {
            $message = "Registration failed. Username may already exist.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SK Official Setup</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 30px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
        }
        h2 {
            background: #800000;
            color: white;
            padding: 15px;
            text-align: center;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            box-sizing: border-box;
        }
        button {
            background: #800000;
            color: white;
            border: none;
            cursor: pointer;
        }
        .message {
            background: #eee;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>SK Official Setup</h2>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <?php if (!$official_exists): ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" minlength="8" required>

        <button type="submit">Create SK Official Account</button>
    </form>

    <?php else: ?>

    <p>An SK Official account already exists.</p>
    <a href="login.php">Go to Login</a>

    <?php endif; ?>
</div>

</body>
</html>
