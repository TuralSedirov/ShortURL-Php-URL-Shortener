<?php
/**
 * Debug Function v1.2.0
 *
 * Author: Your Name
 * Description: A flexible debugging function that prints readable outputs of variables,
 *              supports arrays, handles execution flow, and allows custom formatting.
 *
 * Features:
 * - Prints variables with formatting.
 * - Supports arrays and objects.
 * - Can optionally stop execution after debugging.
 * - Can print separators for readability.
 */

function debug(array $params = []) {
    // Define default parameters
    $defaults = [
        'value' => '',                  // The value to be debugged
        'debug' => true,                 // Enable/disable debugging
        'break_line' => true,            // Add a separator after output
        'die_after_execution' => false,  // Stop script execution after debugging
        'title' => '',                   // Optional title for better organization
        'pre_format' => true,            // Enable <pre> formatting for better readability
    ];

    // Merge provided parameters with defaults
    $params = array_merge($defaults, $params);

    // Execute debugging only if enabled
    if ($params['debug']) {
        // Print an optional title
        if (!empty($params['title'])) {
            echo '<strong>' . htmlspecialchars($params['title']) . '</strong><br>';
        }

        // Display the value with appropriate formatting
        if (is_array($params['value']) || is_object($params['value'])) {
            echo $params['pre_format'] ? '<pre>' : '';
            print_r($params['value']);
            echo $params['pre_format'] ? '</pre>' : '';
        } else {
            echo htmlspecialchars($params['value']) . '<br>';
        }

        // Stop execution if requested
        if ($params['die_after_execution']) {
            die();
        }

        // Print a separator for readability
        if ($params['break_line']) {
            echo '<br>-----------------------------------------<br>';
        }
    }
}
?>