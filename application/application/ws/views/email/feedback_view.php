<?php

##############################################################################################
//Body
##############################################################################################

$html = '<table width="98%" cellspacing="0" cellpadding="0" border="0">
        <tbody>
        <tr>
            <td align="left" style=" background: linear-gradient(to bottom, #64D598, #43B8A8) repeat scroll 0 0 #64D598;
                font-family: verdana; left: 16px; line-height: 0.3em; padding: 18px 0 0;">
                 
            </td>
        </tr>
        <tr style="background:#E6E6E6;">
            <td valign="top" align="left" style="font-family:verdana;font-size:16px;line-height:1.3em;text-align:left;padding:15px">
                <table  width="100%">
                    <tr style="background:#FFFFFF;border-radius:5px;">
                        <td style="font-family:verdana;font-size:13px;line-height:1.3em;text-align:left;padding:15px;">
                            <h1 style="font-family:verdana;color:#424242;font-size:28px;line-height:normal;letter-spacing:-1px">	
									        					Dear Admin,
                            </h1>
                            <p style="color:#ADADAD">
                                Feedback
                            </p>
                            <p> 
                                <b style="color:#036564;">
                                    ' . $vFeedback . ' 
                                </b>
                            </p>
                            <hr style="margin-top:30px;border-top-color:#ccc;border-top-width:1px;border-style:solid none none">
                            <p>
                            	Thanks & regards,
								<br>
                                Masso Team.
                          	</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>';
?>
<?php

##############################################################################################
//SEND MAIL
##############################################################################################

$to = FEEDBACK_EMAIL;

$message = $html;

$headers .= "Content-type: text/html\r\n";
$send_mail = mail($to, $subject, $message, $headers);
if ($send_mail) {
    // mprd($this->email->print_debugger());
    return 1;
} else {
    // mprd($this->email->print_debugger());
    return 0;
}
?>