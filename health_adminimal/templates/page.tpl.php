<?php
/**
* @file
* Main page template.
*/
?>
<div id="branding" class="clearfix">

	<?php print $breadcrumb; ?>

	<?php print render($title_prefix); ?>

	<?php if ($title): ?>
		<h1 class="page-title"><?php print $title; ?></h1>
	<?php endif; ?>

	<?php print render($title_suffix); ?>

</div>

<div id="navigation">

  <?php if ($primary_local_tasks): ?>
    <?php print render($primary_local_tasks); ?>
  <?php endif; ?>

  <?php if ($secondary_local_tasks): ?>
    <div class="tabs-secondary clearfix"><ul class="tabs secondary"><?php print render($secondary_local_tasks); ?></ul></div>
  <?php endif; ?>

</div>

<div id="page">

	<div id="content" class="clearfix">
		<div class="element-invisible"><a id="main-content"></a></div>

    <?php if (theme_get_setting('content_freeze_flag')): ?>
      <div class="maintenance-notification">
        <p class="title">Dual authoring required</p>
        <p class="subtitle">beta.health.gov.au is currently undergoing maintenance.</p>
        <p>Any content changes will need to be <strong>dual authored</strong>
          both here and in <?php print l(theme_get_setting('dual_author_environment'), theme_get_setting('dual_author_environment')  ) ?>
          until maintenance has finished.
        </p>
        <p>We <strong>strongly recommend</strong> waiting until maintenance has finished unless the <strong>content
          change is urgent</strong>.</p>
        <p>Contact <a href="http://my.central.health/Person.aspx?accountname=CENTRAL%5CFinlas">Stuart Finlayson</a> for further information.</>
      </div>
    <?php endif; ?>

	<?php if ($messages): ?>
		<div id="console" class="clearfix"><?php print $messages; ?></div>
	<?php endif; ?>

	<?php if ($page['help']): ?>
		<div id="help">
			<?php print render($page['help']); ?>
		</div>
	<?php endif; ?>

	<?php if (isset($page['content_before'])): ?>
		<div id="content-before">
			<?php print render($page['content_before']); ?>
		</div>
	<?php endif; ?>

	<?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>

  <div id="content-wrapper">

    <?php if (isset($page['sidebar_left'])): ?>
      <div id="sidebar-left">
        <?php print render($page['sidebar_left']); ?>
      </div>
    <?php endif; ?>

    <div id="main-content">
	    <?php print render($page['content']); ?>
	  </div>

    <?php if (isset($page['sidebar_right'])): ?>
      <div id="sidebar-right">
        <?php print render($page['sidebar_right']); ?>
      </div>
    <?php endif; ?>
	
	</div>

	<?php if (isset($page['content_after'])): ?>
		<div id="content-after">
			<?php print render($page['content_after']); ?>
		</div>
	<?php endif; ?>

	</div>

	<div id="footer">
		<?php print $feed_icons; ?>
	</div>

</div>
