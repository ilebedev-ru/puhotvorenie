<!--SMARTBLOG-->
{if !empty($posts)}
    <div id="main-page_article-list">
        <div class="block_content box-line h3">
            <div>
                <ul id="productTabs-6" class="nav nav-tabs">
                    <li class="active"><a>Статьи</a></li>
                </ul>
            </div>
        </div>
        {foreach from=$posts item=post}
            {assign var="options" value=null}
            {$options.id_post = $post.id}
            {$options.slug = $post.link_rewrite}
            <a class="main-page_article" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" title="{$post.title}">
                <p class="main-page_article-date">{$post.date_added|date_format:"d.m.Y"}</p>
                <p class="main-page_article-title">{$post.title}</p>
                {assign var=smartshownoimg value=!empty($smartshownoimg)}
                {if ($post.post_img != "no" && $smartshownoimg == 0) || $smartshownoimg == 1}
                    <img itemprop="image" alt="{$post.title}" src="{$modules_dir}/smartblog/images/{$post.post_img}-home-small.jpg" class="main-page_article-img">
                {/if}
                <p class="main-page_article-text">{$post.short_description}</p>
            </a>
        {/foreach}
    </div>
{/if}
<!--/SMARTBLOG-->    