<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<<?php print $ds_content_wrapper;
print $layout_attributes; ?> class="ds-1col <?php print $classes; ?> clearfix">

<?php if (isset($title_suffix['contextual_links'])): ?>
  <?php print render($title_suffix['contextual_links']); ?>
<?php endif; ?>

<p class="contact__intro">For media enquiries, please get in touch using the contact below</p>
<label for="contact__title">Contact:</label>
<p class="contact__title" id="contact__title"><?php print $title?></p>
<p class="contact__name"><?php print $content['field_firstname'][0]['#markup']; ?> <?php print $content['field_lastname'][0]['#markup']; ?></p>
<?php print render($content['field_para_telephone']); ?>
<?php print _spamspan_filter_process(render($content['field_para_email']), NULL); ?>


</<?php print $ds_content_wrapper ?>>

<?php if (!empty($drupal_render_children)): ?>
  <?php print $drupal_render_children ?>
<?php endif; ?>
