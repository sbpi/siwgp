<?php
session_start();
var_dump($_SESSION);
extract($GLOBALS);
$w_dir_volta = '../../';
require_once($w_dir_volta . 'funcoes.php');
include_once($w_dir_volta . 'classes/db/abreSessao.php');
include_once($w_dir_volta . 'classes/uploadify/uploadify.php');

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
$w_caminho = $_REQUEST['w_caminho'];
$w_chave = $_REQUEST['w_chave'];
$w_cliente = $_REQUEST['w_cliente'];


if (!empty($_FILES)) {
  $tempFile = $_FILES['Filedata']['tmp_name'];
  $targetPath = $w_caminho . '/';
  $targetFile = str_replace('//', '/', $targetPath) . $_FILES['Filedata']['name'];
  $w_tamanho = $_FILES['Filedata']['size'];
  $w_nome = $_FILES['Filedata']['name'];
  $w_tipo = $_FILES['Filedata']['type'];

  // $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
  // $fileTypes  = str_replace(';','|',$fileTypes);
  // $typesArray = explode('\|',$fileTypes);
  // $fileParts  = pathinfo($_FILES['Filedata']['name']);
  // if (in_array($fileParts['extension'],$typesArray)) {
  // Uncomment the following line if you want to make the directory if it doesn't exist
  // mkdir(str_replace('//','/',$targetPath), 0755, true);
  move_uploaded_file($tempFile, $targetFile);
  echo "1";

  // } else {
  //   echo 'Invalid file type.';
  // }
}
?>
<!--

$w_dir_volta = '../../';
include_once($w_dir_volta . 'classes/db/abreSessao.php');
include_once($w_dir_volta . 'classes/sp/dml_putSolicRelAnexo.php');
include_once($w_dir_volta . 'funcoes.php');
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
// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_REQUEST['dbms']);
$w_caminho = $_REQUEST['w_caminho'];
$w_chave = $_REQUEST['w_chave'];
$w_cliente = $_REQUEST['w_cliente'];

if (!empty($_FILES) && $w_caminho) {
  $tempFile = $_FILES['Filedata']['tmp_name'];
  $targetPath = $w_caminho . '/';
  $targetFile = str_replace('//', '/', $targetPath) . $_FILES['Filedata']['name'];
  $w_tamanho = $_FILES['Filedata']['size'];
  $w_nome = $_FILES['Filedata']['name'];
  $w_tipo = $_FILES['Filedata']['type'];

  // $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
  // $fileTypes  = str_replace(';','|',$fileTypes);
  // $typesArray = explode('\|',$fileTypes);
  // $fileParts  = pathinfo($_FILES['Filedata']['name']);
  // if (in_array($fileParts['extension'],$typesArray)) {
  // Uncomment the following line if you want to make the directory if it doesn't exist
  // mkdir(str_replace('//','/',$targetPath), 0755, true);
  move_uploaded_file($tempFile, $targetFile);
  $SQL = new dml_putSolicRelAnexo; $SQL->getInstanceOf($dbms, 'I', $w_cliente, $w_chave, $_REQUEST['w_chave_aux'], $w_nome, null, $w_file, $w_tamanho, $w_tipo, $w_nome);
  echo "1";

  // } else {
  //   echo 'Invalid file type.';
  // }
}
-->