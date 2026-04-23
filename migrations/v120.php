<?php
namespace eunaumtenhoid\groupjoinactions\migrations;

class v120 extends \phpbb\db\migration\migration
{
	/**
	 * Verifica se a coluna já existe
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_join_add_item');
	}

	/**
	 * Depende da versão v110
	 */
	public static function depends_on()
	{
		return ['\eunaumtenhoid\groupjoinactions\migrations\v110'];
	}

	/**
	 * Adiciona a coluna para item de entrada no grupo
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
	 * Remove a coluna caso a migração seja revertida
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
