<?php if (isset($searchForm)): ?>
<div class="messages-panel forum-messages-panel">
<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#user-messages-panel" aria-expanded="false">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Forum::ROUTE)) ?>" title="<?php echo tm('Forum','forum') ?>"><span class="glyphicon glyphicon-link"></span></a>
</div>
<div class="collapse navbar-collapse" id="user-messages-panel">
<?php if (Zira\User::isAuthorized()): ?>
<ul class="nav navbar-nav">
<li class="active"><a href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Forum::ROUTE.'/user')) ?>"><span class="glyphicon glyphicon-comment"></span> <?php echo tm('My discussions','forum') ?></a></li>
</ul>
<?php endif; ?>
<?php echo $searchForm; ?>
</div>
</div>
</nav>
</div>
<?php endif; ?>

<?php if (!empty($items)): ?>
<ul class="forum-list list">
<?php $co = 0; ?>
<?php foreach($items as $item): ?>
<li class="list-item no-thumb <?php echo ($co%2==0 ? 'odd' : 'even') ?>">
<h3 class="list-title-wrapper">
<?php if ($item->user_username): ?>
<?php echo Zira\User::generateUserProfileLink($item->creator_id, $item->user_firstname, $item->user_secondname, $item->user_username, 'author') ?>
<?php else: ?>
<?php echo tm('User deleted', 'forum'); ?>
<?php endif; ?>
</h3>
<div class="list-content-wrapper forum-message-wrapper">
<div class="forum-avatar-wrapper">
<?php if ($item->user_username): ?>
<?php echo Zira\User::generateUserProfileThumbLink($item->creator_id, $item->user_firstname, $item->user_secondname, $item->user_username, null, $item->user_image, null, array('class'=>'forum-avatar')) ?>
<?php else: ?>
<?php echo Zira\User::generateUserProfileThumb($item->user_image, null, array('class'=>'forum-avatar')) ?>
<?php endif; ?>
<div class="forum-user-info">
<?php $user_group_name = !empty($user_groups) && $item->user_group_id && array_key_exists($item->user_group_id, $user_groups) ? Zira\Helper::html($user_groups[$item->user_group_id]) : ''; ?>
<?php if (!empty($user_group_name) && $item->user_group_id == Zira\User::GROUP_SUPERADMIN) $user_group_name = '<span class="forum-group-super-admin">'.$user_group_name.'</span>'; ?>
<?php if (!empty($user_group_name) && $item->user_group_id == Zira\User::GROUP_ADMIN) $user_group_name = '<span class="forum-group-admin">'.$user_group_name.'</span>'; ?>
<?php if (!empty($user_group_name)) echo '<div>'.$user_group_name.'</div>' ?>
<?php if ($item->user_posts) echo '<div>'.tm('Posts: %s', 'forum', Zira\Helper::html($item->user_posts)).'</div>' ?>
</div>
</div>
<?php $mclass = ''; ?>
<?php if ($item->status == Forum\Models\Message::STATUS_MESSAGE) $mclass = ' alert alert-success'; ?>
<?php if ($item->status == Forum\Models\Message::STATUS_INFO) $mclass = ' alert alert-info'; ?>
<?php if ($item->status == Forum\Models\Message::STATUS_WARNING) $mclass = ' alert alert-danger'; ?>
<?php $micon = ''; ?>
<?php if ($item->status == Forum\Models\Message::STATUS_MESSAGE) $micon = '<span class="glyphicon glyphicon-info-sign"></span> '; ?>
<?php if ($item->status == Forum\Models\Message::STATUS_INFO) $micon = '<span class="glyphicon glyphicon-exclamation-sign"></span> '; ?>
<?php if ($item->status == Forum\Models\Message::STATUS_WARNING) $micon = '<span class="glyphicon glyphicon-warning-sign"></span> '; ?>
<p class="parse-content forum-message<?php echo $mclass ?>"><?php echo $micon ?><?php echo Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($item->content))) ?></p>
<?php $images = Forum\Models\File::extractItemFiles($item, 'file_', 'images'); ?>
<?php $files = Forum\Models\File::extractItemFiles($item, 'file_', 'files'); ?>
<?php if (!empty($images) || !empty($files)): ?>
<div class="forum-message-attaches">
<div class="forum-message-attaches-title"><span class="glyphicon glyphicon-paperclip"></span> <?php echo tm('Attached files', 'forum') ?> (<?php echo count($images)+count($files); ?>) <span class="glyphicon glyphicon-chevron-down attach-arrow-down"></span></div>
<div class="forum-message-attaches-content">
<?php foreach($images as $filepath=>$filename): ?>
<?php if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . $filepath)): ?>
<?php $filesrc = UPLOADS_DIR . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $filepath); ?>
<a class="forum-message-attach" data-lightbox="forum-message-<?php echo Zira\Helper::html($item->id) ?>" href="<?php echo Zira\Helper::html(Zira\Helper::baseUrl($filesrc)) ?>" title="<?php echo Zira\Helper::html($filename) ?>">
<img src="<?php echo Zira\Helper::html(Zira\Helper::baseUrl($filesrc)) ?>" alt="<?php echo Zira\Helper::html($filename) ?>" width="<?php echo Zira\Config::get('thumbs_width') ?>" />
</a>
<?php else: ?>
<?php echo '<span class="forum-message-attach">'.tm('File "%s" not found', 'forum', Zira\Helper::html($filename)).'</span>'; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php foreach($files as $filepath=>$filename): ?>
<?php if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . $filepath)): ?>
<?php $filesrc = UPLOADS_DIR . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $filepath); ?>
<a class="forum-message-attach" href="<?php echo Zira\Helper::html(Zira\Helper::baseUrl($filesrc)) ?>" title="<?php echo Zira\Helper::html($filename) ?>" download="<?php echo Zira\Helper::html($filename) ?>" target="_blank" rel="nofollow">
<?php echo Zira\Helper::html($filename) ?>
</a>
<?php else: ?>
<?php echo '<span class="forum-message-attach">'.tm('File "%s" not found', 'forum', Zira\Helper::html($filename)).'</span>'; ?>
<?php endif; ?>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>
</div>
<div class="list-info-wrapper forum-info-wrapper">
<?php if ($item->topic_title): ?>
<a class="list-info link" href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Models\Topic::generateUrl($item->topic_id))) ?>" title="<?php echo Zira\Helper::html($item->topic_title) ?>"><span class="glyphicon glyphicon-new-window"></span></a>
<?php endif; ?>
<?php if ($item->modified_by): ?>
<span class="list-info note" title="<?php echo tm('Edited by moderator', 'forum') ?>"><span class="glyphicon glyphicon-warning-sign"></span></span>
<?php endif; ?>
<span class="list-info date"><span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($item->date_modified)) ?></span>
<a href="javascript:void(0)" class="list-info forum-rating forum-like" data-value="1" data-type="forum_message" data-id="<?php echo intval($item->id) ?>" data-token="<?php echo Zira\User::getToken() ?>" data-url="<?php echo Zira\Helper::url('forum/poll') ?>">
<span class="glyphicon glyphicon-thumbs-up"></span>
</a> &nbsp;
<a href="javascript:void(0)" class="list-info forum-rating forum-dislike" data-value="-1" data-type="forum_message" data-id="<?php echo intval($item->id) ?>" data-token="<?php echo Zira\User::getToken() ?>" data-url="<?php echo Zira\Helper::url('forum/poll') ?>">
<span class="glyphicon glyphicon-thumbs-down"></span>
</a>
<?php $rating = intval($item->rating); ?>
<?php if ($rating>0) $rating = '<span class="positive-rating">+'.$rating.'</span>'; ?>
<?php if ($rating<0) $rating = '<span class="negative-rating">'.$rating.'</span>'; ?>
<span class="list-info forum-rating-value"><?php echo $rating; ?></span>
</div>
</li>
<?php $co++; ?>
<?php endforeach; ?>
</ul>
<?php if (isset($pagination)) echo $pagination; ?>
<?php else: ?>
<p><?php echo tm('No posts found', 'forum') ?></p>
<?php endif; ?>