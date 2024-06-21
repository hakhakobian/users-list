<?php
class HHUsersList_ADMIN_PAGE {
  private object $obj;
  private $users_per_page = 10;
  private $user_roles;
  public function __construct($that) {
    $this->obj = $that;
    $this->user_roles = wp_roles();;
    $this->add_actions();
  }

  /**
   * Actions.
   */
  private function add_actions(): void {
    add_action( 'admin_menu', array($this, 'admin_menu') );
    // Register an ajax action to save images to the gallery.
    add_action('wp_ajax_hhul_ajax', [ $this, 'ajax' ]);
  }

  /**
   * Add "Users list" menu to the WordPress menu.
   *
   * @return void
   */
  public function admin_menu() {
    add_menu_page($this->obj->nicename, $this->obj->nicename, 'manage_options', $this->obj->prefix . '_admin', array($this, 'admin_page'));
  }

  /**
   * Ajax.
   *
   * @return void
   */
  public function ajax() {
    if ( isset( $_GET[$this->obj->nonce] )
      && wp_verify_nonce( $_GET[$this->obj->nonce]) ) {
      $this->content();
    }

    wp_die();
  }

  /**
   * Admin page.
   *
   * @return void
   */
  public function admin_page() {
    // Get options from DB.
    if ( !current_user_can('manage_options') ) {
      wp_die('You do not have sufficient permissions to access this page.');
    }

    wp_enqueue_style($this->obj->prefix . '_admin');
    wp_enqueue_script($this->obj->prefix . '_admin');

    $ajax_url = admin_url('admin-ajax.php');
    $ajax_url = wp_nonce_url($ajax_url, -1, $this->obj->nonce);
    ?>
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div class="wrap" data-ajax-url="<?php echo esc_url($ajax_url); ?>">
      <?php
      $this->content();
      ?>
    </div>
    <?php
  }

  /**
   * The list content.
   *
   * @return void
   */
  private function content() {
    $orderby_possible_values = [
      'user_login',
      'display_name',
    ];

    $data = [
      'sort' => isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : '',
      'orderby' => isset($_POST['orderby']) && in_array($_POST['orderby'], $orderby_possible_values) ? sanitize_text_field($_POST['orderby']) : '',
      'order' => isset($_POST['order']) && in_array($_POST['order'], ['asc', 'desc']) ? sanitize_text_field($_POST['order']) : 'asc',
      'page' => isset($_POST['page']) ? (int) $_POST['page'] : 1,
    ];

    $args = [
      'role' => $data['sort'],
      'orderby' => $data['orderby'],
      'order' => $data['order'],
      'fields' => [
        'user_login',
        'display_name',
        'user_email',
      ],
    ];
    $users_count = count(get_users($args));

    $args['number'] = $this->users_per_page;
    $args['paged'] = $data['page'];

    $users = get_users($args);
    ?>
    <div id="hhul_container">
      <div class="hhul_sort">
        <label for="hhul_sort"><?php esc_html_e('User roles', 'hhul'); ?>:</label>
        <select id="hhul_sort">
          <option value=""><?php esc_html_e('All', 'hhul'); ?></option>
          <?php
          foreach ( $this->user_roles->roles as $role => $role_name ) {
            ?>
            <option <?php if ($data['sort'] == $role) echo 'selected="selected"'; ?> value="<?php echo esc_attr($role); ?>"><?php echo esc_html($role_name['name']); ?></option>
            <?php
          }
          ?>
        </select>
      </div>
      <div class="hhul_table">
        <div class="hhul_table-row hhul_header">
          <div class="hhul_table-cell hhul_sortable" data-value="name"><?php esc_html_e('Name', 'hhul'); ?><span class="hhul_arrow" id="hhul_name_arrow"></span></div>
          <div class="hhul_table-cell hhul_sortable" data-value="username"><?php esc_html_e('Username', 'hhul'); ?><span class="hhul_arrow" id="hhul_username_arrow"></span></div>
          <div class="hhul_table-cell"><?php esc_html_e('Email', 'hhul'); ?></div>
        </div>
        <?php
        if ( !empty($users) ) {
          foreach ( $users as $user ) {
            ?>
            <div class="hhul_table-row">
              <div class="hhul_table-cell"><?php echo esc_html($user->display_name); ?></div>
              <div class="hhul_table-cell"><?php echo esc_html($user->user_login); ?></div>
              <div class="hhul_table-cell"><?php echo esc_html($user->user_email); ?></div>
            </div>
            <?php
          }
        }
        else {
          ?>
          <div class="hhul_table-row">
            <div class="hhul_table-cell">
              <?php esc_html_e('There are no users.', 'hhul'); ?>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
      <div class="hhul_pagination">
        <?php
        $pages_count = round($users_count / $this->users_per_page);
        for ( $i = 1; $i <= $pages_count && $pages_count > 1; ++$i ) {
          ?>
          <button <?php if ($data['page'] == $i) echo 'class="active"'; ?> data-value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></button>
          <?php
        }
        ?>
      </div>
    </div>
    <?php
  }
}
