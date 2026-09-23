
@props([
    "type" => "button",
    "disabled" => false,
    "icon" => null,
])

<button 
    type="{{ $type }}" 
    {{ $attributes->merge([
        "class" => "inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-brand bg-[#001A72] text-white text-sm font-semibold shadow-xs hover:bg-[#001250] active:bg-[#000A30] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#001A72] transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed select-none"
    ]) }}
    @if($disabled) disabled @endif
>
    @if($icon)
        <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
    @endif
    <span>{{ $slot }}</span>
</button>
