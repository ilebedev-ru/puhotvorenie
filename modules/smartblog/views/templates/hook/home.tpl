<!--SMARTBLOG-->
{if !empty($posts)}
    <div id="main-page_article-list">
        <h2 class="article-list-item_title">Статьи</h2>
        {foreach from=$posts item=post}
            {assign var="options" value=null}
            {$options.id_post = $post.id}
            {$options.slug = $post.link_rewrite}
            <a class="main-page_article" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" title="{$post.title}">
                <p class="main-page_article-date">{$post.date_added|date_format:"d.m.Y"}</p>
                <p class="main-page_article-title">{$post.title}</p>
                {assign var="activeimgincat" value='0'}
                {$activeimgincat = Configuration::get('smartshownoimg')}
                {if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}
                    <img itemprop="image" alt="{$post.title}" src="{$modules_dir}smartblog/images/{$post.post_img}-home-default.jpg" class="main-page_article-img">
                {/if}
                <p class="main-page_article-text">{$post.short_description}</p>
            </a>
        {/foreach}
    </div>
{/if}
<!--/SMARTBLOG-->    