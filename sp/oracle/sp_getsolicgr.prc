create or replace procedure SP_GetSolicGR
   (p_menu      in number,
    p_pessoa    in number,
    p_restricao in varchar2 default null,
    p_result    out sys_refcursor) is
begin
   If Substr(p_restricao,1,4) = 'GRDM' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select a.sq_unidade, a.nome, a.sigla,
                (select count(*) qt_solic from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and sq_menu         = p_menu) qt_solic,
                (select sum(valor) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and sq_menu         = p_menu) vl_previsto,
                (select count(*) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and conclusao is not null
                    and sq_menu         = p_menu)  qt_conc,
                (select count(*) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and valor      between 0 and 100
                    and sq_menu         = p_menu) vl_faixa1,
                (select count(*) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and valor      between 101 and 200
                    and sq_menu         = p_menu) vl_faixa2,
                (select count(*) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and valor      > 201
                    and sq_menu         = p_menu) vl_faixa3
           from eo_unidade a
          where a.sq_pessoa = p_pessoa;
   Elsif Substr(p_restricao,1,4) = 'GRPR' Then
      -- Recupera as demandas que o usuário pode ver
      open p_result for
         select a.sq_unidade, a.nome, a.sigla,
                (select count(*) qt_solic from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and sq_menu         = p_menu) qt_solic,
                (select sum(valor) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and sq_menu         = p_menu) vl_previsto,
                (select count(*) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and conclusao is not null
                    and sq_menu         = p_menu)  qt_conc,
                (select count(*) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and valor      between 0 and 100
                    and sq_menu         = p_menu) vl_faixa1,
                (select count(*) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and valor      between 101 and 200
                    and sq_menu         = p_menu) vl_faixa2,
                (select count(*) from siw_solicitacao
                  where sq_unidade = a.sq_unidade
                    and valor      > 201
                    and sq_menu         = p_menu) vl_faixa3
           from eo_unidade a
          where a.sq_pessoa = p_pessoa;
   End If;
end SP_GetSolicGR;
/

