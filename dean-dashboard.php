<?php 
    include 'functions.php';

    // Check if user is logged in and is Dean
    if (!isset($_SESSION['userID'])) {
        header("Location: login.php");
        exit();
    }

    $deanID = $_SESSION['userID'];
    $deanData = getDeanData($deanID);
    
    // Verify this is the Dean
    if (!$deanData) {
        header("Location: unauthorized.php");
        exit();
    }

    $commentAreaValue = '';
    $studID = "";
    $studName = "";
    $studCourse = "";
    $studentFound = false;
    $errorMessage = '';

    // Process form submissions
    if (isset($_POST['searchButton'])) {
        $studID = $_POST['studID'];
        $searchResult = processStudentDeanSearch($studID, $deanData);
        
        $studName = $searchResult['studName'];
        $studCourse = $searchResult['studCourse'];
        $commentAreaValue = $searchResult['commentAreaValue'];
        $studentFound = $searchResult['studentFound'];
        $errorMessage = $searchResult['errorMessage'];

        if ($studentFound) {
            $hasRequested = hasStudentRequested($studID, 'Dean');
            if (!$hasRequested) {
                $errorMessage = "Student hasn't requested Dean clearance";
                $studentFound = false;
            }
        }
    }

    if (isset($_POST['approveButton']) && isset($_POST['studID'])) {
        $studID = $_POST['studID'];
        $deptName = 'Dean';
        $currentDate = date('M d, Y');
        approveStudent($studID, $deptName);
        approveDate($studID, $deptName, $currentDate);
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $studID);
        exit();
    }

    if (isset($_POST['declineButton']) && isset($_POST['studID'])) {
        $studID = $_POST['studID'];
        $deptName = 'Dean';
        $comment = $_POST['commentArea'];
        $currentDate = date('M d, Y');
        approveDate($studID, $deptName, $currentDate);
        storeCommentAndReset($studID, $deptName, $comment);
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $studID);
        exit();
    }

    // Pre-fill ID if coming from request list
    $preFillID = isset($_GET['id']) ? $_GET['id'] : '';

    // Get pending requests count
    $studentRequestCount = count(getPendingStudentRequests('Dean'));
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
    <title>Dean - Student Clearance</title>
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
    </style>
</head>
<body style="background-color: whitesmoke;">
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <div>
            <img src="img/logo.png" alt="" height="80px" class="ps-3">
        </div>
        <div class="head">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white" href="#">Dean - Student Clearance</a>
        </div>
    </header>

    <div class="sidebar pt-5">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class="d-flex gy-1">
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="s" style="font-weight: 500;"><?php echo $deanData['dean_name']; ?></p>
                    <p class="position-absolute" style="top: 35px;">Dean</p>
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

            <div class="d-flex gy-1 pt-1 ms-2">
                <div class="position-relative">
                    <a href="request-list.php" style="text-decoration: none;" class="text-dark">
                        <i class="fa-solid fa-list" style="font-size: 25px;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $studentRequestCount; ?>
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
                    <a href="dean-dashboard.php" class="text-decoration-none text-dark fs-6 fw-medium">Dashboard</a>
                </div>
            </div> 

            <!--<div class="pt-4 ms-2 d-flex align-items-center">
                <div>
                    <i class="fa-solid fa-user-graduate" style="font-size: 25px;"></i>
                </div>
                <div class="ps-3">
                    <a href="dean-clearance.php" class="text-decoration-none text-dark fs-6 fw-medium">Student Clearance</a>
                </div>
            </div>-->
            
            <hr>
            <div class="ps-3 pt-3">
                <a href="logout.php" class="text-danger">               
                    <i class="fa-solid fa-right-from-bracket"> Logout</i>               
                </a>
            </div>
        </div>
    </div>
    
    <div class="container text-center pt-4">
        <p class="fs-1 fw-bold">Student Clearance</p>
    </div>

    <form method="post" id="clearanceForm">
        <div class="container text-center mt-5 custom-search-shadow position-relative z-1 bg-light" style="width: 700px;">
            <div class="d-flex justify-content-center align-items-center py-4">
                <div>
                    <label for="studID" class="form-label fs-5">Enter Student ID:</label>
                </div>
                <div class="ps-4" style="width: 300px;">
                    <input type="number" step="1" name="studID" placeholder="Student ID" class="form-control" 
                        value="<?php echo !empty($preFillID) ? htmlspecialchars($preFillID) : $studID; ?>" required>
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
                <textarea class="form-control align-center" style="width: 400px;" 
                    name="commentArea" id="commentArea" rows="3" 
                    onclick="this.setSelectionRange(0, 0)" 
                    oninput="checkTextareaContent()" 
                    <?php echo !$studentFound ? 'disabled' : ''; ?>><?php echo htmlspecialchars($commentAreaValue); ?></textarea>                
                <div class="pt-4">       
                    <button class="btn btn-danger fs-5" name="declineButton" <?php echo $studentFound ? '' : 'disabled'; ?>>Decline</button>      
                </div>   
            </div>
            <div class="col-lg-4">
                <button class="btn btn-success fs-5" name="approveButton" id="approveButton" 
                    <?php echo (!$studentFound || !empty($commentAreaValue)) ? 'disabled' : ''; ?>>Approve</button>
            </div>
        </div>
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