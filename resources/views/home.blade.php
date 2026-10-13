@extends('layouts.app')

@section('title', 'Home | Hap &amp; Hop')

@section('content')
    <section class="hero">
        <h1>Hap &amp; Hop</h1>
        <p>Catering, broodjes en pakketten in Houten en omgeving.</p>
        <a class="knop" href="{{ route('pakketten.index') }}">Bekijk onze pakketten</a>
        <a class="knop knop-licht" href="{{ route('contact') }}">Neem contact op</a>
    </section>
@endsection