@extends('layouts.app')

@section('title', 'Piano')

@section('content')

<header>
  <div class="eyebrow">Guardaroba matematico</div>
  <h1>Il tuo armadio, testato <em>giorno per giorno</em></h1>
  <p class="sub">Imposta il tuo ciclo del bucato e la simulazione ti mostra un mese intero: cosa indossi ogni giorno, quando lavi, cosa è steso e — soprattutto — se rimani mai senza roba pulita. I capi si gestiscono nella pagina <a href="{{ route('categorie') }}" class="inline-link">Categorie</a>.</p>
</header>

<section class="panel" aria-label="Ciclo del bucato">
  <h2>1 · Il tuo ciclo del bucato</h2>
  <p class="desc">Questi valori determinano quanti giorni devi coprire con capi puliti.</p>

  <div class="profiles">
    <select id="profileSel" aria-label="Programmazioni salvate"><option value="">— Programmazioni salvate —</option></select>
    <button class="mini-btn" id="saveProfile">Salva</button>
    <button class="mini-btn danger" id="delProfile">Elimina</button>
  </div>

  <div class="ctrl">
    <label for="startDate">Data di inizio del piano</label>
    <input type="date" id="startDate" class="date-input">
  </div>

  <div class="ctrl">
    <label for="wash">Ogni quanti giorni fai il bucato</label>
    <span class="val" id="washOut">4</span>
    <input type="range" id="wash" min="2" max="20" step="1" value="4">
  </div>
  <div class="ctrl">
    <label for="dry">Giorni di asciugatura</label>
    <span class="val" id="dryOut">4</span>
    <input type="range" id="dry" min="1" max="6" step="1" value="4">
  </div>
  <div class="ctrl">
    <label for="buf">Scorta di sicurezza (giorni extra)</label>
    <span class="val" id="bufOut">2</span>
    <input type="range" id="buf" min="0" max="4" step="1" value="2">
  </div>
  <div class="ctrl">
    <label for="cap">Capacità della tua lavatrice (kg)</label>
    <span class="val" id="capOut">7</span>
    <input type="range" id="cap" min="5" max="10" step="1" value="7">
  </div>

  <div class="cycle-bar-wrap">
    <div class="cycle-label">
      <span>Giorni da coprire</span>
      <span class="cycle-total"><span id="cycleTot">10</span> giorni</span>
    </div>
    <div class="cycle-bar" role="img" aria-label="Ripartizione del ciclo">
      <div class="seg seg-use" id="segUse"></div>
      <div class="seg seg-dry" id="segDry"></div>
      <div class="seg seg-buf" id="segBuf"></div>
    </div>
  </div>
</section>

<section class="panel" aria-label="Copertura dell'armadio">
  <h2>2 · Copertura dell'armadio</h2>
  <p class="desc">Quanti giorni copre ogni categoria con i capi che possiedi. Canotta, t-shirt, polo e camicie fanno gruppo: indossi un capo "sopra" al giorno, quindi contano insieme.</p>
  <div class="cov-strip" id="covStrip"></div>
  <a class="mini-btn" href="{{ route('categorie') }}">👕 Gestisci categorie e capi</a>
</section>

