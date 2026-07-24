/* Nucleo condiviso da tutte le pagine: stato dell'armadio, persistenza sulle API
   Laravel, motore di simulazione e componenti UI comuni (dialoghi, outfit, foto).
   I parametri del ciclo vivono in CFG e non più nel DOM: così le pagine che non
   hanno gli slider (categorie, storico, bagaglio) possono comunque simulare. */

const PARENTS = DEFAULT_PARENTS;
const CFG = { wash: 4, dry: 4, buf: 2, cap: 7, start: '' };
let overrides = {};                    // { indiceGiorno|chiaveCategoria : 'g:uid' | 'v:vid' }
const BAG_DEFAULT = { days: 5, spare: 1, limitKg: 10, emptyKg: 2.5, extraKg: 1.5, start: '', items: {} };
let BAG = Object.assign({}, BAG_DEFAULT, { items: {} });

const DAYS = 30;        // orizzonte della simulazione in home
const MAX_SIM = 400;    // tetto di sicurezza per lo storico
let uidSeq = 100;

const $ = id => document.getElementById(id);

/* ---------- date ---------- */
function isoDate(d) {
  return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}
function isoToday() { return isoDate(new Date()); }
function startDate() {
  return CFG.start ? new Date(CFG.start + 'T12:00:00') : new Date();
}
function dateForDay(i) {
  const d = new Date(startDate());
  d.setDate(d.getDate() + i);
  return d;
}
/* Giorni trascorsi dall'inizio del piano a oggi (0 se il piano inizia oggi o dopo). */
function daysElapsed() {
  const s = startDate(); s.setHours(12, 0, 0, 0);
  const t = new Date(); t.setHours(12, 0, 0, 0);
  return Math.max(0, Math.round((t - s) / 86400000));
}
const fmtDay = new Intl.DateTimeFormat('it-IT', { weekday: 'short', day: 'numeric', month: 'short' });
const fmtWeekday = new Intl.DateTimeFormat('it-IT', { weekday: 'long' });
const fmtDate = new Intl.DateTimeFormat('it-IT', { day: 'numeric', month: 'long' });
const fmtMonth = new Intl.DateTimeFormat('it-IT', { month: 'long', year: 'numeric' });
const cap = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : s;

/* ---------- calcoli sull'armadio ---------- */
function cycleDays() { return +CFG.wash + +CFG.dry + +CFG.buf; }
function coverage(p) {
  return p.variants.reduce((s, v) => s + v.own * v.wears, 0) +
         p.garments.reduce((s, gm) => s + (gm.wears || 1), 0);
}
/* Copertura combinata del gruppo "sopra" (canotta + t-shirt + polo + camicie):
   si indossa UN capo sopra al giorno, quindi conta il totale del gruppo. */
function groupCoverage() {
  return PARENTS.filter(p => p.active && p.group === 'top').reduce((s, p) => s + coverage(p), 0);
}
function hexDim(hex, a) {
  const r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
  return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
}
function textOn(hex) {
  const r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
  return (r*.299 + g*.587 + b*.114) > 140 ? '#15171f' : '#E6E8F2';
}
function shortName(n) {
  const words = n.trim().split(/\s+/);
  let s = words[0].slice(0, 4);
  if (words[1]) s += words[1][0].toUpperCase();
  return s;
}
function newUid() { return 'u' + (uidSeq++) + Date.now().toString(36); }
function findParent(key) { return PARENTS.find(p => p.key === key); }
/* Etichetta leggibile di un tipo generico: "Jeans" al posto di "Jeans · 700 g". */
function presetLabel(pr) { return pr.l.split(' ·')[0]; }

/* ---------- persistenza (API Laravel) ---------- */
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
function api(url, method, body) {
  return fetch(url, {
    method: method || 'GET',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: body ? JSON.stringify(body) : undefined
  }).then(r => { if (!r.ok) throw new Error('API ' + r.status); return r.json(); });
}

