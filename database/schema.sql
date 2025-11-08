-- Grand Jyothi Construction Database Schema
-- MySQL Database: constructioninnagpur

CREATE DATABASE IF NOT EXISTS constructioninnagpur CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE constructioninnagpur;

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(100) DEFAULT 'tool',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects Table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT 'placeholder.jpg',
    completed_on DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at),
    INDEX idx_completed (completed_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Team Table
CREATE TABLE IF NOT EXISTS team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    photo VARCHAR(255) DEFAULT 'avatar.jpg',
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Testimonials Table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    text TEXT NOT NULL,
    project_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    selected_plan VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at),
    INDEX idx_plan (selected_plan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Packages Table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    price_per_sqft DECIMAL(10,2) NOT NULL,
    description TEXT,
    features TEXT NOT NULL,
    notes TEXT,
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'text',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Articles Table
CREATE TABLE IF NOT EXISTS blog_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content TEXT NOT NULL,
    featured_image VARCHAR(255),
    category VARCHAR(100),
    tags VARCHAR(255),
    author VARCHAR(100) DEFAULT 'Admin',
    is_published BOOLEAN DEFAULT 1,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_category (category),
    INDEX idx_published (is_published),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password_hash) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample services
INSERT INTO services (title, description, icon) VALUES
('Residential Construction', 'We build quality homes tailored to your lifestyle and budget with attention to every detail.', 'home'),
('Commercial Projects', 'From office buildings to retail spaces, we deliver commercial projects on time and within budget.', 'briefcase'),
('Interior Design', 'Transform your space with our expert interior design and renovation services.', 'layout'),
('Project Management', 'End-to-end project management ensuring quality, timeline, and budget adherence.', 'clipboard');

-- Insert sample projects (Latest Completed Projects - Bangalore)
INSERT INTO projects (title, location, description, image, completed_on) VALUES
('Mr. Kushal Harish Residence', 'Nelamangala, Bangalore', 'G+2.5 residential construction with site dimensions 23''X45''. Modern architecture with quality finishes.', 'project1.jpg', '2025-01-15'),
('Ms. Rajeshwari Renovation', 'BTM Layout, Bangalore', 'Complete renovation project with site dimension 27''X46''. Transformed existing structure with contemporary design.', 'project2.jpg', '2025-01-10'),
('Mr. Venu MV Residence', 'Manganahalli, Bangalore', 'G+2.5 residential building with site dimension 30''X42''. Spacious layout with modern amenities.', 'project3.jpg', '2024-12-20'),
('Mr. Sampath Kumar S Residence', 'Hosa Rd, Bangalore', 'G+3.5 residential construction with site dimensions 35''X40''. Premium finishes throughout.', 'project4.jpg', '2024-11-25'),
('Mr. Christudas Residence', 'Magadi Rd, Bangalore', 'G+2.5 residential building with site dimension 27''x44''. Quality construction with attention to detail.', 'project5.jpg', '2024-09-15'),
('Mr. Suresh Residence', 'Banashankari, Bangalore', 'G+4.5 residential construction with site dimensions 30''x45''. Multi-story building with elegant design.', 'project6.jpg', '2024-08-20');

-- Insert sample team members
INSERT INTO team (name, role, photo, bio) VALUES
('Rajesh Kumar', 'Managing Director', 'team1.jpg', 'With over 20 years of experience in construction, Rajesh leads our team with vision and expertise.'),
('Priya Sharma', 'Chief Architect', 'team2.jpg', 'Award-winning architect specializing in sustainable and innovative design solutions.'),
('Amit Patel', 'Project Manager', 'team3.jpg', 'Ensures every project is delivered on time with the highest quality standards.');

-- Insert sample testimonials
INSERT INTO testimonials (client_name, text, project_id) VALUES
('Mr. Suresh Deshmukh', 'Grand Jyothi Construction exceeded our expectations. The quality of work and attention to detail is remarkable.', 1),
('Mrs. Anjali Mehta', 'Professional team, timely delivery, and excellent craftsmanship. Highly recommended!', 2),
('Mr. Vikram Singh', 'They transformed our vision into reality. The entire process was smooth and transparent.', 3);

