<?php 
    include 'functions.php';

    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        header("Location: login.php");
        exit();
    }

    $facultyID = $_SESSION['userID'];
    $facultyData = getFacultyData($facultyID);
    
    // Verify this is an employee (not student)
    if (!$facultyData || $facultyData['type'] !== 'Employee') {
        header("Location: unauthorized.php");
        exit();
    }

    $commentAreaValue = '';
    $empID = "";
    $empName = $empDept = "";
    $employeeFound = false;
    $errorMessage = '';

    // Process form submissions
    if (isset($_POST['searchButton'])) {
        $empID = $_POST['empID'];
        $searchResult = processEmployeeSearch($empID, $facultyData);
        
        $empName = $searchResult['empName'];
        $empDept = $searchResult['empDepartment'];
        $commentAreaValue = $searchResult['commentAreaValue'];
        $employeeFound = $searchResult['employeeFound'];
        $errorMessage = $searchResult['errorMessage'];

        if ($employeeFound) {
            $hasRequested = hasEmployeeRequested($empID, $facultyData['dept_name']);
            if (!$hasRequested) {
                $errorMessage = "Employee hasn't requested clearance from this department";
                $employeeFound = false;
            }
        }
    }

    if (isset($_POST['approveButton']) && isset($_POST['empID'])) {
        $empID = $_POST['empID'];
        $deptName = $facultyData['dept_name'];
        $currentDate = date('M d, Y');
        approveEmployee($empID, $deptName);
        approveEmployeeDate($empID, $deptName, $currentDate);
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $empID);
        exit();
    }

    if (isset($_POST['declineButton']) && isset($_POST['empID'])) {
        $empID = $_POST['empID'];
        $deptName = $facultyData['dept_name'];
        $comment = $_POST['commentArea'];
        $currentDate = date('M d, Y');
        approveEmployeeDate($empID, $deptName, $currentDate);
        storeEmployeeCommentAndReset($empID, $deptName, $comment);
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $empID);
        exit();
    }

    // Get pending requests
    $employeeRequests = getPendingEmployeeRequests($facultyData['dept_name']);
    $employeeRequestCount = count($employeeRequests);
    $approvedEmployeesCount = getApprovedEmployeeCount($facultyData['dept_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="styles/faculty-dashboard.css">
    <link rel="icon" href="img/logo.png">
    <title>Employee Clearance</title>
    <style>
        .custom-search-shadow {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .custom-status {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        textarea {
            resize: none;
        }
        .main-content {
            margin-left: 250px;
        }
    </style>
</head>
<body style="background-color: whitesmoke;">
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <div>
            <img src="img/logo.png" alt="" height="80px" class="ps-3">
        </div>
        <div class="head">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white" href="#"><?php echo $facultyData['dept_name'] . " - Clearance"; ?></a>
        </div>
    </header>

    <div class="sidebar">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class="pt-4 d-flex gy-1">
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="fs-6" style="font-weight: 500;"><?php echo $facultyData['employee_name']; ?></p>
                    <p class="position-absolute" style="top: 52px;"><?php echo $facultyData['dept_name'] . ""; ?></p>
                </div>
            </div>
            
            <div class="d-flex pt-5 ps-2">
                <div>
                    <a href="principal-dashboard.php" style="text-decoration: none;" class="text-dark"><i class="fa-solid fa-house" style="font-size: 25px;"></i></a>
                </div>
                <div class="ps-2">
                    <a href="principal-dashboard.php" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium">Dashboard</p></a>
                </div>
            </div>
            
            <div class="pt-3 text-center ps-2">
                <div class="d-flex gy-1">
                    <div>
                        <a href="employee-clearance.php" style="text-decoration: none;" class="text-dark"><i class="fa-solid fa-user-graduate" style="font-size: 25px;"></i></a>
                    </div>
                    <div class="ps-2">
                        <a href="employee-clearance.php" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium">Clearance</p></a>
                    </div>  
                </div>  
            </div>   
            
            <div class="d-flex gy-1 pt-3 ms-2">
                <div>
                    <a href="change-password.php" style="text-decoration: none;" class="text-dark">
                        <i class="fa-solid fa-unlock" style="font-size: 25px;"></i>
                    </a>
                </div>
                <div class="ps-2">
                    <a href="change-password.php" style="text-decoration: none;" class="text-dark">
                        <p class="fs-6 fw-medium">Change Password</p>
                    </a>
                </div>  
            </div>

            <div class="d-flex pt-3 ps-2">
                <div>
                    <a href="principal-dashboard.php" style="text-decoration: none;" class="text-dark"><i class="fa-solid fa-pen-to-square" style="font-size: 25px;"></i></a>
                </div>
                <div class="ps-2">
                    <a href="semester.php" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium">Semester - S.Y.</p></a>
                </div>
            </div>
            
            <!-- Request List -->
            <div class="d-flex gy-1 pt-3 ms-2">
                <div class="position-relative">
                    <a href="request-list.php" style="text-decoration: none;" class="text-dark">
                        <i class="fa-solid fa-list" style="font-size: 25px;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $employeeRequestCount; ?>
                        </span>
                    </a>
                </div>
                <div class="ps-2">
                    <a href="request-list.php" style="text-decoration: none;" class="text-dark">
                        <p class="fs-6 fw-medium mb-0">Request List</p>
                    </a>
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
        
    <div class="main-content">
        <div class="container text-center pt-4">
            <p class="fs-1 fw-bold">Employee Clearance</p>
        </div>

        <form method="post" id="clearanceForm">
            <div class="container text-center mt-5 custom-search-shadow position-relative z-1 bg-light" style="width: 700px;">
                <div class="d-flex justify-content-center align-items-center py-4">
                    <div>
                        <label for="empID" class="form-label fs-5">Enter Employee ID:</label>
                    </div>
                    <div class="ps-4" style="width: 300px;">
                        <?php $preFillID = isset($_GET['id']) ? $_GET['id'] : ''; ?>
                        <input type="number" step="1" name="empID" placeholder="Employee ID" class="form-control" 
                            value="<?php echo !empty($preFillID) ? htmlspecialchars($preFillID) : $empID; ?>" required>
                    </div>
                    <div class="ps-4">
                        <button class="btn btn-info fs-5" name="searchButton">Search</button>
                    </div>
                </div>
            </div>

            <div class="container pt-4 col-lg-6">
                <table class="table table-striped col-lg-12">
                    <thead>
                        <tr class="table-dark">
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Department</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($employeeFound && isset($_POST['searchButton'])): ?>
                            <tr>
                                <th><?php echo $empID ?></th>
                                <th><?php echo $empName ?></th>
                                <th><?php echo $empDept ?></th>
                            </tr>
                        <?php elseif (isset($_POST['searchButton']) && !$employeeFound): ?>
                            <tr>
                                <td colspan="3" class="text-center">
                                    <?php echo $errorMessage; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="container d-flex justify-content-center align-items-center pt-5 mt-5 custom-status pb-4" style="width: 1000px;">
                <div class="col-lg-8 text-center d-flex flex-column justify-content-center align-items-center">
                    <label for="commentArea" class="form-label fs-6 fw-medium">Comment for declined employee:</label>
                    <textarea class="form-control align-center" style="width: 400px;" 
                        name="commentArea" id="commentArea" rows="3" 
                        onclick="this.setSelectionRange(0, 0)" 
                        oninput="checkTextareaContent()" 
                        <?php echo !$employeeFound ? 'disabled' : ''; ?>><?php echo htmlspecialchars($commentAreaValue); ?></textarea>                
                    <div class="pt-4">       
                        <button class="btn btn-danger fs-5" name="declineButton" <?php echo $employeeFound ? '' : 'disabled'; ?>>Decline</button>      
                    </div>   
                </div>
                <div class="col-lg-4">
                    <button class="btn btn-success fs-5" name="approveButton" id="approveButton" 
                        <?php echo (!$employeeFound || !empty($commentAreaValue)) ? 'disabled' : ''; ?>>Approve</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function checkTextareaContent() {
            var commentArea = document.getElementById('commentArea');
            var approveButton = document.getElementById('approveButton');
            if (commentArea.value.trim() !== '') {
                approveButton.disabled = true;
            } else {
                approveButton.disabled = false;
            }
        }

        // Auto-focus the comment area if it has content
        document.addEventListener("DOMContentLoaded", function() {
            var commentArea = document.getElementById('commentArea');
            if (commentArea.value.trim() !== '') {
                commentArea.focus();
                commentArea.setSelectionRange(commentArea.value.length, commentArea.value.length);
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>