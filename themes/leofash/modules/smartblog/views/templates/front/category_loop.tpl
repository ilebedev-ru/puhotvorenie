<div id="smartblogpost-{$post.id_post}" class="article-list-item">
    <div class="article-list-item_description">
        <h4 class="article-list-item_time" itemprop="dateCreated">{$post.created|date_format:"d.m.Y"}</h4>
        <div class="article-list-item_title">
            {assign var="options" value=null}
            {$options.id_post = $post.id_post}
            {$options.slug = $post.link_rewrite}
            <a title="{$post.title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{$post.title}</a>
        </div>
        <div class="article-list-item_text">
            <p itemprop="description">{$post.short_description}</p>
        </div>
        <div class="article-list-item_more">
            {assign var="options" value=null}
            {$options.id_post = $post.id_post}
            {$options.slug = $post.link_rewrite}
            <a class="more" title="{$post.title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{l s='Read more' mod='smartblog'} </a>
            <a class="comment" title="{$post.totalcomment} Comments" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}#articleComments">{$post.totalcomment} {l s=' Comments' mod='smartblog'}</a>
        </div>
    </div>
    <a itemprop="url" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" title="{$post.title}"
       class="article-list-item_img">
        {assign var="activeimgincat" value='0'}
        {$activeimgincat = $smartshownoimg}
        {if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}
            <img itemprop="image" alt="{$post.title}"
                 src="{$modules_dir}/smartblog/images/{$post.post_img}-home-small.jpg" class="imageFeatured">
        {/if}
    </a>
</div>
