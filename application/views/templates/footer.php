    </div>
    </main>
    <!-- Modal -->
    <div class="modal fade" id="newsletter" tabindex="-1" role="dialog" aria-labelledby="newsletterLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
          <?= form_open('newsletter', array('id'=> 'newsletterForm')); ?>
            <div class="modal-header">
              <h2 class="modal-title" id="newsletterLabel">Newsletter</h2>
              <span class="close cursor-pointer" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </span>
            </div>
            <div class="modal-body">
              <p>Souhaitez-vous vous inscrire et recevoir les informations parlementaires et les nouvelles de Datan.fr <b>une fois par mois</b> ?</p>
              <input type="email" name="email" required class="form-control" placeholder="Votre email">
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Recevoir la newsletter</button>
            </div>
            <?= form_close() ?>
          <div id="modalSubscription">
            <div class="modal-header">
              <h2 class="modal-title">Félicitations</h2>
              <span class="close cursor-pointer" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </span>
            </div>
            <div class="modal-body">
              <p>Vous serez maintenant informé des actualités politiques de nos députés et un peu du site aussi :)</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
          </div>
          <div id="modalFail">
            <div class="modal-header">
              <h2 class="modal-title">Quelque chose s'est mal passé</h2>
              <span class="close cursor-pointer" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </span>
            </div>
            <div class="modal-body">
              <p>Vous êtes sans doute déjà inscrit !</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <footer>
      <div class="container-fluid footer">
        <div class="container p-0">
          <div class="row row-grid py-3">
            <div class="col-md-4 d-flex flex-column justify-content-center">
              <img class="img-lazy img-fluid" src="<?= asset_url() ?>imgs/placeholder/placeholder-social-media-big.png" width="4500" height="1975" data-src="<?= asset_url(); ?>imgs/datan/logo_baseline_white_transp.png" alt="Logo Datan">
            </div>
            <div class="col-md-4 d-flex flex-column justify-content-center">
              <p>© Datan 2021 - Tous droits réservés</p>
              <p>Nous contacter : <a href="mailto:info@datan.fr" class="no-decoration underline">info@datan.fr</a></p>
              <div class="social-media">
                <a href="https://www.facebook.com/datanFR/" target="_blank" class="no-decoration" rel="noreferrer">
                  <img class="img-lazy" src="<?= asset_url() ?>imgs/placeholder/placeholder-social-media.png" width="542" height="542" data-src="<?= asset_url() ?>imgs/logos/facebook_datan.png" alt="Logo Facebook Datan">
                </a>
                <a href="https://twitter.com/datanFR" target="_blank" class="no-decoration" rel="noreferrer">
                  <img class="img-lazy" src="<?= asset_url() ?>imgs/placeholder/placeholder-social-media.png" width="542" height="542" data-src="<?= asset_url() ?>imgs/logos/twitter_datan.png" alt="Logo Twitter Datan">
                </a>
              </div>
            </div>
            <div class="col-md-4 d-flex flex-column justify-content-center mt-md-0">
              <div class="d-flex flex-row liste justify-content-center">
                <div class="d-flex flex-column mx-1">
                  <p>
                    <a href="<?= base_url(); ?>a-propos" class="no-decoration underline">À propos</a>
                  </p>
                  <p>
                    <a href="<?= base_url(); ?>login" class="no-decoration underline">Connexion</a>
                  </p>
                  <p>
                    <a href="<?= base_url(); ?>statistiques/aide" class="no-decoration underline">Nos statistiques</a>
                  </p>
                </div>
                <div class="d-flex flex-column mx-1">
                  <p>
                    <a href="<?= base_url(); ?>blog" class="no-decoration underline">Actualités</a>
                  </p>
                  <p>
                    <a href="<?= base_url(); ?>mentions-legales" class="no-decoratio underline">Mentions légales</a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid footer-data">
        <div class="container">
          <div class="row py-3">
            <div class="col-md-6 text-center">
              Nos données sont disponibles sur le site <a href="https://www.data.gouv.fr/fr/organizations/datan/" target="_blank" rel="noreferrer">data.gouv.fr</a>
            </div>
            <div class="col-md-6">
              Notre projet est disponible sur
              <a href="https://github.com/datanFR/datan" target="_blank" rel="noreferrer" class="no-decoration underline">
                <img src="<?= asset_url() ?>imgs/logos/GitHub-Mark.png" width="25" height="25" alt="Logo GitHub">
                GitHub
              </a>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <!-- JS files -->
    <script src="<?= asset_url(); ?>js/jquery-3.5.1.min.js"></script>
    <script src="<?= asset_url(); ?>js/jquery-ui.min.js"></script>
    <script src="<?= asset_url(); ?>js/popper.min.js"></script>
    <script type="text/javascript" src="<?= asset_url() ?>js/bootstrap.min.js"></script>
    <?php if (isset($js_to_load_before_datan)) : ?>
      <?php foreach ($js_to_load_before_datan as $file) : ?>
        <script src="<?= asset_url(); ?>js/<?= $file ?>.js"></script>
      <?php endforeach; ?>
    <?php endif; ?>
    <script type="text/javascript" src="<?= asset_url() ?>js/jquery.unveil.min.js"></script>

    <script type="text/javascript">
      var tarteaucitronForceLanguage = "fr";
    </script>
    <script src="https://cdn.jsdelivr.net/gh/AmauriC/tarteaucitron.js@20210329/tarteaucitron.min.js"></script>
    <script type="text/javascript">
      tarteaucitron.init({
        "privacyUrl": "<?= base_url() ?>mentions-legales",
        /* Privacy policy url */

        "hashtag": "#tarteaucitron",
        /* Open the panel with this hashtag */
        "cookieName": "tarteaucitron",
        /* Cookie name */

        "orientation": "bottom",
        /* Banner position (top - bottom - middle) */
        "showAlertSmall": false,
        /* Show the small banner on bottom right */
        "cookieslist": true,
        /* Show the cookie list */

        "adblocker": false,
        /* Show a Warning if an adblocker is detected */
        "AcceptAllCta": true,
        /* Show the accept all button when highPrivacy on */
        "highPrivacy": true,
        /* Disable auto consent */
        "handleBrowserDNTRequest": false,
        /* If Do Not Track == 1, disallow all */

        "removeCredit": false,
        /* Remove credit link */
        "moreInfoLink": true,
        /* Show more info link */
        "useExternalCss": false,
        /* If false, the tarteaucitron.css file will be loaded */

        //"cookieDomain": ".my-multisite-domaine.fr", /* Shared cookie for multisite */

        "readmoreLink": "/cookiespolicy" /* Change the default readmore link */
      });
    </script>

    <script type="text/javascript">
      tarteaucitron.user.googletagmanagerId = 'GTM-K3QQNK2';
      (tarteaucitron.job = tarteaucitron.job || []).push('googletagmanager');
      tarteaucitron.user.tawktoId = '5f5637ebf0e7167d000e19cd';
      (tarteaucitron.job = tarteaucitron.job || []).push('tawkto');
    </script>

    <?php if (isset($js_to_load)) : ?>
      <?php foreach ($js_to_load as $file) : ?>
        <script type="text/javascript" src="<?= asset_url() ?>js/<?= $file ?>.js?v=<?= getVersion() ?>"></script>
      <?php endforeach; ?>
    <?php endif; ?>

    <script type="text/javascript" src="<?= asset_url() ?>js/main.min.js?v=<?= getVersion() ?>"></script>
    <script type="text/javascript" src="<?= asset_url() ?>js/datan/url_obf2.min.js"></script>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K3QQNK2" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    </body>

    </html>
