<?php

class Permission extends CI_Controller {

    var $controller;

    function __construct() {
        parent::__construct();
        $this->controller = 'permission';

        $this->load->model('permission_model');
    }

    /* permission types @START  */

    function types() {
        $this->load->view('permission/types/list');
    }

    function paginate_types() {
        $data = $this->permission_model->get_paginate_types();
        echo json_encode($data);
    }

    function add_type($type_id = '') {
        $viewData['title'] = "Permission Type Management";

        $viewData['ACTION_LABEL'] = (isset($type_id) && $type_id != '') ? "Edit" : "Add";

        if ($type_id != '') {
            $viewData['getPermissionTypeData'] = $this->permission_model->getPermissionRecordById('type', $type_id);
        }

        $this->load->view('permission/types/form', $viewData);

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.add') {
            $dealsAdd = $this->permission_model->addType($_POST);
            redirect('permission/types');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.edit') {
            $dealsAdd = $this->permission_model->editType($_POST);
            redirect('permission/types');
        }
    }

    function delete_types() {
        $data = $_POST['rows'];
        $resp = $this->permission_model->remove_all('type', $_POST['rows']);
        echo (string) $resp;
    }

    function status($type, $id) {
        $resp = $this->permission_model->changeStatus($type, $id);
        echo (string) $resp;
    }

    /* permission types @END */

    /* permission modules @START */

    function module() {
        $this->load->view('permission/modules/list');
    }

    function paginate_module() {
        $data = $this->permission_model->get_paginate_modules();
        echo json_encode($data);
    }

    function add_module($type_id = '') {
        $viewData['title'] = "Permission Module Management";

        $viewData['ACTION_LABEL'] = (isset($type_id) && $type_id != '') ? "Edit" : "Add";

        if ($type_id != '') {
            $viewData['getPermissionTypeData'] = $this->permission_model->getPermissionRecordById('module', $type_id);
        }

        $this->load->view('permission/modules/form', $viewData);

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.add') {
            $dealsAdd = $this->permission_model->addModule($_POST);
            redirect('permission/module');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.edit') {
            $dealsAdd = $this->permission_model->editModule($_POST);
            redirect('permission/module');
        }
    }

    function delete_module() {
        $data = $_POST['rows'];
        $resp = $this->permission_model->remove_all('module', $_POST['rows']);
        echo (string) $resp;
    }

    /* permission modules @END */

    /* permission pages @START */

    function pages() {
        $this->load->view('permission/pages/list');
    }

    function paginate_page() {
        $data = $this->permission_model->get_paginate_page();
        echo json_encode($data);
    }

    function add_page($type_id = '') {
        $viewData['title'] = "Permission Page Management";

        $viewData['ACTION_LABEL'] = (isset($type_id) && $type_id != '') ? "Edit" : "Add";
        $viewData['allModules'] = $this->permission_model->get_paginate_modules()['aaData'];

        if ($type_id != '') {
            $viewData['getPermissionTypeData'] = $this->permission_model->getPermissionRecordById('page', $type_id);
        }

        $this->load->view('permission/pages/form', $viewData);

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.add') {
            $dealsAdd = $this->permission_model->addPage($_POST);
            redirect('permission/pages');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.edit') {
            $dealsAdd = $this->permission_model->editPage($_POST);
            redirect('permission/pages');
        }
    }

    function delete_page() {
        $data = $_POST['rows'];
        $resp = $this->permission_model->remove_all('page', $_POST['rows']);
        echo (string) $resp;
    }

    /* permission pages @END */

    /* permission @START */

    function manage() {
        $this->load->view('permission/list');
    }

    function add_permission($type_id = '') {
        $viewData['title'] = "Permission Page Management";

        $viewData['ACTION_LABEL'] = (isset($type_id) && $type_id != '') ? "Edit" : "Add";
        $viewData['admin_type_id'] = $type_id;

        if ($type_id != '') {
            $viewData['getPermissionTypeData'] = $this->permission_model->getPermissionRecordById('type', $type_id);
        }

        $this->load->view('permission/form', $viewData);

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.add') {
            //$dealsAdd = $this->permission_model->addPermission($_POST);
            redirect('permission/manage');
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.edit') {
            $dealsAdd = $this->permission_model->editPermission($_POST);
            redirect('permission/manage');
        }
    }

    /* permission pages @END */

    /* permission user @START */

    function user($type = '') {
        if ($type != '') {
            
        } else {
            $this->load->view('permission/user/list');
        }
    }

    function paginate_user() {
        $data = $this->permission_model->get_paginate_user();
        echo json_encode($data);
    }

    function add_user($admin_id = '') {
        $viewData['title'] = "Permission User Management";

        $viewData['ACTION_LABEL'] = (isset($admin_id) && $admin_id != '') ? "Edit" : "Add";
        $viewData['admin_id'] = $admin_id;
        $viewData['admin_types'] = $this->permission_model->get_all_permission_type();
        $viewData['get_restaurants'] = $this->permission_model->get_all_restaurants();

        if ($admin_id != '') {
            $viewData['getPermissionTypeData'] = $this->permission_model->getPermissionRecordById('user', $admin_id);
        }

        $this->load->view('permission/user/form', $viewData);

        if ($this->input->post('action') && $this->input->post('action') == 'backoffice.add') {
            $resp = $this->permission_model->addUser($_POST);
            if ($resp == 1) {
                $succ = array('0' => USER_ADDED);
                $this->session->set_userdata('SUCCESS', $succ);
                redirect('permission/user');
            } else if ($resp == -2) {
                $succ = array('0' => 'Email address already exists!!');
                $this->session->set_userdata('ERROR', $succ);
            }
        } else if ($this->input->post('action') && $this->input->post('action') == 'backoffice.edit') {
            $resp = $this->permission_model->editUser($_POST);
            if ($resp == 1) {
                $succ = array('0' => USER_EDITED);
                $this->session->set_userdata('SUCCESS', $succ);
                redirect('permission/user');
            } else if ($resp == -2) {
                $succ = array('0' => 'Email address already exists!!');
                $this->session->set_userdata('ERROR', $succ);
            }
        }
    }

    function delete_user() {
        $data = $_POST['rows'];
        $resp = $this->permission_model->remove_all('user', $_POST['rows']);
        echo (string) $resp;
    }

    /* permission user @END */
}
