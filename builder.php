<?php
session_start();
require_once('includes/middleware.php');

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Get user data from session
$firstName = $_SESSION['fname'] ?? '';
$lastName = $_SESSION['lname'] ?? '';
$email = $_SESSION['email'] ?? '';
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
            class="hidden sm:flex items-center gap-2 text-gray-600 hover:text-gray-900 px-4 py-2 rounded-xl hover:bg-gray-50 transition font-medium text-sm">
            <i class="fas fa-save"></i>
            <span>Save</span>
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
          <div class="flex gap-2">
            <button
              onclick="changeTemplate('modern')"
              class="px-3 py-1.5 text-xs bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 font-medium transition">
              Modern
            </button>
            <button
              onclick="changeTemplate('classic')"
              class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition">
              Classic
            </button>
          </div>
        </div>
        <div id="resumePreview" class="a4-page bg-white shadow-xl p-12 min-h-screen border border-gray-100 rounded-lg overflow-hidden">
          <!-- Header -->
          <div class="border-b-2 border-orange-500 pb-6 mb-6">
            <h1
              id="previewName"
              class="text-4xl font-bold text-gray-900 mb-2 heading-font">
              Your Name
            </h1>
            <p
              id="previewTitle"
              class="text-xl text-orange-600 mb-4 font-medium">
              Professional Title
            </p>
            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
              <span id="previewEmail"><i class="fas fa-envelope mr-2 text-orange-500"></i>email@example.com</span>
              <span id="previewPhone"><i class="fas fa-phone mr-2 text-orange-500"></i>+1 (555)
                123-4567</span>
              <span id="previewLocation"><i class="fas fa-map-marker-alt mr-2 text-orange-500"></i>Location</span>
            </div>
          </div>

          <!-- Summary -->
          <div id="summarySection" class="mb-6 hidden">
            <h2
              class="text-lg font-bold text-gray-900 mb-3 uppercase tracking-wide heading-font flex items-center gap-2">
              <span class="w-1 h-6 bg-orange-500 rounded"></span>
              Professional Summary
            </h2>
            <p id="previewSummary" class="text-gray-700 leading-relaxed"></p>
          </div>

          <!-- Experience -->
          <div id="experienceSection" class="mb-6 hidden">
            <h2
              class="text-lg font-bold text-gray-900 mb-4 uppercase tracking-wide heading-font flex items-center gap-2">
              <span class="w-1 h-6 bg-orange-500 rounded"></span>
              Work Experience
            </h2>
            <div id="previewExperience" class="space-y-5"></div>
          </div>

          <!-- Education -->
          <div id="educationSection" class="mb-6 hidden">
            <h2
              class="text-lg font-bold text-gray-900 mb-4 uppercase tracking-wide heading-font flex items-center gap-2">
              <span class="w-1 h-6 bg-orange-500 rounded"></span>
              Education
            </h2>
            <div id="previewEducation" class="space-y-5"></div>
          </div>

          <!-- Skills -->
          <div id="skillsSection" class="mb-6 hidden">
            <h2
              class="text-lg font-bold text-gray-900 mb-4 uppercase tracking-wide heading-font flex items-center gap-2">
              <span class="w-1 h-6 bg-orange-500 rounded"></span>
              Skills
            </h2>
            <div id="previewSkills" class="flex flex-wrap gap-2"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/JS/builder.js"></script>
</body>

</html>