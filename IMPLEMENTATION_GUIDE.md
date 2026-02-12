# Resume Template & Database Implementation Guide

## Overview
This document explains all the new files and code added to implement the resume template system with database persistence and distinct template designs.

---

## New Files Created

### 1. `includes/resume.model.php`
**Purpose:** Database model layer for resume CRUD operations

**Key Functions:**

#### `create_resume($user_id, $template_id, $title)`
- Creates a new resume record in the database
- Parameters:
  - `$user_id`: The user creating the resume (from session)
  - `$template_id`: Which template design to use (1-8)
  - `$title`: Resume title
- Returns: The auto-generated resume ID
- Example: `$resume_id = create_resume(123, 2, "My Professional Resume");`

#### `update_resume_sections($resume_id, $section_data)`
- Saves all resume content to the database
- Handles atomic transactions (all-or-nothing saves)
- Parameters:
  - `$resume_id`: Which resume to update
  - `$section_data`: Array containing:
    ```php
    [
        'personal' => ['firstName', 'lastName', 'title', 'email', 'phone', 'location', 'summary'],
        'experiences' => [
            ['title', 'company', 'location', 'startDate', 'endDate', 'current', 'description'],
            ...
        ],
        'education' => [
            ['school', 'degree', 'field', 'startYear', 'endYear', 'current', 'grade', 'info'],
            ...
        ],
        'skills' => ['skill1', 'skill2', ...]
    ]
    ```
- Deletes old sections before inserting new ones (reset and rebuild)
- Returns: `true` on success, throws exception on failure

#### `get_resume($resume_id)`
- Retrieves complete resume with all sections
- Returns: Array with structure:
  ```php
  [
      'id', 'user_id', 'template_id', 'title', 'template_name',
      'font_family', 'color_scheme' (as array),
      'personal' (array), 'experiences' (array),
      'education' (array), 'skills' (array)
  ]
  ```
- Used when editing an existing resume

#### `get_user_resumes($user_id)`
- Fetches all resumes for a specific user
- Returns: Array of resume summaries
  ```php
  [
      ['id', 'title', 'template_id', 'template_name', 'created_at', 'updated_at'],
      ...
  ]
  ```
- Ordered by most recently updated first
- Used on `my-resume.php` to display user's resume list

#### `get_template($template_id)`
- Retrieves template styling information
- Returns: Array with:
  ```php
  [
      'id', 'name', 'slug', 'description', 'category',
      'font_family', 'color_scheme' (as parsed JSON array)
  ]
  ```
- Used to get template metadata when editing/creating resumes

#### `get_all_templates()`
- Retrieves all active templates from database
- Returns: Array of all template objects
- Used on `templates.php` to display available templates

---

### 2. `includes/resume-save-handler.php`
**Purpose:** API endpoint for saving/updating resumes to database

**Functionality:**
- Receives JSON POST data from JavaScript
- Validates user authentication (must be logged in)
- Handles both creating new resumes and updating existing ones
- Validates user ownership (users can only save their own resumes)

**Expected Input (JSON):**
```json
{
  "resume_id": null,
  "template_id": 1,
  "title": "John Doe's Resume",
  "personal": {
    "firstName": "John",
    "lastName": "Doe",
    "title": "Software Engineer",
    "email": "john@example.com",
    "phone": "+1-555-1234",
    "location": "New York, NY",
    "linkedin": "linkedin.com/in/johndoe",
    "summary": "Experienced engineer..."
  },
  "experiences": [
    {
      "title": "Senior Developer",
      "company": "Tech Corp",
      "location": "NYC",
      "startDate": "2020-01-01",
      "endDate": "2023-12-31",
      "current": false,
      "description": "Led development..."
    }
  ],
  "education": [
    {
      "school": "University Name",
      "degree": "Bachelor's",
      "field": "Computer Science",
      "startYear": "2018",
      "endYear": "2022",
      "current": false,
      "grade": "3.8",
      "info": "Additional info"
    }
  ],
  "skills": ["JavaScript", "PHP", "React", "MySQL"]
}
```

**Output (JSON):**
- Success: `{"success": true, "resume_id": 123, "message": "Resume saved successfully"}`
- Error: `{"success": false, "error": "Error message describing what went wrong"}`

**Workflow:**
1. Check user authentication via `$_SESSION['uid']`
2. If `resume_id` provided: Verify user owns the resume
3. If creating new: Call `create_resume()` with user_id, template_id, title
4. Call `update_resume_sections()` with all form data
5. Return JSON response

---

### 3. `includes/resume-load-handler.php`
**Purpose:** API endpoint for loading resume data for editing

