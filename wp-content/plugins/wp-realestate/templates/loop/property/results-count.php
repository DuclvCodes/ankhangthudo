<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="results-count">
	<?php
		if ( $total <= $per_page || -1 === $per_page ) {
			/* translators: %d: total results */
			printf( _n( 'Showing the single result', 'Showing all %d results', $total, 'wp-realestate' ), $total );
		} else {
			$first = ( $per_page * $current ) - $per_page + 1;
			$last  = min( $total, $per_page * $current );
			/* translators: 1: first result 2: last result 3: total results */
			printf( _nx( 'Showing the single result', 'Showing <span class="first">%1$d</span> &ndash; <span class="last">%2$d</span> of %3$d results', $total, 'with first and last result', 'wp-realestate' ), $first, $last, $total );
		}
	?>
</div>