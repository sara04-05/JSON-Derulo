<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Study Buddy — ElevUra Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet" />
<style>
/* ─── Reset & Base ─────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg-root:      #181818;
  --bg-panel:     #1d1f24;
  --bg-card:      #1d1f24;
  --bg-card-2:    #252a31;
  --bg-input:     #171f21;
  --border:       rgba(255,255,255,0.05);
  --border-hover: rgba(0, 242, 255, 0.12);
  --border-glow:  rgba(0, 242, 255, 0.42);

  --cyan:         #00e7ff;
  --cyan-dim:     #00b8d4;
  --teal:         #00b8d4;
  --purple:       #b08cff;
  --purple-dim:   #8b5cf6;
  --pink:         #f472b6;

  --grad-cyan:    linear-gradient(135deg, #00e7ff 0%, #00b8d4 100%);
  --grad-purple:  linear-gradient(135deg, #b08cff 0%, #8b5cf6 100%);
  --grad-mixed:   linear-gradient(135deg, #00e7ff 0%, #b08cff 100%);
  --grad-warm:    linear-gradient(135deg, #f472b6 0%, #b08cff 100%);

  --text-primary: #f3f4f6;
  --text-secondary: #9ca3af;
  --text-muted:   #6b7280;
  --text-accent:  #00e7ff;

  --font-display: 'Inter', sans-serif;
  --font-mono:    'JetBrains Mono', monospace;

  --radius-sm:    6px;
  --radius-md:    10px;
  --radius-lg:    14px;
  --radius-xl:    20px;

  --shadow-glow:  0 0 30px rgba(99,215,210,0.12);
  --shadow-card:  0 4px 24px rgba(0,0,0,0.4);
  --shadow-float: 0 8px 40px rgba(0,0,0,0.5);
  --transition:   0.22s cubic-bezier(0.4,0,0.2,1);
}

html { scroll-behavior: smooth; }

body {
  font-family: var(--font-display);
  background: rgb(24, 24, 24);
  background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
  color: var(--text-primary);
  min-height: 100vh;
  line-height: 1.6;
  overflow-x: hidden;
}

/* ─── Noise Texture Overlay ─────────────────────────── */
body::before {
  content: '';
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
  opacity: 0.4;
}

/* ─── Grid Background ───────────────────────────────── */
.grid-bg {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background-image:
    linear-gradient(rgba(0, 242, 255, 0.02) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0, 242, 255, 0.02) 1px, transparent 1px);
  background-size: 48px 48px;
  mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black 30%, transparent 100%);
}

/* ─── Ambient Glows ─────────────────────────────────── */
.ambient {
  position: fixed; z-index: 0; pointer-events: none; border-radius: 50%;
  filter: blur(80px); opacity: 0.08;
}
.ambient-1 { width: 600px; height: 600px; background: var(--cyan); top: -200px; left: -100px; opacity: 0.06; }
.ambient-2 { width: 500px; height: 500px; background: var(--purple); top: 30%; right: -150px; opacity: 0.06; }
.ambient-3 { width: 400px; height: 400px; background: var(--cyan-dim); bottom: 0; left: 30%; opacity: 0.06; }

/* ─── Layout ─────────────────────────────────────────── */
.container-wrapper {
  position: relative;
  z-index: 1;
  display: flex;
}

/* ─── Sidebar ─────────────────────────────────────────── */
.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  width: 190px;
  min-width: 190px;
  height: 100vh;
  background: rgba(16, 16, 16, 0.99);
  border-right: 1px solid rgba(255,255,255,0.05);
  display: flex;
  flex-direction: column;
  padding: 0;
  z-index: 100;
  overflow-y: auto;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 11px;
  border-bottom: 1px solid rgba(255,255,255,0.05);
  padding: 22px 16px 18px;
  height: 60px;
  flex-shrink: 0;
}

.sidebar-brand-mark {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: linear-gradient(145deg, var(--text-primary), #00b8d4);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.sidebar-brand-mark svg {
  width: 19px;
  height: 19px;
  fill: #050810;
}

.sidebar-brand-text {
  font-size: 17px;
  font-weight: 800;
  letter-spacing: -0.35px;
  color: var(--text-primary);
}

.sidebar-menu {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: auto;
  position: relative;
  z-index: 2;
  padding: 14px 10px 12px;
}

.sidebar-item {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 44px;
  padding: 0 14px;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
  color: rgba(255,255,255,0.48);
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  border: 1px solid transparent;
  position: relative;
  z-index: 2;
}

.sidebar-item:hover {
  background: rgba(0, 242, 255, 0.05);
  color: rgba(255,255,255,0.92);
  border-color: rgba(0, 242, 255, 0.12);
}

.sidebar-item.active {
  background: rgba(0, 242, 255, 0.09);
  border: 1px solid rgba(0, 242, 255, 0.42);
  color: var(--text-primary);
}

.sidebar-item-icon {
  font-size: 0;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: rgba(255,255,255,0.42);
}

.sidebar-item.active .sidebar-item-icon {
  color: var(--cyan);
}

.sidebar-item-icon-terminal {
  font-family: 'JetBrains Mono', ui-monospace, monospace;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: -0.5px;
  color: inherit;
  width: 22px;
}

.sidebar-item-icon svg {
  width: 20px;
  height: 20px;
  stroke: currentColor;
  fill: none;
  stroke-width: 1.75;
  stroke-linecap: round;
  stroke-linejoin: round;
}

/* ─── Main Content ────────────────────────────────────── */
.main-content {
  margin-left: 190px;
  width: calc(100% - 190px);
  display: flex;
  flex-direction: column;
}

/* ─── Top Header ──────────────────────────────────────── */
.top-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 32px;
  height: 72px;
  background: rgba(24, 24, 24, 0.8);
  border-bottom: 1px solid rgba(255,255,255,0.05);
  gap: 16px;
  z-index: 50;
  position: sticky;
  top: 0;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-left-meta {
  display: flex;
  align-items: center;
  gap: 12px;
}

.environment-text {
  font-size: 12px;
  color: var(--text-muted);
  font-family: var(--font-mono);
}

.environment-text .production {
  color: #22c55e;
  font-weight: 600;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-left: auto;
}

.notification-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: var(--text-secondary);
  transition: all 0.25s ease;
  position: relative;
}

.notification-icon:hover {
  color: var(--cyan);
  border-color: rgba(0, 242, 255, 0.15);
  background: rgba(0, 242, 255, 0.05);
}

.notification-dot {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 8px;
  height: 8px;
  background: #ef4444;
  border-radius: 50%;
  box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--cyan), var(--purple));
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.user-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-primary);
}

.user-tier {
  font-size: 11px;
  color: var(--text-muted);
  font-family: var(--font-mono);
}

.header-fullscreen {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: var(--text-secondary);
  transition: all 0.25s ease;
}

.header-fullscreen:hover {
  color: var(--cyan);
  border-color: rgba(0, 242, 255, 0.15);
  background: rgba(0, 242, 255, 0.05);
}

.header-fullscreen svg {
  width: 18px;
  height: 18px;
  stroke: currentColor;
  fill: none;
  stroke-width: 1.75;
  stroke-linecap: round;
  stroke-linejoin: round;
}

/* ─── Content Area ────────────────────────────────────── */
.content-area {
  flex: 1;
  padding: 0;
  overflow-y: auto;
}

.page-wrap {
  position: relative; z-index: 1;
  max-width: 1500px; margin: 0 auto;
  padding: 0 32px 80px;
  margin-left: 0;
}

/* ─── Breadcrumb Bar ────────────────────────────────── */
.topbar {
  display: flex; align-items: center; gap: 8px;
  padding: 16px 0 12px;
  font-family: var(--font-mono);
  font-size: 11px;
  color: var(--text-muted);
  letter-spacing: 0.05em;
}
.topbar-sep { color: var(--text-muted); }
.topbar-current { color: var(--text-accent); }
.topbar-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--cyan); margin-left: auto;
  box-shadow: 0 0 8px var(--cyan);
  animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: 0.4; transform: scale(0.7); }
}

/* ─── Hero ──────────────────────────────────────────── */
.hero {
  padding: 32px 0 28px;
  display: grid; grid-template-columns: 1fr auto; align-items: start; gap: 32px;
}
.hero-eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  font-family: var(--font-mono); font-size: 11px; font-weight: 600;
  letter-spacing: 0.15em; text-transform: uppercase;
  color: var(--cyan);
  background: rgba(0, 242, 255, 0.08);
  border: 1px solid rgba(0, 242, 255, 0.2);
  padding: 6px 12px; border-radius: 100px;
  margin-bottom: 16px;
}
.hero-eyebrow::before {
  content: '';
  width: 5px; height: 5px; border-radius: 50%;
  background: var(--cyan);
  box-shadow: 0 0 8px rgba(0, 242, 255, 0.4);
}
.hero-title {
  font-size: clamp(2.2rem, 5vw, 3.2rem);
  font-weight: 800; line-height: 1.2; letter-spacing: -0.02em;
}
.hero-title .highlight {
  background: var(--grad-mixed);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  background-clip: text;
}
.hero-desc {
  margin-top: 16px;
  max-width: 520px;
  font-size: 15px; color: var(--text-secondary); line-height: 1.7;
  font-weight: 400;
}
.hero-meta {
  display: flex; align-items: center; gap: 20px; margin-top: 28px;
}
.hero-stat {
  text-align: left;
}
.hero-stat-val {
  font-size: 22px; font-weight: 700;
  background: var(--grad-cyan);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  background-clip: text;
}
.hero-stat-label {
  font-size: 11px; color: var(--text-muted);
  font-family: var(--font-mono); letter-spacing: 0.06em;
}
.hero-divider { width: 1px; height: 32px; background: var(--border); }

.hero-cta-group { display: flex; flex-direction: column; gap: 12px; align-items: flex-end; }
.badge-group { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
.badge {
  font-size: 11px; font-family: var(--font-mono); letter-spacing: 0.05em;
  padding: 6px 12px; border-radius: 8px;
  border: 1px solid var(--border);
  color: var(--text-secondary);
  background: rgba(255,255,255,0.03);
}
.badge.cyan { border-color: rgba(0, 242, 255, 0.15); color: var(--cyan); background: rgba(0, 242, 255, 0.05); }
.badge.purple { border-color: rgba(176, 140, 255, 0.15); color: var(--purple); background: rgba(176, 140, 255, 0.05); }
.badge.pink { border-color: rgba(244, 114, 182, 0.15); color: var(--pink); background: rgba(244, 114, 182, 0.05); }

/* ─── Input Control Panel ───────────────────────────── */
.control-panel {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-xl);
  padding: 28px 32px;
  margin-bottom: 32px;
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-card);
  transition: border-color var(--transition), box-shadow var(--transition);
}
.control-panel::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 1px;
  background: var(--grad-mixed); opacity: 0.5;
}
.control-panel:hover {
  border-color: rgba(0, 242, 255, 0.12);
  box-shadow: var(--shadow-card), 0 0 20px rgba(0, 242, 255, 0.08);
}

.cp-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 24px;
}
.cp-title {
  font-size: 13px; font-weight: 600; letter-spacing: 0.06em;
  text-transform: uppercase; color: var(--text-secondary);
  font-family: var(--font-mono);
}
.cp-mode-toggle {
  display: flex; gap: 2px;
  background: rgba(255,255,255,0.04);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 3px;
}
.cp-mode-btn {
  font-size: 12px; font-family: var(--font-mono);
  padding: 5px 14px; border-radius: 4px;
  border: none; cursor: pointer;
  color: var(--text-muted);
  background: transparent;
  transition: all var(--transition);
}
.cp-mode-btn.active {
  background: rgba(0, 242, 255, 0.12);
  color: var(--cyan);
}
.cp-mode-btn:hover:not(.active) { color: var(--text-primary); }

.cp-grid {
  display: grid; grid-template-columns: 1fr 180px 200px auto;
  gap: 12px; align-items: end;
}

.field { display: flex; flex-direction: column; gap: 7px; }
.field-label {
  font-size: 11px; font-weight: 500; letter-spacing: 0.1em;
  text-transform: uppercase; color: var(--text-muted);
  font-family: var(--font-mono);
}

