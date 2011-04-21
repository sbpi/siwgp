create or replace procedure sp_putIndicador_Aferidor
   (p_operacao        in  varchar2,
    p_usuario         in  number,
    p_chave           in  number,
    p_chave_aux       in  number   default null,
    p_pessoa          in  number   default null,
    p_prazo           in  varchar2 default null,
    p_inicio          in  date     default null,
    p_fim             in  date     default null
   ) is
   w_chave  number(18);
   w_fim    date;
begin
   -- Configura o término da responsabilidade. Se for prazo indefinido, coloca 31/12/2100
   If p_prazo is not null Then
     If p_prazo = 'N'
        Then w_fim := to_date('31/12/2100','dd/mm/yyyy');
        Else w_fim := p_fim;
     End If;
   End If;

   If p_operacao = 'I' or p_operacao = 'C' Then
      -- Gera a nova chave do registro, a partir da sequence
      select sq_eoindicador_aferidor.nextval into w_chave from dual;

      -- Insere registro
      insert into eo_indicador_aferidor
        (sq_eoindicador_aferidor, sq_eoindicador, sq_pessoa, prazo_definido, inicio,   fim)
      values
        (w_chave,                 p_chave,        p_pessoa,  p_prazo,        p_inicio, nvl(p_fim,w_fim));
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update eo_indicador_aferidor
         set sq_eoindicador = p_chave,
             sq_pessoa      = p_pessoa,
             prazo_definido = p_prazo,
             inicio         = p_inicio,
             fim            = nvl(p_fim,w_fim)
       where sq_eoindicador_aferidor = p_chave_aux;
   Elsif p_operacao = 'E' Then
      -- Recupera o período do registro
      delete eo_indicador_aferidor where sq_eoindicador_aferidor = p_chave_aux;
   End If;
end sp_putIndicador_Aferidor;
/