-- Insert sample packages
INSERT INTO packages (title, price_per_sqft, description, features, notes, display_order) VALUES
('Gold Plan', 1699.00, 'Perfect for those seeking quality construction with essential features', 'Design & Drawings|Structure (Foundation, Columns, Beams)|Flooring (Vitrified Tiles)|Kitchen (Granite Platform)|Bathroom (Standard Fittings)|Electrical (Basic Wiring)|Painting (Emulsion)', 'Ideal for budget-conscious homeowners', 1),
('Platinum Plan', 1899.00, 'Enhanced features with premium materials and finishes', 'All Gold Plan Features|Premium Flooring Options|Modular Kitchen|Designer Bathroom Fittings|Advanced Electrical Setup|Premium Paint Finish|False Ceiling (Living Room)', 'Most popular choice among customers', 2),
('Diamond Plan', 2099.00, 'Luxury construction with high-end materials throughout', 'All Platinum Plan Features|Imported Flooring|Premium Modular Kitchen|Luxury Bathroom Fittings|Home Automation Ready|Texture Painting|Full False Ceiling', 'For those who demand excellence', 3),
('Diamond Plus Plan', 2499.00, 'Ultra-premium package with designer elements', 'All Diamond Plan Features|Designer Flooring|Italian Kitchen Fittings|Jacuzzi & Premium Sanitary|Smart Home Integration|Designer Lighting|Landscaping Included', 'Premium luxury living experience', 4),
('Luxury Plan', 3099.00, 'The ultimate in luxury home construction', 'All Diamond Plus Features|Exotic Materials|Fully Automated Kitchen|Spa-grade Bathrooms|Complete Home Automation|Premium Landscaping|Infinity Pool Ready', 'For the most discerning clients', 5);

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'Grand Jyothi Construction', 'text'),
('site_tagline', 'Building your vision with excellence and trust', 'text'),
('site_logo', 'logo.png', 'text'),
('site_favicon', 'favicon.ico', 'text'),
('contact_email', 'info@grandjyothi.com', 'email'),
('contact_phone', '+91 98765 43210', 'text'),
('contact_address', 'Nagpur, Maharashtra, India', 'textarea'),
('company_description', 'Building your vision with excellence and trust since 2005. We specialize in residential, commercial, and industrial construction projects across Nagpur and Maharashtra.', 'textarea'),
('facebook_url', '', 'text'),
('twitter_url', '', 'text'),
('instagram_url', '', 'text'),
('linkedin_url', '', 'text'),
('years_experience', '18', 'number'),
('projects_completed', '500', 'number'),
('happy_clients', '450', 'number');

-- Insert sample blog articles
INSERT INTO blog_articles (title, slug, excerpt, content, featured_image, category, tags, author, is_published) VALUES
('Top 10 Construction Trends in 2024', 'top-10-construction-trends-2024', 'Discover the latest trends shaping the construction industry this year, from sustainable building to smart home technology.', 'The construction industry is evolving rapidly with new technologies and methodologies. Here are the top 10 trends:\n\n1. **Sustainable Building Materials**: Eco-friendly materials are becoming the norm.\n2. **Smart Home Integration**: IoT devices and automation systems.\n3. **Modular Construction**: Faster, cost-effective building methods.\n4. **3D Printing**: Revolutionary construction techniques.\n5. **Green Building Certifications**: LEED and other standards.\n6. **Energy-Efficient Designs**: Solar panels and better insulation.\n7. **Virtual Reality Planning**: Visualize before building.\n8. **Drone Surveys**: Accurate site assessments.\n9. **Prefabricated Components**: Quality control and speed.\n10. **Wellness-Focused Designs**: Healthier living spaces.', 'blog1.jpg', 'Construction Trends', 'construction,trends,2024,technology', 'Admin', 1),