.input-wrap { position: relative; }
.input-icon {
  position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
  color: var(--text-muted); pointer-events: none;
  font-size: 14px; transition: color var(--transition);
}
input[type="text"], select {
  width: 100%;
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  color: var(--text-primary);
  font-family: var(--font-mono);
  font-size: 13px;
  padding: 11px 14px 11px 38px;
  transition: all var(--transition);
  outline: none; -webkit-appearance: none; appearance: none;
}
select { padding-left: 38px; cursor: pointer; }
input[type="text"]:focus, select:focus {
  border-color: rgba(0, 242, 255, 0.3);
  box-shadow: 0 0 0 3px rgba(0, 242, 255, 0.08);
  background: rgba(21, 28, 33, 0.9);
}
input[type="text"]:focus ~ .input-icon,
select:focus ~ .input-icon { color: var(--cyan); }
input::placeholder { color: var(--text-muted); }

.btn {
  display: inline-flex; align-items: center; gap: 8px;
  font-family: var(--font-display); font-weight: 600;
  font-size: 13px; letter-spacing: 0.02em;
  padding: 11px 22px; border-radius: var(--radius-md);
  border: none; cursor: pointer;
  transition: all var(--transition); white-space: nowrap;
}
.btn-primary {
  background: var(--grad-mixed);
  color: #181818;
  box-shadow: 0 4px 20px rgba(0, 242, 255, 0.15);
}
.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 28px rgba(0, 242, 255, 0.25);
}
.btn-primary:active { transform: translateY(0); }

.btn-secondary {
  background: rgba(255,255,255,0.05);
  border: 1px solid var(--border);
  color: var(--text-primary);
}
.btn-secondary:hover {
  border-color: rgba(0, 242, 255, 0.12);
  background: rgba(0, 242, 255, 0.05);
  color: var(--cyan);
}

.btn-ghost {
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-secondary);
  padding: 8px 14px; font-size: 12px;
}
.btn-ghost:hover { color: var(--text-primary); border-color: rgba(255,255,255,0.15); }

.btn-icon { font-size: 15px; line-height: 1; }

/* ─── Tabs ──────────────────────────────────────────── */
.tabs-wrap {
  display: flex; gap: 4px; margin-bottom: 28px;
  background: rgba(255,255,255,0.03);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 5px;
}
.tab-btn {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
  font-size: 13px; font-weight: 600; letter-spacing: 0.02em;
  padding: 10px 16px; border-radius: var(--radius-md);
  border: none; cursor: pointer;
  background: transparent; color: var(--text-muted);
  transition: all var(--transition);
  position: relative;
}
.tab-btn .tab-icon { font-size: 16px; }
.tab-btn.active {
  background: var(--bg-card-2);
  color: var(--text-primary);
  box-shadow: 0 2px 12px rgba(0,0,0,0.3);
}
.tab-btn.active .tab-label-accent { color: var(--cyan); }
.tab-btn:hover:not(.active) { color: var(--text-primary); }
.tab-count {
  font-family: var(--font-mono); font-size: 10px;
  background: rgba(0, 242, 255, 0.12); color: var(--cyan);
  border-radius: 100px; padding: 2px 8px; min-width: 24px;
  text-align: center;
}

.tab-panel { display: none; animation: fadeUp 0.25s ease; }
.tab-panel.active { display: block; }

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ─── Grid Layouts ──────────────────────────────────── */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.grid-sidebar { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }

/* ─── Card Base ─────────────────────────────────────── */
.card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 24px;
  position: relative; overflow: hidden;
  box-shadow: var(--shadow-card);
  transition: border-color var(--transition), box-shadow var(--transition), transform var(--transition);
}
.card:hover {
  border-color: rgba(0, 242, 255, 0.12);
  box-shadow: var(--shadow-card), 0 0 20px rgba(0, 242, 255, 0.06);
}
.card-glow::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 1px;
  background: var(--grad-mixed); opacity: 0.4;
}
.card-glow-purple::before { background: var(--grad-purple); }
.card-glow-warm::before   { background: var(--grad-warm); }

.card-header {
  display: flex; align-items: flex-start;
  justify-content: space-between; gap: 16px; margin-bottom: 20px;
}
.card-icon-wrap {
  width: 40px; height: 40px; border-radius: var(--radius-md);
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; flex-shrink: 0;
  background: rgba(0, 242, 255, 0.08);
  border: 1px solid rgba(0, 242, 255, 0.15);
}
.card-icon-wrap.purple {
  background: rgba(176, 140, 255, 0.08);
  border-color: rgba(176, 140, 255, 0.15);
}
.card-icon-wrap.warm {
  background: rgba(244,114,182,0.08);
  border-color: rgba(244,114,182,0.15);
}
.card-title {
  font-size: 15px; font-weight: 700; margin-bottom: 4px;
  letter-spacing: -0.01em;
}
.card-subtitle { font-size: 12px; color: var(--text-secondary); font-weight: 400; }

/* ─── Explain Topic Panel ───────────────────────────── */
.explain-preview {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 20px; min-height: 180px;
  position: relative;
}
.explain-preview-empty {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  min-height: 180px; gap: 12px; text-align: center;
}
.explain-preview-content { display: none; }

.empty-icon {
  font-size: 32px; opacity: 0.25;
  animation: float 3s ease-in-out infinite;
}
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-6px); }
}
.empty-text { font-size: 13px; color: var(--text-muted); font-family: var(--font-mono); }

.skeleton-lines { display: flex; flex-direction: column; gap: 10px; }
.sk-line {
  height: 10px; border-radius: 4px;
  background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.04) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.6s infinite;
}
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
.sk-line.w-90 { width: 90%; }
.sk-line.w-75 { width: 75%; }
.sk-line.w-85 { width: 85%; }
.sk-line.w-60 { width: 60%; }
.sk-line.w-40 { width: 40%; }

.concept-tag {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 11px; font-family: var(--font-mono);
  padding: 6px 12px; border-radius: 8px;
  border: 1px solid rgba(0, 242, 255, 0.15);
  color: var(--cyan); background: rgba(0, 242, 255, 0.05);
  cursor: pointer; transition: all var(--transition);
  margin: 4px;
}
.concept-tag:hover {
  border-color: rgba(0, 242, 255, 0.3);
  background: rgba(0, 242, 255, 0.1);
}

.depth-slider-wrap { margin-top: 16px; }
.depth-label {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 11px; color: var(--text-muted); font-family: var(--font-mono);
  margin-bottom: 8px;
}
.depth-label span { color: var(--cyan); }
input[type="range"] {
  -webkit-appearance: none; width: 100%; height: 3px;
  border-radius: 2px; outline: none;
  background: linear-gradient(90deg, var(--cyan) 50%, rgba(255,255,255,0.1) 50%);
  cursor: pointer;
}
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 14px; height: 14px; border-radius: 50%;
  background: var(--cyan); cursor: pointer;
  box-shadow: 0 0 8px rgba(0, 242, 255, 0.4);
  transition: box-shadow var(--transition);
}
input[type="range"]::-webkit-slider-thumb:hover {
  box-shadow: 0 0 14px rgba(0, 242, 255, 0.6);
}

/* ─── Quiz Section ──────────────────────────────────── */
.quiz-config-grid {
  display: grid; grid-template-columns: 1fr 1fr 1fr;
  gap: 12px; margin-bottom: 20px;
}
.question-type-grid {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 8px; margin-bottom: 20px;
}
.q-type-card {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 14px 10px; text-align: center;
  cursor: pointer; transition: all var(--transition);
}
.q-type-card:hover { border-color: rgba(0, 242, 255, 0.15); }
.q-type-card.selected {
  border-color: rgba(0, 242, 255, 0.3);
  background: rgba(0, 242, 255, 0.05);
}
.q-type-icon { font-size: 20px; margin-bottom: 6px; }
.q-type-label { font-size: 11px; color: var(--text-secondary); font-family: var(--font-mono); }
.q-type-card.selected .q-type-label { color: var(--cyan); }

.quiz-preview-card {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 20px; margin-top: 8px;
}
.quiz-question-num {
  font-size: 11px; font-family: var(--font-mono);
  color: var(--text-muted); margin-bottom: 10px;
}
.quiz-question-text {
  font-size: 14px; font-weight: 600; color: var(--text-primary);
  margin-bottom: 16px; line-height: 1.5;
}
.quiz-options { display: flex; flex-direction: column; gap: 8px; }
.quiz-option {
  display: flex; align-items: center; gap: 12px;
  background: rgba(255,255,255,0.03);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 11px 14px; cursor: pointer;
  transition: all var(--transition); font-size: 13px;
}
.quiz-option:hover { border-color: rgba(0, 242, 255, 0.15); background: rgba(0, 242, 255, 0.03); }
.quiz-option.selected {
  border-color: rgba(0, 242, 255, 0.3);
  background: rgba(0, 242, 255, 0.08);
  color: var(--cyan);
}
.quiz-option.correct {
  border-color: rgba(0, 200, 212, 0.5);
  background: rgba(0, 200, 212, 0.08);
  color: var(--teal);
}
.quiz-option-key {
  width: 22px; height: 22px; border-radius: var(--radius-sm);
  background: rgba(255,255,255,0.06);
  border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-family: var(--font-mono); font-weight: 600;
  flex-shrink: 0;
}

.quiz-footer {
  display: flex; align-items: center; justify-content: space-between;
  margin-top: 16px; padding-top: 16px;
  border-top: 1px solid var(--border);
}
.quiz-progress-bar-wrap {
  flex: 1; height: 3px; background: rgba(255,255,255,0.07);
  border-radius: 2px; margin-right: 16px; overflow: hidden;
}
.quiz-progress-bar-fill {
  height: 100%; width: 30%; border-radius: 2px;
  background: var(--grad-cyan);
  transition: width 0.5s ease;
}

/* ─── Study Plan Section ────────────────────────────── */
.plan-timeline { display: flex; flex-direction: column; gap: 1px; }
.plan-week {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}
.plan-week-header {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 18px; cursor: pointer;
  transition: background var(--transition);
}
.plan-week-header:hover { background: rgba(255,255,255,0.03); }
.plan-week-num {
  font-family: var(--font-mono); font-size: 10px;
  letter-spacing: 0.1em; text-transform: uppercase;
  color: var(--cyan); background: rgba(0, 242, 255, 0.08);
  border: 1px solid rgba(0, 242, 255, 0.15);
  padding: 4px 10px; border-radius: 6px;
  white-space: nowrap;
}
.plan-week-title {
  font-size: 13px; font-weight: 600; flex: 1;
}
.plan-week-meta {
  font-size: 11px; color: var(--text-muted);
  font-family: var(--font-mono);
}
.plan-week-chevron {
  color: var(--text-muted); font-size: 12px;
  transition: transform var(--transition);
}
.plan-week.open .plan-week-chevron { transform: rotate(180deg); }

.plan-week-body {
  display: none; padding: 0 18px 16px;
  border-top: 1px solid var(--border);
}
.plan-week.open .plan-week-body { display: block; }
.plan-day-list { display: flex; flex-direction: column; gap: 6px; margin-top: 12px; }
.plan-day {
  display: flex; align-items: center; gap: 10px;
  font-size: 12px; color: var(--text-secondary);
}
.plan-day-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: rgba(0, 242, 255, 0.2); flex-shrink: 0;
}
.plan-day.done .plan-day-dot { background: var(--cyan); box-shadow: 0 0 8px rgba(0, 242, 255, 0.3); }
.plan-day.done span { color: var(--text-primary); }
.plan-day-duration {
  margin-left: auto; font-family: var(--font-mono);
  font-size: 10px; color: var(--text-muted);
}

.plan-goal-selector { margin-bottom: 20px; }
.goal-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
.goal-chip {
  font-size: 12px; padding: 6px 14px;
  border-radius: 100px; border: 1px solid var(--border);
  color: var(--text-secondary); background: transparent;
  cursor: pointer; font-family: var(--font-mono);
  transition: all var(--transition);
}
.goal-chip:hover { border-color: rgba(176, 140, 255, 0.2); color: var(--purple); }
.goal-chip.active {
  border-color: rgba(176, 140, 255, 0.3);
  background: rgba(176, 140, 255, 0.06);
  color: var(--purple);
}

