$(document).ready(() => {
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia){
        navigator.mediaDevices.getUserMedia (
            // constraints - only audio needed
            {audio: true})
            .then(function(stream) {

                let recorder = new MediaRecorder(stream);
                $("#finEmission").hide();

                let musiques = [];
                let onAir = false;
                let pisteEmission = new Blob([],{
                    type: "audio\*"
                });

                $("#songs").change(() => {
                    let fichier = document.querySelector("#songs");

                    for(let i = 0; i < fichier.files.length; i++){
                        musiques.push(fichier.files[i]);
                    }

                    afficherMusiques(musiques);
                });

                $("#musiquesList").on("click", ".musique", (evenement) => {
                    let id = evenement.currentTarget.getAttribute("data-id");

                    let audios = document.getElementsByClassName("musique");
                    for(let i = 0; i < audios.length; i++){
                        let id_audio = audios[i].getAttribute("data-id");
                        let tmp = document.getElementById("audio" + id_audio);
                        tmp.muted = true;
                    }

                    let musique = document.getElementById("audio" + id);
                    musique.muted = false;
                    let lecteur = document.getElementById("audio" + id);
                    lecteur.play();
                    $("#timer").html(lecteur.duration);
                    $("#onAir h3").removeClass("red");

                    lecteur.addEventListener("timeupdate", (event) => {
                        let time = ((Math.floor(lecteur.duration) - Math.floor(lecteur.currentTime)));

                        let min = ((Math.floor(time / 60)));
                        let sec = ((time % 60));

                        $("#timer").html(min + " min " + sec);
                    });

                    lecteur.onended = function () {
                        $("#onAir h3").addClass("red");
                        recorder = lancerRecordMicro(recorder);
                        $("#finEmission").prop("disabled", false);
                    };

                    let retour = arreterEnregistrement(pisteEmission, recorder, dataRecord);
                    pisteEmission = retour.pisteEmission;
                    dataRecord = retour.dataRecord;
                    recorder = retour.recorder;
                    pisteEmission = concatener2Blobs(pisteEmission, musiques[id]);
                    changerDureeEmission(pisteEmission);

                    $("#finEmission").prop("disabled", true);
                });

                $("#musiquesList").on("click", ".suppMusique", (evenement) => {
                    let id = evenement.currentTarget.getAttribute("data-id");

                    musiques.splice(id, 1);

                    afficherMusiques(musiques);
                });

                $("#lancerEnregistrement").click(() => {
                    $("#finEmission").show();
                    $("#lancerEnregistrement").hide();
                    recorder = lancerRecordMicro(recorder);
                });

                $("#finEmission").click(() => {
                    $("#finEmission").hide();
                    $("#lancerEnregistrement").show();
                    let retour = arreterEnregistrement(pisteEmission, recorder, dataRecord);
                    pisteEmission = retour.pisteEmission;
                    dataRecord = retour.dataRecord;
                    recorder = retour.recorder;

                    //envoi en bdd
                    let emission_id = $("#emission_id").val();
                    let datas = new FormData();
                    datas.append("emission_id", emission_id);
                    datas.append("song", pisteEmission);

                    let route = $("#route").val();

                    fetch(route + "/addSongEmission", {
                        method: "POST",
                        body: datas
                    }).then(
                        () => {
                            window.location.href = route + "/animateur";
                        }
                    )
                });

                //enregistrement de l'audio
                let dataRecord = [];
                recorder.ondataavailable = function (element){
                    dataRecord.push(element.data);
                };


            }).catch(function(err) {
            $.notify(err, "error");
        });
    }else{
        $.notify("Le navigateur n'est pas compatible", "error");
    }


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

    function lancerRecordMicro(recorder) {
        $("#onAir h3").addClass("red");
        recorder.start();
        return recorder;
    }

    function arreterEnregistrement(pisteEmission, recorder, dataRecord) {
        $("#onAir h3").removeClass("red");

        //on stoppe l'enregistrement
        recorder.stop();

        //creation du blob pour le son
        let blobby = new Blob(dataRecord, { 'type' : 'audio/mpeg; codecs=opus' });
        dataRecord = []; //reinitialisation des données du record

        pisteEmission = concatener2Blobs(pisteEmission, blobby);
        changerDureeEmission(pisteEmission);
        return {pisteEmission : pisteEmission, recorder: recorder, dataRecord: dataRecord};
    }

    function concatener2Blobs(blob1, blob2) {
        let tmp = new Blob([blob1, blob2], {
            type: "audio\*"
        });

        return tmp;
    }

    function changerDureeEmission(pisteEmission){
        let audio = document.createElement("audio");
        audio.setAttribute("src", pisteEmission);

        let duration = audio.duration;

        let min = Math.floor(((duration / 60)));
        let sec = Math.floor(((duration % 60)));
        let heure = Math.floor(((min / 60)));
        min = Math.floor(((min % 60)));

        $("#footer").html(" Temps total de l'émission : " + heure + " h " + min + " min " + sec + " sec");
    }
});
