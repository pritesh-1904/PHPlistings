<?php if ($view->total > 0) { ?>
<div class="row mt-4">
    <div class="col-lg-6">
    <?php

    $adjacents = 2;
    $page = request()->get->page ?? 1;
    $pages = ceil($view->total / $view->limit);

    if ($view->total > 0) {
        $from = ((($page - 1) * $view->limit) + 1);
        $to = ((($page - 1) * $view->limit) + $view->results);
        $results = e(__('pagination.results', ['from' => $from, 'to' => $to, 'total' => $view->total], $view->results));
    }

    if ($view->total > $view->limit) {
        echo '<ul class="pagination">';

        if ($page != 1) {
            echo '<li class="page-item"><a class="page-link grey-bg" aria-label="' . e(__('pagination.first')) . '" href="' . request()->urlWithQuery(['page' => null]) . '"><span aria-hidden="true">&laquo;</span></a></li>';
            echo '<li class="page-item"><a class="page-link grey-bg" aria-label="' . e(__('pagination.previous')) . '" href="' . request()->urlWithQuery(['page' => $page - 1]) . '"><span aria-hidden="true">&lt;</span></a></li>';
        }

        $start = ($page <= $adjacents ? 1 : $page - $adjacents);
        $end = ($page >= $pages - $adjacents ? $pages : $page + $adjacents);

        for ($i = $start; $i <= $end; $i++) {
            echo '<li class="page-item' . (($i == $page) ? ' active' : '') . '"><a class="page-link" href="' . request()->urlWithQuery(['page' => (($i == 1) ? null : $i)]) . '">' . $i . '</a></li>';
        }

        if ($page < $pages) {
            echo '<li class="page-item"><a class="page-link grey-bg" aria-label="' . e(__('pagination.next')) . '" href="' . request()->urlWithQuery(['page' => $page + 1]) . '"><span aria-hidden="true">&gt;</span></a></li>';
            echo '<li class="page-item"><a class="page-link grey-bg" aria-label="' . e(__('pagination.last')) . '" href="' . request()->urlWithQuery(['page' => $pages]) . '"><span aria-hidden="true">&raquo;</span></a></li>';
        }
    
        echo '</ul>';
    }

    $limits = [
        '10' => request()->urlWithQuery(['page' => null, 'limit' => 10]),
        '25' => request()->urlWithQuery(['page' => null, 'limit' => 25]),
        '50' => request()->urlWithQuery(['page' => null, 'limit' => 50]),
        '75' => request()->urlWithQuery(['page' => null, 'limit' => 75]),
        '100' => request()->urlWithQuery(['page' => null, 'limit' => 100]),
    ];
?>
    </div>
    <div class="col-lg-6 text-right">
        <div class="row">
            <div class="col-12 col-sm-9 pt-2">
                <small><?php echo $results ?? ''; ?></small>
            </div>
            <div class="col-sm-3 d-none d-sm-block">
                <form class="form-inline">
                    <select class="_limits custom-select custom-select-sm">
                        <?php foreach ($limits as $limit => $url) { ?>
                            <option value="<?php echo e($url); ?>"<?php if ((null !== request()->get->get('limit') && (string) $limit == (string) request()->get->get('limit'))) echo ' selected'; ?>><?php echo e($limit); ?></option>
                        <?php } ?>
                    </select>
                </form>
                <script>
                    $(document).ready(function() {
                        $('._limits').on('change', function() {
                            var url = $(this).find(":selected").val();
                            if ('' != url) {
                                window.location.href = url;
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>
<?php } ?>
