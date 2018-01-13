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
                        <th>Search Text</th>
                        <th>Search Count</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th>Date</th>
                        <th>Search Text</th>
                        <th>Search Count</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php $ADMINTYPE = $this->session->userdata('ADMINTYPE'); ?>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate_indirectSearch?<?php echo http_build_query($data); ?>';
            $(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                var btn_show_hide = parseInt('<?= ($ADMINTYPE == 1 || $ADMINTYPE == 2) ? 1 : 0; ?>');

                var target = [
                ];
                var aoculumn = [
                    /*1*/ {"mData": "tCreatedAt", "sWidth": "15%"},
                    /*2*/ {"mData": "vSearchText", "sWidth": "20%"},
                    /*4*/ {"mData": "searchCount", "sWidth": "15%"},
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 2, 'desc');
            });

        </script>

    </body>