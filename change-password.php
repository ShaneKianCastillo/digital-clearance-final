<?php 
    session_start(); // If not already included
    if (!isset($_SESSION['return_to'])) {
        $_SESSION['return_to'] = $_SERVER['HTTP_REFERER'] ?? 'student-dashboard.php';
    }
    include 'functions.php'; 
    $checkID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'Student';
    $studentID = $_SESSION['userID'];

    $message = '';
    $messageType = ''; // 'success' or 'danger'

    if (isset($_POST['saveButton'])) {
        // Get user info from session
        $userID = $_SESSION['userID'] ?? null;
        $role = $_SESSION['role'] ?? null; // This should be set during login
        
        if (!$userID || !$role) {
            die("Session error: User not properly authenticated");
        }
        
        $oldPass = $_POST['oldPass'];
        $newPass = $_POST['newPass'];
        $confirmNewPass = $_POST['confirmNewPass'];
    
        // Validate inputs
        if (empty($oldPass) || empty($newPass) || empty($confirmNewPass)) {
            $message = 'All fields are required!';
            $messageType = 'danger';
        } elseif ($newPass !== $confirmNewPass) {
            $message = 'New passwords do not match!';
            $messageType = 'danger';
        } elseif (strlen($newPass) < 8) {
            $message = 'Password must be at least 8 characters long!';
            $messageType = 'danger';
        } else {
            // Call the changePassword function with role
            if (changePassword($userID, $oldPass, $newPass, $role)) {
                // Redirect after success
                $returnTo = $_SESSION['return_to'] ?? 'student-dashboard.php';
                unset($_SESSION['return_to']); // Clean up session variable
                header("Location: $returnTo");
                exit;
            } else {
                $message = 'Failed to change password. Old password may be incorrect.';
                $messageType = 'danger';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Change Password</title>
</head>
<body>
   <div class="container shadow mt-5 col-lg-4 p-5 position-relative">
    <div>
        <p class="fs-4 fw-medium">Change Password</p>
    </div>
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <label for="oldPass" class="input-group-text">Old Password</label>
                <input type="password" name="oldPass" id="oldPassword" placeholder="Input Old Password" class="form-control" required>
                <i class="fa-solid fa-eye position-absolute" style="left: 101%; top: 10px; cursor:pointer; font-size:20px" id="toggleOldPassword"></i>
            </div>
            <div class="input-group pt-5">
                <label for="newPass" class="input-group-text">New Password</label>
                <input type="password" name="newPass" id="newPassword" placeholder="Input New Password" class="form-control" required>
                <i class="fa-solid fa-eye position-absolute" style="left: 101%; top:58px; cursor:pointer; font-size:20px" id="toggleNewPassword"></i>
            </div>
            <div class="input-group pt-5">
                <label for="conNewPassword" class="input-group-text">Confirm New Password</label>
                <input type="password" name="confirmNewPass" id="conNewPassword" placeholder="Confirm New Password" class="form-control" required>
                <i class="fa-solid fa-eye position-absolute" style="left: 101%; top:58px; cursor:pointer; font-size:20px" id="toggleConNewPassword"></i>
            </div>
            <div class="pt-4 text-center">
                <button class="btn btn-danger fs-5" type="reset" onclick="goBack()">Cancel</button>
                <button class="btn btn-success fs-5" type="submit" name="saveButton">Save</button>
        </div>
        </form>
   </div>


   <script>
    // Add toggle functionality for each field
    document.getElementById('toggleOldPassword').addEventListener('click', () => togglePassword('oldPassword', 'toggleOldPassword'));
            document.getElementById('toggleNewPassword').addEventListener('click', () => togglePassword('newPassword', 'toggleNewPassword'));
            document.getElementById('toggleConNewPassword').addEventListener('click', () => togglePassword('conNewPassword', 'toggleConNewPassword'));

            // Reusable function for toggling password visibility
            function togglePassword(inputId, iconId) {
                const passwordInput = document.getElementById(inputId);
                const toggleIcon = document.getElementById(iconId);

                // Toggle input type
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';

                // Toggle icon class
                toggleIcon.classList.toggle('fa-eye');
                toggleIcon.classList.toggle('fa-eye-slash');
            }
   </script>
   <script>
   function goBack() {
    window.location.href = "<?php echo $_SESSION['return_to'] ?? 'student-dashboard.php'; ?>";
}



</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>