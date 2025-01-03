{{-- dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-gray-500 text-sm">Total Penjualan Hari Ini</h3>
            <div class="mt-2 flex items-baseline">
                <span class="text-3xl font-bold text-gray-900">Rp 500.000</span>
            </div>
        </div>
        <!-- Add other dashboard cards -->
    </div>
@endsection
