
@props([
    "title" => null,
    "subtitle" => null,
    "headerAction" => null,
    "footer" => null,
])

<div {{ $attributes->merge(["class" => "bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden flex flex-col"]) }}>
    @if($title || $headerAction)
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-sm text-slate-900 leading-tight">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if($headerAction)
                <div>{{ $headerAction }}</div>
            @endif
        </div>
    @endif

    <div class="p-5 flex-1">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            {{ $footer }}
        </div>
    @endif
</div>
