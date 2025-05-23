<?php 
    include 'functions.php';

    // Initialize session variables
    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Faculty';
    $deanID = $_SESSION['userID'];

    // Fetch dean data
    $deanData = getDeanData($deanID);
    $deptName = 'Dean';

    // Initialize variables for student data
    $studID = "";
    $studName = "";
    $studCourse = "";
    $studentFound = false;
    $commentAreaValue = '';
    $errorMessage = '';

    // Process search button submission
    if (isset($_POST['searchButton'])) {
        $studID = $_POST['userID'];
        $searchResult = processStudentDeanSearch($studID, $deanData);

        // Assign search results to variables
        $studName = $searchResult['studName'];
        $studCourse = $searchResult['studCourse'];
        $commentAreaValue = $searchResult['commentAreaValue'];
        $studentFound = $searchResult['studentFound'];
        $errorMessage = $searchResult['errorMessage'];

        // Check if the student has requested clearance from this department
        if ($studentFound) {
            $hasRequested = hasStudentRequested($studID, $deptName);
            if (!$hasRequested) {
                $errorMessage = "Student hasn't requested clearance from this department";
                $studentFound = false;
            }
        }
    }

    // Process approval button submission
    if (isset($_POST['approveButton']) && isset($_POST['userID'])) {
        $studID = $_POST['userID'];
        $currentDate = date('M d, Y');
        approveStudent($studID, $deptName);  
        approveDate($studID, $deptName, $currentDate); 
        header("Location: " . $_SERVER['PHP_SELF']); 
        exit();
    }

    // Process decline button submission
    if (isset($_POST['declineButton']) && isset($_POST['userID'])) {
        $studID = $_POST['userID'];
        $comment = $_POST['commentArea'];
        storeCommentAndReset($studID, $deptName, $comment); 
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $deptName = 'Dean'; 
    $studentRequests = [];
    $studentRequestCount = 0;
    $studentRequests = getPendingStudentRequests($deptName);
    $studentRequestCount = count($studentRequests);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    
    <title>Dean - Student Clearance</title>
</head>
<body>
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <div>
            <img src="img/logo.png" alt="" height="80px" class="ps-3">
        </div>
        <div class="head">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white " href="#">Dean - Student Clearance</a>
        </div>
    </header>

    <div class="sidebar position-relative">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class="pt-4 d-flex gy-1 ">
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
                        <a href="dean-clearance.php" style="text-decoration: none;" class="text-dark"><i class="fa-solid fa-user-graduate " style="font-size: 25px;"></i></a>
                    </div>
                    <div class="ps-2">
                        <a href="dean-clearance.php" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium"> Clearance</p></a>
                    </div>  
                </div>  
            </div>
            <div class="d-flex pt-3 ps-2">
                <div>
                    <a href="dean-dashboard.php" style="text-decoration: none;" class="text-dark"><i class="fa-solid fa-pen-to-square" style="font-size: 25px;"></i></a>
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
                                <?php echo $studentRequestCount ?>
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

    <form method="post">
        <div class="container text-center mt-5 custom-search-shadow position-relative z-1 bg-light" style="width: 700px;">
            <div class="d-flex justify-content-center align-items-center py-4">
                <div>
                    <label for="studentID" class="form-label fs-5">Enter Student ID:</label>
                </div>
                <div class="ps-4" style="width: 300px;">
                    <input type="number" step="1" name="userID" placeholder="" class="form-control" value="<?php echo $studID ?>" required>
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
                                <?php echo $errorMessage; ?>
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
        </div>
    </form>

    <script>
        var studentFound = <?php echo $studentFound ? 'true' : 'false'; ?>;
        var studentApproved = <?php echo isset($studentApproved) ? ($studentApproved ? 'true' : 'false') : 'false'; ?>;
        function checkTextareaContent() {
            var commentArea = document.getElementById('commentArea');
            var approveButton = document.getElementById('approveButton');
            if (commentArea.value.trim() !== '') {
                approveButton.disabled = true;
            } else {
                approveButton.disabled = false;
            }
        }
        window.onload = function() {
            checkTextareaContent();
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
