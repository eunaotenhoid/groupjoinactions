<?php
namespace eunaumtenhoid\groupjoinactions\migrations;

class v120 extends \phpbb\db\migration\migration
{
	/**
	 * Checks if column already exists
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_join_add_item');
	}

	/**
	 * Depends on version v110
	 */
	public static function depends_on()
	{
		return ['\eunaumtenhoid\groupjoinactions\migrations\v110'];
	}

	/**
	 * Adds column for group entry item
	 */
	public function update_schema()
	{
		return [
			'add_columns' => [
				$this->table_prefix . 'groups' => [
					'group_join_add_item' => ['UINT', 0],
				],
			],
		];
	}

	/**
	 * Removes column if migration is reverted
	 */
	public function revert_schema()
	{
		return [
			'drop_columns' => [
				$this->table_prefix . 'groups' => [
					'group_join_add_item',
				],
			],
		];
	}
}
