<div class="form-group">
{if isset($label)}
    <label>{$label}</label>
{/if}
{if isset($help_above)}
    <div class="form-text text-muted mt-0 mb-1">{$help_above nofilter}</div>
{/if}
	<select class="form-control" {if isset($name)}name="{$name}" {/if} {if isset($id)}id="{$id}" {/if}>
{if isset($add_empty)}
		<option value="">--</option>
{/if}
{if isset($options) && !empty($options)}
    {foreach $options as $key => $val}
        <option value="{$key}" {if isset($selected) && $selected == $key}selected{/if}>{$val}</option>
    {/foreach}
{/if}
	</select>
{if isset($help_below)}
    <div class="form-text text-muted">{$help_below nofilter}</div>
{/if}
{if isset($name) && isset($errors[$name])}
    <div class="my-1 error-message">{$errors[$name]}</div>
{/if}
</div>