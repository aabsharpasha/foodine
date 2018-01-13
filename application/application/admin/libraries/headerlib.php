<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//require_once(DOCROOT."classes/libraries/user_agent.class.php");
class CI_Headerlib {

    public $title;
    public $body_id;
    public $body_function;
    public $iphone_css;
    public $safari_css;
    public $opera_css;
    public $icon_path;
    public $css_path;
    public $javascripts;
    public $dash_javascripts;
    public $jquery_view;
    public $jquery_latest;
    public $javascript_path;
    public $dash_stylesheets;
    public $login_stylesheets;
    public $javascript_plugins;
    public $login_javascripts;
    public $plugins;
    public $http_meta_tags;
    public $content_meta_tags;
    public $keywords;
    public $doctype;
    //public $user_agent;
    public $config;

    public function __construct() {
        $this->doctype = 'XHTML1.1'; // Set the Doctype definition
        $this->title = MAINTITLE; // Set the default page title
        $this->body_id = "mainbody"; // Set the default body id (leave blank for no id)
        $this->icon_path = ''; // Set default icon path for iPhone relative BASEURL.
        $this->css_path = CSS_URL; // Set default path to browser specific css files relative to BASEURL.
        $this->plugin_path = PLUGIN_URL; // Set default path to browser specific css files relative to BASEURL.
        $this->safari_css = TRUE; // Safari specific stylesheet
        $this->opera_css = FALSE; // Opera specific stylesheet
        $this->iphone_css = TRUE; // iPhone specific stylesheet
        $this->login_stylesheets = array(
            'theme' => 'css/cloud-admin.css',
            'font' => 'font-awesome/css/font-awesome.min.css',
            'date' => 'js/bootstrap-daterangepicker/daterangepicker-bs3.css',
            'uniform' => 'js/uniform/css/uniform.default.min.css',
            'style' => 'css/style.css',
            'animate' => 'css/animatecss/animate.min.css'
                //'default'     =>  'css/themes/default.css',
                //'responsive'  =>  'css/responsive.css'
        );
        $this->stylesheets = array(
            'theme' => 'css/cloud-admin.min.css',
            'hover' => 'css/hover.min.css',
            'homestyle' => 'css/style.min.css',
            'default' => 'css/themes/default.min.css',
            'responsive' => 'css/responsive.min.css',
            'font' => 'font-awesome/css/font-awesome.min.css',
            'animate' => 'css/animatecss/animate.min.css',
            'style' => 'js/jquery-todo/css/styles.css',
            'date' => 'js/bootstrap-daterangepicker/daterangepicker-bs3.css',
            'griter' => 'js/gritter/css/jquery.gritter.css',
            'customui' => 'js/jquery-ui-1.10.3.custom/css/custom-theme/jquery-ui-1.10.3.custom.min.css',
            'datatablecss' => 'js/datatables/media/css/jquery.dataTables.min.css',
            'datatblejs' => 'js/datatables/media/assets/css/datatables.min.css',
            'tabltool' => 'js/datatables/extras/TableTools/media/css/TableTools.min.css',
            'ColReorder' => 'js/datatables/extras/ColReorder/media/css/ColReorder.css',
            'ColVis' => 'js/datatables/extras/ColVis/media/css/ColVis.css'
        );
        $this->dash_stylesheets = array(
            'theme' => 'css/cloud-admin.min.css',
            'hover' => 'css/hover.min.css',
            'homestyle' => 'css/style.min.css',
            'default' => 'css/themes/default.min.css',
            'responsive' => 'css/responsive.min.css',
            'font' => 'font-awesome/css/font-awesome.min.css',
            'animate' => 'css/animatecss/animate.min.css'
        );
        $this->stylesheets_forms = array(
            'theme' => 'css/cloud-admin.css',
            'homestyle' => 'css/style.css',
            'default' => 'css/themes/default.css',
            'responsive' => 'css/responsive.css',
            'font' => 'font-awesome/css/font-awesome.min.css',
            'animate' => 'css/animatecss/animate.min.css',
            'style' => 'js/jquery-todo/css/styles.css',
            'date' => 'js/bootstrap-daterangepicker/daterangepicker-bs3.css',
            'griter' => 'js/gritter/css/jquery.gritter.css',
            'customui' => 'js/jquery-ui-1.10.3.custom/css/custom-theme/jquery-ui-1.10.3.custom.min.css',
            'typhead' => 'js/typeahead/typeahead.css',
            'uniform' => 'js/uniform/css/uniform.default.min.css',
            'fileupload' => 'js/bootstrap-fileupload/bootstrap-fileupload.min.css',
            'select2' => 'js/select2/select2.min.css',
            'blueimp' => 'js/blueimp/gallery/blueimp-gallery.min.css',
            'jquery-upload' => 'js/jquery-upload/css/jquery.fileupload.css',
            'jquery.fileupload-ui' => 'js/jquery-upload/css/jquery.fileupload-ui.css'
        );




        $this->plugins = array(); // Set a default stylesheet
        $this->javascript_path = JS_URL; // Set path to javascripts
        $this->external_js = array(
        );
        $this->external_js_view = array(
            "modernizrjs" => "https://code.jquery.com/jquery-migrate-1.2.1.js"
        );
        $this->external_css = array(
            'jquerymin' => 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700'
        );


        $this->javascripts = array(
            "modernizrjs" => "js/jquery/jquery-2.0.3.min.js",
            "bootstrap" => "js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js",
            "jqueryjs" => "bootstrap-dist/js/bootstrap.min.js",
            "datatable" => "js/datatables/media/js/jquery.dataTables.min.js",
            "assetsdatatable" => "js/datatables/media/assets/js/datatables.min.js",
            "celsdlreorder" => "js/datatables/extras/ColReorder/media/js/ColReorderwithresize.js",
            "colvis" => "js/datatables/extras/ColVis/media/js/ColVis.js",
            "masdoment" => "js/bootstrap-daterangepicker/moment.min.js",
            "timeag" => "js/timeago/jquery.timeago.min.js",
            // "date"           =>  "js/bootstrap-daterangepicker/daterangepicker.min.js",
            "slimscroll" => "js/jQuery-slimScroll-1.3.0/jquery.slimscroll.min.js",
            "slimscrollhorizontal" => "js/jQuery-slimScroll-1.3.0/slimScrollHorizontal.min.js",
            "block" => "js/jQuery-BlockUI/jquery.blockUI.min.js",
            "tabletool" => "js/datatables/extras/TableTools/media/js/TableTools.min.js",
            "clipboard" => "js/datatables/extras/TableTools/media/js/ZeroClipboard.min.js",
            "cellreorder" => "js/datatables/extras/FixedHeader/js/FixedHeader.min.js",
            "cookie" => "js/jQuery-Cookie/jquery.cookie.min.js",
            "griter" => "js/gritter/js/jquery.gritter.min.js",
            "script" => "js/script.js",
            "main" => "js/main.js",
            "isotope" => "js/isotope/jquery.isotope.min.js",
            "imagesloaded" => "js/isotope/imagesloaded.pkgd.min.min.js",
            "blockUI" => "js/jQuery-BlockUI/jquery.blockUI.min.js"
        );
        $this->dash_javascripts = array(
            "modernizrjs" => "js/jquery/jquery-2.0.3.min.js",
            "bootstrap" => "js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js",
            "jqueryjs" => "bootstrap-dist/js/bootstrap.min.js",
            "masdoment" => "js/bootstrap-daterangepicker/moment.min.js",
            "timeag" => "js/timeago/jquery.timeago.min.js",
            // "date"           =>  "js/bootstrap-daterangepicker/daterangepicker.min.js",
            "slimscroll" => "js/jQuery-slimScroll-1.3.0/jquery.slimscroll.min.js",
            "slimscrollhorizontal" => "js/jQuery-slimScroll-1.3.0/slimScrollHorizontal.min.js",
            "block" => "js/jQuery-BlockUI/jquery.blockUI.min.js",
            "cookie" => "js/jQuery-Cookie/jquery.cookie.min.js",
            "script" => "js/script.min.js",
            "main" => "js/main.min.js"
        );
        $this->javascripts_form = array(
            "modernizrjs" => "js/jquery/jquery-2.0.3.min.js",
            "bootstrap" => "js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js",
            "jqueryjs" => "bootstrap-dist/js/bootstrap.min.js",
            "masdoment" => "js/bootstrap-daterangepicker/moment.min.js",
            "date" => "js/bootstrap-daterangepicker/daterangepicker.min.js",
            "slimscroll" => "js/jQuery-slimScroll-1.3.0/jquery.slimscroll.min.js",
            "slimscrollhorizontal" => "js/jQuery-slimScroll-1.3.0/slimScrollHorizontal.min.js",
            "typehead" => "js/typeahead/typeahead.min.js",
            "autosize" => "js/autosize/jquery.autosize.min.js",
            "validation" => "js/jquery.validate.min.js",
            "countable" => "js/countable/jquery.simplyCountable.min.js",
            "select2" => "js/select2/select2.min.js",
            "uniform" => "js/uniform/jquery.uniform.min.js",
            "block" => "js/jQuery-BlockUI/jquery.blockUI.min.js",
            "cookie" => "js/jQuery-Cookie/jquery.cookie.min.js",
            "griter" => "js/gritter/js/jquery.gritter.min.js",
            "script" => "js/script.js",
            "main" => "js/main.js"
        );


        $this->login_javascripts = array(
            "modernizrjs" => "js/jquery/jquery-2.0.3.min.js",
            "bootstrap" => "js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js",
            "jqueryjs" => "bootstrap-dist/js/bootstrap.min.js",
            "validation" => "js/jquery.validate.min.js",
            "cookie" => "js/jQuery-Cookie/jquery.cookie.min.js",
            "sparklinejs" => "js/uniform/jquery.uniform.min.js",
            "canvasjs" => "js/backstretch/jquery.backstretch.min.js",
            "transitionjs" => "js/script.js",
            "main" => "js/main.js"
        );

        $this->javascript_plugins = array();
        //$this->user_agent             = new User_agent();
        $this->keywords = '';
        $this->http_meta_tags = array();
        $this->content_meta_tags = array('description' => '',
            'apple-mobile-web-app-capable' => 'yes',
            'apple-mobile-web-app-status-bar-style' => 'black',
            'viewport' => 'width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no'
        );
        //$this->config = $GLOBALS['config'];
    }

// End __construct function

