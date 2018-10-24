<?php

/**
 * $ModDesc
 * 
 * @version     $Id: file.php $Revision
 * @package     modules
 * @subpackage  $Subpackage.
 * @copyright   Copyright (C) Jan 2012 leotheme.com <@emai:leotheme@gmail.com>.All rights reserved.
 * @license     GNU General Public License version 2
 */
if (!defined('_CAN_LOAD_FILES_'))
    exit;

class Datasample {

    public function createConfigSample($module, $configPrefix, $isCreateData) {
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE'))
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'configuration WHERE name LIKE (\'%' . pSQL($configPrefix) . '%\') AND `id_shop`=' . (int) Context::getContext()->shop->id;
        else
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'configuration WHERE name LIKE (\'%' . pSQL($configPrefix) . '%\') AND `id_shop` IS NULL';
        $oldData = "<?php";
        $data = Db::getInstance()->executeS($sql);

        if (!$data || empty($data)) {
            //echo Tools::displayError('Do not find configuration');
            return "ERROR_DATA_NULL";
        }

        $installFolder = _PS_MODULE_DIR_ . $module . "/install";
        if (!is_dir($installFolder))
            mkdir($installFolder, 0755);
        $backupfile = $installFolder . "/db_sample.php";

        if ($isCreateData == "OK") {
            $oldData = Tools::file_get_contents(_PS_MODULE_DIR_ . $module . "/install/db_sample.php");
        }

        if (!is_dir($installFolder))
            mkdir($installFolder, 0755);
        //if this module have query create table
        $fp = @fopen($backupfile, 'w');
        if ($fp === false) {
            echo Tools::displayError('Unable to create backup file') . ' "' . addslashes($backupfile) . '"';
            return "ERROR_WRITE_FILE";
        }
        $configData = "\n\$dataConfig = Array(";
        //echo "<pre>";print_r($data);die;
        foreach ($data as $key => $val) {
            if ($configData == "\n\$dataConfig = Array(")
                $configData .= "\"" . $val["name"] . "\"=>\"" . $val["value"] . "\"";
            else
                $configData .= ",\"" . $val["name"] . "\"=>\"" . $val["value"] . "\"";
        }
        $configData .= ")";
        fwrite($fp, $oldData);
        fwrite($fp, $configData . ";");
        fclose($fp);
        return "OK";
    }

