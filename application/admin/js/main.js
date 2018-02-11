$(document).ready(function() {
    $("#passchange").click(function(a) {
        $(".passtohide").toggle("clip", 500)
    });
    $("#alert").mouseover(function(a) {
        $(this).effect("fade", 1000)
    });
    $("#datatable tbody").click(function(a) {
        if ($(a.target.parentNode).hasClass("row_selected")) {
            $(a.target.parentNode).removeClass("row_selected")
        } else {
            $(a.target.parentNode).addClass("row_selected")
        }
    });
    $(".reload").click(function(a) {
        oTable.fnDraw(false)
    });
    $("#ToolTables_datatable_6").click(function() {
        alert();
        var a = $("#datatable");
        a.children().each(function() {
            var b = $(this)
        })
    });
      setInterval(function () {
        var uper_li = '';
        var notifyId = $('#lastNotifyId').length > 0 ? $('#lastNotifyId').val() : 0;
        //alert(notifyId);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                id: notifyId
            },
            url: BASEURL + 'notification/newlist',
            success: function (resp) {
                if (resp.STATUS == 200) {
                    var rec = resp.RECORD;
                    //alert(rec.length);
                    var li = '';
                    var finalNotifyId = 0;
                    for (var i = 0; i < rec.length; i++) {
                        
                        finalNotifyId = rec[i]['notifyId'];
                        
                        var messageValue = '<i>' + rec[i]['restaurantName'] + '</i>';
                        //messageValue += ' ' + rec[i]['tableString'];
                       // messageValue += ' <i>' + rec[i]['recordField'] + '</i>';
                        //messageValue += ' ' + rec[i]['fromValue'] + '.';

                        li += '<div class="notify-container background-fff">';
                        li += '<div class="notify-message">' + messageValue + '<div>';
                        li += '<i class="fa fa-clock-o"></i>' + rec[i]['notifyDate'] + '</div></div>';
                        li += '<hr class="margin0auto"/>';
                        li += '<div class="notify-action margin-top-10">';
                        if (rec[i]['notifyAction'] == 'pending') {
                            li += ' <button class="btn btn-warning btn-xs notify-btn" data-type="yes" data-id="' + rec[i]['recordId'] + '" data-target="' + rec[i]['notifyId'] + '" data-action="' + rec[i]['activityId'] + '"><i class="fa fa-check"></i> Accept</button> ';
                            li += ' <button class="btn btn-danger btn-xs notify-btn" data-type="no" data-target="' + rec[i]['notifyId'] + '" data-id="' + rec[i]['recordId'] + '" data-action="' + rec[i]['activityId'] + '"><i class="fa fa-times"></i> Decline</button> ';
                        } else {
                            li += 'You have ' + (rec[i]['notifyAction'] == 'yes' ? 'accept' : 'decline') + ' this request.';
                        }
                        li += '</div>';
                        li += '</div>';
                        li += '</div>';
                        li += '</div>';


                        if (i <= 4) {
                            uper_li += '<li class="notification-li">';
                            uper_li += '<a href="' + BASEURL + 'booking" >';
                            uper_li += '<span class="label label-success">';
                            uper_li += '<i class="fa fa-user"></i>';
                            uper_li += '</span>';
                            uper_li += '<span class="body">';
                            uper_li += '<span class="message">' + messageValue + '</span>';
                            uper_li += '<span class="time">';
                            uper_li += '<i class="fa fa-clock-o"></i>';
                            uper_li += '<span>' + rec[i]['notifyDate'] + '</span>';
                            uper_li += '</span>';
                            uper_li += '</span>';
                            uper_li += '</a>';
                            uper_li += '</li>';
                        }
                        
                    }
                    $('#lastNotifyId').val(finalNotifyId);
                    $('.dropdown-title').after(uper_li);
                    $('.no-notification').remove();
                    $('.badge').html(rec.length);
                    if(rec.length) {
                        playSound('bing');
                        playSound('bing');
                    }
                    $('#badge-count').html('<i class="fa fa-bell"></i> ' + rec.length + ' Notifications');
                    $('.notify-container:first').before(li);

                    var foundRec = 0;
                    $('.notification').children().each(function () {
                        var $li = $(this);
                        if ($li.hasClass('notification-li')) {
                            foundRec++;
                            if (foundRec > 5) {
                                $li.remove();
                            }
                        }
                    });
                }
            }
        });
    }, 10000);
});
var oTable;

function validateRemove(d, a) {
    $confirm = confirm("Are you sure you want to delete this?");
    if ($confirm == true) {
        var b = [];
        b.push(d);
        var c = {
            rows: b
        };
        $.ajax({
            url: BASEURL + a,
            type: "POST",
            data: c,
            success: function(e) {
                if (e == "1") {
                    $("#" + d).remove()
                } else {
                    $("#divtoappend").append('<div id="alert"><div class="alert alert-danger center">Please try after some time</div></div>')
                }
            }
        })
    } else {
        return false
    }
}

