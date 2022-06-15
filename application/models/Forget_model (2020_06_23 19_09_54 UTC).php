<?php
	class Forget_model extends CI_Model{

		public function val_email($email){// validate email for changing new password

			$this->db->where('email', $email);//fetch user data with same email
			$result = $this->db->get('users');
			/*select * from users where email = $email*/

			if($result->num_rows() == 1){//fetch data

        $data['user_id'] = $result->row(0)->id;
        $data['name'] = $result->row(0)->name;
        $data['email'] = $result->row(0)->email;

				return $data;

      }else{
        return false;
      }
		}

    public function check_password_same($password, $id){// check if password same

      $this->db->where('id', $id);//fetch user data with same id
      $result = $this->db->get('users');
			/*select * from users where id = $id*/

      if($result->num_rows() == 1){

        $hashed_password = $result->row(0)->password;//get hashed password

        if(password_verify($password, $hashed_password)){//comapre input password with hashed password
          return false;//return true when same password
					//prohibt user using same old password when changing new password
        }else{
          return true;
        }
      }
    }


    public function change_password($enc_password, $id){//change password
      $this->db->set('password', $enc_password);
			$this->db->where('id',$id);
      return $this->db->update('users');
			/*UPDATE users SET password = $enc_password, WHERE id = $id; */

    }
}
