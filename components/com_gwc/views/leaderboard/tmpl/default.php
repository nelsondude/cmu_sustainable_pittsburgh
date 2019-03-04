<?php
echo "<!-- AB -->";

// no direct access

defined('_JEXEC') or die;

$year  = (JRequest::getVar('cycle') ? JRequest::getVar('cycle') : gwcHelper::getCycle());

$size  = JRequest::getVar('size',1);

$type  = JRequest::getVar('type',1);

$cycle = '&cycle='.$year;

$activepage = '?size='.$size.'&type='.$type;

$base = JRoute::_('index.php') .  $cycle;

$ongoing = gwcHelper::ongoing();

$obj = new stdClass;

$objk->board = "K-12 School";

$objk->type = 4;

$objk->size = 1;

echo "<!--".var_dump($this->items)."-->";
$this->items[14] = $objk;

$obj->board = "Teams";

$obj->type = 8;

$obj->size = 1;

//$this->items[11] = $obj;

$obj2 = new stdClass;

$obj2->board = "Neighborhoods";

$obj2->type = 7;

$obj2->size = 1;

//$this->items[12] = $obj2;

$categories = array();

$data = array();
$colors = array();
foreach ($this->graph as $i=>$graph) {
	$graph->company;
	if (!in_array($graph->company, $categories)){
	    $categories[] = $graph->company; 
	}

	foreach ($graph->colors as $key => $value) {
		if (!in_array($key, $data)){
		    $data[$key] = array($key);
		    $colors[$key] = "#".$value->color;
		}
	}

}

foreach ($this->graph as $i=>$graph) {
	foreach ($data as $key => $value) {
		if (array_key_exists($key, $graph->colors)){
		   $data[$key][] = $graph->colors[$key]->points; 
		} else {
		   $data[$key][] = 0; 
		}
	}
}
$companyCount = count($categories);
//echo "<pre>".print_r(json_encode($colors),1)."</pre>";
//echo "<pre>".print_r($data,1)."</pre>";
//die('<pre>'.print_r($this->items,1));

?>
<!-- 102018 -->
<div id="gwc_leadboard">
<h1>Leaderboards - <?php echo gwcHelper::getCycle();?></h1>



<label for="board">Choose a leaderboard:</label>

<select id="board" name="board">

<?php foreach($this->items as $i=>$item) : ?>

	<?php $selected = ($item->type == $type && $item->size == $size) ? 'selected="selected"' : '';?>

	<option <?php echo $selected;?> value="<?php echo $item->size.'-'.$item->type; ?>"><?php echo $item->board;?></option>

<?php endforeach;?>

</select>

<div style="" class="btn btn-primary" id="showchart" >View Chart</div>

<div style="" class="btn btn-primary" id="showparticipants">View Participants</div>

<div id="participants" class="participantmodal" style="display:none;">

	<div class="close"></div>

	<h3>Participants</h3>

	<ul>

	<?php foreach($this->participants as $participant):?>

		<li><?php echo $participant->name;?></li>

	<?php endforeach;?>

	</ul>

</div>

<?php 

	$pct 	= (100 / count($this->graph));

	$max 	= 0;

?>

<div id="graphing">

	<div id="graph_list" class="graph-overflow modal" style="display:none;">
		<?php //echo print_r($this->colors,1); ?>
		<?php //echo "<pre>".print_r($this->graph,1)."</pre>"; ?>
		<div class="close"></div>
		<h1 id="chartlabel">Leaderboard</h1>
		
				<?php  //echo "<pre>".print_r($companyCount,1)."</pre>"; ?>

		<div id="chart">
		</div>
		
		<!-- <ul class="graph" style="width:<?php echo 500*count($this->graph);?>px;">

		<?php 

		$n=0;

		foreach ($this->graph as $i=>$graph) {

			$sum	= 0;

			//echo '<li class="graphbar" style="width:'.$pct.'%;left:'.$pct*$n.'%;">';

			echo '<li class="graphbar" style="width:'.$pct.'%;left:'. 500*$n.'px;">';

			foreach($graph->colors as $j=>$bar){

				echo '<div class="colorbar" style="background-color:#'.$j.';">';				

				echo '<div style="height:'.intval($bar).'px;"></div>';

				echo '</div>';

				$sum += $bar;

				$max = ($sum > $max ? $sum : $max);

			}

			echo '<span>'.$graph->company .'</span></li>';

			$n++;

		}

		

		?>

			<div class="grid" style="height:<?php echo $max;?>px">

			<?php

				for($i=0;$i<=$max+100;$i=$i+100){

					echo '<div style="bottom:'.($i).'px;">'.$i.'</div>';

				}

			?>

			</div>

		</ul> -->





	</div>

