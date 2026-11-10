
<?php
session_start();
include 'database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn,
        "SELECT official_id, full_name, password
         FROM sk_officials
         WHERE username = ?");

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($official = mysqli_fetch_assoc($result)) {

        if (password_verify($password, $official['password'])) {

            session_regenerate_id(true);

            $_SESSION['official_id'] = $official['official_id'];
            $_SESSION['official_name'] = $official['full_name'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header("Location: index.php");
            exit;

        } else {
            $message = "Incorrect username or password.";
        }

    } else {
        $message = "Incorrect username or password.";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SK Officials Login</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 30px 15px;
        }

        .container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }

        h2 {
            background: #800000;
            color: white;
            text-align: center;
            padding: 15px;
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
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>SK Officials Login</h2>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>

    </form>

</div>

</body>
</html>
