<p>
  <label for="url-post">Ligazón do patrocinador (se ten algunha)</label>
  <br/>
  <input class="widefat" type="text" name="url-post"
      id="url-metabox" value="<?php
      	global $post;
        echo esc_attr( get_post_meta( $post->ID, 'url', true ) );
        ?>" size="300" />
</p>