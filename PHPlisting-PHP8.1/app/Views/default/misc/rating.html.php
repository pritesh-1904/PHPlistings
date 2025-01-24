<?php

    $response = '';
            
    for ($i = 0; $i < 5; $i++) {
        if ($view->rating > $i) {
            if ($view->rating - $i > 0 && $view->rating - $i < 1) {
                $response .= '<i class="fas fa-star-half-alt"></i>';
            } else {
                $response .= '<i class="fas fa-star"></i>';
            }
        } else {
            $response .= '<i class="far fa-star"></i>';
        }
    }

    echo $response;

?>