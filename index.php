
<?php
require 'auth.php';
include 'database.php';

$sql = "SELECT * FROM youth ORDER BY youth_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SK Youth Dashboard</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
        }

        header {
            background: #800000;
            color: white;
            padding: 20px;
            text-align: center;
        }

        header h2 {
            margin: 0;
        }

        .container {
            padding: 25px;
        }

        .top-buttons {
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 9px 14px;
            text-decoration: none;
            border-radius: 4px;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }

        .maroon {
            background: #800000;
        }

        .green {
            background: #198754;
        }

        .red {
            background: #dc3545;
        }

        .blue {
            background: #0d6efd;
        }

        .table-container {
            background: white;
            padding: 15px;
            border-radius: 7px;
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
        }

        th {
            background: #800000;
            color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .status {
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .Pending {
            background: #fff3cd;
            color: #856404;
        }

        .Approved {
            background: #d4edda;
            color: #155724;
        }

        .Rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .actions form {
            display: inline-block;
            margin: 2px;
        }

        .actions button {
            border: none;
        }

        .no-file {
            color: #888;
        }

        .no-records {
            padding: 20px;
            color: #777;
        }

        .btn:hover {
            opacity: 0.85;
        }

        @media screen and (max-width: 768px) {
            .container {
                padding: 10px;
            }

            header {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<header>
    <h2>SK Youth Management System</h2>
    <p>Barangay San Agustin</p>
</header>

<div class="container">

    <div class="top-buttons">
        <a href="create.php" class="btn maroon">+ Register Youth</a>
        <a href="logout.php" class="btn red">Logout</a>
    </div>

    <div class="table-container">

        <h3>Registered Youth and Verification</h3>

        <table>
            <thead>
                <tr>
                    <th>Youth ID</th>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Purok</th>
                    <th>Contact Number</th>
                    <th>Username</th>
                    <th>ID Type</th>
                    <th>Submitted Picture / ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result && mysqli_num_rows($result) > 0): ?>

                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <tr>

                    <td>
                        <?php echo (int)$row['youth_id']; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['full_name']); ?>
                    </td>

                    <td>
                        <?php echo (int)$row['age']; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['gender']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['purok']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['contact_number']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['username']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['id_type'] ?? 'N/A'); ?>
                    </td>

                    <td>
                        <?php if (!empty($row['id_file'])): ?>

                            <a
                                href="view_id.php?id=<?php echo (int)$row['youth_id']; ?>"
                                target="_blank"
                                class="btn blue"
                            >
                                View Picture / ID
                            </a>

                        <?php else: ?>

                            <span class="no-file">
                                No uploaded file
                            </span>

                        <?php endif; ?>
                    </td>

                    <td>
                        <span class="status <?php echo htmlspecialchars($row['verification_status']); ?>">
                            <?php echo htmlspecialchars($row['verification_status']); ?>
                        </span>
                    </td>

                    <td class="actions">

                        <?php if ($row['verification_status'] === 'Pending'): ?>

                            <!-- APPROVE BUTTON -->
                            <form action="verify.php" method="POST">

                                <input type="hidden"
                                       name="csrf_token"
                                       value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                                <input type="hidden"
                                       name="youth_id"
                                       value="<?php echo (int)$row['youth_id']; ?>">

                                <input type="hidden"
                                       name="status"
                                       value="Approved">

                                <button type="submit"
                                        class="btn green"
                                        onclick="return confirm('Approve this youth account?');">
                                    Approve
                                </button>

                            </form>

                            <!-- REJECT BUTTON -->
                            <form action="verify.php" method="POST">

                                <input type="hidden"
                                       name="csrf_token"
                                       value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                                <input type="hidden"
                                       name="youth_id"
                                       value="<?php echo (int)$row['youth_id']; ?>">

                                <input type="hidden"
                                       name="status"
                                       value="Rejected">

                                <button type="submit"
                                        class="btn red"
                                        onclick="return confirm('Reject this youth account?');">
                                    Reject
                                </button>

                            </form>

                        <?php else: ?>

                            <span style="color:#777;font-size:12px;">
                                No action available
                            </span>

                        <?php endif; ?>

                    </td>

                </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="11" class="no-records">
                        No youth records found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>
