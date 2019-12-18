<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Document class
*/
class Document {
	private $title;
    private $bodyclass;
    private $selflink;
	private $description;
	private $keywords;
    private $icon;
	private $links = array();
    private $metas = array();
	private $styles = array();
	private $scripts = array();

	/**
     *
     *
     * @param	string	$title
     */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
     *
	 *
	 * @return	string
     */
	public function getTitle() {
		return $this->title;
	}

    public function setBodyClass($bodyclass)
    {
        $this->bodyclass = $bodyclass;
    }

	/**
     *
     *
     * @param	string	$description
     */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
     *
     *
     * @param	string	$description
	 *
	 * @return	string
     */
	public function getDescription() {
		return $this->description;
	}

	/**
     *
     *
     * @param	string	$keywords
     */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	/**
     *
	 *
	 * @return	string
     */
	public function getKeywords() {
		return $this->keywords;
	}
	/**
     *
     *
     * @param	string	$href
	 * @param	string	$rel
     */
	public function addLink($href, $rel) {
		$this->links[$href] = array(
			'href' => $href,
			'rel'  => $rel
		);
	}

	/**
     *
	 *
	 * @return	array
     */
	public function getLinks() {
		return $this->links;
	}

	/**
     *
     *
     * @param	string	$href
	 * @param	string	$rel
	 * @param	string	$media
     */
	public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
		$this->styles[$href] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}

	/**
     *
	 *
	 * @return	array
     */
	public function getStyles() {
		return $this->styles;
	}

	/**
     *
     *
     * @param	string	$href
	 * @param	string	$position
     */
	public function addScript($href, $position = 'header') {
		$this->scripts[$position][$href] = $href;
	}

	/**
     *
     *
     * @param	string	$position
	 *
	 * @return	array
     */
	public function getScripts($position = 'header') {
		if (isset($this->scripts[$position])) {
			return $this->scripts[$position];
		} else {
			return array();
		}
	}

    public function getBodyClass()
    {
        return $this->bodyclass;
    }

    public function setSelfLink($self_link)
    {
        $this->selflink = $self_link;
    }

    public function getSelfLink()
    {
        return $this->selflink;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function addMeta($name, $data)
    {
        $this->metas[$name] = array(
            'name' => $name,
            'content'  => $data
        );
    }

    public function getMetas()
    {
        return $this->metas;
    }

}
