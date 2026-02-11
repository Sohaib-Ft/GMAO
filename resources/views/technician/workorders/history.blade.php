@extends('layouts.app')

@section('title', 'Historique Interventions')
@section('header', 'Historique de mes travaux')

@section('content')
<div class="space-y-6">
    <div class="flex justify-start items-center gap-4">
     
    </div>

    <!-- History Table -->
    <x-work-orders.table :workOrders="$workOrders" role="technician_history" />
</div>
</div>
@endsection