**Functionality:**
- Receives GET request with `resume_id` parameter
- Fetches resume from database using `get_resume()`
- Validates user ownership
- Transforms database format to JavaScript format
- Returns formatted JSON

**Input:** `?resume_id=123`

**Output (JSON):**
```json
{
  "success": true,
  "resume": {
    "id": 123,
    "title": "John's Resume",
    "template_id": 2,
    "template_name": "Executive Classic",
    "font_family": "Georgia, serif",
    "color_scheme": {"primary": "#1e40af", "accent": "#ffffff"},
    "personal": {
      "firstName": "John",
      "lastName": "Doe",
      ...
    },
    "experiences": [
      {
        "title": "Senior Developer",
        "company": "Tech Corp",
        ...
      }
    ],
    "education": [...],
    "skills": ["JavaScript", "PHP", ...]
  }
}
```

**Transformations:**
- Database dates are converted to expected format
- Database column names mapped to JavaScript property names
- Dates reformatted from YYYY-MM-DD to only year for education

---

### 4. `setup-templates.php`
**Purpose:** One-time setup script to populate templates into database

**Functionality:**
- Checks if templates already exist (prevents duplicates)
- Inserts 8 predefined template records with:
  - Name, slug, category, description
  - Font family for each template
  - Color scheme (JSON encoded)
  - is_premium flag
  - is_active = 1

