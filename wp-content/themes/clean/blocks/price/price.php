<?php
$fields = get_fields();

?>

<section class="price">
    <div class="container">
        <div class="price__left">
            <?php
            if (!empty($fields['image'])) { ?>
                <div class="price__left-image"><img src="<?php echo wp_kses_post($fields['image']['url']); ?>"></div>
            <?php }?>
        </div>
        <div class="price__right">
            <?php if (!empty($fields['title'])) { ?>
                <h2><?php echo wp_kses_post($fields['title']); ?></h2>
            <?php } ?>
            <?php if (!empty($fields['description'])) { ?>
                <div class="price__right-desc"><?php echo wp_kses_post($fields['description']); ?></div>
            <?php } ?>
            <?php if (!empty($fields['button']) && !empty($fields['button_link'])) { ?>
                <div class="price__right-button"><a href="<?php echo wp_kses_post($fields['button_link']); ?>"><?php echo wp_kses_post($fields['button']); ?></a></div>
            <?php } ?>
        </div>


    </div>
</section>
