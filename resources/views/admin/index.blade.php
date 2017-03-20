@extends('layouts.app')

@section('content')
    {{--current id = 5--}}
    <!-- Static navbar -->
    <div id="admin-step-container">
        @include('admin.navigator')
    </div>
@endsection

@push('script')
<script src="{{ url('js/vue.min.js') }}"></script>
<script></script>
@endpush