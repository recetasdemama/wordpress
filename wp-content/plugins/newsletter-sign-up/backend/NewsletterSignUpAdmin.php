<?php
if (!class_exists('NewsletterSignUpAdmin')) {

    class NewsletterSignUpAdmin {

        private $hook = 'newsletter-sign-up';
        private $longname = 'Newsletter Sign-Up';
        private $shortname = 'Newsletter Sign-Up';
        private $plugin_url = 'http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/';
        private $filename = 'newsletter-sign-up/newsletter-sign-up.php';
        private $accesslvl = 'manage_options';
        private $icon_url = '';
        private $bp_active = FALSE;
        private $options = array();

       public function __construct(NewsletterSignUp $NSU) {
            $this->options = $NSU->getOptions();
            $this->icon_url = plugins_url('/backend/img/icon.png', dirname(__FILE__));

            add_filter("plugin_action_links_{$this->filename}", array($this, 'add_settings_link'));
            add_action('admin_menu', array($this, 'add_option_page'));
            add_action('admin_init', array($this, 'settings_init'));

            // register function to remove options upon deactivation
            register_deactivation_hook($this->filename, array($this, 'remove_options'));

            add_action( 'admin_enqueue_scripts', array($this, 'load_css_and_js') );
            add_action('bp_include', array($this, 'set_bp_active'));
        }

        public function load_css_and_js($hook)
        {
            if(!stripos($hook, $this->hook)) { return false; }

            wp_enqueue_style($this->hook, plugins_url('/backend/css/backend.css', dirname(__FILE__)));
            wp_enqueue_script(array('jquery', 'dashboard', 'postbox'));
            wp_enqueue_script($this->hook, plugins_url('/backend/js/backend.js', dirname(__FILE__)));           
        }

        /**
         * If buddypress is loaded, set buddypress_active to TRUE
         */
        public function set_bp_active() {
            $this->bp_active = TRUE;
        }

        /**
         * The default settings page
         */
        public function options_page_default() {
            $opts = $this->options['mailinglist'];

            $viewed_mp = NULL;
            if (!empty($_GET['mp']))
                $viewed_mp = $_GET['mp'];
            elseif (empty($_GET['mp']) && isset($opts['provider']))
                $viewed_mp = $opts['provider'];
            if (!in_array($viewed_mp, array('mailchimp', 'icontact', 'aweber', 'phplist', 'ymlp', 'other')))
                $viewed_mp = NULL;

            // Fill in some predefined values if options not set or set for other newsletter service
            if ($opts['provider'] != $viewed_mp) {
                switch ($viewed_mp) {

                    case 'mailchimp':
                        if (empty($opts['email_id']))
                            $opts['email_id'] = 'EMAIL';
                        if (empty($opts['name_id']))
                            $opts['name_id'] = 'NAME';
                        break;

                    case 'ymlp':
                        if (empty($opts['email_id']))
                            $opts['email_id'] = 'YMP0';
                        break;

                    case 'aweber':
                        if (empty($opts['form_action']))
                            $opts['form_action'] = 'http://www.aweber.com/scripts/addlead.pl';
                        if (empty($opts['email_id']))
                            $opts['email_id'] = 'email';
                        if (empty($opts['name_id']))
                            $opts['name_id'] = 'name';
                        break;

                    case 'icontact':
                        if (empty($opts['email_id']))
                            $opts['email_id'] = 'fields_email';
                        break;
                }
            }

            require 'views/dashboard.php';
        }
        
        /**
         * The admin page for managing checkbox settings
         */
        public function options_page_checkbox_settings() {
            $opts = $this->options['checkbox'];
            require 'views/checkbox_settings.php';
        }

        /**
         * The admin page for managing form settings
         */
        public function options_page_form_settings() {
            $opts = $this->options['form'];
            $opts['mailinglist'] = $this->options['mailinglist'];
            require 'views/form_settings.php';
        }

        /**
         * The page for the configuration extractor
         */
        public function options_page_config_helper() {

            if (isset($_POST['form'])) {
                $error = true;

                $form = $_POST['form'];

                // strip unneccessary tags
                $form = strip_tags($form, '<form><input><button>');


                preg_match_all("'<(.*?)>'si", $form, $matches);

                if (is_array($matches) && isset($matches[0])) {
                    $matches = $matches[0];
                    $html = stripslashes(join('', $matches));

                    $clean_form = htmlspecialchars(str_replace(array('><', '<input'), array(">\n<", "\t<input"), $html), ENT_NOQUOTES);

                    $doc = new DOMDocument();
                    $doc->strictErrorChecking = FALSE;
                    $doc->loadHTML($html);
                    $xml = simplexml_import_dom($doc);

                    if ($xml) {
                        $result = true;
                        $form = $xml->body->form;

                        if ($form) {
                            unset($error);
                            $form_action = (isset($form['action'])) ? $form['action'] : 'Can\'t help you on this one..';

                            if ($form->input) {

                                $additional_data = array();

                                /* Loop trough input fields */
                                foreach ($form->input as $input) {

                                    // Check if this is a hidden field
                                    if ($input['type'] == 'hidden') {
                                        $additional_data[] = array($input['name'], $input['value']);
                                        // Check if this is the input field that is supposed to hold the EMAIL data
                                    } elseif (stripos($input['id'], 'email') !== FALSE || stripos($input['name'], 'email') !== FALSE) {
                                        $email_identifier = $input['name'];

                                        // Check if this is the input field that is supposed to hold the NAME data
                                    } elseif (stripos($input['id'], 'name') !== FALSE || stripos($input['name'], 'name') !== FALSE) {
                                        $name_identifier = $input['name'];
                                    }
                                }
                            }
                        }



                        // Correct value's
                        if (!isset($email_identifier))
                            $email_identifier = 'Can\'t help you on this one..';
                        if (!isset($name_identifier))
                            $name_identifier = 'Can\'t help you on this one. Not using name data?';
                    }
                }
            }

            require 'views/config_helper.php';
        }

        /**
         * Renders a donate box
         */
        public function donate_box() {
            $content = '
            <p>I spent countless hours developing this plugin for <b>FREE</b>. If you like it, consider donating a token of your appreciation.</p>
					
			<form class="donate" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_donations">
                <input type="hidden" name="business" value="AP87UHXWPNBBU">
                <input type="hidden" name="lc" value="US">
                <input type="hidden" name="item_name" value="Danny van Kooten">
                <input type="hidden" name="item_number" value="Newsletter Sign-Up">
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
            </form>

			<p>Alternatively, you can: </p>
            <ul>
                <li><a href="http://wordpress.org/support/view/plugin-reviews/newsletter-sign-up?rate=5#postform" target="_blank">Give a 5&#9733; rating on WordPress.org</a></li>
                <li><a href="http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/" target="_blank">Blog about it and link to the plugin page</a></li>
                <li><a href="http://twitter.com/?status=I%20manage%20my%20%23WordPress%20sign-up%20forms%20using%20%40DannyvanKooten%20%27s%20Newsletter%20Sign-Up%20plugin%20and%20I%20love%20it%20-%20check%20it%20out!%20http%3A%2F%2Fwordpress.org%2Fplugins%2Fnewsletter-sign-up%2F" target="_blank">Tweet about Newsletter Sign-Up</a></li>
            </ul>';
            $this->postbox($this->hook . '-donatebox', 'Donate $10, $20 or $50!', $content);
        }

        /**
         * Renders a box with the latests posts from DannyvanKooten.com
         */
        public function latest_posts() {
            require_once(ABSPATH . WPINC . '/rss.php');
            if ($rss = fetch_rss('http://feeds.feedburner.com/dannyvankooten')) {
                $content = '<ul>';
                $rss->items = array_slice($rss->items, 0, 5);

                foreach ((array) $rss->items as $item) {
                    $content .= '<li class="dvk-rss-item">';
                    $content .= '<a target="_blank" href="' . clean_url($item['link'], $protocolls = null, 'display') . '">' . $item['title'] . '</a> ';
                    $content .= '</li>';
                }
                $content .= '<li class="dvk-rss"><a href="http://dannyvankooten.com/feed/">Subscribe to my RSS feed</a></li>';
                $content .= '<li class="dvk-email"><a href="http://dannyvankooten.com/newsletter/">Subscribe by email</a></li>';
                $content .= '<li class="dvk-twitter">You should follow me on twitter <a href="http://twitter.com/dannyvankooten">here</a></li>';
                $content .= '</ul><br style="clear:both;" />';
            } else {
                $content = '<p>No updates..</p>';
            }
            $this->postbox($this->hook . '-latestpostbox', 'Latest blog posts..', $content);
        }


        /**
         * Renders a box with a link to the support forums for NSU
         */
        public function support_box() {
            $content = '<p>Are you having trouble setting-up ' . $this->shortname . ', experiencing an error or got a great idea on how to improve it?</p><p>Please, post
				your question or tip in the <a target="_blank" href="http://wordpress.org/tags/' . $this->hook . '">support forums</a> on WordPress.org. This is so that others can benefit from this too.</p>';
            $this->postbox($this->hook . '-support-box', "Looking for support?", $content);
        }

        /**
         * Output's the necessary HTML formatting for a postbox
         * 
         * @param string $id
         * @param string $title
         * @param string $content 
         */
        public function postbox($id, $title, $content) {
            ?>
            <div id="<?php echo $id; ?>" class="dvk-box">		
               
                <h3 class="hndle"><?php echo $title; ?></h3>
                <div class="inside">
                    <?php echo $content; ?>			
                </div>
            </div>
            <?php
        }

        /**
         * Adds the different menu pages
         */
        public function add_option_page() {
            add_menu_page($this->longname, "Newsl. Sign-up", $this->accesslvl, $this->hook, array($this, 'options_page_default'), $this->icon_url);
            add_submenu_page($this->hook, "Newsletter Sign-Up :: Mailinglist Settings", "List Settings", $this->accesslvl, $this->hook, array($this, 'options_page_default'));
            add_submenu_page($this->hook, "Newsletter Sign-Up :: Checkbox Settings", "Checkbox Settings", $this->accesslvl, $this->hook . '/checkbox-settings', array($this, 'options_page_checkbox_settings'));
            add_submenu_page($this->hook, "Newsletter Sign-Up :: Form Settings", "Form Settings", $this->accesslvl, $this->hook . '/form-settings', array($this, 'options_page_form_settings'));
            add_submenu_page($this->hook, "Newsletter Sign-Up :: Configuration Extractor", "Config Extractor", $this->accesslvl, $this->hook . '/config-helper', array($this, 'options_page_config_helper'));
        }

        /**
         * Adds the settings link on the plugin's overview page
         * @param array $links Array containing all the settings links for the various plugins.
         * @return array The new array containing all the settings links
         */
        public function add_settings_link($links) {
            $settings_link = '<a href="admin.php?page=' . $this->hook . '">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        /**
         * Registers the settings using WP Settings API.
         */
        public function settings_init() {
            register_setting('nsu_form_group', 'nsu_form', array($this, 'validate_form_options'));
            register_setting('nsu_mailinglist_group', 'nsu_mailinglist', array($this, 'validate_mailinglist_options'));
            register_setting('nsu_checkbox_group', 'nsu_checkbox', array($this, 'validate_checkbox_options'));
        }

        /**
         * Removes the options from database, this function is hooked to deactivation of NSU.
         */
       public function remove_options() {
            delete_option('nsu_form');
            delete_option('nsu_checkbox');
            delete_option('nsu_mailinglist');
        }

        /**
         * Validate the submitted options
         * @param array $options The submitted options
         */
        public function validate_options($options) {
            return $options;
        }

        public function validate_form_options($options) {
            $options['text_after_signup'] = strip_tags($options['text_after_signup'], '<a><b><strong><i><img><em><br><p><ul><li><ol>');
            
            // redirect to url should start with http
            if(isset($options['redirect_to']) && substr($options['redirect_to'],0,4) != 'http') {
                $options['redirect_to'] = '';
            }
            
            return $options;
        }

        public function validate_mailinglist_options($options) {
            if (is_array($options['extra_data'])) {
                foreach ($options['extra_data'] as $key => $value) {
                    if (empty($value['name']))
                        unset($options['extra_data'][$key]);
                }
            }

            return $options;
        }

        public function validate_checkbox_options($options) {
            return $options;
        }

    }

}