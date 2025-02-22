<?php
// Assuming you're working with a form that includes the profile picture upload

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize a flag to track if there are any errors
    $uploadError = false;
    $errorMessage = "";

    // Check if a file has been uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file = $_FILES['profile_picture'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        // File extension validation
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Allowed image types
        if (!in_array($fileExt, $allowedExtensions)) {
            $uploadError = true;
            $errorMessage = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }

        // File size validation (e.g., max size 5MB)
        if ($fileSize > 5000000) {
            $uploadError = true;
            $errorMessage = "File is too large. Max file size is 5MB.";
        }

        // If no errors, proceed with uploading the file
        if (!$uploadError) {
            // Generate a unique name for the file
            $newFileName = uniqid('', true) . "." . $fileExt;
            $fileDestination = 'uploads/profile_pictures/' . $newFileName;

            // Move the uploaded file to the destination folder
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                // If file upload is successful, update the database
                // Assuming you have a function to update the user's profile picture in the database
                $userId = $_SESSION['user_id']; // Assuming user ID is stored in session

                // Prepare SQL query to update profile picture in the database
                $query = "UPDATE users SET profile_picture = ? WHERE id = ?";
                if ($stmt = $mysqli->prepare($query)) {
                    $stmt->bind_param("si", $fileDestination, $userId);
                    $stmt->execute();
                    $stmt->close();

                    echo "Profile picture updated successfully!";
                } else {
                    $uploadError = true;
                    $errorMessage = "Database update failed.";
                }
            } else {
                $uploadError = true;
                $errorMessage = "There was an error uploading your file.";
            }
        }
    } else {
        $uploadError = true;
        $errorMessage = "No file was uploaded or there was an upload error.";
    }

    // If there was any error, display the error message
    if ($uploadError) {
        echo "Error: " . $errorMessage;
    }
}
?>