.intensity-grid {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 8px; margin-bottom: 20px;
}
.intensity-card {
  padding: 14px; border-radius: var(--radius-md);
  border: 1px solid var(--border);
  background: var(--bg-input);
  cursor: pointer; text-align: center;
  transition: all var(--transition);
}
.intensity-card:hover { border-color: rgba(176, 140, 255, 0.15); }
.intensity-card.selected {
  border-color: rgba(176, 140, 255, 0.3);
  background: rgba(176, 140, 255, 0.06);
}
.intensity-emoji { font-size: 22px; margin-bottom: 6px; }
.intensity-label {
  font-size: 12px; font-weight: 600; display: block; margin-bottom: 2px;
}
.intensity-card.selected .intensity-label { color: var(--purple); }
.intensity-desc {
  font-size: 10px; color: var(--text-muted);
  font-family: var(--font-mono); line-height: 1.4;
}

/* ─── Interview Prep Section ────────────────────────── */
.interview-role-grid {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 10px; margin-bottom: 20px;
}
.role-card {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 16px 12px; text-align: center;
  cursor: pointer; transition: all var(--transition);
}
.role-card:hover { border-color: rgba(244,114,182,0.25); }
.role-card.active {
  border-color: rgba(244,114,182,0.5);
  background: rgba(244,114,182,0.06);
}
.role-icon { font-size: 24px; margin-bottom: 8px; }
.role-title {
  font-size: 12px; font-weight: 600; margin-bottom: 2px;
}
.role-card.active .role-title { color: var(--pink); }
.role-count {
  font-size: 10px; color: var(--text-muted);
  font-family: var(--font-mono);
}

.interview-question-wrap { display: flex; flex-direction: column; gap: 10px; }
.iq-card {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 16px 18px;
  cursor: pointer; transition: all var(--transition);
  position: relative;
}
.iq-card:hover { border-color: rgba(244,114,182,0.2); }
.iq-card.expanded { border-color: rgba(244,114,182,0.35); }
.iq-header { display: flex; align-items: flex-start; gap: 12px; }
.iq-difficulty {
  font-size: 10px; font-family: var(--font-mono); letter-spacing: 0.08em;
  padding: 3px 8px; border-radius: 100px; white-space: nowrap;
  flex-shrink: 0;
}
.iq-difficulty.easy { background: rgba(45,212,191,0.1); color: var(--teal); border: 1px solid rgba(45,212,191,0.2); }
.iq-difficulty.med { background: rgba(251,191,36,0.1); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2); }
.iq-difficulty.hard { background: rgba(244,114,182,0.1); color: var(--pink); border: 1px solid rgba(244,114,182,0.2); }
.iq-text { font-size: 13px; font-weight: 500; flex: 1; line-height: 1.5; }
.iq-chevron {
  color: var(--text-muted); font-size: 12px; flex-shrink: 0;
  transition: transform var(--transition);
}
.iq-card.expanded .iq-chevron { transform: rotate(180deg); }
.iq-answer {
  display: none; margin-top: 14px; padding-top: 14px;
  border-top: 1px solid var(--border);
  font-size: 12px; color: var(--text-secondary); line-height: 1.7;
  font-family: var(--font-mono);
}
.iq-card.expanded .iq-answer { display: block; }
.iq-tags { display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; }
.iq-tag {
  font-size: 10px; padding: 2px 8px; border-radius: 100px;
  background: rgba(255,255,255,0.04);
  border: 1px solid var(--border);
  color: var(--text-muted); font-family: var(--font-mono);
}

/* ─── Sidebar Widgets ───────────────────────────────── */
.widget-stack { display: flex; flex-direction: column; gap: 16px; }

.proficiency-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 20px; overflow: hidden; position: relative;
}
.proficiency-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 1px;
  background: var(--grad-mixed);
}

.prof-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px;
}
.prof-title { font-size: 13px; font-weight: 700; }
.prof-score {
  font-size: 22px; font-weight: 800;
  background: var(--grad-cyan);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  background-clip: text;
}

.radial-wrap {
  display: flex; justify-content: center; margin: 16px 0;
  position: relative;
}
.radial-svg { width: 100px; height: 100px; transform: rotate(-90deg); }
.radial-track { fill: none; stroke: rgba(255,255,255,0.07); stroke-width: 6; }
.radial-fill {
  fill: none; stroke-width: 6; stroke-linecap: round;
  stroke: url(#grad-radial);
  stroke-dasharray: 251;
  stroke-dashoffset: 75;
  transition: stroke-dashoffset 1s ease;
}
.radial-label {
  position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
  text-align: center;
}
.radial-label-val {
  font-size: 20px; font-weight: 800;
  background: var(--grad-cyan);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  background-clip: text; line-height: 1;
}
.radial-label-sub {
  font-size: 9px; color: var(--text-muted);
  font-family: var(--font-mono); letter-spacing: 0.06em;
}

.skill-bars { display: flex; flex-direction: column; gap: 10px; }
.skill-bar-item { display: flex; flex-direction: column; gap: 5px; }
.skill-bar-label {
  display: flex; justify-content: space-between;
  font-size: 11px; color: var(--text-secondary); font-family: var(--font-mono);
}
.skill-bar-label span { color: var(--text-accent); }
.skill-bar-track {
  height: 4px; border-radius: 2px;
  background: rgba(255,255,255,0.07); overflow: hidden;
}
.skill-bar-fill {
  height: 100%; border-radius: 2px;
  transition: width 1.2s cubic-bezier(0.4,0,0.2,1);
}

.activity-list { display: flex; flex-direction: column; gap: 2px; }
.activity-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 10px 14px; border-radius: var(--radius-md);
  transition: background var(--transition); cursor: default;
}
.activity-item:hover { background: rgba(255,255,255,0.025); }
.activity-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: var(--cyan); margin-top: 5px; flex-shrink: 0;
}
.activity-dot.purple { background: var(--purple); }
.activity-dot.pink { background: var(--pink); }
.activity-info { flex: 1; min-width: 0; }
.activity-text {
  font-size: 12px; font-weight: 500;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.activity-time {
  font-size: 10px; color: var(--text-muted); font-family: var(--font-mono);
  margin-top: 2px;
}

.streak-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
.streak-day {
  aspect-ratio: 1; border-radius: 3px;
  background: rgba(255,255,255,0.05);
  transition: background var(--transition);
}
.streak-day.s1 { background: rgba(0, 242, 255, 0.15); }
.streak-day.s2 { background: rgba(0, 242, 255, 0.3); }
.streak-day.s3 { background: rgba(0, 242, 255, 0.5); }
.streak-day.s4 { background: rgba(0, 242, 255, 0.8); }
.streak-day-labels {
  display: grid; grid-template-columns: repeat(7, 1fr);
  gap: 4px; margin-bottom: 4px;
}
.streak-day-lbl {
  font-size: 9px; color: var(--text-muted); font-family: var(--font-mono);
  text-align: center; letter-spacing: 0.04em;
}

.suggest-chip-wrap { display: flex; flex-wrap: wrap; gap: 6px; }
.suggest-chip {
  font-size: 11px; padding: 5px 12px; border-radius: 100px;
  border: 1px solid var(--border);
  color: var(--text-secondary); background: rgba(255,255,255,0.03);
  cursor: pointer; font-family: var(--font-mono);
  transition: all var(--transition);
}
.suggest-chip:hover {
  border-color: rgba(0, 242, 255, 0.2);
  color: var(--cyan); background: rgba(0, 242, 255, 0.04);
}

/* ─── Stats Strip ───────────────────────────────────── */
.stats-strip {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 12px; margin-bottom: 28px;
}
.stat-tile {
  background: var(--bg-card); border: 1px solid var(--border);
  border-radius: var(--radius-lg); padding: 18px 20px;
  position: relative; overflow: hidden;
  transition: all var(--transition);
}
.stat-tile:hover { border-color: rgba(0, 242, 255, 0.12); }
.stat-tile::after {
  content: attr(data-icon);
  position: absolute; right: 14px; bottom: 10px;
  font-size: 28px; opacity: 0.12;
}
.stat-tile-val {
  font-size: 28px; font-weight: 800; letter-spacing: -0.02em;
  background: var(--grad-mixed);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  background-clip: text; line-height: 1.2;
}
.stat-tile-label {
  font-size: 11px; color: var(--text-muted);
  font-family: var(--font-mono); margin-top: 4px;
  letter-spacing: 0.04em;
}
.stat-tile-delta {
  font-size: 10px; font-family: var(--font-mono);
  color: var(--teal); margin-top: 8px;
}

/* ─── Section Title ─────────────────────────────────── */
.section-title {
  font-size: 12px; font-weight: 600; letter-spacing: 0.1em;
  text-transform: uppercase; color: var(--text-muted);
  font-family: var(--font-mono); margin-bottom: 14px;
  display: flex; align-items: center; gap: 10px;
}
.section-title::after {
  content: ''; flex: 1; height: 1px;
  background: var(--border);
}

/* ─── Scrollbar ─────────────────────────────────────── */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.18); }

/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 1024px) {
  .grid-sidebar { grid-template-columns: 1fr; }
  .cp-grid { grid-template-columns: 1fr 1fr; }
  .cp-grid .btn-primary { grid-column: span 2; }
  .stats-strip { grid-template-columns: repeat(2, 1fr); }
  .interview-role-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 900px) {
  .sidebar {
    width: 60px;
    align-items: center;
  }

  .sidebar-brand {
    justify-content: center;
    height: 60px;
    padding: 22px 8px 18px;
  }

  .sidebar-brand-text {
    display: none;
  }

  .sidebar-item {
    justify-content: center;
    padding: 10px 8px;
  }

  .sidebar-item-icon {
    margin-right: 0;
  }

  .main-content {
    margin-left: 60px;
    width: calc(100% - 60px);
  }

  .top-header {
    padding: 0 16px;
  }

  .header-left {
    gap: 8px;
  }

  .environment-text {
    font-size: 12px;
  }

  .page-wrap {
    padding: 0 16px 60px;
  }
}

@media (max-width: 768px) {
  .hero { grid-template-columns: 1fr; }
  .hero-cta-group { align-items: flex-start; }
  .badge-group { justify-content: flex-start; }
  .grid-2 { grid-template-columns: 1fr; }
  .grid-3 { grid-template-columns: 1fr; }
  .tabs-wrap { overflow-x: auto; }
  .tab-btn { font-size: 12px; padding: 9px 12px; }
  .quiz-config-grid { grid-template-columns: 1fr 1fr; }
  .question-type-grid { grid-template-columns: repeat(2, 1fr); }
  .cp-grid { grid-template-columns: 1fr; }
  .cp-grid .btn-primary { grid-column: 1; }
  .intensity-grid { grid-template-columns: 1fr; }
  .stats-strip { grid-template-columns: 1fr 1fr; }
  .interview-role-grid { grid-template-columns: repeat(2, 1fr); }
  .sidebar { width: 50px; }
  .main-content { margin-left: 50px; width: calc(100% - 50px); }
  .top-header { padding: 0 12px; }
  .page-wrap { padding: 0 12px 60px; }
}
</style>
</head>
<body>
<div class="grid-bg"></div>
<div class="ambient ambient-1"></div>
<div class="ambient ambient-2"></div>
<div class="ambient ambient-3"></div>

