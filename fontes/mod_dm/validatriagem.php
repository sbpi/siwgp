<?php
// =========================================================================
// Rotina de valida��o dos dados da demanda de triagem
// -------------------------------------------------------------------------

function ValidaTriagem($l_cliente,$l_chave,$l_sg1,$l_sg2,$l_sg3,$l_sg4,$l_sg_tramite) {
  extract($GLOBALS);
  // Se n�o encontrar erro, esta fun��o retorna cadeia fazia.
  // Se o retorno for diferente de cadeia vazia, o primeiro byte indica o tipo de erro
  // 0 - Erro de integridade. Nem gestores podem encaminhar a solicita��o
  // 1 - Erro de regra de neg�cio. Apenas gestores podem encaminhar a solicita��o
  // 2 - Alerta. O sistema indica uma situa��o n�o desej�vel mas permite que o usu�rio
  //     encaminhe o projeto
  //-----------------------------------------------------------------------------------
  // Cria recordsets e vari�veis de trabalho.
  // l_rs1 at� l_rs4 s�o recordsets que podem ser usados para armazenar dados de blocos
  // de dados espec�ficos da solicita��o que est� sendo validada.
  //-----------------------------------------------------------------------------------

  //-----------------------------------------------------------------------------------
  // Esta primeira parte carrega recordsets com os diferentes blocos de dados que
  // comp�em a solicita��o
  //-----------------------------------------------------------------------------------
  // Recupera os dados da solicita��o
  $sql = new db_getSolicData; $l_rs_solic = $sql->getInstanceOf($dbms,$l_chave,$l_sg1);
  // Se a solicita��o informada n�o existir, abandona a execu��o
  if (count($l_rs_solic)==0) {
    return '0<li>N�o existe registro no banco de dados com o n�mero informado.';
  } 
  $l_erro='';
  $l_tipo='';  

  // Este bloco faz verifica��es em solicita��es que est�o em fases posteriores ao
  // cadastramento inicial
  //Este bloco faz a verifica��o dos dados de triagem na fase de an�lise
  if ($l_sg_tramite=='EA') {
    if(nvl(f($l_rs_solic,'sq_solic_pai'),'')=='' && nvl(f($l_rs_solic,'sq_cc'),'')=='') {
      $l_erro=$l_erro.'<li>Os dados de triagem n�o foram informados.';
      $l_tipo=0;
    }
    if($l_erro!='') 
      $l_erro=$l_erro.'<li>Os dados de triagem devem ser informados na opera��o <b>IN</b>.';
  } 
  //Este bloco faz a verifica��o dos dados de execu��o na fase de execu��
  if ($l_sg_tramite=='EE') {
    if(nvl(f($l_rs_solic,'inicio'),'')=='' || nvl(f($l_rs_solic,'fim'),'')=='' || nvl(f($l_rs_solic,'valor'),'')=='') {
      $l_erro=$l_erro.'<li>Os dados de execu�ao n�o foram informados';
      $l_tipo=0;
    }
    if($l_erro!='') 
      $l_erro=$l_erro.'<li>Os dados de execu��o devem ser informados na opera��o <b>IN</b>';
  } 
  
  // Configura a vari�vel de retorno com o tipo de erro e a mensagem
  $l_erro = $l_tipo.$l_erro;
  //-----------------------------------------------------------------------------------
  // Ap�s as verifica��es feitas, devolve cadeia vazia se n�o encontrou erros, ou string
  // para ser usada com a tag <UL>.
  //-----------------------------------------------------------------------------------

  return $l_erro;
}
?>
