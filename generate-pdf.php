<?php 

    include 'functions.php'; 
    
    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Faculty';
    $facultyID = $_SESSION['userID'];

    $facultyData = getFacultyData($facultyID);

    $studID = "";
    $studName = "";
    $studCourse = "";
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
    
        $deptName = $facultyData['dept_name'];
        $studentApproved = isStudentEligibleForDepartment($studID, $deptName, $orderedDepartments);
    
        if ($student && $studentApproved) {
            $studName = $student['stud_name'];
            $studCourse = $student['course'];
            $studentFound = true;
    
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

    <div class="sidebar position-relative">
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
            <div class="pt-4 ms-2 d-flex align-items-center">
                <div class="fs-2 ">
                    <i class="fa-solid fa-house"></i>
                </div>
                <div class="ps-3 fs-5">
                    <a href="faculty-dashboard.php" class="text-decoration-none text-dark">Dashboard</a>
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
        <div class="container mt-5  col-lg-4" >
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
                    <p>FULL NAME: Ram Iturralde</p>
                </div>
            </div>
            <div class="d-flex pt-2">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>STUDENT ID NUMBER: 09876</p>
                </div>
            </div>
        </div>
        <div class="container border border-dark d-flex">
            <div class="d-flex col-lg-4 pt-2">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>COURSE: CSS</p>
                </div>
            </div>
            <div class="d-flex pt-2 col-lg-4">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>YEAR LEVEL: 4th year</p>
                </div>
            </div>
            <div class="d-flex pt-2 col-lg-4">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>SEMESTER: 1st Semester S.Y. 2024-2025</p>
                </div>
            </div>
        </div>
        <div class="container border border-dark d-flex">
            <div class="d-flex col-lg-6 pt-2">
                <div>
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="ps-1">
                    <p>CONTACT NUMBER: 0919900909</p>
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
                    <tr>
                        <th>1. LIBRARY</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>2. OSA</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>3. GUIDANCE</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>4. FOREIN AFFAIRS</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>5. COMPUTER LAB</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tbody>
            </table>
        
            <div class="container">
                <div>
                    <p>RECOMMENDING APPROVAL</p>
                </div>
            </div>

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
                            <p class="text-success" style="line-height: 1;">Approved</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                            <p style="line-height: 1;">01-31-02</p>
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
                            <p class="text-success" style="line-height: 1;">Approved</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                            <p style="line-height: 1;">01-31-02</p>
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
                            <p class="text-success" style="line-height: 1;">Approved</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                            <p style="line-height: 1;">01-31-02</p>
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
                            <p class="text-success" style="line-height: 1;">Approved</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                            <p style="line-height: 1;">01-31-02</p>
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
                            <p class="text-success" style="line-height: 1;">Approved</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p style="line-height: 1;">DATE:</p>
                        </div>  
                        <div class="ps-1">
                            <p style="line-height: 1;">01-31-02</p>
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