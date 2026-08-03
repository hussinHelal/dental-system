@props(['icon' => 'bi-inbox', 'message' => null])

<div class="empty-state">
    <i class="bi {{ $icon }}" style="font-size: 2.5rem;"></i>
    <p class="mt-2 mb-0">{{ $message ?? __('messages.no_results') }}</p>
</div>
