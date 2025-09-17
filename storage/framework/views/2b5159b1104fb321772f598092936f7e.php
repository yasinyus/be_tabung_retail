<?php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Support\Enums\Width;
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Actions\HeaderActionsPosition;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Columns\ColumnGroup;
    use Filament\Tables\Enums\FiltersLayout;
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;
    use Filament\Tables\View\TablesRenderHook;
    use Illuminate\Support\Str;
    use Illuminate\View\ComponentAttributeBag;

    $defaultRecordActions = $getRecordActions();
    $flatRecordActionsCount = count($getFlatRecordActions());
    $recordActionsAlignment = $getRecordActionsAlignment();
    $recordActionsPosition = $getRecordActionsPosition();
    $recordActionsColumnLabel = $getRecordActionsColumnLabel();

    if (! $recordActionsAlignment instanceof Alignment) {
        $recordActionsAlignment = filled($recordActionsAlignment) ? (Alignment::tryFrom($recordActionsAlignment) ?? $recordActionsAlignment) : null;
    }

    $activeFiltersCount = $getActiveFiltersCount();
    $isSelectionDisabled = $isSelectionDisabled();
    $maxSelectableRecords = $getMaxSelectableRecords();
    $columns = $getVisibleColumns();
    $collapsibleColumnsLayout = $getCollapsibleColumnsLayout();
    $columnsLayout = $getColumnsLayout();
    $content = $getContent();
    $contentGrid = $getContentGrid();
    $contentFooter = $getContentFooter();
    $filterIndicators = $getFilterIndicators();
    $filtersApplyAction = $getFiltersApplyAction();
    $filtersForm = $getFiltersForm();
    $filtersFormWidth = $getFiltersFormWidth();
    $hasColumnGroups = $hasColumnGroups();
    $hasColumnsLayout = $hasColumnsLayout();
    $hasSummary = $hasSummary($this->getAllTableSummaryQuery());
    $header = $getHeader();
    $headerActions = array_filter(
        $getHeaderActions(),
        fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
    );
    $headerActionsPosition = $getHeaderActionsPosition();
    $heading = $getHeading();
    $group = $getGrouping();
    $toolbarActions = array_filter(
        $getToolbarActions(),
        fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
    );

    $hasNonBulkToolbarAction = false;

    foreach ($toolbarActions as $toolbarAction) {
        if ($toolbarAction instanceof \Filament\Actions\BulkActionGroup) {
            continue;
        }

        if ($toolbarAction instanceof \Filament\Actions\ActionGroup) {
            if ($toolbarAction->hasNonBulkAction()) {
                $hasNonBulkToolbarAction = true;

                break;
            }

            continue;
        }

        if (! $toolbarAction->isBulk()) {
            $hasNonBulkToolbarAction = true;

            break;
        }
    }

    $groups = $getGroups();
    $description = $getDescription();
    $isGroupsOnly = $isGroupsOnly() && $group;
    $isReorderable = $isReorderable();
    $isReordering = $isReordering();
    $areGroupingSettingsVisible = (! $isReordering) && count($groups) && (! $areGroupingSettingsHidden());
    $isGroupingDirectionSettingHidden = $isGroupingDirectionSettingHidden();
    $areGroupingSettingsInDropdownOnDesktop = $areGroupingSettingsInDropdownOnDesktop();
    $isColumnSearchVisible = $isSearchableByColumn();
    $isGlobalSearchVisible = $isSearchable();
    $isSearchOnBlur = $isSearchOnBlur();
    $isSelectionEnabled = $isSelectionEnabled() && (! $isGroupsOnly);
    $selectsCurrentPageOnly = $selectsCurrentPageOnly();
    $recordCheckboxPosition = $getRecordCheckboxPosition();
    $isStriped = $isStriped();
    $isLoaded = $isLoaded();
    $hasFilters = $isFilterable();
    $filtersLayout = $getFiltersLayout();
    $filtersTriggerAction = $getFiltersTriggerAction();
    $hasFiltersDialog = $hasFilters && in_array($filtersLayout, [FiltersLayout::Dropdown, FiltersLayout::Modal]);
    $hasFiltersAboveContent = $hasFilters && in_array($filtersLayout, [FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible]);
    $hasFiltersAboveContentCollapsible = $hasFilters && ($filtersLayout === FiltersLayout::AboveContentCollapsible);
    $hasFiltersBelowContent = $hasFilters && ($filtersLayout === FiltersLayout::BelowContent);
    $hasColumnManagerDropdown = $hasColumnManager();
    $hasReorderableColumns = $hasReorderableColumns();
    $hasToggleableColumns = $hasToggleableColumns();
    $columnManagerApplyAction = $getColumnManagerApplyAction();
    $columnManagerTriggerAction = $getColumnManagerTriggerAction();
    $hasHeader = $header || $heading || $description || ($headerActions && (! $isReordering)) || $isReorderable || $areGroupingSettingsVisible || $isGlobalSearchVisible || $hasFilters || count($filterIndicators) || $hasColumnManagerDropdown;
    $hasHeaderToolbar = $isReorderable || $areGroupingSettingsVisible || $isGlobalSearchVisible || $hasFiltersDialog || $hasColumnManagerDropdown;
    $headingTag = $getHeadingTag();
    $secondLevelHeadingTag = $heading ? $getHeadingTag(1) : $headingTag;
    $pluralModelLabel = $getPluralModelLabel();
    $records = $isLoaded ? $getRecords() : null;
    $searchDebounce = $getSearchDebounce();
    $allSelectableRecordsCount = ($isSelectionEnabled && $isLoaded) ? $getAllSelectableRecordsCount() : null;
    $columnsCount = count($columns);
    $reorderRecordsTriggerAction = $getReorderRecordsTriggerAction($isReordering);
    $page = $this->getTablePage();
    $defaultSortOptionLabel = $getDefaultSortOptionLabel();
    $sortDirection = $getSortDirection();

    if (count($defaultRecordActions) && (! $isReordering)) {
        $columnsCount++;
    }

    if ($isSelectionEnabled || $isReordering) {
        $columnsCount++;
    }

    if ($group) {
        $groupedSummarySelectedState = $this->getTableSummarySelectedState($this->getAllTableSummaryQuery(), modifyQueryUsing: fn (\Illuminate\Database\Query\Builder $query) => $group->groupQuery($query, model: $getQuery()->getModel()));
    }
?>

<div
    <?php if(! $isLoaded): ?>
        wire:init="loadTable"
    <?php endif; ?>
    x-data="filamentTable({
                canTrackDeselectedRecords: <?php echo \Illuminate\Support\Js::from($canTrackDeselectedRecords())->toHtml() ?>,
                currentSelectionLivewireProperty: <?php echo \Illuminate\Support\Js::from($getCurrentSelectionLivewireProperty())->toHtml() ?>,
                maxSelectableRecords: <?php echo \Illuminate\Support\Js::from($maxSelectableRecords)->toHtml() ?>,
                selectsCurrentPageOnly: <?php echo \Illuminate\Support\Js::from($selectsCurrentPageOnly)->toHtml() ?>,
                $wire,
            })"
    <?php echo e($getExtraAttributeBag()->class([
            'fi-ta',
            'fi-loading' => $records === null,
        ])); ?>

>
    <input
        type="hidden"
        value="<?php echo e($allSelectableRecordsCount); ?>"
        x-ref="allSelectableRecordsCount"
    />

    <div
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'fi-ta-ctn',
            'fi-ta-ctn-with-header' => $hasHeader,
        ]); ?>"
    >
        <div
            <?php if(! $hasHeader): ?> x-cloak <?php endif; ?>
            x-show="<?php echo \Illuminate\Support\Js::from($hasHeader)->toHtml() ?> || <?php echo \Illuminate\Support\Js::from($hasNonBulkToolbarAction)->toHtml() ?> || (getSelectedRecordsCount() && <?php echo \Illuminate\Support\Js::from(count($toolbarActions))->toHtml() ?>)"
            class="fi-ta-header-ctn"
        >
            <?php echo e(FilamentView::renderHook(TablesRenderHook::HEADER_BEFORE, scopes: static::class)); ?>


            <!--[if BLOCK]><![endif]--><?php if($header): ?>
                <?php echo e($header); ?>

            <?php elseif(($heading || $description || $headerActions) && ! $isReordering): ?>
                <div
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'fi-ta-header',
                        'fi-ta-header-adaptive-actions-position' => $headerActions && ($headerActionsPosition === HeaderActionsPosition::Adaptive),
                    ]); ?>"
                >
                    <!--[if BLOCK]><![endif]--><?php if($heading || $description): ?>
                        <div>
                            <!--[if BLOCK]><![endif]--><?php if($heading): ?>
                                <<?php echo e($headingTag); ?>

                                    class="fi-ta-header-heading"
                                >
                                    <?php echo e($heading); ?>

                                </<?php echo e($headingTag); ?>>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <!--[if BLOCK]><![endif]--><?php if($description): ?>
                                <p class="fi-ta-header-description">
                                    <?php echo e($description); ?>

                                </p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if((! $isReordering) && $headerActions): ?>
                        <div class="fi-ta-actions fi-align-start fi-wrapped">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $headerActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($action); ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <?php echo e(FilamentView::renderHook(TablesRenderHook::HEADER_AFTER, scopes: static::class)); ?>


            <!--[if BLOCK]><![endif]--><?php if($hasFiltersAboveContent): ?>
                <div
                    x-data="{ areFiltersOpen: <?php echo \Illuminate\Support\Js::from(! $hasFiltersAboveContentCollapsible)->toHtml() ?> }"
                    x-bind:class="{ 'fi-open': areFiltersOpen }"
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'fi-ta-filters-above-content-ctn',
                    ]); ?>"
                >
                    <?php if (isset($component)) { $__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.filters','data' => ['applyAction' => $filtersApplyAction,'form' => $filtersForm,'headingTag' => $secondLevelHeadingTag,'xCloak' => true,'xShow' => 'areFiltersOpen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['apply-action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersApplyAction),'form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersForm),'heading-tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($secondLevelHeadingTag),'x-cloak' => true,'x-show' => 'areFiltersOpen']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39)): ?>
<?php $attributes = $__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39; ?>
<?php unset($__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39)): ?>
<?php $component = $__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39; ?>
<?php unset($__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39); ?>
<?php endif; ?>

                    <!--[if BLOCK]><![endif]--><?php if($hasFiltersAboveContentCollapsible): ?>
                        <span
                            x-on:click="areFiltersOpen = ! areFiltersOpen"
                            class="fi-ta-filters-trigger-action-ctn"
                        >
                            <?php echo e($filtersTriggerAction->badge($activeFiltersCount)); ?>

                        </span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_BEFORE, scopes: static::class)); ?>


            <div
                <?php if(! $hasHeaderToolbar): ?> x-cloak <?php endif; ?>
                x-show="<?php echo \Illuminate\Support\Js::from($hasHeaderToolbar)->toHtml() ?> || <?php echo \Illuminate\Support\Js::from($hasNonBulkToolbarAction)->toHtml() ?> || (getSelectedRecordsCount() && <?php echo \Illuminate\Support\Js::from(count($toolbarActions))->toHtml() ?>)"
                class="fi-ta-header-toolbar"
            >
                <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_START, scopes: static::class)); ?>


                <div class="fi-ta-actions fi-align-start fi-wrapped">
                    <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_BEFORE, scopes: static::class)); ?>


                    <!--[if BLOCK]><![endif]--><?php if($isReorderable): ?>
                        <?php echo e($reorderRecordsTriggerAction); ?>

                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_AFTER, scopes: static::class)); ?>


                    <?php if((! $isReordering) && count($toolbarActions)): ?>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $toolbarActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($action); ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_BEFORE, scopes: static::class)); ?>


                    <!--[if BLOCK]><![endif]--><?php if($areGroupingSettingsVisible): ?>
                        <div
                            x-data="{
                                grouping: $wire.$entangle('tableGrouping', true),
                                group: null,
                                direction: null,
                            }"
                            x-init="
                                if (grouping) {
                                    ;[group, direction] = grouping.split(':')
                                    direction ??= 'asc'
                                }

                                $watch('grouping', function () {
                                    if (! grouping) {
                                        group = null
                                        direction = null

                                        return
                                    }

                                    ;[group, direction] = grouping.split(':')
                                    direction ??= 'asc'
                                })

                                $watch('direction', function () {
                                    grouping = group ? `${group}:${direction}` : null
                                })

                                $watch('group', function (newGroup, oldGroup) {
                                    if (! newGroup) {
                                        direction = null
                                        grouping = group ? `${group}:${direction}` : null

                                        return
                                    }

                                    if (oldGroup) {
                                        grouping = group ? `${group}:${direction}` : null

                                        return
                                    }

                                    direction ??= 'asc'
                                    grouping = group ? `${group}:${direction}` : null
                                })
                            "
                            class="fi-ta-grouping-settings"
                        >
                            <?php if (isset($component)) { $__componentOriginal22ab0dbc2c6619d5954111bba06f01db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22ab0dbc2c6619d5954111bba06f01db = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.dropdown.index','data' => ['placement' => 'bottom-start','shift' => true,'width' => 'xs','wire:key' => ''.e($this->getId()).'.table.grouping','class' => \Illuminate\Support\Arr::toCssClasses([
                                    'sm:fi-hidden' => ! $areGroupingSettingsInDropdownOnDesktop,
                                ])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['placement' => 'bottom-start','shift' => true,'width' => 'xs','wire:key' => ''.e($this->getId()).'.table.grouping','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([
                                    'sm:fi-hidden' => ! $areGroupingSettingsInDropdownOnDesktop,
                                ]))]); ?>
                                 <?php $__env->slot('trigger', null, []); ?> 
                                    <?php echo e($getGroupRecordsTriggerAction()); ?>

                                 <?php $__env->endSlot(); ?>

                                <div class="fi-ta-grouping-settings-fields">
                                    <label>
                                        <span>
                                            <?php echo e(__('filament-tables::table.grouping.fields.group.label')); ?>

                                        </span>

                                        <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                            <?php if (isset($component)) { $__componentOriginal97dc683fe4ff7acce9e296503563dd85 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97dc683fe4ff7acce9e296503563dd85 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.select','data' => ['xModel' => 'group','xOn:change' => 'resetCollapsedGroups()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-model' => 'group','x-on:change' => 'resetCollapsedGroups()']); ?>
                                                <option value="">-</option>

                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option
                                                        value="<?php echo e($groupOption->getId()); ?>"
                                                    >
                                                        <?php echo e($groupOption->getLabel()); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $attributes = $__attributesOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $component = $__componentOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__componentOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
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
                                    </label>

                                    <!--[if BLOCK]><![endif]--><?php if(! $isGroupingDirectionSettingHidden): ?>
                                        <label x-cloak x-show="group">
                                            <span>
                                                <?php echo e(__('filament-tables::table.grouping.fields.direction.label')); ?>

                                            </span>

                                            <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                                <?php if (isset($component)) { $__componentOriginal97dc683fe4ff7acce9e296503563dd85 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97dc683fe4ff7acce9e296503563dd85 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.select','data' => ['xModel' => 'direction']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-model' => 'direction']); ?>
                                                    <option value="asc">
                                                        <?php echo e(__('filament-tables::table.grouping.fields.direction.options.asc')); ?>

                                                    </option>

                                                    <option value="desc">
                                                        <?php echo e(__('filament-tables::table.grouping.fields.direction.options.desc')); ?>

                                                    </option>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $attributes = $__attributesOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $component = $__componentOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__componentOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
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
                                        </label>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22ab0dbc2c6619d5954111bba06f01db)): ?>
