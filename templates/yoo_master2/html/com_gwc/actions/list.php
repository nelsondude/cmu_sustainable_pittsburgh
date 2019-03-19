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
$grouped = array();
foreach ($this->items as $i => $item) {
    $grouped[$item->category][] = $item;
}
//var_dump($grouped);

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
                    <ul>
                        <?php foreach ($items as $index => $item) : ?>
                            <li>
                                <?php if ($ongoing): ?>
                                    <a class=""
                                       href="index.php?option=com_gwc&view=actions&layout=default&id=<?php echo $item->id; ?>">
                                        <?php echo $item->name; ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo $item->name; ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>

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
        });
    }());
</script>

<ul class="actionlist">

<?php foreach ($this->items as $i => $item) : ?>
<?php //if ((substr($item->action_number, 0, 3) != "K12" && $this->userinfo->type !=4) || (substr($item->action_number, 0, 3) == "K12" && $this->userinfo->type ==4)) { ?>
<?php if (in_array($this->userinfo->type, explode(",",$item->type_ids))) { ?>

	<?php if($cat != $item->category):?>

		<h3><?php echo $item->category;?></h3>

	<?php endif;?>
	<li class="clearfix row<?php echo $i%2;?>"><strong><?php echo $item->action_number;?></strong> 
		<?php if($ongoing):?>

		<a class="" href="index.php?option=com_gwc&view=actions&layout=default&id=<?php echo $item->id;?>">

			<?php echo $item->name;?>

		</a>

		<?php else :?>

			<?php echo $item->name;?>

		<?php endif;?>

	</li>

	<?php $cat = $item->category;?>
<?php } ?>
<?php endforeach;?>

</ul>
