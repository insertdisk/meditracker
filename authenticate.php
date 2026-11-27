<?php
// Start session
session_start();

// Include database connection
include('db_connection.php');

// Set response header to JSON
header('Content-Type: application/json');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from POST data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter both username and password']);
        exit;
    }
    
    // Query to check if username exists
    $sql = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Check if password is hashed (starts with $2y$) or plain text
        if (substr($row['password'], 0, 4) === '$2y$') {
            // Verify hashed password
            $password_correct = password_verify($password, $row['password']);
        } else {
            // Compare plain text password
            $password_correct = ($password === $row['password']);
        }
        
        if ($password_correct) {
            // Password is correct, create session
            $_SESSION['loggedin'] = true; // Added for session_check.php compatibility
            $_SESSION['user_id'] = $row['admin_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            
            // Return success response
            echo json_encode(['success' => true, 'message' => 'Login successful']);
            exit;
        } else {
            // Invalid password
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
            exit;
        }
    } else {
        // Username not found
        echo json_encode(['success' => false, 'message' => 'Username not found']);
        exit;
    }
    
    mysqli_close($conn);
} else {
    // Not a POST request
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
?>
