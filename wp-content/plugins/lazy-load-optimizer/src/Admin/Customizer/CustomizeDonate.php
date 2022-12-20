<?php

namespace LazyLoadOptimizer\Admin\Customizer;

class CustomizeDonate extends \WP_Customize_Control
{
    public function render_content()
    {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?>:</span>
        </label>
        <a href="https://www.patreon.com/processby">
            <?php _e('Support on Patreon', 'lazy-load-optimizer'); ?>
        </a>
        <?php
    }

}