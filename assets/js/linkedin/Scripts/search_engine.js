$(document).ready(function () {
    $("#search").click(function () {
        var email = $('#search-content_email').val();
        var password = $('#search-content_password').val();
        var content_general = $('#search-content_about').val();
        var content_employ = $('#search-content_employment').val();
        var content_edu = $('#search-content_education').val();
        var content_endor = $('#search-content_endorsements').val();

        var dataValue = {
            general: content_general,
            edu: content_edu,
            empl: content_employ,
            endor: content_endor,
            mail: email,
            password: password,
            code: ''
        };

        $('#table1').empty();
        $('#loading-label').html('Fetching profiles...');
        $('#loading-label2').show();
        $('#loading').show();
        $.ajax({
            type: "POST",
            url: "Main.aspx/OnSearch",
            data: JSON.stringify(dataValue),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#loading').hide();
                $('#loading-label2').hide();
                alert("Request: " + XMLHttpRequest.toString() + "\n\nStatus: " + textStatus + "\n\nError: " + errorThrown);
            },
            success: function (result) {
                console.log(JSON.stringify(result));
                $('#loading').hide();
                $('#loading-label2').hide();
                var res = JSON.parse(result.d);
                if (res.success) {
                    var profiles = JSON.parse(res.data);
                    for (var profile of profiles) {
                        $('#table1').append(`
                            <tr>
                                <td class="name">${profile.name || ""}</td>
                                <td class="employer">${profile.curEmployer || ""}</td>
                                <td class="position">${profile.position || ""}</td>
                                <td class="date">${profile.employmentDate || ""}</td>
                                <td class="school">${profile.school || ""}</td>
                                <td class="link"><a href="${profile.link || "#"}"><img src="img/linkedin-blk.svg" /></a></td>
                            </tr>`
                        );
                    }
                } else if (res.verify) {
                    if (res.message.length > 0) {
                        alert(res.message);
                    }
                    $('#verify-email').html(res.email);
                    $('#verify-modal').show();
                    $('#verify-code').focus();
                } else {
                    if (res.message.length > 0) {
                        alert(res.message);
                    }
                }                
            }
        });
    });

    $("#verify-submit").click(function () {
        var email = $('#search-content_email').val();
        var content_general = $('#search-content_about').val();
        var content_employ = $('#search-content_employment').val();
        var content_edu = $('#search-content_education').val();
        var content_endor = $('#search-content_endorsements').val();
        var code = $('#verify-code').val();

        if (code.length == 0) {
            alert("Please input code.");
            return;
        }

        $("#verify-modal").hide();
        $("#verify-code").val('');

        var dataValue = {
            general: content_general,
            edu: content_edu,
            empl: content_employ,
            endor: content_endor,
            mail: email,
            password: '',
            code: code
        };

        $('#loading-label').html('Fetching profiles...');
        $('#loading-label2').show();
        $('#loading').show();
        $.ajax({
            type: "POST",
            url: "Main.aspx/OnSearch",
            data: JSON.stringify(dataValue),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#loading').hide();
                $('#loading-label2').hide();
                alert("Request: " + XMLHttpRequest.toString() + "\n\nStatus: " + textStatus + "\n\nError: " + errorThrown);
            },
            success: function (result) {
                $('#loading').hide();
                $('#loading-label2').hide();
                var res = JSON.parse(result.d);
                if (res.success) {
                    var profiles = JSON.parse(res.data);
                    for (var profile of profiles) {
                        $('#table1').append(`
                            <tr>
                                <td class="name">${profile.name || ""}</td>
                                <td class="employer">${profile.curEmployer || ""}</td>
                                <td class="position">${profile.position || ""}</td>
                                <td class="date">${profile.employmentDate || ""}</td>
                                <td class="school">${profile.school || ""}</td>
                                <td class="link"><a href="${profile.link || "#"}"><img src="img/linkedin-blk.svg" /></a></td>
                            </tr>`
                        );
                    }
                } else if (res.verify) {
                    if (res.message.length > 0) {
                        alert(res.message);
                    }
                    $('#verify-email').html(res.email);
                    $('#verify-modal').show();
                    $('#verify-code').focus();
                } else {
                    if (res.message.length > 0) {
                        alert(res.message);
                    }
                }
            }
        });
    });

    $("#verify-cancel").click(function () {
        $('#verify-modal').hide();
    });
});