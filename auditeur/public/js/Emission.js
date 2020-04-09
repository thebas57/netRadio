$(() => {
   $("#conteneur-emission").on('click',".btn-ecoute",function () {
       console.log($(this).siblings("#ecoute"));
       $(this).siblings("#ecoute").show(1000);

   })
});