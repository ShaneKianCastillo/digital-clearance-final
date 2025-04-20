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
            
            <div class="pt-3 ps-2">
        <div class="d-flex gy-1">
            <div>
                <a href="#" data-bs-toggle="modal" data-bs-target="#manageSignatoriesModal" style="text-decoration: none;" class="text-dark">
                    <i class="fa-solid fa-pen-to-square" style="font-size: 20px;"></i>
                </a>
            </div>
            <div class="ps-2">
                <a href="#" data-bs-toggle="modal" data-bs-target="#manageSignatoriesModal" style="text-decoration: none;" class="text-dark">
                    <p class="fs-6 fw-medium">Manage Signatories</p>
                </a>
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
                    'Foreign Affairs',
                    'Computer Lab',
                    'Program Chair',
                    'Dean',
                    'Registrar',
                    'Vice President',
                    'Accounting'
                ];
                
                // Reorder the clearanceData array according to the desired order
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
                        if (empty($data['status'])) {
                            echo 'text-secondary'; // Dark gray (plain)
                        } elseif ($data['status'] == 'Approved') {
                            echo 'text-success';
                        } else {
                            echo 'text-danger';
                        }
                    ?>">
                        <?php
                            echo htmlspecialchars(!empty($data['status']) ? $data['status'] : 'N/A');
                        ?>
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
                        <button class="btn btn-danger mt-2" type="submit" name="removeButton">
                                Remove
                            </button>
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

        <!-- Manage Signatories Modal -->
<div class="modal fade" id="manageSignatoriesModal" tabindex="-1" aria-labelledby="manageSignatoriesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="manageSignatoriesLabel">Manage Signatories</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Default Signatories List -->
       
        <table class="table table-striped">
        <thead>
            <tr class="table-dark">
                <th>Office/Dept</th>
                <th>Signatory Name</th>
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
                    <th>
                        <button class="btn btn-info">add</button>
                        <button class="btn btn-danger">remove</button>
                    </th>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
        
            <div>
                <button class="btn btn-danger">Cancel</button>
                <button class="btn btn-success">Save</button>
            </div>

      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('addSignatoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('newSignatoryName').value.trim();
    if (name) {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = `${name} <button class="btn btn-sm btn-outline-danger">Remove</button>`;
      document.getElementById('custom-signatories').appendChild(li);
      document.getElementById('newSignatoryName').value = '';
    }
  });

  // Delegate click events for Remove buttons
  document.addEventListener('click', function(e) {
    if (e.target && e.target.matches('.btn-outline-danger')) {
      e.target.closest('li').remove();
    }
  });
</script>

</body>
</html>