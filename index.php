<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 06/12/2018
 * Time: 15:19
 */

?>
<!DOCTYPE html>
<html>
    <head>
        <title>HomePage - Lapiary</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/styles.css" />
    </head>
    <body>
        <header>
            <div class="strato1">
                <div class="strato2">
                    <h1>Lapiary _ </h1>
                    <h2>Homepage</h2>
                </div>
            </div>
        </header>
        <div class="container" id="menu">
            <h2 class="benvenuto">Benvenuto, scegli una sezione</h2>
            <section class="sezioni">
                <article>
                    <a id="utenti" href="utenti.php">
                        <img src="img/address_book.png" alt="contatti" />
                        <p class="sezione_descr">Lista Utenti</p>
                    </a>
                </article>
                <article>
                    <a id="libri" href="libri.php">
                        <img src="img/book.png" alt="libro" />
                        <p class="sezione_descr">Lista Libri</p>
                    </a>
                </article>
                <article>
                    <a id="prestiti" href="prestiti.php">
                        <img src="img/prestiti.png" alt="prestiti"/>
                        <p class="sezione_descr">Prestiti</p>
                    </a>
                </article>
            </section>
        </div>
        <footer>
            <p class="piccolino">Lapiary - 2018</p>
        </footer>
    </body>
</html>