    public function set_page_info($title, $body_id) {
        $this->title = $title;
        $this->body_id = ' id="' . $body_id . '" ';
    }

// End set_page_info function

    public function set_title($title) {
        $this->title = $title;
    }

// End set_title function

    public function set_body_id($body_id) {
        $this->body_id = ' id="' . $body_id . '"';
    }

// End set_body_id function

    public function add_stylesheet($name, $file) {
        $this->stylesheets[$name] = $file;
    }

// End add_stylesheet function

    public function add_plugin($name, $file) {
        $this->plugins[$name] = $file;
    }

// End add_plugins function

    public function add_javascript($name, $file) {
        $this->javascripts[$name] = $file;
    }

// End add_javascript function

    public function add_login_javascripts($name, $file) {
        $this->login_javascripts[$name] = $file;
    }

// End add_javascript function

    public function add_javascript_plugins($name, $file) {
        $this->javascript_plugins[$name] = $file;
    }

// End add_javascript_plugins function

    public function add_meta_tag($name, $content) {
        $this->meta_tags[$name] = $content;
    }

// End add_meta_tag function

    public function data() {
        $data['doctype'] = $this->_doctype();
        $data['meta_tags'] = $this->_meta_tags();
        $data['title'] = $this->title;
        $data['body_id'] = $this->body_id;
        //$data['browser'] = $this->_browser();
        //$data['os'] = $this->user_agent->platform();
        //$data['iphone_headers'] = $this->_iPhone_headers();
        $data['stylesheets'] = $this->_stylesheets();
        $data['stylesheets_dash'] = $this->_stylesheets_dash();
        $data['stylesheets_form'] = $this->_stylesheets_form();
        $data['login_stylesheets'] = $this->_login_stylesheets();
        $data['plugins'] = $this->_plugins();
        $data['javascript'] = $this->_javascript();
        $data['javascript_dash'] = $this->_javascript_dash();
        $data['javascript_view'] = $this->_javascript_view();
        $data['javascript_form'] = $this->_javascript_form();
        $data['javascript_plugins'] = $this->_javascript_plugins();
        $data['login_javascripts'] = $this->_login_javascripts();
        $data['iejavascript'] = $this->_iejavascript();
        $data['gmap'] = $this->_gmap();
        $data['favicon'] = $this->_favicon();
        return $data;
    }

// End data function

