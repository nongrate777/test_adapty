<?php
$fields = get_fields();

$desktop_image_url = !empty($fields['image']) ? $fields['image']['url'] : '';
?>

<section class="banner">
    <div class="container">
       <div class="banner__left">
           <?php if (!empty($fields['description'])) { ?>
               <p><?php echo wp_kses_post($fields['description']); ?></p>
           <?php }

           if (!empty($fields['title'])) { ?>
               <h1><?php echo wp_kses_post($fields['title']); ?></h1>
           <?php }?>
           <?php if (!empty($fields['button']) && !empty($fields['button_link'])) { ?>
               <div class="banner__button"><a href="<?php echo wp_kses_post($fields['button_link']); ?>"><?php echo wp_kses_post($fields['button']); ?></a></div>
           <?php } ?>
       </div>
        <div class="banner__right">
            <?php if(!empty($fields['image'])) { ?>
                <div class="banner__right-image">
                    <img src="<?php echo $fields['image']['url'];?>">
                </div>
            <?php } ?>
        </div>

    </div>
</section>
