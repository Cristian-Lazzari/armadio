@extends('layouts.app')

@section('title', 'Bagaglio')

@section('content')

<header class="page-head">
  <div class="eyebrow">Valigia</div>
  <h1>Crea il tuo <em>bagaglio</em></h1>
  <p class="sub">Dici quanti giorni parti e con che limite di peso viaggi: l'app riempie la valigia partendo dai capi più leggeri, ti avvisa se sfori il limite d'imbarco e ti mostra già cosa metterti ogni giorno del viaggio.</p>
</header>

<section class="panel" aria-label="Dati del viaggio">
  <h2>1 · Il viaggio</h2>
  <p class="desc">La scorta sono giorni extra di capi, per gli imprevisti. Il peso della valigia vuota e degli extra non vestiario conta nel limite d'imbarco.</p>

  <div class="trip-grid">
    <div class="trip-field">
      <label for="bStart">Data di partenza</label>
      <input type="date" class="num-input" id="bStart">
    </div>
    <div class="trip-field">
      <label for="bDays">Giorni di viaggio</label>
      <div class="stepper" style="width:100%;justify-content:space-between">
        <button type="button" id="bDaysDec" aria-label="Meno giorni">−</button>
        <span class="n" id="bDays">5</span>
        <button type="button" id="bDaysInc" aria-label="Più giorni">+</button>
      </div>
    </div>
    <div class="trip-field">
      <label for="bSpare">Scorta (giorni extra)</label>
      <div class="stepper" style="width:100%;justify-content:space-between">
        <button type="button" id="bSpareDec" aria-label="Meno scorta">−</button>
        <span class="n" id="bSpare">1</span>
        <button type="button" id="bSpareInc" aria-label="Più scorta">+</button>
      </div>
    </div>
    <div class="trip-field">
      <label for="bLimitSel">Limite d'imbarco</label>
      <select id="bLimitSel">
        <option value="7">Zaino piccolo · 7 kg</option>
        <option value="8">Bagaglio a mano · 8 kg</option>
        <option value="10">Bagaglio a mano · 10 kg</option>
        <option value="15">Trolley medio · 15 kg</option>
        <option value="20">Stiva · 20 kg</option>
        <option value="23">Stiva · 23 kg</option>
        <option value="custom">Personalizzato…</option>
      </select>
      <input type="number" class="num-input" id="bLimit" min="1" max="40" step="0.5" hidden aria-label="Limite personalizzato in kg">
    </div>
    <div class="trip-field">
      <label for="bEmpty">Valigia vuota (kg)</label>
      <input type="number" class="num-input" id="bEmpty" min="0" max="15" step="0.1">
      <span class="hint">Il peso del trolley/zaino a vuoto.</span>
    </div>
    <div class="trip-field">
      <label for="bExtra">Extra non vestiario (kg)</label>
      <input type="number" class="num-input" id="bExtra" min="0" max="20" step="0.1">
      <span class="hint">Scarpe, trousse, caricabatterie, libri…</span>
    </div>
  </div>
</section>

<div class="bag-sticky">
  <div class="gauge" id="gauge">
    <div class="gauge-top">
      <div><span class="gauge-kg" id="gKg">0,0</span><small>kg in valigia</small></div>
      <div class="gauge-limit" id="gLimit">limite 10 kg</div>
    </div>
    <div class="gauge-track"><div class="gauge-fill" id="gFill" style="width:0%"></div></div>
    <div class="gauge-msg" id="gMsg"></div>
    <div class="gauge-breakdown" id="gBreak"></div>
  </div>
</div>

<section class="panel" aria-label="Contenuto della valigia">
  <h2>2 · Cosa c'è in valigia</h2>
  <p class="desc">Tocca un capo reale per metterlo o toglierlo; per i capi generici usa i tasti + e −. Il riempimento automatico copre <strong id="needLbl">6 giorni</strong> scegliendo prima i capi più leggeri per ogni uso.</p>

  <div class="actions" style="margin-top:0;margin-bottom:18px">
    <button class="primary" id="autoFill">✨ Riempi in automatico</button>
    <button class="ghost" id="emptyBag">Svuota valigia</button>
  </div>

  <div id="packBody"></div>
</section>

<section class="panel" aria-label="Piano del viaggio">
  <h2>3 · Cosa metti ogni giorno</h2>
  <p class="desc">Assegnazione giorno per giorno dei capi che hai in valigia, con la stessa rotazione della simulazione mensile (i capi da più usi si ripetono). I giorni della scorta non compaiono: sono il margine.</p>
  <div id="planAlert"></div>
  <div class="plan-days" id="planDays"></div>
</section>

