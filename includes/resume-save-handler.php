<?php
// Resume save handler - saves resume data to database
// POST endpoint for creating and updating resumes

require_once('db.php');
require_once('resume.model.php');

header('Content-Type: application/json');

// Check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $user_id = $_SESSION['uid'];

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }

    // Determine if creating new or updating existing
    $resume_id = $data['resume_id'] ?? null;
    $template_id = $data['template_id'] ?? null;
    $title = $data['title'] ?? 'Untitled Resume';

    if ($resume_id) {
        // UPDATING EXISTING RESUME
        // Verify ownership
        $check = $conn->prepare("SELECT user_id FROM resumes WHERE id = :id");
        $check->execute(['id' => $resume_id]);
        $resume = $check->fetch(PDO::FETCH_ASSOC);

        if (!$resume || $resume['user_id'] != $user_id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            exit;
        }

        // Update title if provided
        if (isset($data['title'])) {
            $update = $conn->prepare("UPDATE resumes SET title = :title WHERE id = :id");
            $update->execute(['title' => $title, 'id' => $resume_id]);
        }
    } else {
        // CREATING NEW RESUME
        if (!$template_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Template ID required for new resume']);
            exit;
        }

        // Create new resume
        $resume_id = create_resume($user_id, $template_id, $title);
    }

    // Prepare section data
    $section_data = [
        'personal' => $data['personal'] ?? [],
        'experiences' => $data['experiences'] ?? [],
        'education' => $data['education'] ?? [],
        'skills' => $data['skills'] ?? []
    ];

    // Save all sections to database
    update_resume_sections($resume_id, $section_data);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'resume_id' => $resume_id,
        'message' => 'Resume saved successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

?>
