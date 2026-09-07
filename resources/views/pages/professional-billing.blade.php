@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Professional Billing</div>
        <h1>Professional Billing System</h1>
        <div class="sub">Manage all professional billing types in one place</div>
      </div>
    </div>

    <!-- Billing Type Cards -->
    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 billing-card" onclick="window.location.href='/professional-billing/supply-chain'">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-box bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-box-seam fs-3"></i>
              </div>
              <div class="ms-3">
                <h5 class="card-title mb-0">Supply Chain</h5>
                <small class="text-muted">سپلائی چین</small>
              </div>
            </div>
            <p class="card-text text-muted">Manage supply chain billing with load ID, gate pass, and vehicle category tracking.</p>
            <div class="badge bg-primary">سیریل نمبر/تاریخ/گاڑی نمبر/لوڈ آئی ڈی/گیٹ پاس نمبر</div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 billing-card" onclick="window.location.href='/professional-billing/branding'">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-box bg-success bg-opacity-10 text-success">
                <i class="bi bi-badge-ad fs-3"></i>
              </div>
              <div class="ms-3">
                <h5 class="card-title mb-0">Branding</h5>
                <small class="text-muted">بائر،بریڈنگ</small>
              </div>
            </div>
            <p class="card-text text-muted">Track branding billing with kilometers and rate calculations.</p>
            <div class="badge bg-success">سیریل نمبر، تاریخ،ڈلیوری پوائنٹ /کلومیٹر/ریٹ /رقم</div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 billing-card" onclick="window.location.href='/professional-billing/marketing-development'">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-box bg-info bg-opacity-10 text-info">
                <i class="bi bi-graph-up-arrow fs-3"></i>
              </div>
              <div class="ms-3">
                <h5 class="card-title mb-0">Marketing Development</h5>
                <small class="text-muted">بائر،مارکیٹنگ ڈویلپمنٹ</small>
              </div>
            </div>
            <p class="card-text text-muted">Manage marketing development billing with distance and rate tracking.</p>
            <div class="badge bg-info">Development Billing</div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 billing-card" onclick="window.location.href='/professional-billing/spr'">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-box bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-building fs-3"></i>
              </div>
              <div class="ms-3">
                <h5 class="card-title mb-0">SPR</h5>
                <small class="text-muted">بائر،ایس۔پی۔آر</small>
              </div>
            </div>
            <p class="card-text text-muted">Handle SPR billing with delivery point and rate management.</p>
            <div class="badge bg-warning">SPR Billing</div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 billing-card" onclick="window.location.href='/professional-billing/cement-pakistan'">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-box bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-building-fill fs-3"></i>
              </div>
              <div class="ms-3">
                <h5 class="card-title mb-0">Cement Pakistan</h5>
                <small class="text-muted">سیمنٹ پاکستان</small>
              </div>
            </div>
            <p class="card-text text-muted">Manage Cement Pakistan billing with delivery tracking.</p>
            <div class="badge bg-danger">Cement Billing</div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 billing-card" onclick="window.location.href='/professional-billing/open-market-work'">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-box bg-secondary bg-opacity-10 text-secondary">
                <i class="bi bi-shop fs-3"></i>
              </div>
              <div class="ms-3">
                <h5 class="card-title mb-0">Open Market Work</h5>
                <small class="text-muted">اوپن مارکیٹ کام</small>
              </div>
            </div>
            <p class="card-text text-muted">Track open market work with driver, customer, and expense management.</p>
            <div class="badge bg-secondary">تاریخ،گاڑی نمبر،سیریل نمبر،ڈرائیور نام، کسٹمر نام/لوڈنگ پوائنٹ،ان لوڈنگ،رینٹ،خرچہ جات</div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 billing-card" onclick="window.location.href='/professional-billing/seed-supply'">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-box bg-success bg-opacity-10 text-success">
                <i class="bi bi-seedling fs-3"></i>
              </div>
              <div class="ms-3">
                <h5 class="card-title mb-0">Seed Supply</h5>
                <small class="text-muted">بائر سیڈ سپلائی</small>
              </div>
            </div>
            <p class="card-text text-muted">Manage seed supply billing with guarantor and payment tracking.</p>
            <div class="badge bg-success">تاریخ،گاڑی نمبر،ڈرائیور نام، فون نمبر، تعداد،ڈلیوری پوائنٹ،ضمانتی،کرایہ ادا کیا،ادائیگی تفصیل،رسیونگ تفصیل،سٹیٹس</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .billing-card {
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      border: 2px solid transparent;
    }
    
    .billing-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      border-color: var(--primary);
    }
    
    .icon-box {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
@endsection
