<?php

/**
 * UFO CMF
 *
 * PHP application framework
 *
 * @package		UFO CMF
 * @copyright		(c) 2013, Rinamika, http://rinamika.ru
 * @author			Viktor Suprun
 * @since			1.0
 * @license		http://rinamika.ru/ufo/licence.txt Rinamika Application License
 * @filesource
 */

/**
 * String helpers
 */

function h($str) {
	return Phpr_Html::encode($str);
}

function plainText($str) {
	return Phpr_Html::plainText($str);
}

function l($key) {
	return Phpr::$lang->app($key);
}

/*
 * Date helpers
 */

/**
 * @param $date
 * @param string $format
 * @return string
 */
function displayDate($date, $format = '%x') {
	return Phpr_Date::display($date, $format);
}

/**
 * @return Phpr_DateTime
 */
function gmtNow() {
	return Phpr_DateTime::gmtNow();
}

/*
 * Other helpers
 */

function traceLog($Str, $Listener = 'INFO') {
	if (Phpr::$traceLog)
		Phpr::$traceLog->write($Str, $Listener);
}

function flash() {
	return Admin_Html::flash();
}

function post($name, $default = null) {
	return Phpr::$request->post($name, $default);
}

/*
 * Form helpers
 */

function optionState($currentValue, $selectedValue) {
	return PHpr_Form::optionState($selectedValue, $currentValue);
}

function checkboxState($value) {
	return Phpr_Form::checkboxState($value);
}

function radioState($currentValue, $selectedValue) {
	return Phpr_Form::radioState($currentValue, $selectedValue);
}

function a_link($url, $title) {
	if ($url != Phpr::$request->getCurrentUri()) {
		return sprintf('<a href="%s">%s</a>', $url, $title);
	} else {
		return sprintf('<span>%s</span>', $title);
	}
}

/**
 * Генерирует ссылку для именованного роутинг-правила
 *
 * <pre>
 * <a href="<?= u('news-post', $post->id) ?>"></a>
 * <a href="<?= u('news-post', array($post->id)) ?>"></a>
 * <a href="<?= u('news-post', array('id' => $post->id)) ?>"></a>
 * <a href="<?= u('news-category', $category->id, $page+1) ?>"></a>
 * <a href="<?= u('news-category', array($category->id, $page+1)) ?>"></a>
 * <a href="<?= u('news-category', array('category_id' => $category->id, 'page' => $page+1)) ?>"></a>
 * </pre>
 *
 * @param string $name Название роута
 * @param string ... Остальные параметры
 * @see Phpr_Router#url()
 * @return string
 */
function u($name) {
	$args = func_get_args();
	$name = array_shift($args);
	reset($args);
	$passedArgs = null;
	switch (count($args)) {
		case 1:
			if (is_array(current($args))) {
				$passedArgs = current($args);
			} else {
				$passedArgs = $args;
			}
			break;
		case 0:
			$passedArgs = null;
			break;
		default:
			$passedArgs = $args;
	}
	return Phpr::$router->url($name, $passedArgs);
}