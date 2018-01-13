<?php

class MY_Language extends CI_Language {

    function MY_Language() {
        parent::CI_Language();
    }

    /**
     * Makes switching between languages easier
     *
     *  example : http://codeigniter.com/forums/viewreply/339962/
     */
    function switch_to($idiom) {
        $CI = & get_instance();
        if (is_string($idiom) && $idiom != $CI->config->item('language')) {
            $CI->config->set_item('language', $idiom);

            $loaded = $this->is_loaded;

            $this->is_loaded = array();

            foreach ($loaded as $file) {
                $this->load(str_replace('_lang.php', '', $file));
            }
        }
    }

}

?>