<section class="panel" aria-label="Simulazione del mese">
  <h2>3 · Il tuo mese, giorno per giorno</h2>
  <p class="desc">Simulazione di 30 giorni, <strong>un giorno per schermata</strong>: scorri lateralmente (swipe) o usa le frecce per navigare. Tocca il giorno per vedere foto e alternative. Ogni capo è numerato (es. Tee3 = t-shirt n°3) così vedi la rotazione reale.</p>

  <div class="summary" id="summary"></div>
  <div id="verdict"></div>

  <div class="legend">
    <span><span class="dot" style="background:var(--amber)"></span> Giorno di bucato (bordo ambra)</span>
    <span><span class="dot" style="background:var(--amber);opacity:.4"></span> Asciugatura in corso (barra di avanzamento)</span>
    <span><span class="dot" style="background:var(--green)"></span> Capi tornati puliti</span>
    <span><span class="dot" style="background:var(--red)"></span> Manca un capo pulito</span>
    <span><span class="chip reuse" style="border-color:var(--faint);color:var(--muted)">tratteggiato</span> capo al 2° uso</span>
  </div>

  <div class="view-toggle" id="viewToggle">
    <button type="button" data-view="swipe" class="active">📅 Giorno</button>
    <button type="button" data-view="calendar">🗓 Calendario</button>
  </div>

  <div class="day-nav" id="dayNav">
    <button class="day-arrow" id="dayPrev" aria-label="Giorno precedente">‹</button>
    <div class="day-nav-center">
      <div class="day-nav-title" id="dayNavTitle">—</div>
      <div class="day-nav-sub" id="dayNavSub">—</div>
      <div class="day-progress" id="dayProg" aria-hidden="true"></div>
    </div>
    <button class="day-arrow" id="dayNext" aria-label="Giorno successivo">›</button>
  </div>
  <button class="day-today" id="dayToday" hidden>Vai a oggi</button>
  <div class="day-view" id="cal"></div>
  <div class="cal-grid" id="calGrid" hidden></div>

  <div class="actions">
    <button class="primary" id="genBtn">Genera lista della spesa</button>
    <button class="ghost" id="copyBtn" style="display:none">Copia lista</button>
  </div>
  <div id="lista"><pre id="listaTxt"></pre><div class="copied" id="copiedMsg">Copiata negli appunti ✓</div></div>
</section>

<footer>Consigliato = usi al giorno × (giorni tra bucati + asciugatura + scorta) ÷ usi per capo · Simulazione su 30 giorni<br>Pesi di riferimento da asciutto: maglietta 150–250 g (tessuto 130–220 GSM) · jeans 600–800 g · felpa 500–1000 g (250–450 GSM) · la capacità della lavatrice si riferisce sempre al peso asciutto</footer>

@endsection

@push('modals')
<div class="modal-bg" id="modalBg" hidden>
  <div class="modal" role="dialog" aria-modal="true" aria-label="Outfit del giorno">
    <div class="modal-head">
      <span class="modal-title" id="modalTitle"></span>
      <button class="modal-close" id="modalClose" aria-label="Chiudi">✕</button>
    </div>
    <div class="outfit" id="outfitBody"></div>
    <div class="modal-extra" id="modalExtra"></div>
  </div>
</div>
@endpush

@push('scripts')
<script>
const wash = $('wash'), dry = $('dry'), buf = $('buf');
let LAST = [];

/* ---------- sincronizzazione controlli ↔ CFG ---------- */
function cfgToInputs() {
  wash.value = CFG.wash; dry.value = CFG.dry; buf.value = CFG.buf;
  $('cap').value = CFG.cap; $('startDate').value = CFG.start;
}
function inputsToCfg() {
  CFG.wash = +wash.value; CFG.dry = +dry.value; CFG.buf = +buf.value;
  CFG.cap = +$('cap').value; CFG.start = $('startDate').value;
}

/* ---------- copertura per categoria ---------- */
function renderCoverage() {
  const need = cycleDays();
  $('covStrip').innerHTML = PARENTS.map(p => {
    const cov = p.group === 'top' ? groupCoverage() : coverage(p);
    if (!p.active) {
      return '<span class="cov-item off"><span class="cat-dot" style="background:' + p.color + '"></span>' + p.name + ' <b>esclusa</b></span>';
    }
    const ok = cov >= need;
    return '<span class="cov-item' + (ok ? '' : ' ko') + '">' +
      '<span class="cat-dot" style="background:' + p.color + '"></span>' + p.name +
      ' <b>' + cov + '/' + need + '</b>' + (ok ? ' ✓' : '') + '</span>';
  }).join('');
}

