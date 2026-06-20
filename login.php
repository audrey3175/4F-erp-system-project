<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['role']) && $_SESSION['role'] == 'Finance') {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if (isset($_POST['btn_login'])) {
    $email = $_POST['email']; 
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['email'] = $data['email'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'Finance') {
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Akses ditolak! Anda bukan dari divisi Finance.";
        }
    } else {
        $error = "Email atau Password tidak valid!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - FoodSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-login-outer {
            background-color: #0d2b4d;
            background-image: url('https://images.unsplash.com/photo-1554469384-e58fac16e23a?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-blend-mode: overlay;
        }
        .glass-card {
            background: rgba(30, 30, 30, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body class="bg-login-outer h-screen w-screen flex items-center justify-center p-6">

    <div class="w-full max-w-[1000px] h-[600px] bg-white rounded-[40px] shadow-2xl flex overflow-hidden">
        
        <div class="w-1/2 px-14 flex flex-col justify-center relative bg-white">
            <div class="text-center mb-8">
                <h2 class="text-[36px] font-black text-[#003E7B] leading-none mb-2">Sign In</h2>
                <p class="text-[14px] text-[#003E7B] font-medium">Please enter your details</p>
            </div>

            <?php if($error != ''): ?>
                <div class="bg-red-50 text-red-500 p-2 rounded-lg text-xs font-bold mb-4 text-center border border-red-200">
                    <?= $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-5">
                    <label class="block text-[13px] font-bold text-black mb-2">Email</label>
                    <input type="email" name="email" placeholder="E.g. nayla.mardhiyah24@mhs.uinjkt.ac.id" required
                           class="w-full h-[45px] px-4 rounded-full border-2 border-[#4a90e2] focus:outline-none focus:ring-2 focus:ring-blue-400 text-[13px] text-slate-700 placeholder-slate-300">
                </div>
                
                <div class="mb-5">
                    <label class="block text-[13px] font-bold text-black mb-2">Password</label>
                    <input type="password" name="password" placeholder="••••••••" required
                           class="w-full h-[45px] px-4 rounded-full border-2 border-[#4a90e2] focus:outline-none focus:ring-2 focus:ring-blue-400 text-[13px] text-slate-700 placeholder-slate-300">
                </div>
                
                <div class="flex items-center justify-between mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-[12px] font-bold text-black">Remember log in activity</span>
                    </label>
                    <a href="#" class="text-[11px] text-slate-400 hover:text-[#003E7B] transition-colors">forgot password?</a>
                </div>

                <button type="submit" name="btn_login" 
                        class="w-full h-[50px] bg-[#053c7a] hover:bg-[#032a56] text-white rounded-full font-bold text-[16px] transition-all shadow-md">
                    Sign In
                </button>
            </form>
        </div>

        <div class="w-1/2 relative">
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                 alt="Building Facade" 
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-[#0d2b4d]/40 mix-blend-multiply"></div>
            
            <div class="absolute inset-0 flex items-center justify-center p-10">
                <div class="glass-card w-full h-[80%] rounded-[30px] flex flex-col items-center justify-center p-8 text-white text-center shadow-2xl">
                    <h2 class="text-[42px] font-bold leading-tight mb-2 drop-shadow-md">Hey,<br>Welcome Back!</h2>
                    <p class="text-[15px] font-medium text-white/90 mb-12 drop-shadow-sm">We hope you had a great day</p>
                    
                    <p class="text-[13px] text-white/80 mb-3 font-medium">Not yet a member?</p>
                    <a href="#" class="w-[180px] h-[45px] bg-[#0070f0] hover:bg-[#005bb5] text-white rounded-full flex items-center justify-center font-bold text-[15px] transition-all shadow-lg border border-blue-400/50">
                        Register
                    </a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>