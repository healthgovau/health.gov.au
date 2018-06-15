<?php
// We have to remove all white space between elements otherwise we get gaps between the inline elements.
// There isn't a css solution for this for now, so sad :( My soul hurts ):
?>
<sup class="footnote__links">[<?php $index=1; foreach($items as $id => $text): ?><span class="footnote__link"><a href="#footnote-<?php print $id?>" aria-describedby="#footnote-<?php print $id?>__description"><span class="sr-only">footnote:</span><?php print trim($text)?></a></span><?php if ($index<count($items)) { print ','; } $index++; ?><?php endforeach; ?>]</sup>