@extends('generators.layout')

<div class="h-screen w-screen bg-gradient-to-tr from-zinc-900 to-zinc-950 p-12 text-white overflow-hidden relative">
    <img class="absolute inset-0 blur-3xl scale-110 opacity-30" src="https://stratbox.app/images/maps/ancient.jpg">
    <div class="flex items-center justify-between relative z-10">
        <div class="flex items-center gap-12">
            @if ($series->event?->logo)
                <img
                    src="{{ $series->event?->logo }}"
                    class="aspect-square w-32 overflow-hidden rounded-2xl object-contain"
                >
            @endif
            <div>
                <div class="text-6xl font-bold tracking-tighter">Upcoming Match</div>
                <div class="mt-4 text-4xl opacity-50 tracking-tighter">
                    {{ $series->event?->name }}
                </div>
            </div>
        </div>
    </div>

    <div class="mt-24 flex items-center justify-center relative z-10">
        <div class="text-center">
            <div class="aspect-square h-[300px] w-[300px]">
                @if ($series->teamA->logo)
                    <div class="relative mb-6">
                        <img
                            src="{{ $series->teamA->logo }}"
                            class="z-10 aspect-square w-full object-contain"
                        >
                        <img
                            src="{{ $series->teamA->logo }}"
                            class="absolute inset-0 aspect-square w-full object-contain opacity-30 blur-2xl"
                        >
                    </div>
                @else
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-full object-contain opacity-25"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                        />
                    </svg>
                @endif
            </div>
            <div class="mt-16 text-6xl font-medium tracking-tighter">{{ $series->teamA?->name }}</div>
        </div>
        <div class="px-32 text-6xl font-medium tracking-tighter opacity-50">
            vs
        </div>
        <div class="text-center">
            <div class="aspect-square h-[300px] w-[300px]">
                @if ($series->teamB?->logo)
                    <div class="relative mb-6">
                        <img
                            src="{{ $series->teamB->logo }}"
                            class="z-10 aspect-square w-full object-contain"
                        >
                        <img
                            src="{{ $series->teamB->logo }}"
                            class="absolute inset-0 aspect-square w-full opacity-30 blur-2xl"
                        >
                    </div>
                @else
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-full object-contain opacity-25"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                        />
                    </svg>
                @endif
            </div>
            <div class="mt-16 text-6xl font-medium tracking-tighter">{{ $series->teamB?->name ?? $series->team_b_name }}
            </div>
        </div>
    </div>

    <div class="mt-20 flex items-end justify-between relative z-10">
        <div class="text-4xl opacity-50 tracking-tighter">
            <p> {{ Illuminate\Support\Carbon::parse($series->start_date)->format('l jS M') }} at
                {{ Illuminate\Support\Carbon::parse($series->start_date)->format('H:i') }}</p>
        </div>
        <img
            src="/bsite.svg"
            class="w-48"
            alt=""
        >
    </div>
</div>
