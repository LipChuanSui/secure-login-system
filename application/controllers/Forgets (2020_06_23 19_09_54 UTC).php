<?php
	class Forgets extends CI_Controller{

    public function valEmail(){//user will enter email for changing new password
      $data['title'] = 'Enter Email';//title

      $this->form_validation->set_rules('email', 'Email', 'trim|required');
			//password is trimmed and set to required

      if($this->form_validation->run() === FALSE){//if form input is invalid, page will refresh

        $this->load->view('templates/header');
        $this->load->view('users/valEmail', $data);
        $this->load->view('templates/footer');

      } else {

        $email = xss_clean($this->input->post('email'));// filter email input using XSS filter

        $authentication = $this->forget_model->val_email($email);//check if user exist with input email
				//see ppw/application/models/forget_model.php -> val_email function


        if($authentication){	//if user with this email exist

					$this->session->set_userdata('user_id', $authentication['user_id']);//create session with current user id
					$this->session->set_userdata('time', date('Y-m-d h:i:s', time()));	//create session with current time
					$this->session->set_tempdata('otp_val', true, 180);//create session which only last for 3 minutes

					$rand = random_string('alnum', 10);//create random OTP with 10 characters long

					$this->user_model->login_record($rand, $authentication['user_id'], $this->session->userdata('time'));
					//insert records into database, later to compare OTP and time to ensure correct OTP
					//see ppw/application/models/user_model.php -> login_record function

					$this->sendForgetMail($authentication,$rand, $this->session->userdata('time'));	//send email containng OTP to user

					redirect('forgets/validate');//redirect to page to insert OTP

        } else {

          $this->session->set_flashdata('login_failed', "Invalid Email.");//error message if wrong email is given

          redirect('forgets/valEmail');//refresh page
        }
      }
    }

		public function validate(){// validate otp
			if(!$this->session->tempdata('otp_val')){//prohibit user from accessing this url without session

				redirect(base_url());

			}

			header("refresh: 10");//refresh page every 10 seconds, auto redirect to login page if session expired after 3 minutes

			if(!$this->session->tempdata('otp_attempt')){

				$this->session->set_tempdata('otp_attempt', 0,180);//set attempt session to 3 mins

			}

			$data['title'] = 'Enter OTP';//title
			$data['url'] = 'forgets/validate';//to open form validation in templates
			//both forgets and login use same valdate templates

			$this->form_validation->set_rules('otp', 'OTP', 'required');//form validation

			if($this->form_validation->run() === FALSE){

				$this->load->view('templates/header');
				$this->load->view('users/validate', $data);
				$this->load->view('templates/footer');
				//refresh page if invalid input

			}else{

				$otp = xss_clean($this->input->post('otp'));// filter otp input using XSS filter

				$otp_verified = $this->user_model->validate($otp, $this->session->userdata('user_id'), $this->session->userdata('time'));
				// validate OTP by calling query function in model
				//see ppw/application/models/user_model.php -> validate function

				if($otp_verified){// if otp is correct

					$this->session->set_flashdata('user_loggedin', 'You are allowed to reset password.');//success message

					$this->session->unset_userdata('otp_val');//unset otp session

					$this->session->set_userdata('reset_pass', true);//set reset_pass session

					redirect('forgets/resetPass');//redirect to reset password

				}else{

					$this->session->set_tempdata('otp_attempt', $this->session->tempdata('otp_attempt') + 1 , 180) ;//session last for 180 seconds
					//increase attempt session if wrong OTP is given

					if($this->session->tempdata('otp_attempt') > 3){//if wrong OTP is given 3 times

						$this->sendNoticePass($this->session->userdata('user_id'));//send email to notice user

						$this->session->unset_tempdata('otp_attempt');//unset sesssion
						$this->session->unset_tempdata('otp_val');
						$this->session->unset_userdata('user_id');

						$this->session->set_flashdata('login_failed', "You are not allowed to reset password due to incorrect OTP.");//error message

						redirect('users/login');//redirect to login oage

					}else{

						$this->session->set_flashdata('login_failed', 'Wrong OTP');//error message for wrong OTP

					}
					redirect('forgets/validate');
					//refresh  page
				}
			}
		}

		public function resetPass(){
			if(!$this->session->userdata('reset_pass')){//avoid direct access to this url

				redirect(base_url());

			}


			$data['title'] = 'Enter New Password';

			$this->form_validation->set_rules('password', 'Password', 'required|callback_check_password_same');//form validation
			$this->form_validation->set_rules('password2', 'Confirm Password', 'matches[password]');


			if($this->form_validation->run() === FALSE){//refresh page if invalid input

				$this->load->view('templates/header');
				$this->load->view('users/resetPass', $data);
				$this->load->view('templates/footer');

			} else {

				$enc_password = password_hash(xss_clean($this->input->post('password')), PASSWORD_DEFAULT);// hash password

				$this->forget_model->change_password($enc_password, $this->session->userdata('user_id'));
				//call query function to change password
				//see ppw/application/models/forget_model.php -> chnage_password function

				$this->session->unset_userdata('user_id');//unset session
				$this->session->unset_userdata('reset_pass');

				$this->session->set_flashdata('user_registered', "You could now login with new password.");//success message
				redirect('users/login');
			}

		}

		/***************************************************************************************
		*    Title: Send email by using codeigniter library via localhost
		*    Author: Venkata Krishna
		*    Date: 20/4/2020
		*    Availability: https://stackoverflow.com/questions/18586801/send-email-by-using-codeigniter-library-via-localhost
		*
		***************************************************************************************/

    function sendForgetMail($data,$rand, $time){//send email containing OTP for changing new password

			$this->load->config('email');//load library and configuration for email
			$this->load->library('email');
			$from = $this->config->item('smtp_user');


			$to = $data['email'];//send email to user
			$subject = 'OTP for reset password';
			$message = "Dear  ".$data['name'].
									"\n Here is the OTP you need to reset new password at ".$time.
									"\n Your OTP is ".$rand.
									"\n The login attempt included your correct account name and password.";
			//message
			$this->email->set_newline("\r\n");
			$this->email->from($from);
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($message);

			if ($this->email->send()) {

				$this->session->set_flashdata('otp_sent', 'You have have received OTP via email.');//success message

			} else {

				$this->session->set_flashdata('otp_failed', 'Could not send email. Please check configuration.');// error message
				//show_error($this->email->print_debugger());

			}
		}




    public function sendNoticePass($id){//send email to notice user about 3 times wrong input of OTP

			$this->load->config('email');//load library and configuration for email
			$this->load->library('email');

			$data = $this->user_model->get_user_by_ID($id);//fetch information of user
			//see ppw/application/models/user_model.php -> get_user_by_ID function

			$from = $this->config->item('smtp_user');
			$to = $data['email'];
			$subject = 'Enter Wrong OTP for 3 times when attempting to chnage password';
			$message = "Dear  ".$data['name'].
									"\n We detect that someone is attempting to change your password but couldn't input correct OTP. We highly recommend you to change your email password to ensure your account security. ";
			//body of email
			$this->email->set_newline("\r\n");
			$this->email->from($from);
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();

			/*if ($this->email->send()) {//debugging
				$this->session->set_flashdata('otp_sent', 'You have received email.');
			} else {
				//show_error($this->email->print_debugger());
			}*/

		}

    // Check if email exists
		public function check_password_same($password){
			$this->form_validation->set_message('check_password_same', 'You cannot use same old password.');
			if($this->forget_model->check_password_same($password, $this->session->userdata('user_id'))){
				//see ppw/application/models/user_model.php -> check_password_same function
				return true;
			} else {
				return false;
			}
		}

}
