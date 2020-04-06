$(document).ready(() => {
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia){
        navigator.mediaDevices.getUserMedia (
            // constraints - only audio needed
            {audio: true})
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

                function arreterEnregistrement() {
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
                        datas.append("audio",new Blob([element.data],{"type": "audio/mpeg;codecs=opus"}));
                        console.log(datas);
                        let route = $("#route").val();
                        fetch(route + "/emission/receiveAudio", {
                            method: "POST",
                            body: datas
                        }).then((res) => {
                            res.json().then((res) => {
                                console.log(res);
                            })
                            // window.location.href = route + "/animateur";

                        })

                    };
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

                // $("#musiquesList").on("click", ".musique", (evenement) => {
                //     // $("#finEmission").prop("disabled", true);
                //     let id = evenement.currentTarget.getAttribute("data-id");
                //
                //     let audios = document.getElementsByClassName("musique");
                //     for(let i = 0; i < audios.length; i++){
                //         let id_audio = audios[i].getAttribute("data-id");
                //         let tmp = document.getElementById("audio" + id_audio);
                //         tmp.muted = true;
                //     }
                //
                //     let lecteur = document.getElementById("audio" + id);
                //     lecteur.muted = false;
                //     lecteur.play();
                //     $("#timer").html(lecteur.duration);
                //     $("#onAir h3").removeClass("red");
                //     // pistesEmission.push(musiques[id]);
                //
                //     lecteur.addEventListener("timeupdate", (event) => {
                //         let time = ((Math.floor(lecteur.duration) - Math.floor(lecteur.currentTime)));
                //
                //         let min = ((Math.floor(time / 60)));
                //         let sec = ((time % 60));
                //
                //         $("#timer").html(min + " min " + sec);
                //     });
                //
                //     lecteur.onended = function () {
                //         $("#onAir h3").addClass("red");
                //         recorder = lancerRecordMicro(recorder);
                //         $("#finEmission").prop("disabled", false);
                //     };
                //
                //     let retour = arreterEnregistrement(pistesEmission, recorder, dataRecord);
                //     pistesEmission = retour.pisteEmission;
                //     dataRecord = retour.dataRecord;
                //     recorder = retour.recorder;
                //     // changerDureeEmission(pistesEmission);
                //
                //     $("#finEmission").prop("disabled", true);
                // });
                //
                // $("#musiquesList").on("click", ".suppMusique", (evenement) => {
                //     let id = evenement.currentTarget.getAttribute("data-id");
                //
                //     musiques.splice(id, 1);
                //
                //     afficherMusiques(musiques);
                // });

                $("#lancerEnregistrement").click((e) => {
                    e.preventDefault();

                    recorder.start();

                    $("#onAir h3").addClass("red");
                    $("#finEmission").show();
                    $("#lancerEnregistrement").hide();
                });

                $("#finEmission").click(() => {
                    $("#onAir h3").removeClass("red");

                    //on stoppe l'enregistrement
                    recorder.stop();
                    arreterEnregistrement();

                    $("#finEmission").hide();
                    $("#lancerEnregistrement").show();

                });

            }).catch(function(err) {
            $.notify(err, "error");
        });
    }else{
        $.notify("Le navigateur n'est pas compatible", "error");
    }


});
