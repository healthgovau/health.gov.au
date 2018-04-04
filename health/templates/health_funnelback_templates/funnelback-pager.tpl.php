<?php
/**
 * @file
 * Template file for the funnelback pager.
 */
?>
<?php if (count($pager['pages']) > 1): ?>
  <ul class="funnelback-pager pager">

    <?php if (isset($pager['first_link'])): ?>
      <li class="pager-prev"><a href="<?php print $pager['first_link'] ?>">First</a></li>
    <?php endif ?>

    <?php if (isset($pager['first']) && !$pager['first']): ?>
      <li class="pager-prev"><a href="<?php print $pager['prev_link'] ?>">Prev</a></li>
    <?php endif ?>

    <?php foreach($pager['pages'] as $page): ?>

      <?php if ($page['current']): ?>
        <li class="pager-current"><?php print $page['title'] ?></li>
      <?php else: ?>
        <li class="pager-item"><a href="<?php print $page['link'] ?>"><?php print $page['title'] ?></a></li>
      <?php endif; ?>

    <?php endforeach ?>

    <li class="pager-next"><a href="<?php print $pager['next_link'] ?>">next ></a></li>

  </ul>
<?php endif ?>