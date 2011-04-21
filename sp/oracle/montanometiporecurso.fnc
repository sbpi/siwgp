create or replace function MontaNomeTipoRecurso(p_chave in number, p_retorno in varchar2 default null) return varchar2 is
  cursor c_ordem is
     select sq_tipo_recurso, sq_tipo_pai, nome
       from eo_tipo_recurso
     start with sq_tipo_recurso = p_chave
     connect by prior sq_tipo_pai = sq_tipo_recurso;

  Result varchar2(2000) := '';
  w_pai  varchar2(2000) := '';
begin
  -- Se não foi informada a chave, retorna nulo
  If p_chave is null Then return null; End If;

  -- Monta o nome varrendo do registro informado para cima
  for crec in c_ordem loop Result :=  crec.nome || ' - ' || Result; end loop;

  -- Se retornar apenas o primeiro nível
  If p_retorno = 'PRIMEIRO' Then
     return(substr(Result,1,instr(Result,' - ')));
  Else
     return(substr(Result,1,length(Result)-3));
  End If;
end MontaNomeTipoRecurso;
/

