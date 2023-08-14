<?php
$fields = get_field('header', 'options');
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php wp_head(); ?>
</head>
<body>
<header class="header">
    <div class="container">
        <?php if (function_exists('pll_the_languages')) : ?>
            <ul class="language-switcher">
                <?php pll_the_languages(array(
                    'show_flags' => 0,
                    'show_names' => 1,
                    'display_names_as' => 'slug',
                    'hide_if_empty' => 0,
                    'force_home' => 1
                )); ?>
            </ul>
        <?php endif; ?>

    </div>
</header>
<main>
