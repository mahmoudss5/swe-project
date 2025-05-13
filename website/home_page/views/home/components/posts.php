<?php if (isset($_SESSION['user_id'])): ?>
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <div class="alert alert-warning mb-4">
            You cannot interact with posts as an admin.
        </div>
    <?php else: ?>
        <div class="card mb-4 post-card">
            <div class="card-body">
                <form method="POST" action="index.php?action=createPost">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="2" placeholder="What's on your mind?" required></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="post_tag" placeholder="Tags (comma separated)">
                    </div>
                    <button type="submit" class="btn btn-orange">Post</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (empty($posts)): ?>
    <div class="post-card text-center">No posts found.</div>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
    <div class="post-card mb-4" id="post-<?php echo $post['post_id']; ?>">
        <div class="d-flex">
            <div class="flex-grow-1">
                <h5 class="mb-2 post-content" id="post-content-<?php echo $post['post_id']; ?>"><?php echo htmlspecialchars($post['content']); ?></h5>
                <form class="edit-post-form" id="edit-post-form-<?php echo $post['post_id']; ?>" method="POST" action="index.php?action=editPost" style="display:none;">
                    <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                    <textarea name="content" class="form-control mb-2" rows="2"><?php echo htmlspecialchars($post['content']); ?></textarea>
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                </form>
                <div class="d-flex flex-wrap mb-2">
                    <?php foreach (explode(',', $post['post_tag']) as $tag): ?>
                        <span class="tag me-1"><?php echo htmlspecialchars(trim($tag)); ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <span>Posted by</span> <span class="fw-bold"><?php echo htmlspecialchars($post['user_name']); ?></span>
                        <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $post['user_id']): ?>
                            <?php if (!$post['is_following']): ?>
                                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <div class="alert alert-warning mb-2 p-2">You cannot interact with posts as an admin.</div>
                                <?php else: ?>
                                    <form method="POST" action="index.php?action=followUser" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $post['user_id']; ?>">
                                        <button type="submit" class="btn btn-outline-primary btn-sm ms-2 follow-btn">Follow</button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <button type="button" class="btn btn-success btn-sm ms-2" disabled>Following</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm report-post-btn" data-post-id="<?php echo $post['post_id']; ?>">Report</button>
                        <?php
                        $reportReasons = $lang === 'ar'
                            ? [
                                '' => 'اختر السبب',
                                'offensive' => 'محتوى مسيء',
                                'spam' => 'سبام',
                                'off-topic' => 'خارج الموضوع',
                                'other' => 'أخرى'
                            ]
                            : [
                                '' => 'Select reason',
                                'offensive' => 'Offensive Content',
                                'spam' => 'Spam',
                                'off-topic' => 'Off-topic',
                                'other' => 'Other'
                            ];
                        ?>
                        <form class="report-post-form mt-2" id="report-post-form-<?php echo $post['post_id']; ?>" method="POST" action="index.php?action=reportPost" style="display:none;">
                            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                            <select name="reason" class="form-select form-select-sm mb-2" required>
                                <?php foreach ($reportReasons as $val => $label): ?>
                                    <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-danger btn-sm"><?php echo $lang === 'ar' ? 'إبلاغ' : 'Report'; ?></button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-report-post"><?php echo $lang === 'ar' ? 'إلغاء' : 'Cancel'; ?></button>
                        </form>
                        <?php // Debug
                        echo '<!-- user_points: ' . ($_SESSION['user_points'] ?? 'NOT SET') . ' -->';
                        ?>
                        <?php
                        $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id'];
                        $isAdmin = isset($_SESSION['user_id']) && ($_SESSION['user_id'] == 1 || ($_SESSION['is_admin'] ?? false));
                        $hasPoints = isset($_SESSION['user_points']) && $_SESSION['user_points'] >= 500;
                        if ($isOwner || $isAdmin || $hasPoints): ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm edit-post-btn" data-post-id="<?php echo $post['post_id']; ?>" data-owner-id="<?php echo $post['user_id']; ?>">Edit</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="action-buttons align-items-center mt-2">
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <div class="alert alert-warning mb-2 p-2">You cannot interact with posts as an admin.</div>
                    <?php else: ?>
                        <form method="POST" action="index.php?action=votePost" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                            <input type="hidden" name="vote_type" value="up">
                            <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                            <button type="submit" class="btn btn-outline-success btn-sm">
                                ▲ Upvote <span class="badge bg-success ms-1 vote-count"><?php echo $post['up_vote']; ?></span>
                            </button>
                        </form>
                        <form method="POST" action="index.php?action=votePost" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                            <input type="hidden" name="vote_type" value="down">
                            <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                ▼ Downvote <span class="badge bg-danger ms-1 vote-count"><?php echo $post['down_vote']; ?></span>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <h6>Comments</h6>
                    <?php if (empty($post['comments'])): ?>
                        <div class="text-muted">No comments yet.</div>
                    <?php else: ?>
                        <?php foreach ($post['comments'] as $comment): ?>
                            <div class="mb-2 p-2 bg-light rounded d-flex justify-content-between align-items-center" id="comment-<?php echo $comment['comment_id']; ?>">
                                <div>
                                    <span class="comment-content" id="comment-content-<?php echo $comment['comment_id']; ?>">
                                        <strong><?php echo htmlspecialchars($comment['user_name']); ?>:</strong>
                                        <?php echo htmlspecialchars($comment['content']); ?>
                                        <span class="text-muted small ms-2"><?php echo date('M j, Y', strtotime($comment['created_at'])); ?></span>
                                    </span>
                                    <form class="edit-comment-form" id="edit-comment-form-<?php echo $comment['comment_id']; ?>" method="POST" action="index.php?action=editComment" style="display:none;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                        <textarea name="content" class="form-control mb-2" rows="2"><?php echo htmlspecialchars($comment['content']); ?></textarea>
                                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                                        <button type="button" class="btn btn-secondary btn-sm cancel-edit-comment">Cancel</button>
                                    </form>
                                    <div class="d-flex align-items-center mt-1">
                                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                            <div class="alert alert-warning mb-2 p-2">You cannot interact with posts as an admin.</div>
                                        <?php else: ?>
                                            <form method="POST" action="index.php?action=voteComment" style="display:inline;">
                                                <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                                <input type="hidden" name="vote_type" value="up">
                                                <button type="submit" class="btn btn-outline-success btn-sm">
                                                    ▲ <span class="badge bg-success ms-1 vote-count"><?php echo $comment['up_vote']; ?></span>
                                                </button>
                                            </form>
                                            <form method="POST" action="index.php?action=voteComment" style="display:inline; margin-left:4px;">
                                                <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                                <input type="hidden" name="vote_type" value="down">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    ▼ <span class="badge bg-danger ms-1 vote-count"><?php echo $comment['down_vote']; ?></span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-danger btn-sm report-comment-btn" data-comment-id="<?php echo $comment['comment_id']; ?>">Report</button>
                                    <form class="report-comment-form mt-2" id="report-comment-form-<?php echo $comment['comment_id']; ?>" method="POST" action="index.php?action=reportComment" style="display:none;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                        <select name="reason" class="form-select form-select-sm mb-2" required>
                                            <?php foreach ($reportReasons as $val => $label): ?>
                                                <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-danger btn-sm"><?php echo $lang === 'ar' ? 'إبلاغ' : 'Report'; ?></button>
                                        <button type="button" class="btn btn-secondary btn-sm cancel-report-comment"><?php echo $lang === 'ar' ? 'إلغاء' : 'Cancel'; ?></button>
                                    </form>
                                    <?php
                                    $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id'];
                                    $isAdmin = isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1;
                                    $hasPoints = isset($_SESSION['user_points']) && $_SESSION['user_points'] >= 500;
                                    if ($isOwner || $isAdmin || $hasPoints): ?>
                                        <button type="button" class="btn btn-outline-secondary btn-sm ms-1 edit-comment-btn" data-comment-id="<?php echo $comment['comment_id']; ?>">Edit</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <div class="alert alert-warning mb-2 p-2">You cannot interact with posts as an admin.</div>
                        <?php else: ?>
                            <form class="mt-2" method="POST" action="index.php?action=createComment">
                                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                                <div class="input-group">
                                    <input type="text" name="content" class="form-control" placeholder="Add a comment..." required>
                                    <button class="btn btn-orange" type="submit">Comment</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
