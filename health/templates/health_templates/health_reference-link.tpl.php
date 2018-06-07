<?php
// We have to remove all white space between elements otherwise we get gaps between the inline elements.
// There isn't a css solution for this for now, so sad :( My soul hurts ):
?>
<span class="reference__links">(<?php $index=1; foreach($items as $id => $text): ?><span class="reference__link"><a href="#reference-<?php print $id?>" aria-describedby="#reference-<?php print $id?>__description"><span class="sr-only">Reference:</span><?php print trim($text)?></a></span><?php if ($index<count($items)) { print ';'; } $index++; ?><?php endforeach; ?>)</span>