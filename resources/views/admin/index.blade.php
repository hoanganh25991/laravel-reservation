@extends('layouts.app')

@section('content')
    @include('auth.register')
@endsection

@push('script')
<script src="{{ url('js/vue.min.js') }}"></script>
<script></script>
@endpush