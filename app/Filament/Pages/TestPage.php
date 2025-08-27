<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TestPage extends Page
{
    protected static string $view = 'filament.pages.test-page';
    
    public static function getNavigationLabel(): string
    {
        return 'Test';
    }
    
    public function getTitle(): string
    {
        return 'Test Page';
    }
}
