<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    $reasons = [
        'config_difficulties' => __("I'm having trouble setting it up", 'qodax-checkout-manager'),
        'stopped_working' => __("The plugin stopped working", 'qodax-checkout-manager'),
        'found_another_plugin' => __("I found another plugin", 'qodax-checkout-manager'),
        'missing_feature' => __("The plugin doesn't have the functionality I need", 'qodax-checkout-manager'),
        'temporary_deactivation' => __("This is a temporary deactivation", 'qodax-checkout-manager'),
        'other' => __("Other", 'qodax-checkout-manager'),
    ];
?>

<div id="qxcm-deactivate-form" class="qxcm-modal-wrap">
    <div class="qxcm-modal qxcm-deactivate-form">
        <h2 class="qxcm-deactivation__title qxcm-mb-4">
            <?php esc_html_e('You are deactivating the Qodax Checkout Manager plugin', 'qodax-checkout-manager'); ?>
        </h2>
        <div class="qxcm-mb-4">
            <?php esc_html_e('Please, take 30 seconds to tell us what brought you to this decision.', 'qodax-checkout-manager'); ?>
        </div>

        <?php foreach ($reasons as $value => $text) { ?>
            <div class="qxcm-mb-2">
                <label>
                    <input type="radio" name="reason" value="<?php echo esc_attr($value); ?>" <?php echo $value === 'config_difficulties' ? 'checked' : ''; ?> />
                    <?php echo esc_html($text); ?>
                </label>
            </div>
        <?php } ?>

        <div class="qxcm-form-group qxcm-mt-5">
            <label class="qxcm-form-group__label"><?php esc_html_e('Please describe the deactivation details?', 'qodax-checkout-manager'); ?></label>
            <textarea class="qxcm-form-control"
                      name="message"
                      placeholder="<?php esc_attr_e('Deactivation details', 'qodax-checkout-manager'); ?>"
                      style="height: auto !important; min-height: 100px !important; width: 100%;"></textarea>
        </div>
        <div class="qxcm-d-flex justify-space-between align-items-center">
            <a href="#" class="j-qxcm-deactivation-skip"><?php esc_html_e('Skip and deactivate', 'qodax-checkout-manager'); ?></a>
            <div>
                <button class="qxcm-btn qxcm-btn--danger qxcm-btn--md j-qxcm-deactivation-cancel"><?php esc_html_e('Cancel', 'qodax-checkout-manager'); ?></button>
                <button class="qxcm-btn qxcm-btn--primary qxcm-btn--md j-qxcm-deactivation-btn"><?php esc_html_e('Deactivate', 'qodax-checkout-manager'); ?></button>
            </div>
        </div>
    </div>
</div>