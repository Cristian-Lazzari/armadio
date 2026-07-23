<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Armadio · Future Plus</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #0B0D17;
  --surface: #131628;
  --surface-2: #1A1E36;
  --border: #262B4A;
  --indigo: #6366F1;
  --indigo-soft: #818CF8;
  --indigo-dim: rgba(99,102,241,.14);
  --amber: #F0B252;
  --amber-dim: rgba(240,178,82,.14);
  --red: #F27878;
  --red-dim: rgba(242,120,120,.14);
  --green: #6EE7A7;
  --green-dim: rgba(110,231,167,.12);
  --text: #E6E8F2;
  --muted: #8B90A8;
  --faint: #565B75;
  --radius: 14px;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
  background: var(--bg);
  color: var(--text);
  font-family: 'Inter', system-ui, sans-serif;
  line-height: 1.6;
  min-height: 100vh;
}
.wrap { max-width: 900px; margin: 0 auto; padding: 0 20px 80px; }

header { padding: 52px 0 36px; }
.eyebrow {
  font-size: 12px; letter-spacing: .14em; text-transform: uppercase;
  color: var(--indigo-soft); font-weight: 600; margin-bottom: 14px;
}
h1 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: clamp(28px, 5vw, 42px);
  font-weight: 700; line-height: 1.15; letter-spacing: -.02em;
}
h1 em { font-style: normal; color: var(--indigo-soft); }
.sub { color: var(--muted); margin-top: 12px; max-width: 60ch; font-size: 15px; }

.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 26px 24px;
  margin-bottom: 20px;
}
.panel h2 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 17px; font-weight: 500; margin-bottom: 6px;
}
.panel .desc { font-size: 13px; color: var(--faint); margin-bottom: 20px; }

.ctrl { display: grid; grid-template-columns: 1fr auto; gap: 6px 16px; align-items: center; margin-bottom: 20px; }
.ctrl label { font-size: 14px; color: var(--muted); }
.ctrl .val {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 20px; font-weight: 700; color: var(--indigo-soft);
  min-width: 34px; text-align: right;
}
.ctrl input[type=range] { grid-column: 1 / -1; width: 100%; }

input[type=range] {
  -webkit-appearance: none; appearance: none;
  height: 6px; border-radius: 3px;
  background: var(--surface-2);
  outline: none; cursor: pointer;
}
input[type=range]::-webkit-slider-thumb {
  -webkit-appearance: none; appearance: none;
  width: 20px; height: 20px; border-radius: 50%;
  background: var(--indigo); border: 3px solid var(--bg);
  box-shadow: 0 0 0 1px var(--indigo);
}
input[type=range]::-moz-range-thumb {
  width: 20px; height: 20px; border-radius: 50%;
  background: var(--indigo); border: 3px solid var(--bg);
  box-shadow: 0 0 0 1px var(--indigo); cursor: pointer;
}
input[type=range]:focus-visible { box-shadow: 0 0 0 2px var(--indigo-soft); }

.cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 14px; }
.cat-card {
  position: relative; overflow: hidden;
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 20px; padding: 18px 16px 15px;
  display: flex; flex-direction: column; gap: 13px;
  box-shadow: 0 12px 30px rgba(0,0,0,.26);
}
.cat-card::before {
  content: ''; position: absolute; inset: 0 0 auto 0; height: 96px; pointer-events: none;
  background: radial-gradient(120% 100% at 50% 0, var(--cat-accent, var(--indigo-dim)), transparent 72%);
}
.cat-card > * { position: relative; }
.cat-card select, .cat-card .stepper { background: var(--surface-2); }
.cat-head { display: flex; justify-content: space-between; align-items: center; gap: 8px 10px; flex-wrap: wrap; }
.cat-title { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 16px; display: inline-flex; align-items: center; gap: 9px; }
.cat-dot { width: 11px; height: 11px; border-radius: 4px; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(255,255,255,.05); }
.pill { font-size: 11px; font-weight: 700; padding: 5px 10px; border-radius: 999px; white-space: nowrap; }
.pill-ok { background: var(--green-dim); color: var(--green); }
.pill-ko { background: var(--red-dim); color: var(--red); }
.cat-row { display: flex; gap: 10px; }
.cat-row-bottom { justify-content: space-between; align-items: center; flex-wrap: wrap; }
.field { display: flex; flex-direction: column; gap: 4px; flex: 1; min-width: 0; }
.field-label { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; color: var(--faint); font-weight: 600; }
.field-static { font-size: 14px; color: var(--muted); padding: 11px 0; }
select {
  background: var(--surface); color: var(--text);
  border: 1px solid var(--border); border-radius: 10px;
  font-family: 'Inter', sans-serif; font-size: 16px;
  padding: 10px 12px; cursor: pointer;
  width: 100%; min-height: 44px;
  -webkit-appearance: none; appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%238B90A8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 12px center;
}
select:focus-visible, .stepper button:focus-visible, button:focus-visible { outline: 2px solid var(--indigo-soft); outline-offset: 2px; }
.stepper { display: inline-flex; align-items: center; gap: 4px; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 3px; }
.stepper button {
  width: 44px; height: 44px; border: none; border-radius: 9px;
  background: transparent; color: var(--text);
  font-size: 22px; font-weight: 600; line-height: 1; cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}
.stepper button:active { background: var(--indigo-dim); color: var(--indigo-soft); }
.stepper .n {
  font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 20px;
  min-width: 34px; text-align: center;
}
.sug-btn {
  border: 1px dashed var(--indigo); background: transparent;
  color: var(--indigo-soft); font-family: 'Inter', sans-serif;
  font-size: 13px; font-weight: 500;
  padding: 10px 14px; border-radius: 10px; cursor: pointer;
  min-height: 44px; -webkit-tap-highlight-color: transparent;
}
.sug-btn b { font-family: 'Space Grotesk', sans-serif; font-size: 15px; }
.sug-btn:active { background: var(--indigo-dim); }
.var-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.var-main { display: flex; gap: 8px; flex: 1; min-width: 180px; }
.var-main select { flex: 2; min-width: 0; }
.var-main .wears-sel { flex: 1; min-width: 84px; }
.var-side { display: flex; gap: 8px; align-items: center; }
.del-btn {
  width: 40px; height: 40px; border-radius: 10px;
  border: 1px solid var(--border); background: transparent;
  color: var(--faint); font-size: 14px; cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}
