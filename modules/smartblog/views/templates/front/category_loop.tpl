<div id="smartblogpost-{$post.id_post}" class="article-list-item">    
	<div class="article-list-item_description">       
		<div class="article-list-item_title">
			{assign var="options" value=null}       
			{$options.id_post = $post.id_post}      
			{$options.slug = $post.link_rewrite}      
			<a title="{$post.title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{$post.title}</a>
		</div>      
		<div class="article-list-item_text rte">
			<p itemprop="description">{$post.short_description}</p> 
		</div>   
		<div class="article-list-item_more">   
			{assign var="options" value=null}          
			{$options.id_post = $post.id_post}    
			{$options.slug = $post.link_rewrite}     
			<a class="more" title="{$post.title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{l s='Читать далее »' mod='smartblog'} </a>
            {if $post.totalcomment}<a class="comment" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}#articleComments">{l s='Комментариев:' mod='smartblog'} {$post.totalcomment} {l s='шт:' mod='smartblog'}</a>{/if}
		</div>  
	</div>   
	<a itemprop="url" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" title="{$post.title}" class="article-list-item_img">       
		{assign var="activeimgincat" value='0'}        
		{$activeimgincat = $smartshownoimg}
		{if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}     
			<img itemprop="image" alt="{$post.title}" src="{$modules_dir}/smartblog/images/{$post.post_img}-home-default.jpg" class="imageFeatured"> 
		{/if}   
	</a>
</div>