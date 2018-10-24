<?php
abstract class NewsSystem
{
    public      $name               = 'newscore';
    public      $table              = 'news';
    protected   $identifier         = 'id_entry';
    public      $classPath;
    public      $className;
    public      $lang               = true;
    public      $edit               = true;
    public      $view               = true;
    public      $delete             = true;
    protected   $_select;
    protected   $_join;
    protected   $_where;
    protected   $_group;
    protected   $_having;
    public      $fieldsDisplay      = array();
    protected   $_list              = array();
    protected   $_listTotal         = 0;
    protected   $_filter            = '';
    protected   $_tmpTableFilter    = '';
    protected   $_pagination        = array(20, 50, 100, 300);
    protected   $_orderBy;
    protected   $_defaultOrderBy    = false;
    protected   $_orderWay;
    public      $_errors            = array();
    private     $_object            = false;
    private     $_className         = 'News';
    protected   $token;
    
    public function __construct()
    {
        global $cookie;
        
        if (!$this->identifier) { 
            $this->identifier = 'id_'.$this->table;
        }
        
        if (!$this->_defaultOrderBy) { 
            $this->_defaultOrderBy = $this->identifier;
        }
    }
    
    public function l($string, $specific = false)
    {
        global $_MODULES, $_MODULE, $cookie;
        $id_lang = (!isset($cookie) OR !is_object($cookie)) ? intval(Configuration::get('PS_LANG_DEFAULT')) : intval($cookie->id_lang);

        $file = _PS_MODULE_DIR_.$this->name.'/'.Language::getIsoById($id_lang).'.php';
        
        if (file_exists($file) AND include_once($file)) {
            $_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;
        }

        if (!is_array($_MODULES)) {
            return (str_replace('"', '&quot;', $string));
        }

        $source = $specific ? $specific : get_class($this);
        $string2 = str_replace('\'', '\\\'', $string);
        $currentKey = '<{'.$this->name.'}'._THEME_NAME_.'>'.$source.'_'.md5($string2);
        $defaultKey = '<{'.$this->name.'}prestashop>'.$source.'_'.md5($string2);
        
        if (key_exists($currentKey, $_MODULES)) {
            $ret = stripslashes($_MODULES[$currentKey]);
        } elseif (key_exists($defaultKey, $_MODULES)) {
            $ret = stripslashes($_MODULES[$defaultKey]);
        } else {
            $ret = $string;
        }
        
        return str_replace('"', '&quot;', $ret);
    }
    
    public function display()
    {
        global $currentIndex, $cookie;
       // echo '<strong>' . $this->l('Be sure to read') . ' <a style="color:#268CCD;" href="http://www.eihwazblog.com/cms.php?id_cms=9" target="_blank">' . $this->l('the legal notice') . '</a>.</strong><br /><br />';
        $this->displayErrors();

        // Include current tab
        if ((Tools::getValue('submitAdd'.$this->table) AND sizeof($this->_errors)) OR isset($_GET['add'.$this->table])) {
            $this->displayForm();
            
            echo '
            <br /><br />
            <a href="'.$currentIndex.'&token='.$this->token.'">
                <img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list', __CLASS__).'
            </a><br />';
        } elseif (isset($_GET['update'.$this->table])) {
            $this->displayForm();
            
            echo '
            <br /><br />
            <a href="'.$currentIndex.'&token='.$this->token.'">
                <img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list', __CLASS__).'
            </a><br />';
        } elseif (isset($_GET['view'.$this->table])) {
            echo '
            <a href="'.$currentIndex.'&token='.$this->token.'">
                <img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list', __CLASS__).'
            </a><br /><br />';
            
            $this->{'view'.$this->table}();
            
            echo '
            <br />
            <a href="'.$currentIndex.'&token='.$this->token.'">
                <img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list', __CLASS__).'
            </a><br />';
        } else {
            $this->getList(intval($cookie->id_lang));
            $this->displayList();
            $this->displayOptionsList();
        }
    }
    
