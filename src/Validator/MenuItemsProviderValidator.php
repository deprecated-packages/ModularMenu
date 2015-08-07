<?php

/**
 * This file is part of Zenify.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Zenify\ModularMenu\Validator;

use Assert\Assertion;
use Zenify\ModularMenu\Contract\Provider\MenuItemsProviderInterface;
use Zenify\ModularMenu\Contract\Structure\MenuItemCollectionInterface;
use Zenify\ModularMenu\Exception\InvalidArgumentException;
use Zenify\ModularMenu\Structure\AbstractMenuItem;
use Zenify\ModularMenu\Structure\MenuHeadline;


class MenuItemsProviderValidator
{

	public function validate(MenuItemsProviderInterface $provider)
	{
		return $this->validateItems($provider->getItems());
	}


	/**
	 * @param mixed|MenuItemCollectionInterface|AbstractMenuItem[] $items
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	private function validateItems($items)
	{
		if ($items !== []) {
            Assertion::allIsObject($items);
            Assertion::isInstanceOf($items, MenuItemCollectionInterface::class);
            Assertion::allIsInstanceOf($items, AbstractMenuItem::class);
			$this->containsMaximallyOneInstance($items, MenuHeadline::class);
		}

		return TRUE;
	}


	/**
	 * @param array $items
	 * @param string $class
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	private function containsMaximallyOneInstance(array $items, $class)
	{
		$contains = FALSE;
		foreach ($items as $item) {
			if (is_object($item) && get_class($item) === $class) {
				if ($contains) {
					throw new InvalidArgumentException('Only one instance of ' . $class . ' is allowed.');

				} else {
					$contains = TRUE;
				}
			}
		}

		return TRUE;
	}

}
