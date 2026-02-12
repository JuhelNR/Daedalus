<?php
require_once('includes/middleware.php');

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
    <title>Help - Daedalus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .faq-answer {
            display: none;
        }

        .faq-answer.active {
            display: block;
        }
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
            <a href="templates.php" class="flex items-center px-4 py-2.5 mx-2 rounded text-gray-500 font-medium text-sm hover:bg-gray-100 hover:text-gray-700 transition-all"><i class="fa-brands fa-themeco w-5 mr-3 text-base"></i><span>Templates</span></a>
            <a href="#" class="flex items-center px-4 py-2.5 mx-2 rounded text-gray-500 font-medium text-sm hover:bg-gray-100 hover:text-gray-700 transition-all"><i class="fas fa-share-nodes w-5 mr-3 text-base"></i><span>Resignation Letters</span></a>
        </nav>
        <div class="border-t border-gray-200 my-1"></div>
        <nav class="py-2">
            <a href="account-settings.php" class="flex items-center px-4 py-2.5 mx-2 rounded text-gray-500 font-medium text-sm hover:bg-gray-100 hover:text-gray-700 transition-all"><i class="fas fa-cog w-5 mr-3 text-base"></i><span>Settings</span></a>
            <a href="help.php" class="flex items-center px-4 py-2.5 mx-2 rounded bg-amber-100 text-amber-600 font-medium text-sm"><i class="fas fa-question-circle w-5 mr-3 text-base"></i><span>Help</span></a>
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
            <h1 class="text-2xl font-bold text-gray-900 m-0">Help Center</h1>
            <p class="text-sm text-gray-500 mt-1">Find answers and get support</p>
        </div>
        
        <div class="px-8 py-8 max-w-3xl mx-auto">
            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:shadow-lg transition-all">
                    <i class="fas fa-book text-orange-500 text-2xl mb-3"></i>
                    <h3 class="font-semibold mb-2 text-sm">Getting Started Guide</h3>
                    <p class="text-gray-500 text-xs">Learn the basics of creating your first resume</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:shadow-lg transition-all">
                    <i class="fas fa-video text-blue-500 text-2xl mb-3"></i>
                    <h3 class="font-semibold mb-2 text-sm">Video Tutorials</h3>
                    <p class="text-gray-500 text-xs">Watch step-by-step video guides</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:shadow-lg transition-all">
                    <i class="fas fa-envelope text-green-500 text-2xl mb-3"></i>
                    <h3 class="font-semibold mb-2 text-sm">Contact Support</h3>
                    <p class="text-gray-500 text-xs">Get help from our support team</p>
                </div>
            </div>

            <!-- FAQs -->
            <h2 class="text-xl font-bold mb-4">Frequently Asked Questions</h2>

            <div class="bg-white border border-gray-200 rounded-lg mb-3 overflow-hidden">
                <div class="faq-question px-5 py-4 font-semibold cursor-pointer flex justify-between items-center hover:bg-gray-50 transition-all" onclick="toggleFaq(this)">
                    <span>How do I create a new resume?</span>
                    <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                </div>
                <div class="faq-answer px-5 pb-4 text-gray-500 leading-relaxed">
                    Click the "Create new" button on your dashboard or in the top navigation. Choose a template or start from scratch, then fill in your information step by step.
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg mb-3 overflow-hidden">
                <div class="faq-question px-5 py-4 font-semibold cursor-pointer flex justify-between items-center hover:bg-gray-50 transition-all" onclick="toggleFaq(this)">
                    <span>Can I download my resume in different formats?</span>
                    <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                </div>
                <div class="faq-answer px-5 pb-4 text-gray-500 leading-relaxed">
                    Yes! You can download your resume as PDF, Word document, or plain text. Simply click the download button on your resume card and select your preferred format.
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg mb-3 overflow-hidden">
                <div class="faq-question px-5 py-4 font-semibold cursor-pointer flex justify-between items-center hover:bg-gray-50 transition-all" onclick="toggleFaq(this)">
                    <span>How do I use the AI writing assistant?</span>
                    <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                </div>
                <div class="faq-answer px-5 pb-4 text-gray-500 leading-relaxed">
                    When editing your resume, look for the "Generate with AI" button in the professional summary section. The AI will create personalized content based on your job title and experience.
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg mb-3 overflow-hidden">
                <div class="faq-question px-5 py-4 font-semibold cursor-pointer flex justify-between items-center hover:bg-gray-50 transition-all" onclick="toggleFaq(this)">
                    <span>Can I share my resume with others?</span>
                    <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                </div>
                <div class="faq-answer px-5 pb-4 text-gray-500 leading-relaxed">
                    Yes! Click the share button on any resume to generate a shareable link. You can control whether others can view or edit your resume.
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg mb-3 overflow-hidden">
                <div class="faq-question px-5 py-4 font-semibold cursor-pointer flex justify-between items-center hover:bg-gray-50 transition-all" onclick="toggleFaq(this)">
                    <span>What's included in the free plan?</span>
                    <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                </div>
                <div class="faq-answer px-5 pb-4 text-gray-500 leading-relaxed">
                    The free plan includes 3 resume templates, basic editing tools, and PDF downloads. Upgrade to Pro for unlimited resumes, 50+ premium templates, and AI-powered features.
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg mb-3 overflow-hidden">
                <div class="faq-question px-5 py-4 font-semibold cursor-pointer flex justify-between items-center hover:bg-gray-50 transition-all" onclick="toggleFaq(this)">
                    <span>How do I cancel my subscription?</span>
                    <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                </div>
                <div class="faq-answer px-5 pb-4 text-gray-500 leading-relaxed">
                    Go to Account Settings > Subscription & Billing. Click "Manage Subscription" and follow the prompts to cancel. Your access will continue until the end of your billing period.
                </div>
            </div>

            <!-- Contact Card -->
            <div class="bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg p-8 mt-8 text-center text-white">
                <h3 class="text-xl font-bold mb-2">Still need help?</h3>
                <p class="mb-5 opacity-90">Our support team is here to assist you</p>
                <button class="bg-white text-amber-500 px-6 py-2 rounded font-semibold cursor-pointer">Contact Support</button>
            </div>
        </div>
    </div>

    <script>
        function toggleFaq(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('i');
            const allAnswers = document.querySelectorAll('.faq-answer');
            const allIcons = document.querySelectorAll('.faq-question i');

            allAnswers.forEach(a => {
                if (a !== answer) a.classList.remove('active');
            });
            allIcons.forEach(i => {
                if (i !== icon) i.style.transform = 'rotate(0deg)';
            });

            answer.classList.toggle('active');
            icon.style.transform = answer.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
            icon.style.transition = 'transform 0.2s';
        }
    </script>
</body>

</html>