<?php
include('db_connection.php');  // Include the database connection

// Handle sorting
$orderBy = "name"; // Default sort column
$orderDirection = "ASC"; // Default sort direction

if (isset($_GET['sort']) && isset($_GET['order'])) {
    $allowedColumns = ['name', 'unique_id', 'form', 'expiry_date', 'location', 'quantity'];
    $column = $_GET['sort'];
    $order = strtoupper($_GET['order']);
    
    if (in_array($column, $allowedColumns) && ($order === 'ASC' || $order === 'DESC')) {
        $orderBy = $column;
        $orderDirection = $order;
    }
}

// SQL query to fetch all items from the medicines table with sorting
$sql = "SELECT * FROM medicines ORDER BY $orderBy $orderDirection";
$result = $conn->query($sql);

// Check if any rows are returned
if ($result->num_rows > 0) {
    // Output data for each row
    while($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "' data-remarks='" . htmlspecialchars($row['remarks'], ENT_QUOTES) . "'>";
        echo "<td><input type='checkbox' class='checkbox'></td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['unique_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['form']) . "</td>";
        echo "<td>" . htmlspecialchars($row['expiry_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
        echo "<td class='quantity-cell'>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "<td><i class='fas fa-info-circle info-icon' data-remarks='" . htmlspecialchars($row['remarks'], ENT_QUOTES) . "'></i></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' style='text-align: center;'>No items found</td></tr>";
}

$conn->close();  // Close the database connection
?>
