<style>
    body{font-family: Calibri;}
    table{width: 100%; }
    table td{vertical-align: top; text-align: justify;}
    table a{text-decoration: none;}
    table.content td{border: solid 1px #AAA; padding: 5px;}
    table.content table.description td{border: none; padding: 2px;}
    table.content td.first{width: 20%;}
    table.content td.second{width: 80%;}
    table.content td.third{width: 30%; }

    table tr.header td.logo{width: 70%;}
    table tr.header td.logo span{font-size: 15px;margin-top: 5px;}
    table tr.header td.addr{width: 30%;}
    span{font-size: 12px;}
    span.date{margin-top: 10px;font-weight: bold;font-size: 13px;}
    span.big{font-size: 14px;}
    span.small{font-size: 10px;}
    span.email a{color: #999 !important;}
    span.notes{color: #555;font-size: 12px;}
    span.strong{font-size: 13px; font-weight: bold; }
    td.greyback{background: #EEE;}
</style>
<page>
    <table>
        <tr class="header">
            <td class="logo">
                <table>
                    <tr>
                        <td>
                            <img src="%HEADER_LOGO%" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="date">Date: %PICKUP_DATE%</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="addr">
                <table>
                    <tr>
                        <td>
                            <span class="big">%CUSTOMER_NAME%</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="email small"><a href="mailto:%CUSTOMER_EMAIL%">%CUSTOMER_EMAIL%</a></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="small">%CUSTOMER_LOCATION%</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="small">%CUSTOMER_ADDRESS%</span>
                            <br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="%CUSTOMER_SIGN%" width="125"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td>
                <h2>Work Order: #%WORK_ORDER_NUMBER%</h2>
                <h4>Pickup Type: %PICKUP_TYPE%</h4>
            </td>
        </tr>
        <tr>
            <td>
                <table class="content">
                    <tr>
                        <td class="first greyback" align="center" ><span class="strong">Item No.</span></td>
                        <td class="second greyback"><span class="strong">Description</span></td>
                    </tr>
                    %PICKUP_DETAIL_LIST%
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td>
                            <span class="notes">Additional Notes: %ADDITIONAL_NOTES%</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</page>