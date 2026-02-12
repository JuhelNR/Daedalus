<?php
require_once('includes/middleware.php');
require_once('includes/resume.model.php');

// Get user data from session (middleware ensures user is logged in)
$firstName = $_SESSION['fname'] ?? 'User';
$lastName = $_SESSION['lname'] ?? '';
$email = $_SESSION['email'] ?? '';
$userId = $_SESSION['uid'] ?? '';

// Get all resumes for this user from database
$resumes = get_user_resumes($userId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Resumes - Daedalus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-['Inter']">
    <!-- Sidebar -->
    <div class="fixed left-0 top-0 bottom-0 w-60 bg-white border-r border-gray-200 overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <a href="index.html" class="flex items-center gap-3 no-underline">
                <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-amber-600 rounded flex items-center justify-center">
                    <i class="fas fa-feather text-white text-sm"></i>
                </div>
                <span class="text-lg font-bold text-gray-900">Daedalus</span>
            </a>
        </div>
        <nav class="py-4">
            <a href="dashboard.php" class="flex items-center px-4 py-2.5 mx-2 rounded text-gray-500 font-medium text-sm hover:bg-gray-100 hover:text-gray-700 transition-all"><i class="fas fa-th-large w-5 mr-3 text-base"></i><span>Dashboard</span></a>
            <a href="my-resumes.php" class="flex items-center px-4 py-2.5 mx-2 rounded bg-amber-100 text-amber-600 font-medium text-sm"><i class="fas fa-file-alt w-5 mr-3 text-base"></i><span>My Resumes</span></a>
            <a href="templates.php" class="flex items-center px-4 py-2.5 mx-2 rounded text-gray-500 font-medium text-sm hover:bg-gray-100 hover:text-gray-700 transition-all"><i class="fa-brands fa-themeco w-5 mr-3 text-base"></i><span>Templates</span></a>
            <a href="#" class="flex items-center px-4 py-2.5 mx-2 rounded text-gray-500 font-medium text-sm hover:bg-gray-100 hover:text-gray-700 transition-all"><i class="fas fa-share-nodes w-5 mr-3 text-base"></i><span>Resignation Letters</span></a>
        </nav>
        <div class="border-t border-gray-200 my-1"></div>
        <nav class="py-2">
            <a href="account-settings.php" class="flex items-center px-4 py-2.5 mx-2 rounded text-gray-500 font-medium text-sm hover:bg-gray-100 hover:text-gray-700 transition-all"><i class="fas fa-cog w-5 mr-3 text-base"></i><span>Settings</span></a>
            <a href="help.php" class="flex items-center px-4 py-2.5 mx-2 rounded text-gray-500 font-medium text-sm hover:bg-gray-100 hover:text-gray-700 transition-all"><i class="fas fa-question-circle w-5 mr-3 text-base"></i><span>Help</span></a>
        </nav>
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                        <?php echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1)); ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-gray-900 whitespace-nowrap overflow-hidden text-ellipsis"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></div>
                        <div class="text-xs text-gray-500 whitespace-nowrap overflow-hidden text-ellipsis"><?php echo htmlspecialchars($email); ?></div>
                    </div>
                </div>
                <a href="includes/logout.php" class="text-gray-400 ml-2 no-underline"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-60 bg-gray-50 min-h-screen">
        <div class="bg-white border-b border-gray-200 px-8 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900 m-0">My Resumes</h1>
                <a href="templates.php" class="no-underline">
                    <button class="bg-amber-500 text-white px-5 py-2.5 rounded font-semibold text-sm hover:bg-amber-600 transition-all"><i class="fas fa-plus mr-2"></i>Create new</button>
                </a>
            </div>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:border-amber-500 hover:bg-amber-50 transition-all" style="aspect-ratio: 8.5/14;">
                    <a href="templates.php" class="w-full h-full flex flex-col items-center justify-center no-underline text-inherit p-6">
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-plus text-amber-500 text-xl"></i>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm">Create new resume</div>
                    </a>
                </div>
                <?php
                if (empty($resumes)): ?>
                    <div class="col-span-full bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                        <i class="fas fa-file-alt text-blue-400 text-3xl mb-3 block"></i>
                        <p class="text-gray-700 font-medium">No resumes yet</p>
                        <p class="text-gray-500 text-sm mt-1">Click "Create new" to get started with a template</p>
                    </div>
                <?php else:
                  foreach ($resumes as $resume):
                    // Get template color if available
                    $primary_color = '#f59e0b'; // default
                    if ($resume && isset($resume['template_id'])) {
                        $template = get_template($resume['template_id']);
                        if ($template) {
                            $color_scheme = is_string($template['color_scheme']) ? json_decode($template['color_scheme'], true) : $template['color_scheme'];
                            $primary_color = $color_scheme['primary'] ?? '#f59e0b';
                        }
                    }

                    // Format date
                    $updated_date = isset($resume['updated_at']) ? date('M d, Y', strtotime($resume['updated_at'])) : 'Just now';
                ?>
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden cursor-pointer hover:shadow-lg hover:border-gray-300 transition-all h-full">
                        <a href="builder.php?resume_id=<?php echo $resume['id']; ?>" class="no-underline text-inherit block">
                            <div class="bg-gray-50 p-6 overflow-hidden" style="aspect-ratio: 8.5/11;">
                                <div class="bg-white border border-gray-200 rounded p-5 h-full shadow-sm">
                                    <div class="h-2 rounded w-2/3 mb-3" style="background: <?php echo htmlspecialchars($primary_color); ?>;"></div>
                                    <div class="h-1 bg-gray-200 rounded w-full mb-1"></div>
                                    <div class="h-1 bg-gray-200 rounded w-5/6 mb-2"></div>
                                    <div class="mt-4">
                                        <div class="h-0.5 bg-gray-100 rounded w-full mb-0.5"></div>
                                        <div class="h-0.5 bg-gray-100 rounded w-11/12 mb-0.5"></div>
                                        <div class="h-0.5 bg-gray-100 rounded w-10/12"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-sm font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($resume['title']); ?></h3>
                                <p class="text-xs text-gray-500">Edited <?php echo htmlspecialchars($updated_date); ?></p>
                            </div>
                        </a>
                        <div class="px-4 pb-4 flex gap-2">
                            <a href="builder.php?resume_id=<?php echo $resume['id']; ?>" class="flex-1 bg-white text-gray-700 px-3 py-1.5 rounded font-medium text-xs border border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-all no-underline text-center"><i class="fas fa-edit mr-1"></i>Edit</a>
                            <button class="bg-white text-gray-700 px-3 py-1.5 rounded font-medium text-xs border border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-all"><i class="fas fa-ellipsis-h"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>