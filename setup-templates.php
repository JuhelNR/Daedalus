<?php
// This script populates the resume_templates table with templates
// Access via: http://localhost/Daedalus/setup-templates.php
// Delete after use

require_once('includes/db.php');

// Templates data with resume.io inspiration
$templates = [
    [
        'name' => 'Modern Professional',
        'slug' => 'modern-professional',
        'category' => 'Modern',
        'description' => 'Clean single column with amber accents - perfect for contemporary professionals',
        'font_family' => 'Inter, sans-serif',
        'color_scheme' => json_encode(['primary' => '#f59e0b', 'accent' => '#ffffff']),
        'is_premium' => 0
    ],
    [
        'name' => 'Executive Classic',
        'slug' => 'executive-classic',
        'category' => 'Professional',
        'description' => 'Two-column layout with left sidebar - traditional corporate style inspired by resume.io Dublin',
        'font_family' => 'Georgia, serif',
        'color_scheme' => json_encode(['primary' => '#3b82f6', 'accent' => '#ffffff']),
        'is_premium' => 0
    ],
    [
        'name' => 'Creative Designer',
        'slug' => 'creative-designer',
        'category' => 'Creative',
        'description' => 'Bold gradient sidebar with purple accents - inspired by resume.io Madrid Creative',
        'font_family' => 'Playfair Display, serif',
        'color_scheme' => json_encode(['primary' => '#8b5cf6', 'accent' => '#ffffff']),
        'is_premium' => 0
    ],
    [
        'name' => 'Minimalist Clean',
        'slug' => 'minimalist-clean',
        'category' => 'Simple',
        'description' => 'Maximum whitespace with green accents - inspired by resume.io Singapore Minimalist',
        'font_family' => 'Inter, sans-serif',
        'color_scheme' => json_encode(['primary' => '#10b981', 'accent' => '#f3f4f6']),
        'is_premium' => 0
    ],
    [
        'name' => 'Berlin Clean',
        'slug' => 'berlin-clean',
        'category' => 'Professional',
        'description' => 'Modern bold formatting with dark headers - inspired by resume.io Berlin template',
        'font_family' => 'Helvetica, Arial, sans-serif',
        'color_scheme' => json_encode(['primary' => '#2d3748', 'accent' => '#4299e1']),
        'is_premium' => 0
    ],
    [
        'name' => 'Vienna Contemporary',
        'slug' => 'vienna-contemporary',
        'category' => 'Modern',
        'description' => 'Two-column striking layout with navy theme - inspired by resume.io Vienna',
        'font_family' => 'Open Sans, sans-serif',
        'color_scheme' => json_encode(['primary' => '#003366', 'accent' => '#0066cc']),
        'is_premium' => 0
    ],
    [
        'name' => 'Madrid Creative',
        'slug' => 'madrid-creative',
        'category' => 'Creative',
        'description' => 'Bold colored section headers - inspired by resume.io Madrid template',
        'font_family' => 'Montserrat, sans-serif',
        'color_scheme' => json_encode(['primary' => '#17a2b8', 'accent' => '#20c997']),
        'is_premium' => 0
    ],
    [
        'name' => 'Tokyo Tech',
        'slug' => 'tokyo-tech',
        'category' => 'Modern',
        'description' => 'Ultra-minimal with cyan accents - inspired by resume.io Tokyo tech template',
        'font_family' => 'Roboto, sans-serif',
        'color_scheme' => json_encode(['primary' => '#1a202c', 'accent' => '#00d4ff']),
        'is_premium' => 0
    ],
];

try {
    // Insert or update templates
    $stmt = $conn->prepare("
        INSERT INTO resume_templates (name, slug, category, description, font_family, color_scheme, is_premium, is_active, created_at, updated_at)
        VALUES (:name, :slug, :category, :description, :font_family, :color_scheme, :is_premium, 1, NOW(), NOW())
        ON DUPLICATE KEY UPDATE
            description = VALUES(description),
            font_family = VALUES(font_family),
            color_scheme = VALUES(color_scheme),
            updated_at = NOW()
    ");

    $inserted = 0;
    foreach ($templates as $template) {
        try {
            $stmt->execute([
                'name' => $template['name'],
                'slug' => $template['slug'],
                'category' => $template['category'],
                'description' => $template['description'],
                'font_family' => $template['font_family'],
                'color_scheme' => $template['color_scheme'],
                'is_premium' => $template['is_premium']
            ]);
            $inserted++;
        } catch (Exception $e) {
            // Continue if one fails
            continue;
        }
    }

    echo "✅ Successfully inserted/updated " . $inserted . " templates into the database!";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

?>
