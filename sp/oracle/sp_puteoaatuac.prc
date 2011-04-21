create or replace procedure SP_PutEOAAtuac
   (p_operacao                 in  varchar2,
    p_chave                    in  number default null,
    p_cliente                  in  number default null,
    p_nome                     in  varchar2,
    p_ativo                    in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
     insert into eo_area_atuacao (sq_area_atuacao, sq_pessoa, nome, ativo)
         (select sq_area_atuacao.nextval,
                 p_cliente,
                 trim(p_nome),
                 p_ativo
            from dual
          );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_area_atuacao set
        nome  = trim(p_nome),
        ativo = p_ativo
        where sq_area_atuacao = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete eo_area_atuacao where sq_area_atuacao = p_chave;
   End If;
end SP_PutEOAAtuac;
/