/* ---------- render ---------- */
function render() {
  inputsToCfg();
  $('washOut').textContent = CFG.wash;
  $('dryOut').textContent = CFG.dry;
  $('bufOut').textContent = CFG.buf;
  const c = cycleDays();
  $('cycleTot').textContent = c;
  $('segUse').style.flexGrow = CFG.wash;
  $('segUse').textContent = 'Uso · ' + CFG.wash + 'g';
  $('segDry').style.flexGrow = CFG.dry;
  $('segDry').textContent = 'Stesi · ' + CFG.dry + 'g';
  $('segBuf').style.flexGrow = Math.max(CFG.buf, .4);
  $('segBuf').textContent = CFG.buf > 0 ? '+' + CFG.buf : '';

  renderCoverage();

  const capKg = CFG.cap;
  $('capOut').textContent = capKg;
  const { days, shortagesTotal, batches } = simulate(DAYS);
  LAST = days;

  const washDays = days.filter(x => x.isWash && x.washedCount > 0).length;
  const totCapi = PARENTS.filter(p => p.active).reduce((s, p) => s + p.garments.length + p.variants.reduce((a, v) => a + v.own, 0), 0);
  const kgs = batches.map(b => b.kg);
  const avgKg = kgs.length ? kgs.reduce((a, b) => a + b, 0) / kgs.length : 0;
  const maxKg = kgs.length ? Math.max(...kgs) : 0;
  const overloads = kgs.filter(k => k > capKg).length;

  $('summary').innerHTML =
    '<div class="stat"><div class="label">Capi in rotazione</div><div class="value">' + totCapi + '</div></div>' +
    '<div class="stat"><div class="label">Bucati nel mese</div><div class="value">' + washDays + '</div></div>' +
    '<div class="stat"><div class="label">Carico medio</div><div class="value">' + avgKg.toFixed(1) + '<span style="font-size:14px;color:var(--muted)"> kg</span></div><div class="label">' + Math.round(avgKg / capKg * 100) + '% della lavatrice</div></div>' +
    '<div class="stat"><div class="label">Carico massimo</div><div class="value ' + (maxKg > capKg ? 'ko' : '') + '">' + maxKg.toFixed(1) + '<span style="font-size:14px;color:var(--muted)"> kg</span></div><div class="label">limite ' + capKg + ' kg</div></div>' +
    '<div class="stat"><div class="label">Giorni scoperti</div><div class="value ' + (shortagesTotal > 0 ? 'ko' : 'ok') + '">' + shortagesTotal + '</div></div>';

  let verdictHtml = shortagesTotal === 0
    ? '<div class="alert ok">✓ Con queste quantità arrivi a fine mese sempre coperto, bucato ogni ' + CFG.wash + ' giorni incluso.</div>'
    : '<div class="alert ko">⚠ Ti mancano capi puliti in ' + shortagesTotal + ' occasioni. Aggiungi capi nelle categorie che non coprono il ciclo, oppure lava più spesso.</div>';
  if (overloads > 0) {
    verdictHtml += '<div class="alert ko">⚠ In ' + overloads + ' bucati superi i ' + capKg + ' kg della lavatrice: dividi il carico in due lavaggi o lava più spesso.</div>';
  } else if (avgKg < capKg * 0.45 && kgs.length > 0) {
    verdictHtml += '<div class="alert warn">◐ La lavatrice parte mezza vuota (' + avgKg.toFixed(1) + ' kg su ' + capKg + '): allunga l\'intervallo tra i bucati per fare carichi pieni e consumare meno.</div>';
  }
  $('verdict').innerHTML = verdictHtml;

  const cal = $('cal');
  cal.innerHTML = '';
  days.forEach((dd, di) => {
    const el = document.createElement('div');
    el.className = 'day' + (dd.isWash ? ' washday' : '') + (dd.shortage ? ' shortage' : '');
    const dt = dateForDay(di);
    const isToday = new Date().toDateString() === dt.toDateString();
    if (dd.dryingBatches.length > 0) el.classList.add('dryingday');

    let dryHtml = '';
    dd.dryingBatches.forEach(b => {
      const pct = Math.round(b.dayN / b.total * 100);
      const readyDt = fmtDay.format(dateForDay(b.ready - 1));
      dryHtml +=
        '<div class="dry-batch">' +
          '<div class="dry-row"><span>〰 ' + b.count + ' capi stesi · giorno ' + b.dayN + '/' + b.total + '</span><span class="dry-ready">pronti ' + readyDt + '</span></div>' +
          '<div class="dry-track"><div class="dry-fill" style="width:' + pct + '%"></div></div>' +
        '</div>';
    });

    let washHtml = '';
    if (dd.isWash && dd.washedCount > 0) {
      const pctLoad = Math.min(Math.round(dd.washedKg / capKg * 100), 100);
      const over = dd.washedKg > capKg;
      const comp = dd.washedByCat.map(x => x.n + ' ' + x.name).join(', ');
      washHtml =
        '<div class="load' + (over ? ' over' : '') + '">' +
          '<div class="dry-row"><span>🧺 ' + dd.washedCount + ' capi · ' + dd.washedKg.toFixed(1) + ' kg</span><span class="dry-ready">' + Math.round(dd.washedKg / capKg * 100) + '% di ' + capKg + ' kg</span></div>' +
          '<div class="dry-track"><div class="load-fill' + (over ? ' over' : '') + '" style="width:' + pctLoad + '%"></div></div>' +
          '<div class="day-foot">' + comp + (over ? ' · ⚠ dividi in 2 lavaggi' : '') + '</div>' +
        '</div>';
    }

    el.innerHTML =
      '<div class="day-head">' +
        '<div class="day-when">' +
          '<span class="day-num">' + (isToday ? 'Oggi' : cap(fmtWeekday.format(dt))) + '</span>' +
          '<span class="day-date">' + cap(fmtDate.format(dt)) + ' · giorno ' + dd.day + ' di ' + days.length + '</span>' +
        '</div>' +
      (dd.shortage ? '<span class="badge badge-ko">Manca</span>' : (dd.isWash ? '<span class="badge badge-wash">🧺 Bucato</span>' : '')) +
      '</div>' +
      '<div class="day-outfit">' + ofitSlots(dd, di) + '</div>' +
      (dd.readyCount > 0 ? '<div class="ready-note">✓ ' + dd.readyCount + ' capi tornati puliti</div>' : '') +
      dryHtml +
      washHtml;
    cal.appendChild(el);
  });
  renderCalGrid();
  $('dayProg').innerHTML = days.map((dd, i) =>
    '<span class="dp-seg' + (dd.shortage ? ' dp-miss' : (dd.isWash ? ' dp-wash' : '')) +
    '" data-goto="' + i + '" title="Giorno ' + dd.day + (dd.shortage ? ' · manca un capo' : (dd.isWash ? ' · bucato' : '')) + '"></span>'
  ).join('');
  requestAnimationFrame(() => scrollToDay(Math.min(curDayIdx, Math.max(0, days.length - 1)), false));

  $('lista').classList.remove('show');
  $('copyBtn').style.display = 'none';

  autosave();
}

