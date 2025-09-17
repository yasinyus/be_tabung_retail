<?php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\Width;
    use Filament\Support\View\Components\ModalComponent\IconComponent;
    use Illuminate\View\ComponentAttributeBag;
?>

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'alignment' => Alignment::Start,
    'ariaLabelledby' => null,
    'autofocus' => \Filament\Support\View\Components\ModalComponent::$isAutofocused,
    'closeButton' => \Filament\Support\View\Components\ModalComponent::$hasCloseButton,
    'closeByClickingAway' => \Filament\Support\View\Components\ModalComponent::$isClosedByClickingAway,
    'closeByEscaping' => \Filament\Support\View\Components\ModalComponent::$isClosedByEscaping,
    'closeEventName' => 'close-modal',
    'closeQuietlyEventName' => 'close-modal-quietly',
    'description' => null,
    'extraModalWindowAttributeBag' => null,
    'footer' => null,
    'footerActions' => [],
    'footerActionsAlignment' => Alignment::Start,
    'header' => null,
    'heading' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconColor' => 'primary',
    'id' => null,
    'openEventName' => 'open-modal',
    'slideOver' => false,
    'stickyFooter' => false,
    'stickyHeader' => false,
    'trigger' => null,
    'visible' => true,
    'width' => 'sm',
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
    'alignment' => Alignment::Start,
    'ariaLabelledby' => null,
    'autofocus' => \Filament\Support\View\Components\ModalComponent::$isAutofocused,
    'closeButton' => \Filament\Support\View\Components\ModalComponent::$hasCloseButton,
    'closeByClickingAway' => \Filament\Support\View\Components\ModalComponent::$isClosedByClickingAway,
    'closeByEscaping' => \Filament\Support\View\Components\ModalComponent::$isClosedByEscaping,
    'closeEventName' => 'close-modal',
    'closeQuietlyEventName' => 'close-modal-quietly',
    'description' => null,
    'extraModalWindowAttributeBag' => null,
    'footer' => null,
    'footerActions' => [],
    'footerActionsAlignment' => Alignment::Start,
    'header' => null,
    'heading' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconColor' => 'primary',
    'id' => null,
    'openEventName' => 'open-modal',
    'slideOver' => false,
    'stickyFooter' => false,
    'stickyHeader' => false,
    'trigger' => null,
    'visible' => true,
    'width' => 'sm',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $hasContent = ! \Filament\Support\is_slot_empty($slot);
    $hasDescription = filled($description);
    $hasFooter = (! \Filament\Support\is_slot_empty($footer)) || (is_array($footerActions) && count($footerActions)) || (! is_array($footerActions) && (! \Filament\Support\is_slot_empty($footerActions)));
    $hasHeading = filled($heading);
    $hasIcon = filled($icon);

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    if (! $footerActionsAlignment instanceof Alignment) {
        $footerActionsAlignment = filled($footerActionsAlignment) ? (Alignment::tryFrom($footerActionsAlignment) ?? $footerActionsAlignment) : null;
    }

    if (is_string($width)) {
        $width = Width::tryFrom($width) ?? $width;
    }

    $closeEventHandler = filled($id) ? '$dispatch(' . \Illuminate\Support\Js::from($closeEventName) . ', { id: ' . \Illuminate\Support\Js::from($id) . ' })' : 'close()';

    $wireSubmitHandler = $attributes->get('wire:submit.prevent');
    $attributes = $attributes->except(['wire:submit.prevent']);
?>

<!--[if BLOCK]><![endif]--><?php if($trigger): ?>
    <?php echo '<div>'; ?>

    

    <div
        <?php if(! $trigger->attributes->get('disabled')): ?>
            x-on:click="$el.nextElementSibling.dispatchEvent(new CustomEvent(<?php echo \Illuminate\Support\Js::from($openEventName)->toHtml() ?>))"
        <?php endif; ?>
        <?php echo e($trigger->attributes->except(['disabled'])->class(['fi-modal-trigger'])); ?>

    >
        <?php echo e($trigger); ?>

    </div>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->

