<?php
get_header();
?>

	<main id="primary" class="site-main single-doctor">
		<?php while ( have_posts() ) : the_post(); ?>

		<div class="doctor-photo">
			<?php the_post_thumbnail( 'medium' ); ?>
		</div>

		<div class="doctor-info">

			<h2><?php the_title(); ?></h2>

			<div class="excerpt"><?php the_excerpt(); ?></div>

			<div class="content"><?php the_content(); ?></div>

			<div class="meta">
				<?php
					$experience = get_post_meta( get_the_ID(), 'cpt_dctrs_experience', true );
					$price = get_post_meta( get_the_ID(), 'cpt_dctrs_price', true );
					$rating = get_post_meta( get_the_ID(), 'cpt_dctrs_rating', true );
				?>
				<ul>
				    <li>Опыт (лет): <?php echo ! empty( $experience ) ? esc_html( $experience ) : 'без стажа'; ?></li>
				    <li>Цена от: <?php echo ! empty( $price ) ? esc_html( $price ) : 'не указана'; ?></li>
				    <li>Рейтинг: <?php echo ! empty( $rating ) ? str_repeat( '<span class="dashicons dashicons-star-filled"></span>', absint( $rating ) ) : 'без рейтинга'; ?></li>
				</ul>
			</div>

			<?php
				$specialization = wp_get_post_terms( get_the_ID(), 'specialization', array( 'fields' => 'names' ) );
				$cities = wp_get_post_terms( get_the_ID(), 'cities', array( 'fields' => 'names' ) );
			?>
			<div class="specs">
				Специализация: <?php echo ( count( $specialization ) > 0 ) ? esc_html( implode( ', ', $specialization ) ) : '&mdash;'; ?>
			</div>
			<div class="cities">
				<?php printf( 'Город%s:', ( count( $cities ) > 1 ) ? 'а' : '' ); ?>
				<?php echo ( count( $cities ) > 1 ) ? esc_html( implode( ', ', $cities ) ) : '&mdash;'; ?>
			</div>
		</div>

		<?php endwhile; // End of the loop. ?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
