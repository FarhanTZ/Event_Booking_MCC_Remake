
@props([
    "status" => "available", // available, locked, confirmed, revised, rejected, buffer
    "pulse" => false,
])

@php
    $config = match($status) {
        "confirmed", "approved", "active" => [
            "class" => "bg-emerald-50 text-emerald-700 border-emerald-200",
            "dot" => "bg-emerald-600",
            "label" => "Disetujui"
        ],
        "locked", "in_progress", "awaiting_marketing" => [
            "class" => "bg-amber-50 text-amber-700 border-amber-200",
            "dot" => "bg-amber-500",
            "label" => "Terkunci 15m"
        ],
        "revised", "revision" => [
            "class" => "bg-indigo-50 text-indigo-700 border-indigo-200",
            "dot" => "bg-indigo-600",
            "label" => "Perlu Revisi"
        ],
        "rejected", "incident" => [
            "class" => "bg-rose-50 text-rose-700 border-rose-200",
            "dot" => "bg-rose-600",
            "label" => "Ditolak / KTP Ditahan"
        ],
        "buffer" => [
            "class" => "bg-slate-100 text-slate-600 border-slate-200",
            "dot" => "bg-slate-400",
            "label" => "Buffer 30m"
        ],
        default => [
            "class" => "bg-slate-100 text-slate-700 border-slate-200",
            "dot" => "bg-emerald-500",
            "label" => "Tersedia"
        ]
    };
@endphp

<span {{ $attributes->merge(["class" => "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border " . $config["class"]]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $config["dot"] }} {{ $pulse ? "animate-pulse" : "" }}"></span>
    <span>{{ $slot->isNotEmpty() ? $slot : $config["label"] }}</span>
</span>
