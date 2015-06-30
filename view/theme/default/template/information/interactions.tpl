<?php echo $header; ?>

<article>

        <?php foreach($recent_interactions as $interaction){ ?>
        <div class="h-entry" id="i<?php echo $interaction['interaction_id']?>">
            <span class="h-card p-author">
            <a class="u-url p-name" href="<?php echo $interaction['author_url']?>">
                <?php if($interaction['author_image']){ ?>
                    <img class="logo u-photo" src="<?php echo $interaction['author_image']?>" alt="<?php echo $interaction['author_name']?>" width="48" />
                <?php } ?>
                <?php echo $interaction['author_name']?>
            </a> 
            </span>

            <?php 
                switch($interaction['interaction_type']){
                case 'like':
                    echo 'liked a';
                    $linkclasses = 'u-like-of';
                    break;
                case 'reply':
                    echo 'commented on a';
                    $linkclasses = 'u-in-reply-to';
                    break;
                case 'repost':
                    echo 'reposted a';
                    $linkclasses = 'u-repost-of';
                    break;
                case 'mention':
                    echo 'mentioned a';
                    $linkclasses = 'x-u-mention-of';
                    break;
                case 'person-mention':
                    echo 'mentioned';
                    $linkclasses = 'x-u-person-mention-of';
                    break;
                case 'tag':
                    echo 'tagged a';
                    $linkclasses = 'x-u-tag-of';
                    break;
                }
            ?>

            <?php if($interaction['post']['post_type']) {?>
                <a class="<?php echo $linkclasses?>" href="<?php echo $interaction['post']['permalink']?>">
                    <?php echo $interaction['post']['post_type']?>
                </a>
            <?php } elseif($interaction['interaction_type'] == 'person-mention') { ?>
                <a class="<?php echo $linkclasses?>" href="<?php echo $interaction['target_url']?>">
                    Me
                </a>
            <?php } else { ?>
                <a class="<?php echo $linkclasses?>" href="<?php echo $interaction['target_url']?>">
                    page
                </a>
            <?php } ?>
        </div>
        <?php } //end foreach whitelist as entry ?>



</article>

<?php echo $footer; ?>