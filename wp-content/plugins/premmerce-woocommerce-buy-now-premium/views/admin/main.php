<?php

if ( ! defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h2><?php _e('Buy now', 'premmerce-woocommerce-buy-now'); ?></h2>

    <div class="form-wrap">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field(); ?>

            <input type="hidden" name="action" value="premmerce_oneclickorder_save_settings">

            <table class="form-table">
                <tbody>

                <tr>
                    <th scope="row"><?php _e('Product archive', 'premmerce-woocommerce-buy-now'); ?></th>
                    <td>
                        <label>
                            <?php $checked = isset($data['displayOnCatalog']) && $data['displayOnCatalog'] ? 'checked' : '' ?>
                            <input type="checkbox" name="displayOnCatalog" value="1" <?= $checked ?>>
                            <?php _e('Display on product archive', 'premmerce-woocommerce-buy-now'); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Name', 'premmerce-woocommerce-buy-now'); ?></th>
                    <td>
                        <label>
                            <?php $checked = isset($data['name']) && $data['name'] ? 'checked' : '' ?>
                            <input type="checkbox" name="name" value="1" <?= $checked ?>>
                            <?php _e('Customer name', 'premmerce-woocommerce-buy-now'); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('E-mail', 'premmerce-woocommerce-buy-now'); ?></th>
                    <td>
                        <label>
                            <?php $checked = isset($data['email']) && $data['email'] ? 'checked' : '' ?>
                            <input type="checkbox" name="email" value="1" <?= $checked ?>>
                            <?php _e('Customer e-mail', 'premmerce-woocommerce-buy-now'); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Phone number', 'premmerce-woocommerce-buy-now'); ?></th>
                    <td>
                        <label>
                            <?php $checked = isset($data['phone']) && $data['phone'] ? 'checked' : '' ?>
                            <input type="checkbox" name="phone" value="1" <?= $checked ?>>
                            <?php _e('Customer phone number', 'premmerce-woocommerce-buy-now'); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Comment', 'premmerce-woocommerce-buy-now'); ?></th>
                    <td>
                        <label>
                            <?php $checked = isset($data['comment']) && $data['comment'] ? 'checked' : '' ?>
                            <input type="checkbox" name="comment" value="1" <?= $checked ?>>
                            <?php _e('Comment to order', 'premmerce-woocommerce-buy-now'); ?>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit"
                       id="submit"
                       name="save_changes"
                       class="button button-primary"
                       value="<?php _e('Save Changes', 'premmerce-woocommerce-buy-now'); ?>"
                >
            </p>
        </form>
    </div>
</div>