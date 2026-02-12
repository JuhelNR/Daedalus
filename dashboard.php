<?php
session_start();
require_once('includes/middleware.php');

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Get user data from session (middleware ensures user is logged in)
$firstName = $_SESSION['fname'] ?? 'John';
$lastName = $_SESSION['lname'] ?? 'Doe';
$email = $_SESSION['email'] ?? 'DoeJohndoe@gmail.com';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daedalus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: none;
        }

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
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 240px;
            background: #f9fafb;
            min-height: 100vh;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            margin: 2px 8px;
            border-radius: 6px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
        }

        .nav-item:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .nav-item.active {
            background: #fef3c7;
            color: #d97706;
        }

        .nav-item i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }

        .document-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
            height: 100%;
        }

        .document-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #d1d5db;
        }

        .document-preview {
            background: #f9fafb;
            padding: 24px;
            aspect-ratio: 8.5/11;
            position: relative;
            overflow: hidden;
        }

        .document-preview-inner {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 20px;
            height: 100%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .btn-primary {
            background: #f59e0b;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-primary:hover {
            background: #d97706;
        }

        .btn-secondary {
            background: white;
            color: #374151;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 13px;
            border: 1px solid #d1d5db;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .tab-button {
            padding: 12px 0;
            margin-right: 32px;
            border-bottom: 2px solid transparent;
            color: #6b7280;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.15s;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
        }

        .tab-button:hover {
            color: #1f2937;
        }

        .tab-button.active {
            color: #f59e0b;
            border-bottom-color: #f59e0b;
        }

        .create-card {
            background: white;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            aspect-ratio: 8.5/14;
        }

        .create-card:hover {
            border-color: #f59e0b;
            background: #fffbeb;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo -->
        <div style="padding: 20px 16px; border-bottom: 1px solid #e5e7eb;">
            <a href="index.html" style="display: flex; align-items: center; text-decoration: none;">
                <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class="fas fa-feather" style="color: white; font-size: 14px;"></i>
                </div>
                <span style="font-size: 18px; font-weight: 700; color: #1f2937;">Daedalus</span>
            </a>
        </div>

        <!-- Navigation -->
        <nav style="padding: 16px 0;">
            <a href="dashboard.php" class="nav-item active">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="my-resume.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>My Resumes</span>
            </a>
            <a href="templates.php" class="nav-item">
                <i class="fa-brands fa-themeco"></i>
                <span>Templates</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-share-nodes"></i>
                <span>Resignation Letters</span>
            </a>
        </nav>

        <!-- Divider -->
        <div style="margin: 8px 0; border-top: 1px solid #e5e7eb;"></div>

        <!-- Bottom Nav -->
        <nav style="padding: 8px 0;">
            <a href="account-settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-question-circle"></i>
                <span>Help</span>
            </a>
        </nav>

        <!-- User Profile at Bottom -->
        <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 16px; border-top: 1px solid #e5e7eb; background: white;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                    <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 13px; flex-shrink: 0;">
                        <?php echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1)); ?>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 13px; font-weight: 600; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?>
                        </div>
                        <div style="font-size: 11px; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($email); ?>
                        </div>
                    </div>
                </div>
                <a href="includes/logout.php" style="color: #9ca3af; margin-left: 8px; text-decoration: none;">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div style="background: white; border-bottom: 1px solid #e5e7eb; padding: 16px 32px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <h1 style="font-size: 24px; font-weight: 700; color: #1f2937; margin: 0;">Documents</h1>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button class="btn-secondary">
                        <i class="fas fa-search" style="margin-right: 6px;"></i>
                        Search
                    </button>
                    <a href="builder.php" style="text-decoration: none;">
                        <button class="btn-primary">
                            <i class="fas fa-plus" style="margin-right: 8px;"></i>
                            Create new
                        </button>
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div style="padding: 32px;">
            <!-- Filter Buttons -->
            <div style="margin-bottom: 24px; display: flex; gap: 8px;">
                <button class="btn-secondary filter-btn active-filter" style="background: #f9fafb; border-color: #f59e0b; color: #f59e0b;">
                    All Documents
                </button>
                <button class="btn-secondary filter-btn">
                    My Resumes
                </button>
                <button class="btn-secondary filter-btn">
                    Shared With Me
                </button>
            </div>

            <!-- Documents Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 24px;">
                <!-- Create New Card -->
                <div class="create-card">
                    <a href="builder.php" style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: inherit; padding: 24px;">
                        <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                            <i class="fas fa-plus" style="color: #f59e0b; font-size: 20px;"></i>
                        </div>
                        <div style="font-weight: 600; color: #1f2937; font-size: 14px;">Create new resume</div>
                    </a>
                </div>

                <!-- Resume Card 1 -->
                <div class="document-card">
                    <a href="builder.php" style="text-decoration: none; color: inherit; display: block;">
                        <div class="document-preview">
                            <div class="document-preview-inner">
                                <div style="height: 8px; background: #f59e0b; border-radius: 2px; width: 70%; margin-bottom: 12px;"></div>
                                <div style="height: 4px; background: #e5e7eb; border-radius: 2px; width: 100%; margin-bottom: 4px;"></div>
                                <div style="height: 4px; background: #e5e7eb; border-radius: 2px; width: 85%; margin-bottom: 8px;"></div>
                                <div style="margin-top: 16px;">
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 100%; margin-bottom: 3px;"></div>
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 95%; margin-bottom: 3px;"></div>
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 90%; margin-bottom: 3px;"></div>
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 75%;"></div>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 16px;">
                            <h3 style="font-size: 14px; font-weight: 600; color: #1f2937; margin: 0 0 4px 0;">Software Engineer Resume</h3>
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">Edited 2 days ago</p>
                        </div>
                    </a>
                    <div style="padding: 0 16px 16px 16px; display: flex; gap: 8px;">
                        <button class="btn-secondary" style="flex: 1; font-size: 12px; padding: 6px 12px;">
                            <i class="fas fa-download" style="margin-right: 4px;"></i>
                            Download
                        </button>
                        <button class="btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>
                </div>

                <!-- Resume Card 2 -->
                <div class="document-card">
                    <a href="builder.php" style="text-decoration: none; color: inherit; display: block;">
                        <div class="document-preview">
                            <div class="document-preview-inner">
                                <div style="height: 8px; background: #3b82f6; border-radius: 2px; width: 65%; margin-bottom: 12px;"></div>
                                <div style="height: 4px; background: #e5e7eb; border-radius: 2px; width: 100%; margin-bottom: 4px;"></div>
                                <div style="height: 4px; background: #e5e7eb; border-radius: 2px; width: 80%; margin-bottom: 8px;"></div>
                                <div style="margin-top: 16px;">
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 100%; margin-bottom: 3px;"></div>
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 92%; margin-bottom: 3px;"></div>
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 88%;"></div>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 16px;">
                            <h3 style="font-size: 14px; font-weight: 600; color: #1f2937; margin: 0 0 4px 0;">Marketing Manager CV</h3>
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">Edited 1 week ago</p>
                        </div>
                    </a>
                    <div style="padding: 0 16px 16px 16px; display: flex; gap: 8px;">
                        <button class="btn-secondary" style="flex: 1; font-size: 12px; padding: 6px 12px;">
                            <i class="fas fa-download" style="margin-right: 4px;"></i>
                            Download
                        </button>
                        <button class="btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>
                </div>

                <!-- Resume Card 3 -->
                <div class="document-card">
                    <a href="builder.php" style="text-decoration: none; color: inherit; display: block;">
                        <div class="document-preview">
                            <div class="document-preview-inner">
                                <div style="height: 8px; background: #10b981; border-radius: 2px; width: 60%; margin-bottom: 12px;"></div>
                                <div style="height: 4px; background: #e5e7eb; border-radius: 2px; width: 100%; margin-bottom: 4px;"></div>
                                <div style="height: 4px; background: #e5e7eb; border-radius: 2px; width: 75%; margin-bottom: 8px;"></div>
                                <div style="margin-top: 16px;">
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 100%; margin-bottom: 3px;"></div>
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 90%; margin-bottom: 3px;"></div>
                                    <div style="height: 3px; background: #f3f4f6; border-radius: 2px; width: 85%;"></div>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 16px;">
                            <h3 style="font-size: 14px; font-weight: 600; color: #1f2937; margin: 0 0 4px 0;">Product Designer Resume</h3>
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">Edited 2 weeks ago</p>
                        </div>
                    </a>
                    <div style="padding: 0 16px 16px 16px; display: flex; gap: 8px;">
                        <button class="btn-secondary" style="flex: 1; font-size: 12px; padding: 6px 12px;">
                            <i class="fas fa-download" style="margin-right: 4px;"></i>
                            Download
                        </button>
                        <button class="btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.style.background = 'white';
                    b.style.borderColor = '#d1d5db';
                    b.style.color = '#374151';
                    b.classList.remove('active-filter');
                });
                this.style.background = '#f9fafb';
                this.style.borderColor = '#f59e0b';
                this.style.color = '#f59e0b';
                this.classList.add('active-filter');
            });
        });
    </script>
</body>

</html>