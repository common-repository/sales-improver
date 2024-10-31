<?php
    $option = $this->get_option();
    if(($license['type'] ?? 'trial') == 'trial') $option['type'] = 'views';
    $this->update_option();
?>
<div class="wrap woocommerce">
    <form action="options.php" method="post" class="sales-improver">
        <?php
            wp_nonce_field('update-options');
            settings_fields( SALES_IMPROVER_SLUG );
            do_settings_sections( SALES_IMPROVER_SLUG );
        ?>

        <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
            <label for="views" class="nav-tab <?php if($option['type'] == 'views'): ?> nav-tab-active <?php endif; ?>">
                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[type]" value="views"
                       id="views" <?php if($option['type'] == 'views'): ?> checked <?php endif; ?>
                /> Based on Views
            </label>

            <label for="sales" class="nav-tab <?php if($option['type'] == 'sales'): ?> nav-tab-active <?php endif; ?> <?php if( ($license['type'] ?? 'trial') == 'trial' ): ?> disabled <?php endif; ?>">
                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[type]" value="sales"
                       id="sales" <?php if($option['type'] == 'sales'): ?> checked <?php endif; ?>
                /> Based on Sales
            </label>

            <label for="traffic" class="nav-tab <?php if($option['type'] == 'traffic'): ?> nav-tab-active <?php endif; ?> <?php if( ($license['type'] ?? 'trial') == 'trial' ): ?> disabled <?php endif; ?>">
                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[type]" value="traffic"
                    id="traffic" <?php if($option['type'] == 'traffic'): ?> checked <?php endif; ?>
                /> Based on Actual Traffic
            </label>

            <label for="ratings" class="nav-tab <?php if($option['type'] == 'ratings'): ?> nav-tab-active <?php endif; ?> <?php if( ($license['type'] ?? 'trial') == 'trial' ): ?> disabled <?php endif; ?>">
                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[type]" value="ratings"
                       id="ratings" <?php if($option['type'] == 'ratings'): ?> checked <?php endif; ?>
                /> Based on Ratings
            </label>
            <?php do_action('improve_sales_tab') ?>
        </nav>

        <div class="tab-content">
            <div class="tab-pane views <?php if($option['type'] == 'views'): ?> active <?php endif; ?>">
                <div class="row py">
                    <div class="col-4">
                        <label for="views_message">Message shown</label>
                    </div>
                    <div class="col-4">
                        <textarea name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][views][message]" id="views_message" ><?php echo esc_attr($option['options']['views']['message']) ?></textarea>
                    </div>
                    <div class="col-4">
                        (Example: "Viewed 12x today")<br />
                        (%amount% = amount of views<br />
                        %interval% = interval (choose below)
                    </div>
                </div>

                <div class="row py">
                    <div class="col-4">
                        <label for="views_intervals">Intervals used</label>
                    </div>
                    <div class="col-4">
                        <div>
                            <label for="views_today">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][views][interval]" id="views_today" value="today" <?php if($option['options']['views']['interval'] == 'today'): ?> checked <?php endif; ?> > Today
                            </label>
                        </div>

                        <div>
                            <label for="views_week">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][views][interval]" id="views_week" value="week" <?php if($option['options']['views']['interval'] == 'week'): ?> checked <?php endif; ?>> This Week
                            </label>
                        </div>

                        <div>
                            <label for="views_month">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][views][interval]" id="views_month" value="month" <?php if($option['options']['views']['interval'] == 'month'): ?> checked <?php endif; ?>> This Month
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        Daily (example: today)<br />
                        Weekly (example: this week)<br />
                        Monthly (example: this month)
                    </div>
                </div>

                <div class="row py">
                    <div class="col-4">
                        <label for="views_where">Where to show this message</label>
                    </div>
                    <div class="col-4">
                        <div>
                            <label for="views_all">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][views][where]" id="views_all" value="all" <?php if($option['options']['views']['where'] == 'all'): ?> checked <?php endif; ?>> All Products
                            </label>
                        </div>
                        <div>
                            <label for="views_top">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][views][where]" id="views_top" value="top" <?php if($option['options']['views']['where'] == 'top'): ?> checked <?php endif; ?>> Top
                                <input type="number" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][views][top]" id="views_top_count" min="1" value="<?php echo esc_attr($option['options']['views']['top']) ?>"> viewed products
                            </label>
                        </div>
                    </div>
                    <div class="col-4">

                    </div>
                </div>
            </div>
            <div class="tab-pane sales <?php if($option['type'] == 'sales'): ?> active <?php endif; ?>">
                <div class="row py">
                    <div class="col-4">
                        <label for="message">Message shown</label>
                    </div>
                    <div class="col-4">
                        <textarea  name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][sales][message]" id="sales_message"><?php echo esc_attr($option['options']['sales']['message']) ?></textarea>
                    </div>
                    <div class="col-4">
                        (Example: "Sold 12x today")<br />
                        (%amount% = amount of views<br />
                        %interval% = interval (choose below)
                    </div>
                </div>

                <div class="row py">
                    <div class="col-4">
                        <label for="sales_interval">Intervals used</label>
                    </div>
                    <div class="col-4">
                        <div>
                            <label for="sales_today">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][sales][interval]" id="sales_today" value="today" <?php if($option['options']['sales']['interval'] == 'today'): ?> checked <?php endif; ?>> Today
                            </label>
                        </div>

                        <div>
                            <label for="sales_week">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][sales][interval]" id="sales_week" value="week" <?php if($option['options']['sales']['interval'] == 'week'): ?> checked <?php endif; ?>> This Week
                            </label>
                        </div>

                        <div>
                            <label for="sales_month">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][sales][interval]" id="sales_month" value="month" <?php if($option['options']['sales']['interval'] == 'month'): ?> checked <?php endif; ?>> This Month
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        Daily (example: today)<br />
                        Weekly (example: this week)<br />
                        Monthly (example: this month)
                    </div>
                </div>

                <div class="row py">
                    <div class="col-4">
                        <label for="where">Where to show this message</label>
                    </div>
                    <div class="col-4">
                        <div>
                            <label for="where_all">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][sales][where]" id="where_all" value="all" <?php if($option['options']['sales']['where'] == 'all'): ?> checked <?php endif; ?>> All Products
                            </label>
                        </div>
                        <div>
                            <label for="where_top">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][sales][where]" id="where_top" value="top" <?php if($option['options']['sales']['where'] == 'top'): ?> checked <?php endif; ?>> Top
                                <input type="number" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][sales][top]" id="top_count" min="1" value="<?php echo esc_attr($option['options']['sales']['top']); ?>" > sold products
                            </label>
                        </div>
                    </div>
                    <div class="col-4">

                    </div>
                </div>
            </div>
            <div class="tab-pane traffic <?php if($option['type'] == 'traffic'): ?> active <?php endif; ?>">
                <div class="row py">
                    <div class="col-4">
                        <label for="traffic_message">Message shown</label>
                    </div>
                    <div class="col-4">
                        <textarea name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][traffic][message]" id="traffic_message"><?php echo esc_attr($option['options']['traffic']['message']); ?></textarea>
                    </div>
                    <div class="col-4">
                        (Example: "12 people are now looking at this product")<br />
                        (%amount% = amount of people at the page<!--<br />
                        %interval% = interval (choose below)-->
                    </div>
                </div>

                <div class="row py">
                    <div class="col-4">
                        <label for="traffic_where">Where to show this message</label>
                    </div>
                    <div class="col-4">
                        <div>
                            <label for="all">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][traffic][where]" id="traffic_all" value="all" <?php if($option['options']['traffic']['where'] == 'all'): ?> checked <?php endif; ?>> All Products
                            </label>
                        </div>
                        <div>
                            <label for="all">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][traffic][where]" id="traffic_top" value="top" <?php if($option['options']['traffic']['where'] == 'top'): ?> checked <?php endif; ?>> Top
                                <input type="number" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][traffic][top]" id="traffic_top_count" min="1" value="<?php echo esc_attr($option['options']['traffic']['top']); ?>"> viewed products
                            </label>
                        </div>
                    </div>
                    <div class="col-4">

                    </div>
                </div>

                <div class="row py">
                    <div class="col-4">
                        <label for="traffic_when">When to show</label>
                    </div>
                    <div class="col-4">
                        <div>
                            <label for="traffic_when">
                                Show only when more then
                                <input type="number" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][traffic][when]" id="traffic_when" min="1" value="<?php echo esc_attr($option['options']['traffic']['when']); ?>"> visitors are on the page
                            </label>
                        </div>
                    </div>
                    <div class="col-4">

                    </div>
                </div>
            </div>
            <div class="tab-pane ratings <?php if($option['type'] == 'ratings'): ?> active <?php endif; ?>">
                <div class="row py">
                    <div class="col-4">
                        <label for="message">Message shown</label>
                    </div>
                    <div class="col-4">
                        <textarea name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][ratings][message]" id="ratings_message"><?php echo esc_attr($option['options']['ratings']['message']); ?></textarea>
                    </div>
                    <div class="col-4">
                        (Example: "Customers rated this product with 4,8/5")<br />
                        (%rating% = rating of the product<!--<br />
                    %interval% = interval (choose below)-->
                    </div>
                </div>

                <div class="row py">
                    <div class="col-4">
                        <label for="ratings_where">Where to show this message</label>
                    </div>
                    <div class="col-4">
                        <div>
                            <label for="ratings_all">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][ratings][where]" id="ratings_all" value="all" <?php if($option['options']['ratings']['where'] == 'all'): ?> checked <?php endif; ?>> All Products
                            </label>
                        </div>
                        <div>
                            <label for="ratings_top">
                                <input type="radio" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][ratings][where]" id="ratings_top" value="top" <?php if($option['options']['ratings']['where'] == 'top'): ?> checked <?php endif; ?>> Top products
                                <input type="number" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][ratings][top]" id="top" min="1" value="<?php echo esc_attr($option['options']['ratings']['top']) ?>"> highest rated
                            </label>
                        </div>
                    </div>
                    <div class="col-4">

                    </div>
                </div>

                <div class="row py">
                    <div class="col-4">
                        <label for="ratings_when">When to show</label>
                    </div>
                    <div class="col-4">
                        <div>
                            <label for="ratings_when">
                                Show only when more then
                                <input type="number" name="<?php echo esc_attr(SALES_IMPROVER_SLUG); ?>[options][ratings][when]" id="ratings_when" min="1" max="5" step=".01" value="<?php echo esc_attr($option['options']['ratings']['when']); ?>"> rating is given to the product
                            </label>
                        </div>
                    </div>
                    <div class="col-4">

                    </div>
                </div>

            </div>

            <?php do_action('improve_sales_tab_pane') ?>
        </div>

        <p class="submit">
            <input type='hidden' name='action' value='update' />
            <button name="save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">Save changes</button>
        </p>
    </form>
</div>

<style>
    input, textarea{
        border-radius: 0 !important;
    }
</style>