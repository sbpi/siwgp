<?php

/**
* class DatabaseQueries
*
* { Description :- 
*    This class is the base class for executing database queries
* }
*/

class DatabaseQueries
{
    var $conHandle;
    var $query;
    var $result;
    var $resultData;
    var $stmt;
    var $num_rows;
    
    /**
    * Method DatabaseQueries::getResultSet()
    *
    * { Description :- 
    *    This class is the base class for executing database queries.
    * }
    */
    
    function getResultSet() { return $this->result; }

    /**
    * Method DatabaseQueries::getResultData()
    *
    * { Description :- 
    *    Returns all data from a result set.
    * }
    */
    
    function getResultData() { return $this->resultData; }

    /**
    * Method DatabaseQueries::getNumRows()
    *
    * { Description :- 
    *    This class returns the number of rows found in a query or 
    *   returned by a stored procedure
    * }
    */
    
    function getNumRows() { return $this->num_rows; }

    /**
    * Method DatabaseQueries::getError()
    *
    * { Description :- 
    *    This class returns the error message ocurred while calling a stored procedure
    * }
    */
    
    function getError() { return $this->error; }
}

/**
* class MSSqlDatabaseQueries
*
* { Description :- 
*    This class is the sub class for executing MSSql database queries.
* }
*/

class MSSqlDatabaseQueries extends DatabaseQueries {    
    
    /**
    * Method MSSqlDatabaseQueries::executeQuery()
    *
    * { Description :- 
    *    This method executes the the query.
    * }
    */
    
    function MSSqlDatabaseQueries($query, $conHandle) {
        $this->query = $query;
        $this->conHandle = $conHandle;
    }
    
    /**
    * Method MSSqlDatabaseQueries::executeQuery()
    *
    * { Description :- 
    *    This method executes the the query.
    * }
    */
    
    function executeQuery() {
      if(!($this->result = mssql_query($this->query, $this->conHandle))) { return false; }
        else { 
           if(is_resource($this->result)) { $this-> num_rows = mssql_num_rows($this->result); }
           else { $this-> num_rows =  -1; }
           return true;     
        }
    }
    
    /**
    * Method MSSqlDatabaseQueries::getResultArray()
    *
    * { Description :- 
    *    This method returns the one row from the resultset
    * }
    */
    
    function getResultArray() {
        if(is_resource($this->result)) { 
          for ($i = 0; $i < mssql_num_fields($this->result); $i++) {
            if (mssql_field_type($this->result, $i)=='datetime' || mssql_field_type($this->result, $i)=='date' || mssql_field_type($this->result, $i)=='numeric') { $this->column_datatype[mssql_field_name($this->result, $i)] = mssql_field_type($this->result, $i); }
            elseif (substr(strtolower(mssql_field_name($this->result, $i)),0,6)=='phpdt_') { $this->column_datatype[strtolower(mssql_field_name($this->result, $i))] = 'datetime'; }
          }
          $this->resultData  = mssql_fetch_array($this->result);
          $this->num_rows    = mssql_num_rows($this->result);
          if (isset($this->column_datatype)) {
            foreach ($this->column_datatype as $key => $val) {
              if (nvl($this->resultData[$key],'')>'') { 
                if ($val=='datetime') {
                  $tmp = $this->resultData[$key];
                  if (strpos($tmp,',')===false) {
                    $tmp = formatDateTime($this->resultData[$key]);
                    $this->resultData[$key] = mktime(0,0,0,substr($tmp,3,2),substr($tmp,0,2),substr($tmp,6,4)); 
                  } else {
                    $this->resultData[$key] = mktime(substr($tmp,12,2),substr($tmp,15,2),substr($tmp,18,2),substr($tmp,3,2),substr($tmp,0,2),substr($tmp,6,4));
                  }
                } else {
                  $this->temp[$key] = str_replace(',','.',$this->temp[$key]); 
                }
              }
            }
          }
          return $this->resultData; 
        } else { 
          return null; 
        }
    }

