<?php
/* 
Plugin Name: They Said So
Plugin URI: https://theysaidso.com/extensions/wordpress
Version: 1.2.8
Author: theysaidso.com
Author URI: https://theysaidso.com
Description: Embed beautiful quotes in your wordpress site very easily. Lot of <a href="https://theysaidso.com/embed/styles">display styles </a> to choose from. Can't find a quote that you like you can add your own at theysaidso.com and embed it in all your sites. 
*/


if ( ! class_exists("theysaidso") )
{
  class theysaidso 
  {
      var $version = "1.2.9";
      var $admin_options_name = "theysaidso_admin_options";

      function __construct()
      {
      }

      function init()
      {
        $this->admin_options();
      }

      function get_options()
      {
        return $this->admin_options();
      }

      function add_header()
      {
         wp_enqueue_script('theysaidso_script_handle','https://theysaidso.com/gadgets/v3/theysaidso.js',array(),false);
      }

      function content_prepare($content = '')
      {
         $content = html_entity_decode($content);
         $opts = get_option($this->admin_options_name);
         $options = "";
         if ( !empty($opts) )
         {
           foreach($opts as $k => $v)
             $options = $k . ":" . "'" . $v . "',";
         }

         $pattern = '/{theysaidso}.*{\/theysaidso}/i';
         $content = preg_replace_callback($pattern,
                                           function ($matches )
                                           {
                                             $s = array('‘','’','"','“','”');
                                             return str_replace($s,"'",$matches[0]);
                                           }
                                          ,$content);
         $pattern = '/\s*{theysaidso}(.*(display_style\s*:\s*\'(\w*)\').*){\/theysaidso}/i';
         $replace = '<div class="tso_style ${3}"><script>TheySaidSo.render({' . $options . '${1}});</script></div>';
         $content = preg_replace($pattern,$replace,$content);
         return $content;
      }

      function comment_text($comment= '')
      {
         $opts = get_option($this->admin_options_name);
         $style = $opts['comments_display_style'];

         if ( empty($style) )
            $style = "tso_classic";

         return "<div class=\"tso_style $style\"><blockquote class=\"theysaidso_quote\"><span class=\"quote\"><span class=\"quote_text\">$comment</span></span></blockquote></div>";
      }

      function admin_options()
      {
        $admin_options = array( 'beautify_comments' => 'true', 
                                'comments_display_style' => 'tso_orange_card',
                                'publisher_id' => '' 
                              );
        $opts = get_option($this->admin_options_name);
        if ( !empty($opts) )
        {
          foreach($opts as $k => $v)
             $admin_options[$k] = $v;
        }
        update_option($this->admin_options_name,$admin_options);
        return $admin_options;
      }

      function print_admin_page()
      {
        $opts = get_option($this->admin_options_name);
        $styles = self::get_styles();

        if ( isset($_POST['update_theysaidso_settings']) )
        {
           if ( isset($_POST['beautify_comments']) )
           {
              $bc = $_POST['beautify_comments'];
              $bc = sanitize_text_field($bc);
              $bc = ($bc == 'enable' ) ? $bc : "disable";
              $opts['beautify_comments'] = $bc;
           }
           if ( isset($_POST['comments_display_style']) )
           {
              $ds = $_POST['comments_display_style'];
              $ds = sanitize_text_field($ds);
              $ds = sanitize_html_class($ds);
              $selected = "tso_default";
              foreach($styles as $style)
              {
                 if ( $ds == $style['style'] )
                 {
                    $selected = $ds;
                    break;
                 }
              }
              $opts['comments_display_style'] = $ds;
           }
           if ( isset($_POST['publisher_id']) )
           {
              $pid  = $_POST['publisher_id'];
              $pid = sanitize_text_field($pid);
              $opts['publisher_id'] = $pid;
           }

           update_option($this->admin_options_name,$opts);
           print "<div class=\"updated\"><p><strong>" .  _e("Settings Updated.", "theysaidso_admin_options") . "</strong></p></div>"; 
        }
        $display_style = $opts['comments_display_style'];

        print "<div class=\"wrap\">";
        print "<form method=\"post\" action=\"". $_SERVER["REQUEST_URI"] . "\">";
        print "<h2>They Said So Options</h2>";
        print "<h3>They Said So Publisher ID if available</h3>";
        print "<p>A Publisher ID allows you to unlock more cool features. Don't worry for most of the functionality you don't need this and leave it empty!</p>";
        print "<input class=\"form-control input-lg\" placeholder=\"Publisher ID\" name=\"publisher_id\" type=\"text\" value=\"" . $opts[publisher_id] ."\">";
     print "<p>";
     print "<h3><label for=\"beautify_comments\">Beautify blog comments</label></h3>";
     $checked = "";
     if ( $opts['beautify_comments'] == 'enable')
          $checked = "checked";
     print "<input value=\"enable\" type=\"radio\" name=\"beautify_comments\" $checked>Enable<br/>";
     $checked = "";
     if ( $opts['beautify_comments'] == 'disable')
          $checked = "checked";
     print "<input value=\"disable\" type=\"radio\" name=\"beautify_comments\" $checked>Disable<br/>";
     print "</p>";
     print "<p>";
     print "<h3><label for=\"comments_display_style\">Comments Display style</label></h3>";
     print "<select id=\"comments_display_style\" name=\"comments_display_style\" class=\"widefat\" style=\"width:40%;\">";
     foreach($styles as $style)
     {
        $selected = "";
        if ( $display_style == $style['style'] )
           $selected = " selected=\"selected\"";
        print "<option value=\"". $style['style'] . "\"" . $selected .">" . $style['name'] ."</option>";
     }
     print "</select>";
     print "</p>";
     print '<input name="update_theysaidso_settings" type="submit" value="Save" class="button"/>';
        print "</form>";
        print "</div>";
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

  };

} // class_exists

if ( class_exists("theysaidso") )
{
   $theysaidso = new theysaidso();
}

if ( ! function_exists("theysaidso_adminpage") )
{
   function theysaidso_adminpage()
   {
      global $theysaidso;
      if ( !isset($theysaidso) )
         return;
      if ( function_exists('add_options_page')  )
      {
         add_options_page('They Said So','They Said So',9,basename(__FILE__),array($theysaidso,'print_admin_page'));
      }
   }
}

if ( isset($theysaidso) )
{
   // Actions
   add_action('wp_head',array(&$theysaidso,'add_header'),1);
   add_action('activate_theysaidso/theysaidso.php',  array(&$theysaidso, 'init'));
   add_action('admin_menu','theysaidso_adminpage');
   // filters
   add_action('the_content',array(&$theysaidso,'content_prepare'));
   $opts = $theysaidso->get_options() ;
   if ( $opts['beautify_comments'] == 'enable' )
      add_action('get_comment_text',array(&$theysaidso,'comment_text'));
   //add_filter ('pre_set_site_transient_update_plugins', array(&$theysaidso,'display_transient_update_plugins'));
}

require_once('theysaidso_widget.php');
if ( class_exists("theysaidso_widget") )
{
  add_action('widgets_init', function() {
                                register_widget('theysaidso_widget'); 
                             }
             );
}

?>
