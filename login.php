<?php
session_start();

include('connection.php');

    
    if( isset($_SESSION['signup_id']) ){
        header('Location:index.php');
    }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = array(); 
    // Retrieve login data
    $email = ($_POST["email"]);
    $password = ($_POST["password"]);
    if (empty($email)) {
        $response['email'] = 'Email is required';
    }
    if (empty($password)) {
        $response['password'] = 'Password is required';
    }
    if (empty($response)) {
        $encpassword = md5($password);
        $selectquery = "SELECT * FROM signup WHERE email='$email' AND password='$encpassword'";
        $result = $connection->query($selectquery);

        if ($result->num_rows == 0) {
            $response['error'] = 'Password and Email combination dose not match';
        } 
            else{
                    $data=mysqli_fetch_assoc($result);

                    $_SESSION['signup_id']=$data['signup_id'];

                    $_SESSION['username']=$data['username'];

                    $_SESSION['email']=$data['email'];

                    $_SESSION['password']=$data['password'];

                    $_SESSION['confirm_password']=$data['confirm_password'];
                    $response['redirect'] = 'index.php';
        }

        $connection->close();
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
    <title>Daily Expense Tracker|Login Form</title>
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
            <h2 class="mb-4">Login Form</h2>
            <form id="loginForm" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                 <button type="button" class="btn btn-primary" id="loginBtn">Login</button>
                <div class="link text-dark">
                    <p>If you don't have an account<a href="signup.php"> Signup </a>Here</p>
                </div>
            </form>
            <div id="loginResult" class="mt-3"></div>
        </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script>
 $(document).ready(function(){
        $('#loginBtn').click(function(){
            // Clear previous messages
            $('#loginResult').removeClass('error-message success-message').html('');

            // Client-side validation
            var email = $('#email').val();
            var password = $('#password').val();

            if(email === '' || password === ''){
                alert('All fields are required.');
            } else {
                // AJAX form submission
                $.ajax({
                    type: 'POST',
                    url: 'login.php',
                    data: $('#loginForm').serialize(),
                    dataType: 'json',
                    success: function(response){
                        if(response.email) {
                            $('#loginResult').html(response.email).addClass('error-message');
                        }
                        if(response.password) {
                            $('#loginResult').append('<br>' + response.password).addClass('error-message');
                        }
                        if(response.redirect) {
                            window.location.href = response.redirect;
                        }
                        if(response.error) {
                            $('#loginResult').append('<br>' + response.error).addClass('error-message');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        console.log(status);
                        console.log(error);
                        $('#loginResult').html('Error submitting the form.').addClass('error-message');
                    }
                });
            }
        });
    });
</script>
</body>
</html>