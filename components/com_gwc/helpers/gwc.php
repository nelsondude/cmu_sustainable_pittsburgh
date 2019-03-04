<?php
// No direct access to this file
defined('_JEXEC') or die;
jimport('joomla.application.component.helper');
abstract class gwcHelper {
	public function getCycle(){
		$app = JComponentHelper::getParams('com_gwc');
		$cycle = (JRequest::getVar('cycle') ? JRequest::getVar('cycle') : $app->get('current_cycle'));
		$cycle = (JRequest::getVar('filter')["cycle"] ? JRequest::getVar('filter')["cycle"] : $cycle);
		return $cycle;
	}
	
	public function ongoing(){
		$app = JComponentHelper::getParams('com_gwc');
		$start = strtotime($app->get('startdate'));
		$end = strtotime($app->get('enddate'));
		$now = time();
		return ($start < $now && $now < $end);
	}
	
	public function getArchives(){
		$app = JComponentHelper::getParams('com_gwc');
		$archives = explode(',',$app->get('past_cycles'));
		return $archives;
	}
	
	public function getLegacyPoints(){
		$past_yrs = implode(',',gwcHelper::getArchives());
		$db = JFactory::getDBO();
		$q = $db->getQuery(true);
		//GROUP_CONCAT(s.final_points) as total, GROUP_CONCAT(CONVERT(c.legacy_percentage / 100, DECIMAL(2,1))) as pct,
		$q->select("s.company_id, SUM(FORMAT(s.final_points * (c.legacy_percentage / 100),1)) as mathed");
		$q->from("#__gwc_submittals s");
		$q->join("INNER", "#__gwc_actions a ON s.action_id = a.id");
		$q->join("INNER", "#__gwc_action_categories c ON c.id = a.category");
		$q->where("s.year IN ($past_yrs)");
		$q->where("s.approved = 1");
		$q->where("s.final_points >= 1");
		$q->group("s.company_id");
		$db->setQuery($q);
		$points = array_map(function($p){return $p->mathed;},$db->loadObjectList('company_id'));
		
		return $points;
	}
	
	public function updateLegacyPoints(){
		$db = JFactory::getDBO();
		$legacies = self::getLegacyPoints();
		foreach($legacies as $company_id=>$legacypoints){
			$db->setQuery("UPDATE #__gwc_companies SET legacy_points = $legacypoints WHERE id = $company_id");
			if(!$db->query()) JError(500,$db->getErrorMsg());
		}
	}
	
	public function saveUserCompany($userid,$companyid,$createcompany = '',$type = 1,$size = 1, $referred_by = ''){
		$db 	= JFactory::getDbo();
		$user	= JFactory::getUser();		
		if(!isset($companyid) && strlen($createcompany)) {
			$db->setQuery("SELECT id FROM #__gwc_companies WHERE name = '{$createcompany}'");
			$companyid = $db->loadResult();			
			$db->setQuery("INSERT INTO #__gwc_companies (name,type,size) VALUES('".$createcompany."',".$type.",".$size.")");
			if(!$db->query()) JError(500,$db->getErrorMsg());
			$companyid = $db->insertid();
		}
		
		$db->setQuery("INSERT INTO #__gwc_company_user (company_id,user_id,referred_by) VALUES(".$companyid.",".$userid.",'".$referred_by."')
				ON DUPLICATE KEY UPDATE company_id = {$companyid}");
		if(!$db->query()) JError(500,$db->getErrorMsg());		
	}
	
	public function getCompanyByUser($userid){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('company_id');
		$query->from('#__gwc_company_user');
		$query->where('user_id = '.$userid);
		$db->setQuery($query);
		if(!$db->query())JError::raiseError(500,$db->getErrorMsg());
		return $db->loadResult();	
	}
	
	public function getCompanyNameByUser($userid){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('name,type,size')
			->from('#__gwc_company_user cu')
			->join("INNER", "#__gwc_companies c ON c.id = cu.company_id")
			->where('user_id = '.$userid);
		$db->setQuery($query);
		if(!$db->query())JError::raiseError(500,$db->getErrorMsg());
		return $db->loadObject();	
	}	
	
	public function getUserByCompany($userid){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('company_id');
		$query->from('#__gwc_company_user');
		$query->where('user_id = '.$userid);
		$db->setQuery($query);
		if(!$db->query())JError::raiseError(500,$db->getErrorMsg());
		return $db->loadResult();
	}

	public function getTypesSizes() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,name');
		$query->from('#__gwc_company_types');
		$db->setQuery($query);
		$data = $db->loadObjectList();
		
		$types = array_reduce(
			$data,
			function(&$result, $item){
				$result[$item->id] = $item->name;
				return $result;
			}
		);
		$query = $db->getQuery(true);
		$query->select('id,name');
		$query->from('#__gwc_company_sizes');
		$db->setQuery($query);
		$data = $db->loadObjectList();
		$sizes = array_reduce(
			$data,
			function(&$result, $item){
				$result[$item->id] = $item->name;
				return $result;
			}
		);		
		return array('types' => $types, 'sizes' => $sizes);
	}
	
	public function checkActive($companyId){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->select('active')
			->from('#__gwc_companies')
			->where("id = {$companyId}");
		$db->setQuery($query);
		return $db->loadResult();			
	}
	
	public function getCompanies(){
		$db = JFactory::getDbo();
		$db->setQuery("select name, id from #__gwc_companies order by id");
		if(!$db->query()) JError(500,$db->getErrorMsg());
		$orgs = $db->loadObjectList('name');
		$js_array = json_encode($orgs);
		/*
		JFactory::getDocument()->addScriptDeclaration('
jQuery(document).ready(function() {
	
	var orgs = '.$js_array.';
	
	var org = jQuery.map(orgs, function(value) {
		return value["name"];
	});
	jQuery(".typeahead").typeahead({
		items: 5,
		source: org,
		updater: function(item) {		
			jQuery("#jform_company_name").attr("value",orgs[item].id);
			jQuery("#jform_company_id").attr("value",orgs[item].id);
			return item;
		},
		sorter: function(items) {
			if (items.length == 0) {
				var noResult = new Object();
				items.push(noResult);
			}
			return items;    
		},
		highlighter: function(item) {
			if (org.indexOf(item) == -1) {
				jQuery("#connect").hide();
				jQuery("#addcompany").show();
				$stuff = jQuery("#jform_namiicompany_company_name").val();
				jQuery("#jform_namiicompany_company_name").keyup(function(e){
					var code = e.keyCode || e.which;
					if (code == "9") {
						jQuery("#jform_namiicompany_company_name").val($stuff);
					}
				});
				jQuery("ul.typeahead").addClass("nothing");
				jQuery("input.companyname").addClass("addcompany");
				jQuery(".reg_submit").removeAttr("disabled");
			} else {
				jQuery("ul.typeahead").removeClass("nothing");
				jQuery("#addcompany").hide();
				jQuery("#connect").show();
				return "<span>"+item+"</span>";
			}
		}
}).on("blur keyup", function() {
	if (jQuery(this).val() === "") {
		jQuery("#connect").show();
		jQuery("#addcompany").hide();
		jQuery("input.companyname").removeClass("addcompany");
		jQuery(".reg_submit").attr("disabled", "disabled");
	
	}
});	
});
');		*/
	}
}