{assign var="is_small_content" value="1"}
{extends file='../commons/layout/app.tpl'}

{block name=content}

    <h1 class="page-header text-danger">
        <i class="fa fa-exclamation-triangle"></i> エラー
    </h1>

    <article>
        {if isset($error_message)}
            {{$error_message}|nl2br nofilter}
        {/if}
    </article>

{/block}
