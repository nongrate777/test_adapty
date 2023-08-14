<?php
$fields = get_field('footer', 'options');
?>
</main>
<footer class="footer">
    <div class="container">
        <?php if (!empty($fields['copyright'])) { ?>
            <p class="footer__copyright">© <?php echo date('Y') . ' ' . $fields['copyright']; ?></p>
        <?php } ?>
    </div>
</footer>
<?php
wp_footer();
?>
</body>
</html>