const isAdmin = <?php echo isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'true' : 'false'; ?>;
if (isAdmin) {
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll(
            'form[action*=\"createPost\"], form[action*=\"createComment\"], form[action*=\"votePost\"], form[action*=\"voteComment\"], form[action*=\"followUser\"]'
        ).forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('You cannot interact with posts as an admin.');
            });
        });
        // Also intercept edit buttons if needed
        document.querySelectorAll('.edit-post-btn, .edit-comment-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('You cannot interact with posts as an admin.');
            });
        });
    });
}

document.querySelectorAll('.edit-post-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const postId = this.getAttribute('data-post-id');
        const ownerId = this.getAttribute('data-owner-id');
        const currentUserId = <?php echo json_encode($_SESSION['user_id']); ?>;
        const userPoints = <?php echo json_encode($_SESSION['user_points'] ?? 0); ?>;
        const isAdmin = currentUserId == 1;
        if (currentUserId == ownerId || userPoints >= 500 || isAdmin) {
            document.getElementById('post-content-' + postId).style.display = 'none';
            document.getElementById('edit-post-form-' + postId).style.display = 'block';
        } else {
            alert('this privilege is not allowed for you');
        }
    });
});
document.querySelectorAll('.cancel-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('.edit-post-form');
        form.style.display = 'none';
        const postId = form.querySelector('input[name="post_id"]').value;
        document.getElementById('post-content-' + postId).style.display = 'block';
    });
});
document.querySelectorAll('.edit-comment-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const commentId = this.getAttribute('data-comment-id');
        document.getElementById('comment-content-' + commentId).style.display = 'none';
        document.getElementById('edit-comment-form-' + commentId).style.display = 'block';
    });
});
document.querySelectorAll('.cancel-edit-comment').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('.edit-comment-form');
        form.style.display = 'none';
        const commentId = form.querySelector('input[name="comment_id"]').value;
        document.getElementById('comment-content-' + commentId).style.display = 'block';
    });
});
document.querySelectorAll('.report-post-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const postId = this.getAttribute('data-post-id');
        document.getElementById('report-post-form-' + postId).style.display = 'block';
    });
});
document.querySelectorAll('.cancel-report-post').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('.report-post-form');
        form.style.display = 'none';
    });
});
document.querySelectorAll('.report-comment-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const commentId = this.getAttribute('data-comment-id');
        document.getElementById('report-comment-form-' + commentId).style.display = 'block';
    });
});
document.querySelectorAll('.cancel-report-comment').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('.report-comment-form');
        form.style.display = 'none';
    });
});
</script> 