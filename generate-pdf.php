<?php 
    include 'functions.php'; 
    
    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Faculty';
    $facultyID = $_SESSION['userID'];

    $facultyData = getFacultyData($facultyID);

    $userType = 'student'; // Default value

    if (isset($_POST['userType'])) {
        $userType = $_POST['userType'];
    }

    // Initialize variables
    $studID = $empID = "";
    $studName = $empName = $studCourse = $empDepartment = $empPosition = "";
    $studentFound = $employeeFound = false;
    $errorMessage = '';
    $studContactNumber = '';

    if ($userType == 'student') {
        if (isset($_POST['searchButton'])) {
            $studID = $_POST['userID'];
            $student = fetchStudentInfo($studID);
    
            if ($student) {
                $studName = $student['stud_name'];
                $studCourse = $student['course'];
                $studYearLevel = $student['year_level'];
                $studContactNumber = $student['contact_number'];
                $studentFound = true;
            } else {
                $studentFound = false;
                $errorMessage = "No student found with ID: " . htmlspecialchars($studID);
            }
        }
    } 
    elseif ($userType == 'employee') {
        if (isset($_POST['searchButton'])) {
            $empID = $_POST['userID'];
            $employee = fetchEmployeeInfo($empID);
    
            if ($employee) {
                $empName = $employee['name'];
                $empDepartment = $employee['department'];
                $empPosition = $employee['position'];
                $employeeFound = true;
            } else {
                $employeeFound = false;
                $errorMessage = "No employee found with ID: " . htmlspecialchars($empID);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="styles/faculty-dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Generate PDF</title>
</head>
<body style="height: auto; min-height: 100vh;">
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <div>
            <img src="img/logo.png" alt="" height="80px" class="ps-3">
        </div>
        <div class="head">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white " href="#">Generate PDF</a>
        </div>
    </header>

    <div class="sidebar">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class="pt-4 d-flex gy-1 " >
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="fs-5" style="font-weight: 500;"><?php echo $facultyData['employee_name']; ?></p>
                    <p class="position-absolute" style="top: 52px;"><?php echo $facultyData['dept_name'] . " Employee"; ?></p>
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
            <div class="pt-1 ms-2 d-flex align-items-center">
                <div class="" style="font-size: 25px">
                    <i class="fa-solid fa-house"></i>
                </div>
                <div class="ps-3">
                    <a href="faculty-dashboard.php" class="text-decoration-none text-dark fs-6 fw-medium">Dashboard</a>
                </div>
            </div>
            <div class="pt-4 ms-2 d-flex align-items-center">
                <div class="" style="font-size: 25px">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <div class="ps-3">
                    <a href="generate-pdf.php" class="text-decoration-none text-dark fs-6 fw-medium">Print PDF File</a>
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

    <div class="container align-items-center">
        <div class="container mt-5  pt-5 col-lg-4" >
            <h1>Clearance PDF File</h1>
        </div>
    </div>
    
    <!-- Main Form Container -->
    <div class="container">
        <form method="post" id="pdfForm">
            <!-- Search Bar -->
            <div class="container text-center mt-5 custom-search-shadow position-relative z-1 bg-light" style="width: 700px;">
                <div class="d-flex justify-content-center align-items-center py-4">
                    <div>
                        <label for="userID" class="form-label fs-5">
                            <?php echo ($userType == 'student') ? 'Enter Student ID:' : 'Enter Employee ID:'; ?>
                        </label>
                    </div>
                    <div class="ps-4" style="width: 300px;">
                        <input type="number" step="1" name="userID" placeholder="" class="form-control" 
                            value="<?php echo ($userType == 'student') ? $studID : $empID; ?>" required>
                    </div>
                    <div class="ps-4">
                        <button class="btn btn-info fs-5" name="searchButton">Search</button>
                    </div>
                </div>
            </div>
            
            <!-- Radio Buttons -->
            <div class="container text-center d-flex justify-content-center form-check" style="gap: 50px; margin-top: 20px;">
                <div class="border border-1 shadow p-3">
                    <input type="radio" name="userType" id="studentRadio" class="form-check-input" 
                        value="student" <?php echo ($userType == 'student') ? 'checked' : ''; ?>>
                    <label for="studentRadio" class="form-check-label fs-6 fw-meduim">Student Clearance Form</label>
                </div>
                <div class="border border-1 shadow p-3">
                    <input type="radio" name="userType" id="employeeRadio" class="form-check-input" 
                        value="employee" <?php echo ($userType == 'employee') ? 'checked' : ''; ?>>
                    <label for="employeeRadio" class="form-check-label fs-6 fw-meduim">Employee Clearance Form</label>
                </div>
            </div>
            
            <!-- PDF Download Button -->
            <div class="container text-center pt-3">
                <button type="button" class="btn btn-danger" onclick="downloadPDF()">Download PDF</button>
            </div>
            
            <!-- Error Message Display -->
            <?php if (!empty($errorMessage)): ?>
                <div class="container alert alert-danger alert-dismissible fade show mt-3" style="width: 700px;" role="alert">
                    <?php echo $errorMessage; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Student Clearance Form -->
    <div class="container pt-3 col-lg-7" id="generatePDF" style="display: <?php echo ($userType == 'student') ? 'block' : 'none'; ?>;">
    <div class="container border border-dark text-center pt-2">
            <p class="fs-3 fw-bold">Clearance Form</p>
        </div>
        <div class="container col-lg-12 border pt-2 border-dark" style="background-color: lightcyan;">
            <p class="fs-5 fw-bold">PRIVACY NOTICE</p>
        </div>
        <div class="container border border-dark pt-2">
            <div>
                <p class="fst-italic">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Vero natus numquam recusandae enim harum ipsam odit veritatis cumque illum alias?</p>
            </div>
        </div>
        <div class="container col-lg-12 border pt-2 border-dark" style="background-color: lightcyan;">
            <p class="fs-5 fw-bold">PERSONAL INFORMATION</p>
        </div>

        <div class="container border border-dark d-flex align-items-center">
            <div class="d-flex col-lg-6 pt-2">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>FULL NAME: <?php echo $studName ?></p>
                </div>
            </div>
            <div class="d-flex pt-2">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>STUDENT ID NUMBER: <?php echo $studID ?></p>
                </div>
            </div>
        </div>
        <div class="container border border-dark d-flex">
            <div class="d-flex col-lg-4 pt-2">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>COURSE: <?php echo $studCourse ?></p>
                </div>
            </div>
            <div class="d-flex pt-2 col-lg-4">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>YEAR LEVEL: <?php echo !empty($studYearLevel) ? getOrdinal($studYearLevel) . " YEAR" : ""; ?></p>
                </div>
            </div>
            <div class="d-flex pt-2 col-lg-4">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>

                <?php $semData = getSemesterAndSchoolYear(); ?>
                
                <div class="ps-1">
                    <p>SEMESTER: <?= htmlspecialchars($semData['semester']) ?> S.Y. <?= htmlspecialchars($semData['school_year']) ?></p>
                </div>
            </div>
        </div>
        <div class="container border border-dark d-flex">
            <div class="d-flex col-lg-6 pt-2">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>CONTACT NUMBER: <?php echo $studContactNumber ?></p>
                </div>
            </div>
            
            <div class="d-flex pt-2 col-lg-6">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>STUDENT SIGNATURE: </p>
                </div>
            </div>
        </div>
        <div class="container col-lg-12 border pt-2 border-dark" style="background-color: lightcyan;">
            <p class="fs-5 fw-bold">CLEARANCE FORM</p>
        </div>
        
        <table class="table table-striped container border border-dark">
            <thead>
                <tr class="text-center">
                    <th>Office/Dept</th>
                    <th>Signatory Name</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if (!empty($studID)) {
                    // Get student info to check foreigner status
                    $studentInfo = fetchStudentInfo($studID);
                    $isForeigner = isset($studentInfo['foreigner']) && $studentInfo['foreigner'] == 1;
                    
                    $departments = getDepartmentsStudentForPDF($studID);

                    if (!empty($departments)) {
                        $count = 1;
                        foreach ($departments as $dept) {
                            if (is_array($dept)) { 
                                // Check if status is "Approved" and set color accordingly
                                $statusColor = (strtolower($dept['status']) === 'approved') ? 'green' : 'red';
                                ?>
                                <tr>
                                    <td><strong><?php echo $count . ". " . strtoupper($dept['dept_name']); ?></strong></td>
                                    <td class="text-center"><strong><?php echo htmlspecialchars($dept['signatory']); ?></strong></td>
                                    <td class="text-center"><strong style="color: <?php echo $statusColor; ?>;"><?php echo htmlspecialchars($dept['status']); ?></strong></td>
                                    <td class="text-center"><strong><?php echo htmlspecialchars($dept['date']); ?></strong></td>
                                    <td class="text-center"><strong><?php echo htmlspecialchars($dept['remarks']); ?></strong></td>
                                </tr>
                                <?php 
                            }
                            $count++;
                        }
                    } else { ?>
                        <tr><td colspan="5" class="text-center">No clearance data found for this student.</td></tr>
                    <?php }
                } else { ?>
                    <tr><td colspan="5" class="text-center">Search for a student to display clearance data.</td></tr>
                <?php } ?>            
            </tbody>
        </table>
        
            <div class="container">
                <div>
                    <p>RECOMMENDING APPROVAL</p>
                </div>
            </div>

            <?php
                $selectedClearanceData = getSelectedClearanceData($studID);
                $selectedClearanceDataDate = getSelectedClearanceDataDate($studID);
            ?>

            <div class="container border border-dark mb-5 d-flex">
                <div class="border-end col-lg-2 border-dark">
                    <div class="pt-2 text-center">
                        <p class="fw-bold" style="line-height: 1;">Daisie W. Pinzon</p>
                    </div>
                    <div class="d-flex text-center">
                        <div>
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <p style="line-height: 1;" class="fw-bold">PROGRAM CHAIR</p>
                        </div>
                    </div>
                    <div>
                        <p class="fst-italic" style="line-height: 1;">(CCIS Faculty/Office)</p>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">STATUS:</p>
                        </div>  
                        <div class="ps-1">
                        <p class=" fw-bold <?=  !empty($studID) && isset($selectedClearanceData['Program Chair']) ? getStatusClass($selectedClearanceData['Program Chair']) : '' ?>" style="line-height: 1;">
                            <?= !empty($studID) && isset($selectedClearanceData['Program Chair']) ? getStatusText($selectedClearanceData['Program Chair']) : '' ?>
                        </p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                            <p class="fst-italic" style="line-height: 1;">
                                <?= !empty($studID) && isset($selectedClearanceDataDate['Program Chair']) ? formatDate($selectedClearanceDataDate['Program Chair']) : '' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-end col-lg-3 border-dark ps-2">
                    <div class="pt-2 text-center">
                        <p class="fw-bold" style="line-height: 1;">GEORGE M. GRANADOS, MM, MIT</p>
                    </div>
                    <div class="d-flex text-center">
                        <div>
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="">
                            <p style="line-height: 1;" class="fw-bold">DEAN, CCIS</p>
                        </div>
                    </div>
                    <div>
                        <p class="fst-italic" style="line-height: 1;">(CCIS Dean's Office)</p>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">STATUS:</p>
                        </div>  
                        <div class="ps-1">
                            <p class=" fw-bold <?=  !empty($studID) && isset($selectedClearanceData['Dean']) ? getStatusClass($selectedClearanceData['Dean']) : '' ?>" style="line-height: 1;">
                                <?= !empty($studID) && isset($selectedClearanceData['Dean']) ? getStatusText($selectedClearanceData['Dean']) : '' ?>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                        <p class="fst-italic" style="line-height: 1;">
                                <?= !empty($studID) && isset($selectedClearanceDataDate['Dean']) ? formatDate($selectedClearanceDataDate['Dean']) : '' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-end col-lg-2 border-dark ps-2">
                    <div class="pt-2">
                        <p class="fw-bold" style="line-height: 1;">Daisie W. Pinzon</p>
                    </div>
                    <div class="d-flex text-center">
                        <div>
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <p style="line-height: 1;" class="fw-bold">REGISTRAR</p>
                        </div>
                    </div>
                    <div>
                        <p class="fst-italic" style="line-height: 1;">(Window 3)</p>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">STATUS:</p>
                        </div>  
                        <div class="ps-1">
                            <p class=" fw-bold <?=  !empty($studID) && isset($selectedClearanceData['Registrar']) ? getStatusClass($selectedClearanceData['Registrar']) : '' ?>" style="line-height: 1;">
                                <?= !empty($studID) && isset($selectedClearanceData['Registrar']) ? getStatusText($selectedClearanceData['Registrar']) : '' ?>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                        <p class="fst-italic" style="line-height: 1;">
                                <?= !empty($studID) && isset($selectedClearanceDataDate['Registrar']) ? formatDate($selectedClearanceDataDate['Registrar']) : '' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-end col-lg-3 border-dark ps-2">
                    <div class="pt-2">
                        <p class="fw-bold" style="line-height: 1;">DR. ROY D. DAYRIT</p>
                    </div>
                    <div class="d-flex text-center">
                        <div>
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <p style="line-height: 1;" class="fw-bold">V.P. FOR ACADEMIC AFFAIRS</p>
                        </div>
                    </div>
                    <div>
                        <p class="fst-italic" style="line-height: 1;">(2nd Floor, IT Bldg)</p>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">STATUS:</p>
                        </div>  
                        <div class="ps-1">
                            <p class=" fw-bold <?=  !empty($studID) && isset($selectedClearanceData['Vice President']) ? getStatusClass($selectedClearanceData['Vice President']) : '' ?>" style="line-height: 1;">
                                <?= !empty($studID) && isset($selectedClearanceData['Vice President']) ? getStatusText($selectedClearanceData['Vice President']) : '' ?>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                            <p class="fst-italic" style="line-height: 1;">
                                <?= !empty($studID) && isset($selectedClearanceDataDate['Vice President']) ? formatDate($selectedClearanceDataDate['Vice President']) : '' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class=" col-lg-2 border-dark ps-2">
                    <div class="pt-2">
                        <p class="fw-bold" style="line-height: 1;">JEMELYN A DAYRIT</p>
                    </div>
                    <div class="d-flex text-center">
                        <div>
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <p style="line-height: 1;" class="fw-bold">ACCOUNTING</p>
                        </div>
                    </div>
                    <div>
                        <p class="fst-italic" style="line-height: 1;">(Window 10)</p>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">STATUS:</p>
                        </div>  
                        <div class="ps-1">
                            <p class=" fw-bold <?=  !empty($studID) && isset($selectedClearanceData['Accounting']) ? getStatusClass($selectedClearanceData['Accounting']) : '' ?>" style="line-height: 1;">
                                <?= !empty($studID) && isset($selectedClearanceData['Accounting']) ? getStatusText($selectedClearanceData['Accounting']) : '' ?>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                            <p class="fst-italic" style="line-height: 1;">
                                <?= !empty($studID) && isset($selectedClearanceDataDate['Accounting']) ? formatDate($selectedClearanceDataDate['Accounting']) : '' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Employee Clearance Form -->
    <div class="container pt-3 col-lg-7 mb-5" id="generatePDFEmployee" style="display: <?php echo ($userType == 'employee') ? 'block' : 'none'; ?>;">
    <div class="container text-center border border-dark pt-2">
            <p class="fs-3 fw-bold">TEACHING CLEARANCE FORM</p>
        </div>
        <div class="container border border-dark">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Harum, sequi nam. Vero recusandae inventore perspiciatis tempora totam. Placeat, vero quasi!</p>
        </div>
        <div class="container text-center border border-dark">
            <p class="fs-3 fw-bold">PERSONAL INFORMATION</p>
        </div>
        <div class="container border border-dark">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia repellat provident cum, dolorem laboriosam autem? Veniam cupiditate ducimus enim in!s</p>
        </div>
        <div class="container d-flex border border-dark justidfy-content-center align-items-center">
            <div class="container d-flex  border-dark border-end">
                <p class="fw-bold">NAME:</p>
                <p> <?php echo $empName ?></p>
            </div>
            <div class="container d-flex">
                <p class="fw-bold">EMPLOYEE ID NO:</p>
                <p> <?php echo $empID ?></p>
            </div>
        </div>
        <div class="container d-flex border border-dark justidfy-content-center align-items-center">
            <div class="container d-flex  border-dark border-end">
                <p class="fw-bold">DEPARTMENT:</p>
                <p> <?php echo $empDepartment ?></p>
            </div>
            <div class="container d-flex">
                <p class="fw-bold">POSITION:</p>
                <p> <?php echo $empPosition ?></p>
            </div>
        </div>
        <div class="container border border-dark">
            <div>
                <p class="fw-bold">EMPLOYEE CATEGORY:</p>
            </div>
            <div class="d-flex text-center align-items-center justify-content-center" style="gap: 150px;">
                <div class="">
                    <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                    <label class="form-check-label" for="checkDefault">
                        REGULAR
                    </label>
                </div>
                <div>
                    <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                    <label class="form-check-label" for="checkDefault">
                        PROBATIONARY FULL-TIME
                    </label>
                </div>
                <div>
                    <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                    <label class="form-check-label" for="checkDefault">
                        PROBATIONARY PART-TIME
                    </label>
                </div>
            </div>        
        </div>
        <div class="container border border-dark">
            <div>
                <p class="fw-bold">PURPOSE:</p>
            </div>
            <div class="d-flex text-center align-items-center justify-content-center" style="gap: 150px;">
                <div class="">
                    <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                    <label class="form-check-label" for="checkDefault">
                        RESIGNATION
                    </label>
                </div>
                <div>
                    <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                    <label class="form-check-label" for="checkDefault">
                        RETIREMENT
                    </label>
                </div>
                <div>
                    <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                    <label class="form-check-label" for="checkDefault">
                        END OF CONTRACT (SEMESTER)
                    </label>
                </div>
            </div>
            <div class="pt-5">
                <p>OTHERS (PLEASE SPECIFY): ___________________</p>
            </div>        
        </div>
        <div class="container border border-dark d-flex">
               <div class="pt-3">
                    <p class="fw-bold">CLEARANCE COVERED</p>
               </div>
               <div class="ps-5 pt-3">
                    <p>FROM: ____________________UNTIL:____________________</p>
               </div>     
        </div>
        <div class="container pt-3">
            <p class="" style="font-size: 18.5px;">This is to certify that I, _________________________________________________ has no pending accountability with the following offices as of today, __________________.</p>
        </div>

            <table class="table table-striped  container border border-dark">
                <thead>
                    <tr class="text-center">
                        <th>DEPARTMENT/OFFICES</th>
                        <th>STATUS</th>
                        <th>NAME OF CLEARING OFFICER/HEAD</th>
                        <th>DATE SIGNED</th>
                        <th>REMARKS</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($empID)) {
                    // Define the department order for employees
                    $orderedDepartments = [
                        'Grade Level/Strand Coordinators',
                        'Program Chair',
                        'Principal',
                        'Registrar',
                        'Library',
                        'ITS',
                        'PPFO',
                        'Vice President',
                        'Human Resources',
                        'Accounting'
                    ];

                    $con = openCon();
                    
                    // Get department signatories
                    $signatories = [];
                    $deptQuery = "SELECT dept_name, employee_name FROM deptartments_cred WHERE type = 'Employee' OR type = 'Both'";
                    $deptResult = mysqli_query($con, $deptQuery);
                    while ($row = mysqli_fetch_assoc($deptResult)) {
                        $signatories[$row['dept_name']] = $row['employee_name'];
                    }

                    // Get clearance data for each department
                    foreach ($orderedDepartments as $index => $deptName) {
                        // Get status
                        $statusQuery = "SELECT `$deptName` FROM employee_clearance WHERE emp_id = '$empID'";
                        $statusResult = mysqli_query($con, $statusQuery);
                        $statusRow = mysqli_fetch_assoc($statusResult);
                        $status = isset($statusRow[$deptName]) && $statusRow[$deptName] == 1 ? 'Approved' : 'Declined';
                        $statusColor = $status == 'Approved' ? 'green' : 'red';

                        // Get date
                        $dateQuery = "SELECT `$deptName` FROM employee_date WHERE emp_id = '$empID'";
                        $dateResult = mysqli_query($con, $dateQuery);
                        $dateRow = mysqli_fetch_assoc($dateResult);
                        $date = isset($dateRow[$deptName]) && !empty($dateRow[$deptName]) ? formatDate($dateRow[$deptName]) : 'N/A';

                        // Get remarks
                        $remarksQuery = "SELECT `$deptName` FROM employee_comment WHERE emp_id = '$empID'";
                        $remarksResult = mysqli_query($con, $remarksQuery);
                        $remarksRow = mysqli_fetch_assoc($remarksResult);
                        $remarks = isset($remarksRow[$deptName]) && !empty($remarksRow[$deptName]) ? $remarksRow[$deptName] : 'No Remarks';

                        ?>
                        <tr>
                            <td><strong><?php echo ($index + 1) . ". " . strtoupper($deptName); ?></strong></td>
                            <td class="text-center"><strong><?php echo isset($signatories[$deptName]) ? htmlspecialchars($signatories[$deptName]) : 'N/A'; ?></strong></td>
                            <td class="text-center"><strong style="color: <?php echo $statusColor; ?>;"><?php echo htmlspecialchars($status); ?></strong></td>
                            <td class="text-center"><strong><?php echo htmlspecialchars($date); ?></strong></td>
                            <td class="text-center"><strong><?php echo htmlspecialchars($remarks); ?></strong></td>
                        </tr>
                        <?php
                    }
                    
                    closeCon($con);
                } else {
                    ?>
                    <tr><td colspan="5" class="text-center">Search for an employee to display clearance data.</td></tr>
                    <?php
                }
                ?>
        </tbody>
            </table> 
            
        <div class="container">
            <p class="fw-bold">CLEARED:</p>
        </div>  
        <table class="table table-striped  container border border-dark">
            <thead>
                <tr class="text-center">
                    <th>DEPARTMENT/OFFICES</th>
                    <th>STATUS</th>
                    <th>DATE SIGNED</th>
                    <th>REMARKS</th>
                </tr>
            </thead>
            <tbody>
                <tr style="font-size: 15px;">
                    <th>EXECUTIVE VICE PRESIDENT</th>
                </tr>
                </tr>
            </tbody>
        </table>
    </div>
    
    <script type="text/javascript">
    function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'p',
            unit: 'mm',
            format: 'a4'
        });

        // Check which radio is selected
        const selectedType = document.querySelector('input[name="userType"]:checked').value;

        // Get the corresponding element based on selection
        const elementId = selectedType === 'student' ? "generatePDF" : "generatePDFEmployee";
        const element = document.getElementById(elementId);

        // Make sure the element exists before proceeding
        if (!element) {
            alert("No content found to generate PDF.");
            return;
        }

        html2canvas(element, {
            scale: 2,
            useCORS: true
        }).then(canvas => {
            const imgData = canvas.toDataURL("image/png");
            const imgWidth = 210;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            doc.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);
            doc.save("clearance_form.pdf");
        });
    }

    function toggleDivs() {
        const studentDiv = document.getElementById('generatePDF');
        const employeeDiv = document.getElementById('generatePDFEmployee');
        const selectedValue = document.querySelector('input[name="userType"]:checked').value;

        if (selectedValue === 'student') {
            studentDiv.style.display = 'block';
            employeeDiv.style.display = 'none';
        } else if (selectedValue === 'employee') {
            studentDiv.style.display = 'none';
            employeeDiv.style.display = 'block';
        }
        
        // Submit the form to update the PHP variables
        document.getElementById('pdfForm').submit();
    }

    // Add event listeners to radio buttons
    document.querySelectorAll('input[name="userType"]').forEach(radio => {
        radio.addEventListener('change', toggleDivs);
    });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>