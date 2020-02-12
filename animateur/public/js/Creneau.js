$(document).ready(() => {

    $('.supprCreneau').click(function () {
        fetch('supprCreneau/' + $(this).attr("data-id")).then(() => {
           $($(this).parent().parent().parent()).remove(); 
        })
    });

});