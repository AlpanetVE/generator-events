jQuery(document).ready(function($) {
   $("#btn-vote").click(function(){
var url = "http://localhost/quaszy/wp-content/plugins/star-rating/ajax_vote.php"; // El script a dónde se realizará la petición.

   $.ajax({
          type: "POST",
          url: url,
          data: $("#form-star").serialize(), // Adjuntar los campos del formulario enviado.
          success: function(data)
          {
              alert(data); // Mostrar la respuestas del script PHP.
          }
        });

   return false; // Evitar ejecutar el submit del formulario.
 });
});
