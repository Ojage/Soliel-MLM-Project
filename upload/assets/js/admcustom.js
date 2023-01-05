"use strict";

$(document).ready(function () {
    load_notitoast();
    setInterval(function () {
        load_notitoast();
    }, 90000);

});

function load_notitoast() {
    $.ajax({
        url: "dtfetch.php",
        method: "POST",
        dataType: "json",
        success: function (data) {
            if (data.notitoaststr !== '') {
                var strdat = data.notitoaststr;
                var resarr = strdat.split("|");
                var index;
                for (index = 0; index < resarr.length; ++index) {
                    var subitem = resarr[index];
                    var valarr = subitem.split(":");
                    notifytoast(valarr[0], valarr[1], valarr[2]);
                }
                //$('.notitoastcnt').html(data.notitoaststr);
            }
        }
    });
}