<?php $attributes = $__attributesOriginal22ab0dbc2c6619d5954111bba06f01db; ?>
<?php unset($__attributesOriginal22ab0dbc2c6619d5954111bba06f01db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22ab0dbc2c6619d5954111bba06f01db)): ?>
<?php $component = $__componentOriginal22ab0dbc2c6619d5954111bba06f01db; ?>
<?php unset($__componentOriginal22ab0dbc2c6619d5954111bba06f01db); ?>
<?php endif; ?>

                            <!--[if BLOCK]><![endif]--><?php if(! $areGroupingSettingsInDropdownOnDesktop): ?>
                                <div class="fi-ta-grouping-settings-fields">
                                    <label>
                                        <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => ['prefix' => __('filament-tables::table.grouping.fields.group.label')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament-tables::table.grouping.fields.group.label'))]); ?>
                                            <?php if (isset($component)) { $__componentOriginal97dc683fe4ff7acce9e296503563dd85 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97dc683fe4ff7acce9e296503563dd85 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.select','data' => ['xModel' => 'group','xOn:change' => 'resetCollapsedGroups()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-model' => 'group','x-on:change' => 'resetCollapsedGroups()']); ?>
                                                <option value="">-</option>

                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option
                                                        value="<?php echo e($groupOption->getId()); ?>"
                                                    >
                                                        <?php echo e($groupOption->getLabel()); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $attributes = $__attributesOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $component = $__componentOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__componentOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
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
                                    </label>

                                    <!--[if BLOCK]><![endif]--><?php if(! $isGroupingDirectionSettingHidden): ?>
                                        <label x-cloak x-show="group">
                                            <span class="fi-sr-only">
                                                <?php echo e(__('filament-tables::table.grouping.fields.direction.label')); ?>

                                            </span>

                                            <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                                <?php if (isset($component)) { $__componentOriginal97dc683fe4ff7acce9e296503563dd85 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97dc683fe4ff7acce9e296503563dd85 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.select','data' => ['xModel' => 'direction']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-model' => 'direction']); ?>
                                                    <option value="asc">
                                                        <?php echo e(__('filament-tables::table.grouping.fields.direction.options.asc')); ?>

                                                    </option>

                                                    <option value="desc">
                                                        <?php echo e(__('filament-tables::table.grouping.fields.direction.options.desc')); ?>

                                                    </option>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $attributes = $__attributesOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $component = $__componentOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__componentOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
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
                                        </label>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_AFTER, scopes: static::class)); ?>

                </div>

                <!--[if BLOCK]><![endif]--><?php if($isGlobalSearchVisible || $hasFiltersDialog || $hasColumnManagerDropdown): ?>
                    <div>
                        <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_BEFORE, scopes: static::class)); ?>


                        <!--[if BLOCK]><![endif]--><?php if($isGlobalSearchVisible): ?>
                            <?php
                                $searchPlaceholder = $getSearchPlaceholder();
                            ?>

                            <?php if (isset($component)) { $__componentOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.search-field','data' => ['debounce' => $searchDebounce,'onBlur' => $isSearchOnBlur,'placeholder' => $searchPlaceholder]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::search-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['debounce' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchDebounce),'on-blur' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSearchOnBlur),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchPlaceholder)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b)): ?>
<?php $attributes = $__attributesOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b; ?>
<?php unset($__attributesOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b)): ?>
<?php $component = $__componentOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b; ?>
<?php unset($__componentOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b); ?>
<?php endif; ?>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_AFTER, scopes: static::class)); ?>


                        <!--[if BLOCK]><![endif]--><?php if($hasFiltersDialog || $hasColumnManagerDropdown): ?>
                            <!--[if BLOCK]><![endif]--><?php if($hasFiltersDialog): ?>
                                <!--[if BLOCK]><![endif]--><?php if(($filtersLayout === FiltersLayout::Modal) || $filtersTriggerAction->isModalSlideOver()): ?>
                                    <?php
                                        $filtersTriggerActionModalAlignment = $filtersTriggerAction->getModalAlignment();
                                        $filtersTriggerActionIsModalAutofocused = $filtersTriggerAction->isModalAutofocused();
                                        $filtersTriggerActionHasModalCloseButton = $filtersTriggerAction->hasModalCloseButton();
                                        $filtersTriggerActionIsModalClosedByClickingAway = $filtersTriggerAction->isModalClosedByClickingAway();
                                        $filtersTriggerActionIsModalClosedByEscaping = $filtersTriggerAction->isModalClosedByEscaping();
                                        $filtersTriggerActionModalDescription = $filtersTriggerAction->getModalDescription();
                                        $filtersTriggerActionVisibleModalFooterActions = $filtersTriggerAction->getVisibleModalFooterActions();
                                        $filtersTriggerActionModalFooterActionsAlignment = $filtersTriggerAction->getModalFooterActionsAlignment();
                                        $filtersTriggerActionModalHeading = $filtersTriggerAction->getCustomModalHeading() ?? __('filament-tables::table.filters.heading');
                                        $filtersTriggerActionModalIcon = $filtersTriggerAction->getModalIcon();
                                        $filtersTriggerActionModalIconColor = $filtersTriggerAction->getModalIconColor();
                                        $filtersTriggerActionIsModalSlideOver = $filtersTriggerAction->isModalSlideOver();
                                        $filtersTriggerActionIsModalFooterSticky = $filtersTriggerAction->isModalFooterSticky();
                                        $filtersTriggerActionIsModalHeaderSticky = $filtersTriggerAction->isModalHeaderSticky();
                                    ?>

                                    <?php if (isset($component)) { $__componentOriginal0942a211c37469064369f887ae8d1cef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0942a211c37469064369f887ae8d1cef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.modal.index','data' => ['alignment' => $filtersTriggerActionModalAlignment,'autofocus' => $filtersTriggerActionIsModalAutofocused,'closeButton' => $filtersTriggerActionHasModalCloseButton,'closeByClickingAway' => $filtersTriggerActionIsModalClosedByClickingAway,'closeByEscaping' => $filtersTriggerActionIsModalClosedByEscaping,'description' => $filtersTriggerActionModalDescription,'footerActions' => $filtersTriggerActionVisibleModalFooterActions,'footerActionsAlignment' => $filtersTriggerActionModalFooterActionsAlignment,'heading' => $filtersTriggerActionModalHeading,'icon' => $filtersTriggerActionModalIcon,'iconColor' => $filtersTriggerActionModalIconColor,'slideOver' => $filtersTriggerActionIsModalSlideOver,'stickyFooter' => $filtersTriggerActionIsModalFooterSticky,'stickyHeader' => $filtersTriggerActionIsModalHeaderSticky,'width' => $filtersFormWidth,'wire:key' => $this->getId() . '.table.filters','class' => 'fi-ta-filters-modal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['alignment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionModalAlignment),'autofocus' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionIsModalAutofocused),'close-button' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionHasModalCloseButton),'close-by-clicking-away' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionIsModalClosedByClickingAway),'close-by-escaping' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionIsModalClosedByEscaping),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionModalDescription),'footer-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionVisibleModalFooterActions),'footer-actions-alignment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionModalFooterActionsAlignment),'heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionModalHeading),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionModalIcon),'icon-color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionModalIconColor),'slide-over' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionIsModalSlideOver),'sticky-footer' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionIsModalFooterSticky),'sticky-header' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersTriggerActionIsModalHeaderSticky),'width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersFormWidth),'wire:key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getId() . '.table.filters'),'class' => 'fi-ta-filters-modal']); ?>
                                         <?php $__env->slot('trigger', null, []); ?> 
                                            <?php echo e($filtersTriggerAction->badge($activeFiltersCount)); ?>

                                         <?php $__env->endSlot(); ?>

                                        <?php echo e($filtersTriggerAction->getModalContent()); ?>


                                        <?php echo e($filtersForm); ?>


                                        <?php echo e($filtersTriggerAction->getModalContentFooter()); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0942a211c37469064369f887ae8d1cef)): ?>
