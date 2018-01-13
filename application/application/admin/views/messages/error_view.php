<?php

if ($this->session->userdata('ERROR')) {
    $errors = $this->session->userdata('ERROR');
    $this->session->unset_userdata('ERROR');
    foreach ($errors as $key => $val)
        echo "<div id='alert'><div class='alert alert-danger center'>" . $val . "</div></div>";
}
?>