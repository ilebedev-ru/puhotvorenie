
<?php
include_once(_PS_MODULE_DIR_ . 'newscore/classes/NewsSystem.php');

class NewsHandler extends NewsSystem
{
    static public $categoriesInstalled  = false;
    static public $commentsInstalled    = false;
    private $_newsCategoriesObj         = null;
    private $_commentsObj               = null;
    
    public function __construct()
    {
        global $currentIndex, $cookie;
        $this->_defaultOrderBy  = 'date_add';
        $this->_orderWay        = 'ASC';
        $this->token            = Tools::getValue('token');
        
        $this->fieldsDisplay = array(
			'id_entry'      => array(
			    'title'     => $this->l('ID'), 
			    'align'     => 'center', 
			    'width'     => 25
			),
			'date_add'      => array(
			    'title'     => $this->l('Добавлено'), 
			    'width'     => 60, 
			    'type'      => 'date'
			),
			'name'    => array(
			    'title'     => $this->l('Заголовок'), 
			    'width'     => 300
			)
		);
		
		$this->optionTitle = $this->l('Настройки новостей');
		
		if(Module::isInstalled('newscategoriesmod')) {
            include_once(_PS_MODULE_DIR_ . 'newscategoriesmod/newscategoriesmod.php');
            self::$categoriesInstalled = true;
            $this->_newsCategoriesObj = new NewsCategoriesMod();
       
		$recent_categories = $this->_newsCategoriesObj->getNameIdList();
		 }
		if(Module::isInstalled('newsrecentblock')) {
		    $this->_fieldsOptions['NEWS_RECENT_AMOUNT'] = array(
		        'title'     => $this->l('Отображать последних новостей:'), 
		        'desc'      => $this->l('Сколько отображать новостей в блоке "последние нововсти"'), 
		        'cast'      => 'intval', 
		        'size'      => 5, 
		        'type'      => 'text', 
		        'suffix'    => ' entries'
		    );
			/*$this->_fieldsOptions['NEWS_RECENT_CATEGORY'] = array(
		        'title'     => $this->l('Категория по умолчанию для последних новостей:'), 
		        'desc'      => '', 
		        'cast'      => 'intval', 
		        'size'      => 5, 
		        'type'      => 'select', 
				'filter_key' => 'id_recent_category',
		        'list'    => $recent_categories 
		    );*/
		}
		
		if(Module::isInstalled('newscategoriesblock')) {
		    $this->_fieldsOptions['NEWS_CATEGORIES_AMOUNT'] = array(
		        'title'     => $this->l('Категорий отображать:'), 
		        'desc'      => $this->l('Сколько отображать категорий в блоке "категории новостей"'), 
		        'cast'      => 'intval', 
		        'size'      => 5, 
		        'type'      => 'text', 
		        'suffix'    => ' categories'
		    );
		}


        
        if(Module::isInstalled('newscomments')) {
            include_once(_PS_MODULE_DIR_ . 'newscomments/newscomments.php');
            self::$commentsInstalled = true;
            $this->_commentsObj = new NewsComments();
        }
		
		if(self::$categoriesInstalled) {
		    $this->_selectFilter[] = 'ntc.`id_category`, ncl.`meta_title` as `category_name`';
		    $this->_join = '
		    LEFT JOIN `' . _DB_PREFIX_ . 'newstocategories` ntc ON (a.`id_entry` = ntc.`id_entry`) 
		    LEFT JOIN `' . _DB_PREFIX_ . 'newscategories_lang` ncl ON (ntc.`id_category` = ncl.`id_category` AND ncl.`id_lang` = ' . $cookie->id_lang . ')';

		    if($category_id = Tools::getValue('category_id', 1)) {
		        $this->_where = 'AND ntc.`id_category` = ' . (intval($category_id) > 0 ? intval($category_id) : 1);
		    }
		}
		
		if(self::$commentsInstalled) {
		    $settings = $this->_commentsObj->displaySettings();
		    foreach($settings as $key => $value) {
		        $this->_fieldsOptions[$key] = $value;  
		    }
		    $this->_selectFilter[] = '(SELECT CONCAT(COUNT(nc.`id_entry_comment`), "\\\" , COUNT( NULLIF(nc.`validate`, 1) )) FROM `'._DB_PREFIX_.'newscomments` nc WHERE nc.`id_entry` = a.`id_entry`) AS `comments`';
		    $this->fieldsDisplay['comments'] = array('title' => $this->l('Comments<br />(Total/Pending)'), 'width' => 40, 'search' => false);
		}
		
		if(sizeof($this->_selectFilter)) {
		    $this->_select .= implode(',', $this->_selectFilter);
		}
		
		$this->fieldsDisplay['status'] = array('title' => $this->l('Вкл.'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false);

    }
    
    public function displayListHeader($token = null)
    {
        if(self::$categoriesInstalled) {
            echo '
            <script language="JavaScript">
                function selectCategory(category_id) {
                    location.href="index.php?tab=AdminModules&configure=newscore&token=' . Tools::getValue('token') . '&category_id="+ category_id;
                }
            </script>';
            echo '
            <select name="category_id" onchange="selectCategory(this.options[this.selectedIndex].value);">
            ' . $this->_newsCategoriesObj->getCategoriesDropdown(Tools::getValue('category_id', 1)) . '
            </select><br />';
        }
        return parent::displayListHeader($token);
    }
    
	public function getAdminCommentsTree($comment, $controlLink) 
	{
	    global $cookie;
	    echo '
	        <li class="' . $comment['type'] . '">
                <div class="commentWrapper ' . $comment['type'] . ($comment['validate'] == 0 ? ' pending' : '') . '">
                    <div class="commentLeftSide">
                        <h4>' . ($comment['username'] ? $comment['username'] : $this->l('Anonymous')) . ' (' . Tools::displayDate($comment['date_add'], $cookie->id_lang, false) . ')</h4>
                    </div>
                    <div class="commentContent">
                        ' . nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')) . '
                    </div>
                    <div class="controlPanel">
                        <a href="' . $controlLink . '&toggleCommentStatus&comment_id=' . $comment['id_entry_comment'] . '"><img src="../img/admin/' . ($comment['validate'] == 0 ? 'enabled' : 'forbbiden') . '.gif">' . $this->l(($comment['validate'] == 0 ? 'Approve' : 'Disapprove')) . '</a>
                        <a href="' . $controlLink . '&deleteComment&comment_id=' . $comment['id_entry_comment'] . '"><img src="../img/admin/delete.gif">' . $this->l('Delete') . '</a>
                    </div>';
        if(isset($comment['children']) && sizeof($comment['children']) > 0) {
            echo '<ul>';
            foreach ($comment['children'] as $subcomment) {
                echo $this->getAdminCommentsTree($subcomment, $controlLink);
            }
            echo '</ul>';
        }
        echo '
                </div>
            </li>';
	}
    
    public function viewNews()
    {
        global $currentIndex, $cookie;
        $news = $this->loadObject();
        if($entry = $news->getEntryPreview($cookie->id_lang, $news->id, $news->id_category_default)) {
            $controlLink = $currentIndex.'&id_entry=' . $entry['id_entry'] . '&updatenews&token=' . $this->token;
            $currentLink = $currentIndex.'&id_entry=' . $entry['id_entry'] . '&viewnews&token=' . $this->token;
            echo '<div>
		<fieldset style="width: auto;"><div style="float: right"><a href="' . $controlLink . '"><img src="../img/admin/edit.gif" /></a></div>
			<span style="font-weight: bold; font-size: 14px;">'.$entry['meta_title'].'</span>
			<br /><br />
			' . $this->l('ID:') . ' ' . sprintf('%06d', $entry['id_entry']) . '<br />
			' . ($entry['category_name'] ? ($this->l('Категория') . ': ' . $entry['category_name']) : '') . '<br />
			' . $this->l('Добавлено:') . ' '.Tools::displayDate($entry['date_add'], intval($cookie->id_lang), true) . '<br />
			' . $this->l('Обновлено:') . ' ' . ($entry['date_upd'] != $entry['date_add'] ? Tools::displayDate($entry['date_upd'], intval($cookie->id_lang), true) : $this->l('never')) . '
			<br /><br />
			' . $entry['description_short'] . '
			' . $entry['content'];
			if(self::$commentsInstalled) {
			    echo '<span style="font-weight: bold; font-size: 14px;">' . $this->l('Комментарии') . ':</span>';
			    echo '<link type="text/css" rel="stylesheet" href="../modules/newscore/admin.css" />';
			    include_once(_PS_MODULE_DIR_ . 'newscomments/NewsComment.php');
			    $comments = NewsComment::getByEntry($news->id);
			    echo '<div id="commentsWrapper">';
			    if($comments) {
			        echo '<div id="commentsWrapper">';
			        echo '<ul>';
			        foreach($comments as $comment) {
			            $this->getAdminCommentsTree($comment, $currentLink);
			        }
			        echo '</ul>';
			        echo '</div>';
			    } else {
			        echo '<p>' . $this->l('No comments yet') . '</p>';
			    }
			    echo '</div>';			    
			}
			echo '
		</fieldset>
		</div>
		<div class="clear">&nbsp;</div>';
		
		    if(Tools::getIsset('toggleCommentStatus') && $comment_id = Tools::getValue('comment_id')) {
                if($this->_commentsObj->changeStatus($comment_id)) {
                    Tools::redirectAdmin($currentLink . '&conf=5');
                }
            } elseif(Tools::getIsset('deleteComment') && $comment_id = Tools::getValue('comment_id')) {
                if($this->_commentsObj->deleteComment($comment_id)) {
                    Tools::redirectAdmin($currentLink . '&conf=1');
                }
            }
        }
    }
    
    public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL)
	{
		global $cookie;
		return parent::getList(intval($cookie->id_lang), !Tools::getValue($this->table.'Orderby') ? 'date_add' : NULL, !Tools::getValue($this->table.'Orderway') ? 'DESC' : NULL);
	}
	
	public function displayList()
	{
	    global $currentIndex;
	    echo '<a href="' . $currentIndex . '&add'.$this->table.'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Добавить новую запись').'</a>';
	    if(self::$categoriesInstalled) {
	        echo $this->_newsCategoriesObj->getEntryListCategoriesControls();
	    }
	    echo '<br /><br />';
	    return parent::displayList();
	}
	
	public function displayForm()
	{
		global $currentIndex, $cookie;
		
		$obj                = $this->loadObject(true);
		$defaultLanguage    = intval(Configuration::get('PS_LANG_DEFAULT'));
		$iso                = Language::getIsoById(intval($cookie->id_lang));
		$languages          = Language::getLanguages();
		$divLangName        = 'meta_title¤meta_description¤meta_keywords¤link_rewrite¤cdescription_short¤ccontent';

		echo '
		<script type="text/javascript">
			id_language = Number(' . $defaultLanguage . ');
		</script>
		<link rel="stylesheet" href="/modules/newscore/redactor/redactor.css" />
		<script src="/modules/newscore/redactor/redactor.min.js"></script>
		<script src="/modules/newscore/news-handler.js"></script>
		<script type="text/javascript">
		$(document).ready(
			function()
			{
				$(".redactor").redactor({
					imageUpload: "/modules/newscore/image_upload.php"
				});
			}
		);
		</script>		
		<form action="'.$currentIndex.'&token=' . $this->token . '" method="post" enctype="multipart/form-data">
			' . ($obj->id ? '<input type="hidden" name="'.$this->identifier.'" value="'.$obj->id.'" />' : '') . '
			<fieldset><legend><img src="../modules/newscore/logo.gif" />' . $this->l('Запись') . '</legend>';
		echo '	<label>'.$this->l('Заголовок').' </label>
				<div class="margin-form">';
		foreach ($languages as $language)
			echo '	<div id="xname_' . $language['id_lang'] . '" style="display: ' . ($language['id_lang'] == $defaultLanguage ? 'block' : 'none') . '; float: left;">
						<input id="name_'.$language['id_lang'] .'" size="80" type="text" name="name_' . $language['id_lang'] . '" '.(!$obj->id ? 'onkeyup="copy2friendlyURL();"' : '').' value="' . htmlentities($this->getFieldValue($obj, 'name', intval($language['id_lang'])), ENT_COMPAT, 'UTF-8') . '" /><sup> *</sup>
					</div>';	
		echo '	<div class="clear"></div> </div>
		<label>'.$this->l('Мета-заголовок для SEO').' </label>
				<div class="margin-form">';
		foreach ($languages as $language)
			echo '	<div id="meta_title_' . $language['id_lang'] . '" style="display: ' . ($language['id_lang'] == $defaultLanguage ? 'block' : 'none') . '; float: left;">
						<input id="metaname_'.$language['id_lang'] .'" size="120" type="text" name="meta_title_' . $language['id_lang'] . '" value="' . htmlentities($this->getFieldValue($obj, 'meta_title', intval($language['id_lang'])), ENT_COMPAT, 'UTF-8') . '" /><sup> *</sup>
					</div>';
		$this->displayFlags($languages, $defaultLanguage, $divLangName, 'meta_title');
		echo '	    <div class="clear"></div>
		            <p>'.$this->l('Специальный meta-title').'</p>
		        </div>';
		
		echo '  <label>'.$this->l('Статус:').' </label>
				<div class="margin-form">
					<input type="radio" name="status" id="status_on" value="1" '.($this->getFieldValue($obj, 'status') ? 'checked="checked" ' : '').'/>
					<label class="t" for="status_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Активно').'" /></label>
					<input type="radio" name="status" id="status_off" value="0" '.(!$this->getFieldValue($obj, 'status') ? 'checked="checked" ' : '').'/>
					<label class="t" for="status_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Отключено').'" /></label>
					<p>'.$this->l('Выберите статус - работает или нет').'</p>
				</div>';
	echo '  <label>Картинка </label>
				<div class="margin-form">
					'.(is_file('../img/preview/'.$obj->id.'.jpg') ? '<img src="/img/preview/'.$obj->id.'.jpg?rnd='.rand(1,100000).'" width="100px"><br />' :'').'
					<input type="file" name="preview">
				</div>';
		
		echo '	<label>'.$this->l('Мета описание').' </label>
				<div class="margin-form">';
		foreach ($languages as $language)
			echo '	<div id="meta_description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea cols="70" rows="5" name="meta_description_'.$language['id_lang'].'">'.htmlentities($this->getFieldValue($obj, 'meta_description', intval($language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>';
		$this->displayFlags($languages, $defaultLanguage, $divLangName, 'meta_description');
		echo '	    <div class="clear"></div>
		            <p>' . $this->l('Опишите свою запись в нескольких предложениях') . '</p>
		        </div>';
		echo '<label>'.$this->l('Товары через запятую').' </label>
				<script>
					var generate_qty = 4;
				</script>
				<div class="margin-form">
				<input id="productlist" style="width:400px" type="text" name="product_list" value="'.$this->getFieldValue($obj, 'product_list') . '" />
				<input type="submit" name="generate" class="generate button" value="Сгенерировать случайные 4 товара">
				</div>';
		echo '	<label>'.$this->l('Мета ключи').' </label>
				<div class="margin-form">';
		foreach ($languages as $language)
			echo '	<div id="meta_keywords_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input size="50" type="text" name="meta_keywords_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'meta_keywords', intval($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
					</div>';
		$this->displayFlags($languages, $defaultLanguage, $divLangName, 'meta_keywords');
		echo '	    <div class="clear"></div>
		            <p>' . $this->l('Пару слов, чтобы охарактеризовать запись') . '</p>
		        </div>';
		
		echo '	<label>'.$this->l('Дружественный URL').' </label>
				<div class="margin-form">';
		foreach ($languages as $language)
			echo '	<div id="inputlink_rewrite_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input id="link_rewrite_'.$language['id_lang'].'" size="60" type="text" name="link_rewrite_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'link_rewrite', intval($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" onkeyup="this.value = str2url(this.value); updateFriendlyURL();" /><sup> *</sup>
					</div>';
		$this->displayFlags($languages, $defaultLanguage, $divLangName, 'link_rewrite');
		echo '	    <div class="clear"></div>
		            <p>' . $this->l('ЧПУ') . '</p>
		        </div>';
		
		if(self::$categoriesInstalled) {
		    echo $this->_newsCategoriesObj->getDefaultCategoriesDropdown($obj->id_category_default);
		    echo $this->_newsCategoriesObj->getCategoriesTable($obj->id ? $obj->id : false);
		}
		global $cookie;
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		
		echo '	<label>'.$this->l('Короткое описание').' </label>
				<div class="margin-form">';
		foreach ($languages as $language)
			echo '	<div id="cdescription_short_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<textarea class="redactor" cols="80" rows="30" id="description_short_'.$language['id_lang'].'" name="description_short_'.$language['id_lang'].'">'.htmlentities(stripslashes($this->getFieldValue($obj, 'description_short', $language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>';
		$this->displayFlags($languages, $defaultLanguage, $divLangName, 'cdescription_short');
		echo '	</div><div class="clear space">&nbsp;</div>';
		
		echo '	<label>'.$this->l('Содержимое').' </label>
				<div class="margin-form">';
		foreach ($languages as $language)
			echo '	<div id="ccontent_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<textarea class="redactor" cols="80" rows="30" id="content_'.$language['id_lang'].'" name="content_'.$language['id_lang'].'">'.htmlentities(stripslashes($this->getFieldValue($obj, 'content', $language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>';
		$this->displayFlags($languages, $defaultLanguage, $divLangName, 'ccontent');
		echo '	</div><div class="clear space">&nbsp;</div>';
		
		echo '	<div class="margin-form space">
					<input type="submit" value="'.$this->l('   Сохранить   ').'" name="submitAdd'.$this->table.'" class="button" />
					<input type="submit" value="'.$this->l('Сохранить и остаться').'" name="submitAdd'.$this->table.'AndStay" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Обязательное поле').'</div>
			</fieldset>
		</form>';
	}
	
	public function postProcess()
	{
		global $cookie;

		
		return parent::postProcess();
	}

}
?>