<div class="container-wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            <span class="sidebar-brand-text">ElevUra</span>
        </div>
        <nav class="sidebar-menu">
            <a href="dashboard.html" class="sidebar-item">
                <span class="sidebar-item-icon sidebar-item-icon-terminal" aria-hidden="true">&gt;_</span>
                <span>Command Center</span>
            </a>
            <a href="dashboard.html" class="sidebar-item">
                <span class="sidebar-item-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <span>AI Career Coach</span>
            </a>
            <a href="#" class="sidebar-item">
                <span class="sidebar-item-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                </span>
                <span>CV Optimizer</span>
            </a>
            <a href="StudyBuddy.php" class="sidebar-item active">
                <span class="sidebar-item-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><circle cx="9" cy="12" r="5"/><circle cx="15" cy="12" r="5"/></svg>
                </span>
                <span>Study Buddy</span>
            </a>
            <a href="ResearchAssistant.php" class="sidebar-item">
                <span class="sidebar-item-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M9 3h6v2H9V3z"/><path d="M10 5v5.2c0 .86-.37 1.68-1 2.26L6 16h12l-3-3.54c-.63-.58-1-1.4-1-2.26V5"/><path d="M6 16h12v2a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-2z"/></svg>
                </span>
                <span>Research Assistant</span>
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- TOP HEADER -->
        <header class="top-header">
            <div class="header-left">
                <div class="header-left-meta">
                    <div class="environment-text">
                        Environment: <span class="production">Production</span>
                    </div>
                </div>
            </div>

            <div class="header-right">
                <div class="notification-icon" title="Notifications" aria-label="Notifications">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    <span class="notification-dot"></span>
                </div>

                <div class="user-info">
                    <div class="user-avatar">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=128&h=128&fit=crop&crop=faces" width="36" height="36" alt="" loading="lazy" decoding="async">
                    </div>
                    <div class="user-meta">
                        <div class="user-name">Alex Mercer</div>
                        <div class="user-tier">Pro Tier</div>
                    </div>
                </div>

                <button type="button" class="header-fullscreen" title="Fullscreen" aria-label="Toggle fullscreen">
                    <svg viewBox="0 0 24 24"><path d="M8 3H5a2 2 0 0 0-2 2v3M21 8V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3M16 21h3a2 2 0 0 0 2-2v-3"/></svg>
                </button>
            </div>
        </header>

        <!-- CONTENT AREA -->
        <section class="content-area">

