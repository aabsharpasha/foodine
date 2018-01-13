<?php

class Order extends CI_Controller {

    var $viewData = array();

    function __construct() {

        parent::__construct();

        $this->controller = 'order';
        $this->uppercase = 'Online Orders';
        $this->title = 'Online Orders';

//        $this->load->model('order_model');
        $this->load->model('menu_item_model');
        $this->load->library('aws_sdk');
    }

    function viewMenuItems() {
        $viewData = array("title" => "MenuItems");
        $this->load->view('order/view_menu_items', $viewData);
    }

    function paginateMenuItems() {
        $data = $this->menu_item_model->getMenuItemsPagination();
        echo json_encode($data);
    }

    function addMenuItem($iItemId = '', $ed = '') {
        try {
            $viewData['title'] = "Menu Items";

            $viewData['ACTION_LABEL'] = (isset($iItemId) && $iItemId != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

            if ($iItemId != '' && $ed != '' && $ed == 'y') {
                $getData = $this->menu_item_model->getMenuItemById($iItemId);
                $viewData['itemData'] = $getData;
            }

            if ($this->input->post('action') && $this->input->post('action') == 'backoffice.add') {
                $id = $this->menu_item_model->addMenuItem($_POST);
                if ($id) {
                    if (isset($_FILES) && !empty($_FILES)) {
                        $param = array(
                            'fileType' => 'image',
                            'maxSize' => 20,
                            'uploadFor' => array(
                                'key' => 'orderMenuItem',
                                'id' => $id
                            ),
                            'requireThumb' => TRUE
                        );
                        $this->load->library('fileupload', $param);
                        $upload_files = $this->fileupload->upload($_FILES, 'vItemImage');
                        if (!empty($upload_files)) {
                            foreach ($upload_files as $V) {
                                $this->menu_item_model->updateMenuItemImage($id, $V);
                            }
                        }
                    }
                    $succ = array('0' => "Menu Item added successfully.");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Menu Item not added, please try again.");
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('order/viewMenuItems', 'refresh');
            } elseif ($this->input->post('action') && $this->input->post('action') == 'backoffice.edit') {
                $oldImage=$_POST['vItemImage'];
                unset($_POST['vItemImage']);
                //print_r($_POST['vItemImage']);die;
                if ($this->menu_item_model->editMenuItem($_POST)) {
                    $param = array(
                        'fileType' => 'image',
                        'maxSize' => 20,
                        'uploadFor' => array(
                            'key' => 'orderMenuItem',
                            'id' => $this->input->post('iItemId')
                        ),
                        'requireThumb' => TRUE
                    );

                    $this->load->library('fileupload', $param);
                    //echo $this->input->post('removepic');die;
                    if ($this->input->post('removepic') == '1') {
                        $this->fileupload->removeFile();                     
                        if (!empty($oldImage)) {
                            $ImagePath = 'orderMenuItem/' . $this->input->post('iItemId') . '/' . $oldImage;
                            $ImageThumbPath = 'orderMenuItem/' . $this->input->post('iItemId') . '/thumb/' . $oldImage;
                            //$this->aws_sdk->deleteImage($ImagePath);
                            //$this->aws_sdk->deleteImage($ImageThumbPath);
                        }

                        $this->menu_item_model->updateMenuItemImage($this->input->post('iItemId'), '');
                    }
                    if (isset($_FILES) && !empty($_FILES) && $_FILES['vItemImage']['error'] === 0) {
                        $this->load->library('fileupload', $param);
                        $this->fileupload->removeFile();
                        $upload_files = $this->fileupload->upload($_FILES, 'vItemImage');
                        if (!empty($upload_files)) {
                            foreach ($upload_files as $V) {
                                $this->menu_item_model->updateMenuItemImage($this->input->post('iItemId'), $V);
                            }
                            
                            if (!empty($oldImage)) {
                                $ImagePath = 'orderMenuItem/' . $this->input->post('iItemId') . '/' . $oldImage;
                                $ImageThumbPath = 'orderMenuItem/' . $this->input->post('iItemId') . '/thumb/' . $oldImage;
                                //$this->aws_sdk->deleteImage($ImagePath);
                                //$this->aws_sdk->deleteImage($ImageThumbPath);
                            }
                        }
                    }
                    $succ = array('0' => "Menu Item updated successfully.");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Unable to update Menu Item, please try again.");
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('order/viewMenuItems', 'refresh');
            }

            $viewData['restaurants'] = $this->menu_item_model->getRestaurants();

            $viewData['menuItemCategories'] = $this->menu_item_model->getMenuItemCategories();

            $viewData['mealTypes'] = $this->menu_item_model->getMealTypes();

            $this->load->view('order/menu_item_add', $viewData);
        } catch (Exception $ex) {
            throw new Exception('Error in addMenuItem function - ' . $ex);
        }
    }

    function deleteMenuItems() {
        $data = $_POST['rows'];
        $removeCategory = $this->menu_item_model->softDelete($_POST['rows']);
        if ($removeCategory != '') {
            //$res=$this->aws_sdk->deleteDirectory('orderMenuItem/'.$data[0]);
            echo '1';
        } else {
            echo '0';
        }
    }

    function menuItemStatus($id = '', $rm = '') {
        if ($id != '' && $rm != '' && $rm == 'y') {
            $changeStatus = $this->menu_item_model->changeStatus($id);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("category", 'refresh');
    }

    function viewMenuItemCategory() {
        $viewData = array("title" => "MenuItems");
        $this->load->view('order/view_menu_item_categories', $viewData);
    }

    function paginateMenuItemCategory() {
        $this->load->model('menu_item_category_model');
        $data = $this->menu_item_category_model->getMenuItemCategoryPagination();
        echo json_encode($data);
    }

    function addMenuItemCategory($id = '', $ed = '') {
        try {
            $this->load->model('menu_item_category_model');
            $viewData['title'] = "Menu Items";

            $viewData['ACTION_LABEL'] = (isset($id) && $id != '' && $ed != '' && $ed == 'y') ? "Edit" : "Add";

            if ($id != '' && $ed != '' && $ed == 'y') {
                $getData = $this->menu_item_category_model->getMenuItemCategoryById($id);
                $viewData['itemData'] = $getData;
            }

            if ($this->input->post('action') && $this->input->post('action') == 'backoffice.add') {
                $id = $this->menu_item_category_model->addMenuItemCategory($_POST);
                if ($id) {
                    $succ = array('0' => "Menu Item Category added successfully.");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Menu Item Category not added, please try again.");
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('order/viewMenuItemCategory', 'refresh');
            } elseif ($this->input->post('action') && $this->input->post('action') == 'backoffice.edit') {
                if ($this->menu_item_category_model->editMenuItemCategory($_POST)) {
                    $succ = array('0' => "Menu Item Category updated successfully.");
                    $this->session->set_userdata('SUCCESS', $succ);
                } else {
                    $err = array('0' => "Unable to update Menu Item Category, please try again.");
                    $this->session->set_userdata('ERROR', $err);
                }
                redirect('order/viewMenuItemCategory', 'refresh');
            }
            $this->load->view('order/menu_item_category_add', $viewData);
        } catch (Exception $ex) {
            throw new Exception('Error in addMenuItemCategory function - ' . $ex);
        }
    }

    function deleteMenuItemCategory() {
        $this->load->model('menu_item_category_model');
        $data = $_POST['rows'];
        $removeCategory = $this->menu_item_category_model->softDelete($_POST['rows']);
        if ($removeCategory != '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    function menuItemCategoryStatus($id = '', $rm = '') {
        if ($id != '' && $rm != '' && $rm == 'y') {
            $this->load->model('menu_item_category_model');
            $changeStatus = $this->menu_item_category_model->changeStatus($id);
            if ($changeStatus != '') {
                echo '1';
            } else {
                echo '0';
            }
        }
        // redirect("category", 'refresh');
    }

}
