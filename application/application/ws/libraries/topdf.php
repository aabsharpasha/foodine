<?php

/**
 * Description of pickup
 * @author OpenXcell Technolabs
 */
class ToPDF {

    var $PDF_OBJ;

    function __construct() {

        /*
         * FIRST WE HAVE LOAD HTML2PDF CLASS FILE...
         */

        require_once DIR_LIB . 'html2pdf.php';
        $this->PDF_OBJ = new HTML2PDF('P', 'A4', 'en');
    }

    function create($FILE_NAME = '', $REPLACE_VALUES = array(), $fileName = '') {
        /*
         * WHICH ONE FILE I NEED TO READ IT DOWN...
         */
        $CONTENT = file_get_contents(DIR_VIEW . 'pdf/' . $FILE_NAME, TRUE);

        /*
         * REPLACE THE VALUES WHICH ARE PASSED FROM THE REQUESTED SIDE...
         */
        foreach ($REPLACE_VALUES AS $K => $V) {
            $CONTENT = str_replace($K, $V, $CONTENT);
        }

        /*
         * WRITE THE HTML TO THE PDF FILE...
         */
        $this->PDF_OBJ->WriteHTML($CONTENT);

        /*
         * SAVE IT TO THE EXISTING LOCATION...
         */
        $this->PDF_OBJ->Output(DIR_PDF . 'finalized-order-' . $fileName . '.pdf', 'F');
    }

}
