<?php
header('Expires: ' . -1500);
$session = $_REQUEST['sid'];
session_id($session); // reestabele-ce a sessуo
session_start();

$w_dir_volta = '../';
require_once($w_dir_volta . 'funcoes.php');
include_once($w_dir_volta . 'classes/db/abreSessao.php');

// =========================================================================
// Retorna valores nulos se chegar cadeia vazia
// -------------------------------------------------------------------------
/*
  Uploadify v2.1.0
  Release Date: August 24, 2009

  Copyright (c) 2009 Ronnie Garcia, Travis Nickels

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.
 */
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_REQUEST['dbms']);
$w_caminho  = $_REQUEST['w_caminho'];
$w_chave    = $_REQUEST['w_chave'];
$w_tipo_reg = $_REQUEST['w_tipo_reg'];
$w_cliente  = $_REQUEST['w_cliente'];


if (!empty($_FILES)) {
  include_once($w_dir_volta . 'classes/mimetype/class.mime.php');
  
  $tempFile = $_FILES['Filedata']['tmp_name'];
  $w_file = str_replace('.tmp','',basename($_FILES['Filedata']['tmp_name']));
  if (strpos($_FILES['Filedata']['name'],'.')!==false) {
    $w_file = $w_file.substr($_FILES['Filedata']['name'],(strrpos($_FILES['Filedata']['name'],'.') ? strrpos($_FILES['Filedata']['name'],'.')+1 : 0)-1,10);
  }
  $mime = new MIMETypes();
  $w_tipo = $mime->getMimeType($tempFile);
  $targetPath = $w_caminho . '/';
  $targetFile = str_replace('//', '/', $targetPath) . $w_file; //utf8_decode(str_replace(" ", "_", $_FILES['Filedata']['name']));
  $w_tamanho = $_FILES['Filedata']['size'];
  $w_nome = str_replace(" ", "_", utf8_decode($_FILES['Filedata']['name']));
  if (move_uploaded_file($tempFile, $targetFile)) {
    //Criar aчуo de sucesso
  }
  echo "1";


  // } else {
  //   echo 'Invalid file type.';
  // }
}
?>