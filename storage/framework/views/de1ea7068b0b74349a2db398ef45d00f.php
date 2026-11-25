<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(__('errors.422.title')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full flex items-center justify-center p-6 bg-slate-50 text-slate-800">
    <div class="max-w-md w-full p-8 rounded-2xl bg-white border border-amber-200 text-center shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200">
            <?php echo e(__('errors.422.badge')); ?>

        </span>
        <h1 class="text-xl font-bold text-slate-900 mt-3"><?php echo e(__('errors.422.heading')); ?></h1>
        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
            <?php echo e($exception?->getMessage() ?? __('errors.422.default_message')); ?>

        </p>
        <div class="mt-6">
            <a href="javascript:history.back()" class="inline-block px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                &larr; <?php echo e(__('errors.422.back')); ?>

            </a>
        </div>
    </div>
</body>
</html>
<?php /**PATH /app/applet/resources/views/errors/422.blade.php ENDPATH**/ ?>