function deleteerows(b) {
    $confirm = confirm("Are you sure you want to delete this?");
    if ($confirm == true) {
        var c = [];
        var a = fnGetSelected(oTable);
        $.each(a, function(e, f) {
            c.push($(f).attr("id"))
        });
        var d = {
            rows: c
        };
        $.ajax({
            url: BASEURL + b,
            type: "POST",
            data: d,
            success: function(e) {
                if (e == "1") {
                    $(a).remove()
                } else {
                    $("#divtoappend").append('<div class="alert alert-block alert-danger fade in col-sm-12 borderradius0">Please try after some time<a class="close" data-dismiss="alert" href="#" aria-hidden="true">×</a></div>')
                }
            }
        })
    } else {
        return false
    }
}

function fnGetSelected(a) {
    return a.$("tr.row_selected")
}

function getdatatable(g, k, d, l, c, i, f) {
    var j = 0;
    var e = "asc";
    if (typeof c === "undefined") {
        j = 0;
        e = "asc"
    } else {
        j = c;
        e = i
    }
    var h = {};
    if (g !== "") {
        h = {
            sExtends: "text",
            sButtonText: "Delete",
            fnClick: function(m, n, o) {
                deleteerows(g)
            }
        }
    } else {
        h = {
            sExtends: "text",
            sButtonText: "Delete",
            fnClick: function(m, n, o) {
                alert("You are not allow to delete..!!")
            }
        }
    }
    if (typeof f === "undefined") {
        f = "datatable"
    }
    oTable = $("#" + f).dataTable({
        sPaginationType: "full_numbers",
        bJQueryUI: true,
        sDom: "<'row'<'dataTables_header  clearfix'<'col-md-4'lC><'col-md-8'TRf>r>>t<'row'<'dataTables_footer clearfix'<'col-md-6'i><'col-md-6'p>>> ",
        bStateSave: true,
        oTableTools: {
            sRowSelect: "multi",
            aButtons: [{
                sExtends: "copy",
                sButtonText: "COPY",
                mColumns: "visible"
            }, {
                sExtends: "print",
                sButtonText: "PRINT",
                mColumns: "visible"
            }, {
                sExtends: "csv",
                sButtonText: "CSV",
                mColumns: "visible"
            }, {
                sExtends: "xls",
                sButtonText: "XLS",
                mColumns: "visible",
                sFileName: "*.xls"
            }, {
                sExtends: "pdf",
                sButtonText: "PDF",
                mColumns: "visible"
            }, h, {
                sExtends: "select_all",
                sButtonText: "Select All",
                mColumns: "visible"
            }, "select_none"],
            sSwfPath: BASEURL + "js/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
        },
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: BASEURL + k,
        sServerMethod: "POST",
        aoColumns: d,
        aoColumnDefs: l,
        oLanguage: {
            sSearch: "Search:"
        },
        bSortCellsTop: true,
        aaSorting: [
            [0, "asc"]
        ]
    });
    var b = $("#datatable_wrapper").find(".dataTables_header");
    b.parent().switchClass("row", "container");
    var a = $("#datatable_wrapper").find(".dataTables_footer");
    a.parent().switchClass("row", "container");
    $("#" + f).css("width", "100%");
    setTimeout(function() {
        oTable.fnGetData().length === 0 ? $("#" + f).css("width", "100%") : ""
    }, 500)
}

function updateStausWithTime(e, c, a) {
    var d = '<h4><label class="control-label" for="waitTime">Select Wait Time</label></h4>';
    d += '<select id="waitTime" class="form-control">';
    for (var b = 0; b < 60; b = b + 5) {
        d += '<option value="' + b + '">' + b + "</option>"
    }
    d += "</select>";
    bootbox.dialog({
        message: d,
        title: "",
        buttons: {
            success: {
                label: "Ok",
                className: "btn-success",
                callback: function() {
                    var g = [];
                    g.push(e);
                    var f = $("#waitTime").val();
                    var h = {
                        rows: g,
                        waitTime: f
                    };
                    $.ajax({
                        url: BASEURL + c,
                        type: "POST",
                        data: h,
                        success: function(i) {
                            $("#atag" + e).html('<i class="fa fa-times-circle-o "></i> Rejected').switchClass("btn-warning", "btn-inverse");
                            $("#wtag" + e).html('<i class="fa fa-clock-o "></i> ' + f + " min to wait").switchClass("btn-sm", "btn-xs").addClass("btn-disabled").attr("disabled", "disabled");
                            bootbox.hideAll()
                        }
                    })
                }
            },
            danger: {
                label: "Cancel",
                className: "btn-danger",
                callback: function() {}
            }
        }
    })
}

