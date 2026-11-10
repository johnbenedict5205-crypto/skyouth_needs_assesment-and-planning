
<?php
include 'database.php';

if (!isset($_GET['id'])) {
    die("Youth ID not found.");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM youth WHERE youth_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$youth = mysqli_fetch_assoc($result);

if (!$youth) {
    die("Youth record not found.");
}

$message = "";

if (isset($_POST['update'])) {

    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $purok = $_POST['purok'];
    $contact_number = $_POST['contact_number'];
    $username = $_POST['username'];

    $sql = "UPDATE youth SET
            full_name = ?,
            age = ?,
            gender = ?,
            purok = ?,
            contact_number = ?,
            username = ?
            WHERE youth_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sissssi",
        $full_name,
        $age,
        $gender,
        $purok,
        $contact_number,
        $username,
        $id
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit;
    } else {
        $message = "Error updating youth.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Youth</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f7f5;
    margin: 0;
}

header {
    background: #00b14f;
    color: white;
    text-align: center;
    padding: 20px;
}

.container {
    width: 400px;
    max-width: 90%;
    margin: 30px auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 10px #ddd;
}

input, select {
    width: 100%;
    padding: 10px;
    margin: 8px 0 15px;
    box-sizing: border-box;
}

button {
    background: #00b14f;
    color: white;
    border: none;
    padding: 12px;
    width: 100%;
    cursor: pointer;
    border-radius: 5px;
}

button:hover {
    background: #008f40;
}

.back {
    display: block;
    margin-top: 15px;
    text-align: center;
    color: #00b14f;
    text-decoration: none;
}
</style>
</head>

<body>

<header>
    <h1>SK YOUTH MANAGEMENT SYSTEM</h1>
    <p>Edit Youth Information</p>
</header>

<div class="container">

<h2>Update Youth</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">

<label>Full Name</label>
<input type="text" name="full_name"
value="<?php echo htmlspecialchars($youth['full_name']); ?>" required>

<label>Age</label>
<input type="number" name="age"
value="<?php echo htmlspecialchars($youth['age']); ?>" required>

<label>Gender</label>
<select name="gender" required>
<option value="Male" <?php if($youth['gender']=="Male") echo "selected"; ?>>Male</option>
<option value="Female" <?php if($youth['gender']=="Female") echo "selected"; ?>>Female</option>
</select>

<label>Purok</label>
<input type="text" name="purok"
value="<?php echo htmlspecialchars($youth['purok']); ?>" required>

<label>Contact Number</label>
<input type="text" name="contact_number"
value="<?php echo htmlspecialchars($youth['contact_number']); ?>">

<label>Username</label>
<input type="text" name="username"
value="<?php echo htmlspecialchars($youth['username']); ?>" required>

<button type="submit" name="update">Update Youth</button>

</form>

<a href="index.php" class="back">← Back to Youth List</a>

</div>

</body>
</html>
