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
    
    function addUser() {
        $con = openCon();
        if ($con) {
            $userID = '300908645';
            $hashedPassword = md5('Dean0005'); 
            $name = 'Dean';
            $sql = "INSERT INTO users (user_id, password, name) VALUES ('$userID', '$hashedPassword', '$name')";
            if (mysqli_query($con, $sql)) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
            closeCon($con);
        } else {
            echo "Failed to connect to the database.";
        }
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

?>