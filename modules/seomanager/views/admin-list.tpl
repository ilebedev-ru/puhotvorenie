{*
*
*  @author    Onasus <contact@onasus.com>
*  @copyright 20012-2015 Onasus www.onasus.com
*  @license   Refer to the terms of the license you bought at codecanyon.net
*
*}

<link href="http://localhost/prestashop1560/modules/seomanager/views/bt/css/bootstrap.css" rel="stylesheet">

<br/><br/><br/><br/>
	<div class="container">
		
		<form class="form-horizontal" role="form">
			<div class="col-lg-offset-2 col-lg-10"><h4>Category pages</h4></div>
			<br/><br/>
			<div class="form-group">
				<label for="categorytitlemeta" class="col-lg-2 control-label" >Title</label>
				<div class="col-lg-5 tooltip-demo">
					<input class="form-control" id="categorytitlemeta" name="categorytitlemeta" value="copy current cat values">
					<input type="hidden" name="languageName" id="languageID" value='.$this->context->language->id.'  />
				</div>
				
				<div class="col-lg-5 tooltip-button">
					<button id="categorytitlemeta-po" class="btn btn-info" rel="popover" data-toggle='tooltip' data-original-title="Bootstrap popover" data-content="&#123;CATEROGY_NAME&#125;, &#123;CATEROGY_NAME_PARENT&#125;, &#123;SHOP_NAME&#125;" title="Product tooltip">Shortcodes</button>
				</div>
			</div><!--/ end category title -->
			<div class="form-group">
				<label for="categorydescmeta" class="col-lg-2 control-label" >Description</label>
				<div class="col-lg-5 tooltip-demo">
					<input class="form-control" id="categorydescmeta" name="categorytitlemeta" value="copy current cat values">
				</div>
				
				<div class="col-lg-5 tooltip-button">
					<button id="categorydescmeta-po" class="btn btn-info" rel="popover" data-toggle='tooltip' data-original-title="Bootstrap popover" data-content="&#123;CATEROGY_NAME&#125;, &#123;CATEROGY_NAME_PARENT&#125;, &#123;SHOP_NAME&#125;" title="Product tooltip">Shortcodes</button>
				</div>
				
				<div class="col-lg-offset-2 col-lg-10">
					<div class="checkbox">
					<label>
						<input type="checkbox" name="categoryrad" > Update Category
					</label>
					</div>
					<input type="hidden" value="Customer" name="categoryradhidden"/>
				</div>	
			</div><!--/ end category description -->
			
			
			<br/>
			<div class="form-group">
				<div class="col-lg-offset-2 col-lg-10">
				  <button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>
		</form>

	</div><!-- /.container -->
	<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!--script src="bt/js/jquery.js"></script-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script src="http://localhost/prestashop1560/modules/seomanager/views/bt/js/bootstrap.min.js"></script>
    <!--script src="bt/js/holder.js"></script-->
	<script src="http://localhost/prestashop1560/modules/seomanager/views/js/tooltip.js"></script>
	<script src="http://localhost/prestashop1560/modules/seomanager/views/js/popover.js"></script>
	<!--script src="web/seomanager.js"></script-->
	<script type="text/javascript">  
		$(document).ready(function (){
			
			$("#categorytitlemeta-po").popover({
			trigger:'hover',
			html:true
			});
			$("#categorydescmeta-po").popover({
			trigger:'hover',
			html:true
			});
	
		}); 
	</script>