    function getResultData() {
        if(is_resource($this->result)) { 
          for ($i = 0; $i < mssql_num_fields($this->result); $i++) {
            if (mssql_field_type($this->result, $i)=='datetime' || mssql_field_type($this->result, $i)=='date' || mssql_field_type($this->result, $i)=='numeric') { $this->column_datatype[mssql_field_name($this->result, $i)] = mssql_field_type($this->result, $i); }
            elseif (substr(strtolower(mssql_field_name($this->result, $i)),0,6)=='phpdt_') { $this->column_datatype[strtolower(mssql_field_name($this->result, $i))] = 'datetime'; }
          }
          $this->resultData  = mssql_fetch_all($this->result);
          $this->num_rows    = mssql_num_rows($this->result);
          if (isset($this->column_datatype)) {
            for ($i = 0; $i < $this->num_rows; $i++) {
              foreach ($this->column_datatype as $key => $val) {
                if ($val=='datetime') {
                  $tmp = $this->resultData[$i][$key];
                  if (strpos($tmp,',')===false) {
                    $tmp = formatDateTime($this->resultData[$i][$key]);
                    $this->resultData[$i][$key] = mktime(0,0,0,substr($tmp,3,2),substr($tmp,0,2),substr($tmp,6,4)); 
                  } else {
                    $this->resultData[$i][$key] = toDate($this->resultData[$i][$key]);
                  }
                } else {
                  $this->resultData[$i][$key] = str_replace(',','.',$this->resultData[$i][$key]); 
                }
              }
            }
          }
          return $this->resultData; 
        } else { 
          return null; 
        }
    }
}

/**
* class MSSqlDatabaseQueryProc
*
* { Description :- 
*    This class is the sub of MSSqlDatabaseQuery class for executing MSSql database Procedures.
*    $proc -- Procedure Name.
*    $conHandle -- Connection Handle.
*    $params -- Associative array  eg. array("@edited"=>array($edited=>SQLCHAR, false));
*                                        @edited is input paramter,
*                                        $edited is the value of Input Parameter @edited,
*                                        SQLCHAR is a the MSSQL Constant for CHAR column type,
*                                        false indicates @edited is not an output parameter.        
*                
* }                            
*/

class MSSqlDatabaseQueryProc extends MSSqlDatabaseQueries {
    var $params;
    var $paramName;
    var $paramValue;
    var $paramType;    
    var $paramLength;

    /* Method MSSqlDatabaseQueryProc(). Constructor.
    *
    * { Description :- 
    *    This class is the sub of MSSqlDatabaseQuery class for executing MSSql database Procedures.
    *    $proc -- Procedure Name.
    *    $conHandle -- Connection Handle.
    *    $params -- Associative array  eg. array("@edited"=>array($edited=>SQLCHAR, false));
    *                                        @edited is input paramter,
    *                                        $edited is the value of Input Parameter @edited,
    *                                        SQLCHAR is a the MSSQL Constant for CHAR column type,
    *                                        false indicates @edited is not an output parameter.        
    *                
    * }                            
    */    
    
    function MSSqlDatabaseQueryProc($proc, $conHandle, $params) {
        $this->query = $proc;
        $this->conHandle = $conHandle;
        $this->params = $params;
    }
    
    function mssql_fetch_all($resource) {
      $temp = array();
      while ($row=mssql_fetch_array($resource)) {
        array_push($temp,$row);
      }
      return $temp;
    }
    
    /**
    * Method MSSqlDatabaseQueries::executeQuery()
    *
    * { Description :- 
    *    This method executes the the query.
    * }
    */    
    
    function executeQuery() {
        if (substr($this->query,0,8)=='FUNCTION') $this->query = str_replace('.','.sp_Get',substr($this->query,8));
      
        $this->stmt = mssql_init("$this->query",$this->conHandle);

        foreach($this->params as $paramName=>$value) {
          foreach($value as $paramValue=>$paramType) {
                if (!($value[1]==B_CURSOR)) { 
                  if ($value[1]==B_VARCHAR) {
                     mssql_bind($this->stmt, "@$paramName", $value[0], $value[1], false, is_null($value[0]), $value[2]); 
                  } elseif ($value[1]==B_DATE) {
                    mssql_bind($this->stmt, "@$paramName", toSQLDate($value[0]), SQLVARCHAR, false, is_null($value[0]), 30); 
                  } else {
                     mssql_bind($this->stmt, "@$paramName", $value[0], $value[1], false, is_null($value[0])); 
                   }
                }
                 break;
            }
        }
        if(!($this->result = mssql_execute($this->stmt))) { 

          $this->error['message'] = mssql_get_last_message() ;

          $this->error['sqltext'] = $this->query;
          return false; 
        } else {
          if(is_resource($this->result)) { $this->num_rows = mssql_num_rows($this->result); }
           else { $this->num_rows = -1; }
           return true;
        }
    }
    
