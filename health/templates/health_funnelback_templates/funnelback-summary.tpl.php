<?php
/**
 * @file
 * Search summary template.
 *
 * Available variables:
 * - $summary: An array of summary information.
 */
?>
<div id="funnelback-summary">
    Search results
  <?php print $summary['start'] ?> - <?php print $summary['end'] ?> of <?php print $summary['total'] ?>
</div>
