@extends('layouts.app')

@section('title', 'Storico')

@section('content')

<header class="page-head">
  <div class="eyebrow">Archivio</div>
  <h1>I tuoi <em>outfit passati</em></h1>
  <p class="sub">Tutti i giorni già trascorsi dall'inizio del piano, con quello che avevi addosso, i bucati fatti e i giorni in cui è mancato qualcosa. Sola lettura: qui non si modifica niente, si guarda e basta.</p>
</header>

<section class="panel" aria-label="Storico outfit">
  <div class="toolbar">
    <select id="histPlan" aria-label="Programmazione da consultare">
      <option value="">Piano corrente</option>
    </select>
  </div>

  <div class="summary" id="histSummary"></div>

  <div class="hist-nav" id="histNav" hidden>
    <button class="day-arrow" id="mPrev" aria-label="Mese precedente">‹</button>
    <div>
      <div class="hist-month" id="mTitle">—</div>
      <div class="hist-count" id="mCount">—</div>
    </div>
    <button class="day-arrow" id="mNext" aria-label="Mese successivo">›</button>
  </div>

  <div class="hist-list" id="histList"></div>
</section>

@endsection

@push('scripts')
<script>
let HIST = [];      // giorni simulati dall'inizio del piano fino a oggi
let MONTHS = [];    // raggruppamento per mese: { key, label, idxs }
let mIdx = 0;       // mese mostrato (parte dal più recente)

/* Costruisce lo storico: dal giorno di inizio piano fino a oggi incluso. */
function buildHistory() {
  const elapsed = daysElapsed();
  const total = Math.min(elapsed + 1, MAX_SIM);
  HIST = startDate() > new Date() ? [] : simulate(total).days;

  MONTHS = [];
  HIST.forEach((dd, i) => {
    const dt = dateForDay(i);
    const key = dt.getFullYear() + '-' + dt.getMonth();
    let m = MONTHS.find(x => x.key === key);
    if (!m) { m = { key, label: cap(fmtMonth.format(dt)), idxs: [] }; MONTHS.push(m); }
    m.idxs.push(i);
  });
  mIdx = Math.max(0, MONTHS.length - 1);
}

function renderSummary() {
  const bucati = HIST.filter(d => d.isWash && d.washedCount > 0).length;
  const scoperti = HIST.filter(d => d.shortage).length;
  const kg = HIST.reduce((s, d) => s + d.washedKg, 0);
  $('histSummary').innerHTML =
    '<div class="stat"><div class="label">Giorni registrati</div><div class="value">' + HIST.length + '</div><div class="label">dal ' + fmtDay.format(startDate()) + '</div></div>' +
    '<div class="stat"><div class="label">Bucati fatti</div><div class="value">' + bucati + '</div><div class="label">' + kg.toFixed(1) + ' kg lavati</div></div>' +
    '<div class="stat"><div class="label">Giorni scoperti</div><div class="value ' + (scoperti > 0 ? 'ko' : 'ok') + '">' + scoperti + '</div></div>';
}

function renderHistory() {
  const list = $('histList');
  const nav = $('histNav');

  if (!HIST.length) {
    nav.hidden = true;
    $('histSummary').innerHTML = '';
    const futuro = startDate() > new Date();
    list.innerHTML =
      '<div class="empty"><span class="big">🕘</span>' +
      (futuro
        ? 'Il piano inizia il <strong>' + fmtDay.format(startDate()) + '</strong>: non c\'è ancora nessun giorno da archiviare.'
        : 'Nessun giorno da mostrare. Imposta una data di inizio nella pagina Piano.') +
      '</div>';
    return;
  }

  renderSummary();
  nav.hidden = MONTHS.length < 2;
  const m = MONTHS[mIdx];
  $('mTitle').textContent = m.label;
  $('mCount').textContent = m.idxs.length + (m.idxs.length === 1 ? ' giorno' : ' giorni');
  $('mPrev').disabled = mIdx <= 0;
  $('mNext').disabled = mIdx >= MONTHS.length - 1;

  const todayStr = new Date().toDateString();
  // più recenti in cima
  list.innerHTML = m.idxs.slice().reverse().map(i => {
    const dd = HIST[i];
    const dt = dateForDay(i);
    const isToday = dt.toDateString() === todayStr;
    let tags = '';
    if (isToday) tags += '<span class="badge" style="background:var(--indigo-dim);color:var(--indigo-soft)">Oggi</span>';
    if (dd.shortage) tags += '<span class="badge badge-ko">Mancava un capo</span>';
    if (dd.isWash && dd.washedCount > 0) tags += '<span class="badge badge-wash">🧺 ' + dd.washedCount + ' capi · ' + dd.washedKg.toFixed(1) + ' kg</span>';
    return '<article class="hist-day' + (dd.isWash && dd.washedCount > 0 ? ' washday' : '') + (dd.shortage ? ' shortage' : '') + (isToday ? ' is-today' : '') + '">' +
      '<div class="hist-head">' +
        '<div class="hist-when">' +
          '<span class="hist-dow">' + cap(fmtWeekday.format(dt)) + '</span>' +
          '<span class="hist-date">' + cap(fmtDate.format(dt)) + ' · giorno ' + dd.day + '</span>' +
        '</div>' +
        '<div class="hist-tags">' + tags + '</div>' +
      '</div>' +
      '<div class="day-outfit">' + ofitSlots(dd, i, { swap: false }) + '</div>' +
    '</article>';
  }).join('');
}

function refresh() { buildHistory(); renderHistory(); }

$('mPrev').addEventListener('click', () => { if (mIdx > 0) { mIdx--; renderHistory(); window.scrollTo({ top: 0, behavior: 'smooth' }); } });
$('mNext').addEventListener('click', () => { if (mIdx < MONTHS.length - 1) { mIdx++; renderHistory(); window.scrollTo({ top: 0, behavior: 'smooth' }); } });

/* Cambio programmazione: carica lo stato solo per la consultazione, senza salvarlo. */
$('histPlan').addEventListener('change', e => {
  const id = e.target.value;
  const req = id ? api('/api/plans/' + id).then(p => p.state) : api('/api/plans/auto');
  req.then(s => { applyState(s); refresh(); }).catch(() => {});
});

/* ---------- init ---------- */
loadAuto().then(() => {
  refresh();
  loadProfiles($('histPlan'), '').then(() => {
    $('histPlan').querySelector('option[value=""]').textContent = 'Piano corrente';
  });
});
</script>
@endpush
