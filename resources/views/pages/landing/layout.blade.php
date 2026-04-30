<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'LegMed Directory')</title>
  <meta name="description" content="@yield('meta_description', 'Browse departments, hospitals, and doctor profiles on LegMed.')">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/images/favicon/msit_logo.jpg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('/assets/css/main.css') }}">
  <style>
    :root{
      --landing-bg:#f5f9fc;
      --landing-surface:#ffffff;
      --landing-surface-soft:#f2f8ff;
      --landing-ink:#11385c;
      --landing-copy:#64748b;
      --landing-line:rgba(17,56,92,.10);
      --landing-accent:#0e7ac4;
      --landing-accent-2:#10b3a3;
      --landing-accent-soft:rgba(14,122,196,.10);
      --landing-dark:#0f3251;
      --landing-dark-2:#0c2941;
      --landing-shadow:0 26px 70px rgba(15,50,81,.10);
      --landing-shadow-soft:0 18px 42px rgba(15,50,81,.07);
      --landing-radius:26px;
    }

    *{box-sizing:border-box}

    body{
      font-family:"DM Sans",sans-serif;
      color:var(--landing-ink);
      background:
        radial-gradient(circle at top left, rgba(16,179,163,.08), transparent 22%),
        radial-gradient(circle at top right, rgba(14,122,196,.08), transparent 18%),
        linear-gradient(180deg,#fbfdff 0%, var(--landing-bg) 100%);
      min-height:100vh;
    }

    h1,h2,h3,h4,h5,.landing-brand,.landing-display{
      font-family:"Space Grotesk",sans-serif;
      letter-spacing:-.03em;
    }

    a{transition:color .18s ease, transform .18s ease, box-shadow .18s ease}
    img{max-width:100%}

    .landing-shell{max-width:1240px;margin:0 auto;padding:0 18px}

    .landing-topbar{
      background:linear-gradient(90deg,var(--landing-dark) 0%, #15486e 100%);
      color:rgba(255,255,255,.86);
      font-size:.9rem;
    }

    .landing-topbar-inner{
      min-height:48px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:18px;
      flex-wrap:wrap;
    }

    .landing-topbar-group{
      display:flex;
      align-items:center;
      gap:16px;
      flex-wrap:wrap;
    }

    .landing-topbar-item{
      display:inline-flex;
      align-items:center;
      gap:8px;
      color:inherit;
      text-decoration:none;
    }

    .landing-topbar-item:hover{color:#fff}

    .landing-socials{
      display:flex;
      align-items:center;
      gap:8px;
    }

    .landing-socials a{
      width:32px;
      height:32px;
      display:grid;
      place-items:center;
      border-radius:999px;
      background:rgba(255,255,255,.12);
      color:#fff;
      text-decoration:none;
    }

    .landing-socials a:hover{background:rgba(255,255,255,.2)}

    .landing-header{
      position:sticky;
      top:0;
      z-index:40;
      background:rgba(255,255,255,.95);
      border-bottom:1px solid rgba(17,56,92,.06);
      backdrop-filter:blur(14px);
    }

    .landing-navbar{
      min-height:84px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:18px;
    }

    .landing-brand{
      display:flex;
      align-items:center;
      gap:12px;
      color:var(--landing-ink);
      text-decoration:none;
      font-size:1.15rem;
      font-weight:700;
    }

    .landing-brand img{
      width:auto;
      height:44px;
      display:block;
    }

    .landing-brand small{
      display:block;
      font-family:"DM Sans",sans-serif;
      font-size:.74rem;
      letter-spacing:.08em;
      text-transform:uppercase;
      color:var(--landing-copy);
    }

    .landing-nav-links{
      display:flex;
      align-items:center;
      gap:18px;
      flex-wrap:wrap;
    }

    .landing-nav-links a{
      position:relative;
      padding:10px 2px 14px;
      color:#50657d;
      font-weight:700;
      text-decoration:none;
      letter-spacing:.01em;
    }

    .landing-nav-links a::after{
      content:"";
      position:absolute;
      left:0;
      right:0;
      bottom:0;
      height:2px;
      border-radius:999px;
      background:linear-gradient(90deg,var(--landing-accent),var(--landing-accent-2));
      transform:scaleX(0);
      transform-origin:center;
      transition:transform .2s ease;
    }

    .landing-nav-links a:hover{
      color:var(--landing-ink);
    }

    .landing-nav-links a:hover::after,
    .landing-nav-links a.is-active::after{
      transform:scaleX(1);
    }

    .landing-nav-links a.is-active{
      color:var(--landing-ink);
    }

    .landing-cta-group{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }

    .landing-btn{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:13px 20px;
      border-radius:999px;
      border:0;
      font-weight:700;
      text-decoration:none;
      transition:transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .landing-btn:hover{transform:translateY(-1px)}

    .landing-btn-primary{
      background:linear-gradient(135deg,var(--landing-accent) 0%, #1aa3f1 100%);
      color:#fff;
      box-shadow:0 18px 32px rgba(14,122,196,.24);
    }

    .landing-btn-light{
      background:#fff;
      color:var(--landing-ink);
      border:1px solid var(--landing-line);
      box-shadow:0 12px 24px rgba(15,50,81,.06);
    }

    .landing-btn-dark{
      background:var(--landing-dark);
      color:#fff;
      box-shadow:0 18px 30px rgba(15,50,81,.18);
    }

    .landing-page-bar{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:18px;
      margin:18px 0 10px;
      padding:18px 22px;
      border-radius:22px;
      background:#fff;
      border:1px solid var(--landing-line);
      box-shadow:var(--landing-shadow-soft);
    }

    .landing-breadcrumbs{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      color:var(--landing-copy);
      font-weight:700;
      font-size:.92rem;
    }

    .landing-breadcrumbs a{
      color:var(--landing-copy);
      text-decoration:none;
    }

    .landing-breadcrumbs a:hover{color:var(--landing-accent)}

    .landing-page-meta{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }

    .landing-page-meta-minimal{
      color:var(--landing-copy);
      font-size:.95rem;
      font-weight:700;
      line-height:1.4;
    }

    .landing-section{margin:34px 0}

    .landing-section-head{
      display:flex;
      align-items:end;
      justify-content:space-between;
      gap:18px;
      margin-bottom:18px;
    }

    .landing-section-head p{
      margin:8px 0 0;
      color:var(--landing-copy);
      max-width:680px;
      line-height:1.7;
    }

    .landing-kicker{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:8px 14px;
      border-radius:999px;
      background:rgba(255,255,255,.16);
      color:#fff;
      font-size:.84rem;
      font-weight:700;
      letter-spacing:.08em;
      text-transform:uppercase;
    }

    .landing-display{
      font-size:clamp(2.35rem, 5vw, 4.75rem);
      line-height:.94;
      margin:18px 0;
    }

    .landing-lead{
      font-size:1.04rem;
      line-height:1.8;
      color:rgba(255,255,255,.86);
      max-width:680px;
    }

    .landing-search-card{
      padding:0;
      border-radius:0;
      background:transparent;
      border:0;
      box-shadow:none;
      text-align:center;
    }

    .landing-search-card h3{
      margin:0 0 14px;
      color:var(--landing-ink);
    }

    .landing-search-form{
      width:100%;
      max-width:560px;
      margin:0 auto;
      display:flex;
      align-items:stretch;
      justify-content:center;
      gap:0;
      border-radius:999px;
      overflow:hidden;
      background:#fff;
      border:1px solid rgba(17,56,92,.10);
      box-shadow:0 14px 24px rgba(15,50,81,.06);
    }

    .landing-search-input{
      flex:1 1 auto;
      min-width:0;
      padding:16px 20px;
      border:0;
      background:transparent;
      color:var(--landing-ink);
      font-size:1rem;
      outline:none;
    }

    .landing-search-input::placeholder{
      color:#8b9ab0;
    }

    .landing-search-submit{
      flex:0 0 auto;
      min-width:170px;
      border-radius:0;
      box-shadow:none;
      padding-inline:22px;
    }

    .landing-badge{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:8px 12px;
      border-radius:999px;
      background:var(--landing-accent-soft);
      color:var(--landing-accent);
      font-size:.82rem;
      font-weight:700;
    }

    .landing-copy{color:var(--landing-copy);line-height:1.7}
    .landing-muted{color:var(--landing-copy)}

    .landing-card-grid,
    .landing-chip-row,
    .landing-stat-row,
    .landing-metric-row,
    .landing-pills{
      display:flex;
      flex-wrap:wrap;
      gap:12px;
    }

    .landing-grid{display:grid;gap:18px}
    .landing-grid.departments{grid-template-columns:repeat(3,minmax(0,1fr))}
    .landing-grid.doctors{grid-template-columns:repeat(3,minmax(0,1fr))}
    .landing-grid.hospitals{grid-template-columns:repeat(3,minmax(0,1fr))}

    .landing-soft-1{--tile-bg:linear-gradient(135deg,#e8efff,#edf2ff);--tile-icon-bg:rgba(255,255,255,.78);--tile-icon:#6675d8}
    .landing-soft-2{--tile-bg:linear-gradient(135deg,#e7fbf7,#ecfffb);--tile-icon-bg:rgba(255,255,255,.78);--tile-icon:#16a085}
    .landing-soft-3{--tile-bg:linear-gradient(135deg,#fff0ea,#fff6f1);--tile-icon-bg:rgba(255,255,255,.78);--tile-icon:#f08a5d}
    .landing-soft-4{--tile-bg:linear-gradient(135deg,#f4ecff,#faf5ff);--tile-icon-bg:rgba(255,255,255,.78);--tile-icon:#9b72cf}
    .landing-soft-5{--tile-bg:linear-gradient(135deg,#edf6ff,#f4fbff);--tile-icon-bg:rgba(255,255,255,.78);--tile-icon:#2692d0}

    .landing-department-showcase{
      display:grid;
      grid-template-columns:repeat(5,minmax(0,1fr));
      gap:18px;
    }

    .landing-department-link{
      display:block;
      text-decoration:none;
      color:inherit;
    }

    .landing-department-link:hover{
      transform:translateY(-3px);
    }

    .landing-department-tile{
      height:100%;
      display:flex;
      flex-direction:column;
      align-items:center;
      text-align:center;
      padding:6px 10px 10px;
    }

    .landing-department-visual{
      width:160px;
      height:160px;
      border-radius:50%;
      display:grid;
      place-items:center;
      overflow:hidden;
      background:var(--tile-bg, linear-gradient(135deg,#e7eeff,#e7edf5));
      box-shadow:0 18px 34px rgba(15,50,81,.08);
      margin-bottom:16px;
    }

    .landing-department-visual img{
      width:100%;
      height:100%;
      object-fit:cover;
      display:block;
    }

    .landing-department-icon{
      width:68px;
      height:68px;
      border-radius:20px;
      display:grid;
      place-items:center;
      background:var(--tile-icon-bg, rgba(255,255,255,.7));
      color:var(--tile-icon, var(--landing-accent));
      font-size:2rem;
      box-shadow:0 14px 24px rgba(15,50,81,.08);
    }

    .landing-department-title{
      margin:0;
      font-size:1rem;
      line-height:1.24;
      color:#3f4551;
      max-width:200px;
    }

    .landing-department-meta{
      margin-top:8px;
      color:var(--landing-copy);
      font-size:.85rem;
      line-height:1.45;
    }

    .landing-department-cta{
      margin-top:10px;
      color:#19a9eb;
      font-weight:800;
      letter-spacing:.01em;
      text-transform:uppercase;
      font-size:.82rem;
    }

    .landing-card{
      height:100%;
      padding:22px;
      border:1px solid var(--landing-line);
      border-radius:28px;
      background:#fff;
      box-shadow:var(--landing-shadow-soft);
    }

    .landing-card-link{
      display:block;
      color:inherit;
      text-decoration:none;
      transition:transform .22s ease;
    }

    .landing-card-link:hover{transform:translateY(-4px)}
    .landing-card-link:hover .landing-card{box-shadow:0 24px 48px rgba(15,50,81,.11)}

    .landing-card-link.card-static:hover{transform:none}

    .landing-media{
      width:100%;
      height:210px;
      border-radius:22px;
      object-fit:cover;
      background:linear-gradient(135deg,#d6efff,#dff9f4);
    }

    .landing-avatar,
    .landing-avatar-fallback{
      width:76px;
      height:76px;
      border-radius:24px;
    }

    .landing-avatar{
      object-fit:cover;
      background:linear-gradient(135deg,#d6efff,#dff9f4);
    }

    .landing-avatar-fallback{
      display:grid;
      place-items:center;
      background:var(--tile-bg, linear-gradient(135deg,#d6efff,#dff9f4));
      color:var(--tile-icon, var(--landing-accent));
      font-weight:800;
    }

    .landing-avatar-fallback i{
      font-size:2rem;
    }

    .landing-metric{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 13px;
      border-radius:14px;
      background:#f6fbff;
      border:1px solid rgba(17,56,92,.05);
      color:var(--landing-ink);
      font-weight:700;
      font-size:.92rem;
    }

    .landing-pill{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 14px;
      border-radius:999px;
      background:#eef7ff;
      border:1px solid rgba(14,122,196,.10);
      color:var(--landing-ink);
      font-weight:700;
      text-decoration:none;
    }

    .landing-doctor-results{
      display:grid;
      gap:18px;
    }

    .landing-doctor-result{
      padding:24px;
      border-radius:30px;
      background:#fff;
      border:1px solid var(--landing-line);
      box-shadow:var(--landing-shadow-soft);
    }

    .landing-doctor-result-grid{
      display:grid;
      grid-template-columns:112px minmax(0,1fr) minmax(250px, .65fr);
      gap:20px;
      align-items:start;
    }

    .landing-doctor-result .landing-avatar,
    .landing-doctor-result .landing-avatar-fallback{
      width:112px;
      height:112px;
      border-radius:28px;
    }

    .landing-doctor-head{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:14px;
      flex-wrap:wrap;
      margin-bottom:10px;
    }

    .landing-doctor-name{
      margin:0;
      font-size:1.45rem;
      line-height:1.05;
    }

    .landing-doctor-subtitle{
      margin-top:6px;
      color:var(--landing-copy);
      font-weight:600;
    }

    .landing-doctor-support{
      display:grid;
      gap:14px;
      padding:18px;
      border-radius:24px;
      background:linear-gradient(180deg,#f6fbff 0%, #eef7ff 100%);
      border:1px solid rgba(14,122,196,.10);
    }

    .landing-doctor-support .support-row{
      display:flex;
      justify-content:space-between;
      gap:14px;
      align-items:flex-start;
    }

    .landing-doctor-support .support-row strong{
      color:var(--landing-ink);
      font-size:1.06rem;
    }

    .landing-doctor-support .support-copy{
      color:var(--landing-copy);
      font-size:.92rem;
      line-height:1.6;
    }

    .landing-action-row{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      margin-top:16px;
    }

    .landing-action-row .landing-btn{
      padding:12px 16px;
      font-size:.95rem;
    }

    .landing-doctor-meta{
      display:grid;
      gap:12px;
    }

    .landing-doctor-meta-item{
      display:flex;
      align-items:flex-start;
      gap:10px;
      color:var(--landing-copy);
      line-height:1.6;
    }

    .landing-doctor-meta-item i{
      width:18px;
      margin-top:3px;
      color:var(--landing-accent);
      text-align:center;
    }

    .landing-empty{
      padding:42px 24px;
      border-radius:28px;
      border:1px dashed rgba(100,116,139,.28);
      background:rgba(255,255,255,.82);
      text-align:center;
      color:var(--landing-copy);
    }

    .landing-slab{
      padding:22px;
      border-radius:28px;
      background:#fff;
      border:1px solid var(--landing-line);
      box-shadow:var(--landing-shadow-soft);
    }

    .landing-detail-grid{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:22px}
    .landing-detail-stack{display:grid;gap:18px}
    .landing-sticky{position:sticky;top:114px}
    .landing-list{display:grid;gap:14px}
    .landing-list-card{padding:18px;border-radius:22px;background:#fff;border:1px solid var(--landing-line);box-shadow:var(--landing-shadow-soft)}
    .landing-timeline{display:grid;gap:14px}
    .landing-timeline-item{padding-left:18px;border-left:3px solid rgba(14,122,196,.16)}
    .landing-contact-list{display:grid;gap:10px}
    .landing-contact-item{display:flex;align-items:flex-start;gap:12px;color:var(--landing-copy)}
    .landing-profile-layout{
      display:grid;
      grid-template-columns:320px minmax(0,1fr);
      gap:24px;
      align-items:start;
    }
    .landing-profile-rail{
      display:grid;
      gap:16px;
    }
    .landing-profile-card,
    .landing-doctor-summary-card{
      padding:20px 22px;
      border-radius:24px;
      background:#fff;
      border:1px solid var(--landing-line);
      box-shadow:var(--landing-shadow-soft);
    }
    .landing-profile-identity{
      display:grid;
      gap:16px;
    }
    .landing-profile-call-trigger{
      min-height:44px;
      border:0;
      border-radius:999px;
      background:linear-gradient(135deg,var(--landing-accent),#1aa3f1);
      color:#fff;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:0 16px;
      box-shadow:0 16px 28px rgba(14,122,196,.24);
    }
    .landing-profile-call-trigger:hover{
      transform:translateY(-1px);
      color:#fff;
    }
    .landing-profile-book-trigger{
      min-height:44px;
      border:1px solid rgba(15,50,81,.10);
      border-radius:999px;
      background:#fff;
      color:var(--landing-ink);
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:0 16px;
      box-shadow:0 12px 24px rgba(15,50,81,.08);
    }
    .landing-profile-book-trigger:hover{
      transform:translateY(-1px);
      color:var(--landing-ink);
      background:#f7fbff;
    }
    .landing-profile-actions{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:10px;
      width:100%;
    }
    .landing-profile-actions .landing-profile-call-trigger,
    .landing-profile-actions .landing-profile-book-trigger{
      width:100%;
    }
    .landing-profile-head{
      display:flex;
      align-items:flex-start;
      gap:14px;
    }
    .landing-profile-head .landing-avatar,
    .landing-profile-head .landing-avatar-fallback{
      width:88px;
      height:88px;
      border-radius:26px;
      flex-shrink:0;
      background:#eef6ff;
      color:var(--landing-accent);
    }
    .landing-profile-name{
      margin:0;
      color:var(--landing-ink);
      font-size:1.65rem;
      line-height:1.15;
    }
    .landing-profile-subtitle{
      margin-top:7px;
      color:var(--landing-copy);
      font-size:.98rem;
      font-weight:700;
    }
    .landing-profile-subtitle-accent{
      margin-top:8px;
      color:var(--landing-accent);
      font-size:.84rem;
      line-height:1.5;
      font-weight:800;
    }
    .landing-profile-divider{
      height:1px;
      width:100%;
      background:var(--landing-line);
      margin:2px 0 0;
    }
    .landing-profile-meta{
      display:grid;
      gap:10px;
    }
    .landing-profile-meta-line{
      display:grid;
      gap:2px;
      color:var(--landing-copy);
      font-size:.88rem;
      line-height:1.45;
    }
    .landing-profile-meta-line strong{
      color:var(--landing-ink);
      font-size:.74rem;
      font-weight:800;
      letter-spacing:.05em;
      text-transform:uppercase;
    }
    .landing-profile-meta-line span{
      color:var(--landing-copy);
      font-weight:600;
    }
    .landing-profile-meta-row{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:10px;
    }
    .landing-profile-mini-grid{
      display:grid;
      grid-template-columns:1fr;
      gap:10px;
    }
    .landing-profile-mini-item{
      display:flex;
      align-items:flex-start;
      gap:10px;
      padding:11px 12px;
      border-radius:16px;
      background:#f7fbff;
      border:1px solid rgba(14,122,196,.08);
    }
    .landing-profile-mini-item i{
      width:16px;
      text-align:center;
      color:var(--landing-accent);
      margin-top:2px;
      flex-shrink:0;
    }
    .landing-profile-mini-item strong{
      display:block;
      color:var(--landing-ink);
      font-size:.92rem;
      line-height:1.2;
    }
    .landing-profile-mini-item span{
      display:block;
      margin-top:4px;
      color:var(--landing-copy);
      font-size:.78rem;
      line-height:1.45;
    }
    .landing-call-modal{
      position:fixed;
      inset:0;
      z-index:120;
      display:none;
      align-items:center;
      justify-content:center;
      padding:20px;
      background:rgba(8,20,33,.46);
      backdrop-filter:blur(8px);
    }
    .landing-call-modal.is-open{
      display:flex;
    }
    .landing-call-card{
      width:min(100%, 460px);
      border-radius:28px;
      background:#fff;
      border:1px solid var(--landing-line);
      box-shadow:0 28px 64px rgba(15,50,81,.18);
      overflow:hidden;
    }
    .landing-call-card-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      padding:18px 20px;
      border-bottom:1px solid var(--landing-line);
      background:#fbfdff;
    }
    .landing-call-brand{
      display:flex;
      align-items:center;
      gap:12px;
      min-width:0;
    }
    .landing-call-brand img{
      width:48px;
      height:48px;
      object-fit:contain;
      flex-shrink:0;
    }
    .landing-call-brand strong{
      display:block;
      color:var(--landing-ink);
      font-size:1rem;
      line-height:1.2;
    }
    .landing-call-brand span{
      display:block;
      margin-top:4px;
      color:var(--landing-copy);
      font-size:.86rem;
      line-height:1.4;
    }
    .landing-call-close{
      width:38px;
      height:38px;
      border:0;
      border-radius:999px;
      background:#eef6ff;
      color:var(--landing-ink);
      display:grid;
      place-items:center;
      flex-shrink:0;
    }
    .landing-call-card-body{
      padding:20px;
      display:grid;
      gap:16px;
    }
    .landing-call-detail{
      display:grid;
      gap:6px;
    }
    .landing-call-detail strong{
      color:var(--landing-ink);
      font-size:.82rem;
      font-weight:800;
      letter-spacing:.05em;
      text-transform:uppercase;
    }
    .landing-call-detail span,
    .landing-call-detail p{
      margin:0;
      color:var(--landing-copy);
      line-height:1.6;
    }
    .landing-call-number-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
      padding:14px 16px;
      border-radius:18px;
      background:#f7fbff;
      border:1px solid rgba(14,122,196,.08);
    }
    .landing-call-number{
      color:var(--landing-ink);
      font-size:1.45rem;
      font-weight:800;
      letter-spacing:.01em;
    }
    .landing-call-copy{
      padding:10px 14px;
      border-radius:999px;
      border:1px solid var(--landing-line);
      background:#fff;
      color:var(--landing-ink);
      font-weight:700;
    }
    .landing-call-copy.is-copied{
      background:#e8fff7;
      border-color:rgba(16,179,163,.26);
      color:#08785e;
    }
    .landing-call-actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
    }
    .landing-call-note{
      font-size:.84rem;
      color:var(--landing-copy);
      line-height:1.6;
    }
    .landing-call-note a{
      color:var(--landing-accent);
      text-decoration:none;
    }
    .landing-book-alert{
      display:none;
      padding:12px 14px;
      border-radius:16px;
      font-size:.9rem;
      line-height:1.55;
    }
    .landing-book-alert.is-visible{
      display:block;
    }
    .landing-book-alert.is-error{
      background:rgba(220,53,69,.08);
      border:1px solid rgba(220,53,69,.14);
      color:#a52834;
    }
    .landing-book-alert.is-success{
      background:rgba(16,179,163,.10);
      border:1px solid rgba(16,179,163,.18);
      color:#08785e;
    }
    .landing-book-success{
      display:none;
      gap:16px;
    }
    .landing-book-success.is-visible{
      display:grid;
    }
    .landing-book-success-icon{
      width:64px;
      height:64px;
      border-radius:22px;
      display:grid;
      place-items:center;
      background:linear-gradient(135deg,rgba(14,122,196,.12),rgba(16,179,163,.14));
      color:var(--landing-accent);
      font-size:1.5rem;
    }
    .landing-book-success h3{
      margin:0;
      color:var(--landing-ink);
    }
    .landing-book-success p{
      margin:0;
      color:var(--landing-copy);
      line-height:1.7;
    }
    .landing-call-card.is-booking{
      width:min(100%, 760px);
      max-height:min(92vh, 860px);
      display:flex;
      flex-direction:column;
    }
    .landing-call-card.is-booking .landing-call-card-body{
      overflow:auto;
      padding:22px;
    }
    .landing-booking-panel{
      display:grid;
      gap:18px;
    }
    .landing-booking-header{
      display:grid;
      gap:8px;
    }
    .landing-booking-header h3{
      margin:0;
      font-size:1.2rem;
      color:var(--landing-ink);
    }
    .landing-booking-header p{
      margin:0;
      color:var(--landing-copy);
      line-height:1.6;
      font-size:.94rem;
    }
    .landing-booking-choice-grid{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:12px;
    }
    .landing-booking-choice{
      width:100%;
      text-align:left;
      padding:16px 18px;
      border-radius:20px;
      border:1px solid rgba(17,56,92,.10);
      background:#fff;
      transition:border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .landing-booking-choice:hover{
      transform:translateY(-1px);
      border-color:rgba(14,122,196,.24);
    }
    .landing-booking-choice.is-active{
      border-color:rgba(14,122,196,.32);
      box-shadow:0 18px 32px rgba(14,122,196,.10);
      background:linear-gradient(180deg,rgba(14,122,196,.05),rgba(16,179,163,.03));
    }
    .landing-booking-choice i{
      color:var(--landing-accent);
      margin-bottom:10px;
      font-size:1.05rem;
    }
    .landing-booking-choice strong{
      display:block;
      color:var(--landing-ink);
      font-size:1rem;
    }
    .landing-booking-choice span{
      display:block;
      margin-top:6px;
      color:var(--landing-copy);
      font-size:.88rem;
      line-height:1.55;
    }
    .landing-booking-summary{
      padding:14px 16px;
      border-radius:18px;
      background:#f7fbff;
      border:1px solid rgba(14,122,196,.08);
      color:var(--landing-copy);
      font-size:.9rem;
      line-height:1.6;
    }
    .landing-booking-summary strong{
      color:var(--landing-ink);
    }
    .landing-booking-grid{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:14px;
    }
    .landing-booking-field{
      display:grid;
      gap:7px;
    }
    .landing-booking-field.is-span-2{
      grid-column:1 / -1;
    }
    .landing-booking-field label{
      color:var(--landing-ink);
      font-size:.86rem;
      font-weight:700;
    }
    .landing-booking-field input,
    .landing-booking-field select,
    .landing-booking-field textarea{
      width:100%;
      padding:13px 14px;
      border-radius:16px;
      border:1px solid rgba(17,56,92,.12);
      background:#fff;
      color:var(--landing-ink);
      font:inherit;
      outline:none;
      transition:border-color .18s ease, box-shadow .18s ease;
    }
    .landing-booking-field textarea{
      min-height:108px;
      resize:vertical;
    }
    .landing-booking-field input:focus,
    .landing-booking-field select:focus,
    .landing-booking-field textarea:focus{
      border-color:rgba(14,122,196,.38);
      box-shadow:0 0 0 4px rgba(14,122,196,.10);
    }
    .landing-booking-field small{
      color:var(--landing-copy);
      font-size:.8rem;
      line-height:1.5;
    }
    .landing-booking-field.has-error input,
    .landing-booking-field.has-error select,
    .landing-booking-field.has-error textarea{
      border-color:rgba(220,53,69,.34);
      box-shadow:0 0 0 4px rgba(220,53,69,.08);
    }
    .landing-booking-error{
      color:#a52834;
      font-size:.8rem;
      line-height:1.45;
    }
    .landing-booking-loading{
      display:none;
      align-items:center;
      gap:10px;
      color:var(--landing-copy);
      font-size:.92rem;
      line-height:1.5;
    }
    .landing-booking-loading.is-visible{
      display:flex;
    }
    .landing-booking-loading i{
      color:var(--landing-accent);
    }
    .landing-booking-footer{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }
    .landing-booking-footer-note{
      color:var(--landing-copy);
      font-size:.84rem;
      line-height:1.6;
    }
    .landing-booking-actions{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      margin-left:auto;
    }
    .landing-booking-empty{
      padding:16px 18px;
      border-radius:18px;
      background:#fdf7ef;
      border:1px solid rgba(182,102,24,.14);
      color:#8b5b1c;
      font-size:.9rem;
      line-height:1.6;
    }
    .landing-booking-empty.is-span-2{
      grid-column:1 / -1;
    }
    .landing-profile-content{
      display:grid;
      gap:18px;
      min-width:0;
    }
    .landing-profile-metrics{
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:12px;
    }
    .landing-profile-metric-card{
      padding:16px 18px;
      border-radius:22px;
      background:#fff;
      border:1px solid var(--landing-line);
      box-shadow:var(--landing-shadow-soft);
    }
    .landing-profile-metric-card .eyebrow{
      display:inline-flex;
      align-items:center;
      gap:7px;
      color:var(--landing-copy);
      font-size:.76rem;
      font-weight:800;
      letter-spacing:.06em;
      text-transform:uppercase;
    }
    .landing-profile-metric-card strong{
      display:block;
      margin-top:10px;
      color:var(--landing-ink);
      font-size:1.2rem;
      line-height:1.2;
    }
    .landing-profile-metric-card span{
      display:block;
      margin-top:4px;
      color:var(--landing-copy);
      font-size:.9rem;
      line-height:1.5;
    }
    .landing-doctor-summary{
      margin-top:0;
      display:grid;
      grid-template-columns:minmax(0,1fr);
      gap:18px;
      align-items:start;
    }
    .landing-doctor-summary-card h3{
      margin:0 0 10px;
      color:var(--landing-ink);
      font-size:1.05rem;
    }
    .landing-doctor-summary-card p{
      margin:0;
      color:var(--landing-copy);
      line-height:1.7;
    }
    .landing-doctor-summary-aside{
      display:grid;
      gap:16px;
    }
    .landing-tab-shell{
      display:grid;
      gap:18px;
    }
    .landing-tab-nav{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      padding:10px 12px;
      border-radius:24px;
      background:#fff;
      border:1px solid var(--landing-line);
      box-shadow:var(--landing-shadow-soft);
    }
    .landing-tab-btn{
      border:0;
      background:transparent;
      color:var(--landing-copy);
      font-weight:800;
      font-size:.95rem;
      padding:12px 16px;
      border-radius:999px;
      transition:background .18s ease,color .18s ease,transform .18s ease;
    }
    .landing-tab-btn:hover{
      color:var(--landing-ink);
      background:#f4f9ff;
    }
    .landing-tab-btn.is-active{
      color:#fff;
      background:linear-gradient(135deg,var(--landing-accent),var(--landing-accent-2));
      box-shadow:0 12px 24px rgba(14,122,196,.18);
    }
    .landing-tab-panel{display:none}
    .landing-tab-panel.is-active{display:block}
    .landing-clinic-grid{
      display:grid;
      gap:14px;
    }
    .landing-overview-grid{
      display:grid;
      grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);
      gap:18px;
    }
    .landing-overview-side{
      display:grid;
      gap:14px;
    }
    .landing-info-grid{
      display:grid;
      gap:10px;
    }
    .landing-info-row{
      display:flex;
      align-items:flex-start;
      gap:12px;
      color:var(--landing-copy);
      line-height:1.6;
    }
    .landing-info-row i{
      width:18px;
      color:var(--landing-accent);
      margin-top:3px;
      text-align:center;
    }
    .landing-hero-doctor{display:flex;gap:18px;align-items:flex-start}
    .landing-hero-doctor .landing-avatar,.landing-hero-doctor .landing-avatar-fallback{width:96px;height:96px;border-radius:28px;background:#eef6ff;color:var(--landing-accent)}
    .landing-cover{
      position:relative;
      overflow:hidden;
      padding:28px;
      border-radius:32px;
      background:#fff;
      color:var(--landing-ink);
      box-shadow:var(--landing-shadow-soft);
      border:1px solid var(--landing-line);
    }
    .landing-cover-image{display:none}
    .landing-cover-content{position:relative;z-index:1}
    .landing-cover .landing-kicker{
      background:#eef7ff;
      color:var(--landing-accent);
    }
    .landing-cover .landing-display{
      color:var(--landing-ink);
      text-shadow:none;
    }
    .landing-cover .landing-pill{
      background:#f5f9ff;
    }

    .landing-home-hero-bleed{
      position:relative;
      overflow:hidden;
      margin:0 0 34px;
      background:
        linear-gradient(90deg, rgba(9,34,55,.92) 0%, rgba(10,45,73,.88) 36%, rgba(14,100,133,.52) 100%),
        var(--hero-image, linear-gradient(135deg,#0d3352 0%, #16507b 45%, #1a8294 100%));
      background-repeat:no-repeat;
      background-position:center center;
      background-size:cover;
      color:#fff;
      min-height:680px;
      box-shadow:var(--landing-shadow);
    }

    .landing-home-hero-bleed::before{
      content:"";
      position:absolute;
      inset:0;
      background:
        radial-gradient(circle at 76% 24%, rgba(255,255,255,.18), transparent 20%),
        linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,0) 28%, rgba(4,22,37,.18) 100%);
      pointer-events:none;
    }

    .landing-home-hero{
      position:relative;
      z-index:1;
      min-height:680px;
      display:flex;
      align-items:center;
      padding:40px 0 48px;
    }

    .landing-home-hero .landing-display{
      color:#fff;
      text-shadow:0 14px 34px rgba(4,22,37,.24);
    }

    .landing-home-hero-grid{
      position:relative;
      z-index:1;
      display:grid;
      grid-template-columns:minmax(0,1.05fr) minmax(320px,.95fr);
      gap:34px;
      align-items:center;
    }

    .landing-hero-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:26px}

    .landing-home-stat{
      min-width:150px;
      flex:1 1 0;
      padding:18px 18px 16px;
      border-radius:22px;
      background:rgba(255,255,255,.10);
      border:1px solid rgba(255,255,255,.12);
      backdrop-filter:blur(12px);
    }

    .landing-home-stat strong{
      display:block;
      font-family:"Space Grotesk",sans-serif;
      font-size:1.8rem;
      line-height:1;
      margin-bottom:6px;
    }

    .landing-home-stat span{
      color:rgba(255,255,255,.78);
      font-size:.95rem;
    }

    .landing-hero-panel{
      display:grid;
      gap:16px;
    }

    .landing-hero-photo{
      width:100%;
      height:370px;
      border-radius:30px;
      object-fit:cover;
      border:6px solid rgba(255,255,255,.14);
      box-shadow:0 26px 48px rgba(6,24,39,.22);
      background:linear-gradient(135deg,#cdeefe,#d9fbf3);
    }

    .landing-hero-float{
      max-width:330px;
      margin-left:auto;
      padding:18px 20px;
      border-radius:24px;
      background:rgba(255,255,255,.16);
      border:1px solid rgba(255,255,255,.16);
      backdrop-filter:blur(14px);
      box-shadow:0 24px 40px rgba(6,24,39,.16);
    }

    .landing-hero-float .eyebrow{
      display:inline-flex;
      align-items:center;
      gap:8px;
      color:#fff;
      font-size:.8rem;
      font-weight:700;
      letter-spacing:.08em;
      text-transform:uppercase;
      opacity:.9;
    }

    .landing-hero-float h3{
      margin:12px 0 8px;
      color:#fff;
      font-size:1.2rem;
    }

    .landing-hero-float p{
      margin:0;
      color:rgba(255,255,255,.84);
      line-height:1.7;
      font-size:.95rem;
    }

    .landing-mini-card{
      padding:24px;
      border-radius:28px;
      background:#fff;
      color:var(--landing-ink);
      box-shadow:0 24px 40px rgba(6,24,39,.18);
    }

    .landing-mini-card h3{
      margin:0 0 12px;
      font-size:1.15rem;
    }

    .landing-mini-card p{
      margin:0;
      color:var(--landing-copy);
      line-height:1.7;
    }

    .landing-inline-list{
      display:grid;
      gap:10px;
      margin-top:14px;
    }

    .landing-inline-item{
      display:flex;
      align-items:center;
      gap:10px;
      font-weight:700;
      color:var(--landing-ink);
    }

    .landing-feature-strip{
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:16px;
      margin-top:-24px;
      position:relative;
      z-index:2;
    }

    .landing-feature-card{
      padding:22px;
      border-radius:26px;
      background:#fff;
      border:1px solid var(--landing-line);
      box-shadow:var(--landing-shadow-soft);
    }

    .landing-feature-icon{
      width:52px;
      height:52px;
      display:grid;
      place-items:center;
      border-radius:18px;
      background:linear-gradient(135deg,#e1f5ff,#e4fbf6);
      color:var(--landing-accent);
      font-size:1.15rem;
      margin-bottom:16px;
    }

    .landing-about-grid{
      display:grid;
      grid-template-columns:minmax(0,.95fr) minmax(0,1.05fr);
      gap:22px;
      align-items:center;
    }

    .landing-about-photo{
      width:100%;
      height:420px;
      border-radius:32px;
      object-fit:cover;
      box-shadow:var(--landing-shadow);
      background:linear-gradient(135deg,#d6efff,#dff9f4);
    }

    .landing-check-list{
      display:grid;
      gap:12px;
      margin-top:18px;
    }

    .landing-check-item{
      display:flex;
      align-items:flex-start;
      gap:10px;
      color:var(--landing-copy);
    }

    .landing-check-item i{
      color:var(--landing-accent-2);
      margin-top:4px;
    }

    .landing-cta-band{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:18px;
      padding:28px 30px;
      border-radius:30px;
      background:linear-gradient(135deg,#11385c 0%,#0f6a8f 100%);
      color:#fff;
      box-shadow:var(--landing-shadow);
    }

    .landing-cta-band p{
      color:rgba(255,255,255,.82);
      margin:8px 0 0;
      max-width:700px;
    }

    .landing-footer{
      margin-top:54px;
      background:linear-gradient(180deg,#14334f 0%, #0c2235 100%);
      color:rgba(255,255,255,.76);
    }

    .landing-footer-main{
      padding:54px 0 24px;
    }

    .landing-footer-grid{
      display:grid;
      grid-template-columns:1.2fr .8fr .8fr .9fr;
      gap:28px;
    }

    .landing-footer-title{
      color:#fff;
      font-family:"Space Grotesk",sans-serif;
      font-size:1.08rem;
      margin-bottom:16px;
    }

    .landing-footer-list{
      display:grid;
      gap:10px;
    }

    .landing-footer-list a{
      color:rgba(255,255,255,.74);
      text-decoration:none;
    }

    .landing-footer-list a:hover{color:#fff}

    .landing-footer-copy{
      padding:18px 0 28px;
      border-top:1px solid rgba(255,255,255,.10);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
      font-size:.92rem;
    }

    @media (max-width: 1199.98px){
      .landing-home-hero-grid,
      .landing-about-grid,
      .landing-detail-grid,
      .landing-profile-layout,
      .landing-profile-metrics,
      .landing-doctor-result-grid,
      .landing-doctor-summary,
      .landing-doctor-hero,
      .landing-overview-grid{grid-template-columns:1fr}
      .landing-feature-strip{grid-template-columns:repeat(2,minmax(0,1fr))}
      .landing-department-showcase{grid-template-columns:repeat(4,minmax(0,1fr))}
      .landing-grid.departments{grid-template-columns:repeat(2,minmax(0,1fr))}
      .landing-grid.doctors,
      .landing-grid.hospitals{grid-template-columns:repeat(2,minmax(0,1fr))}
      .landing-footer-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    }

    @media (max-width: 991.98px){
      .landing-department-showcase{grid-template-columns:repeat(3,minmax(0,1fr))}

      .landing-navbar{
        flex-direction:column;
        align-items:flex-start;
        padding:16px 0;
      }

      .landing-nav-links,
      .landing-cta-group{
        width:100%;
      }
    }

    @media (max-width: 767.98px){
      .landing-topbar-inner,
      .landing-page-bar,
      .landing-section-head,
      .landing-cta-band{
        flex-direction:column;
        align-items:flex-start;
      }

      .landing-home-hero-bleed,
      .landing-home-hero{
        min-height:auto;
      }

      .landing-home-hero{
        padding:32px 0 34px;
      }

      .landing-feature-strip,
      .landing-department-showcase,
      .landing-grid.departments,
      .landing-grid.doctors,
      .landing-grid.hospitals,
      .landing-footer-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
      }

      .landing-hero-photo,
      .landing-about-photo{
        height:280px;
      }

      .landing-department-visual{
        width:138px;
        height:138px;
        margin-bottom:14px;
      }

      .landing-profile-head{
        flex-direction:column;
        align-items:flex-start;
      }

      .landing-profile-mini-grid{
        grid-template-columns:1fr;
      }

      .landing-profile-meta-row{
        grid-template-columns:1fr;
      }

      .landing-profile-actions{
        grid-template-columns:1fr;
      }

      .landing-search-form{
        max-width:none;
        flex-direction:column;
        background:transparent;
        border:0;
        box-shadow:none;
        border-radius:22px;
        overflow:visible;
        gap:10px;
      }

      .landing-search-input{
        background:#fff;
        border:1px solid rgba(17,56,92,.10);
        border-radius:18px;
        box-shadow:0 10px 20px rgba(15,50,81,.06);
      }

      .landing-search-submit{
        width:100%;
        border-radius:999px;
      }

      .landing-doctor-result .landing-avatar,
      .landing-doctor-result .landing-avatar-fallback{
        width:92px;
        height:92px;
        border-radius:24px;
      }

      .landing-hero-float{
        max-width:none;
        margin-left:0;
      }

      .landing-page-bar{padding:18px}
      .landing-cta-group .landing-btn{flex:1 1 auto}
      .landing-call-modal{
        align-items:flex-end;
        padding:12px;
      }
      .landing-call-card{
        border-radius:24px;
      }
      .landing-call-card.is-booking{
        max-height:calc(100vh - 24px);
      }
      .landing-call-card.is-booking .landing-call-card-body{
        padding:18px;
      }
      .landing-booking-choice-grid,
      .landing-booking-grid{
        grid-template-columns:minmax(0,1fr);
      }
      .landing-booking-field.is-span-2{
        grid-column:auto;
      }
      .landing-booking-footer{
        align-items:stretch;
      }
      .landing-booking-actions{
        width:100%;
        margin-left:0;
      }
      .landing-booking-actions .landing-btn{
        flex:1 1 0;
      }
    }

    @media (max-width: 575.98px){
      .landing-feature-strip,
      .landing-department-showcase,
      .landing-grid.departments,
      .landing-grid.doctors,
      .landing-grid.hospitals,
      .landing-footer-grid{
        grid-template-columns:1fr;
      }
    }
  </style>
  @stack('styles')
</head>
<body class="@yield('body_class')">
  <div class="landing-topbar">
    <div class="landing-shell">
      <div class="landing-topbar-inner">
        <div class="landing-topbar-group">
          <a href="{{ route('directory.home') }}" class="landing-topbar-item">
            <i class="fa-solid fa-phone-volume"></i>
            <span>Patient Helpdesk</span>
          </a>
          <span class="landing-topbar-item">
            <i class="fa-solid fa-envelope-open-text"></i>
            <span>support@legmed.test</span>
          </span>
          <span class="landing-topbar-item">
            <i class="fa-solid fa-clock"></i>
            <span>Mon - Sat: 8:00 AM to 8:00 PM</span>
          </span>
        </div>

        <div class="landing-socials">
          <a href="{{ route('directory.home') }}" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="{{ route('directory.home') }}" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="{{ route('directory.home') }}" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>

  <header class="landing-header">
    <div class="landing-shell">
      <div class="landing-navbar">
        <a href="{{ route('directory.home') }}" class="landing-brand">
          <img src="{{ asset('/assets/media/images/web/logo.png') }}" alt="LegMed">
          <span>
            LegMed Care
            <small>Doctors, Departments & Hospitals</small>
          </span>
        </a>

        <nav class="landing-nav-links">
          <a href="{{ route('directory.home') }}" class="{{ request()->routeIs('directory.home') ? 'is-active' : '' }}">Home</a>
          <a href="{{ route('directory.departments.index') }}" class="{{ request()->routeIs('directory.departments.*') ? 'is-active' : '' }}">Departments</a>
        </nav>

        <div class="landing-cta-group">
          <a href="{{ route('directory.departments.index') }}" class="landing-btn landing-btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span>Find Doctors</span>
          </a>
        </div>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  <footer class="landing-footer">
    <div class="landing-shell landing-footer-main">
      <div class="landing-footer-grid">
        <div>
          <a href="{{ route('directory.home') }}" class="landing-brand text-white mb-3 d-inline-flex">
            <img src="{{ asset('/assets/media/images/web/logo.png') }}" alt="LegMed">
            <span>
              LegMed Care
              <small style="color:rgba(255,255,255,.6)">Public Healthcare Discovery</small>
            </span>
          </a>
          <p class="mb-3">Browse doctors, departments, and hospital-backed care journeys through a cleaner public-facing healthcare directory built from your platform data.</p>
          <div class="landing-socials">
            <a href="{{ route('directory.home') }}" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="{{ route('directory.home') }}" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="{{ route('directory.home') }}" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          </div>
        </div>

        <div>
          <div class="landing-footer-title">Quick Links</div>
          <div class="landing-footer-list">
            <a href="{{ route('directory.home') }}">Home</a>
            <a href="{{ route('directory.departments.index') }}">Departments</a>
            <a href="{{ route('login') }}">Admin Login</a>
          </div>
        </div>

        <div>
          <div class="landing-footer-title">Browse Care</div>
          <div class="landing-footer-list">
            <span>Department-based doctor discovery</span>
            <span>Hospital-backed doctor cards</span>
            <span>Public doctor profile previews</span>
          </div>
        </div>

        <div>
          <div class="landing-footer-title">Contact Window</div>
          <div class="landing-footer-list">
            <span><i class="fa-solid fa-phone me-2"></i>Patient support desk</span>
            <span><i class="fa-solid fa-envelope me-2"></i>support@legmed.test</span>
            <span><i class="fa-solid fa-clock me-2"></i>Mon - Sat / 8 AM - 8 PM</span>
          </div>
        </div>
      </div>

      <div class="landing-footer-copy">
        <span>&copy; {{ date('Y') }} LegMed Directory. Public healthcare browse experience.</span>
        <span>Built inside your existing application.</span>
      </div>
    </div>
  </footer>
</body>
</html>
