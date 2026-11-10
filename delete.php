
<?php
include 'database.php';

if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $sql = "DELETE FROM youth WHERE youth_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error deleting youth.";
    }

} else {
    echo "Youth ID not found.";
}
?>
