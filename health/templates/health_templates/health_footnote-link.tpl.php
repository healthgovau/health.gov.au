<?php
// We have to remove all white space between elements otherwise we get gaps between the inline elements.
// There isn't a css solution for this for now, so sad :( My soul hurts ):
?>
<span class="<?php print $type?>__links"><?php print $prefix; ?><?php $index=1; foreach($items as $id => $text): ?><span class="<?php print $type?>__link"><a href="#<?php print $type?>-<?php print $id?>" aria-describedby="#<?php print $type?>-<?php print $id?>__description"><span class="sr-only"><?php print $type?>:</span><?php print trim($text)?></a></span><?php if ($index<count($items)) { print $divider; } $index++; ?><?php endforeach; ?><?php print $suffix; ?></span>