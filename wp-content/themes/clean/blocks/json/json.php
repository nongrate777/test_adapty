<?php
$fields = get_fields();

?>

<section class="json">
    <div class="container">
        <div class="json__left">
            <?php
            if (!empty($fields['image'])) { ?>
                <div class="json__left-image"><img src="<?php echo wp_kses_post($fields['image']['url']); ?>"></div>
            <?php }?>
        </div>
        <div class="json__right">
            <?php if (!empty($fields['title'])) { ?>
                <h2><?php echo wp_kses_post($fields['title']); ?></h2>
            <?php } ?>
            <?php if (!empty($fields['description'])) { ?>
                <div class="json__right-desc"><?php echo wp_kses_post($fields['description']); ?></div>
            <?php } ?>
            <?php if (!empty($fields['button']) && !empty($fields['button_link'])) { ?>
                <div class="json__right-button"><a href="<?php echo wp_kses_post($fields['button_link']); ?>"><?php echo wp_kses_post($fields['button']); ?></a></div>
            <?php } ?>
        </div>


    </div>
</section>
