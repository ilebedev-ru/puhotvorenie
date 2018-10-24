<?php
define('_PS_ADMIN_DIR_', getcwd());
include( _PS_ADMIN_DIR_ .'/../../config/config.inc.php');
include( _PS_ADMIN_DIR_ .'/classes/LeoManageWidget.php');
include( _PS_ADMIN_DIR_ .'/leomanagewidgets.php');
global $adminfolder;
$module = new leomanagewidgets();
$adminfolder = $module->getFolderAdmin();
?>
<html>
	<head>
		<title>Pop up</title>
		<link href="<?php echo __PS_BASE_URI__;?>css/admin.css"  type="text/css" rel="stylesheet"/>
		<link href="<?php echo __PS_BASE_URI__.$adminfolder;?>/themes/default/css/admin.css"  type="text/css" rel="stylesheet"/>
		<link href="<?php echo _MODULE_DIR_;?>leomanagewidgets/assets/admin/style.css"  type="text/css" rel="stylesheet"/>
		<style type="text/css">
			body{height:100%;background-color: #FFFFFF;}
			#container{height:100%}
			#content{height:90%;border: none;padding: 0px;}
		</style>
		<script src="<?php echo _MODULE_DIR_;?>leomanagewidgets/assets/admin/jquery-1.7.2.min.js" type="text/javascript"></script>	
		<script src="<?php echo _MODULE_DIR_;?>leomanagewidgets/assets/admin/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>	
		<script src="<?php echo _MODULE_DIR_;?>leomanagewidgets/assets/admin/admin.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo __PS_BASE_URI__;?>js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="<?php echo __PS_BASE_URI__;?>js/tinymce.inc.js"></script>
		<script type="text/javascript">
			var helpboxes = false;
			var id_language = <?php echo Tools::getValue('id_lang');?>;
			function changeLofLanguage(field, fieldsString, id_language_new, iso_code)
			{
				$('div[id^='+field+'_]').hide();
				var fields = fieldsString.split('¤');
				for (var i = 0; i < fields.length; ++i)
				{
					$('div[id^='+fields[i]+'_]').hide();
					$('#'+fields[i]+'_'+id_language_new).show();
					$('#'+'language_current_'+fields[i]).attr('src', '<?php echo __PS_BASE_URI__;?>img/l/' + id_language_new + '.jpg');
				}
				$('#languages_' + field).hide();
				id_language = id_language_new;
			}
		</script>
		<script src="<?php echo __PS_BASE_URI__;?>js/admin.js" type="text/javascript"></script>	
	</head>
	<body>
	<div id="container">
		<div id="content">
		<?php 
			$id_shop = Tools::getValue('id_shop');
			$id_lang = Tools::getValue('id_lang');
			$id = Tools::getValue('id_leomanagewidgets');
			
			if(Tools::isSubmit('submitSave')){
				$error = $module->_postValidation();
				if(!isset($error['msg'])){
					$return = $module->_postProcess();
					$obj = $return['obj'];
				}else{
					$obj = new LeoManageWidget($id, 0, $id_shop);
				}
			}else{
				$obj = new LeoManageWidget($id, 0, $id_shop);
			}
			$task = Tools::getValue('task', $obj->task);
			$html = '';
			
			if(file_exists(dirname(__FILE__).'/form_'.$task.'.php')){
				require_once ( dirname(__FILE__).'/form_'.$task.'.php' );
			}else{
				?>
				<script type="text/javascript">
					window.parent.location.href = '<?php echo __PS_BASE_URI__.$adminfolder.'/index.php?tab=AdminModules&configure=' . $module->name . '&token=' . Tools::getValue('token');?>';
				</script>
				<?php 
			}
		?>
		<div class="container_html">
		<br/>
		<br/>
		<br/>
		<?php echo (isset($error['msg']) ? $error['msg'] : ''); ?>
		<?php if(isset($return) && $return){ ?>
		
			<?php echo $return['msg']; ?>
			<?php if($return['status'] == 'success'){ ?>
			<script type="text/javascript">
				window.parent.location.href = '<?php echo __PS_BASE_URI__.$adminfolder.'/index.php?tab=AdminModules&configure=' . $module->name . '&token=' . Tools::getValue('token');?>';
			</script>
			<?php } ?>
		
		<?php } ?>
		
		<?php echo $html; ?>
		</div>
		</div>
	</body>
</html>