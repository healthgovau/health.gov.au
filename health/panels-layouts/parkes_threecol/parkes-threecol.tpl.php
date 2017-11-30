<?php
/**
 * @file
 * Template for a four column panel layout.
 *
 * This template provides a four column panel display layout, with
 * additional areas for the top and the bottom.
 *
 * Variables:
 * - $id: An optional CSS id to use for the layout.
 * - $content: An array of content, each item in the array is keyed to one
 *   panel of the layout. This layout supports the following sections:
 *   - $content['left_col']: Content in the left column.
 *   - $content['middle_top']: Content in the middle column at the top.
 *   - $content[middle_bottom']: Content in the middle column at the bottom.
 *   - $content['right_col']: Content in the right column.
 */
?>
<div class="panel-featured clearfix panel-display" <?php if (!empty($css_id)) { print "id=\"$css_id\""; } ?>>
  <?php if ($content['left_col']): ?>
    <div class="panel-left-col col-md-4 panel-panel">
      <?php print $content['left_col']; ?>
    </div>
  <?php endif; ?>
  <?php if ($content['middle_col']): ?>
    <div class="panel-middle-col col-md-4 panel-panel">
      <?php print $content['middle_col']; ?>
    </div>
  <?php endif; ?>
  <?php if ($content['right_col']): ?>
    <div class="panel-right-col col-md-4 panel-panel">
      <?php print $content['right_col']; ?>
    </div>
  <?php endif; ?>
</div>
