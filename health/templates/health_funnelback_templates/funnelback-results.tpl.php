<?php
/**
 * @file
 * Template file for funnelback results.
 */
?>
<div class="funnelback-results-page">

<?php if ($total > 0): ?>

  <?php print render($summary); ?>

  <?php print render($breadcrumb); ?>

  <?php print render($curator); ?>

<?php endif; ?>

<?php // always show spelling options ?>
<?php print render($spell); ?>

<?php if ($total > 0): ?>

<div class="funnelback-results">
<?php foreach ($items as $item): ?>
    <div class="ds-1col node node-page contextual-links-region view-mode-search_result  clearfix">
      <?php print render($item); ?>
    </div>
<?php endforeach; ?>
</div>

<?php else: ?>

    <div class="no-result-text">
      <?php print $no_result_text; ?>
    </div>

<?php endif; ?>

<?php print render($pager) ?>

</div>
