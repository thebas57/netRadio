$(document).ready(() => {

    $('.supprEmission').click(function () {
        fetch('supprEmission/' + $(this).attr("data-id")).then(() => {
           $($(this).parent().parent()).remove(); 
        })
    });

});