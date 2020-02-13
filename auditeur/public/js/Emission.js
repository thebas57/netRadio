//Pareil que $(document).ready()
$(() => {
   $("#conteneur-emission").on('click',".btn-ecoute",function () {
       console.log($(this).siblings("#ecoute"));
       $(this).siblings("#ecoute").show(1000);
   //     Affiche la balise audio pour écouter l'émission
   })
});