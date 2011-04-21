<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_putSiwCliConf
*
* { Description :- 
*    Manipula registros de siw_cliente
* }
*/

class dml_putSiwCliConf {
   function getInstanceOf($dbms, $chave, $tamanho_minimo_senha, $tamanho_maximo_senha, $maximo_tentativas,
        $dias_vigencia_senha, $dias_aviso_expiracao, $smtp_server, $siw_email_nome, $siw_email_conta,
        $siw_email_senha, $logo, $logo1, $fundo, $tipo, $upload_maximo ,
        $ad_account_sufix,$ad_base_dn,$ad_domain_controllers,
        $ol_account_sufix,$ol_base_dn,$ol_domain_controllers,
        $p_sl_server, $p_sl_protocol, $p_sl_port, $p_sl_facility ,$p_sl_base_dn ,$p_sl_timeout ,$p_sl_pass_ok,
        $p_sl_pass_er, $p_sl_sign_er, $p_sl_write_ok, $p_sl_write_er, $p_sl_res_er
        ) {
              
          
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSiwCliConf';
     $params=array('chave'                  =>array($chave,                 B_NUMERIC,     32),
                   'tamanho_minimo_senha'   =>array($tamanho_minimo_senha,  B_NUMERIC,     32),
                   'tamanho_maximo_senha'   =>array($tamanho_maximo_senha,  B_NUMERIC,     32),
                   'maximo_tentativas'      =>array($maximo_tentativas,     B_NUMERIC,     32),
                   'dias_vigencia_senha'    =>array($dias_vigencia_senha,   B_NUMERIC,     32),
                   'dias_aviso_expiracao'   =>array($dias_aviso_expiracao,  B_NUMERIC,     32),
                   'smtp_server'            =>array($smtp_server,           B_VARCHAR,     60),
                   'siw_email_nome'         =>array($siw_email_nome,        B_VARCHAR,     60),
                   'siw_email_conta'        =>array($siw_email_conta,       B_VARCHAR,     60),
                   'siw_email_senha'        =>array($siw_email_senha,       B_VARCHAR,     60),
                   'logo'                   =>array($logo,                  B_VARCHAR,     60),
                   'logo1'                  =>array($logo1,                 B_VARCHAR,     60),
                   'fundo'                  =>array($fundo,                 B_VARCHAR,     60),
                   'tipo'                   =>array($tipo,                  B_VARCHAR,     15),
                   'upload_maximo'          =>array($upload_maximo,         B_NUMERIC,     32),
                   
                   'ad_account_sufix'       =>array($ad_account_sufix,      B_VARCHAR,     40),
                   'ad_base_dn'             =>array($ad_base_dn,            B_VARCHAR,     40),
                   'ad_domain_controlers'   =>array($ad_domain_controllers, B_VARCHAR,     40),
                   
                   'ol_account_sufix'       =>array($ol_account_sufix,      B_VARCHAR,     40),
                   'ol_base_dn'             =>array($ol_base_dn,            B_VARCHAR,     40),
                   'ol_domain_controlers'   =>array($ol_domain_controllers, B_VARCHAR,     40),
                                     
                   'p_sl_server'            =>array($p_sl_server,           B_VARCHAR,     30),
                   'p_sl_protocol'          =>array($p_sl_protocol,         B_VARCHAR,     10),
                   'p_sl_port'              =>array($p_sl_port,             B_VARCHAR,      5),
                   'p_sl_facility'          =>array($p_sl_facility,         B_NUMERIC,     32),
                   'p_sl_base_dn'           =>array($p_sl_base_dn,          B_VARCHAR,     40),
                   'p_sl_timeout'           =>array($p_sl_timeout,          B_NUMERIC,     32),
                   'p_sl_pass_ok'           =>array($p_sl_pass_ok,          B_NUMERIC,     32),
                   'p_sl_pass_er'           =>array($p_sl_pass_er,          B_NUMERIC,     32),
                   'p_sl_sign_er'           =>array($p_sl_sign_er,          B_NUMERIC,     32),
                   'p_sl_write_ok'          =>array($p_sl_write_ok,         B_NUMERIC,     32),
                   'p_sl_write_er'          =>array($p_sl_write_er,         B_NUMERIC,     32),
                   'p_sl_res_er'            =>array($p_sl_res_er,           B_NUMERIC,     32)
     );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); 
     
     error_reporting(0); 
     if(!$l_rs->executeQuery()) { 
       error_reporting($l_error_reporting); 
       TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); 
     } else {
       error_reporting($l_error_reporting); 
       return true;
     }
   }
}
?>
