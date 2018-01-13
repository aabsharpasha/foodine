<?php

class Forgot_pass extends CI_Controller {

    var $viewData = array();

    function __construct() {
        parent::__construct();
    }

    function index($check = '') {
        try {
            //mprd($this->input->post());
            if ($this->input->post('vEmail')) {

                /*
                 * REQUEST TO RESET THE PASSWORD...
                 */
                $new_password = $this->admin_model->_reset_admin_password($this->input->post('vEmail'));

                /*
                 * SEND MAIL 
                 */
                if ($new_password !== '') {
                    $mailEmailID = $this->input->post('vEmail');

                    /* $tmpltParam['%NEW_PASSWORD%'] = $new_password;
                      $tmpltParam['%CMS_LOGIN_LINK%'] = BASEURL . 'login';
                      $tmpltParam['%USERNAME%'] = $this->input->post('vEmail');
                      $tmpltParam['%LOGO_IMAGE%'] = IMAGE_PATH . 'blacklist.jpg';
                      $tmpltParam['%MAILSUBJECT%'] = 'Blacklist Reset Password';

                      $tmpltPath = DIR_ADMIN_VIEW . 'login/email/forgotpassword.php';
                      $mailSubject = EMAIL_SUBJECT . ' : Reset Password';

                      $this->load->model('smtpmail_model');
                      $this->smtpmail_model->send($mailEmailID, $mailSubject, $tmpltPath, $tmpltParam);
                     *
                     */

                    /*
                     * SEND MAIL FUNCTIONALITY...
                     */
                    $this->load->library('maillib');
                    $param = array(
                        '%MAILSUBJECT%' => 'HungerMafia : Welcome',
                        '%LOGO_IMAGE%' => DOMAIN_URL . '/images/hungermafia.png',
                        '%NEW_PASSWORD%' => $new_password,
                        '%CMS_LOGIN_LINK%' => BASEURL . 'login',
                        '%USERNAME%' => $this->input->post('vEmail')
                    );
                    $tmplt = DIR_ADMIN_VIEW . 'login/email/forgotpassword.php';
                    $subject = 'HungerMafia : Reset Password';
                    $to = $this->input->post('vEmail');
                   // $this->maillib->sendMail($to, $subject, $tmplt, $param);
 		     $this->load->model("smtpmail_model", "smtpmail_model");
                     $data = $this->smtpmail_model->send($to, $subject, $tmplt, $param);


                    $succ = array('0' => 'Your newly generated password has been sent to your email address successfully.');
                    $this->session->set_userdata('SUCCESS', $succ);

                    redirect('login', 'refresh');
                }
            }
        } catch (Exception $ex) {
            exit('Login Controller : Error in forgotpass function - ' . $ex);
        }
    }

    /*
      | -------------------------------------------------------------------
      |  ACTIVATE EMAIL ACCOUNT
      | -------------------------------------------------------------------
     */

    function activate($id) {
        $this->load->library('encrypt');
        //$id	=	$this->encrypt->decode(strtr($id, '___', '+/='));
        $id = $this->encrypt->decode($id);
        // CHECK IF URL IS NOT WRONG

        if (is_numeric($id)) {

            $data = array(
                'id' => $id,
                'title' => 'Reset Password'
            );
            $this->load->view('reset_pass_view', $data);
        }
    }

    /*
      | -------------------------------------------------------------------
      |  RESET PASSWORD
      | -------------------------------------------------------------------
     */

    function reset_pass() {
        if (count($_POST) > 0) {
            $this->admin_model->reset_admin_pass($this->input->post('vPassword'));
        }
    }

    /*
      | -------------------------------------------------------------------
      |  END OF CLASS FILE
      | -------------------------------------------------------------------
     */
}
