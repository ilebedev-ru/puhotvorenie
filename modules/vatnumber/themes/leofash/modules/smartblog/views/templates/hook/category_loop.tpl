<div itemtype="#" itemscope="" class="sdsarticleCat clearfix default-block-bg">
    <div id="smartblogpost-{$post.id_post}">
        <div class="sdsarticleHeader">
            {assign var="options" value=null}
            {$options.id_post = $post.id_post}
            {$options.slug = $post.link_rewrite}
            <div class="sdstitle_block"><a title="{$post.title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{$post.title}</a></div>
            {*
            {assign var="options" value=null}
            {$options.id_post = $post.id_post}
            {$options.slug = $post.link_rewrite}
            {assign var="catlink" value=null}
            {$catlink.id_category = $post.id_category}
            {$catlink.slug = $post.cat_link_rewrite}
            {if $smartshowauthor ==1}<span>{l s='Posted by' mod='smartblog'} <span itemprop="author">&nbsp;<i
                            class="icon icon-user"></i>&nbsp;
                    {if $smartshowauthorstyle != 0}{$post.firstname} {$post.lastname}{else}{$post.lastname} {$post.firstname}{/if}</span>{/if}
                &nbsp;&nbsp;
		 <i class="icon icon-tags"></i>&nbsp; 
		 <span itemprop="articleSection"><a
                     href="{smartblog::GetSmartBlogLink('smartblog_category',$catlink)}">{if $title_category != ''}{$title_category}{else}{$post.cat_name}{/if}</a></span>
		 &nbsp;<span class="comment"> &nbsp;<i class="icon icon-comments"></i>&nbsp; 
		 <a title="{$post.totalcomment} Comments"
            href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}#articleComments">{$post.totalcomment} {l s=' Comments' mod='smartblog'}</a>
		 </span>
                {if $smartshowviewed ==1}<span class="views-count">{l s=' views' mod='smartblog'} ({$post.viewed}
                    )</span>{/if}</span>
                    *}
        </div>
        <div class="articleContent">
            <a itemprop="url" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" title="{$post.title}" class="imageFeaturedLink">
                {assign var="activeimgincat" value='0'}
                {$activeimgincat = $smartshownoimg}
                {if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}
                    <img itemprop="image" alt="{$post.title}" src="{$modules_dir}/smartblog/images/{$post.post_img}-home-small.jpg" class="imageFeatured">
                {/if}
            </a>
            <div class="sdsarticle-des">
                <span itemprop="description" class="clearfix">{$post.short_description}</span>
            </div>
        </div>
        <div class="sdsreadMore">
            {assign var="options" value=null}
            {$options.id_post = $post.id_post}
            {$options.slug = $post.link_rewrite}
            <span class="more"><a title="{$post.title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}"class="r_more">{l s='Read more' mod='smartblog'} </a></span>
        </div>
    </div>
</div>