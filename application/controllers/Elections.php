<?php
  class Elections extends CI_Controller{
    public function __construct() {
      parent::__construct();
      $this->load->model('elections_model');
      //$this->password_model->security_password(); Former login protection
    }

    public function index(){
      // Data
      $data['elections'] = $this->elections_model->get_election_all();
      $data['electionsColor'] = $this->elections_model->get_election_color();

      // Breadcrumb
      $data['breadcrumb'] = array(
        array(
          "name" => "Datan", "url" => base_url(), "active" => FALSE
        ),
        array(
          "name" => "Élections", "url" => base_url()."elections", "active" => TRUE
        )
      );

      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      // Meta
      $data['url'] = $this->meta_model->get_url();
      $data['title_meta'] = "Les élections en France - Candidats et résultats | Datan";
      $data['description_meta'] = "Retrouvez toutes les informations sur les différentes élections en France (présidentielle, législative, régionales). Découvrez les députés candidats et les résultats.";
      $data['title'] = "Les élections politiques en France";
      //Open Graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // Load Views
      $this->load->view('templates/header', $data);
      $this->load->view('elections/index', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer', $data);
    }

    public function individual($slug){
      $data['election'] = $this->elections_model->get_election($slug);

      if (empty($data['election'])) {
        show_404();
      }

      // STATE
      $data['state'] = 0;
      if ($data['election']['id'] == '1') {
        $data['state'] = 2;
      }

      // Data
      $data['deputes'] = $this->elections_model->get_all_candidate($data['election']['id'], TRUE);
      $data['districts'] = $this->elections_model->get_all_districts($data['election']['id']);
      $data['electionInfos'] = $this->elections_model->get_election_infos($data['election']['libelleAbrev']);
      $data['candidatsN'] = $this->elections_model->count_candidats($data['election']['id'], FALSE, FALSE);
      $data['candidatsN_second'] = $this->elections_model->count_candidats($data['election']['id'], TRUE, FALSE);
      $data['candidatsN_elected'] = $this->elections_model->count_candidats($data['election']['id'], TRUE, TRUE);
      $data['mapLegend'] = $this->elections_model->get_map_legend($data['election']['id']);
      $data['today'] = date("Y-m-d");

      // badgeCenter
      foreach ($data['deputes'] as $key => $value) {
        $state = $this->elections_model->get_state($value['secondRound'], $value['elected']);
        $data['deputes'][$key]['electionState'] = $state;
        if ($state == 'lost') {
          if ($value["civ"] == "Mme") {
            $data['deputes'][$key]['badgeCenter'] = "Éliminée";
          } else {
            $data['deputes'][$key]['badgeCenter'] = "Éliminé";
          }
          $data['deputes'][$key]['badgeCenterColor'] = "badge-danger";
        }
        if ($state == 'second') {
          $data['deputes'][$key]['badgeCenter'] = "Second tour";
          $data['deputes'][$key]['badgeCenterColor'] = "badge-secondary";
        }
        if ($state == 'elected') {
          $data['deputes'][$key]['badgeCenterColor'] = "badge-primary";
          if ($value["civ"] == "Mme") {
            $data['deputes'][$key]['badgeCenter'] = "Élue";
          } else {
            $data['deputes'][$key]['badgeCenter'] = "Élu";
          }
        }
      }

      // Breadcrumb
      $data['breadcrumb'] = array(
        array(
          "name" => "Datan", "url" => base_url(), "active" => FALSE
        ),
        array(
          "name" => "Élections", "url" => base_url()."elections", "active" => FALSE
        ),
        array(
          "name" => $data['election']['libelleAbrev'] . " " . $data['election']['dateYear'], "url" => base_url()."elections/" . $data['election']['slug'], "active" => TRUE
        )
      );

      $data['breadcrumb_json'] = $this->breadcrumb_model->breadcrumb_json($data['breadcrumb']);
      // Meta
      $data['url'] = $this->meta_model->get_url();
      $data['title_meta'] = "Les " . mb_strtolower($data['election']['libelle']) . " de " . $data['election']['dateYear'] . " - Candidats et résultats | Datan";
      $data['description_meta'] = "Retrouvez toutes les informations sur les " . mb_strtolower($data['election']['libelle']) . " de " . $data['election']['dateYear'] . " en France. Découvrez les députés candidats et les résultats.";
      $data['title'] = "Les " . mb_strtolower($data['election']['libelle']) . " de " . $data['election']['dateYear'];
      //Open Graph
      $controller = $this->router->fetch_class()."/".$this->router->fetch_method();
      $data['ogp'] = $this->meta_model->get_ogp($controller, $data['title_meta'], $data['description_meta'], $data['url'], $data);
      // CSS
      $data['css_to_load']= array(
        array(
          "url" => asset_url() . "css/jquery-jvectormap-2.0.5.css",
          "async" => FALSE
        )
      );
      // JS
      $data['js_to_load_before_datan'] = array("isotope.pkgd.min");
      if (count($data['districts']) <= 25) {
        $data['js_to_load'] = array("datan/sorting_elections");
      } else {
        $data['js_to_load'] = array("datan/sorting_select");
      }
      array_push($data['js_to_load'], "jvectormap/jquery-jvectormap-2.0.5.min");
      if ($data['election']['libelleAbrev'] == "Régionales") {
        array_push($data['js_to_load'], "jvectormap/jquery-jvectormap-fr_regions_2016-merc");
      } elseif ($data['election']['libelleAbrev'] == "Départementales") {
        array_push($data['js_to_load'], "jvectormap/jquery-jvectormap-fr-merc");
      }
      array_push($data['js_to_load'], "jvectormap/maps_jvectormap-datan");
      // Load Views
      $this->load->view('templates/header', $data);
      $this->load->view('templates/button_up');
      $this->load->view('elections/candidats', $data);
      $this->load->view('templates/breadcrumb', $data);
      $this->load->view('templates/footer', $data);
    }
  }
?>
