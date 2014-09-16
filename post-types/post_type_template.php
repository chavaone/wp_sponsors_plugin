<?php
if(!class_exists('Sponsors_Post_Type'))
{
    /**
     * A PostTypeTemplate class that provides 3 additional meta fields
     */
    class Sponsors_Post_Type
    {
        const POST_TYPE = "patrocinador";

        public function __construct()
        {
            // register actions
            add_action('init', array(&$this, 'init'));
            add_action('admin_init', array(&$this, 'admin_init'));
        }


        public function init()
        {
            // Initialize Post Type
            $this->create_post_type();
            add_action ("save_post", array(&$this, 'save_post'), 10, 2 );
            add_filter ("manage_edit-patrocinador_columns", array(&$this, "edit_columns"));
            add_action ("manage_posts_custom_column", array(&$this, "custom_columns"));
            add_action ("admin_head", array(&$this, 'add_menu_icons_styles' ));
        }


        public function create_post_type()
        {
            $labels = array(
                'name' => _x('Patrocinadores', 'post type general name'),
                'singular_name' => _x('Patrocinador', 'post type singular name'),
                'add_new' => _x('Engadir novo', 'events'),
                'add_new_item' => __('Engadir novo patrocinador'),
                'edit_item' => __('Editar Patrocinador'),
                'new_item' => __('Novo Patrocinador'),
                'view_item' => __('Ver Patrocinador'),
                'search_items' => __('Buscar Patrocinador'),
                'not_found' =>  __('Non se atoparon patrocinadores'),
                'not_found_in_trash' => __('Non se atoparon patrocinadores no lixo'),
                'parent_item_colon' => '',
            );

            $args = array(
                'label' => __('Patrocinador'),
                'labels' => $labels,
                'public' => true,
                'can_export' => true,
                'show_ui' => true,
                '_builtin' => false,
                'capability_type' => 'post',
                'menu_icon' => '',
                'menu_position' => 5,
                'hierarchical' => false,
                'rewrite' => array( "slug" => "patrocinador" ),
                'supports'=> array(
                  'title',
                  'revisions',
                  'thumbnail',
                  'editor'
                  ),
                'show_in_nav_menus' => true,
                'has_archive' => true
            );

            register_post_type(self::POST_TYPE, $args);
        }


        public function save_post($post_id, $post )
        {
            /* Verify the nonce before proceeding. */
            if ( !isset( $_POST['patrocinador_url_post_nonce'] ) || !wp_verify_nonce( $_POST['patrocinador_url_post_nonce'], basename( __FILE__ ) ) )
              return $post_id;

            /* Get the post type object. */
            $post_type = get_post_type_object( $post->post_type );

            /* Check if the current user has permission to edit the post. */
            if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
              return $post_id;

            $new_meta_value = ( isset( $_POST['url-post'] ) ? $_POST['url-post'] : '' );

            /* Get the meta key. */
            $meta_key = 'url';

            /* Get the meta value of the custom field key. */
            $meta_value = get_post_meta( $post_id, $meta_key, true );

            /* If a new meta value was added and there was no previous value, add it. */
            if ( $new_meta_value && '' == $meta_value )
              add_post_meta( $post_id, $meta_key, $new_meta_value, true );

            /* If the new meta value does not match the old value, update it. */
            elseif ( $new_meta_value && $new_meta_value != $meta_value )
              update_post_meta( $post_id, $meta_key, $new_meta_value );

            /* If there is no new meta value but an old value exists, delete it. */
            elseif ( '' == $new_meta_value && $meta_value )
              delete_post_meta( $post_id, $meta_key, $meta_value );
        }


        public function edit_columns($columns)
        {
              $columns = array(
                  "cb" => "<input type=\"checkbox\" />",
                  "title" => "Patrocinador",
                  "col_patrocinador_url" => "Ligazón",
                  "col_patrocinador_thumb" => "Imaxe",
                  );

              return $columns;
        }


        public function custom_columns($column)
        {
            global $post;

            $custom = get_post_custom();

            switch ($column)
            {
            case "col_patrocinador_url":
            if (isset($custom["url"][0]))
            {
              $url = $custom["url"][0];
              echo '<a href="' . $url . '">' . $url . '</a>';
            }
            break;
            case "col_patrocinador_thumb":
            $thumb_id = get_post_thumbnail_id();
            $thumb_url = wp_get_attachment_image_src($thumb_id,'thumbnail-size', true);
            echo '<img src="' . $thumb_url[0] . '" height="175px"/>';
            break;
            }
        }


        public function add_menu_icons_styles()
        {?>
            <style>
                #adminmenu .menu-icon-eventos div.wp-menu-image:before
                {
                  content: "\f488";
                }
            </style>
            <?php
        }


        public function admin_init()
        {
            add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
        }


        public function add_meta_boxes()
        {

            // Add this metabox to every selected post
            add_meta_box(
              'sponsors_meta',      // Unique ID
              'Descripción da Nova ou do Evento',    // Title
              array(&$this, 'add_inner_meta_boxes'),   // Callback function
              'patrocinador',         // Admin page (or post type)
              'normal',         // Context
              'default'         // Priority
            );
        } // END public function add_meta_boxes()


        public function add_inner_meta_boxes($post)
        {
            // Render the job order metabox
            include(sprintf("%s/../templates/%s_metabox.php", dirname(__FILE__), self::POST_TYPE));
        } // END public function add_inner_meta_boxes($post)

    } // END class Post_Type_Template
} // END if(!class_exists('Post_Type_Template'))
