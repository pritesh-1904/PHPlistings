<?php 

$url = $view->template;

$replacements = [
    'link' => $view->url,
    'site_name' => config()->general->get('site_name'),
];

foreach ($replacements as $key => $value) {
    if (strstr($url, '{' . $key. '}')) {
        $url = preg_replace('/{' . $key . '}/u', str_replace('$', '\$', $value), $url);
    }
}

$message = '
    <p><strong>' . __('pricing.info.backlink.heading') . '</strong></p>
    <p>' . __('pricing.info.backlink.description') . '</p>
    <form>
        <div class="form-group">
            <label for="_backlink"></label>
            <div class="input-group">
                <input id="_backlink" class="form-control form-control-lg" type="text" value="' . $url . '">
                <div class="input-group-append">
                    <span id="_backlink_trigger" class="input-group-text"><i class="far fa-copy"></i></span>
                </div>
            </div>
        </div>
    </form>
    <script>
        $(document).ready( function() {
            $("#_backlink_trigger").on("click", function () {
                $("#_backlink").select();
                document.execCommand("copy");
                $("#_backlink").blur();

                $("#_backlink_trigger").attr("style", "background-color: #EEFFEE;");
            });
        });
    </script>
    ';

echo view('flash/primary', [
    'message' => $message,
]);