<?php $attributes = $__attributesOriginal0942a211c37469064369f887ae8d1cef; ?>
<?php unset($__attributesOriginal0942a211c37469064369f887ae8d1cef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0942a211c37469064369f887ae8d1cef)): ?>
<?php $component = $__componentOriginal0942a211c37469064369f887ae8d1cef; ?>
<?php unset($__componentOriginal0942a211c37469064369f887ae8d1cef); ?>
<?php endif; ?>
                                <?php else: ?>
                                    <?php
                                        $filtersFormMaxHeight = $getFiltersFormMaxHeight();
                                    ?>

                                    <?php if (isset($component)) { $__componentOriginal22ab0dbc2c6619d5954111bba06f01db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22ab0dbc2c6619d5954111bba06f01db = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.dropdown.index','data' => ['maxHeight' => $filtersFormMaxHeight,'placement' => 'bottom-end','shift' => true,'width' => $filtersFormWidth ?? Width::ExtraSmall,'wire:key' => $this->getId() . '.table.filters','class' => 'fi-ta-filters-dropdown']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['max-height' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersFormMaxHeight),'placement' => 'bottom-end','shift' => true,'width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersFormWidth ?? Width::ExtraSmall),'wire:key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getId() . '.table.filters'),'class' => 'fi-ta-filters-dropdown']); ?>
                                         <?php $__env->slot('trigger', null, []); ?> 
                                            <?php echo e($filtersTriggerAction->badge($activeFiltersCount)); ?>

                                         <?php $__env->endSlot(); ?>

                                        <?php if (isset($component)) { $__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.filters','data' => ['applyAction' => $filtersApplyAction,'form' => $filtersForm,'headingTag' => $secondLevelHeadingTag]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['apply-action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersApplyAction),'form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersForm),'heading-tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($secondLevelHeadingTag)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39)): ?>
<?php $attributes = $__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39; ?>
<?php unset($__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39)): ?>
<?php $component = $__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39; ?>
<?php unset($__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39); ?>
<?php endif; ?>
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22ab0dbc2c6619d5954111bba06f01db)): ?>
<?php $attributes = $__attributesOriginal22ab0dbc2c6619d5954111bba06f01db; ?>
<?php unset($__attributesOriginal22ab0dbc2c6619d5954111bba06f01db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22ab0dbc2c6619d5954111bba06f01db)): ?>
<?php $component = $__componentOriginal22ab0dbc2c6619d5954111bba06f01db; ?>
<?php unset($__componentOriginal22ab0dbc2c6619d5954111bba06f01db); ?>
<?php endif; ?>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE, scopes: static::class)); ?>


                            <!--[if BLOCK]><![endif]--><?php if($hasColumnManagerDropdown): ?>
                                <?php
                                    $columnManagerMaxHeight = $getColumnManagerMaxHeight();
                                    $columnManagerWidth = $getColumnManagerWidth();
                                    $columnManagerColumns = $getColumnManagerColumns();
                                ?>

                                <?php if (isset($component)) { $__componentOriginal22ab0dbc2c6619d5954111bba06f01db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22ab0dbc2c6619d5954111bba06f01db = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.dropdown.index','data' => ['maxHeight' => $columnManagerMaxHeight,'placement' => 'bottom-end','shift' => true,'width' => $columnManagerWidth,'wire:key' => $this->getId() . '.table.column-manager','class' => 'fi-ta-col-manager-dropdown']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['max-height' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columnManagerMaxHeight),'placement' => 'bottom-end','shift' => true,'width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columnManagerWidth),'wire:key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getId() . '.table.column-manager'),'class' => 'fi-ta-col-manager-dropdown']); ?>
                                     <?php $__env->slot('trigger', null, []); ?> 
                                        <?php echo e($columnManagerTriggerAction); ?>

                                     <?php $__env->endSlot(); ?>

                                    <?php if (isset($component)) { $__componentOriginale0a88ee0f601f2ff5f39fba36ac88c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale0a88ee0f601f2ff5f39fba36ac88c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.column-manager','data' => ['applyAction' => $columnManagerApplyAction,'columns' => $columnManagerColumns,'hasReorderableColumns' => $hasReorderableColumns,'hasToggleableColumns' => $hasToggleableColumns,'headingTag' => $secondLevelHeadingTag,'reorderAnimationDuration' => $getReorderAnimationDuration()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::column-manager'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['apply-action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columnManagerApplyAction),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columnManagerColumns),'has-reorderable-columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasReorderableColumns),'has-toggleable-columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasToggleableColumns),'heading-tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($secondLevelHeadingTag),'reorder-animation-duration' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($getReorderAnimationDuration())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale0a88ee0f601f2ff5f39fba36ac88c56)): ?>
<?php $attributes = $__attributesOriginale0a88ee0f601f2ff5f39fba36ac88c56; ?>
<?php unset($__attributesOriginale0a88ee0f601f2ff5f39fba36ac88c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale0a88ee0f601f2ff5f39fba36ac88c56)): ?>
<?php $component = $__componentOriginale0a88ee0f601f2ff5f39fba36ac88c56; ?>
<?php unset($__componentOriginale0a88ee0f601f2ff5f39fba36ac88c56); ?>
<?php endif; ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22ab0dbc2c6619d5954111bba06f01db)): ?>
<?php $attributes = $__attributesOriginal22ab0dbc2c6619d5954111bba06f01db; ?>
<?php unset($__attributesOriginal22ab0dbc2c6619d5954111bba06f01db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22ab0dbc2c6619d5954111bba06f01db)): ?>
<?php $component = $__componentOriginal22ab0dbc2c6619d5954111bba06f01db; ?>
<?php unset($__componentOriginal22ab0dbc2c6619d5954111bba06f01db); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER, scopes: static::class)); ?>

                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_END)); ?>

            </div>

            <?php echo e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_AFTER)); ?>

        </div>

        <!--[if BLOCK]><![endif]--><?php if($isReordering): ?>
            <div
                x-cloak
                wire:key="<?php echo e($this->getId()); ?>.table.reorder.indicator"
                class="fi-ta-reorder-indicator"
            >
                <?php echo e(\Filament\Support\generate_loading_indicator_html(new \Illuminate\View\ComponentAttributeBag([
                        'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                        'wire:target' => 'reorderTable',
                    ]))); ?>


                <?php echo e(__('filament-tables::table.reorder_indicator')); ?>

            </div>
        <?php elseif($isSelectionEnabled && ($maxSelectableRecords !== 1) && $isLoaded): ?>
            <div
                x-cloak
                x-bind:hidden="! getSelectedRecordsCount()"
                x-show="getSelectedRecordsCount()"
                wire:key="<?php echo e($this->getId()); ?>.table.selection.indicator"
                class="fi-ta-selection-indicator"
            >
                <div>
                    <?php echo e(\Filament\Support\generate_loading_indicator_html(new \Illuminate\View\ComponentAttributeBag([
                            'x-show' => 'isLoading',
                        ]))); ?>


                    <span
                        x-text="
                            window.pluralize(<?php echo \Illuminate\Support\Js::from(__('filament-tables::table.selection_indicator.selected_count'))->toHtml() ?>, getSelectedRecordsCount(), {
                                count: new Intl.NumberFormat(<?php echo \Illuminate\Support\Js::from(str_replace('_', '-', app()->getLocale()))->toHtml() ?>).format(getSelectedRecordsCount()),
                            })
                        "
                    ></span>
                </div>

                <!--[if BLOCK]><![endif]--><?php if(! $isSelectionDisabled): ?>
                    <div>
                        <?php echo e(FilamentView::renderHook(TablesRenderHook::SELECTION_INDICATOR_ACTIONS_BEFORE, scopes: static::class)); ?>


                        <div class="fi-ta-selection-indicator-actions-ctn">
                            <?php if (isset($component)) { $__componentOriginal549c94d872270b69c72bdf48cb183bc9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal549c94d872270b69c72bdf48cb183bc9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.link','data' => ['color' => 'primary','tag' => 'button','xOn:click' => 'selectAllRecords','xShow' => 'canSelectAllRecords()','wire:key' => $this->getId() . 'table.selection.indicator.actions.select-all.' . $allSelectableRecordsCount . '.' . $page]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'primary','tag' => 'button','x-on:click' => 'selectAllRecords','x-show' => 'canSelectAllRecords()','wire:key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getId() . 'table.selection.indicator.actions.select-all.' . $allSelectableRecordsCount . '.' . $page)]); ?>
                                <?php echo e(trans_choice('filament-tables::table.selection_indicator.actions.select_all.label', $allSelectableRecordsCount, ['count' => \Illuminate\Support\Number::format($allSelectableRecordsCount, locale: app()->getLocale())])); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal549c94d872270b69c72bdf48cb183bc9)): ?>
<?php $attributes = $__attributesOriginal549c94d872270b69c72bdf48cb183bc9; ?>
<?php unset($__attributesOriginal549c94d872270b69c72bdf48cb183bc9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal549c94d872270b69c72bdf48cb183bc9)): ?>
<?php $component = $__componentOriginal549c94d872270b69c72bdf48cb183bc9; ?>
<?php unset($__componentOriginal549c94d872270b69c72bdf48cb183bc9); ?>
<?php endif; ?>

                            <?php if (isset($component)) { $__componentOriginal549c94d872270b69c72bdf48cb183bc9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal549c94d872270b69c72bdf48cb183bc9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.link','data' => ['color' => 'danger','tag' => 'button','xOn:click' => 'deselectAllRecords']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'danger','tag' => 'button','x-on:click' => 'deselectAllRecords']); ?>
                                <?php echo e(__('filament-tables::table.selection_indicator.actions.deselect_all.label')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal549c94d872270b69c72bdf48cb183bc9)): ?>
