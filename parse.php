<?php

class MoaParseHTML {

	private $_encodeMe = array();
	private $_page;

	function __construct($address) {
		$this->_page = new DOMDocument();
		$page = file_get_contents($address);
		# fix broken <a>
		$page = str_replace('<a>', '</a>', $page);
		$this->_page->loadHTML($page);

		$html = $this->_page->getElementsByTagName('html')->item(0);

		$this->parse_html($html);
	}

	private function parse_attributes($node, &$arr) {
		foreach ($node->attributes as $attr) {
			$arr['attributes'][$attr->nodeName] = $attr->nodeValue;
		}
	}

	private function parse_childs($childs) {
		$_childArr = [];
		foreach($childs as $child) {
			$_key = $child->nodeName;
			if ($child->nodeType == XML_ELEMENT_NODE) {
				if ($child->hasAttributes() &&
						($_id = $child->attributes->getNamedItem('id'))) {
					$_key .= '#' . $_id->nodeValue;
				}
				if ($child->hasAttributes()) {
					$this->parse_attributes($child, $_childArr[$_key]);
				}
				if ($child->hasChildNodes()) {
					$_childArr[$_key]['child_nodes'] =
							$this->parse_childs($child->childNodes);
				}
				if (empty($_childArr[$_key]['child_nodes'])) {
					unset($_childArr[$_key]['child_nodes']);
				}
			} else if ($child->nodeType == XML_TEXT_NODE
					&& !empty($child->nodeValue)) {
				$_childArr[$_key]['value'] = trim(str_replace("\n", "\r\n",
					$child->nodeValue));
			}
		}

		return $_childArr;
	}

	private function parse_html($html) {
		$_htmlArr = [];
		$this->parse_attributes($html, $_htmlArr);

		$title = $html->ownerDocument->getElementsByTagName('title')->item(0);
		$_htmlArr['value'] = $title->nodeValue;

		$stile = $html->ownerDocument->getElementsByTagName('style')->item(0);
		# Convert to windows CRLF
		$_htmlArr['value'] .= trim(str_replace("\n", "\r\n",
				$stile->nodeValue));

		$_htmlArr['child_nodes'] = $this->parse_childs($html->childNodes);

		$this->_encodeMe['html'] = $_htmlArr;
	}

	public function to_json() {
		return json_encode($this->_encodeMe);
	}
}

$parse_page = new MoaParseHTML(
	'http://testing.moacreative.com/job_interview/php/index.html');

echo ($parse_page->to_json());
echo ("\n");
?>