.del-btn:active { border-color: var(--red); color: var(--red); background: var(--red-dim); }
.add-btn {
  border: 1px dashed var(--border); background: transparent;
  color: var(--muted); font-family: 'Inter', sans-serif;
  font-size: 13px; font-weight: 500;
  padding: 10px 14px; border-radius: 10px; cursor: pointer;
  min-height: 44px; -webkit-tap-highlight-color: transparent;
}
.add-btn:active { border-color: var(--indigo); color: var(--indigo-soft); background: var(--indigo-dim); }
.profiles { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
.profiles select { flex: 1; min-width: 150px; }
.mini-btn {
  border: 1px solid var(--indigo); background: transparent; color: var(--indigo-soft);
  font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 600;
  padding: 0 16px; border-radius: 10px; cursor: pointer; min-height: 44px;
  -webkit-tap-highlight-color: transparent;
}
.mini-btn:active { background: var(--indigo-dim); }
.mini-btn.danger { border-color: var(--border); color: var(--faint); }
.mini-btn.danger:active { border-color: var(--red); color: var(--red); background: var(--red-dim); }
.date-input {
  background: var(--surface-2); color: var(--text);
  border: 1px solid var(--border); border-radius: 10px;
  font-family: 'Inter', sans-serif; font-size: 16px;
  padding: 10px 12px; min-height: 44px;
  color-scheme: dark;
}
.switch { position: relative; width: 46px; height: 26px; flex-shrink: 0; }
.switch input { opacity: 0; width: 100%; height: 100%; position: absolute; margin: 0; cursor: pointer; z-index: 1; }
.switch .track {
  position: absolute; inset: 0; border-radius: 999px;
  background: var(--surface); border: 1px solid var(--border);
  transition: background .2s, border-color .2s;
}
.switch .thumb {
  position: absolute; top: 3px; left: 3px; width: 20px; height: 20px;
  border-radius: 50%; background: var(--faint); transition: transform .2s, background .2s;
}
.switch input:checked ~ .track { background: var(--indigo-dim); border-color: var(--indigo); }
.switch input:checked ~ .thumb { transform: translateX(20px); background: var(--indigo-soft); }
.cat-card.off { opacity: .55; }
.cat-card.off .var-row, .cat-card.off .add-btn, .cat-card.off .sug-btn { display: none; }
.day-date { font-size: 13px; color: var(--muted); text-transform: capitalize; }
.chip[data-swap] { cursor: pointer; -webkit-tap-highlight-color: transparent; }
.saved-note { font-size: 11px; color: var(--faint); margin-top: 8px; }
.garm-list { display: flex; flex-direction: column; gap: 8px; }
.garm {
  display: flex; align-items: center; gap: 10px;
  background: var(--surface-2); border: 1px solid var(--border);
  border-radius: 12px; padding: 8px 10px;
}
.garm input[type=color] {
  width: 30px; height: 30px; border: none; border-radius: 8px;
  padding: 0; background: none; cursor: pointer; flex-shrink: 0;
}
.garm input[type=color]::-webkit-color-swatch-wrapper { padding: 2px; }
.garm input[type=color]::-webkit-color-swatch { border: 1px solid var(--border); border-radius: 6px; }
.garm-name { flex: 1; font-size: 14px; font-weight: 500; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.garm-del {
  width: 34px; height: 34px; border-radius: 8px; flex-shrink: 0;
  border: 1px solid transparent; background: transparent;
  color: var(--faint); font-size: 13px; cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}
.garm-del:active { color: var(--red); background: var(--red-dim); }
.anon-label { font-size: 11px; color: var(--faint); margin-top: 4px; }
.cdot {
  display: inline-block; width: 9px; height: 9px; border-radius: 50%;
  margin-right: 5px; vertical-align: baseline;
  border: 1px solid rgba(255,255,255,.25);
}
.garm-photo {
  width: 38px; height: 38px; border-radius: 8px; flex-shrink: 0;
  border: 1px dashed var(--border); background: var(--surface);
  color: var(--faint); font-size: 15px; cursor: pointer; padding: 0; overflow: hidden;
  -webkit-tap-highlight-color: transparent;
}
.garm-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
.garm-photo:active { border-color: var(--indigo); }
.cg-cell { cursor: pointer; }
.modal-bg {
  position: fixed; inset: 0; z-index: 50;
  background: rgba(5,7,14,.72); backdrop-filter: blur(4px);
  display: flex; align-items: flex-end; justify-content: center;
}
.modal-bg[hidden] { display: none; }
.modal {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 18px 18px 0 0; width: 100%; max-width: 560px;
  max-height: 88vh; overflow-y: auto;
  padding: 20px 18px calc(20px + env(safe-area-inset-bottom));
  animation: slideUp .25s ease;
}
@keyframes slideUp { from { transform: translateY(40px); opacity: 0; } to { transform: none; opacity: 1; } }
@media (min-width: 640px) {
  .modal-bg { align-items: center; }
  .modal { border-radius: 18px; }
}
.modal-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.modal-title { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 18px; text-transform: capitalize; }
.modal-close {
  width: 40px; height: 40px; border-radius: 10px;
  border: 1px solid var(--border); background: var(--surface-2);
  color: var(--muted); font-size: 15px; cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}
.dlg { max-width: 440px; }
.dlg-msg { color: var(--text); font-size: 15px; line-height: 1.5; margin: 4px 0 18px; white-space: pre-line; }
.dlg-msg[hidden] { display: none; }
.dlg-input {
  width: 100%; background: var(--surface); color: var(--text);
  border: 1px solid var(--border); border-radius: 10px;
  padding: 12px 14px; font-size: 16px; font-family: inherit; margin-bottom: 20px;
}
.dlg-input[hidden] { display: none; }
.dlg-input:focus-visible { outline: 2px solid var(--indigo-soft); outline-offset: 2px; }
.dlg-actions { display: flex; gap: 10px; }
.dlg-actions button { flex: 1; }
.dlg-actions button[hidden] { display: none; }
.outfit { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.slot {
  background: var(--surface-2); border: 1px solid var(--border);
  border-radius: 14px; padding: 12px; display: flex; flex-direction: column; gap: 10px;
}
.slot.wide { grid-column: 1 / -1; flex-direction: row; align-items: center; }
.slot-img {
  width: 100%; aspect-ratio: 1; border-radius: 10px; overflow: hidden;
  background: var(--surface); display: flex; align-items: center; justify-content: center;
}
.slot.wide .slot-img { width: 64px; aspect-ratio: 1; flex-shrink: 0; }
.slot-img img { width: 100%; height: 100%; object-fit: cover; }
.slot-ph {
  width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
  font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 18px;
}
.slot-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; flex: 1; }
.slot-cat { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; color: var(--faint); font-weight: 600; }
.slot-name { font-size: 13px; font-weight: 600; line-height: 1.3; }
.slot-name.miss { color: var(--red); }
.slot-swap {
  border: 1px solid var(--border); background: var(--surface);
  color: var(--indigo-soft); font-size: 12px; font-weight: 600;
  padding: 8px 10px; border-radius: 8px; cursor: pointer; align-self: flex-start;
  min-height: 36px; -webkit-tap-highlight-color: transparent;
}
.slot-swap:active { background: var(--indigo-dim); border-color: var(--indigo); }
.modal-extra { margin-top: 14px; display: flex; flex-direction: column; gap: 8px; }

.cycle-bar-wrap { margin-top: 8px; }
.cycle-label { display: flex; justify-content: space-between; font-size: 12px; color: var(--faint); margin-bottom: 8px; }
.cycle-total { font-family: 'Space Grotesk', sans-serif; font-weight: 700; color: var(--text); font-size: 13px; }
.cycle-bar { display: flex; height: 32px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border); }
.seg { display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; white-space: nowrap; overflow: hidden; transition: flex-grow .3s ease; }
.seg-use { background: var(--indigo-dim); color: var(--indigo-soft); }
.seg-dry { background: var(--amber-dim); color: var(--amber); }
.seg-buf { background: var(--surface-2); color: var(--muted); }

.dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; display: inline-block; }
.legend { display: flex; flex-wrap: wrap; gap: 14px 20px; margin-bottom: 18px; font-size: 12px; color: var(--muted); }
.legend span { display: inline-flex; align-items: center; gap: 6px; }

