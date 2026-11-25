<?php $__env->startSection('title', __('auth.login_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight"><?php echo e(__('auth.login_title')); ?></h1>
        <p class="text-xs text-slate-500 mt-1"><?php echo e(__('auth.login_subtitle')); ?></p>
    </div>

    <!-- Quick Demo Accounts Selector (Vanilla JS Quick Fill) -->
    <div class="mb-6 p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider"><?php echo e(__('auth.demo_accounts')); ?></span>
            <span class="text-[10px] text-blue-600 font-medium"><?php echo e(__('auth.click_autofill')); ?></span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <button type="button" onclick="fillDemo('admin@cargotrace.test', 'Password123!')" class="px-2 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-semibold transition cursor-pointer text-center">
                <?php echo e(__('common.roles.admin')); ?>

            </button>
            <button type="button" onclick="fillDemo('operator@cargotrace.test', 'Password123!')" class="px-2 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold transition cursor-pointer text-center">
                <?php echo e(__('common.roles.operator')); ?>

            </button>
            <button type="button" onclick="fillDemo('customer1@cargotrace.test', 'Password123!')" class="px-2 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold transition cursor-pointer text-center">
                <?php echo e(__('common.roles.customer')); ?>

            </button>
        </div>
    </div>

    <!-- Login Form -->
    <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5"><?php echo e(__('auth.email')); ?></label>
            <div class="relative">
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="<?php echo e(old('email')); ?>"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition shadow-2xs"
                    placeholder="name@cargotrace.test"
                />
            </div>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-xs text-rose-600 mt-1 font-medium"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700"><?php echo e(__('auth.password_label')); ?></label>
            </div>
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition shadow-2xs"
                    placeholder="••••••••••••"
                />
            </div>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-xs text-rose-600 mt-1 font-medium"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20"
                />
                <?php echo e(__('auth.remember_me')); ?>

            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button
                type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm hover:shadow transition duration-200 cursor-pointer"
            >
                <?php echo e(__('auth.sign_in_button')); ?>

            </button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
        <?php echo e(__('auth.dont_have_account')); ?>

        <a href="<?php echo e(route('register')); ?>" class="font-semibold text-blue-600 hover:text-blue-700 transition"><?php echo e(__('auth.sign_up_button')); ?></a>
    </div>
</div>

<script>
    function fillDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/auth/login.blade.php ENDPATH**/ ?>