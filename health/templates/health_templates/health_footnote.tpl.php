<a class="<?php print $type?>__anchor" name="<?php print $type?>-<?php print _health_prepare_filename($id)?>"></a>
<?php if (!empty($number)):?>
<span class="<?php print $type?>__number"><?php print $number ?></span>
<?php endif; ?>
<span id="<?php print $type?>-<?php print _health_prepare_filename($id)?>__description"><?php print $text ?></span>
