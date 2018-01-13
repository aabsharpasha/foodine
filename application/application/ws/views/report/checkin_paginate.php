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
                        <th>Date</th>
                        <th>Restaurant</th>
                        <th>Normal Check Ins</th>
                        <th>Booking Check Ins</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th>Date</th>
                        <th>Restaurant</th>
                        <th>Normal Check Ins</th>
                        <th>Booking Check Ins</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php $ADMINTYPE = $this->session->userdata('ADMINTYPE'); ?>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate_checkIn?<?php echo http_build_query($data); ?>';
            $(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                var btn_show_hide = parseInt('<?= ($ADMINTYPE == 1 || $ADMINTYPE == 2) ? 1 : 0; ?>');

                var target = [
                    {
                        "aTargets": [2], // Column to target
                        "mRender": function (data, type, full) {
                            return parseInt(full['totalCheckIns']) - parseInt(full['bookingCheckIns']);
                        }
                    }
                ];
                var aoculumn = [
                    /*1*/ {"mData": "tCreatedAt", "sWidth": "15%"},
                    /*2*/ {"mData": "restaurantName", "sWidth": "20%"},
                    /*3*/ {"mData": "totalCheckIns", "sWidth": "15%"},
                    /*4*/ {"mData": "bookingCheckIns", "sWidth": "15%"},
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 2, 'desc');
            });

        </script>

    </body>