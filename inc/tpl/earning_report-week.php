<?php
$wixbu_from = date( 'Y-m-d', strtotime( '7 days ago' ) );

$from = str_replace( '-', '', $wixbu_from );
$to = str_replace( '-', '', $wixbu_to );

$income_data = Wixbu_Instructors::query_umeta_table(
	"`meta_key` BETWEEN 'subs-{$from}0' AND 'subs-{$to}z' OR " .
	"`meta_key` BETWEEN 'sale-{$from}0' AND 'sale-{$to}z'"
);

$data = [
	[
		'start'   => strtotime( '6 days ago' ),
		'end'     => strtotime( '5 days ago' ),
		'numbers' => [ 0, 0, 0 ],
	],
	[
		'start'   => strtotime( '4 days ago' ),
		'end'     => strtotime( '3 days ago' ),
		'numbers' => [ 0, 0, 0 ],
	],
	[
		'start'   => strtotime( '2 days ago' ),
		'end'     => strtotime( '1 days ago' ),
		'numbers' => [ 0, 0, 0 ],
	],
	[
		'start'   => time(),
		'end'     => strtotime( 'today' ),
		'numbers' => [ 0, 0, 0 ],
	],
];

foreach ( $data as $k => $d ) {
	$s = $d['start'];
	$e = $d['end'];
	$data[$k]['label'] = date( 'D', $s ) . ' - ' . date( 'D', $e );
	$data[$k]['start'] = date( 'Ymd', $s );
	$data[$k]['end'] = date( 'Ymd', $e );
	$data[ $k ]['label'] = strtoupper( $data[ $k ]['label'] );
}

$data[ 3 ]['label'] = strtoupper( date( 'D' ) );

foreach ( $income_data as $inc_d ) {
	$k = explode( '::', $inc_d->key )[0];
	$k = explode( '-', $k );

	$amt = explode( '::', $inc_d->value )[0];
	foreach ( $data as &$d ) {
		if ( $d['start'] > $k[1] ) {
			$d['numbers'][ $data_maps[ $k[0] ] ] += $amt;
			break;
		}
	}
}

Wixbu_Instructors_Public::output_chart( $data );
