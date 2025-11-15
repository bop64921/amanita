@extends('layouts.landing')

@section('title', 'Services')
    
@section('content')
<h1>Services</h1>
@component('_components.card')
@slot ('title', 'Service 1')
@slot ('content', 'Lorem Ipsum')
    
@endcomponent

@component('_components.card')
@slot ('title', 'Service 2')
@slot ('content', 'Lorem Ipsum 2')
    
@endcomponent

@endsection