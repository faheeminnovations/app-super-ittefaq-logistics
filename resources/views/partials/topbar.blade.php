<!-- Topbar extracted from original HTML -->
<div id="topbar">
  <button class="btn btn-sm d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
    <i class="bi bi-list fs-4"></i>
  </button>
  <div class="ms-auto d-flex align-items-center gap-2">
    <!-- Messages -->
    <button class="topbar-icon-btn position-relative">
      <i class="bi bi-envelope"></i>
      <span class="dot"></span>
    </button>
    
    <!-- User Menu -->
    <div class="dropdown">
      <div class="user-chip" data-bs-toggle="dropdown" style="cursor: pointer;">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AU', 0, 2)) }}</div>
        <div>
          <div style="font-size:12.5px;font-weight:700;color:var(--navy-900);">{{ auth()->user()->name ?? 'Admin User' }}</div>
          <div style="font-size:10.5px;color:var(--muted);">{{ ucfirst(auth()->user()->role ?? 'Administrator') }}</div>
        </div>
        <i class="bi bi-chevron-down" style="font-size:11px;color:var(--muted);"></i>
      </div>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
        @if(auth()->user()->isAdmin())
        <li><a class="dropdown-item" href="{{ url('/settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
        @endif
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}" id="logoutForm">
            @csrf
          </form>
          <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

