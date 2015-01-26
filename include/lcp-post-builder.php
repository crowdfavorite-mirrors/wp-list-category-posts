<?

class LcpPostBuilder{
  // Singleton implementation
  private static $instance = null;

	public static function get_instance(){
		if( !isset( self::$instance ) ){
			self::$instance = new self;
		}
		return self::$instance;
	}

  /**
   * This function should be overriden for template system.
   * @param post $single
   * @param HTML tag to display $tag
   * @return string
   */
  public function lcp_build_post($single, $tag){
    global $post;

    $class ='';
    if ( $post->ID == $single->ID ):
      $class = " class = current ";
    endif;

    $lcp_display_output = '<'. $tag . $class . '>';

    $lcp_display_output .= $this->get_post_title($single);

    // Comments count
    $lcp_display_output .= $this->get_comments($single, $this->params['comments_tag'], $this->params['comments_class']);

    // Date
    $lcp_display_output .= $this->get_date($single,
                                             $this->params['date_tag'],
                                             $this->params['date_class']);

    // Date Modified
    if (!empty($this->params['date_modified_tag']) || !empty($this->params['date_modified_class'])):
      $lcp_display_output .= $this->get_modified_date($single,
                                             $this->params['date_modified_tag'],
                                             $this->params['date_modified_class']);
    else:
      $lcp_display_output .= $this->get_modified_date($single);
    endif;

    // Author
    $lcp_display_output .= $this->get_author($single,
                                     $this->params['author_tag'],
                                     $this->params['author_class']);

    // Display ID
    if (!empty($this->params['display_id']) && $this->params['display_id'] == 'yes'){
        $lcp_display_output .= $single->ID;
    }

    // Custom field display
    $lcp_display_output .= $this->get_custom_fields($single);

    $lcp_display_output .= $this->get_thumbnail($single);

    // Content
    $lcp_display_output .= $this->get_content($single,
                                     $this->params['content_tag'],
                                     $this->params['content_class']);

    // Excerpt
    $lcp_display_output .= $this->get_excerpt($single,
                                     $this->params['excerpt_tag'],
                                     $this->params['excerpt_class']);

    $lcp_display_output .= $this->get_posts_morelink($single);

    $lcp_display_output .= '</' . $tag . '>';
    return $lcp_display_output;
  }

    private function get_post_title($single, $tag = null, $css_class = null){
    $info = '<a href="' . get_permalink($single->ID);

    $lcp_post_title = apply_filters('the_title', $single->post_title, $single->ID);

    if ( !empty($this->params['title_limit']) && $this->params['title_limit'] != "0" ):
      $lcp_post_title = substr($lcp_post_title, 0, intval($this->params['title_limit']));
      if( strlen($lcp_post_title) >= intval($this->params['title_limit']) ):
        $lcp_post_title .= "&hellip;";
      endif;
    endif;

    $info.=  '" title="' . wptexturize($single->post_title) . '"';

    if (!empty($this->params['link_target'])):
      $info .= ' target="' . $this->params['link_target'] . '" ';
    endif;

    if ( !empty($this->params['title_class'] ) &&
         empty($this->params['title_tag']) ):
      $info .= ' class="' . $this->params['title_class'] . '"';
    endif;

    $info .= '>' . $lcp_post_title . '</a>';

    if( !empty($this->params['post_suffix']) ):
      $info .= " " . $this->params['post_suffix'];
    endif;

    if (!empty($this->params['title_tag'])){
      $pre = "<" . $this->params['title_tag'];
      if (!empty($this->params['title_class'])){
        $pre .= ' class="' . $this->params['title_class'] . '"';
      }
      $pre .= '>';
      $post = "</" . $this->params['title_tag'] . ">";
      $info = $pre . $info . $post;
    }

    if( $tag !== null || $css_class !== null){
      $info = $this->assign_style($info, $tag, $css_class);
    }

    return $info;
  }

}