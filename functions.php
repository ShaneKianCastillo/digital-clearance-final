<?php 

    function checkUserSessionIsActive() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); 
        }
    }

    checkUserSessionIsActive();

    function openCon() {
        $con = mysqli_connect("localhost", "root", "", "digital-clearance-final");  
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        } 
        return $con;
    }

    function closeCon($con) {
        return mysqli_close($con);
    }

    function getUsers() {
        $con = openCon();

        $sql = "
            SELECT 'student' AS role, stud_id AS user_id, password 
            FROM students_cred
            UNION ALL
            SELECT 'department' AS role, dept_id AS user_id, password 
            FROM deptartments_cred
            UNION ALL
            SELECT 'dean' AS role, dean_id AS user_id, password 
            FROM dean_cred";
    
        $result = mysqli_query($con, $sql);    
        $users = [];
    
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $users[$row['user_id']] = [
                    'password' => $row['password'],
                    'role' => $row['role']
                ];
            }
        }

        closeCon($con);     
        return $users;
    }
    
    function checkLoginCredentials($userID, $password, $users) {
        return isset($users[$userID]) && $users[$userID]['password'] === md5($password);
    }
    
    function validateLoginCredentials($userID, $password) {
        $errorArray = [];
        $users = getUsers();
    
        if (!isset($users[$userID]) || $users[$userID]['password'] !== md5($password)) {
            $errorArray['credentials'] = 'Incorrect ID or password!';
        }

        return [$errorArray, $users];
    }

    function displayErrors($errors) {
        if (empty($errors)) {
            return ''; 
        }
        $output = '
        <div class="alert alert-danger alert-dismissible fade show mx-auto my-3" style="margin-bottom: 20px;" role="alert">
            <strong>System Errors:</strong> Please correct the following errors.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <hr>
            <ul>';
    
        foreach ($errors as $key => $error) {
            if (is_array($error)) {
                foreach ($error as $nestedError) {
                    if (is_array($nestedError)) {
                        $output .= '<li>' . htmlspecialchars(json_encode($nestedError)) . '</li>';
                    } else {
                        $output .= '<li>' . htmlspecialchars((string)$nestedError) . '</li>';
                    }
                }
            } else {
                $output .= '<li>' . htmlspecialchars((string)$error) . '</li>';
            }
        }
    
        $output .= '</ul></div>';
        return $output;
    }

    function getFacultyData($facultyID) {
        $con = openCon();

        $facultyID = mysqli_real_escape_string($con, $facultyID);
        $sql = "SELECT * FROM deptartments_cred WHERE dept_id = '$facultyID'";
        $result = mysqli_query($con, $sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $facultyData = mysqli_fetch_assoc($result);
            closeCon($con);
            return $facultyData;
        } else {
            closeCon($con);
            return null;
        }
    }

    function getStudentData($studentID) {
        $con = openCon();

        $studentID = mysqli_real_escape_string($con, $studentID);
        $sql = "SELECT * FROM students_cred WHERE stud_id = '$studentID'";
        $result = mysqli_query($con, $sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $facultyData = mysqli_fetch_assoc($result);
            closeCon($con);
            return $facultyData;
        } else {
            closeCon($con);
            return null;
        }
    }

    function getDeanData($deanID) {
        $con = openCon();

        $deanID = mysqli_real_escape_string($con, $deanID);
        $sql = "SELECT * FROM dean_cred WHERE dean_id = '$deanID'";
        $result = mysqli_query($con, $sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $facultyData = mysqli_fetch_assoc($result);
            closeCon($con);
            return $facultyData;
        } else {
            closeCon($con);
            return null;
        }
    }

    function addStudentUser($userID, $password, $name) {
        $con = openCon(); 
    
        if ($con) {
            $hashedPassword = md5($password); 
            $sql = "INSERT INTO students_cred (stud_id, password, name) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($con, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $userID, $hashedPassword, $name);
                if (mysqli_stmt_execute($stmt)) {
                    echo "New record created successfully.";
                } else {
                    echo "Error executing query: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Error preparing statement: " . mysqli_error($con);
            }
            closeCon($con);
        } else {
            echo "Failed to connect to the database.";
        }
    }

    function fetchDepartments() {
        $con = openCon(); 
    
        if ($con) {
            $sql = "SELECT dept_id, dept_name, employee_name FROM deptartments_cred";
            $result = mysqli_query($con, $sql);
    
            $departments = [];
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $departments[] = $row;
                }
            }
            closeCon($con); 
            return $departments;
        } else {
            echo "Failed to connect to the database.";
            return [];
        }
    }

    function updateDepartment($deptID, $deptName, $deptEmp) {
        $con = openCon();
    
        $deptID = mysqli_real_escape_string($con, $deptID);
        $deptName = mysqli_real_escape_string($con, $deptName);
        $deptEmp = mysqli_real_escape_string($con, $deptEmp);
    
        $sql = "UPDATE deptartments_cred SET dept_name = '$deptName', employee_name = '$deptEmp' WHERE dept_id = '$deptID'";
    
        if (mysqli_query($con, $sql)) {
            closeCon($con);  
            return true;  
        } else {
            closeCon($con);  
            return false; 
        }
    }

    function addStudentInfo($studID, $studName, $course, $contactNumber, $yearLevel) {
        $con = openCon();
    
        $studID = mysqli_real_escape_string($con, $studID);
        $studName = mysqli_real_escape_string($con, $studName);
        $course = mysqli_real_escape_string($con, $course);
        $contactNumber = mysqli_real_escape_string($con, $contactNumber);
        $yearLevel = mysqli_real_escape_string($con, $yearLevel);
    
        $sql = "INSERT INTO student_info (stud_id, stud_name, course, contact_number, year_level) 
                VALUES ('$studID', '$studName', '$course', '$contactNumber', '$yearLevel')";
    
        $result = mysqli_query($con, $sql);
    
        if ($result) {
            closeCon($con);
            return true; 
        } else {
            closeCon($con);
            return false; 
        }
    }

    function fetchStudentInfo($studID) {
        $con = openCon();
    
        // Sanitize the input to prevent SQL injection
        $studID = mysqli_real_escape_string($con, $studID);
    
        // Query to fetch the student record
        $sql = "SELECT * FROM student_info WHERE stud_id = '$studID'";
        $result = mysqli_query($con, $sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $student = mysqli_fetch_assoc($result); // Fetch the student record as an associative array
            closeCon($con);
            return $student; // Return the student record
        } else {
            closeCon($con);
            return null; // Return null if no record is found
        }
    }

    function addStudentClearance($studID) {
        $con = openCon();
        
        // Escape input to prevent SQL injection
        $studID = mysqli_real_escape_string($con, $studID);
        
        // Prepare the SQL query
        $sql = "INSERT INTO student_clearance (stud_id, Library, OSA, Guidance, `Foreign Affairs`, `Computer Lab`, `Program Chair`, Registrar, `Vice President`, Accounting, Dean) 
                VALUES ('$studID', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";
        
        // Execute the query and check if successful
        if (mysqli_query($con, $sql)) {
            echo "New clearance record added successfully for student ID: $studID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
        
        closeCon($con);
    }

    function addStudentComment($studID) {
        $con = openCon();
    
        // Escape the input to prevent SQL injection
        $studID = mysqli_real_escape_string($con, $studID);
    
        // Prepare the SQL query
        $sql = "INSERT INTO student_comment (stud_id, Library, OSA, Guidance, `Foreign Affairs`, `Computer Lab`, `Program Chair`, Registrar, `Vice President`, Accounting, Dean)
                VALUES ('$studID', '', '', '', '', '', '', '', '', '', '')";
    
        // Execute the query and check if successful
        if (mysqli_query($con, $sql)) {
            echo "New comment record added successfully for student ID: $studID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    
        closeCon($con);
    }

    function addStudentDate($studID) {
        $con = openCon();
    
        // Escape the input to prevent SQL injection
        $studID = mysqli_real_escape_string($con, $studID);
    
        // Prepare the SQL query
        $sql = "INSERT INTO student_date (stud_id, Library, OSA, Guidance, `Foreign Affairs`, `Computer Lab`, `Program Chair`, Registrar, `Vice President`, Accounting, Dean)
                VALUES ('$studID', '', '', '', '', '', '', '', '', '', '')";
    
        // Execute the query and check if successful
        if (mysqli_query($con, $sql)) {
            echo "New date record added successfully for student ID: $studID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    
        closeCon($con);
    }

  
    function getStudentClearanceData($studID) {
        $con = openCon();

        // Initialize an array to hold the department data
        $clearanceData = [];

        // Fetch department info (dept_id, dept_name, employee_name) from departments_cred table
        $queryDept = "SELECT dept_id, dept_name, employee_name FROM deptartments_cred";
        $resultDept = mysqli_query($con, $queryDept);

        if (!$resultDept) {
            die("Error fetching departments: " . mysqli_error($con));
        }

        // Fetch clearance status, date, and remarks for each department
        while ($rowDept = mysqli_fetch_assoc($resultDept)) {
            $deptID = $rowDept['dept_id'];
            $deptName = $rowDept['dept_name'];
            $signatory = $rowDept['employee_name'];

            // Fetch status from student_clearance
            $queryStatus = "SELECT `$deptName` AS status FROM student_clearance WHERE stud_id = '$studID'";
            $resultStatus = mysqli_query($con, $queryStatus);
            $statusRow = mysqli_fetch_assoc($resultStatus);
            $status = isset($statusRow['status']) && $statusRow['status'] == 1 ? 'Approved' : 'Declined';

            // Fetch date from student_date
            $queryDate = "SELECT `$deptName` AS date FROM student_date WHERE stud_id = '$studID'";
            $resultDate = mysqli_query($con, $queryDate);
            $dateRow = mysqli_fetch_assoc($resultDate);
            $date = isset($dateRow['date']) ? $dateRow['date'] : 'N/A';

            // Fetch remarks from student_comment
            $queryRemarks = "SELECT `$deptName` AS remarks FROM student_comment WHERE stud_id = '$studID'";
            $resultRemarks = mysqli_query($con, $queryRemarks);
            $remarksRow = mysqli_fetch_assoc($resultRemarks);
            $remarks = isset($remarksRow['remarks']) ? $remarksRow['remarks'] : 'No Remarks';

            // Store data in the array
            $clearanceData[] = [
                'dept_name' => $deptName,
                'signatory' => $signatory,
                'status' => $status,
                'date' => $date,
                'remarks' => $remarks
            ];
        }

        closeCon($con);
        return $clearanceData;
    }

    function countApprovals($studID) {
        $con = openCon();
    
        // Initialize count
        $approvalCount = 0;
    
        // Fetch the department columns (Library to Dean) for the student
        $query = "SELECT Library, OSA, Guidance, `Foreign Affairs`, `Computer Lab`, `Program Chair`, Registrar, `Vice President`, Accounting, Dean FROM student_clearance WHERE stud_id = '$studID'";
        $result = mysqli_query($con, $query);
    
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            
            // Iterate over each department column and count the ones (approved statuses)
            foreach ($row as $deptID => $status) {
                if ($status == 1) { // Only count `1` values (approved)
                    $approvalCount++;
                }
            }
        }
    
        closeCon($con);
        return $approvalCount;
    }


    
    
    
    
    
    

?>