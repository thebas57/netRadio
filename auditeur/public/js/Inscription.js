$(document).ready(() => {
    // $('#inscription').on("submit",(event) => {
    //     event.preventDefault();
    // });
    function testerPassword(){
        return $('#password').val() === $('#password2').val();
    }

    function testerRemplissage(){
        if ($('#mail').val() !== "" && $('#login').val() !== "" && $('#password').val() !== "" ){
            console.log("Rempli ! ");
            if (testerPassword()) {
                $('#inscription').prop("disabled", false);
                $('#inscription').removeClass("disabled");

            }
        }
    }

    $(document).change(testerRemplissage);
});