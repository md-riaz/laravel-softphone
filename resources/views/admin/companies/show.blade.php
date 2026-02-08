@extends('layouts.app')
@section('title', 'Company Details')
@section('content')
<h1>{{ $company->name }}</h1>
<p>Domain: {{ $company->domain ?? 'N/A' }}</p>
<p>Active: {{ $company->is_active ? 'Yes' : 'No' }}</p>
@endsection
