<?php

if ( ! defined('WPINC')) {
    die;
}

?>

<div class="oco-modal oco-modal--sm">
    <div class="oco-modal__header">
        <div class="oco-modal__header-title">
            <?php _e('Buy now', 'premmerce-woocommerce-buy-now'); ?>
        </div>
        <div class="oco-modal__header-close" data-oneclick-order--btn-close>
            <i class="oco-modal__header-close-ico">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path d="M.66.7l.71-.71 15 15-.71.71z"/>
                    <path d="M16.02 1.06l-.7-.72-15.3 15.3.72.72z"/>
                </svg>
            </i>
        </div>
    </div>

    <form method="POST" action="<?= $routeUrl; ?>" data-oneclick-order--ajax>

        <?php if ($success): ?>
            <div class="oco-modal__content">
                <div class="typo">
                    <?php echo __('Order was created', 'premmerce-woocommerce-buy-now'); ?>
                </div>
            </div>
        <?php else: ?>

            <input type="hidden" name="product_id" value="<?= $productId; ?>">

            <?php wp_nonce_field('wp_rest'); ?>

            <div class="oco-modal__content">
                <?php if ($options['name']): ?>
                    <p class="form-row form-row-wide">
                        <label for="name" class="">
                            <?php _e('Your name', 'premmerce-woocommerce-buy-now'); ?> <abbr class="required"
                                                                                                   title="required">*</abbr>
                        </label>
                        <?php $value = $userData && isset($userData['first_name']) ? $userData['first_name'] : ''; ?>
                        <input type="text" class="input-text" name="name" id="name" value="<?= $value; ?>" required>
                    </p>
                <?php endif; ?>

                <?php if ($options['email']): ?>
                    <p class="form-row form-row-wide">
                        <label for="email" class="">
                            <?php _e('Your E-mail', 'premmerce-woocommerce-buy-now'); ?> <abbr class="required"
                                                                                                     title="required">*</abbr>
                        </label>
                        <?php $value = $userData && isset($userData['email']) ? $userData['email'] : ''; ?>
                        <input type="text" class="input-text" name="email" id="email" value="<?= $value; ?>" required>
                    </p>
                <?php endif; ?>

                <?php if ($options['phone']): ?>
                    <p class="form-row form-row-wide">
                        <label for="phone" class="">
                            <?php _e('Your phone number', 'premmerce-woocommerce-buy-now'); ?> <abbr
                                    class="required" title="required">*</abbr>
                        </label>
                        <?php $value = $userData && isset($userData['phone']) ? $userData['phone'] : ''; ?>
                        <input type="text" class="input-text" name="phone" id="phone" value="<?= $value; ?>" required>
                    </p>
                <?php endif; ?>

                <?php if ($options['comment']): ?>
                    <p class="form-row form-row-wide">
                        <label for="comment" class="">
                            <?php _e('Your comment', 'premmerce-woocommerce-buy-now'); ?>
                        </label>
                        <textarea class="input-text" name="comment" id="comment" style="resize: none;"
                                  rows="3"></textarea>
                    </p>
                <?php endif; ?>
            </div>

            <div class="oco-modal__footer">
                <div class="oco-modal__footer-row">
                    <div class="oco-modal__footer-btn hidden-xs">
                        <button class="button alt" type="reset" data-oneclick-order--btn-close>
                            <?php _e('Close', 'premmerce-woocommerce-buy-now'); ?>
                        </button>
                    </div>

                    <div class="oco-modal__footer-btn">
                        <button class="button alt" type="submit" data-button-loader="button">
                            <?php _e('Place order', 'premmerce-woocommerce-buy-now'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </form>

</div>