    function getResultData() {
        if(is_resource($this->result)) {
          for ($i = 0; $i < mssql_num_fields($this->result); $i++) {
            if (mssql_field_type($this->result, $i)=='datetime' || mssql_field_type($this->result, $i)=='date' || mssql_field_type($this->result, $i)=='real') { 
              $this->column_datatype[mssql_field_name($this->result, $i)] = mssql_field_type($this->result, $i); 
            } elseif (substr(strtolower(mssql_field_name($this->result, $i)),0,6)=='phpdt_') { 
              $this->column_datatype[strtolower(mssql_field_name($this->result, $i))] = 'datetime'; 
            }
          }
          $this->resultData  = $this->mssql_fetch_all($this->result);
          $this->num_rows    = mssql_num_rows($this->result);
          if (isset($this->column_datatype)) {
            for ($i = 0; $i < $this->num_rows; $i++) {
              foreach ($this->column_datatype as $key => $val) {
                if (nvl($this->resultData[$i][$key],'')>'') { 
                  if ($val=='datetime') {
                    $tmp = $this->resultData[$i][$key];
                    if (strpos($tmp,',')===false) {
                      $tmp = formatDateTime($this->resultData[$i][$key]);
                      $this->resultData[$i][$key] = mktime(0,0,0,substr($tmp,3,2),substr($tmp,0,2),substr($tmp,6,4)); 
                    } else {
                      $this->resultData[$i][$key] = toDate($this->resultData[$i][$key]);
                    }
                  } else {
                    $this->resultData[$i][$key] = str_replace(',','.',$this->resultData[$i][$key]); 
                  }
                }
              }
            }
          }
          return $this->resultData; 
        } else { 
          return null; 
        }
    }
} 

/**
* class OraDatabaseQueries
*
* { Description :- 
*    This class is the sub class for executing Oracle database queries.
* }
*/

class OraDatabaseQueries extends DatabaseQueries {    
    
    /**
    * Method OraDatabaseQueries::executeQuery()
    *
    * { Description :- 
    *    This method executes the the query.
    * }
    */
    
    function OraDatabaseQueries($query, $conHandle) {
        $this->query = $query;
        $this->conHandle = $conHandle;
    }
    
    /**
    * Method OraDatabaseQueries::executeQuery()
    *
    * { Description :- 
    *    This method executes the the query.
    * }
    */
    
    function executeQuery() {
      if(!($this->result = oci_parse($this->conHandle, $this->query))) { 
        return false; 
      } else { 
        if(is_resource($this->result)) { 
          if (!oci_execute($this->result)) { die($this->query.'<br>'); }
          $command = strtoupper(substr(trim($this->query),0,strpos(trim($this->query),' ')));
          if (false!==strpos('INSERT,UPDATE,DELETE',strtoupper($command))) {
            $this->num_rows = oci_num_rows($this->result);
          } else {
            $this->num_rows = oci_fetch_all($this->result, $this->resultData, 0, -1,OCI_ASSOC+OCI_FETCHSTATEMENT_BY_ROW);
            oci_execute($this->result);
          }
        } else { 
          $this->num_rows = -1; 
        }
      }
      return true; 
    }
    
    /**
    * Method OraDatabaseQueries::getResultArray()
    *
    * { Description :- 
    *    This method returns the one row from the resultset
    * }
    */
    
