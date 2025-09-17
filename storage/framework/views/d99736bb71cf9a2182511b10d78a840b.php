<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'active' => false,
    'activeChildItems' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'badgeTooltip' => null,
    'childItems' => [],
    'first' => false,
    'grouped' => false,
    'icon' => null,
    'last' => false,
    'shouldOpenUrlInNewTab' => false,
    'sidebarCollapsible' => true,
    'subGrouped' => false,
    'url',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'active' => false,
    'activeChildItems' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'badgeTooltip' => null,
    'childItems' => [],
    'first' => false,
    'grouped' => false,
    'icon' => null,
    'last' => false,
    'shouldOpenUrlInNewTab' => false,
    'sidebarCollapsible' => true,
    'subGrouped' => false,
    'url',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $sidebarCollapsible = $sidebarCollapsible && filament()->isSidebarCollapsibleOnDesktop();
?>

<li
    <?php echo e($attributes->class([
            'fi-sidebar-item',
            'fi-active' => $active,
            'fi-sidebar-item-has-active-child-items' => $activeChildItems,
            'fi-sidebar-item-has-url' => filled($url),
        ])); ?>

>
    <a
        <?php echo e(\Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab)); ?>

        x-on:click="window.matchMedia(`(max-width: 1024px)`).matches && $store.sidebar.close()"
        <?php if($sidebarCollapsible): ?>
            x-data="{ tooltip: false }"
            x-effect="
                tooltip = $store.sidebar.isOpen
                    ? false
                    : {
                          content: <?php echo \Illuminate\Support\Js::from($slot->toHtml())->toHtml() ?>,
                          placement: document.dir === 'rtl' ? 'left' : 'right',
                          theme: $store.theme,
                      }
            "
            x-tooltip.html="tooltip"
        <?php endif; ?>
        class="fi-sidebar-item-btn"
    >
        <!--[if BLOCK]><![endif]--><?php if(filled($icon) && ((! $subGrouped) || $sidebarCollapsible)): ?>
            <?php echo e(\Filament\Support\generate_icon_html(($active && $activeIcon) ? $activeIcon : $icon, attributes: (new \Illuminate\View\ComponentAttributeBag([
                    'x-show' => ($subGrouped && $sidebarCollapsible) ? '! $store.sidebar.isOpen' : false,
                ]))->class(['fi-sidebar-item-icon']), size: \Filament\Support\Enums\IconSize::Large)); ?>

        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if((blank($icon) && $grouped) || $subGrouped): ?>
            <div
                <?php if(filled($icon) && $subGrouped && $sidebarCollapsible): ?>
                    x-show="$store.sidebar.isOpen"
                <?php endif; ?>
                class="fi-sidebar-item-grouped-border"
            >
                <!--[if BLOCK]><![endif]--><?php if(! $first): ?>
                    <div
                        class="fi-sidebar-item-grouped-border-part-not-first"
                    ></div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><?php if(! $last): ?>
                    <div
                        class="fi-sidebar-item-grouped-border-part-not-last"
                    ></div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div class="fi-sidebar-item-grouped-border-part"></div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <span
            <?php if($sidebarCollapsible): ?>
                x-show="$store.sidebar.isOpen"
                x-transition:enter="fi-transition-enter"
                x-transition:enter-start="fi-transition-enter-start"
                x-transition:enter-end="fi-transition-enter-end"
            <?php endif; ?>
            class="fi-sidebar-item-label"
        >
            <?php echo e($slot); ?>

        </span>

        <!--[if BLOCK]><![endif]--><?php if(filled($badge)): ?>
            <span
                <?php if($sidebarCollapsible): ?>
                    x-show="$store.sidebar.isOpen"
                    x-transition:enter="fi-transition-enter"
                    x-transition:enter-start="fi-transition-enter-start"
                    x-transition:enter-end="fi-transition-enter-end"
                <?php endif; ?>
                class="fi-sidebar-item-badge-ctn"
            >
                <?php if (isset($component)) { $__componentOriginal986dce9114ddce94a270ab00ce6c273d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal986dce9114ddce94a270ab00ce6c273d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.badge','data' => ['color' => $badgeColor,'tooltip' => $badgeTooltip]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badgeColor),'tooltip' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badgeTooltip)]); ?>
                    <?php echo e($badge); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $attributes = $__attributesOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $component = $__componentOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__componentOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
            </span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </a>

    <!--[if BLOCK]><![endif]--><?php if(($active || $activeChildItems) && $childItems): ?>
        <ul class="fi-sidebar-sub-group-items">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $childItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isChildActive = $childItem->isActive();
                    $isChildItemChildItemsActive = $childItem->isChildItemsActive();
                    $childItemActiveIcon = $childItem->getActiveIcon();
                    $childItemBadge = $childItem->getBadge();
                    $childItemBadgeColor = $childItem->getBadgeColor();
                    $childItemBadgeTooltip = $childItem->getBadgeTooltip();
                    $childItemIcon = $childItem->getIcon();
                    $shouldChildItemOpenUrlInNewTab = $childItem->shouldOpenUrlInNewTab();
                    $childItemUrl = $childItem->getUrl();
                ?>

                <?php if (isset($component)) { $__componentOriginal7edbc33aaa546e1feb86647dcd0e4eb8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7edbc33aaa546e1feb86647dcd0e4eb8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.sidebar.item','data' => ['active' => $isChildActive,'activeChildItems' => $isChildItemChildItemsActive,'activeIcon' => $childItemActiveIcon,'badge' => $childItemBadge,'badgeColor' => $childItemBadgeColor,'badgeTooltip' => $childItemBadgeTooltip,'first' => $loop->first,'grouped' => true,'icon' => $childItemIcon,'last' => $loop->last,'shouldOpenUrlInNewTab' => $shouldChildItemOpenUrlInNewTab,'subGrouped' => true,'url' => $childItemUrl]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isChildActive),'active-child-items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isChildItemChildItemsActive),'active-icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($childItemActiveIcon),'badge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($childItemBadge),'badge-color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($childItemBadgeColor),'badge-tooltip' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($childItemBadgeTooltip),'first' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loop->first),'grouped' => true,'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($childItemIcon),'last' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loop->last),'should-open-url-in-new-tab' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($shouldChildItemOpenUrlInNewTab),'sub-grouped' => true,'url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($childItemUrl)]); ?>
                    <?php echo e($childItem->getLabel()); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7edbc33aaa546e1feb86647dcd0e4eb8)): ?>
<?php $attributes = $__attributesOriginal7edbc33aaa546e1feb86647dcd0e4eb8; ?>
<?php unset($__attributesOriginal7edbc33aaa546e1feb86647dcd0e4eb8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7edbc33aaa546e1feb86647dcd0e4eb8)): ?>
<?php $component = $__componentOriginal7edbc33aaa546e1feb86647dcd0e4eb8; ?>
<?php unset($__componentOriginal7edbc33aaa546e1feb86647dcd0e4eb8); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </ul>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</li>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\vendor\filament\filament\resources\views/components/sidebar/item.blade.php ENDPATH**/ ?>