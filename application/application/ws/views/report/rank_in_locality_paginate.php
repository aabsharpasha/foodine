<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 33);
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
                        <th>Category</th>
                        <th>Location</th>
                        <th>No Of Clicks</th>
                        <th>Table Bookings</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th>Restaurant</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>No Of Clicks</th>
                        <th>Table Bookings</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php $ADMINTYPE = $this->session->userdata('ADMINTYPE'); ?>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate_rankInLocalityReport?<?php echo http_build_query($data); ?>';
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
                            if(full["clicks"]==null){
                                return 0;
                            }else{
                                return full["clicks"];
                            }
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vRestaurantName", "sWidth": "15%"},
                    /*1*/ {"mData": "iCategoryID", "sWidth": "20%"},
                    /*2*/ {"mData": "vLocationName", "sWidth": "15%"},
                    /*3*/ {"mData": "clicks", "sWidth": "10%"},
                    /*4*/ {"mData": "tableBooking", "sWidth": "15%"},
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 3, 'desc');
            });

        </script>

    </body>