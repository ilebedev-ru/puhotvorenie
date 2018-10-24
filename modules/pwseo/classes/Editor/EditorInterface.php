<?php

/**
 * Интерфейс редактора для программы-клиента
 */
interface EditorInterface {

	/**
	 * Загрузка данных
	 * @return bool
	 */
	public function load();

	/**
	 * Сохранение данных
	 * @return bool
	 */
	public function save();

	public function getFields();

	public function getFieldValue($fieldId);

	public function getStatus($withName = true);

	/**
	 * Получить id сущности (тех. название)
	 * @return string
	 */
	public function getIdEntity();

	/**
	 * Получить идентификатор экземпляра сущности (id)
	 * @return integer
	 */
	public function getIdItem();

}