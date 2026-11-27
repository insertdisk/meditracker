<?php
include('db_connection.php');

// Get the product ID to delete
$productId = mysqli_real_escape_string($conn, $_POST['productId']);

// SQL query to delete the item
$sql = "DELETE FROM medicines WHERE unique_id = '$productId'";

if ($conn->query($sql) === TRUE) {
    echo "Item deleted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>