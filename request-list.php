<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>Request List</title>
    <style>
        *{
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container mt-5 pb-3">
        <h1>Request List</h1>
    </div>
    <nav aria-label="breadcrumb" class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="faculty-dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Request List</li>
        </ol>
    </nav>
    <div class="container d-flex">
        <div>
            <input type="radio" name="list" id="" class="form-check-input" checked>
            <label for="">Student List</label>
        </div>
        <div class="ps-3">
            <input type="radio" name="list" id="" class="form-check-input">
            <label for="">Employee List</label>
        </div>
    </div>
    <div class="container">
        <table class="table table-bordered table-striped table-hover mt-3">
            <thead>
                <tr class="table-dark">
                    <th>ID No.</th>
                    <th>Name</th>
    
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>0121300331</th>
                    <th>Ram Yturralde</th>
                    
                </tr>
            </tbody>
        </table>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>