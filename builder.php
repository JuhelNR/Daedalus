<?php
// Include middleware to require login
require_once('includes/middleware.php');
require_once('includes/resume.model.php');

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Get user data from session (middleware ensures user is logged in)
$firstName = $_SESSION['fname'] ?? '';
$lastName = $_SESSION['lname'] ?? '';
$email = $_SESSION['email'] ?? '';
$userId = $_SESSION['uid'] ?? '';

// Get template_id and resume_id from URL parameters
$template_id = $_GET['template_id'] ?? null;
$resume_id = $_GET['resume_id'] ?? null;
$template_name = '';
$font_family = 'Inter, sans-serif';
$primary_color = '#f59e0b';

// If editing existing resume, load it
$resume_data = [];
if ($resume_id) {
    $resume = get_resume($resume_id);
    if ($resume && $resume['user_id'] == $userId) {
        $resume_data = $resume;
        $template_id = $resume['template_id'];
        $template_name = $resume['template_name'] ?? '';
        $font_family = $resume['font_family'] ?? 'Inter, sans-serif';

        // Parse color scheme
        $color_scheme = is_string($resume['color_scheme']) ? json_decode($resume['color_scheme'], true) : $resume['color_scheme'];
        $primary_color = $color_scheme['primary'] ?? '#f59e0b';
    }
} elseif ($template_id) {
    // If creating new resume with template, get template details
    $template = get_template($template_id);
    if ($template) {
        $template_name = $template['name'];
        $font_family = $template['font_family'] ?? 'Inter, sans-serif';

        // Parse color scheme
        $color_scheme = is_string($template['color_scheme']) ? json_decode($template['color_scheme'], true) : $template['color_scheme'];
        $primary_color = $color_scheme['primary'] ?? '#f59e0b';
    }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Resume Builder - Daedalus</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <script src="https://unpkg.com/flowbite@1.7.0/dist/flowbite.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap"
    rel="stylesheet" />
  <style>
    * {
      scrollbar-width: none;
    }

    body {
      font-family:
        "Inter",
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;
    }

    .heading-font {
      font-family: "Playfair Display", Georgia, serif;
    }

    .orange-gradient {
      background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
    }

    @media print {
      body * {
        visibility: hidden;
      }

      #resumePreview,
      #resumePreview * {
        visibility: visible;
      }

      #resumePreview {
        position: absolute;
        left: 0;
        top: 0;
      }
    }

    .a4-page {
      width: 150;
      height: 297mm;
      margin: 0 auto;
      padding: 20mm;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      background-color: white;
      box-sizing: border-box;
      overflow: hidden;
    }
  </style>
</head>

