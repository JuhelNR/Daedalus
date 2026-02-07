// Resume data object
let resumeData = {
  personal: {
    firstName: "",
    lastName: "",
    title: "",
    email: "",
    phone: "",
    location: "",
    linkedin: "",
    summary: "",
  },
  experiences: [],
  education: [],
  skills: [],
};

let experienceCount = 0;
let educationCount = 0;

// Initialize
document.addEventListener("DOMContentLoaded", function () {
  // Add input listeners for personal info
  const personalFields = [
    "firstName",
    "lastName",
    "title",
    "email",
    "phone",
    "location",
    "linkedin",
    "summary",
  ];
  personalFields.forEach((field) => {
    const element = document.getElementById(field);
    if (element) {
      element.addEventListener("input", function () {
        resumeData.personal[field] = this.value;
        updatePreview();
      });
    }
  });

  // Add initial experience and education
  addExperience();
  addEducation();

  // Save button
  document.getElementById("saveBtn").addEventListener("click", saveResume);

  // Download button
  document.getElementById("downloadBtn").addEventListener("click", downloadPDF);

  // Skill input enter key
  document
    .getElementById("skillInput")
    .addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        addSkill();
      }
    });
});

// Navigation between steps
function nextStep(step) {
  // Hide all steps
  for (let i = 1; i <= 4; i++) {
    document.getElementById(`step${i}`).classList.add("hidden");
    const indicator = document.getElementById(`step${i}-indicator`);
    indicator.classList.remove(
      "orange-gradient",
      "text-white",
      "shadow-lg",
      "shadow-orange-500/30",
    );
    indicator.classList.add("bg-gray-200", "text-gray-500");
  }

  // Show current step
  document.getElementById(`step${step}`).classList.remove("hidden");
  const currentIndicator = document.getElementById(`step${step}-indicator`);
  currentIndicator.classList.remove("bg-gray-200", "text-gray-500");
  currentIndicator.classList.add(
    "orange-gradient",
    "text-white",
    "shadow-lg",
    "shadow-orange-500/30",
  );

  // Update progress bars
  for (let i = 1; i < step; i++) {
    const indicator = document.getElementById(`step${i}-indicator`);
    indicator.classList.remove("bg-gray-200", "text-gray-500");
    indicator.classList.add(
      "orange-gradient",
      "text-white",
      "shadow-lg",
      "shadow-orange-500/30",
    );
  }

  // Scroll to top
  window.scrollTo({ top: 0, behavior: "smooth" });
}

// Add experience entry
function addExperience() {
  experienceCount++;
  const id = `exp${experienceCount}`;

  const experienceHTML = `
        <div id="${id}" class="border-2 border-orange-100 rounded-xl p-5 bg-orange-50/30 hover:border-orange-200 transition">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-900">Position ${experienceCount}</h3>
                <button onclick="removeExperience('${id}')" class="text-red-500 hover:text-red-700 hover:bg-red-50 w-8 h-8 rounded-lg transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                    <input type="text" id="${id}-title" placeholder="Software Engineer" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                    <input type="text" id="${id}-company" placeholder="Tech Company Inc." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="text" id="${id}-startDate" placeholder="Jan 2020" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="text" id="${id}-endDate" placeholder="Present" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" id="${id}-location" placeholder="New York, NY" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="${id}-description" rows="3" placeholder="• Developed and maintained web applications
• Collaborated with cross-functional teams
• Improved system performance by 40%" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none resize-none transition bg-white"></textarea>
                </div>
            </div>
        </div>
    `;

  document
    .getElementById("experienceList")
    .insertAdjacentHTML("beforeend", experienceHTML);

  // Add event listeners
  const fields = [
    "title",
    "company",
    "startDate",
    "endDate",
    "location",
    "description",
  ];
  fields.forEach((field) => {
    document
      .getElementById(`${id}-${field}`)
      .addEventListener("input", function () {
        updateExperienceData();
      });
  });

  updateExperienceData();
}

function removeExperience(id) {
  document.getElementById(id).remove();
  updateExperienceData();
}