<?php $attributes = $__attributesOriginal549c94d872270b69c72bdf48cb183bc9; ?>
<?php unset($__attributesOriginal549c94d872270b69c72bdf48cb183bc9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal549c94d872270b69c72bdf48cb183bc9)): ?>
<?php $component = $__componentOriginal549c94d872270b69c72bdf48cb183bc9; ?>
<?php unset($__componentOriginal549c94d872270b69c72bdf48cb183bc9); ?>
<?php endif; ?>
                        </div>

                        <?php echo e(FilamentView::renderHook(TablesRenderHook::SELECTION_INDICATOR_ACTIONS_AFTER, scopes: static::class)); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($filterIndicators): ?>
            <!--[if BLOCK]><![endif]--><?php if(filled($filterIndicatorsView = FilamentView::renderHook(TablesRenderHook::FILTER_INDICATORS, scopes: static::class, data: ['filterIndicators' => $filterIndicators]))): ?>
                <?php echo e($filterIndicatorsView); ?>

            <?php else: ?>
                <div class="fi-ta-filter-indicators">
                    <div>
                        <span class="fi-ta-filter-indicators-label">
                            <?php echo e(__('filament-tables::table.filters.indicator')); ?>

                        </span>

                        <div class="fi-ta-filter-indicators-badges-ctn">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterIndicators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $indicator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $indicatorColor = $indicator->getColor();
                                ?>

                                <?php if (isset($component)) { $__componentOriginal986dce9114ddce94a270ab00ce6c273d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal986dce9114ddce94a270ab00ce6c273d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.badge','data' => ['color' => $indicatorColor]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($indicatorColor)]); ?>
                                    <?php echo e($indicator->getLabel()); ?>


                                    <!--[if BLOCK]><![endif]--><?php if($indicator->isRemovable()): ?>
                                        <?php
                                            $indicatorRemoveLivewireClickHandler = $indicator->getRemoveLivewireClickHandler();
                                        ?>

                                         <?php $__env->slot('deleteButton', null, ['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament-tables::table.filters.actions.remove.label')),'wire:click' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($indicatorRemoveLivewireClickHandler),'wire:loading.attr' => 'disabled','wire:target' => 'removeTableFilter']); ?>  <?php $__env->endSlot(); ?>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if(collect($filterIndicators)->contains(fn (\Filament\Tables\Filters\Indicator $indicator): bool => $indicator->isRemovable())): ?>
                        <button
                            type="button"
                            x-tooltip="{
                                content: <?php echo \Illuminate\Support\Js::from(__('filament-tables::table.filters.actions.remove_all.tooltip'))->toHtml() ?>,
                                theme: $store.theme,
                            }"
                            wire:click="removeTableFilters"
                            wire:loading.attr="disabled"
                            wire:target="removeTableFilters,removeTableFilter"
                            class="fi-icon-btn fi-size-sm"
                        >
                            <?php echo e(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::XMark, alias: \Filament\Tables\View\TablesIconAlias::FILTERS_REMOVE_ALL_BUTTON, size: \Filament\Support\Enums\IconSize::Small)); ?>

                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if(((! $content) && (! $hasColumnsLayout)) || ($records === null) || count($records)): ?>
            <div
                <?php if((! $isReordering) && ($pollingInterval = $getPollingInterval())): ?>
                    wire:poll.<?php echo e($pollingInterval); ?>

                <?php endif; ?>
                class="fi-ta-content-ctn"
            >
                <!--[if BLOCK]><![endif]--><?php if(($content || $hasColumnsLayout) && ($records !== null) && count($records)): ?>
                    <!--[if BLOCK]><![endif]--><?php if(! $isReordering): ?>
                        <?php
                            $sortableColumns = array_filter(
                                $columns,
                                fn (\Filament\Tables\Columns\Column $column): bool => $column->isSortable(),
                            );
                        ?>

                        <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled || count($sortableColumns)): ?>
                            <div class="fi-ta-content-header">
                                <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && ($maxSelectableRecords !== 1) && (! $isReordering)): ?>
                                    <input
                                        aria-label="<?php echo e(__('filament-tables::table.fields.bulk_select_page.label')); ?>"
                                        type="checkbox"
                                        <?php if($isSelectionDisabled): ?>
                                            disabled
                                        <?php elseif($maxSelectableRecords): ?>
                                            x-bind:disabled="
                                                const recordsOnPage = getRecordsOnPage()

                                                return recordsOnPage.length && ! areRecordsToggleable(recordsOnPage)
                                            "
                                        <?php endif; ?>
                                        x-bind:checked="
                                            const recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                        x-on:click="toggleSelectRecordsOnPage"
                                        
                                        wire:key="<?php echo e($this->getId()); ?>.table.bulk-select-page.checkbox.<?php echo e(\Illuminate\Support\Str::random()); ?>"
                                        wire:loading.attr="disabled"
                                        wire:target="<?php echo e(implode(',', \Filament\Tables\Table::LOADING_TARGETS)); ?>"
                                        class="fi-ta-page-checkbox fi-checkbox-input"
                                    />
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!--[if BLOCK]><![endif]--><?php if(count($sortableColumns)): ?>
                                    <div
                                        x-data="{
                                            sort: $wire.$entangle('tableSort', true),
                                            column: null,
                                            direction: null,
                                        }"
                                        x-init="
                                            if (sort) {
                                                ;[column, direction] = sort.split(':')
                                                direction ??= 'asc'
                                            }

                                            $watch('sort', function () {
                                                if (! sort) {
                                                    return
                                                }

                                                ;[column, direction] = sort.split(':')
                                                direction ??= 'asc'
                                            })

                                            $watch('direction', function () {
                                                sort = column ? `${column}:${direction}` : null
                                            })

                                            $watch('column', function (newColumn, oldColumn) {
                                                if (! newColumn) {
                                                    direction = null
                                                    sort = column ? `${column}:${direction}` : null

                                                    return
                                                }

                                                if (oldColumn) {
                                                    sort = column ? `${column}:${direction}` : null

                                                    return
                                                }

                                                direction = 'asc'
                                                sort = column ? `${column}:${direction}` : null
                                            })
                                        "
                                        class="fi-ta-sorting-settings"
                                    >
                                        <label>
                                            <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => ['prefix' => __('filament-tables::table.sorting.fields.column.label')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament-tables::table.sorting.fields.column.label'))]); ?>
                                                <?php if (isset($component)) { $__componentOriginal97dc683fe4ff7acce9e296503563dd85 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97dc683fe4ff7acce9e296503563dd85 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.select','data' => ['xModel' => 'column']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-model' => 'column']); ?>
                                                    <option value="">
                                                        <?php echo e($defaultSortOptionLabel); ?>

                                                    </option>

                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sortableColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option
                                                            value="<?php echo e($column->getName()); ?>"
                                                        >
                                                            <?php echo e($column->getLabel()); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $attributes = $__attributesOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $component = $__componentOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__componentOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
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
                                        </label>

                                        <label x-cloak x-show="column">
                                            <span class="fi-sr-only">
                                                <?php echo e(__('filament-tables::table.sorting.fields.direction.label')); ?>

                                            </span>

                                            <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                                <?php if (isset($component)) { $__componentOriginal97dc683fe4ff7acce9e296503563dd85 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97dc683fe4ff7acce9e296503563dd85 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.select','data' => ['xModel' => 'direction']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-model' => 'direction']); ?>
                                                    <option value="asc">
                                                        <?php echo e(__('filament-tables::table.sorting.fields.direction.options.asc')); ?>

                                                    </option>

                                                    <option value="desc">
                                                        <?php echo e(__('filament-tables::table.sorting.fields.direction.options.desc')); ?>

                                                    </option>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $attributes = $__attributesOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $component = $__componentOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__componentOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
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
                                        </label>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if($content): ?>
                        <?php echo e($content->with(['records' => $records])); ?>

                    <?php else: ?>
                        <div
                            <?php if($isReorderable): ?>
                                x-on:end.stop="
                                    $wire.reorderTable(
                                        $event.target.sortable.toArray(),
                                        $event.item.getAttribute('x-sortable-item'),
                                    )
                                "
                                x-sortable
                                data-sortable-animation-duration="<?php echo e($getReorderAnimationDuration()); ?>"
                            <?php endif; ?>
                            <?php echo e((new ComponentAttributeBag)
                                    ->when($contentGrid, fn (ComponentAttributeBag $attributes) => $attributes->grid($contentGrid))
                                    ->class([
                                        'fi-ta-content',
                                        'fi-ta-content-grid' => $contentGrid,
                                        'fi-ta-content-grouped' => $this->getTableGrouping(),
                                    ])); ?>

                        >
                            <?php
                                $previousRecord = null;
                                $previousRecordGroupKey = null;
                                $previousRecordGroupTitle = null;
                            ?>

                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $recordAction = $getRecordAction($record);
                                    $recordKey = $getRecordKey($record);
                                    $recordUrl = $getRecordUrl($record);
                                    $openRecordUrlInNewTab = $shouldOpenRecordUrlInNewTab($record);
                                    $recordGroupKey = $group?->getStringKey($record);
                                    $recordGroupTitle = $group?->getTitle($record);
                                    $isRecordGroupCollapsible = $group?->isCollapsible();

                                    $collapsibleColumnsLayout?->record($record)->recordKey($recordKey);
                                    $hasCollapsibleColumnsLayout = (bool) $collapsibleColumnsLayout?->isVisible();

                                    $recordActions = array_reduce(
                                        $defaultRecordActions,
                                        function (array $carry, $action) use ($record): array {
                                            $action = $action->getClone();

                                            if (! $action instanceof \Filament\Actions\BulkAction) {
                                                $action->record($record);
                                            }

                                            if ($action->isHidden()) {
                                                return $carry;
                                            }

                                            $carry[] = $action;

                                            return $carry;
                                        },
                                        initial: [],
                                    );
                                ?>

                                <!--[if BLOCK]><![endif]--><?php if($recordGroupTitle !== $previousRecordGroupTitle): ?>
                                    <!--[if BLOCK]><![endif]--><?php if($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle)): ?>
                                        <table
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'fi-ta-table',
                                                'fi-ta-table-reordering' => $isReordering,
                                            ]); ?>"
                                        >
                                            <tbody>
                                                <?php
                                                    $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                                                ?>

                                                <?php if (isset($component)) { $__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.summary.row','data' => ['columns' => $columns,'extraHeadingColumn' => true,'heading' => 
                                                        __('filament-tables::table.summary.subheadings.group', [
                                                            'group' => $previousRecordGroupTitle,
                                                            'label' => $pluralModelLabel,
                                                        ])
                                                    ,'placeholderColumns' => false,'query' => $groupScopedAllTableSummaryQuery,'selectedState' => $groupedSummarySelectedState[$previousRecordGroupKey] ?? []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::summary.row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'extra-heading-column' => true,'heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(
                                                        __('filament-tables::table.summary.subheadings.group', [
                                                            'group' => $previousRecordGroupTitle,
                                                            'label' => $pluralModelLabel,
                                                        ])
                                                    ),'placeholder-columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'query' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupScopedAllTableSummaryQuery),'selected-state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupedSummarySelectedState[$previousRecordGroupKey] ?? [])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb)): ?>
