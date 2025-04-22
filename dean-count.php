<?php 
    include 'functions.php'; 

    $deanID = $_SESSION['userID'];
    $deanData = getDeanData($deanID);

    $commentAreaValue = '';
    $studID = $studName = $studCourse = "";
    $studentFound = false;
    $errorMessage = '';
    $currentDate = '';

    // Get pending student requests for Dean
    $studentRequests = getPendingStudentRequests('Dean');
    $studentRequestCount = count($studentRequests);
    $approvedStudentsCount = getApprovedStudentCount('Dean');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dean Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="styles/faculty-dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
    <div>
        <img src="img/logo.png" alt="" height="80px" class="ps-3">
    </div>
    <div class="head">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white " href="#">
            <?php echo "Dean - Dashboard"; ?>
        </a>
    </div>
</header>

<!-- Sidebar -->
<div class="sidebar pt-5">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class=" d-flex gy-1 " >
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="s" style="font-weight: 500;"><?php echo $deanData['dean_name']; ?></p>
                    <p class="position-absolute" style="top: 35px;">Dean</p>
                </div>
            </div>
            <div class="d-flex gy-1 pt-5 ms-2">
                    <div>
                        <a href="change-password.php" style="text-decoration: none;" class="text-dark">
                            <i class="fa-solid fa-unlock" style="font-size: 25px;"></i>
                        </a>
                    </div>
                    <div class="ps-3">
                        <a href="change-password.php" style="text-decoration: none;" class="text-dark">
                            <p class="fs-6 fw-medium">Change Password</p>
                        </a>
                    </div>  
                </div>

                <!-- Request List -->
                <div class="d-flex gy-1 pt-1 ms-2">
                    <div class="position-relative">
                        <a href="request-list.php" style="text-decoration: none;" class="text-dark">
                            <i class="fa-solid fa-list" style="font-size: 25px;"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">             
                                <?php echo $studentRequestCount;   ?>      
                            </span>
                        </a>
                    </div>
                    <div class="ps-3">
                        <a href="request-list.php" style="text-decoration: none;" class="text-dark">
                            <p class="fs-6 fw-medium mb-0">Request List</p>
                        </a>
                    </div>  
                </div>  

                <div class="pt-3 ms-2 d-flex align-items-center">
                <div class="fs-5">
                    <i class="fa-solid fa-house" style="font-size: 25px;"></i>
                </div>
                <div class="ps-3">
                    <a href="dean-count.php" class="text-decoration-none text-dark fs-6 fw-medium">Dashboard</a>
                </div>
            </div> 

            <div class="pt-4 ms-2 d-flex align-items-center">
                <div>
                    <i class="fa-solid fa-user-graduate " style="font-size: 25px;"></i>
                </div>
                <div class="ps-3">
                    <a href="dean-dashboard.php" class="text-decoration-none text-dark fs-6 fw-medium">Clearance</a>
                </div>
            </div>
                
            <br>  
            <hr>
            <div class="ps-3 pt-3">
                <a href="logout.php" class="text-danger">               
                    <i class="fa-solid fa-right-from-bracket">Logout</i>               
                </a>
            </div>
        </div>
        </div>
    </div>

<!-- Main Content -->
<div class="main-content pt-5">
    <div class="container pt-5">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card p-3 bg-primary text-white">
                    <h5>Student Requests</h5>
                    <h3><?php echo $studentRequestCount ?? 0; ?></h3>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card p-3 bg-success text-white">
                    <h5>Approved Students</h5>
                    <h3><?php echo $approvedStudentsCount ?? 0; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>