    //install
    public function createTableSample($module, $tablePrefix, $installFolder="") {
        //get create table from Prefix
        $list = Db::getInstance()->executeS("SHOW TABLES LIKE  '%" . $tablePrefix . "%'");
        //$date = time();
        $createTable = "\$query = \"";
        $dataWithLang = "\$dataLang = Array(";
        $psBackupDropTable = 0;
        if (count($list)) {
            if($installFolder){
                $psBackupDropTable = 1;
                $backupfile = $installFolder . $module.time().".php";
            }else{
                $installFolder = _PS_MODULE_DIR_ . $module . "/install";
                $backupfile = $installFolder ."/db_sample.php";
            }
            if (!is_dir($installFolder))
                mkdir($installFolder, 0755);
            


            $fp = @fopen($backupfile, 'w');
            if ($fp === false) {
                echo Tools::displayError('Unable to create backup file') . ' "' . addslashes($backupfile) . '"';
                return "ERROR_WRITE_FILE";
            }
            fwrite($fp, "<?php");
            fwrite($fp, "\n/* Data sample for module" . $module . "*/\n");

            $dataLanguage = Array();

            $listLang = array();
            $languages = Language::getLanguages(true, Context::getContext()->shop->id);
            foreach ($languages as $lang) {
                $listLang[$lang["id_lang"]] = $lang["iso_code"];
            }

            foreach ($list as $table) {
                $table = current($table);
                $tableName = str_replace(_DB_PREFIX_, "_DB_PREFIX_", $table);
                // Skip tables which do not start with _DB_PREFIX_
                if (strlen($table) < strlen(_DB_PREFIX_) || strncmp($table, _DB_PREFIX_, strlen(_DB_PREFIX_)) != 0)
                    continue;
                $schema = Db::getInstance()->executeS('SHOW CREATE TABLE `' . $table . '`');

                if (count($schema) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table'])) {
                    fclose($fp);
                    //echo Tools::displayError('An error occurred while backing up. Unable to obtain the schema of') . ' "' . $table;
                    return "ERROR_BACKING_UP";
                }
                if(!$psBackupDropTable)
                    $createTable .= $schema[0]['Create Table'] . ";\n";
                else
                    $createTable .= "DROP TABLE IF EXISTS `".$tableName."`;\n".$schema[0]['Create Table'] . ";\n";

                if (strpos($schema[0]['Create Table'], "`id_shop`"))
                    $data = Db::getInstance()->query("SELECT * FROM `" . $schema[0]['Table'] . "` WHERE `id_shop`=" . (int) Context::getContext()->shop->id, false);
                else
                    $data = Db::getInstance()->query('SELECT * FROM `' . $schema[0]['Table'] . '`', false);

                $sizeof = DB::getInstance()->NumRows();
                $lines = explode("\n", $schema[0]['Create Table']);

                if ($data && $sizeof > 0) {
                    //if table is language
                    $id_language = 0;
                    if (strpos($schema[0]['Table'], "lang") !== false) {
                        $dataLanguage[$schema[0]['Table']] = array();
                        $i = 1;
                        while ($row = DB::getInstance()->nextRow($data)) {
                            $s = '(';
                            foreach ($row as $field => $value) {
                                if ($field == "id_lang") {
                                    $id_language = $value;
                                    $tmp = "'" . pSQL("LEO_ID_LANGUAGE", true) . "',";
                                } else if ($field == "id_shop") {
                                    $tmp = "'" . pSQL("LEO_ID_SHOP", true) . "',";
                                }
                                else
                                    $tmp = "'" . pSQL($value, true) . "',";

                                if ($tmp != "'',")
                                    $s .= $tmp;
                                else {
                                    foreach ($lines as $line)
                                        if (strpos($line, '`' . $field . '`') !== false) {
                                            if (preg_match('/(.*NOT NULL.*)/Ui', $line))
                                                $s .= "'',";
                                            else
                                                $s .= 'NULL,';
                                            break;
                                        }
                                }
                            }

                            if (!isset($listLang[$id_language]))
                                continue;

                            if (!isset($dataLanguage[$schema[0]['Table']][strtolower($listLang[$id_language])])) {
                                $dataLanguage[$schema[0]['Table']][strtolower($listLang[$id_language])] = 'INSERT INTO `' . $tableName . "` VALUES\n";
                            }

                            $s = rtrim($s, ',');
                            if ($i % 200 == 0 && $i < $sizeof)
                                $s .= ");\nINSERT INTO `" . $tableName . "` VALUES\n";
                            else
                                $s .= "),\n";
                            /* elseif ($i < $sizeof)
                              $s .= "),\n";
                              else
                              $s .= ");\n"; */
                            $dataLanguage[$schema[0]['Table']][strtolower($listLang[$id_language])] .= $s;
                            //++$i;
                        }
                    }
                    else if (strpos($schema[0]['Table'], "leomanagewidgets_shop") !== false) {
                        $createTable .= $this->createInsert($data, $tableName, $lines, $sizeof, $listLang, 1);
                    }
                    //normal table
                    else {
                        $createTable .= $this->createInsert($data, $tableName, $lines, $sizeof, $listLang);
                    }
                }
            }
            //foreach by table
            $tpl = array();
            if(!$psBackupDropTable){
                $createTable = str_replace('CREATE TABLE `' . _DB_PREFIX_, "CREATE TABLE IF NOT EXISTS `_DB_PREFIX_", $createTable);
                //$query = str_replace('"modules/' . $module . '/', '"'.__PS_BASE_URI__ . 'modules/' . $module . '/', $query);
                $createTable = str_replace('"'.__PS_BASE_URI__ . 'modules/' . $module , '"modules/' . $module , $createTable);
            }
            fwrite($fp, $createTable . "\";\n");
            if ($dataLanguage) {
                foreach ($dataLanguage as $key => $value) {
                    foreach ($value as $key1 => $value1) {
                        if (!isset($tpl[$key1]))
                            $tpl[$key1] = substr($value1, 0, -2) . ";\n";
                        else
                            $tpl[$key1] .= substr($value1, 0, -2) . ";\n";
                    }
                }

                foreach ($tpl as $key => $value) {
                    if ($dataWithLang != "\$dataLang = Array(")
                        $dataWithLang .= ",\"" . $key . "\"=>" . "\"" . $value . "\"";
                    else
                        $dataWithLang .= "\"" . $key . "\"=>" . "\"" . $value . "\"";
                }
                //delete base uri when export
				
                if(!$psBackupDropTable)
                    $dataWithLang = str_replace('"'.__PS_BASE_URI__ . 'modules/' . $module , '"modules/' . $module , $dataWithLang);
                fwrite($fp, $dataWithLang . ");");
            }
            fclose($fp);
        }
        return "OK";
    }

