<?php
session_start();
if (!isset($_SESSION['signup_id'])) {
    header('location:login.php');
    exit();
}

include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = array(); 

    $item_date = $_POST["item_date"];
    $item_name = $_POST["item_name"];
    $item_cost = $_POST["item_cost"];
    $item_qty = $_POST["item_qty"];
    $info = $_POST["info"];

    if (empty($item_date)) $response['item_date'] = 'item_date is required';
    if (empty($item_name)) $response['item_name'] = 'item_name is required';
    if (empty($item_cost)) $response['item_cost'] = 'item cost is required';
    if (empty($item_qty)) $response['item_qty'] = 'item quantity is required';

    if (empty($response)) {
        $signup_id = $_SESSION['signup_id'];
        $info = str_replace("'", "", $info);

        $stmt = $connection->prepare("INSERT INTO add_expense (signup_id, item_date, item_name, item_cost, item_qty, info) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdis", $signup_id, $item_date, $item_name, $item_cost, $item_qty, $info);

        if ($stmt->execute()) {
            $response['success'] = 'Expense added successfully!';
        } else {
            $response['error'] = "Error: " . $stmt->error;
            error_log("Error in dashboard.php: " . $stmt->error);
        }
        $stmt->close();
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
    <title>Daily Expense Tracker|Dashboard</title>

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
  <div class="sidebar-toggle" onclick="toggleSidebar()">
        <button class="btn btn-primary">☰</button>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebarnavs">
            <h5>Welcome to dashboard</h5>
            <a href="javascript:void(0);" onclick="showDashboard()">Dashboard</a>
            <a href="javascript:void(0);" onclick="showProfile()">Profile</a>
            <a href="javascript:void(0);" onclick="showAdd()">Add Expenses</a>
            <a href="javascript:void(0);" onclick="showManage()">Manage Expenses</a>
            <a href="javascript:void(0);" onclick="showdatewise()">Date Wise Expense</a>
            <a href="javascript:void(0);" onclick="showMonthly()">Monthly Expense</a>
            <a href="javascript:void(0);" onclick="showYearly()">Yearly Expense</a>
        </div>
    </div>

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







                                <!-- profileSection -->
<div class="content">
    <div class="card" id="profileSection"  style="display: none;">
        <div class="card-header">
            <h5 class="card-title">Profile Details</h5>
        </div>
        <div class="card-body">
      
        </div>
    </div>
</div>    








<!-- Add Expense section -->
<div class="content" id="addExpense" style="display: none;">
    <div id="resultMessage" class="my-3"></div>
    <h2 class="mb-4 text-dark">Add Expense</h2>
    <form id="addform">
        <div class="mb-3">
           <label  class="form-label text-dark">Date of Expense</label>
           <input type="date" class="form-control" name="item_date" id="item_date" autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="item_name" class="form-label text-dark">Item Name</label>
            <input type="text" class="form-control" name="item_name" id="item_name"  autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="item_cost" class="form-label text-dark">Cost of Item</label>
            <input type="number" class="form-control" name="item_cost" id="item_cost">
        </div>
        <div class="mb-3">
            <label for="item_qty" class="form-label text-dark">Item Quantity</label>
            <input type="number" class="form-control" name="item_qty" id="item_qty">
        </div>
        <div class="mb-3">
            <label for="info" class="form-label text-dark">Additional Info</label>
            <textarea class="form-control" name="info" id="info"></textarea>
        </div>
        <button type="button" id="save-button" class="btn btn-primary btn-block"><strong>Add Expense</strong></button>
    </form>
</div>





<!-- ManageExpense section -->
<div class="content" id="manageExpense"  style="display: none;">
        <div class="d-flex button justify-content-between">
            <h1 class="text-dark">Manage Expense</h1>
        </div>
        <button type="button" id="showData" class="btn btn-primary btn-block mb-3"><strong>Show Data</strong></button>
        <table class="table table-bordered mt-5 table-striped ">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Item Date</th>
            <th scope="col">Item Name</th>
            <th scope="col">Item Cost</th>
            <th scope="col">Item Qty</th>
            <th scope="col">Item Information</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody id="result">
        <!-- Data will be dynamically inserted here -->
    </tbody>
</table>

    </div>    
</div>




<!-- daily datewise expense report -->
<div class="content" id="dateWise" style="display: none;">
    <div id="resultText" class="mt-3"></div>
    <h2 class="mb-4 text-dark">Date Wise Expense Report</h2>
    <form id="dateForm">
        <div class="mb-3">
           <label  class="form-label text-dark fs-5">From Date</label>
           <input type="date" class="form-control" id="startDate" name="startDate" autocomplete="off">
        </div>
        <div class="mb-3">
           <label  class="form-label text-dark fs-5">From Date</label>
           <input type="date" class="form-control" id="endDate" name="endDate" autocomplete="off">
        </div>
        <button type="button" onclick="fetchData()" class="btn btn-primary btn-block fs-5 mb-5"><strong>Submit</strong></button>
    </form>
    <table class="table table-bordered  table-striped ">
    <thead>
        <tr>
            <th scope="col">S.No</th>
            <th scope="col">Item Date</th>
            <th scope="col">Item Name</th>
            <th scope="col">Item Cost</th>
        </tr>
    </thead>
    <tbody id="yourresult">
        <!-- Data will be dynamically inserted here -->
    </tbody>
</table>
    <div id="result"></div>
</div>




<!-- Monthly Expense section -->
<div class="content" id="Monthly" style="display: none;">
    <div id="resultText" class="mt-3"></div>
    <h2 class="mb-4 text-dark">Monthly Expense Report</h2>
    <form id="monthlyForm">

        <div class="mb-3">
           <label  class="form-label text-dark fs-5">From Date</label>
           <input type="date" class="form-control" id="startMonth" name="startMonth" autocomplete="off">
        </div>
        <div class="mb-3">
           <label  class="form-label text-dark fs-5">From Date</label>
           <input type="date" class="form-control" id="endMonth" name="endMonth" autocomplete="off">
        </div>
        <button type="button" onclick="fetch()" class="btn btn-primary btn-block fs-5 mb-5"><strong>Submit</strong></button>
    </form>
    <table class="table table-bordered  table-striped ">
    <thead>
        <tr>
            <th scope="col">S.No</th>
            <th scope="col">Item Date</th>
            <th scope="col">Item Name</th>
            <th scope="col">Item Cost</th>
        </tr>
    </thead>
    <tbody id="your_result">
        
    </tbody>
</table>
    <div id="result-2"></div>
</div>




<!-- Yearly Expense section -->
<div class="content" id="Yearly" style="display: none;">
    <div id="resultText" class="mt-3"></div>
    <h2 class="mb-4 text-dark">Yearly Expense Report</h2>
    <form id="yearlyForm">

        <div class="mb-3">
           <label  class="form-label text-dark fs-5">From Date</label>
           <input type="date" class="form-control" id="startYear" name="startYear" autocomplete="off">
        </div>
        <div class="mb-3">
           <label  class="form-label text-dark fs-5">To Date</label>
           <input type="date" class="form-control" id="endYear" name="endYear" autocomplete="off">
        </div>
        <button type="button" onclick="fetchresult()" class="btn btn-primary btn-block fs-5 mb-5"><strong>Submit</strong></button>
    </form>
    <table class="table table-bordered  table-striped ">
    <thead>
        <tr>
            <th scope="col">S.No</th>
            <th scope="col">Item Date</th>
            <th scope="col">Item Name</th>
            <th scope="col">Item Cost</th>
        </tr>
    </thead>
    <tbody id="yourdata">
        <!-- Data will be dynamically inserted here -->
    </tbody>
</table>
    <div id="result-3"></div>
</div>



<?php 
$yesterday = date('Y-m-d', strtotime('-1 day'));
$expenseQuery = "SELECT SUM(item_cost) as total_cost FROM add_expense WHERE item_date = '$yesterday'";
    $expenseResult = mysqli_query($connection, $expenseQuery);
    $expenseRow = mysqli_fetch_assoc($expenseResult);
    $totalCost = $expenseRow['total_cost'] ? $expenseRow['total_cost'] : 0;
 ?>
<div class="container mt-2" id="dashboardSection">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 cards">
        <!-- Yesterday Expense -->
        <div class="col">
            <div class="card card-style">
                <h5>Yesterday Expense</h5>
                <div class="d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-envelope-open-text fs-1 mx-3 mb-2"></i>
                    <h1>Pkr <?php echo $totalCost; ?></h1>
                </div>
            </div>
        </div>
<?php  
$startDate = date('Y-m-d', strtotime('-7 days'));
$endDate = date('Y-m-d');
$expenseQuery = "SELECT SUM(item_cost) as total_cost FROM add_expense WHERE item_date BETWEEN '$startDate' AND '$endDate'";
$expenseResult = mysqli_query($connection, $expenseQuery);
$expenseRow = mysqli_fetch_assoc($expenseResult);
$totalCostLast7Days = $expenseRow['total_cost'] ? $expenseRow['total_cost'] : 0;
?>
        <!-- Last 7 days Expense -->
        <div class="col">
            <div class="card card-style">
                <h5>Last 7 days Expense</h5>
                <div class="d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-envelope-open-text fs-1 mx-3 mb-2"></i>
                    <h1> Pkr <?php echo $totalCostLast7Days; ?></h1>
                </div>
            </div>
        </div>

<?php 
$startDate = date('Y-m-d', strtotime('-30 days'));
$endDate = date('Y-m-d');
$expenseQuery = "SELECT SUM(item_cost) as total_cost FROM add_expense WHERE item_date BETWEEN '$startDate' AND '$endDate'";
$expenseResult = mysqli_query($connection, $expenseQuery);
$expenseRow = mysqli_fetch_assoc($expenseResult);
$totalCostLast30Days = $expenseRow['total_cost'] ? $expenseRow['total_cost'] : 0;
 ?>
        
        <!-- Last 30 days Expense -->
        <div class="col">
            <div class="card card-style">
                <h5>Last 30 days Expense</h5>
                <div class="d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-envelope-open-text fs-1 mx-3 mb-2"></i>
                    <h1>Pkr <?php echo $totalCostLast30Days; ?></h1>
                </div>
            </div>
        </div>
<?php 
$startDate = date('Y-m-d', strtotime('-1 year'));
$endDate = date('Y-m-d');

// Query to sum the cost of expenses from the last year
$expenseQuery = "SELECT SUM(item_cost) as total_cost FROM add_expense WHERE item_date BETWEEN '$startDate' AND '$endDate'";
$expenseResult = mysqli_query($connection, $expenseQuery); 
$expenseRow = mysqli_fetch_assoc($expenseResult);
$totalCostLastYear = $expenseRow['total_cost'] ? $expenseRow['total_cost'] : 0;
?>
        <!-- Current Year Expense -->
        <div class="col">
            <div class="card card-style">
                <h5>Current Year Expense</h5>
                <div class="d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-envelope-open-text fs-1 mx-3 mb-2"></i>
                    <h1>Pkr <?php echo $totalCostLastYear; ?></h1>
                </div>
            </div>
        </div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script>
function showDashboard() {
               $('#dashboardSection').show();
               $('#profileSection').hide();
               $('#addExpense').hide();
               $('#manageExpense').hide();
               $('#dateWise').hide();
               $('#expenseReport').hide();          
               $('#Monthly').hide();
               $('#Yearly').hide();


            }
             function showProfile() {
                $('#dashboardSection').hide();
                $('#profileSection').show();
                $('#addExpense').hide();
                $('#manageExpense').hide();
                $('#dateWise').hide();                
                $('#Monthly').hide();
                $('#Yearly').hide();

            }
            function showAdd() {
                $('#dashboardSection').hide();
                $('#profileSection').hide();
                $('#addExpense').show();
                $('#manageExpense').hide();
                $('#dateWise').hide();               
                $('#Monthly').hide();
                $('#Yearly').hide();
            }
             function showManage() {
                $('#dashboardSection').hide();
                $('#profileSection').hide();   
                $('#addExpense').hide();
                $('#manageExpense').show();
                $('#dateWise').hide();
                $('#Monthly').hide();
                $('#Yearly').hide();
            }

            function showdatewise(){
                $('#dashboardSection').hide();
                $('#profileSection').hide();
                $('#addExpense').hide();
                $('#manageExpense').hide();
                $('#dateWise').show();
                $('#Monthly').hide();
                $('#Yearly').hide();
            }

             function showWeekly() {
                $('#dashboardSection').hide();
                $('#profileSection').hide();
                $('#addExpense').hide();
                $('#manageExpense').hide();
                $('#dateWise').hide();
                $('#Monthly').hide();
                $('#Yearly').hide();
            }
             function showMonthly() {
                $('#dashboardSection').hide();
                $('#profileSection').hide();
                $('#addExpense').hide();
                $('#manageExpense').hide();
                $('#dateWise').hide();
                $('#Monthly').show();
                $('#Yearly').hide();
            }
             function showYearly() {
                $('#dashboardSection').hide();
                $('#profileSection').hide();
                $('#addExpense').hide();
                $('#manageExpense').hide();
                $('#dateWise').hide();
                $('#Monthly').hide();
                $('#Yearly').show();
            }

/*add expense*/
$(document).ready(function(){
    $('#save-button').click(function(event){
        event.preventDefault(); 
        var item_date = $('#item_date').val();
        var item_name = $('#item_name').val();
        var item_cost = $('#item_cost').val();
        var item_qty = $('#item_qty').val();
        var info = $('#info').val();

        if(item_date === '' || item_name === '' || item_cost === '' || item_qty === '' || info === '' ){
            alert('All fields are required.');
        } else {
            $.ajax({
                type: 'POST',
                url: 'dashboard.php',
                data: $('#addform').serialize(),
                dataType: 'json', 
                success: function(response){
                    
                    if(response.item_date) {
                        $('#resultMessage').html('<span class="error-message">' + response.item_date + '</span>');
                    }
                    if(response.item_name) {
                        $('#resultMessage').append('<br><span class="error-message">' + response.item_name + '</span>');
                    }
                    if(response.item_cost) {
                        $('#resultMessage').append('<br><span class="error-message">' + response.item_cost + '</span>');
                    }
                    if(response.item_qty) {
                        $('#resultMessage').append('<br><span class="error-message">' + response.item_qty + '</span>');
                    }
                    if(response.info) {
                        $('#resultMessage').append('<br><span class="error-message">' + response.info + '</span>');
                    }
                    // Handle success or other error messages
                    if(response.success) {
                        $('#resultMessage').html('<span class="success-message">' + response.success + '</span>');
                        $('#addform')[0].reset();
                    }
                    if(response.error) {
                        $('#resultMessage').append('<br><span class="error-message">' + response.error + '</span>');
                    }
                },
                error: function(){
                    $('#resultMessage').html('<span class="error-message">Error submitting the form.</span>');
                }
            });
        }
    });
});      


/*profile data*/

$.ajax({
        type: 'GET',
        url: 'profile_data.php',
        dataType: 'json',
        success: function (profileData) {
            if (profileData.err) {
                console.err(profileData.err);
            } else {
                $('#profileSection .card-title').html('Profile Details');
                $('#profileSection .card-body').html('<p class="card-text"><strong>Name: ' + profileData.username + '</strong></p>' +
                    '<p class="card-text"><strong>Email: ' + profileData.email + '</strong></p>');
            }
        },
        err: function () {
            // Handle err, such as displaying an err message
            console.err('Error fetching profile data.');
        }
    });


/*manage expense data*/
$(document).ready(function(){
    $('#showData').on('click', function(){
        // Clear existing data in the table body
        $('#result').empty();

        $.ajax({
            url: 'manage_expense.php', 
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.length > 0) {
                    // Use response directly, no need to parse
                    $.each(response, function(key, value){
                        $('#result').append('<tr>' +
                            '<td>' + value['add_id'] + '</td>' +
                            '<td>' + value['item_date'] + '</td>' +
                            '<td>' + value['item_name'] + '</td>' +
                            '<td>' + value['item_cost'] + '</td>' +
                            '<td>' + value['item_qty'] + '</td>' +
                            '<td>' + value['info'] + '</td>' +
                            '<td><button class="btn btn-danger btn-sm delete-btn" data-id="' + value['add_id'] + '">Delete</button></td>' +
                        '</tr>');
                    });
                } else {
                    // If no data found, display a message
                    $('#result').append('<tr><td colspan="7">No data found</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status, error);
                console.log(xhr.responseText);
            }
        });
    });
});



/*delete data*/
$(document).on('click', '.delete-btn', function() {
    var addId = $(this).data('id');

    // Confirm before deleting
    if (confirm("Are you sure you want to delete this record?")) {
        $.ajax({
            type: 'POST',
            url: 'delete_expense.php', // Create a new PHP file for delete operation
            data: { add_id: addId },
            dataType: 'json',
            success: function(response) {
                // Handle success or other error messages
                if (response.success) {
                    // Refresh the data after successful deletion
                    $('#showData').trigger('click');
                    alert(response.success);
                } else if (response.error) {
                    alert(response.error);
                }
            },
            error: function() {
                alert('Error deleting the record.');
            }
        });
    }
});


/*datewise data*/

function fetchData() {
    var startDate = $("#startDate").val();
    var endDate = $("#endDate").val();

    $.ajax({
        type: "POST",
        url: "datewise.php",
        data: { startDate: startDate, endDate: endDate },
        dataType: 'json',
        success: function(response) {
            console.log("Response:", response);

            // Check if the response is an array and has length
            if (Array.isArray(response) && response.length > 0) {
                // Clear existing data
                $("#yourresult").empty();

                var totalCost = 0; // Initialize the total cost

                // Iterate over the response array and append rows to the tbody
                $.each(response, function(index, record) {
                    $("#yourresult").append("<tr>" +
                        "<td>" + record.add_id + "</td>" +
                        "<td>" + record.item_date + "</td>" +
                        "<td>" + record.item_name + "</td>" +
                        "<td>" + record.item_cost + "</td>" +
                        "</tr>");

                    // Add the item_cost to the totalCost
                    totalCost += parseFloat(record.item_cost);
                });

                // Display the total cost
                $("#yourresult").append("<tr><td colspan='3'></td><td>Total Cost: " + totalCost + "</td></tr>");

            } else {
                // Handle case when no records are found
                $("#yourresult").html("<tr><td colspan='4'>No records found</td></tr>");
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status, error);
            console.log(xhr.responseText);
        }
    });
}


function fetch() {
    var startMonth = $('#startMonth').val(); // Corrected: Added parentheses to val() method
    var endMonth = $('#endMonth').val(); // Corrected: Added parentheses to val() method

    $.ajax({
        type: "POST",
        url: 'monthly.php',
        data: { startMonth: startMonth, endMonth: endMonth }, // Corrected: Added data property to send startMonth and endMonth to the server
        dataType: 'json',
        success: function (response) {
            console.log("Response:", response);
            if (Array.isArray(response) && response.length > 0) {
                $('#your_result').empty();
                var total = 0;
                $.each(response, function (index, record) {
                    $("#your_result").append("<tr>" +
                        "<td>" + record.add_id + "</td>" +
                        "<td>" + record.item_date + "</td>" +
                        "<td>" + record.item_name + "</td>" +
                        "<td>" + record.item_cost + "</td>" +
                        "</tr>");

                    // Add the item_cost to the total
                    total += parseFloat(record.item_cost);
                });
                // Display the total cost
                $("#your_result").append("<tr><td colspan='3'></td><td>Total Cost: " + total + "</td></tr>");

            } else {
                // Handle case when no records are found
                $("#your_result").html("<tr><td colspan='4'>No records found</td></tr>");
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status, error);
            console.log(xhr.responseText);
        },
    });
}

/*fetch year wise data*/
function fetchresult() {
    var startYear = $('#startYear').val();
    var endYear = $('#endYear').val();

    $.ajax({
        type: 'POST',
        url: 'year.php',
        data: { startYear: startYear, endYear: endYear },
        dataType: 'json',
        success: function(response) {
            console.log("Response:", response);
            if (Array.isArray(response) && response.length > 0) {
                $('#yourdata').empty();
                var total = 0;
                $.each(response, function(index, record) {
                    $("#yourdata").append("<tr>" +
                        "<td>" + record.add_id + "</td>" +
                        "<td>" + record.item_date + "</td>" +
                        "<td>" + record.item_name + "</td>" +
                        "<td>" + record.item_cost + "</td>" +
                        "</tr>");

                    // Add the item_cost to the total
                    total += parseFloat(record.item_cost);
                });
                // Display the total cost
                $("#yourdata").append("<tr><td colspan='3'></td><td>Total Cost: " + total + "</td></tr>");

            } else {
                // Handle case when no records are found
                $("#yourdata").html("<tr><td colspan='4'>No records found</td></tr>");
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status, error);
            console.log(xhr.responseText);
        },
    });
}

  function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
        

</script>
</body>
</html>

