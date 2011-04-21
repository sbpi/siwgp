create or replace procedure SP_GetSegVincData
   (p_sigla          in  varchar2 default null,
    p_sq_segmento    in  number,
    p_nome           in  varchar2 default null,
    p_sq_seg_vinculo in  number   default null,
    p_result         out sys_refcursor
   ) is
begin
   If p_sigla = 'SEGMENTOVINC' Then
      -- Recupera os dados do vículo do segmento  escolhido
      open p_result for
         select sq_seg_vinculo, b.sq_tipo_pessoa, b.nome nm_tipo_pessoa, a.nome nome_pessoa,
                a.ativo, a.padrao, a.sq_tipo_pessoa sq_tipo_pessoa, a.interno, a.contratado, a.ordem
           from dm_seg_vinculo a,
                co_tipo_pessoa      b
          where a.sq_tipo_pessoa = b.sq_tipo_pessoa
            and a.sq_segmento = p_sq_segmento
            and (p_sq_seg_vinculo is null or (p_sq_seg_vinculo is not null and a.sq_seg_vinculo = p_sq_seg_vinculo));
   ElsIf p_sigla = 'SEGMENTOMENU' Then
      -- Recupera os dados do menu do segmento escolhido
      open p_result for
         select a.nome nome, b.objetivo_especif objetivo, c.nome nm_modulo, a.sq_modulo sq_modulo,
                a.ativo ativo, b.comercializar
           from dm_segmento_menu     a
                  left outer join siw_mod_seg b on (a.sq_segmento = b.sq_segmento and a.sq_modulo = b.sq_modulo)
                  left outer join siw_modulo  c on (b.sq_modulo = c.sq_modulo)
          where a.sq_segmento = p_sq_segmento;
   ElsIf p_sigla = 'SEGMENTOMOD' Then
      -- Recupera os dados dos módulos do segmento escolhido
      open p_result for
         select a.sq_segmento, b.sq_modulo, b.nome nm_modulo, a.objetivo_especif,
                a.ativo, a.comercializar
           from siw_mod_seg a left outer join siw_modulo b on (a.sq_modulo = b.sq_modulo)
          where a.sq_segmento = p_sq_segmento;
   End If;
end SP_GetSegVincData;
/