@endsection

@push('scripts')
<script>
/* ---------- lettura del contenuto valigia ---------- */
/* Ogni voce impacchettabile: capo reale (unico, max 1) o tipo generico (fino a quanti ne possiedi). */
function packables(p) {
  const out = [];
  p.garments.forEach(gm => out.push({
    key: 'g:' + gm.uid, real: true, cat: p, name: gm.n, short: gm.s, color: gm.c, img: gm.img || null,
    g: gm.g, wears: gm.wears || 1, max: 1
  }));
  p.variants.forEach((v, vi) => {
    if (v.own <= 0) return;
    const pr = p.presets[v.pi];
    out.push({
      key: 'v:' + p.key + '|' + vi, real: false, cat: p, name: presetLabel(pr) + ' (generici)', short: pr.s, color: p.color, img: null,
      g: pr.g, wears: v.wears, max: v.own
    });
  });
  return out;
}
function allPackables() {
  return PARENTS.filter(p => p.active).reduce((acc, p) => acc.concat(packables(p)), []);
}
function qtyOf(key) { return BAG.items[key] || 0; }
function tripDays() { return BAG.days + BAG.spare; }

function bagGrams() {
  return allPackables().reduce((s, it) => s + qtyOf(it.key) * it.g, 0);
}
function totalKg() {
  return bagGrams() / 1000 + (+BAG.emptyKg || 0) + (+BAG.extraKg || 0);
}
const kg1 = n => n.toFixed(1).replace('.', ',');

/* ---------- riempimento automatico ---------- */
/* Copre `need` giorni con una lista di categorie trattata come un unico pool
   (serve per canotta/t-shirt/polo/camicie: un capo "sopra" al giorno).
   Sceglie prima chi pesa meno per ogni uso indossato: valigia più leggera. */
function fillPool(cats, need) {
  const cand = [];
  cats.forEach(p => packables(p).forEach(it => cand.push(it)));
  cand.sort((a, b) => (a.g / a.wears) - (b.g / b.wears));
  let covered = 0;
  cand.forEach(it => {
    while (covered < need && qtyOf(it.key) < it.max) {
      BAG.items[it.key] = qtyOf(it.key) + 1;
      covered += it.wears;
    }
  });
  return covered;
}
function autoFill() {
  const need = tripDays();
  BAG.items = {};
  const act = PARENTS.filter(p => p.active);
  act.filter(p => p.group !== 'top').forEach(p => fillPool([p], need));
  const tops = act.filter(p => p.group === 'top');
  if (tops.length) fillPool(tops, need);
}

/* ---------- piano giornaliero del viaggio ---------- */
/* Nessun bucato in viaggio: ogni capo dura i suoi usi e poi è finito. */
function tripPlan() {
  const act = PARENTS.filter(p => p.active);
  const pool = {};
  act.forEach(p => {
    pool[p.key] = [];
    packables(p).forEach(it => {
      for (let i = 0; i < qtyOf(it.key); i++) {
        pool[p.key].push({ name: it.real ? it.name : presetLabel(p.presets[p.variants[+it.key.split('|')[1]].pi]) + ' n°' + (i + 1),
                           color: it.real ? it.color : p.color, wearsLeft: it.wears, wearsMax: it.wears });
      }
    });
  });

  const solos = act.filter(p => p.group !== 'top');
  const tops = act.filter(p => p.group === 'top');
  const days = [];
  let topPtr = 0;

  for (let d = 1; d <= BAG.days; d++) {
    const worn = [];
    let miss = false;
    const take = p => {
      const avail = pool[p.key].filter(x => x.wearsLeft > 0);
      // finisce prima i capi già iniziati, così ne sporchi meno
      const pick = avail.filter(x => x.wearsLeft < x.wearsMax)[0] || avail[0];
      if (!pick) { miss = true; worn.push({ cat: p.name, name: 'manca', color: p.color, miss: true }); return; }
      pick.wearsLeft--;
      worn.push({ cat: p.name, name: pick.name, color: pick.color, miss: false, reuse: pick.wearsLeft < pick.wearsMax - 1 });
    };
    solos.forEach(take);
    if (tops.length) {
      let chosen = null;
      for (let i = 0; i < tops.length; i++) {
        const c = tops[(topPtr + i) % tops.length];
        if (pool[c.key].some(x => x.wearsLeft > 0)) { chosen = c; topPtr = (topPtr + i + 1) % tops.length; break; }
      }
      if (chosen) take(chosen); else { miss = true; worn.push({ cat: 'Sopra', name: 'manca', color: '#F27878', miss: true }); }
    }
    days.push({ d, worn, miss });
  }
  return days;
}

