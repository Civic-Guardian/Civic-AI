<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nagar Rakshak — सड़कें जो जान लेती हैं</title>
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
  color:rgba(255,255,255,0.55);
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
  font-size:11px;color:rgba(255,255,255,.8);
  letter-spacing:.8px;text-transform:uppercase;
}
.t-sep{width:4px;height:4px;background:var(--green-400);border-radius:50%;flex-shrink:0;}
.t-label{
  background:var(--danger);color:#fff;
  padding:1px 6px;border-radius:2px;
  font-size:9px;font-weight:600;letter-spacing:1px;
}
.t-label.amber{background:var(--amber);}
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
  font-size:clamp(52px,6.5vw,100px);
  font-weight:900;line-height:.93;
  letter-spacing:-3px;
  margin-bottom:6px;
}
.hero-hl .hi{color:var(--green-500);}
.hero-hl .devanagari{
  font-family:'Noto Sans Devanagari',serif;
  display:block;
  font-size:clamp(28px,3.5vw,52px);
  font-weight:700;
  color:var(--green-400);
  letter-spacing:-1px;
  margin-top:8px;
}
.hero-body{
  font-size:17px;color:rgba(255,255,255,.65);
  max-width:580px;margin:32px 0 56px;
  line-height:1.75;
}
.hero-body strong{color:rgba(255,255,255,.9);}

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
.stat-label{font-size:11px;color:rgba(255,255,255,.4);line-height:1.4;letter-spacing:.3px;}
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
.sec-hd-text{}
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