<body class="bg-gray-50 antialiased">
  <!-- Navigation -->
  <nav class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <a href="index.html" class="flex items-center gap-2">
          <div
            class="w-8 h-8 orange-gradient rounded-lg flex items-center justify-center">
            <i class="fas fa-feather text-white text-sm"></i>
          </div>
          <span class="text-lg font-bold heading-font text-gray-900">Daedalus</span>
        </a>
        <div class="flex items-center gap-3">
          <button
            id="saveBtn"
            onclick="saveResumeToDatabase()"
            class="hidden sm:flex items-center gap-2 text-gray-600 hover:text-gray-900 px-4 py-2 rounded-xl hover:bg-gray-50 transition font-medium text-sm">
            <i class="fas fa-save"></i>
            <span>Save Resume</span>
          </button>
          <button
            id="downloadBtn"
            onclick="downloadPDF()"
            class="flex items-center gap-2 orange-gradient text-white px-5 py-2.5 rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition font-medium text-sm">
            <i class="fas fa-download"></i>
            <span>Download PDF</span>
          </button>

          <div
            id="avatarButton"
            data-dropdown-toggle="userDropdown"
            data-dropdown-placement="bottom-end"
            class="w-8 h-8 orange-gradient rounded-full flex items-center justify-center text-white text-sm font-semibold cursor-pointer">
            <?php echo strtoupper(substr($firstName, 0, 1)); ?>
          </div>
          <!-- Dropdown menu -->
          <div id="userDropdown" class="z-10 hidden bg-white border border-default-medium rounded-base shadow-lg w-44">
            <div class="px-4 py-3 border-b border-default-medium text-sm text-heading">
              <div class="font-medium"><?php echo $firstName . " " . $lastName; ?></div>
              <div class="truncate text-xs text-gray-500"><?php echo $email; ?></div>
            </div>
            <ul class="p-2 text-sm text-body font-medium" aria-labelledby="avatarButton">
              <li>
                <a href="account_settings.php" class="block w-full p-2 hover:bg-gray-100 hover:text-heading rounded-md">Settings</a>
              </li>
              <li>
                <a href="includes/logout.php" class="block w-full p-2 hover:bg-gray-100 text-fg-danger rounded-md">Sign out</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Builder -->
  <div class="flex flex-col lg:flex-row h-[calc(100vh-4rem)]">
    <!-- Left Panel - Form -->
    <div class="w-full lg:w-1/2 overflow-y-auto p-6 bg-gray-50">
      <div class="max-w-2xl mx-auto">
        <!-- Hidden fields for template and resume tracking -->
        <input type="hidden" id="resumeId" value="<?php echo htmlspecialchars($resume_id ?? ''); ?>">
        <input type="hidden" id="templateId" value="<?php echo htmlspecialchars($template_id ?? ''); ?>">
        <?php if ($template_name): ?>
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
          <p class="text-sm text-amber-900"><strong>Template:</strong> <?php echo htmlspecialchars($template_name); ?></p>
        </div>
        <?php endif; ?>

        <!-- Progress Steps -->
        <div class="mb-10">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center">
                <div
                  id="step1-indicator"
                  class="w-10 h-10 orange-gradient text-white rounded-full flex items-center justify-center font-semibold shadow-lg shadow-orange-500/30">
                  1
                </div>
                <div class="flex-1 h-1 bg-orange-500 mx-3"></div>
              </div>
              <p class="text-xs mt-2 text-orange-600 font-medium">Personal</p>
            </div>
            <div class="flex-1">
              <div class="flex items-center">
                <div
                  id="step2-indicator"
                  class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">
                  2
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-3"></div>
              </div>
              <p class="text-xs mt-2 text-gray-500 font-medium">Experience</p>
            </div>
            <div class="flex-1">
              <div class="flex items-center">
                <div
                  id="step3-indicator"
                  class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">
                  3
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-3"></div>
              </div>
              <p class="text-xs mt-2 text-gray-500 font-medium">Education</p>
            </div>
            <div class="flex-1">
              <div class="flex items-center">
                <div
                  id="step4-indicator"
                  class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">
                  4
                </div>
              </div>
              <p class="text-xs mt-2 text-gray-500 font-medium">Skills</p>
            </div>
          </div>
        </div>

        <!-- Step 1: Personal Information -->
        <div
          id="step1"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-2 heading-font">
            Personal Information
          </h2>
          <p class="text-gray-600 mb-8 text-sm">
            Let's start with your basic details
          </p>
          <div class="space-y-5">
            <div class="grid grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                <?php
                if (isset($_SESSION['fname'])) {
                  echo '<input
                  type="text"
                  id="firstName"
                  value="' . $_SESSION['fname'] . '"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />';
                } else {
                  echo '<input
                  type="text"
                  id="firstName"
                  placeholder="John"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />';
                }
                ?>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                <?php
                if (isset($_SESSION['lname'])) {
                  echo '<input
                  type="text"
                  id="lastName"
                  value="' . $_SESSION['lname'] . '"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />';
                } else {
                  echo '<input
                  type="text"
                  id="lastName"
                  placeholder="Doe"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />';
                }
                ?>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Professional Title *</label>
              <input
                type="text"
                id="title"
                placeholder="Senior Software Engineer"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
              <?php
              if (isset($_SESSION['email'])) {
                echo '<input disabled
                  type="email"
                  id="email"
                  value="' . $_SESSION['email'] . '"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />';
              } else {
                echo '<input
                  type="email"
                  id="email"
                  placeholder="john.doe@example.com"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />';
              }
              ?>
            </div>
            <div class="grid grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input
                  type="tel"
                  id="phone"
                  placeholder="+1 (555) 123-4567"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input
                  type="text"
                  id="location"
                  placeholder="New York, NY"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Profile</label>
              <input
                type="url"
                id="linkedin"
                placeholder="linkedin.com/in/johndoe"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Professional Summary</label>
              <div class="relative">
                <textarea
                  id="summary"
                  rows="4"
                  placeholder="A results-driven professional with expertise in..."
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none resize-none transition"></textarea>
                <button
                  type="button"
                  onclick="generateAISummary(event)"
                  class="absolute bottom-3 right-3 flex items-center gap-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:from-purple-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                  <i class="fas fa-magic"></i>
                  Generate with AI
                </button>
              </div>
              <p class="text-xs text-gray-500 mt-1.5">
                2-3 sentences highlighting your key strengths and experience
              </p>
            </div>
          </div>
          <div class="mt-8 flex justify-end">
            <button
              onclick="nextStep(2)"
              class="orange-gradient text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition font-medium flex items-center gap-2">
              Continue to Experience
              <i class="fas fa-arrow-right text-sm"></i>
            </button>
          </div>
        </div>

        <!-- Step 2: Work Experience -->
        <div
          id="step2"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6 hidden">
          <h2 class="text-2xl font-bold text-gray-900 mb-2 heading-font">
            Work Experience
          </h2>
          <p class="text-gray-600 mb-8 text-sm">
            Add your professional experience
          </p>
          <div id="experienceList" class="space-y-6">
            <!-- Experience items will be added here -->
          </div>
          <button
            onclick="addExperience()"
            class="w-full border-2 border-dashed border-orange-300 rounded-xl py-4 text-orange-600 hover:border-orange-400 hover:bg-orange-50 transition font-medium">
            <i class="fas fa-plus mr-2"></i>Add Experience
          </button>
          <div class="mt-8 flex justify-between">
            <button
              onclick="nextStep(1)"
              class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
              <i class="fas fa-arrow-left"></i>
              Previous
            </button>
            <button
              onclick="nextStep(3)"
              class="orange-gradient text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition font-medium flex items-center gap-2">
              Continue to Education
              <i class="fas fa-arrow-right text-sm"></i>
            </button>
          </div>
        </div>

        <!-- Step 3: Education -->
        <div
          id="step3"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6 hidden">
          <h2 class="text-2xl font-bold text-gray-900 mb-2 heading-font">
            Education
          </h2>
          <p class="text-gray-600 mb-8 text-sm">
            Add your educational background
          </p>
          <div id="educationList" class="space-y-6">
            <!-- Education items will be added here -->
          </div>
          <button
            onclick="addEducation()"
            class="w-full border-2 border-dashed border-orange-300 rounded-xl py-4 text-orange-600 hover:border-orange-400 hover:bg-orange-50 transition font-medium">
            <i class="fas fa-plus mr-2"></i>Add Education
          </button>
          <div class="mt-8 flex justify-between">
            <button
              onclick="nextStep(2)"
              class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
              <i class="fas fa-arrow-left"></i>
              Previous
            </button>
            <button
              onclick="nextStep(4)"
              class="orange-gradient text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition font-medium flex items-center gap-2">
              Continue to Skills
              <i class="fas fa-arrow-right text-sm"></i>
            </button>
          </div>
        </div>

        <!-- Step 4: Skills -->
        <div
          id="step4"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6 hidden">
          <h2 class="text-2xl font-bold text-gray-900 mb-2 heading-font">
            Skills
          </h2>
          <p class="text-gray-600 mb-8 text-sm">
            Add your key skills and competencies
          </p>
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Add Skills</label>
            <div class="flex gap-2">
              <input
                type="text"
                id="skillInput"
                placeholder="e.g., JavaScript, Project Management, Adobe Photoshop"
                class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" />
              <button
                onclick="addSkill()"
                class="orange-gradient text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition font-medium">
                Add
              </button>
            </div>
          </div>
          <div id="skillsList" class="flex flex-wrap gap-2 mb-6">
            <!-- Skills will be added here -->
          </div>
          <div class="mt-8 flex justify-between">
            <button
              onclick="nextStep(3)"
              class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
              <i class="fas fa-arrow-left"></i>
              Previous
            </button>
            <button
              onclick="
                  alert('Resume completed! Use the Download PDF button above.')
                "
              class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-medium flex items-center gap-2">
              <i class="fas fa-check mr-2"></i>
              Complete Resume
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Panel - Live Preview -->
    <div
      class="hidden lg:block lg:w-1/2 bg-white border-l border-gray-100 p-6 overflow-y-auto">
      <div class="max-w-2xl mx-auto">
        <div class="mb-4 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-700 heading-font">
            Live Preview
          </h3>
        </div>

        <!-- Template-specific preview layouts -->
        <div id="resumePreview" class="a4-page bg-white shadow-xl min-h-screen border border-gray-100 rounded-lg overflow-hidden">
          <?php
          // Render different layouts based on template
          $template_layout = $template_id ?? 1;
          ?>

          <div style="display: <?php echo ($template_layout == 1) ? 'block' : 'none'; ?>" class="template-preview" data-template="1">
            <!-- Modern Professional: Clean single column, amber/orange accents -->
            <div style="font-family: Inter, sans-serif; padding: 40px; background: white;">
              <div style="border-bottom: 3px solid #f59e0b; padding-bottom: 24px; margin-bottom: 24px;">
                <h1 id="previewName1" style="font-size: 32px; font-weight: bold; margin: 0 0 8px 0; color: #111827;">Your Name</h1>
                <p id="previewTitle1" style="font-size: 18px; color: #f59e0b; margin: 0 0 12px 0; font-weight: 500;">Professional Title</p>
                <div style="display: flex; gap: 16px; font-size: 13px; color: #4b5563;">
                  <span id="previewEmail1"><i class="fas fa-envelope" style="color: #f59e0b; margin-right: 6px;"></i>email@example.com</span>
                  <span id="previewPhone1"><i class="fas fa-phone" style="color: #f59e0b; margin-right: 6px;"></i>+1 (555) 123-4567</span>
                  <span id="previewLocation1"><i class="fas fa-map-marker-alt" style="color: #f59e0b; margin-right: 6px;"></i>Location</span>
                </div>
              </div>
              <div id="summarySection1" style="margin-bottom: 24px; display: none;">
                <h2 style="font-size: 14px; font-weight: bold; color: #111827; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 1px;">Professional Summary</h2>
                <p id="previewSummary1" style="color: #374151; line-height: 1.6; margin: 0;"></p>
              </div>
              <div id="experienceSection1" style="margin-bottom: 24px; display: none;">
                <h2 style="font-size: 14px; font-weight: bold; color: #111827; text-transform: uppercase; margin: 0 0 16px 0; letter-spacing: 1px;">Work Experience</h2>
                <div id="previewExperience1" style="display: flex; flex-direction: column; gap: 16px;"></div>
              </div>
              <div id="educationSection1" style="margin-bottom: 24px; display: none;">
                <h2 style="font-size: 14px; font-weight: bold; color: #111827; text-transform: uppercase; margin: 0 0 16px 0; letter-spacing: 1px;">Education</h2>
                <div id="previewEducation1" style="display: flex; flex-direction: column; gap: 16px;"></div>
              </div>
              <div id="skillsSection1" style="display: none;">
                <h2 style="font-size: 14px; font-weight: bold; color: #111827; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 1px;">Skills</h2>
                <div id="previewSkills1" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
              </div>
            </div>
          </div>

          <div style="display: <?php echo ($template_layout == 2) ? 'block' : 'none'; ?>" class="template-preview" data-template="2">
            <!-- Executive Classic: Two-column, Georgia serif, blue theme -->
            <div style="font-family: Georgia, serif; padding: 40px; background: white; display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
              <div style="border-right: 2px solid #1e40af; padding-right: 20px;">
                <h1 id="previewName2" style="font-size: 28px; font-weight: bold; color: #111827; margin: 0 0 4px 0; word-break: break-word;">Your Name</h1>
                <p id="previewTitle2" style="font-size: 14px; color: #1e40af; font-weight: 600; margin: 0 0 20px 0;">Professional Title</p>
                <div style="background: #eff6ff; padding: 16px; border-radius: 4px; font-size: 12px; color: #1e3a8a; line-height: 1.8;">
                  <div id="previewEmail2" style="margin-bottom: 8px;"><strong>Email:</strong> email@example.com</div>
                  <div id="previewPhone2" style="margin-bottom: 8px;"><strong>Phone:</strong> +1 (555) 123-4567</div>
                  <div id="previewLocation2"><strong>Location:</strong> Location</div>
                </div>
              </div>
              <div>
                <div id="summarySection2" style="margin-bottom: 24px; display: none;">
                  <h2 style="font-size: 16px; font-weight: bold; color: #1e40af; margin: 0 0 12px 0; border-bottom: 1px solid #bfdbfe; padding-bottom: 8px;">PROFESSIONAL SUMMARY</h2>
                  <p id="previewSummary2" style="color: #374151; line-height: 1.7; font-size: 13px; margin: 0;"></p>
                </div>
                <div id="experienceSection2" style="margin-bottom: 24px; display: none;">
                  <h2 style="font-size: 16px; font-weight: bold; color: #1e40af; margin: 0 0 16px 0; border-bottom: 1px solid #bfdbfe; padding-bottom: 8px;">EXPERIENCE</h2>
                  <div id="previewExperience2" style="display: flex; flex-direction: column; gap: 16px;"></div>
                </div>
                <div id="educationSection2" style="margin-bottom: 24px; display: none;">
                  <h2 style="font-size: 16px; font-weight: bold; color: #1e40af; margin: 0 0 16px 0; border-bottom: 1px solid #bfdbfe; padding-bottom: 8px;">EDUCATION</h2>
                  <div id="previewEducation2" style="display: flex; flex-direction: column; gap: 16px;"></div>
                </div>
                <div id="skillsSection2" style="display: none;">
                  <h2 style="font-size: 16px; font-weight: bold; color: #1e40af; margin: 0 0 12px 0; border-bottom: 1px solid #bfdbfe; padding-bottom: 8px;">SKILLS</h2>
                  <div id="previewSkills2" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
                </div>
              </div>
            </div>
          </div>

          <div style="display: <?php echo ($template_layout == 3) ? 'block' : 'none'; ?>" class="template-preview" data-template="3">
            <!-- Creative Designer: Sidebar + main, Playfair Display, purple -->
            <div style="font-family: 'Playfair Display', serif; padding: 0; background: white; display: grid; grid-template-columns: 280px 1fr;">
              <div style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); padding: 32px 24px; color: white;">
                <h1 id="previewName3" style="font-size: 26px; font-weight: bold; margin: 0 0 16px 0; word-break: break-word;">Your Name</h1>
                <p id="previewTitle3" style="font-size: 14px; margin: 0 0 24px 0; opacity: 0.9;">Professional Title</p>
                <div style="font-family: Inter, sans-serif; font-size: 12px; space-y: 12px;">
                  <div id="previewEmail3" style="margin-bottom: 12px; overflow-wrap: break-word;"><i class="fas fa-envelope" style="margin-right: 8px;"></i>email@example.com</div>
                  <div id="previewPhone3" style="margin-bottom: 12px;"><i class="fas fa-phone" style="margin-right: 8px;"></i>+1 (555) 123-4567</div>
                  <div id="previewLocation3"><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Location</div>
                </div>
              </div>
              <div style="padding: 32px;">
                <div id="summarySection3" style="margin-bottom: 24px; display: none;">
                  <h2 style="font-size: 16px; font-weight: bold; color: #8b5cf6; margin: 0 0 12px 0;">ABOUT</h2>
                  <p id="previewSummary3" style="color: #374151; line-height: 1.6; font-size: 13px; margin: 0; font-family: Inter, sans-serif;"></p>
                </div>
                <div id="experienceSection3" style="margin-bottom: 24px; display: none;">
                  <h2 style="font-size: 16px; font-weight: bold; color: #8b5cf6; margin: 0 0 16px 0;">EXPERIENCE</h2>
                  <div id="previewExperience3" style="display: flex; flex-direction: column; gap: 16px;"></div>
                </div>
                <div id="educationSection3" style="margin-bottom: 24px; display: none;">
                  <h2 style="font-size: 16px; font-weight: bold; color: #8b5cf6; margin: 0 0 16px 0;">EDUCATION</h2>
                  <div id="previewEducation3" style="display: flex; flex-direction: column; gap: 16px;"></div>
                </div>
                <div id="skillsSection3" style="display: none;">
                  <h2 style="font-size: 16px; font-weight: bold; color: #8b5cf6; margin: 0 0 12px 0;">SKILLS</h2>
                  <div id="previewSkills3" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
                </div>
              </div>
            </div>
          </div>

          <div style="display: <?php echo ($template_layout == 4) ? 'block' : 'none'; ?>" class="template-preview" data-template="4">
            <!-- Minimalist Clean: Very minimal, lots of whitespace, green -->
            <div style="font-family: Inter, sans-serif; padding: 50px 40px; background: #fafafa;">
              <div style="margin-bottom: 48px;">
                <h1 id="previewName4" style="font-size: 36px; font-weight: 300; color: #111827; margin: 0; letter-spacing: -0.5px;">Your Name</h1>
                <p id="previewTitle4" style="font-size: 16px; color: #10b981; margin: 8px 0 0 0; font-weight: 500; letter-spacing: 0.5px;">PROFESSIONAL TITLE</p>
              </div>
              <div style="display: flex; gap: 32px; font-size: 12px; color: #6b7280; margin-bottom: 48px;">
                <span id="previewEmail4">email@example.com</span>
                <span id="previewPhone4">+1 (555) 123-4567</span>
                <span id="previewLocation4">Location</span>
              </div>
              <div id="summarySection4" style="margin-bottom: 48px; display: none;">
                <p id="previewSummary4" style="color: #374151; line-height: 1.8; font-size: 13px; margin: 0; max-width: 500px;"></p>
              </div>
              <div id="experienceSection4" style="margin-bottom: 48px; display: none;">
                <h2 style="font-size: 11px; font-weight: 600; color: #111827; text-transform: uppercase; margin: 0 0 24px 0; letter-spacing: 2px;">Experience</h2>
                <div id="previewExperience4" style="display: flex; flex-direction: column; gap: 24px;"></div>
              </div>
              <div id="educationSection4" style="margin-bottom: 48px; display: none;">
                <h2 style="font-size: 11px; font-weight: 600; color: #111827; text-transform: uppercase; margin: 0 0 24px 0; letter-spacing: 2px;">Education</h2>
                <div id="previewEducation4" style="display: flex; flex-direction: column; gap: 24px;"></div>
              </div>
              <div id="skillsSection4" style="display: none;">
                <h2 style="font-size: 11px; font-weight: 600; color: #111827; text-transform: uppercase; margin: 0 0 16px 0; letter-spacing: 2px;">Skills</h2>
                <div id="previewSkills4" style="display: flex; flex-wrap: wrap; gap: 12px;"></div>
              </div>
            </div>
          </div>

          <div style="display: <?php echo ($template_layout == 9) ? 'block' : 'none'; ?>" class="template-preview" data-template="9">
            <!-- Berlin Clean: Modern bold, Helvetica, dark gray headers -->
            <div style="font-family: Helvetica, Arial, sans-serif; padding: 40px; background: white;">
              <div style="margin-bottom: 32px;">
                <h1 id="previewName9" style="font-size: 36px; font-weight: bold; color: #2d3748; margin: 0 0 4px 0;">Your Name</h1>
                <p id="previewTitle9" style="font-size: 14px; color: #4299e1; font-weight: 600; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Professional Title</p>
              </div>
              <div style="display: flex; gap: 24px; font-size: 12px; color: #4b5563; margin-bottom: 32px;">
                <span id="previewEmail9">email@example.com</span>
                <span id="previewPhone9">+1 (555) 123-4567</span>
                <span id="previewLocation9">Location</span>
              </div>
              <div id="summarySection9" style="margin-bottom: 28px; display: none;">
                <h2 style="font-size: 13px; font-weight: bold; color: #2d3748; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 0.5px; border-bottom: 2px solid #4299e1; padding-bottom: 8px;">Summary</h2>
                <p id="previewSummary9" style="color: #374151; line-height: 1.6; margin: 0; font-size: 13px;"></p>
              </div>
              <div id="experienceSection9" style="margin-bottom: 28px; display: none;">
                <h2 style="font-size: 13px; font-weight: bold; color: #2d3748; text-transform: uppercase; margin: 0 0 16px 0; letter-spacing: 0.5px; border-bottom: 2px solid #4299e1; padding-bottom: 8px;">Experience</h2>
                <div id="previewExperience9" style="display: flex; flex-direction: column; gap: 12px;"></div>
              </div>
              <div id="educationSection9" style="margin-bottom: 28px; display: none;">
                <h2 style="font-size: 13px; font-weight: bold; color: #2d3748; text-transform: uppercase; margin: 0 0 16px 0; letter-spacing: 0.5px; border-bottom: 2px solid #4299e1; padding-bottom: 8px;">Education</h2>
                <div id="previewEducation9" style="display: flex; flex-direction: column; gap: 12px;"></div>
              </div>
              <div id="skillsSection9" style="display: none;">
                <h2 style="font-size: 13px; font-weight: bold; color: #2d3748; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 0.5px; border-bottom: 2px solid #4299e1; padding-bottom: 8px;">Skills</h2>
                <div id="previewSkills9" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
              </div>
            </div>
          </div>

          <div style="display: <?php echo ($template_layout == 10) ? 'block' : 'none'; ?>" class="template-preview" data-template="10">
            <!-- Vienna Contemporary: Two-column, Open Sans, navy -->
            <div style="font-family: 'Open Sans', sans-serif; padding: 40px; background: white; display: grid; grid-template-columns: 35% 65%; gap: 40px;">
              <div>
                <h1 id="previewName10" style="font-size: 32px; font-weight: 600; color: #1a202c; margin: 0 0 8px 0; line-height: 1.2;">Your Name</h1>
                <p id="previewTitle10" style="font-size: 14px; color: #003366; font-weight: 700; margin: 0 0 32px 0; text-transform: uppercase; letter-spacing: 1px;">Professional Title</p>
                <div style="background: #f0f5ff; padding: 20px; border-radius: 4px; font-size: 12px; color: #003366; line-height: 1.8;">
                  <div id="previewEmail10" style="margin-bottom: 10px;"><strong>Email</strong><br>email@example.com</div>
                  <div id="previewPhone10" style="margin-bottom: 10px;"><strong>Phone</strong><br>+1 (555) 123-4567</div>
                  <div id="previewLocation10"><strong>Location</strong><br>Location</div>
                </div>
              </div>
              <div>
                <div id="summarySection10" style="margin-bottom: 24px; display: none;">
                  <h2 style="font-size: 14px; font-weight: 700; color: #003366; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 1px;">Profile</h2>
                  <p id="previewSummary10" style="color: #374151; line-height: 1.7; margin: 0; font-size: 13px;"></p>
                </div>
                <div id="experienceSection10" style="margin-bottom: 24px; display: none;">
                  <h2 style="font-size: 14px; font-weight: 700; color: #003366; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 1px;">Experience</h2>
                  <div id="previewExperience10" style="display: flex; flex-direction: column; gap: 14px;"></div>
                </div>
                <div id="educationSection10" style="margin-bottom: 24px; display: none;">
                  <h2 style="font-size: 14px; font-weight: 700; color: #003366; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 1px;">Education</h2>
                  <div id="previewEducation10" style="display: flex; flex-direction: column; gap: 14px;"></div>
                </div>
                <div id="skillsSection10" style="display: none;">
                  <h2 style="font-size: 14px; font-weight: 700; color: #003366; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 1px;">Skills</h2>
                  <div id="previewSkills10" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
                </div>
              </div>
            </div>
          </div>

          <div style="display: <?php echo ($template_layout == 11) ? 'block' : 'none'; ?>" class="template-preview" data-template="11">
            <!-- Madrid Creative: Bold colored headers, Montserrat -->
            <div style="font-family: 'Montserrat', sans-serif; padding: 40px; background: #fafbfc;">
              <div style="margin-bottom: 32px;">
                <h1 id="previewName11" style="font-size: 38px; font-weight: 700; color: #1a202c; margin: 0 0 4px 0;">Your Name</h1>
                <p id="previewTitle11" style="font-size: 16px; color: #17a2b8; font-weight: 600; margin: 0;">Professional Title</p>
              </div>
              <div style="display: flex; gap: 20px; font-size: 13px; color: #4b5563; margin-bottom: 28px;">
                <span id="previewEmail11">email@example.com</span>
                <span id="previewPhone11">+1 (555) 123-4567</span>
                <span id="previewLocation11">Location</span>
              </div>
              <div id="summarySection11" style="margin-bottom: 28px; padding: 16px; background: rgba(23, 162, 184, 0.1); border-radius: 4px; display: none;">
                <h2 style="font-size: 14px; font-weight: 700; color: #17a2b8; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px;">Summary</h2>
                <p id="previewSummary11" style="color: #374151; line-height: 1.6; margin: 0; font-size: 13px;"></p>
              </div>
              <div id="experienceSection11" style="margin-bottom: 28px; padding: 16px; background: rgba(23, 162, 184, 0.08); border-radius: 4px; display: none;">
                <h2 style="font-size: 14px; font-weight: 700; color: #17a2b8; margin: 0 0 14px 0; text-transform: uppercase; letter-spacing: 1px;">Experience</h2>
                <div id="previewExperience11" style="display: flex; flex-direction: column; gap: 12px;"></div>
              </div>
              <div id="educationSection11" style="margin-bottom: 28px; padding: 16px; background: rgba(32, 201, 151, 0.08); border-radius: 4px; display: none;">
                <h2 style="font-size: 14px; font-weight: 700; color: #20c997; margin: 0 0 14px 0; text-transform: uppercase; letter-spacing: 1px;">Education</h2>
                <div id="previewEducation11" style="display: flex; flex-direction: column; gap: 12px;"></div>
              </div>
              <div id="skillsSection11" style="padding: 16px; background: rgba(23, 162, 184, 0.06); border-radius: 4px; display: none;">
                <h2 style="font-size: 14px; font-weight: 700; color: #17a2b8; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 1px;">Skills</h2>
                <div id="previewSkills11" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
              </div>
            </div>
          </div>

          <div style="display: <?php echo ($template_layout == 12) ? 'block' : 'none'; ?>" class="template-preview" data-template="12">
            <!-- Tokyo Tech: Ultra-minimal, Roboto, cyan accents -->
            <div style="font-family: 'Roboto', sans-serif; padding: 50px 40px; background: #ffffff; color: #1a202c;">
              <div style="margin-bottom: 50px;">
                <h1 id="previewName12" style="font-size: 40px; font-weight: 300; color: #1a202c; margin: 0; letter-spacing: -0.5px;">Your Name</h1>
                <div style="height: 2px; background: #00d4ff; width: 60px; margin: 16px 0;"></div>
                <p id="previewTitle12" style="font-size: 13px; color: #00d4ff; margin: 0; font-weight: 500; text-transform: uppercase; letter-spacing: 2px;">professional::title()</p>
              </div>
              <div style="display: flex; gap: 32px; font-size: 12px; margin-bottom: 50px; color: #6b7280;">
                <span id="previewEmail12">email@example.com</span>
                <span id="previewPhone12">+1 (555) 123-4567</span>
                <span id="previewLocation12">Location</span>
              </div>
              <div id="summarySection12" style="margin-bottom: 50px; display: none;">
                <p id="previewSummary12" style="color: #374151; line-height: 1.8; margin: 0; font-size: 13px; max-width: 550px;"></p>
              </div>
              <div id="experienceSection12" style="margin-bottom: 50px; display: none;">
                <h2 style="font-size: 11px; font-weight: 600; color: #1a202c; text-transform: uppercase; margin: 0 0 24px 0; letter-spacing: 2px;">» experience[]</h2>
                <div id="previewExperience12" style="display: flex; flex-direction: column; gap: 20px;"></div>
              </div>
              <div id="educationSection12" style="margin-bottom: 50px; display: none;">
                <h2 style="font-size: 11px; font-weight: 600; color: #1a202c; text-transform: uppercase; margin: 0 0 24px 0; letter-spacing: 2px;">» education[]</h2>
                <div id="previewEducation12" style="display: flex; flex-direction: column; gap: 20px;"></div>
              </div>
              <div id="skillsSection12" style="display: none;">
                <h2 style="font-size: 11px; font-weight: 600; color: #1a202c; text-transform: uppercase; margin: 0 0 16px 0; letter-spacing: 2px;">» skills</h2>
                <div id="previewSkills12" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
              </div>
            </div>
          </div>

          <div style="display: <?php echo !in_array($template_layout, [1,2,3,4,9,10,11,12]) ? 'block' : 'none'; ?>" class="template-preview" data-template="default">
            <!-- Default/Fallback: Modern Professional -->
            <div style="font-family: Inter, sans-serif; padding: 40px; background: white;">
              <div style="border-bottom: 3px solid #1e40af; padding-bottom: 24px; margin-bottom: 24px;">
                <h1 id="previewName" style="font-size: 32px; font-weight: bold; margin: 0 0 8px 0; color: #111827;">Your Name</h1>
                <p id="previewTitle" style="font-size: 18px; color: #1e40af; margin: 0 0 12px 0; font-weight: 500;">Professional Title</p>
                <div style="display: flex; gap: 16px; font-size: 13px; color: #4b5563;">
                  <span id="previewEmail"><i class="fas fa-envelope" style="color: #1e40af; margin-right: 6px;"></i>email@example.com</span>
                  <span id="previewPhone"><i class="fas fa-phone" style="color: #1e40af; margin-right: 6px;"></i>+1 (555) 123-4567</span>
                  <span id="previewLocation"><i class="fas fa-map-marker-alt" style="color: #1e40af; margin-right: 6px;"></i>Location</span>
                </div>
              </div>
              <div id="summarySection" style="margin-bottom: 24px; display: none;">
                <h2 style="font-size: 14px; font-weight: bold; color: #111827; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 1px;">Professional Summary</h2>
                <p id="previewSummary" style="color: #374151; line-height: 1.6; margin: 0;"></p>
              </div>
              <div id="experienceSection" style="margin-bottom: 24px; display: none;">
                <h2 style="font-size: 14px; font-weight: bold; color: #111827; text-transform: uppercase; margin: 0 0 16px 0; letter-spacing: 1px;">Work Experience</h2>
                <div id="previewExperience" style="display: flex; flex-direction: column; gap: 16px;"></div>
              </div>
              <div id="educationSection" style="margin-bottom: 24px; display: none;">
                <h2 style="font-size: 14px; font-weight: bold; color: #111827; text-transform: uppercase; margin: 0 0 16px 0; letter-spacing: 1px;">Education</h2>
                <div id="previewEducation" style="display: flex; flex-direction: column; gap: 16px;"></div>
              </div>
              <div id="skillsSection" style="display: none;">
                <h2 style="font-size: 14px; font-weight: bold; color: #111827; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 1px;">Skills</h2>
                <div id="previewSkills" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Database Save Integration -->
  <script>
    // Template theme configuration from PHP
    const themeConfig = {
      primaryColor: '<?php echo htmlspecialchars($primary_color); ?>',
      fontFamily: '<?php echo htmlspecialchars($font_family); ?>',
      templateName: '<?php echo htmlspecialchars($template_name); ?>'
    };

    // Apply theme to preview
    function applyTheme() {
      const preview = document.getElementById('resumePreview');
      if (!preview) return;

      // Each template has its own complete styling via inline styles
      // No additional theme application needed as layouts are pre-styled
    }

    // Get hidden form values
    const resumeId = document.getElementById('resumeId').value;
    const templateId = document.getElementById('templateId').value;

    // Load existing resume data if editing
    if (resumeId) {
      fetch(`includes/resume-load-handler.php?resume_id=${resumeId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.resume) {
            const resume = data.resume;

            // Populate form fields
            if (resume.personal) {
              document.getElementById('firstName').value = resume.personal.firstName || '';
              document.getElementById('lastName').value = resume.personal.lastName || '';
              document.getElementById('title').value = resume.personal.title || '';
              document.getElementById('email').value = resume.personal.email || '';
              document.getElementById('phone').value = resume.personal.phone || '';
              document.getElementById('location').value = resume.personal.location || '';
              document.getElementById('summary').value = resume.personal.summary || '';
            }

            // Restore resume data in localStorage for builder.js to use
            const resumeData = {
              personal: resume.personal || {},
              experiences: resume.experiences || [],
              education: resume.education || [],
              skills: resume.skills || []
            };
            localStorage.setItem("resumeData", JSON.stringify(resumeData));

            // Trigger builder.js to load and render
            if (window.loadResumeFromStorage) {
              window.loadResumeFromStorage();
            }
          }
        })
        .catch(error => console.error('Error loading resume:', error));
    }

    // Override save to go to database instead of localStorage
    async function saveResumeToDatabase() {
      // Gather all form data
      const resumeData = {
        personal: {
          firstName: document.getElementById('firstName').value,
          lastName: document.getElementById('lastName').value,
          title: document.getElementById('title').value,
          email: document.getElementById('email').value,
          phone: document.getElementById('phone').value,
          location: document.getElementById('location').value,
          linkedin: document.getElementById('linkedin')?.value || '',
          summary: document.getElementById('summary').value
        },
        experiences: [],
        education: [],
        skills: []
      };

      // Gather experiences
      const experienceList = document.querySelectorAll('[data-experience-index]');
      experienceList.forEach((item, index) => {
        resumeData.experiences.push({
          title: item.querySelector('[data-exp-title]')?.value || '',
          company: item.querySelector('[data-exp-company]')?.value || '',
          location: item.querySelector('[data-exp-location]')?.value || '',
          startDate: item.querySelector('[data-exp-start]')?.value || '',
          endDate: item.querySelector('[data-exp-end]')?.value || '',
          current: item.querySelector('[data-exp-current]')?.checked || false,
          description: item.querySelector('[data-exp-description]')?.value || ''
        });
      });

      // Gather education
      const educationList = document.querySelectorAll('[data-education-index]');
      educationList.forEach((item, index) => {
        resumeData.education.push({
          school: item.querySelector('[data-edu-school]')?.value || '',
          degree: item.querySelector('[data-edu-degree]')?.value || '',
          field: item.querySelector('[data-edu-field]')?.value || '',
          startYear: item.querySelector('[data-edu-start]')?.value || '',
          endYear: item.querySelector('[data-edu-end]')?.value || '',
          current: item.querySelector('[data-edu-current]')?.checked || false,
          grade: item.querySelector('[data-edu-grade]')?.value || '',
          info: item.querySelector('[data-edu-info]')?.value || ''
        });
      });

      // Gather skills - from preview skills div or input
      const skillsInput = document.getElementById('skills');
      if (skillsInput && skillsInput.value) {
        resumeData.skills = skillsInput.value.split(',').map(s => s.trim()).filter(s => s);
      }

      // Prepare save data
      const saveData = {
        ...resumeData,
        title: document.getElementById('firstName').value + ' ' + document.getElementById('lastName').value + '\'s Resume'
      };

      if (resumeId) {
        saveData.resume_id = resumeId;
      } else {
        saveData.template_id = templateId;
      }

      try {
        const response = await fetch('includes/resume-save-handler.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(saveData)
        });

        const result = await response.json();

        if (result.success) {
          alert('Resume saved successfully!');
          // Redirect to my-resume.php
          window.location.href = 'my-resume.php';
        } else {
          alert('Error saving resume: ' + (result.error || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error saving resume: ' + error.message);
      }
    }
  </script>

  <script src="assets/JS/builder.js"></script>

  <!-- Apply template theme after builder.js loads -->
  <script>
    // Get template ID
    const currentTemplateId = parseInt(document.getElementById('templateId').value) || 1;

    // Wait for DOM and builder.js to be ready
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        applyTheme();
      }, 100);
    });

    // Override updatePreview to apply theme after each update
    const originalUpdatePreview = window.updatePreview;
    if (originalUpdatePreview) {
      window.updatePreview = function() {
        originalUpdatePreview.apply(this, arguments);

        // Update template-specific elements
        const templateSuffix = currentTemplateId;
        const { personal, experiences, education, skills } = resumeData;

        // Update personal info for current template
        const fullName = `${personal.firstName} ${personal.lastName}`.trim() || "Your Name";
        const elements = {
          name: document.getElementById(`previewName${templateSuffix}`),
          title: document.getElementById(`previewTitle${templateSuffix}`),
          email: document.getElementById(`previewEmail${templateSuffix}`),
          phone: document.getElementById(`previewPhone${templateSuffix}`),
          location: document.getElementById(`previewLocation${templateSuffix}`),
          summary: document.getElementById(`previewSummary${templateSuffix}`),
          summarySection: document.getElementById(`summarySection${templateSuffix}`),
          experienceSection: document.getElementById(`experienceSection${templateSuffix}`),
          previewExperience: document.getElementById(`previewExperience${templateSuffix}`),
          educationSection: document.getElementById(`educationSection${templateSuffix}`),
          previewEducation: document.getElementById(`previewEducation${templateSuffix}`),
          skillsSection: document.getElementById(`skillsSection${templateSuffix}`),
          previewSkills: document.getElementById(`previewSkills${templateSuffix}`),
        };

        // Update name and title
        if (elements.name) elements.name.textContent = fullName;
        if (elements.title) {
          if (templateSuffix === 12) {
            // Template 12: Tech-style formatting
            elements.title.textContent = (personal.title || "Professional Title").toLowerCase() + "()";
          } else {
            elements.title.textContent = personal.title || "Professional Title";
          }
        }

        // Update contact info
        if (elements.email) {
          if ([2, 6, 10].includes(templateSuffix)) {
            // Templates 2, 6, 10: Boxed format with labels
            elements.email.innerHTML = `<strong>Email</strong><br>${personal.email}`;
          } else {
            elements.email.textContent = personal.email;
            elements.email.innerHTML = `<i class="fas fa-envelope" style="margin-right: 6px;"></i>${personal.email}`;
          }
        }
        if (elements.phone) {
          if ([2, 6, 10].includes(templateSuffix)) {
            // Templates 2, 6, 10: Boxed format with labels
            elements.phone.innerHTML = `<strong>Phone</strong><br>${personal.phone}`;
          } else {
            elements.phone.textContent = personal.phone;
            elements.phone.innerHTML = `<i class="fas fa-phone" style="margin-right: 6px;"></i>${personal.phone}`;
          }
        }
        if (elements.location) {
          if ([2, 6, 10].includes(templateSuffix)) {
            // Templates 2, 6, 10: Boxed format with labels
            elements.location.innerHTML = `<strong>Location</strong><br>${personal.location}`;
          } else {
            elements.location.textContent = personal.location;
            elements.location.innerHTML = `<i class="fas fa-map-marker-alt" style="margin-right: 6px;"></i>${personal.location}`;
          }
        }

        // Update summary
        if (personal.summary && elements.summary) {
          elements.summarySection.style.display = "block";
          elements.summary.textContent = personal.summary;
        } else if (elements.summarySection) {
          elements.summarySection.style.display = "none";
        }

        // Update experiences
        if (experiences.length > 0 && experiences.some(exp => exp.title || exp.company)) {
          if (elements.experienceSection) elements.experienceSection.style.display = "block";
          if (elements.previewExperience) {
            elements.previewExperience.innerHTML = experiences.map(exp => {
              if (!exp.title && !exp.company) return "";
              return `
                <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                      <h3 style="font-weight: bold; color: #111827; margin: 0; font-size: 14px;">${exp.title || 'Job Title'}</h3>
                      <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 13px;">${exp.company || 'Company'}</p>
                    </div>
                    <div style="text-align: right; font-size: 12px; color: #6b7280;">
                      ${exp.startDate ? exp.startDate + ' - ' + (exp.endDate || 'Present') : ''}
                    </div>
                  </div>
                  ${exp.location ? `<p style="font-size: 12px; color: #6b7280; margin: 0;">${exp.location}</p>` : ''}
                  ${exp.description ? `<p style="color: #374151; margin: 8px 0 0 0; font-size: 13px;">${exp.description}</p>` : ''}
                </div>
              `;
            }).join("");
          }
        } else if (elements.experienceSection) {
          elements.experienceSection.style.display = "none";
        }

        // Update education
        if (education.length > 0 && education.some(edu => edu.degree || edu.school)) {
          if (elements.educationSection) elements.educationSection.style.display = "block";
          if (elements.previewEducation) {
            elements.previewEducation.innerHTML = education.map(edu => {
              if (!edu.degree && !edu.school) return "";
              return `
                <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                      <h3 style="font-weight: bold; color: #111827; margin: 0; font-size: 14px;">${edu.degree || 'Degree'}</h3>
                      <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 13px;">${edu.school || 'School'}</p>
                    </div>
                    <div style="text-align: right; font-size: 12px; color: #6b7280;">
                      ${edu.startYear ? edu.startYear + ' - ' + (edu.endYear || 'Present') : ''}
                    </div>
                  </div>
                  ${edu.field ? `<p style="font-size: 12px; color: #6b7280; margin: 0;">${edu.field}</p>` : ''}
                  ${edu.info ? `<p style="color: #374151; margin: 8px 0 0 0; font-size: 13px;">${edu.info}</p>` : ''}
                </div>
              `;
            }).join("");
          }
        } else if (elements.educationSection) {
          elements.educationSection.style.display = "none";
        }

        // Update skills
        if (skills.length > 0) {
          if (elements.skillsSection) elements.skillsSection.style.display = "block";
          if (elements.previewSkills) {
            elements.previewSkills.innerHTML = skills.map(skill =>
              `<span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 4px; font-size: 12px;">${skill}</span>`
            ).join("");
          }
        } else if (elements.skillsSection) {
          elements.skillsSection.style.display = "none";
        }

        applyTheme();
      };
    }
  </script>
</body>

</html>