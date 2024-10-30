<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Package_Installer_Page' ) ) :

	/**
	 * Iboxindia_WP_Package_Installer_Page
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Package_Installer_Page {

    /**
     * Instance of Iboxindia_WP_Package_Installer_Page
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP_Package_Installer_Page
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP_Package_Installer_Page.
     *
     * @since 2.3.7
     *
     * @return object Class object.
     */
    public static function get_instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new self();
      }

      return self::$instance;
    }

    /**
     * Constructor.
     *
     * @since 2.3.7
     */
    private function __construct() {
    }

    public function show() {
      $slug = sanitize_key( $_GET['slug'] );
      $nonce = wp_create_nonce( $slug );
    ?>
    <div class="installer">
      <table>
      <tr>
          <td>
            <a class="info-btn ajax-btn" data-action="ibx_wp_get_package_info" data-nonce="<?php echo $nonce; ?>" href="#">Get Information</a>
          </td>
          <td><div class="progressbar">&nbsp;</div></td>
          <td><div class="info"></div></td>
        </tr>
        <tr>
          <td>
          <a class="download-btn ajax-btn" data-action="ibx_wp_download_package" data-nonce="<?php echo $nonce; ?>" href="#">Download</a>
          </td>
          <td><div class="progressbar">&nbsp;</div></td>
          <td><div class="info"></div></td>
        </tr>
        <tr>
          <td>
            <a class="install-btn install-btn" data-action="ibx_wp_install_package" data-nonce="<?php echo $nonce; ?>" href="#">Install</a>
          </td>
          <td><div class="progressbar">&nbsp;</div></td>
          <td><div class="info"></div></td>
        </tr>
      </table>
    </div>

    <script>
      jQuery(document).ready( function() {
        asset = "";
        type = "";
        jQuery(".info-btn").click( function(e) {
          e.preventDefault();
          ele = this;
          jQuery(ele).closest('tr').find('.progressbar').addClass('loading');
          action = jQuery(this).attr("data-action")
          link = "<?php echo admin_url('admin-ajax.php?slug=' . $slug . '&nonce=' . $nonce . '&action='); ?>" + action
          jQuery.ajax({
            type : "post",
            dataType : "json",
            url : link,
            data : {slug: "<?php echo $slug; ?>", nonce: "<?php echo $nonce; ?>"},
            success: function(response) {
              jQuery(ele).closest('tr').find('.progressbar').removeClass('loading');
              type = response.type;
              jQuery(ele).closest('tr').find('.info').text(JSON.stringify(response));
            }
          });
        });
        jQuery(".download-btn, .install-btn").click( function(e) {
          e.preventDefault();
          ele = this;
          jQuery(ele).closest('tr').find('.progressbar').addClass('loading');
          action = jQuery(this).attr("data-action")
          link = "<?php echo admin_url('admin-ajax.php?slug=' . $slug . '&nonce=' . $nonce . '&action='); ?>" + action
          jQuery.ajax({
            type : "post",
            dataType : "json",
            url : link,
            data : {slug: "<?php echo $slug; ?>", nonce: "<?php echo $nonce; ?>"},
            success: function(response) {
              jQuery(ele).closest('tr').find('.progressbar').removeClass('loading');
              console.log(response);
              jQuery(ele).closest('tr').find('.info').text(JSON.stringify(response));
            }
          });
        });
      });
    </script>

    <?php
      $link = "";//admin_url('admin.php?page=iboxindia&tab='.$result['type']);
    ?>
    <a href="<?php echo $link; ?>"> <-- Go Back</a>
    <script>
    // setTimeout(function(){
      // window.location = "";
    // }, 10000);
    </script> <?php
    }
  }
endif;
?>