</div>

<div id="leaderlist">

	<ul style="margin-top:20px;" id="board_list" class="zebra leaderboard"  data-colors='<?php echo json_encode($colors); ?>' data-groups='<?php echo json_encode(array_keys($data)); ?>' data-data='<?php echo json_encode(array_values($data)); ?>' data-cats='<?php echo json_encode($categories); ?>' data-companyCount='<?php echo $companyCount; ?>'>

		<?php 

		$n = 1;

		foreach($this->board as $i=>$company) {

			echo '<li class="row-'.($i % 2 ? 'even' : 'odd').'">'.$n++.' - '.$company->name . '<span>' . intval($company->cycle_points + $company->legacy_points). '</span></li>';

		}

		?>

	</ul>
	
</div>
	<div class="leader_loading">
		<i class="uk-icon-spinner" aria-hidden="true"></i>
	</div>
</div>



<script>

var cycle = <?php echo $year;?>;

var size  = <?php echo $size;?>;

var type  = <?php echo $type;?>;

(function($){

	function showBoard(size,type){
		$(".leader_loading").show();
		$("#leaderlist").load('<?php echo JURI::root();?>?option=com_gwc&view=leaderboard&size=' + size + '&type=' + type + '&cycle=' + cycle + ' #board_list', function(){
			$(".leader_loading").hide();
		});	

		$("#graphing").load('<?php echo JURI::root();?>?option=com_gwc&view=leaderboard&tmpl=chart&size=' + size + '&type=' + type + '&cycle=' + cycle + ' #graph_list', function(){
			$("#graph_list .close").click(function(){
				$("#graph_list").hide();
			});
		});	

		if (history.pushState) {
          	var newurl = '<?php echo JURI::root();?>?option=com_gwc&view=leaderboard&size=' + size + '&type=' + type + '&cycle=' + cycle

	        window.history.pushState({path:newurl},'',newurl);
	      }

	}

	$(document).ready(function() {

		

		$("#board").change(function(){

			parts = $(this).val().split('-');

			showBoard(parts[0],parts[1]);

		});

		$("#graph_list .close").click(function(){

			$("#graph_list").hide();

		});

		$("#showchart").click(function(){
			$(".graph-overflow").show();
			if($("#leaderlist > ul").data('companycount') > 6) {
				chartHeight = $("#leaderlist > ul").data('companycount') * 115
			} else {
				chartHeight = 600
			}
			//console.log($(this).data('data'))
			//console.log($(this).data('groups'))
			var chart = c3.generate({
				size: {
					height: chartHeight
				},
			    data: {
			        columns: $("#leaderlist > ul").data('data'),
			        type: 'bar',
			        colors: $("#leaderlist > ul").data('colors'),
			        groups: [
			            $("#leaderlist > ul").data('groups')
			        ]
			    },
		        padding: {
		            top: 30
		        },
			    axis: {
			        x: {
			            type: 'category',
			            categories: $("#leaderlist > ul").data('cats')
			        },
			        rotated:true
			    },
			    tooltip: {
			        show: false
			    },
			    bar: {
			    	width: 75
			    }
			});

			

		});

		$("#showparticipants").click(function(){

			$("#participants").show();

		});

/*		

		var thispage = "<?php echo $activepage;?>";

		var base = ('<?php echo $base;?>').replace('&amp;','&');

		$("#board").val(thispage);

		

		$("#chartlabel").prepend($("#board option[value='" + thispage + "']").text());

		$("#board").change(function(){

			window.location.href = base + $(this).val();

		});

*/		

	});

})(jQuery);

</script>
