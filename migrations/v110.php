<?php
namespace eunaumtenhoid\groupjoinactions\migrations;

class v110 extends \phpbb\db\migration\migration
{
	/**
	 * Checks if columns already exist
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_post_color') && 
			   $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_icon') &&
			   $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_join_add_uploaded');
	}

	/**
	 * Depends on version v100
	 */
	public static function depends_on()
	{
		return ['\eunaumtenhoid\groupjoinactions\migrations\v100'];
	}

	/**
	 * Adds columns for colored border, icons and upload per group
	 */
	public function update_schema()
	{
		return [
			'add_columns' => [
				$this->table_prefix . 'groups' => [
					'group_post_color'        => ['TINT:1', 0],
					'group_icon'              => ['VCHAR:255', ''],
					'group_join_add_uploaded' => ['UINT:10', 0],
				],
			],
		];
	}

	/**
	 * Removes columns if migration is reverted
	 */
	public function revert_schema()
	{
		return [
			'drop_columns' => [
				$this->table_prefix . 'groups' => [
					'group_post_color',
					'group_icon',
					'group_join_add_uploaded',
				],
			],
		];
	}
}
