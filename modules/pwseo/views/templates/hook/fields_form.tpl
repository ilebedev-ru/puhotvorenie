{if !empty($editor->multiclass)}
    {if $editor_type == "meta"}
        {foreach from=$editor->getFields() key=entity item=entitys}
            <input type="hidden" name="entity[]" value="{$entity}">
            <input type="hidden" name="entityId[]" value="{$entitys.id}">
            {foreach from=$entitys.fields key=fieldKey item=fieldParams}
                {if isset($fieldParams.type) && $fieldParams.type == "meta" && $editor->getFieldValue($fieldKey) !== false}
                    <div class="pwseo_zone edit-meta">
                        <div id="{$fieldKey}" class="pwseo_field-ajax">
                            <label>{$fieldParams.name}</label>
                            {if isset($fieldParams.size) && $fieldParams.size == "textarea"}
                                <textarea cols="70" rows="4" name="{$fieldKey}">{$editor->getFieldValue($fieldKey)|htmlentitiesUTF8}</textarea>
                            {else}
                                <input type="text" width="520" name="{$fieldKey}" value="{$editor->getFieldValue($fieldKey)|htmlentitiesUTF8}">
                            {/if}
                        </div>
                    </div>
                {/if}
            {/foreach}
        {/foreach}
    {elseif $editor_type == "description"}
        {foreach from=$editor->getFields() key=entity item=entitys}
            <input type="hidden" name="entity[]" value="{$entity}">
            <input type="hidden" name="entityId[]" value="{$entitys.id}">
            {foreach from=$entitys.fields key=fieldKey item=fieldParams}
                {if isset($fieldParams.type) && $fieldParams.type == "description" && $editor->getFieldValue($fieldKey) !== false}
                    <div class="pwseo_zone edit-meta">
                        <div id="{$fieldKey}" class="pwseo_field-ajax">
                            <label>{$fieldParams.name}</label>
                            {if isset($fieldParams.size) && $fieldParams.size == "textarea"}
                                <textarea cols="100" rows="30" name="{$fieldKey}">{$editor->getFieldValue($fieldKey)|htmlentitiesUTF8}</textarea>
                            {else}
                                <input type="text" width="520" name="{$fieldKey}" value="{$editor->getFieldValue($fieldKey)|htmlentitiesUTF8}">
                            {/if}
                        </div>
                    </div>
                {/if}
            {/foreach}
        {/foreach}
    {/if}
{else}
    {if $editor_type == "meta"}
        {foreach from=$editor->getFields() key=fieldKey item=fieldParams}
            {if isset($fieldParams.type) && $fieldParams.type == "meta" && $editor->getFieldValue($fieldKey) !== false}
                <div class="pwseo_zone edit-meta">
                    <div id="{$fieldKey}" class="pwseo_field-ajax">
                        <label>{$fieldParams.name}</label>
                        {if isset($fieldParams.size) && $fieldParams.size == "textarea"}
                            <textarea cols="70" rows="4" name="{$fieldKey}">{$editor->getFieldValue($fieldKey)|htmlentitiesUTF8}</textarea>
                        {else}
                            <input type="text" width="520" name="{$fieldKey}" value="{$editor->getFieldValue($fieldKey)|htmlentitiesUTF8}">
                        {/if}
                    </div>
                </div>
            {/if}
        {/foreach}
    {elseif $editor_type == "description"}
        {foreach from=$editor->getFields() key=fieldKey item=fieldParams}
            {if isset($fieldParams.type) && $fieldParams.type == "description" && $editor->getFieldValue($fieldKey) !== false}
                <div class="pwseo_zone edit-meta">
                    <div id="{$fieldKey}" class="pwseo_field-ajax">
                        <label>{$fieldParams.name}</label>
                        {if isset($fieldParams.size) && $fieldParams.size == "textarea"}
                            <textarea cols="100" rows="30" name="{$fieldKey}">{$editor->getFieldValue($fieldKey)|htmlentitiesUTF8}</textarea>
                        {else}
                            <input type="text" width="520" name="{$fieldKey}" value="{$editor->getFieldValue($fieldKey)|htmlentitiesUTF8}">
                        {/if}
                    </div>
                </div>
            {/if}
        {/foreach}
    {/if}
{/if}