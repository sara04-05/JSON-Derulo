<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Research Assistant — ElevUra</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="shared.css"/>
<style>
.job-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;margin-bottom:12px;transition:all var(--transition);cursor:pointer}
.job-card:hover{border-color:rgba(0,242,255,0.15);transform:translateY(-2px)}
.job-title{font-size:15px;font-weight:700;margin-bottom:4px}
.job-company{font-size:13px;color:var(--cyan);font-weight:600}
.job-meta{display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;font-size:11px;color:var(--text-muted);font-family:var(--font-mono)}
.job-skills{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
.job-skills span{font-size:10px;padding:3px 8px;border-radius:100px;background:rgba(0,242,255,0.06);border:1px solid rgba(0,242,255,0.12);color:var(--cyan);font-family:var(--font-mono)}
.match-score{position:absolute;top:16px;right:16px;font-size:20px;font-weight:800;background:var(--grad-mixed);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.skill-tag{display:inline-flex;align-items:center;gap:4px;font-size:11px;padding:5px 12px;border-radius:8px;margin:3px;font-family:var(--font-mono)}
.skill-tag.tech{background:rgba(0,242,255,0.06);border:1px solid rgba(0,242,255,0.15);color:var(--cyan)}
.skill-tag.soft{background:rgba(176,140,255,0.06);border:1px solid rgba(176,140,255,0.15);color:var(--purple)}
.skill-tag.tool{background:rgba(244,114,182,0.06);border:1px solid rgba(244,114,182,0.15);color:var(--pink)}
.compare-table{width:100%;border-collapse:collapse;font-size:13px}
.compare-table th{text-align:left;padding:10px 14px;border-bottom:1px solid var(--border);color:var(--cyan);font-family:var(--font-mono);font-size:11px;font-weight:600;letter-spacing:0.05em}
.compare-table td{padding:10px 14px;border-bottom:1px solid var(--border);color:var(--text-secondary)}
.compare-table tr:hover td{background:rgba(0,242,255,0.02)}
.history-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:var(--radius-md);cursor:pointer;transition:background var(--transition)}
.history-item:hover{background:rgba(255,255,255,0.03)}
.history-dot{width:8px;height:8px;border-radius:50%;background:var(--cyan);flex-shrink:0}
.history-text{font-size:12px;font-weight:500;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.history-time{font-size:10px;color:var(--text-muted);font-family:var(--font-mono)}
#loading-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center}
#loading-overlay .loader{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:40px;text-align:center}
</style>
</head>
<body>
<div class="container-wrapper">
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-brand-mark"><svg viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div>
    <span class="sidebar-brand-text">ElevUra</span>
  </div>
  <nav class="sidebar-menu">
    <a href="dashboard.html" class="sidebar-item"><span class="sidebar-item-icon sidebar-item-icon-terminal">&gt;_</span><span>Command Center</span></a>
    <a href="dashboard.html" class="sidebar-item"><span class="sidebar-item-icon"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><span>AI Career Coach</span></a>
    <a href="#" class="sidebar-item"><span class="sidebar-item-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg></span><span>CV Optimizer</span></a>
    <a href="StudyBuddy.php" class="sidebar-item"><span class="sidebar-item-icon"><svg viewBox="0 0 24 24"><circle cx="9" cy="12" r="5"/><circle cx="15" cy="12" r="5"/></svg></span><span>Study Buddy</span></a>
    <a href="ResearchAssistant.php" class="sidebar-item active"><span class="sidebar-item-icon"><svg viewBox="0 0 24 24"><path d="M9 3h6v2H9V3z"/><path d="M10 5v5.2c0 .86-.37 1.68-1 2.26L6 16h12l-3-3.54c-.63-.58-1-1.4-1-2.26V5"/><path d="M6 16h12v2a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-2z"/></svg></span><span>Research Assistant</span></a>
  </nav>
</aside>

<main class="main-content">
  <header class="top-header">
    <div class="header-left"><div class="environment-text">Environment: <span class="production">Production</span></div></div>
    <div class="header-right">
      <div class="user-info">
        <div class="user-avatar"><img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=128&h=128&fit=crop&crop=faces" width="36" height="36" alt="" loading="lazy"></div>
        <div><div class="user-name">Alex Mercer</div><div class="user-tier">Pro Tier</div></div>
      </div>
    </div>
  </header>

  <section class="content-area">
    <div class="page-wrap">
      <div class="topbar"><span>Dashboard</span><span>/</span><span>Research</span><span>/</span><span class="topbar-current">Research Assistant</span></div>

      <section class="hero">
        <div>
          <div class="hero-eyebrow">AI-Powered Research</div>
          <h1 class="hero-title">Research &amp;<br><span class="highlight">Job Assistant</span></h1>
          <p class="hero-desc">Find opportunities, extract skills, compare roles, and build your career strategy — all powered by AI.</p>
        </div>
      </section>

      <!-- Tabs -->
      <div class="tabs-wrap">
        <button class="tab-btn active" data-tab="search"><span>🔍</span><span class="tab-accent">Job Search</span></button>
        <button class="tab-btn" data-tab="skills"><span>🧬</span><span class="tab-accent">Skill Analysis</span></button>
        <button class="tab-btn" data-tab="compare"><span>⚖️</span><span class="tab-accent">Compare Roles</span></button>
        <button class="tab-btn" data-tab="history"><span>📋</span><span class="tab-accent">History</span></button>
      </div>

      <!-- TAB: Job Search -->
      <div class="tab-panel active" id="tab-search">
        <div class="card card-glow" style="margin-bottom:24px;padding:28px 32px;">
          <div style="display:grid;grid-template-columns:1fr 160px 160px auto;gap:12px;align-items:end;">
            <div class="field"><label class="field-label">Search Query</label><div class="input-wrap"><span class="input-icon">🔍</span><input type="text" id="search-query" placeholder="e.g. Frontend Developer, Data Scientist…"/></div></div>
            <div class="field"><label class="field-label">Location</label><div class="input-wrap"><span class="input-icon">📍</span><select id="search-location"><option value="">Any</option><option>Remote</option><option>New York</option><option>San Francisco</option><option>London</option><option>Berlin</option></select></div></div>
            <div class="field"><label class="field-label">Level</label><div class="input-wrap"><span class="input-icon">📊</span><select id="search-level"><option value="">Any</option><option>Junior</option><option>Mid-level</option><option>Senior</option><option>Lead</option></select></div></div>
            <div class="field"><label class="field-label">&nbsp;</label><button class="btn btn-primary" id="btn-search"><span class="btn-icon">✦</span>Search</button></div>
          </div>
        </div>
        <div id="search-insights" style="display:none;margin-bottom:20px;"></div>
        <div id="search-results"></div>
        <div id="search-loading" style="display:none;"><div class="skeleton-lines"><div class="sk-line w-90"></div><div class="sk-line w-75"></div><div class="sk-line w-85"></div><div class="sk-line w-60"></div></div></div>
      </div>

      <!-- TAB: Skills -->
      <div class="tab-panel" id="tab-skills">
        <div class="grid-sidebar">
          <div>
            <div class="section-title">Skill Extraction</div>
            <div class="card card-glow" style="margin-bottom:16px;">
              <div style="margin-bottom:16px;"><label class="field-label">Paste Job Description or Resume</label></div>
              <textarea id="skills-text" style="width:100%;padding:14px;min-height:180px;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-md);color:var(--text-primary);font-size:13px;outline:none;resize:vertical;" placeholder="Paste a job description, resume, or any text to extract skills..."></textarea>
              <button class="btn btn-primary" id="btn-skills" style="margin-top:12px;"><span class="btn-icon">🧬</span>Extract Skills</button>
            </div>
            <div id="skills-results" style="display:none;"></div>
            <div id="skills-loading" style="display:none;"><div class="skeleton-lines"><div class="sk-line w-90"></div><div class="sk-line w-75"></div><div class="sk-line w-60"></div></div></div>
          </div>
          <div class="widget-stack">
            <div class="card card-glow"><div style="text-align:center;padding:20px;"><div style="font-size:32px;margin-bottom:12px;">🧬</div><div style="font-size:14px;font-weight:700;margin-bottom:6px;">Skill Analyzer</div><div style="font-size:12px;color:var(--text-secondary);line-height:1.5;">Paste any job description to extract technical skills, soft skills, tools, and certifications.</div></div></div>
          </div>
        </div>
      </div>

      <!-- TAB: Compare -->
      <div class="tab-panel" id="tab-compare">
        <div class="card card-glow" style="margin-bottom:24px;padding:28px 32px;">
          <div style="display:flex;gap:12px;align-items:end;">
            <div class="field" style="flex:1;"><label class="field-label">Role 1</label><div class="input-wrap"><span class="input-icon">👤</span><input type="text" id="compare-role1" placeholder="e.g. Frontend Developer"/></div></div>
            <div class="field" style="flex:1;"><label class="field-label">Role 2</label><div class="input-wrap"><span class="input-icon">👤</span><input type="text" id="compare-role2" placeholder="e.g. Backend Developer"/></div></div>
            <div class="field"><label class="field-label">&nbsp;</label><button class="btn btn-primary" id="btn-compare"><span class="btn-icon">⚖️</span>Compare</button></div>
          </div>
        </div>
        <div id="compare-results" style="display:none;"></div>
        <div id="compare-loading" style="display:none;"><div class="skeleton-lines"><div class="sk-line w-90"></div><div class="sk-line w-75"></div><div class="sk-line w-85"></div></div></div>
      </div>

      <!-- TAB: History -->
      <div class="tab-panel" id="tab-history">
        <div class="section-title">Saved Searches</div>
        <div id="history-list"></div>
        <div id="history-empty" class="card" style="text-align:center;padding:40px;"><div style="font-size:32px;margin-bottom:12px;opacity:0.3;">📋</div><div style="font-size:13px;color:var(--text-muted);font-family:var(--font-mono);">No saved searches yet</div></div>
      </div>

    </div>
  </section>
</main>
</div>

<script>
(function(){
  const API='api.php';
  function qs(s){return document.querySelector(s)}
  function qsa(s){return document.querySelectorAll(s)}
  async function api(module,action,body={}){
    const r=await fetch(`${API}?module=${module}&action=${action}`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
    return r.json();
  }
  async function apiGet(module,action){
    const r=await fetch(`${API}?module=${module}&action=${action}`);return r.json();
  }

  // Tabs
  qsa('.tab-btn').forEach(btn=>{btn.addEventListener('click',()=>{
    qsa('.tab-btn').forEach(b=>b.classList.remove('active'));
    qsa('.tab-panel').forEach(p=>p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-'+btn.dataset.tab)?.classList.add('active');
    if(btn.dataset.tab==='history')loadHistory();
  })});

  // Job Search
  qs('#btn-search')?.addEventListener('click',async()=>{
    const query=qs('#search-query')?.value?.trim();
    if(!query){alert('Enter a search query');return;}
    qs('#search-results').innerHTML='';qs('#search-insights').style.display='none';qs('#search-loading').style.display='block';
    const r=await api('research','search',{query,location:qs('#search-location')?.value||'',level:qs('#search-level')?.value||''});
    qs('#search-loading').style.display='none';
    if(r.success&&r.results){
      const d=r.results;
      // Insights
      if(d.market_insights){
        let ih='<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">';
        ih+='<div class="stat-tile"><div class="stat-tile-val">'+((d.market_insights.demand_level)||'—')+'</div><div class="stat-tile-label">Market Demand</div></div>';
        ih+='<div class="stat-tile"><div class="stat-tile-val">'+(d.market_insights.avg_salary||'—')+'</div><div class="stat-tile-label">Avg Salary</div></div>';
        ih+='<div class="stat-tile"><div class="stat-tile-val">'+(d.total_results||d.results?.length||0)+'</div><div class="stat-tile-label">Results Found</div></div>';
        ih+='</div>';
        if(d.market_insights.trending_skills){
          ih+='<div style="margin-bottom:16px;">';
          d.market_insights.trending_skills.forEach(s=>{ih+='<span class="skill-tag tech">'+s+'</span>';});
          ih+='</div>';
        }
        qs('#search-insights').innerHTML=ih;qs('#search-insights').style.display='block';
      }
      // Results
      let html='';
      (d.results||[]).forEach(j=>{
        html+='<div class="job-card" style="position:relative;"><div class="match-score">'+(j.match_score||'—')+'%</div>';
        html+='<div class="job-title">'+j.title+'</div><div class="job-company">'+j.company+'</div>';
        html+='<div class="job-meta"><span>📍 '+(j.location||'Remote')+'</span><span>💼 '+(j.type||'Full-time')+'</span><span>💰 '+(j.salary_range||'N/A')+'</span><span>📅 '+(j.posted_ago||'Recent')+'</span></div>';
        if(j.description) html+='<div style="margin-top:10px;font-size:12px;color:var(--text-secondary);line-height:1.6;">'+j.description+'</div>';
        if(j.key_skills){html+='<div class="job-skills">';j.key_skills.forEach(s=>{html+='<span>'+s+'</span>';});html+='</div>';}
        html+='</div>';
      });
      qs('#search-results').innerHTML=html;
      // Auto-save
      api('research','save',{type:'job_search',query,results_count:d.results?.length||0,timestamp:new Date().toISOString()});
    }else{
      qs('#search-results').innerHTML='<div class="card" style="color:#f87171;padding:20px;">'+(r.message||'Search failed')+'</div>';
    }
  });

  // Skill Extraction
  qs('#btn-skills')?.addEventListener('click',async()=>{
    const text=qs('#skills-text')?.value?.trim();
    if(!text){alert('Paste some text first');return;}
    qs('#skills-results').style.display='none';qs('#skills-loading').style.display='block';
    const r=await api('research','skills',{text});
    qs('#skills-loading').style.display='none';
    if(r.success&&r.skills){
      const s=r.skills;let html='<div class="card card-glow" style="margin-bottom:16px;">';
      if(s.summary) html+='<div style="font-size:13px;color:var(--text-secondary);margin-bottom:16px;line-height:1.6;">'+s.summary+'</div>';
      if(s.technical_skills?.length){
        html+='<div class="section-title">Technical Skills</div><div style="margin-bottom:16px;">';
        s.technical_skills.forEach(sk=>{html+='<span class="skill-tag tech">'+(sk.name||sk)+' '+(sk.proficiency?'· '+sk.proficiency:'')+'</span>';});
        html+='</div>';
      }
      if(s.soft_skills?.length){
        html+='<div class="section-title">Soft Skills</div><div style="margin-bottom:16px;">';
        s.soft_skills.forEach(sk=>{html+='<span class="skill-tag soft">'+(sk.name||sk)+'</span>';});
        html+='</div>';
      }
      if(s.tools_platforms?.length){
        html+='<div class="section-title">Tools & Platforms</div><div style="margin-bottom:16px;">';
        s.tools_platforms.forEach(sk=>{html+='<span class="skill-tag tool">'+(sk.name||sk)+'</span>';});
        html+='</div>';
      }
      if(s.certifications_mentioned?.length){
        html+='<div class="section-title">Certifications</div><div style="margin-bottom:16px;">';
        s.certifications_mentioned.forEach(c=>{html+='<span class="badge purple">'+c+'</span> ';});
        html+='</div>';
      }
      if(s.skill_gap_tips?.length){
        html+='<div class="section-title">Tips</div><div style="font-size:12px;color:var(--text-secondary);line-height:1.7;">';
        s.skill_gap_tips.forEach(t=>{html+='<div style="margin-bottom:6px;">💡 '+t+'</div>';});
        html+='</div>';
      }
      html+='</div>';
      qs('#skills-results').innerHTML=html;qs('#skills-results').style.display='block';
    }else{
      qs('#skills-results').innerHTML='<div class="card" style="color:#f87171;">'+(r.message||'Extraction failed')+'</div>';
      qs('#skills-results').style.display='block';
    }
  });

  // Compare Roles
  qs('#btn-compare')?.addEventListener('click',async()=>{
    const r1=qs('#compare-role1')?.value?.trim();
    const r2=qs('#compare-role2')?.value?.trim();
    if(!r1||!r2){alert('Enter both roles');return;}
    qs('#compare-results').style.display='none';qs('#compare-loading').style.display='block';
    const r=await api('research','compare',{roles:[r1,r2]});
    qs('#compare-loading').style.display='none';
    if(r.success&&r.comparison){
      const c=r.comparison;let html='<div class="card card-glow">';
      if(c.comparison_table?.length){
        html+='<table class="compare-table"><thead><tr><th>Category</th>';
        (c.roles_compared||[r1,r2]).forEach(role=>{html+='<th>'+role+'</th>';});
        html+='</tr></thead><tbody>';
        c.comparison_table.forEach(row=>{
          html+='<tr><td style="font-weight:600;color:var(--text-primary);">'+row.category+'</td>';
          Object.values(row.values||{}).forEach(v=>{html+='<td>'+v+'</td>';});
          html+='</tr>';
        });
        html+='</tbody></table>';
      }
      if(c.recommendation){
        html+='<div style="margin-top:20px;padding:16px;background:rgba(0,242,255,0.04);border:1px solid rgba(0,242,255,0.12);border-radius:var(--radius-md);font-size:13px;color:var(--text-secondary);line-height:1.7;">💡 '+c.recommendation+'</div>';
      }
      if(c.detailed_analysis){
        html+='<div style="margin-top:16px;font-size:13px;color:var(--text-secondary);line-height:1.7;">'+c.detailed_analysis+'</div>';
      }
      html+='</div>';
      qs('#compare-results').innerHTML=html;qs('#compare-results').style.display='block';
      api('research','save',{type:'role_comparison',roles:[r1,r2],timestamp:new Date().toISOString()});
    }else{
      qs('#compare-results').innerHTML='<div class="card" style="color:#f87171;">'+(r.message||'Comparison failed')+'</div>';
      qs('#compare-results').style.display='block';
    }
  });

  // History
  async function loadHistory(){
    const r=await apiGet('research','history');
    const list=qs('#history-list');const empty=qs('#history-empty');
    if(r.success&&r.history?.length){
      empty.style.display='none';let html='';
      r.history.forEach(h=>{
        const label=h.query||h.roles?.join(' vs ')||h._type||'Search';
        const time=h._updated?new Date(h._updated).toLocaleDateString():'';
        html+='<div class="history-item"><div class="history-dot" style="background:'+(h._type==='role_comparison'?'var(--purple)':'var(--cyan)')+'"></div>';
        html+='<div class="history-text">'+label+'</div><div class="history-time">'+time+'</div>';
        html+='<button class="btn btn-ghost" style="padding:4px 10px;font-size:10px;" onclick="deleteHistory(\''+h._id+'\')">✕</button></div>';
      });
      list.innerHTML=html;
    }else{
      list.innerHTML='';empty.style.display='block';
    }
  }
  window.deleteHistory=async function(id){
    await api('research','delete',{id});loadHistory();
  };
})();
</script>
</body>
</html>
