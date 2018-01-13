<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <script>
           // window.location.href = '<?= BASEURL; ?>payumoney/success.php?payuid=<?php print htmlspecialchars($_POST['payuMoneyId']); ?>&txnid=<?php print htmlspecialchars($_POST['txnid']); ?>&status=success';
        </script>
    </head>
    <title>PayUMoney</title>
    <body>
        Your Payment has been successful. Below are the details
        <p>Transaction No: <?php echo $_POST['txnid'] ?></p>
    </body>
</html>