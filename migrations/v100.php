<?php
namespace eunaumtenhoid\groupjoinactions\migrations;

class v100 extends \phpbb\db\migration\migration
{
	/**
	 * Checks if new columns already exist to avoid re-installation errors
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_join_pm_subject');
	}

	/**
	 * Defines the minimum phpBB version required
	 */
	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v310\dev'];
	}

	/**
	 * Adds standardized columns and new PM fields
	 */
	public function update_schema()
	{
		return [
			'add_columns' => [
				$this->table_prefix . 'groups' => [
					'group_join_add_points'  => ['UINT', 0],
					'group_join_add_invites' => ['UINT', 0],
					'group_join_sender_id'   => ['UINT', 2], // Default ID 2 (Usually the Admin)
					'group_join_pm_subject'  => ['VCHAR:255', ''],
					'group_join_pm_message'  => ['MTEXT_UNI', ''], // Supports BBCodes and long texts
				],
			],
		];
	}

	/**
	 * Removes columns if the extension is deleted via ACP
	 */
	public function revert_schema()
	{
		return [
			'drop_columns' => [
				$this->table_prefix . 'groups' => [
					'group_join_add_points',
					'group_join_add_invites',
					'group_join_sender_id',
					'group_join_pm_subject',
					'group_join_pm_message',
				],
			],
		];
	}
}