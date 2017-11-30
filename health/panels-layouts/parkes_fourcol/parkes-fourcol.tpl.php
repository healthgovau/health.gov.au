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
 *   - $content['col1']: Content in the first column.
 *   - $content['col2']: Content in the second column.
 *   - $content['col3']: Content in the third column.
 *   - $content['col4']: Content in the fourth column.
 */
?>
<div class="panel-4col clearfix panel-display" <?php if (!empty($css_id)) { print "id=\"$css_id\""; } ?>>
  <?php if ($content['col1']): ?>
    <div class="panel-col-1 col-md-3 panel-panel">
      <?php print $content['col1']; ?>
    </div>
  <?php endif; ?>
  <?php if ($content['col2']): ?>
    <div class="panel-col-2 col-md-3 panel-panel">
      <?php print $content['col2']; ?>
    </div>
  <?php endif; ?>
  <?php if ($content['col3']): ?>
    <div class="panel-col-3 col-md-3 panel-panel">
      <?php print $content['col3']; ?>
    </div>
  <?php endif; ?>
  <?php if ($content['col4']): ?>
    <div class="panel-col-4 col-md-3 panel-panel">
      <?php print $content['col4']; ?>
    </div>
  <?php endif; ?>

</div>
