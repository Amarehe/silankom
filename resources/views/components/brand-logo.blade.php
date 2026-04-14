@php
    $isLoginPage = request()->routeIs('filament.admin.auth.login');
@endphp

@if($isLoginPage)
    {{-- Login Page: Logo + full name --}}
    <div style="display: inline-flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
        <img
            src="{{ asset('images/logo.png') }}"
            alt="Logo SILANKOM"
            style="height: 5rem; width: auto; max-width: 5rem; object-fit: contain; flex-shrink: 0;"
        />
        <span style="font-size: 1.2rem; font-weight: 700; line-height: 1.3; color: inherit; max-width: 18rem;">
            Sistem Informasi Bag Komlek Lemhannas RI
        </span>
    </div>
@else
    {{-- Sidebar: Logo + SILANKOM --}}
    <div style="display: inline-flex; align-items: center; gap: 0.5rem;">
        <img
            src="{{ asset('images/logo.png') }}"
            alt="Logo SILANKOM"
            style="height: 3.5rem; width: auto; max-width: 3.5rem; object-fit: contain; flex-shrink: 0;"
        />
        <span style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.025em; color: inherit; white-space: nowrap;">
            SIKOMLEK
        </span>
    </div>
@endif
