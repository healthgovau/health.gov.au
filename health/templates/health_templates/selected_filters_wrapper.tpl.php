
<div id="edit-custom-remove" class="au-listing__filters au-listing__filters--inline field col-sm-12">
  <?php if ($selected_filters): ?>
    <label for="edit-custom-remove" class="field__label">Filters applied:</label>
    <div>
      <?php print $selected_filters; ?>
    </div>
    <?php print $clear_all ?>
  <?php endif; ?>
</div>
