<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$phone = homeo_agent_display_phone($post, 'title', false);
$fax = homeo_agent_display_fax($post, 'title', false);
$email = homeo_agent_display_email($post, 'title', false);
$website = homeo_agent_display_website($post, 'title', false);
$job = homeo_agent_display_job($post, false);

?>
<div class="agent-detail-header top-detail-member">
    <div class="flex">

        <?php if ( has_post_thumbnail() ) { ?>
            <div class="member-thumbnail-wrapper flex-middle justify-content-center">
                <?php homeo_agent_display_image($post,'full'); ?>
                <?php homeo_agent_display_nb_properties($post); ?>
            </div>
        <?php } ?>

        <div class="member-information flex-middle">
            <div class="inner">
                <?php homeo_agent_display_property($post); ?>
                
                <div class="title-wrapper">
                    <?php the_title( '<h1 class="member-title">', '</h1>' ); ?>
                    <?php homeo_agent_display_featured_icon($post); ?>
                </div>
                <?php echo trim($job); ?>
                <?php if ( $fax || $phone || $email || $website ) { ?>
                    
                    <div class="member-metas">
                        <?php echo trim($phone); ?>

                        <?php echo trim($fax); ?>

                        <?php echo trim($email); ?>

                        <?php echo trim($website); ?>
                    </div>
                    
                <?php } ?>

                <?php homeo_agent_display_socials($post); ?>
            </div>
        </div>
    </div>
</div>