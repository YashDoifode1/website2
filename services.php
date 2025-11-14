<?php
/**
 * Services Page – Grand Jyothi Construction
 * Modern design 100% aligned with blog‑detail / project‑info / projects
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Our Services | Grand Jyothi Construction';

// ------------------------------------------------------------------
// 1. Fetch all services
// ------------------------------------------------------------------
$sql = "SELECT title, description, icon, slug, cover_image, created_at
        FROM services
        ORDER BY created_at DESC";
$stmt = executeQuery($sql);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>

    <!-- Bootstrap + Icons + Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;
            --light-gray:#f8f9fa;--medium-gray:#e9ecef;
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);line-height:1.6;}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}

        /* ==== BUTTONS ==== */
        .btn-primary{
            background:var(--primary-yellow);border-color:var(--primary-yellow);
            color:var(--charcoal);font-weight:600;padding:10px 25px;border-radius:8px;
        }
        .btn-primary:hover{
            background:#e89a1f;border-color:#e89a1f;color:var(--charcoal);
        }

        /* ==== HERO ==== */
        .services-banner{
            height:500px;background:linear-gradient(rgba(26,26,26,.6),rgba(26,26,26,.6)),
                url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
                center/cover no-repeat;
            display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;
        }
        .services-banner::before{
            content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.1) 0%,transparent 70%);
        }
        .banner-title{font-size:3rem;margin-bottom:20px;line-height:1.2;}
        .banner-subtitle{font-size:1.2rem;opacity:.9;}

        /* ==== BREADCRUMB ==== */
        .breadcrumb{background:transparent;padding:0;margin-bottom:20px;}
        .breadcrumb-item a{color:rgba(255,255,255,.8);text-decoration:none;}
        .breadcrumb-item.active{color:var(--primary-yellow);}

        /* ==== CONTENT ==== */
        .services-section{padding:80px 0;}
        .section-title{
            font-size:1.8rem;margin-bottom:30px;padding-bottom:15px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;
        }

        /* ==== SERVICES GRID ==== */
        .services-grid{
            display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
            gap:30px;
        }
        .service-card{
            background:var(--white);border-radius:10px;overflow:hidden;
            box-shadow:0 5px 15px rgba(0,0,0,.05);transition:all .3s ease;
            position:relative;height:100%;
        }
        .service-card:hover{
            transform:translateY(-8px);box-shadow:0 15px 30px rgba(0,0,0,.12);
        }
        .service-cover{
            height:120px;background-size:cover;background-position:center;
            opacity:.3;
        }
        .service-icon{
            color:var(--primary-yellow);font-size:2.2rem;margin:20px 0 15px;
            display:flex;justify-content:center;
        }
        .service-title{
            font-size:1.3rem;margin-bottom:12px;color:var(--charcoal);
            text-align:center;
        }
        .service-desc{
            color:#666;font-size:.95rem;line-height:1.5;
            padding:0 20px 25px;text-align:center;
        }

        /* ==== PROCESS ==== */
        .process-grid{
            display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
            gap:25px;
        }
        .process-card{
            background:var(--white);border-radius:10px;padding:30px;
            box-shadow:0 5px 15px rgba(0,0,0,.05);text-align:center;
            transition:all .3s ease;height:100%;
        }
        .process-card:hover{
            transform:translateY(-8px);box-shadow:0 15px 30px rgba(0,0,0,.12);
        }
        .process-number{
            width:50px;height:50px;background:var(--primary-yellow);
            color:var(--charcoal);border-radius:50%;display:flex;align-items:center;
            justify-content:center;font-size:1.5rem;font-weight:700;margin:0 auto 15px;
        }

        /* ==== SERVICE AREAS ==== */
        .areas-grid{
            display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
            gap:25px;
        }
        .area-card{
            background:var(--light-gray);border-radius:10px;padding:25px;
            text-align:center;transition:all .3s ease;
        }
        .area-card:hover{
            background:var(--primary-yellow);color:var(--charcoal);
            transform:translateY(-5px);
        }
        .area-icon{
            color:var(--primary-yellow);font-size:1.8rem;margin-bottom:15px;
            transition:color .3s ease;
        }
        .area-card:hover .area-icon{color:var(--charcoal);}

        /* ==== CTA ==== */
        .cta-section{
            background:linear-gradient(135deg,var(--charcoal) 0%,#2d2d2d 100%);
            color:var(--white);padding:80px 0;text-align:center;
        }
        .cta-section h2{color:var(--white);margin-bottom:1.5rem;}
        .btn-outline-light{
            border:2px solid rgba(255,255,255,.3);color:var(--white);
            padding:12px 30px;border-radius:30px;
        }
        .btn-outline-light:hover{
            background:rgba(255,255,255,.1);border-color:var(--white);
        }

        /* ==== RESPONSIVE ==== */
        @media (max-width:768px){
            .services-banner{height:400px;padding:40px 0;}
            .banner-title{font-size:2.2rem;}
            .services-grid,.process-grid,.areas-grid{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="services-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/constructioninnagpur/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </nav>
        <h1 class="banner-title">Our Construction Services</h1>
        <p class="banner-subtitle">Comprehensive solutions from concept to completion</p>
    </div>
</section>

<!-- ====================== MAIN CONTENT ====================== -->
<section class="services-section">
    <div class="container">

        <!-- Intro -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-9 text-center">
                <p class="lead text-muted">
                    At Grand Jyothi Construction, we offer a complete range of construction services.
                    From concept to completion, we handle every aspect of your project with expertise and care.
                </p>
            </div>
        </div>

        <!-- Services Grid -->
        <?php if (empty($services)): ?>
            <div class="text-center py-5">
                <p class="text-muted">No services available at the moment. Please check back later.</p>
            </div>
        <?php else: ?>
            <div class="services-grid">
                <?php foreach ($services as $s):
                    $icon  = sanitizeOutput($s['icon'] ?? 'tools');
                    $title = sanitizeOutput($s['title']);
                    $desc  = sanitizeOutput(substr($s['description'], 0, 120)) . '...';
                    $slug  = sanitizeOutput($s['slug'] ?? strtolower(str_replace(' ', '-', $s['title'])));
                    $cover = $s['cover_image'] ? sanitizeOutput($s['cover_image']) : '';
                ?>
                    <a href="service-info.php?slug=<?= urlencode($slug) ?>" class="service-card text-decoration-none">
                        <?php if ($cover): ?>
                            <div class="service-cover" style="background-image:url('<?= $cover ?>');"></div>
                        <?php endif; ?>
                        <div class="service-icon"><i class="fas fa-<?= $icon ?>"></i></div>
                        <h3 class="service-title"><?= $title ?></h3>
                        <p class="service-desc"><?= $desc ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Our Process -->
        <div class="mt-5">
            <h2 class="section-title text-center">Our Proven Process</h2>
            <div class="process-grid">
                <?php
                $steps = [
                    ['Consultation','We begin with a detailed consultation to understand your requirements, budget, and timeline.'],
                    ['Planning & Design','Our architects and engineers create detailed plans and designs that align with your vision.'],
                    ['Execution','Skilled team begins construction using quality materials and modern techniques.'],
                    ['Quality Check','Regular inspections ensure the highest standards are maintained throughout.'],
                    ['Handover','Final walkthrough, documentation, and handover with complete satisfaction.'],
                    ['After‑Sales Support','Ongoing support and warranty services to ensure long‑term satisfaction.']
                ];
                foreach ($steps as $i => $step):
                ?>
                    <div class="process-card">
                        <div class="process-number"><?= $i+1 ?></div>
                        <h4 class="h5 fw-bold"><?= $step[0] ?></h4>
                        <p class="small"><?= $step[1] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Service Areas -->
        <div class="mt-5">
            <h2 class="section-title text-center">Our Service Areas</h2>
            <p class="text-center mb-5 lead text-muted">Serving clients across Nagpur and surrounding regions</p>
            <div class="areas-grid">
                <?php
                $areas = [
                    ['Nagpur City','Core city and surrounding areas'],
                    ['Dharampeth','Premium residential & commercial zone'],
                    ['Sadar','Central business district'],
                    ['Ramdaspeth','High‑end residential area'],
                    ['Civil Lines','Heritage and government zone'],
                    ['Sitabuldi','Commercial & market hub'],
                    ['Wardha Road','Industrial & logistics corridor'],
                    ['Kamptee','Suburban residential growth'],
                    ['Hingna','Industrial & manufacturing zone'],
                    ['Koradi','Emerging residential area'],
                    ['Manish Nagar','Modern residential locality'],
                    ['And Surrounding Areas','We serve all nearby regions']
                ];
                foreach ($areas as $a):
                ?>
                    <div class="area-card">
                        <div class="area-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <h4><?= $a[0] ?></h4>
                        <p><?= $a[1] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>

<!-- ====================== CTA ====================== -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4">Ready to Build Your Dream?</h2>
                <p class="lead mb-4">Let's discuss your vision and create something extraordinary together</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="/constructioninnagpur/contact.php" class="btn btn-primary btn-lg">
                        Get Free Consultation
                    </a>
                    <a href="/constructioninnagpur/projects.php" class="btn btn-outline-light btn-lg">
                        View Projects
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>