<div class="page-wrap">

  <!-- Breadcrumb / Topbar -->
  <div class="topbar">
    <span>Dashboard</span>
    <span class="topbar-sep">/</span>
    <span>Learning</span>
    <span class="topbar-sep">/</span>
    <span class="topbar-current">Study Buddy</span>
    <div class="topbar-dot"></div>
  </div>

  <!-- Hero -->
  <section class="hero">
    <div>
      <div class="hero-eyebrow">AI-Powered Learning</div>
      <h1 class="hero-title">
        Your Personal<br>
        <span class="highlight">Study Buddy</span>
      </h1>
      <p class="hero-desc">
        Master any topic with AI-powered explanations, adaptive quizzes, personalized study plans, and targeted interview preparation — all in one place.
      </p>
      <div class="hero-meta">
        <div class="hero-stat">
          <div class="hero-stat-val">2,400+</div>
          <div class="hero-stat-label">Topics covered</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
          <div class="hero-stat-val">98%</div>
          <div class="hero-stat-label">Retention rate</div>
        </div>
        <div class="hero-divider"></div>
        <div class="hero-stat">
          <div class="hero-stat-val">4.9★</div>
          <div class="hero-stat-label">User rating</div>
        </div>
      </div>
    </div>
    <div class="hero-cta-group">
      <div class="badge-group">
        <span class="badge cyan">Adaptive Learning</span>
        <span class="badge purple">AI-Powered</span>
        <span class="badge pink">Interview Ready</span>
      </div>
      <button class="btn btn-primary">
        <span class="btn-icon">⚡</span> Quick Start
      </button>
      <button class="btn btn-secondary">
        <span class="btn-icon">📋</span> Resume Session
      </button>
    </div>
  </section>

  <!-- Stats Strip -->
  <div class="stats-strip">
    <div class="stat-tile" data-icon="📚">
      <div class="stat-tile-val">14</div>
      <div class="stat-tile-label">Topics studied</div>
      <div class="stat-tile-delta">↑ 3 this week</div>
    </div>
    <div class="stat-tile" data-icon="🧩">
      <div class="stat-tile-val">87%</div>
      <div class="stat-tile-label">Quiz accuracy</div>
      <div class="stat-tile-delta">↑ 12% vs last week</div>
    </div>
    <div class="stat-tile" data-icon="🔥">
      <div class="stat-tile-val">9</div>
      <div class="stat-tile-label">Day streak</div>
      <div class="stat-tile-delta">Personal best: 21</div>
    </div>
    <div class="stat-tile" data-icon="🎯">
      <div class="stat-tile-val">3</div>
      <div class="stat-tile-label">Goals active</div>
      <div class="stat-tile-delta">1 due this week</div>
    </div>
  </div>

  <!-- Input Control Panel -->
  <div class="control-panel">
    <div class="cp-header">
      <span class="cp-title">// Learning Configuration</span>
      <div class="cp-mode-toggle">
        <button class="cp-mode-btn active" data-mode="guided">Guided</button>
        <button class="cp-mode-btn" data-mode="freeform">Freeform</button>
        <button class="cp-mode-btn" data-mode="adaptive">Adaptive</button>
      </div>
    </div>
    <div class="cp-grid">
      <div class="field">
        <label class="field-label">Topic or Concept</label>
        <div class="input-wrap">
          <span class="input-icon">🔍</span>
          <input type="text" placeholder="e.g. Binary search trees, Async/Await, React hooks…" />
        </div>
      </div>
      <div class="field">
        <label class="field-label">Difficulty</label>
        <div class="input-wrap">
          <span class="input-icon">⚙️</span>
          <select>
            <option>Beginner</option>
            <option selected>Intermediate</option>
            <option>Advanced</option>
            <option>Expert</option>
          </select>
        </div>
      </div>
      <div class="field">
        <label class="field-label">Learning Goal</label>
        <div class="input-wrap">
          <span class="input-icon">🎯</span>
          <select>
            <option>Understand concepts</option>
            <option>Pass an exam</option>
            <option>Job interview prep</option>
            <option>Build a project</option>
          </select>
        </div>
      </div>
      <div class="field">
        <label class="field-label">&nbsp;</label>
        <button class="btn btn-primary">
          <span class="btn-icon">✦</span> Generate
        </button>
      </div>
    </div>
  </div>

  <!-- Tabs -->
  <div class="tabs-wrap">
    <button class="tab-btn active" data-tab="explain">
      <span class="tab-icon">💡</span>
      <span class="tab-label-accent">Explain</span>
      <span style="display:none" class="tab-label"> Topic</span>
    </button>
    <button class="tab-btn" data-tab="quiz">
      <span class="tab-icon">🧩</span>
      <span class="tab-label-accent">Quiz</span>
      <span style="display:none" class="tab-label"> Me</span>
      <span class="tab-count">12</span>
    </button>
    <button class="tab-btn" data-tab="plan">
      <span class="tab-icon">📅</span>
      <span class="tab-label-accent">Study</span>
      <span style="display:none" class="tab-label"> Plan</span>
    </button>
    <button class="tab-btn" data-tab="interview">
      <span class="tab-icon">🎤</span>
      <span class="tab-label-accent">Interview</span>
      <span style="display:none" class="tab-label"> Prep</span>
      <span class="tab-count">48</span>
    </button>
    <button class="tab-btn" data-tab="flashcards">
      <span class="tab-icon">🃏</span>
      <span class="tab-label-accent">Flash</span>
      <span style="display:none" class="tab-label">cards</span>
    </button>
  </div>

  <!-- ── TAB: Explain ─────────────────────────────── -->
  <div class="tab-panel active" id="tab-explain">
    <div class="grid-sidebar">
      <div>
        <div class="section-title">Explanation Output</div>

        <div class="card card-glow" style="margin-bottom: 16px;">
          <div class="card-header">
            <div style="display:flex;gap:12px;align-items:center">
              <div class="card-icon-wrap">💡</div>
              <div>
                <div class="card-title">Explain a Topic</div>
                <div class="card-subtitle">AI-generated breakdown with examples</div>
              </div>
            </div>
            <button class="btn btn-ghost">
              <span class="btn-icon">⚡</span> Explain
            </button>
          </div>

          <div class="explain-preview" id="explain-preview">
            <div class="explain-preview-empty" id="explain-empty">
              <div class="empty-icon">📖</div>
              <div class="empty-text">Enter a topic above and click Generate</div>
            </div>
            <div class="explain-preview-content" id="explain-content">
              <div style="margin-bottom:16px;">
                <div style="font-size:11px;font-family:var(--font-mono);color:var(--text-muted);margin-bottom:8px;">CONCEPT OVERVIEW</div>
                <div class="skeleton-lines">
                  <div class="sk-line w-90"></div>
                  <div class="sk-line w-75"></div>
                  <div class="sk-line w-85"></div>
                  <div class="sk-line w-60"></div>
                </div>
              </div>
              <div style="margin-bottom:16px;">
                <div style="font-size:11px;font-family:var(--font-mono);color:var(--text-muted);margin-bottom:8px;">KEY CONCEPTS</div>
                <div class="concept-tag">Time Complexity</div>
                <div class="concept-tag">Recursion</div>
                <div class="concept-tag">Tree Traversal</div>
                <div class="concept-tag">Balancing</div>
                <div class="concept-tag">Big-O Notation</div>
              </div>
              <div class="skeleton-lines">
                <div class="sk-line w-85"></div>
                <div class="sk-line w-40"></div>
              </div>
            </div>
          </div>

          <div class="depth-slider-wrap">
            <div class="depth-label">
              <span>Explanation Depth</span>
              <span id="depth-val">Intermediate</span>
            </div>
            <input type="range" min="1" max="4" value="2" id="depth-slider" />
          </div>
        </div>

        <div class="section-title">Suggested Subtopics</div>
        <div class="card card-glow" style="margin-bottom: 16px;">
          <div class="suggest-chip-wrap">
            <span class="suggest-chip">→ Binary Search</span>
            <span class="suggest-chip">→ Linked Lists</span>
            <span class="suggest-chip">→ Hash Maps</span>
            <span class="suggest-chip">→ Graph Theory</span>
            <span class="suggest-chip">→ Dynamic Programming</span>
            <span class="suggest-chip">→ Sorting Algorithms</span>
            <span class="suggest-chip">→ Big-O Analysis</span>
            <span class="suggest-chip">→ Memory Management</span>
          </div>
        </div>

        <div class="section-title">Related Concepts</div>
        <div class="grid-3">
          <div class="card card-glow" style="padding:16px;cursor:pointer;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
            <div style="font-size:20px;margin-bottom:8px;">🌿</div>
            <div style="font-size:13px;font-weight:700;margin-bottom:4px;">Trees & Graphs</div>
            <div style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">12 subtopics</div>
          </div>
          <div class="card card-glow-purple" style="padding:16px;cursor:pointer;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
            <div style="font-size:20px;margin-bottom:8px;">⚙️</div>
            <div style="font-size:13px;font-weight:700;margin-bottom:4px;">Algorithms</div>
            <div style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">24 subtopics</div>
          </div>
          <div class="card card-glow-warm" style="padding:16px;cursor:pointer;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
            <div style="font-size:20px;margin-bottom:8px;">🧮</div>
            <div style="font-size:13px;font-weight:700;margin-bottom:4px;">Complexity</div>
            <div style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">8 subtopics</div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="widget-stack">
        <div class="proficiency-card">
          <div class="prof-header">
            <span class="prof-title">Proficiency</span>
          </div>
          <div class="radial-wrap">
            <svg class="radial-svg" viewBox="0 0 100 100">
              <defs>
                <linearGradient id="grad-radial" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#63d7d2"/>
                  <stop offset="100%" stop-color="#a78bfa"/>
                </linearGradient>
              </defs>
              <circle class="radial-track" cx="50" cy="50" r="40"/>
              <circle class="radial-fill" cx="50" cy="50" r="40"/>
            </svg>
            <div class="radial-label">
              <div class="radial-label-val">70%</div>
              <div class="radial-label-sub">mastery</div>
            </div>
          </div>
          <div class="skill-bars">
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Theory</span><span>82%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:82%;background:var(--grad-cyan)"></div></div>
            </div>
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Application</span><span>65%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:65%;background:var(--grad-purple)"></div></div>
            </div>
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Problem Solving</span><span>71%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:71%;background:var(--grad-mixed)"></div></div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="prof-header" style="margin-bottom:12px;">
            <span class="prof-title">Weekly Activity</span>
            <span style="font-size:10px;font-family:var(--font-mono);color:var(--text-muted);">Last 28 days</span>
          </div>
          <div class="streak-day-labels">
            <span class="streak-day-lbl">M</span><span class="streak-day-lbl">T</span>
            <span class="streak-day-lbl">W</span><span class="streak-day-lbl">T</span>
            <span class="streak-day-lbl">F</span><span class="streak-day-lbl">S</span>
            <span class="streak-day-lbl">S</span>
          </div>
          <div class="streak-grid">
            <div class="streak-day s1"></div><div class="streak-day s3"></div><div class="streak-day s2"></div><div class="streak-day s4"></div><div class="streak-day s1"></div><div class="streak-day"></div><div class="streak-day"></div>
            <div class="streak-day s2"></div><div class="streak-day s4"></div><div class="streak-day s3"></div><div class="streak-day s2"></div><div class="streak-day s4"></div><div class="streak-day s1"></div><div class="streak-day"></div>
            <div class="streak-day s3"></div><div class="streak-day s2"></div><div class="streak-day s4"></div><div class="streak-day s3"></div><div class="streak-day s2"></div><div class="streak-day s4"></div><div class="streak-day s1"></div>
            <div class="streak-day s4"></div><div class="streak-day s3"></div><div class="streak-day s4"></div><div class="streak-day s2"></div><div class="streak-day s4"></div><div class="streak-day"></div><div class="streak-day"></div>
          </div>
        </div>

        <div class="card">
          <div class="prof-header" style="margin-bottom:12px;">
            <span class="prof-title">Recent Activity</span>
          </div>
          <div class="activity-list">
            <div class="activity-item">
              <div class="activity-dot"></div>
              <div class="activity-info">
                <div class="activity-text">Explained: React Fiber Architecture</div>
                <div class="activity-time">2 hours ago</div>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-dot purple"></div>
              <div class="activity-info">
                <div class="activity-text">Quiz: CSS Grid — 9/10 correct</div>
                <div class="activity-time">Yesterday, 8:45 PM</div>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-dot pink"></div>
              <div class="activity-info">
                <div class="activity-text">Interview: System Design Q&A</div>
                <div class="activity-time">2 days ago</div>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-dot"></div>
              <div class="activity-info">
                <div class="activity-text">Study Plan: TypeScript Basics</div>
                <div class="activity-time">3 days ago</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── TAB: Quiz ────────────────────────────────── -->
  <div class="tab-panel" id="tab-quiz">
    <div class="grid-sidebar">
      <div>
        <div class="section-title">Quiz Configuration</div>
        <div class="card card-glow" style="margin-bottom: 16px;">
          <div class="card-header">
            <div style="display:flex;gap:12px;align-items:center">
              <div class="card-icon-wrap">🧩</div>
              <div>
                <div class="card-title">Build a Quiz</div>
                <div class="card-subtitle">Adaptive questions based on your level</div>
              </div>
            </div>
            <button class="btn btn-ghost">⚙️ Configure</button>
          </div>
          <div class="quiz-config-grid">
            <div class="field">
              <label class="field-label">Questions</label>
              <div class="input-wrap">
                <span class="input-icon">#</span>
                <select>
                  <option>5 questions</option>
                  <option selected>10 questions</option>
                  <option>20 questions</option>
                  <option>Custom</option>
                </select>
              </div>
            </div>
            <div class="field">
              <label class="field-label">Time Limit</label>
              <div class="input-wrap">
                <span class="input-icon">⏱</span>
                <select>
                  <option>No limit</option>
                  <option>30 sec / Q</option>
                  <option selected>60 sec / Q</option>
                  <option>2 min / Q</option>
                </select>
              </div>
            </div>
            <div class="field">
              <label class="field-label">Focus Area</label>
              <div class="input-wrap">
                <span class="input-icon">🎯</span>
                <select>
                  <option>All areas</option>
                  <option selected>Weak spots</option>
                  <option>Recent topics</option>
                </select>
              </div>
            </div>
          </div>

          <div style="margin-bottom:20px;">
            <div class="field-label" style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:10px;">Question Types</div>
            <div class="question-type-grid">
              <div class="q-type-card selected">
                <div class="q-type-icon">✔</div>
                <div class="q-type-label">Multiple Choice</div>
              </div>
              <div class="q-type-card">
                <div class="q-type-icon">T/F</div>
                <div class="q-type-label">True / False</div>
              </div>
              <div class="q-type-card selected">
                <div class="q-type-icon">✏️</div>
                <div class="q-type-label">Fill Blank</div>
              </div>
              <div class="q-type-card">
                <div class="q-type-icon">💬</div>
                <div class="q-type-label">Open Ended</div>
              </div>
            </div>
          </div>

          <div style="display:flex;gap:12px;">
            <button class="btn btn-primary" style="flex:1;">
              <span class="btn-icon">⚡</span> Generate Quiz
            </button>
            <button class="btn btn-secondary">
              <span class="btn-icon">🔀</span> Randomize
            </button>
          </div>
        </div>

        <div class="section-title">Preview — Question 3 of 10</div>
        <div class="card card-glow">
          <div class="quiz-question-num">QUESTION 03 / 10 · INTERMEDIATE · Binary Trees</div>
          <div class="quiz-question-text">
            What is the time complexity of searching for a value in a balanced Binary Search Tree?
          </div>
          <div class="quiz-options">
            <div class="quiz-option" onclick="selectOption(this)">
              <div class="quiz-option-key">A</div>
              O(n) — Linear time
            </div>
            <div class="quiz-option correct" onclick="selectOption(this)">
              <div class="quiz-option-key">B</div>
              O(log n) — Logarithmic time
            </div>
            <div class="quiz-option" onclick="selectOption(this)">
              <div class="quiz-option-key">C</div>
              O(n²) — Quadratic time
            </div>
            <div class="quiz-option" onclick="selectOption(this)">
              <div class="quiz-option-key">D</div>
              O(1) — Constant time
            </div>
          </div>
          <div class="quiz-footer">
            <div class="quiz-progress-bar-wrap">
              <div class="quiz-progress-bar-fill" id="quiz-bar"></div>
            </div>
            <div style="display:flex;gap:8px;">
              <button class="btn btn-ghost">← Prev</button>
              <button class="btn btn-primary">Next →</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="widget-stack">
        <div class="card card-glow">
          <div class="prof-header" style="margin-bottom:16px;">
            <span class="prof-title">Quiz Stats</span>
          </div>
          <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-md);">
              <span style="font-size:12px;color:var(--text-secondary);font-family:var(--font-mono);">Completed today</span>
              <span style="font-size:16px;font-weight:700;color:var(--cyan);">3</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-md);">
              <span style="font-size:12px;color:var(--text-secondary);font-family:var(--font-mono);">Average score</span>
              <span style="font-size:16px;font-weight:700;color:var(--purple);">87%</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-md);">
              <span style="font-size:12px;color:var(--text-secondary);font-family:var(--font-mono);">Best streak</span>
              <span style="font-size:16px;font-weight:700;color:var(--pink);">14 correct</span>
            </div>
          </div>
        </div>

        <div class="card card-glow-purple">
          <div class="prof-header" style="margin-bottom:14px;">
            <span class="prof-title">Topic Performance</span>
          </div>
          <div class="skill-bars">
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Data Structures</span><span>91%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:91%;background:var(--grad-cyan)"></div></div>
            </div>
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Algorithms</span><span>74%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:74%;background:var(--grad-purple)"></div></div>
            </div>
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>System Design</span><span>58%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:58%;background:var(--grad-warm)"></div></div>
            </div>
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>JavaScript</span><span>88%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:88%;background:var(--grad-mixed)"></div></div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="prof-header" style="margin-bottom:12px;">
            <span class="prof-title">Saved Quizzes</span>
          </div>
          <div class="activity-list">
            <div class="activity-item">
              <div class="activity-dot"></div>
              <div class="activity-info">
                <div class="activity-text">React Hooks Deep Dive</div>
                <div class="activity-time">10 questions · 85%</div>
              </div>
              <button class="btn btn-ghost" style="padding:5px 10px;font-size:11px;">▶</button>
            </div>
            <div class="activity-item">
              <div class="activity-dot purple"></div>
              <div class="activity-info">
                <div class="activity-text">CSS Flexbox & Grid</div>
                <div class="activity-time">15 questions · 92%</div>
              </div>
              <button class="btn btn-ghost" style="padding:5px 10px;font-size:11px;">▶</button>
            </div>
            <div class="activity-item">
              <div class="activity-dot pink"></div>
              <div class="activity-info">
                <div class="activity-text">Node.js Fundamentals</div>
                <div class="activity-time">20 questions · 79%</div>
              </div>
              <button class="btn btn-ghost" style="padding:5px 10px;font-size:11px;">▶</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── TAB: Study Plan ──────────────────────────── -->
  <div class="tab-panel" id="tab-plan">
    <div class="grid-sidebar">
      <div>
        <div class="section-title">Plan Builder</div>
        <div class="card card-glow-purple" style="margin-bottom:16px;">
          <div class="card-header">
            <div style="display:flex;gap:12px;align-items:center">
              <div class="card-icon-wrap purple">📅</div>
              <div>
                <div class="card-title">Personalized Study Plan</div>
                <div class="card-subtitle">Structured roadmap tailored to your goals</div>
              </div>
            </div>
          </div>

          <div class="plan-goal-selector">
            <div class="field-label" style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:10px;">Your Goal</div>
            <div class="goal-chips">
              <button class="goal-chip active">Land a Dev Job</button>
              <button class="goal-chip">Pass a Certification</button>
              <button class="goal-chip">Learn a Framework</button>
              <button class="goal-chip">Build a Portfolio</button>
              <button class="goal-chip">Academic Exam</button>
              <button class="goal-chip">Side Project</button>
            </div>
          </div>

          <div class="field-label" style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:10px;">Study Intensity</div>
          <div class="intensity-grid">
            <div class="intensity-card">
              <div class="intensity-emoji">🌱</div>
              <span class="intensity-label">Casual</span>
              <div class="intensity-desc">30 min/day<br>Low pressure</div>
            </div>
            <div class="intensity-card selected">
              <div class="intensity-emoji">🚀</div>
              <span class="intensity-label">Focused</span>
              <div class="intensity-desc">90 min/day<br>Steady growth</div>
            </div>
            <div class="intensity-card">
              <div class="intensity-emoji">🔥</div>
              <span class="intensity-label">Intensive</span>
              <div class="intensity-desc">3+ hrs/day<br>Fast track</div>
            </div>
          </div>

          <div style="display:flex;gap:12px;">
            <button class="btn btn-primary" style="flex:1;">
              <span class="btn-icon">✦</span> Build My Plan
            </button>
            <button class="btn btn-secondary">
              <span class="btn-icon">⬇</span> Export
            </button>
          </div>
        </div>

        <div class="section-title">4-Week Roadmap — JavaScript Mastery</div>
        <div class="plan-timeline">
          <div class="plan-week open">
            <div class="plan-week-header" onclick="toggleWeek(this)">
              <span class="plan-week-num">WEEK 01</span>
              <span class="plan-week-title">Foundations & Core Concepts</span>
              <span class="plan-week-meta">5 days · 7.5 hrs</span>
              <span class="plan-week-chevron">▾</span>
            </div>
            <div class="plan-week-body">
              <div class="plan-day-list">
                <div class="plan-day done"><div class="plan-day-dot"></div><span>Variables, Scope & Hoisting</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day done"><div class="plan-day-dot"></div><span>Functions & Closures</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day done"><div class="plan-day-dot"></div><span>Prototypes & Inheritance</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Event Loop & Async</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Week 1 Review Quiz</span><div class="plan-day-duration">60 min</div></div>
              </div>
            </div>
          </div>
          <div class="plan-week">
            <div class="plan-week-header" onclick="toggleWeek(this)">
              <span class="plan-week-num">WEEK 02</span>
              <span class="plan-week-title">Modern JavaScript (ES6+)</span>
              <span class="plan-week-meta">5 days · 8 hrs</span>
              <span class="plan-week-chevron">▾</span>
            </div>
            <div class="plan-week-body">
              <div class="plan-day-list">
                <div class="plan-day"><div class="plan-day-dot"></div><span>Destructuring & Spread</span><div class="plan-day-duration">60 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Promises & Async/Await</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Modules & Bundling</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Generators & Iterators</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Week 2 Practice Project</span><div class="plan-day-duration">120 min</div></div>
              </div>
            </div>
          </div>
          <div class="plan-week">
            <div class="plan-week-header" onclick="toggleWeek(this)">
              <span class="plan-week-num">WEEK 03</span>
              <span class="plan-week-title">Browser APIs & DOM Mastery</span>
              <span class="plan-week-meta">5 days · 9 hrs</span>
              <span class="plan-week-chevron">▾</span>
            </div>
            <div class="plan-week-body">
              <div class="plan-day-list">
                <div class="plan-day"><div class="plan-day-dot"></div><span>DOM Manipulation</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Fetch API & REST</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Web Storage & Cookies</span><div class="plan-day-duration">60 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Performance Optimization</span><div class="plan-day-duration">120 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Week 3 Assessment</span><div class="plan-day-duration">90 min</div></div>
              </div>
            </div>
          </div>
          <div class="plan-week">
            <div class="plan-week-header" onclick="toggleWeek(this)">
              <span class="plan-week-num">WEEK 04</span>
              <span class="plan-week-title">Advanced Patterns & Interview Prep</span>
              <span class="plan-week-meta">5 days · 10 hrs</span>
              <span class="plan-week-chevron">▾</span>
            </div>
            <div class="plan-week-body">
              <div class="plan-day-list">
                <div class="plan-day"><div class="plan-day-dot"></div><span>Design Patterns</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>TypeScript Transition</span><div class="plan-day-duration">120 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Testing with Jest</span><div class="plan-day-duration">90 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Mock Interview Session</span><div class="plan-day-duration">120 min</div></div>
                <div class="plan-day"><div class="plan-day-dot"></div><span>Final Project & Review</span><div class="plan-day-duration">120 min</div></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="widget-stack">
        <div class="proficiency-card">
          <div class="prof-header" style="margin-bottom:14px;">
            <span class="prof-title">Plan Progress</span>
            <span style="font-size:11px;font-family:var(--font-mono);color:var(--text-muted);">Week 1/4</span>
          </div>
          <div class="skill-bars">
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Overall</span><span>38%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:38%;background:var(--grad-mixed)"></div></div>
            </div>
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Week 1</span><span>60%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:60%;background:var(--grad-purple)"></div></div>
            </div>
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Consistency</span><span>80%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:80%;background:var(--grad-cyan)"></div></div>
            </div>
          </div>
        </div>

        <div class="card card-glow">
          <div class="prof-header" style="margin-bottom:12px;">
            <span class="prof-title">Upcoming</span>
          </div>
          <div class="activity-list">
            <div class="activity-item">
              <div class="activity-dot cyan"></div>
              <div class="activity-info">
                <div class="activity-text">Event Loop & Async</div>
                <div class="activity-time">Today · 90 min</div>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-dot purple"></div>
              <div class="activity-info">
                <div class="activity-text">Week 1 Review Quiz</div>
                <div class="activity-time">Tomorrow · 60 min</div>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-dot"></div>
              <div class="activity-info">
                <div class="activity-text">Destructuring & Spread</div>
                <div class="activity-time">In 2 days · 60 min</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card card-glow-purple" style="text-align:center;padding:28px 20px;">
          <div style="font-size:32px;margin-bottom:12px;">🎯</div>
          <div style="font-size:15px;font-weight:700;margin-bottom:6px;">Goal Deadline</div>
          <div style="font-size:28px;font-weight:800;background:var(--grad-purple);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">23 days</div>
          <div style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);margin-top:6px;">until target date</div>
          <button class="btn btn-secondary" style="margin-top:16px;width:100%;justify-content:center;">Adjust Timeline</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ── TAB: Interview ───────────────────────────── -->
  <div class="tab-panel" id="tab-interview">
    <div class="grid-sidebar">
      <div>
        <div class="section-title">Interview Configuration</div>
        <div class="card card-glow-warm" style="margin-bottom:16px;">
          <div class="card-header">
            <div style="display:flex;gap:12px;align-items:center">
              <div class="card-icon-wrap warm">🎤</div>
              <div>
                <div class="card-title">Interview Prep</div>
                <div class="card-subtitle">Real questions from top companies</div>
              </div>
            </div>
          </div>

          <div class="field-label" style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:10px;">Target Role</div>
          <div class="interview-role-grid">
            <div class="role-card" onclick="selectRole(this)">
              <div class="role-icon">⚛️</div>
              <div class="role-title">Frontend</div>
              <div class="role-count">142 Q&A</div>
            </div>
            <div class="role-card active" onclick="selectRole(this)">
              <div class="role-icon">⚙️</div>
              <div class="role-title">Backend</div>
              <div class="role-count">198 Q&A</div>
            </div>
            <div class="role-card" onclick="selectRole(this)">
              <div class="role-icon">🏗️</div>
              <div class="role-title">Full Stack</div>
              <div class="role-count">231 Q&A</div>
            </div>
            <div class="role-card" onclick="selectRole(this)">
              <div class="role-icon">🧠</div>
              <div class="role-title">ML / AI</div>
              <div class="role-count">89 Q&A</div>
            </div>
          </div>

          <div style="display:flex;gap:12px;">
            <div class="field" style="flex:1;">
              <label class="field-label">Company Focus</label>
              <div class="input-wrap">
                <span class="input-icon">🏢</span>
                <select>
                  <option>Any company</option>
                  <option>FAANG</option>
                  <option>Startups</option>
                  <option>Mid-size</option>
                </select>
              </div>
            </div>
            <div class="field" style="flex:1;">
              <label class="field-label">Interview Round</label>
              <div class="input-wrap">
                <span class="input-icon">🎯</span>
                <select>
                  <option>Phone Screen</option>
                  <option selected>Technical</option>
                  <option>System Design</option>
                  <option>Behavioral</option>
                </select>
              </div>
            </div>
            <div class="field" style="align-self:flex-end;">
              <button class="btn btn-primary">
                <span class="btn-icon">🎤</span> Start Prep
              </button>
            </div>
          </div>
        </div>

        <div class="section-title">Practice Questions — Backend · Technical</div>
        <div class="interview-question-wrap">
          <div class="iq-card expanded" onclick="toggleIQ(this)">
            <div class="iq-header">
              <span class="iq-difficulty med">MEDIUM</span>
              <span class="iq-text">Explain the difference between concurrency and parallelism in Node.js.</span>
              <span class="iq-chevron">▾</span>
            </div>
            <div class="iq-answer">
              Concurrency handles multiple tasks by interleaving their execution using the event loop, while parallelism executes tasks simultaneously across multiple threads or processes. Node.js achieves concurrency through its single-threaded non-blocking I/O model, using the event loop to handle async operations without blocking execution.
              <div class="iq-tags">
                <span class="iq-tag">Node.js</span>
                <span class="iq-tag">Event Loop</span>
                <span class="iq-tag">Architecture</span>
              </div>
            </div>
          </div>
          <div class="iq-card" onclick="toggleIQ(this)">
            <div class="iq-header">
              <span class="iq-difficulty hard">HARD</span>
              <span class="iq-text">Design a rate limiting system that supports 1M requests per second across distributed nodes.</span>
              <span class="iq-chevron">▾</span>
            </div>
            <div class="iq-answer">
              Use a distributed token bucket algorithm with Redis as a centralized counter, combined with a sliding window for accurate rate limiting. Shard across Redis cluster nodes using consistent hashing. Implement local in-memory caches per node to reduce latency.
              <div class="iq-tags">
                <span class="iq-tag">System Design</span>
                <span class="iq-tag">Redis</span>
                <span class="iq-tag">Distributed Systems</span>
              </div>
            </div>
          </div>
          <div class="iq-card" onclick="toggleIQ(this)">
            <div class="iq-header">
              <span class="iq-difficulty easy">EASY</span>
              <span class="iq-text">What is the difference between SQL and NoSQL databases? When would you choose each?</span>
              <span class="iq-chevron">▾</span>
            </div>
            <div class="iq-answer">
              SQL databases are relational, schema-based, and ACID compliant. NoSQL databases are schema-flexible and optimized for scale and speed. Choose SQL for complex queries and transactional integrity; NoSQL for high-throughput, flexible data models, or distributed scaling.
              <div class="iq-tags">
                <span class="iq-tag">Databases</span>
                <span class="iq-tag">SQL</span>
                <span class="iq-tag">NoSQL</span>
              </div>
            </div>
          </div>
          <div class="iq-card" onclick="toggleIQ(this)">
            <div class="iq-header">
              <span class="iq-difficulty med">MEDIUM</span>
              <span class="iq-text">How would you implement authentication in a microservices architecture?</span>
              <span class="iq-chevron">▾</span>
            </div>
            <div class="iq-answer">
              Use a centralized Auth Service with JWTs. Each service verifies the token using a shared secret or public key. Implement an API Gateway that handles authentication before routing to downstream services. Use refresh tokens with short-lived access tokens for security.
              <div class="iq-tags">
                <span class="iq-tag">Auth</span>
                <span class="iq-tag">JWT</span>
                <span class="iq-tag">Microservices</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="widget-stack">
        <div class="proficiency-card">
          <div class="prof-header" style="margin-bottom:14px;">
            <span class="prof-title">Interview Readiness</span>
          </div>
          <div class="radial-wrap">
            <svg class="radial-svg" viewBox="0 0 100 100">
              <defs>
                <linearGradient id="grad-radial-2" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#f472b6"/>
                  <stop offset="100%" stop-color="#a78bfa"/>
                </linearGradient>
              </defs>
              <circle class="radial-track" cx="50" cy="50" r="40"/>
              <circle class="radial-fill" cx="50" cy="50" r="40" style="stroke:url(#grad-radial-2);stroke-dashoffset:88;"/>
            </svg>
            <div class="radial-label">
              <div class="radial-label-val" style="background:var(--grad-warm);-webkit-background-clip:text;background-clip:text;">65%</div>
              <div class="radial-label-sub">ready</div>
            </div>
          </div>
          <div class="skill-bars" style="margin-top:8px;">
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Technical</span><span>72%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:72%;background:var(--grad-mixed)"></div></div>
            </div>
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>System Design</span><span>55%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:55%;background:var(--grad-warm)"></div></div>
            </div>
            <div class="skill-bar-item">
              <div class="skill-bar-label"><span>Behavioral</span><span>80%</span></div>
              <div class="skill-bar-track"><div class="skill-bar-fill" style="width:80%;background:var(--grad-purple)"></div></div>
            </div>
          </div>
        </div>

        <div class="card card-glow-warm">
          <div class="prof-header" style="margin-bottom:12px;">
            <span class="prof-title">Prep Checklist</span>
          </div>
          <div style="display:flex;flex-direction:column;gap:8px;">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
              <input type="checkbox" checked style="accent-color:var(--cyan);width:14px;height:14px;"> Arrays & Strings
            </label>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
              <input type="checkbox" checked style="accent-color:var(--cyan);width:14px;height:14px;"> Linked Lists
            </label>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
              <input type="checkbox" style="accent-color:var(--cyan);width:14px;height:14px;"> Trees & Graphs
            </label>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
              <input type="checkbox" style="accent-color:var(--cyan);width:14px;height:14px;"> Dynamic Programming
            </label>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
              <input type="checkbox" style="accent-color:var(--cyan);width:14px;height:14px;"> System Design
            </label>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;">
              <input type="checkbox" style="accent-color:var(--cyan);width:14px;height:14px;"> Behavioral STAR
            </label>
          </div>
          <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);font-family:var(--font-mono);font-size:11px;color:var(--text-muted);">
            2 / 6 complete
          </div>
        </div>

        <div class="card" style="text-align:center;padding:24px 20px;">
          <div style="font-size:28px;margin-bottom:10px;">🏢</div>
          <div style="font-size:14px;font-weight:700;margin-bottom:6px;">Mock Interview</div>
          <div style="font-size:12px;color:var(--text-secondary);margin-bottom:16px;line-height:1.5;">Simulate a full 45-minute technical interview session</div>
          <button class="btn btn-primary" style="width:100%;justify-content:center;">
            <span class="btn-icon">🎤</span> Start Mock
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ── TAB: Flashcards ───────────────────────────── -->
  <div class="tab-panel" id="tab-flashcards">
    <div class="grid-sidebar">
      <div>
        <div class="section-title">Flashcard Generator</div>
        <div class="card card-glow" style="margin-bottom:16px;">
          <div class="card-header">
            <div style="display:flex;gap:12px;align-items:center">
              <div class="card-icon-wrap">🃏</div>
              <div>
                <div class="card-title">Generate Flashcards</div>
                <div class="card-subtitle">AI-powered spaced-repetition cards</div>
              </div>
            </div>
          </div>
          <div class="cp-grid" style="grid-template-columns:1fr 140px auto;">
            <div class="field">
              <label class="field-label">Topic</label>
              <div class="input-wrap">
                <span class="input-icon">🃏</span>
                <input type="text" id="fc-topic" placeholder="e.g. JavaScript closures, React hooks…" />
              </div>
            </div>
            <div class="field">
              <label class="field-label">Cards</label>
              <div class="input-wrap">
                <span class="input-icon">#</span>
                <select id="fc-count"><option value="5">5</option><option value="10" selected>10</option><option value="15">15</option><option value="20">20</option></select>
              </div>
            </div>
            <div class="field"><label class="field-label">&nbsp;</label>
              <button class="btn btn-primary" id="btn-gen-flashcards"><span class="btn-icon">✦</span> Generate</button>
            </div>
          </div>
        </div>

        <div id="fc-container" style="display:none;">
          <div class="section-title" id="fc-title-bar">Flashcards</div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <button class="btn btn-ghost" id="fc-prev">← Prev</button>
            <span id="fc-counter" style="font-family:var(--font-mono);font-size:12px;color:var(--text-muted);">1 / 10</span>
            <button class="btn btn-ghost" id="fc-next">Next →</button>
          </div>
          <div class="card card-glow" id="fc-card" style="min-height:220px;cursor:pointer;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;transition:transform 0.5s ease;" title="Click to flip">
            <div id="fc-hint" style="font-size:10px;font-family:var(--font-mono);color:var(--text-muted);margin-bottom:12px;letter-spacing:0.08em;">CLICK TO FLIP</div>
            <div id="fc-text" style="font-size:16px;font-weight:600;line-height:1.6;max-width:500px;"></div>
            <div id="fc-diff" style="margin-top:16px;"></div>
          </div>
        </div>
        <div id="fc-loading" style="display:none;" class="card" style="padding:40px;text-align:center;">
          <div class="empty-icon">🃏</div>
          <div class="empty-text">Generating flashcards…</div>
          <div class="skeleton-lines" style="margin-top:16px;"><div class="sk-line w-90"></div><div class="sk-line w-75"></div><div class="sk-line w-60"></div></div>
        </div>
      </div>
      <div class="widget-stack">
        <div class="card card-glow">
          <div class="prof-header" style="margin-bottom:12px;"><span class="prof-title">Card Stats</span></div>
          <div id="fc-stats" style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;justify-content:space-between;padding:10px 14px;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-md);">
              <span style="font-size:12px;color:var(--text-secondary);font-family:var(--font-mono);">Total cards</span>
              <span id="fc-total" style="font-size:16px;font-weight:700;color:var(--cyan);">0</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px 14px;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-md);">
              <span style="font-size:12px;color:var(--text-secondary);font-family:var(--font-mono);">Reviewed</span>
              <span id="fc-reviewed" style="font-size:16px;font-weight:700;color:var(--purple);">0</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div><!-- /page-wrap -->

        </section>
        </main>
    </div>