    public function createInsert($data, $tableName, $lines, $sizeof, $listLang, $specialTable = 0) {
        $dataNoLang = 'INSERT INTO `' . $tableName . "` VALUES\n";
        $i = 1;
        while ($row = DB::getInstance()->nextRow($data)) {
            if ($specialTable) {
                $title = unserialize(base64_decode($row['title']));
                $tmp = array();
                foreach ($title as $key => $value) {
                    if(isset($listLang[$key]))
                        $tmp[strtolower($listLang[$key])] = $value;
                }

                $row['title'] = base64_encode(serialize($tmp));

                $configs = unserialize(base64_decode($row['configs']));
                $tmp = array();

                foreach ($configs as $key => $value) {
					$value = str_replace('"'.__PS_BASE_URI__ . 'modules/' , '"modules/' , $value);
                    $tmpArr = explode("_", $key);
					//check if save language
                    if (isset($tmpArr[1]) && isset($listLang[$tmpArr[1]])) {
                        $str = $tmpArr[0] . "_" . strtolower($listLang[$tmpArr[1]]);
                        $tmp[$str] = $value;
                    } else {
                        $tmp[$key] = $value;
                    }
                }
				
                $row['configs'] = base64_encode(serialize($tmp));
            }

            $s = '(';
            foreach ($row as $field => $value) {
                if ($field == "id_shop") {
                    $tmp = "'" . pSQL("LEO_ID_SHOP", true) . "',";
                }
                else
                    $tmp = "'" . pSQL($value, true) . "',";
                if ($tmp != "'',")
                    $s .= $tmp;
                else {
                    foreach ($lines as $line)
                        if (strpos($line, '`' . $field . '`') !== false) {
                            if (preg_match('/(.*NOT NULL.*)/Ui', $line))
                                $s .= "'',";
                            else
                                $s .= 'NULL,';
                            break;
                        }
                }
            }
            $s = rtrim($s, ',');
            if ($i % 200 == 0 && $i < $sizeof)
                $s .= ");\nINSERT INTO `" . $tableName . "` VALUES\n";
            elseif ($i < $sizeof)
                $s .= "),\n";
            else
                $s .= ");\n";
            $dataNoLang .= $s;

            ++$i;
        }
        return $dataNoLang;
        //$dataNoLang = str_replace('INSERT INTO ` `'._DB_PREFIX_,"INSERT INTO ` `_DB_PREFIX_",$dataNoLang);
        //$createTable .= $dataNoLang;
    }

    /*
     * export db struct to download file
     */

