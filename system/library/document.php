<?php
class Document {
	private $title;
	private $description;
	private $keywords;
	private $icon;
	private $links = array();
	private $metas = array();
	private $styles = array();
	private $scripts = array();

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	public function getKeywords() {
		return $this->keywords;
	}

	public function setIcon($icon) {
		$this->icon = $icon;
	}

	public function getIcon() {
		return $this->icon;
	}

	public function addLink($href, $rel) {
		$this->links[$href] = array(
			'href' => $href,
			'rel'  => $rel
		);
	}

	public function getLinks() {
		return $this->links;
	}

	public function addMeta($name, $data) {
		$this->metas[$name] = array(
			'name' => $name,
			'content'  => $data
		);
	}

	public function getMetas() {
		return $this->metas;
	}

	public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
		$this->styles[$href] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}

	public function getStyles() {
		return $this->styles;
	}

	public function addScript($script) {
		$this->scripts[md5($script)] = $script;
	}

	public function getScripts() {
		return $this->scripts;
	}
}