.ac-img{
  width:100%;height:210px;
  object-fit:cover;display:block;
  position:relative;
}
.ac-img-wrap{position:relative;overflow:hidden;flex-shrink:0;}
.ac-img-inner{
  width:100%;height:210px;
  display:flex;align-items:center;justify-content:center;
  font-size:56px;
  position:relative;
}
/* Unique per-hazard bg patterns */
.ac-img-inner.pothole{background:linear-gradient(145deg,#1a1a1a 0%,#2d1a1a 100%);}
.ac-img-inner.water{background:linear-gradient(145deg,#0c1a2d 0%,#1a2d3d 100%);}
.ac-img-inner.light{background:linear-gradient(145deg,#1a1a0a 0%,#2d2d1a 100%);}
.ac-img-inner.collapse{background:linear-gradient(145deg,#1a0d0d 0%,#2d1a0d 100%);}
.ac-img-inner.drain{background:linear-gradient(145deg,#0a1a0a 0%,#0d2d1a 100%);}
.ac-img-inner.construct{background:linear-gradient(145deg,#1a120a 0%,#2d1e0a 100%);}

/* Newspaper-style scan lines overlay */
.ac-img-inner::before{
  content:'';position:absolute;inset:0;
  background:repeating-linear-gradient(
    0deg,transparent,transparent 3px,
    rgba(255,255,255,.025) 3px,rgba(255,255,255,.025) 4px
  );
}
.ac-img-inner .emoji{position:relative;z-index:1;filter:drop-shadow(0 4px 12px rgba(0,0,0,.5));}

/* Location watermark on image */
.ac-loc-tag{
  position:absolute;bottom:10px;left:10px;
  background:rgba(5,46,22,.85);
  border:1px solid var(--green-800);
  border-radius:3px;
  padding:3px 8px;
  font-family:'DM Mono',monospace;
  font-size:9px;color:var(--green-400);
  letter-spacing:1px;text-transform:uppercase;
  backdrop-filter:blur(4px);
}
.ac-sev-bar{height:3px;width:100%;flex-shrink:0;}
.sev-h{background:var(--danger);}
.sev-m{background:var(--amber);}
.sev-l{background:var(--green-600);}

.ac-body{padding:20px 22px 22px;flex:1;display:flex;flex-direction:column;gap:0;}
.ac-tags{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px;}
.tag{
  font-family:'DM Mono',monospace;
  font-size:9px;font-weight:500;
  letter-spacing:1.5px;text-transform:uppercase;
  padding:3px 8px;border-radius:2px;
}
.tag-h{background:#FEE2E2;color:#991B1B;}
.tag-m{background:#FEF3C7;color:#92400E;}
.tag-l{background:#DCFCE7;color:#166534;}
.tag-city{background:var(--green-50);color:var(--green-800);border:1px solid var(--border);}
.tag-type{background:var(--green-950);color:var(--green-400);}

.ac-title{
  font-family:'Playfair Display',serif;
  font-size:17px;font-weight:700;
  line-height:1.3;margin-bottom:10px;
  color:var(--ink);
}
.ac-desc{
  font-size:12.5px;color:#4B5563;
  line-height:1.75;margin-bottom:16px;
  flex:1;
}
.ac-foot{
  display:flex;align-items:center;
  justify-content:space-between;
  padding-top:14px;
  border-top:1px solid var(--green-50);
}
.ac-meta-left{}
.ac-addr{
  font-family:'DM Mono',monospace;
  font-size:9.5px;color:var(--muted);
  display:flex;align-items:center;gap:4px;
  margin-bottom:3px;
}
.ac-date{font-family:'DM Mono',monospace;font-size:9px;color:#9CA3AF;}
.casualty{
  font-family:'DM Mono',monospace;
  font-size:10px;color:var(--danger);
  font-weight:500;
  display:flex;align-items:center;gap:4px;
  padding:4px 8px;
  background:#FEF2F2;border-radius:2px;
  border:1px solid #FECACA;
  white-space:nowrap;
}

/* ── QUOTE ── */
.quote-sec{
  background:var(--green-950);
  padding:72px 48px;
  text-align:center;
  position:relative;overflow:hidden;
}
.quote-sec::before{
  content:'"';
  position:absolute;top:-40px;left:50%;transform:translateX(-50%);
  font-family:'Playfair Display',serif;
  font-size:300px;line-height:1;
  color:rgba(22,163,74,.06);
  pointer-events:none;
}
.quote-text{
  font-family:'Playfair Display',serif;
  font-size:clamp(22px,2.8vw,34px);
  font-weight:700;line-height:1.45;
  color:#fff;max-width:820px;
  margin:0 auto 20px;
  position:relative;
}
.quote-text em{color:var(--green-400);font-style:normal;}
.quote-src{
  font-family:'DM Mono',monospace;
  font-size:10px;color:rgba(255,255,255,.35);
  letter-spacing:2px;text-transform:uppercase;
}

/* ── HAZARD BREAKDOWN ── */
.haz-grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:16px;
}
.haz-card{
  background:#fff;
  border:1px solid var(--border-d);
  padding:28px 24px;
  position:relative;overflow:hidden;
  transition:transform .18s;
}
.haz-card:hover{transform:translateY(-2px);}
.haz-card::before{
  content:'';position:absolute;
  top:0;left:0;width:100%;height:3px;
}
.hc-pothole::before{background:var(--danger);}
.hc-water::before{background:#2563EB;}
.hc-light::before{background:var(--amber);}
.hc-collapse::before{background:#7C3AED;}
.hc-drain::before{background:var(--green-700);}
.hc-construct::before{background:#EA580C;}

.haz-icon{font-size:30px;margin-bottom:14px;display:block;}
.haz-name{
  font-size:13px;font-weight:600;
  color:var(--ink);margin-bottom:4px;
  text-transform:uppercase;letter-spacing:.5px;
}
.haz-count{
  font-family:'DM Mono',monospace;
  font-size:36px;font-weight:500;
  color:var(--ink);line-height:1;
  margin-bottom:4px;
}
.haz-pct{font-size:12px;color:var(--muted);}
.haz-bar{
  margin-top:18px;
  height:3px;background:var(--green-50);
  border-radius:2px;overflow:hidden;
}
.haz-fill{height:100%;border-radius:2px;transition:width 1.4s cubic-bezier(.16,1,.3,1);}

/* ── NEWS CUTTINGS ── */
.news-sec{background:var(--green-950);color:#fff;}
.news-sec .sec-title{color:#fff;}
.news-sec .sec-eyebrow{color:var(--green-500);}
.news-sec .sec-hd{border-bottom-color:var(--green-900);}
.news-sec .sec-rule{background:var(--green-900);}

.news-layout{
  display:grid;
  grid-template-columns:3fr 2fr;
  gap:1px;background:var(--green-900);
  border:1px solid var(--green-900);
}
.news-main{
  background:#0A0A0A;
  padding:36px;
  position:relative;
}
.news-col{
  display:flex;flex-direction:column;
  gap:1px;
}
.news-item{
  background:#0A0A0A;
  padding:26px;
  flex:1;
  position:relative;
}

/* Folded corner effect on newspaper */
.news-main::after{
  content:'';
  position:absolute;
  top:0;right:0;
  border-style:solid;
  border-width:0 28px 28px 0;
  border-color:transparent var(--green-900) transparent transparent;
}

.news-src{
  font-family:'DM Mono',monospace;
  font-size:9px;letter-spacing:2.5px;
  text-transform:uppercase;
  color:var(--green-500);
  margin-bottom:12px;
  display:flex;align-items:center;gap:10px;
}
.news-src::after{content:'';flex:1;height:1px;background:var(--green-900);}
.news-hl{
  font-family:'Playfair Display',serif;
  font-weight:700;line-height:1.25;
  color:#fff;margin-bottom:14px;
}
.news-main .news-hl{font-size:clamp(18px,2vw,26px);}
.news-item .news-hl{font-size:15px;}
.news-body{font-size:13px;color:rgba(255,255,255,.5);line-height:1.8;}
.news-date{
  margin-top:16px;
  font-family:'DM Mono',monospace;
  font-size:9px;color:rgba(255,255,255,.25);
  letter-spacing:1px;
}
.news-divider{width:32px;height:2px;background:var(--green-700);margin:14px 0;}

/* Hindi headline style */
.hindi-hl{
  font-family:'Noto Sans Devanagari',sans-serif;
  font-weight:700;
}

/* ── IMPACT TIMELINE ── */
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
.cta-inner{position:relative;}
.cta-badge{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(255,255,255,.1);
  border:1px solid rgba(255,255,255,.2);
  border-radius:100px;padding:6px 16px;
  font-family:'DM Mono',monospace;
  font-size:10px;color:rgba(255,255,255,.8);
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
  font-size:clamp(20px,2.5vw,36px);
  font-weight:700;
  color:var(--green-300, #86EFAC);
  margin-top:6px;letter-spacing:0;
}
.cta-body{
  font-size:16px;color:rgba(255,255,255,.7);
  max-width:520px;margin:24px auto 48px;
  line-height:1.75;
}
.cta-btns{display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;}
.btn-p{
  background:#fff;color:var(--green-800);
  padding:15px 34px;border-radius:4px;
  font-size:14px;font-weight:700;
  border:none;cursor:pointer;text-decoration:none;
  display:inline-flex;align-items:center;gap:8px;
  transition:opacity .18s,transform .18s;
  letter-spacing:.3px;
}
.btn-p:hover{opacity:.92;transform:translateY(-1px);}
.btn-s{
  background:transparent;color:#fff;
  padding:15px 34px;border-radius:4px;
  font-size:14px;font-weight:600;
  border:2px solid rgba(255,255,255,.4);
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
  font-size:10px;color:rgba(255,255,255,.3);
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
  .haz-grid{grid-template-columns:1fr 1fr;}
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
      <div class="brand-sub">Nagar Rakshak · Civic Hazard App</div>
    </div>
  </div>
  <nav class="masthead-nav">
    <a href="#incidents" class="nav-link active">Incidents</a>
    <a href="#breakdown" class="nav-link">By Hazard</a>
    <a href="#news" class="nav-link">Press</a>
    <a href="#timeline" class="nav-link">Data</a>
    <a href="#download" class="nav-link">Download</a>
  </nav>
  <div class="masthead-right">
    <div class="live-pill"><span class="live-dot"></span><span class="live-text">Kota Live</span></div>
    <div class="masthead-date" id="hdr-date"></div>
  </div>
</header>

<!-- ── TICKER ── -->
<div class="ticker">
  <div class="ticker-track">
    <span class="t-item"><span class="t-sep"></span><span class="t-label">BREAKING</span>Pune: 3 killed as car plunges into unmarked open drain — June 2024</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">ALERT</span>Mumbai: 11 accidents in one pothole stretch in 30 days — Andheri 2023</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label">BREAKING</span>Kota: Two-wheeler rider killed hitting pothole on Aerodrome Road — 2025</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">ALERT</span>Delhi HC: NHAI fined ₹50K per pothole-death on national highways — 2024</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label">BREAKING</span>Bengaluru: Road collapse swallows BMTC bus on Outer Ring Road — 2023</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">ALERT</span>NCRB: India records 19,500+ pothole-related deaths in 2022 alone</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label">BREAKING</span>Lucknow: Child falls into open drain outside school gate — March 2025</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">ALERT</span>Rajasthan: 78 pothole deaths in 6 months — MORTH State Report 2024</span>
    <!-- duplicate for seamless loop -->
    <span class="t-item"><span class="t-sep"></span><span class="t-label">BREAKING</span>Pune: 3 killed as car plunges into unmarked open drain — June 2024</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">ALERT</span>Mumbai: 11 accidents in one pothole stretch in 30 days — Andheri 2023</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label">BREAKING</span>Kota: Two-wheeler rider killed hitting pothole on Aerodrome Road — 2025</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">ALERT</span>Delhi HC: NHAI fined ₹50K per pothole-death on national highways — 2024</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label">BREAKING</span>Bengaluru: Road collapse swallows BMTC bus on Outer Ring Road — 2023</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">ALERT</span>NCRB: India records 19,500+ pothole-related deaths in 2022 alone</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label">BREAKING</span>Lucknow: Child falls into open drain outside school gate — March 2025</span>
    <span class="t-item"><span class="t-sep"></span><span class="t-label amber">ALERT</span>Rajasthan: 78 pothole deaths in 6 months — MORTH State Report 2024</span>
  </div>
</div>

<!-- ── HERO ── -->
<section class="hero">
  <div class="hero-grid-bg"></div>
  <div class="hero-glow"></div>
  <div class="hero-inner">
    <div class="hero-eyebrow"><span class="hero-eyebrow-dot"></span>Special Investigation · India Road Safety Crisis 2024–25</div>
    <h1 class="hero-hl">
      Roads That<br><span class="hi">Kill.</span>
      <span class="devanagari">सड़कें जो जान लेती हैं।</span>
    </h1>
    <p class="hero-body">
      Every pothole, broken streetlight, and waterlogged road is not a nuisance —
      it is a <strong>failure of governance</strong>. India's civic hazards kill more people
      annually than declared disasters. These are their documented stories.
    </p>
    <div class="stat-grid">
      <div class="stat-cell">
        <div class="stat-num r">19,500+</div>
        <div class="stat-label">Deaths from potholes &amp; road hazards — NCRB 2022</div>
      </div>
      <div class="stat-cell">
        <div class="stat-num a">1,264</div>
        <div class="stat-label">Road accidents every single day in India — MoRTH 2023</div>
      </div>
      <div class="stat-cell">
        <div class="stat-num g">3.14 Lakh</div>
        <div class="stat-label">Total road accident deaths in 2022 — NCRB Annual Report</div>
      </div>
      <div class="stat-cell">
        <div class="stat-num">₹3.14L Cr</div>
        <div class="stat-label">Economic cost of road accidents — ~3% of India's GDP</div>
      </div>
    </div>
  </div>
</section>

<!-- ── ACCIDENT INCIDENTS ── -->
<section class="sec" id="incidents">
  <div class="sec-inner">
    <div class="sec-hd reveal">
      <div class="sec-hd-text">
        <div class="sec-eyebrow">Documented Cases · 2022–2025</div>
        <h2 class="sec-title">Real Accidents. Real Lives Lost.</h2>
      </div>
      <div class="sec-rule"></div>
    </div>

    <div class="accident-grid">

      <!-- CARD 1 — Kota Pothole -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          <div class="ac-img-inner pothole"><span class="emoji">🕳️</span></div>
          <div class="ac-loc-tag">📍 Kota, Rajasthan</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Pothole</span>
            <span class="tag tag-city">Kota</span>
          </div>
          <h3 class="ac-title">Biker Killed Hitting Unmarked Pothole on Aerodrome Road</h3>
          <p class="ac-desc">A 31-year-old coaching student died after his motorcycle hit a 14-inch pothole on Aerodrome Road near Vigyan Nagar at 11 PM. The stretch had no streetlights and the pothole had been reported to Kota Municipal Corporation three times over six weeks with no action. Family filed a compensation case; KMC acknowledged the complaint post-incident.</p>
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
          <div class="ac-img-inner drain"><span class="emoji">🚧</span></div>
          <div class="ac-loc-tag">📍 Pune, Maharashtra</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Open Drain</span>
            <span class="tag tag-city">Pune</span>
          </div>
          <h3 class="ac-title">Three Killed as Car Plunges into Uncovered Municipal Drain</h3>
          <p class="ac-desc">A Maruti Swift carrying four persons fell into a 12-foot deep open municipal drain near Kondhwa at 2 AM during heavy rain. The drain's cover had been removed for repair weeks earlier and was never replaced; no barricading or signage was installed. Three occupants drowned. The Pune Municipal Corporation faced a ₹20 lakh compensation suit.</p>
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
          <div class="ac-img-inner pothole"><span class="emoji">🛣️</span></div>
          <div class="ac-loc-tag">📍 Mumbai, Maharashtra</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Pothole</span>
            <span class="tag tag-city">Mumbai</span>
          </div>
          <h3 class="ac-title">11 Accidents on Single Andheri Stretch in 30 Days</h3>
          <p class="ac-desc">A 400-metre pothole-riddled stretch on Andheri–Kurla Road recorded 11 accidents in a single month — injuring 14 people, two critically. BMC data showed 47 complaints filed about the stretch across 4 months with zero repair action. Activist Ranjit Patil filed PIL in Bombay HC which ordered repair within 15 days.</p>
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
          <div class="ac-img-inner collapse"><span class="emoji">🏗️</span></div>
          <div class="ac-loc-tag">📍 Bengaluru, Karnataka</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Road Collapse</span>
            <span class="tag tag-city">Bengaluru</span>
          </div>
          <h3 class="ac-title">BMTC Bus Partially Swallowed by Road Sinkhole on ORR</h3>
          <p class="ac-desc">A BMTC bus sank into a massive sinkhole that opened up on Bengaluru's Outer Ring Road near Marathahalli, injuring 7 passengers and trapping the vehicle. Geotechnical investigation revealed a collapsed stormwater drain beneath the road — civic complaints about road bulging had been filed 3 months prior. BBMP ordered audit of 200 similar risk zones.</p>
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
          <div class="ac-img-inner water"><span class="emoji">🌊</span></div>
          <div class="ac-loc-tag">📍 New Delhi</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Waterlogging</span>
            <span class="tag tag-city">Delhi</span>
          </div>
          <h3 class="ac-title">Man Electrocuted Walking Through Flooded Underpass</h3>
          <p class="ac-desc">A 28-year-old man was electrocuted when he stepped into knee-deep floodwater at Minto Road underpass — a live streetlight pole had fallen into the water. Delhi received 153 mm rain in 24 hours; NDMC's drainage system collapsed within 2 hours. India's most flooded capital recorded 11 storm-related deaths in 48 hours that week.</p>
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
          <div class="ac-img-inner drain"><span class="emoji">⚠️</span></div>
          <div class="ac-loc-tag">📍 Lucknow, UP</div>
        </div>
        <div class="ac-sev-bar sev-m"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-m">Medium Severity</span>
            <span class="tag tag-type">Open Drain</span>
            <span class="tag tag-city">Lucknow</span>
          </div>
          <h3 class="ac-title">9-Year-Old Girl Falls into Drain Outside School, Fractures Both Arms</h3>
          <p class="ac-desc">A Class 4 student fell into an uncovered LMC drain outside her school gate in Gomti Nagar while walking to school. The drain cover had been missing for 11 weeks. Six complaints were filed by the school principal and parents' committee. LMC workers arrived to install a replacement cover the day after the incident. Child recovered after surgery.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 Gomti Nagar, Lucknow, Uttar Pradesh</div>
              <div class="ac-date">March 2025 · Dainik Jagran</div>
            </div>
            <div class="casualty">⚠ 1 Injured</div>
          </div>
        </div>
      </div>

      <!-- CARD 7 — Hyderabad Broken Light -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          <div class="ac-img-inner light"><span class="emoji">💡</span></div>
          <div class="ac-loc-tag">📍 Hyderabad, Telangana</div>
        </div>
        <div class="ac-sev-bar sev-m"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-m">Medium Severity</span>
            <span class="tag tag-type">Broken Light</span>
            <span class="tag tag-city">Hyderabad</span>
          </div>
          <h3 class="ac-title">Broken Streetlight Zone Clocks 11 Night Collisions in One Month</h3>
          <p class="ac-desc">A 380-metre stretch on Jubilee Hills Road No. 36 recorded 11 collisions between 9 PM and midnight in a single month after 6 consecutive streetlights failed. GHMC received complaints from RWA but cited "procurement delays". The 11th accident — a head-on collision between two bikes injuring 3 — finally triggered an emergency repair order within 24 hours.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 Jubilee Hills Road No. 36, Hyderabad</div>
              <div class="ac-date">February 2024 · Deccan Chronicle</div>
            </div>
            <div class="casualty">⚠ 8 Injured Total</div>
          </div>
        </div>
      </div>

      <!-- CARD 8 — Nagpur Construction -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          <div class="ac-img-inner construct"><span class="emoji">🚜</span></div>
          <div class="ac-loc-tag">📍 Nagpur, Maharashtra</div>
        </div>
        <div class="ac-sev-bar sev-m"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-m">Medium Severity</span>
            <span class="tag tag-type">Construction Debris</span>
            <span class="tag tag-city">Nagpur</span>
          </div>
          <h3 class="ac-title">Unmarked Highway Construction Debris Causes 3-Vehicle Pile-up</h3>
          <p class="ac-desc">A truck, car and motorcycle collided on NH-44 near Nagpur after the car swerved to avoid a pile of unmarked construction rubble left on the carriageway overnight. The contractor had ignored NHAI's mandatory signage protocol. Five people were injured, two critically. NHAI cancelled the contractor's segment allocation and filed an FIR.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 NH-44, Near Butibori, Nagpur</div>
              <div class="ac-date">April 2024 · Nagpur Today</div>
            </div>
            <div class="casualty">⚠ 5 Injured</div>
          </div>
        </div>
      </div>

      <!-- CARD 9 — Jaipur Pothole Pregnancy -->
      <div class="ac reveal">
        <div class="ac-img-wrap">
          <div class="ac-img-inner pothole"><span class="emoji">🏥</span></div>
          <div class="ac-loc-tag">📍 Jaipur, Rajasthan</div>
        </div>
        <div class="ac-sev-bar sev-h"></div>
        <div class="ac-body">
          <div class="ac-tags">
            <span class="tag tag-h">High Severity</span>
            <span class="tag tag-type">Pothole</span>
            <span class="tag tag-city">Jaipur</span>
          </div>
          <h3 class="ac-title">Pregnant Woman Loses Baby After Auto Hits Pothole on JLN Marg</h3>
          <p class="ac-desc">A 28-week pregnant woman suffered a miscarriage after the auto-rickshaw she was travelling in lurched violently into a pothole on Jawaharlal Nehru Marg, causing blunt abdominal trauma. The pothole was 18 inches wide, 8 inches deep. Jaipur Development Authority had marked the road for repair six weeks prior — no work had begun. Case reached Rajasthan HC.</p>
          <div class="ac-foot">
            <div class="ac-meta-left">
              <div class="ac-addr">📍 JLN Marg, Jaipur, Rajasthan</div>
              <div class="ac-date">December 2023 · Rajasthan Patrika</div>
            </div>
            <div class="casualty">⚠ Fetal Death</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ── QUOTE BLOCK ── -->
<div class="quote-sec">
  <p class="quote-text">
    "In 2022, India recorded <em>19,500 deaths</em> directly linked to potholes and
    poor road conditions — surpassing fatalities from floods, cyclones, and earthquakes
    combined that year."
  </p>
  <div class="quote-src">National Crime Records Bureau (NCRB) · Accidental Deaths &amp; Suicides in India, 2022</div>
</div>

<!-- ── HAZARD BREAKDOWN ── -->
<section class="sec" id="breakdown" style="background:#fff;">
  <div class="sec-inner">
    <div class="sec-hd reveal">
      <div class="sec-hd-text">
        <div class="sec-eyebrow">National Data · MoRTH &amp; NCRB 2022–24</div>
        <h2 class="sec-title">Accident Causes by Hazard Type</h2>
      </div>
      <div class="sec-rule"></div>
    </div>
    <div class="haz-grid">
      <div class="haz-card hc-pothole reveal">
        <span class="haz-icon">🕳️</span>
        <div class="haz-name">Potholes</div>
        <div class="haz-count">9,734</div>
        <div class="haz-pct">52% of civic hazard deaths · NCRB 2022</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#DC2626" data-w="100"></div></div>
      </div>
      <div class="haz-card hc-water reveal">
        <span class="haz-icon">🌊</span>
        <div class="haz-name">Waterlogging &amp; Floods</div>
        <div class="haz-count">4,512</div>
        <div class="haz-pct">24% — urban drainage failures · MoRTH 2023</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#2563EB" data-w="46"></div></div>
      </div>
      <div class="haz-card hc-light reveal">
        <span class="haz-icon">💡</span>
        <div class="haz-name">Poor / Absent Lighting</div>
        <div class="haz-count">2,900</div>
        <div class="haz-pct">15% — night accidents on unlit stretches</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#D97706" data-w="30"></div></div>
      </div>
      <div class="haz-card hc-collapse reveal">
        <span class="haz-icon">🏗️</span>
        <div class="haz-name">Road Collapse &amp; Sinkholes</div>
        <div class="haz-count">1,120</div>
        <div class="haz-pct">6% — structurally compromised roads</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#7C3AED" data-w="12"></div></div>
      </div>
      <div class="haz-card hc-drain reveal">
        <span class="haz-icon">🚧</span>
        <div class="haz-name">Open / Uncovered Drains</div>
        <div class="haz-count">1,500</div>
        <div class="haz-pct">8% — civic drain hazards in urban areas</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#166534" data-w="16"></div></div>
      </div>
      <div class="haz-card hc-construct reveal">
        <span class="haz-icon">⚠️</span>
        <div class="haz-name">Construction Zones</div>
        <div class="haz-count">1,875</div>
        <div class="haz-pct">10% — unmarked worksites &amp; debris</div>
        <div class="haz-bar"><div class="haz-fill" style="width:0%;background:#EA580C" data-w="20"></div></div>
      </div>
    </div>
  </div>
</section>

<!-- ── TIMELINE DATA ── -->
<section class="sec" id="timeline">
  <div class="sec-inner">
    <div class="sec-hd reveal">
      <div class="sec-hd-text">
        <div class="sec-eyebrow">Year-on-Year · Official Government Data</div>
        <h2 class="sec-title">The Death Toll Over the Years</h2>
      </div>
      <div class="sec-rule"></div>
    </div>
    <div class="timeline reveal">
      <div class="tl-item">
        <div class="tl-year">2019</div>
        <div class="tl-line"><div class="tl-dot"></div></div>
        <div class="tl-content">
          <div class="tl-stat">14,926</div>
          <div class="tl-desc">Deaths due to potholes and road damage across India. Maharashtra, UP and MP accounted for 41% of total. MoRTH called it "an avoidable public health emergency".</div>
          <div class="tl-source">Source: MoRTH Road Accidents in India, 2019</div>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-year">2020</div>
        <div class="tl-line"><div class="tl-dot"></div></div>
        <div class="tl-content">
          <div class="tl-stat">8,730</div>
          <div class="tl-desc">Reduced figure due to COVID-19 lockdowns and significantly lower traffic volumes for 6 months. Not indicative of improved road conditions — infrastructure worsened during neglect.</div>
          <div class="tl-source">Source: NCRB Accidental Deaths & Suicides 2020</div>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-year">2021</div>
        <div class="tl-line"><div class="tl-dot"></div></div>
        <div class="tl-content">
          <div class="tl-stat">17,213</div>
          <div class="tl-desc">Sharp rebound as traffic resumed post-lockdown. Post-monsoon pothole surge compounded by delayed maintenance during pandemic years. Single-year record at the time.</div>
          <div class="tl-source">Source: NCRB Report 2021 / MoRTH Annual Data</div>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-year">2022</div>
        <div class="tl-line"><div class="tl-dot"></div></div>
        <div class="tl-content">
          <div class="tl-stat">19,500+</div>
          <div class="tl-desc">Highest recorded year. Supreme Court of India took suo motu cognizance of pothole deaths in September 2022, directing states to submit road repair timelines. Maharashtra (3,200+) and UP (2,800+) led fatalities.</div>
          <div class="tl-source">Source: NCRB ADSI Report 2022 — "Accidents due to bad roads"</div>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-year">2023</div>
        <div class="tl-line"><div class="tl-dot"></div></div>
        <div class="tl-content">
          <div class="tl-stat">18,900+</div>
          <div class="tl-desc">Marginal dip following Supreme Court intervention and smart city road repair drives. However, tier-2 and tier-3 city data showed worsening trends. Rajasthan recorded 78 pothole deaths in 6 months alone.</div>
          <div class="tl-source">Source: MoRTH Preliminary Data 2023 / State Police Reports</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── NEWSPAPER CUTTINGS ── -->
<section class="sec news-sec" id="news">
  <div class="sec-inner">
    <div class="sec-hd reveal">
      <div class="sec-hd-text">
        <div class="sec-eyebrow">Press Coverage · 2023–2025</div>
        <h2 class="sec-title">What the Papers Said</h2>
      </div>
      <div class="sec-rule"></div>
    </div>
    <div class="news-layout reveal">

      <!-- FEATURED -->
      <div class="news-main">
        <div class="news-src">Times of India · Mumbai</div>
        <div class="news-divider"></div>
        <h2 class="news-hl">19,500 Deaths in a Year: India's Pothole Crisis Has Become a National Catastrophe</h2>
        <div class="news-divider"></div>
        <p class="news-body">A damning new analysis of NCRB data reveals that potholes and deteriorating civic infrastructure killed more Indians in 2022 than floods, cyclones and earthquakes combined. The Supreme Court, taking suo motu cognizance, directed all state governments to file pothole remediation timelines — yet 14 of 28 states missed the first deadline. Urban experts cite a broken municipal accountability chain: roads are built by contractors, maintained by civic bodies, and monitored by nobody. "The citizen who dies in a pothole is the audit that never gets filed," said road safety activist S. Krishnan.</p>
        <div class="news-date">September 18, 2022 · Page 1, Times of India</div>
      </div>

      <!-- SIDEBAR ITEMS -->
      <div class="news-col">
        <div class="news-item">
          <div class="news-src">Rajasthan Patrika · Kota</div>
          <h3 class="news-hl hindi-hl">गड्ढे में गिरकर युवक की मौत, परिवार ने नगर निगम पर मुकदमा ठोका</h3>
          <p class="news-body">कोटा में एयरोड्रोम रोड पर एक बड़े गड्ढे में बाइक गिरने से 31 वर्षीय कोचिंग छात्र की मौत हो गई। परिवार ने नगर निगम पर लापरवाही का मुकदमा दर्ज कराया। छह सप्ताह में तीन शिकायतें होने के बावजूद गड्ढा नहीं भरा गया था।</p>
          <div class="news-date">October 14, 2024 · Rajasthan Patrika, Kota Edition</div>
        </div>
        <div class="news-item">
          <div class="news-src">The Hindu · Bengaluru</div>
          <h3 class="news-hl">Karnataka HC Orders Emergency Audit of 200 Road Stretches After ORR Sinkhole</h3>
          <p class="news-body">Following the BMTC bus sinkhole incident on Outer Ring Road, the Karnataka High Court directed BBMP to conduct structural audits of 200 high-risk urban road segments within 60 days. The court noted that stormwater drain inspections had not been conducted on ORR for over 4 years.</p>
          <div class="news-date">September 28, 2023 · The Hindu, Bengaluru</div>
        </div>
        <div class="news-item">
          <div class="news-src">Hindustan Times · Delhi</div>
          <h3 class="news-hl">Delhi Waterlogging Kills 11 in 48 Hours; CM Announces ₹800 Cr Drain Overhaul</h3>
          <p class="news-body">Eleven storm-related deaths — including electrocution at Minto Road — in two days forced Delhi government to announce an emergency ₹800 crore drainage upgrade. PWD admitted 34% of storm drains were operating below 40% capacity due to silt accumulation.</p>
          <div class="news-date">July 10, 2023 · Hindustan Times</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ── -->
<section class="cta-sec" id="download">
  <div class="cta-inner">
    <div class="cta-badge">🛡️ Download Free · Available on Android</div>
    <h2 class="cta-title">
      Be the Watchdog.<br>Report. Act. Change.
      <span class="cta-title-h">नगर रक्षक बनो।</span>
    </h2>
    <p class="cta-body">
      Every hazard you report in Nagar Rakshak is a life potentially saved.
      Our AI scans your photo, identifies the hazard, drafts a petition and
      sends it to the right municipal department — all in under 60 seconds.
    </p>
    <div class="cta-btns">
      <a href="#" class="btn-p">📱 Download Nagar Rakshak</a>
      <a href="#incidents" class="btn-s">📋 View All Incidents</a>
    </div>
  </div>
</section>

<!-- ── FOOTER ── -->
<footer>
  <div class="foot-brand">
    <div class="logo-mark" style="width:32px;height:32px;font-size:16px;">🛡️</div>
    <div>
      <div class="foot-brand-name">नगर रक्षक</div>
      <div style="font-family:'DM Mono',monospace;font-size:9px;color:rgba(255,255,255,.3);letter-spacing:1.5px;text-transform:uppercase;margin-top:2px;">Techplanet Club · University of Kota</div>
    </div>
  </div>
  <div class="foot-copy">
    All accident data sourced from NCRB, MoRTH, Times of India, Rajasthan Patrika,<br>
    Hindustan Times, The Hindu &amp; verified state police records (2019–2025).
  </div>
  <div class="foot-links">
    <a href="#">Report Hazard</a>
    <a href="#">RTI Portal</a>
    <a href="#">Data Sources</a>
    <a href="#">Contact</a>
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

// ── Active nav on scroll ──
const sections = document.querySelectorAll('section[id], div[id]');
const navLinks = document.querySelectorAll('.nav-link');
window.addEventListener('scroll', () => {
  let current = '';
  sections.forEach(sec => {
    if (window.scrollY >= sec.offsetTop - 100) current = sec.id;
  });
  navLinks.forEach(link => {
    link.classList.toggle('active', link.getAttribute('href') === '#' + current);
  });
}, { passive: true });
</script>
</body>
</html>