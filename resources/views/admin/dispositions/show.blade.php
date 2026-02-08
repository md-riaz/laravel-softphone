@extends('layouts.app')
@section('title', 'Disposition Details')
@section('content')
<h1>{{ $disposition->name }}</h1>
<p>Color: <span style="color:{{ $disposition->color }}">{{ $disposition->color }}</span></p>
<p>Active: {{ $disposition->is_active ? 'Yes' : 'No' }}</p>
@endsection
