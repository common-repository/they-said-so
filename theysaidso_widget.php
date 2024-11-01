<?php

if ( ! class_exists("theysaidso_widget") )
{
class theysaidso_widget extends WP_Widget
{

   private $options;

   function __construct() {
       $options = array ( 'qod_category' => 'inspire', 
                           'publisher_id' => null, 
                           'quote_id' => null,
                           'display_style' => 'tso_blackcard'
                        );
                         
       parent::__construct( 'theysaidso_widget', 'They Said So Widget' );
   }

   public function widget($args, $instance)
   {
       print $args['before_widget'];

       $display_style = $this->options['display_sityle'];

       $options = "";
       if( isset($instance['quote_id']) && (!empty($instance['quote_id'])) )
       {
          $options .= 'quote_id: \'' . $instance['quote_id'] . '\',';
       }
       if( isset($instance['publisher_id']) && (!empty($instance['publisher_id'])) )
       {
          $options .= 'publisher_id: \'' . $instance['publisher_id'] . '\',';
       }
       if( isset($instance['qod_category']) && (!empty($instance['qod_category'])) )
       {
          $options .= 'qod_category: \'' . $instance['qod_category'] . '\',';
       }

       if( isset($instance['display_style']) )
       {
          $options .= 'display_style: \'' . $instance['display_style']. '\',';
          $display_style = $instance['display_style'];
       }

       $options = trim($options, ",");
       print "<div class=\"" . $display_style . "\">";
       print "<script>TheySaidSo.render({";
       print $options; 
       print "});";
       print "</script></div>";

       print $args['after_widget'];
   }


   public function form( $instance ) 
   {
     $defaults = $this->options;

     $instance = wp_parse_args( (array)$instance,$defaults);

     $display_style = $instance['display_style'];
     $qod_cat = $instance['qod_category'];

     print "<p>";
     print "<label for=\"" . $this->get_field_id( 'quote_id' ) ."\">Quote ID(optional):</label>";
     print "<input id=\"" . $this->get_field_id( 'quote_id' ) . "\" name=\"" . $this->get_field_name( 'quote_id' ) . "\" value=\"" . $instance['quote_id'] . "\" style=\"width:100%;\" />";
     print "</p>";
     print "<p>";
     print "<label for=\"" . $this->get_field_id( 'qod_category' ) ."\">QOD Category(Optional. Used if the quote id is not set):</label>";
     print "<select id=\"" . $this->get_field_id( 'qod_category' ) . "\" name=\"" . $this->get_field_name( 'qod_category') . "\" class=\"widefat\" style=\"width:100%;\">";
     $cats = self::get_categories();
     foreach($cats as $cat)
     {
        $selected = "";
        if ( $qod_cat == $cat['name'] )
           $selected = " selected=\"selected\"";
        print "<option value=\"". $cat['name'] . "\"" . $selected .">" . $cat['description'] ."</option>";
     }
     print "</select>";
     print "<p>";
     print "<label for=\"" . $this->get_field_id( 'display_style' ) ."\">Display style:</label>";
     print "<select id=\"" . $this->get_field_id( 'display_style' ) . "\" name=\"" . $this->get_field_name( 'display_style') . "\" class=\"widefat\" style=\"width:100%;\">";
     $styles = self::get_styles();
     foreach($styles as $style)
     {
        $selected = "";
        if ( $display_style == $style['style'] )
           $selected = " selected=\"selected\"";
        print "<option value=\"". $style['style'] . "\"" . $selected .">" . $style['name'] ."</option>";
     }
     print "</select>";
     print "</p>";
   }

   public function update( $new_instance,$old_instance ) 
   {
       $instance = $old_instance;

       $instance['quote_id'] = strip_tags($new_instance['quote_id']);
       $instance['qod_category'] = strip_tags($new_instance['qod_category']);
       $instance['publisher_id'] = strip_tags($new_instance['publisher_id']);
       $instance['display_style'] = strip_tags($new_instance['display_style']);
       $instance['context'] = strip_tags($new_instance['context']);

       return $instance;
   }

   private function get_categories()
   {
     return array( 
                array('name' => 'inspire', 'description' => 'Inspiring Quote of the day'),
                array('name' => 'management', 'description' => 'Management Quote of the day'),
                array('name' => 'life', 'description' => 'Life Quote of the day'),
                array('name' => 'funny', 'description' => 'Funny Quote of the day'),
                array('name' => 'love', 'description' => 'Love Quote of the day'),
                array('name' => 'art', 'description' => 'Art Quote of the day'),
                  );
   }

   private function get_styles()
   {
	   $styles = array ( 
			   array( 'style' => 'tso_default', 'name' => 'Default' , 'description' => 'Default style. No decoration. Blends with rest of your website.','tags' => 'minimal,modern'),
			   array( 'style' => 'tso_grey_leader', 'name' => 'Just Basics' , 'description' => 'Beautiful dark grey quote symbol draws attention to the quote', 'tags' => 'grey,clean,modern'),
			   array( 'style' => 'tso_classic', 'name' => 'Classic' , 'description' => 'Classic yellow post it style backgrounds with classic fonts.','tags' => 'classic,yellow'),
			   array( 'style' => 'tso_blackcard', 'name' => 'BlackWhiteShadow' , 'description' => 'Clean white rectangle with author name in dark font. Quote printed with white font and contained with in a black card. A clip in top right and drop shadow on bottom corners give this design a elegant look and would go with most grey scale websites.','tags' => 'black,white,pin,clean,modern'),
			   array( 'style' => 'tso_simple_borders', 'name' => 'SimpleFences' , 'description' => 'Cursive grey words that are demarcated from surrounding content by top and bottom lines ','tags' => 'borders,italic,grey'),
			   array( 'style' => 'tso_purply_box', 'name' => 'PurpleWhite' , 'description' => 'Quote text is below a purple large purple quote icon both contained in a rectangle white card with rounded corners. The card drops shadow all around it','tags' => 'purple,grey,white,unique'),
			   array( 'style' => 'tso_golden_black', 'name' => 'BoldShapes' , 'description' => 'Bold grey card with quote in lighter text and author name in golden letters in a angular black band','tags' => 'bold,bright'),
			   array( 'style' => 'tso_circly_head', 'name' => 'PublicSecret' , 'description' => 'Cozy orange curve on the left and a huge orange quote ball on the top adds artistic value to the overlined orange text','tags' => 'cozy,orange,artistic'),
			   array( 'style' => 'tso_wild_strawberries', 'name' => 'Wild Strawberries' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_modern_green', 'name' => 'Modern Green' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_clean_single', 'name' => 'Clean Single' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_blueborder_floating', 'name' => 'Floating Box of Blue' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_bubble_right_bottom', 'name' => 'Speech from bottom' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_pinched', 'name' => 'Pinched' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_oval_thought', 'name' => 'Oval Thought Bubble' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_oval_thought_border', 'name' => 'Oval Thought Border' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_green_speech', 'name' => 'Green Rectangle Speech Bubble' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_oval_speech_border', 'name' => 'Oval Speech Border' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_orange_card', 'name' => 'Orange Card' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_red_card', 'name' => 'Red Card' , 'description' => '','tags' => ''),
			   array( 'style' => 'tso_green_card', 'name' => 'Green Card' , 'description' => '','tags' => '')
				   );

	   return $styles;
   }
}; // class

} 

?>
