<?php 

    include 'functions.php'; 
    
    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Faculty';
    $facultyID = $_SESSION['userID'];

    $facultyData = getFacultyData($facultyID);

    $studID = "";
    $studName = "";
    $studCourse = "";
    $studYearLevel = "";
    $studContactNumber = "";
    $commentAreaValue = '';
    $studentFound = false;

    if (isset($_POST['searchButton'])) {
        $studID = $_POST['userID'];
        $student = fetchStudentInfo($studID);
    
        $con = openCon();
        $orderedDepartments = [];
        $deptQuery = "SELECT dept_name FROM deptartments_cred ORDER BY id ASC";
        $result = mysqli_query($con, $deptQuery);
    
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $orderedDepartments[] = $row['dept_name'];
            }
        } else {
            echo "Error fetching departments: " . mysqli_error($con);
            $studentFound = false;
            $commentAreaValue = '';
            return;
        }
    
        if ($student) { // Removed clearance completion check
            $studName = $student['stud_name'];
            $studCourse = $student['course'];
            $studYearLevel = $student['year_level'];
            $studContactNumber = $student['contact_number'];
            $studentFound = true;
    
            $deptName = $facultyData['dept_name'];
            $commentAreaValue = fetchStudentComment($studID, $deptName);
        } else {
            $studentFound = false;
            $commentAreaValue = '';
        }
        closeCon($con);
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
    <div class="">
        <form method="post">
        <div class="container text-center mt-5 custom-search-shadow position-relative z-1 bg-light" style="width: 700px;">
            <div class="d-flex justify-content-center align-items-center py-4">
                <div>
                    <label for="studentID" class="form-label fs-5">Enter Student ID:</label>
                </div>
                <div class="ps-4" style="width: 300px;">
                    <input type="number" step="1" name="userID" placeholder="" class="form-control" value="<?php echo $studID ?>" >
                </div>
                <div class="ps-4">
                    <button class="btn btn-info fs-5" name="searchButton">Search</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container text-center d-flex justify-content-center form-check" style="gap: 50px">
        <div>
            <input type="radio" name="pdfForm" id="" class="form-check-input">
            <label for="" class="form-check-label fs-6 fw-meduim">Student Clearance Form</label>
        </div>
        <div>
            <input type="radio" name="pdfForm" id="" class="form-check-input">
            <label for="" class="form-check-label fs-6 fw-meduim">Employee Clearance Form</label>
        </div>
    </div>
    <div class="container text-center pt-3">
        <div>
            <button class="btn btn-danger" onclick="downloadPDF()">Download PDF</button>
        </div>
    </div>
   

    <div class="container pt-3 col-lg-7" id="generatePDF">
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

                function getFirstFiveDepartments($studID) {
                    $departments = getStudentClearanceData($studID);
                    
                    // Ensure $departments is an array before slicing
                    if (is_array($departments)) {
                        return array_slice($departments, 0, 5); // Get only the first 5 departments
                    } else {
                        return []; // Return an empty array if no data found
                    }
                }

                if (!empty($studID)) { // Instead of $_GET['studID']
                    $departments = getFirstFiveDepartments($studID);

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
    
    <script type="text/javascript">
    function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'p',
            unit: 'mm',
            format: 'a4'
        });

        const element = document.getElementById("generatePDF");

        html2canvas(element, {
            scale: 2, // Increases resolution
            useCORS: true // Fixes potential image loading issues
        }).then(canvas => {
            const imgData = canvas.toDataURL("image/png");
            const imgWidth = 210; // A4 width in mm
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            doc.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);
            doc.save("clearance_form.pdf");
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector(".btn-danger").addEventListener("click", function (event) {
            event.preventDefault(); // Prevent any default action
            downloadPDF();
        });
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>