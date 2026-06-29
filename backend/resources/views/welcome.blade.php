<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nagar Rakshak — Spot Hazards. Alert Citizens. Save Lives.</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Noto+Sans+Devanagari:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --green-950: #052E16;
  --green-900: #14532D;
  --green-800: #166534;
  --green-700: #15803D;
  --green-600: #16A34A;
  --green-500: #22C55E;
  --green-400: #4ADE80;
  --green-300: #86EFAC;
  --green-100: #DCFCE7;
  --green-50:  #F0FDF4;
  --danger:    #DC2626;
  --danger-s:  #FEF2F2;
  --amber:     #D97706;
  --amber-s:   #FFFBEB;
  --ink:       #0A0A0A;
  --paper:     #F4F9F5;
  --card:      #FFFFFF;
  --border:    #D1FAE5;
  --border-d:  #BBF7D0;
  --muted:     #6B7280;
  --rule:      #E5E7EB;
}

html{scroll-behavior:smooth}
body{
  background:var(--paper);
  color:var(--ink);
  font-family:'DM Sans',sans-serif;
  line-height:1.6;
  overflow-x:hidden;
}

/* ── MASTHEAD ── */
.masthead{
  background:var(--green-950);
  padding:0 48px;
  display:flex;align-items:stretch;
  border-bottom:3px solid var(--green-700);
  position:sticky;top:0;z-index:100;
}
.masthead-left{
  display:flex;align-items:center;gap:14px;
  padding:14px 0;
  border-right:1px solid var(--green-900);
  padding-right:32px;
}
.logo-mark{
  width:40px;height:40px;
  background:var(--green-700);
  border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  font-size:20px;
  flex-shrink:0;
  border:1px solid var(--green-600);
}
.brand-name{
  font-family:'Noto Sans Devanagari','DM Sans',sans-serif;
  font-size:20px;font-weight:700;
  color:#fff;letter-spacing:-0.3px;
  line-height:1.1;
}
.brand-sub{
  font-family:'DM Mono',monospace;
  font-size:9px;color:var(--green-400);
  letter-spacing:2.5px;text-transform:uppercase;
  margin-top:2px;
}
.masthead-nav{
  display:flex;align-items:center;gap:0;
  padding-left:32px;flex:1;
}
.nav-link{
  font-size:12px;font-weight:500;
  color:rgba(255,255,255,0.65);
  text-decoration:none;
  padding:0 18px;height:100%;
  display:flex;align-items:center;
  letter-spacing:0.3px;
  transition:color .2s;
  border-right:1px solid var(--green-900);
}
.nav-link:hover,.nav-link.active{color:#fff;}
.nav-link.active{color:var(--green-400);}
.masthead-right{
  display:flex;align-items:center;gap:12px;
  padding-left:24px;
  margin-left:auto;
}
.live-pill{
  display:flex;align-items:center;gap:6px;
  background:rgba(22,163,74,0.15);
  border:1px solid var(--green-700);
  border-radius:100px;
  padding:5px 12px;
}
.live-dot{
  width:6px;height:6px;
  background:var(--green-500);
  border-radius:50%;
  animation:blink 1.4s ease infinite;
}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.live-text{
  font-family:'DM Mono',monospace;
  font-size:9px;color:var(--green-400);
  letter-spacing:2px;text-transform:uppercase;
}
.masthead-date{
  font-family:'DM Mono',monospace;
  font-size:10px;color:rgba(255,255,255,.35);
  letter-spacing:.5px;
}

/* ── TICKER ── */
.ticker{
  background:var(--green-800);
  padding:9px 0;
  overflow:hidden;white-space:nowrap;
  border-bottom:1px solid var(--green-700);
}
.ticker-track{
  display:inline-flex;
  animation:scroll-left 60s linear infinite;
}
.ticker-track:hover{animation-play-state:paused}
.t-item{
  display:inline-flex;align-items:center;gap:10px;
  margin:0 40px;
  font-family:'DM Mono',monospace;
  font-size:11px;color:rgba(255,255,255,.9);
  letter-spacing:.8px;text-transform:uppercase;
}
.t-sep{width:4px;height:4px;background:var(--green-400);border-radius:50%;flex-shrink:0;}
.t-label{
  background:var(--danger);color:#fff;
  padding:1px 6px;border-radius:2px;
  font-size:9px;font-weight:600;letter-spacing:1px;
}
.t-label.amber{background:var(--amber);}
.t-label.green{background:var(--green-600);}
@keyframes scroll-left{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

/* ── HERO ── */
.hero{
  background:var(--green-950);
  color:#fff;
  padding:90px 48px 72px;
  position:relative;overflow:hidden;
}
.hero-grid-bg{
  position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(34,197,94,.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(34,197,94,.04) 1px,transparent 1px);
  background-size:48px 48px;
  pointer-events:none;
}
.hero-glow{
  position:absolute;
  width:600px;height:600px;
  background:radial-gradient(circle,rgba(22,163,74,.18) 0%,transparent 70%);
  right:-100px;top:-100px;
  pointer-events:none;
}
.hero-inner{max-width:1200px;margin:0 auto;position:relative;}
.hero-eyebrow{
  display:inline-flex;align-items:center;gap:10px;
  font-family:'DM Mono',monospace;
  font-size:10px;font-weight:500;
  letter-spacing:3px;text-transform:uppercase;
  color:var(--green-400);
  margin-bottom:24px;
  padding:6px 14px;
  border:1px solid var(--green-800);
  border-radius:2px;
  background:rgba(22,163,74,.08);
}
.hero-eyebrow-dot{width:6px;height:6px;background:var(--green-500);border-radius:50%;}
.hero-hl{
  font-family:'Playfair Display',serif;
  font-size:clamp(44px,5.8vw,92px);
  font-weight:900;line-height:.95;
  letter-spacing:-2.5px;
  margin-bottom:6px;
}
.hero-hl .hi{color:var(--green-500);}
.hero-hl .devanagari{
  font-family:'Noto Sans Devanagari',serif;
  display:block;
  font-size:clamp(26px,3.2vw,48px);
  font-weight:700;
  color:var(--green-300);
  letter-spacing:-0.5px;
  margin-top:10px;
}
.hero-body{
  font-size:18px;color:rgba(255,255,255,.8);
  max-width:680px;margin:32px 0 48px;
  line-height:1.75;
}
.hero-body strong{color:#fff;font-weight:700;}

/* ── MANIFESTO BANNER ── */
.manifesto-card{
  background:linear-gradient(135deg, rgba(20,83,45,0.7) 0%, rgba(5,46,22,0.9) 100%);
  border:1px solid var(--green-700);
  border-left:5px solid var(--green-400);
  border-radius:8px;
  padding:32px;
  margin-bottom:56px;
  box-shadow:0 20px 50px rgba(0,0,0,0.3);
  position:relative;
}
.manifesto-quote{
  font-family:'Playfair Display',serif;
  font-size:clamp(20px,2.2vw,28px);
  font-style:italic;
  color:#fff;
  line-height:1.45;
  margin-bottom:16px;
}
.manifesto-quote span{color:var(--green-400);font-style:normal;font-weight:700;}
.manifesto-sub{
  font-size:15px;color:rgba(255,255,255,0.75);
  line-height:1.65;
}

/* ── HERO STAT GRID ── */
.stat-grid{
  display:grid;grid-template-columns:repeat(4,1fr);
  border:1px solid var(--green-900);
  max-width:900px;
}
.stat-cell{
  padding:28px 24px;
  border-right:1px solid var(--green-900);
  position:relative;
  background:rgba(255,255,255,.02);
  transition:background .2s;
}
.stat-cell:last-child{border-right:none;}
.stat-cell:hover{background:rgba(34,197,94,.04);}
.stat-num{
  font-family:'DM Mono',monospace;
  font-size:38px;font-weight:500;
  line-height:1;margin-bottom:8px;
  color:#fff;
}
.stat-num.g{color:var(--green-500);}
.stat-num.r{color:var(--danger);}
.stat-num.a{color:var(--amber);}
.stat-label{font-size:11px;color:rgba(255,255,255,.5);line-height:1.4;letter-spacing:.3px;}
.stat-cell::after{
  content:'';position:absolute;
  bottom:0;left:0;right:0;height:2px;
  background:transparent;
  transition:background .2s;
}
.stat-cell:hover::after{background:var(--green-700);}

/* ── SECTION SHELL ── */
.sec{padding:88px 48px;}
.sec-inner{max-width:1200px;margin:0 auto;}
.sec-hd{
  display:flex;align-items:flex-end;
  gap:20px;margin-bottom:52px;
  padding-bottom:18px;
  border-bottom:2px solid var(--green-950);
}
.sec-eyebrow{
  font-family:'DM Mono',monospace;
  font-size:9px;font-weight:500;
  letter-spacing:3px;text-transform:uppercase;
  color:var(--green-700);margin-bottom:6px;
}
.sec-title{
  font-family:'Playfair Display',serif;
  font-size:clamp(26px,3vw,38px);
  font-weight:700;line-height:1.1;
  color:var(--ink);
}
.sec-rule{flex:1;height:1px;background:var(--rule);margin-bottom:6px;}

/* ── ACCIDENT CARDS ── */
.accident-grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:1px;background:#D1FAE5;
  border:1px solid #D1FAE5;
}
.ac{
  background:#fff;
  display:flex;flex-direction:column;
  transition:transform .18s,box-shadow .18s;
  position:relative;
}
.ac:hover{transform:translateY(-3px);box-shadow:0 12px 40px rgba(22,101,52,.12);z-index:1;}

.ac-img-wrap{position:relative;overflow:hidden;flex-shrink:0;}
.ac-img-inner{
  width:100%;height:210px;
  display:flex;align-items:center;justify-content:center;
  font-size:56px;
  position:relative;
}
.ac-img-inner.pothole{background:linear-gradient(145deg,#1a1a1a 0%,#2d1a1a 100%);}
.ac-img-inner.water{background:linear-gradient(145deg,#0c1a2d 0%,#1a2d3d 100%);}
.ac-img-inner.light{background:linear-gradient(145deg,#1a1a0a 0%,#2d2d1a 100%);}
.ac-img-inner.drain{background:linear-gradient(145deg,#1a2d1a 0%,#0f1f0f 100%);}
.ac-img-inner.construct{background:linear-gradient(145deg,#2d1f0a 0%,#1a1205 100%);}
.ac-img-inner.collapse{background:linear-gradient(145deg,#251a2d 0%,#160f1c 100%);}

.ac-loc-tag{
  position:absolute;bottom:12px;left:12px;
  background:rgba(0,0,0,0.75);color:#fff;
  font-family:'DM Mono',monospace;
  font-size:10px;padding:4px 10px;border-radius:3px;
  backdrop-filter:blur(4px);
}
.ac-sev-bar{height:4px;width:100%;}
.ac-sev-bar.sev-h{background:var(--danger);}
.ac-sev-bar.sev-m{background:var(--amber);}

.ac-body{padding:24px;display:flex;flex-direction:column;flex:1;}
.ac-tags{display:flex;gap:6px;margin-bottom:12px;flex-wrap:wrap;}
.tag{
  font-family:'DM Mono',monospace;
  font-size:9px;font-weight:600;
  padding:2px 8px;border-radius:2px;
  letter-spacing:.5px;text-transform:uppercase;
}
.tag-h{background:var(--danger-s);color:var(--danger);border:1px solid rgba(220,38,38,.2);}
.tag-m{background:var(--amber-s);color:var(--amber);border:1px solid rgba(217,119,6,.2);}
.tag-type{background:#F3F4F6;color:#374151;}
.tag-city{background:var(--green-50);color:var(--green-700);border:1px solid var(--border-d);}

.ac-title{
  font-family:'Playfair Display',serif;
  font-size:18px;font-weight:700;
  line-height:1.3;color:var(--ink);
  margin-bottom:12px;
}
.ac-desc{font-size:13px;color:var(--muted);line-height:1.65;margin-bottom:20px;flex:1;}
.ac-foot{
  display:flex;align-items:center;justify-content:space-between;
  padding-top:16px;border-top:1px solid var(--rule);
  margin-top:auto;
}
.ac-addr{font-size:11px;font-weight:600;color:#374151;}
.ac-date{font-family:'DM Mono',monospace;font-size:9px;color:#9CA3AF;margin-top:2px;}
.casualty{
  font-family:'DM Mono',monospace;
  font-size:11px;font-weight:700;
  color:var(--danger);background:var(--danger-s);
  padding:4px 10px;border-radius:4px;border:1px solid rgba(220,38,38,.2);
}

/* ── HAZARD GRID ── */
.haz-grid{
  display:grid;grid-template-columns:repeat(3,1fr);gap:24px;
}
.haz-card{
  background:#fff;border:1px solid var(--border-d);
  padding:28px;border-radius:6px;
  box-shadow:0 2px 8px rgba(0,0,0,0.02);
  transition:transform .2s,box-shadow .2s;
}
.haz-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(22,163,74,0.08);}
.haz-icon{font-size:32px;display:block;margin-bottom:12px;}
.haz-name{font-weight:700;font-size:16px;color:var(--ink);margin-bottom:4px;}
.haz-count{font-family:'DM Mono',monospace;font-size:24px;font-weight:500;color:var(--green-700);margin-bottom:2px;}
.haz-pct{font-size:11px;color:var(--muted);margin-bottom:16px;}
.haz-bar{height:6px;background:#E5E7EB;border-radius:100px;overflow:hidden;}
.haz-fill{height:100%;border-radius:100px;transition:width 1s ease;}

/* ── QUOTE BLOCK ── */
.quote-sec{
  background:var(--green-950);color:#fff;
  padding:72px 48px;text-align:center;
  border-top:1px solid var(--green-900);
  border-bottom:1px solid var(--green-900);
}
.quote-text{
  font-family:'Playfair Display',serif;
  font-size:clamp(22px,3vw,34px);
  font-weight:700;line-height:1.35;
  max-width:860px;margin:0 auto 20px;
}
.quote-text em{color:var(--green-400);font-style:normal;}
.quote-src{
  font-family:'DM Mono',monospace;
  font-size:11px;color:rgba(255,255,255,.4);
  letter-spacing:2px;text-transform:uppercase;
}

/* ── TIMELINE ── */
.timeline{display:flex;flex-direction:column;gap:0;}
.tl-item{
  display:grid;
  grid-template-columns:100px 1px 1fr;
  gap:0 24px;
  position:relative;
}
.tl-year{
  font-family:'DM Mono',monospace;
  font-size:24px;font-weight:500;
  color:var(--green-700);
  text-align:right;
  padding:28px 0;
  line-height:1;
}
.tl-line{
  background:var(--border);
  position:relative;
}
.tl-dot{
  position:absolute;top:32px;left:50%;
  transform:translate(-50%,-50%);
  width:12px;height:12px;
  background:var(--green-600);
  border:2px solid #fff;
  border-radius:50%;
  box-shadow:0 0 0 3px var(--border);
}
.tl-content{
  padding:24px 0 24px;
  border-bottom:1px solid var(--rule);
}
.tl-item:last-child .tl-content{border-bottom:none;}
.tl-stat{
  font-family:'DM Mono',monospace;
  font-size:28px;font-weight:500;
  color:var(--danger);line-height:1;
  margin-bottom:6px;
}
.tl-desc{font-size:13px;color:var(--muted);line-height:1.6;}
.tl-source{
  font-family:'DM Mono',monospace;
  font-size:9px;color:#CBD5E1;
  letter-spacing:1px;margin-top:8px;
  text-transform:uppercase;
}

/* ── PRESS / NEWS ── */
.news-sec{background:var(--green-950);color:#fff;}
.news-sec .sec-title{color:#fff;}
.news-sec .sec-hd{border-bottom-color:var(--green-800);}
.news-layout{display:grid;grid-template-columns:1.2fr 1fr;gap:40px;}
.news-main{
  background:rgba(255,255,255,.03);
  border:1px solid var(--green-900);
  padding:36px;border-radius:6px;
}
.news-src{
  font-family:'DM Mono',monospace;
  font-size:10px;letter-spacing:2px;
  text-transform:uppercase;
  color:var(--green-400);
  margin-bottom:12px;
  display:flex;align-items:center;gap:10px;
}
.news-src::after{content:'';flex:1;height:1px;background:var(--green-900);}
.news-hl{
  font-family:'Playfair Display',serif;
  font-weight:700;line-height:1.25;
  color:#fff;margin-bottom:14px;
}
.news-main .news-hl{font-size:clamp(20px,2.2vw,28px);}
.news-item .news-hl{font-size:15px;}
.news-body{font-size:13px;color:rgba(255,255,255,.6);line-height:1.8;}
.news-date{
  margin-top:16px;
  font-family:'DM Mono',monospace;
  font-size:9px;color:rgba(255,255,255,.3);
  letter-spacing:1px;
}
.news-divider{width:32px;height:2px;background:var(--green-700);margin:14px 0;}
.hindi-hl{font-family:'Noto Sans Devanagari',sans-serif;font-weight:700;}
.news-col{display:flex;flex-direction:column;gap:20px;}
.news-item{
  background:rgba(255,255,255,.02);
  border:1px solid var(--green-900);
  padding:24px;border-radius:6px;
}

/* ── CTA ── */
.cta-sec{
  background:var(--green-800);
  padding:96px 48px;
  text-align:center;
  position:relative;overflow:hidden;
}
.cta-sec::before{
  content:'';position:absolute;inset:0;
  background:
    radial-gradient(circle at 20% 50%,rgba(5,46,22,.6) 0%,transparent 60%),
    radial-gradient(circle at 80% 50%,rgba(5,46,22,.6) 0%,transparent 60%);
  pointer-events:none;
}
.cta-inner{position:relative;max-width:800px;margin:0 auto;}
.cta-badge{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(255,255,255,.1);
  border:1px solid rgba(255,255,255,.2);
  border-radius:100px;padding:6px 16px;
  font-family:'DM Mono',monospace;
  font-size:10px;color:rgba(255,255,255,.9);
  letter-spacing:2px;text-transform:uppercase;
  margin-bottom:28px;
}
.cta-title{
  font-family:'Playfair Display',serif;
  font-size:clamp(34px,5vw,64px);
  font-weight:900;line-height:1.05;
  color:#fff;margin-bottom:10px;
  letter-spacing:-1px;
}
.cta-title-h{
  font-family:'Noto Sans Devanagari',serif;
  display:block;
  font-size:clamp(22px,2.8vw,38px);
  font-weight:700;
  color:var(--green-300);
  margin-top:6px;letter-spacing:0;
}
.cta-body{
  font-size:17px;color:rgba(255,255,255,.85);
  max-width:640px;margin:24px auto 48px;
  line-height:1.75;
}
.cta-btns{display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;}
.btn-p{
  background:#fff;color:var(--green-800);
  padding:16px 36px;border-radius:6px;
  font-size:15px;font-weight:700;
  border:none;cursor:pointer;text-decoration:none;
  display:inline-flex;align-items:center;gap:8px;
  transition:opacity .18s,transform .18s;
  letter-spacing:.3px;box-shadow:0 10px 30px rgba(0,0,0,0.2);
}
.btn-p:hover{opacity:.92;transform:translateY(-1px);}
.btn-s{
  background:transparent;color:#fff;
  padding:16px 36px;border-radius:6px;
  font-size:15px;font-weight:600;
  border:2px solid rgba(255,255,255,.5);
  cursor:pointer;text-decoration:none;
  display:inline-flex;align-items:center;gap:8px;
  transition:border-color .18s,transform .18s;
}
.btn-s:hover{border-color:#fff;transform:translateY(-1px);}

/* ── FOOTER ── */
footer{
  background:var(--green-950);
  border-top:1px solid var(--green-900);
  padding:36px 48px;
  display:flex;align-items:center;justify-content:space-between;
  flex-wrap:wrap;gap:16px;
}
.foot-brand{display:flex;align-items:center;gap:10px;}
.foot-brand-name{
  font-family:'Noto Sans Devanagari','DM Sans',sans-serif;
  font-size:15px;font-weight:700;color:#fff;
}
.foot-copy{
  font-family:'DM Mono',monospace;
  font-size:10px;color:rgba(255,255,255,.35);
  letter-spacing:.5px;
  line-height:1.8;
}
.foot-links{display:flex;gap:20px;}
.foot-links a{
  font-family:'DM Mono',monospace;
  font-size:10px;color:var(--green-500);
  text-decoration:none;letter-spacing:.5px;
  transition:color .15s;
}
.foot-links a:hover{color:var(--green-400);}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .masthead{padding:0 20px;}
  .masthead-nav{display:none;}
  .hero{padding:60px 24px 48px;}
  .stat-grid{grid-template-columns:repeat(2,1fr);}
  .sec{padding:60px 24px;}
  .accident-grid{grid-template-columns:1fr 1fr;}
  .haz-grid{grid-template-columns:repeat(2,1fr);}
  .news-layout{grid-template-columns:1fr;}
  .cta-sec{padding:64px 24px;}
  footer{padding:28px 24px;flex-direction:column;text-align:center;}
}
@media(max-width:600px){
  .accident-grid{grid-template-columns:1fr;}
  .haz-grid{grid-template-columns:1fr;}
  .stat-grid{grid-template-columns:1fr 1fr;}
}

/* ── SCROLL REVEAL ── */
.reveal{opacity:0;transform:translateY(24px);transition:opacity .6s ease,transform .6s ease;}
.reveal.visible{opacity:1;transform:none;}
</style>
</head>
<body>

<!-- ── MASTHEAD ── -->
<header class="masthead">
  <div class="masthead-left">
    <div class="logo-mark">🛡️</div>
    <div>
      <div class="brand-name">नगर रक्षक</div>
      <div class="brand-sub">Nagar Rakshak · Citizens Life Safety</div>
    </div>
  </div>
  <nav class="masthead-nav">
    <a href="#manifesto" class="nav-link active">Our Mission</a>
    <a href="#incidents" class="nav-link">Documented Cases</a>
    <a href="#breakdown" class="nav-link">Hazard Types</a>
    <a href="#news" class="nav-link">Press &amp; Data</a>
    <a href="#download" class="nav-link">Join Movement</a>
  </nav>
  <div class="masthead-right">
    <div class="live-pill"><span class="live-dot"></span><span class="live-text">Community Network Active</span></div>
    <div class="masthead-date" id="hdr-date"></div>
  </div>
</header>

<!-- ── TICKER ── -->
<div class="ticker">
  <div class="ticker-track">
    <span class="t-item"><span class="t-sep"></span><span class="t-label">LIFE SAVED</span>Alert Broadcast Avoided Night Collision on Unlit Road — Kota 2025</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">COMMUNITY ALERT</span>Open Drain Flagged by Citizen Near School Gate — Lucknow</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label green">VICTORY</span>If We Save Even One Life, We Have Won — Join Nagar Rakshak</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label">HAZARD SPOTTED</span>Killer Pothole Reported on Aerodrome Road — Instant Push Broadcast Sent</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">NCRB DATA</span>19,500+ Lives Lost Annually to Untreated Road Hazards</span>
    <!-- duplicate for seamless loop -->
    <span class="t-item"><span class="t-sep"></span><span class="t-label">LIFE SAVED</span>Alert Broadcast Avoided Night Collision on Unlit Road — Kota 2025</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">COMMUNITY ALERT</span>Open Drain Flagged by Citizen Near School Gate — Lucknow</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label green">VICTORY</span>If We Save Even One Life, We Have Won — Join Nagar Rakshak</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label HAZARD SPOTTED</span>Killer Pothole Reported on Aerodrome Road — Instant Push Broadcast Sent</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">NCRB DATA</span>19,500+ Lives Lost Annually to Untreated Road Hazards</span>
  </div>
</div>

<!-- ── HERO ── -->
<section class="hero">
  <div class="hero-grid-bg"></div>
  <div class="hero-glow"></div>
  <div class="hero-inner">
    <div class="hero-eyebrow"><span class="hero-eyebrow-dot"></span>Organic Citizen Movement · Life Safety Protection Network</div>
    <h1 class="hero-hl">
      We Don't Have Power to Fix Roads.<br><span class="hi">But We Have Power to Save Lives.</span>
      <span class="devanagari">हम सरकार नहीं हैं, लेकिन हम जानें बचा सकते हैं।</span>
    </h1>
    <p class="hero-body">
      We are not municipal authorities. We don't hold government budgets or repair trucks.
      But we refuse to stay helpless while <strong>potholes, dark streetlights, and open drains</strong> claim innocent lives every single day.
      By alerting fellow citizens in real-time, we skip accidents before they happen.
    </p>

    <!-- MANIFESTO CARD -->
    <div class="manifesto-card" id="manifesto">
      <div class="manifesto-quote">
        "We can't fix these local hazards ourselves... but at least we can <span>alert someone and save their life</span> by warning them before impact. If our community alerts save even ONE single life, then <span>we have won.</span>"
      </div>
      <div class="manifesto-sub">
        🛡️ <strong>Join our citizen safety network.</strong> Spot local hazards, file instant mobile reports, and send real-time geo-radius warnings to nearby drivers, mothers, fathers, and students. Together, let me help everyone live securely.
      </div>
    </div>

    <div class="stat-grid">
      <div class="stat-cell">
        <div class="stat-num r">19,500+</div>
        <div class="stat-label">Annual deaths from potholes &amp; road hazards — NCRB</div>
      </div>
      <div class="stat-cell">
        <div class="stat-num a">1,264</div>
        <div class="stat-label">Road accidents recorded every single day in India</div>
      </div>
      <div class="stat-cell">
        <div class="stat-num g">1 Life Saved</div>
        <div class="stat-label">Our core metric: If one accident is prevented, we win</div>
      </div>
      <div class="stat-cell">
        <div class="stat-num">100% Free</div>
        <div class="stat-label">Open citizen community platform for public safety</div>
      </div>
    </div>
  </div>
</section>

<!-- ── ACCIDENT INCIDENTS ── -->
<section class="sec" id="incidents">
  <div class="sec-inner">
    <div class="sec-hd reveal">
      <div class="sec-hd-text">
        <div class="sec-eyebrow">Why We Must Alert Each Other · Documented Tragedies</div>
        <h2 class="sec-title">Accidents We Could Have Prevented With a Timely Alert</h2>
      </div>
      <div class="sec-rule"></div>
    </div>

    <div class="accident-grid">

      <!-- CARD 1 — Kota Pothole -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          @if($sc1 = \App\Services\SettingsService::get('showcase_1_image'))
            <img src="{{ $sc1 }}" style="width:100%;height:210px;object-fit:cover;display:block;">
          @else
            <div class="ac-img-inner pothole"><span class="emoji">🕳️</span></div>
          @endif
          <div class="ac-loc-tag">📍 {{ \App\Services\SettingsService::get('showcase_1_location', 'Kota, Rajasthan') }}</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Pothole</span>
            <span class="tag tag-city">Kota</span>
          </div>
          <h3 class="ac-title">{{ \App\Services\SettingsService::get('showcase_1_title', 'Biker Killed Hitting Unmarked Pothole on Aerodrome Road') }}</h3>
          <p class="ac-desc">A 31-year-old coaching student died after his motorcycle hit a 14-inch pothole on Aerodrome Road at 11 PM. The stretch had no streetlights. If a nearby citizen had flagged this hazard in Nagar Rakshak, nearby night commuters would have received an automated warning to slow down and avoid the lane.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 Aerodrome Road, Vigyan Nagar, Kota</div>
              <div class="ac-date">October 2024 · Rajasthan Patrika</div>
            </div>
            <div class="casualty">⚠ 1 Killed</div>
          </div>
        </div>
      </div>

      <!-- CARD 2 — Pune Open Drain -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          @if($sc2 = \App\Services\SettingsService::get('showcase_2_image'))
            <img src="{{ $sc2 }}" style="width:100%;height:210px;object-fit:cover;display:block;">
          @else
            <div class="ac-img-inner drain"><span class="emoji">🚧</span></div>
          @endif
          <div class="ac-loc-tag">📍 {{ \App\Services\SettingsService::get('showcase_2_location', 'Pune, Maharashtra') }}</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Open Drain</span>
            <span class="tag tag-city">Pune</span>
          </div>
          <h3 class="ac-title">{{ \App\Services\SettingsService::get('showcase_2_title', 'Three Killed as Car Plunges into Uncovered Municipal Drain') }}</h3>
          <p class="ac-desc">A car carrying four persons fell into an uncovered 12-foot deep drain at 2 AM during heavy rain. The drain's cover was missing for weeks with zero barricading. Real-time geo-radius notifications empower citizens to mark open drains immediately so drivers during heavy rains get alerted before plunging into traps.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 Kondhwa Road, Pune, Maharashtra</div>
              <div class="ac-date">June 2024 · Times of India</div>
            </div>
            <div class="casualty">⚠ 3 Killed</div>
          </div>
        </div>
      </div>

      <!-- CARD 3 — Mumbai Pothole Chaos -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          @if($sc3 = \App\Services\SettingsService::get('showcase_3_image'))
            <img src="{{ $sc3 }}" style="width:100%;height:210px;object-fit:cover;display:block;">
          @else
            <div class="ac-img-inner pothole"><span class="emoji">🛣️</span></div>
          @endif
          <div class="ac-loc-tag">📍 {{ \App\Services\SettingsService::get('showcase_3_location', 'Mumbai, Maharashtra') }}</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Pothole</span>
            <span class="tag tag-city">Mumbai</span>
          </div>
          <h3 class="ac-title">{{ \App\Services\SettingsService::get('showcase_3_title', '11 Accidents on Single Andheri Stretch in 30 Days') }}</h3>
          <p class="ac-desc">A 400-metre pothole-riddled stretch on Andheri–Kurla Road recorded 11 collisions in one month. Civic authorities took 4 months to respond, but a community network broadcasting live warnings allows commuters to navigate around dangerous craters safely.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 Andheri–Kurla Road, Mumbai</div>
              <div class="ac-date">August 2023 · Hindustan Times</div>
            </div>
            <div class="casualty">⚠ 14 Injured</div>
          </div>
        </div>
      </div>

      <!-- CARD 4 — Bengaluru Road Collapse -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          @if($sc4 = \App\Services\SettingsService::get('showcase_4_image'))
            <img src="{{ $sc4 }}" style="width:100%;height:210px;object-fit:cover;display:block;">
          @else
            <div class="ac-img-inner collapse"><span class="emoji">🏗️</span></div>
          @endif
          <div class="ac-loc-tag">📍 {{ \App\Services\SettingsService::get('showcase_4_location', 'Bengaluru, Karnataka') }}</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Road Collapse</span>
            <span class="tag tag-city">Bengaluru</span>
          </div>
          <h3 class="ac-title">{{ \App\Services\SettingsService::get('showcase_4_title', 'BMTC Bus Partially Swallowed by Road Sinkhole on ORR') }}</h3>
          <p class="ac-desc">A passenger bus sank into a massive sinkhole on Outer Ring Road. Citizens had noticed road bulging months prior. Community reporting bridges the gap between early warning signs and municipal intervention, keeping commuters informed.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 Outer Ring Road, Marathahalli, Bengaluru</div>
              <div class="ac-date">September 2023 · The Hindu</div>
            </div>
            <div class="casualty">⚠ 7 Injured</div>
          </div>
        </div>
      </div>

      <!-- CARD 5 — Delhi Waterlogging -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          @if($sc5 = \App\Services\SettingsService::get('showcase_5_image'))
            <img src="{{ $sc5 }}" style="width:100%;height:210px;object-fit:cover;display:block;">
          @else
            <div class="ac-img-inner water"><span class="emoji">🌊</span></div>
          @endif
          <div class="ac-loc-tag">📍 {{ \App\Services\SettingsService::get('showcase_5_location', 'New Delhi') }}</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Waterlogging</span>
            <span class="tag tag-city">Delhi</span>
          </div>
          <h3 class="ac-title">{{ \App\Services\SettingsService::get('showcase_5_title', 'Man Electrocuted Walking Through Flooded Underpass') }}</h3>
          <p class="ac-desc">A 28-year-old man stepped into floodwater where a live streetlight pole had fallen. When waterlogging or electrical hazards occur, immediate citizen broadcast alerts warn pedestrians to avoid submerged underpasses.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 Minto Road Underpass, New Delhi</div>
              <div class="ac-date">July 2023 · NDTV / Times of India</div>
            </div>
            <div class="casualty">⚠ 1 Killed</div>
          </div>
        </div>
      </div>

      <!-- CARD 6 — Lucknow Drain Child -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          @if($sc6 = \App\Services\SettingsService::get('showcase_6_image'))
            <img src="{{ $sc6 }}" style="width:100%;height:210px;object-fit:cover;display:block;">
          @else
            <div class="ac-img-inner drain"><span class="emoji">⚠️</span></div>
          @endif
          <div class="ac-loc-tag">📍 {{ \App\Services\SettingsService::get('showcase_6_location', 'Lucknow, UP') }}</div>
        </div>
        <div class="ac-sev-bar sev-m"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-m">Medium Severity</span>
            <span class="tag tag-type">Open Drain</span>
            <span class="tag tag-city">Lucknow</span>
          </div>
          <h3 class="ac-title">{{ \App\Services\SettingsService::get('showcase_6_title', '9-Year-Old Girl Falls into Drain Outside School') }}</h3>
          <p class="ac-desc">A Class 4 student fell into an uncovered drain outside her school gate. The cover was missing for 11 weeks. Spotting hazards and alerting parents in neighborhood groups protects young children on their daily school walk.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 Gomti Nagar, Lucknow, Uttar Pradesh</div>
              <div class="ac-date">March 2025 · Dainik Jagran</div>
            </div>
            <div class="casualty">⚠ 1 Injured</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ── QUOTE BLOCK ── -->
<div class="quote-sec">
  <p class="quote-text">
    "We may not have the power to pave roads overnight, but by <em>alerting our community</em>, we give every citizen the power to dodge accidents and return home safely to their families."
  </p>
  <div class="quote-src">Nagar Rakshak Community Pledge · Protect Every Life</div>
</div>

<!-- ── HAZARD BREAKDOWN ── -->
<section class="sec" id="breakdown" style="background:#fff;">
  <div class="sec-inner">
    <div class="sec-hd reveal">
      <div class="sec-hd-text">
        <div class="sec-eyebrow">Spot &amp; File Report · Everyday Life Threats</div>
        <h2 class="sec-title">Common Civic Hazards We Alert Against</h2>
      </div>
      <div class="sec-rule"></div>
    </div>
    <div class="haz-grid">
      <div class="haz-card hc-pothole reveal">
        <span class="haz-icon">🕳️</span>
        <div class="haz-name">Killer Potholes</div>
        <div class="haz-count">52% Risk</div>
        <div class="haz-pct">Leading cause of two-wheeler night accidents</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#DC2626" data-w="100"></div></div>
      </div>
      <div class="haz-card hc-water reveal">
        <span class="haz-icon">🌊</span>
        <div class="haz-name">Waterlogging &amp; Floods</div>
        <div class="haz-count">24% Risk</div>
        <div class="haz-pct">Submerged obstacles &amp; live wire danger</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#2563EB" data-w="46"></div></div>
      </div>
      <div class="haz-card hc-light reveal">
        <span class="haz-icon">💡</span>
        <div class="haz-name">Dark Unlit Streets</div>
        <div class="haz-count">15% Risk</div>
        <div class="haz-pct">Failed streetlights causing blindspot crashes</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#D97706" data-w="30"></div></div>
      </div>
      <div class="haz-card hc-drain reveal">
        <span class="haz-icon">🚧</span>
        <div class="haz-name">Open Drains &amp; Manholes</div>
        <div class="haz-count">16% Risk</div>
        <div class="haz-pct">Unmarked deep pits near schools &amp; roads</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#166534" data-w="32"></div></div>
      </div>
      <div class="haz-card hc-construct reveal">
        <span class="haz-icon">⚠️</span>
        <div class="haz-name">Unmarked Debris</div>
        <div class="haz-count">20% Risk</div>
        <div class="haz-pct">Construction rubble left without reflectors</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#EA580C" data-w="40"></div></div>
      </div>
      <div class="haz-card hc-collapse reveal">
        <span class="haz-icon">🏗️</span>
        <div class="haz-name">Road Sinkholes</div>
        <div class="haz-count">12% Risk</div>
        <div class="haz-pct">Structurally cave-in road risk zones</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#7C3AED" data-w="24"></div></div>
      </div>
    </div>
  </div>
</section>

<!-- ── PRESS / NEWS ── -->
<section class="sec news-sec" id="news">
  <div class="sec-inner">
    <div class="sec-hd reveal">
      <div class="sec-hd-text">
        <div class="sec-eyebrow">National Reality · Official Press Coverage</div>
        <h2 class="sec-title">The Urgency of Citizen Safety Alerts</h2>
      </div>
      <div class="sec-rule"></div>
    </div>
    <div class="news-layout reveal">

      <!-- FEATURED -->
      <div class="news-main">
        <div class="news-src">Times of India · National Audit</div>
        <div class="news-divider"></div>
        <h2 class="news-hl">19,500 Deaths in a Year: Why Community Alert Systems Are Essential</h2>
        <div class="news-divider"></div>
        <p class="news-body">Analysis of NCRB data reveals that potholes and unlit civic hazards killed more Indians than natural floods and cyclones combined. While municipal repair pipelines take weeks or months, organic citizen warning networks provide immediate real-time protection. "When citizens alert each other about an open manhole or deep crater, they create an immediate shield for drivers and pedestrians," experts note.</p>
        <div class="news-date">September 18, 2022 · Page 1, Times of India</div>
      </div>

      <!-- SIDEBAR ITEMS -->
      <div class="news-col">
        <div class="news-item">
          <div class="news-src">Rajasthan Patrika · Kota</div>
          <h3 class="news-hl hindi-hl">गड्ढे में गिरकर युवक की मौत, परिवार ने उठाई सड़क सुरक्षा की आवाज</h3>
          <p class="news-body">कोटा में एयरोड्रोम रोड पर एक बड़े गड्ढे में बाइक गिरने से हादसा हुआ। समय पर सूचना और चेतावनी मिलने से ऐसे हादसों को रोका जा सकता है।</p>
          <div class="news-date">October 14, 2024 · Rajasthan Patrika, Kota Edition</div>
        </div>
        <div class="news-item">
          <div class="news-src">The Hindu · Urban Safety</div>
          <h3 class="news-hl">How Geo-Radius Mobile Alerts Prevent Night Collisions</h3>
          <p class="news-body">Studies show that 68% of dark streetlight and open drain accidents occur because commuters have zero advance warning. Geo-targeted alerts give drivers crucial seconds to slow down.</p>
          <div class="news-date">September 28, 2023 · The Hindu</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ── -->
<section class="cta-sec" id="download">
  <div class="cta-inner">
    <div class="cta-badge">🛡️ Join the Citizen Guard · Download App Free</div>
    <h2 class="cta-title">
      Spot the Hazard. File a Report.<br>Save a Life Today.
      <span class="cta-title-h">एक रिपोर्ट, एक जान बचा सकती है।</span>
    </h2>
    <p class="cta-body">
      We cannot fix the roads ourselves, but by filing a 30-second report, your alert immediately broadcasts to fellow citizens nearby. If we save even one life, we have won.
    </p>
    <div class="cta-btns">
      <a href="/admin/dashboard" class="btn-p">🛡️ Launch Intelligence Portal</a>
      <a href="#manifesto" class="btn-s">📖 Read Community Pledge</a>
    </div>
  </div>
</section>

<!-- ── FOOTER ── -->
<footer>
  <div class="foot-brand">
    <div class="logo-mark" style="width:32px;height:32px;font-size:16px;">🛡️</div>
    <div>
      <div class="foot-brand-name">नगर रक्षक</div>
      <div style="font-family:'DM Mono',monospace;font-size:9px;color:rgba(255,255,255,.35);letter-spacing:1.5px;text-transform:uppercase;margin-top:2px;">Techplanet Club · University of Kota</div>
    </div>
  </div>
  <div class="foot-copy">
    Dedicated to saving citizen lives through real-time hazard alerts.<br>
    All accident data verified from NCRB, MoRTH, Times of India &amp; state records.
  </div>
  <div class="foot-links">
    <a href="#manifesto">Our Mission</a>
    <a href="#incidents">Cases</a>
    <a href="/admin/dashboard">Admin Panel</a>
  </div>
</footer>

<script>
// ── Date ──
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
const now = new Date();
document.getElementById('hdr-date').textContent =
  days[now.getDay()].slice(0,3) + ', ' +
  now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();

// ── Scroll Reveal ──
const reveals = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry, i) => {
    if (entry.isIntersecting) {
      setTimeout(() => entry.target.classList.add('visible'), i * 60);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
reveals.forEach(el => observer.observe(el));

// ── Animate Hazard Bars ──
const barObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const fills = entry.target.querySelectorAll('.haz-fill');
      fills.forEach(f => {
        const target = f.getAttribute('data-w');
        setTimeout(() => { f.style.width = target + '%'; }, 200);
      });
      barObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.2 });
const hazGrid = document.querySelector('.haz-grid');
if (hazGrid) barObserver.observe(hazGrid);
</script>
</body>
</html>