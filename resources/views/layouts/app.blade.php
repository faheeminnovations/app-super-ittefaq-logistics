<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Super Ittefaq Logistics') }}</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Roboto+Mono:wght@500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="{{ asset('assets/style.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body>
  @include('partials.sidebar')
  <div id="main">
    @include('partials.topbar')

    <div class="page-wrap">
      @yield('content')
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <script>
  // Clear any cached errors and fix background request issues
  window.addEventListener('error', function(e) {
    console.error('Global error caught:', e.message);
    // Prevent error messages from showing from other pages
    if (e.message.includes('vehicle') || e.message.includes('driver') || e.message.includes('trip')) {
      e.preventDefault();
      return false;
    }
  });
  </script>
  
  @stack('scripts')
</body>
</html>
