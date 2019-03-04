<?php
/**
 * @package Module Smart Countdown 3 for Joomla! 3.0
 * @version 3.0
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// no direct access
defined ( '_JEXEC' ) or die ();

$module_DOM_id = $options['id'];

$deadline = strtotime(($options['deadline']));
if ($deadline - time() > 0):

?>

<div class="smart-flipclock" id="<?php echo $module_DOM_id; ?>">
  <div class="smart-flipclock-inner">
    <div class="smart-flipclock-flipclock"></div>
    <?php echo !empty($options['text1']) ? "<div class='smart-flipclock-text1'>{$options['text1']}</div>" : '' ; ?>
    <?php echo !empty($options['text2']) ? "<div class='smart-flipclock-text2'>{$options['text2']}</div>" : '' ; ?>
  </div>
</div>

<script>
  (function($) {
    moment.tz.add('America/New_York|EST EDT|50 40|0101|1Lz50 1zb0 Op0');
    var deadline = moment.tz('<?php echo $options["deadline"]; ?>', 'America/New_York');
    deadline = deadline.diff(moment(), 'seconds');

    if (deadline > 0) {
      var clock = $('#<?php echo $module_DOM_id; ?> .smart-flipclock-flipclock').FlipClock(deadline, {
        countdown: true,
        clockFace: 'DailyCounter',
        destroy: function() {
          $('#<?php echo $module_DOM_id; ?>').remove();
        }
      });

      setInterval(function() {
        if (clock.getTime().time == 0) {
          clock.destroy();
        }
      }, 1000)
    }
  })(jQuery);
</script>

<?php endif;