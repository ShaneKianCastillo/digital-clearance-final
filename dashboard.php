<?php 
    include 'functions.php'; 

    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Faculty';
    $facultyID = $_SESSION['userID'];
    $facultyData = getFacultyData($facultyID);

    $commentAreaValue = '';

    $userType = 'Student'; // Default value

    if ($facultyData['type'] == 'Employee') {
        $userType = 'Employee';
    } elseif ($facultyData['type'] == 'Both') {
        $userType = isset($_POST['userClearance']) ? $_POST['userClearance'] : 'Student';
    }

    // Initialize variables for both student and employee
    $studID = $empID = "";
    $studName = $empName = $studCourse = $empDept = "";
    $studentFound = $employeeFound = false;
    $errorMessage = '';
    $currentDate = '';

    // Process form submissions based on selected user type
    if ($userType == 'Student' && ($facultyData['type'] == 'Student' || $facultyData['type'] == 'Both')) {
        if (isset($_POST['searchButton'])) {
            $studID = $_POST['userID'];
            $searchResult = processStudentSearch($studID, $facultyData);
            
            $studName = $searchResult['studName'];
            $studCourse = $searchResult['studCourse'];
            $commentAreaValue = $searchResult['commentAreaValue'];
            $studentFound = $searchResult['studentFound'];
            $errorMessage = $searchResult['errorMessage'];

            if ($studentFound) {
                $hasRequested = hasStudentRequested($studID, $facultyData['dept_name']);
                if (!$hasRequested) {
                    $errorMessage = "Student hasn't requested clearance from this department";
                    $studentFound = false;
                }
            }
        }

        if (isset($_POST['approveButton']) && isset($_POST['userID'])) {
            $studID = $_POST['userID'];
            $deptName = $facultyData['dept_name'];
            $currentDate = date('M d, Y');
            approveStudent($studID, $deptName);
            approveDate($studID, $deptName, $currentDate);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        if (isset($_POST['declineButton']) && isset($_POST['userID'])) {
            $studID = $_POST['userID'];
            $deptName = $facultyData['dept_name'];
            $comment = $_POST['commentArea'];
            $currentDate = date('M d, Y');
            approveDate($studID, $deptName, $currentDate);
            storeCommentAndReset($studID, $deptName, $comment);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } 
    elseif ($userType == 'Employee' && ($facultyData['type'] == 'Employee' || $facultyData['type'] == 'Both')) {
        if (isset($_POST['searchButton'])) {
            $empID = $_POST['userID'];
            $searchResult = processEmployeeSearch($empID, $facultyData);

            if ($searchResult) {
                $empName = $searchResult['empName'];
                $empDept = $searchResult['empDepartment'];
                $employeeFound = $searchResult['employeeFound'];
                $errorMessage = $searchResult['errorMessage'];
                $commentAreaValue = $searchResult['commentAreaValue'] ?? '';

                if ($employeeFound) {
                    $hasRequested = hasEmployeeRequested($empID, $facultyData['dept_name']);
                    if (!$hasRequested) {
                        $errorMessage = "Employee hasn't requested clearance from this department";
                        $employeeFound = false;
                    }
                }
            }       
        }

        if (isset($_POST['approveButtonEmployee']) && isset($_POST['userID'])) {
            $empID = $_POST['userID'];
            $deptName = $facultyData['dept_name'];
            $currentDate = date('M d, Y');
            approveEmployee($empID, $deptName);
            approveEmployeeDate($empID, $deptName, $currentDate);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    
        if (isset($_POST['declineButton']) && isset($_POST['userID'])) {
            $empID = $_POST['userID'];
            $deptName = $facultyData['dept_name'];
            $comment = $_POST['commentArea'];
            $currentDate = date('M d, Y');
            approveEmployeeDate($empID, $deptName, $currentDate);
            storeEmployeeCommentAndReset($empID, $deptName, $comment);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    $deptName = $facultyData['dept_name'];
    $facultyType = $facultyData['type'];

    $studentRequests = [];
    $studentRequestCount = 0;
    $employeeRequests = [];
    $employeeRequestCount = 0;
    $approvedStudentsCount = 0;
    $approvedEmployeesCount = 0;

    if ($facultyType === 'Student' || $facultyType === 'Both') {
        $studentRequests = getPendingStudentRequests($deptName);
        $studentRequestCount = count($studentRequests);
        $approvedStudentsCount = getApprovedStudentCount($deptName);
    }

    if ($facultyType === 'Employee' || $facultyType === 'Both') {
        $employeeRequests = getPendingEmployeeRequests($deptName);
        $employeeRequestCount = count($employeeRequests);
        $approvedEmployeesCount = getApprovedEmployeeCount($deptName);
    }
         
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
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
            <?php echo $facultyData['dept_name'] . " - Dashboard"; ?>
        </a>
    </div>
</header>

<!-- Sidebar -->
<div class="sidebar pt-5">
    <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
        <div class="d-flex gy-1">
            <div>
                <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
            </div>
            <div class="ps-3">
                <p class="s fw-semibold"><?php echo $facultyData['employee_name']; ?></p>
                <p class="position-absolute" style="top: 35px;"><?php echo $facultyData['dept_name'] . " Employee"; ?></p>
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
                        5
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
                    <a href="dashboard.php" class="text-decoration-none text-dark fs-6 fw-medium">Dashboard</a>
                </div>
            </div> 

            <div class="pt-4 ms-2 d-flex align-items-center">
                <div>
                    <i class="fa-solid fa-user-graduate " style="font-size: 25px;"></i>
                </div>
                <div class="ps-3">
                    <a href="faculty-dashboard.php" class="text-decoration-none text-dark fs-6 fw-medium">Clearance</a>
                </div>
            </div>

        <div id="accountingLinks"> 
            
            <div class="pt-4 ms-2 d-flex align-items-center">
                <div class="fs-5">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <div class="ps-3">
                    <a href="generate-pdf.php" class="text-decoration-none text-dark fs-6 fw-medium">Print PDF File</a>
                </div>
            </div>
        </div>
        <br>
        <hr>
        <div class="ps-3 pt-3">
            <a href="logout.php" class="text-danger text-decoration-none">               
                <i class="fa-solid fa-right-from-bracket">Logout</i>              
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content pt-5">
    <div class="container pt-5">
        <div class="row g-4">

        <?php if ($facultyType === 'Student' || $facultyType === 'Both'): ?>
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
        <?php endif; ?>

        <?php if ($facultyType === 'Employee' || $facultyType === 'Both'): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card p-3 bg-info text-white">
                    <h5>Employee Requests</h5>
                    <h3><?php echo $employeeRequestCount ?? 0; ?></h3>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card p-3 bg-success text-white">
                    <h5>Approved Employees</h5>
                    <h3><?php echo $approvedEmployeesCount ?? 0; ?></h3>
                </div>
            </div>
        <?php endif; ?>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var departmentName = "<?php echo $facultyData['dept_name']; ?>";
        var sidebarLinks = document.getElementById("accountingLinks");
        if (departmentName !== "Accounting") {
            sidebarLinks.style.display = "none"; // Hide if not Accounting
        } else {
            sidebarLinks.style.display = "block"; // Show if Accounting
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
