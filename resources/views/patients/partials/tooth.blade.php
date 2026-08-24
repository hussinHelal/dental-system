@php
$status = $record?->status ?? 'healthy';
$hasBraces = str_contains((string) ($record?->notes ?? ''), '[braces]');
$classes = 'tooth-wrapper '.$status.($hasBraces ? ' has-braces' : '');
@endphp
<div class="{{ $classes }}"
    data-tooth="{{ $number }}"
    data-label="{{ $label ?? $number }}"
     data-status="{{ $status }}"
     data-treatment="{{ $record?->treatment_id ?? '' }}"
     data-notes="{{ $record?->notes ?? '' }}"
    title="{{ __('messages.tooth') }} {{ $label ?? $number }} — {{ __('messages.status_'.$status) }}{{ $hasBraces ? ' + ' . __('messages.status_braces') : '' }}">
    <svg class="tooth-svg" viewBox="0 0 40 52" xmlns="http://www.w3.org/2000/svg">
        <!-- Realistic tooth shape -->
        <path d="M20 2 
                 C26 2, 32 6, 34 12 
                 C36 18, 35 24, 33 30 
                 C32 36, 30 42, 28 48 
                 C26 50, 24 50, 22 48 
                 C21 46, 20 44, 20 42 
                 C20 44, 19 46, 18 48 
                 C16 50, 14 50, 12 48 
                 C10 42, 8 36, 7 30 
                 C5 24, 4 18, 6 12 
                 C8 6, 14 2, 20 2 Z" 
              fill="#fff" 
              stroke="#666" 
              stroke-width="1.5"
              stroke-linejoin="round"/>
        
        <!-- Root lines for molars -->
        @if(in_array($number, [1,2,3,14,15,16,17,18,19,30,31,32]))
            <path d="M13 35 L13 48 M20 38 L20 48 M27 35 L27 48" 
                  stroke="#999" 
                  stroke-width="0.8" 
                  fill="none"
                  opacity="0.5"/>
        @endif
        <!-- Crown indicator -->
        @if($status === 'crown')
            <ellipse cx="20" cy="16" rx="10" ry="8" fill="none" stroke="#ff9800" stroke-width="1.5" opacity="0.6"/>
        @endif
        <!-- Root canal indicator -->
        @if($status === 'root_canal')
            <line x1="20" y1="10" x2="20" y2="45" stroke="#03a9f4" stroke-width="1.5" stroke-dasharray="3,2"/>
        @endif
        <!-- Extraction X mark -->
        @if($status === 'extracted')
            <line x1="10" y1="10" x2="30" y2="40" stroke="#666" stroke-width="2"/>
            <line x1="30" y1="10" x2="10" y2="40" stroke="#666" stroke-width="2"/>
        @endif
        <!-- Fracture line -->
        @if($status === 'fractured')
            <path d="M8 20 L15 25 L12 32 L20 35" stroke="#c62828" stroke-width="1.5" fill="none"/>
        @endif
        <!-- Abscess dot -->
        @if($status === 'abscess')
            <circle cx="20" cy="45" r="3" fill="#ad1457" opacity="0.7"/>
        @endif
        <!-- Braces bracket -->
        @if($status === 'braces' || $hasBraces)
            <rect x="8" y="18" width="24" height="6" rx="2" fill="none" stroke="#3f51b5" stroke-width="1.5"/>
            <line x1="14" y1="18" x2="14" y2="24" stroke="#3f51b5" stroke-width="1"/>
            <line x1="20" y1="18" x2="20" y2="24" stroke="#3f51b5" stroke-width="1"/>
            <line x1="26" y1="18" x2="26" y2="24" stroke="#3f51b5" stroke-width="1"/>
        @endif
    </svg>
    
    <span class="tooth-number">{{ $label ?? $number }}</span>
</div>