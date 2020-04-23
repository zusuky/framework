<div class="form-group">
{if isset($label)}
	<label>{$label}</label>
{/if}
{if isset($help_above)}
    <div class="form-text text-muted mt-0 mb-1">{$help_above nofilter}</div>
{/if}
    <input type="email" class="form-control" {if isset($name)}name="{$name}" {/if}
{if isset($id)}id="{$id}" {/if}
{if isset($value)}value="{$value}" {else}value="" {/if}
{if isset($placeholder)}placeholder="{$placeholder}" {/if}
{if isset($maxlength)}maxlength="{$maxlength}" {/if}
{if isset($readonly)}readonly {/if}
{if isset($disabled)}disabled {/if}/>
{if isset($help_below)}
    <div class="form-text text-muted">{$help_below nofilter}</div>
{/if}
{if isset($name) && isset($errors[$name])}
    <div class="my-1 error-message">{$errors[$name]}</div>
{/if}
</div>
