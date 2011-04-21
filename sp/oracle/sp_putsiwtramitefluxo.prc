create or replace procedure SP_PutSiwTramiteFluxo
   (p_operacao                 in  varchar2,
    p_chave                    in  number    default null,
    p_destino                  in  number    default null
   ) is
begin
   If p_operacao = 'I' and p_chave is not null and p_destino is not null Then
      -- Insere registro
      insert into siw_tramite_fluxo
             (sq_siw_tramite_origem, sq_siw_tramite_destino)
      (select p_chave,               p_destino
         from dual
      );
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete siw_tramite_fluxo where sq_siw_tramite_origem = p_chave;
   End If;
end SP_PutSiwTramiteFluxo;
/

