<?php
// Language translations
$lang = [
    'en' => [
        'homepage' => 'Home page',
        'products' => 'Products',
        'title' => 'KG Kitchenware',
        'review_title' => 'Title',
        'subtitle' => 'Innovative Kitchen Tools',
        'description' => 'We bring practicality and style to your kitchen! KG Kitchenware offers high-quality kitchen gadgets that make everyday life smoother and cooking more enjoyable.',
        'login' => 'Log in',
        'logout' => 'Log out',
        'register' => 'Register',
        'latest_products' => 'Latest Products',
        'no_products' => 'No new products available at the moment.',
        'price' => 'Price',
        'product_image' => 'Product Image',
        'quantity' => 'Quantity',
        'add_to_cart' => 'Add to cart',
        'give_review' => 'Give a Review',
        'read_reviews' => 'Read Reviews',
        'latest_reviews' => 'Latest Reviews',
        'review' => 'Review',
        'stars' => 'Stars',
        'publish_date' => 'Publish Date ',
        'no_reviews' => 'No reviews available at the moment.',
        'leaveReview' => 'Leave a review.',
    ],
    'fi' => [
        'homepage' => 'Etusivu',
        'products' => 'Tuotteet',
        'title' => 'KG Keittiövälineet',
        'review_title' => 'Otsikko',
        'subtitle' => 'Innovatiiviset Keittiövälineet',
        'description' => 'Tuomme keittiöösi käytännöllisyyttä ja tyyliä! KG Keittiökalusteet tarjoaa laadukkaita keittiögadgeteja, jotka tekevät arjesta sujuvampaa ja ruoanlaitosta nautinnollisempaa.',
        'login' => 'Kirjaudu sisään',
        'logout' => 'Kirjaudu ulos',
        'register' => 'Rekisteröidy',
        'latest_products' => 'Uusimmat tuotteet',
        'no_products' => 'Ei uusia tuotteita saatavilla tällä hetkellä.',
        'price' => 'Hinta',
        'product_image' => 'Tuotekuva',
        'quantity' => 'Määrä',
        'add_to_cart' => 'Lisää ostoskoriin',
        'give_review' => 'Anna arvostelu',
        'read_reviews' => 'Lue arvosteluja',
        'latest_reviews' => 'Uusimmat arvostelut',
        'review' => 'Arvostelu',
        'stars' => 'Tähtiä',
        'publish_date' => 'Julkaisupäivämäärä ',
        'no_reviews' => 'Ei arvosteluja saatavilla tällä hetkellä.',
        'leaveReview' => 'Jätä arvostelu.',
    ]
];

// Current language selection (default to 'fi' if not specified)
$active_language = 'fi'; // Change dynamically based on user selection
$current_lang = $lang[$active_language];

// Function to fetch translations
function t($key)
{
    global $current_lang;
    return $current_lang[$key] ?? $key; // Return key itself if translation is missing
}
?>