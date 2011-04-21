<?php
// =========================================================================
// Montagem do cabeçalho de documentos Word
// -------------------------------------------------------------------------
function CabecalhoWordOR($l_titulo,$l_pagina,$w_logo) {
  extract($GLOBALS);
  ShowHTML("<BASE HREF=\"".$conRootSIW."\">");
  ShowHTML("<TABLE WIDTH=\"100%\" BORDER=0><TR><TD ROWSPAN=2><IMG ALIGN=\"LEFT\" SRC=\"".$w_logo."\"><TD ALIGN=\"RIGHT\"><B><FONT SIZE=4 COLOR=\"#000000\">");
  ShowHTML($l_titulo);
  ShowHTML("</FONT><TR><TD WIDTH=\"50%\" ALIGN=\"RIGHT\"><B><font size=1 COLOR=\"#000000\">".DataHora()."</B>");
  ShowHTML("<TR><TD COLSPAN=\"2\" ALIGN=\"RIGHT\"><B><FONT SIZE=2 COLOR=\"#000000\">Página: ".$l_pagina."</B></TD></TR>");
  ShowHTML("</TD></TR>");
  ShowHTML("</FONT></B></TD></TR></TABLE>");
}
?>