<?php 
    include 'functions.php';
    checkUserSessionIsActive();

    $deptName = '';
    $requests = [];
    $userType = 'Student'; 

    if ($_SESSION['role'] === 'dean') {
        $deanID = $_SESSION['userID'];
        $deanData = getDeanData($deanID);
        $deptName = 'Dean'; 
        $userType = 'Student'; 
        $requests = getPendingStudentRequests($deptName); 
    } else {
        $facultyID = $_SESSION['userID'];
        $facultyData = getFacultyData($facultyID);
        $deptName = $facultyData['dept_name'];

        if ($facultyData['type'] == 'Employee') {
            $userType = 'Employee';
        } elseif ($facultyData['type'] == 'Both') {
            $userType = isset($_POST['userClearance']) ? $_POST['userClearance'] : 'Student';
        }

        // Get the appropriate pending requests
        $requests = ($userType == 'Student') 
            ? getPendingStudentRequests($deptName) 
            : getPendingEmployeeRequests($deptName);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <title>Request List</title>
    <style>
        * {
            font-family: 'Roboto', sans-serif;
        }
        .active-list {
            font-weight: bold;
            color: #0d6efd !important;
        }
        .action-btns {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container mt-5 pb-3">
        <h1>Pending Requests - <?php echo htmlspecialchars($deptName); ?></h1>
    </div>

    <nav aria-label="breadcrumb" class="container">
        <ol class="breadcrumb">

            <li class="breadcrumb-item">
                <a href="<?php echo ($_SESSION['role'] === 'dean') ? 'dean-dashboard.php' : 'dashboard.php'; ?>">
                    Dashboard
                </a>
            </li>

            <li class="breadcrumb-item active" aria-current="page">Request List</li>
        </ol>
    </nav>

    <?php if (isset($facultyData) && $facultyData['type'] === 'Both'): ?>
    <div class="container mb-3">
        <form method="POST" id="filterForm" class="d-flex gap-3">
            <div class="form-check">
                <input type="radio" class="form-check-input" id="studentList" name="userClearance" value="Student"
                    <?= $userType === 'Student' ? 'checked' : '' ?>>
                <label class="form-check-label" for="studentList">Student List</label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input" id="employeeList" name="userClearance" value="Employee"
                    <?= $userType === 'Employee' ? 'checked' : '' ?>>
                <label class="form-check-label" for="employeeList">Employee List</label>
            </div>
            <input type="submit" hidden id="submitFilter">
        </form>
    </div>
    <script>
        document.querySelectorAll('input[name="userClearance"]').forEach((input) => {
            input.addEventListener('change', () => {
                document.getElementById('submitFilter').click();
            });
        });
    </script>
    <?php endif; ?>

    <div class="container">
        <?php if (!empty($requests)): ?>
            <table class="table table-bordered table-striped table-hover mt-3">
                <thead>
                    <tr class="table-dark text-center">
                        <th>ID No.</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr class="text-center">
                            <td><?php echo htmlspecialchars($request['id']); ?></td>
                            <td><?php echo htmlspecialchars($request['name']); ?></td>
                            <td><button class="btn btn-info assess-btn" data-id="<?php echo htmlspecialchars($request['id']); ?>">Assess</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                No pending <?php echo htmlspecialchars($userType); ?> requests found for this department.
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.querySelectorAll('.assess-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                // Redirect with ID as query param
                window.location.href = `faculty-dashboard.php?id=${id}`;
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>