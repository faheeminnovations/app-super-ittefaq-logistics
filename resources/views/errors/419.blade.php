@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="panel text-center" style="max-width: 500px; margin: 50px auto; padding: 40px;">
        <div style="font-size: 60px; color: #dc3545; margin-bottom: 20px;">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h2 style="color: var(--navy-900); margin-bottom: 15px;">Page Expired</h2>
        <p style="color: var(--muted); margin-bottom: 30px;">
            Your session has expired due to inactivity. Please refresh the page to continue.
        </p>
        <a href="{{ url()->previous() }}" class="btn btn-primary">
            <i class="bi bi-arrow-clockwise me-1"></i> Go Back
        </a>
        <a href="{{ url('/warehouse-trips') }}" class="btn btn-outline-secondary ms-2">
            <i class="bi bi-house me-1"></i> Go to Dashboard
        </a>
    </div>
</div>

<script>
// Auto-refresh after 3 seconds
setTimeout(function() {
    window.location.href = "{{ url()->previous() }}";
}, 3000);
</script>
@endsection
