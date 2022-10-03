<?php

class Sale extends App_Controller
{

	public function index()
	{
		$this->viewData['products'] = $this->catalog->getProducts("cp.old_price is not null", null, null, self::sorting());
	}


	public function sorting()
	{
		$sorting = 'sales desc';
		$partsURI = parse_url($_SERVER['REQUEST_URI']);

		if (isset($partsURI['query'])) {

			parse_str($partsURI['query'], $query);

			if (isset($query['order'])) {
				switch ($query['order']) {
					case 'name':
						$sorting = 'name';
						break;

					case 'new':
						$sorting = 'id desc';
						break;

					case '-price':
						$sorting = 'price desc';
						break;

					case 'price':
						$sorting = 'price asc';
						break;
				}
			}
		}

		return $sorting;
	}
}
