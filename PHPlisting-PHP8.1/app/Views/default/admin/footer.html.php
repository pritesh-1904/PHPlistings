    <script src="<?php echo asset('js/bootstrap/' . locale()->getDirection() . '/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo asset('js/bootstrap-confirmation/bootstrap-confirmation.js'); ?>"></script>
    <script src="<?php echo asset('js/sortable/sortable.min.js'); ?>"></script>
    <script src="<?php echo asset('js/js.js?v=108'); ?>"></script>
    <?php echo layout()->getFooterJs(); ?>
    <script>
        $(function () {
           $('[data-toggle="tooltip"]').tooltip();
        })
        $(function () {
            $('[data-toggle="popover"]').popover();
        })

        $(document).ready(function() {
            var active = $('.default-theme ul.sidebar-nav li.active');

            if (typeof active != 'undefined') {
                active.parentsUntil('ul.sidebar-nav', 'ul').each(function () {
                    $(this).slideToggle(200);
                });
            }
 
            $('.default-theme li.has-dropdown > a').on('click', function () {
                $(this).find('.ch-right').toggleClass('ch-down');
                $(this).parent().siblings().find('ul').stop(false, true).slideUp(200);
                $(this).parent().children('ul').stop(false, true).slideToggle(200);
            }); 
       
            $('#btn-hide').on('click', function () {
                if ($(window).width() < 860) {
                    $('.sidebar').slideToggle(200);
                }
        
                $('.default-theme').toggleClass('min-theme');

                $('ul.mega-menu').css('display', 'none');       
            });

            $(window).resize(function () {
                if ($(window).width() < 860) {
                    $('.sidebar').css('display', 'none');
                } else {
                    if (!$('.sidebar').is(':visible')) {
                        $('.sidebar').css('display', 'block');
                    }
                }
            });
        });
    </script>
    </body>
</html>
