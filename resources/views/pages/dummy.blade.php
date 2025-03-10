@extends('layouts.app')

@section('title', $title)

@section('header', $title)

@section('content')
    <div class="min-h-[60vh] flex flex-col items-center justify-center">
        <div class="text-center space-y-4">
            <div class="bg-amber-100 text-amber-700 p-8 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h2 class="text-2xl font-bold mb-2">Dalam Pengembangan</h2>
                <p class="text-lg">{{ $message }}</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center space-x-2 text-amber-600 hover:text-amber-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>
@endsection
