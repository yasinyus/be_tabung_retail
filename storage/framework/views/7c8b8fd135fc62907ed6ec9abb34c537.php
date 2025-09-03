<?php
    use Filament\Actions\Action;
    use Filament\Support\Enums\Alignment;
    use Illuminate\View\ComponentAttributeBag;

    $fieldWrapperView = $getFieldWrapperView();

    $items = $getItems();

    $addAction = $getAction($getAddActionName());
    $addActionAlignment = $getAddActionAlignment();
    $addBetweenAction = $getAction($getAddBetweenActionName());
    $cloneAction = $getAction($getCloneActionName());
    $collapseAllAction = $getAction($getCollapseAllActionName());
    $expandAllAction = $getAction($getExpandAllActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $moveDownAction = $getAction($getMoveDownActionName());
    $moveUpAction = $getAction($getMoveUpActionName());
    $reorderAction = $getAction($getReorderActionName());
    $extraItemActions = $getExtraItemActions();

    $isAddable = $isAddable();
    $isCloneable = $isCloneable();
    $isCollapsible = $isCollapsible();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $collapseAllActionIsVisible = $isCollapsible && $collapseAllAction->isVisible();
    $expandAllActionIsVisible = $isCollapsible && $expandAllAction->isVisible();

    $key = $getKey();
    $statePath = $getStatePath();

    $itemLabelHeadingTag = $getHeadingTag();
    $isItemLabelTruncated = $isItemLabelTruncated();
    $labelBetweenItems = $getLabelBetweenItems();
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $fieldWrapperView] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => $field]); ?>
    <div
        <?php echo e($attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-repeater',
                    'fi-collapsible' => $isCollapsible,
                ])); ?>

    >
        <!--[if BLOCK]><![endif]--><?php if($collapseAllActionIsVisible || $expandAllActionIsVisible): ?>
            <div
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'fi-fo-repeater-actions',
                    'fi-hidden' => count($items) < 2,
                ]); ?>"
            >
                <!--[if BLOCK]><![endif]--><?php if($collapseAllActionIsVisible): ?>
                    <span
                        x-on:click="$dispatch('repeater-collapse', '<?php echo e($statePath); ?>')"
                    >
                        <?php echo e($collapseAllAction); ?>

                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><?php if($expandAllActionIsVisible): ?>
                    <span
                        x-on:click="$dispatch('repeater-expand', '<?php echo e($statePath); ?>')"
                    >
                        <?php echo e($expandAllAction); ?>

                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if(count($items)): ?>
            <ul
                x-sortable
                <?php echo e((new ComponentAttributeBag)
                        ->grid($getGridColumns())
                        ->merge([
                            'data-sortable-animation-duration' => $getReorderAnimationDuration(),
                            'wire:end.stop' => 'mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
                        ], escape: false)
                        ->class(['fi-fo-repeater-items'])); ?>

            >
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemKey => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $itemLabel = $getItemLabel($itemKey);
                        $visibleExtraItemActions = array_filter(
                            $extraItemActions,
                            fn (Action $action): bool => $action(['item' => $itemKey])->isVisible(),
                        );
                        $cloneAction = $cloneAction(['item' => $itemKey]);
                        $cloneActionIsVisible = $isCloneable && $cloneAction->isVisible();
                        $deleteAction = $deleteAction(['item' => $itemKey]);
                        $deleteActionIsVisible = $isDeletable && $deleteAction->isVisible();
                        $moveDownAction = $moveDownAction(['item' => $itemKey])->disabled($loop->last);
                        $moveDownActionIsVisible = $isReorderableWithButtons && $moveDownAction->isVisible();
                        $moveUpAction = $moveUpAction(['item' => $itemKey])->disabled($loop->first);
                        $moveUpActionIsVisible = $isReorderableWithButtons && $moveUpAction->isVisible();
                        $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                    ?>

                    <li
                        wire:ignore.self
                        wire:key="<?php echo e($item->getLivewireKey()); ?>.item"
                        x-data="{
                            isCollapsed: <?php echo \Illuminate\Support\Js::from($isCollapsed($item))->toHtml() ?>,
                        }"
                        x-on:repeater-expand.window="$event.detail === '<?php echo e($statePath); ?>' && (isCollapsed = false)"
                        x-on:repeater-collapse.window="$event.detail === '<?php echo e($statePath); ?>' && (isCollapsed = true)"
                        x-on:expand="isCollapsed = false"
                        x-sortable-item="<?php echo e($itemKey); ?>"
                        class="fi-fo-repeater-item"
                        x-bind:class="{ 'fi-collapsed': isCollapsed }"
                    >
                        <!--[if BLOCK]><![endif]--><?php if($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || filled($itemLabel) || $cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions): ?>
                            <div
                                <?php if($isCollapsible): ?>
                                    x-on:click.stop="isCollapsed = !isCollapsed"
                                <?php endif; ?>
                                class="fi-fo-repeater-item-header"
                            >
                                <!--[if BLOCK]><![endif]--><?php if($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible): ?>
                                    <ul
                                        class="fi-fo-repeater-item-header-start-actions"
                                    >
                                        <!--[if BLOCK]><![endif]--><?php if($reorderActionIsVisible): ?>
                                            <li
                                                x-sortable-handle
                                                x-on:click.stop
                                            >
                                                <?php echo e($reorderAction); ?>

                                            </li>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if($moveUpActionIsVisible || $moveDownActionIsVisible): ?>
                                            <li x-on:click.stop>
                                                <?php echo e($moveUpAction); ?>

                                            </li>

                                            <li x-on:click.stop>
                                                <?php echo e($moveDownAction); ?>

                                            </li>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </ul>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!--[if BLOCK]><![endif]--><?php if(filled($itemLabel)): ?>
                                    <<?php echo e($itemLabelHeadingTag); ?>

                                        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'fi-fo-repeater-item-header-label',
                                            'fi-truncated' => $isItemLabelTruncated,
                                        ]); ?>"
                                    >
                                        <?php echo e($itemLabel); ?>

                                    </<?php echo e($itemLabelHeadingTag); ?>>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!--[if BLOCK]><![endif]--><?php if($cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions): ?>
                                    <ul
                                        class="fi-fo-repeater-item-header-end-actions"
                                    >
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $visibleExtraItemActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extraItemAction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li x-on:click.stop>
                                                <?php echo e($extraItemAction(['item' => $itemKey])); ?>

                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if($cloneActionIsVisible): ?>
                                            <li x-on:click.stop>
                                                <?php echo e($cloneAction); ?>

                                            </li>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if($deleteActionIsVisible): ?>
                                            <li x-on:click.stop>
                                                <?php echo e($deleteAction); ?>

                                            </li>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if($isCollapsible): ?>
                                            <li
                                                class="fi-fo-repeater-item-header-collapsible-actions"
                                                x-on:click.stop="isCollapsed = !isCollapsed"
                                            >
                                                <div
                                                    class="fi-fo-repeater-item-header-collapse-action"
                                                >
                                                    <?php echo e($getAction('collapse')); ?>

                                                </div>

                                                <div
                                                    class="fi-fo-repeater-item-header-expand-action"
                                                >
                                                    <?php echo e($getAction('expand')); ?>

                                                </div>
                                            </li>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </ul>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div
                            x-show="! isCollapsed"
                            class="fi-fo-repeater-item-content"
                        >
                            <?php echo e($item); ?>

                        </div>
                    </li>

                    <!--[if BLOCK]><![endif]--><?php if(! $loop->last): ?>
                        <!--[if BLOCK]><![endif]--><?php if($isAddable && $addBetweenAction(['afterItem' => $itemKey])->isVisible()): ?>
                            <li class="fi-fo-repeater-add-between-items-ctn">
                                <div class="fi-fo-repeater-add-between-items">
                                    <?php echo e($addBetweenAction(['afterItem' => $itemKey])); ?>

                                </div>
                            </li>
                        <?php elseif(filled($labelBetweenItems)): ?>
                            <li class="fi-fo-repeater-label-between-items-ctn">
                                <span
                                    class="fi-fo-repeater-label-between-items"
                                >
                                    <?php echo e($labelBetweenItems); ?>

                                </span>
                            </li>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </ul>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($isAddable && $addAction->isVisible()): ?>
            <div
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'fi-fo-repeater-add',
                    ($addActionAlignment instanceof Alignment) ? ('fi-align-' . $addActionAlignment->value) : $addActionAlignment,
                ]); ?>"
            >
                <?php echo e($addAction); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\vendor\filament\forms\resources\views/components/repeater/index.blade.php ENDPATH**/ ?>