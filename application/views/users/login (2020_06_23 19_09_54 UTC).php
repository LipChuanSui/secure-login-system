<?php echo form_open('users/login'); ?>

	<div class="row justify-content-center align-self-center">
		<div class="col-md-4 col-md-offset-4 mt-5">
			<h1 class="text-center"><?php echo $title; ?></h1>

			<div class="form-group">
				<label>Email</label>
				<input type="email" name="email" class="form-control" placeholder="Enter Email" autofocus>
				<p><?php echo form_error('email'); ?></p><!--display erroe message-->
			</div>

			<div class="form-group">
				<label>Password</label>
				<input id="password-field" type="password" name="password" class="form-control" placeholder="Enter Password" autofocus>
				<span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
				<p><?php echo form_error('password'); ?></p><!--display erroe message-->
			</div>

			<div class="form-group">
	 			<label for="captcha"><?php echo $captcha['image']; ?></label>
	 			<br>
	 			<input class="form-control" type="text" autocomplete="off" name="userCaptcha" placeholder="Enter above text" value="<?php if(!empty($userCaptcha)){ echo $userCaptcha;} ?>" />
	 			<span class="required-server"><?php echo form_error('userCaptcha','<p style="color:#F83A18">','</p>'); ?></span>
 			</div>

			<!--disable button for 3 minutes if user enter invalid input for 3 times -->
			<button type="submit" class="btn btn-success btn-block"
			<?php if($this->session->userdata('attempt') >= 3 ) : ?>
			disabled
			<?php endif; ?>
			>Login</button>

			<div class="row">
				<div class="col-6">
					<a class="btn btn-read mt-4 w-100" href="<?php echo base_url('users/register') ?>">Register</a>
				</div>
				<div class="col-6">
					<a class="btn btn-read mt-4 w-100" href="<?php echo base_url('forgets/valEmail') ?>">Forget Password</a>
				</div>
			</div>

		</div>
	</div>

<?php echo form_close(); ?>
