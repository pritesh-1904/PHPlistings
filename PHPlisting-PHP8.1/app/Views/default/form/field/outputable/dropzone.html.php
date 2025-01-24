<?php
    if ($view->value->count() > 0) {
        $id = bin2hex(random_bytes(12));

        $files = [];

        foreach ($view->value as $upload) {
            if ('' != $upload->get('title', '')) {
                $name = $upload->title;
                $description = $upload->description;
            } else {
                $name = $upload->name . '.' . $upload->extension;
                $description = '';
            }

            if ($upload->isImage()) {
                layout()->addCss('<link href="' . asset('js/simplelightbox/simplelightbox.min.css') . '" rel="stylesheet">');
                layout()->addFooterJs('<script src="' . asset('js/simplelightbox/simple-lightbox.min.js') . '"></script>');

                layout()->addFooterJs('<script>
                    var lightbox = $(\'.gallery-' . $id . ' a\').simpleLightbox({alertError: false});
                </script>');

                $attributes = attr([
                    'src' => $upload->small()->getUrl(),
                    'width' => $upload->small()->getWidth(),
                    'height' => $upload->small()->getHeight(),
                    'alt' => e($upload->title),
                ]);

                $files[] = '<a href="' . $upload->large()->getUrl() . '" title="' . e($description) . '"><img ' . $attributes . ' /></a>';
            } else {
                $files[] = '<a href="' . $upload->getUrl() . '" target="_blank" title="' . e($description) . '">' . e($name) . '</a>';
            }
        }

        echo '<div class="d-inline gallery-' . $id . '">' . implode(' ', $files) . '</div>';
    }
