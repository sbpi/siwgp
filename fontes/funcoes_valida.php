<?php 
// FunÁ„o para comparaÁ„o de datas

function fCompData($Date1,$DisplayName1,$Operator,$Date2,$DisplayName2) {
  extract($GLOBALS);
  $w_erro='';
  if (strlen($Date1)>0 && strlen($Date2)>0) {
    if (strlen($Date1)==17) {
      $d1=substr($Date1,0,2);
      $m1=substr($Date1,3,2);
      $a1=substr($Date1,6,4);
      $h1=substr($Date1,12,2).substr($Date1,15,2);
      $d2=substr($Date2,0,2);
      $m2=substr($Date2,3,2);
      $a2=substr($Date2,6,4);
      $h2=substr($Date2,12,2).substr($Date2,15,2);
      $data1=$a1.$m1.$d1.$h1;
      $data2=$a2.$m2.$d2.$h2;
    } elseif (strlen($Date1)==10) {
      $d1=substr($Date1,0,2);
      $m1=substr($Date1,3,2);
      $a1=substr($Date1,6,4);
      $d2=substr($Date2,0,2);
      $m2=substr($Date2,3,2);
      $a2=substr($Date2,6,4);
      $data1=$a1.$m1.$d1;
      $data2=$a2.$m2.$d2;
    } elseif (strlen($Date1)==7) {
      $d1='01';
      $m1=substr($Date1,0,2);
      $a1=substr($Date1,3,4);
      $d2='01';
      $m2=substr($Date2,0,2);
      $a2=substr($Date2,3,4);
      $data1=$a1.$m1.$d1;
      $data2=$a2.$m2.$d2;
    } 
    switch ($Operator) {
      case '=':     if (!($data1==$data2))  $w_erro=$DisplayName1.' deve ser igual a '.$DisplayName2;           break;
      case '<>':    if (!($data1!=$data2))  $w_erro=$DisplayName1.' deve ser diferente de '.$DisplayName2;      break;
      case '>':     if (!($data1>$data2))   $w_erro=$DisplayName1.' deve ser maior que '.$DisplayName2;         break;
      case '<':     if (!($data1<$data2))   $w_erro=$DisplayName1.' deve ser menor que '.$DisplayName2;         break;
      case '>=':    if (!($data1>=$data2))  $w_erro=$DisplayName1.' deve ser maior ou igual a '.$DisplayName2;  break;
      case '=>':    if (!($data1>=$data2))  $w_erro=$DisplayName1.' deve ser maior ou igual a '.$DisplayName2;  break;
      case '<=':    if (!($data1<=$data2))  $w_erro=$DisplayName1.' deve ser menor ou igual a '.$DisplayName2;  break;
      case '=<':    if (!($data1<=$data2))  $w_erro=$DisplayName1.' deve ser menor ou igual a '.$DisplayName2;break;
    } 
  } 
  return $w_erro;
} 
// FunÁ„o auxiliar para verificaÁ„o de datas
function fcheckbranco($elemento) {
  extract($GLOBALS);
  $flagbranco = true;
  for ($i=1; $i<=strlen($elemento); $i=$i+1) {
    if (substr($elemento,$i-1)!=' ') $flagbranco = false;
  } 
  $fcheckbranco = $flagbranco;
  $flagbranco   = null;
  $i            = null;

  return $fcheckbranco;
} 
// FunÁ„o para c·lculo de mÛdulo
function fModulo($dividendo,$divisor) {
  extract($GLOBALS);
  $quociente    = 0;
  $ModN         = 0;
  $quociente    = $Fix[$dividendo/$divisor];
  $ModN         = $dividendo-($divisor*$quociente);
  $fModulo      = $divisor-$ModN;
  $quociente    = null;
  $ModN         = null;
  return $fModulo;
} 
function fValidate($Tipo,$Value,$DisplayName,$DataType,$ValueRequired,$MinimumLength,$MaximumLength,$AllowLetters,$AllowDigits) {
  extract($GLOBALS);
  // Tipo = 0 -> retorna o erro assim que encontr·-lo. O retorno conter· o primeiro erro encontrado.
  // Tipo = 1 -> retorna o erro somente no final da rotina. O retorno conter· todos os erros encontrados.
  $w_erro='';
  if ($ValueRequired>'') {
    if (Nvl($Value,'nulo')=='nulo') $w_erro='; campo obrigatÛrio deve conter valor';
  } 
  if ($w_erro>'' && $Tipo==0) return str_replace('; ','',$w_erro);
  if ($MinimumLength>'') {
    if (strlen($Value)<$MinimumLength && $Value>'') $w_erro=$w_erro.'; tamanho mÌnimo È de '.$MinimumLength.' posiÁıes';
  } 
  if ($w_erro>'' && $Tipo==0) return  str_replace('; ','',$w_erro);
  if ($MaximumLength>'') {
    if (strlen($Value)>$MaximumLength && $Value>'') $w_erro=$w_erro.'; tamanho m·ximo È de '.$MaximumLength.' posiÁıes';
  } 
  if ($w_erro>'' && $Tipo==0) return str_replace('; ','',$w_erro);
  if ($AllowLetters>'' || $AllowDigits>'') {
    $checkOK='';
    if ($AllowLetters>'') {
      if ($AllowLetters=='1')   $checkOK='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz¿¡¬√«»… ÃÕŒ“”‘’Ÿ⁄€‹‡·‚„ÁÈÍÌÓÛÙı˙˚0123456789-,.()-:;[]{}*&%$#@!/∫™?<>|+=_\""\'¥ ';
      else                      $checkOK=$checkOK.$AllowLetters;
    } 
    if ($AllowDigits>'') {
      if ($AllowDigits=='1')    $checkOK=$checkOK.'0123456789-.,/: ';
      else                      $checkOK=$checkOK.$AllowDigits;
    } 
    for ($i=1; $i<=strlen($Value); $i=$i+1) {
      $ch=substr($Value,$i-1,1);
      if ((strpos($checkOK,$ch)===false) && ord($ch)!=13 && ord($ch)!=10) {
        $w_erro=$w_erro.'; caracteres inv·lidos no campo';
        break;
      } 
    } 
  } 
  if ($w_erro>'' && $Tipo==0) return str_replace('; ','',$w_erro);
  if (upper($DataType)=='CGC' || upper($DataType)=='CNPJ') {
    $checkOK='';
    $allValid=true;
    $D1=0;
    $D2=0;
    $checkStr='0'.str_replace('-','',str_replace('/','',str_replace('.','',$Value))) ;
    $soma=0;
    for ($i=1; $i<=12; $i++) {
      if ($i<5) $mult = 6;
      else      $mult = 14;
      $soma+=(substr($checkStr,$i,1)*($mult-$i));
    }
    $D1 = 11 - ($soma % 11);
    if (($D1>9)) $D1=0;
    $soma=0;
    for ($i=1; $i<=13; $i++) {
      if ($i<6)  $mult = 7;
      else       $mult = 15;
      $soma+=(substr($checkStr,$i,1)*($mult-$i));
    } 
    $D2 = 11 - ($soma % 11);
    if (($D2>9)) $D2=0;
    if ($D1!=substr($checkStr,13,1)||$D2!=substr($checkStr,14,1))   $w_erro=$w_erro.'; dÌgito verificador inv·lido';
    if ($w_erro>'' && $Tipo==0)   return str_replace('; ','',$w_erro);
  } elseif (upper($DataType)=='CPF') {
    $checkOK    = '';
    $allValid   = true;
    $D1         = 0;
    $D2         = 0;
    $checkStr   = str_replace('-','',str_replace('.','',$Value));
    $soma       = 0;
    $igual      = 0;
    for ($i=2; $i<=10; $i=$i+1) {
      $soma=$soma+(substr($checkStr,$i-1-1,1)*(12-$i));
      if (substr($checkStr,$i-1,1)!=substr($checkStr,$i-1-1,1)) $igual=1;
    } 
    $D1 = ($soma % 11);
    if (($D1>9))    $D1=0;
    if ($igual==0)  $w_erro=$w_erro.'; CPF inv·lido';
    if ($w_erro>'' && $Tipo==0) return str_replace('; ','',$w_erro);
    $soma=0;
    for ($i=3; $i<=11; $i=$i+1) {
      $soma = $soma+(substr($checkStr,$i-1-1,1)*(13-$i));
      if (substr($checkStr,$i-1,1)!=substr($checkStr,$i-1-1,1)) $igual=1;
    } 
    $D2=($soma % 11);
    if (($D2>9)) $D1=0;
    if ($D1!=substr($checkStr,9,1) || $D2!=substr($checkStr,10,1))  $w_erro=$w_erro.'; dÌgito verificador inv·lido';
    if ($w_erro>'' && $Tipo==0) return str_replace('; ','',$w_erro);
  } elseif (upper($DataType)=='DATA') {
    $err=0;
    $psj=0;
    if (strlen($Value)>0) {
      if (!fcheckbranco($Value)) {
        if (strlen($Value)!=10) {
          $err=1;
        } else {
          $dia=substr($Value,0,2);
          $barra1=substr($Value,2,1);
          $mes=substr($Value,3,2);
          $barra2=substr($Value,5,1);
          $ano=substr($Value,6,4);
          // verificaÁıes b·sicas
          if (($mes<1||$mes>12)||($barra1!='/')||($dia<1||$dia>31)||($barra2!='/')||($ano<1900 || $ano>2900)) {
            $err=1;
          } 
          // verificaÁıes avanÁadas
          // mÍs com 30 dias
          if (($mes==4 || $mes==6 || $mes==9 || $mes==11) && ($dia==31)) $err=1;
          // fevereiro e ano bissexto
          if (($mes==2)) {
            if (intval($ano/4)==($ano/4)) {
              if (($dia>29)) $err=1;
            } else {
              if (($dia>28)) $err=1;
            } 
          } 
        } 
      } else {
        $err=1;
      } 
    } 
    if ($err==1) $w_erro = $w_erro.'; data inv·lida';
    if ($w_erro>'' && $Tipo==0) return str_replace('; ','',$w_erro);
  } elseif (upper($DataType)=='DATADM') {
    $err=0;
    $psj=0;
    if (strlen($Value)>0) {
      if (!fcheckbranco($Value)) {
        if (strlen($Value)!=5) {
          $w_erro=1;
        } else {
          $dia=substr($Value,0,2);
          $barra1=substr($Value,2,1);
          $mes=substr($Value,3,2);
          // verificaÁıes b·sicas
          if (($mes<1||$mes>12)||($barra1!='/')||($dia<1||$dia>31)) $err=1;
          // verificaÁıes avanÁadas
          // mÍs com 30 dias
          if (($mes==4 || $mes==6 || $mes==9 || $mes==11) && ($dia==31)) $err=1;
          // fevereiro - como o ano n„o È informado, fevereiro sÛ pode ter 28 dias
          if (($mes==2 && $dia>28)) $err=1;
        } 
      } else $err=1;
    } 
    if ($err==1) $w_erro=$w_erro.'; data inv·lida';
    if ($w_erro>'' && $Tipo==0) return str_replace('; ','',$w_erro);
  } elseif (upper($DataType)=='DATAMA') {
    $err=0;
    $psj=0;
    if (strlen($Value)>0) {
      if (!fcheckbranco($Value)) {
        if (strlen($Value)!=7) {
          $w_erro=1;
        } else {
          $mes=substr($Value,0,2);
          $barra1=substr($Value,2,1);
          $ano=substr($Value,3,4);
          // verificaÁıes b·sicas
          if (($mes<1||$mes>12)||($barra1!='/')||($ano<1900 || $ano>2900)) $err=1;
        } 
      } else {
        $err=1;
      } 
    } 
    if ($err==1) $w_erro=$w_erro.'; data inv·lida';
    if ($w_erro>'' && $Tipo==0) return str_replace('; ','',$w_erro);
  } 
  $fValidate = substr($w_erro,2,strlen($w_erro));
  return $fValidate;
} 
?>