**Templates Inserted:**
1. Modern Professional - Inter font, Amber (#f59e0b)
2. Executive Classic - Georgia font, Blue (#3b82f6)
3. Creative Designer - Playfair Display font, Purple (#8b5cf6)
4. Minimalist Clean - Inter font, Green (#10b981)
5. Corporate Blue - Calibri font, Deep Blue (#1e40af)
6. Tech Innovator - Inter font, Teal (#059669)
7. Elegant Rose - Playfair Display font, Pink (#ec4899)
8. Bold Impact - Inter font, Red (#dc2626)

**Usage:** Access via `http://localhost/Daedalus/setup-templates.php`

---

## Modified Files

### 1. `templates.php`
**Changes:**

**Line 3:** Added require for resume.model.php
```php
require_once('includes/resume.model.php');
```

**Lines 11-15:** Changed from hardcoded array to database query
```php
// Before: $templates = [array of templates]
// After:
$templates = get_all_templates();
$categories = array_values(array_unique(array_column($templates, 'category')));
```

**Lines 75-101:** Updated template rendering loop
- Now iterates through database templates
- Parses color_scheme JSON for each template
- Links to builder: `builder.php?template_id=<?php echo $template['id']; ?>`

**Lines 106-129:** Enhanced filter button functionality
- Dynamic category buttons generated from database
- JavaScript filter now hides/shows template cards by category

---

### 2. `builder.php`
**Major Changes:**

**Lines 2-50:** Added middleware and template loading
```php
require_once('includes/middleware.php');
require_once('includes/resume.model.php');

// Get template_id or resume_id from URL parameters
$template_id = $_GET['template_id'] ?? null;
$resume_id = $_GET['resume_id'] ?? null;

// Load template styling information
$template = get_template($template_id);
```

**Lines 182-188:** Added hidden fields and template display
```php
<input type="hidden" id="resumeId" value="<?php echo htmlspecialchars($resume_id ?? ''); ?>">
<input type="hidden" id="templateId" value="<?php echo htmlspecialchars($template_id ?? ''); ?>">
<?php if ($template_name): ?>
  <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
    <p class="text-sm text-amber-900"><strong>Template:</strong> <?php echo htmlspecialchars($template_name); ?></p>
  </div>
<?php endif; ?>
```

**Lines 134-140:** Changed Save button
```php
// Before: Save button was hidden, did nothing
// After:
<button id="saveBtn" onclick="saveResumeToDatabase()"...>
  <i class="fas fa-save"></i>
  <span>Save Resume</span>
</button>
```

**Lines 498-661:** Replaced single template with 4 distinct template layouts

Each template preview is a complete, styled HTML structure:
- **Template 1 (Modern Professional):** Single column, amber accents
- **Template 2 (Executive Classic):** Two-column, blue theme, Georgia serif
- **Template 3 (Creative Designer):** Purple gradient sidebar, Playfair serif
- **Template 4 (Minimalist Clean):** Minimal layout, green accents, lots of whitespace

Each template has separate element IDs:
- `previewName1`, `previewName2`, `previewName3`, `previewName4`
- `previewExperience1`, `previewExperience2`, etc.
- This allows different content rendering per template

**Lines 667-682:** Theme configuration
```php
const themeConfig = {
  primaryColor: '<?php echo htmlspecialchars($primary_color); ?>',
  fontFamily: '<?php echo htmlspecialchars($font_family); ?>',
  templateName: '<?php echo htmlspecialchars($template_name); ?>'
};
```

**Lines 684-799:** Resume loading logic
```javascript
// If editing existing resume:
if (resumeId) {
  fetch(`includes/resume-load-handler.php?resume_id=${resumeId}`)
    .then(response => response.json())
    .then(data => {
      // Populate form fields with loaded data
      // Load resume data into builder
    });
}
```

**Lines 801-868:** Save to database function
```javascript
async function saveResumeToDatabase() {
  // Gather all form data
  const saveData = {
    personal: {firstName, lastName, title, email, ...},
    experiences: [...],
    education: [...],
    skills: [...]
  };

  // Include resume_id (if editing) or template_id (if creating)
  if (resumeId) {
    saveData.resume_id = resumeId;
  } else {
    saveData.template_id = templateId;
  }

  // POST to save handler
  const response = await fetch('includes/resume-save-handler.php', {
    method: 'POST',
    body: JSON.stringify(saveData)
  });

  // Redirect to my-resume.php on success
}
```

**Lines 873-1020:** Updated preview rendering
- Overrides `updatePreview()` from builder.js
- Updates template-specific element IDs (previewName1, previewName2, etc.)
- Renders content differently based on template layout
- Handles contact info formatting specific to each template
- Experience/education rendering with template-specific styling

---

### 3. `my-resume.php`
**Changes:**

**Lines 1-12:** Added database integration
```php
require_once('includes/middleware.php');
require_once('includes/resume.model.php');

$userId = $_SESSION['uid'] ?? '';
$resumes = get_user_resumes($userId);
```

**Line 71:** Changed "Create new" button link
```php
// Before: href="builder.php"
// After:
<a href="templates.php">
  <button>Create new</button>
</a>
```

**Lines 78-134:** Complete redesign of resume list
- Shows "No resumes yet" message if empty
- Loops through database resumes
- Each resume card displays template name and updated date
- "Edit" button links to `builder.php?resume_id=X`
- Shows template color in card preview
- Displays formatted last-edited date

**Template color mapping:**
```php
$template = get_template($resume['template_id']);
$color_scheme = is_string($template['color_scheme'])
  ? json_decode($template['color_scheme'], true)
  : $template['color_scheme'];
$primary_color = $color_scheme['primary'] ?? '#f59e0b';
```

---

## Database Schema Usage

### Tables Involved:

**`resume_templates`**
- Stores template designs
- Columns: id, name, slug, category, description, font_family, color_scheme, is_premium, is_active

**`resumes`**
- Main resume records
- Columns: id, user_id, template_id, title, slug, is_public, created_at, updated_at, deleted_at
- Foreign key: user_id (references users table)
- Foreign key: template_id (references resume_templates table)

**`resume_sections`**
- Stores personal info as JSON
- Columns: id, resume_id, section_type ('personal'), content (JSON), section_order, is_visible

**`work_experiences`**
- Individual work experience entries
- Columns: id, resume_id, job_title, company_name, location, employment_type, start_date, end_date, is_currently_working, description, order_position

**`education`**
- Individual education entries
- Columns: id, resume_id, school_name, degree, field_of_study, start_date, end_date, is_currently_studying, grade, description, order_position

**`skills`**
- Individual skill entries
- Columns: id, resume_id, skill_name, proficiency_level, endorsement_count, order_position

---

## Data Flow Diagrams

### Creating a New Resume:
```
User visits templates.php
        ↓
Selects template (click "Use Template")
        ↓
Redirects to builder.php?template_id=2
        ↓
builder.php:
  - Calls get_template(2) to get styling
  - Renders template layout #2 in preview
  - Shows form for entering content
        ↓
User fills form + clicks "Save Resume"
        ↓
saveResumeToDatabase() gathers data
        ↓
POST to resume-save-handler.php with:
  - template_id: 2
  - personal, experiences, education, skills data
        ↓
resume-save-handler.php:
  - Calls create_resume(user_id, 2, title) → returns resume_id
  - Calls update_resume_sections(resume_id, data)
  - Returns {success: true, resume_id: 123}
        ↓
JavaScript redirects to my-resume.php
        ↓
Resume appears in user's resume list
```

### Editing Existing Resume:
```
User visits my-resume.php
        ↓
Clicks "Edit" on a resume
        ↓
Redirects to builder.php?resume_id=123
        ↓
builder.php:
  - Calls get_resume(123) to load all data
  - Calls get_template(template_id) for styling
  - Renders correct template layout
        ↓
JavaScript (resume-load-handler.php):
  - Fetches data via GET ?resume_id=123
  - Returns complete resume data
  - Populates all form fields
  - Calls updatePreview() to show in preview
        ↓
User edits content + clicks "Save Resume"
        ↓
saveResumeToDatabase() gathers updated data
        ↓
POST to resume-save-handler.php with:
  - resume_id: 123
  - updated personal, experiences, education, skills
        ↓
resume-save-handler.php:
  - Calls update_resume_sections(123, data)
  - Returns {success: true, resume_id: 123}
        ↓
JavaScript redirects to my-resume.php
        ↓
Resume shows with updated content and date
```

---

## Template Designs

### Template 1: Modern Professional
- **Font:** Inter (sans-serif)
- **Layout:** Single column
- **Primary Color:** #f59e0b (Amber)
- **Key Features:**
  - Clean border-bottom header divider
  - Inline contact icons
  - Minimalist design

### Template 2: Executive Classic
- **Font:** Georgia (serif)
- **Layout:** Two-column with left sidebar
- **Primary Color:** #1e40af (Blue)
- **Key Features:**
  - Left column has contact info in box
  - Right column has main content
  - Formal, corporate feel
  - Blue underlines for section headers

### Template 3: Creative Designer
- **Font:** Playfair Display (serif)
- **Layout:** Gradient sidebar + main
- **Primary Color:** #8b5cf6 (Purple)
- **Key Features:**
  - Purple gradient background on left
  - Contact info in sidebar
  - Modern, creative aesthetic
  - Bold typography

### Template 4: Minimalist Clean
- **Font:** Inter (sans-serif)
- **Layout:** Single column with lots of whitespace
- **Primary Color:** #10b981 (Green)
- **Key Features:**
  - Maximum breathing room
  - Light gray background
  - Very subtle styling
  - Elegant simplicity

---

## API Endpoints

### POST `/includes/resume-save-handler.php`
**Purpose:** Create or update resume

**Authentication:** Requires logged-in user (checks `$_SESSION['uid']`)

**Request:** JSON POST body with resume data

**Response:**
```json
{
  "success": true/false,
  "resume_id": 123,
  "message": "Resume saved successfully" / "error message"
}
```

### GET `/includes/resume-load-handler.php?resume_id=123`
**Purpose:** Load resume data for editing

**Authentication:** Requires logged-in user

**Response:** Complete resume data in JSON format

---

## Security Measures

1. **User Authentication:** Both handlers check `$_SESSION['uid']`
2. **User Ownership:** Handlers verify resume belongs to logged-in user
3. **SQL Injection Prevention:** All queries use prepared statements with PDO
4. **Input Sanitization:** All user input validated/escaped
5. **Middleware Protection:** builder.php requires login via middleware.php

---

## Error Handling

**Resume Save Errors:**
- User not logged in → 401 Unauthorized
- Invalid JSON → 400 Bad Request
- Database error → 500 Internal Server Error
- Access denied (not user's resume) → 403 Forbidden

**Resume Load Errors:**
- User not logged in → 401 Unauthorized
- Resume not found → 404 Not Found
- Access denied → 403 Forbidden
- Database error → 500 Internal Server Error

---

## File Dependencies

```
templates.php
  ├── requires: includes/middleware.php
  ├── requires: includes/resume.model.php
  └── uses: resume.model::get_all_templates()

builder.php
  ├── requires: includes/middleware.php
  ├── requires: includes/resume.model.php
  ├── uses: resume.model::get_template()
  ├── uses: resume.model::get_resume()
  ├── calls: includes/resume-save-handler.php (API)
  ├── calls: includes/resume-load-handler.php (API)
  └── assets/JS/builder.js (for form functionality)

my-resume.php
  ├── requires: includes/middleware.php
  ├── requires: includes/resume.model.php
  ├── uses: resume.model::get_user_resumes()
  └── uses: resume.model::get_template()

includes/resume-save-handler.php
  ├── requires: includes/db.php
  ├── requires: includes/resume.model.php
  └── uses: PDO $conn global

includes/resume-load-handler.php
  ├── requires: includes/db.php
  ├── requires: includes/resume.model.php
  └── uses: PDO $conn global

includes/resume.model.php
  ├── requires: includes/db.php
  └── uses: PDO $conn global

setup-templates.php
  ├── requires: includes/db.php
  └── uses: PDO $conn global
```

---

## Testing Checklist

- [ ] Run `setup-templates.php` to populate templates
- [ ] Visit templates.php - see 8 templates with different colors
- [ ] Select a template - redirects to builder with correct layout
- [ ] Fill out resume form - preview updates in real-time
- [ ] Click "Save Resume" - saves to database
- [ ] Visit my-resume.php - resume appears in list
- [ ] Click "Edit" - builder loads with saved data
- [ ] Modify and save again - database updates
- [ ] Logout and login - resume list persists
- [ ] Check database directly - verify data structure