/* ---------- eventi ---------- */
document.addEventListener('click', e => {
  const sw = e.target.closest('[data-swap]');
  if (sw) { if (cycleOverride(sw.getAttribute('data-swap'))) render(); return; }
  const msw = e.target.closest('[data-mswap]');
  if (msw) {
    const k = msw.getAttribute('data-mswap');
    if (cycleOverride(k)) render();
    openModal(+k.split('|')[0]);
    return;
  }
  const dayEl = e.target.closest('[data-day]');
  if (dayEl && !e.target.closest('.chip')) { openModal(+dayEl.getAttribute('data-day')); }
});

/* ---------- vista calendario (griglia mese) ---------- */
function renderCalGrid() {
  const grid = $('calGrid');
  const days = LAST;
  if (!days.length) { grid.innerHTML = ''; return; }
  const lead = (dateForDay(0).getDay() + 6) % 7; // lunedì = 0
  const head = ['L','M','M','G','V','S','D'].map(x => '<div class="cg-h">' + x + '</div>').join('');
  let cells = '';
  for (let k = 0; k < lead; k++) cells += '<div class="cg-cell cg-empty"></div>';
  days.forEach((dd, i) => {
    const isToday = new Date().toDateString() === dateForDay(i).toDateString();
    const dots = dd.worn.filter(w => !w.miss).slice(0, 8).map(w => '<span class="cg-dot" style="background:' + w.color + '"></span>').join('');
    const flag = dd.shortage ? '<span class="cg-flag">✕</span>' : (dd.isWash ? '<span class="cg-flag">🧺</span>' : '');
    cells +=
      '<button type="button" class="cg-cell' + (dd.isWash ? ' wash' : '') + (dd.shortage ? ' miss' : '') + (isToday ? ' today' : '') + '" data-day="' + i + '" aria-label="Giorno ' + dd.day + '">' +
        '<span class="cg-num">' + dd.day + '</span>' +
        '<span class="cg-dots">' + dots + '</span>' + flag +
      '</button>';
  });
  grid.innerHTML = '<div class="cg-head">' + head + '</div><div class="cg-body">' + cells + '</div>';
}

