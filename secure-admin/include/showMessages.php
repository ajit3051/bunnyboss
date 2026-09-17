<div id="toastsContainerTopRight" class="toasts-top-right fixed">
	<?php foreach ($errorMessageArr as $errorMessage) { ?>
		<div class="toast bg-danger show" role="alert" aria-live="assertive" aria-atomic="true">
			<div class="toast-body">
				<button data-dismiss="toast" type="button" class="ml-2 mb-1 close" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<div class="tst-msg"><?= xssSafe($errorMessage); ?></div>
			</div>
			<div class="progress" style="height: 3px;">
				<div class="progress-bar danger" role="progressbar" data-width="0" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
		</div>
	<?php } ?>
	
	<?php if($_SESSION['showMessages']){ ?>
		<div class="toast bg-<?= empty($_SESSION['errorMessages']) ? 'success' : 'danger' ?> show" role="alert" aria-live="assertive" aria-atomic="true">
			<div class="<?= $_SESSION['errorMessages'] === 2 ? '' : 'toast-body' ?>">
				<button data-dismiss="toast" type="button" class="ml-2 mb-1 close" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<div class="tst-msg"><?= xssSafe($_SESSION['showMessages']); ?></div>
			</div>
			<div class="progress" style="height: 3px;">
				<div class="progress-bar <?= empty($_SESSION['errorMessages']) ? 'success' : 'danger' ?>" role="progressbar" data-width="0" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
		</div>
	<?php } ?>
	<?php 
	$_SESSION['showMessages']=''; 
	$_SESSION['errorMessages']=''; 
	?>
</div>
<script>
 $(document).ready(function () {
	$("body").delegate(".close", "click", function () {
        $(this).closest('.toast').remove();
    });

	setInterval(function() {
			var dw = $('.progress-bar').attr('data-width');

			if (dw <= 99) {
				var count = parseInt(dw) + 1;
				$('.progress-bar').attr('data-width', count);
				$('.progress-bar').css('width', count + '%');
			} else if (dw == 100) {
				$('#toastsContainerTopRight .toast').remove();
			}

		}, 300); 
});
 
</script>