<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ina Farmers Bio-Data Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1; 
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
            filter: drop-shadow(1px 1px 0 white);
        }
        
        .title-container h1 {
            color: rgb(255, 255, 255);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0;
        }
        
        .subtitle {
            color: rgb(0, 0, 0);
            font-size: 1rem;
        }
        
        .program-card {
            height: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .program-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .card-img-top {
            height: 200px;
            object-fit: contain;
            padding: 20px;
            background: rgba(248, 249, 250, 0.5);
        }
        
        .card-body {
            padding: 2rem;
            text-align: center;
        }
        
        .card-title {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
            padding: 12px 25px;
            font-weight: 500;
            border-radius: 50px;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .footer {
            background-color: #343a40;
            color: #fff;
            padding-top: 10px;
            height: 100px;
            margin-top: auto; 
            width: 100%;
        }

        .footer p {
            margin: 0;
            font-size: 0.85rem;
        }
        
        .developer-credit {
            font-size: 0.75rem;
            color: #adb5bd;
        }
        
        .program-description {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #dc3545;
        }
        
        .admin-link {
            position: fixed;
            bottom: 120px;
            right: 20px;
            background-color: rgba(52, 58, 64, 0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.9rem;
            z-index: 100;
            transition: all 0.3s;
        }
        
        .admin-link:hover {
            background-color: #343a40;
            color: white;
            transform: scale(1.05);
        }
        
        .hero-section {
            padding: 50px 0;
            background: linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.8)), url('images/farm-background.jpg');
            background-size: cover;
            background-position: center;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 10px;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 15px;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            max-width: 800px;
            margin: 0 auto 30px;
        }
        
        .feature-item {
            margin-bottom: 10px;
            color: #495057;
        }
        
        .feature-item i {
            color: #28a745;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="logo-container">
                <img src="Images/ina farm logo.png" alt="INAFARM Logo" class="logo">
                <div class="title-container">
                    <h1>Ina Farmers Learning Site & Agri-Farm Inc.</h1>
                    <p class="subtitle">Agricultural Training Center and Agri-Tourism Farm</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="hero-section">
            <h1 class="hero-title">Welcome to Our Bio-Data Management System</h1>
            <p class="hero-subtitle">Choose from our student internship program or TESDA training courses to enhance your skills and advance your career in agriculture</p>
            
            <div class="row justify-content-center">
                <!--<div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i> Hands-on agricultural training
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i> Industry-recognized certifications
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i> Experienced instructors
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i> Modern farming techniques
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i> Career placement assistance
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i> Flexible learning options
                            </div>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>

        <div class="text-center mb-5">
            <h2>Please select your program</h2>
            <p class="lead">Choose the option that best fits your educational and career goals</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
                <div class="program-card card shadow h-100">
                    <div class="text-center pt-4">
                        <i class="bi bi-mortarboard card-icon"></i>
                    </div>
                    <img src="Images/id-card_1154552.png" class="card-img-top" alt="Student Internship">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title">Student Internship Program</h3>
                        <p class="program-description">Gain practical experience through our structured internship program designed for students pursuing agricultural education.</p>
                        <ul class="text-start mb-4">
                            <li>Hands-on training in modern agricultural practices</li>
                            <li>Mentorship from industry professionals</li>
                            <li>Academic credit for your educational program</li>
                            <li>Networking opportunities with potential employers</li>
                        </ul>
                        <div class="mt-auto">
                            <a href="user/student_internship.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-pencil-square me-2"></i>Apply for Internship
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="program-card card shadow h-100">
                    <div class="text-center pt-4">
                        <i class="bi bi-award card-icon"></i>
                    </div>
                    <img src="Images/tesda_logo.png" class="card-img-top" alt="TESDA Training">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title">TESDA Training Programs</h3>
                        <p class="program-description">Enroll in our TESDA-accredited training programs to acquire certified skills and qualifications in agricultural specialties.</p>
                        <ul class="text-start mb-4">
                            <li>Nationally recognized certification</li>
                            <li>Competency-based training methodology</li>
                            <li>Industry-aligned curriculum</li>
                            <li>Career advancement opportunities</li>
                        </ul>
                        <div class="mt-auto">
                            <a href="user/tesda_biodata.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-journal-text me-2"></i>Register for TESDA Training
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="admin.php" class="admin-link">
        <i class="bi bi-shield-lock"></i> Admin Login
    </a>

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