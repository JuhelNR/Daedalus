<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['uid'])) {
    header('location: login.php');
    exit();
}

// Get user data from session
$firstName = $_SESSION['fname'] ?? '';
$lastName = $_SESSION['lname'] ?? '';
$email = $_SESSION['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Daedalus</title>
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
    </style>
</head>

<body class="bg-gray-50 antialiased">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="index.html" class="flex items-center gap-2">
                    <div class="w-8 h-8 orange-gradient rounded-lg flex items-center justify-center">
                        <i class="fas fa-feather text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-bold heading-font text-gray-900">Daedalus</span>
                </a>
                <div class="flex items-center gap-3">
                    <a href="builder.php">
                        <button class="hidden sm:flex items-center gap-2 text-gray-700 hover:text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                            <i class="fas fa-file-alt"></i>
                            <span>My Resumes</span>
                        </button>
                    </a>
                    <div class="w-8 h-8 orange-gradient rounded-full flex items-center justify-center text-white text-sm font-semibold cursor-pointer">
                        <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2 heading-font">Account Settings</h1>
            <p class="text-gray-600">Manage your account information and preferences</p>
        </div>

        <!-- Settings Sections -->
        <div class="space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>
                </div>
                <div class="p-6">
                    <form action="update_profile.php" method="POST" class="space-y-5">
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First name</label>
                                <input
                                    type="text"
                                    name="firstName"
                                    value="<?php echo htmlspecialchars($firstName); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none text-sm"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last name</label>
                                <input
                                    type="text"
                                    name="lastName"
                                    value="<?php echo htmlspecialchars($lastName); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none text-sm"
                                    required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
                            <input
                                type="email"
                                name="email"
                                value="<?php echo htmlspecialchars($email); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm text-gray-600"
                                disabled>
                            <p class="text-xs text-gray-500 mt-1">Your email address cannot be changed</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone number</label>
                            <input
                                type="tel"
                                name="phone"
                                placeholder="+1 (555) 000-0000"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none text-sm">
                                <option>United States</option>
                                <option>United Kingdom</option>
                                <option>Canada</option>
                                <option>Australia</option>
                                <option>Germany</option>
                                <option>France</option>
                                <option>Spain</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                                Save changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Password</h2>
                </div>
                <div class="p-6">
                    <form action="change_password.php" method="POST" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current password</label>
                            <input
                                type="password"
                                name="currentPassword"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none text-sm"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New password</label>
                            <input
                                type="password"
                                name="newPassword"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none text-sm"
                                required>
                            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm new password</label>
                            <input
                                type="password"
                                name="confirmPassword"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none text-sm"
                                required>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                                Update password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Subscription & Billing -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Subscription & Billing</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-base font-semibold text-gray-900">Free Plan</h3>
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-medium">Active</span>
                            </div>
                            <p class="text-sm text-gray-600">You're currently on the free plan</p>
                        </div>
                        <a href="#pricing">
                            <button class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                                Upgrade Plan
                            </button>
                        </a>
                    </div>

                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-crown text-orange-600 mt-0.5"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-orange-900 mb-1">Unlock Premium Features</h4>
                                <p class="text-sm text-orange-800 mb-3">Get access to 50+ premium templates, AI writing assistant, and unlimited downloads.</p>
                                <a href="#pricing" class="inline-flex items-center gap-2 text-sm font-medium text-orange-700 hover:text-orange-800">
                                    View pricing plans
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Preferences</h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Language</h3>
                            <p class="text-xs text-gray-600">Choose your preferred language</p>
                        </div>
                        <select class="px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none text-sm">
                            <option>English</option>
                            <option>Spanish</option>
                            <option>French</option>
                            <option>German</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Email notifications</h3>
                            <p class="text-xs text-gray-600">Receive updates about your account</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Marketing emails</h3>
                            <p class="text-xs text-gray-600">Receive tips and product updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-3">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Auto-save</h3>
                            <p class="text-xs text-gray-600">Automatically save your work</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Data & Privacy -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Data & Privacy</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-3 py-3">
                        <i class="fas fa-download text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Download your data</h3>
                            <p class="text-xs text-gray-600 mb-3">Get a copy of all your personal data</p>
                            <button class="text-sm font-medium text-orange-600 hover:text-orange-700">
                                Request download
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex items-start gap-3 py-3">
                            <i class="fas fa-trash-alt text-red-400 mt-1"></i>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Delete account</h3>
                                <p class="text-xs text-gray-600 mb-3">Permanently delete your account and all data</p>
                                <button class="text-sm font-medium text-red-600 hover:text-red-700">
                                    Delete account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sign Out -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Sign out of your account</h3>
                            <p class="text-xs text-gray-600">You'll need to sign in again to access your account</p>
                        </div>
                        <a href="logout.php">
                            <button class="border border-gray-300 hover:border-gray-400 text-gray-700 hover:bg-gray-50 px-5 py-2 rounded-lg text-sm font-medium transition">
                                Sign out
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Spacing -->
        <div class="py-8"></div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = event.currentTarget.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const newPassword = this.querySelector('input[name="newPassword"]');
                const confirmPassword = this.querySelector('input[name="confirmPassword"]');

                if (newPassword && confirmPassword) {
                    if (newPassword.value !== confirmPassword.value) {
                        e.preventDefault();
                        alert('Passwords do not match!');
                    }
                    if (newPassword.value.length < 8) {
                        e.preventDefault();
                        alert('Password must be at least 8 characters long!');
                    }
                }
            });
        });
    </script>
</body>

</html>