{config_load file="message.conf"}

<!DOCTYPE html>
<html>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <link rel="icon" type="image/png" href="{Html::img('favicon.png')}">
    <link rel="apple-touch-icon" sizes="152x152" href="{Html::img('apple-touch-icon.png')}">
    <meta property="og:type" content="website">
    <meta property="og:description" content="">
    <meta property="og:title" content="">
    <meta property="og:url" content="">
    <meta property="og:image" content="{Html::img('og-img.png')}">
    <meta property="og:site_name" content="">
    <meta name="twitter:card" content="summary" />
    <title></title>
    <script
        src="https://code.jquery.com/jquery-3.5.0.min.js"
        integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
        integrity="sha256-UzFD2WYH2U1dQpKDjjZK72VtPeWP50NoJjd26rnAdUI="
        crossorigin="anonymous" />
    <link rel="stylesheet"
        href="{Html::css('app.css')}"/>
</head>
<body>

    {* toast *}
    <div class="toast-group">
        <div class="toast toast-error shadow" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
            <div class="toast-header">
                <strong class="mr-auto"><i class="fas fa-exclamation-triangle mr-2"></i>エラー</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="閉じる">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body"></div>
        </div>
        <div class="toast toast-info shadow" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
            <div class="toast-header">
                <strong class="mr-auto"><i class="fas fa-exclamation-triangle"></i> エラー</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="閉じる">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
            </div>
        </div>
    </div>

    {* header *}
    {include file='./header.tpl'}

    {* main *}
    <main>
        <div class="container py-5">
            {if isset($is_small_content)}
                <div class="row justify-content-center">
                    <div class="col-md-8 col-sm-12">
                        {block name=content}{/block}
                    </div>
                </div>
            {else}
                {block name=content}{/block}
            {/if}
        </div>
    </main>

    {* footer *}
    {include file='./footer.tpl'}

</body>
</html>
