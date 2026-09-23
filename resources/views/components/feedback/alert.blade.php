
@props([
    "type" => "info", // info, success, warning, error
    "title" => null,
    "dismissible" => false,
])

@php
$config = match ($type) {
    "success" => [
        "container" => "bg-emerald-50 border-emerald-200 text-emerald-900",
        "icon" => "check-circle",
        "iconClass" => "text-emerald-600",
    ],
    "warning" => [
        "container" => "bg-amber-50 border-amber-200 text-amber-900",
        "icon" => "alert-triangle",
        "iconClass" => "text-amber-600",
    ],
    "error", "danger" => [
        "container" => "bg-rose-50 border-rose-200 text-rose-900",
        "icon" => "alert-circle",
        "iconClass" => "text-rose-600",
    ],
    default => [
        "container" => "bg-[#EEF2FF] border-[#C7D2FE] text-[#001A72]",
        "icon" => "info",
        "iconClass" => "text-[#001A72]",
    ],
};
@endphp

<div 
    x-data="{ show: true }" 
    x-show="show" 
    {{ $attributes->merge(["class" => "rounded-brand border p-4 flex items-start gap-3 text-xs leading-relaxed " . $config["container"]]) }}
>
    <i data-lucide="{{ $config["icon"] }}" class="w-4 h-4 shrink-0 mt-0.5 {{ $config["iconClass"] }}"></i>
    <div class="flex-1">
        @if($title)
            <strong class="font-bold block text-xs uppercase tracking-wider mb-0.5">{{ $title }}</strong>
        @endif
        <div>{{ $slot }}</div>
    </div>
    @if($dismissible)
        <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 p-0.5 rounded-sm">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    @endif
</div>
