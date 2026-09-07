@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Transport Management</div>
        <h1>Import Excel (New Format)</h1>
        <div class="sub">Import 2-sheet Excel format with invoice and trip details</div>
      </div>
    </div>
    
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="panel">
      <div class="panel-title">Import Excel File</div>
      <div class="panel-sub">Upload Excel file with 2 sheets (Invoice details + Trip details)</div>
      
      <form action="{{ route('transport.import-new-format.process') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        
        <div class="mb-4">
          <label for="import_file" class="form-label">Select Excel File</label>
          <input type="file" class="form-control" name="import_file" id="import_file" accept=".xlsx,.xls" required>
          <div class="form-text">Accepts .xlsx and .xls files with 2 sheets</div>
        </div>
        
        <div class="alert alert-info">
          <strong>Excel Format Requirements:</strong>
          <ul class="mb-0 mt-2">
            <li><strong>Sheet 1:</strong> Invoice details (company info, billing month, GL number, etc.)</li>
            <li><strong>Sheet 2:</strong> Trip details (Sr, Date, Load ID, Freight Bill NO, Vhl No, GP#, Drop/Delivery Point, Vhl, Km, Rate, FRT)</li>
          </ul>
        </div>
        
        <button type="submit" class="btn btn-navy">
          <i class="bi bi-upload me-1"></i> Import Excel
        </button>
      </form>
    </div>
    
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>
@endsection