<div
    <?php if($ariaLabelledby): ?>
        aria-labelledby="<?php echo e($ariaLabelledby); ?>"
    <?php elseif($heading): ?>
        aria-labelledby="<?php echo e("{$id}.heading"); ?>"
    <?php endif; ?>
    aria-modal="true"
    id="<?php echo e($id); ?>"
    role="dialog"
    x-data="filamentModal({
                id: <?php echo \Illuminate\Support\Js::from($id)->toHtml() ?>,
            })"
    <?php if($id): ?>
        data-fi-modal-id="<?php echo e($id); ?>"
        x-on:<?php echo e($closeEventName); ?>.window="if (($event.detail.id === <?php echo \Illuminate\Support\Js::from($id)->toHtml() ?>) && isOpen) close()"
        x-on:<?php echo e($closeQuietlyEventName); ?>.window="if (($event.detail.id === <?php echo \Illuminate\Support\Js::from($id)->toHtml() ?>) && isOpen) closeQuietly()"
        x-on:<?php echo e($openEventName); ?>.window="if (($event.detail.id === <?php echo \Illuminate\Support\Js::from($id)->toHtml() ?>) && (! isOpen)) open()"
    <?php endif; ?>
    x-on:<?php echo e($closeEventName); ?>.stop="if (isOpen) close()"
    x-on:<?php echo e($closeQuietlyEventName); ?>.stop="if (isOpen) closeQuietly()"
    x-on:<?php echo e($openEventName); ?>.stop="if (! isOpen) open()"
    x-bind:class="{
        'fi-modal-open': isOpen,
    }"
    x-cloak
    x-show="isOpen"
    x-trap.noscroll<?php echo e($autofocus ? '' : '.noautofocus'); ?>="isOpen"
    <?php echo e($attributes->class([
            'fi-modal',
            'fi-modal-slide-over' => $slideOver,
            'fi-width-screen' => $width === Width::Screen,
        ])); ?>

