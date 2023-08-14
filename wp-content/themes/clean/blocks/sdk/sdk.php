<?php
$fields = get_fields();

?>

<section class="sdk">
    <div class="container">
        <div class="sdk__left">
            <?php if (!empty($fields['title'])) { ?>
                <h2><?php echo wp_kses_post($fields['title']); ?></h2>
            <?php } ?>
            <?php if (!empty($fields['description'])) { ?>
                <div class="sdk__left-desc"><?php echo wp_kses_post($fields['description']); ?></div>
            <?php } ?>
            <?php if (!empty($fields['button']) && !empty($fields['button_link'])) { ?>
                <div class="sdk__left-button"><a href="<?php echo wp_kses_post($fields['button_link']); ?>"><?php echo wp_kses_post($fields['button']); ?></a></div>
            <?php } ?>
        </div>
        <div class="sdk__right">
            <?php
            if (!empty($fields['image'])) { ?>
                <div class="sdk__right-image"><img src="<?php echo wp_kses_post($fields['image']['url']); ?>"></div>
            <?php }?>
        </div>


    </div>
</section>
