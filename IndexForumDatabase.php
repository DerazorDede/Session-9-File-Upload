<?php
    // Database configuration
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $dbname = "file_upload";

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Define variables to store user inputs
    $email = $fileError = $uploadSuccess = '';

    // Sanitize user inputs
    function sanitizeInput($input) {
        global $conn;
        return $conn->real_escape_string(htmlspecialchars(trim($input)));
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and validate email
        if (!empty($_POST['email'])) {
            $email = sanitizeInput($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '<p class="error">Invalid email format</p>';
            }
        } else {
            echo '<p class="error">Email is required</p>';
        }

        // Handle file upload
        if (!empty($_FILES['file']['name'])) {
            $allowedTypes = array('image/jpeg', 'image/png');
            $fileType = $_FILES['file']['type'];

            if (in_array($fileType, $allowedTypes)) {
                $targetDir = 'uploads/';
                $fileName = basename($_FILES['file']['name']);
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                    $uploadSuccess = 'File uploaded successfully';
                    
                    // Insert data into database
                    $stmt = $conn->prepare("INSERT INTO uploads (email, filename) VALUES (?, ?)");
                    $stmt->bind_param("ss", $email, $fileName);
                    if ($stmt->execute()) {
                        // Insertion successful
                    } else {
                        echo '<p class="error">Error inserting data into database</p>';
                    }
                } else {
                    $fileError = 'Error uploading file';
                }
            } else {
                $fileError = 'Only JPEG and PNG files are allowed';
            }
        } else {
            $fileError = 'File is required';
        }
    }
    ?>