<?php $attributes = $__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb; ?>
<?php unset($__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb)): ?>
<?php $component = $__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb; ?>
<?php unset($__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb); ?>
<?php endif; ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <div
                                        <?php if($isRecordGroupCollapsible = $group->isCollapsible()): ?>
                                            x-on:click="toggleCollapseGroup(<?php echo \Illuminate\Support\Js::from($recordGroupTitle)->toHtml() ?>)"
                                            <?php if(! $hasSummary): ?>
                                                x-bind:class="{ 'fi-collapsed': isGroupCollapsed(<?php echo \Illuminate\Support\Js::from($recordGroupTitle)->toHtml() ?>) }"
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'fi-ta-group-header',
                                            'fi-collapsible' => $isRecordGroupCollapsible,
                                        ]); ?>"
                                    >
                                        <?php if($isSelectionEnabled && ($maxSelectableRecords !== 1)): ?>
                                            <input
                                                aria-label="<?php echo e(__('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle])); ?>"
                                                type="checkbox"
                                                data-group-selectable-record-keys="<?php echo e(json_encode($this->getGroupedSelectableTableRecordKeys($recordGroupKey))); ?>"
                                                <?php if($isSelectionDisabled): ?>
                                                    disabled
                                                <?php else: ?>
                                                    x-on:click="toggleSelectRecords(JSON.parse($el.dataset.groupSelectableRecordKeys))"
                                                    <?php if($maxSelectableRecords): ?>
                                                        x-bind:disabled="
                                                            const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                                                            return recordsInGroup.length && ! areRecordsToggleable(recordsInGroup)
                                                        "
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                x-bind:checked="
                                                    const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                                                    if (recordsInGroup.length && areRecordsSelected(recordsInGroup)) {
                                                        $el.checked = true

                                                        return 'checked'
                                                    }

                                                    $el.checked = false

                                                    return null
                                                "
                                                wire:key="<?php echo e($this->getId()); ?>.table.bulk_select_group.checkbox.<?php echo e($page); ?>"
                                                wire:loading.attr="disabled"
                                                wire:target="<?php echo e(implode(',', \Filament\Tables\Table::LOADING_TARGETS)); ?>"
                                                class="fi-ta-group-checkbox fi-checkbox-input"
                                            />
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <div>
                                            <<?php echo e($secondLevelHeadingTag); ?>

                                                class="fi-ta-group-heading"
                                            >
                                                <!--[if BLOCK]><![endif]--><?php if(filled($recordGroupLabel = ($group->isTitlePrefixedWithLabel() ? $group->getLabel() : null))): ?>
                                                        <?php echo e($recordGroupLabel); ?>:
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <?php echo e($recordGroupTitle); ?>

                                            </<?php echo e($secondLevelHeadingTag); ?>>

                                            <!--[if BLOCK]><![endif]--><?php if(filled($recordGroupDescription = $group->getDescription($record, $recordGroupTitle))): ?>
                                                <p
                                                    class="fi-ta-group-description"
                                                >
                                                    <?php echo e($recordGroupDescription); ?>

                                                </p>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>

                                        <!--[if BLOCK]><![endif]--><?php if($isRecordGroupCollapsible): ?>
                                            <button
                                                aria-label="<?php echo e(filled($recordGroupLabel) ? ($recordGroupLabel . ': ' . $recordGroupTitle) : $recordGroupTitle); ?>"
                                                x-bind:aria-expanded="! isGroupCollapsed(<?php echo \Illuminate\Support\Js::from($recordGroupTitle)->toHtml() ?>)"
                                                type="button"
                                                class="fi-icon-btn fi-size-sm"
                                            >
                                                <?php echo e(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronUp, alias: \Filament\Tables\View\TablesIconAlias::GROUPING_COLLAPSE_BUTTON, size: \Filament\Support\Enums\IconSize::Small)); ?>

                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <div
                                    <?php if($hasCollapsibleColumnsLayout): ?>
                                        x-data="{ isCollapsed: <?php echo \Illuminate\Support\Js::from($collapsibleColumnsLayout->isCollapsed())->toHtml() ?> }"
                                        x-init="$dispatch('collapsible-table-row-initialized')"
                                        x-on:collapse-all-table-rows.window="isCollapsed = true"
                                        x-on:expand-all-table-rows.window="isCollapsed = false"
                                        x-bind:class="isCollapsed && 'fi-ta-record-collapsed'"
                                    <?php endif; ?>
                                    wire:key="<?php echo e($this->getId()); ?>.table.records.<?php echo e($recordKey); ?>"
                                    <?php if($isReordering): ?>
                                        x-sortable-item="<?php echo e($recordKey); ?>"
                                        x-sortable-handle
                                    <?php endif; ?>
                                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                        'fi-ta-record',
                                        'fi-clickable' => $recordUrl || $recordAction,
                                        'fi-ta-record-with-content-prefix' => $isReordering || ($isSelectionEnabled && $isRecordSelectable($record)),
                                        'fi-ta-record-with-content-suffix' => $hasCollapsibleColumnsLayout && (! $isReordering),
                                        ...$getRecordClasses($record),
                                    ]); ?>"
                                    x-bind:class="{
                                        <?php echo e($group?->isCollapsible() ? '\'fi-collapsed\': isGroupCollapsed(' . \Illuminate\Support\Js::from($recordGroupTitle) . '),' : ''); ?>

                                        'fi-selected': isRecordSelected(<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>),
                                    }"
                                >
                                    <?php
                                        $hasItemBeforeRecordContent = $isReordering || ($isSelectionEnabled && $isRecordSelectable($record));
                                        $hasItemAfterRecordContent = $hasCollapsibleColumnsLayout && (! $isReordering);
                                    ?>

                                    <!--[if BLOCK]><![endif]--><?php if($isReordering): ?>
                                        <button
                                            class="fi-ta-reorder-handle fi-icon-btn"
                                            type="button"
                                        >
                                            <?php echo e(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE)); ?>

                                        </button>
                                    <?php elseif($isSelectionEnabled && $isRecordSelectable($record)): ?>
                                        <input
                                            aria-label="<?php echo e(__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])); ?>"
                                            type="checkbox"
                                            <?php if($isSelectionDisabled): ?>
                                                disabled
                                            <?php elseif($maxSelectableRecords && ($maxSelectableRecords !== 1)): ?>
                                                x-bind:disabled="! areRecordsToggleable([<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>])"
                                            <?php endif; ?>
                                            value="<?php echo e($recordKey); ?>"
                                            x-on:click="toggleSelectedRecord(<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>)"
                                            x-bind:checked="isRecordSelected(<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>) ? 'checked' : null"
                                            data-group="<?php echo e($recordGroupKey); ?>"
                                            wire:loading.attr="disabled"
                                            wire:target="<?php echo e(implode(',', \Filament\Tables\Table::LOADING_TARGETS)); ?>"
                                            class="fi-ta-record-checkbox fi-checkbox-input"
                                        />
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <div class="fi-ta-record-content-ctn">
                                        <div>
                                            <!--[if BLOCK]><![endif]--><?php if($recordUrl): ?>
                                                <a
                                                    <?php echo e(\Filament\Support\generate_href_html($recordUrl, $openRecordUrlInNewTab)); ?>

                                                    class="fi-ta-record-content"
                                                >
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $columnsLayout; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columnsLayoutComponent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($columnsLayoutComponent
                                                                ->record($record)
                                                                ->recordKey($recordKey)
                                                                ->rowLoop($loop)
                                                                ->renderInLayout()); ?>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </a>
                                            <?php elseif($recordAction): ?>
                                                <?php
                                                    $recordWireClickAction = $getRecordAction($record)
                                                        ? "mountTableAction('{$recordAction}', '{$recordKey}')"
                                                        : $recordWireClickAction = "{$recordAction}('{$recordKey}')";
                                                ?>

                                                <button
                                                    type="button"
                                                    wire:click="<?php echo e($recordWireClickAction); ?>"
                                                    wire:loading.attr="disabled"
                                                    wire:target="<?php echo e($recordWireClickAction); ?>"
                                                    class="fi-ta-record-content"
                                                >
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $columnsLayout; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columnsLayoutComponent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($columnsLayoutComponent
                                                                ->record($record)
                                                                ->recordKey($recordKey)
                                                                ->rowLoop($loop)
                                                                ->renderInLayout()); ?>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </button>
                                            <?php else: ?>
                                                <div
                                                    class="fi-ta-record-content"
                                                >
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $columnsLayout; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columnsLayoutComponent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($columnsLayoutComponent
                                                                ->record($record)
                                                                ->recordKey($recordKey)
                                                                ->rowLoop($loop)
                                                                ->renderInLayout()); ?>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if($hasCollapsibleColumnsLayout && (! $isReordering)): ?>
                                                <div
                                                    x-collapse
                                                    x-show="! isCollapsed"
                                                    class="fi-ta-record-content fi-collapsible"
                                                >
                                                    <?php echo e($collapsibleColumnsLayout); ?>

                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>

                                        <!--[if BLOCK]><![endif]--><?php if($recordActions && (! $isReordering)): ?>
                                            <div
                                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                    'fi-ta-actions fi-wrapped sm:fi-not-wrapped',
                                                    match ($recordActionsAlignment ?? Alignment::Start) {
                                                        Alignment::Start => 'fi-align-start',
                                                        Alignment::Center => 'fi-align-center',
                                                        Alignment::End => 'fi-align-end',
                                                    } => $contentGrid,
                                                    'fi-align-start md:fi-align-end' => ! $contentGrid,
                                                    'fi-ta-actions-before-columns-position' => $recordActionsPosition === RecordActionsPosition::BeforeColumns,
                                                ]); ?>"
                                            >
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recordActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo e($action); ?>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    <!--[if BLOCK]><![endif]--><?php if($hasCollapsibleColumnsLayout && (! $isReordering)): ?>
                                        <button
                                            type="button"
                                            x-on:click="isCollapsed = ! isCollapsed"
                                            class="fi-ta-record-collapse-btn fi-icon-btn"
                                        >
                                            <?php echo e(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronDown, alias: \Filament\Tables\View\TablesIconAlias::COLUMNS_COLLAPSE_BUTTON)); ?>

                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <?php
                                    $previousRecordGroupKey = $recordGroupKey;
                                    $previousRecordGroupTitle = $recordGroupTitle;
                                    $previousRecord = $record;
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                            <?php if($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages()))): ?>
                                <table class="fi-ta-table">
                                    <tbody>
                                        <?php
                                            $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                                        ?>

                                        <?php if (isset($component)) { $__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.summary.row','data' => ['columns' => $columns,'extraHeadingColumn' => true,'heading' => __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel]),'placeholderColumns' => false,'query' => $groupScopedAllTableSummaryQuery,'selectedState' => $groupedSummarySelectedState[$previousRecordGroupKey] ?? []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::summary.row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'extra-heading-column' => true,'heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])),'placeholder-columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'query' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupScopedAllTableSummaryQuery),'selected-state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupedSummarySelectedState[$previousRecordGroupKey] ?? [])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb)): ?>
