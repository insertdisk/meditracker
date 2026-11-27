<?php
// Include your database connection file
include('db_connection.php');

// Handle sorting
$orderBy = "name"; // Default sort
$orderDirection = "ASC"; // Default direction

if (isset($_GET['sort']) && isset($_GET['dir'])) {
    $allowedColumns = ['name', 'unique_id', 'form', 'expiry_date', 'location', 'quantity'];
    $column = $_GET['sort'];
    $direction = $_GET['dir'];
    
    if (in_array($column, $allowedColumns) && ($direction === 'ASC' || $direction === 'DESC')) {
        $orderBy = $column;
        $orderDirection = $direction;
    }
}

// Fetch items from database
$sql = "SELECT * FROM medicines ORDER BY $orderBy $orderDirection";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediStock | USC Clinic Inventory System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #0099cc;
            --secondary-color: #007bff;
            --light-blue: #e6f7ff;
            --border-color: #ddd;
            --header-bg: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        header {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo img {
            width: 40px;
            height: 40px;
        }
        
        .logo h1 {
            font-size: 18px;
            color: #333;
            font-weight: 600;
        }
        
        .search-bar {
            margin-left: auto;
            margin-right: 20px;
            position: relative;
            width: 300px;
        }
        
        .search-bar input {
            width: 100%;
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            outline: none;
            padding-left: 35px;
        }
        
        .search-bar i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 20px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .btn-sort {
            background-color: var(--primary-color);
        }
        
        .btn-add {
            background-color: #00b8d4;
        }
        
        .btn-edit {
            background-color: #00b8d4;
        }
        
        .btn-export {
            background-color: #00b8d4;
        }
        
        /* Change delete button color to red */
        .btn-delete {
            background-color: #e74c3c !important;
        }
        
        /* Exit button style */
        .btn-exit {
            background-color: #34495e;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .inventory-table th {
            background-color: #f0f0f0;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 1px solid var(--border-color);
            position: relative;
            cursor: pointer;
        }
        
        .inventory-table th i {
            margin-left: 5px;
            font-size: 12px;
        }
        
        .inventory-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .inventory-table tr:nth-child(even) {
            background-color: var(--light-blue);
        }
        
        .inventory-table tr.selected {
            background-color: #d4edff;
        }
        
        .checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .quantity-cell {
            font-weight: bold;
            font-size: 18px;
            color: var(--primary-color);
            text-align: center;
        }

        .quantity-low {
         color: #e74c3c !important;
        }

        
        .info-icon {
            color: var(--primary-color);
            cursor: pointer;
            font-size: 18px;
        }
        
        .responsive-table {
            overflow-x: auto;
        }
        
        @media (max-width: 768px) {
            .search-bar {
                width: 200px;
            }
            
            .action-buttons {
                flex-wrap: wrap;
            }
            
            .btn {
                padding: 6px 12px;
                font-size: 14px;
            }
            
            .inventory-table th, 
            .inventory-table td {
                padding: 8px 10px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 576px) {
            header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .search-bar {
                width: 100%;
                margin: 10px 0;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-body label {
            font-size: 14px;
            font-weight: 500;
        }

        .modal-body input {
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            width: 100%;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        .modal-footer button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-cancel {
            background-color: #ccc;
            color: #333;
        }

        .btn-save {
            background-color: var(--primary-color);
            color: white;
        }

        /* Delete modal styles */
        .modal-delete {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-delete-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-delete-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .modal-delete-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-delete-body label {
            font-size: 14px;
            font-weight: 500;
        }

        .modal-delete-body input {
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            width: 100%;
        }

        .modal-delete-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        .modal-delete-footer button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-cancel-delete {
            background-color: #ccc;
            color: #333;
        }

        .btn-confirm-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        /* Sort dropdown menu */
        .sort-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .sort-dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }
        
        .sort-dropdown a:hover {
            background-color: #f1f1f1;
        }
        
        .show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSIjMDA5OWNjIiBkPSJNMjU2IDhDMTE5LjA0MyA4IDggMTE5LjA4MyA4IDI1NmMwIDEzNi45OTcgMTExLjA0MyAyNDggMjQ4IDI0OHMyNDgtMTExLjAwMyAyNDgtMjQ4QzUwNCAxMTkuMDgzIDM5Mi45NTcgOCAyNTYgOHptMCA0NThjLTExMC41MzIgMC0yMDAtODkuNDMxLTIwMC0yMDAgMC0xMTAuNDk1IDg5LjQ3Mi0yMDAgMjAwLTIwMCAxMTAuNDkgMCAyMDAgODkuNDcxIDIwMCAyMDAgMCAxMTAuNTMtODkuNDMxIDIwMC0yMDAgMjAwem0xMDcuMjQ0LTM2Ny4yNTRsLTMwLjUzNiAyMDQuMjA0Yy0xLjU2NSAxMC40MDQtMTAuNDA0IDE4LjE4MS0yMC45MjcgMTguMzgxaC0xMDMuNTZjLTEwLjUyMyAwLTE5LjM2Mi03Ljc3Ny0yMC45MjctMTguMTgxTDE1Ni43NTYgOTguNzQ2Yy0yLjg4MS0xOS4yMDYgMTEuOTM2LTM2Ljc0NiAzMS4yMTctMzYuNzQ2aDEzOC4wNTRjMTkuMjgxIDAgMzQuMDk4IDE3LjU0IDMxLjIxNyAzNi43NDZ6TTI1NiAyNzJjLTIyLjA5NiAwLTQwIDE3Ljk0NC00MCA0MHMxNy45MDQgNDAgNDAgNDAgNDAtMTcuOTQ0IDQwLTQwLTE3LjkwNC00MC00MC00MHoiLz48L3N2Zz4=" alt="MediStock Logo">
                <h1>MediStock | USC Clinic Inventory System</h1>
            </div>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search...">
            </div>
            <div class="action-buttons">
                <div style="position: relative;">
                    <button class="btn btn-sort" id="sortBtn">
                        <i class="fas fa-sort"></i> Sort
                    </button>
                    <div id="sortDropdown" class="sort-dropdown">
                        <a href="?sort=name&dir=ASC">Name (A-Z)</a>
                        <a href="?sort=name&dir=DESC">Name (Z-A)</a>
                        <a href="?sort=expiry_date&dir=ASC">Expiry (Oldest)</a>
                        <a href="?sort=expiry_date&dir=DESC">Expiry (Newest)</a>
                        <a href="?sort=quantity&dir=DESC">Quantity (High-Low)</a>
                        <a href="?sort=quantity&dir=ASC">Quantity (Low-High)</a>
                    </div>
                </div>
                <button class="btn btn-add" id="addBtn">
                    <i class="fas fa-plus"></i> Add
                </button>
                <button class="btn btn-edit" id="editBtn">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-export">
                    <i class="fas fa-share-square"></i>
                </button>
                <button class="btn btn-delete" id="deleteBtn">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button class="btn btn-exit" id="exitBtn">
                    <i class="fas fa-sign-out-alt"></i> Exit
                </button>
            </div>
        </header>
        
        <div class="responsive-table">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th><a href="?sort=name&dir=<?php echo ($orderBy == 'name' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Product <i class="fas fa-sort<?php echo ($orderBy == 'name') ? ($orderDirection == 'ASC' ? '-up' : '-down') : ''; ?>"></i></a></th>
                        <th><a href="?sort=unique_id&dir=<?php echo ($orderBy == 'unique_id' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">ID <i class="fas fa-sort<?php echo ($orderBy == 'unique_id') ? ($orderDirection == 'ASC' ? '-up' : '-down') : ''; ?>"></i></a></th>
                        <th><a href="?sort=form&dir=<?php echo ($orderBy == 'form' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Form <i class="fas fa-sort<?php echo ($orderBy == 'form') ? ($orderDirection == 'ASC' ? '-up' : '-down') : ''; ?>"></i></a></th>
                        <th><a href="?sort=expiry_date&dir=<?php echo ($orderBy == 'expiry_date' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Expiry <i class="fas fa-sort<?php echo ($orderBy == 'expiry_date') ? ($orderDirection == 'ASC' ? '-up' : '-down') : ''; ?>"></i></a></th>
                        <th><a href="?sort=location&dir=<?php echo ($orderBy == 'location' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Location <i class="fas fa-sort<?php echo ($orderBy == 'location') ? ($orderDirection == 'ASC' ? '-up' : '-down') : ''; ?>"></i></a></th>
                        <th><a href="?sort=quantity&dir=<?php echo ($orderBy == 'quantity' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Quantity <i class="fas fa-sort<?php echo ($orderBy == 'quantity') ? ($orderDirection == 'ASC' ? '-up' : '-down') : ''; ?>"></i></a></th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr data-id='" . $row['id'] . "'>";
                            echo "<td><input type='checkbox' class='checkbox'></td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['unique_id'] . "</td>";
                            echo "<td>" . $row['form'] . "</td>";
                            echo "<td>" . date('m/d/Y', strtotime($row['expiry_date'])) . "</td>";
                            echo "<td>" . $row['location'] . "</td>";
                            $quantityClass = ((float)$row['quantity'] < 10) ? 'quantity-cell quantity-low' : 'quantity-cell';

echo "<td class='" . $quantityClass . "'>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td><i class='fas fa-info-circle info-icon' data-remarks='" . htmlspecialchars($row['remarks'], ENT_QUOTES) . "'></i></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' style='text-align: center;'>No items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for adding a new item -->
    <div class="modal" id="addItemModal">
        <div class="modal-content">
            <div class="modal-header">Add New Item</div>
            <form id="addForm" method="post" action="add_item.php">
                <div class="modal-body">
                    <label for="product">Product</label>
                    <input type="text" id="product" name="product" placeholder="Enter product name" required>
                    <label for="productId">ID</label>
                    <input type="text" id="productId" name="productId" placeholder="Enter product ID" required>
                    <label for="form">Form</label>
                    <input type="text" id="form" name="form" placeholder="Enter form (e.g., 50 mg Tablet)" required>
                    <label for="expiry">Expiry</label>
                    <input type="date" id="expiry" name="expiry" required>
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="Enter location" required>
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" step="0.01" placeholder="Enter quantity" required>
                    <label for="remarks">Remarks</label>
                    <input type="text" id="remarks" name="remarks" placeholder="Enter remarks">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="cancelAdd">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for editing an item -->
    <div class="modal" id="editItemModal">
        <div class="modal-content">
            <div class="modal-header">Edit Item</div>
            <form id="editForm" method="post" action="edit_item.php">
                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">
                    <label for="edit-product">Product</label>
                    <input type="text" id="edit-product" name="product" placeholder="Enter product name" required>
                    <label for="edit-productId">ID</label>
                    <input type="text" id="edit-productId" name="productId" placeholder="Enter product ID" required>
                    <label for="edit-form">Form</label>
                    <input type="text" id="edit-form" name="form" placeholder="Enter form (e.g., 50 mg Tablet)" required>
                    <label for="edit-expiry">Expiry</label>
                    <input type="date" id="edit-expiry" name="expiry" required>
                    <label for="edit-location">Location</label>
                    <input type="text" id="edit-location" name="location" placeholder="Enter location" required>
                    <label for="edit-quantity">Quantity</label>
                    <input type="number" id="edit-quantity" name="quantity" step="0.01" placeholder="Enter quantity" required>
                    <label for="edit-remarks">Remarks</label>
                    <input type="text" id="edit-remarks" name="remarks" placeholder="Enter remarks">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="cancelEdit">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for deleting an item -->
    <div class="modal-delete" id="deleteItemModal">
        <div class="modal-delete-content">
            <div class="modal-delete-header">Delete Selected Items</div>
            <div class="modal-delete-body">
                <p id="deleteItemsList">Are you sure you want to delete the following items?</p>
                <form id="deleteForm" method="post">
                    <input type="hidden" id="deleteIds" name="productId">
                </form>
            </div>
            <div class="modal-delete-footer">
                <button class="btn-cancel-delete" id="cancelDelete">Cancel</button>
                <button class="btn-confirm-delete" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>

    <script>
        // Toggle sort dropdown
        document.getElementById('sortBtn').addEventListener('click', function() {
            document.getElementById('sortDropdown').classList.toggle('show');
        });

        // Close the dropdown if clicked outside
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.btn-sort') && !event.target.matches('.btn-sort *')) {
                const dropdown = document.getElementById('sortDropdown');
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        });

        // Add interactivity to checkboxes
        document.querySelectorAll('.checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                if (this.checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            });
        });

        // Add interactivity to add button
        document.getElementById('addBtn').addEventListener('click', function() {
    document.getElementById('addItemModal').style.display = 'flex';
});

// Close add modal on cancel
document.getElementById('cancelAdd').addEventListener('click', function() {
    document.getElementById('addItemModal').style.display = 'none';
});

// Handle form submission for adding items
document.getElementById('addForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'add_item.php', true);
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            if (xhr.responseText.includes('Error')) {
                alert(xhr.responseText);
            } else {
                // Check if the response contains an alert script
                if (xhr.responseText.includes('<script>alert')) {
                    // Extract the alert message
                    const alertMatch = xhr.responseText.match(/<script>alert$$'(.*?)'$$;<\/script>/);
                    if (alertMatch && alertMatch[1]) {
                        alert(alertMatch[1]);
                    }
                    
                    // Reload the page to show the updated inventory
                    window.location.reload();
                } else {
                    // Hide the modal and reload the page
                    document.getElementById('addItemModal').style.display = 'none';
                    window.location.reload();
                }
            }
        } else {
            alert('Error adding item.');
        }
    };
    
    xhr.onerror = function() {
        alert('Error adding item. Please check your connection.');
    };
    
    xhr.send(formData);
});

        // Add interactivity to edit button
        document.getElementById('editBtn').addEventListener('click', function() {
            const selectedRows = document.querySelectorAll('tr.selected');
            if (selectedRows.length !== 1) {
                alert('Please select exactly one item to edit.');
                return;
            }

            const row = selectedRows[0];
            const id = row.getAttribute('data-id') || '';
            const product = row.cells[1].textContent.trim();
            const productId = row.cells[2].textContent.trim();
            const form = row.cells[3].textContent.trim();
            
            // Parse date from MM/DD/YYYY to YYYY-MM-DD for the input field
            let expiry = row.cells[4].textContent.trim();
            const dateParts = expiry.split('/');
            if (dateParts.length === 3) {
                expiry = `${dateParts[2]}-${dateParts[0].padStart(2, '0')}-${dateParts[1].padStart(2, '0')}`;
            }
            
            const location = row.cells[5].textContent.trim();
            const quantity = row.cells[6].textContent.trim();
            const remarks = row.querySelector('.info-icon').getAttribute('data-remarks') || '';

            // Populate the edit form
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-product').value = product;
            document.getElementById('edit-productId').value = productId;
            document.getElementById('edit-form').value = form;
            document.getElementById('edit-expiry').value = expiry;
            document.getElementById('edit-location').value = location;
            document.getElementById('edit-quantity').value = quantity;
            document.getElementById('edit-remarks').value = remarks;

            // Show the edit modal
            document.getElementById('editItemModal').style.display = 'flex';
        });

        // Close edit modal on cancel
        document.getElementById('cancelEdit').addEventListener('click', function() {
            document.getElementById('editItemModal').style.display = 'none';
        });

        // Add interactivity to delete button
        document.getElementById('deleteBtn').addEventListener('click', function() {
            const selectedRows = document.querySelectorAll('tr.selected');
            if (selectedRows.length === 0) {
                alert('Please select at least one item to delete.');
                return;
            }

            const deleteItemsList = document.getElementById('deleteItemsList');
            deleteItemsList.innerHTML = 'Are you sure you want to delete the following items?<ul>';
            
            const selectedIds = [];
            selectedRows.forEach(row => {
                const productName = row.cells[1].textContent;
                const productId = row.cells[2].textContent;
                deleteItemsList.innerHTML += `<li>${productName} (ID: ${productId})</li>`;
                selectedIds.push(productId);
            });
            deleteItemsList.innerHTML += '</ul>';
            
            // Store the selected IDs in the hidden input
            document.getElementById('deleteIds').value = selectedIds.join(',');

            // Show the delete modal
            document.getElementById('deleteItemModal').style.display = 'flex';
        });

        // Close delete modal on cancel
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteItemModal').style.display = 'none';
        });

        // Confirm delete
        document.getElementById('confirmDelete').addEventListener('click', function() {
            const selectedRows = document.querySelectorAll('tr.selected');
            let deletedCount = 0;
            let totalToDelete = selectedRows.length;
            
            selectedRows.forEach(row => {
                const productId = row.cells[2].textContent.trim();
                
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_item.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                
                xhr.onload = function() {
                    deletedCount++;
                    
                    if (xhr.status === 200) {
                        row.remove();
                    } else {
                        alert("Error deleting item: " + xhr.responseText);
                    }
                    
                    if (deletedCount === totalToDelete) {
                        alert("Selected items have been deleted successfully.");
                    }
                };
                
                xhr.send(`productId=${encodeURIComponent(productId)}`);
            });
            
            document.getElementById('deleteItemModal').style.display = 'none';
        });

        // Add interactivity to search
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `search_item.php?searchTerm=${encodeURIComponent(searchTerm)}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const tableBody = document.querySelector('.inventory-table tbody');
                    tableBody.innerHTML = xhr.responseText;
                    
                    // Re-attach checkbox listeners
                    document.querySelectorAll('.inventory-table tbody tr').forEach(row => {
                        const checkbox = row.querySelector('.checkbox');
                        if (checkbox) {
                            checkbox.addEventListener('change', function() {
                                if (this.checked) {
                                    row.classList.add('selected');
                                } else {
                                    row.classList.remove('selected');
                                }
                            });
                        }
                    });
                }
            };
            xhr.send();
        });

        // Add interactivity to info icons
        document.querySelectorAll('.info-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const remarks = this.getAttribute('data-remarks') || 'No remarks available';
                alert(remarks);
            });
        });

        // Add exit button functionality
        document.getElementById('exitBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to exit?')) {
                window.location.href = 'login.php';
            }
        });
    </script>
</body>
</html>
