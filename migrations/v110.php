<?php
namespace eunaumtenhoid\groupjoinactions\migrations;

class v110 extends \phpbb\db\migration\migration
{
	/**
	 * Verifica se as colunas já existem
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_post_color') && 
			   $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_icon') &&
			   $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_join_add_uploaded');
	}

	/**
	 * Depende da versão v100
	 */
	public static function depends_on()
	{
		return ['\eunaumtenhoid\groupjoinactions\migrations\v100'];
	}

	/**
	 * Adiciona as colunas para borda colorida, ícones e upload por grupo
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
	 * Remove as colunas caso a migração seja revertida
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
