<?php
include("../include/url_users.php");

/* If already logged in then redirect to previous page */
if (isset($_SESSION['username'])) {
    header('Location:../index.php');
    exit;
}

if (isset($_POST['submit'])) {
    $errors = []; // Array to store validation errors

    // Validate username
    if (empty($_POST['username'])) {
        $errors[] = "Username is required.";
    } else {
        $username = $_POST['username'];
    }

    // Validate firstname
    if (empty($_POST['firstname'])) {
        $errors[] = "Firstname is required.";
    } else {
        $firstname = $_POST['firstname'];
    }

    // Validate emailid
    if (empty($_POST['emailid'])) {
        $errors[] = "Email is required.";
    } else {
        $emailid = $_POST['emailid'];
    }

    // Validate password
    if (empty($_POST['password'])) {
        $errors[] = "Password is required.";
    } else {
        $password = $_POST['password'];
    }

    // If there are validation errors, display them and include the registration form
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        include("../include/frame_register.php"); // Include the registration form again
        exit; // Stop further execution
    }

    // If no validation errors, proceed to database insertion
    include("../db/dbconnect.php");

    // Check if user or email already exists
    $query = "SELECT * FROM users WHERE users.username='$username' OR users.emailid='$emailid'
              UNION
              SELECT * FROM users_buffer WHERE users_buffer.username='$username' OR users_buffer.emailid='$emailid'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
    }

    $rows = mysqli_num_rows($result);

    if ($rows > 0) {
        echo "Username or Email already exists. Redirecting to registration page...";
        header("refresh:3;url=register.php");
        exit;
    } else {
        // Insert into users_buffer table
        $query = "INSERT INTO users_buffer (username, firstname, password, emailid)
                  VALUES ('$username', '$firstname', '$password', '$emailid')";
        if (mysqli_query($conn, $query)) {
            echo "Registration successful. Redirecting to homepage...";
            header("refresh:3;url=../index.php");
            exit;
        } else {
            die("Error: " . mysqli_error($conn));
        }
    }
} else {
    include("../include/frame_register.php");
}
?>
