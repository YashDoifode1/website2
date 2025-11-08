-- Migration: Add blog_articles table
-- Run this if you already have an existing database

-- Create blog articles table
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

-- Insert sample blog articles
INSERT INTO blog_articles (title, slug, excerpt, content, featured_image, category, tags, author, is_published) VALUES
('Top 10 Construction Trends in 2024', 'top-10-construction-trends-2024', 'Discover the latest trends shaping the construction industry this year, from sustainable building to smart home technology.', 'The construction industry is evolving rapidly with new technologies and methodologies. Here are the top 10 trends:\n\n1. **Sustainable Building Materials**: Eco-friendly materials are becoming the norm.\n2. **Smart Home Integration**: IoT devices and automation systems.\n3. **Modular Construction**: Faster, cost-effective building methods.\n4. **3D Printing**: Revolutionary construction techniques.\n5. **Green Building Certifications**: LEED and other standards.\n6. **Energy-Efficient Designs**: Solar panels and better insulation.\n7. **Virtual Reality Planning**: Visualize before building.\n8. **Drone Surveys**: Accurate site assessments.\n9. **Prefabricated Components**: Quality control and speed.\n10. **Wellness-Focused Designs**: Healthier living spaces.', 'blog1.jpg', 'Construction Trends', 'construction,trends,2024,technology', 'Admin', 1),

('How to Choose the Right Construction Package', 'how-to-choose-right-construction-package', 'A comprehensive guide to selecting the perfect construction package for your dream home based on budget and requirements.', 'Choosing the right construction package is crucial for your project success. Here''s what to consider:\n\n**Budget Planning**\nDetermine your total budget including land cost, construction, and contingencies. Our packages range from Gold (₹1,699/sqft) to Luxury (₹3,099/sqft).\n\n**Quality vs Cost**\nHigher packages offer premium materials and finishes. Consider long-term value over initial savings.\n\n**Customization Needs**\nSome packages allow more flexibility. Discuss your specific requirements with our team.\n\n**Timeline**\nPremium packages may have longer completion times due to specialized work.\n\n**Resale Value**\nInvesting in better packages often yields higher property values.\n\nContact us for a detailed consultation!', 'blog2.jpg', 'Home Building', 'packages,home,construction,guide', 'Admin', 1),

('Understanding Property Registration in Maharashtra', 'understanding-property-registration-maharashtra', 'Everything you need to know about property registration process, documents required, and stamp duty in Maharashtra.', 'Property registration in Maharashtra involves several steps:\n\n**Required Documents**\n- Sale Deed\n- Property Tax Receipts\n- Encumbrance Certificate\n- Identity and Address Proofs\n- PAN Cards of both parties\n\n**Stamp Duty Rates**\nMaharashtra charges 5-6% stamp duty depending on location and property type. Women buyers get 1% discount.\n\n**Registration Process**\n1. Document verification\n2. Stamp duty payment\n3. Biometric authentication\n4. Registration fee payment\n5. Document submission\n\n**Timeline**\nTypically 1-2 weeks for complete registration.\n\n**Important Tips**\n- Verify property titles thoroughly\n- Check for pending dues\n- Ensure clear ownership\n- Get legal advice\n\nOur team can guide you through the entire process!', 'blog3.jpg', 'Real Estate', 'property,registration,maharashtra,legal', 'Admin', 1),

('Vastu Tips for Your New Home', 'vastu-tips-for-new-home', 'Traditional Vastu Shastra principles to consider when planning your new home construction for positive energy and prosperity.', 'Vastu Shastra offers guidelines for harmonious living spaces:\n\n**Main Entrance**\nNorth, East, or North-East facing entrances are considered auspicious.\n\n**Living Room**\nBest in North or East direction. Keep it clutter-free and well-lit.\n\n**Kitchen**\nSouth-East corner is ideal. Cooking should face East.\n\n**Bedrooms**\nMaster bedroom in South-West. Children''s rooms in West or North-West.\n\n**Bathrooms**\nNorth-West or West direction. Never in North-East.\n\n**Pooja Room**\nNorth-East corner for spiritual activities.\n\n**Colors**\n- North: Green, light blue\n- South: Red, orange, pink\n- East: White, light blue\n- West: White, yellow\n\n**Water Elements**\nBore wells and water tanks in North-East.\n\nOur architects can incorporate Vastu principles in your home design!', 'blog4.jpg', 'Home Design', 'vastu,home,design,tips', 'Admin', 1),

('Monsoon Construction: Challenges and Solutions', 'monsoon-construction-challenges-solutions', 'Learn how to manage construction projects during monsoon season with expert tips and best practices.', 'Monsoon season presents unique challenges for construction:\n\n**Common Challenges**\n1. Material damage from rain\n2. Delayed timelines\n3. Worker safety concerns\n4. Foundation issues\n5. Material transportation problems\n\n**Solutions**\n\n**Proper Planning**\n- Schedule critical work before monsoon\n- Stock materials in advance\n- Create covered storage areas\n\n**Site Management**\n- Ensure proper drainage\n- Cover exposed areas with tarpaulins\n- Protect steel reinforcement from rust\n- Store cement in waterproof conditions\n\n**Safety Measures**\n- Provide rain gear for workers\n- Install temporary shelters\n- Ensure proper lighting\n- Maintain first aid kits\n\n**Material Protection**\n- Use waterproof covers\n- Elevate materials from ground\n- Check quality after rains\n\n**Quality Control**\n- Monitor concrete curing\n- Check for water seepage\n- Inspect electrical work\n\nWe have extensive experience in monsoon construction management!', 'blog5.jpg', 'Construction Tips', 'monsoon,construction,tips,safety', 'Admin', 1);

-- Verify the table
DESCRIBE blog_articles;

-- Check sample data
SELECT id, title, category, is_published FROM blog_articles;
