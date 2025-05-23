<?php 
    include 'functions.php'; 
    
    $userID = $_SESSION['userID'];
    $role = $_SESSION['role']; 

    if ($role === 'student') {
        $userData = getStudentData($userID);
        $studentInfo = fetchStudentInfo($userID);
        $approvalCount = countApprovals($userID);
        
        // Handle student request submission
        if (isset($_POST['request_dept']) && isset($_POST['requestButton'])) {
            $deptName = $_POST['request_dept'];
            requestClearanceStudent($userID, $deptName);

            // Set session to trigger Swal alert
            $_SESSION['clearance_requested'] = true;

            // Redirect to refresh the page (prevents form resubmission issues)
            header("Location: student-dashboard.php");
            exit();
        }
    } elseif ($role === 'employee') {
        $userData = getEmployeeData($userID);
        $employeeInfo = fetchEmployeeInfo($userID);
        
        // Handle employee request submission
        if (isset($_POST['request_dept']) && isset($_POST['requestButton'])) {
            $deptName = $_POST['request_dept'];
            if (requestClearanceEmployee($userID, $deptName)) {
                // Set session to trigger Swal alert
                $_SESSION['clearance_requested'] = true;

                // Redirect to refresh the page (prevents form resubmission issues)
                header("Location: student-dashboard.php");
                exit();
            }
        }
    }

    // Handle signatory changes
    if ($role === 'employee') {
        $success = false;
        
        // Handle removals
        if (isset($_POST['removed_departments'])) {
            $removedDepartments = json_decode($_POST['removed_departments'], true);
            if (is_array($removedDepartments)) {
                $success = updateEmployeeSignatories($userID, $removedDepartments, true);
            }
        }
        
        // Handle additions
        if (isset($_POST['added_departments'])) {
            $addedDepartments = json_decode($_POST['added_departments'], true);
            if (is_array($addedDepartments)) {
                $success = updateEmployeeSignatories($userID, $addedDepartments, false); // false = adding back
            }
        }
        
        if ($success) {
            $_SESSION['signatories_updated'] = true;
            header("Location: student-dashboard.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/student-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title><?php echo ucfirst($role) ?> Dashboard</title>
    <style>
        /* Add these styles to your student-dashboard.css */
        @media (max-width: 768px) {
            /* Fix header */
            header.navbar {
                width: 100vw;
                padding: 0 15px;
                margin-left: 0;
            }
            
            /* Center and adjust main containers */
            .custom-container {
                right: 0 !important;
                margin: 15px auto !important;
                width: 95% !important;
                padding: 15px !important;
                position: relative !important;
            }
            
            /* Student image adjustments */
            .student-img {
                position: static !important;
                display: block;
                margin: 0 auto 15px !important;
                height: 120px;
                width: auto;
                display: none;
            }
            
            /* Prevent horizontal scroll */
            html, body {
                overflow-x: hidden;
                width: 100%;
            }
            
            /* Table container adjustments */
            .container.custom-container.d-flex {
                padding: 0 !important;
                margin: 15px auto !important;
                width: 100% !important;
            }
            
            /* Sidebar mobile view */
            .sidebar-container .container {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar-container .container.show {
                transform: translateX(0);
                display: block !important;
                width: 70% !important;
            }
            
            /* Stack columns on mobile */
            .row {
                flex-direction: column;
            }
            
            /* Adjust table font size */
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 8px 4px !important;
                white-space: nowrap;
            }
        }
        
        /* Make table responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .request-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .request-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .request-btn[title]:hover:after {
            content: attr(title);
            position: absolute;
            background: #333;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            z-index: 100;
            white-space: nowrap;
            margin-top: -35px;
            margin-left: -10px;
        }
    </style>
</head>

<body style="background-color: whitesmoke;">
<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow w-100" data-bs-theme="dark">
    <!-- Burger Menu for Mobile View -->
    <button id="menu-toggle" class="btn btn-dark ms-3 d-md-none">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div>
        <img src="img/logo.png" alt="" height="80px" class="ps-3">
    </div>
    <div class="head">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white" href="#"><?php echo ucfirst($role) ?> - Dashboard</a>
    </div>
</header>

<!-- Sidebar -->
<div class="sidebar-container">
    <div id="sidebar" class="sidebar position-relative">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light d-none d-md-block" 
             style="height: 100vh; width:250px;">
            <div class="pt-4 d-flex gy-1">
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="fs-6 fw-medium">
                        <?php echo $role === 'student' ? $userData['name'] : $userData['name']; ?>
                    </p>
                    <p class="position-absolute" style="top: 52px;">
                        <?php echo $role === 'student' ? 'CCIS - Student' : $userData['department'] . ' Employee'; ?>
                    </p>
                </div>
            </div>  
            <div class="pt-5 text-center ps-2">
                <div class="d-flex gy-1">
                    <div>
                        <a href="change-password.php" style="text-decoration: none;" class="text-dark">
                            <i class="fa-solid fa-unlock" style="font-size: 20px;"></i>
                        </a>
                    </div>
                    <div class="ps-2">
                        <a href="change-password.php" style="text-decoration: none;" class="text-dark">
                            <p class="fs-6 fw-medium">Change Password</p>
                        </a>
                    </div>  
                </div>  
            </div> 
            
            <div class="pt-3 ps-2" id="manageSignatories">
        <div class="d-flex gy-1">
            <div>
                <a href="#" data-bs-toggle="modal" data-bs-target="#manageSignatoriesModal" style="text-decoration: none;" class="text-dark">
                    <i class="fa-solid fa-pen-to-square" style="font-size: 20px;"></i>
                </a>
            </div>
            <div id="">
                <div class="ps-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#manageSignatoriesModal" style="text-decoration: none;" class="text-dark">
                        <p class="fs-6 fw-medium">Manage Signatories</p>
                    </a>
                </div>
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

<?php if ($role === 'student'): ?>
    <div class="container custom-container pt-3 mt-4 custom-shadow position-relative col-lg-8 col-md-10 col-sm-12">
    <p class="fw-bold" style="font-size: 30px;">Student Information</p>
    <div class="row">
        <!-- First Column -->
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Name:</p>
                <p class="mb-0"><?php echo $studentInfo['stud_name']; ?></p>
            </div>
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Course:</p>
                <p class="mb-0"><?php echo $studentInfo['course']; ?></p>
            </div>
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Contact Number:</p>
                <p class="mb-0"><?php echo $studentInfo['contact_number']; ?></p>
            </div>
        </div>
        <!-- Second Column -->
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Student Number:</p>
                <p class="mb-0"><?php echo $studentInfo['stud_id']; ?></p>
            </div>
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Year Level:</p>
                <p class="mb-0"><?php echo $studentInfo['year_level']; ?></p>
            </div>
            <div class="d-flex align-items-center gap-2 pb-5">
                <p class="fw-bold mb-0">1st Semester:</p>
                <p class="mb-0">S.Y. 2024 - 2025</p>
            </div>
        </div>
    </div>
    <!-- Student Image -->
    <div class="position-absolute end-0 me-5 top-0 mt-4">
        <img src="img/user-stud.png" alt="Student Image" class="img-fluid student-img">
    </div>
</div>

<!-- Clearance Status Table -->
<div class="container custom-container d-flex justify-content-center mt-5 col-lg-8 text-center">
    <?php $clearanceData = getStudentClearanceData($userID); ?>
    <div class="table">
        <table class="table table-striped">
            <thead>
                <tr class="table-dark">
                    <th>Office/Dept</th>
                    <th>Signatory Name</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Define the desired department order
                $desiredOrder = [
                    'Library',
                    'OSA',
                    'Guidance',
                    // Remove 'Foreign Affairs' from the order for foreign students
                    'Computer Lab',
                    'Program Chair',
                    'Dean',
                    'Registrar',
                    'Vice President',
                    'Accounting'
                ];
                
                // If student is not foreign, include Foreign Affairs in the order
                if (!isset($studentInfo['foreigner']) || $studentInfo['foreigner'] != 1) {
                    array_splice($desiredOrder, 3, 0, 'Foreign Affairs');
                }
                
                // Reorder the clearanceData array according to the modified order
                $orderedData = [];
                foreach ($desiredOrder as $dept) {
                    foreach ($clearanceData as $data) {
                        if ($data['dept_name'] === $dept) {
                            $orderedData[] = $data;
                            break;
                        }
                    }
                }
                
                // Display the reordered data
                foreach ($orderedData as $data): 
                    // Skip Foreign Affairs for foreign students
                    if (isset($studentInfo['foreigner']) && $studentInfo['foreigner'] == 1 && $data['dept_name'] == 'Foreign Affairs') {
                        continue;
                    }
                    
                    $isDisabled = shouldDisableStudentButton($userID, $data['dept_name'], $data['status']);
                    $tooltip = '';
                    
                    if ($data['status'] == 'Approved') {
                        $tooltip = 'title="Already approved"';
                    } else if ($isDisabled) {
                        // Get department order (using the new desired order)
                        $deptOrder = $desiredOrder;
                        $currentPos = array_search($data['dept_name'], $deptOrder);
                        
                        if ($currentPos > 0) {
                            $prevDept = $deptOrder[$currentPos - 1];
                            $tooltip = 'title="Requires approval from '.$prevDept.' first"';
                        } else if ($data['dept_name'] != 'Library') {
                            $tooltip = 'title="Please complete previous departments first"';
                        }
                    }
                ?>
                    <tr>
                        <th><?php echo htmlspecialchars($data['dept_name']); ?></th>
                        <th><?php echo htmlspecialchars($data['signatory']); ?></th>
                        <th class="<?php echo $data['status'] == 'Approved' ? 'text-success' : 'text-danger'; ?>">
                            <?php echo htmlspecialchars($data['status']); ?>
                        </th>
                        <th><?php echo htmlspecialchars($data['date']); ?></th>
                        <th><?php echo htmlspecialchars($data['remarks']); ?></th>
                        <th>
                            <form class="request-form" method="POST">
                                <input type="hidden" name="request_dept" value="<?php echo htmlspecialchars($data['dept_name']); ?>">
                                <button type="submit" name="requestButton" 
                                    class="btn btn-info request-btn" 
                                    <?php echo $isDisabled ? 'disabled' : ''; ?>
                                    <?php echo $tooltip; ?>>
                                    <?php echo $data['status'] == 'Approved' ? 'Approved' : 'Request'; ?>
                                </button>
                            </form>
                        </th>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($role === 'employee'): ?>
    <div class="container custom-container pt-3 mt-4 custom-shadow position-relative col-lg-8 col-md-10 col-sm-12">
    <p class="fw-bold" style="font-size: 30px;">Employee Information</p>
    <div class="row">
        <!-- First Column -->
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Name:</p>
                <p class="mb-0"><?php echo $employeeInfo['name']; ?></p>
            </div>
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Position:</p>
                <p class="mb-0"><?php echo $employeeInfo['position']; ?></p>
            </div>
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Category:</p>
                <p class="mb-0"><?php echo $employeeInfo['category']; ?></p>
            </div>
        </div>
        <!-- Second Column -->
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Employee Number:</p>
                <p class="mb-0"><?php echo $employeeInfo['emp_id']; ?></p>
            </div>
            <div class="d-flex align-items-center gap-2 pb-3">
                <p class="fw-bold mb-0">Department:</p>
                <p class="mb-0"><?php echo $employeeInfo['department']; ?></p>
            </div>
        </div>
    </div>
    <!-- Student Image -->
    <div class="position-absolute end-0 me-5 top-0 mt-4">
        <img src="img/user-stud.png" alt="Student Image" class="img-fluid student-img">
    </div>
</div>

<!-- Clearance Status Table -->
<div class="container custom-container d-flex justify-content-center mt-5 col-lg-8 text-center">
    <?php $clearanceData = getEmployeeClearanceData($userID);  ?>
    <div class="table">
    <table class="table table-striped">
        <thead>
            <tr class="table-dark">
                <th>Office/Dept</th>
                <th>Signatory Name</th>
                <th>Status</th>
                <th>Date</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clearanceData as $data): 
                $isDisabled = shouldDisableEmployeeButton($userID, $data['dept_name'], $data['status']);
                $tooltip = '';
                
                if ($data['status'] == 'Approved') {
                    $tooltip = 'title="Already approved"';
                } else if ($isDisabled) {
                    // Get department order
                    $deptOrder = getEmployeeDepartmentOrder();
                    $currentPos = array_search($data['dept_name'], $deptOrder);
                    
                    if ($currentPos > 0) {
                        $prevDept = $deptOrder[$currentPos - 1];
                        $tooltip = 'title="Requires approval from '.$prevDept.' first"';
                    }
                }
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($data['dept_name']); ?></td>
                    <td><?php echo htmlspecialchars($data['signatory']); ?></td>
                    <td class="<?php 
                        if ($data['status'] == 'Approved') {
                            echo 'text-success';
                        } elseif ($data['status'] == 'Declined') {
                            echo 'text-danger';
                        } elseif ($data['status'] == 'Removed') {
                            echo 'text-secondary';
                        } else {
                            echo 'text-secondary';
                        }
                    ?>">
                        <?php echo htmlspecialchars($data['status']); ?>
                    </td>

                    <td><?php echo htmlspecialchars($data['date']); ?></td>
                    <td><?php echo htmlspecialchars($data['remarks']); ?></td>
                    <th>
                        <form class="request-form" method="POST">
                            <input type="hidden" name="request_dept" value="<?php echo htmlspecialchars($data['dept_name']); ?>">
                            <button type="submit" name="requestButton" 
                                class="btn btn-info request-btn" 
                                <?php echo $isDisabled ? 'disabled' : ''; ?>
                                <?php echo $tooltip; ?>>
                                <?php echo $data['status'] == 'Approved' ? 'Approved' : 'Request'; ?>
                            </button>
                        </form>
                    </th>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.querySelector(".sidebar .container");
        const toggleButton = document.getElementById("menu-toggle");

        toggleButton.addEventListener("click", function (e) {
            e.stopPropagation();
            sidebar.classList.toggle("show");
        });

        document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && event.target !== toggleButton) {
                sidebar.classList.remove("show");
            }
        });

        <?php if ($role === 'student'): ?>
        // Progress animation for students
        let counter = 0;
        let targetValue = <?php echo ($approvalCount * 10) ?>;
        let interval = setInterval(() => {
            if (counter >= targetValue) {
                clearInterval(interval);
            } else {
                counter += 1;
                document.getElementById("progress-percent").innerHTML = counter + "%";
                let dashOffset = 472 - (472 * counter) / 100;
                document.getElementById("progress-circle").style.strokeDashoffset = dashOffset;
            }
        }, 30);
        <?php endif; ?>
    });
</script>

<script>
    <?php if (isset($_SESSION['clearance_requested'])): ?>
        Swal.fire({
            position: "center",
            icon: "success",
            title: "Your request has been sent!",
            showConfirmButton: false,
            timer: 1500
        });
        <?php unset($_SESSION['clearance_requested']); ?> // Remove session variable after showing alert
    <?php endif; ?>
</script>



<!-- Add this style in the head section -->
<style>
    .btn-add {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .btn-add.active {
        background-color: #198754;
        border-color: #198754;
    }
    .btn-remove {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .removed-row {
    background-color: #f8d7da !important;
    color: #721c24 !important;
    text-decoration: line-through;
    }
    .text-grayed {
        color: #6c757d !important;
    }
    .request-btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    }
    #manageSignatoriesModal .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>


        <!-- Manage Signatories Modal -->
<div class="modal fade" id="manageSignatoriesModal" tabindex="-1" aria-labelledby="manageSignatoriesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="manageSignatoriesLabel">Manage Signatories</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php $allDepartments = getAllEmployeeDepartments($userID); ?>
        <table class="table table-striped">
            <thead>
                <tr class="table-dark">
                    <th>Office/Dept</th>
                    <th>Signatory Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allDepartments as $dept): ?>
                    <tr class="<?php echo $dept['is_removed'] ? 'removed-row text-grayed' : ''; ?>">
                        <td class="dept-name"><?php echo htmlspecialchars($dept['dept_name']); ?></td>
                        <td class="signatory-name"><?php echo htmlspecialchars($dept['signatory']); ?></td>
                        <td>
                        <button type="button" class="btn btn-info add-btn" <?= ($dept['is_removed'] && $employeeInfo['hasRequested'] == 0) ? '' : 'disabled' ?>>
                            Add
                        </button>
                        <button type="button" class="btn btn-danger remove-btn" <?= (!$dept['is_removed'] && $employeeInfo['hasRequested'] == 0) ? '' : 'disabled' ?>>
                            Remove
                        </button>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="saveSignatoriesBtn" class="btn btn-success">Save</button>
      </div>
    </div>
  </div>
</div>

    <script>
        // Get the PHP variable into JavaScript
        const role = "<?php echo $role; ?>";

        // Wait until the page is fully loaded
        document.addEventListener("DOMContentLoaded", function () {
            const signatoriesDiv = document.getElementById("manageSignatories");

            if (signatoriesDiv) {
            // Show or hide the div based on the role
            if (role === "employee") {
                signatoriesDiv.style.display = "block";
            } else {
                signatoriesDiv.style.display = "none";
            }
            }
        });
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Get the PHP variable into JavaScript
    const hasRequested = <?php echo $employeeInfo['hasRequested'] ?? 0; ?>;
    
    // Only enable button functionality if no requests are pending
    if (hasRequested === 0) {
        // Button toggle functionality
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const addBtn = row.querySelector('.add-btn');
                const deptName = row.querySelector('.dept-name');
                const signatoryName = row.querySelector('.signatory-name');
                
                // Toggle button states
                addBtn.disabled = !addBtn.disabled;
                this.disabled = !this.disabled;
                
                // Toggle gray text
                deptName.classList.toggle('text-grayed');
                signatoryName.classList.toggle('text-grayed');
            });
        });

        document.querySelectorAll('.add-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const removeBtn = row.querySelector('.remove-btn');
                const deptName = row.querySelector('.dept-name');
                const signatoryName = row.querySelector('.signatory-name');
                
                // Toggle button states
                this.disabled = !this.disabled;
                removeBtn.disabled = !removeBtn.disabled;
                
                // Remove gray text
                deptName.classList.remove('text-grayed');
                signatoryName.classList.remove('text-grayed');
            });
        });

        // Save button functionality
        const saveBtn = document.getElementById('saveSignatoriesBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                const removedDepartments = [];
                const addedDepartments = [];
                
                // Collect all departments where Remove button is disabled (marked for removal)
                document.querySelectorAll('.remove-btn:disabled').forEach(btn => {
                    const row = btn.closest('tr');
                    removedDepartments.push(row.querySelector('.dept-name').textContent);
                });
                
                // Collect all departments where Add button is disabled (marked for addition)
                document.querySelectorAll('.add-btn:disabled').forEach(btn => {
                    const row = btn.closest('tr');
                    addedDepartments.push(row.querySelector('.dept-name').textContent);
                });
                                
                if (removedDepartments.length === 0 && addedDepartments.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No changes to save',
                        text: 'You haven\'t made any changes'
                    });
                    return;
                }
                
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'student-dashboard.php';
                
                if (removedDepartments.length > 0) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'removed_departments';
                    input.value = JSON.stringify(removedDepartments);
                    form.appendChild(input);
                }
                
                if (addedDepartments.length > 0) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'added_departments';
                    input.value = JSON.stringify(addedDepartments);
                    form.appendChild(input);
                }
                
                document.body.appendChild(form);
                form.submit();
            });
        }
    } else {
        // If hasRequested is 1, disable all buttons in the modal
        document.querySelectorAll('#manageSignatoriesModal .btn').forEach(btn => {
            btn.disabled = true;
        });
    }

    // Request button click handler
    document.querySelectorAll('.request-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Store in sessionStorage that clearance has started
            sessionStorage.setItem('clearanceStarted', 'true');
        });
    });
});
</script>

</body>
</html>