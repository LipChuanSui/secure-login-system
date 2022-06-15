<?php echo form_open($url);?>

	<div class="row justify-content-center align-self-center">
		<div class="col-md-4 col-md-offset-4 mt-5">

			<h1 class="text-center"><?php echo $title; ?></h1>

			<div class="form-group">
				<label>OTP</label>
				<input type="password" name="otp" class="form-control" placeholder="Enter OTP" autofocus>
				<p><?php echo form_error('otp'); ?></p>
			</div>

			<button type="submit" class="btn btn-success btn-block">Login</button>

		</div>
	</div>

<?php echo form_close(); ?>
