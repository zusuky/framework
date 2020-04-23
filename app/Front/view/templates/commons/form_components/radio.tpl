<div class="form-group">
{if isset($label)}
    <label>{$label}</label>
{/if}
{if isset($help_above)}
    <div class="form-text text-muted mt-0 mb-1">{$help_above nofilter}</div>
{/if}
{if isset($options) && !empty($options)}
    {foreach $options as $key => $val}
        <div class="form-check">
            <label>
                <input type="radio" class="form-check-input" value="{$key}" {if isset($name)}name="{$name}" {/if} {if isset($checked) && $checked == $key}checked{/if}/>
                {$val}
            </label>
        </div>
    {/foreach}
{/if}
{if isset($help_below)}
    <div class="form-text text-muted">{$help_below nofilter}</div>
{/if}
{if isset($name) && isset($errors[$name])}
    <div class="my-1 error-message">{$errors[$name]}</div>
{/if}
</div>