function updateExperienceData() {
  resumeData.experiences = [];
  const experienceElements = document.querySelectorAll('[id^="exp"]');

  experienceElements.forEach((exp) => {
    const id = exp.id;
    if (document.getElementById(`${id}-title`)) {
      const experience = {
        title: document.getElementById(`${id}-title`).value,
        company: document.getElementById(`${id}-company`).value,
        startDate: document.getElementById(`${id}-startDate`).value,
        endDate: document.getElementById(`${id}-endDate`).value,
        location: document.getElementById(`${id}-location`).value,
        description: document.getElementById(`${id}-description`).value,
      };
      if (experience.title || experience.company) {
        resumeData.experiences.push(experience);
      }
    }
  });

  updatePreview();
}

// Add education entry
function addEducation() {
  educationCount++;
  const id = `edu${educationCount}`;

  const educationHTML = `
        <div id="${id}" class="border-2 border-orange-100 rounded-xl p-5 bg-orange-50/30 hover:border-orange-200 transition">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-900">Education ${educationCount}</h3>
                <button onclick="removeEducation('${id}')" class="text-red-500 hover:text-red-700 hover:bg-red-50 w-8 h-8 rounded-lg transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Degree *</label>
                    <input type="text" id="${id}-degree" placeholder="Bachelor of Science in Computer Science" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">School *</label>
                    <input type="text" id="${id}-school" placeholder="University of Example" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Year</label>
                        <input type="text" id="${id}-startYear" placeholder="2016" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Year</label>
                        <input type="text" id="${id}-endYear" placeholder="2020" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" id="${id}-location" placeholder="Boston, MA" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Info</label>
                    <textarea id="${id}-info" rows="2" placeholder="GPA: 3.8/4.0, Dean's List" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none resize-none transition bg-white"></textarea>
                </div>
            </div>
        </div>
    `;

  document
    .getElementById("educationList")
    .insertAdjacentHTML("beforeend", educationHTML);

  // Add event listeners
  const fields = [
    "degree",
    "school",
    "startYear",
    "endYear",
    "location",
    "info",
  ];
  fields.forEach((field) => {
    document
      .getElementById(`${id}-${field}`)
      .addEventListener("input", function () {
        updateEducationData();
      });
  });

  updateEducationData();
}

function removeEducation(id) {
  document.getElementById(id).remove();
  updateEducationData();
}

function updateEducationData() {
  resumeData.education = [];
  const educationElements = document.querySelectorAll('[id^="edu"]');

  educationElements.forEach((edu) => {
    const id = edu.id;
    if (document.getElementById(`${id}-degree`)) {
      const education = {
        degree: document.getElementById(`${id}-degree`).value,
        school: document.getElementById(`${id}-school`).value,
        startYear: document.getElementById(`${id}-startYear`).value,
        endYear: document.getElementById(`${id}-endYear`).value,
        location: document.getElementById(`${id}-location`).value,
        info: document.getElementById(`${id}-info`).value,
      };
      if (education.degree || education.school) {
        resumeData.education.push(education);
      }
    }
  });

  updatePreview();
}

// Add skill
function addSkill() {
  const skillInput = document.getElementById("skillInput");
  const skill = skillInput.value.trim();

  if (skill) {
    resumeData.skills.push(skill);
    skillInput.value = "";
    renderSkills();
    updatePreview();
  }
}

function removeSkill(index) {
  resumeData.skills.splice(index, 1);
  renderSkills();
  updatePreview();
}

function renderSkills() {
  const skillsList = document.getElementById("skillsList");
  skillsList.innerHTML = "";

  resumeData.skills.forEach((skill, index) => {
    const skillTag = `
            <span class="inline-flex items-center gap-2 bg-orange-100 text-orange-800 px-4 py-2 rounded-xl font-medium">
                ${skill}
                <button onclick="removeSkill(${index})" class="text-orange-600 hover:text-orange-800 ml-1">
                    <i class="fas fa-times"></i>
                </button>
            </span>
        `;
    skillsList.insertAdjacentHTML("beforeend", skillTag);
  });
}

