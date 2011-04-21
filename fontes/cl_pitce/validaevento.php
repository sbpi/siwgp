<?php 
// =========================================================================
// Rotina de validação das solicitações de recursos logísticos
// -------------------------------------------------------------------------
function ValidaGeral($p_cliente,$l_chave,$p_sg1,$p_sg2,$p_sg3,$p_sg4,$p_tramite) {
  extract($GLOBALS);
  // Se não encontrar erro, esta função retorna cadeia fazia.
  // Se o retorno for diferente de cadeia vazia, o primeiro byte indica o tipo de erro
  // 0 - Erro de integridade. Nem gestores podem encaminhar a solicitação
  // 1 - Erro de regra de negócio. Apenas gestores podem encaminhar a solicitação
  // 2 - Alerta. O sistema indica uma situação não desejável mas permite que o usuário
  //     encaminhe o lançamento
  //-----------------------------------------------------------------------------------
  // Cria recordsets e variáveis de trabalho.
  // l_rs1 até l_rs4 são recordsets que podem ser usados para armazenar dados de blocos
  // de dados específicos da solicitação que está sendo validada.
  //-----------------------------------------------------------------------------------
  // Esta primeira parte carrega recordsets com os diferentes blocos de dados que
  // compõem a solicitação
  //-----------------------------------------------------------------------------------
  // Recupera os dados da solicitação
  $sql = new db_getSolicData; $l_rs_solic = $sql->getInstanceOf($dbms,$l_chave,$p_sg1);
  //-----------------------------------------------------------------------------
  // Verificações de integridade de dados da solicitação, feitas sempre que houver
  // um encaminhamento.
  //-----------------------------------------------------------------------------
  // Se a solicitação informada não existir, abandona a execução
  if (count($l_rs_solic)<=0) {
    return '0<li>Não existe registro no banco de dados com o número informado.';
  } 
  // Verifica se o cliente tem o módulo financeiro contratado
  $sql = new db_getSiwCliModLis; $l_rs_modulo = $sql->getInstanceOf($dbms,$p_cliente,null,'FN');
  if (count($l_rs_modulo)<=0) $l_financeiro='S'; else $l_financeiro='N';
  $l_erro='';
  $l_tipo='';
  // Recupera o trâmite atual da solicitação
  $sql = new db_getTramiteData; $l_rs_tramite = $sql->getInstanceOf($dbms,f($l_rs_solic,'sq_siw_tramite'));


  $l_erro=$l_tipo.$l_erro;
  //-----------------------------------------------------------------------------------
  // Após as verificações feitas, devolve cadeia vazia se não encontrou erros, ou string
  // para ser usada com a tag <UL>.
  //-----------------------------------------------------------------------------------
  return $l_erro;
} 
?>