function serialize() {
  return {
    v: 6,
    wash: CFG.wash, dry: CFG.dry, buf: CFG.buf, cap: CFG.cap,
    start: CFG.start,
    parents: PARENTS.map(p => ({
      key: p.key, active: p.active,
      variants: p.variants.map(v => ({ pi: v.pi, own: v.own, wears: v.wears })),
      garments: p.garments.map(gm => ({ uid: gm.uid, n: gm.n, s: gm.s, c: gm.c, g: gm.g, wears: gm.wears, img: gm.img || null }))
    })),
    overrides: overrides,
    bag: BAG
  };
}

/* Converte uno stato salvato col vecchio modello (categoria unica 'mag',
   canotte come strato quotidiano) nel nuovo: t-shirt / polo / camicie separate. */
function migrateParents(list) {
  const out = [];
  const routeKey = gm => /camic/i.test(gm.n) ? 'cam' : /polo/i.test(gm.n) ? 'pol' : 'tee';
  list.forEach(sp => {
    if (sp.key === 'mag') {
      const buckets = { tee: [], pol: [], cam: [] };
      (sp.garments || []).forEach(gm => buckets[routeKey(gm)].push(gm));
      ['tee', 'pol', 'cam'].forEach(k => out.push({
        key: k, active: sp.active !== false,
        variants: [{ pi: 1, own: 0, wears: 1 }], garments: buckets[k]
      }));
    } else if (sp.key === 'can') {
      // azzera le canotte generiche del vecchio modello (~11, indossate ogni giorno); tiene i capi reali
      out.push({
        key: 'can', active: sp.active !== false,
        variants: [{ pi: (sp.variants && sp.variants[0]) ? sp.variants[0].pi : 1, own: 0, wears: 1 }],
        garments: sp.garments || []
      });
    } else {
      out.push(sp);
    }
  });
  return out;
}

function applyState(s) {
  if (!s) return;
  if (s.wash) CFG.wash = +s.wash;
  if (s.dry) CFG.dry = +s.dry;
  if (s.buf !== undefined) CFG.buf = +s.buf;
  if (s.cap) CFG.cap = +s.cap;
  if (s.start) CFG.start = s.start;
  if (s.parents) {
    let plist = s.parents;
    if ((s.v || 0) < 5 || plist.some(sp => sp.key === 'mag')) plist = migrateParents(plist);
    plist.forEach(sp => {
      const p = findParent(sp.key);
      if (!p) return;
      p.active = sp.active !== false;
      if (sp.variants && sp.variants.length) {
        p.variants = sp.variants.map(v => ({ pi: Math.min(v.pi, p.presets.length - 1), own: v.own, wears: v.wears }));
      }
      if (sp.garments) p.garments = sp.garments;
    });
  }
  overrides = s.overrides || {};
  BAG = Object.assign({}, BAG_DEFAULT, { items: {} }, s.bag || {});
  if (!BAG.items) BAG.items = {};
}

let saveTimer = null;
function autosave() {
  clearTimeout(saveTimer);
  saveTimer = setTimeout(() => {
    api('/api/plans/auto', 'PUT', { state: serialize() }).catch(() => {});
  }, 700);
}
/* Carica lo stato autosalvato; se non c'è, inizializza la data di inizio a oggi. */
function loadAuto() {
  return api('/api/plans/auto')
    .then(s => { if (s) applyState(s); return s; })
    .catch(() => null)
    .then(s => { if (!CFG.start) CFG.start = isoToday(); return s; });
}

