<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package doctors
 */

get_header();
?>

	<main id="doctors" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1>Доктора</h1>
			</header><!-- .page-header -->

			<nav class="filters">
				<form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'doctors' ) ); ?>">
					<div class="city-filter">
						<?php
							$selected = isset( $_GET['city'] ) ? absint( $_GET['city'] ) : '';

					    	wp_dropdown_categories(
					    		array(
									'show_option_all'	=> __( 'Показать все города', 'cpt-doctors' ),
									'taxonomy'			=> 'cities',
									'name'				=> 'city',
									'orderby'			=> 'name',
									'selected'			=> $selected,
									'hide_if_empty'		=> true,
									'value_field'		=> 'ID'
					         	)
					    	);
					    ?>
					</div>
					<div class="specialization-filter">
						<?php
							$selected = isset( $_GET['specialization'] ) ? absint( $_GET['specialization'] ) : '';

					    	wp_dropdown_categories(
					    		array(
									'show_option_all'	=> __( 'Показать все специализации', 'cpt-doctors' ),
									'taxonomy'			=> 'specialization',
									'name'				=> 'specialization',
									'orderby'			=> 'name',
									'selected'			=> $selected,
									'hide_if_empty'		=> true,
									'hierarchical'		=> true,
									'value_field'		=> 'ID'
					         	)
					    	);
					    ?>
					</div>
					<div class="sortby">
						<select name="sort">
							<option value="0">Сортировать по:</option>
							<option value="rating_asc" <?php selected( $_GET['sort'] ?? '', 'rating_asc' ); ?>>рейтингу &uparrow;</option>
							<option value="rating_desc" <?php selected( $_GET['sort'] ?? '', 'rating_desc' ); ?>>рейтингу &downarrow;</option>
							<option value="price_asc" <?php selected( $_GET['sort'] ?? '', 'price_asc' ); ?>>цене &uparrow;</option>
							<option value="price_desc" <?php selected( $_GET['sort'] ?? '', 'price_desc' ); ?>>цене &downarrow;</option>
	 						<option value="exp_asc" <?php selected( $_GET['sort'] ?? '', 'exp_asc' ); ?>>стажу &uparrow;</option>
	 						<option value="exp_desc" <?php selected( $_GET['sort'] ?? '', 'exp_desc' ); ?>>стажу &downarrow;</option>
						</select>
					</div>
					<input type="hidden" name="paged" value="1" />
					<button type="submit">Фильтровать</button>
				</form>
			</nav>
			<div class="doctors-list">
			<?php while ( have_posts() ) : the_post(); ?>
				<div id="doctor-<?php echo esc_html( get_the_ID() ); ?>" class="doctor-card">
					
					<div class="doctor-thumbnail">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
					<div class="doctor-info">
						<div class="doctor-name"><?php the_title(); ?></div>
						<?php
							$specialization = wp_get_post_terms( get_the_ID(), 'specialization', array( 'fields' => 'names' ) );
							$max_spec_items = apply_filters( 'sha_spec_max_items', 2 );
							$specialization = array_slice( $specialization, 0, $max_spec_items );
						?>
						<div class="doctor-specialisation">
							Специализация: <?php echo ( count( $specialization ) > 0 ) ? esc_html( implode( ', ', $specialization ) ) : '&mdash;'; ?>
						</div>
						<?php
							$experience = get_post_meta( get_the_ID(), 'cpt_dctrs_experience', true );
							$price = get_post_meta( get_the_ID(), 'cpt_dctrs_price', true );
							$rating = get_post_meta( get_the_ID(), 'cpt_dctrs_rating', true );
						?>
						<div class="doctor-experience">
							Стаж (лет): <?php echo ! empty( $experience ) ? esc_html( $experience ) : 'без стажа'; ?>
						</div>
						<div class="doctor-price">
							Цена от: <?php echo ! empty( $price ) ? esc_html( $price ) : 'не указана'; ?>
						</div>
						<div class="doctor-rating">
							Рейтинг: <?php echo ! empty( $rating ) ? str_repeat( '<span class="dashicons dashicons-star-filled"></span>', absint( $rating ) ) : 'без рейтинга'; ?>
						</div>
						<a href="<?php the_permalink(); ?>" target="blank">Подробно о враче</a>
					</div>
				</div>

			<?php endwhile; ?>

			<?php
			the_posts_pagination(
				array(
			        'prev_text' => __( '&larr;', 'cpt-doctors' ),
			        'next_text' => __( '&rarr;', 'cpt-doctors' ),
		    	)
			);

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
