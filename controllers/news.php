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
 * Class News
 */
class News extends App_Controller {

	public function index($page = null) {
		if (!$page || $page < 1) {
			$page = 1;
		}
		$this->viewData['title'] = 'Новости';
		if (Phpr::$config->get('NEWS_ENABLE_CATEGORIES')) {
			$this->viewData['categories'] = News_Category::create()->find_all();
		} else {
			$pagination = new Phpr_Pagination(Phpr::$config->get('NEWS_ON_PAGE', 5));
			$this->viewData['important'] = News_Post::getImportant();
			$this->viewData['news'] = News_Post::listPublished($pagination, $page - 1, null, null, true);
			$this->viewData['pagination'] = $pagination;
			$this->viewData['page'] = $page;
		}

	}

	public function category($id, $page = null) {
		if (!$page || $page < 1) {
			$page = 1;
		}
		/** @var News_Category $category */
		$category = News_Category::create()->find($id);
		if (!$category) {
			$this->throw404();
		}
		$this->viewData['title'] = $category->name;
		$pagination = new Phpr_Pagination(Phpr::$config->get('NEWS_ON_PAGE', 5));
		$this->viewData['important'] = News_Post::getImportant($category);
		$this->viewData['news'] = News_Post::listPublished($pagination, $page - 1, $category, null, true);
		$this->viewData['categoryId'] = $category->id;
		$this->viewData['pagination'] = $pagination;
		$this->viewData['page'] = $page;
	}

	public function post($id) {
		$this->viewData['entry'] = $entry = News_Post::create()->find($id);
		if (!$entry) {
			$this->throw404();
		}
		Admin_SeoPlugin::apply($entry);
		$this->viewData['title'] = $entry->title_no_typo;
	}

	public function unsubscribe($hash) {
		$this->viewData['success'] = false;
		if (post('submit')) {
			News_Post::unsubscribe(trim($hash));
			$this->viewData['success'] = true;
		}
	}
}

