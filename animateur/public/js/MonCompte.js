$(() => {
    let root = $("#root").val();
    $('#loginDiv').on('click', '#modif-login', function (evt) {
        let login = $("#login").val(), ancienLogin = $("#login").attr("placeholder");
        if (!(login === ancienLogin) && (login !== "")) {
            let datas = new FormData();
            datas.append("login", $("#login").val());
            $.ajax({
                url: root + "/monCompte",
                type: "POST",
                data: datas,
                processData: false,
                contentType: false,
                success: function (data) {
                    data = JSON.parse(data)
                    $.notify(data.error, "success");
                    $("#login").attr("placeholder", login);
                    $("#login").val("");
                },
                error: function (data) {
                    data = JSON.parse(data);
                    $.notify(data.error, "error");
                }
            })
        } else {
            $(this).notify("Rien à modifier", {position: "right", className: "warn"});
        }
    });
    $('#passwordDiv').on('click', '#modif-password', function () {
        //Gérer modif password
        let datas = new FormData();
        if ($("#password").val() !== $("#passwordVerif").val()) {
            $(this).notify("Les mots de passe ne correspondent pas.", {position: "right", className: "error"});
        } else {
            datas.append("password", $("#password").val());
            $.ajax({
                url: root + "/monCompte",
                type: "POST",
                data: datas,
                processData: false,
                contentType: false,
                success: function (data) {
                    data = JSON.parse(data);
                    if (data.status === 400) {
                        $.notify(data.error, "error");
                        $("#passwordVerif").val("");
                        $("#password").val("");
                        $("#passwordVerif").hide(500);
                    } else {
                        $.notify(data.error, "success");
                        $("#passwordVerif").val("");
                        $("#password").val("");
                        $("#passwordVerif").hide(500);
                    }
                },
                error: function (data) {
                    data = JSON.parse(data);
                    $("#modif-password").notify("" + data.error, {position: "right", className: "error"});
                    $("#passwordVerif").val("");
                    $("#password").val("");
                    $("#passwordVerif").hide(500);

                }
            })
        }
    });

    $("#passwordDiv").on("keyup", '#password', function () {
        $('#passwordVerif').show(1000);
    });
});