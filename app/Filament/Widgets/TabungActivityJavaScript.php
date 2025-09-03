<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class TabungActivityJavaScript extends Widget
{
    protected string $view = 'filament.widgets.tabung-activity-javascript';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static bool $isLazy = false;
    
    public function getViewData(): array
    {
        return [
            'widgetId' => 'tabung-activity-js-' . uniqid(),
        ];
    }
}
