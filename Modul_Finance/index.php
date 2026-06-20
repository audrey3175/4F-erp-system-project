<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodSync - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; overflow: hidden; background-color: #ffffff; }
        
        /* Efek gradasi pudar untuk gambar gedung */
        .hero-img-mask {
            -webkit-mask-image: linear-gradient(to right, transparent 0%, black 40%);
            mask-image: linear-gradient(to right, transparent 0%, black 40%);
        }
        
        /* Gradasi tombol Sign In */
        .btn-signin {
            background: linear-gradient(to bottom, #4da4ff 0%, #0070f0 100%);
            box-shadow: 0 4px 10px rgba(0, 112, 240, 0.3);
        }
    </style>
</head>
<body class="relative h-screen w-screen">

    <div class="absolute right-0 top-0 w-[60%] h-full hero-img-mask z-0">
        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" 
             alt="Buildings" 
             class="w-full h-full object-cover object-left">
        <div class="absolute inset-0 bg-blue-50 mix-blend-overlay opacity-50"></div>
    </div>

    <div class="relative z-10 h-full flex flex-col justify-center px-16 lg:px-32 w-full lg:w-[50%]">
        
        <div class="mb-8">
            <h1 class="text-[60px] font-black text-[#004085] italic tracking-tight leading-none">FoodSync</h1>
            <p class="text-[11px] font-bold text-[#004085] tracking-widest uppercase mt-1 border-t-2 border-[#004085] pt-1 inline-block">THE SYSTEM OF INTEGRATED FOOD ERP</p>
        </div>

        <p class="text-[22px] font-medium leading-snug text-black mb-12">
            Integrated Enterprise System Analysis<br>
            Optimizing Business Processes Through<br>
            Technology and Data Integration
        </p>

        <div class="flex flex-col space-y-4 w-[180px]">
            <a href="login.php" class="btn-signin text-white rounded-full py-3 px-6 text-center font-bold text-[18px] transition-transform hover:scale-105 border border-blue-400">
                Sign In
            </a>
            <a href="#" class="bg-white text-[#004085] rounded-full py-3 px-6 text-center font-bold text-[18px] border-2 border-[#0070f0] shadow-md transition-transform hover:scale-105">
                Register
            </a>
        </div>

    </div>

    <div class="absolute bottom-6 w-full text-center z-10 pointer-events-none">
        <p class="text-[12px] text-slate-400 font-medium">Project Sistem Enterprise 4F @2026</p>
    </div>

</body>
</html>