<!-- ── Chat Widget ─────────────────────────────────── -->
<div id="chat-toggle" style="position:fixed;bottom:28px;right:28px;z-index:9999;width:56px;height:56px;border-radius:50%;background:var(--grad-mixed);display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 24px rgba(0,242,255,0.25);font-size:24px;transition:transform 0.2s ease;" title="AI Chat">💬</div>
<div id="chat-panel" style="display:none;position:fixed;bottom:96px;right:28px;z-index:9999;width:380px;max-height:520px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);box-shadow:var(--shadow-float);overflow:hidden;flex-direction:column;">
  <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:rgba(0,242,255,0.04);">
    <div style="display:flex;align-items:center;gap:10px;">
      <span style="font-size:18px;">🤖</span>
      <span style="font-size:14px;font-weight:700;">Study Buddy Chat</span>
    </div>
    <button id="chat-close" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:18px;">✕</button>
  </div>
  <div id="chat-messages" style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:12px;min-height:300px;max-height:360px;">
    <div style="background:rgba(0,242,255,0.06);border:1px solid rgba(0,242,255,0.12);border-radius:12px 12px 12px 4px;padding:12px 16px;font-size:13px;line-height:1.6;max-width:85%;color:var(--text-secondary);">
      Hi! I'm your Study Buddy AI. Ask me anything — concepts, problems, exam prep, or just chat about what you're learning! 🎓
    </div>
  </div>
  <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;gap:8px;">
    <input type="text" id="chat-input" placeholder="Ask anything…" style="flex:1;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-md);color:var(--text-primary);font-family:var(--font-display);font-size:13px;padding:10px 14px;outline:none;" />
    <button id="chat-send" class="btn btn-primary" style="padding:10px 16px;">Send</button>
  </div>
