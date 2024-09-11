<?php
include('connection.php');

if (isset($_SESSION['signup_id'])) {
    header('Location: index.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = array(); 

    // Retrieve signup data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    if (empty($username)) {
        $response['username'] = 'Username is required';
    }

    if (empty($email)) {
        $response['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['email'] = 'Invalid email format';
    }

    if (empty($password)) {
        $response['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $response['password'] = 'Password must be at least eight characters long';
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W)/", $password)) {
        $response['password'] = 'Password must have at least one lowercase letter, one uppercase letter, and one special character';
    }

    if ($password != $confirmPassword) {
        $response['confirmPassword'] = 'Passwords do not match';
    }

    if (empty($response)) {
        $selectquery = "SELECT * FROM signup WHERE email='$email'";
        $result = $connection->query($selectquery);

        if ($result->num_rows == 0) {
            $encpassword = md5($password);

            $insertQuery = "INSERT INTO signup (username, email, password, confirm_password) VALUES ('$username', '$email', '$encpassword', '$encpassword')";

            if ($connection->query($insertQuery) === TRUE) {
                $response['success'] = 'Registration successful. Please login!';
            } else {
                $response['error'] = "Error: " . $insertQuery . "<br>" . $connection->error;
            }
        } else {
            $response['error'] = 'This email already exists. Please login.';
        }
    }

    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Expense Tracker | Signup Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5 d-flex justify-content-center align-items-center vh-100">
    <div class="col-md-6 Form">
        <h2 class="mb-4"><strong>Signup Form</strong></h2>
        <form id="signupForm" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
            </div>
            <button type="button" class="btn btn-primary" id="signupBtn">Signup</button>
            <div class="link text-dark">
                <p>If you already have an account <a href="login.php">Login</a> Here</p>
            </div>
        </form>
        <div id="signupResult" class="mt-3"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script>
    $(document).ready(function(){
        $('#signupBtn').click(function(){
            // Clear previous messages
            $('#signupResult').removeClass('error-message success-message').html('');

            // Client-side validation
            var username = $('#username').val();
            var email = $('#email').val();
            var password = $('#password').val();
            var confirmPassword = $('#confirmPassword').val();

            if(username === '' || email === '' || password === '' || confirmPassword === ''){
                alert('All fields are required.');
            } else if(password !== confirmPassword){
                alert('Passwords do not match.');
            } else {
                // AJAX form submission
                $.ajax({
                    type: 'POST',
                    url: 'signup.php',
                    data: $('#signupForm').serialize(),
                    dataType: 'json', 
                    success: function(response){
                        if(response.username) {
                            $('#signupResult').html(response.username).addClass('error-message');
                        }
                        if(response.email) {
                            $('#signupResult').append('<br>' + response.email).addClass('error-message');
                        }
                        if(response.password) {
                            $('#signupResult').append('<br>' + response.password).addClass('error-message');
                        }
                        if(response.confirmPassword) {
                            $('#signupResult').append('<br>' + response.confirmPassword).addClass('error-message');
                        }
                        // Handle success or other error messages
                        if(response.success) {
                            $('#signupResult').html(response.success).addClass('success-message');
                            // Reset the form on successful registration
                            $('#signupForm')[0].reset();
                        }
                        if(response.error) {
                            $('#signupResult').append('<br>' + response.error).addClass('error-message');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText); // Log the responseText to the console
                        console.log(status); // Log the status
                        console.log(error); // Log the error
                        $('#signupResult').html('Error submitting the form.').addClass('error-message');
                    }
                });
            }
        });
    });

</script>
</body>
</html>
