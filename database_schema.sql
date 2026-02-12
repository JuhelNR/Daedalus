-- ============================================================================
-- DAEDALUS RESUME BUILDER - DATABASE SCHEMA
-- ============================================================================

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    location VARCHAR(255),
    professional_summary LONGTEXT,
    website_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    github_url VARCHAR(255),
    subscription_plan ENUM('free', 'pro', 'premium') DEFAULT 'free',
    subscription_start_date DATETIME,
    subscription_end_date DATETIME,
    is_email_verified TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at),
    INDEX idx_subscription_plan (subscription_plan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resume Templates Table
CREATE TABLE IF NOT EXISTS resume_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) UNIQUE NOT NULL,
    description LONGTEXT,
    category VARCHAR(50) DEFAULT 'professional',
    is_premium TINYINT(1) DEFAULT 0,
    layout_config LONGTEXT,
    color_scheme LONGTEXT,
    font_family VARCHAR(100) DEFAULT 'Inter',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_is_premium (is_premium),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resumes Table
CREATE TABLE IF NOT EXISTS resumes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    template_id INT,
    slug VARCHAR(255) UNIQUE NOT NULL,
    is_public TINYINT(1) DEFAULT 0,
    share_token VARCHAR(100) UNIQUE,
    can_edit_by_link TINYINT(1) DEFAULT 0,
    view_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES resume_templates(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_is_public (is_public),
    INDEX idx_share_token (share_token),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resume Sections Table (stores actual content)
CREATE TABLE IF NOT EXISTS resume_sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    section_type VARCHAR(50) NOT NULL,
    section_order INT DEFAULT 0,
    is_visible TINYINT(1) DEFAULT 1,
    content LONGTEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_section_type (section_type),
    UNIQUE KEY unique_section_per_resume (resume_id, section_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Work Experience Table
CREATE TABLE IF NOT EXISTS work_experiences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    job_title VARCHAR(150) NOT NULL,
    company_name VARCHAR(150) NOT NULL,
    location VARCHAR(200),
    employment_type VARCHAR(50) DEFAULT 'full-time',
    start_date DATE,
    end_date DATE,
    is_currently_working TINYINT(1) DEFAULT 0,
    description LONGTEXT,
    is_visible TINYINT(1) DEFAULT 1,
    order_position INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_order_position (order_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Education Table
CREATE TABLE IF NOT EXISTS education (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    school_name VARCHAR(200) NOT NULL,
    degree VARCHAR(150) NOT NULL,
    field_of_study VARCHAR(150),
    start_date DATE,
    end_date DATE,
    is_currently_studying TINYINT(1) DEFAULT 0,
    grade VARCHAR(10),
    activities_societies VARCHAR(500),
    description LONGTEXT,
    is_visible TINYINT(1) DEFAULT 1,
    order_position INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_order_position (order_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Skills Table
CREATE TABLE IF NOT EXISTS skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    proficiency_level VARCHAR(50) DEFAULT 'intermediate',
    endorsement_count INT DEFAULT 0,
    is_visible TINYINT(1) DEFAULT 1,
    order_position INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_skill_name (skill_name),
    INDEX idx_order_position (order_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects Table
CREATE TABLE IF NOT EXISTS projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    project_title VARCHAR(200) NOT NULL,
    description LONGTEXT,
    project_url VARCHAR(255),
    start_date DATE,
    end_date DATE,
    is_ongoing TINYINT(1) DEFAULT 0,
    technologies_used VARCHAR(500),
    is_visible TINYINT(1) DEFAULT 1,
    order_position INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_order_position (order_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Certifications Table
CREATE TABLE IF NOT EXISTS certifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    certification_name VARCHAR(200) NOT NULL,
    issuing_organization VARCHAR(150) NOT NULL,
    issue_date DATE,
    expiration_date DATE,
    does_not_expire TINYINT(1) DEFAULT 0,
    credential_id VARCHAR(100),
    credential_url VARCHAR(255),
    is_visible TINYINT(1) DEFAULT 1,
    order_position INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_order_position (order_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Languages Table
CREATE TABLE IF NOT EXISTS languages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    language_name VARCHAR(50) NOT NULL,
    proficiency_level VARCHAR(50) DEFAULT 'limited_working',
    is_visible TINYINT(1) DEFAULT 1,
    order_position INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_order_position (order_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volunteer Experience Table
CREATE TABLE IF NOT EXISTS volunteer_experience (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    organization_name VARCHAR(150) NOT NULL,
    role VARCHAR(150) NOT NULL,
    cause VARCHAR(100),
    start_date DATE,
    end_date DATE,
    is_currently_volunteering TINYINT(1) DEFAULT 0,
    description LONGTEXT,
    is_visible TINYINT(1) DEFAULT 1,
    order_position INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_order_position (order_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resume Analytics Table
CREATE TABLE IF NOT EXISTS resume_analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    view_date DATE,
    view_count INT DEFAULT 0,
    downloaded_count INT DEFAULT 0,
    shared_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_view_date (view_date),
    UNIQUE KEY unique_resume_date (resume_id, view_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subscription Plans Table
CREATE TABLE IF NOT EXISTS subscription_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_name VARCHAR(50) NOT NULL,
    description LONGTEXT,
    price DECIMAL(10, 2) NOT NULL,
    billing_cycle VARCHAR(50) DEFAULT 'monthly',
    max_resumes INT,
    max_templates INT,
    ai_writing_assistant TINYINT(1) DEFAULT 0,
    custom_domains TINYINT(1) DEFAULT 0,
    advanced_analytics TINYINT(1) DEFAULT 0,
    priority_support TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_plan_name (plan_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Subscriptions Table
CREATE TABLE IF NOT EXISTS user_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    subscription_plan_id INT NOT NULL,
    subscription_code VARCHAR(100) UNIQUE,
    status VARCHAR(50) DEFAULT 'active',
    start_date DATETIME,
    end_date DATETIME,
    auto_renew TINYINT(1) DEFAULT 1,
    payment_method VARCHAR(50),
    stripe_customer_id VARCHAR(100),
    stripe_subscription_id VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plans(id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Log Table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_value LONGTEXT,
    new_value LONGTEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Share Links Table
CREATE TABLE IF NOT EXISTS share_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    share_token VARCHAR(100) UNIQUE NOT NULL,
    can_view TINYINT(1) DEFAULT 1,
    can_download TINYINT(1) DEFAULT 0,
    can_edit TINYINT(1) DEFAULT 0,
    expiration_date DATETIME,
    view_count INT DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id),
    INDEX idx_share_token (share_token),
    INDEX idx_expiration_date (expiration_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reset Password Tokens Table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PERFORMANCE OPTIMIZATION INDEXES
-- ============================================================================

ALTER TABLE resumes ADD INDEX idx_user_id_created_at (user_id, created_at);
ALTER TABLE work_experiences ADD INDEX idx_resume_id_order (resume_id, order_position);
ALTER TABLE education ADD INDEX idx_resume_id_order (resume_id, order_position);
ALTER TABLE skills ADD INDEX idx_resume_id_order (resume_id, order_position);

-- ============================================================================
-- SAMPLE DATA (Optional - Remove in production)
-- ============================================================================

INSERT INTO subscription_plans (plan_name, description, price, billing_cycle, max_resumes, max_templates, ai_writing_assistant, custom_domains, advanced_analytics, priority_support, is_active) VALUES
('Free', 'Perfect for getting started', 0.00, 'monthly', 3, 5, 0, 0, 0, 0, 1);

INSERT INTO subscription_plans (plan_name, description, price, billing_cycle, max_resumes, max_templates, ai_writing_assistant, custom_domains, advanced_analytics, priority_support, is_active) VALUES
('Pro', 'For serious job seekers', 9.99, 'monthly', 20, 50, 1, 0, 1, 0, 1);

INSERT INTO subscription_plans (plan_name, description, price, billing_cycle, max_resumes, max_templates, ai_writing_assistant, custom_domains, advanced_analytics, priority_support, is_active) VALUES
('Premium', 'Full-featured resume builder', 19.99, 'monthly', 100, 100, 1, 1, 1, 1, 1);

INSERT INTO resume_templates (name, slug, description, category, is_premium, layout_config, color_scheme, is_active) VALUES
('Modern Professional', 'modern-professional', 'Clean and modern design perfect for tech professionals', 'modern', 0, '{}', '{"primary":"#f59e0b","secondary":"#1f2937"}', 1);

INSERT INTO resume_templates (name, slug, description, category, is_premium, layout_config, color_scheme, is_active) VALUES
('Executive Classic', 'executive-classic', 'Professional and elegant design for executives', 'professional', 0, '{}', '{"primary":"#3b82f6","secondary":"#1f2937"}', 1);

INSERT INTO resume_templates (name, slug, description, category, is_premium, layout_config, color_scheme, is_active) VALUES
('Creative Designer', 'creative-designer', 'Bold and creative design for designers and artists', 'creative', 1, '{}', '{"primary":"#8b5cf6","secondary":"#1f2937"}', 1);

INSERT INTO resume_templates (name, slug, description, category, is_premium, layout_config, color_scheme, is_active) VALUES
('Minimalist Clean', 'minimalist-clean', 'Simple and clean minimalist design', 'simple', 0, '{}', '{"primary":"#10b981","secondary":"#1f2937"}', 1);
