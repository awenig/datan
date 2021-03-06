<div class="container pg-elections-candidats">
  <div class="row bloc-titre">
    <div class="col-12">
      <h1><?= $title ?></h1>
    </div>
  </div>
</div>
<div class="container-fluid pg-elections-candidats infosIndividual py-5">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-lg-7 my-4">
        <h2>Informations</h2>
        <p>Les <?= mb_strtolower($election['libelle']) ?> <?= $election['dateYear'] ?> se dérouleront en deux tours.</p>
        <p>Le premier tour <?= $today > $election['dateFirstRound'] ? "s'est tenu" : "se tiendra" ?> le <?= $election['dateFirstRoundFr'] ?>, tandis que le second tour <?= $today > $election['dateSecondRound'] ? "s'est déroulé" : "se déroulera" ?> le <?= $election['dateSecondRoundFr'] ?>.</p>
        <?php if ($election['candidates']): ?>
          <p>
            Découvrez sur cette page les députés candidats aux <?= mb_strtolower($election['libelle']) ?> de <?= $election['dateYear'] ?>.
          </p>
          <p>
            <?php if ($candidatsN): ?>
              Nous avons répertorié <b><?= $candidatsN ?> député<?= $candidatsN > 1 ? "s" : NULL ?> candidat<?= $candidatsN > 1 ? "s" : NULL ?></b>.
            <?php else: ?>
              Nous avons jusqu'à présent répertorié <span class="font-weight-bold">aucun député candidat</span>.
            <?php endif; ?>
          </p>
          <?php if ($state == 1): ?>
            <p>
              De ces candidats, <b><?= $candidatsN_second ?> député<?= $candidatsN_second > 1 ? "s se sont maintenus" : " s'est maintenu" ?> pour le second tour</b>.
            </p>
          <?php endif; ?>
          </p>
          <p>Un député candidat ne se trouve pas dans la liste ? N'hésitez pas à nous le faire savoir: <a href="mailto:info@datan.fr">contact@datan.fr</a> !</p>
        <?php endif; ?>
      </div>
      <div class="col-md-4 col-lg-5 d-flex justify-content-center align-items-center">
        <span class="url_obf btn btn-light btn-lg" url_obf="<?= url_obfuscation("https://elections.interieur.gouv.fr/") ?>">
          Résultats
        </span>
      </div>
    </div>
  </div>