/* ---------- simulazione del ciclo del bucato ---------- */
function simulate(nDays) {
  const total = Math.min(nDays || DAYS, MAX_SIM);
  const w = +CFG.wash, d = +CFG.dry;
  const state = {};
  const act = PARENTS.filter(p => p.active);
  act.forEach(p => {
    const items = [];
    p.garments.forEach(gm => {
      items.push({ named: true, uid: gm.uid, vid: -1, label: gm.s, dotColor: gm.c, wearsMax: gm.wears || 1, wearsLeft: gm.wears || 1, g: gm.g, dirty: false, dryLeft: 0 });
    });
    p.variants.forEach((v, vi) => {
      for (let i = 0; i < v.own; i++) {
        items.push({ named: false, uid: null, vid: vi, label: p.presets[v.pi].s + (i + 1), dotColor: null, wearsMax: v.wears, wearsLeft: v.wears, g: p.presets[v.pi].g, dirty: false, dryLeft: 0 });
      }
    });
    state[p.key] = items;
  });

  const days = [];
  const batches = [];
  let shortagesTotal = 0;
  const soloCats = act.filter(p => p.group !== 'top');   // indossati ogni giorno
  const groupCats = act.filter(p => p.group === 'top');  // gruppo "sopra": uno al giorno + eventuale strato
  const canActive = groupCats.some(p => p.key === 'can');
  let topPtr = 0;        // rotazione del capo "sopra" tra i tipi disponibili
  let camLayerTurn = 0;  // alterna camicia-da-sola / camicia-sopra-canotta

  for (let day = 1; day <= total; day++) {
    let readyCount = 0;
    act.forEach(p => state[p.key].forEach(it => {
      if (it.dryLeft > 0) {
        it.dryLeft--;
        if (it.dryLeft === 0) { it.dirty = false; it.wearsLeft = it.wearsMax; readyCount++; }
      }
    }));

    const isWash = day % w === 0;
    const worn = [];
    let shortage = false;
    const first = list => list[0] || null;
    const partial = list => list.filter(it => it.wearsLeft < it.wearsMax);
    const availOf = k => state[k].filter(it => !it.dirty && it.dryLeft === 0);

    const pickCat = p => {
      const ov = overrides[(day - 1) + '|' + p.key];
      const avail = availOf(p.key);
      let pick = null;
      if (ov) {
        if (ov.slice(0,2) === 'g:') {
          pick = first(avail.filter(it => it.named && it.uid === ov.slice(2)));
        } else if (ov.slice(0,2) === 'v:') {
          const vid = +ov.slice(2);
          const cand = avail.filter(it => !it.named && it.vid === vid);
          pick = first(partial(cand)) || first(cand);
        }
      }
      if (!pick) pick = first(partial(avail)) || first(avail);
      return pick;
    };
    const wearIt = (p, pick, extra) => {
      pick.wearsLeft--;
      const reuse = pick.wearsMax > 1 && pick.wearsLeft < pick.wearsMax - 1;
      const nChoices = p.garments.length + p.variants.filter(v => v.own > 0).length;
      worn.push(Object.assign({ key: p.key, color: p.color, label: pick.label, dotColor: pick.dotColor, uid: pick.uid, vid: pick.vid, reuse, miss: false, swappable: nChoices > 1, layer: false }, extra || {}));
      if (pick.wearsLeft === 0) pick.dirty = true;
    };
    const missCat = p => {
      worn.push({ key: p.key, color: p.color, label: p.garments.length ? p.garments[0].s : p.presets[p.variants[0].pi].s, dotColor: null, uid: null, vid: null, reuse: false, miss: true, swappable: false, layer: false });
      shortage = true;
      shortagesTotal++;
    };

    // capi indossati ogni giorno (mutande, calzini, pantaloni, felpe)
    soloCats.forEach(p => { const pick = pickCat(p); if (pick) wearIt(p, pick); else missCat(p); });

    // gruppo "sopra": UN capo primario a rotazione + eventuale canotta sotto la camicia
    if (groupCats.length) {
      let primary = null;
      for (let i = 0; i < groupCats.length; i++) {
        const cat = groupCats[(topPtr + i) % groupCats.length];
        if (availOf(cat.key).length) { primary = cat; topPtr = (topPtr + i + 1) % groupCats.length; break; }
      }
      if (!primary) {
        missCat(groupCats[topPtr % groupCats.length]);
      } else {
        wearIt(primary, pickCat(primary));
        if (primary.key === 'cam' && canActive) {
          const canAvail = availOf('can');
          if (camLayerTurn % 2 === 0 && canAvail.length) {
            const canCat = groupCats.find(c => c.key === 'can');
            wearIt(canCat, first(partial(canAvail)) || first(canAvail), { layer: true });
          }
          camLayerTurn++;
        }
      }
    }

    let washedCount = 0, washedG = 0;
    const washedByCat = [];
    if (isWash) {
      act.forEach(p => {
        let n = 0;
        state[p.key].forEach(it => {
          if (it.dirty && it.dryLeft === 0) { it.dryLeft = d; washedCount++; n++; washedG += it.g; }
        });
        if (n > 0) washedByCat.push({ name: p.name.toLowerCase(), n });
      });
      if (washedCount > 0) batches.push({ start: day, ready: day + d, count: washedCount, kg: washedG / 1000 });
    }

    const dryingBatches = batches
      .filter(b => day >= b.start && day < b.ready)
      .map(b => ({ count: b.count, dayN: day - b.start + 1, total: d, ready: b.ready }));

    days.push({ day, isWash, washedCount, washedKg: washedG / 1000, washedByCat, worn, shortage, dryingBatches, readyCount });
  }

  return { days, shortagesTotal, batches };
}

