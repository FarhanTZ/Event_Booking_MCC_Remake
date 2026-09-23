
@props([
    "type" => "button",
    "disabled" => false,
    "icon" => null,
])

<button 
    type="{{ $type }}" 
    {{ $attributes->merge([
        "class" => "inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 active:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed select-none"
    ]) }}
    @if($disabled) disabled @endif
>
    @if($icon)
        <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0 text-slate-500"></i>
    @endif
    <span>{{ $slot }}</span>
</button>
