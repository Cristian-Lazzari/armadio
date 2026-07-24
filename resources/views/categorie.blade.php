@extends('layouts.app')

@section('title', 'Categorie')

@section('content')

<header class="page-head">
  <div class="eyebrow">Il contenuto dell'armadio</div>
  <h1>Categorie e <em>capi</em></h1>
  <p class="sub">Qui decidi cosa possiedi. Ogni categoria può avere <strong>capi reali</strong> (con nome, colore e foto) e <strong>capi generici</strong> raggruppati per tipo e peso. Le modifiche si salvano da sole e valgono per tutte le altre pagine.</p>
</header>

<section class="panel" aria-label="Copertura richiesta">
  <h2>Quanti giorni devi coprire</h2>
  <p class="desc">Dipende dal ciclo impostato nella pagina <a href="{{ route('home') }}" class="inline-link">Piano</a>: bucato + asciugatura + scorta.</p>
  <div class="summary" id="needSummary"></div>
</section>

<section class="panel" aria-label="Categorie e quantità">
  <h2>Le tue categorie</h2>
  <p class="desc"><strong>Canotta, t-shirt, polo e camicie</strong> sono il gruppo "sopra": indossi <em>un</em> capo sopra al giorno a rotazione, e alcuni giorni la camicia va sopra una canotta — la pill di queste categorie mostra la copertura del gruppo intero. Il bottone "Completa" aggiunge i capi mancanti.</p>
  <div class="cat-grid" id="catBody"></div>
</section>

@endsection

@push('modals')
<input type="file" id="photoInput" accept="image/*" style="display:none">
@endpush

@push('scripts')
<script>
let pendingPhoto = null;

function renderNeed() {
  const need = cycleDays();
  const scoperte = PARENTS.filter(p => p.active && (p.group === 'top' ? groupCoverage() : coverage(p)) < need).length;
  const totCapi = PARENTS.filter(p => p.active).reduce((s, p) => s + p.garments.length + p.variants.reduce((a, v) => a + v.own, 0), 0);
  const reali = PARENTS.reduce((s, p) => s + p.garments.length, 0);
  $('needSummary').innerHTML =
    '<div class="stat"><div class="label">Giorni da coprire</div><div class="value">' + need + '</div><div class="label">' + CFG.wash + ' bucato + ' + CFG.dry + ' asciugatura + ' + CFG.buf + ' scorta</div></div>' +
    '<div class="stat"><div class="label">Capi in rotazione</div><div class="value">' + totCapi + '</div><div class="label">di cui ' + reali + ' con nome</div></div>' +
    '<div class="stat"><div class="label">Categorie scoperte</div><div class="value ' + (scoperte > 0 ? 'ko' : 'ok') + '">' + scoperte + '</div></div>';
}

