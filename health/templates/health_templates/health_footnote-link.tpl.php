<?php
// We have to remove all white space between elements otherwise we get gaps between the inline elements.
// There isn't a css solution for this for now, so sad :( My soul hurts ):
?>
<sup class="au-footnotes__links">[<?php $index=1; foreach($items as $id => $text): ?><span class="au-footnotes__link" aria-describedby="#footnote-<?php print $id?>__description" tabindex="0" aria-label="Footnote"><?php print trim($text)?></span><?php if ($index<count($items)) { print ','; } $index++; ?><?php endforeach; ?>]</sup>