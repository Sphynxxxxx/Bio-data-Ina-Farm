<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TESDA Bio-Data Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            padding: 10px 0;
            background: linear-gradient(90deg, pink, lightgreen, skyblue);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            
        }
        .logo-container {
            display: flex;
            align-items: center;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-right: 20px;
            filter: drop-shadow(1px 1px 0 white) 
           

        }
        .title-container h1 {
            color:rgb(255, 255, 255);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0;
        }
        .subtitle {
            color:rgb(0, 0, 0);
            font-size: 1rem;
        }
        .card {
            height: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: contain;
        }
        .card-body {
            padding: 2rem;
        }
        .card-title {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
            padding: 10px 20px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .footer {
            background-color: #343a40;
            color: #fff;
            padding-top: 50px;
            height: auto;
            margin-top: 50px;
            width: 100%;
        }
        .option-description {
            min-height: 80px;
            margin-bottom: 1.5rem;
        }
        .developer-credit {
            font-size: 0.75rem;
            color: #adb5bd;
        }

        
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="logo-container">
                <img src="Images/ina farm logo.png" alt="INAFARM Logo" class="logo">
                <div class="title-container">
                    <h1> Ina Farmers Learning Site & Agri-Farm Inc. </h1>
                    <p class="subtitle">Agricultural Training Center and Agri-Tourism Farm</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="text-center mb-5">
            <h2>Bio-Data Management System</h2>
            <p class="lead">Please select the appropriate form to proceed</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <img src="Images/id-card_1154552.png" class="card-img-top" alt="Student Internship">
                    <div class="card-body text-center">
                        <h3 class="card-title">Student Internship</h3>
                        <a href="#" class="btn btn-primary btn-lg">Fill Internship Form</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow">
                    <img src="Images/tesda_logo.png" class="card-img-top" alt="TESDA Biodata">
                    <div class="card-body text-center">
                        <h3 class="card-title">TESDA Manpower Profile</h3>
                        <a href="user\tesda_biodata.php" class="btn btn-primary btn-lg">Fill NMIS Form</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 Ina Farmers Learning Site & Agri-Farm Inc.</p> 
                </div>
                <div class="col-md-6 text-md-end">
                    <p>All Rights Reserved</p>
                    <p class="developer-credit">Developed by:<br>Larry Denver Biaco<br>Vince Javier</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>