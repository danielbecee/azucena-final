@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="/assets/css/employee.css">
@endpush

@push('scripts')
  <script src="/assets/js/employee.js"></script>
@endpush

@section('sidebar')
  @include('partials.sidebar-employee')
@endsection