/* ---------- outfit ---------- */
const SLOT_ORDER = [
  { key: 'fel', cat: 'Felpa' },
  { key: 'cam', cat: 'Camicia' },
  { key: 'pol', cat: 'Polo' },
  { key: 'tee', cat: 'T-shirt' },
  { key: 'can', cat: 'Canotta' },
  { key: 'pan', cat: 'Pantaloni' },
  { key: 'mut', cat: 'Mutande' },
  { key: 'cal', cat: 'Calzini' }
];

/* Miniature dell'outfit di un giorno. Con swap attivo ogni capo è un bottone
   che ruota sulle alternative disponibili; nello storico è sola lettura. */
function ofitSlots(dd, di, opts) {
  const allowSwap = !opts || opts.swap !== false;
  let html = '';
  SLOT_ORDER.forEach(so => {
    const p = findParent(so.key);
    const wn = dd.worn.find(x => x.key === so.key);
    if (!p || !p.active || !wn) return;
    let name = wn.label, imgHtml = '';
    const gm = wn.uid ? p.garments.find(g => g.uid === wn.uid) : null;
    if (gm) {
      name = gm.n;
      imgHtml = gm.img
        ? '<img src="' + gm.img + '" alt="">'
        : '<div class="ofit-ph" style="background:' + gm.c + ';color:' + textOn(gm.c) + '">' + gm.s + '</div>';
    } else if (wn.miss) {
      name = 'Manca!';
      imgHtml = '<div class="ofit-ph" style="background:var(--red-dim);color:var(--red)">✕</div>';
    } else {
      const v = p.variants[wn.vid];
      name = v ? presetLabel(p.presets[v.pi]) : wn.label;
      imgHtml = '<div class="ofit-ph" style="background:' + hexDim(p.color, .25) + ';color:' + p.color + '">' + wn.label + '</div>';
    }
    const swappable = allowSwap && wn.swappable;
    const catLbl = so.cat + (wn.layer ? ' · sotto' : (wn.reuse ? ' · 2° uso' : ''));
    const tag = swappable ? 'button' : 'div';
    const attrs = swappable ? ' type="button" data-swap="' + di + '|' + so.key + '" title="Tocca per cambiare capo"' : '';
    const badge = swappable ? '<span class="ofit-swap">⇄</span>' : '';
    html +=
      '<' + tag + ' class="ofit' + (swappable ? ' swap' : '') + '"' + attrs + '>' +
        badge +
        '<div class="ofit-img">' + imgHtml + '</div>' +
        '<span class="ofit-cat">' + catLbl + '</span>' +
        '<span class="ofit-name' + (wn.miss ? ' miss' : '') + '">' + name + '</span>' +
      '</' + tag + '>';
  });
  return html;
}

/* Ruota il capo assegnato a un giorno tra le alternative della categoria. */
function cycleOverride(k) {
  const parts = k.split('|');
  const di = +parts[0], key = parts[1];
  const p = findParent(key);
  const choices = p.garments.map(gm => 'g:' + gm.uid)
    .concat(p.variants.map((v, i) => v.own > 0 ? 'v:' + i : null).filter(x => x));
  if (choices.length < 2) return false;
  const cur = overrides[di + '|' + key];
  const pos = choices.indexOf(cur);
  overrides[di + '|' + key] = choices[(pos + 1) % choices.length];
  return true;
}

