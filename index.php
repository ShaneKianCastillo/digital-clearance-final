<?php 
    include 'functions.php';

    $userID = "";
    $password = "";
    $errorArray = [];
    $users = [];

    /*$userID = '200890522';
    $password = 'Paul';
    $name = 'Paul';
    $course = 'BSIT';
    $contact = '09326614570';
    $year = '4';
    $foreigner = 1; // 1 for foreigner, 0 for local students

    addStudentUser($userID, $password, $name);
    addStudentInfo($userID, $name, $course, $contact, $year, $foreigner);
    addStudentClearance($userID);
    addStudentRequest($userID);
    addStudentComment($userID);
    addStudentDate($userID);*/
    
    /*$userID = '100254436';
    $password = 'Mananquil';
    $name = 'Lhiz Mananquil';
    $category = 'Teaching';
    $department = 'CICS';
    $position = 'Teacher';
    $status = 'Regular';
    $gradeLevel = 1; //1 for grade 1-12, 0 for college instructors

    addEmployeeUser ($userID, $password, $name);
    addEmployeeClearance($userID);
    addEmployeeRequest($userID);
    addEmployeeComment($userID);
    addEmployeeDate($userID);
    addEmployeeInfo ($userID, $name, $department, $position, $category, $status);*/

    if (isset($_POST['loginButton'])) {
        $userID = $_POST['userID'];
        $password = $_POST['password'];
    
        list($errorArray, $users) = validateLoginCredentials($userID, $password);  
    
        if (empty($errorArray)) {
            session_start();
            if (isset($users[$userID])) {
                $role = $users[$userID]['role']; 
                $_SESSION['userID'] = $userID;
                $_SESSION['role'] = $role; 
                
                if ($role === 'student' || $role === 'employee') {
                    header('Location: student-dashboard.php');
                } elseif ($role === 'department') {
                    header('Location: dashboard.php');
                } elseif ($role === 'dean') {
                    header('Location: dean-dashboard.php'); 
                }
                exit();
            } 
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/icons/icon.ico">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="icon" href="img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/a082745512.js" crossorigin="anonymous"></script>
    <title>Login</title>
</head>
<body>
    <div class="z-3">
        <?php echo displayErrors($errorArray); ?>
    </div>
    <div class="container glass z-0">
        <img src="img/logo.png" alt="Logo" height="120px">
        <h1>Systems Plus</h1>
        <div class="form">
            <form method="post">
                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="mail"></ion-icon>
                    </span>
                    <input type="number" placeholder=" " step="1" name="userID" value="<?php echo htmlspecialchars($userID); ?>" required>
                    <label>ID Number</label>
                </div>
                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="lock-closed"></ion-icon>
                    </span>
                    <input id="password" type="password" placeholder=" " name="password" value="<?php echo htmlspecialchars($password); ?>" required>
                    <label>Password</label>
                    <i class="fa-solid fa-eye position-absolute" style="right: 10px; top: 16px; cursor:pointer" id="togglePassword"></i>
                </div>
                <button type="submit" name="loginButton">Login</button>
            </form>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
        // Toggle the type attribute between 'password' and 'text'
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';

        // Toggle the icon class between 'fa-eye' and 'fa-eye-slash'
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>