<?php $attributes = $__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb; ?>
<?php unset($__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb)): ?>
<?php $component = $__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb; ?>
<?php unset($__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb); ?>
<?php endif; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php if(($content || $hasColumnsLayout) && $contentFooter): ?>
                        <?php echo e($contentFooter->with([
                                'columns' => $columns,
                                'records' => $records,
                            ])); ?>

                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php if($hasSummary && (! $isReordering)): ?>
                        <table class="fi-ta-table">
                            <tbody>
                                <?php if (isset($component)) { $__componentOriginala8bb2de295dfa9cddf00151a9ea585e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb2de295dfa9cddf00151a9ea585e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.summary.index','data' => ['columns' => $columns,'extraHeadingColumn' => true,'placeholderColumns' => false,'pluralModelLabel' => $pluralModelLabel,'records' => $records]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::summary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'extra-heading-column' => true,'placeholder-columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'plural-model-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pluralModelLabel),'records' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($records)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb2de295dfa9cddf00151a9ea585e7)): ?>
<?php $attributes = $__attributesOriginala8bb2de295dfa9cddf00151a9ea585e7; ?>
<?php unset($__attributesOriginala8bb2de295dfa9cddf00151a9ea585e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb2de295dfa9cddf00151a9ea585e7)): ?>
<?php $component = $__componentOriginala8bb2de295dfa9cddf00151a9ea585e7; ?>
<?php unset($__componentOriginala8bb2de295dfa9cddf00151a9ea585e7); ?>
<?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php elseif((! ($content || $hasColumnsLayout)) && ($records !== null)): ?>
                    <table class="fi-ta-table">
                        <thead>
                            <!--[if BLOCK]><![endif]--><?php if($hasColumnGroups): ?>
                                <tr class="fi-ta-table-head-groups-row">
                                    <!--[if BLOCK]><![endif]--><?php if(count($records)): ?>
                                        <!--[if BLOCK]><![endif]--><?php if($isReordering): ?>
                                            <th></th>
                                        <?php else: ?>
                                            <!--[if BLOCK]><![endif]--><?php if(count($defaultRecordActions) && in_array($recordActionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns])): ?>
                                                <th></th>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells): ?>
                                                <th></th>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $columnsLayout; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columnGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <!--[if BLOCK]><![endif]--><?php if($columnGroup instanceof Column): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($columnGroup->isVisible() && (! $columnGroup->isToggledHidden())): ?>
                                                <th></th>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php elseif($columnGroup instanceof ColumnGroup): ?>
                                            <?php
                                                $columnGroupColumnsCount = count($columnGroup->getVisibleColumns());
                                            ?>

                                            <!--[if BLOCK]><![endif]--><?php if($columnGroupColumnsCount): ?>
                                                <th
                                                    colspan="<?php echo e($columnGroupColumnsCount); ?>"
                                                    <?php echo e($columnGroup->getExtraHeaderAttributeBag()->class([
                                                            'fi-ta-header-group-cell',
                                                            'fi-wrapped' => $columnGroup->canHeaderWrap(),
                                                            ((($columnGroupAlignment = $columnGroup->getAlignment()) instanceof \Filament\Support\Enums\Alignment) ? "fi-align-{$columnGroupAlignment->value}" : (is_string($columnGroupAlignment) ? $columnGroupAlignment : '')),
                                                            (filled($columnGroupHiddenFrom = $columnGroup->getHiddenFrom()) ? "{$columnGroupHiddenFrom}:fi-hidden" : ''),
                                                            (filled($columnGroupVisibleFrom = $columnGroup->getVisibleFrom()) ? "{$columnGroupVisibleFrom}:fi-visible" : ''),
                                                        ])); ?>

                                                >
                                                    <?php echo e($columnGroup->getLabel()); ?>

                                                </th>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                                    <?php if((! $isReordering) && count($records)): ?>
                                        <?php if(count($defaultRecordActions) && in_array($recordActionsPosition, [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells])): ?>
                                            <th></th>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells): ?>
                                            <th></th>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <tr>
                                <!--[if BLOCK]><![endif]--><?php if(count($records)): ?>
                                    <!--[if BLOCK]><![endif]--><?php if($isReordering): ?>
                                        <th></th>
                                    <?php else: ?>
                                        <?php if(count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeCells): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($recordActionsColumnLabel): ?>
                                                <th class="fi-ta-header-cell">
                                                    <?php echo e($recordActionsColumnLabel); ?>

                                                </th>
                                            <?php else: ?>
                                                <th
                                                    aria-label="<?php echo e(trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount)); ?>"
                                                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                                ></th>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells): ?>
                                            <th
                                                class="fi-ta-cell fi-ta-selection-cell"
                                            >
                                                <!--[if BLOCK]><![endif]--><?php if($maxSelectableRecords !== 1): ?>
                                                    <input
                                                        aria-label="<?php echo e(__('filament-tables::table.fields.bulk_select_page.label')); ?>"
                                                        type="checkbox"
                                                        <?php if($isSelectionDisabled): ?>
                                                            disabled
                                                        <?php elseif($maxSelectableRecords): ?>
                                                            x-bind:disabled="
                                                                const recordsOnPage = getRecordsOnPage()

                                                                return recordsOnPage.length && ! areRecordsToggleable(recordsOnPage)
                                                            "
                                                        <?php endif; ?>
                                                        x-bind:checked="
                                                            const recordsOnPage = getRecordsOnPage()

                                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                                $el.checked = true

                                                                return 'checked'
                                                            }

                                                            $el.checked = false

                                                            return null
                                                        "
                                                        x-on:click="toggleSelectRecordsOnPage"
                                                        
                                                        wire:key="<?php echo e($this->getId()); ?>.table.bulk-select-page.checkbox.<?php echo e(\Illuminate\Support\Str::random()); ?>"
                                                        wire:loading.attr="disabled"
                                                        wire:target="<?php echo e(implode(',', \Filament\Tables\Table::LOADING_TARGETS)); ?>"
                                                        class="fi-ta-page-checkbox fi-checkbox-input"
                                                    />
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </th>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <?php if(count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeColumns): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($recordActionsColumnLabel): ?>
                                                <th class="fi-ta-header-cell">
                                                    <?php echo e($recordActionsColumnLabel); ?>

                                                </th>
                                            <?php else: ?>
                                                <th
                                                    aria-label="<?php echo e(trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount)); ?>"
                                                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                                ></th>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $columnName = $column->getName();
                                        $columnLabel = $column->getLabel();
                                        $columnAlignment = $column->getAlignment();
                                        $columnWidth = $column->getWidth();
                                        $isColumnActivelySorted = $getSortColumn() === $column->getName();
                                        $isColumnSortable = $column->isSortable() && (! $isReordering);
                                    ?>

                                    <th
                                        <?php if($isColumnActivelySorted): ?>
                                            aria-sort="<?php echo e($sortDirection === 'asc' ? 'ascending' : 'descending'); ?>"
                                        <?php endif; ?>
                                        <?php echo e($column->getExtraHeaderAttributeBag()
                                                ->class([
                                                    'fi-ta-header-cell',
                                                    'fi-ta-header-cell-' . str($columnName)->camel()->kebab(),
                                                    'fi-growable' => blank($columnWidth) && $column->canGrow(default: false),
                                                    'fi-grouped' => $column->getGroup(),
                                                    'fi-wrapped' => $column->canHeaderWrap(),
                                                    'fi-ta-header-cell-sorted' => $isColumnActivelySorted,
                                                    ((($columnAlignment = $column->getAlignment()) instanceof \Filament\Support\Enums\Alignment) ? "fi-align-{$columnAlignment->value}" : (is_string($columnAlignment) ? $columnAlignment : '')),
                                                    (filled($columnHiddenFrom = $column->getHiddenFrom()) ? "{$columnHiddenFrom}:fi-hidden" : ''),
                                                    (filled($columnVisibleFrom = $column->getVisibleFrom()) ? "{$columnVisibleFrom}:fi-visible" : ''),
                                                ])
                                                ->style([
                                                    ('width: ' . $columnWidth) => filled($columnWidth),
                                                ])); ?>

                                    >
                                        <!--[if BLOCK]><![endif]--><?php if($isColumnSortable): ?>
                                            <span
                                                aria-label="<?php echo e(trim(strip_tags($columnLabel))); ?>"
                                                role="button"
                                                tabindex="0"
                                                wire:click="sortTable('<?php echo e($columnName); ?>')"
                                                x-on:keydown.enter.prevent.stop="$wire.sortTable('<?php echo e($columnName); ?>')"
                                                x-on:keydown.space.prevent.stop="$wire.sortTable('<?php echo e($columnName); ?>')"
                                                wire:loading.attr="disabled"
                                                class="fi-ta-header-cell-sort-btn"
                                            >
                                                <?php echo e($columnLabel); ?>


                                                <?php echo e(\Filament\Support\generate_icon_html(($isColumnActivelySorted && $sortDirection === 'asc') ? \Filament\Support\Icons\Heroicon::ChevronUp : \Filament\Support\Icons\Heroicon::ChevronDown, alias: match (true) {
                                                        $isColumnActivelySorted && ($sortDirection === 'asc') => \Filament\Tables\View\TablesIconAlias::HEADER_CELL_SORT_ASC_BUTTON,
                                                        $isColumnActivelySorted && ($sortDirection === 'desc') => \Filament\Tables\View\TablesIconAlias::HEADER_CELL_SORT_DESC_BUTTON,
                                                        default => \Filament\Tables\View\TablesIconAlias::HEADER_CELL_SORT_BUTTON,
                                                    })); ?>

                                            </span>
                                        <?php else: ?>
                                            <?php echo e($columnLabel); ?>

                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                                <?php if((! $isReordering) && count($records)): ?>
                                    <?php if(count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::AfterColumns): ?>
                                        <!--[if BLOCK]><![endif]--><?php if($recordActionsColumnLabel): ?>
                                            <th
                                                class="fi-ta-header-cell fi-align-end"
                                            >
                                                <?php echo e($recordActionsColumnLabel); ?>

                                            </th>
                                        <?php else: ?>
                                            <th
                                                aria-label="<?php echo e(trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount)); ?>"
                                                class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                            ></th>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells): ?>
                                        <th
                                            class="fi-ta-cell fi-ta-selection-cell"
                                        >
                                            <!--[if BLOCK]><![endif]--><?php if($maxSelectableRecords !== 1): ?>
                                                <input
                                                    aria-label="<?php echo e(__('filament-tables::table.fields.bulk_select_page.label')); ?>"
                                                    type="checkbox"
                                                    <?php if($isSelectionDisabled): ?>
                                                        disabled
                                                    <?php elseif($maxSelectableRecords): ?>
                                                        x-bind:disabled="
                                                            const recordsOnPage = getRecordsOnPage()

                                                            return recordsOnPage.length && ! areRecordsToggleable(recordsOnPage)
                                                        "
                                                    <?php endif; ?>
                                                    x-bind:checked="
                                                        const recordsOnPage = getRecordsOnPage()

                                                        if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                            $el.checked = true

                                                            return 'checked'
                                                        }

                                                        $el.checked = false

                                                        return null
                                                    "
                                                    x-on:click="toggleSelectRecordsOnPage"
                                                    
                                                    wire:key="<?php echo e($this->getId()); ?>.table.bulk-select-page.checkbox.<?php echo e(\Illuminate\Support\Str::random()); ?>"
                                                    wire:loading.attr="disabled"
                                                    wire:target="<?php echo e(implode(',', \Filament\Tables\Table::LOADING_TARGETS)); ?>"
                                                    class="fi-ta-page-checkbox fi-checkbox-input"
                                                />
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </th>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <?php if(count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::AfterCells): ?>
                                        <!--[if BLOCK]><![endif]--><?php if($recordActionsColumnLabel): ?>
                                            <th
                                                class="fi-ta-header-cell fi-align-end"
                                            >
                                                <?php echo e($recordActionsColumnLabel); ?>

                                            </th>
                                        <?php else: ?>
                                            <th
                                                aria-label="<?php echo e(trans_choice('filament-tables::table.columns.actions.label', $flatRecordActionsCount)); ?>"
                                                class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                            ></th>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tr>
                        </thead>

                        <!--[if BLOCK]><![endif]--><?php if($isColumnSearchVisible || count($records)): ?>
                            <tbody
                                <?php if($isReorderable): ?>
                                    x-on:end.stop="
                                        $wire.reorderTable(
                                            $event.target.sortable.toArray(),
                                            $event.item.getAttribute('x-sortable-item'),
                                        )
                                    "
                                    x-sortable
                                    data-sortable-animation-duration="<?php echo e($getReorderAnimationDuration()); ?>"
                                <?php endif; ?>
                            >
                                <!--[if BLOCK]><![endif]--><?php if($isColumnSearchVisible): ?>
                                    <tr
                                        class="fi-ta-row fi-ta-row-not-reorderable"
                                    >
                                        <!--[if BLOCK]><![endif]--><?php if(count($records)): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($isReordering): ?>
                                                <td></td>
                                            <?php else: ?>
                                                <!--[if BLOCK]><![endif]--><?php if(count($defaultRecordActions) && in_array($recordActionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns])): ?>
                                                    <td></td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells): ?>
                                                    <td></td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $columnName = $column->getName();
                                            ?>

                                            <td
                                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                    'fi-ta-cell',
                                                    'fi-ta-individual-search-cell' => $isIndividuallySearchable = $column->isIndividuallySearchable(),
                                                    'fi-ta-individual-search-cell-' . str($columnName)->camel()->kebab() => $isIndividuallySearchable,
                                                ]); ?>"
                                            >
                                                <!--[if BLOCK]><![endif]--><?php if($isIndividuallySearchable): ?>
                                                    <?php if (isset($component)) { $__componentOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.search-field','data' => ['debounce' => $searchDebounce,'onBlur' => $isSearchOnBlur,'wireModel' => 'tableColumnSearches.' . $columnName]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::search-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['debounce' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchDebounce),'on-blur' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSearchOnBlur),'wire-model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('tableColumnSearches.' . $columnName)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b)): ?>
<?php $attributes = $__attributesOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b; ?>
<?php unset($__attributesOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b)): ?>
<?php $component = $__componentOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b; ?>
<?php unset($__componentOriginal7ccc00a3eaa8946ec9c0ec17f5ab229b); ?>
<?php endif; ?>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                                        <?php if((! $isReordering) && count($records)): ?>
                                            <?php if(count($defaultRecordActions) && in_array($recordActionsPosition, [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells])): ?>
                                                <td></td>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells): ?>
                                                <td></td>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!--[if BLOCK]><![endif]--><?php if(count($records)): ?>
                                    <?php
                                        $isRecordRowStriped = false;
                                        $previousRecord = null;
                                        $previousRecordGroupKey = null;
                                        $previousRecordGroupTitle = null;
                                    ?>

                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $recordAction = $getRecordAction($record);
                                            $recordKey = $getRecordKey($record);
                                            $recordUrl = $getRecordUrl($record);
                                            $openRecordUrlInNewTab = $shouldOpenRecordUrlInNewTab($record);
                                            $recordGroupKey = $group?->getStringKey($record);
                                            $recordGroupTitle = $group?->getTitle($record);

                                            $recordActions = array_reduce(
                                                $defaultRecordActions,
                                                function (array $carry, $action) use ($record): array {
                                                    $action = $action->getClone();

                                                    if (! $action instanceof \Filament\Actions\BulkAction) {
                                                        $action->record($record);
                                                    }

                                                    if ($action->isHidden()) {
                                                        return $carry;
                                                    }

                                                    $carry[] = $action;

                                                    return $carry;
                                                },
                                                initial: [],
                                            );
                                        ?>

                                        <!--[if BLOCK]><![endif]--><?php if($recordGroupTitle !== $previousRecordGroupTitle): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle)): ?>
                                                <?php
                                                    $groupColumn = $group->getColumn();
                                                    $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                                                ?>

                                                <?php if (isset($component)) { $__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.summary.row','data' => ['actions' => count($defaultRecordActions),'actionsPosition' => $recordActionsPosition,'columns' => $columns,'groupColumn' => $groupColumn,'groupsOnly' => $isGroupsOnly,'heading' => $isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel]),'query' => $groupScopedAllTableSummaryQuery,'recordCheckboxPosition' => $recordCheckboxPosition,'selectedState' => $groupedSummarySelectedState[$previousRecordGroupKey] ?? [],'selectionEnabled' => $isSelectionEnabled]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::summary.row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(count($defaultRecordActions)),'actions-position' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recordActionsPosition),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'group-column' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupColumn),'groups-only' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGroupsOnly),'heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])),'query' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupScopedAllTableSummaryQuery),'record-checkbox-position' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recordCheckboxPosition),'selected-state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupedSummarySelectedState[$previousRecordGroupKey] ?? []),'selection-enabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSelectionEnabled)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb)): ?>
