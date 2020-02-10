$(document).ready(() => {
    let musiques = [];


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
        document.getElementById("audio" + id).play();
    });

    $("#musiquesList").on("click", ".suppMusique", (evenement) => {
        let id = evenement.currentTarget.getAttribute("data-id");

        musiques.splice(id, 1);

        afficherMusiques(musiques);
    })

});

function afficherMusiques(musiques) {
    $("#musiquesList").html("");

    musiques.forEach((element, index) => {

        let enregistrement = document.createElement("article");
        let labelEnregistrement = document.createElement("button");
        let deleteEnregistrement = document.createElement("button");
        let audio = document.createElement("audio");

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