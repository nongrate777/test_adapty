<?php
$fields = get_fields();

?>

<section class="metrics">
    <div class="container">

        <?php if (!empty($fields['title'])) { ?>
        <h2><?php echo wp_kses_post($fields['title']); ?></h2>
        <?php } ?>
        <?php if (!empty($fields['description'])) { ?>
            <div class="metrics__desc"><?php echo wp_kses_post($fields['description']); ?></div>
        <?php } ?>
        <?php if (!empty($fields['button']) && !empty($fields['button_link'])) { ?>
            <div class="metrics__button"><a href="<?php echo wp_kses_post($fields['button_link']); ?>"><?php echo wp_kses_post($fields['button']); ?></a></div>
        <?php } ?>
        <?php
        if (!empty($fields['image'])) { ?>
            <div class="metrics__image"><img src="<?php echo wp_kses_post($fields['image']['url']); ?>"></div>
        <?php }?>
    </div>
</section>
