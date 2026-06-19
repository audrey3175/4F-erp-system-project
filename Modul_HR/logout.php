<?php
session_start();

// Jika tombol YES ditekan
if (isset($_POST['confirm_logout'])) {
    session_destroy();
    header("Location: homepage.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Out - FoodSync ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            margin: 0; padding: 0; height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex; align-items: center; justify-content: center;
            background: url('Plaza_Sudirman-Marein_-_panoramio.jpg.jpg') center/cover;
            position: relative;
        }
        
        body::before {
            content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.7) 0%, rgba(13, 110, 253, 0.4) 100%);
            backdrop-filter: blur(2px);
            z-index: 0;
        }
        
        .logout-box {
            position: relative; z-index: 1;
            background: rgba(255, 255, 255, 0.5); 
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5); 
            border-radius: 35px;
            padding: 50px 40px; text-align: center; width: 380px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .icon-run { color: #004a8f; font-size: 55px; margin-bottom: 20px; }
        .logout-text { font-size: 22px; font-weight: 800; color: #003366; line-height: 1.3; margin-bottom: 30px; letter-spacing:-0.5px;}
        
        .btn-container { display: flex; flex-direction: column; gap: 15px; align-items: center;}
        
        .btn-no { 
            background: linear-gradient(180deg, #e60000 0%, #a30000 100%); 
            color: white; border: none; border-radius: 50px; 
            padding: 10px 0; font-weight: 800; font-size: 15px; 
            text-decoration: none; display: inline-block; width: 140px;
            box-shadow: 0 5px 15px rgba(230, 0, 0, 0.3);
            transition: 0.2s;
        }
        .btn-no:hover { transform: translateY(-2px); color: white;}
        
        .btn-yes { 
            background-color: white; color: #003366; 
            border: 2px solid #a30000; border-radius: 50px; 
            padding: 10px 0; font-weight: 800; font-size: 15px; 
            width: 140px; cursor: pointer; transition: 0.2s;
        }
        .btn-yes:hover { background-color: #f8f9fa; transform: translateY(-2px);}
    </style>
</head>
<body>

    <div class="logout-box">
        <i class="fas fa-running icon-run"></i>
        <div class="logout-text">Are you sure want to<br>logging out?</div>
        
        <form method="POST">
            <div class="btn-container">
                <!-- Tombol NO -->
                <button type="button" class="btn-no" onclick="history.back()">NO</button>
                <!-- Tombol YES -->
                <button type="submit" name="confirm_logout" class="btn-yes">YES</button>
            </div>
        </form>
    </div>

</body>
</html>