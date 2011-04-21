create or replace view vw_calendario as
select chave, cliente, to_number(to_char(data_formatada,'yyyy')) as ano, data_formatada, nome, expediente, nm_expediente
  from (select a.sq_data_especial as chave, a.cliente,
              case a.tipo
                   when 'E' then to_date(a.data_especial,'dd/mm/yyyy')
                   when 'I' then to_date(a.data_especial||'/'||coalesce(b.ano,to_number(to_char(sysdate,'yyyy'))),'dd/mm/yyyy')
                   else          VerificaDataMovel(coalesce(b.ano,to_number(to_char(sysdate,'yyyy'))), a.tipo)
              end as data_formatada,
              a.nome,
              a.expediente,
              case a.expediente
                   when 'S' then ' Normal'
                   when 'M' then ' Apenas manhã'
                   when 'T' then ' Apenas tarde'
                   when 'N' then ' Sem expediente'
              end as nm_expediente
         from eo_data_especial  a,
              (select to_number(to_char(sysdate,'yyyy'))-3 as ano from dual
               UNION
               select to_number(to_char(sysdate,'yyyy'))-2 as ano from dual
               UNION
               select to_number(to_char(sysdate,'yyyy'))-1 as ano from dual
               UNION
               select to_number(to_char(sysdate,'yyyy'))+0 as ano from dual
               UNION
               select to_number(to_char(sysdate,'yyyy'))+1 as ano from dual
               UNION
               select to_number(to_char(sysdate,'yyyy'))+2 as ano from dual
               UNION
               select to_number(to_char(sysdate,'yyyy'))+3 as ano from dual
              ) b
         where a.ativo = 'S'
           and (a.tipo <> 'E' or (a.tipo = 'E' and substr(a.data_especial, 7, 4) = to_char(b.ano)))
       ) k;

