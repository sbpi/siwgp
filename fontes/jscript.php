<?php
// FunÁ„o para crÌtica da hora
function VerfHora() {
  print "function VerfHora(Datac) {"."\r\n";
  print "   var numero = '0123456789:';"."\r\n";
  print "   var conta=0;"."\r\n";
  print "   var i=0;"."\r\n";
  print "   alert(nHora+'-'+nMin);"."\r\n";
  print "   var i=0;"."\r\n";
  print "   return false"."\r\n";
  
  print "   if (Datac.length==4){"."\r\n";
  print "     var nHora = parseFloat(Datac.substring(0,1));"."\r\n";
  print "     var nMin = parseFloat(Datac.substring(2,4));"."\r\n";
  print "   }"."\r\n";
  print "   else if (Datac.length==5){"."\r\n";
  print "     var nHora = parseFloat(Datac.substring(0,2));"."\r\n";
  print "     var nMin = parseFloat(Datac.substring(3,5));"."\r\n";
  print "   }"."\r\n";
  print "   if (Datac.length==0){"."\r\n";
  print "      return (true);"."\r\n";
  print "   }"."\r\n";
  print "   else if ((Datac.length!=5) && (Datac.length!=4)){"."\r\n";
  print "      alert('O formato da hora È HH:MM.');"."\r\n";
  print "      return (false);"."\r\n";
  print "   }"."\r\n";
  print "   if (nHora<0 || nHora>23){"."\r\n";
  print "       alert('Hora inv·lida !');"."\r\n";
  print "       return (false);"."\r\n";
  print "   }"."\r\n";
  print "   if (nMin<0 || nMin>59){"."\r\n";
  print "       alert('Hora inv·lida !');"."\r\n";
  print "       return (false);"."\r\n";
  print "   }"."\r\n";
  print "    for (i=0;i<Datac.length;i++) {"."\r\n";
  print "        if (numero.indexOf(Datac.charAt(i))==-1){"."\r\n";
  print "           alert('O formato da hora È HH:MM.');"."\r\n";
  print "           return (false);"."\r\n";
  print "        }"."\r\n";
  print "        if (Datac.charAt(i)==':') {"."\r\n";
  print "           conta = conta + 1;"."\r\n";
  print "        }"."\r\n";
  print "    }"."\r\n";
  print "    if (conta >1 && conta<1){"."\r\n";
  print "       alert('O formato da hora È HH:MM.');"."\r\n";
  print "       return (false);"."\r\n";
  print "    }"."\r\n";
  print "   return (true);"."\r\n";
  print "}"."\r\n";
}

// Abre a tag SCRIPT
function ScriptOpen($Language) { 
  extract($GLOBALS);
  //print chr(13).chr(10).'<script language="javascript" type="text/javascript" src="'.$conRootSIW.'js/jquery.js"></script>';
  print chr(13).chr(10).'<script type="text/javascript" language="'.$Language.'"><!--'.chr(13).chr(10);
}

// Encerra a tag SCRIPT
function ScriptClose() { print "--></script>"."\r\n"; }

// Abre a funÁ„o de validaÁ„o de formul·rios
function ValidateOpen($FunctionName) { 
  ShowHTML('function Trim(s){ ');
  ShowHTML("  return (s ? '' + s : '').replace(/^\s*|\s*$/g, '');");
  ShowHTML('}');
  ShowHTML('');
  ShowHTML('function RTrim(VALUE){');
  ShowHTML('  var w_space = String.fromCharCode(32);');
  ShowHTML('  var v_length = VALUE.length;');
  ShowHTML('  var strTemp = "";');
  ShowHTML('  if(v_length < 0){ return""; }');
  ShowHTML('  var iTemp = v_length -1;');
  ShowHTML('  while(iTemp > -1){ ');
  ShowHTML('    if(VALUE.charAt(iTemp) != w_space){');
  ShowHTML('      strTemp = VALUE.substring(0,iTemp +1);');
  ShowHTML('      break;');
  ShowHTML('    }');
  ShowHTML('    iTemp = iTemp-1;');
  ShowHTML('  }');
  ShowHTML('  return strTemp;');
  ShowHTML('}');
  ShowHTML('');
  ShowHTML('function LTrim(VALUE){');
  ShowHTML('  var w_space = String.fromCharCode(32);');
  ShowHTML('  if(v_length < 1){ return""; }');
  ShowHTML('  var v_length = VALUE.length;');
  ShowHTML('  var strTemp = "";');
  ShowHTML('  var iTemp = 0;');
  ShowHTML('  while(iTemp < v_length){');
  ShowHTML('    if(VALUE.charAt(iTemp) != w_space){');
  ShowHTML('      strTemp = VALUE.substring(iTemp,v_length);');
  ShowHTML('      break;');
  ShowHTML('    }');
  ShowHTML('    iTemp = iTemp + 1;');
  ShowHTML('  }');
  ShowHTML('  return strTemp;');
  ShowHTML('}');
  ShowHTML('');
  ShowHTML('function '.$FunctionName.' (theForm) {'); 
}

// Encerra a funÁ„o de validaÁ„o de formul·rios
function ValidateClose() {
  extract($GLOBALS);
  print "  return (true); "."\r\n";
  print "} "."\r\n";
  ShowHTML('');
}

// Cria funÁ„o para indicaÁ„o de campos obrigatÛrios
function Required() {
  extract($GLOBALS);
  ScriptOpen('JavaScript');
  ShowHTML('function required(){');
  if (count($w_campo_obrigatorio)>0) {
    foreach($w_campo_obrigatorio as $k => $v) {
      if (strpos($k,'[')===false) {
        ShowHTML('  document.Form.'.$k.'.className='.$v.';');
      } else {
        ShowHTML('  for (ind=1; ind < document.Form'.str_replace('[ind]','',$k).'.length; ind++) {');
        ShowHTML('    document.Form'.$k.'.className='.$v.';');
        ShowHTML('  }');
      }
    }
  } else {
    ShowHTML('  return true;');
  }
  ShowHTML('}');
  ShowHTML('');
  ScriptClose();
}

// C·lculo de mÛdulo
function Modulo() {
  print "  function modulo (dividendo,divisor) { "."\r\n";
  print "    var quociente = 0; "."\r\n";
  print "    var ModN = 0; "."\r\n";
  print "    quociente = Math.floor(dividendo/divisor); "."\r\n";
  print "    ModN = dividendo - (divisor*quociente); "."\r\n";
  print "    return divisor - ModN; "."\r\n";
  print "  } "."\r\n";
}

// Rotina auxiliar ‡ de verificaÁ„o de datas
function CheckBranco() {
  print "  function checkbranco(elemento){ "."\r\n";
  print "    var flagbranco = true "."\r\n";
  print "    //alert( 'elemento = ' + elemento) "."\r\n";
  print "    for (i=0;i < elemento.length;i++){ "."\r\n";
  print "        //alert('elemento.charat( ' + i + ') = ' + elemento.charAt(i) ) "."\r\n";
  print "        if (elemento.charAt(i) != ' '){ "."\r\n";
  print "            flagbranco = false "."\r\n";
  print "        } "."\r\n";
  print "    } "."\r\n";
  print "    //alert('valor de flagbranco = ' + flagbranco) "."\r\n";
  print "    return flagbranco "."\r\n";
  print "  } "."\r\n";
}

