<?php
 /**
  * The admin-specific functionality of the plugin.
  * This file is read by WordPress to generate the plugin information in the plugin
  *
  * @category   Admin
  * @package    Home_Sitemap
  * @subpackage Home_Sitemap/admin
  * @author     Avnish Negi <avneng.negi@gmail.com>
  * @link       https://github.com/evoltix
  * @since      1.0.0
  * Description: This is not just a plugin, it symbolizes the hope and enthusiasm 
  * Author URI:        https://github.com/evoltix
  * Version: 1.6
  * License:           GPL-2.0+
  * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
  * Text Domain:       home-sitemap
  * Domain Path:       /languages
  */

  /**
  * Home_Sitemap_Admin Class.
  */
class Home_Sitemap_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since  1.6
     * @access private
     * @var    string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.6
     * @access private
     * @var    string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since  1.6
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since  1.6
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Home_Sitemap_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Home_Sitemap_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/home-sitemap-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since  1.6
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Home_Sitemap_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Home_Sitemap_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/home-sitemap-admin.js', array( 'jquery' ), $this->version, false);

    }
    
    /**
     * Register the administration menu for Home Sitemap plugin into the WordPress Dashboard menu.
     *
     * @since  1.6
     */

    public function admin_menu_home_sitemap()
    {

        /*
        * Add a settings page for Home Sitemap plugin to the Settings menu.
        *
        */
    
        add_options_page('Generate Sitemap', 'Generate Sitemap', 'manage_options', $this->plugin_name, array($this, 'display_home_sitemap_setup_page'));
    
    }
   
    /**
     * Render the settings page for this plugin.
     *
     * @since  1.6
     */
    
    public function display_home_sitemap_setup_page()
    {
		 /*
		 * Display the settings page for this plugin.
		 *
		 */
	   
        include_once 'partials/home-sitemap-admin-display.php';

    }
	
	 /**
     * Generate home page sitemap and store it temporarily into DB and file.
	 * This function will work as an action hook for the action admin_post_home_sitemap_action
     *
     * @since  1.6
     */
    
    public function generate_home_sitemap()
    {
       
	     /*
		 * Extract the Home page content.
		 * Parse the content and find <a> tags
		 * Extract the inernal and external backlinks
		 * Filter the inernal links
		 * find the title of Internal links
		 * Store the sitemap into database and file
		 * Run the schedule task in every 1 hr
		 */
	   
	    $admin_notice="";
        $hs_admin_notice_arg="";
        $hs_response_post="";
        $admin_notice="<ul>";
        $data="<ul>";
        
        /**
        * Get the page's HTML source using file_get_contents 
        */
        $url = get_site_url();

        $parts = parse_url($url);
        $domain=$parts['host'];

        $html = file_get_contents($url);
        
        $htmlDom = new DOMDocument;

        /**
        * Parse the HTML of the page using DOMDocument::loadHTML 
        **/
        @$htmlDom->loadHTML($html);

        /**
        * Extract the links from the HTML. 
        **/
        $links = $htmlDom->getElementsByTagName('a');
        $filtered_internal_links="<ul>";
        $internal_links_array=array();    
 
        /**
        * Loop through the DOMNodeList. 
        * We can do this because the DOMNodeList object is traversable. 
        **/
        foreach($links as $link){
            /**
            * Get the link text. 
            **/
            $linkText = $link->nodeValue;
    
            /**
            * Get the link in the href attribute. 
            **/
            $link_href = $link->getAttribute('href');

            /**
            * if the URL is home page or anchor or / 
            **/
            if($url == $link_href || $url."/" == $link_href || strpos($link_href, "#") || $link_href == "" || $link_href == "/") {
                continue;
            }
    
            /**
            * if the URL is javascript, tel or mailto or # 
            **/
            preg_match('/javascript:|tel:|mailto:|#/', $link_href, $CheckJavascriptLink);
            if($CheckJavascriptLink != null) {
                continue;
            }

            /**
              * Check the URL is internal or external 
            **/
            preg_match('/'.$domain.'/', $link_href, $Check);
            if($Check == null) {

                /**
                  * Check the URL is internal or external 
                **/
                preg_match('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/', $link_href, $check_external_link);
                if($check_external_link == null) {

                    /**
                    * Check the URL contain uid and save it in array
                    **/
                    if (in_array($url."/".$link_href, $internal_links_array)) {
                        continue;
                    }
                    $internal_links_array[url_to_postid($link_href)]=$url."/".$link_href;
                }
            }
            else
            {

                /**
                * Check the URL is inernal url and save it in array 
                **/
                if(in_array($link_href, $internal_links_array)) {
                    continue;
                }
                $internal_links_array[url_to_postid($link_href)]=$link_href;

            }
        }
        $admin_notice.="<li><span class='dashicons dashicons-yes'></span>Home page Sitemap Internal link extracted </li>";


        /**
        * Retrive the title of url and store it as (A tag)
        **/
        foreach($internal_links_array as $uid=>$internal_link){
            preg_match('/sitemap.html/', $link_href, $Check);
            if(strpos($internal_link, "sitemap.html") ) {
                $filtered_internal_links.= "<li><a href=".$internal_link.">Sitemap</a></li>";
            } else {
                $filtered_internal_links.= "<li><a href=".$internal_link.">".esc_html(get_the_title($uid)) ."</a></li>";
            }
        }

        $filtered_internal_links.="</ul>";

        /**
        * Delete the sitemap from the database 
        **/
        if(get_transient('hs_tra_sitemap')) {
            delete_transient('hs_tra_sitemap'); 
            $admin_notice.="<li><span class='dashicons dashicons-yes'></span>Previous stored temporary sitemap data detected and deleted </li>";
        }

        /**
        * Reset the sitemap into database 
        **/
        set_transient('hs_tra_sitemap', $filtered_internal_links);
        $admin_notice.="<li><span class='dashicons dashicons-yes'></span>Sitemap data stored temporarily into database </li>";

        /**
        * Delete the sitemap.html if exist 
        **/
        if (!file_exists('sitemap.html')) {
            $handle = unlink('sitemap.html');
            $admin_notice.="<li><span class='dashicons dashicons-yes'></span>Old sitemap.html file detected and deleted</li>";
        }

        /**
        * Create a new sitemap 
        **/
        $sitemap_html = fopen("sitemap.html", "w") or die("Unable to open file!");
        fwrite($sitemap_html, $filtered_internal_links);
        fclose($sitemap_html);
        $admin_notice.="<li><span class='dashicons dashicons-yes'></span>New Sitemap.html file generated</li>";
    
        /**
        * Unschedule the action (For testing only) 
        **/
        //   $timestamp = wp_next_scheduled('admin_post_home_sitemap_action');
        //   wp_unschedule_event($timestamp, 'admin_post_home_sitemap_action');
    
        /**
        * Schedule the Sitemap action 
        **/
        if (! wp_next_scheduled('admin_post_home_sitemap_action') ) {
            wp_schedule_event(time(), 'sitemap_interval', 'admin_post_home_sitemap_action');
            $admin_notice.="<li><span class='dashicons dashicons-yes'></span>Sitemap will generate in every one hr</li>";
        }
    
        /**
        * Redirect the admin to the Plugin page 
        **/
        $admin_notice.="</ul>";
        $hs_admin_notice_arg = "success";
        set_transient('hs_response_transient', $admin_notice);
        $hs_response_transient='hs_response_transient';
        $this->hs_custom_redirect($hs_admin_notice_arg, $hs_response_post, $hs_response_transient);

    }


    /**
     * Render sitemap to admin 
	 * This function will work as an action hook for the action admin_post_show_sitemap_admin_action
     * @since 1.0.0
     */
    
    public function show_sitemap_admin()
    { 
        /*
        * Success flasg as hs_admin_notice_arg
		* Send transient as an agrgument 
		* Response note  as hs_response_post
        */
        $hs_admin_notice_arg="success";
          $hs_response_transient='hs_tra_sitemap';
        $hs_response_post="Sitemap fetched from Database";

        $this->hs_custom_redirect($hs_admin_notice_arg, $hs_response_post, $hs_response_transient);


    }

    /**
     * Redirect the admin to the Plugin page 
     *
     * @since 1.0.0
	 * @param 		string 			$hs_admin_notice_arg 		Notices.
	 * @param 		string 			$hs_response_post 		    Output data .
	 * @param 		string 			$hs_response_transient 		The name of this transiet.

     */
	public function hs_custom_redirect( $hs_admin_notice_arg, $hs_response_post,$hs_response_transient )
    {
        /*
        * Redirect the admin to plugin page
		* send the args to filter the filter hs_admin_response
        */
		
		wp_redirect(
            esc_url_raw(
                add_query_arg(
                    array(
                                    'hs_admin_notice_arg' => $hs_admin_notice_arg,
                                    'hs_response_post' => $hs_response_post,
                                    'hs_response_transient' => $hs_response_transient,
                                    ),
                    admin_url('admin.php?page='. $this->plugin_name) 
                ) 
            ) 
        );

    }


    /**
     * Set time interval for  wp_schedule_event  
     *
     * @since 1.0.0
	 * @param 		array 			$schedules 		Sitemap interval.
     */
    function hs_add_cron_interval( $schedules )
    { 
        /*
        * Set time interval for  wp_schedule_event
		* It will work as a filter hook for hs_add_cron_interval filter
        */  
		$schedules['sitemap_interval'] = array(
        'interval' => 3,600,
        'display'  => esc_html__('Time Interval'), );
        return $schedules;
    }

    /**
     * Render Notic and output 
     *
     * @since 1.0.0
     */
    public function hs_admin_response()
    { 
        /*
        * Display the  Notic and output in admin page
		* It will work as a action hook for admin_notices action
        */ 
		 if (isset($_REQUEST['hs_admin_notice_arg']) ) {
            if($_REQUEST['hs_admin_notice_arg'] === "success") {
                $html =    "<div class='notice notice-success is-dismissible'>";
                if($_REQUEST['hs_response_post'] != "") {
                    $html.= "<p><strong>".$_REQUEST['hs_response_post']." </strong></p><br>";
                }
                if($_REQUEST['hs_response_transient'] != "") {
                    $html.= "<p><strong>".get_transient($_REQUEST['hs_response_transient'])." </strong></p><br>";
                }
                 $html.= "</div>";
                 echo $html;
                $timestamp = wp_next_scheduled('hs_response_transient');
                  wp_unschedule_event($timestamp, 'hs_response_transient');
            }
        }
    }

}
