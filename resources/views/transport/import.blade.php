@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Import Excel Data</h4>
                    <a href="{{ route('transport.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Excel Format:</strong> Your Excel file should have the same format as the existing SIMG DEPALPUR spreadsheet with columns: Sr, Date, Vhl No, GP#, Drop/Delivery Point, Vhl, Km, Rate, FRT, FUEL, DRIVER
                    </div>

                    <form method="POST" action="{{ route('transport.import-excel') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="excel_file" class="form-label">Excel File *</label>
                                <input type="file" class="form-control" id="excel_file" name="excel_file" 
                                       accept=".xlsx,.xls" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="month" class="form-label">Billing Month *</label>
                                <select class="form-select" id="month" name="month" required>
                                    <option value="">Select Month</option>
                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}-2026">{{ $month }}-2026</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="year" class="form-label">Year *</label>
                                <input type="number" class="form-control" id="year" name="year" 
                                       value="2026" required>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Import Data
                            </button>
                            <a href="{{ route('transport.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection