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

<div class="mt-6">
    <h3 class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-primary-300/80 dark:text-gray-500"
        x-show="!sidebarCollapsed">
        <?php echo e($title); ?>

    </h3>
    <div class="space-y-1.5">
        <?php echo e($slot); ?>

    </div>
</div><?php /**PATH E:\Dhani\JOB\ConfirmMe-Approval\resources\views/components/sidebar-section.blade.php ENDPATH**/ ?>