<article>
<?php if (!empty($title)): ?>
<div class="page-header forum-page-title">
<h1><?php if (!empty($label)) echo '<span class="label label-warning forum-label">'.Zira\Helper::html($label).'</span>'; ?><?php echo Zira\Helper::html($title) ?></h1>
</div>
<?php endif; ?>
<?php if (!empty($description)): ?>
<p class="description">
<?php echo nl2br(Zira\Helper::html($description)); ?>
</p>
<?php endif; ?>
<?php if (isset($content)): ?>
<div class="article forum">
<?php echo $content; ?>
</div>
<?php endif; ?>
</article>