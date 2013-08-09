<?php
/*
  Plugin Name: JSON API Extra
  Plugin URI: https://github.com/zenozeng/wp-json-api-extra/
  Description: Yet some ugly but useful API for Wordpress
  Version: 0.0.3
  Author: Zeno Zeng
  Author: http://zenoes.com
  License: GNU General Public License Version 3
*/

class JSON_API_Extra {
    static function install() {
        add_option('json_api_extra_site_last_modified', 0);
        self::update_last_modified();
    }
    static function update_last_modified() {
        update_option('json_api_extra_site_last_modified', time());
    }
    static function get_last_modified() {
        return get_option('json_api_extra_site_last_modified');
    }
    static function get_blog_options() {
        $arr = array();
        $arr['blogname'] = get_option('blogname');
        $arr['blogdescription'] = get_option('blogdescription');
        return $arr;
    }
}

function json_api_extra() {
    $data = '';
    if(isset($_GET) && isset($_GET['jsonextra'])) {
        switch ($_GET['jsonextra']) {
            
          case 'lastModified': # the timestamp of the last modifition of this site
              $data = array('lastModified' => JSON_API_Extra::get_last_modified());
              break;
          case 'blogOptions':
              $data = JSON_API_Extra::get_blog_options();
              break;
        }
        $json = json_encode($data);
        if (isset($_GET['callback'])) {
            $callback = $_GET['callback'];
            header('Content-Type: application/javascript');
            echo "$callback($json)";
        } else {
            echo $json;
        }
        exit();
    }
}

add_action('init', 'json_api_extra');
register_activation_hook(__FILE__, array('JSON_API_Extra', 'install'));

// http://codex.wordpress.org/Plugin_API/Action_Reference # Post, Page, Attachment, and Category Actions (Admin) 
$hooks = array('create_category', 'delete_category', 'trashed_post', 'deleted_post', 'edit_category', 'save_post');
foreach($hooks as $hook) {
    add_action($hook, 'JSON_API_Extra::update_last_modified');
}
