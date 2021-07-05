<?php

class LWImageManipulator extends Phpr_Extensible {

	public $implement = "Phpr_ImageManipulator";

	private $path = null;
	private $id = null;

	public function __construct($id, $path) {
		parent::__construct();
		$this->path = $path;
		$this->id = $id;
	}

	public function getPath() {
		return "/uploaded/" . $this->path;
	}

	public function getPrimaryKeyValue() {
		return $this->id;
	}
}