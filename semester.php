<?php 

    include 'functions.php';    


    if (isset($_POST['saveButton'])) {
        $semester = $_POST['semester'];
        $school_year = $_POST['school_year'];
        saveSemesterAndSchoolYear($semester, $school_year);
        header("Location: principal-dashboard.php");
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://kit.fontawesome.com/a082745512.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    <title>Document</title>
    <title>Edit Semester and S.Y.</title>
</head>
<body>
<div class="container shadow mt-5 col-lg-4 p-5 position-relative">
    <div>
        <p class="fs-4 fw-medium">Set Semester and S.Y.</p>
    </div>
    <hr>
    
    <form method="POST">
        
        <div class="mb-3">
            <label class="fw-bold fs-5">Select Semester:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="semester" id="firstSem" value="First Semester" required checked>
                <label class="form-check-label" for="firstSem">First Semester</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="semester" id="secondSem" value="Second Semester" required>
                <label class="form-check-label" for="secondSem">Second Semester</label>
            </div>
        </div>

        
        <div class="mb-4 pt-3">
            <label class="fw-bold fs-5">Select School Year:</label>
            <select class="form-select" name="school_year" id="schoolYearSelect" required>
                
            </select>
        </div>
        
        <div class="pt-4 text-center">
            <a href="principal-dashboard.php" class="btn btn-danger fs-5">Cancel</a>
            <button type="submit" name="saveButton" class="btn btn-success fs-5">Save</button>
        </div>
    </form>
</div>

    <script>
            document.addEventListener("DOMContentLoaded", function () {
                let select = document.getElementById("schoolYearSelect");
                let startYear = 2025; // Start from 2025 - 2026

                for (let i = startYear; i <= startYear + 50; i++) { // Generate future years (up to 50 years ahead)
                    let option = document.createElement("option");
                    option.value = i + " - " + (i + 1);
                    option.textContent = i + " - " + (i + 1);
                    select.appendChild(option);
                }
            });

    </script>
</body>
</html>