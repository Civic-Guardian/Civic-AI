<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vibe2Ship Hackathon Showcase - NagarRakshak</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --hackathon-primary: #8B5CF6;
            --hackathon-primary-hover: #7C3AED;
            --hackathon-dark: #0B0F19;
            --hackathon-slate: #64748B;
            --hackathon-bg: #0F172A;
            --hackathon-card: #1E293B;
            --hackathon-border: #334155;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--hackathon-bg);
            color: #F8FAFC;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        /* Ambient Glow backgrounds */
        .ambient-glow-1 {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, rgba(139, 92, 246, 0) 70%);
            top: -100px;
            left: -100px;
            z-index: 1;
        }

        .ambient-glow-2 {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0) 70%);
            bottom: -150px;
            right: -100px;
            z-index: 1;
        }

        .main-container {
            z-index: 2;
            width: 100%;
            max-width: 850px;
            padding: 2rem 1rem;
        }

        .showcase-card {
            background-color: var(--hackathon-card);
            border: 1px solid var(--hackathon-border);
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.35);
            padding: 3rem;
            backdrop-filter: blur(10px);
        }

        .gradient-text {
            background: linear-gradient(135deg, #A78BFA 0%, #10B981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .hackathon-badge {
            background-color: rgba(139, 92, 246, 0.15);
            border: 1px solid rgba(139, 92, 246, 0.3);
            color: #C084FC;
            font-weight: 700;
            letter-spacing: 0.1em;
            font-size: 0.75rem;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .hackathon-btn {
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            width: 100%;
        }

        .btn-glow-purple {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            color: #FFFFFF;
            border: none;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .btn-glow-purple:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.45);
            color: #FFFFFF;
        }

        .btn-glow-emerald {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: #FFFFFF;
            border: none;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-glow-emerald:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
            color: #FFFFFF;
        }

        .btn-outline-custom {
            border: 1px solid var(--hackathon-border);
            color: #E2E8F0;
            background-color: rgba(255, 255, 255, 0.02);
        }

        .btn-outline-custom:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #FFFFFF;
            border-color: #64748B;
            transform: translateY(-2px);
        }

        .link-desc {
            font-size: 0.8rem;
            color: var(--hackathon-slate);
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>

    <!-- Radial Glowing Ambience -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="main-container">
        <div class="showcase-card text-center">
            <!-- Hackathon Badge -->
            <div class="hackathon-badge"><i class="fa-solid fa-trophy me-1"></i> VIBE2SHIP HACKATHON ENTRY</div>
            
            <!-- Project Header -->
            <h1 class="display-5 fw-extrabold mb-2">Nagar<span class="gradient-text">Rakshak</span></h1>
            <p class="fs-5 text-secondary mb-5" style="max-width: 600px; margin: 0 auto;">An AI-Powered Civic Guardian Platform. Detecting, matching, merging, and resolving infrastructure hazards dynamically to protect citizens and optimize city operations.</p>
            
            <!-- Showcase Links Row -->
            <div class="row g-4 text-start">
                <!-- 1. Live Deployment -->
                <div class="col-md-4">
                    <div class="p-3 border border-secondary border-opacity-10 rounded-4 h-100 bg-black bg-opacity-20 d-flex flex-column justify-content-between">
                        <div>
                            <div class="fs-3 mb-2">🌐</div>
                            <h5 class="fw-bold mb-2">Live Application</h5>
                            <p class="text-secondary small mb-3">Explore the active Administrative Dashboard & Municipal Control centers.</p>
                        </div>
                        <div>
                            <a href="{{ url('/admin/dashboard') }}" class="hackathon-btn btn-glow-purple" target="_blank">
                                <i class="fa-solid fa-window-restore"></i> Open App
                            </a>
                            <div class="link-desc text-center">Admin Portal Live Link</div>
                        </div>
                    </div>
                </div>

                <!-- 2. Android Binary -->
                <div class="col-md-4">
                    <div class="p-3 border border-secondary border-opacity-10 rounded-4 h-100 bg-black bg-opacity-20 d-flex flex-column justify-content-between">
                        <div>
                            <div class="fs-3 mb-2">🤖</div>
                            <h5 class="fw-bold mb-2">Android Client</h5>
                            <p class="text-secondary small mb-3">Download the citizen mobile app APK with active navigation and AI hazard scanner.</p>
                        </div>
                        <div>
                            <a href="https://github.com/Civic-Guardian/Civic-AI/releases/download/v1.0.0-demo/nagarrakshak.apk" class="hackathon-btn btn-glow-emerald" target="_blank">
                                <i class="fa-solid fa-download"></i> Download APK
                            </a>
                            <div class="link-desc text-center">NagarRakshak Android Binary</div>
                        </div>
                    </div>
                </div>

                <!-- 3. Research Report -->
                <div class="col-md-4">
                    <div class="p-3 border border-secondary border-opacity-10 rounded-4 h-100 bg-black bg-opacity-20 d-flex flex-column justify-content-between">
                        <div>
                            <div class="fs-3 mb-2">📄</div>
                            <h5 class="fw-bold mb-2">Research Report</h5>
                            <p class="text-secondary small mb-3">Access the comprehensive research report on civic AI matching & hazard analytics.</p>
                        </div>
                        <div>
                            <a href="https://docs.google.com/document/d/1vibe2ship-hackathon-research-report/edit?usp=sharing" class="hackathon-btn btn-outline-custom" target="_blank">
                                <i class="fa-solid fa-file-lines"></i> View Report
                            </a>
                            <div class="link-desc text-center">Google Docs Research Report</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Details -->
            <div class="mt-5 border-top border-secondary border-opacity-10 pt-4 d-flex justify-content-between align-items-center text-secondary small">
                <div>
                    <i class="fa-solid fa-code-merge me-1"></i> Version 1.2.0-stable
                </div>
                <div>
                    <i class="fa-regular fa-heart text-danger me-1"></i> Crafted for Smart Cities Kota
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
