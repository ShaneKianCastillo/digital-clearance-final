<?php 
    include 'functions.php'; 

    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Faculty';
    $facultyID = $_SESSION['userID'];
    $facultyData = getFacultyData($facultyID);

    $commentAreaValue = '';

    // Determine which UI to show based on radio button selection or default
    $userType = isset($_POST['userClearance']) ? $_POST['userClearance'] : 'Student';

    // Initialize variables for both student and employee
    $studID = $empID = "";
    $studName = $empName = $studCourse = $empDept = "";
    $studentFound = $employeeFound = false;
    $errorMessage = '';

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
            storeCommentAndReset($studID, $deptName, $comment);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } 
    elseif ($userType == 'Faculty' && ($facultyData['type'] == 'Employee' || $facultyData['type'] == 'Both')) {
        if (isset($_POST['searchButton'])) {
            $empID = $_POST['userID'];
            $searchResult = processEmployeeSearch($empID);

            $empName = $searchResult['empName'];
            $empDept = $searchResult['empDepartment'];
            $empPosition = $searchResult['empPosition'];
            $empCategory = $searchResult['empCategory'];
            $empStatus = $searchResult['empStatus'];
            $employeeFound = $searchResult['employeeFound'];
            $errorMessage = $searchResult['errorMessage'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/faculty-dashboard.css">
    <link rel="icon" href="img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>Faculty Dashboard</title>
</head>

<body style="background-color: whitesmoke;">
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <div>
            <img src="img/logo.png" alt="" height="80px" class="ps-3">
        </div>
        <div class="head">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white " href="#"><?php echo $facultyData['dept_name'] . " - Dashboard"; ?></a>
        </div>
    </header>

    <div class="sidebar pt-5">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class=" d-flex gy-1 " >
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="fs-5" style="font-weight: 500;"><?php echo $facultyData['employee_name']; ?></p>
                    <p class="position-absolute" style="top: 35px;"><?php echo $facultyData['dept_name'] . " Employee"; ?></p>
                </div>
            </div>
            <div id="accountingLinks"> 
                <div class="pt-4 ms-2 d-flex align-items-center">
                    <div class="fs-2 ">
                        <i class="fa-solid fa-house"></i>
                    </div>
                    <div class="ps-3 fs-5">
                        <a href="generate-pdf.php" class="text-decoration-none text-dark">Dashboard</a>
                    </div>
                </div>
                <div class="pt-4 ms-2 d-flex align-items-center">
                    <div class="fs-2 ">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div class="ps-3 fs-5">
                        <a href="generate-pdf.php" class="text-decoration-none text-dark">Print PDF File</a>
                    </div>
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
    
    <div class="container text-center pt-4">
        <p class="fs-1 fw-bold"><?php echo "Welcome " . $facultyData['dept_name'] . " Employee!"; ?></p>
    </div>

    <form method="post">
        <div class="container text-center mt-5 custom-search-shadow position-relative z-1 bg-light" style="width: 700px;">
            <div class="d-flex justify-content-center align-items-center py-4">
                <div>
                    <label for="userID" class="form-label fs-5">
                        <?php echo ($userType == 'Student') ? 'Enter Student ID:' : 'Enter Employee ID:'; ?>
                    </label>
                </div>
                <div class="ps-4" style="width: 300px;">
                    <input type="number" step="1" name="userID" placeholder="" class="form-control" 
                        value="<?php echo ($userType == 'Student') ? $studID : $empID; ?>" required>
                </div>
                <div class="ps-4">
                    <button class="btn btn-info fs-5" name="searchButton">Search</button>
                </div>
            </div>
        </div>
        
        <!-- Radio Button Selection -->
        <div class="container border border-1 text-center d-flex align-items-center justify-content-center shadow p-3" style="gap: 20px; width: 300px">
            <div>
                <input type="radio" name="userClearance" id="studentRadio" class="form-check-input" 
                    value="Student" <?php echo ($userType == 'Student') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="studentRadio">Student</label>
            </div>
            <div>
                <input type="radio" name="userClearance" id="facultyRadio" class="form-check-input" 
                    value="Faculty" <?php echo ($userType == 'Faculty') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="facultyRadio">Faculty</label>
            </div>
        </div>

        <?php if ($userType == 'Student' && ($facultyData['type'] == 'Student' || $facultyData['type'] == 'Both')): ?>
            <!-- Student UI -->
            <div class="container pt-4 col-lg-6">
                <table class="table table-striped col-lg-12">
                    <thead>
                        <tr class="table-dark">
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($studentFound && isset($_POST['searchButton'])): ?>
                            <tr>
                                <th><?php echo $studID ?></th>
                                <th><?php echo $studName ?></th>
                                <th><?php echo $studCourse ?></th>
                            </tr>
                        <?php elseif (isset($_POST['searchButton']) && !$studentFound): ?>
                            <tr>
                                <td colspan="3" class="text-center">
                                    <?php 
                                        if (isset($student) && !empty($student['stud_name'])) {
                                            echo htmlspecialchars($student['stud_name']) . " is not yet approved by previous departments.";
                                        } else {
                                            echo "No student found with ID: " . htmlspecialchars($studID);
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="container d-flex justify-content-center align-items-center pt-5 mt-5 custom-status pb-4" style="width: 1000px;">
                <div class="col-lg-8 text-center d-flex flex-column justify-content-center align-items-center">
                    <label for="commentArea" class="form-label fs-6 fw-medium">Comment for declined student:</label>
                    <textarea 
                        class="form-control align-center" 
                        style="width: 400px;" 
                        name="commentArea" 
                        id="commentArea" 
                        rows="3" 
                        onclick="this.setSelectionRange(0, 0)" 
                        oninput="checkTextareaContent()" 
                        <?php echo !$studentFound ? 'disabled' : ''; ?>>
                        <?php echo htmlspecialchars($commentAreaValue); ?>
                    </textarea>                
                <div class="pt-4">       
                    <button class="btn btn-danger fs-5" name="declineButton" <?php echo $studentFound ? '' : 'disabled'; ?>>Decline</button>      
                </div>   
                </div>
                <div class="col-lg-4">
                    <button 
                        class="btn btn-success fs-5" 
                        name="approveButton" 
                        id="approveButton" 
                        <?php echo (!$studentFound || !empty($commentAreaValue)) ? 'disabled' : ''; ?>>Approve
                    </button>
                </div>
            </div>
        <?php elseif ($userType == 'Faculty' && ($facultyData['type'] == 'Employee' || $facultyData['type'] == 'Both')): ?>
            <!-- Faculty/Employee UI -->
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
                                    <?php 
                                        if (isset($employee) && !empty($employee['name'])) {
                                            echo htmlspecialchars($employee['name']) . " is not yet approved by previous departments.";
                                        } else {
                                            echo "No employee found with ID: " . htmlspecialchars($empID);
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="container d-flex justify-content-center align-items-center pt-5 mt-5 custom-status pb-4" style="width: 1000px;">
                <div class="col-lg-8 text-center d-flex flex-column justify-content-center align-items-center">
                    <label for="commentArea" class="form-label fs-6 fw-medium">Comment for declined employee:</label>
                    <textarea 
                        class="form-control align-center" 
                        style="width: 400px;" 
                        name="commentArea" 
                        id="commentArea" 
                        rows="3" 
                        onclick="this.setSelectionRange(0, 0)" 
                        oninput="checkTextareaContent()" 
                        <?php echo !$employeeFound ? 'disabled' : ''; ?>>
                        <?php echo htmlspecialchars($commentAreaValue); ?>
                    </textarea>                
                <div class="pt-4">       
                    <button class="btn btn-danger fs-5" name="declineButton" <?php echo $employeeFound ? '' : 'disabled'; ?>>Decline</button>      
                </div>   
                </div>
                <div class="col-lg-4">
                    <button 
                        class="btn btn-success fs-5" 
                        name="approveButton" 
                        id="approveButton" 
                        <?php echo (!$employeeFound || !empty($commentAreaValue)) ? 'disabled' : ''; ?>>Approve
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </form>
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