CREATE OR REPLACE VIEW VW_INDICADOR_PROJETO AS
SELECT pro.sq_siw_solicitacao, pro.titulo , to_char(pro.ide,'990D00') as ide, to_char(pro.ige,'990D00') as ige, to_char(pro.igc,'990D00') as igc
       ,to_char(pro.idc,'990D00') as idc
       , case
           when ide < 70 then 'Fora da faixa desejável'
           when ide >= 70 and ide < 90 then 'Próximo da faixa desejável'
           when ide >= 90 and ide <= 120 then 'Na faixa desejável'
         end as status_ide
        , case
           when ide >= 90 and ide <= 120 then 1
           when ide < 70 then 2
           when ide >= 70 and ide < 90 then 3
         end as sq_status_ide
       ,case
          when idc >= 90 and idc <= 120 then 1
           when idc  < 70 then 2
           when idc >= 70 and idc < 90 then 3
         end as sq_status_idc
       ,case
           when idc  < 70 then 'Fora da faixa desejável'
           when idc >= 70 and idc < 90 then 'Próximo da faixa desejável'
           when idc >= 90 and idc <= 120 then 'Na faixa desejável'
        end as status_idc
from vw_calculo_indicador_projeto pro;

