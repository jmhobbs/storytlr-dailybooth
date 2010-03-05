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
class DailyboothModel extends SourceModel {

	protected $_name 	= 'dailybooth_data';

	protected $_prefix = 'dailybooth';

	protected $_search  = 'title';
	
	protected $_actor_key = 'username';
	
	protected $_update_tweet = "Added %d photos to vi.sualize.us %s"; 

	public function getServiceName() {
		return "DailyBooth";
	}

	public function isStoryElement() {
		return true;
	}

	public function getServiceURL() {
		if ($username = $this->getProperty('username')) {
			return "http://dailybooth.com/$username";
		}
		else {
			return "http://dailybooth.com";;
		}
	}

	public function getServiceDescription() {
		return "DailyBooth is a picture of you.";
	}

	public function setTitle($id, $title) {
		$this->updateItem($id, array('title' => $title));
	}

	public function importData() {
		$items = $this->updateData();  //took out true for testing only
		$this->setImported(true);
		return $items;
	}


	public function updateData($full=false) {
		$username = $this->getProperty('username');

		$pages		= $full ? 50 : 1;
		$result 	= array();
		$url 		= "http://dailybooth.com/rss/$username.rss";

		if (!($data = $this->loadFeed($url))) {
			throw new Stuffpress_Exception("dailybooth did not return any result for url: $url", 0);
		}
			
		if (!$data->get_item_quantity()) break;
			
		$items = $this->processItems($data);
		
		$result = array_merge($result, $items);
		
		// Mark as updated (could have been with errors)
		$this->markUpdated();
		
		return $result;
	}

	
	private function processItems($items) {
		$result = array();
		foreach ($items->get_items() as $item) {
			
			$data		= array();
			
			$data['pubDate']    = $item->get_date();
			$data['title'] 			= "Dailybooth ".$data['pubDate'];
			$data['link']				= $item->get_link();
			$data['photo_id']		= $this->fetch_id($data['link']);
			list($data['img_url'],$data['description']) = $this->split_img($item->get_content());
			
			$id = $this->addItem($data, strtotime($data['pubDate']), SourceItem::IMAGE_TYPE, false, false, false, $data['title']);
			
			if ($id) $result[] = $id;
		}
		
		return $result;
	}
	
	private function loadFeed($url) {
		// We'll process this feed with all of the default options.
		//$this->load->library('simplepie');
		$feed = new SimplePie();
		$feed->set_feed_url($url);
		$feed->enable_cache(false);
		$feed->init();
		$feed->handle_content_type();
		
		/*try {
			$feed = Zend_Feed::import($url);
		} catch (Zend_Feed_Exception $e) {
			// feed import failed
			return null;
		}*/
		return $feed;
	}
	
	
	private function fetch_id($str) {
		$tmp = explode("/",$str);
		return $tmp[4];
	}
	
	private function split_img($str) {
		$regex = '/<img src="http:\/\/dailybooth\.com\/pictures\/large\/(.*)" \/><br \/>/';
		$return = array();
	  preg_match($regex,$str,$matches);
		$return[0] = $matches[1];
		$return[1] = preg_replace($regex,'',$str);
		
		return $return;
	}

}
