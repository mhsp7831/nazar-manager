<?php
/**
 * Plugin Name: مدیریت نظرات
 * Description: ثبت و مدیریت نظرات متنی، تصویری، صوتی و ویدیویی به صورت Post Type سفارشی.
 * Version: 1.1
 * Author: MHSP :)
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('nazar', [
        'labels' => [
            'name' => 'نظرات',
            'singular_name' => 'نظر',
            'add_new' => 'افزودن نظر جدید',
            'add_new_item' => 'افزودن نظر',
            'edit_item' => 'ویرایش نظر',
            'new_item' => 'نظر جدید',
            'view_item' => 'مشاهده نظر',
            'all_items' => 'همه نظرات',
            'search_items' => 'جستجوی نظرات',
            'not_found' => 'یافت نشد',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_position' => 5,
        'menu_icon' => 'dashicons-format-chat',
        'taxonomies' => ['category', 'nazar_tag'],
    ]);

    $labels = array(
        'name'                       => _x( 'برچسب‌های نظر', 'taxonomy general name', 'textdomain' ),
        'singular_name'              => _x( 'برچسب نظر', 'taxonomy singular name', 'textdomain' ),
        'search_items'               => __( 'جستجوی برچسب‌های نظر', 'textdomain' ),
        'popular_items'              => __( 'برچسب‌های نظر پرکاربرد', 'textdomain' ),
        'all_items'                  => __( 'همه برچسب‌های نظر', 'textdomain' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'ویرایش برچسب نظر', 'textdomain' ),
        'update_item'                => __( 'به‌روزرسانی برچسب نظر', 'textdomain' ),
        'add_new_item'               => __( 'افزودن برچسب نظر جدید', 'textdomain' ),
        'new_item_name'              => __( 'نام برچسب نظر جدید', 'textdomain' ),
        'separate_items_with_commas' => __( 'برچسب‌های نظر را با کاما جدا کنید', 'textdomain' ),
        'add_or_remove_items'        => __( 'افزودن یا حذف برچسب‌های نظر', 'textdomain' ),
        'choose_from_most_used'      => __( 'انتخاب از برچسب‌های نظر پرکاربرد', 'textdomain' ),
        'not_found'                  => __( 'هیچ برچسب نظری یافت نشد.', 'textdomain' ),
        'menu_name'                  => __( 'برچسب‌های نظر', 'textdomain' ),
    );

    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'nazar_tag' ),
        'show_in_rest'          => true,
    );

    register_taxonomy( 'nazar_tag', 'nazar', $args );



    $labels_cat = array(
        'name'                       => _x( 'دسته‌بندی‌های نظر', 'taxonomy general name', 'textdomain' ),
        'singular_name'              => _x( 'دسته‌بندی نظر', 'taxonomy singular name', 'textdomain' ),
        'search_items'               => __( 'جستجوی دسته‌بندی‌های نظر', 'textdomain' ),
        'all_items'                  => __( 'همه دسته‌بندی‌های نظر', 'textdomain' ),
        'parent_item'                => __( 'دسته‌بندی والد', 'textdomain' ),
        'parent_item_colon'          => __( 'دسته‌بندی والد:', 'textdomain' ),
        'edit_item'                  => __( 'ویرایش دسته‌بندی نظر', 'textdomain' ),
        'update_item'                => __( 'به‌روزرسانی دسته‌بندی نظر', 'textdomain' ),
        'add_new_item'               => __( 'افزودن دسته‌بندی نظر جدید', 'textdomain' ),
        'new_item_name'              => __( 'نام دسته‌بندی نظر جدید', 'textdomain' ),
        'menu_name'                  => __( 'دسته‌بندی‌های نظر', 'textdomain' ),
    );

    $args_cat = array(
        'hierarchical'          => true,
        'labels'                => $labels_cat,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'nazar_category' ),
        'show_in_rest'          => true,
    );

    register_taxonomy( 'nazar_category', 'nazar', $args_cat );

});

add_action('add_meta_boxes', function () {
    add_meta_box('nazar_media', 'فایل‌های نظر', 'nazar_media_fields', 'nazar', 'side', 'default');
});

function nazar_media_fields($post)
{
    $audio = get_post_meta($post->ID, '_nazar_audio', true);
    $image = get_post_meta($post->ID, '_nazar_image', true);
    $video = get_post_meta($post->ID, '_nazar_video', true);

    wp_nonce_field('nazar_save_fields', 'nazar_nonce');

    // Display for Image
    echo '<p><label for="nazar_image">تصویر نظر:</label><br>';
    echo '<div id="nazar_image_preview" style="margin-bottom:10px;">';
    if ($image) {
        echo '<img src="' . esc_url($image) . '" style="max-width:100%; height:auto; margin-top:10px; display:block; border-radius:8px;" />';
    }
    echo '</div>';
    echo '<input type="text" id="nazar_image" name="nazar_image" value="' . esc_attr($image) . '" style="width:100%;margin-bottom:10px;" />';
    echo ' <button type="button" class="button upload-button" data-target="nazar_image">آپلود</button>';
    echo ' <button type="button" class="button remove-button" data-target="nazar_image">حذف تصویر</button></p>';
    echo '<em style="display:block;margin-top:10px;margin-bottom:20px;font-size:12px;color:#666;text-align:justify;">برای استفاده از این تصویر در طراحی از کدکوتاه <code>[nazar_image]</code> استفاده کنید. برای مخفی کردن عنوان از <code>[nazar_image title="0"]</code> استفاده کنید. (این کد کوتاه فقط در صفحات آرشیو کار می کند.)</em>';

    // Display for Audio
    echo '<p><label for="nazar_audio">فایل صوتی:</label><br>';
    echo '<div id="nazar_audio_preview" style="margin-bottom:10px;">';
    if ($audio) {
        echo '<audio controls src="' . esc_url($audio) . '" style="width:100%; margin-top:10px; display:block;"></audio>';
    }
    echo '</div>';
    echo '<input type="text" id="nazar_audio" name="nazar_audio" value="' . esc_attr($audio) . '" style="width:100%;margin-bottom:10px;" />';
    echo ' <button type="button" class="button upload-button" data-target="nazar_audio">آپلود</button>';
    echo ' <button type="button" class="button remove-button" data-target="nazar_audio">حذف صوت</button></p>';
    echo '<em style="display:block;margin-top:20px;margin-bottom:20px;font-size:12px;color:#666;text-align:justify;">برای استفاده از این فایل صوتی در طراحی از کدکوتاه <code>[nazar_audio]</code> استفاده کنید. برای مخفی کردن عنوان از <code>[nazar_audio title="0"]</code> استفاده کنید. (این کد کوتاه فقط در صفحات آرشیو کار می کند.)</em>';

    // Display for Video
    echo '<p><label for="nazar_video">فایل ویدیویی:</label><br>';
    echo '<div id="nazar_video_preview" style="margin-bottom:10px;">';
    if ($video) {
        echo '<video controls src="' . esc_url($video) . '" style="width:100%; margin-top:10px; display:block; border-radius:8px;"></video>';
    }
    echo '</div>';
    echo '<input type="text" id="nazar_video" name="nazar_video" value="' . esc_attr($video) . '" style="width:100%;margin-bottom:10px;" />';
    echo ' <button type="button" class="button upload-button" data-target="nazar_video">آپلود</button>';
    echo ' <button type="button" class="button remove-button" data-target="nazar_video">حذف ویدیو</button></p>';
    echo '<em style="display:block;margin-top:20px;font-size:12px;color:#666;text-align:justify;">برای استفاده از این فایل ویدیویی در طراحی از کدکوتاه <code>[nazar_video]</code> استفاده کنید. برای مخفی کردن عنوان از <code>[nazar_video title="0"]</code> استفاده کنید. (این کد کوتاه فقط در صفحات آرشیو کار می کند.)</em>';

    // JS for media uploader and updating previews
    echo '<script>
    jQuery(document).ready(function($){
        // Upload button logic
        $(".upload-button").click(function(e){
            e.preventDefault();
            var target_input_id = $(this).data("target"); // e.g., "nazar_image", "nazar_audio", or "nazar_video"
            var custom_uploader = wp.media({
                title: "انتخاب فایل",
                button: { text: "استفاده از این فایل" },
                multiple: false,
                library: {
                    type: target_input_id === "nazar_video" ? "video" : 
                          target_input_id === "nazar_audio" ? "audio" : "image"
                }
            }).on("select", function(){
                var attachment = custom_uploader.state().get("selection").first().toJSON();
                $("#" + target_input_id).val(attachment.url); // Update input field

                // Update preview based on target_input_id
                if (target_input_id === "nazar_image") {
                    $("#nazar_image_preview").html(\'<img src="\' + attachment.url + \'" style="max-width:100%; height:auto; margin-top:10px; display:block; border-radius:8px;" />\');
                } else if (target_input_id === "nazar_audio") {
                    $("#nazar_audio_preview").html(\'<audio controls src="\' + attachment.url + \'" style="width:100%; margin-top:10px; display:block;"></audio>\');
                } else if (target_input_id === "nazar_video") {
                    $("#nazar_video_preview").html(\'<video controls src="\' + attachment.url + \'" style="width:100%; margin-top:10px; display:block; border-radius:8px;"></video>\');
                }
            }).open();
        });

        // Remove button logic
        $(".remove-button").click(function(e){
            e.preventDefault();
            var target_input_id = $(this).data("target");
            $("#" + target_input_id).val(""); // Clear the input field
            $("#" + target_input_id + "_preview").html(""); // Clear the preview
        });
    });
    </script>';
}

add_action('save_post', function ($post_id) {
    if (!isset($_POST['nazar_nonce']) || !wp_verify_nonce($_POST['nazar_nonce'], 'nazar_save_fields')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Save image URL
    if (isset($_POST['nazar_image'])) {
        update_post_meta($post_id, '_nazar_image', esc_url_raw($_POST['nazar_image']));
    } else {
        delete_post_meta($post_id, '_nazar_image');
    }

    // Save audio URL
    if (isset($_POST['nazar_audio'])) {
        update_post_meta($post_id, '_nazar_audio', esc_url_raw($_POST['nazar_audio']));
    } else {
        delete_post_meta($post_id, '_nazar_audio');
    }

    // Save video URL
    if (isset($_POST['nazar_video'])) {
        update_post_meta($post_id, '_nazar_video', esc_url_raw($_POST['nazar_video']));
    } else {
        delete_post_meta($post_id, '_nazar_video');
    }
});


add_shortcode('nazar_image', function ($atts) {
    $atts = shortcode_atts(array(
        'title' => '1' // Default to showing title
    ), $atts, 'nazar_image');

    $post_id = get_the_ID();
    $image_url = get_post_meta($post_id, '_nazar_image', true);
    if (!$image_url) return '';

    ob_start();
    ?>
    <div class="nazar-image-wrapper">
        <?php if ($atts['title'] === '1'): ?>
        <p><strong>نظر تصویری:</strong></p>
        <?php endif; ?>
        <a href="<?php echo esc_url($image_url); ?>" class="nazar-lightbox-link elementor-clickable elementor-open-lightbox" data-elementor-open-lightbox="default">
            <img src="<?php echo esc_url($image_url); ?>" alt="نظر تصویری"
              class="nazar-lightbox-image"
              style="max-width:100%;height:auto;margin-bottom:-10px!important;"
              oncontextmenu="return false;" draggable="false">
        </a>
    </div>
    <?php
    return ob_get_clean();
});

add_shortcode('nazar_audio', function ($atts) {
    $atts = shortcode_atts(array(
        'title' => '1' // Default to showing title
    ), $atts, 'nazar_audio');

    $post_id = get_the_ID();
    $audio_url = get_post_meta($post_id, '_nazar_audio', true);
    if (!$audio_url) return '';

    ob_start();
    ?>
    <div class="nazar-audio-wrapper">
        <?php if ($atts['title'] === '1'): ?>
        <p><strong>نظر صوتی:</strong></p>
        <?php endif; ?>
        <audio controls controlsList="nodownload" style="width:100%;" oncontextmenu="return false;">
            <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
            مرورگر شما از پخش صوت پشتیبانی نمی‌کند.
        </audio>
    </div>
    <?php
    return ob_get_clean();
});

add_shortcode('nazar_video', function ($atts) {
    $atts = shortcode_atts(array(
        'title' => '1' // Default to showing title
    ), $atts, 'nazar_video');

    $post_id = get_the_ID();
    $video_url = get_post_meta($post_id, '_nazar_video', true);
    if (!$video_url) return '';

    ob_start();
    ?>
    <div class="nazar-video-wrapper">
        <?php if ($atts['title'] === '1'): ?>
        <p><strong>نظر ویدیویی:</strong></p>
        <?php endif; ?>
        <video controls controlsList="nodownload" style="width:100%;" oncontextmenu="return false;">
            <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
            مرورگر شما از پخش ویدیو پشتیبانی نمی‌کند.
        </video>
    </div>
    <?php
    return ob_get_clean();
});