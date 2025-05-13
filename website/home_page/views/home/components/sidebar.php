<div class="leaderboard-card mb-4">
    <h5 class="mb-3"><span style="font-size:1.3em;">🏆</span> <?php echo $t['topContributors']; ?></h5>
    <div class="list-group list-group-flush">
        <?php
        $topUsers = $this->db->query("SELECT name, points FROM users ORDER BY points DESC LIMIT 5");
        $rank = 1;
        while ($user = $topUsers->fetch_assoc()): ?>
            <?php
            $rankClass = '';
            if ($rank == 1) $rankClass = 'rank-1';
            elseif ($rank == 2) $rankClass = 'rank-2';
            elseif ($rank == 3) $rankClass = 'rank-3';
            ?>
            <div class="list-group-item d-flex align-items-center">
                <div class="user-rank me-3 <?php echo $rankClass; ?>" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:50%; font-weight:bold;">
                    <?php echo $rank; ?>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold <?php echo $rankClass; ?>" style="font-size:1.1em;">
                        <?php echo htmlspecialchars($user['name']); ?>
                    </div>
                    <div class="text-muted small" style="font-size:0.95em;">
                        <?php echo number_format($user['points']) . ' ' . ($lang === 'ar' ? 'نقطة' : 'points'); ?>
                    </div>
                </div>
            </div>
        <?php $rank++; endwhile; ?>
    </div>
</div>
<style>
.rank-1 { color: #FFD700 !important; }
.rank-2 { color: #C0C0C0 !important; }
.rank-3 { color: #cd7f32 !important; }
</style>
<!-- Popular Tags -->
<div class="leaderboard-card">
    <h5 class="mb-3"><?php echo $t['popularTags']; ?></h5>
    <div class="d-flex flex-wrap gap-2">
        <span class="tag">JavaScript</span>
        <span class="tag">Python</span>
        <span class="tag">Java</span>
        <span class="tag">React</span>
        <span class="tag">Node.js</span>
        <span class="tag">PHP</span>
        <span class="tag">Laravel</span>
        <span class="tag">MySQL</span>
    </div>
</div> 