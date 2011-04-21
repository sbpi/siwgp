create or replace procedure SP_PutDataEspecial
   (p_operacao                 in  varchar2,
    p_chave                    in  number    default null,
    p_cliente                  in  number    default null,
    p_sq_pais                  in  number    default null,
    p_co_uf                    in  varchar2  default null,
    p_sq_cidade                in  number    default null,
    p_tipo                     in  varchar2  default null,
    p_data_especial            in  varchar2  default null,
    p_nome                     in  varchar2  default null,
    p_abrangencia              in  varchar2  default null,
    p_expediente               in  varchar2  default null,
    p_ativo                    in  varchar2  default null
   ) is
begin
   -- Grava uma modalidade de contratação
   If p_operacao = 'I' Then
      -- Insere registro
      insert into eo_data_especial
        (sq_data_especial, cliente, sq_pais, co_uf, sq_cidade, tipo, data_especial, nome, abrangencia, expediente, ativo)
      values
        (sq_data_especial.nextval, p_cliente, p_sq_pais, p_co_uf, p_sq_cidade, p_tipo, p_data_especial, trim(p_nome), p_abrangencia, p_expediente, p_ativo);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_data_especial
         set sq_pais       = p_sq_pais,
             co_uf         = p_co_uf,
             sq_cidade     = p_sq_cidade,
             tipo          = p_tipo,
             data_especial = p_data_especial,
             nome          = trim(p_nome),
             abrangencia   = p_abrangencia,
             expediente    = p_expediente,
             ativo = p_ativo
       where sq_data_especial = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete eo_data_especial where sq_data_especial = p_chave;
   End If;
end SP_PutDataEspecial;
/

