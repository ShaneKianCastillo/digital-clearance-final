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
        $sql = "SELECT user_id, password FROM users";
        $result = mysqli_query($con, $sql);    
        $users = [];   
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $users[$row['user_id']] = $row['password'];
            }
        }    
        closeCon($con);     
        return $users;
    }

    function checkLoginCredentials($userID, $password, $users) {
        return isset($users[$userID]) && $users[$userID] === md5($password);  
    }

    function validateLoginCredentials($userID, $password) {
        $errorArray = [];
        $users = getUsers();  
        if (empty($errorArray)) {
            if (!checkLoginCredentials($userID, $password, $users)) {
                $errorArray['credentials'] = 'Incorrect email or password!';
            }
        } 
        return $errorArray;
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
        foreach ($errors as $error) {
            $output .= '<li>' . htmlspecialchars($error) . '</li>';
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

    function getUserNameById($idNumber) {
        $con = openCon();
        $query = "SELECT name FROM users WHERE user_id = '$idNumber'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            closeCon($con);
            return $row['name']; 
        }
        closeCon($con);
        return null; 
    }





?>