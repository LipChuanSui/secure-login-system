<!DOCTYPE html>
<html>
<head>
	<title>ppw</title>
	<link rel="icon" href="<?php echo base_url('assets/images/bear-logo.png'); ?>" type="image/gif" sizes="16x16">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scsale=1">
	<link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!--Bootstrap-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<script src="http://cdn.ckeditor.com/4.5.11/standard/ckeditor.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>

<!--reset timer if user move mouse-->
<body

<?php if($this->session->userdata('logged_in')) : ?>
onload="StartTimers();" onmousemove="ResetTimers();"
<?php endif; ?>>

	<!--navbar-->
	<nav class="navbar navbar-expand-lg navbar-light" id="header">
		<div class="container">
			<!--Logo-->
			<div class="logo">
					<a class="navbar-brand" href="<?php echo base_url(); ?>"><img src="<?php echo base_url('assets/images/bear-logo.png'); ?>" alt="otw_logo" class="img-fluid"></a>
			</div>

			<!---only display logout if user successfully log in-->
			<?php if($this->session->userdata('logged_in')) : ?>
				<ul class="navbar-nav ml-auto">
					<li class="nav-item"><a class="nav-link" href="<?php echo base_url(); ?>users/logout">Logout</a></li>
				</ul>
			<?php endif; ?>

		</div>
	</nav>

	<!--display modal to alert user about 2 mins of inactive-->
	<div class="modal fade" id="timeout" role="dialog">
	    <div class="modal-dialog">
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <h4 class="modal-title">Auto Logout due to 3 mins of inactivity.</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
	        </div>
	        <div class="modal-body">
	          <p>This system will automatically logout 1 min.</p>
	        </div>
	      </div>
	    </div>
	  </div>

	<div class="container pad_bot">
		<?php if($this->session->flashdata('user_registered')): ?>
		        <?php echo '<p class="alert alert-success">'.$this->session->flashdata('user_registered').'</p>'; ?>
		      <?php endif; ?>
 <?php if($this->session->flashdata('login_failed')): ?>
		        <?php echo '<p class="alert alert-danger">'.$this->session->flashdata('login_failed').'</p>'; ?>
		      <?php endif; ?>
<?php if($this->session->flashdata('user_loggedin')): ?>
		        <?php echo '<p class="alert alert-success">'.$this->session->flashdata('user_loggedin').'</p>'; ?>
		      <?php endif; ?>
 <?php if($this->session->flashdata('user_loggedout')): ?>
		        <?php echo '<p class="alert alert-success">'.$this->session->flashdata('user_loggedout').'</p>'; ?>
		      <?php endif; ?>
	<?php if($this->session->flashdata('otp_sent')): ?>
				 		 <?php echo '<p class="alert alert-success">'.$this->session->flashdata('otp_sent').'</p>'; ?>
				 	<?php endif; ?>
	<?php if($this->session->flashdata('otp_failed')): ?>
						<?php echo '<p class="alert alert-danger">'.$this->session->flashdata('otp_sent').'</p>'; ?>
					<?php endif; ?>