function renderCats() {
  const tb = $('catBody');
  tb.innerHTML = '';
  const need = cycleDays();
  PARENTS.forEach(p => {
    const cov = coverage(p);
    const isTop = p.group === 'top';
    const covShown = isTop ? groupCoverage() : cov;
    const ok = covShown >= need;
    const card = document.createElement('div');
    card.className = 'cat-card' + (p.active ? '' : ' off');
    card.style.setProperty('--cat-accent', hexDim(p.color, .18));

    let garmHtml = '';
    if (p.garments.length > 0) {
      garmHtml = '<div class="garm-list">' + p.garments.map(gm =>
        '<span class="garm">' +
          '<button class="garm-photo" data-gphoto="' + p.key + '|' + gm.uid + '" aria-label="Foto di ' + gm.n + '">' +
            (gm.img ? '<img src="' + gm.img + '" alt="">' : '📷') +
          '</button>' +
          '<input type="color" value="' + gm.c + '" data-gcolor="' + p.key + '|' + gm.uid + '" aria-label="Colore ' + gm.n + '">' +
          '<span class="garm-name">' + gm.n + '</span>' +
          '<button class="garm-del" data-gdel="' + p.key + '|' + gm.uid + '" aria-label="Rimuovi ' + gm.n + '">✕</button>' +
        '</span>').join('') + '</div>';
    }
    const addGarm = '<button class="add-btn" data-gadd="' + p.key + '">+ Capo reale con nome</button>';

    let rows = '';
    p.variants.forEach((v, vi) => {
      const k = p.key + '|' + vi;
      const typeSel =
        '<select data-vpi="' + k + '" aria-label="Tipo ' + p.name + '">' +
          p.presets.map((pr, i) => '<option value="' + i + '"' + (i === v.pi ? ' selected' : '') + '>' + pr.l + '</option>').join('') +
        '</select>';
      const wearsSel = p.wearsLocked
        ? ''
        : '<select data-vwears="' + k + '" class="wears-sel" aria-label="Usi per capo">' +
            [1,2,3].map(n => '<option value="' + n + '"' + (n === v.wears ? ' selected' : '') + '>' + n + (n === 1 ? ' uso' : ' usi') + '</option>').join('') +
          '</select>';
      const delBtn = (p.multi && p.variants.length > 1)
        ? '<button class="del-btn" data-vdel="' + k + '" aria-label="Rimuovi tipo">✕</button>'
        : '';
      rows +=
        '<div class="var-row">' +
          '<div class="var-main">' + typeSel + wearsSel + '</div>' +
          '<div class="var-side">' +
            '<div class="stepper">' +
              '<button data-vdec="' + k + '" aria-label="Meno">−</button>' +
              '<span class="n">' + v.own + '</span>' +
              '<button data-vinc="' + k + '" aria-label="Più">+</button>' +
            '</div>' + delBtn +
          '</div>' +
        '</div>';
    });
    if (p.garments.length > 0) {
      rows = '<div class="anon-label">Capi generici (senza nome), in aggiunta a quelli reali:</div>' + rows;
    }

    const addBtn = p.multi
      ? '<button class="add-btn" data-vadd="' + p.key + '">+ Aggiungi un tipo generico</button>'
      : '';
    const autoBtn = (p.active && !ok)
      ? '<button class="sug-btn" data-auto="' + p.key + '">Completa: +' + Math.ceil((need - covShown) / p.variants[0].wears) + ' del primo tipo</button>'
      : '';
    const toggle = p.canOff
      ? '<label class="switch" title="Includi nel periodo"><input type="checkbox" data-toggle="' + p.key + '"' + (p.active ? ' checked' : '') + ' aria-label="Includi ' + p.name + ' nel periodo"><span class="track"></span><span class="thumb"></span></label>'
      : '';
    const covLabel = (isTop ? 'sopra: ' : '') + 'copre ' + covShown + '/' + need + ' gg';
    const pill = !p.active
      ? '<span class="pill" style="background:var(--surface);color:var(--faint)">esclusa dal periodo</span>'
      : (ok ? '<span class="pill pill-ok">' + covLabel + ' ✓</span>'
            : '<span class="pill pill-ko">' + covLabel + '</span>');

    card.innerHTML =
      '<div class="cat-head">' +
        '<span class="cat-title"><span class="cat-dot" style="background:' + p.color + '"></span>' + p.name + '</span>' +
        '<span style="display:flex;align-items:center;gap:10px">' + pill + toggle + '</span>' +
      '</div>' +
      garmHtml + addGarm + rows + addBtn + autoBtn;
    tb.appendChild(card);
  });
}

function render() {
  renderNeed();
  renderCats();
  autosave();
}

/* ---------- eventi ---------- */
function findVar(k) {
  const parts = k.split('|');
  const p = findParent(parts[0]);
  return { p, v: p.variants[+parts[1]], vi: +parts[1] };
}

