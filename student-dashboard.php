<?php 
    include 'functions.php'; 
    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Student';
    $studentID = $_SESSION['userID'];

    $studentData = getStudentData($studentID);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/student-dashboard.css">
    <link rel="icon" href="img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>Student Dashboard</title>
</head>

<body style="background-color: whitesmoke;">
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <div>
            <img src="img/logo.png" alt="" height="80px" class="ps-3">
        </div>
        <div class="head">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white " href="#">Student - Dashboard</a>
        </div>
    </header>

    <div class="sidebar position-relative">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class="pt-4 d-flex gy-1 " >
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="fs-6" style="font-weight: 500;">Ram Yturralde</p>
                    <p class="position-absolute" style="top: 52px;">CCIS - Student</p>
                </div>
            </div>  
            <div class="pt-5 text-center ps-2">
                <div class="d-flex gy-1">
                    <div>
                        <a href="change-password.php" style="text-decoration: none;" class="text-dark"><i class="fa-solid fa-unlock" style="font-size: 20px;"></i></a>
                    </div>
                    <div class="ps-2">
                        <a href="change-password.php" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium">Change Password</p></a>
                    </div>  
                </div>  
            </div>
            
            <hr>
            <div class="ps-3 pt-3">
                <a href="logout.php" class="text-danger">               
                    <i class="fa-solid fa-right-from-bracket">Logout</i>               
                </a>
            
            </div>
        </div>
        </div>
    </div>  

    <div class="container col-lg-6 pt-4 welcome">
        <p class="fs-2 fw-semibold">Welcome Ram Yturralde!</p>
    </div>

    <div class="container pt-3 ps-5 mt-4 custom-shadow position-relative col-lg-6">
        <p class="" style="font-size: 40px;">
            Student Information
        </p>
        <div class="d-flex justify-content-between custom-info">
        <div class="col-md-3">   
        <div style="display: flex; gap:10px;">
            <p class="fw-bold">Name:</p>
            <p></p>
        </div>
        <div style="display: flex; gap:10px;">
            <p class="fw-bold">Course:</p>
            <p></p>
        </div>
        <div style="display: flex; gap:10px;">
            <p class="fw-bold">Contact Number:</p>
            <p></p>
        </div>
        </div> 
        <div class="col-md-5">
        <div style="display: flex; gap:10px;">
            <p class="fw-bold">Student Number:</p>
            <p></p>
        </div>
        <div style="display: flex; gap:10px;">
            <p class="fw-bold">Year Level:</p>
            <p></p>
        </div>
        <div style="display: flex; gap:10px;">
            <p class="fw-bold">1st Semester:</p>
            <p> S.Y. 2024 - 2025</p>
        </div>
        </div>
        <div class="position-absolute end-0 me-5  top-0 mt-4">
            <img src="img/user-stud.png" alt="" height="170px">
        </div>
        </div>
    </div>

    <div class="container d-flex mt-5 col-lg-8 text-center">
    <div class="container custom-shadow pt-3 pb-5 ps-4" style="width: 30%;">
            <p class=" fw-semibold fs-5 text-center">
                Progress Bar
            </p>
            <div class="skill" style="padding-left:47px">
                <div class="outer ">
                    <div class="inner">
                        <div id="number">
                            
                        </div>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="160px" height="160px">
                    <defs>
                        <linearGradient id="GradientColor">
                        <stop offset="0%" stop-color="#e91e63" />
                        <stop offset="100%" stop-color="#673ab7" />
                        </linearGradient>
                    </defs>
                    <circle cx="80" cy="80" r="70" stroke-linecap="round" id="progress-circle"/>
                </svg>
            </div>
        </div>
        <div class="container">
            <table class="table">
                <thead>
                    <tr class="table-dark">
                        <th>Office/Dept</th>
                        <th>Signatory Name</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Library</th>
                        <th>Irene M. Mungcal</th>
                        <th class="text-danger">Declined</th>
                        <th>Kunwari Date</th>
                        <th>Balik mo muna libro</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>





    <script>
        let number = document.getElementById("number");
        let progressCircle = document.getElementById("progress-circle");

        let counter =0; // Start from 0 or any initial value
        let targetValue = 100; // Desired end value
        let maxCounter = 100; // Maximum percentage (100%)
        let maxDashOffset = 472; // Full circle dasharray value (circumference of the circle)

        // Set the initial state of the progress circle
        number.innerHTML = counter + "%";
        progressCircle.style.strokeDashoffset = maxDashOffset; // Start from 100% hidden

        // Animate the circle and counter to the target value
        let interval = setInterval(() => {
            if (counter >= targetValue) { // Stop the animation at the target value
                clearInterval(interval);
            } else {
                counter += 1; // Increment the counter
                number.innerHTML = counter + "%"; // Update the percentage text

                // Gradually reduce the stroke-dashoffset
                let dashOffset = maxDashOffset - (maxDashOffset * counter) / maxCounter;
                progressCircle.style.strokeDashoffset = dashOffset; // Apply the new offset
            }
        }, 30); // Adjust the speed of animation


        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>