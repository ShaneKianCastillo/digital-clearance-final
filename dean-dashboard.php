<?php 

    include 'functions.php';

    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Faculty';
    $deanID = $_SESSION['userID'];

    $deanData = getDeanData($deanID);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/dean-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://kit.fontawesome.com/a082745512.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    <title>Document</title>
</head>
<body>
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <div>
            <img src="img/logo.png" alt="" height="80px" class="ps-3">
        </div>
        <div class="head">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white " href="#">Dean - Dashboard</a>
        </div>
    </header>

    <div class="sidebar position-relative">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class="pt-4 d-flex gy-1 " >
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="fs-6" style="font-weight: 500;"><?php echo $deanData['dean_name']; ?></p>
                    <p class="position-absolute" style="top: 52px;">CCIS - Dean</p>
                </div>
            </div>
            <div class="d-flex pt-5 ps-2">
                <div>
                    <a href="dean-dashboard.php" style="text-decoration: none;" class="text-dark"><i class="fa-solid fa-house" style="font-size: 25px;"></i></a>
                </div>
                <div class="ps-2">
                    <a href="dean-dashboard.php" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium">Dashboard</p></a>
                </div>
            </div>
            <div class="pt-3 text-center ps-2">
                <div class="d-flex gy-1">
                    <div>
                        <a href="dean-clearance.php" style="text-decoration: none;" class="text-dark"><i class="fa-regular fa-file" style="font-size: 25px;"></i></a>
                    </div>
                    <div class="ps-2">
                        <a href="dean-clearance.php" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium">Student Clearance</p></a>
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
    <div class="container text-center mt-5">
        <p class="fs-1 fw-bold">Welcome Dean!</p>
    </div>

    <div class="container col-lg-2 mt-4 p-3 shadow-sm bg-body-tertiary rounded">
        <div>
            <div>
                <input type="radio" name="option" class="form-check-input" id="customizeOption">
                <label for="customize" class="form-label fw-medium">Customize</label>
            </div>
            <div>
                <input type="radio" class="form-check-input" name="option" id="standardOption" checked>
                <label for="standart" class="form-label fw-medium">Standard</label>
            </div>
        </div>
    </div>

    <div class="container col-lg-6 shadow mt-4 p-4 " id="clearanceContainer">
        <div>
            <p class="fs-4 fw-medium">Add Clearance Requirement</p>
        </div>
        <div class="">
            <form action="" >
                <div class="input-group">
                    <label for="clearanceName" class="input-group-text">Department ID </label>
                    <input type="number" name="" id=""  class="form-control">
                </div>
                <div class="input-group pt-3">
                    <label for="clearanceName" class="input-group-text">Office/Department </label>
                    <input type="text" name="" id=""  class="form-control">
                </div>
                <div class="input-group pt-3">
                    <label for="clearanceName" class="input-group-text">Signatory Name </label>
                    <input type="text" name="" id=""     class="form-control">
                </div>
                <div class="pt-3">
                    <button class="btn btn-info">Update Info</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container mt-4 shadow col-lg-6" id="tableContainer">
        <table class="table table-striped">
            <thead>
                <tr class="table-dark">
                    <th>Dept. ID</th>
                    <th>Office/Department</th>
                    <th>Signatory</th>
                    <th>Select User</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>2009078</th>
                    <th>Library</th>
                    <th>Irene M. Mungcal</th>
                    <th><button class="btn btn-outline-info">Select</button></th>   
                </tr>
                <tr>
                    <th>2009078</th>
                    <th>Osa</th>
                    <th>Angelo A. Baltazar</th>
                    <th><button class="btn btn-outline-info">Select</button></th>                
                </tr>
                <tr>
                    <th>2009078</th>
                    <th>Guidance</th>
                    <th>Abigail B. Wong</th>
                    <th><button class="btn btn-outline-info">Select</button></th>
                </tr>
                <tr>
                    <th>2009078</th>
                    <th>Foreign Affairs</th>
                    <th>Imelda C. Stevenson</th>
                    <th><button class="btn btn-outline-info">Select</button></th>                
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
        const standardOption = document.getElementById("standardOption");
        const customizeOption = document.getElementById("customizeOption");
        const clearanceContainer = document.getElementById("clearanceContainer");
        const tableContainer = document.getElementById("tableContainer");
        const selectButtons = document.querySelectorAll("table tbody tr button");

        function toggleVisibility() {
            if (standardOption.checked) {
                clearanceContainer.style.display = "none";
                tableContainer.style.marginTop = "0";
                
                selectButtons.forEach(button => button.style.display = "none");
            } else if (customizeOption.checked) {
                clearanceContainer.style.display = "block";
                tableContainer.style.marginTop = "16px";
               
                selectButtons.forEach(button => button.style.display = "inline-block");
            }
        }

        standardOption.addEventListener("change", toggleVisibility);
        customizeOption.addEventListener("change", toggleVisibility);
        toggleVisibility();
    });



    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>