// Rotina de comparaÁ„o de datas
function CompData($Date1,$DisplayName1,$Operator,$Date2,$DisplayName2) {
  if(strpos($Date1,'[')===false) $Form1 = "  theForm."; else $Form1 = "theForm";
  if(strpos($Date2,'[')===false) $Form2 = "  theForm."; else $Form2 = "theForm";
  switch ($Operator) {
    case "==":  $w_Operator=" igual a ";            break;
    case "!=":  $w_Operator=" diferente de ";       break;
    case ">":   $w_Operator=" maior que ";          break;
    case "<":   $w_Operator=" menor que ";          break;
    case ">=":  $w_Operator=" maior ou igual a ";   break;
    case "=>":  $w_Operator=" maior ou igual a ";   break;
    case "<=":  $w_Operator=" menor ou igual a ";   break;
    case "=<":  $w_Operator=" menor ou igual a ";   break;
  }
  print "  var D1 = ".$Form1.$Date1.".value; "."\r\n";
  if (strpos("1234567890",substr($Date2,0,1))===false) {
     print "  var D2 = ".$Form2.$Date2.".value;"."\r\n"; }
  else {
    print "  var D2 = '".$Date2."';"."\r\n";
  }

  print "  if (D1.length != 0 && D2.length != 0) { "."\r\n";
  print "     var d1; "."\r\n";
  print "     var m1; "."\r\n";
  print "     var a1; "."\r\n";
  print "     var h1; "."\r\n";
  print "     var d2; "."\r\n";
  print "     var m2; "."\r\n";
  print "     var a2; "."\r\n";
  print "     var h2; "."\r\n";
  print "     var Data1; "."\r\n";
  print "     var Data2; "."\r\n";
  print "     if (D1.length == 17) { "."\r\n";
  print "        d1 = D1.substr(0,2); "."\r\n";
  print "        m1 = D1.substr(3,2); "."\r\n";
  print "        a1 = D1.substr(6,4); "."\r\n";
  print "        h1 = D1.substr(12,2) + D1.substr(15,2); "."\r\n";
  print "        d2 = D2.substr(0,2); "."\r\n";
  print "        m2 = D2.substr(3,2); "."\r\n";
  print "        a2 = D2.substr(6,4); "."\r\n";
  print "        h2 = D2.substr(12,2) + D2.substr(15,2); "."\r\n";
  print "        Data1 = a1 + m1 + d1 + h1; "."\r\n";
  print "        Data2 = a2 + m2 + d2 + h2; "."\r\n";
  print "     } "."\r\n";
  print "     if (D1.length == 10) { "."\r\n";
  print "        d1 = D1.substr(0,2); "."\r\n";
  print "        m1 = D1.substr(3,2); "."\r\n";
  print "        a1 = D1.substr(6,4); "."\r\n";
  print "        d2 = D2.substr(0,2); "."\r\n";
  print "        m2 = D2.substr(3,2); "."\r\n";
  print "        a2 = D2.substr(6,4); "."\r\n";
  print "        Data1 = a1 + m1 + d1; "."\r\n";
  print "        Data2 = a2 + m2 + d2; "."\r\n";
  print "     } "."\r\n";
  print "     if (D1.length == 7) { "."\r\n";
  print "        d1 = '01'; "."\r\n";
  print "        m1 = D1.substr(0,2); "."\r\n";
  print "        a1 = D1.substr(3,6); "."\r\n";
  print "        d2 = '01'; "."\r\n";
  print "        m2 = D2.substr(0,2); "."\r\n";
  print "        a2 = D2.substr(3,7); "."\r\n";
  print "        Data1 = a1 + m1 + d1; "."\r\n";
  print "        Data2 = a2 + m2 + d2; "."\r\n";
  print "     } "."\r\n";
  print "     if (!(Data1 ".$Operator." Data2)) { "."\r\n";
  print "        alert('".$DisplayName1." deve ser".$w_Operator.$DisplayName2.".'); "."\r\n";
  print "        ".$Form1.$Date1.".focus(); "."\r\n";
  print "        return (false); "."\r\n";
  print "     } "."\r\n";
  print "  } "."\r\n";
}

function CompHora ($hour1, $DisplayName1, $Operator, $hour2, $DisplayName2) {
  if(strpos($hour1,'[')===false) $Form = "  theForm."; else $Form = "theForm";
  switch ($Operator) {
    case "==":  $w_Operator=" igual a ";            break;
    case "!=":  $w_Operator=" diferente de ";       break;
    case ">":   $w_Operator=" maior que ";          break;
    case "<":   $w_Operator=" menor que ";          break;
    case ">=":  $w_Operator=" maior ou igual a ";   break;
    case "=>":  $w_Operator=" maior ou igual a ";   break;
    case "<=":  $w_Operator=" menor ou igual a ";   break;
    case "=<":  $w_Operator=" menor ou igual a ";   break;
  }
  print "  var D1 = ".$Form.$hour1.".value; "."\r\n";
  if (strpos("1234567890", substr($hour2,0,1))===false) {
    print "   var D2 = ".$Form.$hour2.".value;"."\r\n";
  } else {
    print "   var D2 = '".$hour2."';"."\r\n";
  }
  print "  if (D1.length != 0 && D2.length != 0) { "."\r\n";
  print "   var h1; "."\r\n";
  print "   var h2; "."\r\n";
  print "   h1 = D1.substr(0,2) + D1.substr(3,2); "."\r\n";
  print "   h2 = D2.substr(0,2) + D2.substr(3,2); "."\r\n";
  print "   if (!(parseFloat(h1) ".$Operator." parseFloat(h2))) { "."\r\n";
  print "      alert('".$DisplayName1." deve ser ".$w_Operator.$DisplayName2.".'); "."\r\n";
  print "      ".$Form.$hour1.".focus(); "."\r\n";
  print "      return (false); "."\r\n";
  print "   } "."\r\n";
  print " } "."\r\n";
}

function CompValor ($Valor1, $DisplayName1, $Operator, $Valor2, $DisplayName2) {
  if(strpos($Valor1,'[')===false) $Form = "  theForm."; else $Form = "theForm";
  switch ($Operator) {
    case "==":  $w_Operator=" igual a ";            break;
    case "!=":  $w_Operator=" diferente de ";       break;
    case ">":   $w_Operator=" maior que ";          break;
    case "<":   $w_Operator=" menor que ";          break;
    case ">=":  $w_Operator=" maior ou igual a ";   break;
    case "=>":  $w_Operator=" maior ou igual a ";   break;
    case "<=":  $w_Operator=" menor ou igual a ";   break;
    case "=<":  $w_Operator=" menor ou igual a ";   break;
  }
  print "  var V1 = ".$Form.$Valor1 . ".value; "."\r\n";
  if (strpos("1234567890", substr($Valor2,0,1))===false) {
    print "   var V2 = ".$Form.$Valor2 . ".value;"."\r\n";
  } else {
    print "   var V2 = '" . $Valor2 . "';"."\r\n";
  }
  print "  if (V1.length != 0 && V2.length != 0) { "."\r\n";
  print "     V1 = V1.toString().replace(/\\$|\\./g,''); "."\r\n";
  print "     V1 = V1.toString().replace(',','.'); "."\r\n";
  print "     V2 = V2.toString().replace(/\\$|\\./g,''); "."\r\n";
  print "     V2 = V2.toString().replace(',','.'); "."\r\n";
  print "     if (isNaN(V1)) { "."\r\n";
  print "        alert('" . $DisplayName1 . " n„o È um valor v·lido!.'); "."\r\n";
  print "        ".$Form.$Valor1 . ".focus(); "."\r\n";
  print "        return false; "."\r\n";
  print "     } "."\r\n";
  print "     if (isNaN(V2)) { "."\r\n";
  print "        alert('" . $DisplayName2 . " n„o È um valor v·lido!.'); "."\r\n";
  if (strpos("1234567890",substr($Valor2,0,1))===false) {
     print "        ".$Form.$Valor2 . ".focus(); "."\r\n";
  } else {
     print "        ".$Form.$Valor1 . ".focus(); "."\r\n";
  }
  print "        return false; "."\r\n";
  print "     } "."\r\n";
  print "     var v1 = parseFloat(V1);"."\r\n";
  print "     var v2 = parseFloat(V2);"."\r\n";
  print "     if (!(v1 " . $Operator . " v2)) { "."\r\n";
  print "        alert('" . $DisplayName1 . " deve ser " .$w_Operator . $DisplayName2 . ".'); "."\r\n";
  print "        ".$Form.$Valor1 . ".focus(); "."\r\n";
  print "        return false; "."\r\n";
  print "     } "."\r\n";
  print "  } "."\r\n";
}

