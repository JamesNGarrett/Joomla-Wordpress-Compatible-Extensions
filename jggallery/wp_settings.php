<?php

function jg_gallery_add_settings_page(){
    add_options_page( 'JG Gallery Settings', 'JG Gallery', 'manage_options', 'jggallery', 'jg_gallery_render_plugin_settings_page' );
}
add_action( 'admin_menu', 'jg_gallery_add_settings_page' );

function jg_gallery_render_plugin_settings_page(){
    ?>
    <h2>Plugin Settings</h2>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'jg_gallery_plugin_options' );
        do_settings_sections( 'jg_gallery_plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

function jg_gallery_register_settings() {
    register_setting( 'jg_gallery_plugin_options', 'jg_gallery_plugin_options', 'jg_gallery_plugin_options_validate' );
    add_settings_section( 'gallery_settings', 'Gallery Settings', 'jg_gallery_plugin_section_text', 'jg_gallery_plugin' );

    add_settings_field( 'jg_gallery_plugin_setting_thumbnail_width', 'Thumbnail Width', 'jg_gallery_plugin_setting_thumbnail_width', 'jg_gallery_plugin', 'gallery_settings' );
    add_settings_field( 'jg_gallery_plugin_setting_image_width', 'Main Image Width', 'jg_gallery_plugin_setting_image_width', 'jg_gallery_plugin', 'gallery_settings' );
    add_settings_field( 'jg_gallery_plugin_setting_image_quality', 'Image Quality (1-100)', 'jg_gallery_plugin_setting_image_quality', 'jg_gallery_plugin', 'gallery_settings' );
}
add_action( 'admin_init', 'jg_gallery_register_settings' );

function jg_gallery_plugin_options_validate( $input ) {
    if($input['image_quality'] < 1 || $input['image_quality'] > 100){
        $input['image_quality'] = 80;
    }
    return $input;
}

function jg_gallery_plugin_section_text() {
    echo '<p>Here you can set all the options for using the Gallery</p>';
}

function jg_gallery_plugin_setting_thumbnail_width() {
    $options = get_option( 'jg_gallery_plugin_options' );
    echo "<input id='jg_gallery_plugin_setting_thumbnail_width' name='jg_gallery_plugin_options[thumbnail_width]' type='number' value='" . esc_attr( $options['thumbnail_width'] ) . "' />";
}
function jg_gallery_plugin_setting_image_width() {
    $options = get_option( 'jg_gallery_plugin_options' );
    echo "<input id='jg_gallery_plugin_setting_image_width' name='jg_gallery_plugin_options[image_width]' type='number' value='" . esc_attr( $options['image_width'] ) . "' />";
}
function jg_gallery_plugin_setting_image_quality() {
    $options = get_option( 'jg_gallery_plugin_options' );
    echo "<input id='jg_gallery_plugin_setting_image_quality' name='jg_gallery_plugin_options[image_quality]' type='number' value='" . esc_attr( $options['image_quality'] ) . "' />";
}
