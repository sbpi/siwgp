create or replace procedure SP_PutEOLocal
   (p_operacao                 in  varchar2,
    p_chave                    in  number default null,
    p_sq_pessoa_endereco       in  number default null,
    p_sq_unidade               in  number,
    p_nome                     in  varchar2,
    p_fax                      in  varchar2,
    p_telefone                 in  varchar2,
    p_ramal                    in  varchar2,
    p_telefone2                in  varchar2,
    p_ativo                    in  varchar2
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
     insert into eo_localizacao (sq_localizacao, sq_pessoa_endereco,
                  sq_unidade, nome, fax, telefone, ramal, telefone2, ativo, cliente)
          (select sq_localizacao.nextval,
                  p_sq_pessoa_endereco,
                  p_sq_unidade,
                  trim(p_nome),
                  trim(p_fax),
                  trim(p_telefone),
                  trim(p_ramal),
                  trim(p_telefone2),
                  p_ativo,
                  a.sq_pessoa
             from co_pessoa_endereco a
            where sq_pessoa_endereco = p_sq_pessoa_endereco
         );
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_localizacao set
         nome                 = trim(p_nome),
         fax                  = trim(p_fax),
         telefone             = trim(p_telefone),
         ramal                = trim(p_ramal),
         telefone2            = trim(p_telefone2),
         sq_pessoa_endereco   = p_sq_pessoa_endereco,
         sq_unidade           = p_sq_unidade,
         ativo                = p_ativo
      where sq_localizacao    = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete eo_localizacao where sq_localizacao = p_chave;
   End If;
end SP_PutEOLocal;
/

