create or replace procedure SP_GetSolicRecurso
   (p_chave     in number,
    p_chave_aux in number   default null,
    p_restricao in varchar2,
    p_result    out sys_refcursor
   ) is
begin
  If p_restricao = 'LISTA' Then
     -- Recupera os recursos do projeto
     open p_result for
        select a.*
          from pj_projeto_recurso  a
         where a.sq_siw_solicitacao = p_chave;
  Elsif p_restricao = 'REGISTRO' Then
     -- Recupera os dados de um recurso do projeto
     open p_result for
        select a.*
          from pj_projeto_recurso a
         where a.sq_projeto_recurso = p_chave_aux;
  End If;
End SP_GetSolicRecurso;
/