    function getResultArray() {
        if(is_resource($this->result)) { 
          for ($i = 1; $i <= oci_num_fields($this->result); $i++) {
            if (oci_field_type($this->result, $i)=='DATE' || oci_field_type($this->result, $i)=='NUMBER') $this->column_datatype[oci_field_name($this->result, $i)] = oci_field_type($this->result, $i);
            elseif (substr(strtolower(oci_field_name($this->result, $i)),0,6)=='phpdt_') { $this->column_datatype[strtolower(oci_field_name($this->result, $i))] = 'DATETIME'; }
          }
          $this->temp = oci_fetch_array($this->result, OCI_BOTH+OCI_RETURN_NULLS); 
          if (isset($this->column_datatype)) {
            foreach ($this->column_datatype as $key => $val) {
              if (nvl($this->temp[$key],'')>'') { 
                if ($val=='DATE') {
                  $tmp = formatDateTime($this->temp[$key]);
                  $this->temp[$key] = mktime(0,0,0,substr($tmp,3,2),substr($tmp,0,2),substr($tmp,6,4)); 
                } elseif ($val=='DATETIME') {
                  $tmp = $this->temp[$key];
                  $this->resultData[$i][$key] = mktime(substr($tmp,12,2),substr($tmp,15,2),substr($tmp,18,2),substr($tmp,3,2),substr($tmp,0,2),substr($tmp,6,4));
                } else {
                  $this->temp[$key] = str_replace(',','.',$this->temp[$key]); 
                }
              }
            }
          }
          oci_free_statement($this->result);
          return $this->temp;
        } else { return null; }
    }

}

/**
* class OraDatabaseQueryProc
*
* { Description :- 
*    This class is the sub of OraDatabaseQuery class for executing Oracle database Procedures.
*    $proc -- Procedure Name.
*    $conHandle -- Connection Handle.
*    $params -- Associative array  eg. array("@edited"=>array($edited=>SQLCHAR, false));
*                                        @edited is input paramter,
*                                        $edited is the value of Input Parameter @edited,
*                                        SQLCHAR is a the Oracle Constant for CHAR column type,
*                                        false indicates @edited is not an output parameter.        
*                
* }                            
*/

class OraDatabaseQueryProc extends OraDatabaseQueries {
    var $params;
    var $paramName;
    var $paramValue;
    var $paramType;    
    var $paramLength;
    
    /* Method OraDatabaseQueryProc(). Constructor.
    *
    * { Description :- 
    *    This class is the sub of OraDatabaseQuery class for executing Oracle database Procedures.
    *    $proc -- Procedure Name.
    *    $conHandle -- Connection Handle.
    *    $params -- Associative array  eg. array("@edited"=>array($edited=>SQLCHAR, false));
    *                                        @edited is input paramter,
    *                                        $edited is the value of Input Parameter @edited,
    *                                        SQLCHAR is a the Oracle Constant for CHAR column type,
    *                                        false indicates @edited is not an output parameter.        
    *                
    * }                            
    */    
    
    function OraDatabaseQueryProc($proc, $conHandle, $params) {
        $this->query = $proc;
        $this->conHandle = $conHandle;
        $this->params = $params;
    }

    /**
    * Method OraDatabaseQueries::getResultArray()
    *
    * { Description :- 
    *    This method returns the one row from the resultset
    * }
    */
    
    function getResultArray() { 
      return ((is_array($this->resultData)) ? array_pop($this->resultData) : $this->resultData); 
    }
    
    
    /**
    * Method OraDatabaseQueries::executeQuery()
    *
    * { Description :- 
    *    This method executes the the query.
    * }
    */    
    
