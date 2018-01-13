<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 72);
?>
<!doctype html>
<html lang="en-us">
    <head>
        <?= $headerData['stylesheets']; ?>
    </head>
    <style type="text/css">
        .modal-body{
            display:inline;
        }
        .delete_comment{
            position: absolute;
            right:0px;
            top:5px;
        }
    </style>
    <body>
        <div class="box-body" style="border: 1px solid #ddd;">
            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                <thead>
                    <tr>
                        <th>Restaurant</th>
                        <th>Offer Title</th>
                        <th>Type OF Offer</th>
                        <th>Validity</th>
                        <th>Use Count</th>
                        <th>Created On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th>Restaurant</th>
                        <th>Offer Title</th>
                        <th>Type OF Offer</th>
                        <th>Validity</th>
                        <th>Use Count</th>
                        <th>Created On</th>
                        <th>Status</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php $ADMINTYPE = $this->session->userdata('ADMINTYPE'); ?>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate_mostSuccessfullDeal?<?php echo http_build_query($data); ?>';
            $(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                var btn_show_hide = parseInt('<?= ($ADMINTYPE == 1 || $ADMINTYPE == 2) ? 1 : 0; ?>');

                var target = [
                    {
                        "aTargets": [3], // Column to target
                        "mRender": function (data, type, full) {
                            return full['dtStartDate']+"<BR> To <BR>"+full['dtExpiryDate'];
                        }
                    }
                ];
                var aoculumn = [
                    /*1*/ {"mData": "restaurantName", "sWidth": "15%"},
                    /*2*/ {"mData": "vOfferText", "sWidth": "20%"},
                    /*3*/ {"mData": "offerType", "sWidth": "15%"},
                    /*4*/ {"mData": "dtExpiryDate", "sWidth": "15%"},
                    /*5*/ {"mData": "useCount", "sWidth": "10%"},
                    /*6*/ {"mData": "tCreatedAt", "sWidth": "15%"},
                    /*7*/ {"mData": "eStatus", "sWidth": "10%"},
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 4, 'desc');
            });

        </script>

    </body>