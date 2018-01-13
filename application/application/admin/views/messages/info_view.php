<?php

if ($this->session->userdata('INFO')) {
    $errors = $this->session->userdata('INFO');
    $this->session->unset_userdata('INFO');
    foreach ($errors as $key => $val)
        echo "<div class='alert i_magnifying_glass yellow'>$val</div>";
}
?>