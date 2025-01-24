    <a href="#" id="_bttb" class="btn btn-light btn-float" aria-label="hidden"><i class="fas fa-chevron-up"></i></a>
    <div class="d-none">
        <img src="<?php echo route('cron'); ?>" alt="" />
    </div>
    <script src="<?php echo asset('js/bootstrap/' . locale()->getDirection() . '/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo asset('js/bootstrap-confirmation/bootstrap-confirmation.js'); ?>"></script>
    <script src="<?php echo asset('js/js-cookie/js.cookie-2.2.1.min.js'); ?>"></script>
    <script src="<?php echo asset('js/misc/ofi.min.js'); ?>"></script>
    <script src="<?php echo asset('js/js.js?v=108'); ?>"></script>
    <?php echo layout()->getFooterJs(); ?>
    <script>
        var bttb = document.getElementById("_bttb");

        window.onscroll = function() {
            if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                bttb.style.display = "block";
            } else {
                bttb.style.display = "none";
            }
        };

        bttb.addEventListener('click', function (e) {
            e.preventDefault();
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        });

        objectFitImages();

        $(function () {
           $('[data-toggle="tooltip"]').tooltip();
           
        })
        $(function () {
            $('[data-toggle="popover"]').popover();
        })
    </script>
    </body>
</html>