    public function exportDBStruct() {
        $ignore_insert_table = array(
            _DB_PREFIX_ . 'sekeyword', _DB_PREFIX_ . 'statssearch', _DB_PREFIX_ . 'favorite_product',
            _DB_PREFIX_ . 'pagenotfound');
        //copy + export to 
        $installFolder = _PS_MODULE_DIR_ . "leotempcp/install";
        if (!is_dir($installFolder))
            mkdir($installFolder, 0755);
        $backupfile = $installFolder . "/db_structure.sql";

        $fp = @fopen($backupfile, 'w');
        if ($fp === false) {
            return "ERROR_WRITE_FILE";
        }
        fwrite($fp, 'SET NAMES \'utf8\';' . "\n\n");
        // Find all tables
        $tables = Db::getInstance()->executeS('SHOW TABLES');
        //$found = 0;
        $data = "";
        
        foreach ($tables as $table) {
            $table = current($table);

            // Skip tables which do not start with _DB_PREFIX_
            if (strlen($table) < strlen(_DB_PREFIX_) || strncmp($table, _DB_PREFIX_, strlen(_DB_PREFIX_)) != 0)
                continue;
            // Export the table schema
            $schema = Db::getInstance()->executeS('SHOW CREATE TABLE `' . $table . '`');
            if (in_array($schema[0]['Table'], $ignore_insert_table)) {
                continue;
            }
            
            $data .= $schema[0]['Create Table'] . ";\n\n";
            if (count($schema) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table'])) {
                fclose($fp);
                //$this->delete();
                return $table;
                //Tools::displayError('An error occurred while backing up. Unable to obtain the schema of').' "'.$table;
            }
        }
        
        $data = str_replace("CREATE TABLE `" . _DB_PREFIX_, "CREATE TABLE `PREFIX_", $data);
        //$tableName = str_replace(_DB_PREFIX_, "_DB_PREFIX_", $table);
        fwrite($fp, $data);
        fclose($fp);
        return "OK";
    }

    /*
     * export db data to download file
     */

    public function exportThemeSql() {
        $ignore_insert_table = array(
            _DB_PREFIX_ . 'connections', _DB_PREFIX_ . 'connections_page', _DB_PREFIX_ . 'connections_source',
            _DB_PREFIX_ . 'guest', _DB_PREFIX_ . 'statssearch',
            _DB_PREFIX_ . 'sekeyword', _DB_PREFIX_ . 'favorite_product',
            _DB_PREFIX_ . 'pagenotfound', _DB_PREFIX_ . 'shop_url',
            _DB_PREFIX_ . 'employee', _DB_PREFIX_ . 'employee_shop',
            _DB_PREFIX_ . 'contact', _DB_PREFIX_ . 'contact_lang',
            _DB_PREFIX_ . 'contact', _DB_PREFIX_ . 'contact_shop'
        );
        $installFolder = _PS_MODULE_DIR_ . "leotempcp/install";
        if (!is_dir($installFolder))
            mkdir($installFolder, 0755);
        $backupfile = $installFolder . "/theme.sql";

        $fp = @fopen($backupfile, 'w');
        if ($fp === false) {
            return "ERROR_WRITE_FILE";
        }
        fwrite($fp, 'SET NAMES \'utf8\';' . "\n\n");
        // Find all tables
        $tables = Db::getInstance()->executeS('SHOW TABLES');
        $found = 0;
        $sql = '';
        foreach ($tables as $table) {
            $table = current($table);

            // Skip tables which do not start with _DB_PREFIX_
            if (strlen($table) < strlen(_DB_PREFIX_) || strncmp($table, _DB_PREFIX_, strlen(_DB_PREFIX_)) != 0)
                continue;

            // Export the table schema
            $schema = Db::getInstance()->executeS('SHOW CREATE TABLE `' . $table . '`');

            if (count($schema) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table'])) {
                fclose($fp);
                //$this->delete();
                //echo Tools::displayError('An error occurred while backing up. Unable to obtain the schema of').' "'.$table;
                return $table;
            }

            if (!in_array($schema[0]['Table'], $ignore_insert_table)) {
                $sql .= "\n" . 'TRUNCATE TABLE ' . str_replace("`"._DB_PREFIX_ , "`PREFIX_", "`".$schema[0]['Table']) . '`;' . "\n";

                $data = Db::getInstance()->query('SELECT * FROM `' . $schema[0]['Table'] . '`', false);
                $sizeof = DB::getInstance()->NumRows();
                $lines = explode("\n", $schema[0]['Create Table']);

                if ($data && $sizeof > 0) {
                    // Export the table data
                    $sql .= 'INSERT INTO ' . str_replace('`'._DB_PREFIX_, '`PREFIX_', '`'.$schema[0]['Table']) . "` VALUES\n";
                    //fwrite($fp, 'INSERT INTO `'.$schema[0]['Table']."` VALUES\n");
                    $i = 1;
                    while ($row = DB::getInstance()->nextRow($data)) {
                        $s = '(';

                        foreach ($row as $field => $value) {
							//special table
							if($schema[0]['Table'] == _DB_PREFIX_."leomanagewidgets_shop" && $field == "configs"){
								$configs = unserialize(base64_decode($value));
								foreach ($configs as $kconfig=>$vconfig){
                                    if(strpos($kconfig, "title_") !== false || strpos($kconfig, "content_") !== false || strpos($kconfig, "description_") !== false){
											$configs[$kconfig] = str_replace('"'.__PS_BASE_URI__ . 'modules/' , '"modules/' , $vconfig);
                                    }
                                }
								$value = base64_encode(serialize($configs));
							}

                            $tmp = "'" . pSQL($value, true) . "',";
                            if ($tmp != "'',")
                                $s .= $tmp;
                            else {
                                foreach ($lines as $line)
                                    if (strpos($line, '`' . $field . '`') !== false) {
                                        if (preg_match('/(.*NOT NULL.*)/Ui', $line))
                                            $s .= "'',";
                                        else
                                            $s .= 'NULL,';
                                        break;
                                    }
                            }
                        }
                        $s = rtrim($s, ',');

                        if ($i % 200 == 0 && $i < $sizeof)
                            $s .= ");\nINSERT INTO " . str_replace('`'._DB_PREFIX_, '`PREFIX_', '`'.$schema[0]['Table']) . "` VALUES\n";
                        elseif ($i < $sizeof)
                            $s .= "),\n";
                        else
                            $s .= ");\n";
                        $sql .= $s;
                        
                        //fwrite($fp, $s);
                        ++$i;
                    }
                }
            }
            $found++;
        }
        //table PREFIX_condition
        $sql = str_replace(" "._DB_PREFIX_, " PREFIX_", $sql);
        //img link
        $sql = str_replace('src=\"'.__PS_BASE_URI__ . 'modules/' , 'src=\"modules/' , $sql);
        
        fwrite($fp, $sql);
        fclose($fp);
        if ($found == 0) {
            //echo Tools::displayError('No valid tables were found to backup.' );
            return "NO_VALID";
        }

        return true;
    }

