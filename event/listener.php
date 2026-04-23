<?php
namespace eunaumtenhoid\groupjoinactions\event;

if (!defined('IN_PHPBB'))
{
	exit;
}

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $template;
	protected $user;
	protected $request;
	protected $db;
	protected $table_prefix;
	protected $phpbb_log;
	protected $ext_manager;
	protected $root_path;
	protected $php_ext;
	protected $cache;
	protected $user_to_group = [];
	protected $group_data = null;

	public function __construct(
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\request\request $request,
		\phpbb\cache\driver\driver_interface $cache,
		\phpbb\db\driver\driver_interface $db,
		$table_prefix,
		\phpbb\log\log $phpbb_log,
		\phpbb\extension\manager $ext_manager,
		$root_path,
		$php_ext
	) {
		$this->template = $template;
		$this->user = $user;
		$this->request = $request;
		$this->cache = $cache;
		$this->db = $db;
		$this->table_prefix = $table_prefix;
		$this->phpbb_log = $phpbb_log;
		$this->ext_manager = $ext_manager;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'                       => 'load_language_globally',
			'core.group_add_user_after'             => 'on_group_add_user',
			'core.acp_manage_group_display_form'    => 'display_form',
			'core.acp_manage_group_initialise_data' => 'add_group_data',
			'core.viewtopic_post_rowset_data'       => 'on_viewtopic_post_rowset_data',
			'core.viewtopic_modify_post_row'        => 'on_viewtopic_modify_post_row',
			'core.modify_username_string'           => 'on_modify_username_string',
			'core.user_loader_modify_data'          => 'on_user_loader_modify_data',
		);
	}

	public function load_language_globally($event)
	{
		$this->user->add_lang_ext('eunaumtenhoid/groupjoinactions', 'info_acp_actiongroup');
	}

	public function add_group_data($event)
	{
		$action = $event['action'];
		if (in_array($action, array('add', 'edit')))
		{
			$submit_ary = $event['submit_ary'];
			$test_variables = $event['test_variables'];

			$test_variables['join_add_points'] = 'int';
			$test_variables['join_add_invites'] = 'int';
			$test_variables['join_add_uploaded'] = 'int';
			$test_variables['group_join_add_item'] = 'int';
			$test_variables['join_sender_id'] = 'int';
			$test_variables['join_pm_message'] = 'string';
			$test_variables['post_color'] = 'int';
			$test_variables['icon'] = 'string';

			$submit_ary['join_add_points'] = $this->request->variable('action_points', 0);
			$submit_ary['join_add_invites'] = $this->request->variable('action_invites', 0);
			$submit_ary['join_add_uploaded'] = $this->request->variable('action_upload', 0);
			$submit_ary['group_join_add_item'] = $this->request->variable('action_item', 0);
			$submit_ary['join_sender_id'] = $this->request->variable('action_sender', 2);
			$submit_ary['join_pm_subject'] = $this->request->variable('action_pm_subject', '', true);
			$submit_ary['join_pm_message'] = $this->request->variable('action_pm_message', '', true);
			$submit_ary['post_color'] = $this->request->variable('action_post_color', 0);
			$submit_ary['icon'] = $this->request->variable('action_group_icon', '');

			$event['submit_ary'] = $submit_ary;
			$event['test_variables'] = $test_variables;

			// Clear unified group data cache (v2) when any group is edited
			$this->cache->destroy('eunaumtenhoid_group_data_v2');
		}
	}

