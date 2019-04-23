<?php

// no direct access

defined('_JEXEC') or die;

$cat = '';

$stripped_category = '';

$ongoing = gwcHelper::ongoing();

usort($this->items,function($a,$b){
	
	if($a->category != $b->category) return strcmp($a->category, $b->category); //fixes category sorting on actions list page.
	if(preg_match('/([A-Z]+)(\d+)/',$a->action_number,$matches)) list(,$keya,$numa) = $matches;
	if(preg_match('/([A-Z]+)(\d+)/',$b->action_number,$matches)) list(,$keyb,$numb) = $matches;
	if(!$keya) return 1;
	if($keya==$keyb) {
		return (0+$numa < 0+$numb) ? -1 : 1;
	}
	else return strcmp($keya,$keyb);
});

//Group by the item category
$num_planned = 0;
$grouped = array();
foreach ($this->items as $i => $item) {
    $grouped[$item->category][] = $item;
    if ($item->is_planned) {
        $num_planned += 1;
    }
}
//var_dump($grouped);

require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_gwc' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'gwc.php');
gwcHelper::getCompanies();
$user = JFactory::getUser();
$this->data->companyid = gwcHelper::getCompanyByUser($user->id);
$this->data->organization = gwcHelper::getCompanyNameByUser($user->id);
$company_options = gwcHelper::getTypesSizes();

if($_GET["debugActionsObj"]) echo "<pre>".print_r($this->items,1)."</pre>";

?>


<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<div id="accordion_search_bar_container">
    <input type="search"
           id="accordion_search_bar"
           placeholder="Search"/>
    <button class="btn btn-success" id="expandall">Expand All</button>
    <button class="btn btn-success" id="collapseall">Collapse All</button>
</div>

<div id="planned_actions">
    <a href="<?php echo JRoute::_('index.php?option=com_gwc&view=companies&id='.$this->data->companyid.'#planner-title');?>">
        <strong>Planned Actions (<span id="num_planned"><?php echo $num_planned ?></span>)</strong>
    </a>
</div>

<div class="panel-group"
     id="accordion"
     role="tablist"
     aria-multiselectable="true">
    <?php foreach ($grouped as $category => $items) :?>
    <?php $stripped_category = str_replace(' ', '', $category) ?>
        <div class="panel panel-success"
             id="collapse<?php echo $stripped_category;?>_container">
            <div class="panel-heading"
                 role="tab"
                 id="headingOne">
                <h4 class="panel-title">
                    <a role="button"
                       data-toggle="collapse"
                       data-target='#collapse<?php echo $stripped_category?>'
                       href="javascript:void(0);"
                       aria-expanded="true"
                       aria-controls="collapse<?php echo $stripped_category;?>">
                        <?php echo $category ?>
                    </a>
                </h4>
            </div>
            <div id="collapse<?php echo $stripped_category;?>"
                 class="panel-collapse collapse"
                 role="tabpanel"
                 aria-labelledby="headingOne">
                <div class="panel-body">
                    <ul class="actionlist">
                        <?php foreach ($items as $index => $item) : ?>
                            <li class="clearfix row<?php echo $index%2;?>">
                                <strong><?php echo $item->action_number;?></strong>
                                <?php if ($ongoing): ?>
                                    <a style="width: unset;" class="mytooltip"
                                       href="index.php?option=com_gwc&view=actions&layout=default&id=<?php echo $item->id; ?>">
                                        <?php echo $item->name; ?>
                                        <span class="tooltiptext">Click to submit.</span>
                                    </a>
                                <?php else : ?>
                                    <?php echo $item->name; ?>
                                <?php endif; ?>
                                <span style="float: right;">
                                    <input type="checkbox" <?php echo $item->is_planned==1 ? 'checked' : '' ?>
                                           data-id="<?php echo $item->id?>"
                                           data-action="<?php echo $item->action_number;?>"></span>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>
<div id="snackbar">Some text some message..</div>

<script>
    (function(){
        var searchTerm, panelContainerId;
        var $ = jQuery;
        $.expr[':'].containsCaseInsensitive = function (n, i, m) {
            return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
        };

        $('#accordion_search_bar').on('change keyup paste click', function () {
            searchTerm = $(this).val();
            $('#accordion > .panel').each(function () {
                panelContainerId = '#' + $(this).attr('id');
                $(panelContainerId + ':not(:containsCaseInsensitive(' + searchTerm + '))').hide();
                $(panelContainerId + ':containsCaseInsensitive(' + searchTerm + ')').show();
            });
            $('.actionlist li').find(':not(:containsCaseInsensitive(' + searchTerm + '))').each(function() {
                $(this).parent().css('display', 'none');
            })
            $('.actionlist li').find(':containsCaseInsensitive(' + searchTerm + ')').each(function() {
                $(this).parent().css('display', 'block');
            })
        });
        $('#expandall').click(function() {
            $('#accordion .collapse').collapse('show');
        });
        $('#collapseall').click(function() {
            $('#accordion .collapse').collapse('hide');
        });
    }());
</script>

<script>
    function show_toast(message, color) {
        var x = document.getElementById("snackbar");
        x.className = "show";
        x.innerHTML = message;
        x.style.background = color;
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 8000);
    }

    var $ = jQuery;
    $('input:checkbox').change(
        function(){
            var id = $(this).data("id");
            const action = $(this).data("action");
            if ($(this).is(':checked')) {
                // now it is checked, was not checkec
                $.post('index.php?option=com_gwc&task=companies.addPlannedAction', {"action_id": id} )
                    .done(function(data){
                        var currentValue = parseInt($("#num_planned").text(),10);
                        $('#num_planned').text(currentValue + 1);
                        const message = "Added action <b>" + action + "</b> to your planned actions. You can view these at your company profile page.";
                        show_toast(message, "#0B6623");
                    });
            } else {
                // now it is not checked, was checked
                $.post('index.php?option=com_gwc&task=companies.removePlannedAction', {"action_id": id} )
                    .done(function(data){
                        var currentValue = parseInt($("#num_planned").text(),10);
                        const message = "Deleted action <b>" + action + "</b> from your planned actions. You can view these at your company profile page.";
                        $('#num_planned').text(currentValue - 1);
                        show_toast(message, "#FF0000");
                    });
            }
        });
</script>

