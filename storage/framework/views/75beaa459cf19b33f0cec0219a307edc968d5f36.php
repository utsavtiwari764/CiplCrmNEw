<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo e(trans('installer_messages.title')); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon/favicon-16x16.png')); ?>" sizes="16x16"/>
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon/favicon-32x32.png')); ?>" sizes="32x32"/>
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon/favicon-96x96.png')); ?>" sizes="96x96"/>


    <link href="<?php echo e(asset('installer/css/style.min.css')); ?>" rel="stylesheet"/>
    <?php echo $__env->yieldContent('style'); ?>

</head>
<body>
<div class="master">
    <div class="box">
        <div class="header">
            <img class="text-center" src="<?php echo e(asset('app-logo.png')); ?>" style="max-width: 150px" alt="Home"/>
            <h1 class="header__title"><?php echo $__env->yieldContent('title'); ?></h1>
        </div>
        <ul class="step">
            <li class="step__divider"></li>
            <li class="step__item <?php echo e(isActive('LaravelInstaller::final')); ?>"><i class="step__icon database"></i></li>
            <li class="step__divider"></li>
            <li class="step__item <?php echo e(isActive('LaravelInstaller::permissions')); ?>"><i
                        class="step__icon permissions"></i></li>
            <li class="step__divider"></li>
            <li class="step__item <?php echo e(isActive('LaravelInstaller::requirements')); ?>"><i
                        class="step__icon requirements"></i></li>
            <li class="step__divider"></li>
            <li class="step__item <?php echo e(isActive('LaravelInstaller::environment')); ?>"><i class="step__icon update"></i>
            </li>
            <li class="step__divider"></li>
            <li class="step__item <?php echo e(isActive('LaravelInstaller::welcome')); ?>"><i class="step__icon welcome"></i></li>
            <li class="step__divider"></li>
        </ul>
        <div class="main">
            <?php echo $__env->yieldContent('container'); ?>
        </div>
    </div>
</div>
</body>
<?php echo $__env->yieldContent('scripts'); ?>
</html>
<?php /**PATH C:\xampp\htdocs\cipl-rms-main\cipl-rms-main\resources\views/vendor/installer/layouts/master.blade.php ENDPATH**/ ?>