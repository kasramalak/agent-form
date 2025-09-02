<?php
/**
 * Plugin Name: Property Analytics
 * Plugin URI: https://offplanbazaar.ae/
 * Description: A plugin to display property analytics from the Propsearch API.
 * Version: 1.0
 * Author: Jules
 * Author URI: https://offplanbazaar.ae/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function property_analytics_enqueue_scripts() {
    if ( is_singular( 'property' ) ) {
        wp_enqueue_style( 'property-analytics-style', plugin_dir_url( __FILE__ ) . 'css/style.css' );
        wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true );
        wp_enqueue_script( 'property-analytics-script', plugin_dir_url( __FILE__ ) . 'js/main.js', array( 'jquery', 'chart-js' ), null, true );

        wp_localize_script( 'property-analytics-script', 'property_analytics', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'property_analytics_nonce' ),
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'property_analytics_enqueue_scripts' );

function get_property_analytics() {
    check_ajax_referer( 'property_analytics_nonce', 'nonce' );

    $unit = $_POST['unit'];
    $address = $_POST['address'];

    $pfs_code = get_pfs_code( $address );
    if ( ! $pfs_code ) {
        wp_send_json_error( 'Could not get PSL code.' );
    }

    $size_sqm = floatval( $unit['size'] ) * 0.092903; // Convert sqft to sqm
    $bedrooms = intval( $unit['bedrooms'] );
    $segment  = strpos( strtolower( $unit['type'] ), 'apartment' ) !== false ? 2 : 1; // 2 for apartment, 1 for villa

    $valuation_data = get_valuation( $pfs_code, $segment, $size_sqm, $bedrooms );
    if ( ! $valuation_data ) {
        wp_send_json_error( 'Could not get valuation data.' );
    }

    $analytics = array(
        'averagePrices'      => calculate_average_price_for_bedrooms( $valuation_data, $bedrooms ),
        'recentTransactions' => get_recent_transactions( $valuation_data ),
        'averagePriceForType'  => calculate_average_price_for_type( $valuation_data ),
    );

    wp_send_json_success( $analytics );
}
add_action( 'wp_ajax_get_property_analytics', 'get_property_analytics' );
add_action( 'wp_ajax_nopriv_get_property_analytics', 'get_property_analytics' );

function get_pfs_code( $address ) {
    $response = wp_remote_get( 'https://propsearch.ae/api/vista/smart-match?address=' . urlencode( $address ), array(
        'headers' => array(
            'Authorization' => 'Bearer 18|OtD14rmbXTZlPSnMtLlgUBU5hXQmOu44KJvOxb32c1855e61',
        ),
    ) );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( $body['match_status'] === 'Match found' ) {
        return $body['match_psl_code'];
    }

    return false;
}

function get_valuation( $pfs_code, $segment, $size_sqm, $bedrooms ) {
    $url = add_query_arg(
        array(
            'psl_code' => $pfs_code,
            'segment'  => $segment,
            'size_sqm' => $size_sqm,
            'bedrooms' => $bedrooms,
        ),
        'https://propsearch.ae/api/vista/valuations'
    );

    $response = wp_remote_get( $url, array(
        'headers' => array(
            'Authorization' => 'Bearer 18|OtD14rmbXTZlPSnMtLlgUBU5hXQmOu44KJvOxb32c1855e61',
        ),
    ) );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    return json_decode( wp_remote_retrieve_body( $response ), true );
}

function calculate_average_price_for_bedrooms( $valuation_data, $bedrooms ) {
    $sales = $valuation_data['estimate']['sale']['comparables']['government_transactions'];
    $rents = $valuation_data['estimate']['rent']['comparables']['government_transactions'];
    $sale_total = 0;
    $sale_count = 0;
    $rent_total = 0;
    $rent_count = 0;

    foreach ( $sales as $transaction ) {
        if ( intval( $transaction['room_type'] ) === $bedrooms ) {
            $sale_total += $transaction['price_aed'];
            $sale_count++;
        }
    }

    foreach ( $rents as $transaction ) {
        if ( intval( $transaction['room_type'] ) === $bedrooms ) {
            $rent_total += $transaction['price_aed'];
            $rent_count++;
        }
    }

    return array(
        'sale' => $sale_count > 0 ? $sale_total / $sale_count : 0,
        'rent' => $rent_count > 0 ? $rent_total / $rent_count : 0,
    );
}

function get_recent_transactions( $valuation_data ) {
    return array(
        'sales' => array_slice( $valuation_data['estimate']['sale']['comparables']['government_transactions'], 0, 3 ),
        'rents' => array_slice( $valuation_data['estimate']['rent']['comparables']['government_transactions'], 0, 3 ),
    );
}

function calculate_average_price_for_type( $valuation_data ) {
    $sales = $valuation_data['estimate']['sale']['comparables']['government_transactions'];
    $total = 0;

    foreach ( $sales as $transaction ) {
        $total += $transaction['price_aed'];
    }

    return count( $sales ) > 0 ? $total / count( $sales ) : 0;
}
