<?php

class MY_Controller extends Controller {

    var $currentLang;
    var $isAdminLogin;

    function __construct() {
        parent::__construct();


        $this->chk_admin_cookie();
        $this->chk_admin_session();
        $this->lang->switch_to('en');

        //$this->currentLang=$this->getLang();	
        //$this->config->set_item('language',$this->currentLang);
    }

    function getLang() {
        $lang = $this->session->userdata('userLang');

        if ($this->session->userdata('userLang') && in_array($this->session->userdata('userLang'), $this->config->item('avail_languages')))
            return $this->session->userdata('userLang');

        return $this->config->item('language');
    }

    function chk_admin_session() {

        // We need to use $CI->session instead of $this->session
        $user = $this->session->userdata('ADMINLOGIN');
        mprd($user);
        $session_login = $this->session->userdata("ADMINLOGIN");

        if ($session_login == '' && $this->uri->segment(1) != 'login' && $this->uri->segment(1) != 'forgot_pass' && $this->uri->segment(1) != 'confirmation')
            redirect('login');

        /* if($session_login == 1 && $session_login == TRUE && ($this->uri->segment(1) == '' || $this->uri->segment(1) == 'login' || $this->uri->segment(1) == 'forgot_pass'))
          redirect('dashboard'); */
        if ($session_login == 1 && $session_login == TRUE && ($this->uri->segment(1) == '' || $this->uri->segment(1) == 'login' || $this->uri->segment(1) == 'forgot_pass' || $this->uri->segment(1) == 'confirmation'))
            redirect('dashboard');
    }

    function chk_admin_cookie() {
        $loggedin = $this->admin_model->chk_cookie();

        if ($loggedin === TRUE) {
            redirect('dashboard');
        }
    }

}
