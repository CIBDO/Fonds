<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>DNTCP-CIFP</title>
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/bootstrap/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/feather/feather.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/icons/flags/flags.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/fontawesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Inclure DataTables CSS -->
    
    <link href="<?php echo e(asset('assets/css/notifications.css')); ?>" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>

<body>
    
    <div class="main-wrapper">

        <?php echo $__env->make('partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('sweetalert::alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="page-wrapper">
            <?php echo $__env->yieldContent('content'); ?>
            <?php echo $__env->make('partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            
        </div>  
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo e(asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/feather.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/slimscroll/jquery.slimscroll.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/apexchart/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/apexchart/chart-data.js')); ?>"></script>
    <!-- Inclure DataTables JS -->
    
    <script src="<?php echo e(asset('assets/js/script.js')); ?>"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script src="<?php echo e(asset('assets/js/notifications.js')); ?>"></script>
    <?php echo $__env->yieldContent('add-js'); ?> 
   
</body>

</html>
<?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/layouts/master.blade.php ENDPATH**/ ?>