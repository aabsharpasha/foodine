<?php

##############################################################################################
//MAIL BODY
##############################################################################################
/* <img width="60" height="60" alt="Spoco Sports" src="'.DOMAIN_URL.'/images/spoco_logo.png" /> */
$html = '<table width="98%" cellspacing="0" cellpadding="0" border="0">
        <tbody>
        <tr>
            <td align="left" style=" background: linear-gradient(to bottom, #64D598, #43B8A8) repeat scroll 0 0 #64D598;
                font-family: verdana; left: 16px; line-height: 0.3em; padding: 18px 0 0;">
                   <img src="' . DOMAIN_URL . '/images/doodle/logo-shadow-email.png" height="60" />
            </td>
        </tr>
        <tr style="background:#E6E6E6;">
            <td valign="top" align="left" style="font-family:verdana;font-size:16px;line-height:1.3em;text-align:left;padding:15px">
                <table  width="100%">
                    <tr style="background:#FFF;border-radius:5px;">
                        <td style="font-family:verdana;font-size:13px;line-height:1.3em;text-align:left;padding:15px;">
                            <h1 style="font-family:verdana;color:#424242;font-size:28px;line-height:normal;letter-spacing:-1px">
                            Welcome to Spoco,
                            </h1>
                            <p> 
                                <b style="color:#036564;">
                                    To activate your Spoco Sports account please click on the link below:
                                </b><br>
                                 
                            </p>
                            <div style="padding:5px 0 0 20px;">
                                    <p> 
                                           <p><a href="' . DOMAIN_URL . '/activate?' . base64_encode($userid) . '" target="_blank">' . DOMAIN_URL . '</a></p>
                                    </p>
                                    
                            </div>
                            <p>
                            	-The Spoco Sports Team<br>
                          	</p>
                       	 	<hr style="margin-top:30px;border-top-color:#ccc;border-top-width:1px;border-style:solid none none">
                            <p style="font-size:12px"> 
                                <b>Have questions?</b>
                                Contact Spoco Sports;your account administrator&mdash;at 
                                <a target="_blank" href="mailto:admin@spocosports.net" style="color:#666666;">
                                	admin@spocosports.net
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>
';
?>

<?php

##############################################################################################
//SEND MAIL
##############################################################################################

$to = $email;
$from = ADMIN_EMAIL;
$message = $html;
$headers = "From: $from\r\n";
$headers .= "Content-type: text/html\r\n";
$send_mail = mail($to, $subject, $message, $headers);
?>