$(document).ready(() => {

    $('.supprProgramme').click(function () {
        if (confirm("Attention, si vous supprimez ce programme, toutes les émissions associées seront aussi supprimées")) {
            fetch('supprProgramme/' + $(this).attr("data-id")).then(() => {
                $($(this).parent().parent()).remove();
            }).catch(() => { alert("aie"); })
        }
        else {

        }
    });
});