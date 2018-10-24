<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 13.12.2015
 * Time: 1:45
 */
class Rep2
{
    var $repUrl; //Ссылка на репозиторий
    var $admin; //Сущность класса Admin
    var $path_to_host; //Путь до корня престашоп
    var $xmlFile; //Путь до XML файла
    var $errors = Array();

    var $overrideIfExist = false; //Указываем, если модуль нужно перезаливать
    /**
     * Rep constructor.
     */
    public function __construct($repUrl = REP_URL, $path_to_host = '', $xmlFileContent = '')
    {
        $this->repUrl = $repUrl;
        if(!$xmlFileContent) $this->xmlFileContent = dirname(__FILE__).'/../modules/info.xml';
        else $this->xmlFileContent = $xmlFileContent;
        if(!$path_to_host){
            $this->path_to_host = dirname(__FILE__).'/../'; //Тот случай, когда работаем внутри модуля, в других случаях указываем путь корня престашоп
        }else $this->path_to_host = $path_to_host;
    }

    /**
     * @desc Получение ссылки на модуль, чтобы потом его скачтаь
     * @param $name
     * @return bool|string
     */
    public function getUrlFromName($name){
        $modules = $this->getModulesForInstall(); //Получаем список всех доступных модулей для установки
        if($modules[$name]){
            $url = strlen((string)$modules[$name]->file) ? (string)$modules[$name]->file : $name.".zip";
            return $this->repUrl.$url;
        }
        $this->errors[] = "Модуль не был найден в списке URL";
        return false;
    }

    public function copyModuleAndUnzip($url){
        $zipName = "Имя не определенно";
        if ($url) {
            $zipName = explode("/", $url);
            $zipName = $zipName[count($zipName)-1];
            if(!is_dir($this->path_to_host.'/modules/'.basename($zipName, '.zip')) || !$this->overrideIfExist){
                $destination = $this->path_to_host . '/modules/' . $zipName;
                copy($url, $destination);
                if ($this->extractArchive($destination)) return true;
            } else $this->errors[] = "Папка с таким модулем уже существует";
        } else $this->errors[] = "Не получен URL";
        $this->errors[] = "Не смогли разархивировать модуль ".$zipName;
        return false;
    }

    public function getModule($module_name){
        if($this->copyModuleAndUnzip($this->getUrlFromName($module_name))){
            return true;
        }
        return false;
    }

    public function getModulesForInstall(){
        if(!$this->xmlFileContent) {
            $file = $this->xmlFile;
            if (!file_exists($file)) return false;
            $this->xmlFileContent = file_get_contents($file);
        }
        $modulesXML = new SimpleXMLElement($this->xmlFileContent);

        $modules = Array();
        foreach ($modulesXML[0]->module as $item) {
            $modules[(string)$item->name] = $item;
        }
        return $modules;
    }

    public function extractArchive($file)
    {
        $zip_folders = array();
        $tmp_folder = _PS_MODULE_DIR_.md5(time());

        $success = false;
        if (substr($file, -4) == '.zip') {
            if (Tools::ZipExtract($file, $tmp_folder)) {
                $zip_folders = scandir($tmp_folder);
                if (Tools::ZipExtract($file, _PS_MODULE_DIR_)) {
                    $success = true;
                }
            }
        } else {
            require_once(_PS_TOOL_DIR_.'tar/Archive_Tar.php');
            $archive = new Archive_Tar($file);
            if ($archive->extract($tmp_folder)) {
                $zip_folders = scandir($tmp_folder);
                if ($archive->extract(_PS_MODULE_DIR_)) {
                    $success = true;
                }
            }
        }

        if (!$success) {
            $this->errors[] = Tools::displayError('There was an error while extracting the module (file may be corrupted).');
        } else {
            //check if it's a real module
            foreach ($zip_folders as $folder) {
                if (!in_array($folder, array('.', '..', '.svn', '.git', '__MACOSX')) && !Module::getInstanceByName($folder)) {
                    $this->errors[] = sprintf(Tools::displayError('The module %1$s that you uploaded is not a valid module.'), $folder);
                    self::recursiveDeleteOnDisk(_PS_MODULE_DIR_.$folder);
                }
            }
        }

        @unlink($file);
        self::recursiveDeleteOnDisk($tmp_folder);

        return $success;
    }

    public static function recursiveDeleteOnDisk($dir)
    {
        if (strpos(realpath($dir), realpath(_PS_MODULE_DIR_)) === false) {
            return;
        }
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir.'/'.$object) == 'dir') {
                        self::recursiveDeleteOnDisk($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

}