function toMoney() {
  print " function toMoney(campo, fmt) { "."\r\n";
  print "  num = campo.toString().replace(/\$|\,/g,''); "."\r\n";
  print "  if (isNaN(num)) { "."\r\n";
  print "    return false;} "."\r\n";
  print "  if (fmt.toUpperCase() == 'US') { "."\r\n";
  print "   cents = Math.floor((num*100+0.5)%100); "."\r\n";
  print "   num = Math.floor((num*100+0.5)/100).toString(); "."\r\n";
  print "   if(cents < 10) cents = '0' + cents; "."\r\n";
  print "   for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++) "."\r\n";
  print "   num = num.substring(0,num.length-(4*i+3)) + num.substring(num.length-(4*i+3)); "."\r\n";
  print "   return (num + '.' + cents); "."\r\n";
  print "  } "."\r\n";
  print "  if (fmt.toUpperCase() == 'BR') { "."\r\n";
  print "   cents = Math.floor((num*100+0.5)%100); "."\r\n";
  print "   num = Math.floor((num*100+0.5)/100).toString(); "."\r\n";
  print "   if(cents < 10) cents = '0' + cents; "."\r\n";
  print "   for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++) "."\r\n";
  print "   num = num.substring(0,num.length-(4*i+3)) + '.' +num.substring(num.length-(4*i+3)); "."\r\n";
  print "   return (num + ',' + cents); "."\r\n";
  print "  } "."\r\n";
  print "  return false; "."\r\n";
  print " } "."\r\n";
}

function DecodeDate() {
  print "function LeapYear(intYear) { "."\r\n";
  print " if (intYear % 100 == 0) { "."\r\n";
  print "  if (intYear % 400 == 0) { return true; } "."\r\n";
  print " } "."\r\n";
  print "  else { "."\r\n";
  print " if ((intYear % 4) == 0) { return true; } "."\r\n";
  print " } "."\r\n";
  print " return false; "."\r\n";
  print " } "."\r\n";
  print " function DecodeDate(date) { "."\r\n";
  print "  var day, month, year; "."\r\n";
  print "  if (date.length < 10) return false; "."\r\n";
  print "  day = date.substr(0, 2); "."\r\n";
  print "  month = date.substr(3, 2); "."\r\n";
  print "  year = date.substr(6, 4); "."\r\n";
  print "  if (parseInt(month) == 4 || parseInt(month) == 6 || parseInt(month) == 9 || parseInt(month) == 11) { "."\r\n";
  print "   if (parseInt(day) == 31) return false; } "."\r\n";
  print "  if (LeapYear(parseInt(year))) { "."\r\n";
  print "    if (parseInt(day) > 29) return false;  "."\r\n";
  print "    else { "."\r\n";
  print "          if (parseInt(day) > 28) return false; "."\r\n";
  print "         } "."\r\n";
  print "   } "."\r\n";
  print "  return (new Date(parseInt(year), parseInt(month) - 1, parseInt(day))); "."\r\n";
  print " } "."\r\n";
}

// Rotina para salto autom·tico de campo
function SaltaCampo() {
  ShowHTML('function SaltaCampo(form,campo,tammax,event,prox){ ');
  ShowHTML('  var tecla = event.which; ');
  ShowHTML('  vr = campo.value; ');
  ShowHTML('  tam = vr.length; ');
  ShowHTML('  frm = eval(\'document.\'+form); ');
  ShowHTML('  prox = eval(\'document.\'+form+\'.\'+prox); ');
  ShowHTML('  if (tecla != 0 && tecla != 9 && tecla != 16 ){ ');
  ShowHTML('    if ( tam == tammax ){ ');
  ShowHTML('      if ( prox ) { ');
  ShowHTML('        prox.focus(); ');
  ShowHTML('      } else {');
  ShowHTML('        var ind_campo = -1; ');
  ShowHTML('        for (var i=0;i < frm.elements.length;i++) { ');
  ShowHTML('          if (frm.elements[i].name == campo.name && !frm.elements[i].disabled) {');
  ShowHTML('            ind_campo = i;');
  ShowHTML('            break;');
  ShowHTML('          } ');
  ShowHTML('        } ');
  ShowHTML('        var j = ind_campo+1; ');
  ShowHTML('        var ind_prox = ind_campo; ');
  ShowHTML('        for (var i=j;i < frm.elements.length;i++) { ');
  ShowHTML('          tipo = frm.elements[i].type.toLowerCase();');
  ShowHTML('          if ((tipo==\'text\' || tipo==\'file\' || tipo==\'password\' || tipo==\'select-one\' || tipo==\'select-multiple\' || tipo==\'textarea\') && !frm.elements[i].disabled) {');
  ShowHTML('            ind_prox = i;');
  ShowHTML('            break;');
  ShowHTML('          } ');
  ShowHTML('        } ');
  ShowHTML('        frm.elements[ind_prox].focus();');
  ShowHTML('      } ');
  ShowHTML('    } ');
  ShowHTML('  } ');
  ShowHTML('} ');
}

