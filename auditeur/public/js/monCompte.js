$(document).ready(() => {

    $('.btn-confirm').click(() => {
        let val = $('#newLogin').val();
        if(val === "")
        {
            $.notify("Le login doit être renseigné", "error");
            console.log(val);
        }

        console.log(val);
    })

    $.notify("test");

});