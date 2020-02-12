$(document).ready(() => {

    $(document).on('click', '.btn-confirm', function() {
        let val = $('#newLogin').val();
        let myId = $(this).data("id");
        
        if(val === "")
        {
            $.notify("Le login doit être renseigné", "warn");
            console.log(val);
        }
        else 
        {

            datas = new FormData();
            datas.append("id",myId);
            datas.append("login",val);
            fetch("monCompte",
            {
                headers: {
                'Accept': 'application/json',
                // 'Content-Type': 'application/json'
                },
                method: "POST",
                body: datas,
            })
            .then(           
                 function(res){
                     $.notify("Le login a été mis à jour", "success");
                    document.location.href=myId;

                    res.json().then((res) => {
                        console.log(res);
                        
                    })
                    
                    }
            )
            .catch(
                function(res){$.notify("Erreur lors de la modification du login", "error")}
                )
            
        }
    })

});