// Rotina para soma de dias a uma data
function SomaDias() {
  ShowHTML('function somaDias(p_data, p_data_ant, p_dias, p_campo_vinc, p_campo_prox) {');
  ShowHTML('  if (p_data.value != "" && p_data.value != eval(p_data_ant+".value")) {');
  ShowHTML('    if (p_data.value.length < 10 && p_data.value != "") {');
  ShowHTML('      alert("Favor digitar pelo menos 10 posiÁıes!");');
  ShowHTML('      p_data.focus();');
  ShowHTML('      return (false);');
  ShowHTML('    }');
  ShowHTML('    var checkOK = "0123456789/";');
  ShowHTML('    var checkStr =   p_data.value;');
  ShowHTML('    var allValid = true;');
  ShowHTML('    for (i = 0;  i < checkStr.length;  i++) {');
  ShowHTML('      ch = checkStr.charAt(i);');
  ShowHTML('      if ((checkStr.charCodeAt(i) != 13) && (checkStr.charCodeAt(i) != 10) && (checkStr.charAt(i) != "\\\\")) {');
  ShowHTML('        for (j = 0;  j < checkOK.length;  j++) { if (ch == checkOK.charAt(j)) break; } ');
  ShowHTML('        if (j == checkOK.length) {');
  ShowHTML('          allValid = false;');
  ShowHTML('           break;');
  ShowHTML('        }');
  ShowHTML('      } ');
  ShowHTML('    }');
  ShowHTML('    if (!allValid) {');
  ShowHTML('      alert("Favor digitar apenas n˙meros!");');
  ShowHTML('      p_data.focus();');
  ShowHTML('      return (false);');
  ShowHTML('    }');
  ShowHTML('    var checkStr = p_data.value;');
  ShowHTML('    var err=0;');
  ShowHTML('    var psj=0;');
  ShowHTML('    if (checkStr.length != 0) {');
  ShowHTML('       if (!checkbranco(checkStr)) {');
  ShowHTML('         if (checkStr.length != 10) err=1');
  ShowHTML('         dia = checkStr.substring(0, 2);');
  ShowHTML('         barra1 = checkStr.substring(2, 3);');
  ShowHTML('         mes = checkStr.substring(3, 5);');
  ShowHTML('         barra2 = checkStr.substring(5, 6);');
  ShowHTML('         ano = checkStr.substring(6, 10);');
  ShowHTML('         //verificaÁıes b·sicas');
  ShowHTML('         if (mes<1 || mes>12) err = 1;');
  ShowHTML('         if (barra1 != "/") err = 1;');
  ShowHTML('         if (dia<1 || dia>31) err = 1;');
  ShowHTML('         if (barra2 != "/") err = 1;');
  ShowHTML('         if (ano<1900 || ano>2900) err = 1;');
  ShowHTML('         //verificaÁıes avanÁadas');
  ShowHTML('         // mÍs com 30 dias');
  ShowHTML('         if (mes==4 || mes==6 || mes==9 || mes==11) {');
  ShowHTML('            if (dia==31) err=1;');
  ShowHTML('         }');
  ShowHTML('         // fevereiro e ano bissexto');
  ShowHTML('         if (mes==2){');
  ShowHTML('            var g=parseInt(ano/4);');
  ShowHTML('            if (isNaN(g)) err=1;');
  ShowHTML('            if (dia>29) err=1;');
  ShowHTML('            if (dia==29 && ((ano/4)!=parseInt(ano/4))) err=1;');
  ShowHTML('         }');
  ShowHTML('       } else { err=1; }');
  ShowHTML('    }');
  ShowHTML('    if (err==1){');
  ShowHTML('       alert("Data inv·lida!");');
  ShowHTML('       p_data.focus();');
  ShowHTML('       return (false);');
  ShowHTML('    }');
  ShowHTML('    var w_data, w_data1, w_data2;');
  ShowHTML('    w_data = p_data.value;');
  ShowHTML('    w_data = w_data.substr(3,2) + \'/\' + w_data.substr(0,2) + \'/\' + w_data.substr(6,4);');
  ShowHTML('    w_data1  = new Date(Date.parse(w_data));');
  ShowHTML('    var MinMilli = 1000 * 60;');
  ShowHTML('    var HrMilli = MinMilli * 60;');
  ShowHTML('    var DyMilli = HrMilli * 24;');
  ShowHTML('    var w_prazo = DyMilli * (p_dias-1);');
  ShowHTML('    w_fim = new Date(w_data1.getTime() + w_prazo);');
  ShowHTML('    w_day = 100 + w_fim.getDate();');
  ShowHTML('    w_d = (w_day+"").substring(1,3);');
  ShowHTML('    w_month = 101 + w_fim.getMonth();');
  ShowHTML('    w_m = (w_month+"").substring(1,3);');
  ShowHTML('    w_y = w_fim.getFullYear();');
  ShowHTML('    eval(p_data_ant+\'.value = "\'+p_data.value+\'"\');');
  ShowHTML('    eval(p_campo_vinc+".value = w_d + \'/\' + w_m + \'/\' + w_y");');
  ShowHTML('    eval(p_campo_prox+".focus()");');
  ShowHTML('  }');
  ShowHTML('}');
}

function FormataData() {
  print "function FormataData(campo, teclapres) { "."\r\n";
  print "    var tecla = teclapres.keyCode; "."\r\n";
  print "    vr = campo.value; "."\r\n";
  print "    vr = vr.replace( '/', '' ); "."\r\n";
  print "    vr = vr.replace( '/', '' ); "."\r\n";
  print "    tam = vr.length + 1; "."\r\n";
  print "    if (tecla == 8 ) tam = tam - 1 ; "."\r\n";
  print "    if ( tecla != 9 && tecla != 8 ) { "."\r\n";
  print "       if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ) { "."\r\n";
  print "           if ( tam <= 2 ) campo.value = vr ; "."\r\n";
  print "           if ( tam > 2 && tam < 5 ) campo.value = vr.substr( 0, 2 ) + '/' + vr.substr( 2, tam ); "."\r\n";
  print "           if ( tam >= 5 && tam <= 10 ) campo.value = vr.substr( 0, 2 ) + '/' + vr.substr( 2, 2 ) + '/' + vr.substr( 4, tam );  "."\r\n";
  print "      } "."\r\n";
  print "   } "."\r\n";
  print "} "."\r\n";
}

function FormataHora() {
 print "function FormataHora(campo, teclapres) { " ."\r\n";
 print "    var tecla = teclapres.keyCode; " ."\r\n";
 print "    vr = campo.value; " ."\r\n";
 print "    vr = vr.replace( ':', '' ); " ."\r\n";
 print "    tam = vr.length + 1; " ."\r\n";
 print "    if (tecla == 8 ){    tam = tam - 1 ; } " ."\r\n";
 print "    if ( tecla != 9 && tecla != 8 ){ " ."\r\n";
 print "    if ( tam < campo.maxLength && (tecla == 8 || (tecla >= 48 && tecla <= 57) || (tecla >= 96 && tecla <= 105))){ " ."\r\n";
 print "        if ( tam <= 2 ) campo.value = vr ; " ."\r\n";
 print "        if ( tam > 2 ) campo.value = vr.substr( 0, tam-2 ) + ':' + vr.substr( tam-2 ); " ."\r\n";
 print "    } " ."\r\n";
 print "  } " ."\r\n";
 print "} " ."\r\n";
}

function FormataMat() {
  print "function FormataMat (campo,teclapres) { "."\r\n";
  print "    var tecla = teclapres.keyCode; "."\r\n";
  print "    vr = campo.value; "."\r\n";
  print "    vr = vr.replace( '-', '' ); "."\r\n";
  print "    tam = vr.length; "."\r\n";
  print "    if (tam < 9 && tecla != 7){ tam = vr.length + 1 ; } "."\r\n";
  print "    if (tecla == 7 ){    tam = tam - 1 ; } "."\r\n";
  print "    if ( tecla == 7 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ){ "."\r\n";
  print "         if ( (tam > 8) && (tam <= 10) ){ "."\r\n";
  print "             campo.value = vr.substr( 0, tam - 1 ) + '-' + vr.substr( tam - 1, tam ) ; } "."\r\n";
  print "    } "."\r\n";
  print "} "."\r\n";
}

function CriticaNumero() {
  print "function CriticaNumero(campo, teclapres) { "."\r\n";
  print "    var tecla = teclapres.keyCode; "."\r\n";
  print "     alert(tecla); "."\r\n";
  print " }"."\r\n";
}

