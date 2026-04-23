<?php
namespace eunaumtenhoid\groupjoinactions\migrations;

class v100 extends \phpbb\db\migration\migration
{
	/**
	 * Verifica se as novas colunas já existem para evitar erros de re-instalação
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_join_pm_subject');
	}

	/**
	 * Define a versão mínima do phpBB necessária
	 */
	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v310\dev'];
	}

	/**
	 * Adiciona as colunas padronizadas e os novos campos de PM
	 */
	public function update_schema()
	{
		return [
			'add_columns' => [
				$this->table_prefix . 'groups' => [
					'group_join_add_points'  => ['UINT', 0],
					'group_join_add_invites' => ['UINT', 0],
					'group_join_sender_id'   => ['UINT', 2], // Padrão ID 2 (Geralmente o Admin)
					'group_join_pm_subject'  => ['VCHAR:255', ''],
					'group_join_pm_message'  => ['MTEXT_UNI', ''], // Suporta BBCodes e textos longos
				],
			],
		];
	}

	/**
	 * Remove as colunas caso a extensão seja excluída via ACP
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