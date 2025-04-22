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
    
    /*function validateLoginCredentials($userID, $password) {
        $con = openCon();
        $hashedPassword = md5($password);
        $errorArray = [];
        $users = [];
    
        // Query students_cred
        $sql = "SELECT stud_id AS id, password, 'student' AS role FROM students_cred WHERE stud_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['password'] === $hashedPassword) {
                $users[$userID] = ['role' => 'student'];
            }
        }
    
        // Query employees_cred
        $sql = "SELECT emp_id AS id, password, 'employee' AS role FROM employees_cred WHERE emp_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['password'] === $hashedPassword) {
                $users[$userID] = ['role' => 'employee'];
            }
        }
    
        // Query faculty_users
        $sql = "SELECT dept_id AS id, password, 'department' AS role FROM deptartments_cred WHERE dept_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['password'] === $hashedPassword) {
                $users[$userID] = ['role' => 'department'];
            }
        }

        // Query dean_cred
        $sql = "SELECT dean_id AS id, password, 'dean' AS role FROM dean_cred WHERE dean_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['password'] === $hashedPassword) {
                $users[$userID] = ['role' => 'dean'];
            }
        }
    
        // If user is not found
        if (empty($users)) {
            $errorArray['credentials'] = 'Incorrect ID or password!';
        }
    
        closeCon($con);
        return [$errorArray, $users];
    }*/

    function validateLoginCredentials($userID, $password) {
        $con = openCon();
        $hashedPassword = md5($password);
        $errorArray = [];
        $users = [];
    
        // Check students_cred
        $sql = "SELECT stud_id AS id, password, 'student' AS role FROM students_cred WHERE stud_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['password'] === $hashedPassword) {
                $users[$userID] = ['role' => 'student'];
            }
        }
    
        // Check employees_cred
        $sql = "SELECT emp_id AS id, password, 'employee' AS role FROM employees_cred WHERE emp_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['password'] === $hashedPassword) {
                $users[$userID] = ['role' => 'employee'];
            }
        }
    
        // Check deptartments_cred
        $sql = "SELECT dept_id AS id, password, dept_name, 'department' AS role FROM deptartments_cred WHERE dept_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['password'] === $hashedPassword) {
                // Check if this is the Principal
                if ($user['dept_name'] === 'Principal') {
                    $users[$userID] = ['role' => 'principal'];
                } else {
                    $users[$userID] = ['role' => 'department'];
                }
            }
        }
    
        // Check dean_cred
        $sql = "SELECT dean_id AS id, password, 'dean' AS role FROM dean_cred WHERE dean_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['password'] === $hashedPassword) {
                $users[$userID] = ['role' => 'dean'];
            }
        }
    
        // If user is not found
        if (empty($users)) {
            $errorArray['credentials'] = 'Incorrect ID or password!';
        }
    
        closeCon($con);
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

    function updateDepartment($deptID, $newDeptName, $deptEmp) {
        $con = openCon();
    
        $deptID = mysqli_real_escape_string($con, $deptID);
        $newDeptName = mysqli_real_escape_string($con, $newDeptName);
        $deptEmp = mysqli_real_escape_string($con, $deptEmp);
        $currentDeptQuery = "SELECT dept_name FROM deptartments_cred WHERE dept_id = '$deptID'";
        $result = mysqli_query($con, $currentDeptQuery);
    
        if (!$result || mysqli_num_rows($result) === 0) {
            echo "Error: Department not found or query failed: " . mysqli_error($con);
            closeCon($con);
            return false;
        }
    
        $row = mysqli_fetch_assoc($result);
        $currentDeptName = $row['dept_name'];
    
        $sql1 = "UPDATE deptartments_cred SET dept_name = '$newDeptName', employee_name = '$deptEmp' WHERE dept_id = '$deptID'";
        $sql2 = "ALTER TABLE student_clearance CHANGE `$currentDeptName` `$newDeptName` VARCHAR(255)";
        $sql3 = "ALTER TABLE student_comment CHANGE `$currentDeptName` `$newDeptName` VARCHAR(255)";
        $sql4 = "ALTER TABLE student_date CHANGE `$currentDeptName` `$newDeptName` VARCHAR(255)";
    
        if (mysqli_query($con, $sql1)) {
            if (mysqli_query($con, $sql2) && mysqli_query($con, $sql3) && mysqli_query($con, $sql4)) {
                closeCon($con);  
                return true;  
            } else {
                echo "Error updating related tables: " . mysqli_error($con);
                closeCon($con);  
                return false;
            }
        } else {
            echo "Error updating department credentials: " . mysqli_error($con);
            closeCon($con);  
            return false;
        }
    }
    
    function addStudentInfo($studID, $studName, $course, $contactNumber, $yearLevel, $foreigner = 0) {
        $con = openCon();
    
        $studID = mysqli_real_escape_string($con, $studID);
        $studName = mysqli_real_escape_string($con, $studName);
        $course = mysqli_real_escape_string($con, $course);
        $contactNumber = mysqli_real_escape_string($con, $contactNumber);
        $yearLevel = mysqli_real_escape_string($con, $yearLevel);
        $foreigner = mysqli_real_escape_string($con, $foreigner);
    
        $sql = "INSERT INTO student_info (stud_id, stud_name, course, contact_number, year_level, foreigner) 
                VALUES ('$studID', '$studName', '$course', '$contactNumber', '$yearLevel', '$foreigner')";
    
        $result = mysqli_query($con, $sql);
    
        if ($result) {
            closeCon($con);
            return true; 
        } else {
            echo "Error: " . mysqli_error($con);
            closeCon($con);
            return false;
        }
    }

    function fetchStudentInfo($studID) {
        $con = openCon();
    
        $studID = mysqli_real_escape_string($con, $studID);
        $sql = "SELECT * FROM student_info WHERE stud_id = '$studID'";
        $result = mysqli_query($con, $sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $student = mysqli_fetch_assoc($result); 
            closeCon($con);
            return $student; 
        } else {
            closeCon($con);
            return null; 
        }
    }

    function addStudentClearance($studID) {
        $con = openCon();
        
        // Fetch student info to check foreigner status
        $studentInfo = fetchStudentInfo($studID);
        $isForeigner = isset($studentInfo['foreigner']) && $studentInfo['foreigner'] == 1;
        
        $deptQuery = "SELECT dept_name FROM deptartments_cred WHERE dept_name != 'Dean' limit 9";
        $result = mysqli_query($con, $deptQuery);
    
        if (!$result) {
            echo "Error fetching departments: " . mysqli_error($con);
            closeCon($con);
            return;
        }
    
        $columns = [];
        $values = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $deptName = $row['dept_name'];
            $columns[] = "`" . mysqli_real_escape_string($con, $deptName) . "`";
            
            // Set Foreign Affairs to 3 if student is foreigner, otherwise 0
            if ($isForeigner && $deptName == 'Foreign Affairs') {
                $values[] = 3;
            } else {
                $values[] = 0;
            }
        }
    
        $columnsStr = implode(", ", $columns);
        $valuesStr = implode(", ", $values);
        
        // Add Dean column
        $columnsStr .= ", `Dean`";
        $valuesStr .= ", 0";
    
        $sql = "INSERT INTO student_clearance (stud_id, $columnsStr) 
                VALUES ('$studID', $valuesStr)";
    
        if (mysqli_query($con, $sql)) {
            echo "New clearance record added successfully for student ID: $studID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    
        closeCon($con);
    }
    
    function addStudentRequest($studID) {
        $con = openCon();
        
        // Fetch student info to check foreigner status
        $studentInfo = fetchStudentInfo($studID);
        $isForeigner = isset($studentInfo['foreigner']) && $studentInfo['foreigner'] == 1;
        
        $deptQuery = "SELECT dept_name FROM deptartments_cred WHERE dept_name != 'Dean' LIMIT 9";
        $result = mysqli_query($con, $deptQuery);
    
        if (!$result) {
            echo "Error fetching departments: " . mysqli_error($con);
            closeCon($con);
            return;
        }
    
        $columns = [];
        $values = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $deptName = $row['dept_name'];
            $columns[] = "`" . mysqli_real_escape_string($con, $deptName) . "`";
            
            // Set Foreign Affairs to 3 if student is foreigner, otherwise 0
            if ($isForeigner && $deptName == 'Foreign Affairs') {
                $values[] = 3;
            } else {
                $values[] = 0;
            }
        }
    
        $columnsStr = implode(", ", $columns);
        $valuesStr = implode(", ", $values);
        
        // Add Dean column
        $columnsStr .= ", `Dean`";
        $valuesStr .= ", 0";
    
        $sql = "INSERT INTO student_request (stud_id, $columnsStr) 
                VALUES ('$studID', $valuesStr)";
    
        if (mysqli_query($con, $sql)) {
            echo "New request record added successfully for student ID: $studID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    
        closeCon($con);
    }
    
    function addStudentComment($studID) {
        $con = openCon();
    
        $studID = mysqli_real_escape_string($con, $studID);
        $deptQuery = "SELECT dept_name FROM deptartments_cred WHERE dept_name != 'Dean' limit 9";
        $result = mysqli_query($con, $deptQuery);
    
        if (!$result) {
            echo "Error fetching departments: " . mysqli_error($con);
            closeCon($con);
            return;
        }
    
        $departments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $departments[] = "`" . mysqli_real_escape_string($con, $row['dept_name']) . "`";
        }

        $columns = implode(", ", $departments);
        $emptyValues = implode(", ", array_fill(0, count($departments), "''"));
        $columns .= ", `Dean`";
        $emptyValues .= ", ''";
        $sql = "INSERT INTO student_comment (stud_id, $columns) 
                VALUES ('$studID', $emptyValues)";
    
        if (mysqli_query($con, $sql)) {
            echo "New comment record added successfully for student ID: $studID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    
        closeCon($con);
    }

    function addStudentDate($studID) {
        $con = openCon();
    
        $studID = mysqli_real_escape_string($con, $studID);
        $deptQuery = "SELECT dept_name FROM deptartments_cred WHERE dept_name != 'Dean' limit 9";
        $result = mysqli_query($con, $deptQuery);
    
        if (!$result) {
            echo "Error fetching departments: " . mysqli_error($con);
            closeCon($con);
            return;
        }
    
        $departments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $departments[] = "`" . mysqli_real_escape_string($con, $row['dept_name']) . "`";
        }

        $columns = implode(", ", $departments);
        $emptyValues = implode(", ", array_fill(0, count($departments), "''"));
        $columns .= ", `Dean`";
        $emptyValues .= ", ''";

        $sql = "INSERT INTO student_date (stud_id, $columns) 
                VALUES ('$studID', $emptyValues)";
    
        if (mysqli_query($con, $sql)) {
            echo "New date record added successfully for student ID: $studID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    
        closeCon($con);
    }

    function getStudentClearanceData($studID) {
        $con = openCon();
    
        $clearanceData = [];
        $queryDept = "SELECT dept_id, dept_name, employee_name FROM deptartments_cred LIMIT 9";
        $resultDept = mysqli_query($con, $queryDept);
    
        if (!$resultDept) {
            die("Error fetching departments: " . mysqli_error($con));
        }
    
        while ($rowDept = mysqli_fetch_assoc($resultDept)) {
            $deptID = $rowDept['dept_id'];
            $deptName = $rowDept['dept_name'];
            $signatory = $rowDept['employee_name'];
    
            $queryStatus = "SELECT `$deptName` AS status FROM student_clearance WHERE stud_id = '$studID'";
            $resultStatus = mysqli_query($con, $queryStatus);
            $statusRow = mysqli_fetch_assoc($resultStatus);
            
            $status = '';
            if (!isset($statusRow['status'])) {
                $status = 'N/A';
            } else {
                switch ($statusRow['status']) {
                    case 1:
                        $status = 'Approved';
                        break;
                    case 2:
                        $status = 'Declined';
                        break;
                    default:
                        $status = 'N/A';
                }
            }
    
            $queryDate = "SELECT `$deptName` AS date FROM student_date WHERE stud_id = '$studID'";
            $resultDate = mysqli_query($con, $queryDate);
            $dateRow = mysqli_fetch_assoc($resultDate);
            $date = isset($dateRow['date']) ? $dateRow['date'] : 'N/A';
    
            $queryRemarks = "SELECT `$deptName` AS remarks FROM student_comment WHERE stud_id = '$studID'";
            $resultRemarks = mysqli_query($con, $queryRemarks);
            $remarksRow = mysqli_fetch_assoc($resultRemarks);
            $remarks = isset($remarksRow['remarks']) ? $remarksRow['remarks'] : 'No Remarks';
    
            $clearanceData[] = [
                'dept_name' => $deptName,
                'signatory' => $signatory,
                'status' => $status,
                'date' => $date,
                'remarks' => $remarks
            ];
        }
    
        $queryDean = "SELECT dean_name FROM dean_cred LIMIT 1";
        $resultDean = mysqli_query($con, $queryDean);
    
        if ($resultDean) {
            $rowDean = mysqli_fetch_assoc($resultDean);
            $deanName = $rowDean['dean_name'];
    
            $queryStatus = "SELECT Dean AS status FROM student_clearance WHERE stud_id = '$studID'";
            $resultStatus = mysqli_query($con, $queryStatus);
            $statusRow = mysqli_fetch_assoc($resultStatus);
            
            // Apply same logic to Dean status
            $status = '';
            if (!isset($statusRow['status'])) {
                $status = 'N/A';
            } else {
                switch ($statusRow['status']) {
                    case 1:
                        $status = 'Approved';
                        break;
                    case 2:
                        $status = 'Declined';
                        break;
                    default:
                        $status = 'N/A';
                }
            }
    
            $queryDate = "SELECT Dean AS date FROM student_date WHERE stud_id = '$studID'";
            $resultDate = mysqli_query($con, $queryDate);
            $dateRow = mysqli_fetch_assoc($resultDate);
            $date = isset($dateRow['date']) ? $dateRow['date'] : 'N/A';
    
            $queryRemarks = "SELECT Dean AS remarks FROM student_comment WHERE stud_id = '$studID'";
            $resultRemarks = mysqli_query($con, $queryRemarks);
            $remarksRow = mysqli_fetch_assoc($resultRemarks);
            $remarks = isset($remarksRow['remarks']) ? $remarksRow['remarks'] : 'No Remarks';
    
            $clearanceData[] = [
                'dept_name' => 'Dean',
                'signatory' => $deanName,
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
    
        $studID = mysqli_real_escape_string($con, $studID);
        $deptQuery = "SELECT dept_name FROM deptartments_cred LIMIT 9"; 
        $result = mysqli_query($con, $deptQuery);
    
        if (!$result) {
            echo "Error fetching departments: " . mysqli_error($con);
            closeCon($con);
            return 0; 
        }

        $departments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $departments[] = "`" . mysqli_real_escape_string($con, $row['dept_name']) . "`";
        }
        
        $departments[] = "`Dean`";
        $columns = implode(", ", $departments);
        $query = "SELECT $columns FROM student_clearance WHERE stud_id = '$studID'";
        $result = mysqli_query($con, $query);
        $approvalCount = 0;
    
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            foreach ($row as $status) {
                if ($status == 1) { 
                    $approvalCount++;
                }
            }
        } else {
            echo "Error fetching student clearance: " . mysqli_error($con);
        }
        closeCon($con);
        return $approvalCount;
    }
    
    function approveStudent($studID, $deptName) {
        $con = openCon();
        $con->begin_transaction();
        $success = false;
        
        try {
            // 1. Update clearance status
            $query1 = "UPDATE student_clearance SET `$deptName` = 1 WHERE stud_id = ?";
            $stmt1 = $con->prepare($query1);
            $stmt1->bind_param("s", $studID);
            $stmt1->execute();
            
            // 2. Clear the comment
            $query2 = "UPDATE student_comment SET `$deptName` = '' WHERE stud_id = ?";
            $stmt2 = $con->prepare($query2);
            $stmt2->bind_param("s", $studID);
            $stmt2->execute();
            
            $con->commit();
            $success = true;
        } catch (Exception $e) {
            $con->rollback();
            error_log("Student approval error: " . $e->getMessage());
        } finally {
            if (isset($stmt1)) $stmt1->close();
            if (isset($stmt2)) $stmt2->close();
            closeCon($con);
        }
        
        return $success;
    }

    function approveDate($studID, $deptName, $date) {
        $con = openCon();
    
        $query = "UPDATE student_date SET `$deptName` = ? WHERE stud_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("si", $date, $studID);
        $stmt->execute();
        $stmt->close();
        $con->close();
    }

    function storeCommentAndReset($studID, $deptName, $comment) {
        $con = openCon();
    
        $con->begin_transaction();
        try {
            // Store the comment
            $query1 = "UPDATE student_comment SET `$deptName` = ? WHERE stud_id = ?";
            $stmt1 = $con->prepare($query1);
            $stmt1->bind_param("si", $comment, $studID);
            $stmt1->execute();
    
            // Reset the date
            $query2 = "UPDATE student_date SET `$deptName` = '' WHERE stud_id = ?";
            $stmt2 = $con->prepare($query2);
            $stmt2->bind_param("i", $studID);
            $stmt2->execute();
    
            // Set status to declined (2)
            $query3 = "UPDATE student_clearance SET `$deptName` = 2 WHERE stud_id = ?";
            $stmt3 = $con->prepare($query3);
            $stmt3->bind_param("i", $studID);
            $stmt3->execute();
    
            // Reset the request status so they can request again
            $query4 = "UPDATE student_request SET `$deptName` = 0 WHERE stud_id = ?";
            $stmt4 = $con->prepare($query4);
            $stmt4->bind_param("i", $studID);
            $stmt4->execute();
    
            $con->commit();
    
            $stmt1->close();
            $stmt2->close();
            $stmt3->close();
            $stmt4->close();
    
        } catch (Exception $e) {
            $con->rollback();
            throw $e;
        }
        $con->close();
    }
    
    function isStudentEligibleForDepartment($studID, $currentDeptName, $orderedDepartments) {
        $con = openCon();
        $studentInfo = fetchStudentInfo($studID);
        $isForeigner = isset($studentInfo['foreigner']) && $studentInfo['foreigner'] == 1;
        
        // Remove Foreign Affairs from order if student is foreigner
        if ($isForeigner) {
            $orderedDepartments = array_filter($orderedDepartments, function($dept) {
                return $dept != 'Foreign Affairs';
            });
            $orderedDepartments = array_values($orderedDepartments); // Reindex array
        }
    
        foreach ($orderedDepartments as $department) {
            if ($department === $currentDeptName) {
                break;
            }
    
            $queryApprovalStatus = "SELECT `$department` AS status FROM student_clearance WHERE stud_id = ?";
            $stmt = $con->prepare($queryApprovalStatus);
            $stmt->bind_param("i", $studID);
            $stmt->execute();
            $stmt->bind_result($status);
            $stmt->fetch();
            $stmt->close();
    
            if ($status != 1) {
                closeCon($con);
                return false;
            }
        }
    
        closeCon($con);
        return true;
    }

    function fetchStudentComment($studID, $deptName) {
        $con = openCon();
        
        $validDepartments = getDepartmentOrder();
        
        if (!in_array($deptName, $validDepartments)) {
            $deptName = 'Library'; 
        }
    
        // Construct the query with the validated deptName
        $commentQuery = "SELECT `$deptName` AS comment FROM student_comment WHERE stud_id = ?";
        $stmt = $con->prepare($commentQuery);
        $stmt->bind_param("i", $studID);
        $stmt->execute();
        $stmt->bind_result($existingComment);
        $stmt->fetch();
        $stmt->close();
        closeCon($con);
    
        return $existingComment ?? '';
    }
    

    function checkIfAllPreviousDepartmentsApproved($studID) {
        $con = openCon();
    
        $query = "SHOW COLUMNS FROM student_clearance";
        $result = mysqli_query($con, $query);
        $departmentColumns = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $column = $row['Field'];
            if ($column != 'stud_id') { 
                $departmentColumns[] = $column;
            }
        }
    
        $query = "SELECT * FROM student_clearance WHERE stud_id = '$studID'";
        $result = mysqli_query($con, $query);
        $data = mysqli_fetch_assoc($result);
        if (!$data) {
            closeCon($con);
            return false;
        }
        
        closeCon($con);
        
        $allApproved = true;
        foreach ($departmentColumns as $department) {
            if ($data[$department] != 1) {
                $allApproved = false;
                break;
            }
        }
        
        return $allApproved;
    }

    function getSelectedClearanceData($studID) {
        $con = openCon();
        $query = "SELECT `Program Chair`, `Dean`, `Registrar`, `Vice President`, `Accounting` FROM student_clearance WHERE stud_id = ?";
        
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $studID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = $result->fetch_assoc(); 
        
        $stmt->close(); 
        closeCon($con); 
    
        return $data; 
    }

    function getSelectedClearanceDataDate($studID) {
        $con = openCon();
        $query = "SELECT `Program Chair`, `Dean`, `Registrar`, `Vice President`, `Accounting` FROM student_date WHERE stud_id = ?";
        
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $studID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = $result->fetch_assoc(); 
        
        $stmt->close(); 
        closeCon($con); 
    
        return $data; 
    }

    function saveSemesterAndSchoolYear($semester, $school_year) {
        $con = openCon(); 
    
        $query = "SELECT * FROM dean_cred LIMIT 1";
        $stmt = $con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $query = "UPDATE dean_cred SET semester = ?, school_year = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ss", $semester, $school_year);
        } else {
            $query = "INSERT INTO dean_cred (semester, school_year) VALUES (?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ss", $semester, $school_year);
        }
    
        $success = $stmt->execute(); 
    
        $stmt->close();
        closeCon($con); 
    
        return $success;
    }

    function getSemesterAndSchoolYear() {
        $con = openCon(); 
        $query = "SELECT semester, school_year FROM dean_cred LIMIT 1";
    
        $stmt = $con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
    
        $stmt->close();
        closeCon($con); 
    
        if ($data) {
            if ($data['semester'] === 'First Semester') {
                $data['semester'] = '1st Semester';
            } elseif ($data['semester'] === 'Second Semester') {
                $data['semester'] = '2nd Semester';
            }
        } else {
            $data = ['semester' => 'N/A', 'school_year' => 'N/A'];
        }
    
        return $data;
    }

    function changePassword($userID, $oldPass, $newPass, $role) {
        $con = openCon();
    
        switch($role) {
            case 'dean':
                $table = 'dean_cred';
                $idField = 'dean_id';
                break;
            case 'department':
                $table = 'deptartments_cred';
                $idField = 'dept_id';
                break;
            case 'student':
                $table = 'students_cred';
                $idField = 'stud_id';
                break;
            case 'employee':
                $table = 'employees_cred';
                $idField = 'emp_id';
                break;
            default:
                return false; 
        }
        
        $query = "SELECT password FROM $table WHERE $idField = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();   
        
        if ($result->num_rows === 0) {
            return false;
        }
        
        $data = $result->fetch_assoc();
        $storedHash = $data['password'];
        
        if (md5($oldPass) === $storedHash) {
            $newHash = md5($newPass);
            
            $query = "UPDATE $table SET password = ? WHERE $idField = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ss", $newHash, $userID);
            
            if ($stmt->execute()) {
                return true; 
            }
        }
        
        return false; 
    }

    function determineUserType($userID) {
        $con = openCon();
        
        $stmt = $con->prepare("SELECT stud_id FROM students_cred WHERE stud_id = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return 'student';
        
        $stmt = $con->prepare("SELECT dean_id FROM dean_cred WHERE dean_id = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return 'dean';

        $stmt = $con->prepare("SELECT emp_id FROM employees_cred WHERE emp_id = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return 'employee';
        
        $stmt = $con->prepare("SELECT dept_id FROM departments_cred WHERE dept_id = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return 'department';
        
        return false; 
    }

    function getOrdinal($number) {
        if (!in_array(($number % 100), [11, 12, 13])) {
            switch ($number % 10) {
                case 1: return $number . "st";
                case 2: return $number . "nd";
                case 3: return $number . "rd";
            }
        }
        return $number . "th";
    }

    function getStatusClass($status) {
        switch ($status) {
            case 1: return 'text-success';
            case 2: return 'text-danger';
            default: return 'text-secondary';
        }
    }
    
    function getStatusText($status) {
        switch ($status) {
            case 1: return 'Approved';
            case 2: return 'Declined';
            default: return 'N/A';
        }
    }

    function formatDate($dateString) {
        if (empty($dateString)) return ''; 
    
        $timestamp = strtotime($dateString); 
        return date("m-d-y", $timestamp); 
    }

    function addEmployeeUser($userID, $password, $name) {
        $con = openCon(); 
    
        if ($con) {
            $hashedPassword = md5($password); 
            $sql = "INSERT INTO employees_cred (emp_id, password, name) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($con, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $userID, $hashedPassword, $name);
                if (mysqli_stmt_execute($stmt)) {
                    echo "New employee record created successfully.";
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

    function addEmployeeInfo($empID, $empName, $department, $position, $category, $status, $gradeLevel = 0) {
        $con = openCon();
    
        $empID = mysqli_real_escape_string($con, $empID);
        $empName = mysqli_real_escape_string($con, $empName);
        $department = mysqli_real_escape_string($con, $department);
        $position = mysqli_real_escape_string($con, $position);
        $category = mysqli_real_escape_string($con, $category);
        $status = mysqli_real_escape_string($con, $status);
        $gradeLevel = mysqli_real_escape_string($con, $gradeLevel);
    
        $sql = "INSERT INTO employee_info (emp_id, name, department, position, category, status, gradeLevel) 
                VALUES ('$empID', '$empName', '$department', '$position', '$category', '$status', '$gradeLevel')";
    
        $result = mysqli_query($con, $sql);
    
        if ($result) {
            closeCon($con);
            return true; 
        } else {
            closeCon($con);
            return false; 
        }
    }

    function getEmployeeData($employeeID) {
        $con = openCon();
        $sql = "SELECT * FROM employee_info WHERE emp_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $employeeID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        closeCon($con);
        return $result;
    }

    function fetchEmployeeInfo($empID) {
        $con = openCon();
        $sql = "SELECT *, hasRequested FROM employee_info WHERE emp_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $empID);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        closeCon($con);
        return $employee;
    }

    function checkIfStudent($userID) {
        $con = openCon();
    
        $query = "SELECT * FROM students_cred WHERE stud_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $isStudent = $result->num_rows > 0; 
    
        closeCon($con); 
    
        return $isStudent;
    }

    function processStudentSearch($studID, $facultyData) {
        $result = [
            'studID' => $studID,
            'studName' => '',
            'studCourse' => '',
            'commentAreaValue' => '',
            'studentFound' => false,
            'errorMessage' => '',
            'orderedDepartments' => []
        ];
    
        $student = fetchStudentInfo($studID);
        
        if (!$student) {
            $result['errorMessage'] = "No student found with ID: " . htmlspecialchars($studID);
            return $result;
        }
    
        $result['studName'] = $student['stud_name'];
        $result['studCourse'] = $student['course'];
    
        $con = openCon();
        
        $deptQuery = "SELECT dept_name FROM deptartments_cred 
                     WHERE (type = 'Student' OR type = 'Both') AND dept_name != 'Dean'
                     ORDER BY 
                         CASE 
                             WHEN dept_name = 'Library' THEN 1
                             WHEN dept_name = 'OSA' THEN 2
                             WHEN dept_name = 'Guidance' THEN 3
                             WHEN dept_name = 'Foreign Affairs' THEN 4
                             WHEN dept_name = 'Computer Lab' THEN 5
                             WHEN dept_name = 'Program Chair' THEN 6
                             WHEN dept_name = 'Registrar' THEN 7
                             WHEN dept_name = 'Vice President' THEN 8
                             WHEN dept_name = 'Accounting' THEN 9
                             ELSE 10
                         END";
        $queryResult = mysqli_query($con, $deptQuery);
    
        if (!$queryResult) {
            $result['errorMessage'] = "Error fetching departments: " . mysqli_error($con);
            closeCon($con);
            return $result;
        }
    
        while ($row = mysqli_fetch_assoc($queryResult)) {
            $result['orderedDepartments'][] = $row['dept_name'];
        }
    
        $isForeigner = isset($student['foreigner']) && $student['foreigner'] == 1;
        if ($isForeigner) {
            $result['orderedDepartments'] = array_filter($result['orderedDepartments'], function($dept) {
                return $dept != 'Foreign Affairs';
            });
            $result['orderedDepartments'] = array_values($result['orderedDepartments']); // Reindex array
        }
    
        $deptName = $facultyData['dept_name'] ?? '';
        
        if (!in_array($deptName, $result['orderedDepartments'])) {
            $result['errorMessage'] = "Department configuration error: $deptName not found in clearance flow";
            closeCon($con);
            return $result;
        }
    
        $currentPos = array_search($deptName, $result['orderedDepartments']);
        
        if ($currentPos !== false) {
            for ($i = 0; $i < $currentPos; $i++) {
                $prevDept = $result['orderedDepartments'][$i];
                $query = "SELECT `$prevDept` FROM student_clearance WHERE stud_id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("s", $studID);
                $stmt->execute();
                $stmt->bind_result($status);
                $stmt->fetch();
                $stmt->close();
                
                if ($status != 1) {
                    $prevDeptName = htmlspecialchars($prevDept);
                    $result['errorMessage'] = htmlspecialchars($student['stud_name']) . 
                        " is not yet approved by $prevDeptName department.";
                    closeCon($con);
                    return $result;
                }
            }
        }
    
        $hasRequested = hasStudentRequested($studID, $deptName);
        if (!$hasRequested) {
            $result['errorMessage'] = "Student hasn't requested clearance from this department";
            closeCon($con);
            return $result;
        }
    
        $result['studentFound'] = true;
        $result['commentAreaValue'] = fetchStudentComment($studID, $deptName);
    
        closeCon($con);
        return $result;
    }

    function processStudentDeanSearch($studID, $deanData) {
        $result = [
            'studID' => $studID,
            'studName' => '',
            'studCourse' => '',
            'commentAreaValue' => '',
            'studentFound' => false,
            'errorMessage' => '',
            'requiredDeptsApproved' => false,
            'orderedDepartments' => getDepartmentOrder()
        ];
    
        $student = fetchStudentInfo($studID);
        
        if (!$student) {
            $result['errorMessage'] = "No student found with ID: " . htmlspecialchars($studID);
            return $result;
        }
    
        $result['studName'] = $student['stud_name'];
        $result['studCourse'] = $student['course'];
    
        $con = openCon();
    
        $deptOrder = getDepartmentOrder();
        $deanPosition = array_search('Dean', $deptOrder);
        
        $allRequiredApproved = true;
        for ($i = 0; $i < $deanPosition; $i++) {
            $dept = $deptOrder[$i];
            
            if (isset($student['foreigner']) && $student['foreigner'] == 1 && $dept == 'Foreign Affairs') {
                continue;
            }
    
            $status = getDepartmentClearanceStatus($con, $studID, $dept);
            if ($status != 1) {
                $allRequiredApproved = false;
                $result['errorMessage'] = htmlspecialchars($student['stud_name']) . 
                    " is not yet approved by $dept department.";
                break;
            }
        }
    
        $result['requiredDeptsApproved'] = $allRequiredApproved;
    
        if ($allRequiredApproved) {
            $hasRequested = hasStudentRequested($studID, 'Dean');
            
            if ($hasRequested) {
                $result['studentFound'] = true;
                $result['commentAreaValue'] = fetchStudentComment($studID, 'Dean');
            } else {
                $result['errorMessage'] = "Student hasn't requested Dean clearance";
            }
        }
    
        closeCon($con);
        return $result;
    }

    function getDepartmentClearanceStatus($con, $studID, $deptName) {
        $status = null;
        
        if (!$con || empty($studID) || empty($deptName)) {
            error_log("Invalid parameters provided to getDepartmentClearanceStatus");
            return $status;
        }
    
        try {
            $escapedDeptName = mysqli_real_escape_string($con, $deptName);
            
            $query = "SELECT `$escapedDeptName` FROM student_clearance WHERE stud_id = ?";
            $stmt = $con->prepare($query);
            
            if (!$stmt) {
                error_log("Prepare failed: " . $con->error);
                return $status;
            }
    
            $stmt->bind_param("s", $studID);
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return $status;
            }
    
            $stmt->bind_result($status);
            
            if (!$stmt->fetch()) {
                $status = null;
            }
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error in getDepartmentClearanceStatus: " . $e->getMessage());
            $status = null;
        }
        
        return $status;
    }

    /*function processEmployeeSearch($empID, $facultyData) {
        $con = openCon();
        $result = [
            'empID' => $empID,
            'empName' => '',
            'empDepartment' => '',
            'employeeFound' => false,
            'errorMessage' => '',
            'commentAreaValue' => ''
        ];
    
        // Define department approval order
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
    
        // First check employee_info table
        $sql = "SELECT * FROM employee_info WHERE emp_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $empID);
        $stmt->execute();
        $employee = $stmt->get_result()->fetch_assoc();
        
        if (!$employee) {
            $result['errorMessage'] = "No employee found with ID: " . htmlspecialchars($empID);
            closeCon($con);
            return $result;
        }

        $result['empName'] = $employee['name'];
        $result['empDepartment'] = $employee['department'];
    
        // Check if employee is approved by previous departments
        $currentDeptIndex = array_search($facultyData['dept_name'], $orderedDepartments);
        
        if ($currentDeptIndex !== false && $currentDeptIndex > 0) {
            for ($i = 0; $i < $currentDeptIndex; $i++) {
                $prevDept = $orderedDepartments[$i];
                $approvalSql = "SELECT `$prevDept` FROM employee_clearance WHERE emp_id = ?";
                $approvalStmt = $con->prepare($approvalSql);
                $approvalStmt->bind_param("s", $empID);
                $approvalStmt->execute();
                $approvalStmt->bind_result($approved);
                $approvalStmt->fetch();
                $approvalStmt->close();
                
                if (!$approved) {
                    $result['errorMessage'] = htmlspecialchars($employee['name']) . 
                        " is not yet approved by " . $prevDept . " department.";
                    closeCon($con);
                    return $result;
                }
            }
        }
    
        $result['employeeFound'] = true;
        
        // Get existing comment if any
        $commentSql = "SELECT `".$facultyData['dept_name']."` FROM employee_comment WHERE emp_id = ?";
        $commentStmt = $con->prepare($commentSql);
        $commentStmt->bind_param("s", $empID);
        $commentStmt->execute();
        $commentStmt->bind_result($comment);
        $commentStmt->fetch();
        $result['commentAreaValue'] = $comment ?? '';
        
        $commentStmt->close();
        closeCon($con);
        return $result;

        if ($employee) {
            $hasRequested = hasEmployeeRequested($empID, $facultyData['dept_name']);
            if (!$hasRequested) {
                $errorMessage = "Employee found but hasn't requested clearance";
                $employeeFound = false;
            }
        }
        
        return [
            'empID' => $empID,
            'empName' => $employee ? $employee['name'] : '',
            'empDepartment' => $employee ? $employee['department'] : '',
            'employeeFound' => $employeeFound,
            'errorMessage' => $errorMessage,
            'commentAreaValue' => $commentAreaValue ?? '',
            'hasRequested' => $hasRequested ?? false
        ];
    }*/

    /*function processEmployeeSearch($empID, $facultyData) {
        $con = openCon();
        $result = [
            'empID' => $empID,
            'empName' => '',
            'empDepartment' => '',
            'employeeFound' => false,
            'errorMessage' => '',
            'commentAreaValue' => ''
        ];
    
        // First check employee_info table
        $sql = "SELECT * FROM employee_info WHERE emp_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $empID);
        $stmt->execute();
        $employee = $stmt->get_result()->fetch_assoc();
        
        if (!$employee) {
            $result['errorMessage'] = "No employee found with ID: " . htmlspecialchars($empID);
            closeCon($con);
            return $result;
        }
    
        $result['empName'] = $employee['name'];
        $result['empDepartment'] = $employee['department'];
    
        $hasRequested = hasEmployeeRequested($empID, $facultyData['dept_name']);
        if (!$hasRequested) {
            $result['errorMessage'] = "Employee hasn't requested clearance from this department";
            closeCon($con);
            return $result;
        }
    
        $result['employeeFound'] = true;
        
        // Get existing comment if any
        $commentSql = "SELECT `".$facultyData['dept_name']."` FROM employee_comment WHERE emp_id = ?";
        $commentStmt = $con->prepare($commentSql);
        $commentStmt->bind_param("s", $empID);
        $commentStmt->execute();
        $commentStmt->bind_result($comment);
        $commentStmt->fetch();
        $result['commentAreaValue'] = $comment ?? '';
        
        $commentStmt->close();
        closeCon($con);
        return $result;
    }*/

    function processEmployeeSearch($empID, $facultyData) {
        $con = openCon();
        $result = [
            'empID' => $empID,
            'empName' => '',
            'empDepartment' => '',
            'employeeFound' => false,
            'errorMessage' => '',
            'commentAreaValue' => ''
        ];
    
        // First check employee_info table
        $sql = "SELECT * FROM employee_info WHERE emp_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $empID);
        $stmt->execute();
        $employee = $stmt->get_result()->fetch_assoc();
        
        if (!$employee) {
            $result['errorMessage'] = "No employee found with ID: " . htmlspecialchars($empID);
            closeCon($con);
            return $result;
        }
    
        $result['empName'] = $employee['name'];
        $result['empDepartment'] = $employee['department'];
    
        $hasRequested = hasEmployeeRequested($empID, $facultyData['dept_name']);
        if (!$hasRequested) {
            $result['errorMessage'] = "Employee hasn't requested clearance from this department";
            closeCon($con);
            return $result;
        }
    
        $result['employeeFound'] = true;
        
        // Get existing comment if any
        $commentSql = "SELECT `".$facultyData['dept_name']."` FROM employee_comment WHERE emp_id = ?";
        $commentStmt = $con->prepare($commentSql);
        $commentStmt->bind_param("s", $empID);
        $commentStmt->execute();
        $commentStmt->bind_result($comment);
        $commentStmt->fetch();
        $result['commentAreaValue'] = $comment ?? '';
        
        $commentStmt->close();
        closeCon($con);
        return $result;
    }

    function addEmployeeClearance($empID) {
        $con = openCon();
        
        // Escape employee ID
        $empID = mysqli_real_escape_string($con, $empID);
        
        // Define departments in exact order
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
        
        // Prepare columns and values
        $columns = [];
        $values = [];
        
        foreach ($orderedDepartments as $dept) {
            $escapedDept = mysqli_real_escape_string($con, $dept);
            $columns[] = "`$escapedDept`";
            $values[] = "0"; // Default value
        }
        
        $columnsStr = implode(", ", $columns);
        $valuesStr = implode(", ", $values);
        
        // Create the clearance record
        $sql = "INSERT INTO employee_clearance (emp_id, $columnsStr) 
                VALUES ('$empID', $valuesStr)";
        
        if (mysqli_query($con, $sql)) {
            echo "New employee clearance record added successfully for ID: $empID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
        
        closeCon($con);
    }

    function addEmployeeDate($empID) {
        $con = openCon();
        
        // Escape employee ID
        $empID = mysqli_real_escape_string($con, $empID);
        
        // Define departments in exact order
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
        
        // Prepare columns and values
        $columns = [];
        $values = [];
        
        foreach ($orderedDepartments as $dept) {
            $escapedDept = mysqli_real_escape_string($con, $dept);
            $columns[] = "`$escapedDept`";
            $values[] = "''"; // Empty string for date
        }
        
        $columnsStr = implode(", ", $columns);
        $valuesStr = implode(", ", $values);
        
        // Create the date record
        $sql = "INSERT INTO employee_date (emp_id, $columnsStr) 
                VALUES ('$empID', $valuesStr)";
        
        if (mysqli_query($con, $sql)) {
            echo "New employee date record added successfully for ID: $empID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
        
        closeCon($con);
    }

    function addEmployeeRequest($empID) {
        $con = openCon();
        
        // Escape employee ID
        $empID = mysqli_real_escape_string($con, $empID);
        
        // Define departments in the specific order for employees
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
        
        // Prepare columns and values
        $columns = [];
        $values = [];
        
        foreach ($orderedDepartments as $dept) {
            $escapedDept = mysqli_real_escape_string($con, $dept);
            $columns[] = "`$escapedDept`";
            $values[] = "0"; // Default value for request status
        }
        
        $columnsStr = implode(", ", $columns);
        $valuesStr = implode(", ", $values);
        
        // Create the request record
        $sql = "INSERT INTO employee_request (emp_id, $columnsStr) 
                VALUES ('$empID', $valuesStr)";
        
        if (mysqli_query($con, $sql)) {
            error_log("New employee request record added successfully for ID: $empID");
            closeCon($con);
            return true;
        } else {
            error_log("Error adding employee request: " . mysqli_error($con));
            closeCon($con);
            return false;
        }
    }
    
    function addEmployeeComment($empID) {
        $con = openCon();
        
        // Escape employee ID
        $empID = mysqli_real_escape_string($con, $empID);
        
        // Use the specific ordered departments list
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
        
        // Prepare columns and values
        $columns = [];
        $values = [];
        
        foreach ($orderedDepartments as $dept) {
            $escapedDept = mysqli_real_escape_string($con, $dept);
            $columns[] = "`$escapedDept`";
            $values[] = "''"; // Empty string for comment
        }
        
        $columnsStr = implode(", ", $columns);
        $valuesStr = implode(", ", $values);
        
        // Create the comment record
        $sql = "INSERT INTO employee_comment (emp_id, $columnsStr) 
                VALUES ('$empID', $valuesStr)";
        
        if (mysqli_query($con, $sql)) {
            error_log("Employee comment record added for ID: $empID");
            closeCon($con);
            return true;
        } else {
            error_log("Error adding employee comment: " . mysqli_error($con));
            closeCon($con);
            return false;
        }
    }

    function approveEmployee($empID, $deptName) {
        $con = openCon();
        $con->begin_transaction();
        $success = false;
        
        try {
            // 1. Update clearance status to 1 (Approved)
            $query1 = "UPDATE employee_clearance SET `$deptName` = 1 WHERE emp_id = ?";
            $stmt1 = $con->prepare($query1);
            $stmt1->bind_param("s", $empID);
            $stmt1->execute();
            
            // 2. Clear the comment
            $query2 = "UPDATE employee_comment SET `$deptName` = '' WHERE emp_id = ?";
            $stmt2 = $con->prepare($query2);
            $stmt2->bind_param("s", $empID);
            $stmt2->execute();
            
            $con->commit();
            $success = true;
        } catch (Exception $e) {
            $con->rollback();
            error_log("Employee approval error: " . $e->getMessage());
        } finally {
            if (isset($stmt1)) $stmt1->close();
            if (isset($stmt2)) $stmt2->close();
            closeCon($con);
        }
        
        return $success;
    }
    
    function approveEmployeeDate($empID, $deptName, $date) {
        $con = openCon();
    
        $query = "UPDATE employee_date SET `$deptName` = ? WHERE emp_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ss", $date, $empID);
        $stmt->execute();
        $stmt->close();
        
        closeCon($con);
    }
    
    function storeEmployeeCommentAndReset($empID, $deptName, $comment) {
        $con = openCon();
        $con->begin_transaction();
        
        try {
            // 1. Store the comment
            $query1 = "UPDATE employee_comment SET `$deptName` = ? WHERE emp_id = ?";
            $stmt1 = $con->prepare($query1);
            $stmt1->bind_param("ss", $comment, $empID);
            $stmt1->execute();
            
            // 2. Set status to 2 (Declined)
            $query2 = "UPDATE employee_clearance SET `$deptName` = 2 WHERE emp_id = ?";
            $stmt2 = $con->prepare($query2);
            $stmt2->bind_param("s", $empID);
            $stmt2->execute();
            
            // 3. Reset the request status so they can request again
            $query3 = "UPDATE employee_request SET `$deptName` = 0 WHERE emp_id = ?";
            $stmt3 = $con->prepare($query3);
            $stmt3->bind_param("s", $empID);
            $stmt3->execute();
            
            $con->commit();
            return true;
        } catch (Exception $e) {
            $con->rollback();
            error_log("Employee decline error: " . $e->getMessage());
            return false;
        } finally {
            if (isset($stmt1)) $stmt1->close();
            if (isset($stmt2)) $stmt2->close();
            if (isset($stmt3)) $stmt3->close();
            closeCon($con);
        }
    }

    /*function getEmployeeClearanceData($empID) {
        $con = openCon();
        $clearanceData = [];
        
        // Define the ordered departments
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
        
        // Get department signatory info
        $queryDept = "SELECT dept_id, dept_name, employee_name FROM deptartments_cred WHERE type = 'Employee' OR type = 'Both'";
        $resultDept = mysqli_query($con, $queryDept);
        
        if (!$resultDept) {
            die("Error fetching departments: " . mysqli_error($con));
        }
        
        // Map department names to signatories
        $signatories = [];
        while ($rowDept = mysqli_fetch_assoc($resultDept)) {
            $signatories[$rowDept['dept_name']] = $rowDept['employee_name'];
        }
        
        // Get clearance info
        foreach ($orderedDepartments as $deptName) {
            // Status
            $queryStatus = "SELECT `$deptName` AS status FROM employee_clearance WHERE emp_id = '$empID'";
            $resultStatus = mysqli_query($con, $queryStatus);
            $statusRow = mysqli_fetch_assoc($resultStatus);
            $statusValue = $statusRow['status'] ?? 0;
            
            // Convert status value to text
            $status = '';
            if ($statusValue == 1) {
                $status = 'Approved';
            } elseif ($statusValue == 2) {
                $status = 'Declined';
            } else {
                $status = 'N/A';
            }
            
            // Date
            $queryDate = "SELECT `$deptName` AS date FROM employee_date WHERE emp_id = '$empID'";
            $resultDate = mysqli_query($con, $queryDate);
            $dateRow = mysqli_fetch_assoc($resultDate);
            $date = isset($dateRow['date']) ? $dateRow['date'] : 'N/A';
            
            // Remarks
            $queryRemarks = "SELECT `$deptName` AS remarks FROM employee_comment WHERE emp_id = '$empID'";
            $resultRemarks = mysqli_query($con, $queryRemarks);
            $remarksRow = mysqli_fetch_assoc($resultRemarks);
            $remarks = isset($remarksRow['remarks']) ? $remarksRow['remarks'] : 'No Remarks';
            
            $clearanceData[] = [
                'dept_name' => $deptName,
                'signatory' => $signatories[$deptName] ?? 'N/A',
                'status' => $status,
                'date' => $date,
                'remarks' => $remarks,
                'status_value' => $statusValue // Add numeric status for easier checking
            ];
        }
        
        closeCon($con);
        return $clearanceData;
    }*/

    /*function getEmployeeClearanceData($empID) {
        $con = openCon();
        $clearanceData = [];
        
        // Get all departments that handle employee clearance
        $queryDept = "SELECT dept_id, dept_name, employee_name FROM deptartments_cred WHERE type = 'Employee' OR type = 'Both'";
        $resultDept = mysqli_query($con, $queryDept);
        
        if (!$resultDept) {
            die("Error fetching departments: " . mysqli_error($con));
        }
        
        while ($rowDept = mysqli_fetch_assoc($resultDept)) {
            $deptName = $rowDept['dept_name'];
            
            $queryStatus = "SELECT `$deptName` AS status FROM employee_clearance WHERE emp_id = '$empID'";
            $resultStatus = mysqli_query($con, $queryStatus);
            $statusRow = mysqli_fetch_assoc($resultStatus);
            $statusValue = $statusRow['status'] ?? 0;
            
            // Convert status value to text
            $status = '';
            if ($statusValue == 1) {
                $status = 'Approved';
            } elseif ($statusValue == 2) {
                $status = 'Declined';
            } elseif ($statusValue == 3) {
                $status = 'Removed';
            } else {
                $status = 'N/A';
            }
            
            $queryDate = "SELECT `$deptName` AS date FROM employee_date WHERE emp_id = '$empID'";
            $resultDate = mysqli_query($con, $queryDate);
            $dateRow = mysqli_fetch_assoc($resultDate);
            $date = isset($dateRow['date']) ? $dateRow['date'] : 'N/A';
            
            $queryRemarks = "SELECT `$deptName` AS remarks FROM employee_comment WHERE emp_id = '$empID'";
            $resultRemarks = mysqli_query($con, $queryRemarks);
            $remarksRow = mysqli_fetch_assoc($resultRemarks);
            $remarks = isset($remarksRow['remarks']) ? $remarksRow['remarks'] : 'No Remarks';
            
            $clearanceData[] = [
                'dept_name' => $deptName,
                'signatory' => $rowDept['employee_name'],
                'status' => $status,
                'date' => $date,
                'remarks' => $remarks,
                'status_value' => $statusValue
            ];
        }
        
        closeCon($con);
        return $clearanceData;
    }*/

    function getEmployeeClearanceData($empID) {
        $con = openCon();
        $clearanceData = [];
        
        // Get all departments that handle employee clearance
        $queryDept = "SELECT dept_id, dept_name, employee_name FROM deptartments_cred 
                     WHERE (type = 'Employee' OR type = 'Both')";
        $resultDept = mysqli_query($con, $queryDept);
        
        if (!$resultDept) {
            die("Error fetching departments: " . mysqli_error($con));
        }
        
        while ($rowDept = mysqli_fetch_assoc($resultDept)) {
            $deptName = $rowDept['dept_name'];
            
            // First check if this department is marked as removed (status = 3)
            $queryStatus = "SELECT `$deptName` AS status FROM employee_clearance WHERE emp_id = '$empID'";
            $resultStatus = mysqli_query($con, $queryStatus);
            $statusRow = mysqli_fetch_assoc($resultStatus);
            $statusValue = $statusRow['status'] ?? 0;
            
            // Skip this department if it's removed (status = 3)
            if ($statusValue == 3) {
                continue;
            }
            
            // Convert status value to text
            $status = '';
            if ($statusValue == 1) {
                $status = 'Approved';
            } elseif ($statusValue == 2) {
                $status = 'Declined';
            } else {
                $status = 'N/A';
            }
            
            $queryDate = "SELECT `$deptName` AS date FROM employee_date WHERE emp_id = '$empID'";
            $resultDate = mysqli_query($con, $queryDate);
            $dateRow = mysqli_fetch_assoc($resultDate);
            $date = isset($dateRow['date']) ? $dateRow['date'] : 'N/A';
            
            $queryRemarks = "SELECT `$deptName` AS remarks FROM employee_comment WHERE emp_id = '$empID'";
            $resultRemarks = mysqli_query($con, $queryRemarks);
            $remarksRow = mysqli_fetch_assoc($resultRemarks);
            $remarks = isset($remarksRow['remarks']) ? $remarksRow['remarks'] : 'No Remarks';
            
            $clearanceData[] = [
                'dept_name' => $deptName,
                'signatory' => $rowDept['employee_name'],
                'status' => $status,
                'date' => $date,
                'remarks' => $remarks,
                'status_value' => $statusValue
            ];
        }
        
        closeCon($con);
        return $clearanceData;
    }

    function requestClearanceStudent($studID, $deptName) {
        $con = openCon();
        $con->begin_transaction();
        $success = false;
        
        try {
            // 1. Update clearance status
            $query1 = "UPDATE student_request SET `$deptName` = 1 WHERE stud_id = ?";
            $stmt1 = $con->prepare($query1);
            $stmt1->bind_param("s", $studID);
            $stmt1->execute();
            
            $con->commit();
            $success = true;
        } catch (Exception $e) {
            $con->rollback();
            error_log("Student approval error: " . $e->getMessage());
        } finally {
            if (isset($stmt1)) $stmt1->close();
            closeCon($con);
        }        
    }

    function isPreviousDepartmentApproved($studID, $currentDept) {
        $con = openCon();
        $approved = false;
        $deptOrder = getDepartmentOrder();
        
        try {
            $currentPos = array_search($currentDept, $deptOrder);
            
            if ($currentPos === 0) {
                // First department (Library) is always enabled
                $approved = true;
            } else {
                // Get the immediate previous department
                $prevDept = $deptOrder[$currentPos - 1];
                
                $query = "SELECT `$prevDept` FROM student_request WHERE stud_id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("s", $studID);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $approved = ($row[$prevDept] == 1);
                }
                
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Error checking previous department: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $approved;
    }

    /*function requestClearanceEmployee($empID, $deptName) {
        $con = openCon();
        $con->begin_transaction();
        $success = false;
        
        try {
            // 1. Update request status
            $query1 = "UPDATE employee_request SET `$deptName` = 1 WHERE emp_id = ?";
            $stmt1 = $con->prepare($query1);
            $stmt1->bind_param("s", $empID);
            $stmt1->execute();
            
            $con->commit();
            $success = true;
        } catch (Exception $e) {
            $con->rollback();
            error_log("Employee request error: " . $e->getMessage());
        } finally {
            if (isset($stmt1)) $stmt1->close();
            closeCon($con);
        }        
        
        return $success;
    }*/

    function requestClearanceEmployee($empID, $deptName) {
        $con = openCon();
        $con->begin_transaction();
        $success = false;
        
        try {
            // 1. Set request status
            $query = "UPDATE employee_request SET `$deptName` = 1 WHERE emp_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $empID);
            $stmt->execute();
            
            // 2. Set hasRequested flag
            $query = "UPDATE employee_info SET hasRequested = 1 WHERE emp_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $empID);
            $stmt->execute();
            
            $con->commit();
            $success = true;
        } catch (Exception $e) {
            $con->rollback();
            error_log("Request error: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $success;
    }

    function isPreviousDepartmentApprovedEmployee($studID, $currentDept) {
        $con = openCon();
        $approved = false;
        $deptOrder = getEmployeeDepartmentOrder();
        
        try {
            $currentPos = array_search($currentDept, $deptOrder);
            
            if ($currentPos === 0) {
                // First department (Library) is always enabled
                $approved = true;
            } else {
                // Get the immediate previous department
                $prevDept = $deptOrder[$currentPos - 1];
                
                $query = "SELECT `$prevDept` FROM employee_request WHERE emp_id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("s", $studID);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $approved = ($row[$prevDept] == 1);
                }
                
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Error checking previous department: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $approved;
    }

    function getEmployeeDepartmentOrder() {
        return [
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
    }

    function shouldEnableNextStudentRequestButton($studID, $deptName) {
        $con = openCon();
        $enable = false;
        
        $studentInfo = fetchStudentInfo($studID);
        $isForeigner = isset($studentInfo['foreigner']) && $studentInfo['foreigner'] == 1;
        
        // Skip Foreign Affairs for foreign students
        if ($isForeigner && $deptName == 'Foreign Affairs') {
            closeCon($con);
            return true;
        }
        
        try {
            $query = "SELECT `$deptName` FROM student_clearance WHERE stud_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $studID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $enable = ($row[$deptName] == 1);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error checking department approval: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $enable;
    }

    function getDepartmentOrder() {
        return [
            'Library',
            'OSA',
            'Guidance',
            'Foreign Affairs', // Will be removed for foreign students
            'Computer Lab',
            'Program Chair',
            'Dean',
            'Registrar',
            'Vice President',
            'Accounting'
        ];
    }
    
    function shouldDisableStudentButton($studID, $deptName, $currentStatus) {
        if ($currentStatus == 'Approved') return true;
        
        $studentInfo = fetchStudentInfo($studID);
        $isForeigner = isset($studentInfo['foreigner']) && $studentInfo['foreigner'] == 1;
        
        // Skip Foreign Affairs check for foreign students
        if ($isForeigner && $deptName == 'Foreign Affairs') {
            return true;
        }
        
        $con = openCon();
        $disable = false;
    
        try {
            // Check if this department already has a request
            $query = "SELECT `$deptName` FROM student_request WHERE stud_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $studID);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row[$deptName] == 1) {
                    $disable = true;
                }
            }
    
            $stmt->close();
            
            // If not already requested, check department order requirements
            if (!$disable) {
                $deptOrder = getDepartmentOrder();
                
                // Remove Foreign Affairs from order if student is foreigner
                if ($isForeigner) {
                    $deptOrder = array_filter($deptOrder, function($dept) {
                        return $dept != 'Foreign Affairs';
                    });
                    $deptOrder = array_values($deptOrder); // Reindex array
                }
                
                $currentPos = array_search($deptName, $deptOrder);
    
                // Check if the previous department is approved
                if ($currentPos > 0) {
                    $prevDept = $deptOrder[$currentPos - 1];
                    $disable = !shouldEnableNextStudentRequestButton($studID, $prevDept);
                }
    
                // The first department should always be enabled
                if ($currentPos === 0) {
                    $disable = false;
                }
            }
        } catch (Exception $e) {
            error_log("Error in shouldDisableStudentButton: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
    
        return $disable;
    }

    /*function shouldDisableEmployeeButton($empID, $deptName, $currentStatus) {
        // If status is already approved (1), disable the button
        if ($currentStatus == 'Approved') {
            return true;
        }
        
        $con = openCon();
        $disable = false;
        
        try {
            // Check if this department already has a request (value = 1)
            $query = "SELECT `$deptName` FROM employee_request WHERE emp_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $empID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row[$deptName] == 1) {
                    $disable = true;
                }
            }
            
            $stmt->close();
            
            // If not already requested, check department order requirements
            if (!$disable) {
                $deptOrder = getEmployeeDepartmentOrder();
                $currentPos = array_search($deptName, $deptOrder);
                
                // Check if the previous department is approved
                if ($currentPos > 0) {
                    $prevDept = $deptOrder[$currentPos - 1];
                    $disable = !shouldEnableNextEmployeeRequestButton($empID, $prevDept);
                }
                
                // The first department should always be enabled
                if ($currentPos === 0) {
                    $disable = false;
                }
            }
        } catch (Exception $e) {
            error_log("Error in shouldDisableEmployeeButton: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $disable;
    }*/

    function shouldDisableEmployeeButton($empID, $deptName, $currentStatus) {
        // If status is already approved (1) or removed (3), disable the button
        if ($currentStatus == 'Approved' || $currentStatus == 'Removed') {
            return true;
        }
        
        // Check if this department already has a request
        $con = openCon();
        $hasRequest = false;
        
        try {
            $query = "SELECT `$deptName` FROM employee_request WHERE emp_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $empID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $hasRequest = ($row[$deptName] == 1);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error checking employee request: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $hasRequest;
    }

    function shouldEnableNextEmployeeRequestButton($empID, $deptName) {
        $con = openCon();
        $enable = false;
        
        try {
            $query = "SELECT `$deptName` FROM employee_clearance WHERE emp_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $empID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $enable = ($row[$deptName] == 1); // Only enable if approved (1)
            }
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error checking department approval: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $enable;
    }

    function hasStudentRequested($studID, $deptName) {
        $con = openCon();
        $hasRequested = false;
        
        try {
            $query = "SELECT `$deptName` FROM student_request WHERE stud_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $studID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $hasRequested = ($row[$deptName] == 1);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error checking student request: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $hasRequested;
    }
    
    function hasEmployeeRequested($empID, $deptName) {
        $con = openCon();
        $hasRequested = false;
        
        try {
            $query = "SELECT `$deptName` FROM employee_request WHERE emp_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $empID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $hasRequested = ($row[$deptName] == 1);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error checking employee request: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $hasRequested;
    }

    function getPendingStudentRequests($deptName) {
        $con = openCon();
        $requests = [];
        
        // Get students who have requested but not yet been approved (status != 1)
        // Also include those who were declined (status = 2) so they can request again
        $sql = "SELECT sr.stud_id, si.stud_name 
                FROM student_request sr
                JOIN student_info si ON sr.stud_id = si.stud_id
                LEFT JOIN student_clearance sc ON sr.stud_id = sc.stud_id
                WHERE sr.`$deptName` = 1 
                AND (sc.`$deptName` IS NULL OR sc.`$deptName` = 0 OR sc.`$deptName` = 2)";
        
        $result = mysqli_query($con, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $requests[] = [
                    'id' => $row['stud_id'],
                    'name' => $row['stud_name']
                ];
            }
        }
        
        closeCon($con);
        return $requests;
    }
    
    function getPendingEmployeeRequests($deptName) {
        $con = openCon();
        $requests = [];
        
        // Get employees who have requested but not yet been approved (status != 1)
        // Also include those who were declined (status = 2) so they can request again
        $sql = "SELECT er.emp_id, ei.name 
                FROM employee_request er
                JOIN employee_info ei ON er.emp_id = ei.emp_id
                LEFT JOIN employee_clearance ec ON er.emp_id = ec.emp_id
                WHERE er.`$deptName` = 1 
                AND (ec.`$deptName` IS NULL OR ec.`$deptName` = 0 OR ec.`$deptName` = 2)";
        
        $result = mysqli_query($con, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $requests[] = [
                    'id' => $row['emp_id'],
                    'name' => $row['name']
                ];
            }
        }
        
        closeCon($con);
        return $requests;
    }

    function getApprovedStudentCount($deptName) {
        $con = openCon();
        $query = "SELECT COUNT(*) as count FROM student_clearance WHERE `$deptName` = 1";
        $result = mysqli_query($con, $query);
        closeCon($con);
        return $result ? (int)mysqli_fetch_assoc($result)['count'] : 0;
    }

    function getApprovedEmployeeCount($deptName) {
        $con = openCon();
        $query = "SELECT COUNT(*) as count FROM employee_clearance WHERE `$deptName` = 1";
        $result = mysqli_query($con, $query);
        closeCon($con);
        return $result ? (int)mysqli_fetch_assoc($result)['count'] : 0;
    }
    
    function getDepartmentsStudentForPDF($studID) {
        $studentInfo = fetchStudentInfo($studID);
        $isForeigner = isset($studentInfo['foreigner']) && $studentInfo['foreigner'] == 1;
        
        $departments = getStudentClearanceData($studID);
        
        // Filter out Foreign Affairs if student is foreigner
        if ($isForeigner) {
            $departments = array_filter($departments, function($dept) {
                return $dept['dept_name'] !== 'Foreign Affairs';
            });
            // Get first 4 departments for foreign students
            return array_slice(array_values($departments), 0, 4);
        }
        
        // Get first 5 departments for non-foreign students
        return array_slice($departments, 0, 5);
    }

    /*function updateEmployeeSignatories($empID, $departments, $isRemoval) {
        $con = openCon();
        $con->begin_transaction();
        $success = false;
        
        try {
            foreach ($departments as $dept) {
                // For removal, set status to 3. For addition, set to 0 (not removed/not approved)
                $status = $isRemoval ? 3 : 0;
                
                // 1. Update clearance status
                $sql = "UPDATE employee_clearance SET `$dept` = ? WHERE emp_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("is", $status, $empID);
                $stmt->execute();
                
                // 2. Reset request status to 0 (not requested)
                $requestStatus = 0;
                $sql = "UPDATE employee_request SET `$dept` = ? WHERE emp_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("is", $requestStatus, $empID);
                $stmt->execute();
                
                // 3. Clear date and comments
                $sql = "UPDATE employee_date SET `$dept` = '' WHERE emp_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("s", $empID);
                $stmt->execute();
                
                $sql = "UPDATE employee_comment SET `$dept` = '' WHERE emp_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("s", $empID);
                $stmt->execute();
            }
            
            $con->commit();
            $success = true;
        } catch (Exception $e) {
            $con->rollback();
            error_log("Error updating signatories: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $success;
    }*/

    function updateEmployeeSignatories($empID, $departments, $isRemoval) {
        $con = openCon();
        $con->begin_transaction();
        $success = false;
        
        try {
            foreach ($departments as $dept) {
                // For removal, set status to 3. For addition, set to 0 (not removed/not approved)
                $status = $isRemoval ? 3 : 0;
                
                // 1. Update clearance status
                $sql = "UPDATE employee_clearance SET `$dept` = ? WHERE emp_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("is", $status, $empID);
                $stmt->execute();
                
                // 2. Reset request status to 0 (not requested)
                $requestStatus = 0;
                $sql = "UPDATE employee_request SET `$dept` = ? WHERE emp_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("is", $requestStatus, $empID);
                $stmt->execute();
                
                // 3. Clear date and comments
                $sql = "UPDATE employee_date SET `$dept` = '' WHERE emp_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("s", $empID);
                $stmt->execute();
                
                $sql = "UPDATE employee_comment SET `$dept` = '' WHERE emp_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("s", $empID);
                $stmt->execute();
            }
            
            // Reset hasRequested flag if all departments are removed
            $query = "UPDATE employee_info SET hasRequested = 0 WHERE emp_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $empID);
            $stmt->execute();
            
            $con->commit();
            $success = true;
        } catch (Exception $e) {
            $con->rollback();
            error_log("Error updating signatories: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $success;
    }

    function getAllEmployeeDepartments($empID) {
        $con = openCon();
        $departments = [];
        
        // Get all departments that handle employee clearance
        $queryDept = "SELECT dept_id, dept_name, employee_name FROM deptartments_cred 
                     WHERE (type = 'Employee' OR type = 'Both')";
        $resultDept = mysqli_query($con, $queryDept);
        
        if (!$resultDept) {
            die("Error fetching departments: " . mysqli_error($con));
        }
        
        while ($rowDept = mysqli_fetch_assoc($resultDept)) {
            $deptName = $rowDept['dept_name'];
            
            // Check if this department is removed (status = 3)
            $queryStatus = "SELECT `$deptName` AS status FROM employee_clearance WHERE emp_id = '$empID'";
            $resultStatus = mysqli_query($con, $queryStatus);
            $statusRow = mysqli_fetch_assoc($resultStatus);
            $statusValue = $statusRow['status'] ?? 0;
            
            $departments[] = [
                'dept_name' => $deptName,
                'signatory' => $rowDept['employee_name'],
                'is_removed' => ($statusValue == 3)
            ];
        }
        
        closeCon($con);
        return $departments;
    }

    function getEmployeeDepartmentsForPDF($empID) {
        $con = openCon();
        $departments = [];
        
        // Get all departments that handle employee clearance
        $queryDept = "SELECT dept_id, dept_name, employee_name FROM deptartments_cred 
                     WHERE (type = 'Employee' OR type = 'Both')";
        $resultDept = mysqli_query($con, $queryDept);
        
        if (!$resultDept) {
            die("Error fetching departments: " . mysqli_error($con));
        }
        
        while ($rowDept = mysqli_fetch_assoc($resultDept)) {
            $deptName = $rowDept['dept_name'];
            
            // Check if this department is removed (status = 3)
            $queryStatus = "SELECT `$deptName` AS status FROM employee_clearance WHERE emp_id = '$empID'";
            $resultStatus = mysqli_query($con, $queryStatus);
            $statusRow = mysqli_fetch_assoc($resultStatus);
            $statusValue = $statusRow['status'] ?? 0;
            
            // Skip departments with status 3 (removed)
            if ($statusValue == 3) {
                continue;
            }
            
            // Get status text
            $status = '';
            if ($statusValue == 1) {
                $status = 'Approved';
            } elseif ($statusValue == 2) {
                $status = 'Declined';
            } else {
                $status = 'N/A';
            }
            
            // Get date
            $queryDate = "SELECT `$deptName` AS date FROM employee_date WHERE emp_id = '$empID'";
            $resultDate = mysqli_query($con, $queryDate);
            $dateRow = mysqli_fetch_assoc($resultDate);
            $date = isset($dateRow['date']) ? $dateRow['date'] : 'N/A';
            
            $departments[] = [
                'dept_name' => $deptName,
                'signatory' => $rowDept['employee_name'],
                'status' => $status,
                'date' => $date
            ];
        }
        
        closeCon($con);
        return $departments;
    }

    function completeEmployeeClearance($empID) {
        $con = openCon();
        $success = false;
        
        try {
            $query = "UPDATE employee_info SET hasRequested = 0 WHERE emp_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $empID);
            $success = $stmt->execute();
        } catch (Exception $e) {
            error_log("Error resetting hasRequested: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $success;
    }

    function resetEmployeeRequestStatus($empID) {
        $con = openCon();
        $success = false;
        
        try {
            $query = "UPDATE employee_info SET hasRequested = 0 WHERE emp_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $empID);
            $success = $stmt->execute();
        } catch (Exception $e) {
            error_log("Reset error: " . $e->getMessage());
        } finally {
            closeCon($con);
        }
        
        return $success;
    }
 
?>