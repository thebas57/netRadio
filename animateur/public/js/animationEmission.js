$(document).ready(() => {

    $("#emissionAudacity").change(() => {
        if($("#emissionAudacity").get(0).files.length > 0)
            $("#validerImport").prop("disabled", false);
        else
            $("#validerImport").prop("disabled", true);
    });

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia){
        navigator.mediaDevices.getUserMedia (
            // constraints - only audio needed
            {audio: true, video: false})
            .then(function(stream) {

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// initialisation ///////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                let onAir = false;
                let recordDisable = false;
                let stopDisable = true;
                let storage = localStorage;
                let stockageEnregistrements = [];
                let recorder = new MediaRecorder(stream);
                let musiques = [];
                $("#finEmission").hide();


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// Fonctions //////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


                function afficherMusiques(musiques) {
                    $("#musiquesList").html("");

                    musiques.forEach((element, index) => {

                        let enregistrement = document.createElement("article");
                        let labelEnregistrement = document.createElement("a");
                        let deleteEnregistrement = document.createElement("a");
                        let audio = document.createElement("audio");

                        labelEnregistrement.setAttribute("href", "#");
                        deleteEnregistrement.setAttribute("href", "#");

                        audio.setAttribute("controls", true);
                        audio.setAttribute("id", "audio" + index);
                        audio.setAttribute("hidden", true);

                        labelEnregistrement.innerHTML = element.name;
                        deleteEnregistrement.innerHTML = "X";

                        labelEnregistrement.setAttribute("data-id", index);
                        deleteEnregistrement.setAttribute("data-id", index);

                        labelEnregistrement.classList.add("musique");
                        deleteEnregistrement.classList.add("suppMusique");

                        enregistrement.appendChild(labelEnregistrement);
                        enregistrement.appendChild(deleteEnregistrement);
                        enregistrement.appendChild(audio);
                        audio.src = window.URL.createObjectURL(element);

                        let li = document.createElement("li");
                        li.appendChild(enregistrement);

                        document.getElementById("musiquesList").appendChild(li)
                    });
                }

                ///fin musiques ///

                async function arreterEnregistrement() {
                    return new Promise(((resolve, reject) => {
                        onAir = false;
                        $("#onAir h3").removeClass("red");
                        let dataRecord = [];
                        //enregistrement de l'audio
                        recorder.ondataavailable = function (element){
                            dataRecord.push(element.data);
                            console.log("Data record : ",element.data);
                            let emission_id = $("#emission_id").val();
                            let datas = new FormData();
                            datas.append("emission_id", emission_id);
                            datas.append("audio",element.data);
                            datas.append("type", "enregistrement");
                            let route = $("#route").val();
                            fetch(route + "/emission/receiveAudio", {
                                method: "POST",
                                body: datas
                            }).then((res) => {
                                res.json().then((res) => {
                                    resolve(res);
                                })
                                // window.location.href = route + "/animateur";
                            }).catch((err) => reject(err))

                        };
                    }));

                }

                async function ajouterMusique(id) {
                    console.log(musiques);
                    return new Promise((resolve, reject) => {
                        onAir = false;
                        $("#onAir h3").removeClass("red");
                        //enregistrement de l'audio
                        let emission_id = $("#emission_id").val();
                        let datas = new FormData();
                        datas.append("emission_id", emission_id);
                        datas.append("audio",new Blob([musiques[id]],{"type": "audio/mpeg; codecs=opus"}));
                        datas.append("type", "musique");
                        let route = $("#route").val();
                        fetch(route + "/emission/receiveAudio", {
                            method: "POST",
                            body: datas
                        }).then((res) => {
                            res.json().then((res) => {
                               resolve(res);
                            })
                        }).catch((err) => reject(err))
                    });

                    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// fin des fonctions ///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


                $("#songs").change(() => {
                    let fichier = document.querySelector("#songs");

                    for(let i = 0; i < fichier.files.length; i++){
                        musiques.push(fichier.files[i]);
                    }

                    afficherMusiques(musiques);
                });

                $("#musiquesList").on("click", ".musique", async (evenement) => {
                    let id = evenement.currentTarget.getAttribute("data-id");

                    let audios = document.getElementsByClassName("musique");
                    for(let i = 0; i < audios.length; i++){
                        let id_audio = audios[i].getAttribute("data-id");
                        let tmp = document.getElementById("audio" + id_audio);
                        tmp.muted = true;
                    }

                    let lecteur = document.getElementById("audio" + id);
                    lecteur.muted = false;
                    $("#timer").html(lecteur.duration);
                    $("#onAir h3").removeClass("red");
                    recorder.stop();
                    let tmp_res = await arreterEnregistrement();
                    console.log("res ", tmp_res);
                    lecteur.play();

                    $("#finEmission").prop("disabled", true);

                    lecteur.addEventListener("timeupdate", async (event) => {
                        let time = ((Math.floor(lecteur.duration) - Math.floor(lecteur.currentTime)));

                        let min = ((Math.floor(time / 60)));
                        let sec = ((time % 60));

                        $("#timer").html(min + " min " + sec);
                    });

                    lecteur.onended = async function () {
                        let res = await ajouterMusique(id);
                        console.log("envoyé : ", res);
                        recorder.start();
                        $("#onAir h3").addClass("red");
                        $("#finEmission").show();
                        $("#lancerEnregistrement").hide();
                        $("#finEmission").prop("disabled", false);
                    };
                });

                $("#musiquesList").on("click", ".suppMusique", (evenement) => {
                    let id = evenement.currentTarget.getAttribute("data-id");

                    musiques.splice(id, 1);

                    afficherMusiques(musiques);
                });

                $("#lancerEnregistrement").click((e) => {
                    e.preventDefault();

                    recorder.start();

                    $("#onAir h3").addClass("red");
                    $("#finEmission").show();
                    $("#lancerEnregistrement").hide();
                });

                $("#finEmission").click(async () => {
                    $("#onAir h3").removeClass("red");

                    //on stoppe l'enregistrement
                    recorder.stop();
                    await arreterEnregistrement();

                    $("#finEmission").hide();
                    $("#lancerEnregistrement").show();

                    console.log("fin emission");

                });

            }).catch(function(err) {
            $.notify(err, "error");
        });
    }else{
        $.notify("Le navigateur n'est pas compatible", "error");
    }


});
