$(document).on('click', '.sort_wrap', function () {
    var table = $('#table');
    var tbody = $('#table1');

    tbody.find('tr').sort(function (a, b) {
        if ($('#profiles_order').val() != 'desc') {
            if ($('#sort').val() == 'name')
                return $('.name', a).text().localeCompare($('.name', b).text());
            if ($('#sort').val() == 'company')
                return $('.info_company', a).text().localeCompare($('.info_company', b).text());
            if ($('#sort').val() == 'update')
                return $('.update_sort', a).val() - $('.update_sort', b).val();
            if ($('#sort').val() == 'title')
                return $('.info_position', a).text().localeCompare($('.info_position', b).text());
            if ($('#sort').val() == 'employer')
                return $('.info_company', a).text().localeCompare($('.info_company', b).text());
        }
        else {
            if ($('#sort').val() == 'name')
                return $('.name', b).text().localeCompare($('.name', a).text());
            if ($('#sort').val() == 'company')
                return $('.info_company', b).text().localeCompare($('.info_company', a).text());
            if ($('#sort').val() == 'update')
                return $('.update_sort', b).val() - $('.update_sort', a).val();
            if ($('#sort').val() == 'title')
                return $('.info_position', b).text().localeCompare($('.info_position', a).text());
            if ($('#sort').val() == 'employer')
                return $('.info_company', b).text().localeCompare($('.info_company', a).text());
        }

    }).appendTo(tbody);

    var sort_order = $('#profiles_order').val();
    if (sort_order != "desc") {
        $("#sort_icon").css({ 'transform': 'rotate(' + 180 + 'deg)' });
        $('#profiles_order').val("desc");
        if ($("#sort").val() == "update")
            $("#sort option:contains('Update')").text("Update (newest last)")
    }
    else {
        $("#sort_icon").css({ 'transform': 'rotate(' + 0 + 'deg)' });
        $('#profiles_order').val("asc");
        if ($("#sort").val() == "update")
            $("#sort option:contains('Update')").text("Update (newest first)")
    }
});

$(document).on('click', '#search_btn', function () {
    var search_key = $(".search_field").val().toLowerCase();
    if (search_key != "") {
        $(".close_search_btn").css("display", "block");
        $("#table1 tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(search_key) > -1)
        });
    }
});

$(document).on('click', '.close_search_btn', function () {
    $(".search_field").val("");

    $(this).css("display", "none");

    $("#table1 tr").filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf("") > -1)
    });
});

$(document).on('keypress', '.search_field', function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        $("#search_btn").trigger("click");
        return false;
    }
});

$(document).ready(function () {
    $(".form-control").click(function () {
        $(this).parents().addClass("focused");
    });
});


function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("form-control");
    for (var i = 0; i < x.length; i++) {
        if (elmnt != x[i]) {
            x[i].parentNode.classList.remove("focused");
        }
    }
}
/*execute a function when someone clicks in the document:*/
document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});

function chevronUpDownToggle(chevron) {
    if (chevron.classList.contains("fa-chevron-down")) {
        chevron.classList.remove("fa-chevron-down");
        chevron.classList.add("fa-chevron-up");
    } else if (chevron.classList.contains("fa-chevron-up")) {
        chevron.classList.remove("fa-chevron-up");
        chevron.classList.add("fa-chevron-down");
    }
}

// sort
$(document).on('change', '#sort', function () {
    $('.sort_wrap').click();
})