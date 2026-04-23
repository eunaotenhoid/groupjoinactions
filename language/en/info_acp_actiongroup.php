<?php
/**
*
* ActionGroup [English]
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
    'ACTION_GROUP_TITLE'        => 'Group Join Actions',
    'ACTION_POINTS'             => 'Points to add',
    'ACTION_POINTS_EXPLAIN'     => 'Amount of points the user will receive upon joining the group.',
    'ACTION_INVITES'            => 'Invites to add',
    'ACTION_INVITES_EXPLAIN'    => 'Amount of invites the user will receive upon joining the group.',
    'ACTION_UPLOAD'             => 'Upload to add (GB)',
    'ACTION_UPLOAD_EXPLAIN'     => 'Amount of Upload (in Gigabytes) the user will receive in the Tracker upon joining the group.',
    'ACTION_ITEM'               => 'UltimateShop Item',
    'ACTION_ITEM_EXPLAIN'       => 'Item the user will receive automatically upon joining the group.',
    'ACTION_ITEM_NONE'          => '-- No Item --',
    'ACTION_SENDER'             => 'PM Sender',
    'ACTION_SENDER_EXPLAIN'     => 'User ID that will appear as the sender in logs and PMs.',
    'GROUP_JOIN_PM_SETTINGS'    => 'Private Message Settings',
    'ACTION_PM_SUBJECT'         => 'PM Subject',
    'ACTION_PM_SUBJECT_EXPLAIN' => 'Subject for the PM. Leave empty to disable PM.',
    'ACTION_PM_MESSAGE'         => 'PM Message',
    'ACTION_PM_MESSAGE_EXPLAIN' => 'You can use BBCode and variables: {USERNAME}, {USER_ID}, {GROUP_NAME}, {GROUP_ID}, {POINTS}, {INVITES}.',

    // Post border settings
    'ACTION_GROUP_POST_COLOR'			=> 'Enable post border color',
    'ACTION_GROUP_POST_COLOR_EXPLAIN'	=> 'Enable the display of a colored border on posts made by members of this group.',
    'ACTION_GROUP_ICON'                 => 'Group Icon',
    'ACTION_GROUP_ICON_EXPLAIN'         => 'Select an icon to be displayed next to the usernames of this group.',

    // Logs
    'LOG_GROUP_JOIN_BENEFITS'    => '<strong>Benefits acquired upon joining group %s</strong>',
    'LOG_ROW_POINTS'             => '<br />» Points: %s',
    'LOG_ROW_INVITES'            => '<br />» Invites: %s',
    'LOG_ROW_UPLOAD'             => '<br />» Upload bonus: %s GB',
    'LOG_ROW_ITEM'               => '<br />» Received item: %s',

    // Master key to avoid braces { } in the log
    'LOG_GROUP_JOIN_SUCCESS'     => '%s',
    'LOG_APS_GROUP_JOIN_POINTS'  => 'Group join bonus in group %s',
    'LOG_UPS_GROUP_JOIN_POINTS'  => 'Group join bonus in group %s',
));