    public function displayListHeader($token = NULL)
    {
        global $currentIndex, $cookie;
        if (!isset($token) OR empty($token)) {
            $token = $this->token;
        }

        /* Determine total page number */
        $totalPages = ceil($this->_listTotal / Tools::getValue('pagination', (isset($cookie->{$this->table.'_pagination'}) ? $cookie->{$this->table.'_pagination'} : $this->_pagination[0])));
        if (!$totalPages) $totalPages = 1;
        
        echo '<a name="' . $this->table . '">&nbsp;</a>';
        echo '<form method="post" action="' . $currentIndex . '&token=' . $token;
        
        if (Tools::getIsset($this->table.'Orderby')) {
            echo '&'.$this->table.'Orderby=' . urlencode($this->_orderBy) .
            '&' . $this->table . 'Orderway=' . urlencode(strtolower($this->_orderWay));    
        }
        
        echo '#' . $this->table . '" class="form">
        <input type="hidden" id="submitFilter' . $this->table . '" name="submitFilter' . $this->table . '" value="0">
        <table>
            <tr>
                <td style="vertical-align: bottom;">
                    <span style="float: left;">';

        /* Determine current page number */
        $page = intval(Tools::getValue('submitFilter'.$this->table));
        if (!$page) {
            $page = 1;
        }
        
        if ($page > 1) {
            echo '
                        <input type="image" src="../img/admin/list-prev2.gif" onclick="getE(\'submitFilter'.$this->table.'\').value=1"/>
                        &nbsp; <input type="image" src="../img/admin/list-prev.gif" onclick="getE(\'submitFilter'.$this->table.'\').value='.($page - 1).'"/> ';
        }
        
        echo $this->l('Page', __CLASS__).' <b>'.$page.'</b> / '.$totalPages;
        
        if ($page < $totalPages) {
            echo '
                        <input type="image" src="../img/admin/list-next.gif" onclick="getE(\'submitFilter'.$this->table.'\').value='.($page + 1).'"/>
                         &nbsp;<input type="image" src="../img/admin/list-next2.gif" onclick="getE(\'submitFilter'.$this->table.'\').value='.$totalPages.'"/>';
        }
        
        echo '            | '.$this->l('Display', __CLASS__).'
                        <select name="pagination">';
        /* Choose number of results per page */
        $selectedPagination = Tools::getValue('pagination', (isset($cookie->{$this->table.'_pagination'}) ? $cookie->{$this->table.'_pagination'} : NULL));
        
        foreach ($this->_pagination AS $value) {
            echo '<option value="'.intval($value).'"'.($selectedPagination == $value ? ' selected="selected"' : (($selectedPagination == NULL && $value == $this->_pagination[1]) ? ' selected="selected2"' : '')).'>'.intval($value).'</option>';
        }
        
        echo '
                        </select>
                        / '.intval($this->_listTotal).' '.$this->l('result(s)', __CLASS__).'
                    </span>
                    <span style="float: right;">
                        <input type="submit" name="submitReset'.$this->table.'" value="'.$this->l('Reset', __CLASS__).'" class="button" />
                        <input type="submit" id="submitFilterButton_'.$this->table.'" name="submitFilter" value="'.$this->l('Filter', __CLASS__).'" class="button" />
                    </span>
                    <span class="clear"></span>
                </td>
            </tr>
            <tr>
                <td>';
        
        echo '<table class="table" cellpadding="0" cellspacing="0"><tr class="nodrag nodrop">';
        
        if ($this->delete) {
            echo '<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'' . $this->table . 'Box[]\', this.checked)" /></th>';
        }
        
        foreach ($this->fieldsDisplay AS $key => $params) {
            echo '
                <th ' . (isset($params['widthColumn']) ? 'style="width: ' . $params['widthColumn'] . 'px"' : '') . '>
                    ' . $params['title'];
                    
            if (!isset($params['orderby']) OR $params['orderby']) {
                echo '<br />
                    <a href="' . $currentIndex . '&' . $this->table . 'Orderby=' . urlencode($key) . '&' . $this->table . 'Orderway=desc&token=' . $token . '"><img border="0" src="../img/admin/down' . ((isset($this->_orderBy) AND ($key == $this->_orderBy) AND ($this->_orderWay == 'DESC')) ? '_d' : '') . '.gif" /></a>
                    <a href="' . $currentIndex . '&' . $this->table . 'Orderby=' . urlencode($key) . '&' . $this->table . 'Orderway=asc&token=' . $token . '"><img border="0" src="../img/admin/up' . ((isset($this->_orderBy) AND ($key == $this->_orderBy) AND ($this->_orderWay == 'ASC')) ? '_d' : '') . '.gif" /></a>';
            }
            
            echo '
                </th>';
        }

        /* Check if object can be modified, deleted or detailed */
        echo '<th style="width: 52px">' . $this->l('Actions', __CLASS__) . '</th>';
        echo '</tr><tr class="nodrag nodrop" style="height: 35px;">';

        if ($this->delete) {
            echo '<td class="center">--</td>';
        }

        /* Javascript hack in order to catch ENTER keypress event */
        $keyPress = 'onkeypress="formSubmit(event, \'submitFilterButton_' . $this->table . '\');"';

        /* Filters (input, select, date or bool) */
        foreach ($this->fieldsDisplay AS $key => $params) {
            $width = (isset($params['width']) ? ' style="width: ' . intval($params['width']) . 'px;"' : '');
            echo '<td' . (isset($params['align']) ? ' class="' . $params['align'] . '"' : '') . '>';
            if (!isset($params['type'])) {
                $params['type'] = 'text';
            }

            $value = Tools::getValue('conf') ? NULL : Tools::getValue($this->table . 'Filter_' . (array_key_exists('filter_key', $params) ? $params['filter_key'] : $key));
            if (isset($params['search']) AND !$params['search']) {
                echo '--</td>';
                continue;
            }
            
            switch ($params['type']) {
                case 'bool':
                    echo '
                    <select name="'.$this->table.'Filter_' . $key . '">
                        <option value="">--</option>
                        <option value="1"' . ($value == 1 ? ' selected="selected"' : '') . '>' . $this->l('Yes', __CLASS__) . '</option>
                        <option value="0"' . (($value == 0 AND $value != '') ? ' selected="selected"' : '') . '>' . $this->l('No', __CLASS__) . '</option>
                    </select>';
                    break;

                case 'date':
                case 'datetime':
                    if (is_string($value)) {
                        $value = unserialize($value);
                    }
                    
                    $name = $this->table.'Filter_'.(isset($params['filter_key']) ? $params['filter_key'] : $key);
                    $nameId = str_replace('!', '__', $name);
                    includeDatepicker(array($nameId.'_0', $nameId.'_1'));
                    echo $this->l('From', __CLASS__).' <input type="text" id="'.$nameId.'_0" name="'.$name.'[0]" value="'.(isset($value[0]) ? $value[0] : '').'"'.$width.' '.$keyPress.' /><br />
                    '.$this->l('To', __CLASS__).' <input type="text" id="'.$nameId.'_1" name="'.$name.'[1]" value="'.(isset($value[1]) ? $value[1] : '').'"'.$width.' '.$keyPress.' />';
                    break;

                case 'select':
                    if (isset($params['filter_key'])) {
                        echo '<select onchange="getE(\'submitFilter'.$this->table.'\').focus();getE(\'submitFilter'.$this->table.'\').click();" name="'.$this->table.'Filter_'.$params['filter_key'].'" '.(isset($params['width']) ? 'style="width: '.$params['width'].'px"' : '').'>
                                <option value=""'.(($value == 0 AND $value != '') ? ' selected="selected"' : '').'>--</option>';
                        if (isset($params['select']) AND is_array($params['select'])) {
                            foreach ($params['select'] AS $optionValue => $optionDisplay) {
                                echo '<option value="'.$optionValue.'"'.((isset($_POST[$this->table.'Filter_'.$params['filter_key']]) AND Tools::getValue($this->table.'Filter_'.$params['filter_key']) == $optionValue AND Tools::getValue($this->table.'Filter_'.$params['filter_key']) != '') ? ' selected="selected"' : '').'>'.$optionDisplay.'</option>';
                            }
                        }
                        echo '</select>';
                    }
                    break;

                case 'text':
                default:
                    echo '<input type="text" name="'.$this->table.'Filter_'.(isset($params['filter_key']) ? $params['filter_key'] : $key).'" value="'.htmlentities($value, ENT_COMPAT, 'UTF-8').'"'.$width.' '.$keyPress.' />';
            }
            echo '</td>';
        }

        if ($this->edit OR $this->delete OR ($this->view AND $this->view != 'noActionColumn')) {
            echo '<td class="center">--</td>';
        }

        echo '</tr>';
    }
    
