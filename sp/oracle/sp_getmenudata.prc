create or replace procedure SP_GetMenuData
   (p_sq_menu   in  number,
    p_result    out sys_refcursor
   ) is
begin
   -- Recupera os dados de uma opção do menu
   open p_result for
      select a.sq_menu, a.sq_modulo, a.sq_pessoa, a.sq_menu_pai, a.nome, a.link, a.tramite, a.ordem, a.ultimo_nivel,
             a.p1, a.p2, a.p3, a.p4, a.sigla, a.imagem, a.descentralizado, a.externo, a.target, a.ativo, a.acesso_geral,
             a.consulta_geral, a.como_funciona, a.sq_unid_executora, a.finalidade, a.emite_os,
             a.consulta_opiniao, a.envia_email, a.exibe_relatorio, a.vinculacao, a.data_hora, a.envia_dia_util, a.descricao,
             a.justificativa, a.destinatario, a.controla_ano, a.libera_edicao, a.envio_inclusao,
             a.numeracao_automatica, a.servico_numerador, a.sequencial, a.ano_corrente, a.prefixo, a.sufixo,
             a.cancela_sem_tramite,
             case coalesce(b.qtd,0) when 0 then 'N' else 'S' end as solicita_cc,
             case coalesce(f.qtd,0) when 0 then 'N' else 'S' end as mail_tramite,
             c.sigla as sg_modulo, c.nome as nm_modulo, e.sq_cidade,
             d.nome as nm_unidade, d.sigla as sg_unidade
      from siw_menu                               a
             left    join (select x.sq_menu, count(sq_siw_tramite) as qtd
                             from siw_tramite x
                            where x.solicita_cc = 'S'
                              and 'CI'          = coalesce(x.sigla,'--')
                           group by x.sq_menu
                          )                  b on (a.sq_menu  = b.sq_menu)
             left    join (select x.sq_menu, count(sq_siw_tramite) as qtd
                             from siw_tramite x
                            where x.envia_mail = 'S'
                              and 'CI'          = coalesce(x.sigla,'--')
                           group by x.sq_menu
                          )                  f on (a.sq_menu  = f.sq_menu)
             left    join eo_unidade         d on (a.sq_unid_executora  = d.sq_unidade)
               left  join co_pessoa_endereco e on (d.sq_pessoa_endereco = e.sq_pessoa_endereco)
             inner        join siw_modulo         c on (a.sq_modulo          = c.sq_modulo)
      where a.sq_menu   = p_sq_menu;
end SP_GetMenuData;
/

