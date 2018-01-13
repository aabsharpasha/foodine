<?php

class Book extends CI_Controller {

    var $controller, $uppercase, $title;

    function __construct() {
        parent::__construct();
        $this->load->library('DatatablesHelper');
        $this->load->model('book_model');
        $this->load->helper('string');
        $this->controller = 'book';
        $this->uppercase = 'Book Restaurant';
        $this->title = 'Booking Management';
    }

    function add() {
        $viewData['title'] = "Book Table";
        $viewData['ACTION_LABEL'] = "Save";

        if (isset($_POST) && !empty($_POST)) {
            //$_POST['restaurant_id'] = $restaurant_id;
            /* just need to save to database */
            $resp = $this->book_model->book_table($_POST);
            switch ($resp) {
                case -1:
                    $err = array('0' => 'Insufficient data');
                    $this->session->set_userdata('ERROR', $err);
                    break;

                case -2:
                    $err = array('0' => 'The user have already booked the slot.');
                    $this->session->set_userdata('ERROR', $err);
                    break;

                case ($resp > 0):
                    $err = array('0' => 'You have successfully booked the table.');
                    $this->session->set_userdata('SUCCESS', $err);

                    redirect('booking', 'refresh');
                    break;
            }
        }

        $viewData['current_slot'] = $this->book_model->getCurrentSlot();
        $viewData['users'] = $this->book_model->getUsers();
        $viewData['restaurants'] = $this->book_model->getRestaurant();

        $viewData['offers'] = $this->book_model->offers();

        $this->load->view('book/form', $viewData);
    }

    function time_slots() {
        if ($_POST) {
            extract($_POST);

            $slots = $this->book_model->slot_val($id);
            $resp['slots'] = $this->book_model->time_val($slots,$date);
            $resp['offer'] = $this->book_model->offers($id);

            echo json_encode($resp);
        }
    }

}
