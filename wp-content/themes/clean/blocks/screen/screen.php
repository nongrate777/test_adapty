<?php
$fields = get_fields();

?>

<section class="screen">
    <div class="container">
        <?php if (!empty($fields['title'])) { ?>
            <h2><?php echo wp_kses_post($fields['title']); ?></h2>
        <?php }
        if (!empty($fields['image'])) { ?>
            <div class="screen__image"><img src="<?php echo wp_kses_post($fields['image']['url']); ?>"></div>
        <?php }?>
    </div>
</section>
