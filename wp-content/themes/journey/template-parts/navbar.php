<?php
$theme_locations = get_nav_menu_locations();
$menu_obj = get_term( $theme_locations['main'], 'nav_menu');
$menu_items = wp_get_nav_menu_items( $menu_obj->term_id );
?>

<nav class="navbar navbar-dark navbar-expand-lg justify-between py-6 px-8">
    <div class="navbar-brand">
        <?php the_custom_logo(); ?>
    </div>
    <ul class="navbar-nav text-sm font-bold flex items-center gap-5">
        <?php foreach((array)$menu_items as $key => $menu_item) : ?>
            <?php $classes = get_field('classes', $menu_item); ?>
            <li class="nav-item">
                <a href="<?= $menu_item->url ?>" class="nav-link<?php if($classes) : ?> <?= $classes ?><?php endif ?>"><?= $menu_item->title ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<nav class="px-4">
    <div class="container-fluid">
        <div class="py-4">
            <div class="px-7.5">
                <div class="flex justify-between items-center">
                    <div>
                        <?php the_custom_logo(); ?>
                    </div>
                    <div>
                        <ul class="text-sm flex gap-9 items-center font-bold">
                            <?php foreach((array)$menu_items as $key => $menu_item) : ?>
                                <?php $classes = get_field('classes', $menu_item); ?>
                                <li>
                                    <a href="<?= $menu_item->url ?>" class="<?php if($classes) : ?><?= $classes ?><?php endif ?>"><?= $menu_item->title ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>