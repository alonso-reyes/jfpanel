@push('head')
    <link
        href="/favicon.ico"
        id="favicon"
        rel="icon"
    >
@endpush

<div class="h2 d-flex align-items-center">
    @auth
        <x-orchid-icon path="bs.house" class="d-inline d-xl-none"/>
    @endauth

    <p>
        {{ config('app.name') }}
    </p>
</div>