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
    
        $studID = mysqli_real_escape_string($con, $studID);
        $deptQuery = "SELECT dept_name FROM deptartments_cred WHERE dept_name != 'Dean'";
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
        $defaultValues = implode(", ", array_fill(0, count($departments), 0));
    
        $columns .= ", `Dean`";
        $defaultValues .= ", 0";
    
        $sql = "INSERT INTO student_clearance (stud_id, $columns) 
                VALUES ('$studID', $defaultValues)";
    
        if (mysqli_query($con, $sql)) {
            echo "New clearance record added successfully for student ID: $studID";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    
        closeCon($con);
    }
    
    function addStudentComment($studID) {
        $con = openCon();
    
        $studID = mysqli_real_escape_string($con, $studID);
        $deptQuery = "SELECT dept_name FROM deptartments_cred WHERE dept_name != 'Dean'";
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
        $deptQuery = "SELECT dept_name FROM deptartments_cred WHERE dept_name != 'Dean'";
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
            $status = isset($statusRow['status']) && $statusRow['status'] == 1 ? 'Approved' : 'Declined';

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
            $status = isset($statusRow['status']) && $statusRow['status'] == 1 ? 'Approved' : 'Declined';

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
            $query1 = "UPDATE student_comment SET `$deptName` = ? WHERE stud_id = ?";
            $stmt1 = $con->prepare($query1);
            $stmt1->bind_param("si", $comment, $studID);
            $stmt1->execute();

            $query2 = "UPDATE student_date SET `$deptName` = '' WHERE stud_id = ?";
            $stmt2 = $con->prepare($query2);
            $stmt2->bind_param("i", $studID);
            $stmt2->execute();
    
            $query3 = "UPDATE student_clearance SET `$deptName` = 0 WHERE stud_id = ?";
            $stmt3 = $con->prepare($query3);
            $stmt3->bind_param("i", $studID);
            $stmt3->execute();
    
            $con->commit();
    
            $stmt1->close();
            $stmt2->close();
            $stmt3->close();
    
        } catch (Exception $e) {
            $con->rollback();
            throw $e;
        }
        $con->close();
    }

    function isStudentEligibleForDepartment($studID, $currentDeptName, $orderedDepartments) {
        $con = openCon();
    
        if ($currentDeptName === 'Library') {
            closeCon($con);
            return true;
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
            if ($column != 'stud_id' && $column != 'Dean') {
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
        if ($allApproved || ($data['Dean'] == 0 && count(array_filter($data, fn($value) => $value == 1)) == count($departmentColumns))) {
            return true;
        }
        return false;
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
    
        // Determine the table and ID field based on role
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
                return false; // Invalid role
        }
        
        // First, get the stored password hash
        $query = "SELECT password FROM $table WHERE $idField = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();   
        
        if ($result->num_rows === 0) {
            // User not found
            return false;
        }
        
        $data = $result->fetch_assoc();
        $storedHash = $data['password'];
        
        // Verify old password by comparing MD5 hashes
        if (md5($oldPass) === $storedHash) {
            // Hash the new password
            $newHash = md5($newPass);
            
            // Update the password
            $query = "UPDATE $table SET password = ? WHERE $idField = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ss", $newHash, $userID);
            
            if ($stmt->execute()) {
                return true; // Password changed successfully
            }
        }
        
        return false; // Either old password was wrong or update failed
    }

    function determineUserType($userID) {
        $con = openCon();
        
        // Check in students_cred first
        $stmt = $con->prepare("SELECT stud_id FROM students_cred WHERE stud_id = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return 'student';
        
        // Check in dean_cred next
        $stmt = $con->prepare("SELECT dean_id FROM dean_cred WHERE dean_id = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return 'dean';

        // Check in employees_cred next
        $stmt = $con->prepare("SELECT emp_id FROM employees_cred WHERE emp_id = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return 'employee';
        
        // Check in departments_cred last
        $stmt = $con->prepare("SELECT dept_id FROM departments_cred WHERE dept_id = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return 'department';
        
        return false; // Not found in any table
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
        return $status == 1 ? 'text-success' : 'text-danger';
    }
    
    function getStatusText($status) {
        return $status == 1 ? 'Approved' : 'Declined';
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

    function addEmployeeInfo($empID, $empName, $department, $position, $category, $status) {
        $con = openCon();
    
        $empID = mysqli_real_escape_string($con, $empID);
        $empName = mysqli_real_escape_string($con, $empName);
        $department = mysqli_real_escape_string($con, $department);
        $position = mysqli_real_escape_string($con, $position);
        $category = mysqli_real_escape_string($con, $category);
        $status = mysqli_real_escape_string($con, $status);
    
        $sql = "INSERT INTO employee_info (emp_id, name, department, position, category, status) 
                VALUES ('$empID', '$empName', '$department', '$position', '$category', '$status')";
    
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
    
        $empID = mysqli_real_escape_string($con, $empID);
        $sql = "SELECT * FROM employee_info WHERE emp_id = '$empID'";
        $result = mysqli_query($con, $sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $employee = mysqli_fetch_assoc($result); 
            closeCon($con);
            return $employee; 
        } else {
            closeCon($con);
            return null; 
        }
    }

    function checkIfStudent($userID) {
        $con = openCon(); // Open the database connection
    
        // Check if the user exists in the student_users table
        $query = "SELECT * FROM students_cred WHERE stud_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $isStudent = $result->num_rows > 0; // True if user exists, false otherwise
    
        closeCon($con); // Close the database connection
    
        return $isStudent;
    }

    function processStudentSearch($studID, $facultyData) {
        // Initialize result array with default values
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
    
        // Always set the student name and course, even if not approved
        $result['studName'] = $student['stud_name'];
        $result['studCourse'] = $student['course'];
    
        $con = openCon();
        $deptQuery = "SELECT dept_name FROM deptartments_cred ORDER BY id ASC LIMIT 9";
        $queryResult = mysqli_query($con, $deptQuery);
    
        if (!$queryResult) {
            $result['errorMessage'] = "Error fetching departments: " . mysqli_error($con);
            closeCon($con);
            return $result;
        }
    
        while ($row = mysqli_fetch_assoc($queryResult)) {
            $result['orderedDepartments'][] = $row['dept_name'];
        }
    
        $deptName = $facultyData['dept_name'];
        $studentApproved = isStudentEligibleForDepartment($studID, $deptName, $result['orderedDepartments']);
    
        if ($studentApproved) {
            $result['studentFound'] = true;
            $result['commentAreaValue'] = fetchStudentComment($studID, $deptName);
        } else {
            $result['errorMessage'] = htmlspecialchars($student['stud_name']) . " is not yet approved by previous departments.";
        }
    
        closeCon($con);
        return $result;
    }

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
            // 1. Update clearance status
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
            // Store comment
            $query1 = "UPDATE employee_comment SET `$deptName` = ? WHERE emp_id = ?";
            $stmt1 = $con->prepare($query1);
            $stmt1->bind_param("ss", $comment, $empID);
            $stmt1->execute();
            
            // Reset approval status
            $query2 = "UPDATE employee_clearance SET `$deptName` = 0 WHERE emp_id = ?";
            $stmt2 = $con->prepare($query2);
            $stmt2->bind_param("s", $empID);
            $stmt2->execute();
            
            $con->commit();
            return true;
        } catch (Exception $e) {
            $con->rollback();
            error_log("Employee decline error: " . $e->getMessage());
            return false;
        } finally {
            if (isset($stmt1)) $stmt1->close();
            if (isset($stmt2)) $stmt2->close();
            closeCon($con);
        }
    }

    function getEmployeeClearanceData($empID) {
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
            $status = isset($statusRow['status']) && $statusRow['status'] == 1 ? 'Approved' : 'Declined';
    
            // Date
            $queryDate = "SELECT `$deptName` AS date FROM employee_date WHERE emp_id = '$empID'";
            $resultDate = mysqli_query($con, $queryDate);
            $dateRow = mysqli_fetch_assoc($resultDate);
            $date = isset($dateRow['date']) ? $dateRow['date'] : 'No Date';

            // Remarks
            $queryRemarks = "SELECT `$deptName` AS remarks FROM employee_comment WHERE emp_id = '$empID'";
            $resultRemarks = mysqli_query($con, $queryRemarks);
            $remarksRow = mysqli_fetch_assoc($resultRemarks);
            $remarks = isset($remarksRow['remarks']) ? $remarksRow['remarks'] : 'No Remarks';
    
            // Final push
            $clearanceData[] = [
                'dept_name' => $deptName,
                'signatory' => $signatories[$deptName] ?? 'N/A',
                'status' => $status,
                'date' => $date,
                'remarks' => $remarks
            ];
        }
    
        closeCon($con);
        return $clearanceData;
    }
?>