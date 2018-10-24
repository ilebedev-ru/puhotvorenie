<h1>
<?php echo $this->l('LeoTheme - Panel Control') ; ?>
</h1>
<div><?php echo $this->l('Current FrontEnd Theme' ) ; ?>: <b><?php echo _THEME_NAME_;?></b></div>
<style>
	.clearfix:before,.clearfix:after{content:".";display:block;height:0;overflow:hidden}.clearfix:after{clear:both}.clearfix{zoom:1}
	.leotheme-layout{
		background:#f2f2f2;
		width:800px;
		margin:10 auto;padding:12px
	}
	.leotheme-layout > div, .leotheme-layout > div div{
		position:relative;
	}
	.leo-container{ background:red; margin:8px 0px; position:relative; }
	#leo-header .topbar, #leo-menu, #leo-slideshow, #leo-promotetop,#leo-footer,#leo-bottom {
		min-height:40px;
		padding-top:26px;
		padding-bottom:9px;
	}


	#leo-content {
		clear:both;
		display:block
	}
	#leo-content #leo-left,#leo-content #leo-right{ width:25%;float:left;  height:200px}
	#leo-content .leo-container,#leo-hheaderright {margin:0; padding-top:26px; padding-bottom:9px }
		#leo-center{
		 background: none repeat scroll 0 0 red;
			
			float: left;
			height: 200px;
			width: 50%;
		}
		#leo-center .leo-container{
			border-left: 1px solid #CCCCCC;
			border-right: 1px solid #CCCCCC;
			height:100%;
		}
		
	#leologo{  width:30%;float:left; min-height:60px}
	#leo-hheaderright{
		display:block;
		float:right;
		width:60%;
		min-height:60px;
	}
	.placeholder{
		background:blue;
	}
	.module-pos{
		height:40px;
		width:160px;
		margin:6px; 
		cursor:move;
	}
	.leo-editmodule{
		background:#FFF;
		height:100%;
		width:100%;
		border:solid 1px #CCC
	}
	.leo-container > div{
		position:relative; 
	}
	#leo-page .pos{
		font-size: 11px;
		font-weight: bold;
		left: 0;
		padding: 4px 12px;
		position: absolute;
		top: 0;
		background:#FFF;
		z-index:10
	}
	.leotheme-layout{
		width:70%;
		float:left
	}
	.holdposition{
		width:28%;
		float:left;
		height:600px;
		overflow:auto
	}
</style>

<?php echo '<script type="text/javascript" src="'.__PS_BASE_URI__.str_replace("//","/",'modules/leotempcp/assets/admin/jquery-ui-1.9.2.custom.js').'"></script>'; ?>

<?php 
	
		 
?>
<div id="leo-page" class="clearfix">
	<div class="leo-container holdposition" data-position="noposition">
		<div class="pos">Modules </div>
		<?php foreach( $modulesw as $modulessss ) { ?>
		<div>fff</div>
		<?php } ?>
	</div>

	<br>
	<div class="leotheme-layout">
		<div id="leo-header" >
			<div class="topbar leo-container">
				<div class="pos">HOOK_TOP</div>
				<div class="module-pos" id="module-<?php echo '14';?>"><div class="leo-editmodule">D</div></div>
			</div>
			<div class="leoheader clearfix">
				<div id="leologo"><div class="pos">LOGO</div></div>
				<div id="leo-hheaderright" class="leo-container"><div class="pos">HOOK_HEADERRIGHT</div></div>
			</div>
		</div>
		<div id="leo-menu" class="leo-container"><div class="pos">HOOK_TOPNAVIATION</div></div>
		<div id="leo-slideshow" class="leo-container"><div class="pos">HOOK_SLIDESHOW</div></div>
		<div id="leo-promotetop"  class="leo-container"><div class="pos">HOOK_PROMOTETOP</div></div>

		<div id="leo-content" class="clearfix"  >
			<div id="leo-left" class="leo-container" ><div class="pos">HOOK_LEFT</div></div>
			<div id="leo-center"><div  class="leo-container inner"><div class="pos">HOOK_HOME</div></div></div>
			<div id="leo-right" class="leo-container" ><div class="pos">HOOK_RIGHT</div></div>
		</div>
		<div id="leo-bottom" class="leo-container clearfix">
			<div class="pos">HOOK_BOTTOM</div>
		</div>
		<div id="leo-footer" class="clearfix">
			<div id="leo-right"><div class="pos">HOOK_COPYRIGHT</div></div>
		</div>
	</div>
</div>	
<script type="text/javascript">
$('#leo-page .leo-container').sortable( {
			connectWith: '#leo-page .leo-container',
			containment: '#leo-page',
			forceHelperSize: true,
			forcePlaceholderSize: true,
			placeholder: 'placeholder',
			handle:".leo-editmodule"
		});
</script>