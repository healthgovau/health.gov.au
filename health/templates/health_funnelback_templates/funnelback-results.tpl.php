<?php
/**
 * @file
 * Template file for funnelback results.
 */
?>
<div class="funnelback-results-page">

<?php if ($total > 0): ?>

  <?php print $summary; ?>

  <?php print $breadcrumb; ?>

  <?php print $curator; ?>

<?php endif; ?>

<?php // always show spelling options ?>
<?php print $spell; ?>

<?php if ($total > 0): ?>

<div class="funnelback-results">
<?php foreach ($items as $item): ?>
    <div class="ds-1col node node-page contextual-links-region view-mode-search_result  clearfix">
      <?php print $item; ?>
    </div>
<?php endforeach; ?>
</div>

<?php else: ?>

<p>Your search for <strong><?php print $query; ?></strong> did not return any results.
<p>Please ensure that you:
<ul class="no-result">
  <li>are not using any advanced search operators like + - | " etc.</li>
  <li>expect this document to exist within this site</li>
  <li>have permission to see any documents that may match your query</li>
</ul>
</p>

<?php endif; ?>

<?php print $pager ?>

</div>
