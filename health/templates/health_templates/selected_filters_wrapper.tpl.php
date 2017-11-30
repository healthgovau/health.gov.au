<div id="edit-custom-remove" class="form-item form-type-item">
  <?php if ($selected_filters): ?>
    <label for="edit-custom-remove">Filters applied:</label>
    <div class="facet-remove">
      <?php print $selected_filters; ?>
    </div>
    <?php print $clear_all ?>
  <?php endif; ?>
</div>
