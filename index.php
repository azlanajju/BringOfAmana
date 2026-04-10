<?php
/**
 * Bright of Amana Business Group – Landing Page
 */
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base = $base ?: '/brightOfAmana';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bright of Amana Business Group – Real Estate Investment Made Simple</title>
  <meta name="description" content="Bright of Amana Business Group is a trusted real estate investment company offering secure, transparent portfolio management and long-term wealth growth opportunities.">
  <meta name="keywords" content="Bright of Amana, Bright of Amana Business Group, real estate investment, property investment, investment management, portfolio tracking">
  <meta name="robots" content="index, follow, max-image-preview:large">
  <link rel="canonical" href="https://brightofamana.com/">
  <meta property="og:type" content="website">
  <meta property="og:title" content="Bright of Amana Business Group – Real Estate Investment Made Simple">
  <meta property="og:description" content="Secure and transparent real estate investment platform by Bright of Amana Business Group.">
  <meta property="og:url" content="https://brightofamana.com/">
  <meta property="og:image" content="https://brightofamana.com/assets/BABG_Logo.png">
  <meta property="og:site_name" content="Bright of Amana Business Group">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Bright of Amana Business Group – Real Estate Investment Made Simple">
  <meta name="twitter:description" content="Invest in real estate with confidence through Bright of Amana Business Group.">
  <meta name="twitter:image" content="https://brightofamana.com/assets/BABG_Logo.png">
  <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Bright of Amana Business Group",
    "url": "https://brightofamana.com/",
    "logo": "https://brightofamana.com/assets/BABG_Logo.png",
    "description": "Real estate investment and portfolio management services by Bright of Amana Business Group."
  }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      color: #0f172a;
      line-height: 1.6;
      overflow-x: hidden;
    }
    :root {
      --green: #268e45;
      --green-dark: #1e6f38;
      --green-light: #d4edda;
      --green-bg: #f0fdf4;
      --text: #0f172a;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --bg: #f8fafc;
      --white: #ffffff;
    }
    .container { max-width: 1160px; margin: 0 auto; padding: 0 1.5rem; }

    /* Header */
    header {
      background: var(--white);
      box-shadow: 0 1px 3px rgba(0,0,0,.04);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 1.5rem;
    }
    .logo {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      color: inherit;
    }
    .logo img { height: 44px; width: auto; display: block; }
    .logo-text { font-size: 1.2rem; font-weight: 700; color: var(--green); }
    .logo-tag { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; display: block; }
    .nav-links { display: flex; gap: 2rem; align-items: center; }
    .btn-nav {
      padding: 0.5rem 1.15rem;
      background: transparent;
      color: var(--green);
      border: 1.5px solid var(--green);
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.9rem;
      transition: all .2s;
    }
    .btn-nav:hover {
      background: var(--green);
      color: #fff;
      border-color: transparent;
    }
    .btn-nav:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(38,142,69,.25); }

    /* Hero */
    .hero {
      position: relative;
      color: #fff;
      padding: clamp(4rem, 10vw, 7rem) 1.5rem;
      text-align: center;
      background: url('assets/bgImage.png') center / cover no-repeat;
    }
    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(160deg, rgba(15,23,42,.82) 0%, rgba(30,58,138,.75) 50%, rgba(38,142,69,.4) 100%);
      pointer-events: none;
    }
    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 720px;
      margin: 0 auto;
    }
    .hero .hero-logo { max-width: 100px; height: auto; margin-bottom: 1.25rem; display: block; margin-left: auto; margin-right: auto; }
    .hero h1 {
      font-size: clamp(2rem, 5vw, 3.25rem);
      font-weight: 800;
      margin-bottom: 1.25rem;
      line-height: 1.15;
      letter-spacing: -0.02em;
    }
    .hero p {
      font-size: clamp(1rem, 2vw, 1.2rem);
      margin-bottom: 2rem;
      opacity: .92;
      line-height: 1.65;
    }
    .btn-hero {
      display: inline-block;
      padding: 1rem 2.25rem;
      background: #fff;
      color: var(--green);
      font-weight: 700;
      border-radius: 10px;
      text-decoration: none;
      font-size: 1.05rem;
      transition: transform .2s, box-shadow .2s;
    }
    .btn-hero:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 28px rgba(0,0,0,.2);
    }
    .btn-hero:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(255,255,255,.5); }
    .hero-scroll {
      margin-top: 3rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
      color: rgba(255,255,255,.7);
      font-size: 0.8rem;
      font-weight: 500;
      text-decoration: none;
      transition: color .2s;
    }
    .hero-scroll:hover { color: rgba(255,255,255,.95); }
    .hero-scroll-icon {
      width: 24px;
      height: 24px;
      border: 2px solid rgba(255,255,255,.4);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: bounce 2s infinite;
    }
    .hero-scroll-icon::after {
      content: '';
      width: 6px;
      height: 6px;
      background: rgba(255,255,255,.6);
      border-radius: 50%;
      transform: translateY(-2px);
    }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(6px); } }

    /* Stats */
    .stats-section {
      background: var(--white);
      padding: clamp(3rem, 6vw, 4.5rem) 1.5rem;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.5rem;
      max-width: 900px;
      margin: 0 auto;
    }
    .stat-item {
      text-align: center;
      padding: 1.75rem 1rem;
      background: var(--bg);
      border-radius: 14px;
      border: 1px solid var(--border);
      transition: border-color .2s, box-shadow .2s;
    }
    .stat-item:hover {
      border-color: var(--green-light);
      box-shadow: 0 8px 24px rgba(38,142,69,.08);
    }
    .stat-item h3 {
      font-size: 2rem;
      font-weight: 800;
      color: var(--green);
      margin-bottom: 0.35rem;
      letter-spacing: -0.02em;
    }
    .stat-item p {
      color: var(--text-muted);
      font-size: 0.9rem;
      font-weight: 500;
    }

    /* About */
    .about-section {
      background: var(--green-bg);
      padding: clamp(4rem, 8vw, 6rem) 1.5rem;
    }
    .section-label {
      display: inline-block;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--green);
      letter-spacing: 0.08em;
      text-transform: uppercase;
      margin-bottom: 0.75rem;
    }
    .section-title {
      font-size: clamp(1.85rem, 4vw, 2.5rem);
      font-weight: 800;
      margin-bottom: 1rem;
      color: var(--text);
      letter-spacing: -0.02em;
      line-height: 1.2;
    }
    .section-subtitle {
      font-size: 1.1rem;
      color: var(--text-muted);
      margin-bottom: 2.5rem;
      max-width: 640px;
      line-height: 1.65;
    }
    .about-content { max-width: 960px; margin: 0 auto; }
    .about-content .section-title,
    .about-content .section-subtitle { text-align: center; }
    .about-content .section-subtitle { margin-left: auto; margin-right: auto; }
    .about-text {
      font-size: 1.05rem;
      line-height: 1.85;
      color: var(--text);
      margin-bottom: 2.5rem;
    }
    .about-text p { margin-bottom: 1.25rem; }
    .about-highlights {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }
    .highlight-card {
      background: var(--white);
      padding: 2rem 1.75rem;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0,0,0,.04);
      border: 1px solid var(--border);
      transition: transform .2s, box-shadow .2s, border-color .2s;
    }
    .highlight-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(0,0,0,.08);
      border-color: var(--green-light);
    }
    .highlight-card .icon {
      width: 44px;
      height: 44px;
      background: var(--green-light);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.25rem;
      font-size: 1.25rem;
    }
    .highlight-card h3 {
      font-size: 1.15rem;
      color: var(--text);
      margin-bottom: 0.5rem;
      font-weight: 700;
    }
    .highlight-card h3 span { color: var(--green); }
    .highlight-card p {
      color: var(--text-muted);
      line-height: 1.6;
      margin: 0;
      font-size: 0.95rem;
    }

    /* CTA */
    .cta-section {
      background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
      color: #fff;
      padding: clamp(4rem, 8vw, 5.5rem) 1.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .cta-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      pointer-events: none;
      opacity: .6;
    }
    .cta-section .container { position: relative; z-index: 1; }
    .cta-section h2 {
      font-size: clamp(1.75rem, 4vw, 2.5rem);
      font-weight: 800;
      margin-bottom: 0.75rem;
      letter-spacing: -0.02em;
    }
    .cta-section p {
      font-size: 1.05rem;
      margin-bottom: 1.75rem;
      opacity: .95;
      max-width: 520px;
      margin-left: auto;
      margin-right: auto;
      line-height: 1.6;
    }
    .btn-cta {
      display: inline-block;
      padding: 1rem 2.25rem;
      background: #fff;
      color: var(--green);
      font-weight: 700;
      border-radius: 10px;
      text-decoration: none;
      font-size: 1.05rem;
      transition: transform .2s, box-shadow .2s;
    }
    .btn-cta:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 28px rgba(0,0,0,.2);
    }
    .btn-cta:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(255,255,255,.4); }

    /* Footer */
    footer {
      background: #0f172a;
      color: #fff;
      padding: 3rem 1.5rem 2rem;
    }
    .footer-content {
      display: grid;
      grid-template-columns: 1.5fr 1fr 1fr 1fr;
      gap: 2.5rem;
      margin-bottom: 2.5rem;
    }
    .footer-brand .logo { margin-bottom: 0.75rem; }
    .footer-brand .logo img { height: 40px; }
    .footer-brand p {
      color: rgba(255,255,255,.65);
      font-size: 0.95rem;
      line-height: 1.5;
      max-width: 260px;
    }
    .footer-section h4 {
      margin-bottom: 1rem;
      font-size: 0.9rem;
      font-weight: 600;
      color: rgba(255,255,255,.9);
    }
    .footer-section a, .footer-section p {
      display: block;
      color: rgba(255,255,255,.65);
      text-decoration: none;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      transition: color .2s;
    }
    .footer-section a:hover { color: #fff; }
    .footer-contact strong { color: #fff; }
    .footer-bottom {
      text-align: center;
      padding-top: 2rem;
      border-top: 1px solid rgba(255,255,255,.1);
      color: rgba(255,255,255,.5);
      font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .about-highlights { grid-template-columns: 1fr; }
      .footer-content { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
      .stats-grid { grid-template-columns: 1fr; }
      .footer-content { grid-template-columns: 1fr; }
      nav { flex-wrap: wrap; gap: 0.75rem; }
      .hero-scroll { display: none; }
    }
  </style>
</head>
<body>
  <header>
    <nav class="container">
      <a href="./" class="logo">
        <img src="assets/BABG_Logo.png" alt="Bright of Amana">
        <div>
          <span class="logo-text">Bright of Amana</span>
          <span class="logo-tag">Business Group</span>
        </div>
      </a>
      <div class="nav-links">
        <a href="login/" class="btn-nav">Sign In</a>
      </div>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-content">
      <img src="assets/BABG_Logo.png" alt="" class="hero-logo" width="100" height="auto">
      <h1>Invest in Real Estate. Grow Your Wealth.</h1>
      <p>Bright of Amana Business Group helps you invest in property, track your portfolio, and build long-term wealth with a trusted, transparent platform.</p>
      <a href="login/" class="btn-hero">Sign In to Your Account</a>
      <a href="#about" class="hero-scroll">
        <span class="hero-scroll-icon" aria-hidden="true"></span>
        <span>Scroll to learn more</span>
      </a>
    </div>
  </section>

  <section class="stats-section">
    <div class="container">
      <div class="stats-grid">
        <div class="stat-item">
          <h3>100%</h3>
          <p>Transparent Process</p>
        </div>
        <div class="stat-item">
          <h3>24/7</h3>
          <p>Portfolio Access</p>
        </div>
        <div class="stat-item">
          <h3>Secure</h3>
          <p>Data Protection</p>
        </div>
        <div class="stat-item">
          <h3>Fast</h3>
          <p>Approval Process</p>
        </div>
      </div>
    </div>
  </section>

  <section class="about-section" id="about">
    <div class="container">
      <div class="about-content">
        <span class="section-label">About Us</span>
        <h2 class="section-title">About Bright of Amana Business Group</h2>
        <p class="section-subtitle">A trusted real estate investment company committed to your financial growth.</p>

        <div class="about-text">
          <p><strong>Bright of Amana Business Group</strong> is a leading real estate investment company dedicated to secure, transparent, and profitable opportunities. We manage real estate portfolios and help investors build wealth through strategic property investments.</p>
          <p>Our platform lets you track monthly contributions, monitor performance, and access full investment history. Every investment is reviewed and processed with high standards of professionalism.</p>
          <p>Whether you're new to real estate or an experienced investor, we provide the tools, support, and expertise to help you reach your financial goals through trust, transparency, and service.</p>
        </div>

        <div class="about-highlights">
          <div class="highlight-card">
            <div class="icon">🏢</div>
            <h3><span>Real Estate</span> Expertise</h3>
            <p>Years of experience in investment, property management, and market analysis to support informed decisions.</p>
          </div>
          <div class="highlight-card">
            <div class="icon">🔒</div>
            <h3><span>Secure</span> Investments</h3>
            <p>Investments backed by real assets for tangible security and peace of mind.</p>
          </div>
          <div class="highlight-card">
            <div class="icon">✓</div>
            <h3><span>Transparent</span> Process</h3>
            <p>Full visibility into your portfolio and returns across all operations.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="cta-section">
    <div class="container">
      <h2>Start Your Investment Journey Today</h2>
      <p>Join Bright of Amana and take control of your portfolio with our simple, powerful platform.</p>
      <a href="request-investor.php" class="btn-cta">Get Started Now</a>
    </div>
  </section>

  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-section footer-brand">
          <a href="./" class="logo">
            <img src="assets/BABG_Logo.png" alt="Bright of Amana">
          </a>
          <p>Bright of Amana Business Group — Investment Management & Real Estate.</p>
        </div>
        <div class="footer-section">
          <h4>Quick Links</h4>
          <a href="./">Home</a>
          <a href="login/">Sign In</a>
        </div>
        <div class="footer-section footer-contact">
          <h4>Contact</h4>
          <p><strong>Mohammad Sinan</strong><br>Founder &amp; CEO</p>
          <a href="tel:+919964396818">+91 99643 96818</a>
        </div>
        <div class="footer-section">
          <h4>Support</h4>
          <a href="#">Contact Us</a>
          <a href="#">Help Center</a>
          <a href="#">Privacy Policy</a>
          <a href="#">Terms &amp; Conditions</a>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Bright of Amana Business Group. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>