>
    <div
        aria-hidden="true"
        x-show="isOpen"
        x-transition.duration.300ms.opacity
        class="fi-modal-close-overlay"
    ></div>

    <div
        <?php if($closeByClickingAway): ?>
            
            x-on:click.self="
                document.activeElement.selectionStart === undefined &&
                    document.activeElement.selectionEnd === undefined &&
                    <?php echo e($closeEventHandler); ?>

            "
        <?php endif; ?>
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'fi-modal-window-ctn',
            'fi-clickable' => $closeByClickingAway,
        ]); ?>"
    >
        <<?php echo e(filled($wireSubmitHandler) ? 'form' : 'div'); ?>

            <?php if($closeByEscaping): ?>
                x-on:keydown.window.escape="<?php echo e($closeEventHandler); ?>"
            <?php endif; ?>
            x-show="isWindowVisible"
            x-transition:enter="fi-transition-enter"
            x-transition:leave="fi-transition-leave"
            <?php if($width !== Width::Screen): ?>
                x-transition:enter-start="fi-transition-enter-start"
                x-transition:enter-end="fi-transition-enter-end"
                x-transition:leave-start="fi-transition-leave-start"
                x-transition:leave-end="fi-transition-leave-end"
            <?php endif; ?>
            <?php if(filled($wireSubmitHandler)): ?>
                wire:submit.prevent="<?php echo $wireSubmitHandler; ?>"
            <?php endif; ?>
            <?php if(filled($id)): ?>
                wire:key="<?php echo e(isset($this) ? "{$this->getId()}." : ''); ?>modal.<?php echo e($id); ?>.window"
            <?php endif; ?>
            <?php echo e(($extraModalWindowAttributeBag ?? new \Illuminate\View\ComponentAttributeBag)->class([
                    'fi-modal-window',
                    'fi-modal-window-has-close-btn' => $closeButton,
                    'fi-modal-window-has-content' => $hasContent,
                    'fi-modal-window-has-footer' => $hasFooter,
                    'fi-modal-window-has-icon' => $hasIcon,
                    'fi-modal-window-has-sticky-header' => $stickyHeader,
                    'fi-hidden' => ! $visible,
                    ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : null,
                    ($width instanceof Width) ? "fi-width-{$width->value}" : (is_string($width) ? $width : null),
                ])); ?>

        >
            <!--[if BLOCK]><![endif]--><?php if($heading || $header): ?>
                <div
                    <?php if(filled($id)): ?>
                        wire:key="<?php echo e(isset($this) ? "{$this->getId()}." : ''); ?>modal.<?php echo e($id); ?>.header"
                    <?php endif; ?>
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'fi-modal-header',
                        'fi-sticky' => $stickyHeader,
                        'fi-vertical-align-center' => $hasIcon && $hasHeading && (! $hasDescription) && in_array($alignment, [Alignment::Start, Alignment::Left]),
                    ]); ?>"
                >
                    <!--[if BLOCK]><![endif]--><?php if($closeButton): ?>
                        <?php if (isset($component)) { $__componentOriginalf0029cce6d19fd6d472097ff06a800a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf0029cce6d19fd6d472097ff06a800a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon-button','data' => ['color' => 'gray','icon' => \Filament\Support\Icons\Heroicon::OutlinedXMark,'iconAlias' => \Filament\Support\View\SupportIconAlias::MODAL_CLOSE_BUTTON,'iconSize' => 'lg','label' => __('filament::components/modal.actions.close.label'),'tabindex' => '-1','xOn:click' => $closeEventHandler,'class' => 'fi-modal-close-btn']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Filament\Support\Icons\Heroicon::OutlinedXMark),'icon-alias' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Filament\Support\View\SupportIconAlias::MODAL_CLOSE_BUTTON),'icon-size' => 'lg','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament::components/modal.actions.close.label')),'tabindex' => '-1','x-on:click' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($closeEventHandler),'class' => 'fi-modal-close-btn']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf0029cce6d19fd6d472097ff06a800a1)): ?>
<?php $attributes = $__attributesOriginalf0029cce6d19fd6d472097ff06a800a1; ?>
<?php unset($__attributesOriginalf0029cce6d19fd6d472097ff06a800a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf0029cce6d19fd6d472097ff06a800a1)): ?>
<?php $component = $__componentOriginalf0029cce6d19fd6d472097ff06a800a1; ?>
<?php unset($__componentOriginalf0029cce6d19fd6d472097ff06a800a1); ?>
<?php endif; ?>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if($header): ?>
                        <?php echo e($header); ?>

                    <?php else: ?>
                        <!--[if BLOCK]><![endif]--><?php if($hasIcon): ?>
                            <div class="fi-modal-icon-ctn">
                                <div
                                    <?php echo e((new ComponentAttributeBag)->color(IconComponent::class, $iconColor)->class(['fi-modal-icon-bg'])); ?>

                                >
                                    <?php echo e(\Filament\Support\generate_icon_html($icon, $iconAlias, size: \Filament\Support\Enums\IconSize::Large)); ?>

                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div>
                            <h2 class="fi-modal-heading">
                                <?php echo e($heading); ?>

                            </h2>

                            <!--[if BLOCK]><![endif]--><?php if($hasDescription): ?>
                                <p class="fi-modal-description">
                                    <?php echo e($description); ?>

                                </p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php if($hasContent): ?>
                <div
                    <?php if(filled($id)): ?>
                        wire:key="<?php echo e(isset($this) ? "{$this->getId()}." : ''); ?>modal.<?php echo e($id); ?>.content"
                    <?php endif; ?>
                    class="fi-modal-content"
                >
                    <?php echo e($slot); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php if($hasFooter): ?>
                <div
                    <?php if(filled($id)): ?>
                        wire:key="<?php echo e(isset($this) ? "{$this->getId()}." : ''); ?>modal.<?php echo e($id); ?>.footer"
                    <?php endif; ?>
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'fi-modal-footer',
                        'fi-sticky' => $stickyFooter,
                        ($footerActionsAlignment instanceof Alignment) ? "fi-align-{$footerActionsAlignment->value}" : null,
                    ]); ?>"
                >
                    <!--[if BLOCK]><![endif]--><?php if(! \Filament\Support\is_slot_empty($footer)): ?>
                        <?php echo e($footer); ?>

                    <?php else: ?>
                        <div class="fi-modal-footer-actions">
                            <!--[if BLOCK]><![endif]--><?php if(is_array($footerActions)): ?>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $footerActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo e($action); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            <?php else: ?>
                                <?php echo e($footerActions); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </<?php echo e(filled($wireSubmitHandler) ? 'form' : 'div'); ?>>
    </div>
</div>

<!--[if BLOCK]><![endif]--><?php if($trigger): ?>
    <?php echo '</div>'; ?>

    
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\vendor\filament\support\resources\views/components/modal/index.blade.php ENDPATH**/ ?>