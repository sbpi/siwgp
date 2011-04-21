create or replace trigger TG_CO_PESSOA_IN_UP
  before insert or update on co_pessoa
  for each row
declare
  -- local variables here
begin
  :new.nome_indice          := acentos(:new.nome);
  :new.nome_resumido_ind    := acentos(:new.nome_resumido);

  If :new.nome <> :old.nome  and :new.sq_recurso is not null Then
     update eo_recurso set nome = :new.nome where sq_recurso = :new.sq_recurso;
  End If;
end TG_CO_PESSOA_IN_UP;
/

