<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/green-paw.png">
    <link rel="stylesheet" href="assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <title>Southern Leyte Veterinary Clinic</title>
    <style>
        * {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 50%, #d1fae5 100%);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(-45deg);
            }

            50% {
                transform: translateY(-15px) rotate(-45deg);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        header {
            animation: fadeIn 0.8s ease-out;
        }

        footer {
            animation: fadeIn 0.8s ease-out;
        }

        .logo-section {
            animation: slideInLeft 0.8s ease-out;
        }

        .nav-link {
            position: relative;
            display: inline-block;
            animation: fadeIn 0.8s ease-out;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 50%;
            background-color: #15803d;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .login-btn {
            animation: fadeIn 0.8s ease-out 0.3s backwards;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(21, 128, 61, 0.2);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(21, 128, 61, 0.3);
        }

        .hero-text {
            animation: slideInLeft 1s ease-out 0.3s backwards;
        }

        .hero-subtext {
            animation: slideInLeft 1s ease-out 0.5s backwards;
        }

        .about-text {
            animation: slideInLeft 1s ease-out 0.3s backwards;
        }

        .about-subtext {
            animation: slideInLeft 1s ease-out 0.5s backwards;
        }

        .service-section-title {
            animation: fadeInUp 1s ease-out;
        }

        .service-card {
            animation: fadeInUp 1s ease-out backwards;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .service-card:hover {
            transform: translateY(-8px);
        }

        .service-icon {
            transition: transform 0.3s ease;
        }

        .service-card:hover .service-icon {
            transform: scale(1.1);
        }

        .service-card:nth-child(1) {
            animation-delay: 0.2s;
        }

        .service-card:nth-child(2) {
            animation-delay: 0.3s;
        }

        .service-card:nth-child(3) {
            animation-delay: 0.4s;
        }

        .service-card:nth-child(4) {
            animation-delay: 0.5s;
        }

        .developer-section-title {
            animation: fadeInUp 1s ease-out;
        }

        .developer-card {
            animation: fadeInUp 1s ease-out backwards;
        }

        .developer-card:nth-child(1) {
            animation-delay: 0.2s;
        }

        .developer-card:nth-child(2) {
            animation-delay: 0.3s;
        }

        .developer-card:nth-child(3) {
            animation-delay: 0.4s;
        }

        .developer-card:nth-child(4) {
            animation-delay: 0.5s;
        }

        .contact-section {
            animation: slideInLeft 0.8s ease-out;
        }


        .contact-text {
            animation: slideInLeft 0.8s ease-out;
        }

        .paw-background {
            animation: float 6s ease-in-out infinite;
            opacity: 0.15;
        }

        .dog-image {
            animation: slideInRight 1s ease-out 0.3s backwards;
            transition: transform 0.5s ease;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.15));
        }

        .dog-image:hover {
            transform: scale(1.05);
        }

        /* Mobile menu styles */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            right: -100%;
            width: 70%;
            max-width: 300px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            transition: right 0.3s ease;
            z-index: 1000;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .mobile-menu.active {
            right: 0;
        }

        .mobile-menu nav {
            display: flex;
            flex-direction: column;
            padding: 80px 30px 30px;
            gap: 20px;
        }

        .mobile-menu .nav-link {
            font-size: 1.1rem;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .mobile-menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .mobile-menu-overlay.active {
            display: block;
        }

        .menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #15803d;
            cursor: pointer;
            padding: 5px;
            z-index: 1001;
        }

        @media (max-width: 768px) {
            nav {
                display: none;
            }

            .menu-btn {
                display: block;
            }

            .mobile-menu {
                display: block;
            }

            .grid-cols-2 {
                grid-template-columns: 1fr;
            }

            .hero-text {
                font-size: 2rem;
                padding: 2rem;
            }

            .logo-text {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen w-full overflow-y-auto text-gray-800 max-w-[1444px] mx-auto">
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <nav>
            <a href="#header" class="nav-link hover:text-green-900 transition font-semibold">Home</a>
            <a href="#about" class="nav-link hover:text-green-900 transition font-semibold">About</a>
            <a href="#services" class="nav-link hover:text-green-900 transition font-semibold">Services</a>
            <a href="#contact" class="nav-link hover:text-green-900 transition font-semibold">Contact</a>
            <a href="login.php"
                class="login-btn bg-green-700 text-white px-6 py-3 rounded-full font-medium hover:bg-green-800 transition text-center">Login</a>
        </nav>
    </div>

    <main>
        <header id="header"
            class="flex justify-between items-center text-green-700 px-6 py-8 bg-white/40 backdrop-blur-sm w-full">
            <!-- Logo -->
            <div class="logo-section text-xl font-semibold tracking-wide flex justify-between items-center gap-4">
                <img src="assets/img/green-paw.png" class="w-10 transition-transform hover:rotate-12 hover:scale-110"
                    alt="Clinic Logo">
                <h2 class="logo-text text-green-800 hidden sm:inline">SOUTHERN LEYTE VETERINARY CLINIC</h2>
                <h2 class="logo-text text-green-800 inline sm:hidden">SLVC</h2>
            </div>

            <!-- Desktop Nav -->
            <nav class="flex space-x-8 justify-between items-center">
                <a href="#header" class="nav-link hover:text-green-900 transition font-semibold">Home</a>
                <a href="#about" class="nav-link hover:text-green-900 transition font-semibold">About</a>
                <a href="#services" class="nav-link hover:text-green-900 transition font-semibold">Services</a>
                <a href="#contact" class="nav-link hover:text-green-900 transition font-semibold">Contact</a>
                <a href="login.php"
                    class="login-btn bg-green-700 text-white px-6 py-2 rounded-full font-medium hover:bg-green-800 transition text-sm">Login</a>
            </nav>

            <button class="menu-btn" id="menuBtn">
                <i class="fa fa-bars"></i>
            </button>
        </header>

        <section class="grid grid-cols-2 h-screen w-full overflow-hidden">
            <!-- Left Side: Text -->
            <div class="flex justify-center items-center px-8">
                <div class="max-w-2xl">
                    <h1
                        class="hero-text text-4xl md:text-5xl font-bold text-green-600 text-center md:text-left tracking-wide leading-tight mb-6">
                        YOUR TRUSTED PARTNER FOR COMPLETE <span class="text-green-900">ANIMAL HEALTH</span> AND
                        WELLNESS.
                    </h1>
                    <h3
                        class="hero-subtext text-lg md:text-xl text-gray-700 text-center md:text-left font-normal leading-relaxed">
                        Providing compassionate care and expert veterinary services to keep your furry friends healthy
                        and happy.
                    </h3>
                </div>
            </div>

            <!-- Right Side: Background + Dog Image -->
            <div class="relative flex justify-center items-center overflow-hidden">
                <!-- Green paw as background -->
                <div class="paw-background absolute inset-0 bg-no-repeat bg-center bg-contain"
                    style="background-image: url('assets/img/green-paw.png'); transform: rotate(-45deg);">
                </div>

                <!-- Dog image on top -->
                <img src="assets/img/dog.png" alt="Happy Dog"
                    class="dog-image relative z-10 max-h-full max-w-full object-contain">
            </div>
        </section>

        <section id="about"
            class="flex justify-center flex-col py-16 px-6 md:px-10 min-h-screen bg-cover bg-center bg-no-repeat relative"
            style="background-image: url('assets/img/about-section.webp');">

            <!-- Optional: Add overlay for better text contrast -->
            <div class="absolute inset-0 bg-black/30"></div>

            <!-- Your content goes here (on top of background) -->
            <div class="relative z-10 max-w-4xl mx-auto">
                <h1
                    class="about-text text-4xl md:text-5xl font-bold mb-6 text-white drop-shadow-lg text-center sm:text-start">
                    Who We Are</h1>
                <p
                    class="about-subtext text-lg md:text-xl text-white leading-relaxed drop-shadow-md mb-8 text-center sm:text-start">
                    At Southern Leyte Veterinary Clinic, we are dedicated to providing compassionate and
                    comprehensive care for your beloved pets. Our team of experienced veterinarians and staff work
                    tirelessly to ensure the health, happiness, and well-being of every animal that comes through our
                    doors.
                </p>
                <div class="rounded-lg p-6 mb-4 shadow-lg">
                    <h2 class="about-text text-2xl font-bold text-white drop-shadow-lg mb-2 text-center sm:text-start">
                        MISSION</h2>
                    <p class="about-subtext text-white text-lg mb-4 text-center sm:text-start">
                        To make Southern Leyte sufficient in good quality breeder stocks, safe and wholesome meat
                        products and by-products.
                    </p>
                    <h2 class="about-text text-2xl font-bold  text-white drop-shadow-lg mb-2 text-center sm:text-start">
                        VISION</h2>
                    <p class="about-subtext text-white text-lg text-center sm:text-start">
                        Excellent veterinary service provider ensuring food safety, sufficiency and security through
                        livestock and poultry sustainable development programs.
                    </p>
                </div>
            </div>
        </section>

        <section id="services" class="max-w-7xl mx-auto py-16 px-4 md:px-8">
            <!-- Section Header -->
            <div class="service-section-title text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-green-800 mb-4">SERVICES OFFERED</h1>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    We provide a wide range of veterinary and livestock support services to ensure animal health, food
                    safety, and sustainable development in Southern Leyte.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Card 1 -->
                <div class="service-card bg-white rounded-3xl shadow-lg p-8 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <i class="service-icon fas fa-seedling text-4xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-green-800 mb-2">Livestock Production Support Services</h3>
                    <p class="text-gray-700 mb-4 text-start"><i
                            class="fas fa-check-circle text-green-600 mr-2"></i>Provision of
                        livelihood opportunities through livestock breeder loan
                        projects, swine and poultry grow-out activities.</p>
                </div>
                <!-- Card 2 -->
                <div class="service-card bg-white rounded-3xl shadow-lg p-8 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mb-4">
                        <i class="service-icon fas fa-shield-alt text-4xl text-emerald-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-green-800 mb-2">Veterinary Regulatory Support Services</h3>
                    <ul class="text-gray-700 text-left space-y-2 mb-2">
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Prevention and control of animal
                            diseases through field disease diagnosis, treatment and vaccinations.</li>
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Veterinary quarantine program through
                            livestock movement monitoring and regulation, border control for Trans-Boundary Animal
                            Diseases.</li>
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Provision of vaccines to control
                            Rabies, Hemorrhagic Septicemia, Newcastle Disease, Fowl Pox, Fowl Coryza, Hog Cholera and
                            other livestock and poultry viral and bacterial diseases.</li>
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Implementation of food hygiene and
                            food safety regulations, feeds and veterinary drugs establishment regulations.</li>
                    </ul>
                </div>
                <!-- Card 3 -->
                <div class="service-card bg-white rounded-3xl shadow-lg p-8 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                        <i class="service-icon fas fa-graduation-cap text-4xl text-teal-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-green-800 mb-2">Extension, Education and Training Services</h3>
                    <ul class="text-gray-700 text-left space-y-2 mb-2">
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Assist in organizing livestock
                            farmers.</li>
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Conduct and provide capability
                            development trainings on livestock production and financial management to farmer
                            cooperatives and associations.</li>
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Conduct and provide technical
                            management trainings to livestock extension workers.</li>
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Implement and monitor all livestock
                            enterprise development projects.</li>
                    </ul>
                </div>
                <!-- Card 4 -->
                <div class="service-card bg-white rounded-3xl shadow-lg p-8 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-cyan-100 rounded-full flex items-center justify-center mb-4">
                        <i class="service-icon fas fa-chart-line text-4xl text-cyan-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-green-800 mb-2">Market Development and Support Services</h3>
                    <ul class="text-gray-700 text-left space-y-2 mb-2">
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Establishment and monitoring of all
                            slaughterhouses.</li>
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Monitoring and assessments of
                            livestock inventory, farm gate and retail prices of livestock and poultry products and
                            by-products.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Developers Acknowledgement Section -->
        <section class="flex justify-center flex-col py-16 px-6 md:px-10 bg-white/50 backdrop-blur-sm">
            <div class="mb-12 developer-section-title">
                <h1 class="text-center text-3xl md:text-5xl font-bold text-green-800 mb-3">DEVELOPERS ACKNOWLEDGEMENT
                </h1>
                <p class="text-center text-gray-600 text-base md:text-lg">Meet the talented team behind this project</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10 max-w-7xl mx-auto w-full">
                <div class="flex flex-col items-center group developer-card">
                    <div
                        class="w-45 h-45 md:w-55 md:h-55 border-4 border-green-600 rounded-full overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:border-green-700 mb-4">
                        <img src="assets/img/casipong.webp" alt="Developer 1" class="w-full h-full object-cover">
                    </div>
                    <h3
                        class="text-lg md:text-xl font-semibold text-green-800 group-hover:text-green-600 transition text-center">
                        Anthony Casipong</h3>
                    <p class="text-gray-600 text-sm text-center">Leader</p>
                </div>
                <div class="flex flex-col items-center group developer-card">
                    <div
                        class="w-45 h-45 md:w-55 md:h-55 border-4 border-green-600 rounded-full overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:border-green-700 mb-4">
                        <img src="assets/img/mori.webp" alt="Developer 2" class="w-full h-full object-cover">
                    </div>
                    <h3
                        class="text-lg md:text-xl font-semibold text-green-800 group-hover:text-green-600 transition text-center">
                        Dianne Mori</h3>
                    <p class="text-gray-600 text-sm text-center">Member</p>
                </div>
                <div class="flex flex-col items-center group developer-card">
                    <div
                        class="w-45 h-45 md:w-55 md:h-55 border-4 border-green-600 rounded-full overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:border-green-700 mb-4">
                        <img src="assets/img/mancao.webp" alt="Developer 3" class="w-full h-full object-cover">
                    </div>
                    <h3
                        class="text-lg md:text-xl font-semibold text-green-800 group-hover:text-green-600 transition text-center">
                        Jyrus Jiv Mancao</h3>
                    <p class="text-gray-600 text-sm text-center">Member</p>
                </div>
                <div class="flex flex-col items-center group developer-card">
                    <div
                        class="w-45 h-45 md:w-55 md:h-55 border-4 border-green-600 rounded-full overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:border-green-700 mb-4">
                        <img src="assets/img/murcia.webp" alt="Developer 4" class="w-full h-full object-cover">
                    </div>
                    <h3
                        class="text-lg md:text-xl font-semibold text-green-800 group-hover:text-green-600 transition text-center">
                        Archell Murcia</h3>
                    <p class="text-gray-600 text-sm text-center">Member</p>
                </div>
            </div>
        </section>
        <footer id="contact">
            <div class="flex flex-col md:flex-row items-end justify-between w-full overflow-hidden">
                <!-- Left Section -->
                <div class="p-6 md:p-10 flex flex-col gap-6 md:w-1/2">
                    <div class="contact-section flex items-center gap-4">
                        <img src="assets/img/green-paw.png"
                            class="w-10 transition-transform hover:rotate-12 hover:scale-110" alt="Clinic Logo">
                        <h2 class="text-xl font-semibold text-green-800 tracking-wide">
                            SOUTHERN LEYTE VETERINARY CLINIC
                        </h2>
                    </div>

                    <div class="contact-text">
                        <h3 class="text-lg font-semibold mb-2">CONTACT INFORMATION</h3>
                        <address class="not-italic leading-relaxed">
                            Provincial Veterinary Services Office, Capitol Site
                            <br>Asuncion, Maasin City
                            <br>Southern Leyte, 6600<br>
                            09306231352<br>
                            <a href="mailto:pvsosl@gmail.com" class="text-green-700 hover:underline">
                                pvsosl@gmail.com
                            </a>
                        </address>
                        <a href="https://www.facebook.com/pvso.southernleyte" class="text-green-700 hover:underline">
                            <i class="fa-brands fa-facebook"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Section (Cat Image) -->
                <div class="md:w-1/2 flex justify-center md:justify-end items-end">
                    <img src="assets/img/cat.webp" alt="cat"
                        class="block h-auto max-h-[350px] object-contain object-bottom select-none pointer-events-none">
                </div>
            </div>
        </footer>
    </main>

    <script>
        // Mobile menu functionality
        const menuBtn = document.getElementById('menuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const mobileMenuLinks = document.querySelectorAll('.mobile-menu .nav-link, .mobile-menu .login-btn');

        function toggleMenu() {
            mobileMenu.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
        }

        menuBtn.addEventListener('click', toggleMenu);
        mobileMenuOverlay.addEventListener('click', toggleMenu);

        // Close menu when clicking on a link
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', toggleMenu);
        });
    </script>
</body>

</html>