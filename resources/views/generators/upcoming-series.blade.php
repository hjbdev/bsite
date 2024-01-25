@extends('generators.layout')

<div class="w-screen h-screen bg-gradient-to-tr from-zinc-800 to-zinc-950 text-white p-24">
    <div class="flex justify-between items-start">
        <div class="flex gap-12">
            @if ($series->event?->logo)
                <img src="{{ $series->event?->logo }}" class="w-32 rounded-2xl overflow-hidden aspect-square object-contain mb-6">
            @endif
            <div>
                <div class="text-8xl font-bold tracking-tighter">Upcoming Match</div>
                <div class="text-5xl mt-4 opacity-50">
                    {{ $series->event?->name }}
                </div>
            </div>
        </div>
        <div class="text-4xl text-right tracking-wide uppercase opacity-50">
            <p> {{ Illuminate\Support\Carbon::parse($series->start_date)->format('D jS M') }}</p>
            <p>{{ Illuminate\Support\Carbon::parse($series->start_date)->format('H:i') }}</p>
        </div>
    </div>
    <div class="flex items-center mt-16">
        <div class="text-center">
            <div class="w-[350px] h-[350px] aspect-square">
                @if ($series->teamA->logo)
                <img src="{{ $series->teamA->logo }}" class="w-full aspect-square object-contain mb-6">
                @else
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full object-contain opacity-25">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                @endif
            </div>
            <div class="text-6xl tracking-tighter font-medium mt-6">{{ $series->teamA?->name }}</div>
        </div>
        <div class="px-24 text-6xl tracking-tighter font-medium opacity-50">
            vs
        </div>
        <div class="text-center">
            <div class="w-[350px] h-[350px] aspect-square">
                @if ($series->teamB?->logo)
                <img src="{{ $series->teamB->logo }}" class="w-full aspect-square object-contain mb-6">
                @else
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full object-contain opacity-25">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                @endif
            </div>
            <div class="text-6xl tracking-tighter font-medium mt-6">{{ $series->teamB?->name ?? $series->team_b_name }}</div>
        </div>
    </div>
</div>