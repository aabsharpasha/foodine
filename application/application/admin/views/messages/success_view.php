<?php

if ($this->session->userdata('SUCCESS')) {
    $errors = $this->session->userdata('SUCCESS');
    $this->session->unset_userdata('SUCCESS');
    foreach ($errors as $key => $val)
        echo "<div id='alert'><div class=' alert alert-block alert-info fade in center'>$val</div></div>";
}
?>