/* ---------- render ---------- */
function renderGauge() {
  const limit = +BAG.limitKg || 0;
  const tot = totalKg();
  const pct = limit > 0 ? tot / limit : 0;
  const over = pct > 1, near = !over && pct >= .9;
  $('gKg').textContent = kg1(tot);
  $('gKg').className = 'gauge-kg' + (over ? ' over' : near ? ' near' : '');
  $('gLimit').textContent = 'limite ' + kg1(limit) + ' kg';
  const fill = $('gFill');
  fill.style.width = Math.min(100, Math.round(pct * 100)) + '%';
  fill.className = 'gauge-fill' + (over ? ' over' : near ? ' near' : '');
  const diff = Math.abs(tot - limit);
  $('gMsg').className = 'gauge-msg ' + (over ? 'over' : near ? 'near' : 'ok');
  $('gMsg').textContent = over
    ? '⚠ Sfori di ' + kg1(diff) + ' kg: togli qualcosa o passa a un bagaglio più capiente.'
    : near
      ? '◐ Sei al limite: ti restano ' + kg1(diff) + ' kg.'
      : '✓ Sotto il limite: ti restano ' + kg1(diff) + ' kg.';
  const nCapi = Object.values(BAG.items).reduce((s, q) => s + q, 0);
  $('gBreak').textContent = nCapi + ' capi ' + kg1(bagGrams() / 1000) + ' kg · valigia vuota ' + kg1(+BAG.emptyKg || 0) + ' kg · extra ' + kg1(+BAG.extraKg || 0) + ' kg';
}

function renderPack() {
  const body = $('packBody');
  const need = tripDays();
  $('needLbl').textContent = need + (need === 1 ? ' giorno' : ' giorni');

  let html = '';
  PARENTS.filter(p => p.active).forEach(p => {
    const items = packables(p);
    if (!items.length) return;
    const isTop = p.group === 'top';
    const packedUses = items.reduce((s, it) => s + qtyOf(it.key) * it.wears, 0);
    const packedG = items.reduce((s, it) => s + qtyOf(it.key) * it.g, 0);
    const ok = isTop ? true : packedUses >= need;
    html += '<div class="pack-cat">' +
      '<div class="pack-cat-head">' +
        '<span class="cat-dot" style="background:' + p.color + '"></span>' + p.name +
        '<span class="pack-cat-sum">' + (packedG ? kg1(packedG / 1000) + ' kg · ' : '') +
          '<span style="color:' + (ok ? 'var(--green)' : 'var(--red)') + '">' + packedUses + '/' + need + ' gg</span>' +
        '</span>' +
      '</div><div class="pack-list">';

    items.forEach(it => {
      const q = qtyOf(it.key);
      const thumb = it.img
        ? '<span class="pack-thumb"><img src="' + it.img + '" alt=""></span>'
        : '<span class="pack-thumb" style="background:' + hexDim(it.color, .3) + ';color:' + it.color + '">' + it.short + '</span>';
      const meta = it.g + ' g · ' + it.wears + (it.wears === 1 ? ' uso' : ' usi') + (it.real ? '' : ' · ne hai ' + it.max);
      if (it.real) {
        html += '<button type="button" class="pack-item as-btn' + (q ? ' picked' : '') + '" data-pick="' + it.key + '">' +
          thumb +
          '<span class="pack-info"><span class="pack-name">' + it.name + '</span><span class="pack-meta">' + meta + '</span></span>' +
          '<span class="pack-check">✓</span>' +
        '</button>';
      } else {
        html += '<div class="pack-item' + (q ? ' picked' : '') + '">' +
          thumb +
          '<span class="pack-info"><span class="pack-name">' + it.name + '</span><span class="pack-meta">' + meta + '</span></span>' +
          '<span class="stepper">' +
            '<button type="button" data-qdec="' + it.key + '" aria-label="Uno in meno">−</button>' +
            '<span class="n">' + q + '</span>' +
            '<button type="button" data-qinc="' + it.key + '" aria-label="Uno in più">+</button>' +
          '</span>' +
        '</div>';
      }
    });
    html += '</div></div>';
  });

  body.innerHTML = html || '<div class="empty"><span class="big">👕</span>Non hai capi attivi: aggiungili nella pagina <strong>Categorie</strong>.</div>';
}

