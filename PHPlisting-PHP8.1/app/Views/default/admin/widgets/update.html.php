<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('pages', session()->get('admin/pages')); ?>"><?php echo e(__('admin.pages.breadcrumb.index')); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('widgets/' . $view->page->id, session()->get('admin/widgets/' . $view->page->id)); ?>"><?php echo e(__('admin.widgets.breadcrumb.index', ['page' => $view->page->title, 'slug' => $view->page->slug])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.widgets.breadcrumb.update', ['page' => $view->page->title, 'slug' => $view->page->slug])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.widgets.heading.update', ['page' => $view->page->title, 'slug' => $view->page->slug])); ?></h3>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-md border-0 rounded-0 p-3">
            <div class="card-body">
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->form; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        show($('select[name="_access_level"]').val());
            
        $('select[name="_access_level"]').on("change", function() {
            show($(this).val());
        });

        function show(val)
        {
            $('#_access_level_pricing_ids').closest('div[class*="form-group"]').hide();

            if ('5' == val) {
                $('#_access_level_pricing_ids').closest('div[class*="form-group"]').show();
            }
        }
    });
</script>