    function executeQuery() {
      
        $par = "";
        $cursor = false;
        foreach($this->params as $paramName=>$value) {
            foreach($value as $paramValue=>$paramType) { 
              $par .= ", :$paramName";
              if ($paramName == 'p_result') $cursor = true;
              break; 
            }
        }
        $par = substr($par, 1);

        if ($cursor) {
           $this->result = oci_new_cursor($this->conHandle);
           if (substr($this->query,0,8)=='FUNCTION') {
             $this->query = substr($this->query,8);
             $stmt = "select $this->query ($par) from dual;";
           } else { 
             $stmt = "begin $this->query ($par); end;";
           }
           $this->stmt = oci_parse($this->conHandle, $stmt);
           
           $exibe = false;
           foreach($this->params as $paramName=>$value) {
               foreach($value as $paramValue=>$paramType) {
                   if($value[1]!=B_CURSOR)
                      oci_bind_by_name($this->stmt, $paramName, $value[0], $value[2]); 
                   else {
                      oci_bind_by_name($this->stmt, $paramName, &$this->result, $value[2], OCI_B_CURSOR);
                   }
                   break;
               }
           }

           if(!(oci_execute($this->stmt))) { 
             $this->error = oci_error($this->stmt);
             $this->error['sqltext'] = $stmt;
             return false; 
           } else {
              oci_execute($this->result);
              if(is_resource($this->result)) { 
                 for ($i = 1; $i <= oci_num_fields($this->result); $i++) {
                   if (oci_field_type($this->result, $i)=='DATE' || oci_field_type($this->result, $i)=='NUMBER') { $this->column_datatype[strtolower(oci_field_name($this->result, $i))] = oci_field_type($this->result, $i); }
                   elseif (substr(strtolower(oci_field_name($this->result, $i)),0,6)=='phpdt_') { $this->column_datatype[strtolower(oci_field_name($this->result, $i))] = 'DATETIME'; }
                 }
                 $this->num_rows = oci_fetch_all($this->result, $this->resultData, 0, -1,OCI_ASSOC+OCI_FETCHSTATEMENT_BY_ROW);
                 array_key_case_change(&$this->resultData);
                 if (isset($this->column_datatype)) {
                   for ($i = 0; $i < $this->num_rows; $i++) {
                     foreach ($this->column_datatype as $key => $val) {
                       if (nvl($this->resultData[$i][$key],'')>'') { 
                         if ($val=='DATE') {
                           $tmp = formatDateTime($this->resultData[$i][$key]);
                           $this->resultData[$i][$key] = mktime(0,0,0,substr($tmp,3,2),substr($tmp,0,2),substr($tmp,6,4)); 
                         } elseif ($val=='DATETIME') {
                           $this->resultData[$i][$key] = toDate($this->resultData[$i][$key]);
                         } else {
                           $this->resultData[$i][$key] = str_replace(',','.',$this->resultData[$i][$key]); 
                         }
                       }
                     }
                   }
                 }
              } else { 
                $this->num_rows = -1; 
                $this->error = oci_error($this->result); 
                $this->error['sqltext'] = $stmt;
                return false; 
              }
              if (!oci_execute($this->stmt)) { 
                $this->error = oci_error($this->stmt); 
                $this->error = oci_error($this->stmt); 
                $this->error['sqltext'] = $stmt;
                return false; 
              } else {
                if (!oci_execute($this->result)) {
                  $this->error = oci_error($this->result); 
                  $this->error['sqltext'] = $stmt;
                  return false; 
                } 
              } 
           }
        } else {
           $function = false;
           if (substr($this->query,0,8)=='FUNCTION') {
             $function = true;
             $log = false;
             $this->query = substr($this->query,8);
             $stmt = "select $this->query ($par) from dual";
           } else { 
             $log = true;
             $stmt = "begin $this->query ($par); end;";
           }
           $this->result = oci_parse($this->conHandle, $stmt);
           
           foreach($this->params as $paramName=>$value) {
               foreach($value as $paramValue=>$paramType) { 
                  oci_bind_by_name($this->result, $paramName, $value[0], $value[2]); 
                  break;
               }
           }

           if(is_resource($this->result)) {
             if (!oci_execute($this->result)) {
               $this->error = oci_error($this->result); 
               $this->error['sqltext'] = $stmt;
               // Registra no servidor syslog
               $w_resultado = enviaSyslog('GR','ERRO ESCRITA','('.$_SESSION['SQ_PESSOA'].') '.$_SESSION['NOME_RESUMIDO'].' - '.$this->query);
               if ($w_resultado>'') {
                 ScriptOpen('JavaScript');
                 ShowHTML('  alert(\''.$w_resultado.'\');');
                 ScriptClose();
               }
               return false; 
             } else {
               if ($function){
                 $this->num_rows = oci_fetch_all($this->result, $this->resultData, 0, -1,OCI_ASSOC+OCI_FETCHSTATEMENT_BY_ROW);
               } else {
                 if ($log) {
                   // Registra no servidor syslog
                   $w_resultado = enviaSyslog('GV','ESCRITA','('.$_SESSION['SQ_PESSOA'].') '.$_SESSION['NOME_RESUMIDO'].' - '.$this->query);
                   if ($w_resultado>'') {
                     ScriptOpen('JavaScript');
                     ShowHTML('  alert(\''.$w_resultado.'\');');
                     ScriptClose();
                   }
                 }
               }               
             }
           } else { 
             $this->num_rows = -1; 
             return false;
           }
        }
        
        return true;
    }

} 

