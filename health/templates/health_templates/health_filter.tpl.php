<div class="<?php print $class ?> health-accordion block-facetapi">
    <a href="<?php print '#accordion-' . $id ?>"
       class="health-accordion--open health-accordion__title"
       aria-controls="accordion-<?php print $id ?>"
       aria-expanded="true"
       aria-selected="false"
       role="tab">
        <h2 class="block__title"><?php print $title ?></h2>
    </a>
    <div class="health-accordion__body health-accordion--open" id="accordion-<?php print $id ?>" aria-hidden="true">
        <div class="health-accordion__body-wrapper <?php print $class ?>">
            <ol class="uikit-link-list <?php print $list_style ?>">
              <?php foreach ($items as $key => $item): ?>
                  <li>
                    <?php print l(ucfirst(str_replace('_', ' ', $item['name'])), '/topics', [
                            'query' => array_merge($query_string, [$filter_machine_name  => strtolower($key)]),
                            'attributes' => [
                              'class' => $item['status'] ? 'active' : '',
                            ],
                    ]); ?>
                  </li>
              <?php endforeach; ?>
            </ol>
        </div>
    </div>
</div>