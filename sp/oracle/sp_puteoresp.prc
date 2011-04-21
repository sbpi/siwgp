create or replace procedure SP_PutEOResp
   (p_operacao                 in  varchar2,
    p_chave                    in  number default null,
    p_fim_substituto           in  date default null,
    p_sq_pessoa_substituto     in  number,
    p_inicio_substituto        in  date,
    p_fim_titular              in  date default null,
    p_sq_pessoa                in  number,
    p_inicio_titular           in  date
   ) is
begin
   delete eo_unidade_resp where fim is null and sq_unidade = p_chave;
   If p_operacao <> 'E' Then
      If not p_sq_pessoa_substituto is null Then
         insert into eo_unidade_resp(
                     sq_unidade_resp, fim, sq_unidade, sq_pessoa, tipo_respons, inicio)
             (select sq_unidade_responsavel.nextval,
                     p_fim_substituto,
                     p_chave,
                     p_sq_pessoa_substituto,
                     'S',
                     p_inicio_substituto

             from dual
         );
      End If;
      insert into eo_unidade_resp(
               sq_unidade_resp, fim, sq_unidade, sq_pessoa, tipo_respons, inicio)
       (select sq_unidade_responsavel.nextval,
               p_fim_titular,
               p_chave,
               p_sq_pessoa,
               'T',
               p_inicio_titular
               from dual
        );
    End If;
end SP_PutEOResp;
/