function DaysLeft() {
  print " function DaysLeft(date) { "."\r\n";
  print "  var now = date.getDate(); "."\r\n";
  print "  var year = date.getYear(); "."\r\n";
  print "  if (year < 2000) year += 1900; // Y2K fix "."\r\n";
  print "  var month = date.getMonth(); "."\r\n";
  print "  var monarr = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); "."\r\n";
  print "  if (((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0)) monarr[1] = '29'; "."\r\n";
  print "  return (monarr[month]-now); "."\r\n";
  print " } "."\r\n";
}

function FormataValor() {  
?>

    function FormataValor(campo, maximo, tammax, teclapres) {     
    var mascara = "";
    var decimal = "";
    //campo.setAttribute("id", campo.name);                  
    mascara = repeteCaracter("#",tammax);

    if(mascara != ""){
      decimal = "," + mascara;
    }

     $(campo).bind("keyup", function(e){
     var sinal = "";
     if(campo.value.indexOf('-') != -1) sinal = "-"; 
      $(campo).val( sinal + mascaraGlobal('[###.]###' + decimal ,$(campo).val()) );
    });        
    }
<?php

}

function FormataCPF() {
?>
  function FormataCPF (campo,teclapres) {    
     campo.setAttribute("id", campo.name);                      
     $(campo).bind("keyup", function(e){
      $(campo).val( mascaraGlobal('###.###.###-##' ,$(campo).val()) );
    });   
  }
<?php
}

function FormataCNPJ() {
?>
  function FormataCNPJ (campo,teclapres) { 
    campo.setAttribute("id", campo.name);                      
       $(campo).bind("keyup", function(e){
        $(campo).val( mascaraGlobal('##.###.###/####-##' ,$(campo).val()) );
      });     
  }
<?php
}

function FormataProtocolo() {
  print "function FormataProtocolo (campo,teclapres) { "."\r\n";
  print "    var tecla = teclapres.keyCode; "."\r\n";
  print "    vr = campo.value; "."\r\n";
  print "    vr = vr.replace( '-', '' ); "."\r\n";
  print "    vr = vr.replace( '/', '' ); "."\r\n";
  print "    vr = vr.replace( '.', '' ); "."\r\n";
  print "    tam = vr.length; "."\r\n";
  print "    if (tam < 14 && tecla != 8){ tam = vr.length + 1 ; } "."\r\n";
  print "    if (tecla == 8 ){    tam = tam - 1 ; } "."\r\n";
  print "    if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ){ "."\r\n";
  print "      if (tam <= 5 ) campo.value = vr ; "."\r\n";
  print "      else if (tam <= 11) campo.value = vr.substr(0,5)+'.'+ vr.substr(5,tam ); "."\r\n";
  print "      else if (tam <= 15) campo.value = vr.substr(0,5)+'.'+ vr.substr(5,6   )+'/'+vr.substr(11,tam); "."\r\n";
  print "      else if (tam <= 16) campo.value = vr.substr(0,5)+'.'+ vr.substr(5,6   )+'/'+vr.substr(11,4  )+'-'+vr.substr(15,tam); "."\r\n";
  print "    } "."\r\n";
  print "} "."\r\n";
}

function FormataCEP() {
?>
    function FormataCEP (campo,teclapres) { 
     campo.setAttribute("id", campo.name);                      
       $(campo).bind("keyup", function(e){
        $(campo).val( mascaraGlobal('#####-###' ,$(campo).val()) );
      });  
    }
<?php
}

function FormataDataHora() {
  print "function FormataDataHora(campo, teclapres) { "."\r\n";
  print "    var tecla = teclapres.keyCode; "."\r\n";
  print "    vr = campo.value; "."\r\n";
  print "    vr = vr.replace( ':', '' ); "."\r\n";
  print "    vr = vr.replace( ',', '' ); "."\r\n";
  print "    vr = vr.replace( ' ', '' ); "."\r\n";
  print "    vr = vr.replace( '/', '' ); "."\r\n";
  print "    vr = vr.replace( '/', '' ); "."\r\n";
  print "    tam = vr.length + 1; "."\r\n";
  print "    if (tecla == 8 ){    tam = tam - 1 ; } "."\r\n";
  print "    if ( tecla != 9 && tecla != 8 ){ "."\r\n";
  print "    if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ){ "."\r\n";
  print "        if ( tam <= 2 ){ "."\r\n";
  print "             campo.value = vr ; } "."\r\n";
  print "        if ( tam > 2 && tam < 5 ) "."\r\n";
  print "            campo.value = vr.substr( 0, 2 ) + '/' + vr.substr( 2, tam ); "."\r\n";
  print "        if ( tam >= 5 && tam < 10 ) "."\r\n";
  print "            campo.value = vr.substr( 0, 2 ) + '/' + vr.substr( 2, 2 ) + '/' + vr.substr( 4, tam );  "."\r\n";
  print "        if ( tam >=10 && tam <= 11 ) "."\r\n";
  print "            campo.value = vr.substr( 0, 2 ) + '/' + vr.substr( 2, 2 ) + '/' + vr.substr( 4, 4 ) + ', ' + vr.substr( 8, tam );  "."\r\n";
  print "        if ( tam >=12 ) "."\r\n";
  print "            campo.value = vr.substr( 0, 2 ) + '/' + vr.substr( 2, 2 ) + '/' + vr.substr( 4, 4 ) + ', ' + vr.substr( 8, 2 ) + ':' + vr.substr( 10, tam );  "."\r\n";
  print "    } "."\r\n";
  print "  } "."\r\n";
  print "} "."\r\n";
}

function FormataDataDM() {
 print "function FormataDataDM(campo, teclapres) { " ."\r\n";
 print "    var tecla = teclapres.keyCode; " ."\r\n";
 print "    vr = campo.value; " ."\r\n";
 print "    vr = vr.replace( '/', '' ); " ."\r\n";
 print "    tam = vr.length + 1; " ."\r\n";
 print "    if (tecla == 8 ){    tam = tam - 1 ; } " ."\r\n";
 print "    if ( tecla != 9 && tecla != 8 ){ " ."\r\n";
 print "    if ( tam < campo.maxLength && (tecla == 8 || (tecla >= 48 && tecla <= 57) || (tecla >= 96 && tecla <= 105))){ " ."\r\n";
 print "        if ( tam <= 2 ) campo.value = vr ; " ."\r\n";
 print "        if ( tam > 2 ) campo.value = vr.substr( 0, tam-2 ) + '/' + vr.substr( tam-2 ); " ."\r\n";
 print "    } " ."\r\n";
 print "  } " ."\r\n";
 print "} " ."\r\n";
}

function FormataDataMA() {
  print "function FormataDataMA(campo, teclapres) { "."\r\n";
  print "    var tecla = teclapres.keyCode; "."\r\n";
  print "    vr = campo.value; "."\r\n";
  print "    vr = vr.replace( '/', '' ); "."\r\n";
  print "    tam = vr.length + 1; "."\r\n";
  print "    if (tecla == 8 ){    tam = tam - 1 ; } "."\r\n";
  print "    if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ){ "."\r\n";
  print "        if ( tam <= 2 ) campo.value = vr ;  "."\r\n";
  print "        if ( tam > 2 ) campo.value = vr.substr( 0, 2 ) + '/' + vr.substr( 2, tam ); "."\r\n";
  print "    } "."\r\n";
  print "} "."\r\n";
}

