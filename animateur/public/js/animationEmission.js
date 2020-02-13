$(document).ready(() => {
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia){
        navigator.mediaDevices.getUserMedia (
            // constraints - only audio needed
            {audio: true})
            .then(function(stream) {

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// initialisation ///////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                let recorder = new MediaRecorder(stream);
                $("#finEmission").hide();

                // let musiques = [];
                let onAir = false;
                let pistesEmission = [new Blob([], {'type': "audio/ogg; codecs=opus"})];
                let enregistrementEmission = new Blob([], {'type': "audio/ogg; codecs=opus"});
                let dataRecord = [];

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

                function lancerRecordMicro() {
                    $("#onAir h3").addClass("red");
                    recorder.start();
                }

                function arreterEnregistrement() {
                    $("#onAir h3").removeClass("red");
                    //on stoppe l'enregistrement
                    recorder.stop();

                    // changerDureeEmission(pisteEmission);
                }

                function concatenerBlobs(arrayBlob) {
                    let tmp = new Blob(arrayBlob, {
                        "type": "audio/*"
                    });
                    tmp = new Blob([tmp],{
                        "type": "audio/ogg; codecs=opus"
                    });

                    return tmp;
                }

                // function changerDureeEmission(pisteEmission){
                //     let audio = document.createElement("audio");
                //     audio.setAttribute("src", pisteEmission);
                //
                //     let duration = audio.duration;
                //
                //     let min = Math.floor(((duration / 60)));
                //     let sec = Math.floor(((duration % 60)));
                //     let heure = Math.floor(((min / 60)));
                //     min = Math.floor(((min % 60)));
                //
                //     $("#footer").html(" Temps total de l'émission : " + heure + " h " + min + " min " + sec + " sec");
                // }
                //

                //enregistrement de l'audio
                recorder.ondataavailable = function (element){
                    pistesEmission.push(element.data);
                    enregistrementEmission = concatenerBlobs([enregistrementEmission, element.data]);
                    return element.data
                };

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// fin des fonctions ///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


                // $("#songs").change(() => {
                //     let fichier = document.querySelector("#songs");
                //
                //     for(let i = 0; i < fichier.files.length; i++){
                //         musiques.push(fichier.files[i]);
                //     }
                //
                //     afficherMusiques(musiques);
                // });

                $("#lancerEnregistrement").click(() => {
                    $("#finEmission").show();
                    $("#lancerEnregistrement").hide();
                    lancerRecordMicro();
                    onAir = true;
                });

                $("#finEmission").click(() => {
                    $("#finEmission").hide();
                    $("#lancerEnregistrement").show();

                    arreterEnregistrement();

                    console.log("piste ", pistesEmission)

                    let pistes = pistesEmission;

                    let emission = enregistrementEmission;


                    console.log("emission", emission);
                    // pistesEmission = concatenerBlobs(pistesEmission);

                    //envoi en bdd
                    let emission_id = $("#emission_id").val();
                    let datas = new FormData();
                    datas.append("emission_id", emission_id);
                    datas.append("song", pistesEmission);
                    let route = $("#route").val();

                    // $("#testEcoute").prop("src",window.URL.createObjectURL(pistesEmission));
                    $("#testEcoute").prop("src",window.URL.createObjectURL(emission));

                });




            }).catch(function(err) {
            $.notify(err, "error");
        });
    }else{
        $.notify("Le navigateur n'est pas compatible", "error");
    }


});