class PgSqlDatabaseQueries extends DatabaseQueries {    
    
    /**
    * Method PgSqlDatabaseQueries::executeQuery()
    *
    * { Description :- 
    *    This method executes the the query.
    * }
    */
    
    function PgSqlDatabaseQueries($query, $conHandle) {
        $this->query = $query;
        $this->conHandle = $conHandle;
    }
    
    /**
    * Method PgSqlDatabaseQueries::executeQuery()
    *
    * { Description :- 
    *    This method executes the the query.
    * }
    */
    
    function executeQuery() {
      if(!($this->result = pg_query($this->conHandle, $this->query))) { 
        $this->error = pg_result_error($this->result);
        return false; 
      } else { 
        if(is_resource($this->result)) { 
          $command = strtoupper(substr(trim($this->query),0,strpos(trim($this->query),' ')));
          if (false!==strpos('INSERT,UPDATE,DELETE',strtoupper($command))) {
            $this->num_rows = pg_affected_rows($this->result); 
          } else {
            $this->num_rows = pg_num_rows($this->result); 
          }
        } else { 
          $this->num_rows = -1; 
        }
        return true;
      }
    }
    
    /**
    * Method PgSqlDatabaseQueries::getResultArray()
    *
    * { Description :- 
    *    This method returns the one row from the resultset
    * }
    */
    
    function getResultArray() {
        if(is_resource($this->result)) { return pg_fetch_array($this->result); }
        else { return null; }
    }


    function getResultData() {
        if(is_resource($this->result)) { $this->resultData = pg_fetch_all($this->result); return $this->resultData;}
        else { return null; }
    }
}

/**
* class PgSqlDatabaseQueryProc
*
* { Description :- 
*    This class is the sub of PgSqlDatabaseQuery class for executing PgSql database Procedures.
*    $proc -- Procedure Name.
*    $conHandle -- Connection Handle.
*    $params -- Associative array  eg. array("@edited"=>array($edited=>SQLCHAR, false));
*                                        @edited is input paramter,
*                                        $edited is the value of Input Parameter @edited,
*                                        SQLCHAR is a the PgSql Constant for CHAR column type,
*                                        false indicates @edited is not an output parameter.        
*                
* }                            
*/

class PgSqlDatabaseQueryProc extends PgSqlDatabaseQueries {
    var $params;
    var $paramName;
    var $paramValue;
    var $paramType;    
    var $paramLength;

    /* Method PgSqlDatabaseQueryProc(). Constructor.
    *
    * { Description :- 
    *    This class is the sub of PgSqlDatabaseQuery class for executing PgSql database Procedures.
    *    $proc -- Procedure Name.
    *    $conHandle -- Connection Handle.
    *    $params -- Associative array  eg. array("@edited"=>array($edited=>SQLCHAR, false));
    *                                        @edited is input paramter,
    *                                        $edited is the value of Input Parameter @edited,
    *                                        SQLCHAR is a the PgSql Constant for CHAR column type,
    *                                        false indicates @edited is not an output parameter.        
    *                
    * }                            
    */    
    
    
    function PgSqlDatabaseQueryProc($proc, $conHandle, $params) {
        $this->query = $proc;
        if (strpos($this->query,'FUNCTION')!==false) {
          // Chamadas diretas a funções têm tratamento diferenciado.
          $this->query = 'FUNCTION '.substr($this->query,8);
        } else {
          $this->query = substr($proc,strpos($proc,'.'));
        }
        $this->conHandle = $conHandle;
        $this->params = $params;
    }
    
    /**
    * Method PgSqlDatabaseQueries::executeQuery()
    *
    * { Description :- 
    *    This method executes the the query.
    * }
    */    
    