<?php $attributes = $__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb; ?>
<?php unset($__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb)): ?>
<?php $component = $__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb; ?>
<?php unset($__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb); ?>
<?php endif; ?>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if(! $isGroupsOnly): ?>
                                                <tr
                                                    class="fi-ta-row fi-ta-group-header-row"
                                                >
                                                    <?php
                                                        $isRecordGroupCollapsible = $group?->isCollapsible();
                                                        $groupHeaderColspan = $columnsCount;

                                                        if ($isSelectionEnabled) {
                                                            $groupHeaderColspan--;

                                                            if (
                                                                ($recordCheckboxPosition === RecordCheckboxPosition::BeforeCells) &&
                                                                count($defaultRecordActions) &&
                                                                ($recordActionsPosition === RecordActionsPosition::BeforeCells)
                                                            ) {
                                                                $groupHeaderColspan--;
                                                            }
                                                        }
                                                    ?>

                                                    <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells): ?>
                                                        <?php if(count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeCells): ?>
                                                            <td></td>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                        <td
                                                            class="fi-ta-cell fi-ta-group-selection-cell"
                                                        >
                                                            <!--[if BLOCK]><![endif]--><?php if($maxSelectableRecords !== 1): ?>
                                                                <input
                                                                    aria-label="<?php echo e(__('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle])); ?>"
                                                                    type="checkbox"
                                                                    data-group-selectable-record-keys="<?php echo e(json_encode($this->getGroupedSelectableTableRecordKeys($recordGroupKey))); ?>"
                                                                    <?php if($isSelectionDisabled): ?>
                                                                        disabled
                                                                    <?php else: ?>
                                                                        x-on:click="toggleSelectRecords(JSON.parse($el.dataset.groupSelectableRecordKeys))"
                                                                        <?php if($maxSelectableRecords): ?>
                                                                            x-bind:disabled="
                                                                                const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                                                                                return recordsInGroup.length && ! areRecordsToggleable(recordsInGroup)
                                                                            "
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                    x-bind:checked="
                                                                        const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                                                                        if (recordsInGroup.length && areRecordsSelected(recordsInGroup)) {
                                                                            $el.checked = true

                                                                            return 'checked'
                                                                        }

                                                                        $el.checked = false

                                                                        return null
                                                                    "
                                                                    wire:key="<?php echo e($this->getId()); ?>.table.bulk_select_group.checkbox.<?php echo e($page); ?>"
                                                                    wire:loading.attr="disabled"
                                                                    wire:target="<?php echo e(implode(',', \Filament\Tables\Table::LOADING_TARGETS)); ?>"
                                                                    class="fi-ta-group-checkbox fi-checkbox-input"
                                                                />
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </td>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                    <td
                                                        colspan="<?php echo e($groupHeaderColspan); ?>"
                                                        class="fi-ta-group-header-cell"
                                                    >
                                                        <div
                                                            <?php if($isRecordGroupCollapsible): ?>
                                                                x-on:click="toggleCollapseGroup(<?php echo \Illuminate\Support\Js::from($recordGroupTitle)->toHtml() ?>)"
                                                                x-bind:class="isGroupCollapsed(<?php echo \Illuminate\Support\Js::from($recordGroupTitle)->toHtml() ?>) ? 'fi-collapsed' : null"
                                                            <?php endif; ?>
                                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                                'fi-ta-group-header',
                                                                'fi-collapsible' => $isRecordGroupCollapsible,
                                                            ]); ?>"
                                                        >
                                                            <div>
                                                                <<?php echo e($secondLevelHeadingTag); ?>

                                                                    class="fi-ta-group-heading"
                                                                >
                                                                    <!--[if BLOCK]><![endif]--><?php if(filled($recordGroupLabel = ($group->isTitlePrefixedWithLabel() ? $group->getLabel() : null))): ?>
                                                                            <?php echo e($recordGroupLabel); ?>:
                                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                                    <?php echo e($recordGroupTitle); ?>

                                                                </<?php echo e($secondLevelHeadingTag); ?>>

                                                                <!--[if BLOCK]><![endif]--><?php if(filled($recordGroupDescription = $group->getDescription($record, $recordGroupTitle))): ?>
                                                                    <p
                                                                        class="fi-ta-group-description"
                                                                    >
                                                                        <?php echo e($recordGroupDescription); ?>

                                                                    </p>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                            </div>

                                                            <!--[if BLOCK]><![endif]--><?php if($isRecordGroupCollapsible): ?>
                                                                <button
                                                                    aria-label="<?php echo e(filled($recordGroupLabel) ? ($recordGroupLabel . ': ' . $recordGroupTitle) : $recordGroupTitle); ?>"
                                                                    x-bind:aria-expanded="! isGroupCollapsed(<?php echo \Illuminate\Support\Js::from($recordGroupTitle)->toHtml() ?>)"
                                                                    type="button"
                                                                    class="fi-icon-btn fi-size-sm"
                                                                >
                                                                    <?php echo e(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronUp, alias: \Filament\Tables\View\TablesIconAlias::GROUPING_COLLAPSE_BUTTON, size: \Filament\Support\Enums\IconSize::Small)); ?>

                                                                </button>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </td>

                                                    <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells): ?>
                                                        <td
                                                            class="fi-ta-cell fi-ta-group-selection-cell"
                                                        >
                                                            <!--[if BLOCK]><![endif]--><?php if($maxSelectableRecords !== 1): ?>
                                                                <input
                                                                    aria-label="<?php echo e(__('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle])); ?>"
                                                                    type="checkbox"
                                                                    data-group-selectable-record-keys="<?php echo e(json_encode($this->getGroupedSelectableTableRecordKeys($recordGroupKey))); ?>"
                                                                    <?php if($isSelectionDisabled): ?>
                                                                        disabled
                                                                    <?php else: ?>
                                                                        x-on:click="toggleSelectRecords(JSON.parse($el.dataset.groupSelectableRecordKeys))"
                                                                        <?php if($maxSelectableRecords): ?>
                                                                            x-bind:disabled="
                                                                                const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                                                                                return recordsInGroup.length && ! areRecordsToggleable(recordsInGroup)
                                                                            "
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                    x-bind:checked="
                                                                        const recordsInGroup = JSON.parse($el.dataset.groupSelectableRecordKeys)

                                                                        if (recordsInGroup.length && areRecordsSelected(recordsInGroup)) {
                                                                            $el.checked = true

                                                                            return 'checked'
                                                                        }

                                                                        $el.checked = false

                                                                        return null
                                                                    "
                                                                    wire:key="<?php echo e($this->getId()); ?>.table.bulk_select_group.checkbox.<?php echo e($page); ?>"
                                                                    wire:loading.attr="disabled"
                                                                    wire:target="<?php echo e(implode(',', \Filament\Tables\Table::LOADING_TARGETS)); ?>"
                                                                    class="fi-ta-group-checkbox fi-checkbox-input"
                                                                />
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </td>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <?php
                                                $isRecordRowStriped = false;
                                            ?>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <!--[if BLOCK]><![endif]--><?php if(! $isGroupsOnly): ?>
                                            <tr
                                                wire:key="<?php echo e($this->getId()); ?>.table.records.<?php echo e($recordKey); ?>"
                                                <?php echo e($isReordering ? 'x-sortable-handle' : null); ?>

                                                <?php echo $isReordering ? 'x-sortable-item="' . e($recordKey) . '"' : null; ?>

                                                x-bind:class="{
                                                    <?php echo e($group?->isCollapsible() ? '\'fi-collapsed\': isGroupCollapsed(' . \Illuminate\Support\Js::from($recordGroupTitle) . '),' : ''); ?>

                                                    'fi-selected': isRecordSelected(<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>),
                                                }"
                                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                    'fi-ta-row',
                                                    'fi-clickable' => $recordAction || $recordUrl,
                                                    'fi-striped' => $isStriped && $isRecordRowStriped,
                                                    ...$getRecordClasses($record),
                                                ]); ?>"
                                            >
                                                <!--[if BLOCK]><![endif]--><?php if($isReordering): ?>
                                                    <td class="fi-ta-cell">
                                                        <button
                                                            class="fi-ta-reorder-handle fi-icon-btn"
                                                            type="button"
                                                        >
                                                            <?php echo e(\Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE)); ?>

                                                        </button>
                                                    </td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <?php if(count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeCells && (! $isReordering)): ?>
                                                    <td class="fi-ta-cell">
                                                        <div
                                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                                'fi-ta-actions',
                                                                match ($recordActionsAlignment) {
                                                                    Alignment::Center => 'fi-align-center',
                                                                    Alignment::Start, Alignment::Left => 'fi-align-start',
                                                                    Alignment::Between, Alignment::Justify => 'fi-align-between',
                                                                    Alignment::End, Alignment::Right => '',
                                                                    default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                                                                },
                                                            ]); ?>"
                                                        >
                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recordActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php echo e($action); ?>

                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && ($recordCheckboxPosition === RecordCheckboxPosition::BeforeCells) && (! $isReordering)): ?>
                                                    <td
                                                        class="fi-ta-cell fi-ta-selection-cell"
                                                    >
                                                        <!--[if BLOCK]><![endif]--><?php if($isRecordSelectable($record)): ?>
                                                            <input
                                                                aria-label="<?php echo e(__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])); ?>"
                                                                type="checkbox"
                                                                <?php if($isSelectionDisabled): ?>
                                                                    disabled
                                                                <?php elseif($maxSelectableRecords && ($maxSelectableRecords !== 1)): ?>
                                                                    x-bind:disabled="! areRecordsToggleable([<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>])"
                                                                <?php endif; ?>
                                                                value="<?php echo e($recordKey); ?>"
                                                                x-on:click="toggleSelectedRecord(<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>)"
                                                                x-bind:checked="isRecordSelected(<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>) ? 'checked' : null"
                                                                data-group="<?php echo e($recordGroupKey); ?>"
                                                                wire:loading.attr="disabled"
                                                                wire:target="<?php echo e(implode(',', \Filament\Tables\Table::LOADING_TARGETS)); ?>"
                                                                class="fi-ta-record-checkbox fi-checkbox-input"
                                                            />
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <?php if(count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::BeforeColumns && (! $isReordering)): ?>
                                                    <td class="fi-ta-cell">
                                                        <div
                                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                                'fi-ta-actions',
                                                                match ($recordActionsAlignment) {
                                                                    Alignment::Center => 'fi-align-center',
                                                                    Alignment::Start, Alignment::Left => 'fi-align-start',
                                                                    Alignment::Between, Alignment::Justify => 'fi-align-between',
                                                                    Alignment::End, Alignment::Right => '',
                                                                    default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                                                                },
                                                            ]); ?>"
                                                        >
                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recordActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php echo e($action); ?>

                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $column->record($record);
                                                        $column->rowLoop($loop->parent);
                                                        $column->recordKey($recordKey);

                                                        $columnAction = $column->getAction();
                                                        $columnUrl = $column->getUrl();
                                                        $columnHasStateBasedUrls = $column->hasStateBasedUrls();
                                                        $isColumnClickDisabled = $column->isClickDisabled() || $isReordering;

                                                        $columnWrapperTag = match (true) {
                                                            ($columnUrl || ($recordUrl && $columnAction === null)) && (! $columnHasStateBasedUrls) && (! $isColumnClickDisabled) => 'a',
                                                            ($columnAction || $recordAction) && (! $columnHasStateBasedUrls) && (! $isColumnClickDisabled) => 'button',
                                                            default => 'div',
                                                        };

                                                        if ($columnWrapperTag === 'button') {
                                                            if ($columnAction instanceof \Filament\Actions\Action) {
                                                                $columnWireClickAction = "mountTableAction('{$columnAction->getName()}', '{$recordKey}')";
                                                            } elseif ($columnAction) {
                                                                $columnWireClickAction = "callTableColumnAction('{$column->getName()}', '{$recordKey}')";
                                                            } else {
                                                                if ($this->getTable()->getAction($recordAction)) {
                                                                    $columnWireClickAction = "mountTableAction('{$recordAction}', '{$recordKey}')";
                                                                } else {
                                                                    $columnWireClickAction = "{$recordAction}('{$recordKey}')";
                                                                }
                                                            }
                                                        }
                                                    ?>

                                                    <td
                                                        wire:key="<?php echo e($this->getId()); ?>.table.record.<?php echo e($recordKey); ?>.column.<?php echo e($column->getName()); ?>"
                                                        <?php echo e($column->getExtraCellAttributeBag()->class([
                                                                'fi-ta-cell',
                                                                'fi-ta-cell-' . str($column->getName())->camel()->kebab(),
                                                                ((($columnAlignment = $column->getAlignment()) instanceof \Filament\Support\Enums\Alignment) ? "fi-align-{$columnAlignment->value}" : (is_string($columnAlignment) ? $columnAlignment : '')),
                                                                ((($columnVerticalAlignment = $column->getVerticalAlignment()) instanceof \Filament\Support\Enums\VerticalAlignment) ? "fi-vertical-align-{$columnVerticalAlignment->value}" : (is_string($columnVerticalAlignment) ? $columnVerticalAlignment : '')),
                                                                (filled($columnHiddenFrom = $column->getHiddenFrom()) ? "{$columnHiddenFrom}:fi-hidden" : ''),
                                                                (filled($columnVisibleFrom = $column->getVisibleFrom()) ? "{$columnVisibleFrom}:fi-visible" : ''),
                                                            ])); ?>

                                                    >
                                                        <<?php echo e($columnWrapperTag); ?>

                                                            <?php if($columnWrapperTag === 'a'): ?>
                                                                <?php echo e(\Filament\Support\generate_href_html($columnUrl ?: $recordUrl, $columnUrl ? $column->shouldOpenUrlInNewTab() : $openRecordUrlInNewTab)); ?>

                                                            <?php elseif($columnWrapperTag === 'button'): ?>
                                                                type
                                                                ="button"
                                                                wire:click.prevent.stop="<?php echo e($columnWireClickAction); ?>"
                                                                wire:loading.attr="disabled"
                                                                wire:target="<?php echo e($columnWireClickAction); ?>"
                                                            <?php endif; ?>
                                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                                'fi-ta-col',
                                                                'fi-ta-col-has-column-url' => ($columnWrapperTag === 'a') && filled($columnUrl),
                                                            ]); ?>"
                                                        >
                                                            <?php echo e($column); ?>

                                                        </<?php echo e($columnWrapperTag); ?>>
                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                                                <?php if(count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::AfterColumns && (! $isReordering)): ?>
                                                    <td class="fi-ta-cell">
                                                        <div
                                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                                'fi-ta-actions',
                                                                match ($recordActionsAlignment) {
                                                                    Alignment::Center => 'fi-align-center',
                                                                    Alignment::Start, Alignment::Left => 'fi-align-start',
                                                                    Alignment::Between, Alignment::Justify => 'fi-align-between',
                                                                    Alignment::End, Alignment::Right => '',
                                                                    default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                                                                },
                                                            ]); ?>"
                                                        >
                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recordActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php echo e($action); ?>

                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <!--[if BLOCK]><![endif]--><?php if($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells && (! $isReordering)): ?>
                                                    <td
                                                        class="fi-ta-cell fi-ta-selection-cell"
                                                    >
                                                        <!--[if BLOCK]><![endif]--><?php if($isRecordSelectable($record)): ?>
                                                            <input
                                                                aria-label="<?php echo e(__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])); ?>"
                                                                type="checkbox"
                                                                <?php if($isSelectionDisabled): ?>
                                                                    disabled
                                                                <?php elseif($maxSelectableRecords && ($maxSelectableRecords !== 1)): ?>
                                                                    x-bind:disabled="! areRecordsToggleable([<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>])"
                                                                <?php endif; ?>
                                                                value="<?php echo e($recordKey); ?>"
                                                                x-on:click="toggleSelectedRecord(<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>)"
                                                                x-bind:checked="isRecordSelected(<?php echo \Illuminate\Support\Js::from($recordKey)->toHtml() ?>) ? 'checked' : null"
                                                                data-group="<?php echo e($recordGroupKey); ?>"
                                                                wire:loading.attr="disabled"
                                                                wire:target="<?php echo e(implode(',', \Filament\Tables\Table::LOADING_TARGETS)); ?>"
                                                                class="fi-ta-record-checkbox fi-checkbox-input"
                                                            />
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <?php if(count($defaultRecordActions) && $recordActionsPosition === RecordActionsPosition::AfterCells && (! $isReordering)): ?>
                                                    <td class="fi-ta-cell">
                                                        <div
                                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                                'fi-ta-actions',
                                                                match ($recordActionsAlignment) {
                                                                    Alignment::Center => 'fi-align-center',
                                                                    Alignment::Start, Alignment::Left => 'fi-align-start',
                                                                    Alignment::Between, Alignment::Justify => 'fi-align-between',
                                                                    Alignment::End, Alignment::Right => '',
                                                                    default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                                                                },
                                                            ]); ?>"
                                                        >
                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recordActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php echo e($action); ?>

                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </tr>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <?php
                                            $isRecordRowStriped = ! $isRecordRowStriped;
                                            $previousRecord = $record;
                                            $previousRecordGroupKey = $recordGroupKey;
                                            $previousRecordGroupTitle = $recordGroupTitle;
                                        ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                                    <?php if($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages()))): ?>
                                        <?php
                                            $groupColumn = $group->getColumn();
                                            $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                                        ?>

                                        <?php if (isset($component)) { $__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.summary.row','data' => ['actions' => count($defaultRecordActions),'actionsPosition' => $recordActionsPosition,'columns' => $columns,'groupColumn' => $groupColumn,'groupsOnly' => $isGroupsOnly,'heading' => $isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel]),'query' => $groupScopedAllTableSummaryQuery,'recordCheckboxPosition' => $recordCheckboxPosition,'selectedState' => $groupedSummarySelectedState[$previousRecordGroupKey] ?? [],'selectionEnabled' => $isSelectionEnabled]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::summary.row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(count($defaultRecordActions)),'actions-position' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recordActionsPosition),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'group-column' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupColumn),'groups-only' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGroupsOnly),'heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])),'query' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupScopedAllTableSummaryQuery),'record-checkbox-position' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recordCheckboxPosition),'selected-state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupedSummarySelectedState[$previousRecordGroupKey] ?? []),'selection-enabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSelectionEnabled)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb)): ?>
