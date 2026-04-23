<?php
/**
*
* ActionGroup [Portuguese Brazilian]
*
* @package language
* @copyright (c) 2026 eunaumtenhoid
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACTION_GROUP_TITLE'        => 'Ações de Grupo (ActionGroup)', 
	
	// Column names as requested: 'group add points' and 'group add invites'
	'ACTION_POINTS'             => 'group add points',
	'ACTION_POINTS_EXPLAIN'     => 'Quantidade de pontos que o usuário receberá automaticamente ao entrar no grupo.',
	'ACTION_INVITES'            => 'group add invites',
	'ACTION_INVITES_EXPLAIN'    => 'Quantidade de convites que o usuário receberá automaticamente ao entrar no grupo.',
	'ACTION_UPLOAD'             => 'group add uploaded (GB)',
	'ACTION_UPLOAD_EXPLAIN'     => 'Quantidade de Upload (em Gigabytes) que o usuário receberá no Tracker ao entrar no grupo.',
	'ACTION_ITEM'               => 'Item do UltimateShop',
	'ACTION_ITEM_EXPLAIN'       => 'Item que o usuário receberá automaticamente ao entrar no grupo.',
	'ACTION_ITEM_NONE'          => '-- Nenhum Item --',
	
	// Sender ID Configuration
	'ACTION_SENDER'             => 'Remetente da MP',
	'ACTION_SENDER_EXPLAIN'     => 'ID do usuário que aparecerá como remetente nos logs de pontos e como autor da Mensagem Privada (Geralmente o ID 2).',

	// Private Message (PM) Settings in ACP
	'GROUP_JOIN_PM_SETTINGS'     => 'Configurações de Mensagem Privada',
	'GROUP_JOIN_ACTION_SETTINGS' => 'Configurações de Bônus (Pontos/Convites)',
	'ACTION_PM_SUBJECT'          => 'Assunto da PM',
	'ACTION_PM_SUBJECT_EXPLAIN'  => 'Assunto da mensagem. Deixe vazio para NÃO enviar PM.',
	'ACTION_PM_MESSAGE'          => 'Mensagem da PM',
	'ACTION_PM_MESSAGE_EXPLAIN'  => 'Você pode usar BBCode e as variáveis: <strong>{USERNAME}</strong>, <strong>{USER_ID}</strong>, <strong>{GROUP_NAME}</strong>, <strong>{GROUP_ID}</strong>, <strong>{POINTS}</strong>, <strong>{INVITES}</strong>. Deixe vazio para NÃO enviar PM.',

	// Logs
	'LOG_GROUP_JOIN_BENEFITS'    => '<strong>Benefícios adquiridos ao entrar no grupo %s</strong>',
	'LOG_ROW_POINTS'             => '<br />» Pontos: %s',
	'LOG_ROW_INVITES'            => '<br />» Convites: %s',
	'LOG_ROW_UPLOAD'             => '<br />» Bônus de upload: %s GB',
	'LOG_ROW_ITEM'               => '<br />» Item recebido: %s',

	// Master key to avoid braces { } in the log
	'LOG_GROUP_JOIN_SUCCESS'     => '%s',

	// Old keys formatted with the new look for history
	'LOG_GROUP_JOIN_POINTS'      => '<strong>Benefícios adquiridos ao entrar no grupo %2$s</strong><br />» Pontos: %1$s',
	'LOG_GROUP_JOIN_UPLOAD'      => '<strong>Benefícios adquiridos ao entrar no grupo %2$s</strong><br />» Bônus de upload: %1$s GB',

	'LOG_APS_GROUP_JOIN_POINTS'  => 'Bônus de entrada no grupo %s',
	'LOG_UPS_GROUP_JOIN_POINTS'  => 'Bônus de entrada no grupo %s',

	// Post border configuration
	'ACTION_GROUP_POST_COLOR'			=> 'Ativar cor da borda nos posts',
	'ACTION_GROUP_POST_COLOR_EXPLAIN'	=> 'Habilita a exibição de uma borda colorida nos posts dos membros deste grupo.',
	'ACTION_GROUP_ICON'                 => 'Ícone do Grupo',
	'ACTION_GROUP_ICON_EXPLAIN'         => 'Selecione um ícone para ser exibido ao lado do nome dos usuários deste grupo.',
));