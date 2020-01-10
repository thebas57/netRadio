$(document).ready(() => {

    $(document).on('click', '.btn-confirm', function() {
        let val = $('#newLogin').val();
        if(val === "")
        {
            $.notify("Le login doit être renseigné", "warn");
            console.log(val);
        }
        else 
        {
            fetch("monCompte",
            {
                headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({id: $(this).attr("data-id"), login: val})
            })
            .then(           
                 function(res){$.notify("Le login a été mis à jour", "success")}
            )
            .catch(
                function(res){$.notify("Erreur lors de la modification du login", "error")}
                )
            
        }
    })

});