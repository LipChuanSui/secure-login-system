<?php
	class Users extends CI_Controller{

		public function register(){// Register user

      if($this->session->userdata('logged_in')){ //avoid logged in user access register page
				redirect(base_url());
			}

			$data['title'] = 'Sign Up';//title for page

			$this->form_validation->set_rules('name', 'Name', 'trim|required|callback_check_name_exists');//form vlidation
			$this->form_validation->set_rules('email', 'Email', 'trim|required|callback_check_email_exists|callback_check_email_valid');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			$this->form_validation->set_rules('password2', 'Confirm Password', 'trim|matches[password]');
			$this->form_validation->set_rules('userCaptcha', 'Captcha', 'trim|required|callback_check_captcha');

			if($this->form_validation->run() === FALSE){

      	$random_number = substr(number_format(time() * rand(),0,'',''),0,6); // create random numeric number for captcha

      	$vals = array(  // setting up captcha config
             'word' => $random_number,
             'img_path' => './assets/images/captcha_images/',
             'img_url' => base_url().'assets/images/captcha_images/',
             'img_width' => 140,
             'img_height' => 32,
             'expiration' => 7200
            );

      	$data['captcha'] = create_captcha($vals); //create captcha using built in codeigniter function
      	$this->session->set_userdata('captchaWord',$data['captcha']['word']);

				$this->load->view('templates/header');//load page
				$this->load->view('users/register', $data);
				$this->load->view('templates/footer');

			} else {

				$enc_password = password_hash(xss_clean($this->input->post('password')), PASSWORD_DEFAULT);// hash password

				$name = xss_clean($this->input->post('name')); // filter name, email input using XSS filter
				$email = xss_clean($this->input->post('email'));

				$this->user_model->register($name, $email, $enc_password);
				//call query function to insert ner user input
				//see ppw/application/models/user_model.php -> register function

				$this->session->set_flashdata('user_registered', 'You have registered a new user.');// Set message

				redirect('users/login');//redirect to login page
			}
		}


		public function login(){// user Log in

			if(!$this->session->tempdata('attempt')){ // set attempt session

				$this->session->set_tempdata('attempt',0);

			}

      if($this->session->userdata('logged_in')){//avoid logged in user access login page
				redirect(base_url());
			}

			$data['title'] = 'Sign In';//title for page
			//form validation
			$this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			$this->form_validation->set_rules('userCaptcha', 'Captcha', 'trim|required|callback_check_captcha');

			if($this->form_validation->run() === FALSE){

      	$random_number = substr(number_format(time() * rand(),0,'',''),0,6);// create random numeric number for captcha
      	$vals = array(// setting up captcha config
             'word' => $random_number,
             'img_path' => './assets/images/captcha_images/',
             'img_url' => base_url().'assets/images/captcha_images/',
             'img_width' => 140,
             'img_height' => 32,
             'expiration' => 7200
            );
      	$data['captcha'] = create_captcha($vals); //create captcha using built in codeigniter function
      	$this->session->set_userdata('captchaWord',$data['captcha']['word']);

				$this->load->view('templates/header');//load page
				$this->load->view('users/login', $data);
				$this->load->view('templates/footer');

			} else {

				$email = xss_clean($this->input->post('email'));// filter  email password input using XSS filter
				$password = xss_clean($this->input->post('password'));

				$authentication = $this->user_model->login($email, $password);// call query function for logging in
				//see ppw/application/models/user_model.php -> login function

				if($authentication){//if query function return true

					$this->session->set_userdata('user_id', $authentication['user_id']);//Create session
					$this->session->set_userdata('time', date('Y-m-d h:i:s', time()));
					$this->session->set_tempdata('otp_val', true, 180);//create otp session last for 3 minutes
					//after 3 minutes, user can no longer enter otp
					$rand = random_string('alnum', 10);//Create OTP

					$this->user_model->login_record($rand, $authentication['user_id'], $this->session->userdata('time'));
					//inert into database the OTP, time and user id
					//see ppw/application/models/user_model.php -> login_record function

					$this->sendMail($authentication,$rand , $this->session->userdata('time'));//send email containing otp

					redirect('users/validate');

				} else {

					$this->session->set_tempdata('attempt', $this->session->tempdata('attempt') + 1 , 180) ;//180 seconds for session to expire
					//increase attempt session if wrong OTP is given

					if($this->session->tempdata('attempt') > 3){//if wrong credentials is given 3 times

						$this->session->set_flashdata('login_failed', "You are not allowed to login for 3 minutes due to multiple invalid login.");
						//error message
						redirect('users/login');//redirect to login oage

					}else{

						$this->session->set_flashdata('login_failed', 'Invalid email or password');//error message

					}
					redirect('users/login');
					//refresh  page

				}
			}
		}

		public function validate(){

			if(!$this->session->tempdata('otp_val')){//prohibit user from accessing this url without session
				redirect(base_url());
			}
			header("refresh: 10");//refresh page every 10 seconds, auto redirect to login page if session expired after 3 minutes

			if(!$this->session->tempdata('otp_attempt')){
				$this->session->set_tempdata('otp_attempt', 0,180);//set attempt session to last for 3 minutes
			}

			$data['title'] = 'Enter OTP';//title
			$data['url'] = 'users/validate';//to open form validation in templates
			//both forgets and login use same valdate templates

			$this->form_validation->set_rules('otp', 'OTP', 'required');//form validation

			if($this->form_validation->run() === FALSE){
				$this->load->view('templates/header');
				$this->load->view('users/validate', $data);
				$this->load->view('templates/footer');
				//refresh page if invalid input
			}else{

				$otp = xss_clean($this->input->post('otp'));// filter otp input using XSS filter

				// validate OTP by calling query function in model
				//see ppw/application/models/user_model.php -> validate function
				$otp_verified = $this->user_model->validate($otp, $this->session->userdata('user_id'), $this->session->userdata('time'));

				if($otp_verified){// if otp is correct

					$this->session->set_flashdata('user_loggedin', 'You are now logged in');//successfully login
					$this->session->unset_userdata('otp_val');
					$this->session->set_userdata('logged_in', true);//set logged in session
					redirect(base_url());
				}else{
					$this->session->set_tempdata('otp_attempt', $this->session->tempdata('otp_attempt') + 1) ;
					//increase attempt session if wrong OTP is given
					if($this->session->tempdata('otp_attempt') > 3){//if wrong OTP is given 3 times
						//send notice email to user noticing attempt to chnage new password
						$this->sendNoticeMail($this->session->userdata('user_id'));//unset sesssion
						$this->session->unset_tempdata('otp_attempt');
						$this->session->unset_tempdata('otp_val');
						$this->session->unset_userdata('user_id');
						$this->session->set_flashdata('login_failed', "You are not allowed to login due to incorrect OTP.");
						//error message
						redirect('users/login');//redirect to login oage
					}else{
						$this->session->set_flashdata('login_failed', 'Wrong OTP');//error message for wrong OTP
					}
					redirect('users/validate');
					//refresh  page
				}
			}

		}

		/***************************************************************************************
		*    Title: How to use captcha in codeigniter
		*    Author: Cairocoders
		*    Date: 8/5/2020
		*    Availability: https://tutorial101.blogspot.com/2015/12/how-to-use-captcha-in-codeigniter.html
		*
		***************************************************************************************/

		public function check_captcha($str){//check captcha
    	$word = $this->session->userdata('captchaWord');
    	if(strcmp(strtoupper($str),strtoupper($word)) == 0){
      	return true;
    	}else{
      	$this->form_validation->set_message('check_captcha', 'Please enter correct words!');
      	return false;
    	}
 		}

		/***************************************************************************************
		*    Title: Send email by using codeigniter library via localhost
		*    Author: Venkata Krishna
		*    Date: 20/4/2020
		*    Availability: https://stackoverflow.com/questions/18586801/send-email-by-using-codeigniter-library-via-localhost
		*
		***************************************************************************************/

		public function sendMail($data,$rand, $time){
			//send email containing OTP to user
			$this->load->config('email');//load library and configuration for email
			$this->load->library('email');
			$from = $this->config->item('smtp_user');
			$to = $data['email'];
			$subject = 'OTP for log in';
			$message = "Dear  ".$data['name'].
									"\n Here is the OTP you need to login to account at ".$time.
									"\n Your OTP is ".$rand.
									"\n The login attempt included your correct account name and password.".
									"\n You can only use this OTP for 3 minutes.".
									"\n The OTP is required to complete the login. No one can access your account without also accessing this email.".
									"\n If you are not attempting to login then please change your password, and consider changing your email password as well to ensure your account security.";
			//body of email
			$this->email->set_newline("\r\n");
			$this->email->from($from);
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($message);

			if ($this->email->send()) {
				$this->session->set_flashdata('otp_sent', 'You have received OTP via email.');
				//success message
			} else {
				$this->session->set_flashdata('otp_failed', 'Could not send email. Please check configuration.');
				//show_error($this->email->print_debugger());
			}

		}

		public function sendNoticeMail($id){
			//send email to notice user about 3 times wrong input of OTP
			$this->load->config('email');//load library and configuration for email
			$this->load->library('email');
			$data = $this->user_model->get_user_by_ID($id);
			//fetch information of user
			//see ppw/application/models/user_model.php -> get_user_by_ID function

			$from = $this->config->item('smtp_user');
			$to = $data['email'];
			$subject = 'Enter Wrong OTP for 3 times when attempting to login';
			$message = "Dear  ".$data['name'].
									"\n We detect that someone could access to your email and password but couldn't acces OTP. We highly recommend you to change your email password to ensure your account security. ";
			//body of message
			$this->email->set_newline("\r\n");
			$this->email->from($from);
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($message);

			if ($this->email->send()) {
				//$this->session->set_flashdata('otp_sent', 'You have have received OTP via email.');
			} else {
				//show_error($this->email->print_debugger());
			}

		}

		public function logout(){// Log user out

			$this->session->unset_userdata('logged_in');// Unset session
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('username');

			$this->session->set_flashdata('user_loggedout', 'You are now logged out');// Set logout message

			redirect('users/login');
		}

		// Check if username exists
		public function check_name_exists($name){
			$this->form_validation->set_message('check_name_exists', 'That name is taken. Please choose a different one');
			if($this->user_model->check_name_exists($name)){
				return true;
			} else {
				return false;
			}
		}

		// Check if email exists
		public function check_email_exists($email){
			$this->form_validation->set_message('check_email_exists', 'That email is taken. Please choose a different one');
			if($this->user_model->check_email_exists($email)){
				return true;
			} else {
				return false;
			}
		}

		// Check if email valid
		public function check_email_valid($email){
			$this->form_validation->set_message('check_email_valid', 'That email is invalid. Please choose a different one');
			if(!empty($email)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					return true;
				} else {
					return false;
				}
			}
		}
	}
