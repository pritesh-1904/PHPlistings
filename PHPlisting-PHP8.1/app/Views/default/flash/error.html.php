<div class="alert mb-3 d-flex align-items-stretch alert-danger alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center p-0">
        <i class="fas fa-exclamation mr-3 rounded-circle alert-icon d-flex align-items-center justify-content-center border border-danger"></i>
    </div>
    <div class="align-self-center display-10">
        <?php if (is_array($view->message)) { ?>
            <?php foreach ($view->message as $message) { ?>
                <?php echo $message; ?><br />
            <?php } ?>
        <?php } else { ?>
            <?php echo $view->message; ?>
        <?php } ?>
    </div>
    <div class="d-flex align-items-center justify-content-end">
        <button type="button" class="close display-5 text-regular" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
