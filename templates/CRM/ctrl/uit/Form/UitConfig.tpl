<div class="crm-block crm-form-block">
    {* header *}
    <h2>Config</h2>

    {* body *}
    {foreach from=$elementNames item=elementName}
        <div class="crm-section">
            <div class="label">{$form.$elementName.label}</div>
            <div class="content">{$form.$elementName.html}</div>
            <div class="clear"></div>
        </div>
    {/foreach}

    {* footer *}
    <div class="crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
</div>
