<?php
namespace Acd\Model;

class ContentsDo extends Collection 
{
	public function loadFromArray($AContents, $idStructure) {
		foreach ($AContents as $key => $data) {
			// TODO revisar si debe ir el id además de como key en el propio contenido
			unset($content);
			$content = new ContentDo();
			$content->load($data, $idStructure);
			$this->add($content, $content->getId());
		}

	}

}