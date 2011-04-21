create or replace procedure SP_PutSIWCliConf
   (p_chave                    in  number,
    p_tamanho_minimo_senha     in  number   default null,
    p_tamanho_maximo_senha     in  number   default null,
    p_maximo_tentativas        in  number   default null,
    p_dias_vigencia_senha      in  number   default null,
    p_dias_aviso_expiracao     in  number   default null,
    p_smtp_server              in varchar2  default null,
    p_siw_email_nome           in varchar2  default null,
    p_siw_email_conta          in varchar2  default null,
    p_siw_email_senha          in varchar2  default null,
    p_logo                     in varchar2  default null,
    p_logo1                    in varchar2  default null,
    p_fundo                    in varchar2  default null,
    p_tipo                     in varchar2  default null,
    p_upload_maximo            in number    default null,
    p_ad_account_sufix         in varchar2  default null,
    p_ad_base_dn               in varchar2  default null,
    p_ad_domain_controlers     in varchar2  default null,
    p_ol_account_sufix         in varchar2  default null,
    p_ol_base_dn               in varchar2  default null,
    p_ol_domain_controlers     in varchar2  default null,
    p_sl_server                in varchar2  default null,
    p_sl_protocol              in varchar2  default null,
    p_sl_port                  in number    default null,
    p_sl_facility              in number    default null,
    p_sl_base_dn               in varchar2  default null,
    p_sl_timeout               in number    default null,
    p_sl_pass_ok               in number    default null,
    p_sl_pass_er               in number    default null,
    p_sl_sign_er               in number    default null,
    p_sl_write_ok              in number    default null,
    p_sl_write_er              in number    default null,
    p_sl_res_er                in number    default null

   ) is
begin
   If p_Tipo = 'AUTENTICACAO' Then
      -- Altera dados relativos à autenticação de usuários
      update siw_cliente set
         tamanho_min_senha    = p_tamanho_minimo_senha,
         tamanho_max_senha    = p_tamanho_maximo_senha,
         maximo_tentativas    = p_maximo_tentativas,
         dias_vig_senha       = p_dias_vigencia_senha,
         dias_aviso_expir     = p_dias_aviso_expiracao
      where sq_pessoa         = p_chave;
   Elsif p_Tipo = 'SERVIDOR' Then
      -- Altera dados relativos ao serviço de SMTP e imagens do cliente
      update siw_cliente set
         smtp_server            = p_smtp_server,
         siw_email_nome         = p_siw_email_nome,
         siw_email_conta        = p_siw_email_conta,
         siw_email_senha        = Nvl(p_siw_email_senha, siw_email_senha),
         logo                   = Nvl(p_logo, logo),
         logo1                  = Nvl(p_logo1, logo1),
         fundo                  = Nvl(p_fundo, fundo),
         upload_maximo          = p_upload_maximo,

         ad_account_sufix       = p_ad_account_sufix,
         ad_base_dn             = p_ad_base_dn,
         ad_domain_controlers   = p_ad_domain_controlers,
         ol_account_sufix       = p_ol_account_sufix,
         ol_base_dn             = p_ol_base_dn,
         ol_domain_controlers   = p_ol_domain_controlers,
         syslog_server_name     = p_sl_server,
         syslog_server_protocol = p_sl_protocol,
         syslog_server_port     = p_sl_port,
         syslog_facility        = p_sl_facility,
         syslog_fqdn            = p_sl_base_dn,
         syslog_timeout         = p_sl_timeout,
         syslog_level_pass_ok   = p_sl_pass_ok,
         syslog_level_pass_er   = p_sl_pass_er,
         syslog_level_sign_er   = p_sl_sign_er,
         syslog_level_write_ok  = p_sl_write_ok,
         syslog_level_write_er  = p_sl_write_er,
         syslog_level_res_er    = p_sl_res_er

      where sq_pessoa = p_chave;
   End If;
end SP_PutSIWCliConf;
/

