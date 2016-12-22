jQuery(document).ready(function($) {
   $("#btn-vote").click(function(){
var url = $("#btn-vote").data("url"); //"http://localhost/quaszy/wp-content/plugins/star-rating/ajax_vote.php"; // El script a dónde se realizará la petición.
var idEvent = $("#btn-vote").data("event");
   $.ajax({
          type: "POST",
          url: url,
          data: $("#form-star").serialize()+"&idevent="+idEvent, // Adjuntar los campos del formulario enviado.
          success: function(data)
          {
            if(data==1){
              alert('Your Ranking Vote has benn updated');
             }
               // Mostrar la respuestas del script PHP.
          }
        });

   return false; // Evitar ejecutar el submit del formulario.
 });
});
