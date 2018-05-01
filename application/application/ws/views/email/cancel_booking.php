<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title> Marco - Corporate Responsive Email Template</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <style type="text/css">
            @charset "utf-8";
            /* CSS Document */

            body, #body_style {
                width: 100% !important;
                background: #ffffff;
                font-family: Arial, Helvetica, sans-serif;
                color: #333;
                line-height: 1.3;
            }

            .ExternalClass {
                width: 100%;
            }
            .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
                line-height: 100%;
            }

            body {
                -webkit-text-size-adjust: none;
                -ms-text-size-adjust: none;
            }

            body, img, div, p, ul, li, span, strong, a {
                margin: 0;
                padding: 0;
            }

            table {
                border-spacing: 0;
            }

            table td {
                border-collapse: collapse;
            }

            p {
                margin: 0;
                padding: 0;
                margin-bottom: 0;
            }

            h1, h2, h3, h4, h5, h6 {
                line-height: 100%;
                margin: 0;
            }

            a {
                color: #333;
                text-decoration: none;
                outline: none;
            }

            a:link {
                text-decoration: none;
                outline: none;
            }
            a:visited {
                color: #333;
                text-decoration: none;
                outline: none;
            }
            a:focus {
/*                color: #333 !important;*/
                outline: none;
            }
            a:hover{ text-decoration: underline;}
           
            a[href^="tel"], a[href^="sms"] {
                text-decoration: none;
                color: #333;
            }
            .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                text-decoration: default;
                color: #333 !important;
                pointer-events: auto;
                cursor: default;
            }

            p {
                margin-top: 0;
                margin-bottom: 0;
            }
            img {
                display: block;
                border: none;
                outline: none;
                text-decoration: none;
            }

            table {
                border-collapse: collapse;
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
            }

            ol {
                margin: 0;
                padding: 0
            }
        </style>

    </head>

    <body style="background-color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #333; margin: 0;width:100% !important;" yahoo="fix" bgcolor="#ffffff">
        <div  class="page">

            <table width="500" border="0" cellspacing="0" cellpadding="0" align="center" class="wrapper_table" border="0" bgcolor="#f58220" style="background: #f58220;">
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="0" border="0" width="100%" bgcolor="#f58220" style="background:#f58220;">
                             <tr>
                                <td><img src="%BASEURL%images/email_images/banner2.png" alt=""></td>   
                             </tr>
                            <tr>
                                <td>
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%" bgcolor="#fde6d2" style="background:#fde6d2;">
                                        <tr>
                                            <td width="64" style="background: #f58220;"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></td>
                                            <td>
                                                <table cellspacing="0" cellpadding="0" border="0" width="325" align="center">
                                                   <tr><td height="10"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></tr>  
                                                   <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; line-height: 1; color: #58595b; font-weight: bold; text-align: center;">Hi  <span>%NAME%</span> </td></tr>
                                                   <tr><td height="22"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></tr>  
                                                   <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333;">Your reservation has been cancelled. If you did not take this action, please call us at 1800118200.! Here are your cancelled booking details. </td></tr>
                                                   <tr><td height="28"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></tr>  
                                                    <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333;"><strong style="font-weight: 600;">Booking Name : </strong> <span>%BOOKINGNAME%</span></td></tr>
                                                    <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333;"><strong style="font-weight: 600;">Number :  </strong> <span>%BOOKINGNUMBER%</span></td></tr>
                                                    <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333;"><strong style="font-weight: 600;">Booking ID :  </strong> <span>%BOOKINGID%</span></td></tr>
                                                    <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333;"><strong style="font-weight: 600;">When :  </strong> <span>%When%</span></td></tr>
                                                    <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333;"><strong style="font-weight: 600;">Where : </strong> <span>%Where%</span></td></tr>
                                                    <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333;"><strong style="font-weight: 600;">Guests : </strong> <span>%Guests%</span></td></tr>
                                                    <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333;"><strong style="font-weight: 600;">Offer Availed :  </strong> <span>%OfferAvailed%</span></td></tr>
                                                    <tr><td height="38"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></tr>  
                                                    <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333; font-weight: 600;">Cheers,</td></tr> 
                                                    <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #333; font-weight: 600;">Team foodine</td></tr>
                                                    <tr><td height="13"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></tr>
                                                </table>   
                                            </td>
                                            <td width="64" style="background: #f58220;"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></td>
                                        </tr> 
                                         <tr>
                                            <td width="64" style="background: #f58220;"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></td>
                                            <td style="background: #57585b">
                                                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                                    <tr>
                                                     <td width="27"><img src="%BASEURL%images/email_images/core-left.jpg" alt=""></td>
                                                     <td style="font-family: Arial, Helvetica, sans-serif; font-size: 8px; line-height: 20px; color: #fff;">For help, please email us at <a href="#" style="color: #fff;">hi@foodine.in</a> / Call : 9358393588</td>
                                                     <td width="25"><img src="%BASEURL%images/email_images/core-right.jpg" alt=""></td>
                                                    </tr>  
                                                </table>  
                                            </td>
                                            <td width="64" style="background: #f58220;"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></td>
                                         </tr>
                                    </table>  
                                </td>  
                            </tr>
                            <tr><td height="8"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></tr>         
                                <!-- <tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; line-height: 1; color: #333; text-align: center;">GET OUR APP</td></tr> 
                                <tr><td height="10"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></tr> -->         
                                <tr>
                                   <td> 
                                    <table width="195" border="0" cellspacing="0" cellpadding="0" align="center" class="wrapper_table" border="0">
                                        <tr>
                                            <td align="center"><a href="#"><img src="%BASEURL%images/email_images/app-store.png" alt=""></a></td> 
                                            <td align="center"><a href="#"><img src="%BASEURL%images/email_images/google-play.png" alt=""></a></td> 
                                        </tr>
                                        
                                        
                                    </table> 
                                    </td>
                                </tr> 
                                <tr><td height="10"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></tr>
                                <tr>
                                   <td> 
                                    <table width="130" border="0" cellspacing="0" cellpadding="0" align="center" class="wrapper_table" border="0">
                                        <tr>
                                            <td align="center"><a href="#"><img src="%BASEURL%images/email_images/fb.png" alt=""></a></td> 
                                            <td align="center"><a href="#"><img src="%BASEURL%images/email_images/tw.png" alt=""></a></td> 
                                            <td align="center"><a href="#"><img src="%BASEURL%images/email_images/pinterest.png" alt=""></a></td> 
                                        </tr>
                                        
                                        
                                    </table> 
                                    </td>
                                </tr>
                               <tr><td height="16"><img width="1" height="1" alt="" src="%BASEURL%images/email_images/blank.gif"></tr>             
                        </table>
                            
                    </td>  
                </tr>
                
            </table>

        </div>

    </body>
</html>