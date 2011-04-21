create or replace procedure SP_PutCOSegmento
   (p_operacao         in  varchar2,
    p_chave            in  number default null,
    p_nome             in  varchar2,
    p_padrao           in  varchar2,
    p_ativo            in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into co_segmento (sq_segmento, nome, padrao,ativo)
      (select sq_segmento.nextval,
              trim(p_nome),
              trim(p_padrao),
              trim(p_ativo)
         from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update co_segmento set
         nome      = trim(p_nome),
         padrao    = p_padrao,
         ativo     = p_ativo
      where sq_segmento   = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete co_segmento where sq_segmento = p_chave;
   End If;
end SP_PutCOSegmento;
/

