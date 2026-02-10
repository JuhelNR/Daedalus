<?php
session_start();

if (!isset($_SESSION['uid'])) {
    header('location: login.php');
    exit();
}

$firstName = $_SESSION['fname'] ?? 'John';
$lastName  = $_SESSION['lname'] ?? 'Doe';
$email     = $_SESSION['email'] ?? 'user@example.com';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account Settings – Daedalus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            color: #1f2937;
        }

        .sidebar {
            width: 240px;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
        }

        .main-content {
            margin-left: 240px;
            min-height: 100vh;
            background: #f9fafb;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            margin: 2px 8px;
            border-radius: 6px;
            color: #6b7280;
            font-weight: 500;
            text-decoration: none;
        }

        .nav-item:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .nav-item.active {
            background: #fef3c7;
            color: #d97706;
        }

        .btn-primary {
            background: #f59e0b;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            border: none;
        }

        .btn-primary:hover {
            background: #d97706;
        }

        .section-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-4 border-b">
            <a href="dashboard.php" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-600 rounded flex items-center justify-center">
                    <i class="fas fa-feather text-white text-sm"></i>
                </div>
                <span class="font-bold text-lg">Daedalus</span>
            </a>
        </div>

        <nav class="py-4">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-th-large w-5"></i> Dashboard
            </a>
            <a href="builder.php" class="nav-item">
                <i class="fas fa-file-alt w-5"></i> Resumes
            </a>
            <a href="account-settings.php" class="nav-item active">
                <i class="fas fa-cog w-5"></i> Settings
            </a>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold"><?php echo htmlspecialchars("$firstName $lastName"); ?></div>
                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($email); ?></div>
                </div>
                <a href="logout.php" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main -->
    <div class="main-content">

        <!-- Header -->
        <div class="bg-white border-b px-8 py-5">
            <h1 class="text-2xl font-bold">Account Settings</h1>
            <p class="text-sm text-gray-500">Manage your profile and preferences</p>
        </div>

        <!-- Content -->
        <div class="p-8 space-y-6 max-w-4xl">

            <!-- Profile -->
            <div class="section-card">
                <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Personal Information</div>
                <form class="p-6 grid md:grid-cols-2 gap-4">
                    <input class="border rounded px-3 py-2" value="<?php echo htmlspecialchars($firstName); ?>" placeholder="First name">
                    <input class="border rounded px-3 py-2" value="<?php echo htmlspecialchars($lastName); ?>" placeholder="Last name">
                    <input class="border rounded px-3 py-2 md:col-span-2 bg-gray-100" value="<?php echo htmlspecialchars($email); ?>" disabled>
                    <div class="md:col-span-2 flex justify-end">
                        <button class="btn-primary">Save changes</button>
                    </div>
                </form>
            </div>

            <!-- Password -->
            <div class="section-card">
                <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Change Password</div>
                <form class="p-6 space-y-4">
                    <input type="password" class="border rounded px-3 py-2 w-full" placeholder="Current password">
                    <input type="password" class="border rounded px-3 py-2 w-full" placeholder="New password">
                    <input type="password" class="border rounded px-3 py-2 w-full" placeholder="Confirm new password">
                    <div class="flex justify-end">
                        <button class="btn-primary">Update password</button>
                    </div>
                </form>
            </div>

            <!-- Preferences -->
            <div class="section-card">
                <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Preferences</div>
                <div class="p-6 space-y-4">
                    <label class="flex items-center justify-between">
                        <span>Email notifications</span>
                        <input type="checkbox" checked>
                    </label>
                    <label class="flex items-center justify-between">
                        <span>Auto-save documents</span>
                        <input type="checkbox" checked>
                    </label>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="section-card border-red-200">
                <div class="px-6 py-4 border-b bg-red-50 text-red-600 font-semibold">Danger Zone</div>
                <div class="p-6 flex justify-between items-center">
                    <div>
                        <div class="font-medium">Delete account</div>
                        <div class="text-sm text-gray-500">This action is irreversible</div>
                    </div>
                    <button class="text-red-600 font-medium hover:underline">Delete</button>
                </div>
            </div>

        </div>
    </div>

</body>

</html>