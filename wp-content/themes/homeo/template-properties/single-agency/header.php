<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$fax = homeo_agency_display_fax($post, 'no-title', false);
$address = homeo_agency_display_full_location($post, 'no-title', false);
$phone = homeo_agency_display_phone($post, 'no-title', false);
$email = homeo_agency_display_email($post, 'no-title', false);
$website = homeo_agency_display_website($post, 'no-title', false);

?>
<div class="agency-detail-header top-detail-member">
    <div class="flex">

        <?php if ( has_post_thumbnail() ) { ?>
            <div class="member-thumbnail-wrapper flex-middle justify-content-center member-thumbnail-agency">
                <?php homeo_agency_display_image($post,'full'); ?>
                <?php homeo_agency_display_nb_properties($post); ?>
            </div>
        <?php } ?>

        <div class="member-information flex-middle">
            <div class="inner">
                <div class="title-wrapper">
                    <?php the_title( '<h1 class="member-title">', '</h1>' ); ?>
                    <?php homeo_agency_display_featured_icon($post); ?>
                </div>

                <?php if ( $address ) { ?>
                    <?php echo trim($address); ?>
                <?php } ?>

                <?php if ( $fax || $phone || $email || $website ) { ?>
                    
                    <div class="member-metas">
                        <?php echo trim($phone); ?>

                        <?php echo trim($fax); ?>

                        <?php echo trim($email); ?>

                        <?php echo trim($website); ?>
                    </div>
                <?php } ?>

                <?php homeo_agency_display_socials($post); ?>
            </div>
        </div>
    </div>
</div>