// Update preview
function updatePreview() {
  const { personal, experiences, education, skills } = resumeData;

  // Update name and title
  const fullName =
    `${personal.firstName} ${personal.lastName}`.trim() || "Your Name";
  document.getElementById("previewName").textContent = fullName;
  document.getElementById("previewTitle").textContent =
    personal.title || "Professional Title";

  // Update contact info
  document.getElementById("previewEmail").innerHTML =
    `<i class="fas fa-envelope mr-2 text-orange-500"></i>${personal.email || "email@example.com"}`;
  document.getElementById("previewPhone").innerHTML =
    `<i class="fas fa-phone mr-2 text-orange-500"></i>${personal.phone || "+1 (555) 123-4567"}`;
  document.getElementById("previewLocation").innerHTML =
    `<i class="fas fa-map-marker-alt mr-2 text-orange-500"></i>${personal.location || "Location"}`;

  // Update summary
  const summarySection = document.getElementById("summarySection");
  if (personal.summary) {
    summarySection.classList.remove("hidden");
    document.getElementById("previewSummary").textContent = personal.summary;
  } else {
    summarySection.classList.add("hidden");
  }

  // Update experience
  const experienceSection = document.getElementById("experienceSection");
  const previewExperience = document.getElementById("previewExperience");

  if (
    experiences.length > 0 &&
    experiences.some((exp) => exp.title || exp.company)
  ) {
    experienceSection.classList.remove("hidden");
    previewExperience.innerHTML = experiences
      .map((exp) => {
        if (!exp.title && !exp.company) return "";
        return `
                <div class="mb-5 pb-5 border-b border-gray-100 last:border-0">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">${exp.title || "Job Title"}</h3>
                            <p class="text-orange-600 font-medium">${exp.company || "Company Name"}</p>
                        </div>
                        <div class="text-right text-sm text-gray-600">
                            <p class="font-medium">${exp.startDate || "Start"} - ${exp.endDate || "End"}</p>
                            ${exp.location ? `<p class="text-gray-500">${exp.location}</p>` : ""}
                        </div>
                    </div>
                    ${exp.description ? `<p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line mt-3">${exp.description}</p>` : ""}
                </div>
            `;
      })
      .join("");
  } else {
    experienceSection.classList.add("hidden");
  }

  // Update education
  const educationSection = document.getElementById("educationSection");
  const previewEducation = document.getElementById("previewEducation");

  if (
    education.length > 0 &&
    education.some((edu) => edu.degree || edu.school)
  ) {
    educationSection.classList.remove("hidden");
    previewEducation.innerHTML = education
      .map((edu) => {
        if (!edu.degree && !edu.school) return "";
        return `
                <div class="mb-5 pb-5 border-b border-gray-100 last:border-0">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">${edu.degree || "Degree"}</h3>
                            <p class="text-orange-600 font-medium">${edu.school || "School Name"}</p>
                        </div>
                        <div class="text-right text-sm text-gray-600">
                            <p class="font-medium">${edu.startYear || "Start"} - ${edu.endYear || "End"}</p>
                            ${edu.location ? `<p class="text-gray-500">${edu.location}</p>` : ""}
                        </div>
                    </div>
                    ${edu.info ? `<p class="text-gray-700 text-sm mt-2">${edu.info}</p>` : ""}
                </div>
            `;
      })
      .join("");
  } else {
    educationSection.classList.add("hidden");
  }

  // Update skills
  const skillsSection = document.getElementById("skillsSection");
  const previewSkills = document.getElementById("previewSkills");

  if (skills.length > 0) {
    skillsSection.classList.remove("hidden");
    previewSkills.innerHTML = skills
      .map(
        (skill) =>
          `<span class="bg-orange-100 text-orange-800 px-4 py-2 rounded-xl text-sm font-medium">${skill}</span>`,
      )
      .join("");
  } else {
    skillsSection.classList.add("hidden");
  }
}

// Change template
function changeTemplate(template) {
  const preview = document.getElementById("resumePreview");

  if (template === "classic") {
    preview.style.fontFamily = "Georgia, serif";
  } else {
    preview.style.fontFamily = "Inter, system-ui, -apple-system, sans-serif";
  }
}

// Save resume
function saveResume() {
  localStorage.setItem("resumeData", JSON.stringify(resumeData));

  // Show success message
  const btn = document.getElementById("saveBtn");
  const originalHTML = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-check mr-2"></i>Saved!';
  btn.classList.add("text-green-600");

  setTimeout(() => {
    btn.innerHTML = originalHTML;
    btn.classList.remove("text-green-600");
  }, 2000);
}

// Download PDF
function downloadPDF() {
  const element = document.getElementById("resumePreview");
  const opt = {
    margin: 0.5, // margins in inches
    filename: "My_Resume.pdf", // file name
    image: { type: "jpeg", quality: 0.98 },
    html2canvas: { scale: 2, useCORS: true },
    jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
    pagebreak: { mode: "avoid-all" },
  };

  html2pdf().set(opt).from(element).save();
}



// Generate AI Summary
function generateAISummary(event) {
  const summaryField = document.getElementById("summary");
  const firstName = document.getElementById("firstName").value;
  const title = document.getElementById("title").value;

  // Show loading state
  const btn = event.target.closest("button");
  const originalHTML = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
  btn.disabled = true;

  // Simulate AI generation
  setTimeout(() => {
    let summary = "";

    if (title) {
      summary = `Results-driven ${title} with a proven track record of delivering high-impact solutions and driving organizational success. Combining technical expertise with strong leadership skills to mentor teams and implement best practices. Passionate about innovation and continuous improvement, with a focus on creating value and exceeding expectations.`;
    } else {
      summary = `Accomplished professional with extensive experience in delivering exceptional results and driving business growth. Known for strong analytical skills, innovative problem-solving abilities, and effective collaboration across teams. Committed to excellence and continuous learning, with a track record of successfully managing complex projects and exceeding organizational goals.`;
    }

    summaryField.value = summary;
    resumeData.personal.summary = summary;
    updatePreview();

    // Reset button
    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Generated!';
    setTimeout(() => {
      btn.innerHTML = originalHTML;
      btn.disabled = false;
    }, 2000);
  }, 1500);
}


// Load saved resume if exists
window.addEventListener("load", () => {
  // First, PHP session values are already in the inputs
  // Then, try to load saved resume from localStorage
  const savedData = localStorage.getItem("resumeData");
  if (savedData) {
    const data = JSON.parse(savedData);

    // Load personal info, only overwrite if there’s a value
    Object.keys(data.personal).forEach((key) => {
      const element = document.getElementById(key);
      if (element && data.personal[key]) {
        element.value = data.personal[key]; // update input
        resumeData.personal[key] = data.personal[key]; // sync JS object
      } else if (element) {
        // keep PHP value if localStorage empty
        resumeData.personal[key] = element.value;
      }
    });

    // Load experiences if any
    if (data.experiences && data.experiences.length > 0) {
      // Clear any initial empty entries
      document.getElementById("experienceList").innerHTML = "";
      experienceCount = 0;

      data.experiences.forEach((exp) => {
        addExperience();
        const id = `exp${experienceCount}`;
        document.getElementById(`${id}-title`).value = exp.title || "";
        document.getElementById(`${id}-company`).value = exp.company || "";
        document.getElementById(`${id}-startDate`).value = exp.startDate || "";
        document.getElementById(`${id}-endDate`).value = exp.endDate || "";
        document.getElementById(`${id}-location`).value = exp.location || "";
        document.getElementById(`${id}-description`).value =
          exp.description || "";
      });

      updateExperienceData();
    }

    // Load education if any
    if (data.education && data.education.length > 0) {
      document.getElementById("educationList").innerHTML = "";
      educationCount = 0;

      data.education.forEach((edu) => {
        addEducation();
        const id = `edu${educationCount}`;
        document.getElementById(`${id}-degree`).value = edu.degree || "";
        document.getElementById(`${id}-school`).value = edu.school || "";
        document.getElementById(`${id}-startYear`).value = edu.startYear || "";
        document.getElementById(`${id}-endYear`).value = edu.endYear || "";
        document.getElementById(`${id}-location`).value = edu.location || "";
        document.getElementById(`${id}-info`).value = edu.info || "";
      });

      updateEducationData();
    }

    // Load skills
    if (data.skills && data.skills.length > 0) {
      resumeData.skills = data.skills;
      renderSkills();
    }

    // Finally, update the preview
    updatePreview();
  } else {
    // If no localStorage, just sync JS object with PHP default inputs
    const personalFields = [
      "firstName",
      "lastName",
      "title",
      "email",
      "phone",
      "location",
      "linkedin",
      "summary",
    ];
    personalFields.forEach((field) => {
      const element = document.getElementById(field);
      if (element) resumeData.personal[field] = element.value;
    });

    updatePreview();
  }
});

