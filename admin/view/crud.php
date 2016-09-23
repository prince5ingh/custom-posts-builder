<div class="wrap cbp-wrap">
    <div class="cpb-block"></div>
    <div class="cpb-header cpb-colored-bg">
        <h2 class="cpb-title">
            <?php
                _e('Custom Posts <span>Builder</span>',CPB_TEXT_DOMAIN);
            ?>
        </h2>
    </div>
    <div class="cpb-content">

        <?php if( !cpb_has_post_types() ) : ?>

            <div class="cpb-post-types cpb-no-posts">
                <?php
                    echo '<span class="cpb-no-posts-msg">';
                        _e('Wooh ! You have created none, create one now .',CPB_TEXT_DOMAIN);
                    echo '</span>';
                    do_action('cpb_render_menus');
                ?>
            </div>

        <?php else: ?>

            <div class="cpb-post-types cpb-has-posts">
                <?php 
                    do_action('cpb_render_menus');
                ?>
            </div>
            <?php show_cpb_cpt_list(); ?>

        <?php endif; ?>
    </div>
    <div class="cpb-footer">
        <?php do_action('cpb_cpt_template'); ?>
        <?php do_action('cpb_taxonomy_template'); ?>
        <?php do_action('cpb_new_col_template'); ?>
        <?php echo cpb_get_dashicons_list(); ?>
    </div>
</div>