
<?php
include('db_connection.php');

if (isset($_GET['searchTerm'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['searchTerm']);

    $sql = "SELECT * FROM medicines
            WHERE name LIKE '%$searchTerm%'
            OR unique_id LIKE '%$searchTerm%'";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {

            // Check if the medicine quantity is below 10
            $quantityClass = ((float)$row['quantity'] < 10)
                ? 'quantity-cell quantity-low'
                : 'quantity-cell';

            echo "<tr data-id='" . htmlspecialchars($row['id']) . "' data-remarks='" . htmlspecialchars($row['remarks'] ?? '', ENT_QUOTES) . "'>";

            echo "<td><input type='checkbox' class='checkbox'></td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['unique_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['form']) . "</td>";
            echo "<td>" . htmlspecialchars($row['expiry_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";

            // Display quantity with the appropriate color
            echo "<td class='" . $quantityClass . "'>" . htmlspecialchars($row['quantity']) . "</td>";

            echo "<td><i class='fas fa-info-circle info-icon' data-remarks='" . htmlspecialchars($row['remarks'] ?? '', ENT_QUOTES) . "'></i></td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' style='text-align: center;'>No items found</td></tr>";
    }

    if ($result) {
        mysqli_free_result($result);
    }

    mysqli_close($conn);
}
?>
