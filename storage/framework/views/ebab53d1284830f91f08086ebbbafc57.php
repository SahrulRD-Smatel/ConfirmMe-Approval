<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('title', null, []); ?> Buat Pengajuan Baru <?php $__env->endSlot(); ?>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('documents.index')); ?>"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-gray-800 text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 shadow-soft transition-colors">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-left','class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left','class' => 'w-5 h-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Buat Pengajuan Baru</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pilih modul dokumen dan lengkapi form pengajuan Anda (Bisa lebih dari satu sekaligus)
                </p>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div x-data="{
        step: <?php echo e(old('documents') ? 2 : 1); ?>,
        searchQuery: '',
        selectedTemplate: null,
        approvalType: '<?php echo e(old('documents.0.approval_type')); ?>',
        isLoading: false,
        // Inisialisasi form repeater dari Laravel old() jika ada error validasi
        forms: [
            <?php if(old('documents')): ?>
                <?php $__currentLoopData = old('documents'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $oldDoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    { id: <?php echo e($index); ?>, title: '<?php echo e(addslashes($oldDoc['title'] ?? '')); ?>', description: '<?php echo e(addslashes($oldDoc['description'] ?? '')); ?>', subtotal: <?php echo e($oldDoc['subtotal'] ?? 0); ?>, taxAmount: <?php echo e($oldDoc['taxAmount'] ?? 0); ?> }<?php echo e(!$loop->last ? ',' : ''); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                { id: Date.now(), title: '', description: '', subtotal: 0, taxAmount: 0 }
            <?php endif; ?>
        ],
        templates: [
            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            {
                code: '<?php echo e($tpl->code); ?>',
                name: '<?php echo e(addslashes($tpl->name)); ?>',
                description: '<?php echo e(addslashes($tpl->description ?? '')); ?>',
                type: '<?php echo e($tpl->approval_type); ?>',
                category: '<?php echo e($tpl->approval_type === 'only' ? 'Administratif' : ($tpl->approval_type === 'loan' ? 'HR & Pinjaman' : 'Keuangan & PO')); ?>',
                icon: '<?php echo e($tpl->approval_type === 'only' ? 'document-text' : ($tpl->approval_type === 'loan' ? 'user-group' : 'banknotes')); ?>',
                color: '<?php echo e($tpl->approval_type === 'only' ? 'blue' : ($tpl->approval_type === 'loan' ? 'pink' : 'emerald')); ?>',
                defaultTitle: '<?php echo e(addslashes($tpl->fields['title'] ?? '')); ?>',
                defaultDesc: '<?php echo e(addslashes($tpl->fields['description'] ?? '')); ?>',
                defaultCategory: '<?php echo e(addslashes($tpl->fields['document_category'] ?? '')); ?>',
                dynamicFields: <?php echo e(Js::from($tpl->fields['dynamic_fields'] ?? [])); ?>

            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        init() {
            // Restore selected template visually if validation failed
            if (this.approvalType) {
                const oldTplCode = '<?php echo e(old('documents.0.template')); ?>';
                if (oldTplCode) {
                    this.selectedTemplate = this.templates.find(t => t.code === oldTplCode);
                }
            }
        },
        filteredTemplates() {
            if (this.searchQuery === '') return this.templates;
            const q = this.searchQuery.toLowerCase();
            return this.templates.filter(t => t.name.toLowerCase().includes(q) || t.description.toLowerCase().includes(q) || t.category.toLowerCase().includes(q));
        },
        hasCategory(cat) {
            return this.filteredTemplates().some(t => t.category === cat);
        },
        getTemplatesByCategory(cat) {
            return this.filteredTemplates().filter(t => t.category === cat);
        },
        selectTemplate(tpl) {
            this.selectedTemplate = tpl;
            this.approvalType = tpl.type;
            
            // Set defaults to form 0
            this.forms = [{ 
                id: Date.now(), 
                title: tpl.defaultTitle || '', 
                description: tpl.defaultDesc || '',
                subtotal: 0,
                taxAmount: 0
            }];
            
            this.step = 2;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        addForm() {
            if (this.forms.length < 10) {
                this.forms.push({
                    id: Date.now(),
                    title: this.selectedTemplate ? this.selectedTemplate.defaultTitle : '',
                    description: this.selectedTemplate ? this.selectedTemplate.defaultDesc : '',
                    subtotal: 0,
                    taxAmount: 0
                });
            } else {
                alert('Maksimal 10 dokumen sekaligus.');
            }
        },
        removeForm(index) {
            if (this.forms.length > 1) {
                this.forms.splice(index, 1);
            }
        },
        resetSelection() {
            this.step = 1;
            this.selectedTemplate = null;
            this.approvalType = '';
            this.forms = [{ id: Date.now(), title: '', description: '', subtotal: 0, taxAmount: 0 }];
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }">

        <!-- STEP 1: Pilih Template -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100" class="space-y-7">
             
            <!-- Search Bar -->
            <div class="relative mx-auto w-full max-w-3xl">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'magnifying-glass','class' => 'w-6 h-6 text-gray-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'w-6 h-6 text-gray-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                </div>
                <input type="text" x-model="searchQuery"
                    class="block w-full rounded-2xl border-0 bg-white py-4 pl-12 pr-4 text-lg text-gray-900 shadow-soft ring-1 ring-inset ring-gray-200 transition-shadow focus:ring-4 focus:ring-inset focus:ring-primary-500/20/10 dark:bg-gray-900 dark:text-white dark:ring-gray-700"
                    placeholder="Cari modul pengajuan (misal: Cuti, Dinas, Pengadaan)...">
            </div>

            <!-- Categories -->
            <template x-for="category in ['Administratif', 'Keuangan & PO', 'HR & Pinjaman']" :key="category">
                <div x-show="hasCategory(category)" class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider pl-2" x-text="category"></h3>
                    
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <template x-for="tpl in getTemplatesByCategory(category)" :key="tpl.code">
                            <div @click="selectTemplate(tpl)"
                                 class="group relative cursor-pointer overflow-hidden rounded-3xl border border-gray-200/80 bg-white/90 p-5 shadow-soft backdrop-blur-xl transition-all duration-300 hover:-translate-y-0.5 hover:shadow-soft-lg dark:border-gray-700/70 dark:bg-gray-900/90">
                                 
                                <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-white/0 to-current opacity-5 rounded-bl-[100px]" :class="`text-${tpl.color}-500`"></div>
                                
                                <div class="flex items-start gap-4 relative z-10">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-300"
                                         :class="`bg-${tpl.color}-100 dark:bg-${tpl.color}-900/30 text-${tpl.color}-600 dark:text-${tpl.color}-400`">
                                        <template x-if="tpl.icon === 'document-text'"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'document-text','class' => 'w-6 h-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'document-text','class' => 'w-6 h-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></template>
                                        <template x-if="tpl.icon === 'user-group'"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'user-group','class' => 'w-6 h-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user-group','class' => 'w-6 h-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></template>
                                        <template x-if="tpl.icon === 'banknotes'"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'banknotes','class' => 'w-6 h-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'banknotes','class' => 'w-6 h-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></template>
                                        <template x-if="tpl.icon === 'shopping-bag'"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'shopping-bag','class' => 'w-6 h-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shopping-bag','class' => 'w-6 h-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></template>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" x-text="tpl.name"></h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2" x-text="tpl.description || 'Tidak ada deskripsi.'"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- STEP 2: Isi Form (BULK) -->
        <div x-show="step === 2" style="display: none;" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0">
             
            <form action="<?php echo e(route('documents.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-7">
                <?php echo csrf_field(); ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                    <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-xl p-4">
                        <div class="flex gap-3">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'exclamation-triangle','class' => 'w-5 h-5 text-danger-500 shrink-0 mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'exclamation-triangle','class' => 'w-5 h-5 text-danger-500 shrink-0 mt-0.5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                            <div>
                                <h3 class="text-sm font-bold text-danger-800 dark:text-danger-200">Terjadi kesalahan pada validasi (Beberapa isian wajib diperbaiki)</h3>
                                <ul class="mt-2 text-sm text-danger-700 dark:text-danger-300 list-disc list-inside">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </ul>
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400 font-medium">Catatan: Harap unggah ulang file lampiran Anda (jika ada) demi keamanan.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <!-- Selected Module Indicator -->
                <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-2xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'sparkles','class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','class' => 'w-5 h-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                        </div>
                        <div>
                            <p class="text-xs text-primary-600 dark:text-primary-400 font-semibold uppercase tracking-wider">Modul Terpilih (Bulk Request)</p>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="selectedTemplate ? selectedTemplate.name : 'Melanjutkan Form'"></h3>
                        </div>
                    </div>
                    <button type="button" @click="resetSelection()" class="text-sm font-medium text-primary-600 hover:text-primary-700 bg-white/50 dark:bg-gray-800/50 hover:bg-white dark:hover:bg-gray-800/80 px-3 py-1.5 rounded-xl transition-colors border border-primary-200 dark:border-primary-700/50">
                        Ganti Modul
                    </button>
                </div>

                <!-- FORM REPEATER -->
                <div class="space-y-7">
                    <template x-for="(form, index) in forms" :key="form.id">
                        <div class="overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-soft transition-all duration-300 dark:border-gray-700/80 dark:bg-gray-900">
                            
                            <!-- Repeater Header -->
                            <div class="bg-gray-50/80 dark:bg-gray-900/50 px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
                                    <span class="bg-primary-500 text-white w-7 h-7 rounded-full flex items-center justify-center text-sm shadow-soft" x-text="index + 1"></span>
                                    <span>Dokumen #<span x-text="index + 1"></span></span>
                                </h3>
                                <button type="button" @click="removeForm(index)" x-show="forms.length > 1" 
                                    class="text-danger-500 hover:text-danger-700 hover:bg-danger-50 dark:hover:bg-danger-900/30 px-3 py-1.5 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','class' => 'w-4 h-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Hapus
                                </button>
                            </div>

                            <div class="p-6 space-y-8">
                                <!-- Hidden bindings -->
                                <input type="hidden" :name="`documents[${index}][approval_type]`" x-model="approvalType">
                                <input type="hidden" :name="`documents[${index}][template]`" x-bind:value="selectedTemplate ? selectedTemplate.code : ''">

                                <!-- BASIC INFO -->
                                <div>
                                    <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Informasi Utama</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Dokumen <span class="text-danger-500">*</span></label>
                                            <input type="text" :name="`documents[${index}][title]`" required x-model="form.title"
                                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritas <span class="text-danger-500">*</span></label>
                                            <select :name="`documents[${index}][priority]`" required class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                                                <option value="low">Rendah</option>
                                                <option value="normal" selected>Normal</option>
                                                <option value="high">Tinggi</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi <span class="text-danger-500">*</span></label>
                                            <textarea :name="`documents[${index}][description]`" rows="3" required x-model="form.description"
                                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- PO SPECIFIC -->
                                <div x-show="approvalType === 'po'" class="pt-6 border-t border-gray-100 dark:border-gray-700"
                                     x-data="{
                                        subtotal: form.subtotal || 0,
                                        taxAmount: form.taxAmount || 0,
                                        grandTotal: 0,
                                        init() { this.calc(); },
                                        calc() {
                                            this.taxAmount = Math.round(this.subtotal * 0.11);
                                            this.grandTotal = parseInt(this.subtotal || 0) + parseInt(this.taxAmount || 0);
                                        },
                                        fmt(n) { return new Intl.NumberFormat('id-ID').format(n); }
                                     }">
                                    <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Detail Purchase Order</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Vendor <span class="text-danger-500">*</span></label>
                                            <input type="text" :name="`documents[${index}][vendor_name]`" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white focus:ring-4 focus:ring-primary-500/20/10">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori Pembelian <span class="text-danger-500">*</span></label>
                                            <select :name="`documents[${index}][po_category]`" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white focus:ring-4 focus:ring-primary-500/20/10">
                                                <option value="internal">Internal Use</option>
                                                <option value="external">External / Client</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rincian Barang/Jasa <span class="text-danger-500">*</span></label>
                                            <textarea :name="`documents[${index}][item_description]`" rows="2" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white focus:ring-4 focus:ring-primary-500/20/10"></textarea>
                                        </div>
                                        <div class="md:col-span-2 bg-gray-50/80 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (DPP) <span class="text-danger-500">*</span></label>
                                                    <input type="number" :name="`documents[${index}][subtotal]`" x-model="subtotal" @input="calc" min="0" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">PPN 11%</label>
                                                    <input type="number" :name="`documents[${index}][tax_amount]`" x-model="taxAmount" @input="grandTotal = parseInt(subtotal||0) + parseInt(taxAmount||0)" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100" readonly>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex justify-between items-center text-lg font-bold text-primary-700">
                                                <span>Grand Total</span>
                                                <span x-text="'Rp ' + fmt(grandTotal)">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- OTHER TYPES (Simplified for Bulk UI demonstration, typically same as single form) -->
                                <div x-show="approvalType === 'only'" class="pt-6 border-t border-gray-100 dark:border-gray-700">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Dokumen</label>
                                            <input type="text" :name="`documents[${index}][document_category]`" readonly :value="selectedTemplate ? selectedTemplate.defaultCategory : ''" class="block w-full px-4 py-3 border border-gray-200 bg-gray-50 rounded-xl text-gray-500 cursor-not-allowed">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berlaku</label>
                                            <input type="date" :name="`documents[${index}][effective_date]`" class="block w-full px-4 py-3 border border-gray-300 rounded-xl">
                                        </div>
                                    </div>
                                </div>
                                <!-- DYNAMIC FIELDS -->
                                <div x-show="selectedTemplate && selectedTemplate.dynamicFields && selectedTemplate.dynamicFields.length > 0" class="pt-6 border-t border-gray-100 dark:border-gray-700">
                                    <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Informasi Spesifik</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <template x-for="field in (selectedTemplate?.dynamicFields || [])" :key="field.name">
                                            <div :class="['textarea', 'table'].includes(field.type) ? 'md:col-span-2' : ''">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    <span x-text="field.label"></span>
                                                    <span x-show="field.required == '1'" class="text-danger-500">*</span>
                                                </label>
                                                
                                                <!-- Text / Number / Date / Time -->
                                                <template x-if="['text', 'number', 'date', 'time'].includes(field.type)">
                                                    <input :type="field.type" :name="`documents[${index}][metadata][${field.name}]`" :required="field.required == '1'"
                                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                                                </template>

                                                <!-- Textarea -->
                                                <template x-if="field.type === 'textarea'">
                                                    <textarea :name="`documents[${index}][metadata][${field.name}]`" rows="3" :required="field.required == '1'"
                                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"></textarea>
                                                </template>

                                                <!-- Select -->
                                                <template x-if="field.type === 'select'">
                                                    <select :name="`documents[${index}][metadata][${field.name}]`" :required="field.required == '1'"
                                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                                                        <option value="">-- Pilih --</option>
                                                        <template x-for="opt in (field.options ? field.options.split(',').map(s=>s.trim()) : [])">
                                                            <option :value="opt" x-text="opt"></option>
                                                        </template>
                                                    </select>
                                                </template>

                                                <!-- File -->
                                                <template x-if="field.type === 'file'">
                                                    <input type="file" :name="`documents[${index}][metadata_files][${field.name}]`" :required="field.required == '1'"
                                                        class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                                </template>

                                                <!-- Table -->
                                                <template x-if="field.type === 'table'">
                                                    <div class="bg-gray-50/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                                                         x-data="{
                                                            rows: [{}],
                                                            addRow() { this.rows.push({}); },
                                                            removeRow(rIndex) { if(this.rows.length > 1) this.rows.splice(rIndex, 1); },
                                                            getCols(colsStr) { return colsStr ? colsStr.split(',').map(s=>s.trim()) : []; }
                                                         }">
                                                        <div class="overflow-x-auto">
                                                            <table class="w-full text-sm text-left">
                                                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                                    <tr>
                                                                        <template x-for="col in getCols(field.columns)">
                                                                            <th class="px-4 py-3" x-text="col"></th>
                                                                        </template>
                                                                        <th class="px-4 py-3 w-16 text-center">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <template x-for="(row, rIndex) in rows">
                                                                        <tr class="border-b dark:border-gray-700">
                                                                            <template x-for="col in getCols(field.columns)">
                                                                                <td class="p-2">
                                                                                    <input type="text" :name="`documents[${index}][metadata][${field.name}][${rIndex}][${col}]`" :placeholder="col" :required="field.required == '1'"
                                                                                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                                </td>
                                                                            </template>
                                                                            <td class="p-2 text-center">
                                                                                <button type="button" @click="removeRow(rIndex)" class="text-danger-500 hover:text-danger-700" title="Hapus Baris">
                                                                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','class' => 'w-4 h-4 mx-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'w-4 h-4 mx-auto']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    </template>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                                            <button type="button" @click="addRow" class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                                                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','class' => 'w-3 h-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'w-3 h-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Tambah Baris
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- ATTACHMENTS -->
                                <div class="pt-6 border-t border-gray-100 dark:border-gray-700" x-data="{ files: [] }">
                                    <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Lampiran Tambahan <span class="lowercase normal-case font-medium">(Opsional)</span></h4>
                                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer bg-gray-50/80 dark:bg-gray-800/50 hover:bg-gray-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'cloud-arrow-up','class' => 'w-6 h-6 text-gray-400 mb-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'cloud-arrow-up','class' => 'w-6 h-6 text-gray-400 mb-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                                            <p class="text-sm text-gray-500"><span class="font-semibold text-primary-600">Pilih File Tambahan</span> (Jika ada) untuk Dokumen #<span x-text="index+1"></span></p>
                                        </div>
                                        <input :name="`attachments[${index}][]`" type="file" multiple class="hidden" @change="files = Array.from($event.target.files)" />
                                    </label>
                                    <ul class="mt-3 space-y-2" x-show="files.length > 0">
                                        <template x-for="(file, fi) in files" :key="fi">
                                            <li class="text-xs text-primary-700 bg-primary-50 p-2 rounded-xl" x-text="file.name"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- ADD ANOTHER DOC BUTTON -->
                <div class="pt-4 flex justify-center">
                    <button type="button" @click="addForm()" x-show="forms.length < 10" 
                        class="group flex flex-col items-center gap-2 p-6 rounded-3xl border-2 border-dashed border-primary-200 dark:border-primary-800 hover:border-primary-400 dark:hover:border-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 w-full max-w-lg transition-all duration-300">
                        <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','class' => 'w-6 h-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'w-6 h-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                        </div>
                        <div>
                            <span class="block font-bold text-primary-700 dark:text-primary-400 text-lg">Tambah Pengajuan Lain</span>
                            <span class="block text-sm text-primary-500/70" x-text="`${forms.length} dari maksimal 10 form`"></span>
                        </div>
                    </button>
                    
                    <div x-show="forms.length >= 10" class="text-center p-6 text-gray-500">
                        Batas maksimal 10 pengajuan tercapai.
                    </div>
                </div>

                <!-- SUBMIT ACTIONS -->
                <div class="sticky bottom-5 z-40 mt-8 flex items-center justify-end gap-3 rounded-2xl border border-gray-200/80 bg-white/95 p-4 shadow-soft-lg shadow-primary-500/10 backdrop-blur-xl dark:border-gray-700/80 dark:bg-gray-900/95">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','@click' => 'resetSelection()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','@click' => 'resetSelection()']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Batal
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','icon' => 'paper-airplane','class' => 'shadow-lg shadow-primary-500/30 font-bold px-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','icon' => 'paper-airplane','class' => 'shadow-lg shadow-primary-500/30 font-bold px-8']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Ajukan <span x-text="forms.length"></span> Dokumen Sekaligus
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                </div>
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH E:\Dhani\JOB\ConfirmMe-Approval\resources\views/documents/create.blade.php ENDPATH**/ ?>