/* ---------- dettaglio giorno ---------- */
function openModal(di) {
  const dd = LAST[di];
  if (!dd) return;
  const dt = dateForDay(di);
  $('modalTitle').textContent = 'Outfit di ' + fmtDay.format(dt);
  const body = $('outfitBody');
  body.innerHTML = '';
  SLOT_ORDER.forEach(so => {
    const p = findParent(so.key);
    const wn = dd.worn.find(x => x.key === so.key);
    if (!p.active || !wn) return;
    const small = so.key === 'mut' || so.key === 'cal';
    let name = wn.label, imgHtml = '';
    const gm = wn.uid ? p.garments.find(g => g.uid === wn.uid) : null;
    if (gm) {
      name = gm.n;
      imgHtml = gm.img
        ? '<img src="' + gm.img + '" alt="' + gm.n + '">'
        : '<div class="slot-ph" style="background:' + gm.c + ';color:' + textOn(gm.c) + '">' + gm.s + '</div>';
    } else if (wn.miss) {
      name = 'Nessun capo pulito!';
      imgHtml = '<div class="slot-ph" style="background:var(--red-dim);color:var(--red)">✕</div>';
    } else {
      const v = p.variants[wn.vid];
      name = v ? presetLabel(p.presets[v.pi]) + ' n°' + wn.label.replace(/^\D+/, '') : wn.label;
      imgHtml = '<div class="slot-ph" style="background:' + hexDim(p.color, .25) + ';color:' + p.color + '">' + wn.label + '</div>';
    }
    const swapBtn = wn.swappable
      ? '<button class="slot-swap" data-mswap="' + di + '|' + so.key + '">Cambia ⇄</button>'
      : '';
    const div = document.createElement('div');
    div.className = 'slot' + (small ? ' wide' : '');
    div.innerHTML =
      '<div class="slot-img">' + imgHtml + '</div>' +
      '<div class="slot-info">' +
        '<span class="slot-cat">' + so.cat + (wn.reuse ? ' · 2° uso' : '') + (wn.layer ? ' · sotto la camicia' : '') + '</span>' +
        '<span class="slot-name' + (wn.miss ? ' miss' : '') + '">' + name + '</span>' +
      swapBtn + '</div>';
    body.appendChild(div);
  });
  const extra = $('modalExtra');
  extra.innerHTML = '';
  if (dd.isWash && dd.washedCount > 0) {
    extra.innerHTML += '<div class="alert warn" style="margin:0">🧺 Giorno di bucato: lavi e stendi ' + dd.washedCount + ' capi (' + dd.washedKg.toFixed(1) + ' kg)</div>';
  }
  if (dd.dryingBatches.length > 0) {
    dd.dryingBatches.forEach(b => {
      extra.innerHTML += '<div class="alert" style="margin:0;background:var(--amber-dim);border-color:rgba(240,178,82,.35);color:var(--amber)">〰 ' + b.count + ' capi stesi (giorno ' + b.dayN + '/' + b.total + ')</div>';
    });
  }
  $('modalBg').hidden = false;
  document.body.style.overflow = 'hidden';
}
function closeModal() {
  $('modalBg').hidden = true;
  if ($('dlgBg').hidden) document.body.style.overflow = '';
}
$('modalClose').addEventListener('click', closeModal);
$('modalBg').addEventListener('click', e => { if (e.target === $('modalBg')) closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape' && !$('modalBg').hidden) closeModal(); });

/* ---------- programmazioni ---------- */
$('saveProfile').addEventListener('click', () => {
  const cur = $('profileSel');
  const curName = cur.value ? (PROFS.find(p => p.id == cur.value) || {}).name : '';
  uiPrompt('Nome della programmazione:', curName || 'Estate ' + new Date().getFullYear()).then(name => {
    if (!name) return;
    api('/api/plans', 'POST', { name: name, state: serialize() })
      .then(p => loadProfiles($('profileSel'), p.id))
      .catch(() => uiAlert('Errore nel salvataggio.'));
  });
});
$('delProfile').addEventListener('click', () => {
  const id = $('profileSel').value;
  if (!id) return;
  const name = (PROFS.find(p => p.id == id) || {}).name || '';
  uiConfirm('Eliminare la programmazione "' + name + '"?').then(ok => {
    if (!ok) return;
    api('/api/plans/' + id, 'DELETE')
      .then(() => loadProfiles($('profileSel'), ''))
      .catch(() => uiAlert('Errore nella cancellazione.'));
  });
});
$('profileSel').addEventListener('change', e => {
  const id = e.target.value;
  if (!id) return;
  api('/api/plans/' + id).then(p => {
    applyState(p.state);
    cfgToInputs();
    render();
    loadProfiles($('profileSel'), id);
  }).catch(() => {});
});

/* ---------- lista della spesa ---------- */
$('genBtn').addEventListener('click', () => {
  const need = cycleDays();
  const righe = ['🧺 LISTA ARMADIO',
    '(dal ' + fmtDay.format(startDate()) + ' · bucato ogni ' + CFG.wash + 'gg, asciugatura ' + CFG.dry + 'gg, scorta +' + CFG.buf + ')', ''];
  PARENTS.forEach(p => {
    if (!p.active) { righe.push('— ' + p.name.toUpperCase() + ': esclusa in questo periodo'); return; }
    const cov = p.group === 'top' ? groupCoverage() : coverage(p);
    righe.push((cov >= need ? '✓ ' : '⚠ ') + p.name.toUpperCase() + ' — copre ' + cov + '/' + need + ' gg' + (p.group === 'top' ? ' (gruppo sopra)' : ''));
    p.garments.forEach(gm => righe.push('   ★ ' + gm.n));
    p.variants.forEach(v => {
      if (v.own > 0) righe.push('   ☐ ' + presetLabel(p.presets[v.pi]) + ' (generici): ' + v.own + (v.wears > 1 ? ' (' + v.wears + ' usi a capo)' : ''));
    });
    if (cov < need) righe.push('   → aggiungi capi per coprire altri ' + (need - cov) + ' giorni');
  });
  $('listaTxt').textContent = righe.join('\n');
  $('lista').classList.add('show');
  $('copyBtn').style.display = 'inline-block';
  $('copiedMsg').style.display = 'none';
});
$('copyBtn').addEventListener('click', () => {
  navigator.clipboard.writeText($('listaTxt').textContent).then(() => {
    $('copiedMsg').style.display = 'block';
  });
});

wash.addEventListener('input', render);
dry.addEventListener('input', render);
buf.addEventListener('input', render);
$('cap').addEventListener('input', render);
$('startDate').addEventListener('change', render);

/* ---------- vista giorni: navigazione swipe ---------- */
let curDayIdx = 0;
let dayScrollRAF = 0;
function dayPages() { return Array.prototype.slice.call($('cal').children); }
function scrollToDay(i, smooth) {
  const pages = dayPages();
  if (!pages.length) return;
  i = Math.max(0, Math.min(i, pages.length - 1));
  const cal = $('cal'), page = pages[i];
  const left = page.offsetLeft - (cal.clientWidth - page.clientWidth) / 2;
  cal.scrollTo({ left: Math.max(0, left), behavior: smooth === false ? 'auto' : 'smooth' });
  curDayIdx = i;
  syncDayNav();
}
function currentDayFromScroll() {
  const pages = dayPages();
  if (!pages.length) return 0;
  const cal = $('cal'), center = cal.scrollLeft + cal.clientWidth / 2;
  let best = 0, bestD = Infinity;
  pages.forEach((p, i) => { const c = p.offsetLeft + p.offsetWidth / 2; const dd = Math.abs(c - center); if (dd < bestD) { bestD = dd; best = i; } });
  return best;
}
function todayIndex() {
  const t = new Date().toDateString();
  for (let i = 0; i < LAST.length; i++) if (dateForDay(i).toDateString() === t) return i;
  return -1;
}
function syncDayNav() {
  const n = LAST.length;
  if (!n) return;
  const i = Math.min(curDayIdx, n - 1);
  const dt = dateForDay(i);
  const isToday = new Date().toDateString() === dt.toDateString();
  $('dayNavTitle').textContent = isToday ? 'Oggi' : cap(fmtWeekday.format(dt));
  $('dayNavSub').textContent = cap(fmtDate.format(dt)) + ' · giorno ' + (i + 1) + ' di ' + n;
  const segs = $('dayProg').children;
  for (let k = 0; k < segs.length; k++) segs[k].classList.toggle('dp-cur', k === i);
  $('dayPrev').disabled = i <= 0;
  $('dayNext').disabled = i >= n - 1;
  const ti = todayIndex();
  $('dayToday').hidden = (ti < 0 || ti === i);
}
$('cal').addEventListener('scroll', () => {
  if (dayScrollRAF) return;
  dayScrollRAF = requestAnimationFrame(() => {
    dayScrollRAF = 0;
    const i = currentDayFromScroll();
    if (i !== curDayIdx) { curDayIdx = i; syncDayNav(); }
  });
});
$('dayPrev').addEventListener('click', () => scrollToDay(curDayIdx - 1));
$('dayNext').addEventListener('click', () => scrollToDay(curDayIdx + 1));
$('dayToday').addEventListener('click', () => { const ti = todayIndex(); if (ti >= 0) scrollToDay(ti); });
$('dayProg').addEventListener('click', e => { const s = e.target.closest('[data-goto]'); if (s) scrollToDay(+s.getAttribute('data-goto')); });

/* ---------- toggle vista: giorno (swipe) / calendario ---------- */
function setView(mode) {
  const swipe = mode === 'swipe';
  $('cal').hidden = !swipe;
  $('calGrid').hidden = swipe;
  $('dayNav').style.display = swipe ? '' : 'none';
  Array.prototype.forEach.call($('viewToggle').children, b => b.classList.toggle('active', b.getAttribute('data-view') === mode));
  if (swipe) requestAnimationFrame(() => scrollToDay(curDayIdx, false));
  else $('dayToday').hidden = true;
}
$('viewToggle').addEventListener('click', e => { const b = e.target.closest('[data-view]'); if (b) setView(b.getAttribute('data-view')); });

/* ---------- init ---------- */
CFG.start = isoToday();
cfgToInputs();
render();
loadAuto().then(() => { cfgToInputs(); render(); const ti = todayIndex(); if (ti > 0) scrollToDay(ti, false); });
loadProfiles($('profileSel'), '');
</script>
@endpush
