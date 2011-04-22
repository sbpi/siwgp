<?php
// =========================================================================
// Rotina de validação dos dados da demanda de triagem
// -------------------------------------------------------------------------

function ValidaTriagem($l_cliente,$l_chave,$l_sg1,$l_sg2,$l_sg3,$l_sg4,$l_sg_tramite) {
  extract($GLOBALS);
  // Se não encontrar erro, esta função retorna cadeia fazia.
  // Se o retorno for diferente de cadeia vazia, o primeiro byte indica o tipo de erro
  // 0 - Erro de integridade. Nem gestores podem encaminhar a solicitação
  // 1 - Erro de regra de negócio. Apenas gestores podem encaminhar a solicitação
  // 2 - Alerta. O sistema indica uma situação não desejável mas permite que o usuário
  //     encaminhe o projeto
  //-----------------------------------------------------------------------------------
  // Cria recordsets e variáveis de trabalho.
  // l_rs1 até l_rs4 são recordsets que podem ser usados para armazenar dados de blocos
  // de dados específicos da solicitação que está sendo validada.
  //-----------------------------------------------------------------------------------

  //-----------------------------------------------------------------------------------
  // Esta primeira parte carrega recordsets com os diferentes blocos de dados que
  // compõem a solicitação
  //-----------------------------------------------------------------------------------
  // Recupera os dados da solicitação
  $sql = new db_getSolicData; $l_rs_solic = $sql->getInstanceOf($dbms,$l_chave,$l_sg1);
  // Se a solicitação informada não existir, abandona a execução
  if (count($l_rs_solic)==0) {
    return '0<li>Não existe registro no banco de dados com o número informado.';
  } 
  $l_erro='';
  $l_tipo='';  

  // Este bloco faz verificações em solicitações que estão em fases posteriores ao
  // cadastramento inicial
  //Este bloco faz a verificação dos dados de triagem na fase de análise
  if ($l_sg_tramite=='EA') {
    if(nvl(f($l_rs_solic,'sq_solic_pai'),'')=='' && nvl(f($l_rs_solic,'sq_cc'),'')=='') {
      $l_erro=$l_erro.'<li>Os dados de triagem não foram informados.';
      $l_tipo=0;
    }
    if($l_erro!='') 
      $l_erro=$l_erro.'<li>Os dados de triagem devem ser informados na operação <b>IN</b>.';
  } 
  //Este bloco faz a verificação dos dados de execução na fase de execuçã
  if ($l_sg_tramite=='EE') {
    if(nvl(f($l_rs_solic,'inicio'),'')=='' || nvl(f($l_rs_solic,'fim'),'')=='' || nvl(f($l_rs_solic,'valor'),'')=='') {
      $l_erro=$l_erro.'<li>Os dados de execuçao não foram informados';
      $l_tipo=0;
    }
    if($l_erro!='') 
      $l_erro=$l_erro.'<li>Os dados de execução devem ser informados na operação <b>IN</b>';
  } 
  
  // Configura a variável de retorno com o tipo de erro e a mensagem
  $l_erro = $l_tipo.$l_erro;
  //-----------------------------------------------------------------------------------
  // Após as verificações feitas, devolve cadeia vazia se não encontrou erros, ou string
  // para ser usada com a tag <UL>.
  //-----------------------------------------------------------------------------------

  return $l_erro;
}
?>
