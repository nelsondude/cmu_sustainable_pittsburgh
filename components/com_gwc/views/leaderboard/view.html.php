<?php

    defined("_JEXEC") or die('Restricted access');

    jimport('joomla.application.component.view');

    class GwcViewLeaderboard extends JViewLegacy {

		public function display($tpl = null) {

			$document = JFactory::getDocument();

			$gwc_styles = JUri::base() . 'components/com_gwc/assets/css/gwc.css';
			$document->addStyleSheet($gwc_styles);

			$c3_styles = JUri::base() . 'components/com_gwc/assets/css/c3.min.css';
			$document->addStyleSheet($c3_styles);

			$d3_script = JUri::base() . 'components/com_gwc/assets/js/d3.min.js';
			$document->addScript($d3_script);

			$c3_script = JUri::base() . 'components/com_gwc/assets/js/c3.min.js';
			$document->addScript($c3_script);			

			$this->user  = JFactory::getUser();

			$this->items = $this->get('Items');

			$model = $this->getModel();

			$app = JFactory::getApplication('site');

			$params = & $app->getParams('com_gwc');

			$size = JRequest::getVar('size');

			$type = JRequest::getVar('type');

			$layout = JRequest::getVar('layout')?JRequest::getVar('layout'):'default';

            

			if(isset($size) && isset($type)){

				$this->board = $model->getBoard($size,$type);

				$this->graph = $model->getGraph($size,$type);

				$used_cats = '"' . str_replace(',','","', implode(',',array_map(function($e){return $e->actions;},$this->graph))) . '"';

				$this->colors = $model->getColors($used_cats);

			} else {

				$this->board = $model->getBoard();

				$this->graph = $model->getGraph();
				//echo "<pre>".print_r($this->graph,1)."</pre>";
				$used_cats = '"' . str_replace(',','","', implode(',',array_map(function($e){return $e->actions;},$this->graph))) . '"';
				//die($used_cats);
				$this->colors = $model->getColors($used_cats);

			}

			if($layout == 'archive'){

				$this->archives = gwcHelper::getArchives();

			}

			$this->participants = $model->getParticipants();

			$this->colors[] = (object) array("name" => "Legacy","color" => "785ca7");

			

			$this->setLayout($layout);

			parent::display($tpl);

        }

    }

?>