jQuery(document).ready(function($) {
  $("#see-more-event").prop('disabled', false);

  $(document).on( "click", "#see-more-event", function(e) {
    var BtnSeeMore  = $(this);

    sendAjax(BtnSeeMore);

  });


  function sendAjax(BtnSeeMore){


    BtnSeeMore.prop('disabled', true);

    data={
        action:'getEventBefore',
        name_link:BtnSeeMore.data('name'),
        start:BtnSeeMore.data('start')
      };


     $.ajax({
      url:"../wp-content/plugins/generator-events/ajax_ge.php",
      data: data,
      type:"POST",
      dataType: 'json',
      success:function(data){
        if (data.result=='true') {
          var div = document.createElement("div");
          div.className     += ' event-before';
          div.style.display  = "none";
          div.innerHTML      = data.html;

          document.getElementById("cont-event-before").innerHTML+=div.innerHTML;

          $(".event-before").fadeIn("slow");
          BtnSeeMore.data('start',data.start);

          if (data.moreEvent=='false') {
            BtnSeeMore.hide();
          }
        }

        BtnSeeMore.prop('disabled', false);
      }
    });
  }

  

});