</div>
<div class="container pg-elections-candidats py-5">
  <div class="row">
    <?php if ($election['id'] == 1 /*regionales-2021 */): ?>
      <div class="col-md-8 col-lg-7">
        <h2 class="my-4">Résultats : la couleur politique des conseils régionaux</h2>
        <p>Dans les conseils régionaux, les partis politiques qui arrivent en têtes aux élections reçoivent la majorité des sièges et en prennent la présidence. C'est donc la liste arrivée en tête qui se retrouve à la tête de la région.</p>
        <p>Découvrez sur cette carte la couleur politique des différentes régions après le second tour des élections de 2021.</p>
        <div class="map-container my-5">
          <div class="jvmap-smart" id="map-regions"></div>
        </div>
        <div class="row my-4">
          <?php foreach ($mapLegend as $x): ?>
            <div class="map-container-ledend col-6">
              <div class="d-flex my-2">
                <div class="color" style="background-color: <?= $x['color'] ?>"></div>
                <span class="ml-4"><?= $x['party'] ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if ($election['id'] == 2 /* departementales-2021 */): ?>
      <div class="col-md-8 col-lg-7">
        <h2 class="my-4">La couleur politique actuelle des conseils départementaux</h2>
        <p>Les dernières élections départementales ont eu lieu en 2015. Pour retrouver les résultats, <a href="https://www.interieur.gouv.fr/Elections/Les-resultats/Departementales/elecresult__departementales-2015/(path)/departementales-2015/index.html" target="_blank" rel="nofollow noreferrer noopener">cliquez ici</a>.</p>
        <p>Après les élections, les conseillers départementaux élisent le président du conseil départemental. Le président est, dans la plupart des cas, élu par une majorité de droite ou de gauche.</p>
        <p>Le président élu, qui se retrouve à la tête du conseil département, est chargé de l'administration et a la charge des dépenses et des recettes.</p>
        <p>Découvrez sur cette carte la couleur politique des différentes régions de 2015 à 2021.</p>
        <div class="map-container my-5">
          <div class="jvmap-smart" id="map-departements"></div>
        </div>
        <div class="row my-4">
          <?php foreach ($mapLegend as $x): ?>
            <div class="map-container-ledend col-6">
              <div class="d-flex my-2">
                <div class="color" style="background-color: <?= $x['color'] ?>"></div>
                <span class="ml-4"><?= $x['party'] ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if (!empty($electionInfos)): ?>
      <div class="col-md-8 col-lg-4 offset-lg-1 mt-5 mt-lg-0">
        <div class="mt-4 infosGeneral">
          <h2 class="title ml-md-5 ml-3">Mieux comprendre</h2>
          <div class="px-4">
            <?= $electionInfos ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <?php if ($election['candidates']): ?>
    <div class="row mt-5 mb-3">
      <div class="col-12">
        <h2>Retrouvez les députés candidats aux <?= mb_strtolower($election['libelle']) ?> de <?= $election['dateYear'] ?></h2>
      </div>
      <div class="col-12 d-flex flex-column flex-lg-row">
        <div class="d-flex flex-even px-2">
          <div class="d-flex align-items-center">
            <span class="candidatsN"><?= $candidatsN ?></span>
          </div>
          <div class="d-flex align-items-center ml-4">
            <span>Au total, <?= $candidatsN ?> députés étaient candidats au <b>premier tour</b> des <?= mb_strtolower($election['libelle']) ?> de <?= $election['dateYear'] ?>.</span>
          </div>
        </div>
        <?php if ($state > 0): ?>
          <div class="d-flex flex-even px-2">
            <div class="d-flex align-items-center">
              <span class="candidatsN"><?= $candidatsN_second ?></span>
            </div>
            <div class="d-flex align-items-center ml-4">
              <span>Après le premier tour, <?= $candidatsN_second ?> députés se sont maintenus au <b>second tour</b> des <?= mb_strtolower($election['libelle']) ?> de <?= $election['dateYear'] ?>.</span>
            </div>
          </div>
        <?php endif; ?>
        <?php if ($state > 1): ?>
          <div class="d-flex flex-even px-2">
            <div class="d-flex align-items-center">
              <span class="candidatsN"><?= $candidatsN_elected ?></span>
            </div>
            <div class="d-flex align-items-center ml-4">
              <span>Après le second tour, <?= $candidatsN_elected ?> députés ont été <b>élus</b> lors des <?= mb_strtolower($election['libelle']) ?> de <?= $election['dateYear'] ?>.</span>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="row">
      <div class="pb-4 col-lg-3 search-element sticky-top sticky-top-lg">
        <div class="sticky-top sticky-offset">
          <!-- Search -->
          <div class="mt-3 mt-lg-0">
            <input type="text" id="quicksearch" placeholder="Recherchez un député..." />
          </div>
          <!-- Filters state -->
          <div class="filters filtersState mt-md-4 d-none d-lg-block">
            <p class="surtitre">Résultat de l'élection</p>
            <input class="radio-btn" name="state" id="radio-100" type="radio" checked="" value="*">
            <label for="radio-100" class="radio-label d-flex align-items-center">
              <span class="d-flex align-items-center"><b>Tous les députés</b></span>
            </label>
            <input class="radio-btn" name="state" id="radio-101" type="radio" value=".elected">
            <label for="radio-101" class="radio-label d-flex align-items-center">
              <span class="d-flex align-items-center">Élu</span>
            </label>
            <input class="radio-btn" name="state" id="radio-102" type="radio" value=".lost">
            <label for="radio-102" class="radio-label d-flex align-items-center">
              <span class="d-flex align-items-center">Éliminé</span>
            </label>
          </div>
          <!-- Filters -->
          <?php if (count($districts) <= 25): ?>
            <div class="filters filtersDistrict mt-md-5 d-none d-lg-block">
              <p class="surtitre">Région</p>
              <input class="radio-btn" name="district" id="radio-1" type="radio" checked="" value="*">
              <label for="radio-1" class="radio-label d-flex align-items-center">
                <span class="d-flex align-items-center"><b>Tous les députés</b></span>
              </label>
              <?php $i=2 ?>
              <?php foreach ($districts as $district): ?>
                <input class="radio-btn" name="district" id="radio-<?= $i ?>" type="radio" value=".<?= strtolower($district['id']) ?>">
                <label for="radio-<?= $i ?>" class="radio-label d-flex align-items-center">
                  <span class="d-flex align-items-center"><?= $district['libelle'] ?></span>
                </label>
                <?php $i++ ?>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <br>
            <select class="custom-select filters" id="selectFilter" onchange="changeFilterFunc()">
              <option selected value="*">Tous les députés</option>
              <?php foreach ($districts as $district): ?>
                <option value=".<?= $district['id'] ?>"><?= $district['libelle'] ?> (<?= $district['id'] ?>)</option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-lg-9 col-md-12">
        <div class="row mt-2 sorting">
          <?php foreach ($deputes as $depute): ?>
            <div class="col-md-6 col-xl-4 sorting-item <?= strtolower($depute['districtId']) ?> <?= strtolower($depute['electionState']) ?>">
              <div class="d-flex justify-content-center">
                <?php $this->load->view('deputes/partials/card_home.php', array('depute' => $depute, 'tag' => 'h3')) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