function renderPlan() {
  const days = tripPlan();
  const start = BAG.start ? new Date(BAG.start + 'T12:00:00') : new Date();
  const scoperti = days.filter(d => d.miss).length;
  $('planAlert').innerHTML = scoperti === 0
    ? ''
    : '<div class="alert ko">⚠ In ' + scoperti + (scoperti === 1 ? ' giorno' : ' giorni') + ' resti senza un capo pulito: quello che possiedi non basta per ' + BAG.days + ' giorni di viaggio. Aggiungi capi nelle <a href="/categorie" class="inline-link">Categorie</a> oppure metti in conto una lavatrice in viaggio.</div>';
  $('planDays').innerHTML = days.length === 0
    ? '<div class="empty">Imposta almeno un giorno di viaggio.</div>'
    : days.map(dd => {
        const dt = new Date(start); dt.setDate(dt.getDate() + dd.d - 1);
        const chips = dd.worn.map(w =>
          '<span class="plan-chip' + (w.miss ? ' miss' : '') + '">' +
            (w.miss ? '' : '<span class="cdot" style="background:' + w.color + '"></span>') +
            w.name + (w.reuse ? ' · 2° uso' : '') +
          '</span>').join('');
        return '<div class="plan-day' + (dd.miss ? ' miss' : '') + '">' +
          '<div class="plan-day-head">' +
            '<span class="plan-day-n">Giorno ' + dd.d + '</span>' +
            '<span class="plan-day-date">' + (dd.miss ? '⚠ manca un capo' : cap(fmtDay.format(dt))) + '</span>' +
          '</div>' +
          '<div class="plan-chips">' + chips + '</div>' +
        '</div>';
      }).join('');
}

function render() {
  $('bDays').textContent = BAG.days;
  $('bSpare').textContent = BAG.spare;
  renderGauge();
  renderPack();
  renderPlan();
  autosave();
}

/* ---------- controlli del viaggio ---------- */
function syncTripInputs() {
  const presets = ['7','8','10','15','20','23'];
  const cur = String(+BAG.limitKg);
  const isPreset = presets.indexOf(cur) >= 0;
  $('bLimitSel').value = isPreset ? cur : 'custom';
  $('bLimit').hidden = isPreset;
  $('bLimit').value = BAG.limitKg;
  $('bEmpty').value = BAG.emptyKg;
  $('bExtra').value = BAG.extraKg;
  if (!BAG.start) BAG.start = isoToday();
  $('bStart').value = BAG.start;
}

$('bStart').addEventListener('change', e => { BAG.start = e.target.value || isoToday(); render(); });
$('bDaysInc').addEventListener('click', () => { BAG.days = Math.min(BAG.days + 1, 60); render(); });
$('bDaysDec').addEventListener('click', () => { BAG.days = Math.max(BAG.days - 1, 1); render(); });
$('bSpareInc').addEventListener('click', () => { BAG.spare = Math.min(BAG.spare + 1, 7); render(); });
$('bSpareDec').addEventListener('click', () => { BAG.spare = Math.max(BAG.spare - 1, 0); render(); });

$('bLimitSel').addEventListener('change', e => {
  if (e.target.value === 'custom') {
    $('bLimit').hidden = false;
    $('bLimit').focus();
  } else {
    BAG.limitKg = +e.target.value;
    $('bLimit').hidden = true;
    $('bLimit').value = BAG.limitKg;
    render();
  }
});
$('bLimit').addEventListener('input', e => { BAG.limitKg = Math.max(0, +e.target.value || 0); renderGauge(); autosave(); });
$('bEmpty').addEventListener('input', e => { BAG.emptyKg = Math.max(0, +e.target.value || 0); renderGauge(); autosave(); });
$('bExtra').addEventListener('input', e => { BAG.extraKg = Math.max(0, +e.target.value || 0); renderGauge(); autosave(); });

$('autoFill').addEventListener('click', () => { autoFill(); render(); });
$('emptyBag').addEventListener('click', () => { BAG.items = {}; render(); });

/* ---------- contenuto valigia ---------- */
document.addEventListener('click', e => {
  const pick = e.target.closest('[data-pick]');
  if (pick) {
    const k = pick.getAttribute('data-pick');
    if (qtyOf(k)) delete BAG.items[k]; else BAG.items[k] = 1;
    render();
    return;
  }
  const inc = e.target.closest('[data-qinc]');
  const dec = e.target.closest('[data-qdec]');
  if (!inc && !dec) return;
  const key = (inc || dec).getAttribute(inc ? 'data-qinc' : 'data-qdec');
  const it = allPackables().find(x => x.key === key);
  if (!it) return;
  const q = qtyOf(key) + (inc ? 1 : -1);
  if (q <= 0) delete BAG.items[key]; else BAG.items[key] = Math.min(q, it.max);
  render();
});

/* ---------- init ---------- */
loadAuto().then(() => {
  syncTripInputs();
  render();
});
</script>
@endpush
