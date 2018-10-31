<?php

/**
 * @file
 * Override data for a given groomer.
 *
 * @groomer_type entity
 * @entity_type node
 * @bundle basic_page
 *
 * This is a custom file to handle the reformatting of data before it reaches
 * Drupal templates.
 *
 * Available variables:
 * - $groomer_data
 *    The data coming from the groomer.
 * - $data
 *    The data that will be returned to the Twig template.
 *
 * Re-arrange and assign data to the $data variable here, and that's all you
 * need. The rest will be handled in the Twig Templates.
 */

// Alter the data. Anything found in $data is sent to front-end templates.
$data['completely_reformatted'] = 'Hi! I\'ve been completely reformatted!';

// If $data is never set, anything found in $groomer_data is sent instead.
$groomer_data['entity.harmony'] = 'Applied. Hey there!';
