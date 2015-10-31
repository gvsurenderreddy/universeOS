<?php
/*
This file is published by transparency-everywhere with the best deeds.
Check transparency-everywhere.com for further information.
Licensed under the CC License, Version 4.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at
    https://creativecommons.org/licenses/by/4.0/legalcode
Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
@author nicZem for tranpanrency-everywhere.com
*/

function save($str){
    return escape::sql($str);
}

class escape{
    function sql($string){
        //@speed
        $db = new db();
        return $db->escape($string);
    }
}

class db{
        private $pdoDB;
        function __construct(){
            $this->pdoDB = new PDO('mysql:host='.uni_config_database_host.';dbname='.uni_config_database_name.';charset=utf8', uni_config_database_user, uni_config_database_password);
        }
        public function escape($string){
            //@sec
            return substr($this->pdoDB->quote($string), 1, -1);
        }
        public function generateWhere($primary){
            if(is_array($primary)){
                
                //if array length is 2 the basic statement is used
                if(count($primary) == 2){
                    $return = "WHERE `".$primary[0]."`='".save($primary[1])."'";
                }else if(count($primary)>2){
                    //use thrid item of primary array as seperator(OR or and)
                    $return = "WHERE `".$primary[0]."`='".save($primary[1])."' ".$primary[2]." ";

                    $arrayCounter = 3;
                    while(isset($primary[$arrayCounter])){
                        $return .= '`'.$primary[$arrayCounter];
                        $arrayCounter++;
                        $return .= "`='".save($primary[$arrayCounter])."' ".$primary[$arrayCounter+1]." ";
                        $arrayCounter++;
                        $arrayCounter++;
                    }

                }
            }else{
                return 'WHERE '.$primary;
            }
            
            return $return;
        }
        public function shiftResult($result, $testColumn){
            //check if result is array (if result is empty the sql string will be returned)
            if(!is_array($result)){
                return array();
            }
            
            
            //check if collumn which need to be tested exists
            if(isset($result[$testColumn])){
                $return[0] = $result;
                return $return;
            }
            return $result;
        }
        /**
        *Inserts record with $options into db $table 
        *@param string $table Name of table
        *@param array $options Array with insert values mysql_field_name=>values
        *@return int auto_increment value of added record 
        */
	public function insert($table, $options){
					
            //generate update query	
            foreach($options AS $row=>$value){
                    $query[] = "`".save($row)."`";
                    $values[] = "'".save($value)."'";
            }


            $query = "(".implode(',', $query).")";
            $values = "(".implode(',', $values).");";

            $result = $this->pdoDB->exec("INSERT INTO `$table` $query VALUES $values");
            return $this->pdoDB->lastInsertId();
	}
        /**
        *Updates record with $primary[0]=$primary[1] in db $table 
        *@param string $table Name of table
        *@param array $options Array with insert values mysql_field_name=>values
        *@param primary array Primary id of the record
        *@return int affected rows
        */
	public function update($table, $options, $primary){
            $WHERE = $this->generateWhere($primary);
            //generate update query	
            foreach($options AS $row=>$value){

                    //only add row to query if value is not empty
                    if(!empty($value)||($value == 0)){
                            $query[] = " `$row`='".save($value)."'";
                    }
            }
            $query = implode(',', $query);

            return $this->pdoDB->exec("UPDATE `$table` SET $query $WHERE");
	}
        /**
        *Updates record with $primary[0]=$primary[1] in db $table 
        *@param string $table Name of table
        *@primary array Primary id of the record 
        */
        public function delete($table, $primary){
            $WHERE = $this->generateWhere($primary);
            return $this->pdoDB->exec("DELETE FROM `$table` $WHERE");
        }
        /**
        *Updates record with $primary[0]=$primary[1] in db $table 
        *@param string $table Name of table
        *@primary array Primary id of the record( array(columnname, value) )
        *@collumns array columns that will be selcted ('*' if null)
        *@return array mysql_result 
        */
        public function select($table, $primary=NULL, $columns=NULL, $order=NULL, $limit=NULL){
            $WHERE = $this->generateWhere($primary);
            if(!empty($columns)){
                foreach($columns AS $column){
                    $columnQuery[] = '`'.$column.'`';
                }
                $columnQuery = join(',', $columnQuery);
            }else{
                $columnQuery = '*';
            }
            
            if(!empty($order)){
                $ORDER = "ORDER BY $order[0] $order[1]";
            }else{
                $ORDER = "";
            }
            
            if(!empty($limit)){
                $limit = escape::sql($limit);
                $LIMIT = "LIMIT $limit";
            }else{
                $LIMIT = "";
            }
            
                $query = "SELECT $columnQuery FROM `$table` $WHERE $ORDER $LIMIT";
                foreach($this->pdoDB->query($query) AS $data){
                    $return[] = $data;
                }
                
                if(empty($return)){
                    return "the query '$query' didn't return any results";
                }else{
                    if(count($return) == 1){
                        return $return[0];
                    }else if(count($return) > 1){
                        return $return;
                    }
                }


            return $return;
        }
        
        public function query($query){
                foreach($this->pdoDB->query($query) AS $data){
                    $return[] = $data;
                }
                
                if(empty($return)){
                    return "the query '$query' didn't return any results";
                }else{
                    if(count($return) == 1){
                        return $return[0];
                    }else if(count($return) > 1){
                        return $return;
                    }
                }


            return $return;

        }
 }