    /*
     * export db struct to download file
     */

    public function installSampleModule($file, $module='') {
        require_once( $file );
        //install with no language
        if (isset($query) && !empty($query)) {
            $query = str_replace("_DB_PREFIX_", _DB_PREFIX_, $query);
            $query = str_replace("_MYSQL_ENGINE_", _MYSQL_ENGINE_, $query);
            $query = str_replace("LEO_ID_SHOP", (int) Context::getContext()->shop->id, $query);
            $query = str_replace("_MYSQL_ENGINE_", _MYSQL_ENGINE_, $query);
            $query = str_replace("\\'", "\'", $query);
            if($module)
                $query = str_replace('"modules/' . $module . '/', '"'.__PS_BASE_URI__ . 'modules/' . $module . '/', $query);

            $db_data_settings = preg_split("/;\s*[\r\n]+/", $query);
            foreach ($db_data_settings as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    if (!Db::getInstance()->Execute($query)) {
                        //echo "---error--" . $query . "---error--";
                        return false;
                    }
                }
            }
        }
        //echo "<pre>";print_r($dataLang);
        //install with with language
        if (isset($dataLang) && !empty($dataLang)) {
            $languages = Language::getLanguages(true, Context::getContext()->shop->id);
            //print_r($languages);die;
            foreach ($languages as $lang) {
                if (isset($dataLang[strtolower($lang["iso_code"])])){
                    $query = str_replace("_DB_PREFIX_", _DB_PREFIX_, $dataLang[strtolower($lang["iso_code"])]);
                }
				//if not exist language in list, get en
                else {
                    if (isset($dataLang["en"]))
                        $query = str_replace("_DB_PREFIX_", _DB_PREFIX_, $dataLang["en"]);
					//firt item in array
                    else {
                        foreach ($dataLang as $key => $value) {
                            $query = str_replace("_DB_PREFIX_", _DB_PREFIX_, $dataLang[$key]);
                            break;
                        }
                    }
                }
                $query = str_replace("_MYSQL_ENGINE_", _MYSQL_ENGINE_, $query);
                $query = str_replace("LEO_ID_SHOP", (int) Context::getContext()->shop->id, $query);
                $query = str_replace("LEO_ID_LANGUAGE", (int) $lang["id_lang"], $query);
                $query = str_replace("\\\'", "\'", $query);
                if($module)
                    $query = str_replace('"modules/' . $module . '/', '"'.__PS_BASE_URI__ . 'modules/' . $module . '/', $query);

                $db_data_settings = preg_split("/;\s*[\r\n]+/", $query);
                foreach ($db_data_settings as $query) {
                    $query = trim($query);
                    if (!empty($query)) {
                        //echo "---".$query."<br/>";
                        if (!Db::getInstance()->Execute($query)) {
                            return false;
                        }
                    }
                }
            }
        }
        
