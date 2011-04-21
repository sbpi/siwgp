create or replace procedure SP_PutSIWModSeg
   (p_operacao                 in  varchar2,
    p_objetivo_especifico      in  varchar2,
    p_sq_modulo                in  number,
    p_sq_segmento              in  varchar2,
    p_comercializar            in  varchar2,
    p_ativo                    in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into siw_mod_seg (objetivo_especif, sq_modulo, sq_segmento, comercializar, ativo)
      values (
               trim(p_objetivo_especifico),
               p_sq_modulo,
               p_sq_segmento,
               p_comercializar,
               p_ativo
              );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update siw_mod_seg set
         objetivo_especif    = trim(p_objetivo_especifico),
         comercializar       = p_comercializar,
         ativo               = p_ativo
      where sq_modulo   = p_sq_modulo
        and sq_segmento = p_sq_segmento;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
       delete siw_mod_seg
        where sq_modulo   = p_sq_modulo
          and sq_segmento = p_sq_segmento;
   End If;
end SP_PutSIWModSeg;
/

