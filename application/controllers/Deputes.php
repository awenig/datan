<?php
  class Deputes extends CI_Controller {
    public function __construct() {
      parent::__construct();
      $this->load->model('deputes_model');
      $this->load->model('depute_edito');
      $this->load->model('votes_model');
      $this->load->model('departement_model');
      $this->load->model('groupes_model');
      $this->load->model('fields_model');
      $this->load->model('elections_model');
      $this->load->model('jobs_model');
      //$this->password_model->security_password(); Former login protection
    }

    private function get_statistiques($data, $legislature, $mpId, $groupe_id){
      if (in_array($legislature, legislature_all())) {
        $data['participation'] = $this->deputes_model->get_stats_participation_solennels($mpId, $legislature);
        if ($data['participation']['votesN'] < 5) {
          $data['no_participation'] = TRUE;
        } else {
          $data['no_participation'] = FALSE;
        }
        $data['edito_participation'] = $this->depute_edito->participation($data['participation']['score'], $data['participation']['mean']); //edited
        // loyalty
        $data['loyaute'] = $this->deputes_model->get_stats_loyaute($mpId, $legislature);
        if ($data['loyaute']['votesN'] < 75) {
          $data['no_loyaute'] = TRUE;
        } else {
          $data['no_loyaute'] = FALSE;
        }
        $data['edito_loyaute'] = $this->depute_edito->loyaute($data['loyaute']['score'], $data['loyaute']['mean']); // edited
        // loyalty history
        $data['loyaute_history'] = $this->deputes_model->get_stats_loyaute_history($mpId, $legislature);
        // proximity with majority
        $data['majorite'] = $this->deputes_model->get_stats_majorite($mpId, $legislature);
        if ($data['majorite']['votesN'] < 75) {
          $data['no_majorite'] = TRUE;
        } else {
          $data['no_majorite'] = FALSE;
        }
        $data['edito_majorite'] = $this->depute_edito->majorite($data['majorite']['score'], $data['majorite']['mean']); //edited
        // proximity with all groups
        if ($legislature == legislature_current()) /*LEGISLATURE 15*/ {
          $data['accord_groupes'] = $this->deputes_model->get_accord_groupes_actifs($mpId, legislature_current());
          $data['accord_groupes_all'] = $this->deputes_model->get_accord_groupes_all($mpId, legislature_current());
          // Positionnement politique
          $accord_groupes_sorted = $data['accord_groupes'];
          if (empty($accord_groupes_sorted)) {
            $data["no_votes"] = TRUE;
          } else {
            $data["no_votes"] = FALSE;
            $sort_key  = array_column($accord_groupes_sorted, 'accord');
            array_multisort($sort_key, SORT_DESC, $accord_groupes_sorted);
            $data['proximite'] = $this->depute_edito->positionnement($accord_groupes_sorted, $groupe_id);
          }
        } else /* LEGISLATURE 14 */ {
          $data['accord_groupes'] = $this->deputes_model->get_accord_groupes_all($mpId, $legislature);
          $data['accord_groupes_all'] = $data['accord_groupes'];

          if ($data['accord_groupes']) {
            $data['no_votes'] = FALSE;
          } else {
            $data['no_votes'] = TRUE;
          }
        }
        $accord_groupes_n = count($data['accord_groupes']);
        $accord_groupes_divided = round($accord_groupes_n / 2, 0, PHP_ROUND_HALF_UP);
        $data['accord_groupes_first'] = array_slice($data['accord_groupes'], 0, $accord_groupes_divided);
        $data['accord_groupes_first'] = array_slice($data['accord_groupes_first'], 0, 3);
        $data['accord_groupes_last'] = array_slice($data['accord_groupes'], $accord_groupes_divided, $accord_groupes_n);
        $data['accord_groupes_last'] = array_slice($data['accord_groupes_last'], -3);
        $data['accord_groupes_last_sorted'] = array_reverse($data['accord_groupes_last']);
      }

      return $data;
    }

    public function index($legislature = NULL) {

      if ($legislature == legislature_current()) {
        redirect('deputes');
      }

      if ($legislature == NULL) {
        $legislature = legislature_current();
      }

      if (!in_array($legislature, array(14, 15))) {
        show_404();
      }

      if ($legislature == legislature_current()) {
        $data['active'] = TRUE;
      } else {
        $data['active'] = FALSE;
      }

      $data['legislature'] = $legislature;
      $data['deputes'] = $this->deputes_model->get_deputes_all($legislature, $data['active'], NULL);
      $data['groupes'] = $this->groupes_model->get_groupes_all($data['active'], $legislature);
      $number_gender = $this->deputes_model->get_deputes_gender($legislature);
      foreach ($number_gender as $gender) {
        if ($gender["gender"] == "male") {
          $data["male"]["n"] = $gender["n"];
          $data["male"]["percentage"] = $gender["percentage"];
        } elseif ($gender["gender"] == "female") {
          $data["female"]["n"] = $gender["n"];
          $data["female"]["percentage"] = $gender["percentage"];
        }
      }
      $data['number_inactive'] = $this->deputes_model->get_n_deputes_inactive();

      // Groupe_color
      foreach ($data['deputes'] as $key => $value) {
        $data['deputes'][$key]['couleurAssociee'] = $this->groupes_model->get_groupe_color(array($value['groupLibelleAbrev'], $value['couleurAssociee']));
      }

      // Breadcrumb
      if ($legislature == legislature_current()) {
        $data['breadcrumb'] = array(
          array(
            "name" => "Datan", "url" => base_url(), "active" => FALSE
          ),
          array(
            "name" => "Députés", "url" => base_url()."deputes", "active" => TRUE
          )
        );
      } else {
        $data['breadcrumb'] = array(
          array(
            "name" => "Datan", "url" => base_url(), "active" => FALSE
          ),
          array(
            "name" => "Députés", "url" => base_url()."deputes", "active" => FALSE
          ),
          array(
            "name" => $legislature."e législature", "url" => base_url()."deputes/legislature-".$legislature, "active" => TRUE
          )
        );
      }

      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      // Meta
      $data['url'] = $this->meta_model->get_url();
      if ($legislature == legislature_current()) {
        $data['title_meta'] = "Députés - Assemblée Nationale | Datan";
        $data['description_meta'] = "Retrouvez tous les députés en activité de l'Assemblée nationale de la ".legislature_current()."e législature. Résultats de vote et analyses pour chaque député.";
        $data['title'] = "Les députés en activité de l'Assemblée nationale";
      } else {
        $data['title_meta'] = "Députés ".$legislature."e législature - Assemblée nationale | Datan";
        $data['description_meta'] = "Retrouvez tous les députés en activité de l'Assemblée nationale de la ".$legislature."e législature. Résultats de vote et analyses pour chaque député.";
        $data['title'] = "Les députés de la ".$legislature."e législature";
      }
      //Open Graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // JS
      $data['js_to_load_before_datan'] = array("isotope.pkgd.min");
      $data['js_to_load']= array("datan/sorting");
      // Load Views
      $this->load->view('templates/header', $data);
      $this->load->view('templates/button_up');
      $this->load->view('deputes/all', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer', $data);
    }

    public function inactifs(){
      $data['active'] = FALSE;
      $data['legislature'] = legislature_current();
      $data['deputes'] = $this->deputes_model->get_deputes_all(legislature_current(), $data['active'], NULL);
      $data['groupes'] = $this->deputes_model->get_groupes_inactifs();
      $data["number_inactive"] = count($data['deputes']);

      // Groupe_color
      foreach ($data['deputes'] as $key => $value) {
        $data['deputes'][$key]['couleurAssociee'] = $this->groupes_model->get_groupe_color(array($value['groupLibelleAbrev'], $value['couleurAssociee']));
      }

      $data['title'] = "Les députés plus en activité";
      // Breadcrumb
      $data['breadcrumb'] = array(
        array(
          "name" => "Datan", "url" => base_url(), "active" => FALSE
        ),
        array(
          "name" => "Députés", "url" => base_url()."deputes", "active" => FALSE
        ),
        array(
          "name" => "Députés plus en activité", "url" => base_url()."deputes/inactifs", "active" => TRUE
        )
      );
      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      // Meta
      $data['url'] = $this->meta_model->get_url();
      $data['title_meta'] = "Députés plus en activité - Assemblée Nationale | Datan";
      $data['description_meta'] = "Retrouvez tous les députés plus en activité de l'Assemblée nationale de la 15e législature. Résultats de vote et analyses pour chaque député.";
      $data['title'] = "Députés plus en activité de l'Assemblée nationale";
      // Open graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // JS
      $data['js_to_load_before_datan'] = array("isotope.pkgd.min");
      $data['js_to_load']= array("datan/sorting");
      // Load views
      $this->load->view('templates/header', $data);
      $this->load->view('templates/button_up');
      $this->load->view('deputes/all', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer');
    }

    public function individual($nameUrl, $departement) {
      setlocale(LC_TIME, 'french');

      // Get infos MP
      $data['depute'] = $this->deputes_model->get_depute_individual($nameUrl, $departement);

      // Check if depute exists
      if (empty($data['depute'])) {
        show_404();
      }

      // Check if it is in legislature 14 or 15
      if (!in_array($data['depute']['legislature'], legislature_all())) {
        show_404();
      }

      // Caching
      if(!in_array($_SERVER['REMOTE_ADDR'], localhost()) && !$this->session->userdata('logged_in')){
        $this->output->cache("4320"); // Caching enable for 3 days (1440 minutes per day)
      }

      // Main variables
      $mpId = $data['depute']['mpId'];
      $nameLast = $data['depute']['nameLast'];
      $depute_dpt = $data['depute']['dptSlug'];
      $data['active'] = $data['depute']['active'];
      $legislature = $data['depute']['legislature'];
      $data['infos_groupes'] = groupsPositionEdited();
      $depute = $data['depute']['nameFirst'].' '.$data['depute']['nameLast'];
      $data['no_job'] = array('autre profession','autres', 'sans profession déclarée', 'sans profession');

      // Get group
      if (!empty($data['depute']['libelle'])) {
        $groupe_id = $data['depute']['groupeId'];
        $data['depute']['couleurAssociee'] = $this->groupes_model->get_groupe_color(array($data['depute']['libelleAbrev'], $data['depute']['couleurAssociee']));
        // Is the MP a group president?
        $data['group_president'] = $this->deputes_model->depute_group_president($mpId, $groupe_id);
        if (!empty($data['group_president'])) {
          $data['isGroupPresident'] = TRUE;
        } else {
          $data['isGroupPresident'] = FALSE;
        }
      } else {
        $groupe_id = NULL;
      }

      // General infos
      $data["depute"]["dateNaissanceFr"] = utf8_encode(strftime('%d %B %Y', strtotime($data['depute']['birthDate']))); // birthdate
      $data['depute']['circo_abbrev'] = $this->functions_datan->abbrev_n($data['depute']['circo'], TRUE); // circo number
      $data['politicalParty'] = $this->deputes_model->get_political_party($mpId); // political party
      $data['election_canceled'] = NULL;
      if ($legislature == 15) { // Get election if 15th legislature
        $data['election_canceled'] = $this->deputes_model->get_election_canceled($mpId, 15);
        $canceled = array(
          "Annulation de l'élection sur décision du Conseil constitutionnel",
          "Démission d'office sur décision du Conseil constitutionnel"
        );
        if (!in_array($data['election_canceled']['causeFin'], $canceled)) {
          $data['election_canceled']['cause'] = NULL;
          $data['election_result'] = $this->deputes_model->get_election_result($data['depute']['departementCode'], $data['depute']['circo'], $nameLast); // electoral result
          if ($data['election_result']) { // Get electoral infos & mandat not canceled
            $data['election_opponents'] = $this->deputes_model->get_election_opponent($data['depute']['departementCode'], $data['depute']['circo']);
            $data['election_infos'] = $this->deputes_model->get_election_infos($data['depute']['departementCode'], $data['depute']['circo']);
            $data['election_infos']['abstention_rate'] = round($data['election_infos']['abstentions'] * 100 / $data['election_infos']['inscrits']);
            if ($data['election_infos']['tour'] == 2) { // elected at first round
              $data['election_opponent'] = $data['election_opponents'][0];
              $data['election_opponent']['candidat'] = ucwords(mb_strtolower(str_replace(array("M. ", "Mme "), "", $data['election_opponent']['candidat'])));
            } else { // elected at second round
              $data['election_opponent']['voix'] = 0;
              $data['election_opponent']['candidat'] = "Reste des candidats";
              foreach ($data['election_opponents'] as $x) {
                $data['election_opponent']['voix'] += $x['voix'];
              }
            }
          }
        } else {
          switch ($data['election_canceled']['causeFin']) {
            case "Annulation de l'élection sur décision du Conseil constitutionnel":
              $data['election_canceled']['cause'] = "L'élection de " . $depute . ", qui s'est tenue pendant les législatures de juin 2017, a été invalidée par le Conseil constitutionnel en " . $data['election_canceled']['dateFinFR'] . "." ;
              break;
            default:
              $data['election_canceled']['cause'] = NULL;
              break;
          }
        }
      }

      // Get pct famSocPro
      $data['famSocPro'] = $this->jobs_model->get_stats_individual($data['depute']['famSocPro'], $legislature);

      // Get commission parlementaire
      if ($data['active']) {
        $data['commission_parlementaire'] = $this->deputes_model->get_commission_parlementaire($mpId);
      }

      // Regionales 2021
      $data['regionales2021'] = $this->elections_model->get_candidate($mpId, 1/* Régionales 2021 */);
      if ($data['regionales2021']) {
        $data['regionales2021']['state'] = $this->elections_model->get_state($data['regionales2021']['secondRound'], $data['regionales2021']['elected']);
      }

      // Statistiques
      $data = $this->get_statistiques($data, $legislature, $mpId, $groupe_id);

      // Get other MPs
      if ($legislature == legislature_current()) {
        $data['other_deputes'] = $this->deputes_model->get_other_deputes($groupe_id, $nameLast, $mpId, $data['active'], $legislature);
      } else {
        $data['other_deputes'] = $this->deputes_model->get_other_deputes_legislature($nameLast, $mpId, $legislature);
      }
      $data['other_deputes_dpt'] = $this->deputes_model->get_deputes_all(legislature_current(), TRUE, $depute_dpt);

      // Get votes datan
      if ($legislature == legislature_current()) {
        // Get edited votes
        $data['votes_datan'] = $this->votes_model->get_votes_datan_depute($mpId, 5);
        foreach ($data['votes_datan'] as $key => $value) {
          $data['votes_datan'][$key]['dateScrutinFRAbbrev'] = $this->functions_datan->abbrev_months($value['dateScrutinFR']);
        }
        // Get key votes
        $data['key_votes'] = $this->votes_model->get_key_votes_mp($mpId);
      } else {
        $data['votes_datan'] = NULL;
        $data['key_votes'] = NULL;
      }

      // Historique du député
      $data['depute']['datePriseFonctionLettres'] = utf8_encode(strftime('%B %Y', strtotime($data['depute']['datePriseFonction'])));
      $data['history_average'] = $this->deputes_model->get_history_all_deputes($mpId);
      $data['mandat_edito'] = $this->depute_edito->get_nbr_lettre($data['depute']['mandatesN']);
      $duree_depute = round($data['depute']['mpLength']/365);
      $duree_average = $data['history_average']['length'];
      $data['history_edito'] = $this->depute_edito->history($duree_depute, $duree_average);
      $data['mandats'] = $this->deputes_model->get_historique_mandats($mpId);
      $data['mandatsReversed'] = array_reverse($data['mandats']);

      // Gender
      $data['gender'] = $this->depute_edito->gender($data['depute']['civ']);

      // Meta
      $data['url'] = $this->meta_model->get_url();
      $data['title_meta'] = $depute." - Activité Parlementaire | Datan";
      $data['description_meta'] = "Découvrez les résultats des votes ".$data['gender']['du']." député".$data['gender']['e']." ".$depute." : taux de participation, loyauté avec son groupe, proximité avec la majorité présidentielle.";
      $data['title'] = $depute;
      $data['title_breadcrumb'] = mb_substr($data['depute']['nameFirst'], 0, 1).'. '.$data['depute']['nameLast'];
      // Breadcrumb
      $data['breadcrumb'] = array(
        array(
          "name" => "Datan", "url" => base_url(), "active" => FALSE
        ),
        array(
          "name" => "Députés", "url" => base_url()."deputes", "active" => FALSE
        ),
        array(
          "name" => $data['depute']['departementNom']." (".$data['depute']['departementCode'].")", "url" => base_url()."deputes/".$data['depute']['dptSlug'], "active" => FALSE
        ),
        array(
          "name" => $data['title_breadcrumb'], "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl, "active" => TRUE
        )
      );
      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      // Open Graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // Microdata Person
      $data['schema'] = $this->deputes_model->get_person_schema($data['depute']);
      // CSS
      $data['critical_css'] = "depute_individual";
      $data['css_to_load']= array(
        array(
          "url" => css_url()."circle.css",
          "async" => TRUE
        ),
        array(
          "url" => "https://unpkg.com/flickity@2/dist/flickity.min.css",
          "async" => TRUE
        )
      );
      // JS UP
      $data['js_to_load']= array(
        "flickity.pkgd.min",
      );
      // Preloads
      $data['preloads'] = array(
        array("href" => asset_url()."imgs/cover/hemicycle-front-375.jpg", "as" => "image", "media" => "(max-width: 575.98px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front-768.jpg", "as" => "image", "media" => "(min-width: 576px) and (max-width: 970px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front.jpg", "as" => "image", "media" => "(min-width: 970.1px)"),
      );
      // Load Views
      $this->load->view('templates/header', $data);
      $this->load->view('templates/button_up');
      $this->load->view('deputes/individual', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer');
    }

    public function historique($nameUrl, $departement, $legislature){
      setlocale(LC_TIME, 'french');
      // Check with this page : http://localhost/datan/deputes/ille-et-vilaine-35/depute_thierry-benoit/legislature-14
      $data['depute'] = $this->deputes_model->get_depute_individual_historique($nameUrl, $departement, $legislature);
      $data['depute_last'] = $this->deputes_model->get_depute_individual($nameUrl, $departement);

      // Check if MP exists
      if (empty($data['depute'])) {
        show_404();
      }

      // Check if it is in legislature 14 or 15
      if (!in_array($data['depute']['legislature'], legislature_all())) {
        show_404();
      }

      // Redirect if legislature depute and depute_last is the same ==> redirect v/ webpage with last mandate
      if ($legislature == $data['depute_last']['legislature']) {
        redirect("deputes/" . $data['depute']['dptSlug'] . "/depute_" . $data['depute']['nameUrl']);
      }

      // Main variables
      $mpId = $data['depute']['mpId'];
      $nameLast = $data['depute']['nameLast'];
      $depute_dpt = $data['depute']['dptSlug'];
      $data['active'] = $data['depute']['active'];
      $data['legislature'] = $legislature;
      $legislature = $data['depute']['legislature'];
      $data["depute"]["dateNaissanceFr"] = utf8_encode(strftime('%d %B %Y', strtotime($data['depute']['birthDate']))); // birthdate
      $data['depute']['circo_abbrev'] = $this->functions_datan->abbrev_n($data['depute']['circo'], TRUE); // circo number
      $data['mandats'] = $this->deputes_model->get_historique_mandats($mpId);
      $data['mandatsReversed'] = array_reverse($data['mandats']);

      // Gender
      $data['gender'] = $this->depute_edito->gender($data['depute']['civ']);

      // Statistiques
      $data = $this->get_statistiques($data, $legislature, $mpId, NULL);

      // Meta
      $data['url'] = $this->meta_model->get_url();
      $depute = $data['depute']['nameFirst'].' '.$data['depute']['nameLast'];
      $data['title_meta'] = $depute." - Historique ".$legislature."e législature | Datan";
      $data['description_meta'] = "Découvrez l'historique  ".$data['gender']['du']." député".$data['gender']['e']." ".$depute." pour la ".$legislature."e législature : taux de participation, loyauté avec son groupe, proximité avec la majorité présidentielle.";
      $data['title'] = $depute;
      $data['title_breadcrumb'] = mb_substr($data['depute']['nameFirst'], 0, 1).'. '.$data['depute']['nameLast'];
      // Breadcrumb
      $data['breadcrumb'] = array(
        array(
          "name" => "Datan", "url" => base_url(), "active" => FALSE
        ),
        array(
          "name" => "Députés", "url" => base_url()."deputes", "active" => FALSE
        ),
        array(
          "name" => $data['depute']['departementNom']." (".$data['depute']['departementCode'].")", "url" => base_url()."deputes/".$data['depute']['dptSlug'], "active" => FALSE
        ),
        array(
          "name" => $data['title_breadcrumb'], "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl, "active" => FALSE
        ),
        array(
          "name" => "Historique ".$legislature . "e legislature", "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl."/legislature-".$legislature, "active" => TRUE
        ),
      );
      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      // Open Graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // Microdata Person
      $data['schema'] = $this->deputes_model->get_person_schema($data['depute']);
      // CSS
      $data['css_to_load']= array(
        array(
          "url" => css_url()."circle.css",
          "async" => TRUE
        ),
        array(
          "url" => "https://unpkg.com/flickity@2/dist/flickity.min.css",
          "async" => TRUE
        )
      );
      // JS UP
      $data['js_to_load']= array(
        "flickity.pkgd.min",
      );
      // Preloads
      $data['preloads'] = array(
        array("href" => asset_url()."imgs/cover/hemicycle-front-375.jpg", "as" => "image", "media" => "(max-width: 575.98px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front-768.jpg", "as" => "image", "media" => "(min-width: 576px) and (max-width: 970px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front.jpg", "as" => "image", "media" => "(min-width: 970.1px)"),
      );
      // Load Views
      $this->load->view('templates/header', $data);
      $this->load->view('templates/button_up');
      $this->load->view('deputes/historique', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer');
    }

    public function commune($input, $departement){
      $input_ville = $input;
      $data['ville'] = $this->departement_model->get_commune_individual($input_ville, $departement);

      if (empty($data['ville'])) {
        show_404();
      }

      $n_circos = count($data['ville']);

      //Variables
      $v = $data['ville'][0];
      $commune_nom = $v['commune_nom'];
      $dpt_code = $v['dpt'];
      $departement = $v['dpt_slug'];
      $code_postal = $v['code_postal'];

      // GET THE MPs
      // If only one district
      if ($n_circos == 1) {
        $circo[] = $v['circo'];
        $data['n_circos'] = $n_circos;
        $data['deputes_commune'] = $this->departement_model->get_deputes_commune($departement, $circo);
        $data['depute_commune'] = $data['deputes_commune'][0];
        $data['depute_commune']['couleurAssociee'] = $this->groupes_model->get_groupe_color(array($data['depute_commune']['groupLibelleAbrev'], $data['depute_commune']['couleurAssociee']));
        $data['depute_commune']['electionCircoAbbrev'] = $this->functions_datan->abbrev_n($data['depute_commune']['electionCirco'], TRUE);
        $data['depute_commune']['cardCenter'] = $data['depute_commune']['electionCirco']."<sup>".$data['depute_commune']['electionCircoAbbrev']."</sup> circonscription";
        $data['gender'] = $this->depute_edito->gender($data['depute_commune']['civ']);

      // IF more than one district
      } else {
        $circo = array();
        $circo_edited = array();
        foreach ($data['ville'] as $key => $value) {
          $circo[] = $value['circo'];
          $circo_edited[$key]["number"] = $value['circo'];
          $circo_edited[$key]["abbrev"] = $this->functions_datan->abbrev_n($value['circo'], TRUE);
        }
        $data['circos'] = $circo_edited;
        $data['n_circos'] = $n_circos;
        $data['deputes_commune'] = $this->departement_model->get_deputes_commune($departement, $circo);
        foreach ($data['deputes_commune'] as $key => $value) {
          $data['deputes_commune'][$key]['couleurAssociee'] = $this->groupes_model->get_groupe_color(array($value['groupLibelleAbrev'], $value['couleurAssociee']));
          $data['deputes_commune'][$key]['electionCircoAbbrev'] = $this->functions_datan->abbrev_n($value['electionCirco'], TRUE);
          $data['deputes_commune'][$key]['cardCenter'] = $data['deputes_commune'][$key]['electionCirco']."<sup>".$data['deputes_commune'][$key]['electionCircoAbbrev']."</sup> circonscription";
        }
      }

      // Get other MPs from the same department
      $deputes_commune = array();
      foreach ($data['deputes_commune'] as $n) {
        $deputes_commune[] = $n['mpId'];
      }
      $data['deputes_dpt'] = $this->departement_model->get_deputes_commune_dpt($departement, $deputes_commune);
      foreach ($data['deputes_dpt'] as $key => $value) {
        $data['deputes_dpt'][$key]['couleurAssociee'] = $this->groupes_model->get_groupe_color(array($value['groupLibelleAbrev'], $value['couleurAssociee']));
        $data['deputes_dpt'][$key]['electionCircoAbbrev'] = $this->functions_datan->abbrev_n($value['electionCirco'], TRUE);
        $data['deputes_dpt'][$key]['cardCenter'] = $data['deputes_dpt'][$key]['electionCirco']."<sup>".$data['deputes_dpt'][$key]['electionCircoAbbrev']."</sup> circonscription";
      }

      // Some editing depending on the number of MPs
      if ($n_circos > 1) {
        $s = "s";
        $ses = "leurs";
        $son = "leur";
        $le = "les";
        $depute_writing = "députés";
        $elu_writing = "élus";
      } else {
        $s = NULL;
        $ses = "ses";
        $son = "son";
        $le = $data["gender"]["le"];
        $depute_writing = "député".$data["gender"]["e"];
        $elu_writing = "élu".$data["gender"]["e"];
      }
      $vocals = array('A','E','I','O','U');
      if (ctype_alpha($commune_nom) && in_array($commune_nom{0}, $vocals)) {
        $de = "d'";
      } else {
        $de = "de ";
      }

      // Get all big cities from the department
      $data['communes_dpt'] = $this->departement_model->get_communes_population($departement);

      // Clean infos on the city
      $data['ville'] = $data['ville'][0];
      $data['ville']['circo_abbrev'] = $this->functions_datan->abbrev_n($data['ville']['circo'], TRUE);
      $data['ville']['pop2017'] = $this->functions_datan->decRound($data['ville']['pop2017'], mb_strlen($data['ville']['pop2017']) - 4);
      $data['ville']['pop2017_format'] = number_format($data['ville']['pop2017'], 0, ',', ' ');
      if ($data['ville']['evol10'] > 0) {
        $data['ville']['evol10_text'] = 'augmenté';
      } else {
        $data['ville']['evol10_text'] = 'diminué';
      }
      $data['ville']['evol10_edited'] = str_replace("-", "", $data['ville']['evol10']);
      $data['ville_insee'] = $this->departement_model->get_ville_insee($data['ville']['codeRegion'], $data['ville']['dpt'], $data['ville']['insee_city']);

      // Get city mayor
      $data['mayor'] = $this->departement_model->get_city_mayor($data['ville']['dpt'], $data['ville']['insee_city']);
      if ($data['mayor']['gender'] == "F") {
        $data['mayor']['gender_le'] = "la";
      } else {
        $data['mayor']['gender_le'] = "le";
      }

      // Get elections
      // 1. 2017 _ Presidentielles _ 2nd tour
      $data['results_2017_pres_2'] = $this->departement_model->get_results_2017_pres_2($data['ville']['dpt'], $data['ville']['insee_city']);
      // 2. 2019 _ Européennes
      $data['results_2019_europe'] = $this->departement_model->get_results_2019_europe($data['ville']);

      // Meta
      $data['url'] = $this->meta_model->get_url();
      $data['title_meta'] = "Député(s) ".$commune_nom." ".$code_postal." | Datan";
      $data['description_meta'] = "Découvrez le".$s." député".$s." élu".$s." dans la ville ".$de."".$commune_nom." (".$dpt_code.") et tous ".$ses." résultats de vote : taux de participation, loyauté avec ".$son." groupe, proximité avec la majorité présidentielle.";
      $data['title'] = "Découvrez ".$le." ".$depute_writing." ".$elu_writing." dans la ville ".$de."".$commune_nom;
      // Breadcrumb
      $data['breadcrumb'] = array(
        array(
          "name" => "Datan", "url" => base_url(), "active" => FALSE
        ),
        array(
          "name" => "Députés", "url" => base_url()."deputes", "active" => FALSE
        ),
        array(
          "name" => $data['ville']['dpt_nom']." (".$data['ville']['dpt'].")", "url" => base_url()."deputes/".$data['ville']['dpt_slug'], "active" => FALSE
        ),
        array(
          "name" => $data['ville']['commune_nom'], "url" => base_url()."deputes/".$data['ville']['dpt_slug']."/ville_".$input_ville, "active" => TRUE
        ),
      );
      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      //Open Graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // CSS TO LOAD
      $data['critical_css'] = "city";
      // JS UP
      // JS
      // Preloads
      $data['preloads'] = array(
        array("href" => asset_url()."imgs/cover/hemicycle-front-375.jpg", "as" => "image", "media" => "(max-width: 575.98px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front-768.jpg", "as" => "image", "media" => "(min-width: 576px) and (max-width: 970px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front.jpg", "as" => "image", "media" => "(min-width: 970.1px)"),
      );
      // Load Views
      $this->load->view('templates/header', $data);
      $this->load->view('departement/commune', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer');
    }

    // Pages deputes/(:depute)/votes
    public function votes_datan($input, $departement){
      // Query 1 = infos générales députés
      $input_depute = $input;
      $data['depute'] = $this->deputes_model->get_depute_individual($input, $departement);

      if (empty($data['depute'])) {
        show_404();
      }

      // Check if it is in legislature
      if (!in_array($data['depute']['legislature'], array(15))) {
        show_404();
      }

      $mpId = $data['depute']['mpId'];
      $nameLast = $data['depute']['nameLast'];
      $nameUrl = $input_depute;
      $data['active'] = $data['depute']['active'];
      $legislature = $data['depute']['legislature'];
      $groupe_id = $data['depute']['groupeId'];

      // Group color
      $data['depute']['couleurAssociee'] = $this->groupes_model->get_groupe_color(array($data['depute']['libelleAbrev'], $data['depute']['couleurAssociee']));

      // Commission parlementaire
      $data['commission_parlementaire'] = $this->deputes_model->get_commission_parlementaire($mpId);

      // Query - get active fields + votes by field + check the logos
      $data['fields'] = $this->fields_model->get_active_fields();
      foreach ($data['fields'] as $key => $field) {
        // Get votes by field
        $x[$field["slug"]] = $this->votes_model->get_votes_datan_depute_field($mpId, $field['slug'], 2);
        if (!empty($x[$field["slug"]])) {
          $data['fields_voted'][] = $field;
        }
        $x[$field["slug"]] = array_slice($x[$field["slug"]], 0, 2);
        foreach ($x[$field["slug"]] as $key2 => $value) {
          $x[$field["slug"]][$key2 ]['dateScrutinFRAbbrev'] = $this->functions_datan->abbrev_months($value['dateScrutinFR']);
        }
      }
      // Check the logos
      if ($data["fields_voted"]){
        foreach ($data["fields_voted"] as $key => $value) {
          if ($this->functions_datan->get_http_response_code(base_url().'/assets/imgs/fields/'.$value["slug"].'.svg') != "200"){
            $data['fields_voted'][$key]["logo"] = FALSE;
          } else {
            $data['fields_voted'][$key]["logo"] = TRUE;
          }
        }
      }
      $data['by_field'] = $x;

      // Query - gender
      $data['gender'] = $this->depute_edito->gender($data['depute']['civ']);

      // Historique du député
      $data['mandat_edito'] = $this->depute_edito->get_nbr_lettre($data['depute']['mandatesN']);

      // Other MPs from the same group
      $data['other_deputes'] = $this->deputes_model->get_other_deputes($groupe_id, $nameLast, $mpId, $data['active'], $legislature);
      // OTHER MPs from the same departement
      $data['other_deputes_dpt'] = $this->deputes_model->get_deputes_all($legislature, $data['active'], $departement);

      // Meta
      $data['url'] = $this->meta_model->get_url();
      $depute = $data['depute']['nameFirst'].' '.$data['depute']['nameLast'];
      $data['title_meta'] = $depute." - Votes | Datan";
      $data['description_meta'] = "Découvrez toutes les positions ".$data['gender']['du']." député".$data['gender']['e']." ".$depute." quand ".$data['gender']['pronom']." vote l'Assemblée nationale.";
      $data['title'] = $depute;
      $data['title_breadcrumb'] = mb_substr($data['depute']['nameFirst'], 0, 1).'. '.$data['depute']['nameLast'];
      // Breadcrumb
      $data['breadcrumb'] = array(
        array(
          "name" => "Datan", "url" => base_url(), "active" => FALSE
        ),
        array(
          "name" => "Députés", "url" => base_url()."deputes", "active" => FALSE
        ),
        array(
          "name" => $data['depute']['departementNom']." (".$data['depute']['departementCode'].")", "url" => base_url()."deputes/".$data['depute']['dptSlug'], "active" => FALSE
        ),
        array(
          "name" => $data['title_breadcrumb'], "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl, "active" => FALSE
        ),
        array(
          "name" => "Votes", "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl."/votes", "active" => TRUE
        )
      );
      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      // Open Graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // JS
      // CSS
      // Preloads
      $data['preloads'] = array(
        array("href" => asset_url()."imgs/cover/hemicycle-front-375.jpg", "as" => "image", "media" => "(max-width: 575.98px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front-768.jpg", "as" => "image", "media" => "(min-width: 576px) and (max-width: 970px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front.jpg", "as" => "image", "media" => "(min-width: 970.1px)"),
      );
      // Load views
      $this->load->view('templates/header', $data);
      $this->load->view('deputes/votes_datan', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer');
    }

    // Pages deputes/x/votes/field
    public function votes_datan_field($input, $departement, $field){
      // Query 1 = infos générales députés
      $input_depute = $input;
      $data['depute'] = $this->deputes_model->get_depute_individual($input, $departement);
      $mpId = $data['depute']['mpId'];

      if (empty($data['depute'])) {
        show_404();
      }

      // Check if it is in legislature
      if (!in_array($data['depute']['legislature'], array(15))) {
        show_404();
      }

      // Query - get active votes
      $data['votes'] = $this->votes_model->get_votes_datan_depute_field($mpId, $field, FALSE);

      if (empty($data['votes'])) {
        show_404();
      }

      // Change data of votes
      foreach ($data['votes'] as $key => $value) {
        $data['votes'][$key]['dateScrutinFRAbbrev'] = $this->functions_datan->abbrev_months($value['dateScrutinFR']);
      }

      // Variables
      $nameLast = $data['depute']['nameLast'];
      $nameUrl = $input_depute;
      $data['active'] = $data['depute']['active'];
      $legislature = $data['depute']['legislature'];

      // Query get info on field
      $data['field'] = $this->fields_model->get_field($field);
      $groupe_id = $data['depute']['groupeId'];

      // Group color
      $data['depute']['couleurAssociee'] = $this->groupes_model->get_groupe_color(array($data['depute']['libelleAbrev'], $data['depute']['couleurAssociee']));

      // Commission parlementaire
      $data['commission_parlementaire'] = $this->deputes_model->get_commission_parlementaire($mpId);

      // Query - gender
      $data['gender'] = $this->depute_edito->gender($data['depute']['civ']);

      // Historique du député
      $data['mandat_edito'] = $this->depute_edito->get_nbr_lettre($data['depute']['mandatesN']);

      // Other MPs from the same group
      $data['other_deputes'] = $this->deputes_model->get_other_deputes($groupe_id, $nameLast, $mpId, $data['active'], $legislature);
      // OTHER MPs from the same departement
      $data['other_deputes_dpt'] = $this->deputes_model->get_deputes_all($legislature, $data['active'], $departement);

      // Meta
      $data['url'] = $this->meta_model->get_url();
      $depute = $data['depute']['nameFirst'].' '.$data['depute']['nameLast'];
      $data['title_meta'] = $depute." - Votes ".mb_strtolower($data['field']['name'])." | Datan";
      $data['description_meta'] = "Découvrez toutes les positions ".$data['gender']['du']." député".$data['gender']['e']." ".$depute." concernant ".$data['field']['libelle'].".";
      $data['title'] = $depute;
      $data['title_breadcrumb'] = mb_substr($data['depute']['nameFirst'], 0, 1).'. '.$data['depute']['nameLast'];
      // Breadcrumb
      $data['breadcrumb'] = array(
        array(
          "name" => "Datan", "url" => base_url(), "active" => FALSE
        ),
        array(
          "name" => "Députés", "url" => base_url()."deputes", "active" => FALSE
        ),
        array(
          "name" => $data['depute']['departementNom']." (".$data['depute']['departementCode'].")", "url" => base_url()."deputes/".$data['depute']['dptSlug'], "active" => FALSE
        ),
        array(
          "name" => $data['title_breadcrumb'], "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl, "active" => FALSE
        ),
        array(
          "name" => "Votes", "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl."/votes", "active" => FALSE
        ),
        array(
          "name" => $data['field']['name'], "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl."/votes".NULL."/".$data['field']['slug'], "active" => TRUE
        )
      );
      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      // Open Graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // CSS
      // JS
      // Preloads
      $data['preloads'] = array(
        array("href" => asset_url()."imgs/cover/hemicycle-front-375.jpg", "as" => "image", "media" => "(max-width: 575.98px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front-768.jpg", "as" => "image", "media" => "(min-width: 576px) and (max-width: 970px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front.jpg", "as" => "image", "media" => "(min-width: 970.1px)"),
      );
      // Load views
      $this->load->view('templates/header', $data);
      $this->load->view('deputes/votes_datan_field', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer');
    }

    // Pages deputes/x/votes/all
    public function votes_all($input, $departement){
      // Query 1 = infos générales députés
      $input_depute = $input;
      $data['depute'] = $this->deputes_model->get_depute_individual($input, $departement);

      if (empty($data['depute'])) {
        show_404();
      }

      // Check if it is in legislature
      if (!in_array($data['depute']['legislature'], array(15))) {
        show_404();
      }

      $mpId = $data['depute']['mpId'];
      $nameLast = $data['depute']['nameLast'];
      $nameUrl = $input_depute;
      $data['active'] = $data['depute']['active'];
      $groupe_id = $data['depute']['groupeId'];

      // Group color
      $data['depute']['couleurAssociee'] = $this->groupes_model->get_groupe_color(array($data['depute']['libelleAbrev'], $data['depute']['couleurAssociee']));

      // Commission parlementaire
      $data['commission_parlementaire'] = $this->deputes_model->get_commission_parlementaire($mpId);

      // Query - get all votes
      $data['votes'] = $this->votes_model->get_votes_all_depute($mpId, legislature_current());

      // Query - gender
      $data['gender'] = $this->depute_edito->gender($data['depute']['civ']);

      // Historique du député
      $data['mandat_edito'] = $this->depute_edito->get_nbr_lettre($data['depute']['mandatesN']);

      // Meta
      $data['url'] = $this->meta_model->get_url();
      $depute = $data['depute']['nameFirst'].' '.$data['depute']['nameLast'];
      $data['title_meta'] = $depute." - Votes | Datan";
      $data['description_meta'] = "Retrouvez tous les votes ".$data['gender']['du']." député".$data['gender']['e']." ".$depute." à l'Assemblée nationale : sa participation, ses positions, sa loyauté envers son groupe parlementaire.";
      $data['title'] = $depute;
      $data['title_breadcrumb'] = mb_substr($data['depute']['nameFirst'], 0, 1).'. '.$data['depute']['nameLast'];
      // Breadcrumb
      $data['breadcrumb'] = array(
        array(
          "name" => "Datan", "url" => base_url(), "active" => FALSE
        ),
        array(
          "name" => "Députés", "url" => base_url()."deputes", "active" => FALSE
        ),
        array(
          "name" => $data['depute']['departementNom']." (".$data['depute']['departementCode'].")", "url" => base_url()."deputes/".$data['depute']['dptSlug'], "active" => FALSE
        ),
        array(
          "name" => $data['title_breadcrumb'], "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl, "active" => FALSE
        ),
        array(
          "name" => "Votes", "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl."/votes", "active" => FALSE
        ),
        array(
          "name" => "Tous les votes à l'Assemblée nationale", "url" => base_url()."deputes/".$data['depute']['dptSlug']."/depute_".$nameUrl."/votes/all", "active" => TRUE
        )
      );
      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      // Open Graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // CSS
      $data['css_to_load']= array(
        array(
          "url" => css_url()."datatables.bootstrap4.min.css",
          "async" => FALSE
        )
      );
      // JS
      $data['js_to_load']= array("moment.min", "datatable-datan.min", "datetime-moment");
      // Preloads
      $data['preloads'] = array(
        array("href" => asset_url()."imgs/cover/hemicycle-front-375.jpg", "as" => "image", "media" => "(max-width: 575.98px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front-768.jpg", "as" => "image", "media" => "(min-width: 576px) and (max-width: 970px)"),
        array("href" => asset_url()."imgs/cover/hemicycle-front.jpg", "as" => "image", "media" => "(min-width: 970.1px)"),
      );
      // Load views
      $this->load->view('templates/header', $data);
      $this->load->view('deputes/votes', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer');
    }

  }
?>
