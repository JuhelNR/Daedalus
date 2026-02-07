<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Daedalus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .heading-font {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .orange-gradient {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
        }

        .flower-decoration {
            position: absolute;
            width: 100px;
            height: 100px;
            opacity: 0.15;
        }
    </style>
</head>

<body class="bg-gray-50 antialiased">
    <!-- Navigation -->
    <nav class="bg-white/95 backdrop-blur-sm py-4 px-6 border-b border-gray-100">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="index.html" class="flex items-center gap-2">
                <div class="w-8 h-8 orange-gradient rounded-lg flex items-center justify-center">
                    <i class="fas fa-feather text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold heading-font text-gray-900">Daedalus</span>
            </a>
            <a href="index.html" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to Home
            </a>
        </div>
    </nav>

    <!-- Login/Signup Container -->
    <div class="min-h-screen flex items-center justify-center px-6 py-12 relative overflow-hidden">
        <!-- Decorative flowers -->
        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='40' fill='%23f59e0b' opacity='0.6'/%3E%3Ccircle cx='50' cy='50' r='25' fill='%23fbbf24'/%3E%3C/svg%3E" class="flower-decoration top-10 left-10" alt="">
        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='40' fill='%23f59e0b' opacity='0.6'/%3E%3Ccircle cx='50' cy='50' r='25' fill='%23fbbf24'/%3E%3C/svg%3E" class="flower-decoration bottom-10 right-10" alt="">

        <div class="max-w-md w-full">
            <!-- Tab Headers -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="flex border-b border-gray-100">
                    <button id="loginTab" class="flex-1 py-4 text-center font-semibold transition-all border-b-2 border-orange-500 text-orange-600">
                        Sign In
                    </button>
                    <button id="signupTab" class="flex-1 py-4 text-center font-semibold text-gray-500 hover:text-gray-700 transition-all">
                        Sign Up
                    </button>
                </div>

                <!-- Login Form -->
                <div id="loginForm" class="p-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2 heading-font">Welcome Back!</h2>
                    <p class="text-gray-600 mb-8">Sign in to continue building your resume</p>

                    <form class="space-y-5" action="includes/auth.php" method="post">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="hidden" name="action" value="login">
                            <input name="email" placeholder="juhel.nr@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input name="password" type="password" placeholder="••••••••" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                            <a href="#" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Forgot password?</a>
                        </div>
                        <button type="submit" class="w-full orange-gradient text-white py-3.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-orange-500/30 transition-all">
                            Sign In
                        </button>
                    </form>

                    <div class="mt-8">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500">Or continue with</span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                                <i class="fab fa-google text-red-500 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Google</span>
                            </button>
                            <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                                <i class="fab fa-linkedin text-blue-700 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">LinkedIn</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Signup Form -->
                <div id="signupForm" class="p-8 hidden">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2 heading-font">Create Account</h2>
                    <p class="text-gray-600 mb-8">Start building your professional resume today</p>

                    <form class="space-y-5" action="includes/auth.php" method="post">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="hidden" name="action" value="sign-up">
                                <input name="fname" type="text" placeholder="John" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input name="lname" type="text" placeholder="Doe" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input name="email" type="email" placeholder="john.doe@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input name="password" type="password" placeholder="••••••••" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input name="confirm_password" type="password" placeholder="••••••••" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        </div>
                        <label class="flex items-start">
                            <input type="checkbox" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500 mt-1">
                            <span class="ml-2 text-sm text-gray-600">I agree to the <a href="#" class="text-orange-600 hover:text-orange-700 font-medium">Terms of Service</a> and <a href="#" class="text-orange-600 hover:text-orange-700 font-medium">Privacy Policy</a></span>
                        </label>
                        <button type="submit" class="w-full orange-gradient text-white py-3.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-orange-500/30 transition-all">
                            Create Account
                        </button>
                    </form>

                    <div class="mt-8">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500">Or sign up with</span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                                <i class="fab fa-google text-red-500 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Google</span>
                            </button>
                            <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all">
                                <i class="fab fa-linkedin text-blue-700 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">LinkedIn</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guest Access -->
            <div class="mt-8 text-center">
                <p class="text-gray-700 mb-3">Don't want to create an account?</p>
                <a href="builder.html" class="inline-flex items-center gap-2 text-orange-600 font-semibold hover:text-orange-700">
                    Continue as Guest
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
        </div>
    </div>

    <script>
        const loginTab = document.getElementById('loginTab');
        const signupTab = document.getElementById('signupTab');
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');

        loginTab.addEventListener('click', () => {
            loginTab.classList.add('text-orange-600', 'border-b-2', 'border-orange-500');
            loginTab.classList.remove('text-gray-500');
            signupTab.classList.remove('text-orange-600', 'border-b-2', 'border-orange-500');
            signupTab.classList.add('text-gray-500');
            loginForm.classList.remove('hidden');
            signupForm.classList.add('hidden');
        });

        signupTab.addEventListener('click', () => {
            signupTab.classList.add('text-orange-600', 'border-b-2', 'border-orange-500');
            signupTab.classList.remove('text-gray-500');
            loginTab.classList.remove('text-orange-600', 'border-b-2', 'border-orange-500');
            loginTab.classList.add('text-gray-500');
            signupForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
        });
    </script>
</body>

</html>