    private function _doctype() {
        switch ($this->doctype) {
            case 'Strict':
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' . "\n" . '"https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
                break;
            case 'Transitional':
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"' . "\n" . '"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
                break;
            case 'Frameset':
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"' . "\n" . '"https://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd"><html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
            case 'XHTML':
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"' . "\n" . '"https://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
            case 'XHTML1.0':
                return '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
            case 'XHTML1.1':
                return '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "https://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en">' . "\n";
        }
    }

// End _doctype function

    private function _meta_tags() {
        $meta_tags = "<meta charset='utf-8'/>\n";
        foreach ($this->http_meta_tags as $http => $content) {
            $meta_tags .= '<meta http-equiv="' . $http . '" content="' . $content . '"/>' . "\n";
        }
        foreach ($this->content_meta_tags as $name => $content) {
            $meta_tags .= '<meta name="' . $name . '" content="' . $content . '"/>' . "\n";
        }
        return $meta_tags;
    }

// End _meta_tags function

    private function _stylesheets() {
        $stylesheets = "";
        //$this->_browser_specific_stylesheet();

        foreach ($this->stylesheets as $name => $file) {
            if ($name == 'default') {
                $stylesheets .= '<link rel="stylesheet" id="skin-switcher" href="' . $this->css_path . $file . '" type="text/css" media="all" />' . "\n";
            } else {
                $stylesheets .= '<link rel="stylesheet" href="' . $this->css_path . $file . '" type="text/css" media="all" />' . "\n";
            }
        }
        if (count($this->external_css) > 0) {
            foreach ($this->external_css as $name => $url) {
                $stylesheets .= '<link rel="stylesheet" href="' . $url . '" type="text/css" media="all" />' . "\n";
            }
        }
        return $stylesheets;
    }

