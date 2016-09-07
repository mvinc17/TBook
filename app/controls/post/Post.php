<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 8:42 PM
 */
class DisplayPost
{
	public function view($post) {
		?>
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class="thumbnail post-card" data-relevance="<?php echo $post->relevance; ?>">
				<div class="card-img" style="background-image: url(<?php if($post->image) { echo "/media/".$post->image; } else { echo '/media/'.$post->posterProfile->coverPic; } ?>)"></div>
				<?php
				if($post->image) {
					?><!--<img src="/media/<?php echo $post->image; ?>">--><?php
				} else {
					?><!--<img src="<?php echo $post->posterProfile->coverPic; ?>">--><?php
				}
				?>
				<div class="caption">
					<h4><a href="/user/profile/<?php echo $post->posterProfile->username; ?>">
							<?php echo $post->posterProfile->name; ?>
						</a>
						posted an update
					</h4>
					<p class="post-parent">
						<?php
						if($post->group) {
							?>
							in <a href="/group/<?php echo $post->group->slug; ?>"><?php echo $post->group->name; ?></a>
							<?php
						} else if($post->albumID) {
							?>
							in <a href="/photos/album/<?php echo $post->albumID; ?>"><?php echo $post->albumName; ?></a>
							<?php
						}
						?>
					</p>
					<h6><?php echo timeify($post->time); ?>
					<?php
					if(count($post->tagged) > 0) {
						?>
						with <?php foreach($post->tagged as $index=>$tagged) {
								echo '<a href="/user/profile/'.$tagged->username.'">'.$tagged->name."</a> ";
								if($index != (count($post->tagged) -1))
								{
									echo ", ";
								} else {

								}
							} ?>
						<?php
					}
					?>
					</h6>
					<p>
						<?php echo $post->content; ?>
					</p>
					<div class="clearfix">
						<?php
						if(in_array(Profile::GetLoggedIn(), $post->likes)) {
							?><a href="#" data-post-id="<?php echo $post->id; ?>" class="btn btn-success btn-xs pull-right already-liked like-btn" role="button" data-toggle="tooptip" title="You have already liked this"><i class="fa fa-thumbs-up"></i> <?php echo count($post->likes); ?></a><?php
						} else {
							?><a href="#" data-post-id="<?php echo $post->id; ?>" class="btn btn-success btn-xs pull-right like-btn" role="button"><i class="fa fa-thumbs-up"></i> <?php echo count($post->likes); ?></a><?php
						}

						if($post->posterProfile->username == Session::GetUsername()) {
							?><a href="#" data-post-id="<?php echo $post->id; ?>" class="btn btn-danger btn-xs pull-right delete-post-btn" role="button"><i class="fa fa-trash"></i></a><?php
						}
						?>
					</div>
					<br/>
					<p class="text-left"><?php echo count($post->comments); ?> <?php echo pluralise("comment", count($post->comments)); ?></p>
					<table class="table table-striped">
						<?php
							foreach($post->comments as $comment) {
								?>
								<tr>
									<td><a href="/user/profile/<?php echo $comment->poster->username; ?>"><?php echo $comment->poster->name; ?></a>: <?php echo $comment->content; ?></td>
								</tr>
								<?php
							}
						?>
					</table>
					<form class="comment-form form-horizontal" data-postID="<?php echo $post->id; ?>" action="#" method="POST">
						<input type="hidden" name="postID" value="<?php echo $post->id; ?>">
						<div class="form-group form-group-sm">
							<input type="text" class="form-control" name="content" placeholder="Comment @<?php echo $post->posterProfile->username; ?>...">
						</div>
					</form>

				</div>
			</div>
		</div>
		<?php
	}
}