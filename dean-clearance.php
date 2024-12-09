<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://kit.fontawesome.com/a082745512.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    <title>Document</title>
</head>
<body>
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <div>
            <img src="images/logo.jpg" alt="" height="80px" class="ps-3">
        </div>
        <div class="head">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-10 text-white " href="#">Dean - Student Clearance</a>
        </div>
    </header>

    <div class="sidebar position-relative">
        <div class="container position-fixed start-0 sidebar-shadow z-1 bg-light" style="height: 100vh; width:250px;">
            <div class="pt-4 d-flex gy-1 " >
                <div>
                    <i class="fa-regular fa-user" style="font-size: 50px; color:gray"></i>
                </div>
                <div class="ps-3">
                    <p class="fs-6" style="font-weight: 500;">George M. Granados</p>
                    <p class="position-absolute" style="top: 52px;">CCIS - Dean</p>
                </div>
            </div>
            <div class="d-flex pt-5 ps-2">
                <div>
                    <a href="dean-dashboard.php" style="text-decoration: none;" class="text-dark"><i class="fa-solid fa-house" style="font-size: 25px;"></i></a>
                </div>
                <div class="ps-2">
                    <a href="dean-dashboard.php" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium">Dashboard</p></a>
                </div>
            </div>
            <div class="pt-3 text-center ps-2">
                <div class="d-flex gy-1">
                    <div>
                        <a href="dean-clearance.html" style="text-decoration: none;" class="text-dark"><i class="fa-regular fa-file" style="font-size: 25px;"></i></a>
                    </div>
                    <div class="ps-2">
                        <a href="dean-clearance.html" style="text-decoration: none;" class="text-dark"><p class="fs-6 fw-medium">Student Clearance</p></a>
                    </div>  
                </div>  
            </div>
            <hr>
            <div class="ps-3 pt-3">
                <a href="logout.php" class="text-danger">               
                    <i class="fa-solid fa-right-from-bracket">Logout</i>               
                </a>
            
            </div>
        </div>
        </div>
    </div>
    <div class="container text-center mt-5 custom-search-shadow position-relative z-1   bg-light" style="width: 700px;">
        <div class="d-flex justify-content-center align-items-center py-4" >
            <div>
                <label for="studentID" class="form-label fs-5">Enter Student ID:</label>
            </div>
            <div class="ps-4" style="width: 300px;">
                <input type="number" step="1" placeholder="" class="form-control">
            </div>
            <div class="ps-4">
                <button class="btn btn-info fs-5"> Search</button>
            </div>
        </div>
    </div>
    
    <div class="container pt-4 col-lg-6">
        <table class="table col-lg-12">
            <thead>
                <tr class="table-dark">
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Course</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>200999888</th>
                    <th>Ram m Mungcal</th>
                    <th>CCIS</th>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="container d-flex justify-content-center align-items-center pt-5 mt-5 custom-status pb-4" style="width: 1000px;">
        <div class="col-lg-8 text-center d-flex flex-column justify-content-center align-items-center">
            <label for="commentArea" class="form-label fs-6 fw-medium">Comment for declined student:</label>
                    <textarea class="form-control align-center" style="width: 400px;" name="commentArea" id="commentArea" rows="3" onclick="this.setSelectionRange(0, 0)">
                        
                    </textarea>
            <div class="pt-4">       
                <button class="btn btn-danger fs-5">Decline</button>      
            </div>   
        </div>
        <div class="col-lg-4">
            <button class="btn btn-success fs-5">Approve</button>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>