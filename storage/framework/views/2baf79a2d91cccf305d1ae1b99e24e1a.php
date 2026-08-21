<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'padding' => true,
    'hover' => false,
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
    'padding' => true,
    'hover' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $classes = 'relative overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-soft ring-1 ring-black/5 dark:border-gray-800/80 dark:bg-gray-900 dark:shadow-none dark:ring-white/10';
    $classes .= $padding ? ' p-6' : '';
    $classes .= $hover ? ' transition-all duration-300 hover:-translate-y-0.5 hover:shadow-soft-lg cursor-pointer' : '';
?>

<div <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php echo e($slot); ?>

</div>
<?php /**PATH E:\Dhani\JOB\ConfirmMe-Approval\resources\views/components/ui/card.blade.php ENDPATH**/ ?>