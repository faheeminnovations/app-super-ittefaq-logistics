@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Monthly Rate Management</h4>
                    <a href="{{ route('transport.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('transport.rate-management.update') }}">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Vehicle Category</th>
                                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                            <th>{{ $month }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vehicleCategories as $category)
                                        <tr>
                                            <td class="fw-bold">{{ $category }}</td>
                                            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $monthNumber => $month)
                                                <?php
                                                $rate = 0;
                                                if (isset($monthlyRates[2026])) {
                                                    foreach ($monthlyRates[2026] as $monthlyRate) {
                                                        if ($monthlyRate->vehicle_category === $category && 
                                                            $monthlyRate->billing_month_number == $monthNumber + 1) {
                                                            $rate = $monthlyRate->rate_per_km;
                                                            break;
                                                        }
                                                    }
                                                }
                                                ?>
                                                <td>
                                                    <input type="number" step="0.01" 
                                                           name="rates[2026][{{ $monthNumber + 1 }}][{{ $category }}]" 
                                                           class="form-control form-control-sm" 
                                                           value="{{ $rate }}" 
                                                           placeholder="0.00">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Rates
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