document.addEventListener('click', e => {
  const gp = e.target.closest('[data-gphoto]');
  if (gp) {
    pendingPhoto = gp.getAttribute('data-gphoto');
    $('photoInput').click();
    return;
  }
  const t = e.target.closest('[data-vinc],[data-vdec],[data-vadd],[data-vdel],[data-auto],[data-gadd],[data-gdel]');
  if (!t) return;
  const vinc = t.getAttribute('data-vinc');
  const vdec = t.getAttribute('data-vdec');
  const vadd = t.getAttribute('data-vadd');
  const vdel = t.getAttribute('data-vdel');
  const auto = t.getAttribute('data-auto');
  const gadd = t.getAttribute('data-gadd');
  const gdel = t.getAttribute('data-gdel');
  if (vinc) { const r = findVar(vinc); r.v.own = Math.min(r.v.own + 1, 40); render(); }
  if (vdec) { const r = findVar(vdec); r.v.own = Math.max(r.v.own - 1, 0); render(); }
  if (vadd) {
    const p = findParent(vadd);
    const usedPi = p.variants.map(v => v.pi);
    let freePi = -1;
    p.presets.forEach((_, i) => { if (freePi < 0 && usedPi.indexOf(i) < 0) freePi = i; });
    p.variants.push({ pi: freePi >= 0 ? freePi : 0, own: 0, wears: p.wearsLocked ? 1 : 2 });
    render();
  }
  if (vdel) { const r = findVar(vdel); r.p.variants.splice(r.vi, 1); overrides = {}; render(); }
  if (auto) {
    const p = findParent(auto);
    const need = cycleDays();
    const cov = p.group === 'top' ? groupCoverage() : coverage(p);
    if (cov < need) p.variants[0].own += Math.ceil((need - cov) / p.variants[0].wears);
    render();
  }
  if (gadd) {
    const p = findParent(gadd);
    uiPrompt('Nome del capo (es. "Jeans neri", "Polo bianca"):').then(name => {
      if (!name) return;
      const mid = p.presets[Math.floor(p.presets.length / 2)];
      p.garments.push({ uid: newUid(), n: name, s: shortName(name), c: '#8B90A8', g: mid.g, wears: p.wearsLocked ? 1 : (p.multi ? 2 : 1) });
      render();
    });
  }
  if (gdel) {
    const parts = gdel.split('|');
    const p = findParent(parts[0]);
    const gm = p.garments.find(x => x.uid === parts[1]);
    uiConfirm('Rimuovere "' + (gm ? gm.n : 'questo capo') + '" dall\'armadio?').then(ok => {
      if (!ok) return;
      p.garments = p.garments.filter(x => x.uid !== parts[1]);
      delete BAG.items['g:' + parts[1]];
      overrides = {};
      render();
    });
  }
});

document.addEventListener('change', e => {
  const vpi = e.target.getAttribute('data-vpi');
  const vwears = e.target.getAttribute('data-vwears');
  const tog = e.target.getAttribute('data-toggle');
  const gcol = e.target.getAttribute('data-gcolor');
  if (vpi) { const r = findVar(vpi); r.v.pi = +e.target.value; render(); }
  if (vwears) { const r = findVar(vwears); r.v.wears = +e.target.value; render(); }
  if (tog) { const p = findParent(tog); p.active = e.target.checked; render(); }
  if (gcol) {
    const parts = gcol.split('|');
    const p = findParent(parts[0]);
    const gm = p.garments.find(x => x.uid === parts[1]);
    if (gm) { gm.c = e.target.value; render(); }
  }
});

$('photoInput').addEventListener('change', e => {
  const file = e.target.files && e.target.files[0];
  e.target.value = '';
  if (!file || !pendingPhoto) return;
  const parts = pendingPhoto.split('|');
  pendingPhoto = null;
  const p = findParent(parts[0]);
  const gm = p && p.garments.find(x => x.uid === parts[1]);
  if (!gm) return;
  uploadPhoto(file)
    .then(url => { gm.img = url; render(); })
    .catch(() => uiAlert('Upload foto non riuscito.'));
});

/* ---------- init ---------- */
render();
loadAuto().then(() => render());
</script>
@endpush
