@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Trip Log Details #{{ $tripLog->sr }}</h4>
                    <div>
                        <a href="{{ route('trip-logs.print', $tripLog) }}" class="btn btn-secondary btn-sm" target="_blank">
                            <i class="bi bi-printer"></i> Print
                        </a>
                        <a href="{{ route('trip-logs.edit', $tripLog) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('trip-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Serial Number</th>
                                    <td>{{ $tripLog->sr }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $tripLog->date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle No</th>
                                    <td>{{ $tripLog->vehicle_no }}</td>
                                </tr>
                                <tr>
                                    <th>GP#</th>
                                    <td>{{ $tripLog->gp_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Delivery Point</th>
                                    <td>{{ $tripLog->delivery_point }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Category</th>
                                    <td>{{ $tripLog->vehicle_category }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>KM</th>
                                    <td>{{ number_format($tripLog->km, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Rate</th>
                                    <td>{{ number_format($tripLog->rate, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>FRT (Freight)</th>
                                    <td><strong>{{ number_format($tripLog->frt, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Fuel</th>
                                    <td>{{ $tripLog->fuel ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Driver Name</th>
                                    <td>{{ $tripLog->driver_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Business Category</th>
                                    <td>{{ $tripLog->business_category }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($tripLog->business_category === 'Buyer Supply Chain')
                        <hr>
                        <h5>Buyer Supply Chain Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Load ID</th>
                                        <td>{{ $tripLog->load_id ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Cluster</th>
                                        <td>{{ $tripLog->cluster ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($tripLog->business_category === 'Open Market Work')
                        <hr>
                        <h5>Open Market Work Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Customer Name</th>
                                        <td>{{ $tripLog->customer_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Loading Point</th>
                                        <td>{{ $tripLog->loading_point ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Unloading Point</th>
                                        <td>{{ $tripLog->unloading_point ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Rent</th>
                                        <td>{{ $tripLog->rent ? number_format($tripLog->rent, 2) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Expenses</th>
                                        <td>{{ $tripLog->expenses ? number_format($tripLog->expenses, 2) : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($tripLog->business_category === 'Buyer Seed Supply')
                        <hr>
                        <h5>Buyer Seed Supply Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Phone No</th>
                                        <td>{{ $tripLog->phone_no ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Quantity</th>
                                        <td>{{ $tripLog->quantity ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Guarantor</th>
                                        <td>{{ $tripLog->guarantor ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Rent Paid</th>
                                        <td>{{ $tripLog->rent ? number_format($tripLog->rent, 2) : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Payment Detail</th>
                                        <td>{{ $tripLog->payment_detail ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Receiving Detail</th>
                                        <td>{{ $tripLog->receiving_detail ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $tripLog->status ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection