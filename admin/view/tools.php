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

        <div class="cpb-tabs">
            <?php
                $tabs       = cbp_get_tools_tab();
                $current    = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'import'; // default is import
                echo '<h1 class="nav-tab-wrapper">';
                foreach( $tabs as $tab => $tab_options ){
                    $class = ( $tab == $current ) ? ' nav-tab-active' : '';
                    echo "<a class='nav-tab$class' href='?page=cpb_tools&tab=$tab'>{$tab_options['label']}</a>";

                }
                echo '</h1>';
            ?>
        </div>
        <div class="cpb-tool-msgs">
            <?php do_action('cpb_import_status'); ?>
        </div>
        <div class="cpb-tabs-content">
            <form class="cpb-tools-form" method="post">
                <?php
                    call_user_func($tabs[$current]['callback']);
                ?>
                <input type="submit" name="cpb_tools_submit" value="<?php _e('Submit',CPB_TEXT_DOMAIN) ?>" class="cpb-tools-submit button button-primary"/>
            </form>
        </div>

    </div>
    <div class="cpb-footer">
    </div>
</div>