</div>

<script>
(function () {
  const API = 'api.php';
  const chatSessionId = 'chat_' + Date.now();
  let fcCards = [], fcIdx = 0, fcFlipped = false, fcReviewed = new Set();

  // ── Helpers ────────────────────────────────────────
  async function api(module, action, body = {}) {
    const res = await fetch(`${API}?module=${module}&action=${action}`, {
      method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(body)
    });
    return res.json();
  }
  function qs(s) { return document.querySelector(s); }
  function qsa(s) { return document.querySelectorAll(s); }
  function getTopicInput() { return (qs('.cp-grid input[type="text"]')?.value || '').trim(); }
  function getDifficulty() { return qs('.cp-grid select')?.value || 'Intermediate'; }
  function getGoal() { return qsa('.cp-grid select')[1]?.value || 'Understand concepts'; }
  function showLoading(el) { el.innerHTML = '<div class="skeleton-lines"><div class="sk-line w-90"></div><div class="sk-line w-75"></div><div class="sk-line w-85"></div><div class="sk-line w-60"></div></div>'; el.style.display = 'block'; }
  function md(text) {
    if (!text) return '';
    return text.replace(/```(\w*)\n([\s\S]*?)```/g, '<pre style="background:var(--bg-input);border:1px solid var(--border);border-radius:8px;padding:14px;overflow-x:auto;font-family:var(--font-mono);font-size:12px;margin:12px 0;"><code>$2</code></pre>')
      .replace(/## (.+)/g, '<h3 style="color:var(--cyan);margin:18px 0 8px;font-size:15px;font-weight:700;">$1</h3>')
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.+?)\*/g, '<em>$1</em>')
      .replace(/`([^`]+)`/g, '<code style="background:rgba(0,242,255,0.08);padding:2px 6px;border-radius:4px;font-family:var(--font-mono);font-size:12px;color:var(--cyan);">$1</code>')
      .replace(/\n- /g, '\n• ').replace(/\n\d+\. /g, (m) => '\n' + m.trim() + ' ')
      .replace(/\n/g, '<br>');
  }

  // ── Tab Switching ──────────────────────────────────
  qsa('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.tab;
      qsa('.tab-btn').forEach(b => b.classList.remove('active'));
      qsa('.tab-panel').forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('tab-' + target)?.classList.add('active');
    });
  });

  // ── Mode Toggle ───────────────────────────────────
  qsa('.cp-mode-btn').forEach(btn => {
    btn.addEventListener('click', () => { qsa('.cp-mode-btn').forEach(b => b.classList.remove('active')); btn.classList.add('active'); });
  });

  // ── Generate (Explain) ────────────────────────────
  qsa('.cp-grid .btn-primary').forEach(btn => {
    if (btn.closest('#tab-flashcards')) return;
    btn.addEventListener('click', async () => {
      const topic = getTopicInput();
      if (!topic) { alert('Enter a topic first'); return; }
      const preview = qs('#explain-content');
      const empty = qs('#explain-empty');
      if (empty) empty.style.display = 'none';
      if (preview) { showLoading(preview); preview.style.display = 'block'; }
      const tab = qs('[data-tab="explain"]');
      if (tab) tab.click();
      const r = await api('study', 'explain', { topic, difficulty: getDifficulty(), goal: getGoal() });
      if (preview) {
        if (r.success) { preview.innerHTML = '<div style="font-size:13px;line-height:1.8;color:var(--text-secondary);">' + md(r.text) + '</div>'; }
        else { preview.innerHTML = '<div style="color:#f87171;">Error: ' + (r.message||'Unknown error') + '</div>'; }
      }
    });
  });

  // ── Explain button (ghost) ────────────────────────
  qsa('.btn-ghost').forEach(btn => {
    if (btn.textContent.includes('Explain')) {
      btn.addEventListener('click', () => { qs('.cp-grid .btn-primary')?.click(); });
    }
  });

  // ── Quiz Generation ───────────────────────────────
  qsa('#tab-quiz .btn-primary').forEach(btn => {
    if (!btn.textContent.includes('Generate Quiz')) return;
    btn.addEventListener('click', async () => {
      const topic = getTopicInput() || 'General programming';
      const countSel = qs('#tab-quiz .quiz-config-grid select');
      const count = parseInt(countSel?.value) || 10;
      const types = [];
      qsa('.q-type-card.selected .q-type-label').forEach(l => {
        const t = l.textContent.trim().toLowerCase().replace(/[\s\/]+/g, '_');
        types.push(t);
      });
      const previewCard = qs('#tab-quiz .quiz-preview-card') || qs('#tab-quiz .card.card-glow:last-of-type');
      const section = qs('#tab-quiz .section-title:last-of-type');
      if (section) section.textContent = 'Generating quiz…';
      if (previewCard) showLoading(previewCard);
      const r = await api('study', 'quiz', { topic, count, difficulty: getDifficulty(), types: types.length ? types : ['multiple_choice'] });
      if (r.success && r.quiz?.questions) {
        if (section) section.textContent = r.quiz.quiz_title || 'Quiz';
        window._quizData = r.quiz.questions; window._quizIdx = 0;
        renderQuizQuestion(0, previewCard);
      } else {
        if (section) section.textContent = 'Quiz Generation Failed';
        if (previewCard) previewCard.innerHTML = '<div style="color:#f87171;">' + (r.message||'Failed to generate quiz') + '</div>';
      }
    });
  });

  window.renderQuizQuestion = function(idx, container) {
    const q = window._quizData?.[idx]; if (!q || !container) return;
    const total = window._quizData.length;
    let html = '<div class="quiz-question-num">QUESTION ' + String(idx+1).padStart(2,'0') + ' / ' + total + '</div>';
    html += '<div class="quiz-question-text">' + q.question + '</div><div class="quiz-options">';
    (q.options||[]).forEach((opt, i) => {
      const key = String.fromCharCode(65 + i);
      html += '<div class="quiz-option" onclick="checkAnswer(this,\'' + key + '\',' + idx + ')"><div class="quiz-option-key">' + key + '</div>' + opt + '</div>';
    });
    html += '</div><div class="quiz-footer"><div class="quiz-progress-bar-wrap"><div class="quiz-progress-bar-fill" style="width:' + ((idx+1)/total*100) + '%"></div></div>';
    html += '<div style="display:flex;gap:8px;">';
    if (idx > 0) html += '<button class="btn btn-ghost" onclick="renderQuizQuestion(' + (idx-1) + ',this.closest(\'.card\'))">← Prev</button>';
    if (idx < total-1) html += '<button class="btn btn-primary" onclick="renderQuizQuestion(' + (idx+1) + ',this.closest(\'.card\'))">Next →</button>';
    html += '</div></div>';
    container.innerHTML = html;
  };
  window.checkAnswer = function(el, key, idx) {
    const q = window._quizData?.[idx]; if (!q) return;
    const correct = (q.correct_answer||'').charAt(0).toUpperCase();
    el.closest('.quiz-options').querySelectorAll('.quiz-option').forEach(o => o.style.pointerEvents = 'none');
    if (key === correct) { el.classList.add('correct'); }
    else { el.style.borderColor = 'rgba(248,113,113,0.5)'; el.style.background = 'rgba(248,113,113,0.08)'; }
    if (q.explanation) {
      const exp = document.createElement('div');
      exp.style.cssText = 'margin-top:14px;padding:14px;border-top:1px solid var(--border);font-size:12px;color:var(--text-secondary);line-height:1.6;font-family:var(--font-mono);';
      exp.innerHTML = '💡 ' + q.explanation;
      el.closest('.quiz-options').after(exp);
    }
  };

  // ── Study Plan ────────────────────────────────────
  qsa('#tab-plan .btn-primary').forEach(btn => {
    if (!btn.textContent.includes('Build')) return;
    btn.addEventListener('click', async () => {
      const activeGoal = qs('.goal-chip.active')?.textContent || 'Learn programming';
      const activeIntensity = qs('.intensity-card.selected .intensity-label')?.textContent?.toLowerCase() || 'focused';
      const topic = getTopicInput() || activeGoal;
      const timeline = qs('#tab-plan .plan-timeline');
      if (timeline) { timeline.innerHTML = '<div class="skeleton-lines"><div class="sk-line w-90"></div><div class="sk-line w-75"></div><div class="sk-line w-85"></div></div>'; }
      const r = await api('study', 'plan', { goal: activeGoal, intensity: activeIntensity, topics: topic, weeks: 4 });
      if (r.success && r.plan?.weeks && timeline) {
        let html = '';
        r.plan.weeks.forEach((w, wi) => {
          html += '<div class="plan-week' + (wi===0?' open':'') + '"><div class="plan-week-header" onclick="this.closest(\'.plan-week\').classList.toggle(\'open\')">';
          html += '<span class="plan-week-num">WEEK ' + String(w.week||wi+1).padStart(2,'0') + '</span>';
          html += '<span class="plan-week-title">' + (w.title||w.focus||'') + '</span>';
          html += '<span class="plan-week-meta">' + (w.days?.length||0) + ' days</span>';
          html += '<span class="plan-week-chevron">▾</span></div><div class="plan-week-body"><div class="plan-day-list">';
          (w.days||[]).forEach(d => {
            html += '<div class="plan-day"><div class="plan-day-dot"></div><span>' + (d.topic||'') + '</span><div class="plan-day-duration">' + (d.duration||'') + '</div></div>';
          });
          html += '</div></div></div>';
        });
        timeline.innerHTML = html;
        const titleEl = qs('#tab-plan .section-title:last-of-type');
        if (titleEl) titleEl.textContent = r.plan.plan_title || 'Study Plan';
      } else if (timeline) {
        timeline.innerHTML = '<div style="color:#f87171;">' + (r.message||'Failed to generate plan') + '</div>';
      }
    });
  });

  // ── Question Type Toggle ──────────────────────────
  qsa('.q-type-card').forEach(c => c.addEventListener('click', () => c.classList.toggle('selected')));
  qsa('.goal-chip').forEach(c => c.addEventListener('click', () => { qsa('.goal-chip').forEach(x=>x.classList.remove('active')); c.classList.add('active'); }));
  qsa('.intensity-card').forEach(c => c.addEventListener('click', () => { qsa('.intensity-card').forEach(x=>x.classList.remove('selected')); c.classList.add('selected'); }));
  qsa('.role-card').forEach(c => c.addEventListener('click', () => { qsa('.role-card').forEach(x=>x.classList.remove('active')); c.classList.add('active'); }));

  // ── Depth Slider ──────────────────────────────────
  const slider = document.getElementById('depth-slider');
  const depthLabels = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];
  if (slider) {
    slider.addEventListener('input', () => {
      document.getElementById('depth-val').textContent = depthLabels[slider.value - 1];
      const pct = ((slider.value - 1) / 3 * 100).toFixed(0);
      slider.style.background = 'linear-gradient(90deg, var(--cyan) ' + pct + '%, rgba(255,255,255,0.1) ' + pct + '%)';
    });
  }

  // ── Suggest Chip → Input ──────────────────────────
  qsa('.suggest-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      const input = qs('.cp-grid input[type="text"]');
      if (input) { input.value = chip.textContent.replace('→ ', ''); input.focus(); }
    });
  });
  qsa('.concept-tag').forEach(tag => tag.addEventListener('click', () => tag.classList.toggle('active')));

  // ── Flashcards ────────────────────────────────────
  qs('#btn-gen-flashcards')?.addEventListener('click', async () => {
    const topic = qs('#fc-topic')?.value?.trim();
    if (!topic) { alert('Enter a topic'); return; }
    const count = parseInt(qs('#fc-count')?.value) || 10;
    qs('#fc-container').style.display = 'none';
    qs('#fc-loading').style.display = 'block';
    const r = await api('study', 'flashcards', { topic, count, difficulty: getDifficulty() });
    qs('#fc-loading').style.display = 'none';
    if (r.success && r.flashcards?.cards) {
      fcCards = r.flashcards.cards; fcIdx = 0; fcFlipped = false; fcReviewed = new Set();
      qs('#fc-container').style.display = 'block';
      qs('#fc-title-bar').textContent = r.flashcards.title || 'Flashcards';
      qs('#fc-total').textContent = fcCards.length;
      renderFC();
    } else { alert(r.message || 'Failed to generate flashcards'); }
  });
  function renderFC() {
    const c = fcCards[fcIdx]; if (!c) return;
    fcFlipped = false;
    qs('#fc-counter').textContent = (fcIdx+1) + ' / ' + fcCards.length;
    qs('#fc-text').innerHTML = c.front;
    qs('#fc-hint').textContent = 'CLICK TO FLIP';
    qs('#fc-diff').innerHTML = '<span class="badge ' + (c.difficulty==='easy'?'cyan':c.difficulty==='hard'?'pink':'purple') + '">' + (c.difficulty||'medium') + '</span>';
    qs('#fc-card').style.transform = '';
    qs('#fc-reviewed').textContent = fcReviewed.size;
  }
  qs('#fc-card')?.addEventListener('click', () => {
    const c = fcCards[fcIdx]; if (!c) return;
    fcFlipped = !fcFlipped;
    qs('#fc-text').innerHTML = fcFlipped ? c.back : c.front;
    qs('#fc-hint').textContent = fcFlipped ? 'ANSWER' : 'CLICK TO FLIP';
    qs('#fc-card').style.transform = fcFlipped ? 'scale(1.02)' : '';
    if (fcFlipped) { fcReviewed.add(fcIdx); qs('#fc-reviewed').textContent = fcReviewed.size; }
  });
  qs('#fc-prev')?.addEventListener('click', () => { if (fcIdx > 0) { fcIdx--; renderFC(); } });
  qs('#fc-next')?.addEventListener('click', () => { if (fcIdx < fcCards.length-1) { fcIdx++; renderFC(); } });

  // ── Chat Widget ───────────────────────────────────
  qs('#chat-toggle')?.addEventListener('click', () => {
    const p = qs('#chat-panel');
    const vis = p.style.display === 'flex';
    p.style.display = vis ? 'none' : 'flex';
    qs('#chat-toggle').style.transform = vis ? '' : 'scale(0.9)';
    if (!vis) qs('#chat-input')?.focus();
  });
  qs('#chat-close')?.addEventListener('click', () => {
    qs('#chat-panel').style.display = 'none';
    qs('#chat-toggle').style.transform = '';
  });
  function addChatMsg(text, isUser) {
    const msgs = qs('#chat-messages');
    const div = document.createElement('div');
    div.style.cssText = isUser
      ? 'background:rgba(176,140,255,0.1);border:1px solid rgba(176,140,255,0.15);border-radius:12px 12px 4px 12px;padding:12px 16px;font-size:13px;line-height:1.6;max-width:85%;align-self:flex-end;'
      : 'background:rgba(0,242,255,0.06);border:1px solid rgba(0,242,255,0.12);border-radius:12px 12px 12px 4px;padding:12px 16px;font-size:13px;line-height:1.6;max-width:85%;color:var(--text-secondary);';
    div.innerHTML = isUser ? text : md(text);
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
  }
  async function sendChat() {
    const input = qs('#chat-input');
    const msg = input?.value?.trim(); if (!msg) return;
    input.value = '';
    addChatMsg(msg, true);
    const loading = addChatMsg('Thinking…', false);
    const r = await api('study', 'chat', { sessionId: chatSessionId, message: msg });
    loading.remove();
    addChatMsg(r.success ? r.text : ('Error: ' + (r.message||'Unknown')), false);
  }
  qs('#chat-send')?.addEventListener('click', sendChat);
  qs('#chat-input')?.addEventListener('keypress', e => { if (e.key === 'Enter') sendChat(); });

  // ── Interview Prep ───────────────────────────────
  qsa('#tab-interview .btn-primary').forEach(btn => {
    if (!btn.textContent.includes('Start Prep')) return;
    btn.addEventListener('click', async () => {
      const role = qs('.role-card.active .role-title')?.textContent || 'Software Engineer';
      const company = qsa('#tab-interview select')[0]?.value || 'Any company';
      const round = qsa('#tab-interview select')[1]?.value || 'Technical';
      const topic = getTopicInput() || role;
      const container = qs('.interview-question-wrap');
      const section = qs('#tab-interview .section-title:last-of-type');
      
      if (section) section.textContent = 'Preparing interview questions...';
      if (container) showLoading(container);
      
      // We use the quiz API with interview context
      const r = await api('study', 'quiz', { 
        topic: `Interview for ${role} at ${company} (${round} round) focusing on ${topic}`, 
        count: 5, 
        difficulty: getDifficulty(),
        types: ['open_ended'] 
      });
      
      if (r.success && r.quiz?.questions) {
        if (section) section.textContent = `Practice Questions — ${role} · ${round}`;
        let html = '';
        r.quiz.questions.forEach((q, i) => {
          html += `<div class="iq-card ${i===0?'expanded':''}" onclick="toggleIQ(this)">
            <div class="iq-header">
              <span class="iq-difficulty ${i%3===0?'hard':i%2===0?'med':'easy'}">${(q.type||'QUESTION').toUpperCase()}</span>
              <span class="iq-text">${q.question}</span>
              <span class="iq-chevron">▾</span>
            </div>
            <div class="iq-answer">
              ${q.answer || q.explanation || 'Think about your answer, then click to see tips.'}
              <div class="iq-tags">
                <span class="iq-tag">${role}</span>
                <span class="iq-tag">${round}</span>
              </div>
            </div>
          </div>`;
        });
        container.innerHTML = html;
      } else {
        if (container) container.innerHTML = '<div style="color:#f87171;">' + (r.message||'Failed to prepare questions') + '</div>';
      }
    });
  });

  // ── Card hover ────────────────────────────────────
  qsa('.card[style*="cursor:pointer"]').forEach(card => {
    card.addEventListener('mouseenter', () => card.style.transform = 'translateY(-3px)');
    card.addEventListener('mouseleave', () => card.style.transform = '');
  });

  // ── Interview IQ toggle ───────────────────────────
  window.toggleIQ = function(card) { card.classList.toggle('expanded'); };
  window.toggleWeek = function(header) { header.closest('.plan-week').classList.toggle('open'); };
  window.selectRole = function(card) { qsa('.role-card').forEach(c=>c.classList.remove('active')); card.classList.add('active'); };
  window.selectOption = function(opt) {
    opt.closest('.quiz-options')?.querySelectorAll('.quiz-option').forEach(o => o.classList.remove('selected'));
    opt.classList.add('selected');
  };
})();
</script>

<script src="dashboard-interactions.js"></script>
</body>
</html>