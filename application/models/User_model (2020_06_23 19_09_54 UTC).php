<?php
	class User_model extends CI_Model{

		public function register($name, $email, $enc_password){

			$data = array(// put User data into array
				'name' => $name,
				'email' => $email,
        'password' => $enc_password,
			);

			return $this->db->insert('users', $data);// Insert user data
			/*INSERT INTO users (name, email, password) VALUES ($name, $email, $enc_password);*/
		}

		public function login($email, $password){// user log in

			$this->db->where('email', $email);//fetch user data with same email
			$result = $this->db->get('users');
			/*select * from users where email = $email*/

			if($result->num_rows() == 1){

				$hashed_password = $result->row(0)->password;//get user hashed password

				if(password_verify($password, $hashed_password)){//compare hashed password with input password

					$data['user_id'] = $result->row(0)->id;//fetch user data for sending email to user containing otp
					$data['name'] = $result->row(0)->name;
					$data['email'] = $result->row(0)->email;

					return $data;

				}else{
					return false;
				}
			}
		}

		public function get_user_by_ID($id){//get user data by user id

			$query = $this->db->get_where('users', array('id' => $id));
			return $query->row_array();
			/*select * from users where id = $id*/

		}

		public function validate($otp,$id, $time){// validate otp

			$this->db->where('otp', $otp);//fetch data where otp, user id and time are the same
			$this->db->where('user_id', $id);//prevent multiple otp request confusing the system
			$this->db->where('time', $time);
			/*select * from records where otp = $otp AND user_id = $id AND time = $time*/
			$result = $this->db->get('records');

			if($result->num_rows() == 1){//return true if validation correct
				return true;
			} else {
				return false;
			}
		}

		public function login_record($rand, $id, $time){//insert record containing user id, otp and time

			$data = array(	// put data into array
				'user_id' => $id,
				'otp' => $rand,
				'time' => $time
			);

			return $this->db->insert('records', $data);// Insert record
			/*INSERT INTO records (user_id, otp, time) VALUES ($id, $rand, $time);*/

		}


		// Check email exists
		public function check_email_exists($email){
			$query = $this->db->get_where('users', array('email' => $email));
			/*sekect * from users where email = $emails*/

			if(empty($query->row_array())){
				return true;
			} else {
				return false;
			}
		}

		// Check username exists
	public function check_name_exists($name){
		$query = $this->db->get_where('users', array('name' => $name));
		/*sekect * from users where name = $name*/

		if(empty($query->row_array())){
			return true;
		} else {
			return false;
		}
	}
	}
