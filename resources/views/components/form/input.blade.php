
@props([
    "label" => null,
    "name" => "",
    "type" => "text",
    "placeholder" => "",
    "value" => "",
    "required" => false,
    "readonly" => false,
    "icon" => null,
    "help" => null,
    "error" => null,
])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            {{ $label }}
            @if($required) <span class="text-rose-600">*</span> @endif
        </label>
    @endif

    <div class="relative rounded-brand">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
            </div>
        @endif

        <input 
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}"
            @if($required) required @endif
            @if($readonly) readonly @endif
            {{ $attributes->merge([
                "class" => "block w-full " . ($icon ? "pl-10 " : "px-3 ") . "py-2.5 sm:text-sm rounded-brand border " . 
                ($errors->has($name) || $error ? "border-rose-300 text-rose-900 focus:border-rose-600 focus:ring-rose-600 " : "border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] ") .
                ($readonly ? "bg-slate-50 cursor-not-allowed " : "bg-white ") .
                "placeholder:text-slate-400 transition-colors"
            ]) }}
        >
    </div>

    @if($help && !$errors->has($name) && !$error)
        <p class="mt-1 text-[11px] text-slate-500">{{ $help }}</p>
    @endif

    @error($name)
        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
            <span>{{ $message }}</span>
        </p>
    @else
        @if($error)
            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                <span>{{ $error }}</span>
            </p>
        @endif
    @enderror
</div>