    private function _stylesheets_dash() {
        $stylesheets = "";
        //$this->_browser_specific_stylesheet();

        foreach ($this->dash_stylesheets as $name => $file) {
            if ($name == 'default') {
                $stylesheets .= '<link rel="stylesheet" id="skin-switcher" href="' . $this->css_path . $file . '" type="text/css" media="screen" />' . "\n";
            } else {
                $stylesheets .= '<link rel="stylesheet" href="' . $this->css_path . $file . '" type="text/css" media="screen" />' . "\n";
            }
        }
        if (count($this->external_css) > 0) {
            foreach ($this->external_css as $name => $url) {
                $stylesheets .= '<link rel="stylesheet" href="' . $url . '" type="text/css" media="screen" />' . "\n";
            }
        }
        return $stylesheets;
    }

/// End _stylesheets function

    private function _stylesheets_form() {
        $stylesheets = "";
        //$this->_browser_specific_stylesheet();

        foreach ($this->stylesheets_forms as $name => $file) {
            if ($name == 'default') {
                $stylesheets .= '<link rel="stylesheet" id="skin-switcher" href="' . $this->css_path . $file . '" type="text/css" media="screen" />' . "\n";
            } else {
                $stylesheets .= '<link rel="stylesheet" href="' . $this->css_path . $file . '" type="text/css" media="screen" />' . "\n";
            }
        }
        if (count($this->external_css) > 0) {
            foreach ($this->external_css as $name => $url) {
                $stylesheets .= '<link rel="stylesheet" href="' . $url . '" type="text/css" media="screen" />' . "\n";
            }
        }
        return $stylesheets;
    }

/// End _stylesheets function

    private function _login_stylesheets() {
        $stylesheets = "";
        //$this->_browser_specific_stylesheet();

        foreach ($this->login_stylesheets as $name => $file) {
            $stylesheets .= '<link rel="stylesheet" href="' . $this->css_path . $file . '" type="text/css" media="screen" />' . "\n";
        }
        if (count($this->external_css) > 0) {
            foreach ($this->external_css as $name => $url) {
                $stylesheets .= '<link rel="stylesheet" href="' . $url . '" type="text/css" media="screen" />' . "\n";
            }
        }
        return $stylesheets;
    }

/// End _stylesheets function

    private function _plugins() {
        $plugins = "";
        //$this->_browser_specific_stylesheet();
        foreach ($this->plugins as $name => $file) {
            $plugins .= '<link rel="stylesheet" href="' . $this->plugin_path . $file . '" type="text/css" media="screen" />' . "\n";
        }
        return $plugins;
    }

/// End PLUGINS function

    private function _javascript_plugins() {
        $javascript_plugins = "";
        //$this->_browser_specific_stylesheet();
        foreach ($this->javascript_plugins as $name => $file) {

            $javascript_plugins .= '<script src="' . $this->plugin_path . $file . '" type="text/javascript" charset="utf-8"></script>' . "\n";
        }
        return $javascript_plugins;
    }

/// End JAVASCRIPT PLUGINS function

    private function _login_javascripts() {
        $login_javascripts = "\n";
        //$this->_browser_specific_stylesheet();

        $login_javascripts .= '<script type="text/javascript">var BASEURL = "' . BASEURL . '"; </script>' . "\n";
        foreach ($this->login_javascripts as $name => $file) {
            $login_javascripts .= '<script src="' . $this->javascript_path . $file . '" type="text/javascript" charset="utf-8"></script>' . "\n";
        }
        if (count($this->external_js) > 0) {
            foreach ($this->external_js as $name => $url) {
                $login_javascripts .= '<script type="text/javascript" id="' . $name . '" src="' . $url . '"></script>' . "\n";
            }
        }
        return $login_javascripts;
    }

/// End BOTTOM JAVASCRIPT function

