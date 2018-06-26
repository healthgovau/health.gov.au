<?php

/**
 * @file
 * Default theme implementation to navigate books.
 *
 * Presented under nodes that are a part of book outlines.
 *
 * Available variables:
 * - $tree: The immediate children of the current node rendered as an unordered
 *   list.
 * - $current_depth: Depth of the current node within the book outline. Provided
 *   for context.
 * - $prev_url: URL to the previous node.
 * - $prev_title: Title of the previous node.
 * - $parent_url: URL to the parent node.
 * - $parent_title: Title of the parent node. Not printed by default. Provided
 *   as an option.
 * - $next_url: URL to the next node.
 * - $next_title: Title of the next node.
 * - $has_links: Flags TRUE whenever the previous, parent or next data has a
 *   value.
 * - $book_id: The book ID of the current outline being viewed. Same as the node
 *   ID containing the entire outline. Provided for context.
 * - $book_url: The book/node URL of the current outline being viewed. Provided
 *   as an option. Not used by default.
 * - $book_title: The book/node title of the current outline being viewed.
 *   Provided as an option. Not used by default.
 *
 * @see template_preprocess_book_navigation()
 *
 * @ingroup themeable
 */
?>
<?php if ($has_links): ?>
  <div id="book-navigation-<?php print $book_id; ?>" class="book-navigation" role="navigation" aria-label="Book">
    <div class="row">
      <div class="book-navigation__link-wrapper col-xs-6">
        <?php if ($prev_url): ?>
        <a class="uikit-direction-link uikit-direction-link--left" href="<?php print $prev_url; ?>">
          Previous
        </a>
        <span class="book-navigation__link-title"><?php print $prev_title; ?></span>
        <?php endif; ?>
      </div>

      <div class="book-navigation__link-wrapper book-navigation__link-wrapper--right col-xs-6">
        <?php if ($next_url): ?>
        <a class="uikit-direction-link uikit-direction-link--right" href="<?php print $next_url; ?>">
          Next
        </a>
        <span class="book-navigation__link-title"><?php print $next_title; ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
