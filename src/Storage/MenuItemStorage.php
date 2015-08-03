<?php

/**
 * This file is part of Zenify.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Zenify\ModularMenu\Storage;

use Zenify\ModularMenu\Contract\Provider\RankedMenuItemsProviderInterface;
use Zenify\ModularMenu\Exception\MissingPositionException;
use Zenify\ModularMenu\Contract\Provider\MenuItemsProviderInterface;
use Zenify\ModularMenu\Structure\MenuItem;
use Zenify\ModularMenu\Structure\MenuItemCollection;
use Zenify\ModularMenu\Validator\MenuItemsProviderValidator;


class MenuItemStorage
{

	/**
	 * @var int
	 */
	const DEFAULT_RANK = 100;

	/**
	 * @var MenuItem[][]
	 */
	private $menuItems;

	/**
	 * @var MenuItemsProviderValidator
	 */
	private $menuItemsProviderValidator;


	public function __construct(MenuItemsProviderValidator $menuItemsProviderValidator)
	{
		$this->menuItemsProviderValidator = $menuItemsProviderValidator;
	}


	public function addMenuItemsProvider(MenuItemsProviderInterface $menuItemsProvider)
	{
		$this->menuItemsProviderValidator->validate($menuItemsProvider);

		if ($menuItemsProvider instanceof RankedMenuItemsProviderInterface) {
			$this->menuItems[$menuItemsProvider->getPosition()][$menuItemsProvider->getRank()][] = $menuItemsProvider->getItems();

		} else {
			$this->menuItems[$menuItemsProvider->getPosition()][self::DEFAULT_RANK][] = $menuItemsProvider->getItems();
		}
	}


	/**
	 * @param string $position
	 * @return MenuItemCollection[]
	 */
	public function getByPosition($position)
	{
		if (isset($this->menuItems[$position])) {
			$menuItemsGroups = $this->menuItems[$position];
			ksort($menuItemsGroups);

			$menuItemCollections = [];
			foreach ($menuItemsGroups as $priority => $menuItemsGroup) {
				foreach ($menuItemsGroup as $menuItemCollection) {
					$menuItemCollections[] = $menuItemCollection;
				}
			}
			return $menuItemCollections;
		}

		throw new MissingPositionException(
			sprintf('Position "%s" was not found.', $position)
		);
	}

}
