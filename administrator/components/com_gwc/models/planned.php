<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.modellist');
class GwcModelPlanned extends JModelList {

    public function __construct($config = array()) {
        $app = JFactory::getApplication();
        $option = JRequest::getVar('option');
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(

            );
        }

        // Get pagination request variables
        $limit = $app->getUserStateFromRequest('limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $app->setUserState('limit', $limit);
        parent::__construct($config);
    }

    public function getListQuery() {
        $app 	= JFactory::getApplication();
        $filter = JRequest::getVar('filter');
        $list = JRequest::getVar('list');
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('p.id as id, CONCAT(a.action_number, " - ", a.name) as action, co.name as company, p.deadline as deadline');
        $query->from('#__gwc_planned_actions p');
        $query->join('INNER', '#__gwc_companies co ON p.company_id = co.id');
        $query->join('INNER', '#__gwc_actions a ON p.action_id = a.id');
        if($filter['search']){
            $search = $db->quote('%' . $db->escape($filter['search'], true) . '%');
            $query->where('(CONCAT(a.action_number, " - ", a.name) LIKE ' . $search . ') || (co.name LIKE ' . $search . ')');
        }
        parent::populateState('id', 'asc');
        return $query;
    }
}
