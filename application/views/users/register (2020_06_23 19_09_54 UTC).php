<?php echo form_open('users/register'); ?>

	<div class="row justify-content-center align-self-center">
		<div class="col-md-6">
			<h1 class="text-center"><?= $title; ?></h1>

			<div class="form-group">
				<label>Name</label>
				<input type="text" class="form-control" name="name" placeholder="Name">
				<p><?php echo form_error('name'); ?></p><!--display erroe message-->
			</div>

			<div class="form-group">
				<label>Email</label>
				<input type="email" class="form-control" name="email" placeholder="Email">
				<p><?php echo form_error('email'); ?></p><!--display erroe message-->
			</div>

				<div class="form-group">
					<label>Password</label>
					<input id="pass1" type="password" class="form-control" name="password" placeholder="Password" oninput="passStrength()">
					<span toggle="#pass1" class="fa fa-fw fa-eye field-icon toggle-password"></span>
					<p><?php echo form_error('password'); ?></p><!--display erroe message-->
				</div>

				<div class="form-group">
					<label>Confirm Password</label>
					<input type="password" class="form-control" name="password2" placeholder="Confirm Password">
					<p><?php echo form_error('password2'); ?></p><!--display erroe message-->
			</div>

			<p>Please enter password combining uppercase, lowercase, symbol and number.</p>
			<p id="password_strength"></p>

			<div class="form-group">
	 			<label for="captcha"><?php echo $captcha['image']; ?></label>
	 			<br>
	 			<input class="form-control" type="text" autocomplete="off" name="userCaptcha" placeholder="Enter above text" value="<?php if(!empty($userCaptcha)){ echo $userCaptcha;} ?>" />
	 			<span class="required-server"><?php echo form_error('userCaptcha','<p style="color:#F83A18">','</p>'); ?></span>
 			</div>


			<button id="submitBtn" class="btn btn-success btn-block">Submit</button>

			<div class="row">
				<div class="col-6">
					<a class="btn btn-read mt-4 w-100" href="<?php echo base_url('users/login') ?>">Login</a>
				</div>
				<div class="col-6">
					<a class="btn btn-read mt-4 w-100" href="<?php echo base_url('forgets/valEmail') ?>">Forget Password</a>
				</div>
			</div>

		</div>
	</div>

<?php echo form_close(); ?>
