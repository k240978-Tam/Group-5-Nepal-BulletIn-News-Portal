<?php
require_once __DIR__ . '/../../includes/db.php';

$users = [1, 8, 9, 10, 11, 12];
$categories = [1, 2, 3, 4, 5]; // Politics, Business, Sports, Technology, Entertainment

$news_templates = [
    1 => [ // Politics
        ['title' => 'New Election Reform Bill Tabled in Parliament', 'summary' => 'The government has introduced a major election reform bill aiming at increasing digital transparency.'],
        ['title' => 'Diplomatic Talks Strengthen Regional Ties', 'summary' => 'High-level meetings between regional leaders concluded today with several key agreements signed.'],
        ['title' => 'Local Governance Budget Increased by 15%', 'summary' => 'The finance ministry announced a significant boost for local bodies to improve infrastructure.'],
        ['title' => 'Constitutional Amendment Debate Heats Up', 'summary' => 'Political parties are divided over proposed changes to the provincial power structures.'],
    ],
    2 => [ // Business
        ['title' => 'Nepal Stock Exchange Hits New Record High', 'summary' => 'Investor confidence remains strong as NEPSE crosses the 3000-point psychological barrier.'],
        ['title' => 'Hydropower Project Secures International Funding', 'summary' => 'A major 500MW project in Eastern Nepal has received a boost from international investors.'],
        ['title' => 'Tourism Sector Sees 40% Growth This Season', 'summary' => 'Arrival numbers have exceeded expectations, bringing hope to the struggling hospitality industry.'],
        ['title' => 'New Startup Incubator Launched in Kathmandu', 'summary' => 'The initiative aims to support 50 tech startups with seed funding and mentorship over the next year.'],
    ],
    3 => [ // Sports
        ['title' => 'National Cricket Team Qualifies for Global Tournament', 'summary' => 'A historic win against regional rivals has secured Nepal a spot on the world stage.'],
        ['title' => 'Football League Resumes with Full Stadiums', 'summary' => 'The domestic league kicked off today with a thrilling 3-2 victory in the opening match.'],
        ['title' => 'Martial Arts Academy Produces Three Gold Medalists', 'summary' => 'Young athletes from Nepal dominated the international open championship held in Tokyo.'],
        ['title' => 'Marathon Event Attracts 5000 Participants', 'summary' => 'The annual Kathmandu marathon saw a record turnout this weekend with runners from 10 countries.'],
    ],
    4 => [ // Technology
        ['title' => 'Nepal Launches First Dedicated Tech Hub', 'summary' => 'The government and private sector partnered to create a state-of-the-art facility for IT companies.'],
        ['title' => 'Mobile Internet Speeds Set to Triple by End of Year', 'summary' => 'Major telecom providers announced the rollout of enhanced 5G infrastructure across key cities.'],
        ['title' => 'AI Adoption Rises Among Local Businesses', 'summary' => 'A recent survey shows that 30% of Nepali enterprises have started using AI for customer service.'],
        ['title' => 'Cybersecurity Awareness Program Reaches 100 Schools', 'summary' => 'The initiative aims to educate students about online safety and digital literacy.'],
    ],
    5 => [ // Entertainment
        ['title' => 'Local Film Wins Best Director at International Festival', 'summary' => 'The gritty drama about rural life in Nepal has captured the hearts of global critics.'],
        ['title' => 'Music Festival Returns to Pokhara Lakeside', 'summary' => 'Thousands of fans gathered for the 3-day event featuring local and international bands.'],
        ['title' => 'New Streaming Service Focused on Nepali Content', 'summary' => 'A local tech firm has launched a platform dedicated to hosting high-quality Nepali movies and series.'],
        ['title' => 'Cultural Heritage Exhibition Opens in Patan', 'summary' => 'The display features rare artifacts and traditional art forms that were previously unseen.'],
    ]
];

echo "Starting seeding...\n";

foreach ($users as $user_id) {
    echo "Seeding for user ID: $user_id\n";
    // For each user, pick 4 articles from different categories
    for ($i = 0; $i < 4; $i++) {
        $cat_id = $categories[($user_id + $i) % count($categories)];
        $templates = $news_templates[$cat_id];
        $template = $templates[$i % count($templates)];
        
        $title = $template['title'] . " (Update " . rand(1, 100) . ")";
        $summary = $template['summary'];
        $content = "Full content for: " . $title . ". This article was professionally written by our journalist to provide in-depth coverage of the situation. " . str_repeat("Additional detailed analysis and reporting on the ground. ", 10);
        $image_url = "https://images.unsplash.com/photo-" . (1500000000000 + rand(100000000, 999999999)) . "?auto=format&fit=crop&q=80&w=800";
        
        $stmt = $pdo->prepare("INSERT INTO articles (title, content, summary, image_url, author_id, category_id, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, 'published', DATE_SUB(NOW(), INTERVAL ? HOUR))");
        $stmt->execute([
            $title, 
            $content, 
            $summary, 
            $image_url, 
            $user_id, 
            $cat_id,
            rand(1, 72) // Randomly distributed over the last 3 days
        ]);
    }
}

echo "Seeding completed successfully!\n";