/* ---------- foto dei capi ---------- */
/* Ridimensiona lato client a 360 px e carica su storage/app/public/garments. */
function uploadPhoto(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => {
      const img = new Image();
      img.onload = () => {
        const MAX = 360;
        const scale = Math.min(1, MAX / Math.max(img.width, img.height));
        const cv = document.createElement('canvas');
        cv.width = Math.round(img.width * scale);
        cv.height = Math.round(img.height * scale);
        cv.getContext('2d').drawImage(img, 0, 0, cv.width, cv.height);
        cv.toBlob(blob => {
          const fd = new FormData();
          fd.append('photo', blob, 'capo.jpg');
          fetch('/api/garment-photo', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }, body: fd })
            .then(r => r.json())
            .then(res => resolve(res.url))
            .catch(reject);
        }, 'image/jpeg', 0.8);
      };
      img.onerror = reject;
      img.src = reader.result;
    };
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

/* ---------- dialoghi (sostituiscono alert/confirm/prompt nativi) ---------- */
const dlg = { bg: $('dlgBg'), title: $('dlgTitle'), msg: $('dlgMsg'), input: $('dlgInput'),
  ok: $('dlgOk'), cancel: $('dlgCancel'), x: $('dlgX'), resolve: null, mode: 'alert' };

function openDialog(o) {
  dlg.mode = o.mode;
  dlg.title.textContent = o.title || (o.mode === 'confirm' ? 'Conferma' : o.mode === 'prompt' ? 'Nuovo nome' : 'Avviso');
  dlg.msg.textContent = o.message || '';
  dlg.msg.hidden = !o.message;
  const isPrompt = o.mode === 'prompt';
  dlg.input.hidden = !isPrompt;
  dlg.input.value = isPrompt ? (o.defaultValue || '') : '';
  dlg.cancel.hidden = o.mode === 'alert';
  dlg.cancel.textContent = o.cancelLabel || 'Annulla';
  dlg.ok.textContent = o.okLabel || (o.mode === 'confirm' ? 'Elimina' : isPrompt ? 'Salva' : 'OK');
  dlg.bg.hidden = false;
  document.body.style.overflow = 'hidden';
  setTimeout(() => { if (isPrompt) { dlg.input.focus(); dlg.input.select(); } else dlg.ok.focus(); }, 40);
  return new Promise(res => { dlg.resolve = res; });
}
function settleDialog(value) {
  if (!dlg.resolve) return;
  const r = dlg.resolve; dlg.resolve = null;
  dlg.bg.hidden = true;
  if (!document.querySelector('.modal-bg:not([hidden])')) document.body.style.overflow = '';
  r(value);
}
/* valore di dismiss (X / sfondo / Esc) coerente con i nativi */
function dismissDialog() { settleDialog(dlg.mode === 'prompt' ? null : dlg.mode === 'confirm' ? false : true); }

dlg.ok.addEventListener('click', () => settleDialog(dlg.mode === 'prompt' ? dlg.input.value : true));
dlg.cancel.addEventListener('click', () => settleDialog(dlg.mode === 'prompt' ? null : false));
dlg.x.addEventListener('click', dismissDialog);
dlg.bg.addEventListener('click', e => { if (e.target === dlg.bg) dismissDialog(); });
dlg.input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); settleDialog(dlg.input.value); } });
document.addEventListener('keydown', e => { if (e.key === 'Escape' && !dlg.bg.hidden) dismissDialog(); });

function uiAlert(message, title) { return openDialog({ mode: 'alert', title, message }); }
function uiConfirm(message, title) { return openDialog({ mode: 'confirm', title, message }); }
function uiPrompt(message, defaultValue, title) { return openDialog({ mode: 'prompt', title, message, defaultValue }); }

/* ---------- programmazioni salvate ---------- */
let PROFS = [];
function loadProfiles(sel, selectedId) {
  return api('/api/plans').then(list => {
    PROFS = list;
    if (sel) {
      sel.innerHTML = '<option value="">— Programmazioni salvate —</option>' +
        list.map(p => '<option value="' + p.id + '"' + (p.id == selectedId ? ' selected' : '') + '>' + p.name + '</option>').join('');
    }
    return list;
  }).catch(() => []);
}
