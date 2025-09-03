<x-filament-panels::page.simple>
    <style>
        .fi-simple-layout .fi-logo img {
            border-radius: 50% !important;
            object-fit: cover !important;
            aspect-ratio: 1 !important;
            width: 6rem !important;
            height: 6rem !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            margin-bottom: 1rem !important;
        }
        
        .fi-simple-layout .fi-logo {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-bottom: 2rem !important;
        }
        
        .fi-simple-main {
            max-width: 400px !important;
        }
    </style>
    
    @if (filament()->hasLogin())
        <x-slot name="heading">
            {{ __('filament-panels::pages/auth/login.heading') }}
        </x-slot>

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.before') }}

        <x-filament-panels::form wire:submit="authenticate">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.after') }}
    @endif
</x-filament-panels::page.simple>
