<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 */
if ( isset( $_GET['settings-updated'] ) ) {
	add_settings_error(
		'quabads_messages',
		'quabads_messages',
		__( 'Settings Updated', 'quabads' ),
		'updated'
	);
}
settings_errors( 'quabads_messages' );
?>
<style>
    .quab-settings-wrap{
        width: 100%;
        padding:20px;
        box-sizing: border-box;
        margin: 10px;
        margin-right: 30px;
    }
    .qs-wrap{
        background-color: white;
        border:solid thin #ddd;
        padding: 20px;
        box-sizing: border-box;
    }
    .sub-quab-setting{
        margin-bottom: 20px;
    }
</style>
<div class="quab-settings-wrap">
    <div class="qs-wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p>Do you have a QuabAds Publisher account? If not, <a href="https://dashboard.quabads.com/sign-up/" target="_blank"><strong>register one</strong></a> - it takes less than 3 minutes.</p>
        <form class="quabads" action="options.php" method="post">
            <?php settings_fields( $this->plugin_name ); ?>
            <?php do_settings_sections( $this->plugin_name ); ?>
            <?php submit_button(); ?>
        </form>
    </div>    
</div>