        //install config
        //echo "<pre>";print_r($dataConfig);die;
        if (isset($dataConfig) && !empty($dataConfig)) {
            foreach ($dataConfig as $key => $value) {
                //print_r($value);die;
                Configuration::updateValue($key, $value);
            }
        }

        return true;
    }

    /*
     * sample for serialize module
     */

    public function installWidgetsModule($file, $module) {
        require_once( $file );
        if (!isset($query)) return false;
        $query = str_replace('_DB_PREFIX_', _DB_PREFIX_, $query);
        $query = preg_split("/;\s*[\r\n]+/", $query);

        foreach ($query as $val) {
            if (!empty($val)) {
                $val = str_replace("LEO_ID_SHOP", (int) Context::getContext()->shop->id, $val);
                $val = str_replace("\\'", "\'", $val);
                //error because primary_key very long
                if (!Db::getInstance()->Execute(trim($val))){
                    if(strpos($val, "PRIMARY KEY (`id_leomanagewidgets`,`id_shop`,`hook`,`file_name`)") !== false ){
                        $val = str_replace("PRIMARY KEY (`id_leomanagewidgets`,`id_shop`,`hook`,`file_name`)", "PRIMARY KEY (`id_leomanagewidgets`,`id_shop`,`hook`)", $val);
                        if (!Db::getInstance()->Execute(trim($val))) return false;
                    }else return false;
                }
            }
        }
        
        $res = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'leomanagewidgets_shop`');
                
        if($res){
                $new_res = true;
                $langs = Language::getLanguages(false);
                foreach($res as $row){
                        $titles = unserialize(base64_decode($row['title']));
                        $configs = unserialize(base64_decode($row['configs']));
                        $tmpTitle  = array();
                        $tmpConfigs = array();

                        foreach($langs as $lang){

                                foreach ($titles as $key=>$value){
                                    if(strtolower($lang["iso_code"])==strtolower($key)){ 
                                        $tmpTitle[$lang["id_lang"]] = $value;
                                    }
                                }

                                foreach ($configs as $key=>$value){
                                    if((strpos($key, "title_") !== false || strpos($key, "content_") !== false || strpos($key, "description_") !== false) &&  strpos($key, "_".$lang["iso_code"]) !== false){
                                            $value = str_replace('"modules/leomanagewidgets/', '"'.__PS_BASE_URI__.'modules/leomanagewidgets/', $value);
                                            $tmpConfigs[str_replace("_".$lang["iso_code"], "_".$lang["id_lang"], $key)] = $value;
                                    }else $tmpConfigs[$key] = $value;
                                }
                        }
                        $new_res &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'leomanagewidgets_shop` SET title=\''.base64_encode(serialize($tmpTitle)).'\'  WHERE `id_leomanagewidgets` = '.(int)$row['id_leomanagewidgets'].' AND id_shop = '.(int)$row['id_shop']);
                        $new_res &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'leomanagewidgets_shop` SET configs=\''.base64_encode(serialize($tmpConfigs)).'\'  WHERE `id_leomanagewidgets` = '.(int)$row['id_leomanagewidgets'].' AND id_shop = '.(int)$row['id_shop']);
                }
        }
        return true;
    }

}