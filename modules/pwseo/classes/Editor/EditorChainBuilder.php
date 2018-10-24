<?php

require_once(dirname(__FILE__) . '/CmsCategoryEditor.php');
require_once(dirname(__FILE__) . '/CommonEditor.php');
require_once(dirname(__FILE__) . '/IndexEditor.php');
require_once(dirname(__FILE__) . '/OtherEditor.php');

/**
 * Построитель цепочки редакторов
 */
class EditorChainBuilder {

	/**
	 * Построить цепочку
	 * @return AbstractEditor
	 */
	public static function buildChain() {
		$first = new CmsCategoryEditor();
		$first
            ->setNext(new IndexEditor())
			->setNext(new CommonEditor())
			->setNext(new OtherEditor())
		;

		return $first;
	}

}