<?php
  include('../bdd-connexion.php');

  $bdd->query('ALTER TABLE `deputes_all`
    ADD `job` VARCHAR(255) NULL DEFAULT NULL AFTER `age`,
    ADD `catSocPro` VARCHAR(255) NULL DEFAULT NULL AFTER `job`,
    ADD `famSocPro` VARCHAR(255) NULL DEFAULT NULL AFTER `catSocPro`;
    ');

    $bdd->query('ALTER TABLE `deputes`
      ADD `famSocPro` VARCHAR(255) NULL DEFAULT NULL AFTER `catSocPro`;
      ');