('How to Choose the Right Construction Package', 'how-to-choose-right-construction-package', 'A comprehensive guide to selecting the perfect construction package for your dream home based on budget and requirements.', 'Choosing the right construction package is crucial for your project success. Here''s what to consider:\n\n**Budget Planning**\nDetermine your total budget including land cost, construction, and contingencies. Our packages range from Gold (₹1,699/sqft) to Luxury (₹3,099/sqft).\n\n**Quality vs Cost**\nHigher packages offer premium materials and finishes. Consider long-term value over initial savings.\n\n**Customization Needs**\nSome packages allow more flexibility. Discuss your specific requirements with our team.\n\n**Timeline**\nPremium packages may have longer completion times due to specialized work.\n\n**Resale Value**\nInvesting in better packages often yields higher property values.\n\nContact us for a detailed consultation!', 'blog2.jpg', 'Home Building', 'packages,home,construction,guide', 'Admin', 1),

('Understanding Property Registration in Maharashtra', 'understanding-property-registration-maharashtra', 'Everything you need to know about property registration process, documents required, and stamp duty in Maharashtra.', 'Property registration in Maharashtra involves several steps:\n\n**Required Documents**\n- Sale Deed\n- Property Tax Receipts\n- Encumbrance Certificate\n- Identity and Address Proofs\n- PAN Cards of both parties\n\n**Stamp Duty Rates**\nMaharashtra charges 5-6% stamp duty depending on location and property type. Women buyers get 1% discount.\n\n**Registration Process**\n1. Document verification\n2. Stamp duty payment\n3. Biometric authentication\n4. Registration fee payment\n5. Document submission\n\n**Timeline**\nTypically 1-2 weeks for complete registration.\n\n**Important Tips**\n- Verify property titles thoroughly\n- Check for pending dues\n- Ensure clear ownership\n- Get legal advice\n\nOur team can guide you through the entire process!', 'blog3.jpg', 'Real Estate', 'property,registration,maharashtra,legal', 'Admin', 1),

('Vastu Tips for Your New Home', 'vastu-tips-for-new-home', 'Traditional Vastu Shastra principles to consider when planning your new home construction for positive energy and prosperity.', 'Vastu Shastra offers guidelines for harmonious living spaces:\n\n**Main Entrance**\nNorth, East, or North-East facing entrances are considered auspicious.\n\n**Living Room**\nBest in North or East direction. Keep it clutter-free and well-lit.\n\n**Kitchen**\nSouth-East corner is ideal. Cooking should face East.\n\n**Bedrooms**\nMaster bedroom in South-West. Children''s rooms in West or North-West.\n\n**Bathrooms**\nNorth-West or West direction. Never in North-East.\n\n**Pooja Room**\nNorth-East corner for spiritual activities.\n\n**Colors**\n- North: Green, light blue\n- South: Red, orange, pink\n- East: White, light blue\n- West: White, yellow\n\n**Water Elements**\nBore wells and water tanks in North-East.\n\nOur architects can incorporate Vastu principles in your home design!', 'blog4.jpg', 'Home Design', 'vastu,home,design,tips', 'Admin', 1),

('Monsoon Construction: Challenges and Solutions', 'monsoon-construction-challenges-solutions', 'Learn how to manage construction projects during monsoon season with expert tips and best practices.', 'Monsoon season presents unique challenges for construction:\n\n**Common Challenges**\n1. Material damage from rain\n2. Delayed timelines\n3. Worker safety concerns\n4. Foundation issues\n5. Material transportation problems\n\n**Solutions**\n\n**Proper Planning**\n- Schedule critical work before monsoon\n- Stock materials in advance\n- Create covered storage areas\n\n**Site Management**\n- Ensure proper drainage\n- Cover exposed areas with tarpaulins\n- Protect steel reinforcement from rust\n- Store cement in waterproof conditions\n\n**Safety Measures**\n- Provide rain gear for workers\n- Install temporary shelters\n- Ensure proper lighting\n- Maintain first aid kits\n\n**Material Protection**\n- Use waterproof covers\n- Elevate materials from ground\n- Check quality after rains\n\n**Quality Control**\n- Monitor concrete curing\n- Check for water seepage\n- Inspect electrical work\n\nWe have extensive experience in monsoon construction management!', 'blog5.jpg', 'Construction Tips', 'monsoon,construction,tips,safety', 'Admin', 1);
