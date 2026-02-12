<?php
require_once('includes/middleware.php');

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Get user data from session (middleware ensures user is logged in)
$firstName = $_SESSION['fname'] ?? 'User';
$lastName = $_SESSION['lname'] ?? '';
$email = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Daedalus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 text-[14px] text-gray-800" style="font-family: 'Inter', sans-serif;">

    <!-- Sidebar -->
    <div class="fixed left-0 top-0 bottom-0 w-60 bg-white border-r border-gray-200 flex flex-col">

        <!-- Logo -->
        <div class="p-5 border-b border-gray-200">
            <a href="index.html" class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-amber-600 rounded-md flex items-center justify-center">
                    <i class="fas fa-feather text-white text-sm"></i>
                </div>
                <span class="text-lg font-bold">Daedalus</span>
            </a>
        </div>

        <!-- Navigation -->
        <!-- Navigation -->
        <nav class="flex-1 py-4">

            <a href="dashboard.php"
                class="flex items-center px-4 py-[10px] mx-2 my-[2px] rounded-[6px] text-gray-500 text-[14px] font-medium hover:bg-gray-100 hover:text-gray-800 transition-all duration-150">
                <i class="fas fa-th-large w-5 mr-3 text-[16px]"></i>
                <span>Dashboard</span>
            </a>

            <a href="my-resumes.php"
                class="flex items-center px-4 py-[10px] mx-2 my-[2px] rounded-[6px] text-gray-500 text-[14px] font-medium hover:bg-gray-100 hover:text-gray-800 transition-all duration-150">
                <i class="fas fa-file-alt w-5 mr-3 text-[16px]"></i>
                <span>My Resumes</span>
            </a>

            <a href="templates.php"
                class="flex items-center px-4 py-[10px] mx-2 my-[2px] rounded-[6px] text-gray-500 text-[14px] font-medium hover:bg-gray-100 hover:text-gray-800 transition-all duration-150">
                <i class="fa-brands fa-themeco w-5 mr-3 text-[16px]"></i>
                <span>Templates</span>
            </a>

            <a href="#"
                class="flex items-center mb-7 px-4 py-[10px] mx-2 my-[2px] rounded-[6px] text-gray-500 text-[14px] font-medium hover:bg-gray-100 hover:text-gray-800 transition-all duration-150">
                <i class="fas fa-share-nodes w-5 mr-3 text-[16px]"></i>
                <span>Resignation Letters</span>
            </a>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-2 mx-2"></div>

            <!-- Active Item -->
            <a href="account-settings.php"
                class="flex items-center mt-5 px-4 py-[10px] mx-2 my-[2px] rounded-[6px] bg-amber-100 text-amber-600 text-[14px] font-medium">
                <i class="fas fa-cog w-5 mr-3 text-[16px]"></i>
                <span>Settings</span>
            </a>

            <a href="help.php"
                class="flex items-center px-4 py-[10px] mx-2 my-[2px] rounded-[6px] text-gray-500 text-[14px] font-medium hover:bg-gray-100 hover:text-gray-800 transition-all duration-150">
                <i class="fas fa-question-circle w-5 mr-3 text-[16px]"></i>
                <span>Help</span>
            </a>

        </nav>


        <!-- User Profile -->
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                        <?php echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1)); ?>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">
                            <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?>
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            <?php echo htmlspecialchars($email); ?>
                        </p>
                    </div>
                </div>
                <a href="includes/logout.php" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Main Content -->
    <div class="ml-60 min-h-screen">

        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-8 py-4">
            <h1 class="text-2xl font-bold">Account Settings</h1>
        </div>

        <!-- Centered Content -->
        <div class="p-8">
            <div class="max-w-3xl mx-auto space-y-6">

                <!-- Personal Info -->
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 font-semibold">
                        Personal Information
                    </div>
                    <div class="p-6">
                        <form class="space-y-4">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">First name</label>
                                    <input type="text"
                                        value="<?php echo htmlspecialchars($firstName); ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Last name</label>
                                    <input type="text"
                                        value="<?php echo htmlspecialchars($lastName); ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Email address</label>
                                <input type="email"
                                    value="<?php echo htmlspecialchars($email); ?>"
                                    disabled
                                    class="w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-100 text-gray-500">
                                <p class="text-xs text-gray-500 mt-1">Your email cannot be changed</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Phone number</label>
                                <input type="tel"
                                    placeholder="+1 (555) 000-0000"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Country</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                                    <option>United States</option>
                                    <option>United Kingdom</option>
                                    <option>Canada</option>
                                </select>
                            </div>

                            <div class="text-right">
                                <button type="submit"
                                    class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-5 py-2 rounded-md transition">
                                    Save changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password -->
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 font-semibold">
                        Password
                    </div>
                    <div class="p-6">
                        <form class="space-y-4">

                            <div>
                                <label class="block text-sm font-medium mb-1">Current password</label>
                                <input type="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">New password</label>
                                <input type="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Confirm new password</label>
                                <input type="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                            </div>

                            <div class="text-right">
                                <button type="submit"
                                    class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-5 py-2 rounded-md transition">
                                    Update password
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>

</html>