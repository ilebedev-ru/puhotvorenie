{if $html_blocks}
    {foreach from=$html_blocks item=html_block}
        <!-- PWBlockHTML module id {$html_block.id_pwblockhtml} -->
        {$html_block.html}

        {if $html_block.need_css AND $html_block.css}
            <style>
                {$html_block.css}
            </style>
        {/if}

        {if $html_block.need_js AND $html_block.js}
            <script>
                {$html_block.js}
            </script>
        {/if}
        <!-- /PWBlockHTML module -->
    {/foreach}
{/if}