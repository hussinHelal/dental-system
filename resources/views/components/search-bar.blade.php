@props(['placeholder' => null, 'value' => null])

<form method="GET" class="d-flex flex-grow-1" role="search" style="max-width: 420px;">
    @foreach(request()->except(['q', 'page']) as $key => $val)
        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
    @endforeach
    <div class="input-group">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input
            type="search"
            name="q"
            class="form-control"
            placeholder="{{ $placeholder ?? __('messages.search_placeholder') }}"
            value="{{ $value ?? request('q') }}"
        >
    </div>
</form>
