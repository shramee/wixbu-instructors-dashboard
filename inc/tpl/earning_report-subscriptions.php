<?php
$subscriptions_data = [
	(object) [
		'key'   => 'subs-20180502::james::gold',
		'value' => '20::19::Gold',
	],
	(object) [
		'key'   => 'subs-20180502::lily::diamond',
		'value' => '25::23.75::Diamond',
	],
	(object) [
		'key'   => 'subs-20180502::jake::gold',
		'value' => '20::19::Gold',
	],
	(object) [
		'key'   => 'subs-20180502::harry::gold',
		'value' => '20::19::Gold',
	],
	(object) [
		'key'   => 'subs-20180502::amy::diamond',
		'value' => '25::23.75::Diamond',
	],
	(object) [
		'key'   => 'subs-20180502::thom::diamond',
		'value' => '25::23.75::Diamond',
	],
	(object) [
		'key'   => 'subs-20180502::joost::diamond',
		'value' => '25::23.75::Diamond',
	],
];


$numbers_data = [
	'# Sales'    => [
		'value' => 0,
		'help'  => sprintf( __( '# sales during selected period. To learn more please %s click here %s.', WXBIN ), '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
	'Net Income' => [
		'value' => [ 0 ],
		'help'  => sprintf( __( 'Amount earned after legal and Stripe fees. To learn more please %s click here %s.', WXBIN ), '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
	'Your share' => [
		'value' => [ 0 ],
		'help'  => sprintf( __( '%s of net income. To learn more please %s click here %s.', WXBIN ), '70%', '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
];

$table_data = [];

if ( ! empty( $_GET['wer_record'] ) ) {
	// @TODO Use $_GET['wer_record'] to grab user meta
	$sale_meta = array_values( array_filter( $subscriptions_data, function ( $row ) {
		return $row->key === $_GET['wer_record'];
	} ) )[0]->value;

	$k     = explode( '::', $_GET['wer_record'] );
	$datum = explode( '::', $sale_meta );

	$date       = str_replace( 'subs-', '', $k[0] );
	$date       = substr( $date, 6, 2 ) . '-' . substr( $date, 4, 2 ) . '-' . substr( $date, 2, 2 );
	$table_data = [
		[ '<p class="date">Date</p>', 'Student', 'Membership name', 'Price Paid', 'Net Income', 'Your Share', ],
		[ $date, "<span class='futura'>$k[1]</span>", $datum[2], [ $datum[0] ], [ $datum[1] ], [ .7 * $datum[1] ], ],
	];

	?>
	<div class="llms-form-field">
		<a href="#" onclick="window.history.back()" class="futura-li">
			<span class="fa fa-chevron-left"></span>
			Back
		</a>
		<br><br>
	</div>

	<?php include 'earning-report-render-table.php'; ?>

	<style>
		#wixbu-earnings-report .wer-table-wrap .lifterlms-price,
		#wixbu-earnings-report .wer-table-wrap th,
		#wixbu-earnings-report .wer-table-wrap td {
			text-align: center;
		}

		#wixbu-earnings-report .wer-time-range {
			display: none;
		}
	</style>
	<?php
} else {

	if ( ! empty( $_GET['wer_course'] ) ) {
		// @TODO Use $_GET['wer_course'] to only grab rows for specified course sales
		$subscriptions_data = array_filter( $subscriptions_data, function ( $row ) {
			return false !== strpos( $row->key, $_GET['wer_course'] );
		} );

		foreach ( $subscriptions_data as $row ) {
			$k            = explode( '::', $row->key );
			$datum        = explode( '::', $row->value );
			$table_data[] = [
				'<a href="?tab=subscriptions&wer_record=' . $row->key . '" class="futura">' . $k[1] . '</a>',
				1,
				[ $datum[1] ],
				[ .7 * $datum[1] ],
			];
			$numbers_data['# Sales']['value'] ++;
			$numbers_data['Net Income']['value'][] = $datum[1];
			$numbers_data['Your share']['value'][] = .7 * $datum[1];
		}

		$table_header = [ [ 'Students', '# Sales', 'Net Income', 'Your Share', ] ];
		$table_footer = [ [ '', '', '<b class="lifterlms-price">TOTAL</b>', $numbers_data['Your share']['value'], ] ];

		?>
		<div class="llms-form-field">
			<a href="#" onclick="window.history.back()" class="futura">
				<span class="fa fa-chevron-left"></span>
				Back
			</a>
			<br><br>
		</div>

		<?php
	} else {
		// @TODO Grab rows for course sales

		foreach ( $subscriptions_data as $row ) {
			$k     = explode( '::', $row->key );
			$datum = explode( '::', $row->value );
			if ( ! isset( $table_data[ $k[2] ] ) ) {
				$table_data[ $k[2] ] = [
					'<a href="' . add_query_arg( 'wer_course', $k[2] ) . '" class="futura">' . $datum[2] . '</a>',
					0,
					[],
					[],
				];
			}
			$table_data[ $k[2] ][1] ++;
			$table_data[ $k[2] ][2][] = $datum[1];
			$table_data[ $k[2] ][3][] = .7 * $datum[1];
			$numbers_data['# Sales']['value'] ++;
			$numbers_data['Net Income']['value'][] = $datum[1];
			$numbers_data['Your share']['value'][] = .7 * $datum[1];
		}

		$table_header = [ [ 'Membership', '# Sales', 'Net Income', 'Your Share', ] ];
		$table_footer = [ [ '', '', '<b class="lifterlms-price">TOTAL</b>', $numbers_data['Your share']['value'], ] ];

	}

	if ( $table_data ) {
		$table_data = array_merge(
			$table_header,
			$table_data,
			$table_footer
		);
	}
	?>
	<div class="last-row-last-cols-overline">
		<?php
		include 'earning-report-render-numbers.php';
		include 'earning-report-render-table.php';
		?>
		<h5 class="llms-form-field wer-accurate-info">
			<span class="fa fa-asterisk"></span>
			<?php
			printf( __( 'For more accurate information about your earnings, please visit your %s Stripe account %s.', WXBIN ), '<a href="https://dashboard.stripe.com/payments">', '</a>' )
			?>
		</h5>
	</div>
	<?php

}