<?php
namespace eunaumtenhoid\groupjoinactions;

/**
 * Extension class for Group Join Actions
 */
class ext extends \phpbb\extension\base
{
	/**
	 * Check if the extension can be enabled.
	 *
	 * @return bool
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');

		return phpbb_version_compare($config['version'], '3.3.0', '>=') && phpbb_version_compare(PHP_VERSION, '7.1.3', '>=');
	}
}
