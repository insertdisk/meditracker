<?php
include('db_connection.php');  // Include the database connection

// Get data from POST request
$product = $_POST['product'];
$productId = $_POST['productId'];
$form = $_POST['form'];
$expiry = $_POST['expiry'];
$location = $_POST['location'];
$quantity = $_POST['quantity'];
$remarks = $_POST['remarks'];

// First, check if an item with the same product ID already exists
$check_sql = "SELECT * FROM medicines WHERE unique_id = '$productId'";
$check_result = $conn->query($check_sql);

if ($check_result && $check_result->num_rows > 0) {
    // Item already exists, update information and add quantities
    $existing_item = $check_result->fetch_assoc();
    $new_quantity = $existing_item['quantity'] + $quantity;
    
    // Update the existing item with new values and add the quantities
    $update_sql = "UPDATE medicines SET 
                  name = '$product', 
                  form = '$form', 
                  expiry_date = '$expiry', 
                  location = '$location', 
                  quantity = '$new_quantity', 
                  remarks = '$remarks' 
                  WHERE unique_id = '$productId'";
    
    if ($conn->query($update_sql) === TRUE) {
        // If update is successful, return the updated row
        $updated_row = "<tr data-id='" . $existing_item['id'] . "'>
                        <td><input type='checkbox' class='checkbox'></td>
                        <td>{$product}</td>
                        <td>{$productId}</td>
                        <td>{$form}</td>
                        <td>" . date('m/d/Y', strtotime($expiry)) . "</td>
                        <td>{$location}</td>
                        <td class='quantity-cell'>{$new_quantity}</td>
                        <td><i class='fas fa-info-circle info-icon' data-remarks='" . htmlspecialchars($remarks, ENT_QUOTES) . "'></i></td>
                    </tr>";
        echo $updated_row;
        // Display "Item added successfully" message
        echo "<script>alert('Item added successfully!');</script>";
    } else {
        echo "Error updating item: " . $conn->error;
    }
} else {
    // Item doesn't exist, insert a new one
    $insert_sql = "INSERT INTO medicines (name, unique_id, form, expiry_date, location, quantity, remarks) 
            VALUES ('$product', '$productId', '$form', '$expiry', '$location', '$quantity', '$remarks')";

    if ($conn->query($insert_sql) === TRUE) {
        // If insertion is successful, fetch the last inserted item
        $last_id = $conn->insert_id;
        $new_row = "<tr data-id='" . $last_id . "'>
                        <td><input type='checkbox' class='checkbox'></td>
                        <td>{$product}</td>
                        <td>{$productId}</td>
                        <td>{$form}</td>
                        <td>" . date('m/d/Y', strtotime($expiry)) . "</td>
                        <td>{$location}</td>
                        <td class='quantity-cell'>{$quantity}</td>
                        <td><i class='fas fa-info-circle info-icon' data-remarks='" . htmlspecialchars($remarks, ENT_QUOTES) . "'></i></td>
                    </tr>";
        echo $new_row;  // Return the new row as HTML
        echo "<script>alert('Item added successfully!');</script>";
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
