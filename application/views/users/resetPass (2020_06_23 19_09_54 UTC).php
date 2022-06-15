<?php echo form_open('forgets/resetPass'); ?>

	<div class="row justify-content-center align-self-center">
		<div class="col-md-6">
			<h1 class="text-center"><?= $title; ?></h1>

				<div class="form-group">
					<label>Password</label>
					<input id="pass1" type="password" class="form-control" name="password" placeholder="Password" oninput="passStrength()">
					<span toggle="#pass1" class="fa fa-fw fa-eye field-icon toggle-password"></span>
					<p><?php echo form_error('password'); ?></p>
				</div>

				<div class="form-group">
					<label>Confirm Password</label>
					<input type="password" class="form-control" name="password2" placeholder="Confirm Password">
					<p><?php echo form_error('password2'); ?></p>
			</div>

			<p>Please enter password combining upper case, lower case, symbol and number.</p>

			<p id="password_strength"></p>

			<button id="submitBtn" class="btn btn-success btn-block">Submit</button>

		</div>
	</div>

<?php echo form_close(); ?>
