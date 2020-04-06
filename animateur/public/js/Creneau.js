$(document).ready(() => {

    $('.supprCreneau').click(function () {
        fetch('supprCreneau/' + $(this).attr("data-id")).then(() => {
            $($(this).parent().parent().parent()).remove();
        })
    });

    let url = $(location).attr('href')
    let hostname = $('<a>').prop('href', url).prop('hostname');

    $('#ajd').click(function () {
        let date = $('#ajd').val();
        location.href="/netRadio/animateur/aujourdhui";
    })

    $('#demain').click(function () {
        location.href="/netRadio/animateur/demain";
    })
});