<?php
    // Get all properties
    $properties = get_posts([
        'post_type' => 'property',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);

    $locations = [];

    if ($properties) {
        foreach ($properties as $prop) {
            $loc = get_post_meta($prop->ID, 'eb_location', true);
            if ($loc) {
                // Split by comma and trim spaces
                $parts = array_map('trim', explode(',', $loc));

                // Expected format: [neighborhood, city, state]
                $neighborhood = $parts[0] ?? '';
                $city         = $parts[1] ?? '';
                $state        = $parts[2] ?? '';

                if ($state && $city) {
                    $locations[$state][] = $city;
                }
            }
        }

        // Remove duplicates and sort alphabetically
        foreach ($locations as $state => $cities) {
            $locations[$state] = array_unique($cities);
            sort($locations[$state]);
        }
        ksort($locations);
    }

    $locations = get_property_locations();
?>