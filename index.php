<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Expense Tracker</title>
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
<nav class="navbar navbar-expand-lg nav-color">
    <div class="container-fluid">
      <a class="navbar-brand fs-2" href="index.php">D<span>E</span>T</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
          </li>
          <?php  
          if (isset($_SESSION['signup_id'])) {
            if ($_SESSION['signup_id'] == true) {
              echo '
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  ' . $_SESSION['username'] . '
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-color">
                  <li><a class="dropdown-item" href="logout.php">Logout</a></li>';
              echo '</ul>
            </li>';
          }
        }   else {
            echo '
            <li class="nav-item">
              <a class="nav-link btn btn-index" href="login.php">Add Expense</a>
            </li>';
          }
        ?>
      </ul>
    </div>
  </div>
</nav>
<div class="index-content d-flex align-items-center justify-content-center flex-column">
   <h1 class="text-dark"><strong>Wallet Watch <span> Daily Expense </span> Manager</strong></h1>
   <p class="text-dark fs-5">Effortlessly manage your daily expenses with our intuitive tracker, ensuring financial control and peace of mind every day.</p>
   <a href="" class="btn" id="indexBtn">Add Expense Now</a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
 <script>
$(document).ready(function(){
    $('#indexBtn').click(function(){
        $.ajax({
            type: 'POST',
            url: 'check_session.php', 
            dataType: 'json',
            success: function(response){
                console.log(response);
                if(response.redirect) {
                    window.location.href = response.redirect;
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                console.log(status);
                console.log(error);
            }
        });
    });
});
</script>
</body>
</html>
