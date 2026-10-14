<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            padding: 90px 0 70px 0;
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
        }
        .feature-card {
            border: 1px solid #edf2f7;
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        }
        .feature-icon {
            font-size: 2.2rem;
            margin-bottom: 12px;
        }
    </style>
</head>
<body class="bg-white text-dark">

    <!-- 1. NAVIGATION -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary fs-4" href="<?= BASEURL ?>">
                <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= BASEURL ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active px-3 fw-semibold text-primary" href="<?= BASEURL ?>example">Example</a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="btn btn-outline-primary btn-sm px-3" href="#features">Explore Features</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 2. HERO SECTION -->
    <header class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-3 fw-semibold">
                        Simple Landing Page Example
                    </span>
                    <h1 class="display-4 fw-bold lh-sm mb-3">
                        <?= htmlspecialchars($headline, ENT_QUOTES, 'UTF-8') ?>
                    </h1>
                    <p class="lead text-secondary mb-4 fs-5">
                        <?= htmlspecialchars($subheadline, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#features" class="btn btn-primary btn-lg px-4 shadow-sm">
                            Learn More
                        </a>
                        <a href="<?= BASEURL ?>" class="btn btn-outline-secondary btn-lg px-4">
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 3. FEATURES GRID -->
    <section id="features" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Key Architectural Highlights</h2>
                <p class="text-secondary">Explore what's happening under the hood of this page.</p>
            </div>
            <div class="row g-4">
                <?php foreach ($features as $feature): ?>
                    <div class="col-md-4">
                        <div class="card h-100 p-4 feature-card shadow-none">
                            <div class="card-body">
                                <div class="feature-icon"><?= $feature['icon'] ?></div>
                                <h4 class="card-title fw-bold fs-5 mb-2">
                                    <?= htmlspecialchars($feature['title'], ENT_QUOTES, 'UTF-8') ?>
                                </h4>
                                <p class="card-text text-secondary">
                                    <?= htmlspecialchars($feature['desc'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 4. CALL TO ACTION BANNER -->
    <section class="py-5 bg-light border-top border-bottom">
        <div class="container text-center py-4">
            <h2 class="fw-bold mb-3">Ready to Customize?</h2>
            <p class="text-secondary mb-4 mx-auto" style="max-width: 600px;">
                You can edit this template in <code>app/view/example.php</code> and customize data in <code>app/controller/example/example.php</code>.
            </p>
            <a href="<?= BASEURL ?>" class="btn btn-dark btn-lg px-4">
                Visit Default Home
            </a>
        </div>
    </section>

    <!-- 5. FOOTER -->
    <footer class="py-4 bg-white text-center text-secondary small">
        <div class="container">
            <p class="mb-0">
                &copy; <?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?>. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

