@extends('layouts.app')

@section('title', 'Armadio')

@section('content')

{{-- Pagina volutamente vuota: tutto il contenuto è stato spostato sulla rotta base "/".
     Rimane come spazio libero per una prossima sezione. --}}
<header class="page-head">
  <div class="eyebrow">Spazio libero</div>
  <h1>Pagina <em>vuota</em></h1>
  <p class="sub">Il piano del guardaroba ora vive sulla <a href="{{ route('home') }}" class="inline-link">pagina iniziale</a>. Questa rotta è rimasta libera per una prossima sezione.</p>
</header>

@endsection
