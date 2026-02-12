<?php
// Resume load handler - fetches resume data for editing
// GET endpoint for retrieving resume details

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
    $resume_id = $_GET['resume_id'] ?? null;

    if (!$resume_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Resume ID required']);
        exit;
    }

    // Get resume
    $resume = get_resume($resume_id);

    if (!$resume) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Resume not found']);
        exit;
    }

    // Verify ownership
    if ($resume['user_id'] != $user_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        exit;
    }

    // Format the response data
    $response = [
        'success' => true,
        'resume' => [
            'id' => $resume['id'],
            'title' => $resume['title'],
            'template_id' => $resume['template_id'],
            'template_name' => $resume['template_name'],
            'font_family' => $resume['font_family'],
            'color_scheme' => is_string($resume['color_scheme']) ? json_decode($resume['color_scheme'], true) : $resume['color_scheme'],
            'personal' => $resume['personal'] ?? [],
            'experiences' => array_map(function($exp) {
                return [
                    'title' => $exp['job_title'],
                    'company' => $exp['company_name'],
                    'location' => $exp['location'],
                    'startDate' => $exp['start_date'],
                    'endDate' => $exp['end_date'],
                    'current' => (bool)$exp['is_currently_working'],
                    'description' => $exp['description']
                ];
            }, $resume['experiences'] ?? []),
            'education' => array_map(function($edu) {
                return [
                    'school' => $edu['school_name'],
                    'degree' => $edu['degree'],
                    'field' => $edu['field_of_study'],
                    'startYear' => $edu['start_date'] ? date('Y', strtotime($edu['start_date'])) : null,
                    'endYear' => $edu['end_date'] ? date('Y', strtotime($edu['end_date'])) : null,
                    'current' => (bool)$edu['is_currently_studying'],
                    'grade' => $edu['grade'],
                    'info' => $edu['description']
                ];
            }, $resume['education'] ?? []),
            'skills' => $resume['skills'] ?? []
        ]
    ];

    http_response_code(200);
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

?>
