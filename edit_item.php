<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input data to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $product = mysqli_real_escape_string($conn, $_POST['product']);
    $productId = mysqli_real_escape_string($conn, $_POST['productId']);
    $form = mysqli_real_escape_string($conn, $_POST['form']);
    $expiry = mysqli_real_escape_string($conn, $_POST['expiry']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    // If id is not provided, try to find the record by unique_id
    if (empty($id)) {
        $checkSql = "SELECT id FROM medicines WHERE unique_id = '$productId' LIMIT 1";
        $checkResult = mysqli_query($conn, $checkSql);
        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $row = mysqli_fetch_assoc($checkResult);
            $id = $row['id'];
        }
    }

    if (!empty($id)) {
        $sql = "UPDATE medicines SET name='$product', unique_id='$productId', form='$form', 
                expiry_date='$expiry', location='$location', quantity='$quantity', remarks='$remarks' 
                WHERE id='$id'";
    } else {
        $sql = "UPDATE medicines SET name='$product', form='$form', 
                expiry_date='$expiry', location='$location', quantity='$quantity', remarks='$remarks' 
                WHERE unique_id='$productId'";
    }

    if (mysqli_query($conn, $sql)) {
        echo "Item updated successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>