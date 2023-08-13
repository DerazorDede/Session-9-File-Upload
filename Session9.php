<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Simple Web Form and File Upload</title>
</head>
<body>
    <?php
    $email = $fileError = $uploadSuccess = '';
    include 'Session9Database.php';
    function sanitizeInput($input) {
        return htmlspecialchars(trim($input));
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['email'])) {
            $email = sanitizeInput($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '<p class="error">Invalid email format</p>';
            }
        } else {
            echo '<p class="error">Email is required</p>';
        }
        if (!empty($_FILES['file']['name'])) {
            $allowedTypes = array('image/jpeg', 'image/png');
            $fileType = $_FILES['file']['type'];

            if (in_array($fileType, $allowedTypes)) {
                $targetDir = 'uploads/';
                $targetFile = $targetDir . basename($_FILES['file']['name']);

                if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                    $uploadSuccess = 'File uploaded successfully';
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

    <h1>Simple Web Form and File Upload</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
        <br>
        <label for="file">Upload File (JPEG/PNG only):</label>
        <input type="file" name="file" id="file" accept="image/jpeg,image/png" required>
        <br>
        <input type="submit" value="Submit">
    </form>

    <?php
    if (!empty($fileError)) {
        echo '<p class="error">' . $fileError . '</p>';
    }
    if (!empty($uploadSuccess)) {
        echo '<p class="success">' . $uploadSuccess . '</p>';
    }
    ?>
</body>
</html>
