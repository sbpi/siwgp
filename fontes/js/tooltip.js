$(document).ready(function() {
  $.fn.ToolTipDemo = function(bgcolour, fgcolour)
  {
    this.mouseover(
      function(e)
      {
        if((!this.title && !this.alt) && !this.tooltipset) return;
        var mouseX = e.pageX || (e.clientX ? e.clientX + document.body.scrollLeft : 0);

        var mouseY = e.pageY || (e.clientY ? e.clientY + document.body.scrollTop : 0);

        var janelaW = $(window).width();

        if((mouseX + 300) > janelaW){
          mouseX = janelaW - 300;
        }

        mouseX += 10;
        mouseY += 10;
        bgcolour = bgcolour || "#eee";
        fgcolour = fgcolour || "#000";

        if(!this.tooltipdiv)
        {

          var div = document.createElement("div");
          this.tooltipdiv = div;
          $(div).css(
          {
            border: "2px outset #ddd",
            padding: "2px",
            backgroundColor: bgcolour,
            color: fgcolour,
            position: "absolute",
            width: "250px"
          })

          .html((this.title || this.alt));
          this.title = "";
          this.alt = "";



          $("body").append(div);
          this.tooltipset = true;

        }
        $(this.tooltipdiv).show().css({
          left: mouseX + "px",
          top: mouseY + 3 + "px"
          });
      }
      ).mouseout(
      function()
      {
        if(this.tooltipdiv)
        {
          $(this.tooltipdiv).hide();
        }
      }
      );
    return this;
  }

  //Inclui o evento nos elementos
  $( "[title]" ).each(function(){
    $(this).ToolTipDemo("#ffffe1", "#000");
  });
});
