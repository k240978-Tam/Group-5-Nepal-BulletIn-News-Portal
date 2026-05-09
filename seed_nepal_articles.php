<?php
require_once 'includes/db.php';

// Image source paths (from generation)
$src_images = [
    1 => '/Users/khamchaha/.gemini/antigravity/brain/3515d6da-8580-44d4-9945-9049f114736f/nepal_politics_1777729842458.png',
    2 => '/Users/khamchaha/.gemini/antigravity/brain/3515d6da-8580-44d4-9945-9049f114736f/nepal_business_1777729950715.png',
    3 => '/Users/khamchaha/.gemini/antigravity/brain/3515d6da-8580-44d4-9945-9049f114736f/nepal_sports_1777730239073.png',
    4 => '/Users/khamchaha/.gemini/antigravity/brain/3515d6da-8580-44d4-9945-9049f114736f/nepal_tech_1777730284240.png',
    5 => '/Users/khamchaha/.gemini/antigravity/brain/3515d6da-8580-44d4-9945-9049f114736f/nepal_entertainment_1777730408770.png'
];

$dest_dir = '/Applications/XAMPP/xamppfiles/htdocs/newsportal/uploads/';
$image_urls = [];

// Ensure upload directory exists
if (!is_dir($dest_dir)) {
    mkdir($dest_dir, 0777, true);
}

// Copy images and get their URLs
foreach ($src_images as $cat_id => $src_path) {
    if (file_exists($src_path)) {
        $filename = basename($src_path);
        $dest_path = $dest_dir . $filename;
        copy($src_path, $dest_path);
        $image_urls[$cat_id] = '/newsportal/uploads/' . $filename;
    } else {
        echo "Warning: Image not found for category $cat_id at $src_path\n";
        $image_urls[$cat_id] = null;
    }
}

// Data for articles
$articles_data = [
    1 => [ // Politics
        "Parliament Reconvenes After Historic Agreement",
        "New Diplomatic Ties Established Between Nepal and France",
        "Elections Commission Announces Dates for Local By-elections",
        "Prime Minister Addresses the UN General Assembly",
        "Key Infrastructure Bill Passed Unanimously in the House",
        "Provincial Leaders Meet to Discuss Budget Allocations",
        "Anti-Corruption Commission Launches New Investigation",
        "Major Political Parties Agree on Constitutional Amendments",
        "Government Outlines New Foreign Policy Directive",
        "Youth Wing of Major Party Organizes National Convention",
        "New Environmental Regulations Proposed by the Ministry"
    ],
    2 => [ // Business
        "Kathmandu Stock Exchange Hits Record High This Quarter",
        "New Hydropower Project Secures Foreign Direct Investment",
        "Tourism Sector Rebounds Strongly With Record Tourist Arrivals",
        "Major Banking Merger Announced in Nepal's Financial Sector",
        "Government Introduces Tax Incentives for Tech Startups",
        "Inflation Rates Stabilize Amidst Global Economic Challenges",
        "Export of Nepalese Tea and Coffee Sees Significant Growth",
        "Real Estate Market in Kathmandu Shows Signs of Cooling",
        "New Free Trade Agreement Signed With Regional Partners",
        "Small Business Grants Announced to Support Local Artisans",
        "Major Airline Expands International Routes from Kathmandu"
    ],
    3 => [ // Sports
        "Nepal Cricket Team Qualifies for the T20 World Cup",
        "Historic Win for Nepalese Football Team in SAFF Championship",
        "Local Athlete Breaks National Record in Marathon",
        "New Multi-Purpose Sports Complex Opens in Pokhara",
        "National Volleyball Tournament Concludes with Thrilling Final",
        "Mountaineering: New Speed Record Set on Mount Everest",
        "Government Increases Funding for Grassroots Sports Programs",
        "Nepalese Swimmer Shines at the Asian Games",
        "Women's National Team Prepares for International Friendlies",
        "Youth Sports Festival Attracts Thousands of Participants",
        "Major Sponsorship Deal Signed for the Domestic Cricket League"
    ],
    4 => [ // Technology
        "Tech Hub 'NepTech' Opens New Incubation Center in Kathmandu",
        "Government Launches Digitization Drive for Public Services",
        "Nepalese AI Startup Secures Series A Funding from Global VCs",
        "New Cybersecurity Regulations Introduced to Protect User Data",
        "E-commerce Growth Accelerates in Rural Areas of Nepal",
        "Major Telecommunications Provider Rolls Out 5G Testing",
        "IT Outsourcing from Nepal Reaches New Heights",
        "Hackathon in Lalitpur Produces Innovative Solutions for Healthcare",
        "Tech Conference 'Nepal Digital Future' Attracts Global Speakers",
        "Drone Technology Adopted for Agricultural Monitoring in Terai",
        "New Mobile Payment App Hits One Million Users"
    ],
    5 => [ // Entertainment
        "Grand Celebration of Dashain Festival Across the Country",
        "New Nepali Film Breaks Box Office Records on Opening Weekend",
        "International Music Festival Kicks Off in Kathmandu Valley",
        "Traditional Dance Performance Enthralls Tourists at Durbar Square",
        "Popular Nepalese Singer Releases Highly Anticipated Album",
        "Art Exhibition Featuring Contemporary Nepalese Artists Opens",
        "Documentary on Himalayan Culture Wins Award at Film Festival",
        "Food Festival Highlights Diverse Culinary Heritage of Nepal",
        "Theater Group Stages Modern Adaptation of a Classic Tale",
        "Local Fashion Week Showcases Traditional and Modern Designs",
        "Comedy Show Becomes the Most Watched Program on Television"
    ]
];

// Content template
$lorem = "This is a detailed article covering the recent events surrounding this topic. The situation has been developing rapidly, and various stakeholders are closely monitoring the impact. Local authorities and experts have weighed in on the potential long-term consequences. As the story unfolds, we will continue to provide comprehensive updates and in-depth analysis. <br><br> Interviews with key figures indicate a growing consensus on the necessary next steps. The public reaction has been mixed, with some expressing strong support while others remain cautious. Furthermore, recent data suggests a significant shift in the underlying trends. This event highlights the dynamic and ever-changing landscape of Nepal's current affairs. <br><br> In conclusion, the implications of these developments are far-reaching and will likely influence future policies and decisions. We encourage our readers to stay informed and engage in the ongoing discussion.";

$stmt = $pdo->prepare("INSERT INTO articles (title, content, summary, image_url, author_id, category_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");

$author_id = 1; // Assuming user ID 1 is an admin or valid author. The DB structure says default null but it's safe to use 1.
$inserted_count = 0;

foreach ($articles_data as $cat_id => $titles) {
    $img_url = $image_urls[$cat_id];
    foreach ($titles as $index => $title) {
        $summary = "A brief summary of the recent developments regarding: " . $title;
        // Make dates slightly different by waiting a tiny bit or just let them be current timestamp
        $stmt->execute([$title, $lorem, $summary, $img_url, $author_id, $cat_id, 'published']);
        $inserted_count++;
    }
}

echo "Successfully inserted $inserted_count articles.";
?>
