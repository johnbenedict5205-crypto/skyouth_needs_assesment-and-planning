
<?php
include 'database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {

    // Get form data
    $full_name = trim($_POST['full_name'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $gender = $_POST['gender'] ?? '';
    $purok = trim($_POST['purok'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $plain_password = $_POST['password'] ?? '';
    $id_type = $_POST['id_type'] ?? '';

    // Allowed values
    $allowed_genders = ['Male', 'Female', 'Other'];

    $allowed_id_types = [
        'Barangay ID',
        'School ID',
        'Other Proof of Residency'
    ];

    // Validate required fields
    if (
        $full_name === '' ||
        $age < 1 ||
        $gender === '' ||
        $purok === '' ||
        $contact_number === '' ||
        $username === '' ||
        $plain_password === '' ||
        $id_type === ''
    ) {
        $message = 'Please complete all required fields.';
        $message_type = 'error';

    } elseif (
        !in_array($gender, $allowed_genders, true) ||
        !in_array($id_type, $allowed_id_types, true)
    ) {
        $message = 'Invalid gender or ID type.';
        $message_type = 'error';

    } elseif (
        !isset($_FILES['id_file']) ||
        $_FILES['id_file']['error'] !== UPLOAD_ERR_OK
    ) {
        $message = 'Please upload your picture or proof of residency.';
        $message_type = 'error';

    } elseif ($_FILES['id_file']['size'] > 5 * 1024 * 1024) {
        $message = 'File must not exceed 5MB.';
        $message_type = 'error';

    } else {

        // Check actual uploaded file type
        $tmp_file = $_FILES['id_file']['tmp_name'];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($tmp_file);

        $allowed_mime = [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'application/pdf' => 'pdf'
        ];

        if (!array_key_exists($mime_type, $allowed_mime)) {
            $message = 'Only JPG, PNG, and PDF files are allowed.';
            $message_type = 'error';

        } else {

            // Private upload directory outside htdocs
            $upload_dir = dirname(dirname(__DIR__))
                . DIRECTORY_SEPARATOR . 'sk_youth_private_ids';

            // Create upload directory if needed
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0750, true);
            }

            if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
                $message = 'Upload folder is not available.';
                $message_type = 'error';

            } else {

                // Generate a random filename
                $new_filename = bin2hex(random_bytes(16))
                    . '.' . $allowed_mime[$mime_type];

                $destination = $upload_dir
                    . DIRECTORY_SEPARATOR . $new_filename;

                // Move uploaded file
                if (move_uploaded_file($tmp_file, $destination)) {

                    // Hash password
                    $password = password_hash(
                        $plain_password,
                        PASSWORD_DEFAULT
                    );

                    // Default verification status
                    $verification_status = 'Pending';

                    // Insert youth record
                    $sql = "INSERT INTO youth (
                        full_name,
                        age,
                        gender,
                        purok,
                        contact_number,
                        username,
                        password,
                        id_type,
                        id_file,
                        verification_status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "sissssssss",
                        $full_name,
                        $age,
                        $gender,
                        $purok,
                        $contact_number,
                        $username,
                        $password,
                        $id_type,
                        $new_filename,
                        $verification_status
                    );

                    if (mysqli_stmt_execute($stmt)) {

                        $message = "Registration successful! Your registration is now pending SK officials verification.";
                        $message_type = 'success';

                    } else {

                        // Delete uploaded file if database insert fails
                        if (file_exists($destination)) {
                            unlink($destination);
                        }

                        // Check duplicate username
                        if (mysqli_errno($conn) == 1062) {
                            $message = 'Username already exists. Please choose another username.';
                        } else {
                            $message = 'Registration failed. Please try again.';
                        }

                        $message_type = 'error';
                    }

                    mysqli_stmt_close($stmt);

                } else {
                    $message = 'Unable to upload your file.';
                    $message_type = 'error';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Youth Registration | SK Barangay San Agustin</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 650px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .header {
            margin-bottom: 20px;
            padding: 18px;
            background: #800000;
            color: white;
            text-align: center;
            border-radius: 6px;
        }

        .header h2 {
            margin: 0 0 8px;
        }

        .header p {
            margin: 0;
            font-size: 14px;
        }

        label {
            display: block;
            margin-top: 14px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="file"] {
            background: #fafafa;
        }

        .note {
            margin-top: 6px;
            font-size: 13px;
            color: #666;
        }

        .status {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .success {
            background: #e8f5e9;
            color: #256029;
        }

        .error {
            background: #ffebee;
            color: #b71c1c;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            background: #800000;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #600000;
        }

        .back {
            display: block;
            margin-top: 18px;
            color: #800000;
            text-align: center;
            text-decoration: none;
        }

        .back:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 600px) {
            body {
                padding: 15px 10px;
            }

            .container {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <h2>Youth Registration</h2>
        <p>SK Barangay San Agustin</p>
    </div>

    <?php if ($message !== ''): ?>
        <div class="status <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label for="full_name">Full Name</label>
        <input
            type="text"
            id="full_name"
            name="full_name"
            required
        >

        <label for="age">Age</label>
        <input
            type="number"
            id="age"
            name="age"
            min="1"
            max="120"
            required
        >

        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="purok">Purok</label>
        <select id="purok" name="purok" required>
            <option value="">Select Purok</option>
            <option value="1">Purok 1</option>
            <option value="2">Purok 2</option>
            <option value="3">Purok 3</option>
            <option value="4">Purok 4</option>
            <option value="5">Purok 5</option>
            <option value="6">Purok 6</option>
            <option value="7">Purok 7</option>
        </select>

        <label for="contact_number">Contact Number</label>
        <input
            type="tel"
            id="contact_number"
            name="contact_number"
            required
        >

        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            required
        >

        <label for="id_type">Type of Identification</label>
        <select id="id_type" name="id_type" required>
            <option value="">Select ID / Proof</option>
            <option value="Barangay ID">Barangay ID</option>
            <option value="School ID">School ID</option>
            <option value="Other Proof of Residency">Other Proof of Residency</option>
        </select>

        <label for="id_file">Upload Picture / ID / Proof of Residency</label>
        <input
            type="file"
            id="id_file"
            name="id_file"
            accept=".jpg,.jpeg,.png,.pdf"
            required
        >

        <p class="note">
            Accepted files: JPG, PNG, PDF.<br>
            Maximum size: 5MB.<br>
            Your uploaded file will be reviewed by SK officials.
        </p>

        <button type="submit" name="save">Register</button>

    </form>

    <a class="back" href="index.php">Back to Dashboard</a>

</div>

</body>
</html>
