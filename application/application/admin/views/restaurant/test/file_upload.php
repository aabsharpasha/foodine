<form action="<?= BASEURL . $this->controller; ?>/fileupload" method="post" enctype="multipart/form-data">
    <input type="file" name="image_file" id="image_file" />
    <input type="hidden" value="1" name="btnRemove">
    
    <input type="submit" name="btnUpload" id="btnUpload" />
</form>