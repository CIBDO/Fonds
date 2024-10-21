 <?php if(Session::has('flash_notification')): ?>
 <!-- Pull in jQuery from CDN if not already loaded -->
<script>window.jQuery || document.write("<script src='//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'><\/script>")</script>
 <!-- Pull in Toastr CSS and JS from CDN to be always up2date -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	 <script>
	    $('document').ready(function(){
	    toastr.options = $.parseJSON('<?php echo json_encode(config('flash-toastr.options'), JSON_UNESCAPED_SLASHES); ?>');
	    <?php $__currentLoopData = session('flash_notification', collect())->toArray(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			toastr["<?php echo $message['level']; ?>"]("<?php echo $message['message']; ?>", "<?php echo $message['title']; ?>");
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	    });
	</script>
<?php echo e(Session::forget('flash_notification')); ?>

<?php endif; ?>

<?php /**PATH C:\Users\BDO\Desktop\Fonds\vendor\hepplerdotnet\flash-toastr\src\views\message.blade.php ENDPATH**/ ?>