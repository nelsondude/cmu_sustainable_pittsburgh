<?php

defined("_JEXEC") or die('Restricted access');

jimport('joomla.application.component.modellist');

class GwcModelLeaderboard extends JModelList {



	public function getListQuery() {

		$id = JRequest::getVar('id');

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query = "SELECT 

					(CASE (SELECT COUNT(*) from #__gwc_companies WHERE type = t.id AND size != s.id)

						WHEN '0' THEN t.name

						ELSE CONCAT(s.name,' ',t.name)

						END) AS board,

					s.id AS size, t.id AS type 

					FROM #__gwc_company_types t 

					CROSS JOIN #__gwc_company_sizes s

					WHERE (SELECT COUNT(*) from #__gwc_companies WHERE size = s.id AND type = t.id AND active = 1  AND type!=4)

					AND t.id != 5"; //observers don't have leaderboards

		return $query;

	}

	

	public function getBoard($size=1,$type=1){

		$app = JFactory::getApplication();

		if($cycle = JRequest::getVar('cycle')){

			$column = 'past_cycles';

		} else {

			$column = 'points';

		}

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select("c.id, c.name, c.".$column." AS points, SUM(s.final_points) AS cycle_points");

		if(gwcHelper::ongoing()){

			$query->select("legacy_points, (c.".$column." + legacy_points) AS total_points");   // remove "0 as "

			$query->where("(c.points > 0 OR legacy_points > 0)");

		} else {

			$query->select("0 as total_points");

		}

		$query->from("#__gwc_companies c");

		$query->join("LEFT", "#__gwc_submittals s ON s.company_id = c.id AND s.year = ". gwcHelper::getCycle() ." AND s.approved = 1");

		$query->join("LEFT", "#__gwc_actions a ON s.action_id = a.id");
		
		if ($type == 4) {

			$query->where("type = {$type}");
		
		} else {
			
			$query->where("size = {$size} AND type = {$type}");
			
		}

		

		$query->where("c.active = 1");

		$query->where("c.display = 1");

		$query->group("c.id");		

		$db->setQuery($query);

		$results = $db->loadObjectList();

		

		if($cycle){

			foreach($results as $key=>$val){

				$val->points = json_decode($val->points)->$cycle;

			}

		}



		function cmp($a, $b){

			if (($a->cycle_points + $a->legacy_points) == ($b->cycle_points + $b->legacy_points)) {

				return 1;

			}

			return (($a->cycle_points + $a->legacy_points) > ($b->cycle_points + $b->legacy_points)) ? -1 : 1;

		}

		

		usort($results, "cmp");

		return $results;

	}

	public function getGraph($size=1,$type=1){

		$app = JFactory::getApplication();

		$cycle = (JRequest::getVar('cycle') ? JRequest::getVar('cycle') : $app->getParams()->get('current_cycle'));

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		
		if ($type == 4) {

			$query

			->select("c.id,c.name as company,ac.color,ac.name,SUM(s.final_points) as final_points")

			->from("#__gwc_companies c")

			->join("LEFT", "#__gwc_submittals s ON s.company_id = c.id AND s.year = {$cycle} AND s.approved = 1")

			->join("LEFT", "#__gwc_actions a ON s.action_id = a.id")

			->join("LEFT", "#__gwc_action_categories ac ON ac.id = a.category")

			->where("c.type = {$type}")

			->where("c.display = 1")

			->where("c.active = 1")

			->group("c.id,ac.id")

			->order("c.id");
		
		} else {
			
			$query

			->select("c.id,c.name as company,ac.color,ac.name,SUM(s.final_points) as final_points")

			->from("#__gwc_companies c")

			->join("LEFT", "#__gwc_submittals s ON s.company_id = c.id AND s.year = {$cycle} AND s.approved = 1")

			->join("LEFT", "#__gwc_actions a ON s.action_id = a.id")

			->join("LEFT", "#__gwc_action_categories ac ON ac.id = a.category")

			->where("c.size = {$size} AND c.type = {$type}")

			->where("c.display = 1")

			->where("c.active = 1")

			->group("c.id,ac.id")

			->order("c.id");
			
		}

		

		if(gwcHelper::ongoing()){

			$query

				->select("c.legacy_points")

				->where("(c.legacy_points > 0 OR s.final_points > 0)");

		}

		$db->setQuery($query);

		$results = $db->loadObjectList();

		$return = array();

		foreach($results as $row){

			if($return[$row->id]){

				$return[$row->id]->final_points += $row->final_points;

			} else {

				$return[$row->id] = new stdClass;

				$return[$row->id]->company = $row->company;

				$return[$row->id]->final_points  = $row->final_points;

				$return[$row->id]->final_points += $row->legacy_points;	// UNCOMMENT

				$return[$row->id]->legacy_points = $row->legacy_points;	// UNCOMMENT

				$return[$row->id]->colors['Legacy']->points = $row->legacy_points;	// UNCOMMENT
				$return[$row->id]->colors['Legacy']->color = '785CA7';	// UNCOMMENT
				
			}

			if(strlen($row->color))	{
				$return[$row->id]->colors[$row->name]->points = $row->final_points;
				$return[$row->id]->colors[$row->name]->color = $row->color;
			}

		}

		

		

		function cmp2($a, $b){

			$points_a = $a->final_points;

			$points_b = $b->final_points ;

			if (($points_a) == ($points_b)) {

				return 0;

			}

			return (($points_a) > ($points_b)) ? -1 : 1;

		}

		usort($return, "cmp2");

		//echo $query;
		//echo '<pre>'.print_r($results,1)."</pre>";
		//die('<pre>'.print_r($return,1));

		return $return;

	}		

/*	

	public function getGraph($size=1,$type=1){

		$app = JFactory::getApplication();

		$cycle = (JRequest::getVar('cycle') ? JRequest::getVar('cycle') : $app->getParams()->get('current_cycle'));

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query

			->select("c.id, c.name, (c.points + c.legacy_points) as total_points, c.legacy_points, c.points")

			->select("GROUP_CONCAT(s.final_points) as point_values, GROUP_CONCAT(a.id) as actions, s.year, s.approved")

			->select("SUM(s.final_points) as test")

			->select("GROUP_CONCAT(ac.id) as categories, GROUP_CONCAT(ac.color) as colors")

			->from("#__gwc_companies as c")

			//->join("LEFT", "#__gwc_submittals s ON s.company_id = c.id AND s.year = ". gwcHelper::getCycle() ." AND s.approved = 1")

			->join("LEFT", "#__gwc_submittals s ON s.company_id = c.id AND s.year = 2015 AND s.approved = 1")

			->join("LEFT", "#__gwc_actions a ON s.action_id = a.id")

			->join("LEFT", "#__gwc_action_categories ac ON a.category = ac.id")

			->where("c.size = {$size} AND c.type = {$type}")

			->where("(c.points > 0 OR c.legacy_points > 0)")

			->where("c.active = 1")

			->where("c.display = 1")

			->group("c.id");

			//->order("SUM(s.final_points) + c.legacy_points DESC");



		$db->setQuery($query);

		$results = $db->loadObjectList();

		if(!$db->query())JError::raiseError(500,$db->getErrorMsg());

		

		function cmp2($a, $b){

			$points_a = array_sum(explode(",",$a->point_values)) + $a->legacy_points;

			$points_b = array_sum(explode(",",$b->point_values)) + $b->legacy_points ;

			if (($points_a) == ($points_b)) {

				return 0;

			}

			return (($points_a) > ($points_b)) ? -1 : 1;

		}

		

		usort($results, "cmp2");

		return $results;

	}	

*/



	public function getParticipants(){

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query

			->select("id,name")

			->from("#__gwc_companies")

			->where("active = 1")

			->where("display = 1")

			->order("name");

		$db->setQuery($query);

		return $db->loadObjectList();

	}

	

	public function getColors($used_cats){

		$app = JFactory::getApplication();

		$cycle = (JRequest::getVar('cycle') ? JRequest::getVar('cycle') : $app->getParams()->get('current_cycle'));

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		//$query->select("color,GROUP_CONCAT(action_number) as action_numbers");

		$query->select("color,name,parent");

		$query->from("#__gwc_action_categories");

		//$query->where("years = {$cycle}");

		$query->where("parent IS NULL");

		$query->where("name IN ($used_cats)");

		$query->group("color");

		$db->setQuery($query);

		$results = $db->loadObjectList();

		//die('<pre>'.print_r($results,1));

		return $db->loadObjectList();

	}

}

	