/* ---- vista giorni: pager swipe stile iOS ---- */
.day-nav { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
.day-arrow {
  flex: 0 0 auto; width: 44px; height: 44px; border-radius: 50%;
  border: 1px solid var(--border); background: var(--surface-2); color: var(--text);
  font-size: 22px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: background .2s, transform .08s, opacity .2s; -webkit-tap-highlight-color: transparent;
}
.day-arrow:hover { background: var(--indigo-dim); }
.day-arrow:active { transform: scale(.9); }
.day-arrow[disabled] { opacity: .28; pointer-events: none; }
.day-nav-center { flex: 1; min-width: 0; text-align: center; }
.day-nav-title { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 18px; text-transform: capitalize; line-height: 1.1; }
.day-nav-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
.day-progress { display: flex; align-items: center; gap: 2px; height: 12px; margin-top: 12px; }
.dp-seg { flex: 1; height: 5px; min-width: 2px; border-radius: 2px; background: var(--surface-2); cursor: pointer; transition: transform .2s cubic-bezier(.4,0,.2,1), background .2s; }
.dp-seg.dp-wash { background: var(--amber); }
.dp-seg.dp-miss { background: var(--red); }
.dp-seg.dp-cur { transform: scaleY(2.4); background: var(--indigo-soft); }
.dp-seg.dp-cur.dp-wash { background: var(--amber); }
.dp-seg.dp-cur.dp-miss { background: var(--red); }
.day-today {
  display: block; margin: 0 auto 14px; font-size: 13px; font-weight: 600;
  padding: 8px 16px; border-radius: 999px; border: 1px solid var(--indigo);
  background: var(--indigo-dim); color: var(--indigo-soft); cursor: pointer;
  -webkit-tap-highlight-color: transparent; transition: transform .08s;
}
.day-today:active { transform: scale(.95); }
.day-today[hidden] { display: none; }

.day-view {
  position: relative;
  display: flex; gap: 14px; overflow-x: auto; overflow-y: hidden;
  scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scroll-behavior: smooth;
  padding: 6px 2px 12px; scroll-padding-inline: max(0px, calc(50% - 210px));
  overscroll-behavior-x: contain;
  user-select: none; -webkit-user-select: none; -webkit-touch-callout: none;
}
.day-view::-webkit-scrollbar { display: none; }
.day-view { scrollbar-width: none; }
.day {
  flex: 0 0 100%; scroll-snap-align: center; scroll-snap-stop: always;
  /* background: var(--surface); border: 1px solid var(--border); */
  border-radius: 22px; padding: 22px 20px calc(18px + env(safe-area-inset-bottom));
  display: flex; flex-direction: column; gap: 14px;
  min-height: clamp(340px, 58vh, 540px);
  box-shadow: 0 12px 34px rgba(0,0,0,.30); position: relative; overflow: hidden;
  transition: border-color .2s;
}
@media (min-width: 640px) { .day { flex-basis: 420px; } }
.day::before {
  content: ''; position: absolute; inset: 0 0 auto 0; height: 130px; pointer-events: none;
  background: radial-gradient(120% 100% at 50% 0, var(--indigo-dim), transparent 72%);
}
.day.washday { border-color: rgba(240,178,82,.55); }
.day.washday::before { background: radial-gradient(120% 100% at 50% 0, var(--amber-dim), transparent 72%); }
.day.shortage { border-color: rgba(242,120,120,.6); }
.day.shortage::before { background: radial-gradient(120% 100% at 50% 0, var(--red-dim), transparent 72%); }
.day-when { display: flex; flex-direction: column; gap: 2px; min-width: 0; position: relative; }
.day .chips { gap: 7px; }
.day .chip { font-size: 12.5px; padding: 5px 11px; border-radius: 8px; }
.day-hint { margin-top: auto; font-size: 11px; color: var(--faint); display: flex; align-items: center; gap: 6px; padding-top: 6px; }
.day-view[hidden] { display: none; }

/* outfit completo dentro la card giorno (niente modale in vista swipe) */
.day-outfit { display: grid; grid-template-columns: repeat(auto-fill, minmax(88px, 1fr)); gap: 10px; }
.ofit {
  position: relative; text-align: left; font: inherit; color: inherit;
  background: var(--surface-2); border: 1px solid var(--border); border-radius: 13px;
  padding: 8px; display: flex; flex-direction: column; gap: 6px; min-width: 0;
}
.ofit.swap { cursor: pointer; -webkit-tap-highlight-color: transparent; transition: transform .08s, border-color .2s; }
.ofit.swap:active { transform: scale(.96); border-color: var(--indigo); }
.ofit-img { width: 100%; aspect-ratio: 1; border-radius: 9px; overflow: hidden; background: var(--surface); display: flex; align-items: center; justify-content: center; }
.ofit-img img { width: 100%; height: 100%; object-fit: cover; }
.ofit-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 15px; }
.ofit-cat { font-size: 9px; text-transform: uppercase; letter-spacing: .05em; color: var(--faint); font-weight: 700; }
.ofit-name { font-size: 11.5px; font-weight: 600; line-height: 1.25; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
.ofit-name.miss { color: var(--red); }
.ofit-swap { position: absolute; top: 6px; right: 6px; width: 21px; height: 21px; border-radius: 50%; background: var(--indigo); color: #fff; font-size: 11px; line-height: 1; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,.3); }

/* toggle vista giorno / calendario */
.view-toggle { display: inline-flex; background: var(--surface-2); border: 1px solid var(--border); border-radius: 999px; padding: 3px; margin-bottom: 14px; gap: 2px; }
.view-toggle button { border: none; background: transparent; color: var(--muted); font-family: inherit; font-size: 13px; font-weight: 600; padding: 9px 18px; border-radius: 999px; cursor: pointer; -webkit-tap-highlight-color: transparent; transition: background .2s, color .2s; }
.view-toggle button.active { background: var(--indigo); color: #fff; }

/* vista calendario (griglia mese) */
.cal-grid[hidden] { display: none; }
.cg-head, .cg-body { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
.cg-head { margin-bottom: 6px; }
.cg-h { text-align: center; font-size: 10px; font-weight: 700; color: var(--faint); text-transform: uppercase; }
.cg-cell {
  aspect-ratio: 1; border-radius: 11px; border: 1px solid var(--border); background: var(--surface-2);
  display: flex; flex-direction: column; align-items: center; gap: 3px; padding: 5px 3px;
  cursor: pointer; position: relative; overflow: hidden; -webkit-tap-highlight-color: transparent;
  transition: transform .08s, border-color .2s;
}
.cg-cell:active { transform: scale(.95); }
.cg-empty { border: none; background: transparent; pointer-events: none; }
.cg-num { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 13px; }
.cg-dots { display: flex; flex-wrap: wrap; gap: 2px; justify-content: center; }
.cg-dot { width: 6px; height: 6px; border-radius: 50%; }
.cg-cell.wash { border-color: rgba(240,178,82,.55); }
.cg-cell.miss { border-color: var(--red); background: var(--red-dim); }
.cg-cell.today { outline: 2px solid var(--indigo-soft); outline-offset: -2px; }
.cg-flag { position: absolute; bottom: 2px; right: 4px; font-size: 9px; }
.day-head { display: flex; justify-content: space-between; align-items: center; }
.day-num { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 25px; letter-spacing: -.01em; }
.badge {
  font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
  padding: 3px 8px; border-radius: 6px;
}
.badge-wash { background: var(--amber-dim); color: var(--amber); }
.badge-ko { background: var(--red-dim); color: var(--red); }
.chips { display: flex; flex-wrap: wrap; gap: 5px; }
.chip {
  font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px;
  border: 1px solid transparent;
}
.chip.reuse { border-style: dashed; }
.chip-miss { background: var(--red-dim); color: var(--red); }
.day.dryingday { background: linear-gradient(180deg, var(--surface) 62%, var(--amber-dim) 100%); }
.dry-batch { display: flex; flex-direction: column; gap: 4px; }
.dry-row { display: flex; justify-content: space-between; align-items: baseline; font-size: 11px; color: var(--amber); gap: 6px; }
.dry-ready { color: var(--faint); font-size: 10px; white-space: nowrap; }
.dry-track { height: 4px; border-radius: 2px; background: var(--surface); overflow: hidden; }
.dry-fill { height: 100%; background: var(--amber); border-radius: 2px; transition: width .3s ease; }
.ready-note { font-size: 11px; color: var(--green); font-weight: 600; }
.load { display: flex; flex-direction: column; gap: 4px; }
.load .dry-row { color: var(--indigo-soft); }
.load.over .dry-row { color: var(--red); }
.load-fill { height: 100%; background: var(--indigo); border-radius: 2px; transition: width .3s ease; }
.load-fill.over { background: var(--red); }
.alert.warn { background: var(--amber-dim); border-color: rgba(240,178,82,.35); color: var(--amber); }
.day-foot { font-size: 10px; color: var(--faint); margin-top: auto; }

.summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 22px; }
.stat { background: var(--surface-2); border-radius: 10px; padding: 14px 16px; }
.stat .label { font-size: 12px; color: var(--faint); }
.stat .value { font-family: 'Space Grotesk', sans-serif; font-size: 26px; font-weight: 700; }
.stat .value.ok { color: var(--green); }
.stat .value.ko { color: var(--red); }

.alert {
  border-radius: 10px; padding: 14px 16px; font-size: 13px; margin-bottom: 18px;
  border: 1px solid;
}
.alert.ok { background: var(--green-dim); border-color: rgba(110,231,167,.35); color: var(--green); }
.alert.ko { background: var(--red-dim); border-color: rgba(242,120,120,.35); color: var(--red); }

button.primary {
  font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600;
  padding: 12px 20px; border-radius: 10px;
  border: 1px solid var(--indigo);
  background: var(--indigo); color: #fff; cursor: pointer;
  transition: background .2s, transform .1s;
}
button.primary:hover { background: var(--indigo-soft); }
button.primary:active { transform: scale(.97); }
button.ghost {
  font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600;
  padding: 12px 20px; border-radius: 10px;
  background: transparent; color: var(--indigo-soft);
  border: 1px solid var(--indigo); cursor: pointer;
}
button.ghost:hover { background: var(--indigo-dim); }
.actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 20px; }
#lista { display: none; margin-top: 18px; }
#lista.show { display: block; }
#lista pre { font-family: 'Inter', sans-serif; white-space: pre-wrap; font-size: 14px; line-height: 2; }
.copied { color: var(--green); font-size: 13px; margin-top: 8px; display: none; }

footer { text-align: center; color: var(--faint); font-size: 12px; margin-top: 48px; }

@media (prefers-reduced-motion: reduce) { *, *::before, *::after { transition: none !important; } }

@media (pointer: coarse) {
  input[type=range] { height: 8px; }
  input[type=range]::-webkit-slider-thumb { width: 26px; height: 26px; }
  input[type=range]::-moz-range-thumb { width: 26px; height: 26px; }
}

@media (max-width: 560px) {
  .wrap { padding: 0 14px 60px; }
  header { padding: 36px 0 26px; }
  .panel { padding: 18px 14px; margin-bottom: 14px; }
  .cat-grid { grid-template-columns: 1fr; }
  .cat-row-bottom { gap: 10px; }
  .sug-btn { flex: 1; }
  .summary { grid-template-columns: 1fr 1fr; gap: 8px; }
  .stat { padding: 12px; }
  .stat .value { font-size: 22px; }
  .day-view { gap: 10px; }
  .day { padding: 20px 16px calc(16px + env(safe-area-inset-bottom)); min-height: 62vh; }
  .day-num { font-size: 23px; }
  .day-outfit { grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 8px; }
  .var-main { min-width: 140px; }
  .view-toggle { display: flex; width: 100%; }
  .view-toggle button { flex: 1; }
  .cg-head, .cg-body { gap: 5px; }
  .chips { gap: 6px; }
  .chip { font-size: 12px; padding: 5px 10px; }
  .actions { position: sticky; bottom: 0; background: var(--bg); padding: 12px 0; margin-top: 12px; border-top: 1px solid var(--border); }
  .actions button { flex: 1; min-height: 48px; }
  .seg { font-size: 10px; }
  .ctrl label { font-size: 13px; }
}
</style>
</head>
<body>
<div class="wrap">

<header>
  <div class="eyebrow">Guardaroba matematico</div>
  <h1>Il tuo armadio, testato <em>giorno per giorno</em></h1>
  <p class="sub">Imposta il tuo ciclo del bucato, decidi quanti capi hai per categoria e la simulazione ti mostra un mese intero: cosa indossi ogni giorno, quando lavi, cosa è steso e — soprattutto — se rimani mai senza roba pulita.</p>
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

<section class="panel" aria-label="Categorie e quantità">
  <h2>2 · Cosa hai nell'armadio</h2>
  <p class="desc">Per la maggior parte delle categorie puoi aggiungere più tipi (es. fantasmini + lunghi, jeans + tuta), ognuno con quantità e peso propri. <strong>Canotta, t-shirt, polo e camicie</strong> sono il gruppo "sopra": indossi <em>un</em> capo sopra al giorno a rotazione, e alcuni giorni la camicia va sopra una canotta — la pill di queste categorie mostra la copertura del gruppo intero. Il bottone "Completa" aggiunge i capi mancanti.</p>
  <div class="cat-grid" id="catBody"></div>
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

</div>

<input type="file" id="photoInput" accept="image/*" style="display:none">

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

<div class="modal-bg" id="dlgBg" hidden>
  <div class="modal dlg" role="dialog" aria-modal="true" aria-labelledby="dlgTitle">
    <div class="modal-head">
      <span class="modal-title" id="dlgTitle"></span>
      <button class="modal-close" id="dlgX" aria-label="Chiudi">✕</button>
    </div>
    <p class="dlg-msg" id="dlgMsg"></p>
    <input type="text" class="dlg-input" id="dlgInput" hidden>
    <div class="dlg-actions">
      <button class="ghost" id="dlgCancel" type="button">Annulla</button>
      <button class="primary" id="dlgOk" type="button">OK</button>
    </div>
  </div>
</div>

<script>
const PARENTS = [
  { key: 'mut', name: 'Mutande', color: '#818CF8', multi: false, wearsLocked: true, active: true, canOff: false,
    presets: [{ l: 'Slip · 60 g', s: 'Mu', g: 60 }, { l: 'Boxer · 80 g', s: 'Mu', g: 80 }, { l: 'Boxer lungo · 100 g', s: 'Mu', g: 100 }],
    variants: [{ pi: 1, own: 10, wears: 1 }], garments: [] },
  { key: 'cal', name: 'Calzini', color: '#6EE7A7', multi: true, wearsLocked: true, active: true, canOff: false,
    presets: [{ l: 'Fantasmini · 40 g', s: 'Fa', g: 40 }, { l: 'Corti · 60 g', s: 'Co', g: 60 }, { l: 'Lunghi/spugna · 80 g', s: 'Lu', g: 80 }],
    variants: [{ pi: 1, own: 10, wears: 1 }], garments: [] },
  { key: 'can', name: 'Canotte', color: '#7DD3FC', multi: true, wearsLocked: false, active: true, canOff: true, group: 'top',
    presets: [{ l: 'Leggera · 90 g', s: 'Cn', g: 90 }, { l: 'Media · 110 g', s: 'Cn', g: 110 }, { l: 'Pesante · 140 g', s: 'Cn', g: 140 }],
    variants: [{ pi: 1, own: 0, wears: 1 }],
    garments: [
      { uid: 'can1', n: 'Canotta coste nera', s: 'CanN', c: '#1f1f24', g: 110, wears: 1, img: 'data:image/jpeg;base64,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' },
      { uid: 'can2', n: 'Canotta coste bianca', s: 'CanB', c: '#f2f2ef', g: 110, wears: 1, img: 'data:image/jpeg;base64,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' }
    ] },
  { key: 'mag', name: 'Top · t-shirt / polo / camicie', color: '#F0B252', multi: false, wearsLocked: false, active: true, canOff: false,
    presets: [{ l: '~150 GSM · 150 g', s: 'Ma', g: 150 }, { l: '~180 GSM · 200 g', s: 'Ma', g: 200 }, { l: '~220 GSM · 250 g', s: 'Ma', g: 250 }],
    variants: [{ pi: 1, own: 5, wears: 1 }],
    garments: [
      { uid: 'top1', n: 'T-shirt relaxed bianca', s: 'TeeB', c: '#f5f5f2', g: 180, wears: 1, img: 'data:image/jpeg;base64,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' },
      { uid: 'top2', n: 'T-shirt interlock crema', s: 'TeeC', c: '#efe9dc', g: 200, wears: 1, img: 'data:image/jpeg;base64,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' },
      { uid: 'top3', n: 'T-shirt interlock nera', s: 'TeeN', c: '#1f1f24', g: 200, wears: 1, img: 'data:image/jpeg;base64,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' },
      { uid: 'top4', n: 'Polo slim nera', s: 'PoloN', c: '#1f1f24', g: 220, wears: 1, img: 'data:image/jpeg;base64,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' },
      { uid: 'top5', n: 'Polo strutturata nera', s: 'PoloS', c: '#1f1f24', g: 300, wears: 1, img: 'data:image/jpeg;base64,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' },
      { uid: 'top6', n: 'Polo maglia traforata crema', s: 'PoloC', c: '#efe9dc', g: 250, wears: 1, img: 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj/2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj/wAARCAEEAKwDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD6hooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiivNfGnxB13SvHsfhbwz4UGu3Z09dQdjfLb7ULlD94YOCB370AYXxx+Ls/ha2vdM8Hwrf65aRrNez7d8OnxlgAX7FySAF7Zz7V1/ww+IEHjKzmtr23bTPEliAt/pkvDxn++ufvIeoPv9CfPfial7J8CfHV9rHhK28MaldzRPNHFPHMbn95FiRmTvnIwfTPeuy8Rz65pGr6bqXhbwDba9dy6dHDLqP22K2kQZz5XzckcA59/agD0iiuH+F/jW+8YLrsWraKNGvtJvPsc1uLkT/NtyfmAA/LNdxQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABXlY/5Ogb/sVB/6UmvVK8s8aeEfGj/E5PFngu80GI/2UumvHqYlb/lozkgIPde/rQB6beWltfWz217bw3Nu/DxTIHRu/IPBqZQFACgAAYAHavK/s/xp/wCf7wJ/36uf8KPs/wAaf+f7wJ/36uf8KAHfBv8A5G34n/8AYwN/6AK9Srz/AOEnhTXvDb+JbvxTc6bPqGsX/wBtb7Bv8tflAIwwBH616BQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFISBQAtBNQyTKo61nXN+RkR8/SgDUMijOSBinB1PeufjMs0mJCQD2rV6KBmocuw7FzIPcUhdR1YVlz3cMYOZ4hg4OXHFZ09+rrmJ1dT0KnINS6lhqFzpFdXGVII9qdVDTQUtUB6kZNXA49eatO4mh9FFLVCEooooAKKKKACiiigAooooAKKD0qpdXAiHXFAEsswWqc11k4Tk1QFw1zJjJ256irIUKQBgVDl2GkJteQ5dvwFOES4wAKeKKhsZE3yuKtZ3xkr1AqLYHz6jkUsYdOnTvmkM+dPFPwd1u/wBe8QagZtMkF9K8luZGfdFk5zwMZ/OvT/hf4eudE8L6XpV88cs1ohEjRklSSxPGfrXYXkPnyBI35Y8g9qvWEUNum1SN3fNLVvXYeiRajXaoFRP984qZmAHFQk5NXckVZGU+tTpMDwarUDrQm0Fi916UlRRvxUtaJ3JCiiimAUUUUAFFFB4oAhuZAiGuT1W8Mk3loTknFbes3HlwnBrm9NiNxeGRuQpqZOwI2dPg8uNPXvUjyZvkX2qzGuFrOBzqqgdqxZaRqEc0mKcaaaYhV6049KavNObpQBEoHn7u+KlHPXrUa/fpw+9+FMY8U4U3FKKAFpccUlPWgQRmrCHIqqOGqeI81UWJktFFFaCCiiigAqOZtq1JVLUJQiH2FAHO69c5Owdan0iDyoFyOTyazObvUBnlQcmt+AYxispO7KRZHANY8bY1gCtg9D9KwQ2NbUVnLoXHqdD2qNutSfwCoWPNUSSpTn6UkfSlk6UCIk+9Tv4qRaeRzmmMcelAoPSk70Ax1PWmDpT1ouIb3qRDUR609TQgLKnIpaZGe1PrVO5IUUUUwEY4Fc9r05SJgO9b8nSsHVbczUmBl6QmFLkfM1bsS4UEisqBDEw3DgelaizxSKApAb0PFZNMq5Mfuk+1c9nOurXR7SYj06VzUZB10Y7VnPdFw6nT/wDLMfSq/erDf6sfSq461oySxH0ol6UR9KSbpSERrUlRp1qXHFMYZpO9KRxTc5NAEo6U4dKYvSn9qQDD1oU80NxTV5PFCEWFNTg5FQKpqVOK1iJjqKKKoQhGetV5YA3UVZoIzQBmyWq44FUprQYPFbpQVFJECpoA5ia4urNTsfcno3NUbJ/O1RJuhPUVraxFiJqxtIH+mx1lOKLi2de3+rH0qFetSS/cH0qGL71JgWo+lMnqROlRz0PYQyPkip8fLVePqKtfw0hjWHFRL1qV+lRL1pgSrUsYJ6VEtTRUwY4wg9acEA6Cn0VokkSxAKdikopiFpKKKACiiigAoboaKD0oAwdbX90/0rntI/4/k+tdLrQ/dP8ASua0n/j/AE/3qiY0ddL9wfSoYh81SzfcFRxdazKLKdKiuKmT7tQXFN7CGxdatDpVSHrVtelIY1+lRL1qR+lRL1oAmSp4vvCoUqaLqDVR3EyaiiitSQooooAKKKKACiiigAoNFB6UAZGsj9030rltM/5CKf71dVq/+rb6Vymm/wDITX/eqJlI62b7v4UyHrTpjSQdayGWl6VXuasfw1WuDzTYIS361bXpVSDrVyLvQgI36Gol61JJUY60MCZKnj6iq6VPD94VURMnooorUkKKKKACiiigAooooAKD0NFB6UAZWqj9230rlNN/5Cq/71dbqn+rP0rk9O/5Cyj/AGqiY0dRMeafBUU/WpbftWRRYP3ap3B5q43SqU/WgEPt+tXIu9UrbrV2LvVRAikqMdakkqMHmkwJUqeLqKgQ1NFywFVHcTLFFFFakhRRRQAUUUUAFFFFABQaKKAM7UlzGa5WyXbqgY9mrtbiPehFYj6dtk3gc1MlcaJXbdzVi27VW8iSpo1lToBWfKx3LUhwKpSnJqSUzv1IA+lReRIx5Jp8jBMlt+tWoutV4YWXvU5WTHBpqLQXQklQqRuqOWKfPEjU1ElHU5o5GFy2tTQghwT0qCHe3UYq5GmOpoUbA2SUUUVoSFFFFABRRRQAUUUUAFFFFABTCgPan0UAM8sego2ewp9FADNnsKPLHoKfRQAwIBS7afRQAwoD2FJ5a+gqSjNADQgHQUuKXNFACUUUUAFFFFABRRRQAUUUUAFFFFABS5pKM0ALmjNJmjNAC5ozSZozQAtJRmjNABRRmjNABRRmjNABRRmjNABRRmjNABRRmjNABRmkooAM0UUUAFFGaTNAC0UmaM0ALRSZozQAtFJmjNAC0UmaM0ALRSZozQAtFJmjNAC0UmaM0ALRSZozQBLsHvRsHvRRQAmwepo2D3oooANg96Ng96KKAEKD3o2D3oooANg96Ng96KKADYPejYPeiigA2D3o2D3oooANg96Ng96KKADYPejYPeiigA2D3o2D3oooANg96Ng96KKAP//Z' },
      { uid: 'top7', n: 'Camicia bowling verde salvia', s: 'CamS', c: '#b7bdae', g: 250, wears: 1, img: 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj/2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj/wAARCAEEAK8DASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD6bIpCKfSYoAZijFPxSYoAbilxS4pcUAIBSgUuKXFAAKWiloAKKKBQAoooooAKKKKACorm5gtYvMup4oI843yuFGfTJqWvJf2mYIrrwFplvcIJIZdbs43Q/wASliCPyoA2PiHo9z48bTdD0zxHZWuhTOx1WO2mBuriMYIjQg8Ked39eleQ/CnwLJaxat4j8H6nBoup6X4hurFxdOTbXFmHUeVIM9Rng9c++CPcfDfwy8G+F9Yj1PQdBtrK/iDKkyO5IBGD1YjkVm+FvhrZ2fhrxDofiQW+q2GratNqTRYZFAdlZVPOcgqDkUAdrBqunXEqxQahZyyt91I51Yn6AGrleD+IfA3hrwf8YPhk/hrSYdPa5u7sTGNnO8LDxncT0yfzr3igBtJinUmKAExSU7FGKAEoxS4oxQAlLS0UAFFFFABS0UUAFFFFABRRRQAV5P8AtLTR2/gTS5p3WOKPXLJ3djgKAxJJr1iqmq6XYavafZdWsbW+ttwbyrmJZEyOhwwIzQBzLfFHwHk/8Vfof/gYn+NH/C0fAf8A0N+h/wDgYn+NXf8AhAvB/wD0Kmgf+C+H/wCJo/4QLwf/ANCpoH/gvh/+JoA818W+K9A8SfF/4Xr4f1mw1JoLu7MotZhJsBh4zjpnB/Kvbqw9P8IeGtNvI7vTvD2j2l1EcpNBZRo6HGOGAyOCa3KAEopaMUAJRS4pMUAFFGKXFACUUuKKADFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFITgc0ALSE1BLcKg5NY2p65FbD7wJ9KAN4yKOpqOS6hiTfLKiJ0yxwK8m13xvMHaO1bL9gKw7XVb/UbmRr6d2SNMrH/CCT1+tS5WA93jnilGYpUcf7LA0ryogy7qo92ArwTUtctdPVRcX0VuXB2iSQLn6ZrhvEWuW9wkjQ30M2B/DMGx+tLmYH1nBPFOCYZUkUHBKMGAP4VLXyR8CfFF7YeMr22tZCbeaIyNEfusynH54NfUej61bakoCHZOOsbdfw9aadwNSiiiqAKKKKACiiigAooooAKKKKACgnFBOBVG+vFgUlscUATzThB1FY2oazFApJcYHvXHeJ/Fy2wYI/PbmuEvb+51ENJcyMsAUuwzxgetS2B2+seNYPmS2Zpm6fJ0/OuTu9RvL9mMjeXH7HmoraGMwo6DKsMim3zeXAxUdqnmYzE8OGO41DWGDFwk4TJ9dorpLJSDcsB1j/lzXGfDst5urtICGa63AH0xXo1hBvYYHDcGkB5X8R9Cl1+WwlSSEJbFiySZ+YEg4H5V5vqthJpt9dznyESYDEcS4C/TgV634gnMEksTHlCVP4V5B4uuy0pXPencLHcfs/xNP4rup8ZEcGM/U/8A1q98ctE4aNism7IIPIryD9nKxaKx1C+lUr5rBVJHUAf45r16Nlcs7dulFwOh03xZcwnZexiZQcbhw3+Brp9P1qxvsCGYCQ/wPwa8zm4nYD15oQ5PWmpBY9dori/D2uzQzLb3TmSADq3LLXZKwdQykFTyCO9UncQ6igUUwCiiigAoooJwKAIbmURxsT2rzHxz4gMCsiNzXa+Ir3yLR8HFeCeLL83F2+W+UcmpbAoJLNquqIrsSoO5h/IVpeO5E0zQnWLgzKq/rzUfhW2JmVmHzH5m/pWb8YLsLPY2uc8is2UdNpbZ0i1bvsFWZIhJByOoqrpWP7EtP9wVqRLmLHtSA4/VNJWKJ5IGeNickoSP5Vk+BZdUuPHkFodcnt7ONTI8Ej7vtGP4V3d+59q7u6t/NiZPUVzN74bS9jDRu0FzE2+OVOGRh0INUBF8YrZtNvY72NSILsEE9g46j8Rz+deGeRNrviGGytzl5nCg+g7mvoPVnn8V+EdQ8Payqx6/BH51tKBhLgpyGX3xwR71598F/DRKz61dR5mkYxQIeyj7zfnx+FMD1Twhp/8AZulm1tRgImF/Kt3w/Dcm1ae/md2bkIcAL+AqjFFI8qWsZIU4MhHf2relAiiVF4AHSgCBW3SMx70+3Xex+tRJ0J9at6euZB6cGgRmw6oG8Z/2bHklVy/5Yr0nwpd5Wayc/NGSyZ/umvD/AAVci7+JevueWRxj6ZxXqFrdGz1WOYdA3PuO9NMD0EUU1GDKGU5BGRTqsQUUUUAFRysAOakPSs3VLgRQMc44oA4fx9qPlW0gDV4ndSG4ulB/ibJ+ldr4/wBU86Zog3HeuI0pDPes/wDDnArOTKR2XhhDuViOvavPPixcGXxdDEOi7f516loEYRQfSvF/iNOW+Iip6gVLA9V0040m2U9kFbNuMxj6VhWhxp9uPYVu2nMI+lIBknDVCE2OWHGanuB3pkY3pn0qgOf8SRz6nqFjpNliO6uWLLcd4FUZZwfYfzrpdP0q10WyS0tfmWMbd5HLe9ZNqP8AivtMwelpOf8A0GuilGZ2HbNMBNPj2FpGHzE1JO5Z8ZyTTiwWPAqKIbnzSAkk+WIAVf01fkB74rOlb5gta9mNsOfRaBHlfwuYv498RSHs4X9c16nd/wCtzXkvwTkN1qOsXjdZ7lm/DNetXf3qYHa+F7v7TpwRj88Xy/h2rYrhvCl35GoqjH5JPlP9K7mrTuIKKKKYDZDgVxnjTUBb2j4OOK624bCmvKviRcssLLk80mB5T4gvWmmkOcljgVd0G12RoT1PNZKxm6vOmUU8mur0yIHbgfdFZso6HShsQeuK8E8eHzfijEvuK97s2AJ+leD+J0L/ABdiU98H+dID1aNttrCvsP5V0On/ADQj6VzTHCoK6TSTmL8KQBP0NMtvusKknHUVDGcNVAUNMRn+IVuqgny7GU8e7J/hXROf3rH3rH00GPxyzoSrfYTgjt84rWf75z35oARyc1NCNoyaiHLVMxwuKAIfvXFblsMwkeq4rDtvmnY+9blocID70hHlPwWgNtbzhhtbzWBH0Jr1G6b5hXEeCbf7JqOoRkYxdS/+hmuxlbdcAdhzTAfBIUnRlOMH9a9J025F3ZRTdyPm+teZr95h+IrrvCF5kvbsfvfMv171UQOnoooqxFa5GVNcJ4o0oXjEum5cYIr0FhkVRubYODkZoA8Ln8LGznZ7YHyz1Rv6GprKBYFKOCrE969ZutKjfPyisHUNBRw3y1LiO5yMAKlsjtxXiGsL5nxcjOPuqa9o12O60dHkTEkY/havJ5bOSfxmNU+VUddu3uDUWsM7eQ/c+tdJobZSuYlOfL+tdLoX3akC3dDBNVDwK0LpckgVTK/IKoCvpvzeMJD/AHbEZ/Fj/hWtJ/rKytF2/wDCRakzgkC2iUYPI5atN+oNAEkK5OaSVuTjtUi/JF9arn7p96AH2PMrfWtqA/u/xrF07/WN7GtiH7v1pCOfghFtqdw44LzMf1rXh/eOT1PtWzp/g+S8drm7nEcUjl0EfLY7Z9K63TtGstPA+zxDeP425b86pIDkdP8AD19dMHZRBF2Z+p/Cup0rRLewYOC0kv8Aebt9BWrilq0gCiiimIKayg06igCBogTzVS5twQeK0jUE4AU0AeV/EmMR6dJ9K8gRf9Iix/eFexfFL/jwYV5HGMXEX+8KzZRqOcyKPSuo0PiOuUU5mP1rqdGOI1qANafrVOQYq5IODVaQZzVAUNGOdf1MDtDED/49WoeX21leH1dtZ1yRRkKIlPsApP8AWtMN82RQBLKxwAKYThRRnJpJOlAD9O6ufetiDnbWNYcbvrWzb9FoA9D0v/kHW/8AuCrYqppf/IOt/wDcFWxWiEFFFFMQUUUUAFFFFABUNx9ypqhuD8lAHlXxSb/RsV5Qf9dF/vf0r1L4ot+7A968uf8A18P1/pWcii3CczGuq0rhErlIP9cfrXU6WeFqUBuMMxk+1UyetXT/AKgVnycI5pgV9FXbe6xgkbzGfr8tWlbEijtVTSXzeasf7piX/wAcz/WrP/LVPrQBalXb0qLfkGrYxJGT+FU3XaxxQBPacGti3PC1j2veta3P3frQB6Lpf/IOt/8AcFWxVPSf+Qbb/wC5VwVohBRRRTEFFFFABRRRQAVBc/cNT1Bc/cNAHkPxROdv1rzOTi4hHua9Q+JiFgp/2q8xu123sA9QTWciie2OZj9a6jS+grlrQ/vj9a6nS/u1AHQHmEYrPkHY9Kvx8xfQVRufvGqAx/DEhmn8QrICWN2qoQcYxGuPwrWm+SRfasnwuuy+1r0Nwp/8cFaeoEYGDQBcsZNysD0PSmyjk/WqtnL+796tOdye9AD7Tqa1bY/drKtBgHPrWpbnlfpQB6LoxzpkH+7V4VQ0Q50uD6f1q+K0QgooopiCiiigAooooAKguB8hqemsMigDznxrpr3SjYCfm9K8n8VWbWOq2aMMEoW5+tfSNzaLJ1UGsi98NabfTCW8sIJpANoZ0BIHpUtXGfPdqP3mTt610mlSZyOPzr1lfB2ihsjS7UH/AHKtQ+FdJj+7YW4+i1PKM4KH/Vj3qhcjLHB5ya9UHh+x7W0X5U0+HNPPW0h/75osFzxbwuwe81lR1+0Af+OCte8g+UcdK9Og8LaVbvI0FhbRtIdzlUxuPqalPh+wP3rWI/hT5QPGbeRlZlHrV2OUnOea9UHhjSg277BBn121IPDmln/lxg/75o5QPNbc5QEY5NaUAOUx0713kfh/TFIxZQ/981ZXSNPUcWcI/wCA0coXE0DnSYPx/nWiKZBEkMQjiRUQdABwKfVIQUUUUxBRRRQAUUUUAFFFFACEUm2nUUAN20u2looATFGKWigAxRiiigAxRiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA//9k=' },
      { uid: 'top8', n: 'Camicia maglia traforata nera', s: 'CamN', c: '#1f1f24', g: 300, wears: 1, img: 'data:image/jpeg;base64,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' }
    ] },
  { key: 'tee', name: 'T-shirt', color: '#F0B252', multi: true, wearsLocked: false, active: true, canOff: false, group: 'top',
    presets: [{ l: 'Leggera · 150 g', s: 'Tee', g: 150 }, { l: 'Media · 180 g', s: 'Tee', g: 180 }, { l: 'Pesante · 220 g', s: 'Tee', g: 220 }],
    variants: [{ pi: 1, own: 0, wears: 1 }], garments: [] },
  { key: 'pol', name: 'Polo', color: '#2DD4BF', multi: true, wearsLocked: false, active: true, canOff: false, group: 'top',
    presets: [{ l: 'Piqué · 200 g', s: 'Pol', g: 200 }, { l: 'Jersey · 250 g', s: 'Pol', g: 250 }, { l: 'Strutturata · 300 g', s: 'Pol', g: 300 }],
    variants: [{ pi: 1, own: 0, wears: 1 }], garments: [] },
  { key: 'cam', name: 'Camicie', color: '#FB7185', multi: true, wearsLocked: false, active: true, canOff: false, group: 'top',
    presets: [{ l: 'Leggera · 200 g', s: 'Cam', g: 200 }, { l: 'Media · 250 g', s: 'Cam', g: 250 }, { l: 'Pesante · 300 g', s: 'Cam', g: 300 }],
    variants: [{ pi: 1, own: 0, wears: 1 }], garments: [] },
  { key: 'pan', name: 'Pantaloni', color: '#F472B6', multi: true, wearsLocked: false, active: true, canOff: false,
    presets: [{ l: 'Corti/chino · 450 g', s: 'Pc', g: 450 }, { l: 'Jeans · 700 g', s: 'Je', g: 700 }, { l: 'Jeans pesanti · 800 g', s: 'Jp', g: 800 }, { l: 'Tuta · 400 g', s: 'Tu', g: 400 }],
    variants: [{ pi: 1, own: 6, wears: 2 }],
    garments: [
      { uid: 'pan1', n: 'Pantaloni eleganti neri', s: 'PanE', c: '#1f1f24', g: 450, wears: 2, img: 'data:image/jpeg;base64,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' }
    ] },
  { key: 'fel', name: 'Felpe', color: '#C4B5FD', multi: true, wearsLocked: false, active: true, canOff: true,
    presets: [{ l: 'Leggera · 500 g', s: 'Fl', g: 500 }, { l: 'Girocollo media · 700 g', s: 'Fe', g: 700 }, { l: 'Hoodie invernale · 1000 g', s: 'Ho', g: 1000 }],
    variants: [{ pi: 1, own: 7, wears: 2 }], garments: [] }
];
/* Split a runtime della vecchia categoria unica 'mag' in T-shirt / Polo / Camicie:
   così le foto base64 dei capi restano dove sono e vengono smistate per nome. */
(function splitTops() {
  const mag = PARENTS.find(p => p.key === 'mag');
  if (!mag) return;
  const routeKey = gm => /camic/i.test(gm.n) ? 'cam' : /polo/i.test(gm.n) ? 'pol' : 'tee';
  (mag.garments || []).forEach(gm => {
    const t = PARENTS.find(p => p.key === routeKey(gm));
    if (t) t.garments.push(gm);
  });
  PARENTS.splice(PARENTS.indexOf(mag), 1);
})();
const DAYS = 30;
const AUTOKEY = 'armadio:auto';
const PROFKEY = 'armadio:profiles';
const $ = id => document.getElementById(id);
const wash = $('wash'), dry = $('dry'), buf = $('buf');
let overrides = {}; // { dayIndex|parentKey : 'g:uid' | 'v:vid' }
let uidSeq = 100;
let LAST = [];
let pendingPhoto = null;

function cycleDays() { return +wash.value + +dry.value + +buf.value; }
function coverage(p) {
  return p.variants.reduce((s, v) => s + v.own * v.wears, 0) +
         p.garments.reduce((s, gm) => s + (gm.wears || 1), 0);
}
/* Copertura combinata del gruppo "sopra" (canotta + t-shirt + polo + camicie):
   si indossa UN capo sopra al giorno, quindi conta il totale del gruppo, non le singole. */
function groupCoverage() {
  return PARENTS.filter(p => p.active && p.group === 'top').reduce((s, p) => s + coverage(p), 0);
}

function hexDim(hex, a) {
  const r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
  return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
}

/* ---------- date ---------- */
function startDate() {
  const v = $('startDate').value;
  return v ? new Date(v + 'T12:00:00') : new Date();
}
function dateForDay(i) {
  const d = new Date(startDate());
  d.setDate(d.getDate() + i);
  return d;
}
const fmtDay = new Intl.DateTimeFormat('it-IT', { weekday: 'short', day: 'numeric', month: 'short' });
const fmtWeekday = new Intl.DateTimeFormat('it-IT', { weekday: 'long' });
const fmtDate = new Intl.DateTimeFormat('it-IT', { day: 'numeric', month: 'long' });
const cap = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : s;

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
    v: 5,
    wash: wash.value, dry: dry.value, buf: buf.value, cap: $('cap').value,
    start: $('startDate').value,
    parents: PARENTS.map(p => ({
      key: p.key, active: p.active,
      variants: p.variants.map(v => ({ pi: v.pi, own: v.own, wears: v.wears })),
      garments: p.garments.map(gm => ({ uid: gm.uid, n: gm.n, s: gm.s, c: gm.c, g: gm.g, wears: gm.wears, img: gm.img || null }))
    })),
    overrides: overrides
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
  if (s.wash) wash.value = s.wash;
  if (s.dry) dry.value = s.dry;
  if (s.buf !== undefined) buf.value = s.buf;
  if (s.cap) $('cap').value = s.cap;
  if (s.start) $('startDate').value = s.start;
  if (s.parents) {
    let plist = s.parents;
    if ((s.v || 0) < 5 || plist.some(sp => sp.key === 'mag')) plist = migrateParents(plist);
    plist.forEach(sp => {
      const p = PARENTS.find(x => x.key === sp.key);
      if (!p) return;
      p.active = sp.active !== false;
      if (sp.variants && sp.variants.length) {
        p.variants = sp.variants.map(v => ({ pi: Math.min(v.pi, p.presets.length - 1), own: v.own, wears: v.wears }));
      }
      if (sp.garments) p.garments = sp.garments;
    });
  }
  overrides = s.overrides || {};
}

let saveTimer = null;
function autosave() {
  clearTimeout(saveTimer);
  saveTimer = setTimeout(() => {
    api('/api/plans/auto', 'PUT', { state: serialize() }).catch(() => {});
  }, 700);
}

let PROFS = [];
function renderProfiles(selectedId) {
  api('/api/plans').then(list => {
    PROFS = list;
    const sel = $('profileSel');
    sel.innerHTML = '<option value="">— Programmazioni salvate —</option>' +
      list.map(p => '<option value="' + p.id + '"' + (p.id == selectedId ? ' selected' : '') + '>' + p.name + '</option>').join('');
  }).catch(() => {});
}

/* ---------- categorie ---------- */
function shortName(n) {
  const words = n.trim().split(/\s+/);
  let s = words[0].slice(0, 4);
  if (words[1]) s += words[1][0].toUpperCase();
  return s;
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

/* ---------- simulazione ---------- */
function simulate() {
  const w = +wash.value, d = +dry.value;
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

  for (let day = 1; day <= DAYS; day++) {
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

/* ---------- render ---------- */
function render() {
  $('washOut').textContent = wash.value;
  $('dryOut').textContent = dry.value;
  $('bufOut').textContent = buf.value;
  const c = cycleDays();
  $('cycleTot').textContent = c;
  $('segUse').style.flexGrow = +wash.value;
  $('segUse').textContent = 'Uso · ' + wash.value + 'g';
  $('segDry').style.flexGrow = +dry.value;
  $('segDry').textContent = 'Stesi · ' + dry.value + 'g';
  $('segBuf').style.flexGrow = Math.max(+buf.value, .4);
  $('segBuf').textContent = +buf.value > 0 ? '+' + buf.value : '';

  renderCats();

  const capKg = +$('cap').value;
  $('capOut').textContent = capKg;
  const { days, shortagesTotal, batches } = simulate();
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
    '<div class="stat"><div class="label">Carico medio</div><div class="value">' + avgKg.toFixed(1) + '<span style="font-size:14px;color:var(--muted)"> kg</span></div><div class="label">' + Math.round(avgKg / capKg * 100) + '% di ' + capKg + ' kg</div></div>' +
    '<div class="stat"><div class="label">Carico massimo</div><div class="value ' + (maxKg > capKg ? 'ko' : '') + '">' + maxKg.toFixed(1) + '<span style="font-size:14px;color:var(--muted)"> kg</span></div><div class="label">' + Math.round(maxKg / capKg * 100) + '% di ' + capKg + ' kg</div></div>' +
    '<div class="stat"><div class="label">Giorni scoperti</div><div class="value ' + (shortagesTotal > 0 ? 'ko' : 'ok') + '">' + shortagesTotal + '</div></div>';

  let verdictHtml = shortagesTotal === 0
    ? '<div class="alert ok">✓ Con queste quantità arrivi a fine mese sempre coperto, bucato ogni ' + wash.value + ' giorni incluso.</div>'
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
function findVar(k) {
  const parts = k.split('|');
  const p = PARENTS.find(x => x.key === parts[0]);
  return { p, v: p.variants[+parts[1]], vi: +parts[1] };
}

document.addEventListener('click', e => {
  const sw = e.target.closest('[data-swap]');
  if (sw) {
    cycleOverride(sw.getAttribute('data-swap'));
    return;
  }
  const msw = e.target.closest('[data-mswap]');
  if (msw) {
    const k = msw.getAttribute('data-mswap');
    cycleOverride(k);
    openModal(+k.split('|')[0]);
    return;
  }
  const gp = e.target.closest('[data-gphoto]');
  if (gp) {
    pendingPhoto = gp.getAttribute('data-gphoto');
    $('photoInput').click();
    return;
  }
  const dayEl = e.target.closest('[data-day]');
  if (dayEl && !e.target.closest('.chip')) {
    openModal(+dayEl.getAttribute('data-day'));
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
    const p = PARENTS.find(x => x.key === vadd);
    const usedPi = p.variants.map(v => v.pi);
    let freePi = -1;
    p.presets.forEach((_, i) => { if (freePi < 0 && usedPi.indexOf(i) < 0) freePi = i; });
    p.variants.push({ pi: freePi >= 0 ? freePi : 0, own: 0, wears: p.wearsLocked ? 1 : 2 });
    render();
  }
  if (vdel) { const r = findVar(vdel); r.p.variants.splice(r.vi, 1); overrides = {}; render(); }
  if (auto) {
    const p = PARENTS.find(x => x.key === auto);
    const need = cycleDays();
    const cov = p.group === 'top' ? groupCoverage() : coverage(p);
    if (cov < need) p.variants[0].own += Math.ceil((need - cov) / p.variants[0].wears);
    render();
  }
  if (gadd) {
    const p = PARENTS.find(x => x.key === gadd);
    uiPrompt('Nome del capo (es. "Jeans neri", "Polo bianca"):').then(name => {
      if (!name) return;
      const mid = p.presets[Math.floor(p.presets.length / 2)];
      p.garments.push({ uid: 'u' + (uidSeq++) + Date.now().toString(36), n: name, s: shortName(name), c: '#8B90A8', g: mid.g, wears: p.wearsLocked ? 1 : (p.multi ? 2 : 1) });
      render();
    });
  }
  if (gdel) {
    const parts = gdel.split('|');
    const p = PARENTS.find(x => x.key === parts[0]);
    p.garments = p.garments.filter(gm => gm.uid !== parts[1]);
    overrides = {};
    render();
  }
});

document.addEventListener('change', e => {
  const vpi = e.target.getAttribute('data-vpi');
  const vwears = e.target.getAttribute('data-vwears');
  const tog = e.target.getAttribute('data-toggle');
  const gcol = e.target.getAttribute('data-gcolor');
  if (vpi) { const r = findVar(vpi); r.v.pi = +e.target.value; render(); }
  if (vwears) { const r = findVar(vwears); r.v.wears = +e.target.value; render(); }
  if (tog) { const p = PARENTS.find(x => x.key === tog); p.active = e.target.checked; render(); }
  if (gcol) {
    const parts = gcol.split('|');
    const p = PARENTS.find(x => x.key === parts[0]);
    const gm = p.garments.find(x => x.uid === parts[1]);
    if (gm) { gm.c = e.target.value; render(); }
  }
});

/* ---------- override / foto / modal ---------- */
function cycleOverride(k) {
  const parts = k.split('|');
  const di = +parts[0], key = parts[1];
  const p = PARENTS.find(x => x.key === key);
  const choices = p.garments.map(gm => 'g:' + gm.uid)
    .concat(p.variants.map((v, i) => v.own > 0 ? 'v:' + i : null).filter(x => x));
  if (choices.length > 1) {
    const cur = overrides[di + '|' + key];
    const pos = choices.indexOf(cur);
    overrides[di + '|' + key] = choices[(pos + 1) % choices.length];
    render();
  }
}

$('photoInput').addEventListener('change', e => {
  const file = e.target.files && e.target.files[0];
  e.target.value = '';
  if (!file || !pendingPhoto) return;
  const parts = pendingPhoto.split('|');
  pendingPhoto = null;
  const p = PARENTS.find(x => x.key === parts[0]);
  const gm = p && p.garments.find(x => x.uid === parts[1]);
  if (!gm) return;
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
          .then(res => { gm.img = res.url; render(); })
          .catch(() => uiAlert('Upload foto non riuscito.'));
      }, 'image/jpeg', 0.8);
    };
    img.src = reader.result;
  };
  reader.readAsDataURL(file);
});

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

function textOn(hex) {
  const r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
  return (r*.299 + g*.587 + b*.114) > 140 ? '#15171f' : '#E6E8F2';
}

/* Outfit completo (foto + nome + swap) mostrato dentro la card giorno, senza modale. */
function ofitSlots(dd, di) {
  let html = '';
  SLOT_ORDER.forEach(so => {
    const p = PARENTS.find(x => x.key === so.key);
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
      name = v ? p.presets[v.pi].l.split(' ·')[0] : wn.label;
      imgHtml = '<div class="ofit-ph" style="background:' + hexDim(p.color, .25) + ';color:' + p.color + '">' + wn.label + '</div>';
    }
    const catLbl = so.cat + (wn.layer ? ' · sotto' : (wn.reuse ? ' · 2° uso' : ''));
    const tag = wn.swappable ? 'button' : 'div';
    const attrs = wn.swappable ? ' type="button" data-swap="' + di + '|' + so.key + '" title="Tocca per cambiare capo"' : '';
    const badge = wn.swappable ? '<span class="ofit-swap">⇄</span>' : '';
    html +=
      '<' + tag + ' class="ofit' + (wn.swappable ? ' swap' : '') + '"' + attrs + '>' +
        badge +
        '<div class="ofit-img">' + imgHtml + '</div>' +
        '<span class="ofit-cat">' + catLbl + '</span>' +
        '<span class="ofit-name' + (wn.miss ? ' miss' : '') + '">' + name + '</span>' +
      '</' + tag + '>';
  });
  return html;
}

/* Vista calendario: griglia mese, lunedì-domenica, ogni cella tocca per il dettaglio. */
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

function openModal(di) {
  const dd = LAST[di];
  if (!dd) return;
  const dt = dateForDay(di);
  $('modalTitle').textContent = 'Outfit di ' + fmtDay.format(dt);
  const body = $('outfitBody');
  body.innerHTML = '';
  SLOT_ORDER.forEach(so => {
    const p = PARENTS.find(x => x.key === so.key);
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
      name = v ? p.presets[v.pi].l.split(' ·')[0] + ' n°' + wn.label.replace(/^\D+/, '') : wn.label;
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
      extra.innerHTML += '<div class="alert" style="margin:0;background:var(--amber-dim);border-color:rgba(240,178,82,.35);color:var(--amber)">〰 ' + b.count + ' capi stesi (giorno ' + b.dayN + '/' + b.total + '), pronti ' + fmtDay.format(dateForDay(b.ready - 1)) + '</div>';
    });
  }
  $('modalBg').hidden = false;
  document.body.style.overflow = 'hidden';
}
function closeModal() {
  $('modalBg').hidden = true;
  document.body.style.overflow = '';
}
$('modalClose').addEventListener('click', closeModal);
$('modalBg').addEventListener('click', e => { if (e.target === $('modalBg')) closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

/* ---------- dialog (sostituisce alert/confirm/prompt nativi) ---------- */
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
  if ($('modalBg').hidden) document.body.style.overflow = '';
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

/* ---------- profili ---------- */
$('saveProfile').addEventListener('click', () => {
  const cur = $('profileSel');
  const curName = cur.value ? (PROFS.find(p => p.id == cur.value) || {}).name : '';
  uiPrompt('Nome della programmazione:', curName || 'Estate ' + new Date().getFullYear()).then(name => {
    if (!name) return;
    api('/api/plans', 'POST', { name: name, state: serialize() })
      .then(p => renderProfiles(p.id))
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
      .then(() => renderProfiles(''))
      .catch(() => uiAlert('Errore nella cancellazione.'));
  });
});
$('profileSel').addEventListener('change', e => {
  const id = e.target.value;
  if (!id) return;
  api('/api/plans/' + id).then(p => {
    applyState(p.state);
    render();
    renderProfiles(id);
  }).catch(() => {});
});

$('genBtn').addEventListener('click', () => {
  const need = cycleDays();
  const righe = ['🧺 LISTA ARMADIO',
    '(dal ' + fmtDay.format(startDate()) + ' · bucato ogni ' + wash.value + 'gg, asciugatura ' + dry.value + 'gg, scorta +' + buf.value + ')', ''];
  PARENTS.forEach(p => {
    if (!p.active) { righe.push('— ' + p.name.toUpperCase() + ': esclusa in questo periodo'); return; }
    const cov = p.group === 'top' ? groupCoverage() : coverage(p);
    righe.push((cov >= need ? '✓ ' : '⚠ ') + p.name.toUpperCase() + ' — copre ' + cov + '/' + need + ' gg' + (p.group === 'top' ? ' (gruppo sopra)' : ''));
    p.garments.forEach(gm => righe.push('   ★ ' + gm.n));
    p.variants.forEach(v => {
      if (v.own > 0) righe.push('   ☐ ' + p.presets[v.pi].l.split(' ·')[0] + ' (generici): ' + v.own + (v.wears > 1 ? ' (' + v.wears + ' usi a capo)' : ''));
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
let calMode = 'swipe';
function setView(mode) {
  calMode = mode;
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
(function init() {
  const today = new Date();
  $('startDate').value = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2,'0') + '-' + String(today.getDate()).padStart(2,'0');
  render();
  api('/api/plans/auto').then(s => { if (s) { applyState(s); render(); } }).catch(() => {});
  renderProfiles('');
})();
</script>
</body>
</html>
