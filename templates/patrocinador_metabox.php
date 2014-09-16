<?php wp_nonce_field( basename( __FILE__ ), 'patrocinador_url_post_nonce' ); ?>

<p>
  <label for="url-post">Ligazón do patrocinador (se ten algunha)</label>
  <br/>
  <input class="widefat" type="text" name="url-post"
      id="url-metabox" value="<?php
        echo esc_attr( get_post_meta( $object->ID, 'url', true ) );
        ?>" size="300" />
</p>