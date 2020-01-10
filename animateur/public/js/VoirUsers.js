$(document).ready(() => {
   $(document).on('click','.supprUser',function() {
        fetch('supprUser/' + $(this).attr("data-id")).then(() => {
            $($(this).parent()).parent().remove();
        });
    });
});