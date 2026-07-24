@php
  $nav = [
    ['route' => 'home',      'icon' => '📊', 'label' => 'Piano'],
    ['route' => 'categorie', 'icon' => '👕', 'label' => 'Categorie'],
    ['route' => 'storico',   'icon' => '🕘', 'label' => 'Storico'],
    ['route' => 'bagaglio',  'icon' => '🧳', 'label' => 'Bagaglio'],
    ['route' => 'armadio',   'icon' => '🚪', 'label' => 'Armadio'],
  ];
  $current = request()->route()?->getName();
@endphp
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Armadio') · Future Plus</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/app.css?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>

<nav class="nav" aria-label="Navigazione principale">
  <div class="nav-inner">
    <span class="nav-brand">Armadio</span>
    <div class="nav-links">
      @foreach ($nav as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-link{{ $current === $item['route'] ? ' active' : '' }}"
           @if ($current === $item['route']) aria-current="page" @endif>
          <span class="nav-ico" aria-hidden="true">{{ $item['icon'] }}</span>
          <span class="nav-lbl">{{ $item['label'] }}</span>
        </a>
      @endforeach
    </div>
  </div>
</nav>

<div class="wrap">
  @yield('content')
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

@stack('modals')

<script src="/js/defaults.js?v={{ filemtime(public_path('js/defaults.js')) }}"></script>
<script src="/js/core.js?v={{ filemtime(public_path('js/core.js')) }}"></script>
@stack('scripts')
</body>
</html>
