<?php
require_once('includes/middleware.php');
require_once('includes/resume.model.php');

// Get user data from session (middleware ensures user is logged in)
$firstName = $_SESSION['fname'] ?? 'User';
$lastName = $_SESSION['lname'] ?? '';
$email = $_SESSION['email'] ?? '';

// Get all templates from database
$templates = get_all_templates();

// Extract unique categories
$categories = array_values(array_unique(array_column($templates, 'category')));
sort($categories);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Templates - Daedalus</title>
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
            <a href="my-resumes.php" class="flex items-center px-4 py-2.5 mx-2 rounded text-gray-500 font-medium text-sm hover:bg-gray-100 hover:text-gray-700 transition-all"><i class="fas fa-file-alt w-5 mr-3 text-base"></i><span>My Resumes</span></a>
            <a href="templates.php" class="flex items-center px-4 py-2.5 mx-2 rounded bg-amber-100 text-amber-600 font-medium text-sm"><i class="fa-brands fa-themeco w-5 mr-3 text-base"></i><span>Templates</span></a>
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
            <h1 class="text-2xl font-bold text-gray-900 m-0">Resume Templates</h1>
            <p class="text-sm text-gray-500 mt-1">Choose from our professionally designed templates</p>
        </div>
        <div class="p-8">
            <div class="mb-6 flex gap-2 flex-wrap">
                <button class="filter-btn bg-gray-50 text-amber-500 border border-amber-500 active px-4 py-2 rounded font-medium text-xs cursor-pointer transition-all" data-category="all">All Templates</button>
                <?php foreach ($categories as $category): ?>
                    <button class="filter-btn bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded font-medium text-xs cursor-pointer hover:bg-gray-50 transition-all" data-category="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></button>
                <?php endforeach; ?>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($templates as $template):
                    // Parse color_scheme if it's JSON
                    $color_scheme = is_string($template['color_scheme']) ? json_decode($template['color_scheme'], true) : $template['color_scheme'];
                    $primary_color = $color_scheme['primary'] ?? '#f59e0b';
                ?>
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden cursor-pointer hover:shadow-lg hover:border-gray-300 transition-all template-card" data-category="<?php echo htmlspecialchars($template['category']); ?>">
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
                            <h3 class="text-sm font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($template['name']); ?></h3>
                            <p class="text-xs text-gray-500 mb-3"><?php echo htmlspecialchars($template['category']); ?></p>
                            <a href="builder.php?template_id=<?php echo $template['id']; ?>" class="no-underline">
                                <button class="w-full bg-amber-500 text-white px-4 py-2 rounded font-semibold text-xs cursor-pointer hover:bg-amber-600 transition-all">Use Template</button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.getAttribute('data-category');

                // Update active button
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active', 'bg-gray-50', 'text-amber-500', 'border-amber-500');
                    b.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                });

                this.classList.add('active', 'bg-gray-50', 'text-amber-500', 'border-amber-500');
                this.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');

                // Filter templates
                document.querySelectorAll('.template-card').forEach(card => {
                    if (category === 'all' || card.getAttribute('data-category') === category) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>