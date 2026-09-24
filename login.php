<?php
require_once(__DIR__ . "/include/config.php");

// If already authenticated, redirect immediately
if (!empty($_SESSION['user_id'])) {
    $redirect_target = !empty($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : (_BASEURL . 'index.php');
    header("Location: " . $redirect_target);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login &bull; BunnyBoss | Premium Fashion & Footwear</title>
    <meta name="keywords" content="BunnyBoss Login, Footwear, Fashion, Online Store">
    <meta name="description" content="Sign in to your BunnyBoss account with fast and secure Mobile OTP verification.">
    <meta name="author" content="BunnyBoss">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= _BASEURL ?>assets/images/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= _BASEURL ?>assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= _BASEURL ?>assets/images/icons/favicon-16x16.png">
    <meta name="theme-color" content="#111827">

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= _BASEURL ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= _BASEURL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= _BASEURL ?>assets/css/demos/demo-7.css">

    <style>
        :root {
            --bb-primary: #ff6b35;
            --bb-primary-hover: #e85a24;
            --bb-primary-glow: rgba(255, 107, 53, 0.28);
            --bb-accent: #fcb941;
            --bb-dark: #0f172a;
            --bb-dark-card: #1e293b;
            --bb-border: #e2e8f0;
            --bb-text: #1e293b;
            --bb-muted: #64748b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            color: var(--bb-text);
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Hero Wrapper */
        .login-hero-wrapper {
            position: relative;
            min-height: calc(100vh - 180px);
            padding: 50px 15px 70px;
            background: 
                radial-gradient(circle at 10% 15%, rgba(252, 185, 65, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 90% 85%, rgba(255, 107, 53, 0.1) 0%, transparent 50%),
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Floating background decoration */
        .login-hero-wrapper::before {
            content: '';
            position: absolute;
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.08), rgba(252, 185, 65, 0.05));
            border-radius: 50%;
            top: 5%;
            left: 5%;
            filter: blur(60px);
            pointer-events: none;
        }

        .login-hero-wrapper::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, rgba(252, 185, 65, 0.08), rgba(255, 107, 53, 0.05));
            border-radius: 50%;
            bottom: 5%;
            right: 5%;
            filter: blur(60px);
            pointer-events: none;
        }

        /* Main Container Card */
        .auth-container-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1080px;
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.8);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
        }

        /* Left Showcase Panel (Desktop) */
        .auth-showcase-panel {
            flex: 1 1 45%;
            min-width: 320px;
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
            padding: 50px 40px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .auth-showcase-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('<?= _BASEURL ?>assets/images/backgrounds/login-bg.jpg') center/cover no-repeat;
            opacity: 0.14;
            mix-blend-mode: overlay;
        }

        .auth-showcase-panel::after {
            content: '';
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 107, 53, 0.4) 0%, transparent 70%);
            top: -50px;
            right: -50px;
            filter: blur(40px);
        }

        .showcase-content {
            position: relative;
            z-index: 2;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 28px;
            color: #fed7aa;
        }

        .brand-badge img {
            height: 22px;
            width: auto;
        }

        .showcase-title {
            font-size: 34px;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .showcase-title span {
            background: linear-gradient(135deg, #ffd54f 0%, #ff6b35 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .showcase-desc {
            font-size: 15px;
            line-height: 1.6;
            color: #cbd5e1;
            margin-bottom: 35px;
        }

        /* Feature List */
        .showcase-features {
            list-style: none;
            padding: 0;
            margin: 0 0 35px 0;
        }

        .showcase-feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
            font-size: 14.5px;
            color: #f1f5f9;
        }

        .showcase-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 107, 53, 0.15);
            border: 1px solid rgba(255, 107, 53, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff8c5a;
            flex-shrink: 0;
        }

        /* Trust Card on bottom */
        .showcase-trust-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .trust-avatar-group {
            display: flex;
            margin-left: 8px;
        }

        .trust-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #1e293b;
            margin-left: -8px;
            background: #cbd5e1;
            object-fit: cover;
        }

        .trust-text {
            font-size: 12.5px;
            line-height: 1.35;
            color: #e2e8f0;
        }

        .trust-stars {
            color: #f59e0b;
            font-size: 13px;
        }

        /* Right Form Panel */
        .auth-form-panel {
            flex: 1 1 55%;
            min-width: 320px;
            padding: 45px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        @media (max-width: 767px) {
            .auth-form-panel {
                padding: 35px 25px;
            }
            .auth-showcase-panel {
                padding: 35px 25px;
            }
        }

        /* Segmented Pill Tabs */
        .auth-segmented-tabs {
            display: inline-flex;
            background: #f1f5f9;
            padding: 5px;
            border-radius: 14px;
            margin-bottom: 30px;
            width: 100%;
        }

        .auth-tab-btn {
            flex: 1;
            padding: 11px 18px;
            border: none;
            background: transparent;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .auth-tab-btn:hover {
            color: #0f172a;
        }

        .auth-tab-btn.active {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        }

        .auth-header-title {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .auth-header-sub {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 26px;
        }

        /* Form Inputs */
        .form-label-custom {
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            display: block;
        }

        .input-phone-group {
            display: flex;
            align-items: stretch;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
            background: #ffffff;
        }

        .input-phone-group:focus-within {
            border-color: var(--bb-primary);
            box-shadow: 0 0 0 4px var(--bb-primary-glow);
        }

        .input-phone-prefix {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0 14px;
            background: #f8fafc;
            border-right: 1.5px solid #e2e8f0;
            font-weight: 700;
            font-size: 14.5px;
            color: #334155;
        }

        .input-phone-prefix svg {
            width: 20px;
            height: 14px;
            border-radius: 2px;
        }

        .input-phone-field {
            flex: 1;
            border: none;
            padding: 14px 16px;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: 1px;
            color: #0f172a;
            outline: none;
            background: transparent;
        }

        .input-phone-field::placeholder {
            font-size: 14px;
            font-weight: 400;
            letter-spacing: normal;
            color: #94a3b8;
        }

        /* Primary Action Button */
        .btn-auth-primary {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 10px 22px -6px rgba(255, 107, 53, 0.45);
            transition: all 0.25s ease;
            margin-top: 20px;
        }

        .btn-auth-primary:hover {
            background: linear-gradient(135deg, #e85a24 0%, #ff7b30 100%);
            box-shadow: 0 14px 26px -6px rgba(255, 107, 53, 0.6);
            transform: translateY(-2px);
            color: #ffffff;
        }

        .btn-auth-primary:active {
            transform: translateY(0);
        }

        .btn-auth-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none !important;
        }

        /* 6-Digit OTP Box Grid */
        .otp-box-grid {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            margin: 20px 0 15px;
        }

        .otp-digit-input {
            width: 52px;
            height: 60px;
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
            background: #ffffff;
        }

        @media (max-width: 480px) {
            .otp-digit-input {
                width: 42px;
                height: 52px;
                font-size: 20px;
                gap: 6px;
            }
        }

        .otp-digit-input:focus {
            border-color: var(--bb-primary);
            box-shadow: 0 0 0 4px var(--bb-primary-glow);
            transform: translateY(-2px);
        }

        /* Resend Timer Pill */
        .resend-timer-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #64748b;
        }

        .resend-timer-badge strong {
            color: var(--bb-primary);
        }

        .btn-link-resend {
            border: none;
            background: transparent;
            color: var(--bb-primary);
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            padding: 0;
            transition: opacity 0.2s;
        }

        .btn-link-resend:hover {
            text-decoration: underline;
        }

        .btn-link-change {
            border: none;
            background: transparent;
            color: #3b82f6;
            font-weight: 600;
            font-size: 12.5px;
            cursor: pointer;
            padding: 0;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-link-change:hover {
            text-decoration: underline;
        }

        /* Standard input for email/password tab */
        .input-standard {
            width: 100%;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            padding: 13px 16px;
            font-size: 15px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
        }

        .input-standard:focus {
            border-color: var(--bb-primary);
            box-shadow: 0 0 0 4px var(--bb-primary-glow);
        }

        /* Password Wrapper */
        .password-wrapper {
            position: relative;
        }

        .toggle-password-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
        }

        .toggle-password-icon:hover {
            color: #334155;
        }

        /* Animation utilities */
        .fade-slide-in {
            animation: fadeSlideIn 0.35s ease forwards;
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .shake-animation {
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        /* Alert Styling */
        .custom-alert {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13.5px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .custom-alert-danger {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
        }

        .custom-alert-success {
            background: #ecfdf5;
            border: 1px solid #d1fae5;
            color: #065f46;
        }

        .custom-alert-info {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            color: #115e59;
        }

        /* Spinner */
        .spinner-inline {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2.5px solid rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <header class="header header-intro-clearance header-26">
            <?php include('include/header.php');?>  
        </header>

        <main class="main">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0 py-3" style="background: #ffffff; border-bottom: 1px solid #e2e8f0 !important;">
                <div class="container">
                    <ol class="breadcrumb mb-0" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="<?= _BASEURL ?>index.php" class="text-muted"><i class="icon-home mr-1"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-muted">Account</a></li>
                        <li class="breadcrumb-item active font-weight-bold text-dark" aria-current="page">Sign In</li>
                    </ol>
                </div>
            </nav>

            <!-- Hero & Auth Section -->
            <div class="login-hero-wrapper">
                <div class="auth-container-card">
                    
                    <!-- LEFT PANEL: Visual Showcase & Brand Values -->
                    <div class="auth-showcase-panel">
                        <div class="showcase-content">
                            <div class="brand-badge">
                                <img src="<?= _BASEURL ?>assets/images/logo-m.png" alt="BunnyBoss">
                                <span>AUTHENTIC FOOTWEAR &bull; INDIA</span>
                            </div>

                            <h2 class="showcase-title">
                                Step Into <span>Premium Style.</span>
                            </h2>
                            <p class="showcase-desc">
                                Log in to discover hand-curated collections, access members-only deals, and track your orders in real time.
                            </p>

                            <ul class="showcase-features">
                                <li class="showcase-feature-item">
                                    <div class="showcase-icon-box">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <div><strong>Lightning Fast OTP</strong> &mdash; 1-click passwordless access</div>
                                </li>
                                <li class="showcase-feature-item">
                                    <div class="showcase-icon-box">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div><strong>100% Verified Quality</strong> &mdash; Direct warehouse sourcing</div>
                                </li>
                                <li class="showcase-feature-item">
                                    <div class="showcase-icon-box">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    </div>
                                    <div><strong>Live Order Tracking</strong> &mdash; Delhivery & Shadowfax updates</div>
                                </li>
                            </ul>
                        </div>

                        <!-- Trust Badge Footer -->
                        <div class="showcase-trust-card">
                            <div class="trust-avatar-group">
                                <img src="<?= _BASEURL ?>assets/images/testimonials/user-1.jpg" alt="User" class="trust-avatar">
                                <img src="<?= _BASEURL ?>assets/images/testimonials/user-2.jpg" alt="User" class="trust-avatar">
                            </div>
                            <div class="trust-text">
                                <div class="trust-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                                <span>Loved by <strong>10,000+</strong> shoppers across India</span>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT PANEL: Interactive Login Form -->
                    <div class="auth-form-panel">
                        
                        <!-- Tab Selector -->
                        <div class="auth-segmented-tabs">
                            <button type="button" class="auth-tab-btn active" id="tab-btn-otp">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span>Mobile OTP Login</span>
                            </button>
                            <button type="button" class="auth-tab-btn" id="tab-btn-password">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                <span>Password Sign In</span>
                            </button>
                        </div>

                        <!-- SECTION 1: MOBILE OTP FLOW -->
                        <div id="section-otp-flow" class="fade-slide-in">
                            
                            <!-- STEP 1: PHONE NUMBER INPUT -->
                            <div id="step-phone-input">
                                <h3 class="auth-header-title">Welcome to BunnyBoss</h3>
                                <p class="auth-header-sub">Enter your 10-digit mobile number to receive a secure login OTP.</p>

                                <form id="form-login-phone" novalidate>
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom" for="input-mobile-number">Mobile Phone Number</label>
                                        <div class="input-phone-group" id="phone-group-container">
                                            <div class="input-phone-prefix">
                                                <!-- Indian Flag SVG -->
                                                <svg viewBox="0 0 24 16" fill="none">
                                                    <rect width="24" height="5.33" fill="#FF9933"/>
                                                    <rect y="5.33" width="24" height="5.33" fill="#FFFFFF"/>
                                                    <rect y="10.66" width="24" height="5.33" fill="#138808"/>
                                                    <circle cx="12" cy="8" r="2.2" stroke="#000080" stroke-width="0.8" fill="none"/>
                                                </svg>
                                                <span>+91</span>
                                            </div>
                                            <input type="tel" 
                                                   class="input-phone-field" 
                                                   id="input-mobile-number" 
                                                   name="mobile" 
                                                   placeholder="Enter 10-digit number" 
                                                   maxlength="16" 
                                                   autocomplete="tel" 
                                                   inputmode="numeric" 
                                                   required>
                                        </div>
                                    </div>

                                    <div id="phone-feedback-msg"></div>

                                    <button type="submit" id="btn-request-otp" class="btn-auth-primary">
                                        <span class="btn-text">GET VERIFICATION OTP</span>
                                        <svg class="btn-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </form>

                                <p class="text-center text-muted mt-4 mb-0" style="font-size: 12.5px;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align: -2px; margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Your information is encrypted &amp; never shared with third parties.
                                </p>
                            </div>

                            <!-- STEP 2: OTP ENTRY SCREEN -->
                            <div id="step-otp-verify" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h3 class="auth-header-title mb-0">Verify OTP</h3>
                                    <button type="button" id="btn-edit-phone" class="btn-link-change">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        Change Number
                                    </button>
                                </div>
                                <p class="auth-header-sub mb-3">
                                    A 6-digit code was sent to <strong class="text-dark">+91-<span id="display-target-mobile"></span></strong>
                                </p>

                                <form id="form-verify-otp-action" novalidate>
                                    <!-- 6 Box Digit Inputs -->
                                    <div class="otp-box-grid" id="otp-inputs-container">
                                        <input type="text" class="otp-digit-input" maxlength="1" inputmode="numeric" autocomplete="one-time-code" data-index="0" autofocus>
                                        <input type="text" class="otp-digit-input" maxlength="1" inputmode="numeric" data-index="1">
                                        <input type="text" class="otp-digit-input" maxlength="1" inputmode="numeric" data-index="2">
                                        <input type="text" class="otp-digit-input" maxlength="1" inputmode="numeric" data-index="3">
                                        <input type="text" class="otp-digit-input" maxlength="1" inputmode="numeric" data-index="4">
                                        <input type="text" class="otp-digit-input" maxlength="1" inputmode="numeric" data-index="5">
                                    </div>

                                    <!-- Hidden input storing complete combined OTP -->
                                    <input type="hidden" id="full-otp-value" name="otp" value="">

                                    <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                                        <div id="otp-timer-box" class="resend-timer-badge">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Resend in <strong id="otp-seconds-left">30</strong>s</span>
                                        </div>
                                        <button type="button" id="btn-trigger-resend" class="btn-link-resend" style="display: none;">
                                            Resend New OTP
                                        </button>
                                    </div>

                                    <div id="otp-feedback-msg"></div>

                                    <button type="submit" id="btn-submit-verify" class="btn-auth-primary">
                                        <span class="btn-text">VERIFY &amp; LOGIN</span>
                                        <svg class="btn-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- SECTION 2: PASSWORD SIGN IN FLOW -->
                        <div id="section-password-flow" style="display: none;" class="fade-slide-in">
                            <h3 class="auth-header-title">Password Sign In</h3>
                            <p class="auth-header-sub">Enter your email or username and password to log in.</p>

                            <form id="form-password-signin" onsubmit="event.preventDefault(); alert('Please use Mobile OTP Login for instant, secure authentication without remembering passwords.');">
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Email or Username</label>
                                    <input type="text" class="input-standard" placeholder="name@domain.com" required>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label-custom mb-0">Password</label>
                                        <a href="#" class="small text-muted" onclick="alert('Password reset link is sent via customer support. We recommend logging in via Mobile OTP.');">Forgot?</a>
                                    </div>
                                    <div class="password-wrapper">
                                        <input type="password" id="input-signin-pwd" class="input-standard" placeholder="Enter your password" required>
                                        <button type="button" class="toggle-password-icon" onclick="togglePasswordView()">
                                            <svg id="eye-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn-auth-primary">
                                    <span class="btn-text">SIGN IN WITH PASSWORD</span>
                                    <svg class="btn-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </main>

        <?php include('include/bottom.php');?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;

        var countdownTimer = null;
        var secondsRemaining = 30;

        // Tab Switching
        $('#tab-btn-otp').on('click', function() {
            $(this).addClass('active');
            $('#tab-btn-password').removeClass('active');
            $('#section-password-flow').hide();
            $('#section-otp-flow').show().addClass('fade-slide-in');
        });

        $('#tab-btn-password').on('click', function() {
            $(this).addClass('active');
            $('#tab-btn-otp').removeClass('active');
            $('#section-otp-flow').hide();
            $('#section-password-flow').show().addClass('fade-slide-in');
        });

        function cleanPhone(raw) {
            if (!raw) return '';
            var val = String(raw).replace(/\D/g, '');
            if (val.indexOf('0091') === 0 && val.length === 14) val = val.substring(4);
            else if (val.length === 12 && val.indexOf('91') === 0) val = val.substring(2);
            else if (val.length === 11 && val.indexOf('0') === 0) val = val.substring(1);
            else if (val.length > 0 && val.charAt(0) === '0') val = val.replace(/^0+/, '');
            return val.slice(0, 10);
        }

        function checkPhone(num) {
            if (!num) return { valid: false, message: 'Please enter your 10-digit mobile number.' };
            var c = cleanPhone(num);
            if (c.length < 10) return { valid: false, message: 'Please enter complete 10-digit mobile number (' + c.length + '/10 entered).' };
            if (!/^[6-9]/.test(c)) return { valid: false, message: 'Mobile number must start with 6, 7, 8, or 9.' };
            if (!/^[6-9]\d{9}$/.test(c)) return { valid: false, message: 'Please enter a valid 10-digit mobile number.' };
            if (/^(\d)\1{9}$/.test(c)) return { valid: false, message: 'Please enter a genuine, active mobile number.' };
            return { valid: true, clean: c };
        }

        // Real-time mobile input formatting and validation
        $('#input-mobile-number').on('input', function() {
            var raw = $(this).val();
            var cleaned = cleanPhone(raw);
            if (raw !== cleaned) {
                $(this).val(cleaned);
            }
            if (cleaned.length === 10) {
                var res = checkPhone(cleaned);
                if (res.valid) {
                    $('#phone-group-container').css('border-color', '#10b981');
                    $('#phone-feedback-msg').html('');
                } else {
                    $('#phone-group-container').css('border-color', '#ef4444');
                    $('#phone-feedback-msg').html('<div class="custom-alert custom-alert-danger"><span>' + res.message + '</span></div>');
                }
            } else {
                $('#phone-group-container').css('border-color', '');
                $('#phone-feedback-msg').html('');
            }
        });

        $('#input-mobile-number').on('paste', function() {
            var $this = $(this);
            setTimeout(function() {
                $this.val(cleanPhone($this.val())).trigger('input');
            }, 10);
        });

        $('#input-mobile-number').on('blur', function() {
            var val = cleanPhone($(this).val());
            if (val.length > 0 && val.length < 10) {
                $('#phone-group-container').css('border-color', '#ef4444');
                $('#phone-feedback-msg').html('<div class="custom-alert custom-alert-danger"><span>Please enter complete 10-digit mobile number (' + val.length + '/10 entered).</span></div>');
            }
        });

        // 6-digit OTP Box Auto-Advance & Paste Handlers
        var $otpInputs = $('.otp-digit-input');

        $otpInputs.on('input', function(e) {
            var $this = $(this);
            var val = $this.val().replace(/[^0-9]/g, '');
            $this.val(val ? val[0] : '');

            if (val && $this.data('index') < 5) {
                $otpInputs.eq($this.data('index') + 1).focus();
            }
            updateFullOtpValue();
        });

        $otpInputs.on('keydown', function(e) {
            var $this = $(this);
            var idx = $this.data('index');

            if (e.key === 'Backspace' && !$this.val() && idx > 0) {
                $otpInputs.eq(idx - 1).focus().val('');
                updateFullOtpValue();
            }
        });

        $otpInputs.on('paste', function(e) {
            e.preventDefault();
            var clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            var pastedData = clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);

            for (var i = 0; i < pastedData.length; i++) {
                $otpInputs.eq(i).val(pastedData[i]);
            }
            updateFullOtpValue();

            if (pastedData.length === 6) {
                $otpInputs.eq(5).focus();
                $('#form-verify-otp-action').submit();
            } else if (pastedData.length > 0) {
                $otpInputs.eq(Math.min(pastedData.length, 5)).focus();
            }
        });

        function updateFullOtpValue() {
            var combined = '';
            $otpInputs.each(function() {
                combined += $(this).val();
            });
            $('#full-otp-value').val(combined);
            if (combined.length === 6) {
                $('#otp-feedback-msg').html('');
            }
        }

        // Start Countdown Timer
        function runCountdown(duration) {
            clearInterval(countdownTimer);
            secondsRemaining = duration;
            $('#otp-timer-box').show();
            $('#otp-seconds-left').text(secondsRemaining);
            $('#btn-trigger-resend').hide();

            countdownTimer = setInterval(function() {
                secondsRemaining--;
                $('#otp-seconds-left').text(secondsRemaining);
                if (secondsRemaining <= 0) {
                    clearInterval(countdownTimer);
                    $('#otp-timer-box').hide();
                    $('#btn-trigger-resend').fadeIn(200);
                }
            }, 1000);
        }

        // Step 1: Request OTP
        $('#form-login-phone').on('submit', function(e) {
            e.preventDefault();
            var $input = $('#input-mobile-number');
            var rawMobile = $input.val();
            var mobile = cleanPhone(rawMobile);
            $input.val(mobile);
            var $btn = $('#btn-request-otp');
            var $feedback = $('#phone-feedback-msg');
            var $container = $('#phone-group-container');

            var check = checkPhone(mobile);
            if (!check.valid) {
                $container.css('border-color', '#ef4444').addClass('shake-animation');
                setTimeout(function() { $container.removeClass('shake-animation'); }, 400);
                $feedback.html('<div class="custom-alert custom-alert-danger"><span>' + check.message + '</span></div>');
                $input.focus();
                return;
            }

            $container.css('border-color', '#10b981');
            $feedback.html('');
            $btn.prop('disabled', true).find('.btn-text').html('<span class="spinner-inline mr-2"></span>SENDING OTP...');

            $.ajax({
                url: '<?= _BASEURL ?>include/auth_api.php',
                type: 'POST',
                data: { action: 'send_otp', mobile: mobile },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).find('.btn-text').text('GET VERIFICATION OTP');
                    if (res.success) {
                        $('#display-target-mobile').text(mobile);
                        $('#step-phone-input').hide();
                        $('#step-otp-verify').fadeIn(300);
                        
                        // Clear & focus first OTP digit
                        $otpInputs.val('');
                        $('#full-otp-value').val('');
                        $otpInputs.first().focus();

                        runCountdown(30);
                    } else {
                        $feedback.html('<div class="custom-alert custom-alert-danger"><span>' + (res.message || 'Failed to dispatch OTP.') + '</span></div>');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).find('.btn-text').text('GET VERIFICATION OTP');
                    $feedback.html('<div class="custom-alert custom-alert-danger"><span>Network error. Please check your connection and try again.</span></div>');
                }
            });
        });

        // Step 2: Verify OTP
        $('#form-verify-otp-action').on('submit', function(e) {
            e.preventDefault();
            var mobile = $('#input-mobile-number').val().trim();
            var otp = $('#full-otp-value').val().trim();
            var $btn = $('#btn-submit-verify');
            var $feedback = $('#otp-feedback-msg');

            if (otp.length !== 6) {
                $('#otp-inputs-container').addClass('shake-animation');
                setTimeout(function() { $('#otp-inputs-container').removeClass('shake-animation'); }, 400);
                $feedback.html('<div class="custom-alert custom-alert-danger"><span>Please enter all 6 digits of your OTP.</span></div>');
                return;
            }

            $feedback.html('');
            $btn.prop('disabled', true).find('.btn-text').html('<span class="spinner-inline mr-2"></span>VERIFYING...');

            $.ajax({
                url: '<?= _BASEURL ?>include/auth_api.php',
                type: 'POST',
                data: { action: 'verify_otp', mobile: mobile, otp: otp },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $feedback.html('<div class="custom-alert custom-alert-success"><svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>' + res.message + '</span></div>');
                        setTimeout(function() {
                            var urlParams = new URLSearchParams(window.location.search);
                            var redirectTarget = urlParams.get('redirect') || res.redirect_url || '<?= _BASEURL ?>index.php';
                            window.location.href = redirectTarget;
                        }, 700);
                    } else {
                        $btn.prop('disabled', false).find('.btn-text').text('VERIFY & LOGIN');
                        $('#otp-inputs-container').addClass('shake-animation');
                        setTimeout(function() { $('#otp-inputs-container').removeClass('shake-animation'); }, 400);
                        $feedback.html('<div class="custom-alert custom-alert-danger"><span>' + (res.message || 'Invalid or expired OTP.') + '</span></div>');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).find('.btn-text').text('VERIFY & LOGIN');
                    $feedback.html('<div class="custom-alert custom-alert-danger"><span>Verification failed. Please try again.</span></div>');
                }
            });
        });

        // Edit / Change Mobile
        $('#btn-edit-phone').on('click', function() {
            clearInterval(countdownTimer);
            $('#step-otp-verify').hide();
            $('#step-phone-input').fadeIn(300);
            $('#phone-group-container').css('border-color', '');
            $('#phone-feedback-msg').html('');
            $('#input-mobile-number').focus();
        });

        // Resend OTP
        $('#btn-trigger-resend').on('click', function() {
            var mobile = $('#input-mobile-number').val().trim();
            var $feedback = $('#otp-feedback-msg');
            var $btn = $(this);

            $btn.prop('disabled', true).text('Sending...');

            $.ajax({
                url: '<?= _BASEURL ?>include/auth_api.php',
                type: 'POST',
                data: { action: 'send_otp', mobile: mobile },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).text('Resend New OTP');
                    if (res.success) {
                        $feedback.html('<div class="custom-alert custom-alert-success"><span>A new OTP has been dispatched to your mobile.</span></div>');
                        $otpInputs.val('');
                        $('#full-otp-value').val('');
                        $otpInputs.first().focus();
                        runCountdown(30);
                    } else {
                        $feedback.html('<div class="custom-alert custom-alert-danger"><span>' + res.message + '</span></div>');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Resend New OTP');
                    $feedback.html('<div class="custom-alert custom-alert-danger"><span>Failed to resend. Please try again.</span></div>');
                }
            });
        });
    });

    function togglePasswordView() {
        var input = document.getElementById('input-signin-pwd');
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }
    </script>
</body>
</html>