public function display_form($event)
{
    $group_row = $event['group_row'];
    $sender_id = isset($group_row['group_join_sender_id']) ? (int) $group_row['group_join_sender_id'] : 2;
    
    // Busca o nome e a cor do usuário
    $sql = 'SELECT username, user_colour FROM ' . USERS_TABLE . ' WHERE user_id = ' . $sender_id;
    $result = $this->db->sql_query($sql);
    $row = $this->db->sql_fetchrow($result);
    $this->db->sql_freeresult($result);

    $this->template->assign_vars(array(
        'S_HAS_POINTS_EXT' => ($this->ext_manager->is_enabled('dmzx/ultimatepoints') || $this->ext_manager->is_enabled('phpbbstudio/aps')),
        'S_HAS_INVITE_EXT' => $this->ext_manager->is_enabled('leinad4mind/invitation'),
        'S_HAS_XBT_EXT'    => $this->ext_manager->is_enabled('ppk/xbtbb3cker'),
        'S_HAS_SHOP_EXT'   => $this->ext_manager->is_enabled('eunaumtenhoid/ultimateshop'),

        'ACTION_POINTS'      => isset($group_row['group_join_add_points']) ? $group_row['group_join_add_points'] : 0,
        'ACTION_INVITES'     => isset($group_row['group_join_add_invites']) ? $group_row['group_join_add_invites'] : 0,
        'ACTION_UPLOAD'      => isset($group_row['group_join_add_uploaded']) ? $group_row['group_join_add_uploaded'] : 0,
        'ACTION_SENDER'      => $sender_id,
        'ACTION_SENDER_NAME' => ($row) ? $row['username'] : '',
        'ACTION_SENDER_COL'  => ($row) ? $row['user_colour'] : '',
        'U_ACTION_SENDER'    => ($row) ? append_sid($this->root_path . 'memberlist.' . $this->php_ext, 'mode=viewprofile&u=' . $sender_id) : '',
        'ACTION_PM_SUBJECT'  => isset($group_row['group_join_pm_subject']) ? $group_row['group_join_pm_subject'] : '',
        'ACTION_PM_MESSAGE'  => isset($group_row['group_join_pm_message']) ? $group_row['group_join_pm_message'] : '',
        'ACTION_POST_COLOR'  => isset($group_row['group_post_color']) ? $group_row['group_post_color'] : 0,
        'ACTION_GROUP_ICON'  => isset($group_row['group_icon']) ? $group_row['group_icon'] : '',
        'ACTION_ITEM'        => isset($group_row['group_join_add_item']) ? $group_row['group_join_add_item'] : 0,
        'ICONS_PATH'         => $this->ext_manager->get_extension_path('eunaumtenhoid/groupjoinactions', true) . 'images/',
    ));

    // List shop items if extension enabled
    if ($this->ext_manager->is_enabled('eunaumtenhoid/ultimateshop'))
    {
        $current_item = isset($group_row['group_join_add_item']) ? (int) $group_row['group_join_add_item'] : 0;
        $sql = 'SELECT item, name FROM ' . $this->table_prefix . 'ultimateshop_items 
                WHERE item_active = 1 
                ORDER BY name ASC';
        $result = $this->db->sql_query($sql);
        $item_options = '';
        while ($row_item = $this->db->sql_fetchrow($result))
        {
            $selected = ($row_item['item'] == $current_item) ? ' selected="selected"' : '';
            $item_options .= '<option value="' . $row_item['item'] . '"' . $selected . '>' . $row_item['name'] . '</option>';
        }
        $this->db->sql_freeresult($result);
        $this->template->assign_var('S_SHOP_ITEM_OPTIONS', $item_options);
    }

    // List available icons
    $icons_dir = $this->root_path . 'ext/eunaumtenhoid/groupjoinactions/images/';
    $icon_options = '';
    $current_icon = isset($group_row['group_icon']) ? $group_row['group_icon'] : '';

    if (is_dir($icons_dir))
    {
        $icons = array_diff(scandir($icons_dir), array('..', '.'));
        foreach ($icons as $icon)
        {
            if (preg_match('/\\.(png|gif|jpg|jpeg|svg)$/i', $icon))
            {
                $selected = ($icon == $current_icon) ? ' selected="selected"' : '';
                $icon_options .= '<option value="' . $icon . '"' . $selected . '>' . $icon . '</option>';
            }
        }
    }
    
    $this->template->assign_vars(array(
        'S_ICON_OPTIONS' => $icon_options,
        'GROUP_ICON_WEB_PATH' => $this->root_path . 'ext/eunaumtenhoid/groupjoinactions/images/',
    ));
}

	public function on_group_add_user($event)
	{
		$user_id_ary = $event['user_id_ary'];
		$pending = $event['pending'];
		$group_id = (int) $event['group_id'];

		if (!$user_id_ary || $pending)
		{
			return;
		}

		$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_icon, g.group_post_color, 
				g.group_join_add_points, g.group_join_add_invites, g.group_join_add_uploaded, g.group_join_add_item, g.group_join_sender_id,
				g.group_join_pm_subject, g.group_join_pm_message
				FROM ' . GROUPS_TABLE . ' g
				WHERE g.group_id = ' . (int) $group_id;
		$result = $this->db->sql_query($sql);
		$group_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($group_data)
		{
			$raw_group_name = $group_data['group_name'];
			$group_name = (isset($this->user->lang['G_' . $raw_group_name])) ? $this->user->lang['G_' . $raw_group_name] : $this->user->lang($raw_group_name);
			$sender_id = (int) $group_data['group_join_sender_id'];

			if ($group_data['group_join_add_points'] > 0 && ($this->ext_manager->is_enabled('dmzx/ultimatepoints') || $this->ext_manager->is_enabled('phpbbstudio/aps')))
			{
				$this->process_points($user_id_ary, $group_data, $group_name, $sender_id);
			}

			if ($group_data['group_join_add_invites'] > 0 && $this->ext_manager->is_enabled('leinad4mind/invitation'))
			{
				$invite_col = $this->detect_column('users', ['user_invite', 'user_invites']);
				if ($invite_col)
				{
					$this->db->sql_query("UPDATE " . USERS_TABLE . " SET $invite_col = $invite_col + " . (int) $group_data['group_join_add_invites'] . " WHERE " . $this->db->sql_in_set('user_id', $user_id_ary));
				}
			}


			// Process individual user actions (Upload and Unified Logging)
			foreach ($user_id_ary as $u_id)
			{
				$log_msg = $this->user->lang('LOG_GROUP_JOIN_BENEFITS', $group_name);
				$has_benefits = false;

				// 1. Points Log Entry
				if (!empty($group_data['group_join_add_points']) && $group_data['group_join_add_points'] > 0)
				{
					$log_msg .= $this->user->lang('LOG_ROW_POINTS', (string) $group_data['group_join_add_points']);
					$has_benefits = true;
				}

				// 2. Invites Log Entry
				if (!empty($group_data['group_join_add_invites']) && $group_data['group_join_add_invites'] > 0)
				{
					$log_msg .= $this->user->lang('LOG_ROW_INVITES', (string) $group_data['group_join_add_invites']);
					$has_benefits = true;
				}

				// 3. Shop Item Entry
				if (!empty($group_data['group_join_add_item']) && $group_data['group_join_add_item'] > 0 && $this->ext_manager->is_enabled('eunaumtenhoid/ultimateshop'))
				{
					$item_id = (int) $group_data['group_join_add_item'];
					
					// Get item info
					$sql_item = 'SELECT name FROM ' . $this->table_prefix . 'ultimateshop_items WHERE item = ' . $item_id;
					$result_item = $this->db->sql_query($sql_item);
					$item_row = $this->db->sql_fetchrow($result_item);
					$this->db->sql_freeresult($result_item);

					if ($item_row)
					{
						$item_name = $item_row['name'];
						
						// Add item to user
						$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'ultimateshop_items_user ' . $this->db->sql_build_array('INSERT', [
							'item'           => $item_id,
							'user'           => $u_id,
							'quantity_user'  => 1,
							'purchase_time'  => time(),
							'expiry_time'    => 0,
							'remaining_uses' => 0,
							'purchase_price' => 0.00,
							'is_gift'        => 1,
							'sender_id'      => $sender_id,
						]));

						// Log the gift in Shop
						$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'ultimateshop_logs ' . $this->db->sql_build_array('INSERT', [
							'user_id'   => $sender_id,
							'target_id' => $u_id,
							'item_id'   => $item_id,
							'item_name' => $item_name,
							'log_type'  => 'gift',
							'log_price' => 0.00,
							'log_time'  => time(),
							'log_ip'    => $this->user->ip,
							'log_data'  => 'Group Join Benefit'
						]));

						$log_msg .= $this->user->lang('LOG_ROW_ITEM', $item_name);
						$has_benefits = true;
					}
				}

				// 4. Upload Bonus and Log Entry
				if (!empty($group_data['group_join_add_uploaded']) && $group_data['group_join_add_uploaded'] > 0 && $this->ext_manager->is_enabled('ppk/xbtbb3cker'))
				{
					// DB Update for Upload
					$bonus_bytes = (float) $group_data['group_join_add_uploaded'] * 1024 * 1024 * 1024;
					$bytes_string = number_format($bonus_bytes, 0, '.', '');
					$xbt_users_table = 'xbt_users';

					$sql_xbt = "INSERT INTO $xbt_users_table (uid, uploaded) 
							VALUES (" . (int) $u_id . ", $bytes_string)
							ON DUPLICATE KEY UPDATE uploaded = uploaded + $bytes_string";
					$this->db->sql_query($sql_xbt);

					$log_msg .= $this->user->lang('LOG_ROW_UPLOAD', (string) $group_data['group_join_add_uploaded']);
					$has_benefits = true;
				}

				// Save the unified log if any benefit was granted
				if ($has_benefits)
				{
					$this->phpbb_log->add('user', $u_id, $this->user->ip, 'LOG_GROUP_JOIN_SUCCESS', time(), array(), array($log_msg));
				}
			}

			if ($sender_id > 0)
			{
				$this->send_group_pm($user_id_ary, $group_id, $group_name, $sender_id, $group_data);
			}
		}
	}

	protected function send_group_pm($user_id_ary, $group_id, $group_name, $sender_id, $group_data)
{
    // Se assunto ou mensagem estiverem vazios, não envia nada
    if (empty($group_data['group_join_pm_subject']) || empty($group_data['group_join_pm_message']))
    {
        return;
    }

    if (!function_exists('submit_pm'))
    {
        include_once($this->root_path . 'includes/functions_privmsgs.' . $this->php_ext);
    }

    $subject_tpl = $group_data['group_join_pm_subject'];
    $message_tpl = $group_data['group_join_pm_message'];

    foreach ($user_id_ary as $user_id)
    {
        // Busca nome do usuário
        $sql = 'SELECT username FROM ' . USERS_TABLE . ' WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $username = (string) $this->db->sql_fetchfield('username');
        $this->db->sql_freeresult($result);

        $replace_vars = array(
            '{USERNAME}'   => $username,
            '{USER_ID}'     => $user_id,
            '{GROUP_NAME}' => $group_name,
            '{GROUP_ID}'   => $group_id,
            '{POINTS}'     => $group_data['group_join_add_points'],
            '{INVITES}'    => $group_data['group_join_add_invites'],
        );

        $final_subject = str_replace(array_keys($replace_vars), array_values($replace_vars), $subject_tpl);
        $final_message = str_replace(array_keys($replace_vars), array_values($replace_vars), $message_tpl);

        // --- INÍCIO DO TRATAMENTO DE BBCODE ---
        $bbcode_uid = $bbcode_bitfield = $flags = '';
        generate_text_for_storage($final_message, $bbcode_uid, $bbcode_bitfield, $flags, true, true, true);
        // --- FIM DO TRATAMENTO DE BBCODE ---

        $pm_data = array(
            'from_user_id'      => $sender_id,
            'from_user_ip'      => $this->user->ip,
            'from_username'     => '',
            'enable_sig'        => false,
            'enable_bbcode'     => true,
            'enable_smilies'    => true,
            'enable_urls'       => true,
            'icon_id'           => 0,
            'bbcode_bitfield'   => $bbcode_bitfield, // Agora preenchido
            'bbcode_uid'        => $bbcode_uid,      // Agora preenchido
            'message'           => $final_message,
            'address_list'      => array('u' => array($user_id => 'to')),
        );

        submit_pm('post', $final_subject, $pm_data, false);
    }
}

	protected function process_points($user_id_ary, $group_data, $group_name, $sender_id)
	{
		$points_col = $this->detect_column('users', ['user_points']);
		if (!$points_col) return;

		$points_to_add = (float) $group_data['group_join_add_points'];
		$aps_table_exists = $this->table_exists('aps_logs');
		$ups_table_exists = $this->table_exists('points_log');

		$sender_points_old = 0;
		if ($ups_table_exists)
		{
			$sql_s = "SELECT $points_col FROM " . USERS_TABLE . " WHERE user_id = $sender_id";
			$result_s = $this->db->sql_query($sql_s);
			$sender_points_old = (float) $this->db->sql_fetchfield($points_col);
			$this->db->sql_freeresult($result_s);
		}

		foreach ($user_id_ary as $u_id)
		{
			$sql = "SELECT $points_col FROM " . USERS_TABLE . " WHERE user_id = " . (int) $u_id;
			$result = $this->db->sql_query($sql);
			$old_points = (float) $this->db->sql_fetchfield($points_col);
			$this->db->sql_freeresult($result);


			if ($aps_table_exists)
			{
				$log_args = serialize(array($group_name, (string) $points_to_add));
				$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'aps_logs ' . $this->db->sql_build_array('INSERT', [
					'log_action' => 'LOG_APS_GROUP_JOIN_POINTS', 'log_actions' => $log_args, 'log_time' => time(),
					'user_id' => $u_id, 'reportee_id' => $sender_id, 'points_old' => $old_points, 'points_new' => $old_points + $points_to_add, 'points_sum' => $points_to_add, 'log_approved' => 1
				]));
			}

			if ($ups_table_exists)
			{
				$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'points_log ' . $this->db->sql_build_array('INSERT', [
					'point_send' => $sender_id, 'point_recv' => $u_id, 'point_amount' => $points_to_add, 'point_sendold' => $sender_points_old,
					'point_recvold' => $old_points, 'point_comment' => $this->user->lang('LOG_UPS_GROUP_JOIN_POINTS', $group_name), 'point_type' => 1, 'point_date' => time()
				]));
			}
		}
		$this->db->sql_query("UPDATE " . USERS_TABLE . " SET $points_col = $points_col + $points_to_add WHERE " . $this->db->sql_in_set('user_id', $user_id_ary));
	}

	protected function table_exists($table_name)
	{
		$sql = "SHOW TABLES LIKE '" . $this->table_prefix . $table_name . "'";
		$result = $this->db->sql_query($sql);
		$exists = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		return (bool) $exists;
	}

	protected function detect_column($table, $possible_columns)
	{
		foreach ($possible_columns as $column)
		{
			$sql = "SHOW COLUMNS FROM " . $this->table_prefix . $table . " LIKE '$column'";
			$result = $this->db->sql_query($sql);
			$exists = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if ($exists) { return $column; }
		}
		return false;
	}

	protected function hex2rgb($hex)
	{
		$hex = str_replace("#", "", $hex);
		if (strlen($hex) == 3)
		{
			$r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
			$g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
			$b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
		}
		else
		{
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));
		}
		return "$r, $g, $b";
	}

	protected function get_group_data()
	{
		if ($this->group_data !== null)
		{
			return $this->group_data;
		}

		$key = 'eunaumtenhoid_group_data_v2'; // Forced cache refresh
		$this->group_data = $this->cache->get($key);

		if ($this->group_data === false)
		{
			$sql = 'SELECT group_id, group_colour, group_icon, group_name, group_post_color, group_join_add_uploaded
					FROM ' . GROUPS_TABLE;
			$result = $this->db->sql_query($sql);
			$this->group_data = [
				'by_id' => [],
			];

			while ($row = $this->db->sql_fetchrow($result))
			{
				$data = [
					'id'    => (int) $row['group_id'],
					'icon'  => $row['group_icon'],
					'color' => $row['group_colour'],
					'name'  => (isset($this->user->lang['G_' . $row['group_name']])) ? $this->user->lang['G_' . $row['group_name'] ] : $row['group_name'],
					'border'=> (bool) $row['group_post_color'],
					'upload'=> (int) $row['group_join_add_uploaded']
				];

				// Map ONLY by ID (Guaranteed accuracy)
				$this->group_data['by_id'][$data['id']] = $data;
			}
			$this->db->sql_freeresult($result);
			$this->cache->put($key, $this->group_data, 3600);
		}
		return $this->group_data;
	}

	public function on_user_loader_modify_data($event)
	{
		$data = $event['data'];
		foreach ($data as $user_id => $row)
		{
			if (isset($row['group_id']))
			{
				$this->user_to_group[(int) $user_id] = (int) $row['group_id'];
			}
		}
	}

	public function on_modify_username_string($event)
	{
		// Only inject icon for visual modes
		if (in_array($event['mode'], ['full', 'no_profile', 'username']))
		{
			$user_id = (int) $event['user_id'];
			$all_groups = $this->get_group_data();
			$group_id = 0;

			// 1. Try by Group ID (Memory Cache)
			if (isset($this->user_to_group[$user_id]))
			{
				$group_id = $this->user_to_group[$user_id];
			}
			else if ($user_id > 0)
			{
				// 2. Last Resort: Single DB lookup (Fast primary key query)
				// We store it in memory for the rest of the request
				$sql = 'SELECT group_id FROM ' . USERS_TABLE . ' WHERE user_id = ' . $user_id;
				$result = $this->db->sql_query($sql);
				$group_id = (int) $this->db->sql_fetchfield('group_id');
				$this->db->sql_freeresult($result);
				
				$this->user_to_group[$user_id] = $group_id;
			}

			if ($group_id && isset($all_groups['by_id'][$group_id]))
			{
				$group = $all_groups['by_id'][$group_id];
				
				if ($group['icon'])
				{
					$icon_web_path = $this->root_path . 'ext/eunaumtenhoid/groupjoinactions/images/' . $group['icon'];
					$icon_html = '<img src="' . $icon_web_path . '" alt="' . $group['name'] . '" title="' . $group['name'] . '" style="vertical-align: middle; margin-right: 4px; max-height: 16px;" />';
					$event['username_string'] = $icon_html . $event['username_string'];
				}
			}
		}
	}

	public function on_viewtopic_post_rowset_data($event)
	{
		$row = $event['row'];
		$rowset_data = $event['rowset_data'];

		if (isset($row['group_id']))
		{
			$rowset_data['group_id'] = $row['group_id'];
			// Also store it for our global sniffer
			$this->user_to_group[(int) $row['user_id']] = (int) $row['group_id'];
		}

		$event['rowset_data'] = $rowset_data;
	}

	public function on_viewtopic_modify_post_row($event)
	{
		$row = $event['row'];
		if (!isset($row['group_id']))
		{
			return;
		}

		$post_row = $event['post_row'];
		$group_id = (int) $row['group_id'];

		if ($group_id <= 0)
		{
			return;
		}

		$all_groups = $this->get_group_data();
		$group = $all_groups['by_id'][$group_id] ?? null;

		if ($group && $group['border'])
		{
			$post_row['S_GROUP_POST_COLOR'] = true;
			$color = $group['color'] ?: 'CCCCCC';
			$post_row['GROUP_POST_COLOR'] = $color;
			$post_row['GROUP_POST_COLOR_RGB'] = $this->hex2rgb($color);
		}

		$event['post_row'] = $post_row;
	}
}