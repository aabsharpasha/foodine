<div class="container margin-top-5">
    <div class="row padding0 margin0auto">
        <div class="col-lg-8 padding0 margin0auto">
            <div class="padding-left-15 text-justify">
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        <h1 class="padding0 margin0auto"><?= ucfirst($viewData['userName']); ?></h1>
                    </div>
                </div>
                <BR><BR>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total= 0;
                                foreach($viewData["data"] AS $row){ 
                                    $total+=$row['iPoints'];
                                    ?>
                                <tr>
                                    <td><?php echo $row['tCreatedAt']; ?></td>
                                    <td><?php echo $row['vType']; ?></td>
                                    <td><?php echo $row['iPoints']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                    <th><?php echo $total; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>