<?php
    $debounce = filament()->getGlobalSearchDebounce();
    $keyBindings = filament()->getGlobalSearchKeyBindings();
    $suffix = filament()->getGlobalSearchFieldSuffix();
?>

<div class="fi-global-search-ctn">
    <?php echo e(\Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_START)); ?>


    <div
        x-on:focus-first-global-search-result.stop="$el.querySelector('.fi-global-search-result-link')?.focus()"
        class="fi-global-search"
    >
        <div x-id="['input']" class="fi-global-search-field">
            <label x-bind:for="$id('input')" class="fi-sr-only">
                <?php echo e(__('filament-panels::global-search.field.label')); ?>

            </label>

            <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => ['prefixIcon' => \Filament\Support\Icons\Heroicon::MagnifyingGlass,'prefixIconAlias' => \Filament\View\PanelsIconAlias::GLOBAL_SEARCH_FIELD,'inlinePrefix' => true,'suffix' => $suffix,'inlineSuffix' => true,'wire:target' => 'search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['prefix-icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Filament\Support\Icons\Heroicon::MagnifyingGlass),'prefix-icon-alias' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Filament\View\PanelsIconAlias::GLOBAL_SEARCH_FIELD),'inline-prefix' => true,'suffix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($suffix),'inline-suffix' => true,'wire:target' => 'search']); ?>
                <input
                    autocomplete="off"
                    maxlength="1000"
                    placeholder="<?php echo e(__('filament-panels::global-search.field.placeholder')); ?>"
                    type="search"
                    wire:key="global-search.field.input"
                    x-bind:id="$id('input')"
                    x-on:keydown.down.prevent.stop="$dispatch('focus-first-global-search-result')"
                    wire:model.live.debounce.<?php echo e($debounce); ?>="search"
                    x-mousetrap.global.<?php echo e(collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.')); ?>="document.getElementById($id('input')).focus()"
                    class="fi-input fi-input-has-inline-prefix"
                />
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $attributes = $__attributesOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__attributesOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $component = $__componentOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__componentOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
        </div>

        <!--[if BLOCK]><![endif]--><?php if($results !== null): ?>
            <div
                x-data="{
                    isOpen: false,

                    open(event) {
                        this.isOpen = true
                    },

                    close(event) {
                        this.isOpen = false
                    },
                }"
                x-init="$nextTick(() => open())"
                x-on:click.away="close()"
                x-on:keydown.escape.window="close()"
                x-on:keydown.up.prevent="$focus.wrap().previous()"
                x-on:keydown.down.prevent="$focus.wrap().next()"
                x-on:open-global-search-results.window="$nextTick(() => open())"
                x-show="isOpen"
                x-transition:enter-start="fi-transition-enter-start"
                x-transition:leave-end="fi-transition-leave-end"
                class="fi-global-search-results-ctn"
            >
                <!--[if BLOCK]><![endif]--><?php if($results->getCategories()->isEmpty()): ?>
                    <p class="fi-global-search-no-results-message">
                        <?php echo e(__('filament-panels::global-search.no_results_message')); ?>

                    </p>
                <?php else: ?>
                    <ul class="fi-global-search-results">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $results->getCategories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupedResults): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="fi-global-search-result-group">
                                <h3
                                    class="fi-global-search-result-group-header"
                                >
                                    <?php echo e($group); ?>

                                </h3>

                                <ul
                                    class="fi-global-search-result-group-results"
                                >
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $groupedResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $resultVisibleActions = $result->getVisibleActions();
                                        ?>

                                        <li
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'fi-global-search-result',
                                                'fi-global-search-result-has-actions' => $resultVisibleActions,
                                            ]); ?>"
                                        >
                                            <a
                                                <?php echo e(\Filament\Support\generate_href_html($result->url)); ?>

                                                x-on:click="close()"
                                                class="fi-global-search-result-link"
                                            >
                                                <h4
                                                    class="fi-global-search-result-heading"
                                                >
                                                    <?php echo e($result->title); ?>

                                                </h4>

                                                <!--[if BLOCK]><![endif]--><?php if($result->details): ?>
                                                    <dl
                                                        class="fi-global-search-result-details"
                                                    >
                                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $result->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div
                                                                class="fi-global-search-result-detail"
                                                            >
                                                                <!--[if BLOCK]><![endif]--><?php if($isAssoc ??= \Illuminate\Support\Arr::isAssoc($result->details)): ?>
                                                                    <dt
                                                                        class="fi-global-search-result-detail-label"
                                                                    >
                                                                        <?php echo e($label); ?>:
                                                                    </dt>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                                <dd
                                                                    class="fi-global-search-result-detail-value"
                                                                >
                                                                    <?php echo e($value); ?>

                                                                </dd>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                    </dl>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </a>

                                            <!--[if BLOCK]><![endif]--><?php if($resultVisibleActions): ?>
                                                <div
                                                    class="fi-global-search-result-actions"
                                                >
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $resultVisibleActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($action); ?>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </ul>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </ul>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <?php echo e(\Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_END)); ?>

</div>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\vendor\filament\filament\resources\views/livewire/global-search.blade.php ENDPATH**/ ?>