// Abre box modal
function openBox($action=null,$width="80%",$height="95%",$autoScale="true",$autoDimensions="true",$centerOnScroll="true",$type="iframe") {
  print '  $(document).ready(function() {'."\r\n";
  print '    $(".cancelar").click(function(){'."\r\n";
  print '      closeBox()'."\r\n";
  print '    });'."\r\n";
  print '    $(".boxClean").fancybox({'."\r\n";
  print '      "width"           : "'.$width.'",'."\r\n";
  print '      "height"          : "'.$height.'",'."\r\n";
  print '      "autoScale"       : '.$autoScale.','."\r\n";
  print '      "autoDimensions"  : '.$autoDimensions.','."\r\n";
  print '      "centerOnScroll"  : '.$centerOnScroll.','."\r\n";
  print '      "type"            : "'.$type.'"'."\r\n";
  print '    });'."\r\n";
  print '    $(".box").fancybox({'."\r\n";
  print '      "width"           : "'.$width.'",'."\r\n";
  print '      "height"          : "'.$height.'",'."\r\n";
  print '      "autoScale"       : '.$autoScale.','."\r\n";
  print '      "autoDimensions"  : '.$autoDimensions.','."\r\n";
  print '      "centerOnScroll"  : '.$centerOnScroll.','."\r\n";
  print '      "type"            : "'.$type.'"'."\r\n";
  if (lower($action)=='submit')     print '    , "onClosed"        : function() { $("form").submit(); }'."\r\n";
  if (lower($action)=='reload')     print '    , "onClosed"        : function() { document.location.reload(true); }'."\r\n";
  print '    });'."\r\n";
  print '  });'."\r\n";

  print '  function closeBox() {'."\r\n";
  print '    parent.$.fancybox.close();'."\r\n";
  print '  }'."\r\n";
}

