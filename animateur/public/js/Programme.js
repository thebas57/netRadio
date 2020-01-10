$(document).ready(() => {

    $('.supprProgramme').click(function () {
        fetch('supprProgramme/' + $(this).attr("data-id")).then(() => {
           $($(this).parent().parent()).remove(); 
        }).catch(() => { alert("aie"); })
    });

});