function changeStatus(f, c, b) {
    if (confirm("Are you sure you want to change the status?")) {
        var a = true;
        if (typeof(b) !== "undefined") {
            a = false
        }
        var d = [];
        d.push(f);
        var e = {
            rows: d
        };
        $.ajax({
            url: BASEURL + c,
            type: "POST",
            data: e,
            success: function(g) {
                if (g === "1") {
                    if (a === true) {
                        if ($("#atag" + f).hasClass("btn-success")) {
                            $("#atag" + f).html('<i class="fa fa-times-circle-o "></i> Inactive');
                            $("#atag" + f).switchClass("btn-success", "btn-inverse")
                        } else {
                            if ($("#atag" + f).hasClass("btn-inverse")) {
                                $("#atag" + f).html('<i class="fa fa-check-circle-o "></i> Active');
                                $("#atag" + f).switchClass("btn-inverse", "btn-success")
                            }
                        }
                    } else {
                        if ($("#atag" + f).hasClass("btn-success")) {
                            $("#atag" + f).html('<i class="fa fa-crosshairs"></i> Rejected');
                            $("#atag" + f).switchClass("btn-success", "btn-inverse");
                            $("#wtag" + f).html('<i class="fa fa-clock-o "></i> No Wait').switchClass("btn-sm", "btn-xs").addClass("btn-disabled").attr("disabled", "disabled")
                        } else {
                            if ($("#atag" + f).hasClass("btn-warning")) {
                                $("#atag" + f).html('<i class="fa fa-check-circle-o "></i> Accepted');
                                $("#atag" + f).switchClass("btn-warning", "btn-success");
                                $("#wtag" + f).html('<i class="fa fa-clock-o "></i> No Wait').switchClass("btn-sm", "btn-xs").addClass("btn-disabled").attr("disabled", "disabled")
                            } else {
                                if ($("#atag" + f).hasClass("btn-inverse")) {
                                    $("#atag" + f).html('<i class="fa fa-check-circle-o "></i> Accepted');
                                    $("#atag" + f).switchClass("btn-inverse", "btn-success");
                                    $("#wtag" + f).html('<i class="fa fa-clock-o "></i> No Wait').switchClass("btn-sm", "btn-xs").addClass("btn-disabled").attr("disabled", "disabled")
                                }
                            }
                        }
                    }
                } else {
                    $("#divtoappend").append('<div id="alert"><div class="alert alert-danger center">Please try after some time</div></div>')
                }
            }
        })
    } else {
        return false
    }
}

function changeActiveDeactive(g, d, c) {
    var b = true;
    if (typeof(c) !== "undefined") {
        b = false
    }
    var e = [];
    e.push(g);
    var f = {
        rows: e,
        recfor: c
    };
    var a = false;
    bootbox.dialog({
        message: "Do you want to <strong>" + (c == 1 ? "Accept" : "Reject") + "</strong> this reward?",
        title: "",
        buttons: {
            success: {
                label: "Yes",
                className: "btn-success",
                callback: function() {
                    $.ajax({
                        url: BASEURL + d,
                        type: "POST",
                        beforeSend: function() {
                            $(".bootbox-body").html('<div class="text-center"><img src="' + BASEURL + 'img/loading.GIF"></div>');
                            $(".modal-footer").remove()
                        },
                        data: f,
                        success: function(h) {
                            if (h !== "0") {
                                $("#aceept-reject-" + g).html("<strong>" + h + "ed </strong>")
                            } else {
                                $("#divtoappend").append('<div id="alert"><div class="alert alert-danger center">Please try after some time</div></div>')
                            }
                            bootbox.hideAll()
                        }
                    });
                    return false
                }
            },
            danger: {
                label: "No",
                className: "btn-danger",
                callback: function() {}
            }
        }
    });
    return false
}

function onlyNumeric(a) {
    if ($.inArray(a.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 || (a.keyCode == 65 && a.ctrlKey === true) || (a.keyCode >= 35 && a.keyCode <= 40)) {
        return
    }
    if ((a.shiftKey || (a.keyCode < 48 || a.keyCode > 57)) && (a.keyCode < 96 || a.keyCode > 105)) {
        a.preventDefault()
    }
};


function playSound(filename){  
    //filename = 'http://localhost/foodine/assets/js/'+filename;
    filename = 'http://foodine.in/application/images/'+filename;
    document.getElementById("sound").innerHTML='<audio autoplay="autoplay"><source src="' + filename + '.mp3" type="audio/mpeg" /><source src="' + filename + '.ogg" type="audio/ogg" /><embed hidden="true" autostart="true" loop="false" src="' + filename +'.mp3" /></audio>';
}
