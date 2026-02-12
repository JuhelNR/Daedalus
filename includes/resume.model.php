<?php

// Require database connection
require_once('db.php');

// Resume model functions for database operations

/**
 * Create a new resume for a user
 */
function create_resume($user_id, $template_id, $title) {
    global $conn;

    $stmt = $conn->prepare("
        INSERT INTO resumes (user_id, template_id, title, slug, created_at, updated_at)
        VALUES (:user_id, :template_id, :title, :slug, NOW(), NOW())
    ");

    $slug = strtolower(str_replace(' ', '-', $title)) . '-' . time();

    $stmt->execute([
        'user_id' => $user_id,
        'template_id' => $template_id,
        'title' => $title,
        'slug' => $slug
    ]);

    return $conn->lastInsertId();
}

/**
 * Update resume sections (personal, experience, education, skills)
 */
function update_resume_sections($resume_id, $section_data) {
    global $conn;

    try {
        $conn->beginTransaction();

        // Get user_id for ownership verification
        $resume_check = $conn->prepare("SELECT user_id FROM resumes WHERE id = :id");
        $resume_check->execute(['id' => $resume_id]);
        $resume = $resume_check->fetch(PDO::FETCH_ASSOC);

        if (!$resume) {
            throw new Exception("Resume not found");
        }

        $user_id = $resume['user_id'];

        // Clear existing sections
        $conn->prepare("DELETE FROM resume_sections WHERE resume_id = :id")->execute(['id' => $resume_id]);
        $conn->prepare("DELETE FROM work_experiences WHERE resume_id = :id")->execute(['id' => $resume_id]);
        $conn->prepare("DELETE FROM education WHERE resume_id = :id")->execute(['id' => $resume_id]);
        $conn->prepare("DELETE FROM skills WHERE resume_id = :id")->execute(['id' => $resume_id]);

        // Save personal info to resume_sections
        if (isset($section_data['personal'])) {
            $personal_content = json_encode($section_data['personal']);
            $stmt = $conn->prepare("
                INSERT INTO resume_sections (resume_id, section_type, content, section_order, is_visible, created_at, updated_at)
                VALUES (:resume_id, :type, :content, 1, 1, NOW(), NOW())
            ");
            $stmt->execute([
                'resume_id' => $resume_id,
                'type' => 'personal',
                'content' => $personal_content
            ]);
        }

        // Save experiences
        if (isset($section_data['experiences']) && is_array($section_data['experiences'])) {
            $stmt = $conn->prepare("
                INSERT INTO work_experiences (resume_id, job_title, company_name, location, employment_type, start_date, end_date, is_currently_working, description, order_position, is_visible, created_at, updated_at)
                VALUES (:resume_id, :job_title, :company, :location, 'Full-time', :start_date, :end_date, :is_current, :description, :order_pos, 1, NOW(), NOW())
            ");

            foreach ($section_data['experiences'] as $index => $exp) {
                $stmt->execute([
                    'resume_id' => $resume_id,
                    'job_title' => $exp['title'] ?? '',
                    'company' => $exp['company'] ?? '',
                    'location' => $exp['location'] ?? '',
                    'start_date' => $exp['startDate'] ?? null,
                    'end_date' => $exp['endDate'] ?? null,
                    'is_current' => ($exp['current'] ?? false) ? 1 : 0,
                    'description' => $exp['description'] ?? '',
                    'order_pos' => $index + 1
                ]);
            }
        }

        // Save education
        if (isset($section_data['education']) && is_array($section_data['education'])) {
            $stmt = $conn->prepare("
                INSERT INTO education (resume_id, school_name, degree, field_of_study, start_date, end_date, is_currently_studying, grade, description, order_position, is_visible, created_at, updated_at)
                VALUES (:resume_id, :school, :degree, :field, :start_date, :end_date, :is_current, :grade, :description, :order_pos, 1, NOW(), NOW())
            ");

            foreach ($section_data['education'] as $index => $edu) {
                $stmt->execute([
                    'resume_id' => $resume_id,
                    'school' => $edu['school'] ?? '',
                    'degree' => $edu['degree'] ?? '',
                    'field' => $edu['field'] ?? '',
                    'start_date' => $edu['startYear'] ? $edu['startYear'] . '-01-01' : null,
                    'end_date' => $edu['endYear'] ? $edu['endYear'] . '-01-01' : null,
                    'is_current' => ($edu['current'] ?? false) ? 1 : 0,
                    'grade' => $edu['grade'] ?? '',
                    'description' => $edu['info'] ?? '',
                    'order_pos' => $index + 1
                ]);
            }
        }

        // Save skills
        if (isset($section_data['skills']) && is_array($section_data['skills'])) {
            $stmt = $conn->prepare("
                INSERT INTO skills (resume_id, skill_name, proficiency_level, order_position, is_visible, created_at, updated_at)
                VALUES (:resume_id, :skill, 'Intermediate', :order_pos, 1, NOW(), NOW())
            ");

            foreach ($section_data['skills'] as $index => $skill) {
                $stmt->execute([
                    'resume_id' => $resume_id,
                    'skill' => $skill,
                    'order_pos' => $index + 1
                ]);
            }
        }

        // Update resume updated_at timestamp
        $conn->prepare("UPDATE resumes SET updated_at = NOW() WHERE id = :id")->execute(['id' => $resume_id]);

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
}

/**
 * Get a complete resume with all sections
 */
function get_resume($resume_id) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT r.*, t.name as template_name, t.font_family, t.color_scheme, t.layout_config
        FROM resumes r
        LEFT JOIN resume_templates t ON r.template_id = t.id
        WHERE r.id = :id
    ");

    $stmt->execute(['id' => $resume_id]);
    $resume = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resume) {
        return null;
    }

    // Get personal info
    $personal_stmt = $conn->prepare("SELECT content FROM resume_sections WHERE resume_id = :id AND section_type = 'personal'");
    $personal_stmt->execute(['id' => $resume_id]);
    $personal = $personal_stmt->fetch(PDO::FETCH_ASSOC);
    $resume['personal'] = $personal ? json_decode($personal['content'], true) : [];

    // Get experiences
    $exp_stmt = $conn->prepare("
        SELECT job_title, company_name, location, start_date, end_date, is_currently_working, description
        FROM work_experiences
        WHERE resume_id = :id
        ORDER BY order_position ASC
    ");
    $exp_stmt->execute(['id' => $resume_id]);
    $resume['experiences'] = $exp_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get education
    $edu_stmt = $conn->prepare("
        SELECT school_name, degree, field_of_study, start_date, end_date, is_currently_studying, grade, description
        FROM education
        WHERE resume_id = :id
        ORDER BY order_position ASC
    ");
    $edu_stmt->execute(['id' => $resume_id]);
    $resume['education'] = $edu_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get skills
    $skill_stmt = $conn->prepare("
        SELECT skill_name
        FROM skills
        WHERE resume_id = :id
        ORDER BY order_position ASC
    ");
    $skill_stmt->execute(['id' => $resume_id]);
    $skills_rows = $skill_stmt->fetchAll(PDO::FETCH_ASSOC);
    $resume['skills'] = array_column($skills_rows, 'skill_name');

    return $resume;
}

/**
 * Get all resumes for a user
 */
function get_user_resumes($user_id) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT r.id, r.title, r.template_id, r.created_at, r.updated_at, t.name as template_name
        FROM resumes r
        LEFT JOIN resume_templates t ON r.template_id = t.id
        WHERE r.user_id = :user_id AND r.deleted_at IS NULL
        ORDER BY r.updated_at DESC
    ");

    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get template details
 */
function get_template($template_id) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT id, name, slug, description, category, font_family, color_scheme, layout_config
        FROM resume_templates
        WHERE id = :id AND is_active = 1
    ");

    $stmt->execute(['id' => $template_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get all active templates
 */
function get_all_templates() {
    global $conn;

    $stmt = $conn->prepare("
        SELECT id, name, slug, description, category, font_family, color_scheme, is_premium
        FROM resume_templates
        WHERE is_active = 1
        ORDER BY category ASC, name ASC
    ");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
