<?php

/*
Plugin Name: randomimage
Version: 4.1
Plugin URI: http://justinsomnia.org/2005/09/random-image-plugin-for-wordpress/
Description: Display a random image that links back to the post it came from
Author: Justin Watt
Author URI: http://justinsomnia.org/

INSTRUCTIONS

1) Save this file as randomimage.php in /path/to/wordpress/wp-content/plugins/ 
2) Activate "randomimage" from the Wordpress control panel. 
3) Add [?php randomimage(); ?] to your index.php or sidebar.php template file
   in /path/to/wordpress/wp-content/themes/theme-name/ where you want the random image to appear
   (make sure to replace the square brackets [] above with angle brackets <>)

CHANGELOG

4.1
updated to support WordPress 2.3's new db schema for categories and tags (still backwards compatible with 2.1 and 2.0 and maybe 1.5)

4.0.1
fixed bug in v4.0 that displayed %3 if the image had no alt attribute

4.0
converted html around each image to a template, allowing customization of caption and title position and markup
added ability to only select images that have a specific class attribute

3.3.1
added option to spit out debugging info
directly specified mysql resource $link_identifier in mysql_query

3.3
updated for WordPress 2.1's new post_type field (still works for < 2.1)

3.2
fixed bug that might cause no image to be displayed when using regex to exclude a frequently occuring image among a small set of posts 
(thanks to iSynth of http://www.synthdicate.com/ for pointing this out)

3.1
changed recent image behavior to only show one randomly selected image per post (instead of every image in a post)

3.0
added option to specify sort order: random (default) or reverse chronological (recent)
revised how configuration options are initialized

2.1
added the ability to selectively filter images by post category
added instructions to the configuration interface

2.0
created administrative interface for managing options

1.4
prevent displaying the same image twice
added inter_image_html option (<br /><br /> by default)

1.3
no longer selects images from password protected pages
added post_type option to determine whether to grab images from posts, pages, or both (this prevents pulling images from draft posts)

1.2
fixed src and alt regexes (which would have stopped at first occurence of a single or double quote, regardless of the first delimiter)
added newlines for prettier printing
added show_alt_caption option to display alt text as caption below image
added image_src_regex option to select images using a regular expression based on the image src attribute

1.1
fixed bug in posts that have multiple images which prevented any picture but the first to be displayed

1.0
inital version

LICENSE

randomimage.php
Copyright (C) 2007 Justin Watt
justincwatt@gmail.com
http://justinsomnia.org/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


// add configuration page to WordPress
function randomimage_add_page()
{
    add_options_page('Random Image', 'Random Image', 6, __FILE__, 'randomimage_configuration_page');
}
add_action('admin_menu', 'randomimage_add_page');


// helper function to set randomimage defaults (if necessary)
// and return array of options
function get_randomimage_options()
{
    $randomimage_options = get_option('randomimage_options');

    if (!isset($randomimage_options["image_template_html"]) || $randomimage_options["image_template_html"] == '') {

        if (isset($randomimage_options["show_post_title"]) || isset($randomimage_options["show_alt_caption"])) {
            // lets upgrade from v3
            $upgraded_template = '';
            if ($randomimage_options["show_post_title"] === true) {
                $upgraded_template .= "\n<strong>%1</strong><br />\n";
            }

            $upgraded_template .= "%2";

            if ($randomimage_options["show_alt_caption"] === true) {
                $upgraded_template .= "<br />\n<em>%3</em>\n";
            }

            $randomimage_options["image_template_html"] = $upgraded_template;
            
            unset($randomimage_options["show_post_title"]);
            unset($randomimage_options["show_alt_caption"]);

        } else {
            $randomimage_options["image_template_html"] = "\n<strong>%1</strong><br />\n%2<br />\n<em>%3</em>\n";
        }
    }

    // init default options if options aren't set    
    if (!isset($randomimage_options["show_images_in_posts"])) {
        $randomimage_options["show_images_in_posts"] = true;
    }
    
    if (!isset($randomimage_options["show_images_in_pages"])) {
        $randomimage_options["show_images_in_pages"] = false;
    }
    
    if (!isset($randomimage_options["number_of_images"])) {
        $randomimage_options["number_of_images"] = 1;
    }
    
    if (!isset($randomimage_options["image_attributes"])) {
        $randomimage_options["image_attributes"] = "";
    }
    
    if (!isset($randomimage_options["inter_image_html"])) {
        $randomimage_options["inter_image_html"] = "<br /><br />";
    }
    
    if (!isset($randomimage_options["image_src_regex"])) {
        $randomimage_options["image_src_regex"] = "";
    }

    if (!isset($randomimage_options["category_filter"])) {
        $randomimage_options["category_filter"] = array();
    }

    if (!isset($randomimage_options["sort_images_randomly"])) {
        $randomimage_options["sort_images_randomly"] = true;
    }

    if (!isset($randomimage_options["image_class_match"])) {
        $randomimage_options["image_class_match"] = "";
    } 

    add_option('randomimage_options', $randomimage_options);
    return $randomimage_options;
}


// generate configuration page
function randomimage_configuration_page()
{
    $randomimage_options = get_randomimage_options();
    
    // if form has been submitted, save values
    if (isset($_POST['submit']))
    {
        // booleanize all the checkboxes
        isset($_POST['show_images_in_posts']) ? $_POST['show_images_in_posts'] = true : $_POST['show_images_in_posts'] = false;
        isset($_POST['show_images_in_pages']) ? $_POST['show_images_in_pages'] = true : $_POST['show_images_in_pages'] = false;
        isset($_POST['sort_images_randomly']) ? $_POST['sort_images_randomly'] = true : $_POST['sort_images_randomly'] = false;
        
        // correct for empty image number
        if ($_POST['number_of_images'] < 1)
        {
            $_POST['number_of_images'] = 1;
        }

        // correct for posts and pages being deselected
        if (!$_POST['show_images_in_posts'] && !$_POST['show_images_in_pages'])
        {
            $_POST['show_images_in_posts'] = true;
        }

        if (!is_array($_POST['category_filter']))
        {
            $_POST['category_filter'] = array();
        }

        if (trim($_POST['image_template_html']) == '') 
        {
            $_POST['image_template_html'] = "\n<strong>%1</strong><br />\n%2<br />\n<em>%3</em>\n";
        }

        // create array of new options
        $randomimage_options = array(
            "show_images_in_posts" => $_POST['show_images_in_posts'],
            "show_images_in_pages" => $_POST['show_images_in_pages'],
            "number_of_images"     => $_POST['number_of_images'],
            "image_attributes"     => stripslashes($_POST['image_attributes']),
            "inter_image_html"     => stripslashes($_POST['inter_image_html']),
            "image_src_regex"      => stripslashes($_POST['image_src_regex']),
            "category_filter"      => $_POST['category_filter'],
            "sort_images_randomly" => $_POST['sort_images_randomly'],
            "image_class_match"    => stripslashes(trim($_POST['image_class_match'])),
            "image_template_html"  => stripslashes($_POST['image_template_html'])
        );
        update_option('randomimage_options', $randomimage_options);
    }

?>

<div class="wrap">
<h2>Random Image Settings</h2>

<form method="post" action="">

<p><strong>Instructions:</strong> Use the following options to configure how you want the Random Image plugin to appear. The sample image below will reflect the changes you make. When you're satisfied, add <code>&lt?php randomimage(); ?&gt</code> to your index.php or sidebar.php template file located in /<em>path</em>/<em>to</em>/<em>wordpress</em>/wp-content/themes/<em>theme-name</em>/

<div style="clear: both;padding-top:10px;">
<label style="float:left;width:250px;text-align:right;padding-right:6px;" for="show_images_in_posts">Include images from WordPress posts?</label>
<div style="float:left;"><input type="checkbox" id="show_images_in_posts" name="show_images_in_posts" <?php if ($randomimage_options["show_images_in_posts"]) print "checked='on'"; ?>/>&nbsp;&nbsp;<label for="show_images_in_pages">Pages?</label> <input type="checkbox" id="show_images_in_pages" name="show_images_in_pages" <?php if ($randomimage_options["show_images_in_pages"]) print "checked='on'"; ?>/><br /></div>
</div>

<div style="clear: both;padding-top:10px;">
<label style="float:left;width:250px;text-align:right;padding-right:6px;" for="sort_images_randomly">Sort Images Randomly?</label>
<div style="float:left;"><input type="checkbox" id="sort_images_randomly" name="sort_images_randomly" <?php if ($randomimage_options["sort_images_randomly"]) print "checked='on'"; ?>/> Uncheck if you want to show recent images rather than random images<br /></div>
</div>

<div style="clear: both;padding-top:10px;">
<label style="float:left;width:250px;text-align:right;padding-right:6px;padding-top:7px;" for="number_of_images">How many images to display?</label>
<div style="float:left;"><input type="text" id="number_of_images" name="number_of_images" size="1" maxlength="2" <?php if ($randomimage_options["number_of_images"]) print "value='" . $randomimage_options["number_of_images"] . "'"; ?>/></div>
</div>

<div style="clear: both;padding-top:10px;">
<label style="float:left;width:250px;text-align:right;padding-right:6px;padding-top:7px;" for="image_template_html">HTML Template:<br/>(<code>%1</code> = title,<br /><code>%2</code> = image,<br /><code>%3</code> = caption)</label>
<div style="float:left;"><textarea id="image_template_html" name="image_template_html" rows="4" cols="24" style="float:left;"><?php if ($randomimage_options["image_template_html"]) print stripslashes(htmlspecialchars($randomimage_options["image_template_html"])); ?></textarea>e.g.<br /><code>&lt;strong&gt;%1&lt;/strong&gt;&lt;br /&gt;<br />%2&lt;br /&gt;<br />&lt;em&gt;%3&lt;/em&gt;</code></div>
</div>

<div style="clear: both;padding-top:10px;">
<label style="float:left;width:250px;text-align:right;padding-right:6px;padding-top:7px;" for="inter_image_html">HTML between images:</label>
<div style="float:left;"><input type="text" id="inter_image_html" name="inter_image_html" size="12" <?php if (isset($randomimage_options["inter_image_html"])) print "value='" . stripslashes(htmlspecialchars($randomimage_options["inter_image_html"], ENT_QUOTES)) . "'"; ?>/>  e.g. <code>&lt;br /&gt;&lt;br /&gt;</code></div>
</div>

<div style="clear: both;padding-top:10px;">
<label style="float:left;width:250px;text-align:right;padding-right:6px;padding-top:7px;" for="image_attributes">Optional attributes for <code>&lt;img&gt;</code> tags:</label>
<div style="float:left;"><input type="text" id="image_attributes" name="image_attributes"  style="width:200px;" <?php if ($randomimage_options["image_attributes"]) print "value='" . stripslashes(htmlspecialchars($randomimage_options["image_attributes"], ENT_QUOTES)) . "'"; ?>/> e.g. <code>style="width:200px;"</code></div>
</div>

<div style="clear: both;padding-top:10px;">
<label style="float:left;width:250px;text-align:right;padding-right:6px;padding-top:7px;">Limit by categories/tags:<br />(leave unchecked for all)</label>
<div style="float:left;">

<div style='overflow:auto;height:6em;width:200px;background-color:#efefef;border:1px solid #b2b2b2;padding:2px 0 0 3px;'>
<?php
    
    
    // create WordPress-style category multi-select list
    global $wpdb, $wp_version;

    if ($wp_version >= '2.3')
    {
        $categories = $wpdb->get_results("SELECT $wpdb->terms.term_id as cat_ID, $wpdb->terms.name as cat_name
                                          FROM $wpdb->terms LEFT JOIN $wpdb->term_taxonomy on $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
                                          WHERE $wpdb->term_taxonomy.taxonomy IN ('post_tag', 'category')
                                          ORDER BY $wpdb->terms.name");
    }
    else 
    { 
        $categories = $wpdb->get_results("SELECT cat_ID, cat_name
                                          FROM $wpdb->categories
                                          ORDER BY cat_name");
    }

    foreach ($categories as $category) {
        print "<label style='display:block;' for='category-$category->cat_ID'><input type='checkbox' value='$category->cat_ID' name='category_filter[]' id='category-$category->cat_ID'" . (in_array( $category->cat_ID, $randomimage_options["category_filter"] ) ? ' checked="checked"' : "") . " />" .  wp_specialchars($category->cat_name) . "</label>\n";
    }
?>
</div>

</div>
</div>

<div style="clear: both;padding-top:10px;">
<label style="float:left;width:250px;text-align:right;padding-right:6px;padding-top:7px;" for="image_class_match">String to match in the <code>&lt;img&gt;</code> <code>class</code>:</label>
<div style="float:left;"><input type="text" id="image_class_match" name="image_class_match" style="width:200px;" <?php if ($randomimage_options["image_class_match"]) print "value='" . stripslashes(htmlspecialchars($randomimage_options["image_class_match"], ENT_QUOTES)) . "'"; ?>/> e.g. <code>randomimage</code></div>
</div>


<div style="clear: both;padding-top:10px;">
<label style="float:left;width:250px;text-align:right;padding-right:6px;padding-top:7px;" for="image_src_regex">Regex to match against the <code>&lt;img&gt;</code> <code>src</code>:</label>
<div style="float:left;"><input type="text" id="image_src_regex" name="image_src_regex" style="width:200px;" <?php if ($randomimage_options["image_src_regex"]) print "value='" . stripslashes(htmlspecialchars($randomimage_options["image_src_regex"], ENT_QUOTES)) . "'"; ?>/> e.g. <code>images</code></div>
</div>

<div style="clear: both;padding-top:10px;text-align:center;">
<p class="submit"><input type="submit" name="submit" value="Update Options &raquo;" /></p>
</div>
</form>
</div>


<div class="wrap">
<h2>Sample Random Image</h2>
<?php randomimage(); ?>
</div>




<?php
}



function randomimage($show_post_title      = true, 
                     $number_of_images     = 1, 
                     $image_attributes     = "", 
                     $show_alt_caption     = true, 
                     $image_src_regex      = "",
                     $post_type            = "posts",
                     $inter_image_html     = "<br /><br />",
                     $category_filter      = "",
                     $sort_images_randomly = true,
                     $image_class_match    = "",
                     $image_template_html  = "")
{
    // get access to wordpress' database object
    global $wpdb, $wp_version;
    $debugging = false;

    if ($debugging) print "<strong>Random Image Debugging is On!</strong><br/>";

    // if no arguments are specified
    // assume we're going with the configuration options
    if (!func_get_args())
    {
        if ($debugging) print "Configuration options (specified via admin interface):<br />";

        $randomimage_options = get_randomimage_options();

        $number_of_images     = $randomimage_options['number_of_images'];        
        $image_attributes     = $randomimage_options['image_attributes'];        
        $image_src_regex      = $randomimage_options['image_src_regex'];        
        $inter_image_html     = $randomimage_options['inter_image_html'];
        $sort_images_randomly = $randomimage_options['sort_images_randomly'];
        $image_class_match    = $randomimage_options['image_class_match'];
        $image_template_html  = $randomimage_options['image_template_html'];

        if (!is_array($randomimage_options['category_filter']))
        {
            $randomimage_options['category_filter'] = array();
        }

        // convert category filter array into a comma-separated list
        $category_filter  = implode(",", $randomimage_options['category_filter']);

        if ($randomimage_options['show_images_in_posts'] == true && $randomimage_options['show_images_in_pages'] == false)
        {
            $post_type = "posts";
        }
        elseif ($randomimage_options['show_images_in_posts'] == false && $randomimage_options['show_images_in_pages'] == true)
        {
            $post_type = "pages";
        }
        else
        {
            $post_type = "both";
        }

    } 
    else 
    {
        if ($debugging) print "Configuration options (specified via function parameters):<br />";
        
        // if config options were specified via a function call, but no template was supplied,
        // build a template from the show_post_title and show_alt_caption options.
        if ($image_template_html == '') 
        {
            if ($show_post_title) 
            {
                $image_template_html .= "\n<strong>%1</strong><br />\n";
            }

            $image_template_html .= "%2";

            if ($show_alt_caption) 
            {
                $image_template_html .= "<br />\n<em>%3</em>\n";
            }
        }
    }
    
    if ($debugging) 
    {
        print "show_post_title: "      . htmlspecialchars($show_post_title)      . "<br/>";     
        print "number_of_images: "     . htmlspecialchars($number_of_images)     . "<br/>";    
        print "image_attributes: "     . htmlspecialchars($image_attributes)     . "<br/>";    
        print "show_alt_caption: "     . htmlspecialchars($show_alt_caption)     . "<br/>";    
        print "image_src_regex:  "     . htmlspecialchars($image_src_regex)      . "<br/>";     
        print "post_type: "            . htmlspecialchars($post_type)            . "<br/>";           
        print "inter_image_html: "     . htmlspecialchars($inter_image_html)     . "<br/>";    
        print "category_filter: "      . htmlspecialchars($category_filter)      . "<br/>";     
        print "sort_images_randomly: " . htmlspecialchars($sort_images_randomly) . "<br/><br/>";
    }

    // select the post_type sql for both post pages (post_status = 'static') 
    // and posts (AND post_status = 'publish')
    // or for just pages or for just posts (the default)
    // by adding this where criteria, we also solve the problem
    // of accidentally including images from draft posts.
    if ($wp_version < '2.1') 
    {
        if ($post_type == "both")
        {
            $post_type_sql = "AND (post_status = 'publish' OR post_status = 'static')";
        }
        else if ($post_type == "pages")
        {
            $post_type_sql = "AND post_status = 'static'";
        }
        else
        {
            $post_type_sql = "AND post_status = 'publish'";
        }
    } 
    else 
    {
        if ($post_type == 'both')
        {
            $post_type_sql = "AND post_status = 'publish' AND post_type in ('post', 'page')";
        }
        elseif ($post_type == 'pages')
        {
            $post_type_sql = "AND post_status = 'publish' AND post_type = 'page'";
        }
        else
        {
            $post_type_sql = "AND post_status = 'publish' AND post_type = 'post'";
        }
    }
    // assuming $category_filter is a comma separated list of category ids,
    // modify query to join with post2cat table to select from only the chosen categories
    $category_filter_join  = "";
    $category_filter_sql   = "";
    $category_filter_group = "";
    if ($category_filter != "")
    {
        if ($wp_version >= '2.3')
        {
            $category_filter_join  = "LEFT JOIN $wpdb->term_relationships ON $wpdb->posts.ID = $wpdb->term_relationships.object_id LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id";
            $category_filter_sql   = "AND $wpdb->term_taxonomy.term_id IN ($category_filter)";   
            $category_filter_group = "GROUP BY $wpdb->posts.ID";
        }
        else
        {
            $category_filter_join  = "LEFT JOIN $wpdb->post2cat ON $wpdb->posts.ID = $wpdb->post2cat.post_id";
            $category_filter_sql   = "AND $wpdb->post2cat.category_id IN ($category_filter)";   
            $category_filter_group = "GROUP BY $wpdb->posts.ID";
        }
    }
    
    // by default we sort images randomly,
    // but we can also sort them in descending date order
    if ($sort_images_randomly)
    {
        $order_by_sql = "rand()";
    }
    else
    {
        $order_by_sql = "$wpdb->posts.post_date DESC";
    }

    // query records that contain img tags, ordered randomly
    // do not select images from password protected posts
    $sql = "SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.post_content
            FROM $wpdb->posts
            $category_filter_join
            WHERE post_content LIKE '%<img%'
            AND post_password = ''
            $post_type_sql
            $category_filter_sql
            $category_filter_group
            ORDER BY $order_by_sql";
    $resultset = @mysql_query($sql, $wpdb->dbh);
    
    if ($debugging && mysql_error($wpdb->dbh)) print "mysql errors: " . mysql_error($wpdb->dbh) . "<br/> SQL: " . htmlspecialchars($sql) . "<br/>";;
    if ($debugging) print "elligible post count: " . @mysql_num_rows($resultset) . "<br/>"; 
    
    // keep track of multiple images to prevent displaying dups
    $image_srcs = array();

    // loop through each applicable post from the database
    $image_count = 0;
    while ($row = mysql_fetch_array($resultset))
    {
        $post_title     = $row['post_title'];
        $post_permalink = get_permalink($row['ID']);
        $post_content   = $row['post_content'];

        // find all img tags
        preg_match_all("/<img[^>]+>/i", $post_content, $matches);

        // if there are none, try again, 
        if (count($matches[0]) == 0)
        {
            continue;
        }

        // randomize the array of images in this post
        shuffle($matches[0]);

        // loop through each image candidate for this post and try to find a winner        
        foreach ($matches[0] as $image_element)
        {
            // grab the src attribute and see if it exists, if not try again
            preg_match("/src\s*=\s*(\"|')(.*?)\\1/i", $image_element, $image_src);
            $image_src = $image_src[2];
            
            // make sure we haven't displayed this image before
            if ($image_src == "" || in_array($image_src, $image_srcs))
            {
                continue;
            }

            // if a regex is supplied and it doesn't match, try next post
            if ($image_src_regex != "" && !preg_match("/" . $image_src_regex . "/i", $image_src))
            {
                continue;
            }

            if ($image_class_match != "") 
            {
                // grab the class attribute and see if it exists, if not try again
                preg_match("/class\s*=\s*(\"|')(.*?)\\1/i", $image_element, $image_classes);
                $image_classes = $image_classes[2];

                if ($image_classes == '') 
                {
                    continue;
                }

                $image_classes = explode(" ", $image_classes);
                if (!in_array($image_class_match, $image_classes)) 
                {
                    continue;
                }
            }

            // add img src to array to check for dups
            $image_srcs[] = $image_src;
               
            // grab the alt attribute and see if it exists, if not supply default
            preg_match("/alt\s*=\s*(\"|')(.*?)\\1/i", $image_element, $image_alt);
            $image_alt = $image_alt[2];

            if ($image_alt == "")
            {
                $image_alt = "random image";
            }

            $image_html = $image_template_html;
            $image_html = str_replace("%1", $post_title, $image_html);
            $image_html = str_replace("%2", "<a href='$post_permalink'><img src='$image_src' alt='$image_alt' $image_attributes /></a>", $image_html);
            
            if ($image_alt == 'random image')
            {
                $image_html = str_replace("%3", '', $image_html);
            } 
            else 
            {
                $image_html = str_replace("%3", $image_alt, $image_html);
            }

            print $image_html;

            $image_count++;
            
            if ($image_count == $number_of_images)
            {
                return;
            }
            else
            {
                // print a linebreak between each successive image
                print "$inter_image_html\n";
            }
            
            // leave the foreach loop and look for images
            // in other posts
            // TODO: if people wanted to display multiple images per post,
            // we would selectively skip this break
            break;
        }
    }
}
?>
