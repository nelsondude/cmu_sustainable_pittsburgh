<?php
// no direct access
defined('_JEXEC') or die;
$cycle = (JRequest::getVar('cycle') ? '&cycle='.JRequest::getVar('cycle') : '');
?>

<h1>Leaderboards</h1>
<?php 
	$pct 	= (100 / count($this->graph)) . "%";
	$max 	= 0;
?>
<div class="graph-overflow">
	<?php 
			$categories = array();
			$data = array();
			foreach ($this->graph as $i=>$graph) {
				$graph->company;
				if (!in_array($graph->company, $categories)){
				    $categories[] = $graph->company; 
				}

				foreach ($graph->colors as $key => $value) {
					if (!in_array($key, $data)){
					    $data[$key] = array($key); 
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
		?>
				<?php //echo "<pre>".print_r($data,1)."</pre>"; ?>

		<div id="chart">
		</div>
		<script>
			var chart = c3.generate({
			    data: {
			        columns: <?php echo json_encode(array_values($data)); ?>,
			        type: 'bar',
			        groups: [
			            <?php echo json_encode(array_keys($data)); ?>
			        ]
			    },
			    axis: {
			        x: {
			            type: 'category',
			            categories: ['<?php echo implode("','", $categories); ?>']
			        }
			    }
			});
		</script>
</div>