    public function displayList()
    {
        /* Append when we get a syntax error in SQL query */
        if ($this->_list === false) {
            $this->displayWarning($this->l('Bad SQL query', __CLASS__));
            return false;
        }

        /* Display list header (filtering, pagination and column names) */
        $this->displayListHeader();
        if (!sizeof($this->_list)) {
            echo '<tr><td class="center" colspan="'.sizeof($this->fieldsDisplay).'">'.$this->l('Записей не найдено', __CLASS__).'</td></tr>';
        }

        /* Show the content of the table */
        $this->displayListContent();

        /* Close list table and submit button */
        $this->displayListFooter();
    }

    public function displayListContent($token=NULL)
    {
        global $currentIndex, $cookie;

        $irow = 0;
        if ($this->_list) {
            foreach ($this->_list AS $i => $tr) {
                $id = $tr[$this->identifier];
                echo '<tr' . ($irow++ % 2 ? ' class="alt_row"' : '') . '>';
                
                if ($this->delete) {
                    echo '<td class="center"><input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" /></td>';
                }

                foreach ($this->fieldsDisplay AS $key => $params) {
                    $tmp = explode('!', $key);
                    $key = isset($tmp[1]) ? $tmp[1] : $tmp[0];
                    echo '
                    <td class="pointer' . (isset($params['align']) ? ' '.$params['align'] : '').'">';
                    
                    if (isset($params['active']) AND isset($tr[$key])) {
                        echo '
                        <a href="' . $currentIndex . '&' . $this->identifier . '=' . $id . '&' . $params['active'] .
                        ((($id_category = intval(Tools::getValue('id_category'))) AND Tools::getValue('id_product')) ? '&id_category='.$id_category : '').'&token='.($token!=NULL ? $token : $this->token).'">
                        <img src="../img/admin/'.($tr[$key] ? 'enabled.gif' : 'disabled.gif').'"
                        alt="'.($tr[$key] ? $this->l('Enabled', __CLASS__) : $this->l('Disabled', __CLASS__)).'" title="'.($tr[$key] ? $this->l('Enabled', __CLASS__) : $this->l('Disabled', __CLASS__)).'" /></a>';
                    } elseif (isset($params['activeVisu']) AND isset($tr[$key])) {
                        echo '<img src="../img/admin/'.($tr[$key] ? 'enabled.gif' : 'disabled.gif').'"
                        alt="'.($tr[$key] ? $this->l('Enabled', __CLASS__) : $this->l('Disabled', __CLASS__)).'" title="'.($tr[$key] ? $this->l('Enabled', __CLASS__) : $this->l('Disabled', __CLASS__)).'" />';
                    } elseif (isset($params['type']) AND $params['type'] == 'date') {
                        echo Tools::displayDate($tr[$key], $cookie->id_lang);
                    } elseif (isset($params['type']) AND $params['type'] == 'datetime') {
                        echo Tools::displayDate($tr[$key], $cookie->id_lang, true);
                    } elseif (isset($tr[$key])) {
                        $echo = (isset($params['maxlength']) ? Tools::substr($tr[$key], 0, $params['maxlength']) . '...' : $tr[$key]);
                        echo isset($params['callback']) ? call_user_func_array(array($this->className, $params['callback']), array($echo, $tr)) : $echo;
                    } else {
                        echo '--';
                    }

                    echo (isset($params['suffix']) ? $params['suffix'] : '').
                    '</td>';
                }
                
                if ($this->edit OR $this->delete OR ($this->view AND $this->view != 'noActionColumn')) {
                    echo '<td class="center" style="white-space: nowrap;">';
                    
                    if ($this->view) {
                        echo '
                        <a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&view'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'">
                        <img src="../img/admin/details.gif" border="0" alt="'.$this->l('View', __CLASS__).'" title="'.$this->l('View', __CLASS__).'" /></a>';
                    }
                    
                    if ($this->edit) {
                        echo '
                        <a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&update'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'">
                        <img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit', __CLASS__).'" title="'.$this->l('Edit', __CLASS__).'" /></a>';
                    }
                    
                    if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete))) {
                        echo '
                        <a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'" onclick="return confirm(\''.$this->l('Delete item #', __CLASS__).$id.' ?\');">
                        <img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete', __CLASS__).'" title="'.$this->l('Delete', __CLASS__).'" /></a>';
                    }
                }
                echo '</tr>';
            }
        }
    }

    /**
     * Close list table and submit button
     */
    public function displayListFooter($token = NULL)
    {
        echo '</table>';
        
        if ($this->delete) {
            echo '<p><input type="submit" class="button" name="submitDel'.$this->table.'" value="'.$this->l('Delete selection', __CLASS__).'" onclick="return confirm(\''.$this->l('Delete selected items?', __CLASS__).'\');" /></p>';
        }
        
        echo '
                </td>
            </tr>
        </table>
        <input type="hidden" name="token" value="' . $this->token . '" />
        </form>';
    }

    /**
     * Options lists
     */
    public function displayOptionsList()
    {
        global $currentIndex, $cookie, $tab;

        if (!isset($this->_fieldsOptions) OR !sizeof($this->_fieldsOptions)) {
            return false;
        }

        $defaultLanguage    = intval(Configuration::get('PS_LANG_DEFAULT'));
        $languages          = Language::getLanguages();
        $tab                = Tab::getTab(intval($cookie->id_lang), Tab::getIdFromClassName($tab));
        echo '<br /><br />';
        echo (isset($this->optionTitle) ? '<h2>'.$this->optionTitle.'</h2>' : '');
        echo '
        <script type="text/javascript">
            id_language = Number('.$defaultLanguage.');
        </script>
        <form action="'.$currentIndex.'" id="'.$tab['name'].'" name="'.$tab['name'].'" method="post" class="width4">
            <fieldset>';
        echo (isset($this->optionTitle) ? '<legend><img src="../img/t/'.$tab['class_name'].'.gif" />'.$this->optionTitle.'</legend>' : '');
        
        foreach ($this->_fieldsOptions AS $key => $field) {
            $val = Tools::getValue($key, Configuration::get($key));
            echo'
                <label>'.$field['title'].' </label>
                <div class="margin-form">';

            switch ($field['type']) {
                case 'select':
                    echo '<select name="'.$key.'">';
                    foreach ($field['list'] AS $value)
                        echo '<option
                            value="'.(isset($field['cast']) ? $field['cast']($value[$field['identifier']]) : $value[$field['identifier']]).'"'.($val == $value[$field['identifier']] ? ' selected="selected"' : '').'>'.$value['name'].'</option>';
                    echo '</select>';
                break ;

                case 'bool':
                    echo '<label class="t" for="'.$key.'_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Yes', __CLASS__).'" title="'.$this->l('Yes', __CLASS__).'" /></label>
                    <input type="radio" name="'.$key.'" id="'.$key.'_on" value="1"'.($val ? ' checked="checked"' : '').' />
                    <label class="t" for="'.$key.'_on"> '.$this->l('Yes', __CLASS__).'</label>
                    <label class="t" for="'.$key.'_off"><img src="../img/admin/disabled.gif" alt="'.$this->l('No', __CLASS__).'" title="'.$this->l('No', __CLASS__).'" style="margin-left: 10px;" /></label>
                    <input type="radio" name="'.$key.'" id="'.$key.'_off" value="0" '.(!$val ? 'checked="checked"' : '').'/>
                    <label class="t" for="'.$key.'_off"> '.$this->l('No', __CLASS__).'</label>';
                break ;

                case 'textLang':
                    foreach ($languages as $language) {
                        $val = Tools::getValue($key.'_'.$language['id_lang'], Configuration::get($key, $language['id_lang']));
                        echo '
                        <div id="'.$key.'_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
                            <input size="'.$field['size'].'" type="text" name="'.$key.'_'.$language['id_lang'].'" value="'.$val.'" />
                        </div>';
                    }
                    
                    $this->displayFlags($languages, $defaultLanguage, $key, $key);
                    echo '<br style="clear:both">';
                break ;
                
                case 'text':
                default:
                    echo '<input type="text" name="'.$key.'" value="'.$val.'" size="'.$field['size'].'" />'.(isset($field['suffix']) ? $field['suffix'] : '');
            }
            
            echo (isset($field['desc']) ? '<p>'.$field['desc'].'</p>' : '');
            echo '</div>';
        }
        echo '<div class="margin-form">
                    <input type="submit" value="'.$this->l('   Save   ', __CLASS__).'" name="submitOptions'.$this->table.'" class="button" />
                </div>
            </fieldset>
            <input type="hidden" name="token" value="'.$this->token.'" />
        </form>';
    }
    
    public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL)
    {
        global $cookie;

        if (empty($limit)) {
            $limit = ((!isset($cookie->{$this->table.'_pagination'})) ? $this->_pagination[1] : $limit = $cookie->{$this->table.'_pagination'});
        }

        if (!Validate::isTableOrIdentifier($this->table)) {
            die (Tools::displayError('Table name is invalid:').' "'.$this->table.'"');
        }
        
        if (empty($orderBy)) {
            $orderBy = Tools::getValue($this->table.'Orderby', $this->_defaultOrderBy);
        }
        
        if (empty($orderWay)) {
            $orderWay = Tools::getValue($this->table.'Orderway', 'ASC');
        }
        
        $limit = intval(Tools::getValue('pagination', $limit));
        $cookie->{$this->table.'_pagination'} = $limit;

        /* Check params validity */
        if (!Validate::isOrderBy($orderBy) OR !Validate::isOrderWay($orderWay)
        OR !is_numeric($start) OR !is_numeric($limit)
        OR !Validate::isUnsignedId($id_lang)) {
            die(Tools::displayError('get list params is not valid'));
        }

        /* Determine offset from current page */
        if ((isset($_POST['submitFilter'.$this->table]) 
        OR isset($_POST['submitFilter'.$this->table.'_x']) 
        OR isset($_POST['submitFilter'.$this->table.'_y'])) 
        AND !empty($_POST['submitFilter'.$this->table]) 
        AND is_numeric($_POST['submitFilter'.$this->table])) {
            $start = intval($_POST['submitFilter'.$this->table] - 1) * $limit;
        }

        /* Cache */
        $this->_lang        = intval($id_lang);
        $this->_orderBy     = $orderBy;
        $this->_orderWay    = Tools::strtoupper($orderWay);

        /* SQL table : orders, but class name is Order */
        $sqlTable = $this->table == 'order' ? 'orders' : $this->table;
        
        /* Query in order to get results with all fields */
        $sql = 'SELECT SQL_CALC_FOUND_ROWS
        ' . ($this->_tmpTableFilter ? ' * FROM (SELECT ' : '') . '
        '.($this->lang ? 'b.*, ' : '') . 'a.*' . (isset($this->_select) ? ', '.$this->_select.' ' : '') . '
        FROM `' . _DB_PREFIX_ . $sqlTable . '` a
        ' . ($this->lang ? 'LEFT JOIN `' . _DB_PREFIX_ . $this->table . '_lang` b ON (b.`'.$this->identifier.'` = a.`'.$this->identifier.'` AND b.`id_lang` = ' . intval($id_lang) . ')' : '') . '
        ' . (isset($this->_join) ? $this->_join . ' ' : '') . '
        WHERE 1 ' . (isset($this->_where) ? $this->_where . ' ' : '') . $this->_filter . '
        ' . (isset($this->_group) ? $this->_group.' ' : '') . '
        ' . (isset($this->_having) ? $this->_having.' ' : '') . '
        ORDER BY ' . (($orderBy == $this->identifier) ? 'a.' : '') . '`' . pSQL($orderBy) . '` ' . pSQL($orderWay) .
        ($this->_tmpTableFilter ? ') tmpTable WHERE 1'.$this->_tmpTableFilter : '') . '
        LIMIT '.intval($start) . ',' . intval($limit);
        $this->_list = Db::getInstance()->ExecuteS($sql);
        $this->_listTotal = Db::getInstance()->getValue('SELECT FOUND_ROWS()');
    }

    
	public function imageCheck($id_object){
		$uploaddir = '../img/preview/';
		$apend = $id_object.'.jpg';
		$uploadfile = "$uploaddir$apend"; 
		//print_r($_FILES);
		if($_FILES['preview']['tmp_name']){
			if($_FILES['preview']['size'] <1000000) { 
				if (move_uploaded_file($_FILES['preview']['tmp_name'], $uploadfile)) {
					$size = getimagesize($uploadfile);
					if ($size[0] < 601 && $size[1]<5001) {
						return 1;
					}else {
						unlink($uploadfile);
					}
				} else {echo "Файл не загружен, верьнитель и попробуйте еще раз";}
			}else { echo "Размер файла не должен превышать 1000Кб";}
		}
	}
	
    public function postProcess()
    {
        global $currentIndex, $cookie;
        include_once($this->classPath);
        
        $currentIndex .= '&configure=' . $this->name . (Tools::getIsset('category_id') ? '&category_id=' . intval(Tools::getValue('category_id')) : '');
        if (!isset($this->table)) {
            return false;
        }

        // set token
        $token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

        /* Delete object */
        if (isset($_GET['delete'.$this->table])) {
            if (Validate::isLoadedObject($object = $this->loadObject())){
                // check if request at least one object with noZeroObject
                if (isset($object->noZeroObject) AND sizeof($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1) {
                        $this->_errors[] = Tools::displayError('you need at least one object').' <b>'.$this->table.'</b>'.Tools::displayError(', you cannot delete all of them');
                } else {
                    if ($object->delete()) {
                        Tools::redirectAdmin($currentIndex.'&conf=1&token='.$token);
                    }
                    $this->_errors[] = Tools::displayError('an error occurred during deletion');
                }
            } else {
                $this->_errors[] = Tools::displayError('an error occurred while deleting object').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
            }
        }

        /* Change object statuts (active, inactive) */
        elseif (isset($_GET['status']) AND Tools::getValue($this->identifier)) {
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                if ($object->toggleStatus()) {
                    Tools::redirectAdmin($currentIndex.'&conf=5'.((($id_category = intval(Tools::getValue('id_category'))) AND Tools::getValue('id_product')) ? '&id_category='.$id_category : '').'&token='.$token);
                } else {
                    $this->_errors[] = Tools::displayError('an error occurred while updating status');
                }
            } else {
                $this->_errors[] = Tools::displayError('an error occurred while updating status for object').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
            }
        }
        /* Delete multiple objects */
        elseif (Tools::getValue('submitDel'.$this->table)) {
            if (isset($_POST[$this->table.'Box'])) {
                $object = new $this->className();
                if (isset($object->noZeroObject) 
                AND (sizeof(call_user_func(array($this->className, $object->noZeroObject))) <= 1 
                OR sizeof($_POST[$this->table.'Box']) == sizeof(call_user_func(array($this->className, $object->noZeroObject))))) {
                    $this->_errors[] = Tools::displayError('you need at least one object').' <b>'.$this->table.'</b>'.Tools::displayError(', you cannot delete all of them');
                } else {
                    if ($object->deleteSelection($_POST[$this->table.'Box'])) {
                        Tools::redirectAdmin($currentIndex.'&conf=2&token='.$token);
                    }
                    $this->_errors[] = Tools::displayError('an error occurred while deleting selection');
                }
            } else {
                $this->_errors[] = Tools::displayError('you must select at least one element to delete');
            }
        }

        /* Create or update an object */
        elseif (Tools::getValue('submitAdd'.$this->table))
        {
            $this->validateRules();
            if (!sizeof($this->_errors)) {
                $id = intval(Tools::getValue($this->identifier));

                /* Object update */
                if (isset($id) AND !empty($id)) {
                    $object = new $this->className($id);
                    if (Validate::isLoadedObject($object)) {
                        $this->copyFromPost($object, $this->table);
						$this->imageCheck($id);
                        $result = $object->update();
                        if (!$result) {
                            $this->_errors[] = Tools::displayError('an error occurred while updating object').' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
                        } elseif (!sizeof($this->_errors)) {
                          //  Tools::redirectAdmin($currentIndex . '&conf=4&token='.$token);
                        }
                    } else {
                        $this->_errors[] = Tools::displayError('an error occurred while updating object').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                    }
                }

                /* Object creation */
                else {
                    $object = new $this->className();
                    $this->copyFromPost($object, $this->table);
                    if (!$object->add()) {
                        $this->_errors[] = Tools::displayError('an error occurred while creating object').' <b>'.$this->table.' ('.mysql_error().')</b>';
                    } elseif (($_POST[$this->identifier] = $object->id) AND !sizeof($this->_errors)) {
						//$this->twitterPost($object->meta_title[6]." ".$object->getLink(array('entry' => array('id' => $object->id, 'rewrite' => $object->link_rewrite[6]))));
						$this->imageCheck($object->id);
                        Tools::redirectAdmin($currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=3&token='.$token);
                    }
                }
            }
            $this->_errors = array_unique($this->_errors);
        }
		
		/* Create or update an object */
        elseif (Tools::getValue('submitAdd'.$this->table.'AndStay'))
        {
            $this->validateRules();
            if (!sizeof($this->_errors)) {
                $id = intval(Tools::getValue($this->identifier));

                /* Object update */
                if (isset($id) AND !empty($id)) {
                    $object = new $this->className($id);
                    if (Validate::isLoadedObject($object)) {
                        $this->copyFromPost($object, $this->table);
						$this->imageCheck($id);
                        $result = $object->update();
                        if (!$result) {
                            $this->_errors[] = Tools::displayError('an error occurred while updating object').' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
                        } elseif (!sizeof($this->_errors)) {
                          Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$object->id.'&updatenews&token='.$token);
                        }
                    } else {
                        $this->_errors[] = Tools::displayError('an error occurred while updating object').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                    }
                }

                /* Object creation */
                else {
                    $object = new $this->className();
                    $this->copyFromPost($object, $this->table);
                    if (!$object->add()) {
                        $this->_errors[] = Tools::displayError('an error occurred while creating object').' <b>'.$this->table.' ('.mysql_error().')</b>';
                    } elseif (($_POST[$this->identifier] = $object->id) AND !sizeof($this->_errors)) {
						//$this->twitterPost($object->meta_title[6]." ".$object->getLink(array('entry' => array('id' => $object->id, 'rewrite' => $object->link_rewrite[6]))));
						$this->imageCheck($object->id);
                        Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$object->id.'&updatenews&token='.$token);
                    }
                }
            }
            $this->_errors = array_unique($this->_errors);
        }

        /* Cancel all filters for this tab */
        elseif (isset($_POST['submitReset'.$this->table])) {
            $filters = $cookie->getFamily($this->table.'Filter_');
            foreach ($filters AS $cookieKey => $filter) {
                if (strncmp($cookieKey, $this->table.'Filter_', 7 + Tools::strlen($this->table)) == 0) {
                    $key = substr($cookieKey, 7 + Tools::strlen($this->table));
                    $tmpTab = explode('!', $key);
                    $key = (count($tmpTab) > 1 ? $tmpTab[1] : $tmpTab[0]);
                    if (array_key_exists($key, $this->fieldsDisplay)) {
                        unset($cookie->$cookieKey);
                    }
                }
            }
            
            if (isset($cookie->{'submitFilter'.$this->table})) {
                unset($cookie->{'submitFilter'.$this->table});
            }
            
            if (isset($cookie->{$this->table.'Orderby'})) {
                unset($cookie->{$this->table.'Orderby'});
            }
            
            if (isset($cookie->{$this->table.'Orderway'})) {
                unset($cookie->{$this->table.'Orderway'});
            }
            
            unset($_POST);
        }

        /* Submit options list */
        elseif (Tools::getValue('submitOptions'.$this->table)) {
            foreach ($this->_fieldsOptions as $key => $field) {
                if ($field['type'] == 'textLang') {
                    $languages = Language::getLanguages();
                    $list = array();
                    foreach ($languages as $language) {
                        $list[$language['id_lang']] = (isset($field['cast']) ? $field['cast'](Tools::getValue($key.'_'.$language['id_lang'])) : Tools::getValue($key.'_'.$language['id_lang']));
                    }
                    Configuration::updateValue($key, $list);
                } else {
                    Configuration::updateValue($key, (isset($field['cast']) ? $field['cast'](Tools::getValue($key)) : Tools::getValue($key)));
                }
            }
            Tools::redirectAdmin($currentIndex.'&conf=6&token='.$token);
        }

        /* Manage list filtering */
        elseif (Tools::isSubmit('submitFilter'.$this->table) OR $cookie->{'submitFilter'.$this->table} !== false) {
            $_POST = array_merge($cookie->getFamily($this->table.'Filter_'), (isset($_POST) ? $_POST : array()));
            foreach ($_POST AS $key => $value) {
            
                /* Extracting filters from $_POST on key filter_ */
                if ($value != NULL AND !strncmp($key, $this->table.'Filter_', 7 + Tools::strlen($this->table))) {
                    $key    = Tools::substr($key, 7 + Tools::strlen($this->table));
                    $tmpTab = explode('!', $key);
                    $filter = count($tmpTab) > 1 ? $tmpTab[1] : $tmpTab[0];
                    
                    if ($field = $this->filterToField($key, $filter)) {
                        $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
                        
                        if (($type == 'date' OR $type == 'datetime') AND is_string($value)) {
                            $value = unserialize($value);
                        }
                        
                        $key = isset($tmpTab[1]) ? $tmpTab[0].'.`'.$tmpTab[1].'`' : '`'.$tmpTab[0].'`';
                        
                        if (array_key_exists('tmpTableFilter', $field)) {
                            $sqlFilter = & $this->_tmpTableFilter;
                        } else {
                            $sqlFilter = & $this->_filter;
                        }

                        /* Only for date filtering (from, to) */
                        if (is_array($value)) {
                            if (isset($value[0]) AND !empty($value[0])) {
                                if (!Validate::isDate($value[0])) {
                                    $this->_errors[] = Tools::displayError('\'from:\' date format is invalid (YYYY-MM-DD)');
                                } else {
                                    $sqlFilter .= ' AND '.pSQL($key).' >= \''.pSQL(Tools::dateFrom($value[0])).'\'';
                                }
                            }

                            if (isset($value[1]) AND !empty($value[1])) {
                                if (!Validate::isDate($value[1])) {
                                    $this->_errors[] = Tools::displayError('\'to:\' date format is invalid (YYYY-MM-DD)');
                                } else {
                                    $sqlFilter .= ' AND '.pSQL($key).' <= \''.pSQL(Tools::dateTo($value[1])).'\'';
                                }
                            }
                        } else {
                            $sqlFilter .= ' AND ';
                            if ($type == 'int' OR $type == 'bool') {
                                $sqlFilter .= (($key == $this->identifier OR $key == '`'.$this->identifier.'`') ? 'a.' : '').pSQL($key).' = '.intval($value).' ';
                            } elseif ($type == 'decimal') {
                                $sqlFilter .= (($key == $this->identifier OR $key == '`'.$this->identifier.'`') ? 'a.' : '').pSQL($key).' = '.floatval($value).' ';
                            } else {
                                $sqlFilter .= (($key == $this->identifier OR $key == '`'.$this->identifier.'`') ? 'a.' : '').pSQL($key).' LIKE \'%'.pSQL($value).'%\' ';
                            }
                        }
                    }
                }
            }
        }
    }
	
    public function twitterPost($message){
		include_once(_PS_MODULE_DIR_ . 'newscore/twitteroauth.php');
		define("CONSUMER_KEY", "BHWbW7vOfVg40PN3MeKDag");
		define("CONSUMER_SECRET", "tFW1d9OdzcfcJNrN8OsKbXmPwVZKTRuHhJlDz9Gv0A");
		define("OAUTH_TOKEN", "374475935-3v3Tj4CCeXzomGsJ5JQqB4bFiEsK5V74zBYjwCDK");
		define("OAUTH_SECRET", "QrLPpPepBbwxEWyRF69oT7pre5qMFSfHu8Tb9FQDVKc");

		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
		$content = $connection->get('account/verify_credentials');

		$connection->post('statuses/update', array('status' => $message));
	}
    
    public function    displayWarning($warn)
    {
        echo '<div class="warning warn"><h3>'.$warn.'</h3></div>';
    }
    
    
    protected function loadObject($opt = false)
    {
        include_once($this->classPath);
        if ($id = intval(Tools::getValue($this->identifier)) AND Validate::isUnsignedId($id)) {
            
            if (!$this->_object) {

                $this->_object = new $this->className($id);
            }
            
            if (!Validate::isLoadedObject($this->_object)) {
                die (Tools::displayError('object cannot be loaded'));
            }
            
            return $this->_object;
            
        } elseif ($opt) {
            $this->_object = new $this->className();
            return $this->_object;
        } else {
            die(Tools::displayError('object cannot be loaded'));
        }
    }
    
    
    private function filterToField($key, $filter)
    {
        foreach ($this->fieldsDisplay AS $field) {
            if (array_key_exists('filter_key', $field) AND $field['filter_key'] == $key) {
                return $field;
            }
        }
        
        if (array_key_exists($filter, $this->fieldsDisplay)) {
            return $this->fieldsDisplay[$filter];
        }
        
        return false;
    }
    
    public function displayFlags($languages, $defaultLanguage, $ids, $id, $return = false)
    {
            if (sizeof($languages) == 1) {
                return false;
            }
            
            $divClass = 'displayed_flag';
            $onClick = 'toggleLanguageFlags(this);';
            if(floatval(_PS_VERSION_) < 1.3) {
                $divClass = 'display_flags';
                $onClick = 'showLanguages(\''.$id.'\');';
            }
            
            $defaultIso = Language::getIsoById($defaultLanguage);
            $output = '
            <div class="' . $divClass . '">
                <img src="../img/l/'.$defaultLanguage.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="' . $onClick . '" alt="" />
            </div>
            <div id="languages_'.$id.'" class="language_flags">
                '.$this->l('Choose language:', __CLASS__).'<br /><br />';
            foreach ($languages as $language) {
                $output .= '<img src="../img/l/'.intval($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
            }
            $output .= '</div>';
            
            if ($return) {
                return $output;
            }
            echo $output;
    }
    
    public function validateRules()
    {
        include_once($this->classPath);

        /* Class specific validation rules */
        $rules = call_user_func(array($this->className, 'getValidationRules'), $this->className);

        if ((sizeof($rules['requiredLang']) OR sizeof($rules['sizeLang']) OR sizeof($rules['validateLang'])))
        {
            /* Language() instance determined by default language */
            $defaultLanguage = new Language(intval(Configuration::get('PS_LANG_DEFAULT')));

            /* All availables languages */
            $languages = Language::getLanguages();
        }

        /* Checking for required fields */
        foreach ($rules['required'] AS $field) {
            if (($value = Tools::getValue($field)) == false AND (string)$value != '0') {
                if (!Tools::getValue($this->identifier) OR ($field != 'passwd' AND $field != 'no-picture')) {
                    $this->_errors[] = $this->l('the field', __CLASS__).' <b>'.call_user_func(array($this->className, 'displayFieldName'), $field, $this->className).'</b> '.$this->l('is required', __CLASS__);
                }
            }
        }

        /* Checking for multilingual required fields */
        foreach ($rules['requiredLang'] AS $fieldLang) {
            if (($empty = Tools::getValue($fieldLang.'_'.$defaultLanguage->id)) === false OR empty($empty)) {
                $this->_errors[] = $this->l('the field', __CLASS__).' <b>'.call_user_func(array($this->className, 'displayFieldName'), $fieldLang, $this->className).'</b> '.$this->l('is required at least in', __CLASS__).' '.$defaultLanguage->name;
            }
        }

        /* Checking for maximum fields sizes */
        foreach ($rules['size'] AS $field => $maxLength) {
            if (Tools::getValue($field) !== false AND Tools::strlen(Tools::getValue($field)) > $maxLength) {
                $this->_errors[] = $this->l('the field', __CLASS__).' <b>'.call_user_func(array($this->className, 'displayFieldName'), $field, $this->className).'</b> '.$this->l('is too long', __CLASS__).' ('.$maxLength.' '.$this->l('chars max', __CLASS__).')';
            }
        }
            
        /* Checking for maximum multilingual fields size */
        foreach ($rules['sizeLang'] AS $fieldLang => $maxLength)
            foreach ($languages AS $language)
                if (Tools::getValue($fieldLang.'_'.$language['id_lang']) !== false AND Tools::strlen(Tools::getValue($fieldLang.'_'.$language['id_lang'])) > $maxLength)
                    $this->_errors[] = $this->l('the field', __CLASS__).' <b>'.call_user_func(array($this->className, 'displayFieldName'), $fieldLang, $this->className).' ('.$language['name'].')</b> '.$this->l('is too long', __CLASS__).' ('.$maxLength.' '.$this->l('chars max', __CLASS__).')';

        /* Overload this method for custom checking */
        $this->_childValidation();

        /* Checking for fields validity */
        foreach ($rules['validate'] AS $field => $function)
            if (($value = Tools::getValue($field)) !== false AND ($field != 'passwd'))
                if (!Validate::$function($value))
                    $this->_errors[] = $this->l('the field', __CLASS__).' <b>'.call_user_func(array($this->className, 'displayFieldName'), $field, $this->className).'</b> '.$this->l('is invalid', __CLASS__);

        /* Checking for multilingual fields validity */
        foreach ($rules['validateLang'] AS $fieldLang => $function)
            foreach ($languages AS $language)
                if (($value = Tools::getValue($fieldLang.'_'.$language['id_lang'])) !== false AND !empty($value))
                    if (!Validate::$function($value))
                        $this->_errors[] = $this->l('the field', __CLASS__).' <b>'.call_user_func(array($this->className, 'displayFieldName'), $fieldLang, $this->className).' ('.$language['name'].')</b> '.$this->l('is invalid', __CLASS__);
    }
    
    protected function copyFromPost(&$object, $table)
    {
        /* Classical fields */
        foreach ($_POST AS $key => $value) {
            if (key_exists($key, $object) AND $key != 'id_'.$table) {
                $object->{$key} = $value;
            }
        }

        /* Multilingual fields */
        $rules = call_user_func(array(get_class($object), 'getValidationRules'), get_class($object));
        if (sizeof($rules['validateLang']))
        {
            $languages = Language::getLanguages();
            foreach ($languages AS $language) {
                foreach ($rules['validateLang'] AS $field => $validation) {
                    if (isset($_POST[$field.'_'.intval($language['id_lang'])])) {
                        $object->{$field}[intval($language['id_lang'])] = $_POST[$field.'_'.intval($language['id_lang'])];
                    }
                }
            }
        }
    }
    
    public function displayErrors()
    {
        if ($nbErrors = sizeof($this->_errors)) {
            echo '<div class="alert error"><h3>'.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors', __CLASS__) : $this->l('error', __CLASS__)).'</h3>
            <ol>';
            foreach ($this->_errors AS $error) {
                echo '<li>'.$error.'</li>';
            }
            echo '
            </ol></div>';
        }
    }
    
    protected function _childValidation() { }
    
    protected function getFieldValue($obj, $key, $id_lang = NULL)
    {
        if ($id_lang) {
            $defaultValue = ($obj->id AND isset($obj->{$key}[$id_lang])) ? $obj->{$key}[$id_lang] : '';
        } else {
            $defaultValue = isset($obj->{$key}) ? $obj->{$key} : '';
        }
        return Tools::getValue($key.($id_lang ? '_'.$id_lang : ''), $defaultValue);
    }
}
?>
