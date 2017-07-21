<?php
/*
 * This file will be included at catalog/controller/startup.php as first priority
 * Right before inclusion of Theme specific functions.php
 *
 */


/* * * Theme specific - override Settings !  * * */
!defined('DIR_SYSTEM') ? die() : false;
// Example:
$template_config_settings = array(
    'theme_default_product_category_list_resize'     => 'resize',
    'theme_default_product_info_thumb_resize'        => 'resize',
    'theme_default_product_info_image_mid_resize'    => 'resize',
    'theme_default_extension_module_featured'        => 'resize',
    'theme_default_product_short_description_length' => 250,
    // 'theme_default_image_category_width'       => 80,
    // 'theme_default_image_category_height'      => 80,
);

foreach ($template_config_settings as $key => $val) {
    $this->config->set($key, $val);
}

/* Introducing HOOKS */
/* * ********** Example for product/index/after ************* */

/*
 * To set hook.
 * string = hook name
 * string = callback functions
 */

$this->hook->setHook('product/index/after', 'default_remove_image');
$this->hook->setHook('product/index/after', 'content_meta');


/*
 * callback functions
 * reference = to array
 *
 */

function default_remove_image(&$data, &$registry)
{
    $db = $registry->get('db');
    $registry->get('load')->model('catalog/product');
    // Real modifications.
    //
    // $product = $registry->get('model_catalog_product')->getProduct(51);
    // prd($product);
    // prd($db->query('select * from oc_product limit 10'));
    /*
      $data['image_mid'] = '';
      $data['popup'] = '';
      $data['thumb'] = '';
     */
}

function content_meta(&$data, &$registry)
{
    $config = $registry->get('config');
    $url = $registry->get('url');
    if (!empty($data['content_meta'])) {
        foreach ($data['content_meta'] as $meta_type => $val) {
            // Product Videos
            if ($meta_type == 'product_video') {
                $data['product_videos'] = [];
                foreach ($val as $video) {
                    $data['product_videos'][] = [
                        'video'     => 'https://www.youtube.com/watch?v=' . $video['video'][$config->get('config_language_id')] . '',
                        'video_src' => $url->link('common/youtube', 'inpt=' . $video['video'][$config->get('config_language_id')] . '&quality=hq&play')             //   HTTPS_SERVER . 'youtube/yt-thumb.php?inpt=' . $video . '&quality=hq&play"'
                    ];

                }
            }
        }
    }

}