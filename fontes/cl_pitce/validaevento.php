<?php 
// =========================================================================
// Rotina de valida��o das solicita��es de recursos log�sticos
// -------------------------------------------------------------------------
function ValidaGeral($p_cliente,$l_chave,$p_sg1,$p_sg2,$p_sg3,$p_sg4,$p_tramite) {
  extract($GLOBALS);
  // Se n�o encontrar erro, esta fun��o retorna cadeia fazia.
  // Se o retorno for diferente de cadeia vazia, o primeiro byte indica o tipo de erro
  // 0 - Erro de integridade. Nem gestores podem encaminhar a solicita��o
  // 1 - Erro de regra de neg�cio. Apenas gestores podem encaminhar a solicita��o
  // 2 - Alerta. O sistema indica uma situa��o n�o desej�vel mas permite que o usu�rio
  //     encaminhe o lan�amento
  //-----------------------------------------------------------------------------------
  // Cria recordsets e vari�veis de trabalho.
  // l_rs1 at� l_rs4 s�o recordsets que podem ser usados para armazenar dados de blocos
  // de dados espec�ficos da solicita��o que est� sendo validada.
  //-----------------------------------------------------------------------------------
  // Esta primeira parte carrega recordsets com os diferentes blocos de dados que
  // comp�em a solicita��o
  //-----------------------------------------------------------------------------------
  // Recupera os dados da solicita��o
  $sql = new db_getSolicData; $l_rs_solic = $sql->getInstanceOf($dbms,$l_chave,$p_sg1);
  //-----------------------------------------------------------------------------
  // Verifica��es de integridade de dados da solicita��o, feitas sempre que houver
  // um encaminhamento.
  //-----------------------------------------------------------------------------
  // Se a solicita��o informada n�o existir, abandona a execu��o
  if (count($l_rs_solic)<=0) {
    return '0<li>N�o existe registro no banco de dados com o n�mero informado.';
  } 
  // Verifica se o cliente tem o m�dulo financeiro contratado
  $sql = new db_getSiwCliModLis; $l_rs_modulo = $sql->getInstanceOf($dbms,$p_cliente,null,'FN');
  if (count($l_rs_modulo)<=0) $l_financeiro='S'; else $l_financeiro='N';
  $l_erro='';
  $l_tipo='';
  // Recupera o tr�mite atual da solicita��o
  $sql = new db_getTramiteData; $l_rs_tramite = $sql->getInstanceOf($dbms,f($l_rs_solic,'sq_siw_tramite'));


  $l_erro=$l_tipo.$l_erro;
  //-----------------------------------------------------------------------------------
  // Ap�s as verifica��es feitas, devolve cadeia vazia se n�o encontrou erros, ou string
  // para ser usada com a tag <UL>.
  //-----------------------------------------------------------------------------------
  return $l_erro;
} 
?>