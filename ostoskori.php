<?php
session_start();

// Tarkista, onko ostoskori olemassa ja onko se tyhjä
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '<p>Ostoskorisi on tyhjä.</p>';
} else {
    echo '<h1>Ostoskorisi</h1>';
    echo '<table>';
    echo '<tr><th>Tuote</th><th>Hinta</th><th>Määrä</th><th>Yhteensä</th></tr>';
    
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $total += $itemTotal;
        echo '<tr>';
        echo '<td>' . htmlspecialchars($item['name']) . '</td>';
        echo '<td>' . htmlspecialchars($item['price']) . ' €</td>';
        echo '<td>' . htmlspecialchars($item['quantity']) . '</td>';
        echo '<td>' . $itemTotal . ' €</td>';
        echo '</tr>';
    }
    echo '<tr><td colspan="3"><strong>Yhteensä:</strong></td><td>' . $total . ' €</td></tr>';
    echo '</table>';
}
?>