    function executeQuery() {
        $par = "";
        $cursor = false;

        foreach($this->params as $paramName=>$value) {
            foreach($value as $paramValue=>$paramType) {
                if ($value[1]!=B_CURSOR) {
                   if (nvl($value[0],'')==='') { $par .= ", null"; }
                   elseif ($value[1]==B_VARCHAR) { $par .= ", '$value[0]'"; }
                   elseif ($value[1]==B_DATE) { $par .= ", '".date('d/m/Y',toDate($value[0]))."'"; }
                   else { $par .= ", $value[0]"; }
                } else {
                  $cursor = true;
                }
                 break;
            }
        }
        if ($cursor) {
          if ($par=="") {
             $par = "rollback; begin; select $this->query ('p_result'); fetch all in p_result;";
          } else {
             $par = "rollback; begin; select $this->query (".substr($par, 1).", 'p_result'); fetch all in p_result;";
          }
        } else {
          if (substr($this->query,0,8)=='FUNCTION') {
            $log = false;
            $par = "select ".substr($this->query,8)." (".substr($par, 1).");";
          } else {
            $log = true;
            if ($par=="") {
              $par = "rollback; begin; select $this->query; commit;";
            } else {
              $par = "rollback; begin; select $this->query (".substr($par, 1)."); commit;";
            }
          }
        }
        if (!($this->result = pg_query($this->conHandle, $par))) {
           $this->error['message'] = pg_last_error($this->conHandle);
           $this->error['sqltext'] = $par;
           $this->num_rows = -1; 
           // Registra no servidor syslog
           $w_resultado = enviaSyslog('GR','ERRO ESCRITA','('.$_SESSION['SQ_PESSOA'].') '.$_SESSION['NOME_RESUMIDO'].' - '.$this->query);
           if ($w_resultado>'') {
             ScriptOpen('JavaScript');
             ShowHTML('  alert(\''.$w_resultado.'\');');
             ScriptClose();
           }
           return false;
        } else { 
           $this->num_rows = pg_num_rows($this->result); 
           // Registra no servidor syslog
           if ($log) {
             $w_resultado = enviaSyslog('GV','ESCRITA','('.$_SESSION['SQ_PESSOA'].') '.$_SESSION['NOME_RESUMIDO'].' - '.$this->query);
             if ($w_resultado>'') {
               ScriptOpen('JavaScript');
               ShowHTML('  alert(\''.$w_resultado.'\');');
               ScriptClose();
             }
           }
           return true;
        }
    }

    function getResultData() {

        if(is_resource($this->result)) { 
          for ($i = 0; $i < pg_num_fields($this->result); $i++) {
            if (pg_field_type($this->result, $i)=='timestamp' || pg_field_type($this->result, $i)=='date' || pg_field_type($this->result, $i)=='numeric') { $this->column_datatype[pg_field_name($this->result, $i)] = pg_field_type($this->result, $i); }
            elseif (substr(strtolower(pg_field_name($this->result, $i)),0,6)=='phpdt_') { $this->column_datatype[strtolower(pg_field_name($this->result, $i))] = 'timestamp1'; }
          }
          $this->resultData  = pg_fetch_all($this->result);
          $this->num_rows    = pg_num_rows($this->result);
          if (isset($this->column_datatype)) {
            for ($i = 0; $i < $this->num_rows; $i++) {
              foreach ($this->column_datatype as $key => $val) {
                if (nvl($this->resultData[$i][$key],'')>'') { 
                  if ($val=='date') {
                    $tmp = $this->resultData[$i][$key];
                    $this->resultData[$i][$key] = mktime(0,0,0,substr($tmp,5,2),substr($tmp,8,2),substr($tmp,0,4)); 
                  } elseif ($val=='timestamp1') {
                    $tmp = $this->resultData[$i][$key];
                    $this->resultData[$i][$key] = mktime(substr($tmp,12,2),substr($tmp,15,2),substr($tmp,18,2),substr($tmp,3,2),substr($tmp,0,2),substr($tmp,6,4)); 
                  } elseif ($val=='timestamp') {
                    $tmp = $this->resultData[$i][$key];
                    $this->resultData[$i][$key] = mktime(substr($tmp,11,2),substr($tmp,14,2),substr($tmp,17,2),substr($tmp,5,2),substr($tmp,8,2),substr($tmp,0,4)); 
                  } else {
                    $this->resultData[$i][$key] = str_replace(',','.',$this->resultData[$i][$key]); 
                  }
                }
              }
            }
          }
          return $this->resultData; 
        } else { 
          return null; 
        }
    }
} 
?>