    private function _javascripts() {
        $login_javascripts = "\n";
        //$this->_browser_specific_stylesheet();

        $javascripts .= '<script type="text/javascript">var BASEURL = "' . BASEURL . '"; </script>' . "\n";

        foreach ($this->javascripts as $name => $file) {
            $javascripts .= '<script src="' . $this->javascript_path . $file . '" type="text/javascript" charset="utf-8"></script>' . "\n";
        }
        if (count($this->external_js) > 0) {
            foreach ($this->external_js as $name => $url) {
                $javascripts .= '<script type="text/javascript" id="' . $name . '" src="' . $url . '"></script>' . "\n";
            }
        }

        return $javascripts;
    }

/// End BOTTOM JAVASCRIPT function

    private function _iejavascript() {
        $javascript_content = "\n";
        $javascript_content .= '<!--[if lte IE 7]>
                                     <script src="https://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE8.js" type="text/javascript">
                                     </script>
                                     <script src="https://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE7-squish.js"  type="text/javascript">
                                     </script>
                                    <![endif]-->';
        return $javascript_content;
    }

    private function _gmap() {
        $javascript_content = "\n";
        $javascript_content .= '<script id="gmapscript" src="https://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=' . gmapkey . '" type="text/javascript"></script>';
        return $javascript_content;
    }

    private function _javascript_dash() {
        $javascript_content = "\n";

        $javascript_content .= '<script type="text/javascript">var BASEURL = "' . BASEURL . '"; </script>' . "\n";
        foreach ($this->dash_javascripts as $library => $file) {
            $javascript_content .= '<script src="' . $this->javascript_path . $file . '" type="text/javascript" charset="utf-8"></script>' . "\n";
        }
        if (count($this->external_js) > 0) {
            foreach ($this->external_js as $name => $url) {
                $javascript_content .= '<script type="text/javascript" id="' . $name . '" src="' . $url . '"></script>' . "\n";
            }
        }
        return $javascript_content;
    }

    private function _javascript() {
        $javascript_content = "\n";

        $javascript_content .= '<script type="text/javascript">var BASEURL = "' . BASEURL . '"; </script>' . "\n";
        foreach ($this->javascripts as $library => $file) {
            $javascript_content .= '<script src="' . $this->javascript_path . $file . '" type="text/javascript" charset="utf-8"></script>' . "\n";
        }
        if (count($this->external_js) > 0) {
            foreach ($this->external_js as $name => $url) {
                $javascript_content .= '<script type="text/javascript" id="' . $name . '" src="' . $url . '"></script>' . "\n";
            }
        }
        return $javascript_content;
    }

// End _javascript function

    private function _javascript_view() {
        $javascript_content = "\n";

        $javascript_content .= '<script type="text/javascript">var BASEURL = "' . BASEURL . '"; </script>' . "\n";
        foreach ($this->javascripts as $library => $file) {
            $javascript_content .= '<script src="' . $this->javascript_path . $file . '" type="text/javascript" charset="utf-8"></script>' . "\n";
        }
        if (count($this->external_js_view) > 0) {
            foreach ($this->external_js_view as $name => $url) {
                $javascript_content .= '<script type="text/javascript" id="' . $name . '" src="' . $url . '"></script>' . "\n";
            }
        }
        return $javascript_content;
    }

// End _javascript function

    private function _javascript_form() {
        $javascript_content = "\n";

        $javascript_content .= '<script type="text/javascript">var BASEURL = "' . BASEURL . '"; </script>' . "\n";
        foreach ($this->javascripts_form as $library => $file) {
            $javascript_content .= '<script src="' . $this->javascript_path . $file . '" type="text/javascript" charset="utf-8"></script>' . "\n";
        }
        if (count($this->external_js_view) > 0) {
            foreach ($this->external_js_view as $name => $url) {
                $javascript_content .= '<script type="text/javascript" id="' . $name . '" src="' . $url . '"></script>' . "\n";
            }
        }
        return $javascript_content;
    }

// End _javascript function

    private function _favicon() {
        $favicon = '<link rel="shortcut icon" type="image/x-icon"  href="' . $this->icon_path . 'favicon.ico" />';
        return $favicon;
    }

// End _favicon function

    public function debug() {
        $data = $this->data();
        $info = "";
        foreach ($data as $key => $value) {
            $info .= $key . ' - ' . htmlentities($value) . "<br />";
        }
        return $info;
    }

// End debug function
}

// End Header class.
?>
