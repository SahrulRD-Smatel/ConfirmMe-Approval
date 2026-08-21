<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'collapsed' => false]));

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

foreach (array_filter((['title', 'collapsed' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="mt-4">
    <h3 class="px-3 mb-2 text-xs font-semibold text-primary-300 dark:text-gray-500 uppercase tracking-wider"
        x-show="!sidebarCollapsed">
        <?php echo e($title); ?>

    </h3>
    <div class="space-y-1">
        <?php echo e($slot); ?>

    </div>
</div><?php /**PATH E:\Dhani\JOB\ConfirmMe-Approval\resources\views/components/sidebar-section.blade.php ENDPATH**/ ?>