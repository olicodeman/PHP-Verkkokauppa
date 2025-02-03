<?php
// Language translations
$lang = [
    'en' => [
        'homepage' => 'Home page',
        'products' => 'Products',
        'title' => 'KG Kitchenware',
        'profile' => 'Profile',
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
        'allKategories' => 'All kategories.',
        'WelcomeProducts' => 'Welcome to Kitchen Gadget product page! Check out products and purchase!',
        'Search' => 'Search',
        'SearchProduct' => 'Search for a product',
        'Stock' => 'In stock',
        'emptystock' => 'Empty stock, we will fill it as fast as possble!',
        'noAccount' => 'No account?',
        'YesAccount' => 'Do you already have an account?',
        'PressHere' => 'Press here',
        'ToCreate' => ',To create an account!',
        'Login' => 'Login',
        'Password' => 'Password',
        'CheckPassword' => 'Confirm Password',
        'LogIn' => 'Login',
        'FirstName' => 'First name',
        'LastName' => 'Last name',
        'Email' => 'Email',
        'User' => 'Username',
        'Adress' => 'Adress',
        'PhoneNmb' => 'Phone number',
        'ChooseProductS' => 'Choose product from menu',
        'ChooseProduct' => '-- Choose product --',
        'Comment' => 'Comment',
        'SavedReview' => 'Your review has been saved!',
        'review_success' => 'Review saved successfully! Thank you for your feedback!',
        'review_error' => 'Error saving review: ',
        'db_query_error' => 'Database query error: ',
        'AllReviews' => 'All reviews ',
        'FilterStar' => 'Filter reviews according to star rating: ',
        'FilterProduct' => 'Filter according to product',
        'AllProducts' => 'All products',
        'writer' => 'Writer',
        'date' => 'Date',

        
    ],
    'fi' => [
        'homepage' => 'Etusivu',
        'products' => 'Tuotteet',
        'title' => 'KG Keittiövälineet',
        'profile' => 'Profiili',
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
        'allKategories' => 'Kaikki kategoriat.',
        'WelcomeProducts' => 'Tervetuloa Kitchen Gadget tuote sivulle! Katsaise tuotteita ja osta!',
        'Search' => 'Hae',
        'SearchProduct' => 'Hae tuotteita',
        'Stock' => 'Varastossa',
        'emptystock' => 'Varasto tyhjä, täytämme mahdollisimman pian!',
        'noAccount' => 'Eikö ole vielä tiliä?',
        'YesAccount' => 'Onko sinulla jo tili?',
        'PressHere' => 'Paina tästä',
        'ToCreate' => ', jos haluat luoda tilin!',
        'Login' => 'Käyttäjänimi',
        'Password' => 'Salasana',
        'CheckPassword' => 'Tarkista salasana',
        'LogIn' => 'Kirjaudu',
        'FirstName' => 'Etunimi',
        'LastName' => 'Sukunimi',
        'Email' => 'Sähköposti',
        'User' => 'Käyttäjänimi',
        'Adress' => 'Osoite',
        'PhoneNmb' => 'Puhelinnumero',
        'ChooseProductS' => 'Valitse tuote valikosta',
        'ChooseProduct' => '-- Valitse tuote --',
        'Comment' => 'Kommentti',
        'SavedReview' => 'Arvostelusi tallennettu!',
        'review_success' => 'Arvostelu tallennettu onnistuneesti! Kiitos palautteesta!',
        'review_error' => 'Virhe tallentaessa arvostelua: ',
        'db_query_error' => 'Virhe tietokantakyselyssä: ',
        'AllReviews' => 'Kaikki arvostelut ',
        'FilterStar' => 'Suodata arvosteluja tähtiarvostelun mukaan: ',
        'FilterProduct' => 'Suodata tuotteen mukaan: ',
        'AllProducts' => 'Kaikki tuotteet',
        'writer' => 'Kirjoittaja',
        'date' => 'Päivämäärä',

        
    ]
];
$active_language = $_SESSION['lang'] ?? 'fi'; // Default to 'fi' if not set
$current_lang = $lang[$active_language] ?? $lang['fi']; // Fallback to 'fi'

// Function to fetch translations
function t($key)
{
    global $current_lang;
    if (!isset($current_lang[$key])) {
        error_log("Missing translation for key: " . $key);
        return $key;
    }
    return $current_lang[$key];
}
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $lang)) {
    $_SESSION['lang'] = $_GET['lang'];
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh page
    exit;
}

?>