<?php $attributes = $__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb; ?>
<?php unset($__attributesOriginala3ad14087ab6b316cf1e1d1a634acbeb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb)): ?>
<?php $component = $__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb; ?>
<?php unset($__componentOriginala3ad14087ab6b316cf1e1d1a634acbeb); ?>
<?php endif; ?>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <?php if($hasSummary && (! $isReordering)): ?>
                                        <?php
                                            $groupColumn = $group?->getColumn();
                                        ?>

                                        <?php if (isset($component)) { $__componentOriginala8bb2de295dfa9cddf00151a9ea585e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb2de295dfa9cddf00151a9ea585e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.summary.index','data' => ['actions' => count($defaultRecordActions),'actionsPosition' => $recordActionsPosition,'columns' => $columns,'groupColumn' => $groupColumn,'groupsOnly' => $isGroupsOnly,'pluralModelLabel' => $pluralModelLabel,'recordCheckboxPosition' => $recordCheckboxPosition,'records' => $records,'selectionEnabled' => $isSelectionEnabled]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::summary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(count($defaultRecordActions)),'actions-position' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recordActionsPosition),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'group-column' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupColumn),'groups-only' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGroupsOnly),'plural-model-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pluralModelLabel),'record-checkbox-position' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recordCheckboxPosition),'records' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($records),'selection-enabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSelectionEnabled)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb2de295dfa9cddf00151a9ea585e7)): ?>
<?php $attributes = $__attributesOriginala8bb2de295dfa9cddf00151a9ea585e7; ?>
<?php unset($__attributesOriginala8bb2de295dfa9cddf00151a9ea585e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb2de295dfa9cddf00151a9ea585e7)): ?>
<?php $component = $__componentOriginala8bb2de295dfa9cddf00151a9ea585e7; ?>
<?php unset($__componentOriginala8bb2de295dfa9cddf00151a9ea585e7); ?>
<?php endif; ?>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if(($records !== null) && count($records) && $contentFooter): ?>
                            <tfoot>
                                <tr>
                                    <?php echo e($contentFooter->with([
                                            'columns' => $columns,
                                            'records' => $records,
                                        ])); ?>

                                </tr>
                            </tfoot>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </table>
                <?php elseif($records === null): ?>
                    <div class="fi-ta-table-loading-ctn">
                        <?php echo e(\Filament\Support\generate_loading_indicator_html(size: \Filament\Support\Enums\IconSize::TwoExtraLarge)); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <?php if(($records !== null) && ! count($records)): ?>
            <!--[if BLOCK]><![endif]--><?php if($emptyState = $getEmptyState()): ?>
                <?php echo e($emptyState); ?>

            <?php else: ?>
                <div class="fi-ta-empty-state">
                    <div class="fi-ta-empty-state-content">
                        <div class="fi-ta-empty-state-icon-bg">
                            <?php echo e(\Filament\Support\generate_icon_html($getEmptyStateIcon(), size: \Filament\Support\Enums\IconSize::Large)); ?>

                        </div>

                        <<?php echo e($secondLevelHeadingTag); ?>

                            class="fi-ta-empty-state-heading"
                        >
                            <?php echo e($getEmptyStateHeading()); ?>

                        </<?php echo e($secondLevelHeadingTag); ?>>

                        <!--[if BLOCK]><![endif]--><?php if(filled($emptyStateDescription = $getEmptyStateDescription())): ?>
                            <p class="fi-ta-empty-state-description">
                                <?php echo e($emptyStateDescription); ?>

                            </p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if($emptyStateActions = array_filter(
                                 $getEmptyStateActions(),
                                 fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
                             )): ?>
                            <div
                                class="fi-ta-actions fi-align-center fi-wrapped"
                            >
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $emptyStateActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo e($action); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if((($records instanceof \Illuminate\Contracts\Pagination\Paginator) || ($records instanceof \Illuminate\Contracts\Pagination\CursorPaginator)) &&
             ((! ($records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)) || $records->total())): ?>
            <?php
                $hasExtremePaginationLinks = $hasExtremePaginationLinks();
                $paginationPageOptions = $getPaginationPageOptions();
            ?>

            <?php if (isset($component)) { $__componentOriginal0c287a00f29f01c8f977078ff96faed4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0c287a00f29f01c8f977078ff96faed4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.pagination.index','data' => ['extremeLinks' => $hasExtremePaginationLinks,'pageOptions' => $paginationPageOptions,'paginator' => $records]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['extreme-links' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasExtremePaginationLinks),'page-options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paginationPageOptions),'paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($records)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0c287a00f29f01c8f977078ff96faed4)): ?>
<?php $attributes = $__attributesOriginal0c287a00f29f01c8f977078ff96faed4; ?>
<?php unset($__attributesOriginal0c287a00f29f01c8f977078ff96faed4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0c287a00f29f01c8f977078ff96faed4)): ?>
<?php $component = $__componentOriginal0c287a00f29f01c8f977078ff96faed4; ?>
<?php unset($__componentOriginal0c287a00f29f01c8f977078ff96faed4); ?>
<?php endif; ?>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($hasFiltersBelowContent): ?>
            <?php if (isset($component)) { $__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.filters','data' => ['applyAction' => $filtersApplyAction,'form' => $filtersForm,'headingTag' => $secondLevelHeadingTag,'class' => 'fi-ta-filters-below-content']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-tables::filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['apply-action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersApplyAction),'form' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filtersForm),'heading-tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($secondLevelHeadingTag),'class' => 'fi-ta-filters-below-content']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39)): ?>
<?php $attributes = $__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39; ?>
<?php unset($__attributesOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39)): ?>
<?php $component = $__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39; ?>
<?php unset($__componentOriginal8fcc8bb3dcedd6e3c85ec7cd95e48b39); ?>
<?php endif; ?>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <?php if (isset($component)) { $__componentOriginal028e05680f6c5b1e293abd7fbe5f9758 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal028e05680f6c5b1e293abd7fbe5f9758 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-actions::components.modals','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-actions::modals'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal028e05680f6c5b1e293abd7fbe5f9758)): ?>
<?php $attributes = $__attributesOriginal028e05680f6c5b1e293abd7fbe5f9758; ?>
<?php unset($__attributesOriginal028e05680f6c5b1e293abd7fbe5f9758); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal028e05680f6c5b1e293abd7fbe5f9758)): ?>
<?php $component = $__componentOriginal028e05680f6c5b1e293abd7fbe5f9758; ?>
<?php unset($__componentOriginal028e05680f6c5b1e293abd7fbe5f9758); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\Users\yasin\OneDrive\Desktop\ADMIN-TABUNG\vendor\filament\tables\resources\views/index.blade.php ENDPATH**/ ?>