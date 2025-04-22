<?php 
    include 'functions.php';

    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Faculty';
    $principalID = $_SESSION['userID'];
    $principalData = getFacultyData($principalID);
    $departments = fetchDepartments();

    $deptID = "";
    $deptName = "";
    $deptEmp = "";

    $selectedOption = isset($_POST['option']) ? $_POST['option'] : 'standard';

    if (isset($_POST['selectButton'])) {
        $selectedDeptID = $_POST['deptID'];
        $selectedOption = "customize";
        foreach ($departments as $department) {
            if ($department['dept_id'] === $selectedDeptID) {
                $deptID = $department['dept_id'];
                $deptName = $department['dept_name'];
                $deptEmp = $department['employee_name'];
                break;
            }
        }
    }

    if (isset($_POST['updateInfoButton'])) {
        $deptID = $_POST['deptID'];
        $deptName = $_POST['deptName'];   
        $deptEmp = $_POST['deptEmp'];    
        updateDepartment($deptID, $deptName, $deptEmp);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }    

    $deptName = 'Principal'; 
    $employeeRequests = [];
    $employeeRequestCount = 0;
    $employeeRequests = getPendingEmployeeRequests($deptName);
    $employeeRequestCount = count($employeeRequests);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/principal-dashboard.css">
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://kit.fontawesome.com/a082745512.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Principal Dashboard</title>
</head>
<body>
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <div>
            <img src="img/logo.png" alt="" height="80px" class="ps-3">
        </div>
        <div class="head">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white " href="#">Principal - Dashboard</a>
        </div>
    </header>
    <div class="sidebar position-relative">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class="pt-4 d-flex gy-1 " >
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="fs-6" style="font-weight: 500;"><?php echo $principalData['employee_name']; ?></p>
                    <p class="position-absolute" style="top: 52px;">Principal</p>
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
                        <a href="principal-clearance.php" style="text-decoration: none;" class="text-dark"><i class="fa-solid fa-user-graduate " style="font-size: 25px;"></i></a>
                    </div>
                    <div class="ps-2">
                        <a href="principal-clearance.php" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium"> Clearance</p></a>
                    </div>  
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
                <!-- Request List -->
                <div class="d-flex gy-1 pt-3 ms-2">
                    <div class="position-relative">
                        <a href="request-list.php" style="text-decoration: none;" class="text-dark">
                            <i class="fa-solid fa-list" style="font-size: 25px;"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $employeeRequestCount ?>
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
    </div>
    <div class="container text-center mt-5">
        <p class="fs-1 fw-bold">
            Welcome Principal!
        </p>
    </div>
    <div class="container col-lg-2 mt-4 p-3 shadow-sm bg-body-tertiary rounded">
        <div>
            <div>
                <input type="radio" name="option" class="form-check-input" id="customizeOption" value="customize" 
                <?php echo ($selectedOption === 'customize') ? 'checked' : ''; ?>>
                <label for="customize" class="form-label fw-medium">Customize</label>
            </div>
            <div>
                <input type="radio" class="form-check-input" name="option" id="standardOption" value="standard" 
                <?php echo ($selectedOption === 'standard') ? 'checked' : ''; ?>>
                <label for="standard" class="form-label fw-medium">Standard</label>
            </div>
        </div>
    </div>

    <div class="container col-lg-6 shadow mt-4 p-4 " id="clearanceContainer">
        <div>
            <p class="fs-4 fw-medium">Update Department Information</p>
        </div>
        <div class="">
            <form method="post" >
                <div class="input-group">
                    <label for="clearanceName" class="input-group-text">Department ID </label>
                    <input type="text" name="deptID" class="form-control" value="<?php echo htmlspecialchars($deptID); ?>" readonly>
                </div>
                <div class="input-group pt-3">
                    <label for="clearanceName" class="input-group-text">Office/Department </label>
                    <input type="text" name="deptName" class="form-control" value="<?php echo htmlspecialchars($deptName); ?>">
                </div>
                <div class="input-group pt-3">
                    <label for="clearanceName" class="input-group-text">Signatory Name </label>
                    <input type="text" name="deptEmp" class="form-control" value="<?php echo htmlspecialchars($deptEmp); ?>">
                </div>
                <div class="pt-3">
                    <button class="btn btn-info" name="updateInfoButton" <?php if ($deptID == "") echo 'disabled'; ?>>Update Info</button>
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
                <?php if (!empty($departments)): ?>        
                    <?php  $maxDisplay = 9;  
                    $maxSelectable = 5; 
                    $displayCount = 0;

                    foreach ($departments as $department): 
                        if ($displayCount >= $maxDisplay) break; ?>
                        <tr>
                            <form method="post">
                                <th><?php echo htmlspecialchars($department['dept_id']); ?></th>
                                <th><?php echo htmlspecialchars($department['dept_name']); ?></th>
                                <th><?php echo htmlspecialchars($department['employee_name']); ?></th>
                                <th>
                                    <input type="hidden" name="option" value="<?php echo htmlspecialchars($selectedOption); ?>">
                                    <input type="hidden" name="deptID" value="<?php echo htmlspecialchars($department['dept_id']); ?>">
                                    
                                    <?php if ($displayCount < $maxSelectable): ?>
                                        <button class="btn btn-outline-info" name="selectButton">Select</button>
                                    <?php endif; ?>
                                </th>
                            </form>
                        </tr>
                        <?php $displayCount++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No departments found.</td>
                    </tr>
                <?php endif; ?>
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