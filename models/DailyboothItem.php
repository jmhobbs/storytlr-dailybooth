<?php
/*
 *    Copyright 2008-2009 Laurent Eschenauer and Alard Weisscher
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *  
 */
class DailyboothItem extends SourceItem implements ImageItem {

	protected $_prefix 	= 'dailybooth';
	
	protected $_preamble = 'Take a picture: ';
	
	public function getType() {
		return SourceItem::IMAGE_TYPE;
	}
	
	public function getTitle() {
		return $this->_data['title'];
	}
	
	public function setTitle($title) {
		$db = Zend_Registry::get('database');
		$sql = "UPDATE `dailytooth_data` SET `title`=:title "
			 . "WHERE source_id = :source_id AND id = :item_id ";
		$data 		= array("source_id" 	=> $this->getSource(),
							"item_id"		=> $this->getID(),
							"title"			=> $title);
							
 		return $db->query($sql, $data);
	}
	
	public function getDescription() {
		return $this->_data['note'];
	}
	
	public function setDescription($note) {
		$db = Zend_Registry::get('database');
		$sql = "UPDATE `dailytooth_data` SET `note`=:note "
			 . "WHERE source_id = :source_id AND id = :item_id ";
		$data 		= array("source_id" 	=> $this->getSource(),
							"item_id"		=> $this->getID(),
							"note"			=> $note);
							
 		return $db->query($sql, $data);
	}

	public function getLink() {
		return $this->_data['link'];
	}
	
	public function getImageUrl($size=ImageItem::SIZE_MEDIUM) {
		switch ($size) {
			case ImageItem::SIZE_THUMBNAIL:
				$size = 'small.pictures.boss.dailybooth.com';
				break;
			case ImageItem::SIZE_SMALL:
				$size = 'small.pictures.boss.dailybooth.com';
				break;
			case ImageItem::SIZE_MEDIUM:
				$size = 'medium.pictures.boss.dailybooth.com';
				break;
			case ImageItem::SIZE_LARGE:
				$size = 'large.pictures.boss.dailybooth.com';
				break;												
			case ImageItem::SIZE_ORIGINAL:
				$size = 'large.pictures.boss.dailybooth.com';
				break;
		}
		
		//http://large.pictures.boss.dailybooth.com/46013bf043f7ef0492750697d9b302e0_2326406.jpg
		//http://small.pictures.boss.dailybooth.com/46013bf043f7ef0492750697d9b302e0_2326406.jpg
		
		return "http://$size/".$this->_data['img_url'];
	}
	
	public function getRssContent() {
		$url  = $this->getPhotoURL('-');
		$link = $this->getLink();
		$title = $this->_data['title'];
		$html = "<a href='$link'><img src='$url' alt='$title'></a>";
		return $html;
	}
	
	public function getBackup() {
		$item = array();
		$item['Link']				= $this->getLink();
		$item['Title']				= $this->_data['title'];
		$item['Date']				= $this->_data['dateposted'];		
		return $item;
	}
}