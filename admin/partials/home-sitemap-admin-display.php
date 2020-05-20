<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://github.com/evoltix
 * @since 1.0.0
 *
 * @package    Home_Sitemap
 * @subpackage Home_Sitemap/admin/partials
 */
?>

        <h2><?php _e('Click the button below to generate the Home internal link sitemap', $this->plugin_name); ?></h2>        
        <form action="<?php  echo esc_url(admin_url('admin-post.php')); ?>" method="post">            
<input type="hidden" name="action" value="home_sitemap_action">

  
        <?php submit_button('Generate Home Site Map', 'primary', 'submit', true); ?>

</form>


        <h2><?php _e('Click the button below to view the Home internal link sitemap', $this->plugin_name); ?></h2>        
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" >            
<input type="hidden" name="action" value="show_sitemap_admin_action">

       
        <?php submit_button('View Site Map Data', 'secondary', 'submit', true); ?>

</form>

<?php
if(get_transient('hs_tra_sitemap')) {
    ?>
        <h2><?php _e('Once the sitemap is generated, add a custom menu link with the name sitemap.html and publish it on the front end so that visitor can see the sitemap link', $this->plugin_name); ?></h2>    
            
<b><a href="<?php echo get_site_url()?>/sitemap.html" ?>Sitemap</a></b>
    <?php
}

?>

 
 
 
