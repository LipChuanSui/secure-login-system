<?php echo form_open('forgets/valEmail'); ?>

	<div class="row justify-content-center align-self-center">
		<div class="col-md-4 col-md-offset-4 mt-5">
			<h1 class="text-center"><?php echo $title; ?></h1>

			<div class="form-group">
				<label>Email</label>
				<input type="email" name="email" class="form-control" placeholder="Enter Email" autofocus>
				<p><?php echo form_error('email'); ?></p>
			</div>

			<button type="submit" class="btn btn-success btn-block">Submit</button>

			<div class="row">
				<div class="col-6">
					<a class="btn btn-read mt-4 w-100" href="<?php echo base_url('users/register') ?>">Register</a>
				</div>
				<div class="col-6">
					<a class="btn btn-read mt-4 w-100" href="<?php echo base_url('users/login') ?>">Login</a>
				</div>
			</div>

		</div>
	</div>

<?php echo form_close(); ?>
