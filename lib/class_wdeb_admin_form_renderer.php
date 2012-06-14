<?php
class Wdeb_AdminFormRenderer {

	function _get_option ($key=false, $pfx='wdeb') {
		$opt = WP_NETWORK_ADMIN ? get_site_option($pfx) : get_option($pfx);
		if (!$key) return $opt;
		return @$opt[$key];
	}

	function _create_checkbox ($name, $pfx='wdeb') {
		$opt = $this->_get_option($name, $pfx);
		$value = @$opt[$name];
		return
			"<input type='radio' name='{$pfx}[{$name}]' id='{$name}-yes' value='1' " . ((int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-yes'>" . __('Yes', 'wdeb') . "</label>" .
			'&nbsp;' .
			"<input type='radio' name='{$pfx}[{$name}]' id='{$name}-no' value='0' " . (!(int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-no'>" . __('No', 'wdeb') . "</label>" .
		"";
	}

	function _create_radiobox ($name, $value) {
		$opt = $this->_get_option($name);
		$checked = (@$opt == $value) ? true : false;
		return "<input type='radio' name='wdeb[{$name}]' id='{$name}-{$value}' value='{$value}' " . ($checked ? 'checked="checked" ' : '') . " /> ";
	}

	function create_metaboxes_posts_box () {
		$boxes = array (
			'postexcerpt' => __('Excerpt'),
			'postimagediv' => __('Featured Image'),
			'trackbacksdiv' => __('Send Trackbacks'),
			'postcustom' => __('Custom Fields'),
			'commentstatusdiv' => __('Discussion'),
			'slugdiv' => __('Slug'),
			'authordiv' => __('Author'),
			'formatdiv' => __('Format'),
			'categorydiv' => __('Categories'),
			'tagsdiv-post_tag' => __('Post Tags'),
			'revisionsdiv' => __('Revisions'),
		);
		$opt = $this->_get_option('post_boxes');
		$opt = is_array($opt) ? $opt : array();
		foreach ($boxes as $bid => $label) {
			$checked = in_array($bid, $opt) ? 'checked="checked"' : '';
			echo "<input type='hidden' name='wdeb[post_boxes][{$bid}]' value='0' />" .
				"<input {$checked} type='checkbox' name='wdeb[post_boxes][{$bid}]' value='{$bid}' id='wdeb_post_boxes_{$bid}' /> " .
				"<label for='wdeb_post_boxes_{$bid}'>{$label}</label><br />\n";
		}
		_e(
			'<p><b>Warning:</b> all other boxes will be shown or hidden according to their screen settings</p>',
		'wdeb');
	}

	function create_metaboxes_pages_box () {
		$boxes = array (
			'postcustom' => __('Custom Fields'),
			'postimagediv' => __('Featured Image'),
			'commentstatusdiv' => __('Discussion'),
			'slugdiv' => __('Slug'),
			'authordiv' => __('Author'),
			'pageparentdiv' => __('Page Attributes'),
		);
		$opt = $this->_get_option('page_boxes');
		$opt = is_array($opt) ? $opt : array();
		foreach ($boxes as $bid => $label) {
			$checked = in_array($bid, $opt) ? 'checked="checked"' : '';
			echo "<input type='hidden' name='wdeb[page_boxes][{$bid}]' value='0' />" .
				"<input type='checkbox' {$checked} name='wdeb[page_boxes][{$bid}]' value='{$bid}' id='wdeb_page_boxes_{$bid}' /> " .
				"<label for='wdeb_page_boxes_{$bid}'>{$label}</label><br />\n";
		}
		_e(
			'<p><b>Warning:</b> all other boxes will be shown or hidden according to their screen settings</p>',
		'wdeb');
	}

	function create_admin_bar_box () {
		echo $this->_create_checkbox('admin_bar');
		_e(
			'<p>Show WordPress Admin bar in Easy mode.</p>',
		'wdeb');
	}

	function create_screen_options_box () {
		echo $this->_create_checkbox('screen_options');
		_e(
			'<p>Show contextual help and screen options in Easy mode.</p>',
		'wdeb');
	}

	function create_easy_bar_box () {
		echo $this->_create_checkbox('easy_bar');
		_e(
			'<p>Show persistent top right Easy Bar in Easy mode.</p>',
		'wdeb');
	}

	function create_auto_enter_role_box () {
		global $wp_roles;
		/*
		$_roles = array (
			'administrator' => __('Site Admin'),
			'editor' => __('Editor'),
			'author' => __('Author'),
			'contributor' => __('Contributor'),
			'subscriber' => __('Subscriber'),
		);
		*/
		if (!isset($wp_roles)) $wp_roles = new WP_Roles();
		$_roles = $wp_roles->get_names();
		$roles = $this->_get_option('auto_enter_role');
		$roles = is_array($roles) ? $roles : array();

		foreach ($_roles as $role=>$label) {
			$checked = in_array($role, $roles) ? 'checked="checked"' : '';
			echo '' .
				"<input type='checkbox' name='wdeb[auto_enter_role][{$role}]' id='wdeb-auto_enter_role-{$role}' value='{$role}' {$checked} />" .
				' ' .
				"<label for='wdeb-auto_enter_role-{$role}'>{$label}</label>" .
			"<br />";
		}
		_e('<p>Users with selected roles will be forced to use the easy mode.</p>', 'wdeb');
	}

	function create_plugin_theme_box () {
    $themes_dir =  WDEB_PLUGIN_BASE_DIR . '/themes/';

    if(function_exists( 'scandir' )) {
    $themes = scandir($themes_dir);
           } else {

        $themes = array(
			"default" => __("Default %s", 'wdeb'),
			"stripes_red" => __("Stripes Red %s", 'wdeb'),
            "stripes_orange" => __("Stripes Orange %s", 'wdeb'),
            "stripes_green" => __("Stripes Green %s", 'wdeb')
		);
            }

		foreach ($themes as $theme) {
        if ($theme == '.' || $theme == '..') {

            } else {

			$img = WDEB_PLUGIN_URL . '/themes/' . $theme . '/screenshot.png';
            echo "<label style='overflow: hidden; margin-bottom: 20px; float:left; width: 233px; height: 550px;' for='plugin_theme-{$theme}'>";
			echo $this->_create_radiobox('plugin_theme', $theme) . $theme . '<br />';
            echo "<img src='" . $img . "' />";
            echo "</label>";
            }
		}
	}

	function create_hijack_start_page_box () {
		echo $this->_create_checkbox('hijack_start_page');
		_e(
			'<p>If set, this option will allow new users to choose between Easy and Advanced mode the first time they log in.</p>' .
			'<p>Their choice will be stored and used from that point on, as long as this option is turned on.</p>',
		'wdeb');
	}

	function create_show_logout_box () {
		echo $this->_create_checkbox('show_logout');
	}

	function create_logo_box () {
		$opts = new Wdeb_Options;
		$logo = $opts->get_logo();
		if ($logo) {
			printf (__("Current logo:<br /> %s", 'wdeb'), "<img id='wdeb-logo-logo_output' src='{$logo}' /><br />");
			echo '<a href="#remove-logo" id="wdeb-logo-remove_logo">' . __('Reset logo', 'wdeb') . '</a><br />';
		}
		echo "<input type='hidden' name='wdeb[wdeb_logo]' id='wdeb-logo-custom_logo' value='{$logo}' />";
		_e('Upload your own logo:<br /><em>*suitable logo dimension: width=150px height=80px or more</em><br />', 'wdeb');
		echo " <input type='file' name='wdeb_logo' />";

	}

	function create_dashboard_widget_box () {
		echo
			'<labeld for="show_dashboard_widget-yes">' . __('Show dashboard widget', 'wdeb') . '</label> ',
			$this->_create_checkbox('show_dashboard_widget'),
		'<br />';
		echo
			'<labeld for="widget_title">' . __('Widget title', 'wdeb') . '</label> ',
			'<input type="text" class="widefat" id="widget_title" name="wdeb[widget_title]" value="' .
				stripslashes($this->_get_option('widget_title')) .
			'" />',
		'<br />';
		echo '<label for="widget_contents">' . __('Widget contents', 'wdeb') . '</label><br />';
		echo '<textarea id="widget_contents" class="widefat" rows="8" name="wdeb[widget_contents]">' .
			stripslashes($this->_get_option('widget_contents')) .
		'</textarea>';
	}

	function create_dashboard_right_now_widget_box () {
		echo $this->_create_checkbox('dashboard_right_now');
	}

/*** Tooltips ***/
	function create_show_tooltips_box () {
		echo $this->_create_checkbox('show_tooltips', 'wdeb_help');
	}

/*** Wizard ***/
	function create_wizard_enabled_box () {
		echo $this->_create_checkbox('wizard_enabled', 'wdeb_wizard');
	}

	function create_wizard_steps_box () {
		$opts = new Wdeb_Options;
		$steps = $opts->get_option('wizard_steps', 'wdeb_wizard');
		$steps = is_array($steps) ? $steps : array();

		echo "<ul id='wdeb_steps'>";
		$count = 1;
		foreach ($steps as $step) {
			echo '<li class="wdeb_step">' .
				'<h4>' .
					'<span class="wdeb_step_count">' . $count . '</span>' .
					':&nbsp;' .
					'<span class="wdeb_step_title">' . $step['title'] . '</span>' .
				'</h4>' .
				'<div class="wdeb_step_actions">' .
					'<a href="#" class="wdeb_step_delete">' . __('Delete', 'wdeb') . '</a>' .
					'&nbsp;|&nbsp;' .
					'<a href="#" class="wdeb_step_edit">' . __('Edit', 'wdeb') . '</a>' .
				'</div>' .
				'<input type="hidden" class="wdeb_step_url" name="wdeb_wizard[wizard_steps][' . $count . '][url]" value="' . esc_url($step['url']) . '" />' .
				'<input type="hidden" class="wdeb_step_title" name="wdeb_wizard[wizard_steps][' . $count . '][title]" value="' . htmlspecialchars($step['title'], ENT_QUOTES) . '" />' .
				'<input type="hidden" class="wdeb_step_help" name="wdeb_wizard[wizard_steps][' . $count . '][help]" value="' . htmlspecialchars($step['help'], ENT_QUOTES) . '" />' .
			"</li>\n";
			$count++;
		}
		echo "</ul>";
		if ($opts->get_option('wizard_enabled', 'wdeb_wizard')) {
			_e('<p>Drag and drop steps to sort them in the order you want.</p>', 'wdeb');
		} else {
			_e('<p>Enable the Wizard, then drag and drop steps to sort them in the order you want.</p>', 'wdeb');
		}
	}

	function create_wizard_add_step_box () {
		// URL
		echo '<label for="wdeb_last_wizard_step_url">' . __('URL:', 'wdeb') . '</label><br />';
		echo '<select id="wdeb_last_wizard_step_url_type" name="wdeb_wizard[wizard_steps][_last_][url_type]">';
		echo '<option value="/wp-admin">' . __('Administrative page (e.g. "/post-new.php" or "/themes.php")', 'wdeb') . '&nbsp;</option>';
		echo '<option value="">' . __('Site page (e.g. "/" or "/2007-06-05/an-old-post")', 'wdeb') . '&nbsp;</option>';
		echo '</select> <span id="wdeb_url_preview">Preview: <code></code></span><br />';
		echo "<input type='text' class='widefat' id='wdeb_last_wizard_step_url' name='wdeb_wizard[wizard_steps][_last_][url]' /> <br />";

		// Title
		echo '<label for="wdeb_last_wizard_step_title">' . __('Title:', 'wdeb') . '</label>';
		echo "<input type='text' class='widefat' id='wdeb_last_wizard_step_title' name='wdeb_wizard[wizard_steps][_last_][title]' /> <br />";

		// Help string
		echo '<label for="wdeb_last_wizard_step_help">' . __('Help:', 'wdeb') . '</label>';
		echo "<textarea class='widefat' id='wdeb_last_wizard_step_help' name='wdeb_wizard[wizard_steps][_last_][help]'></textarea> <br />";

		echo "<input type='submit' value='" . __('Add', 'wdeb') . "' />";
	}
}