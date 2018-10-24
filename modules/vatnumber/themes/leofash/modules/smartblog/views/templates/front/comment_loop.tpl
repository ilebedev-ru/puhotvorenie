{if $comment.id_smart_blog_comment != ''}
    <div id="comment-{$comment.id_smart_blog_comment}" class="article-comment">
        <div class="personal-img">
            <img class="avatar" alt="Avatar" src="{$modules_dir}/smartblog/images/avatar/avatar-author-default.png">
        </div>
        <div class="comment-text">
            <p class="name">{$childcommnets.name}</p>
            <p class="time" itemprop="commentTime">{$childcommnets.created|date_format}</p>
            <p class="text">{$childcommnets.content}</p>
        </div>
        {*{if Configuration::get('smartenablecomment') == 1}*}
            {*{if $comment_status == 1}*}
                {*<div class="reply">*}
                    {*<a onclick="return addComment.moveForm('comment-{$comment.id_smart_blog_comment}', '{$comment.id_smart_blog_comment}', 'respond', '{$smarty.get.id_post}')"*}
                       {*class="comment-reply-link">{l s="Ответить" mod="smartblog"}</a>*}
                {*</div>*}
            {*{/if}*}
        {*{/if}*}
        {*{if isset($childcommnets.child_comments)}*}
            {*{foreach from=$childcommnets.child_comments item=comment}*}
                {*{if isset($childcommnets.child_comments)}*}
                    {*{include file="./comment_loop.tpl" childcommnets=$comment}*}
                    {*{$i=$i+1}*}
                {*{/if}*}
            {*{/foreach}*}
        {*{/if}*}
    </div>
{/if}
                                        
                                        