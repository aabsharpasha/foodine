<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth_openxcell extends CI_Controller {

    public function check($username, $password) {
        return true;
    }

}

/* End of file auth_openxcell.php */
/* Location: ./application/ws/libraries/auth_openxcell.php */