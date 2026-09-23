
@props([
    "name" => "modal",
    "show" => false,
    "maxWidth" => "md", // sm, md, lg, xl
    "title" => null,
])

@php
$maxWidthClass = match ($maxWidth) {
    "sm" => "sm:max-w-sm",
    "md" => "sm:max-w-md",
    "lg" => "sm:max-w-lg",
    "xl" => "sm:max-w-xl",
    default => "sm:max-w-md",
};
@endphp

<div
    x-data="{ show: @js($show) }"
    x-show="show"
    x-on:open-modal.window="$event.detail == \"{{ $name }}\" ? show = true : null"
    x-on:close-modal.window="$event.detail == \"{{ $name }}\" ? show = false : null"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
>
    <!-- Solid clean backdrop without bloated blur -->
    <div 
        x-show="show" 
        x-transition:enter="ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 transition-opacity"
        x-on:click="show = false"
    ></div>

    <div 
        x-show="show" 
        x-transition:enter="ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white rounded-brand border border-slate-200 shadow-elevated overflow-hidden w-full {{ $maxWidthClass }} z-10 transform transition-all"
    >
        @if($title)
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-900 leading-tight">{{ $title }}</h3>
                <button type="button" x-on:click="show = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-md transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        <div class="p-5">
            {{ $slot }}
        </div>

        @if(isset($footer))
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