// Abre a tag SCRIPT
function Validate($VariableName,$DisplayName,$DataType,$ValueRequired,$MinimumLength,$MaximumLength,$AllowLetters,$AllowDigits) {
  extract($GLOBALS);
  global $w_campo_obrigatorio;
  if(strpos($VariableName,'[')===false) $Form = "  theForm."; else $Form = "theForm";
  if (upper($DataType)!="SELECT" && upper($DataType)!="HIDDEN") {
    print "  ".$Form.$VariableName.".value = Trim(".$Form.$VariableName.".value);"."\r\n"; 
  }
  if ($ValueRequired>"") {
    $w_campo_obrigatorio[$VariableName]='"STIO"';
    if (upper($DataType)=="SELECT") { 
      print "  if (".$Form.$VariableName.".selectedIndex == 0)"."\r\n"; 
    } else { 
      print "  if (".$Form.$VariableName.".value == '')"."\r\n"; 
    }

    print "  {"."\r\n";
    print "    alert('Favor informar um valor para o campo ".$DisplayName."');"."\r\n";
    if (upper($DataType)!="HIDDEN") { print "    ".$Form.$VariableName.".focus();"."\r\n"; }
    print "    return (false);"."\r\n";
    print "  }"."\r\n";
    print "\r\n";
  }

  if ($MinimumLength>"") {
    print "  if (".$Form.$VariableName.".value.length < ".$MinimumLength." && ".$Form.$VariableName.".value != '')"."\r\n";
    print "  {"."\r\n";
    print "    alert('Favor digitar pelo menos ".$MinimumLength." posiÁıes no campo ".$DisplayName."');"."\r\n";
    if (upper($DataType)!="HIDDEN") { print "    ".$Form.$VariableName.".focus();"."\r\n"; }
    print "    return (false);"."\r\n";
    print "  }"."\r\n";
    print "\r\n";
  }

  if ($MaximumLength>"") {
    print "  if (".$Form.$VariableName.".value.length > ".$MaximumLength." && ".$Form.$VariableName.".value != '')"."\r\n";
    print "  {"."\r\n";
    print "    alert('Favor digitar no m·ximo ".$MaximumLength." posiÁıes no campo ".$DisplayName.".\\nForam digitadas ' + ".$Form.$VariableName.".value.length + ' posiÁıes.');"."\r\n";
    if (upper($DataType)!="HIDDEN") { print "    ".$Form.$VariableName.".focus();"."\r\n"; }
    print "    return (false);"."\r\n";
    print "  }"."\r\n";
    print "\r\n";
  }

  if ($AllowLetters>"" || $AllowDigits>"") {
    $checkOK="";
    if ($AllowLetters>"") {
      if ($AllowLetters=='1') { $checkOK='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz¿¡¬√«»… ÃÕŒ“”‘’Ÿ⁄€‹¿»Ã“Ÿ¬ Œ‘€‡·‚„ÁÈÍÌÓÛÙı˙˚¸‡ËÏÚÏ‚ÍÓÙ˚0123456789-ñ,.()-:;[]{}*Ä&%$#@!/∫∞™≤≥?<>|+=_\\ìî"\\\' '; }
      else { $checkOK=$checkOK.$AllowLetters; }
    }

    if ($AllowDigits>"") {
      if ($AllowDigits=="1") { $checkOK=$checkOK."0123456789-.,/: "; }
      else { $checkOK=$checkOK.$AllowDigits; }
    }

    print "  var checkOK = '".$checkOK."';"."\r\n";
    print "  var checkStr = ".$Form.$VariableName.".value;"."\r\n";
    print "  var allValid = true;"."\r\n";
    print "  for (i = 0;  i < checkStr.length;  i++)"."\r\n";
    print "  {"."\r\n";
    print "    ch = checkStr.charAt(i);"."\r\n";
    print "    if ((checkStr.charCodeAt(i) != 13) && (checkStr.charCodeAt(i) != 10) && (checkStr.charAt(i) != '\\\\')) {"."\r\n";
    print "       for (j = 0;  j < checkOK.length;  j++) {"."\r\n";
    print "         if (ch == checkOK.charAt(j))"."\r\n";
    print "           break;"."\r\n";
    print "       } "."\r\n";
    print "       if (j == checkOK.length)"."\r\n";
    print "       {"."\r\n";
    print "         allValid = false;"."\r\n";
    print "         break;"."\r\n";
    print "       }"."\r\n";
    print "    } "."\r\n";
    print "  }"."\r\n";
    print "  if (!allValid)"."\r\n";
    print "  {"."\r\n";
    if     ($AllowLetters>"" && $AllowDigits>"")  { print "    alert('VocÍ digitou caracteres inv·lidos no campo ".$DisplayName.".');"."\r\n"; }
    elseif ($AllowLetters>"" && $AllowDigits=="") { 
      if ($AllowLetters == "1") { 
        print "    alert('Favor digitar apenas letras no campo ".$DisplayName.".');"."\r\n"; 
      } else {
        print "    alert('O campo ".$DisplayName." aceita apenas os caracteres abaixo.\\n".$AllowLetters."');"."\r\n"; 
      }
    } elseif ($AllowLetters=="" && $AllowDigits>"") { print "    alert('Favor digitar apenas n˙meros no campo ".$DisplayName.".');"."\r\n"; }

    if (upper($DataType)!="HIDDEN") { print "   ".$Form.$VariableName.".focus();"."\r\n"; }
    print "    return (false);"."\r\n";
    print "  }"."\r\n";
  }

  if (upper($DataType)=="CGC" || upper($DataType)=="CNPJ") {
    $checkOK="";
    print
    "    var allValid = true;"."\r\n".
    "    var soma = 0;"."\r\n".
    "    var D1 = 0;"."\r\n".
    "    var D2 = 0;"."\r\n".
    "    var checkStr = ".$Form.$VariableName.".value;"."\r\n".
    "    checkStr = checkStr.replace('.','');"."\r\n".
    "    checkStr = checkStr.replace('.','');"."\r\n".
    "    checkStr = checkStr.replace('.','');"."\r\n".
    "    checkStr = checkStr.replace('/','');"."\r\n".
    "    checkStr = checkStr.replace('-','');"."\r\n".
    "    for (i = 1;  i < 13;  i++)"."\r\n".
    "    {"."\r\n".
    "      if (i < 5) { soma = soma + (checkStr.charAt(i-1)*(6-i)); }"."\r\n".
    "      else { soma = soma + (checkStr.charAt(i-1)*(14-i)); }"."\r\n".
    "    }"."\r\n".
    "    D1 = modulo(soma,11)"."\r\n".
    "    if (D1 > 9) { D1 = 0}"."\r\n".
    "    soma = 0;"."\r\n".
    "    for (i = 1;  i < 14;  i++)"."\r\n".
    "    {"."\r\n".
    "      if (i < 6) { soma = soma + (checkStr.charAt(i-1)*(7-i)); }"."\r\n".
    "      else { soma = soma + (checkStr.charAt(i-1)*(15-i)); }"."\r\n".
    "    }"."\r\n".
    "    D2 = modulo(soma,11)"."\r\n".
    "    if (D2 > 9) { D2 = 0}"."\r\n".
    "    if (D1 == checkStr.charAt(13-1) && D2 == checkStr.charAt(14-1)) { allValid = true}"."\r\n".
    "    else { allValid = false }"."\r\n".
    "    if (!allValid) {"."\r\n".
    "       alert('".$DisplayName." inv·lido.');"."\r\n".
    "       ".$Form.$VariableName.".focus();"."\r\n".
    "       return (false);"."\r\n".
    "    }"."\r\n";
  }
  elseif (upper($DataType)=="CPF") {
    $checkOK="";
    print
    "    var igual = 0;"."\r\n".
    "    var allValid = true;"."\r\n".
    "    var soma = 0;"."\r\n".
    "    var D1 = 0;"."\r\n".
    "    var D2 = 0;"."\r\n".
    "    var checkStr = ".$Form.$VariableName.".value;"."\r\n".
    "    checkStr = checkStr.replace('.','');"."\r\n".
    "    checkStr = checkStr.replace('.','');"."\r\n".
    "    checkStr = checkStr.replace('-','');"."\r\n".
    "    igual = 0;"."\r\n".
    "    for (i = 1;  i < 10;  i++)"."\r\n".
    "    {"."\r\n".
    "      soma = soma + (checkStr.charAt(i-1)*(11-i));"."\r\n".
    "      if (checkStr.charAt(i) != checkStr.charAt(i-1)) igual = 1"."\r\n".
    "    }"."\r\n".
    "    if (igual == 0 && checkStr > '') {"."\r\n".
    "       alert('".$DisplayName." inv·lido.');"."\r\n".
    "       ".$Form.$VariableName.".focus();"."\r\n".
    "       return (false);"."\r\n".
    "    }"."\r\n".
    "    D1 = modulo(soma,11);"."\r\n".
    "    if (D1 > 9) { D1 = 0}"."\r\n".
    "    soma = 0;"."\r\n".
    "    for (i = 1;  i < 11;  i++)"."\r\n".
    "    {"."\r\n".
    "      soma = soma + (checkStr.charAt(i-1)*(12-i));"."\r\n".
    "    }"."\r\n".
    "    D2 = modulo(soma,11)"."\r\n".
    "    if (D2 > 9) { D2 = 0}"."\r\n".
    "    if ((D1 == checkStr.charAt(10-1)) && (D2 == checkStr.charAt(11-1))) { allValid = true}"."\r\n".
    "    else { allValid = false }"."\r\n".
    "    if (!allValid && checkStr > '') {"."\r\n".
    "       alert('".$DisplayName." inv·lido.');"."\r\n".
    "       ".$Form.$VariableName.".focus();"."\r\n".
    "       return (false);"."\r\n".
    "    }"."\r\n".
    "    if (igual == 0 && checkStr > '') {"."\r\n".
    "       alert('".$DisplayName." inv·lido.');"."\r\n".
    "       ".$Form.$VariableName.".focus();"."\r\n".
    "       return (false);"."\r\n".
    "    }"."\r\n";
  }
  elseif (upper($DataType)=="DATA") {
    print
    "    var checkStr = ".$Form.$VariableName.".value;"."\r\n".
    "    var err=0;"."\r\n".
    "    var psj=0;"."\r\n".
    "    if (checkStr.length != 0) {"."\r\n".
    "       if (!checkbranco(checkStr))"."\r\n".
    "       {"."\r\n".
    "           if (checkStr.length != 10) err=1"."\r\n".
    "           dia = checkStr.substring(0, 2);"."\r\n".
    "           barra1 = checkStr.substring(2, 3);"."\r\n".
    "           mes = checkStr.substring(3, 5);"."\r\n".
    "           barra2 = checkStr.substring(5, 6);"."\r\n".
    "            ano = checkStr.substring(6, 10);"."\r\n".
    "            //verificaÁıes b·sicas"."\r\n".
    "            if (mes<1 || mes>12) err = 1;"."\r\n".
    "            if (barra1 != '/') err = 1;"."\r\n".
    "            if (dia<1 || dia>31) err = 1;"."\r\n".
    "            if (barra2 != '/') err = 1;"."\r\n".
    "            if (ano<1900 || ano>2900) err = 1;"."\r\n".
    "            //verificaÁıes avanÁadas"."\r\n".
    "            // mÍs com 30 dias"."\r\n".
    "            if (mes==4 || mes==6 || mes==9 || mes==11){"."\r\n".
    "               if (dia==31) err=1;"."\r\n".
    "            }"."\r\n".
    "            // fevereiro e ano bissexto"."\r\n".
    "            if (mes==2){"."\r\n".
    "                var g=parseInt(ano/4);"."\r\n".
    "                if (isNaN(g)) {"."\r\n".
    "                    err=1;"."\r\n".
    "                }"."\r\n".
    "                if (dia>29) err=1;"."\r\n".
    "                if (dia==29 && ((ano/4)!=parseInt(ano/4))) err=1;"."\r\n".
    "            }"."\r\n".
    "       }"."\r\n".
    "       else"."\r\n".
    "       {"."\r\n".
    "           err=1;"."\r\n".
    "       }"."\r\n".
    "    }"."\r\n".
    "    if (err==1){"."\r\n".
    "       alert('Campo ".$DisplayName." inv·lido.');"."\r\n".
    "       ".$Form.$VariableName.".focus();"."\r\n".
    "       return (false);"."\r\n".
    "    }"."\r\n";
  } elseif (upper($DataType)=="DATAHORA") {
    print
    "    var checkStr = ".$Form.$VariableName.".value;"."\r\n".
    "    var err=0;"."\r\n".
    "    var psj=0;"."\r\n".
    "    if (checkStr.length != 0) {"."\r\n".
    "       if (!checkbranco(checkStr))"."\r\n".
    "       {"."\r\n".
    "           if (checkStr.length != 17) err=1"."\r\n".
    "           dia = checkStr.substr(0, 2);"."\r\n".
    "           barra1 = checkStr.substr(2, 1);"."\r\n".
    "           mes = checkStr.substr(3, 2);"."\r\n".
    "           barra2 = checkStr.substr(5, 1);"."\r\n".
    "            ano = checkStr.substr(6, 4);"."\r\n".
    "            hora = checkStr.substr(12, 2);"."\r\n".
    "            minuto = checkStr.substr(15, 2);"."\r\n".
    "            //verificaÁıes b·sicas"."\r\n".
    "            if (mes<1 || mes>12) err = 1;"."\r\n".
    "            if (barra1 != '/') err = 1;"."\r\n".
    "            if (dia<1 || dia>31) err = 1;"."\r\n".
    "            if (barra2 != '/') err = 1;"."\r\n".
    "            if (ano<1900 || ano>2900) err = 1;"."\r\n".
    "            if (hora<0 || hora>23) err = 1;"."\r\n".
    "            if (minuto<0 || minuto>59) err = 1;"."\r\n".
    "            //verificaÁıes avanÁadas"."\r\n".
    "            // mÍs com 30 dias"."\r\n".
    "            if (mes==4 || mes==6 || mes==9 || mes==11){"."\r\n".
    "               if (dia==31) err=1;"."\r\n".
    "            }"."\r\n".
    "            // fevereiro e ano bissexto"."\r\n".
    "            if (mes==2){"."\r\n".
    "                var g=parseInt(ano/4);"."\r\n".
    "                if (isNaN(g)) {"."\r\n".
    "                    err=1;"."\r\n".
    "                }"."\r\n".
    "                if (dia>29) err=1;"."\r\n".
    "                if (dia==29 && ((ano/4)!=parseInt(ano/4))) err=1;"."\r\n".
    "            }"."\r\n".
    "       }"."\r\n".
    "       else"."\r\n".
    "       {"."\r\n".
    "           err=1;"."\r\n".
    "       }"."\r\n".
    "    }"."\r\n".
    "    if (err==1){"."\r\n".
    "       alert('Campo ".$DisplayName." inv·lido.');"."\r\n".
    "       ".$Form.$VariableName.".focus();"."\r\n".
    "       return (false);"."\r\n".
    "    }"."\r\n";
  } elseif (upper($DataType)=="HORA" || upper($DataType)=="HORAS") {
    print
    "    var checkStr = ".$Form.$VariableName.".value;"."\r\n".
    "    var err=0;"."\r\n".
    "    var psj=0;"."\r\n".
    "    var tam=checkStr.length;"."\r\n".
    "    if (tam != 0) {"."\r\n".
    "       if (!checkbranco(checkStr))"."\r\n".
    "       {"."\r\n";
    if (upper($DataType)=="HORA") print "           if (tam != 5) err=1"."\r\n";
    print
    "            hora = checkStr.substr(0, tam-3);"."\r\n".
    "            minuto = checkStr.substr(tam-2, 2);"."\r\n".
    "            //verificaÁıes b·sicas para o tipo ".upper($DataType)."\r\n";
    if (upper($DataType)=="HORA") print "            if (hora<0 || hora>23) err = 2;"."\r\n";
    print
    "            if (minuto<0 || minuto>59) err = 3;"."\r\n".
    "       }"."\r\n".
    "       else"."\r\n".
    "       {"."\r\n".
    "           err=1;"."\r\n".
    "       }"."\r\n".
    "    }"."\r\n".
    "    if (err>0){"."\r\n".
    "       //mensagens para o tipo ".upper($DataType)."\r\n".
    "       if (err==1) alert('Campo ".$DisplayName." inv·lido.');"."\r\n";
    if (upper($DataType)=="HORA") print "       if (err==2) alert('Campo ".$DisplayName." inv·lido. Hora deve ser de 0 a 23');"."\r\n";
    print
    "       if (err==3) alert('Campo ".$DisplayName." inv·lido. Minuto deve ser de 0 a 59');"."\r\n".
    "       ".$Form.$VariableName.".focus();"."\r\n".
    "       return (false);"."\r\n".
    "    }"."\r\n";
  } elseif (upper($DataType)=="DATADM") {
    print
    "    var checkStr = ".$Form.$VariableName.".value;"."\r\n".
    "    var err=0;"."\r\n".
    "    var psj=0;"."\r\n".
    "    if (checkStr.length != 0) {"."\r\n".
    "       if (!checkbranco(checkStr)) {"."\r\n".
    "           if (checkStr.length != 5) err=1"."\r\n".
    "           dia = checkStr.substring(0, 2);"."\r\n".
    "           barra1 = checkStr.substring(2, 3);"."\r\n".
    "           mes = checkStr.substring(3, 5);"."\r\n".
    "            //verificaÁıes b·sicas"."\r\n".
    "            if (mes<1 || mes>12) err = 1;"."\r\n".
    "            if (barra1 != '/') err = 1;"."\r\n".
    "            if (dia<1 || dia>31) err = 1;"."\r\n".
    "            //verificaÁıes avanÁadas"."\r\n".
    "            // mÍs com 30 dias"."\r\n".
    "            if (mes==4 || mes==6 || mes==9 || mes==11){"."\r\n".
    "               if (dia==31) err=1;"."\r\n".
    "            } else if (mes==2) {"."\r\n".
    "               if (dia>28) err=1;"."\r\n".
    "            }"."\r\n".
    "       } else {"."\r\n".
    "         err=1;"."\r\n".
    "       }"."\r\n".
    "    }"."\r\n".
    "    if (err==1){"."\r\n".
    "       alert('Campo ".$DisplayName." inv·lido.');"."\r\n".
    "       ".$Form.$VariableName.".focus();"."\r\n".
    "       return (false);"."\r\n".
    "    }"."\r\n";
  } elseif (upper($DataType)=="DATAMA") {
    print
    "var checkStr = ".$Form.$VariableName.".value;"."\r\n".
    "var err=0;"."\r\n".
    "var psj=0;"."\r\n".
    "if (checkStr.length > 0) {"."\r\n".
    "   if (!checkbranco(checkStr))"."\r\n".
    "   {"."\r\n".
    "       if (checkStr.length != 7) err=1"."\r\n".
    "         mes = checkStr.substring(0, 2)"."\r\n".
    "         barra2 = checkStr.substring(2, 3)"."\r\n".
    "         ano = checkStr.substring(3, 7)"."\r\n".
    "         if (mes<1 || mes>12) err = 1"."\r\n".
    "         if (barra2 != '/') err = 1"."\r\n".
    "         if (ano<1900 || ano>2900) err = 1"."\r\n".
    "   }"."\r\n".
    "   else"."\r\n".
    "   {"."\r\n".
    "       err=1"."\r\n".
    "   }"."\r\n".
    "}"."\r\n".
    "if (err==1){"."\r\n".
    "   alert('Campo ".$DisplayName." inv·lido.');"."\r\n".
    "   ".$Form.$VariableName.".focus();"."\r\n".
    "   return (false);"."\r\n".
    "}"."\r\n";
  } elseif (upper($DataType)=="VALOR") {
    print "  var V1 = ".$Form.$VariableName.".value;"."\r\n"; 
    print "  V1 = V1.toString().replace(/\\$|\\./g,''); "."\r\n";
    print "  V1 = V1.toString().replace(',','.'); "."\r\n";
    print "  if (isNaN(V1)) { "."\r\n";
    print "    alert('" . $DisplayName . " n„o È um valor v·lido!.'); "."\r\n";
    print "    ".$Form.$VariableName.".focus(); "."\r\n";
    print "    return false; "."\r\n";
    print "  } "."\r\n";
  }
}
?>