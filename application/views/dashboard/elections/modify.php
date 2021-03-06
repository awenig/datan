<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark"><?= $title ?></h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <?php if (!empty(validation_errors())): ?>
            <p class="bg-danger">Certains champs n'ont pas été renseignés. Voir ci-dessous.</p>
            <?= validation_errors(); ?>
          <?php endif; ?>
          <div class="card">
            <div class="card-body">
              <?php
                echo form_open_multipart('admin/elections/candidat/modify/'.$candidat['mpId'].'?election='.$election['slug']);
              ?>
              <div class="form-group">
                <label>Député</label>
                <input type="text" class="form-control" autocomplete="off" readonly value="<?= $candidat['nameFirst'] .' ' . $candidat['nameLast'] . ' ('. $candidat['mpId'] . ')' ?>">
                <input name="mpId" type="hidden" class="form-control" autocomplete="off" readonly value="<?= $candidat['mpId']?>">
              </div>
              <div class="form-group">
                <label>Election</label>
                <input name="election" class="form-control" type="text" readonly value="<?= $election['id'] ?>"></input>
              </div>
              <div class="form-group">
                <label for="">Région de candidature</label>
                <select class="form-control" name="district">
                  <option value="<?= $candidat['districtId'] ?>" selected="selected">Selectionné : <?= $candidat['districtLibelle'] ?></option>
                  <?php foreach ($districts as $district): ?>
                    <?php if ($district['libelle'] !== $candidat['regionLibelle']): ?>
                      <option value="<?= $district['id'] ?>"><?= $district['libelle'] ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="">Position (tête de liste ou colistier)</label>
                <select class="form-control" name="position">
                  <option value="<?= $candidat['position'] ?>" selected="selected">Selectionné : <?= $candidat['position'] ?></option>
                  <?php foreach ($positions as $position): ?>
                    <?php if ($position !== $candidat['position']): ?>
                      <option value="<?= $position ?>"><?= $position ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Nuance (ne pas remplir)</label>
                <input name="nuance" class="form-control" type="text" placeholder="Ne pas remplir pour le moment" readonly>
              </div>
              <div class="form-group">
                <label>Source</label>
                <input name="source" class="form-control" type="text" value="<?= $candidat['source']?>" placeholder="ex:  https://www.ouest-france.fr/elections/regionales/elections-regionales-en-pays-de-la-loire-qui-seront-les-candidats-en-juin-2021-7190091"></input>
              </div>
              <div class="form-group p-3" style="background-color: rgba(0, 183, 148, 0.3)">
                <label>Se maintient au 2nd tour ?</label>
                <select class="form-control" name="secondRound">
                  <option value="99" <?= $candidat['secondRound'] === NULL ? " selected='selected'" : NULL ?>>Ne sait pas</option>
                  <option value="1" <?= $candidat['secondRound'] === "1" ? " selected='selected'" : NULL ?>>Oui</option>
                  <option value="0" <?= $candidat['secondRound'] === "0" ? " selected='selected'" : NULL ?>>Non</option>
                </select>
              </div>
              <div class="form-group p-3" style="background-color: rgba(0, 183, 148, 0.3)">
                <label>Elu ?</label>
                <select class="form-control" name="elected">
                  <option value="99" <?= $candidat['elected'] == NULL ? " selected='selected'" : NULL ?>>Ne sait pas</option>
                  <option value="1" <?= $candidat['elected'] === "1" ? " selected='selected'" : NULL ?>>Oui</option>
                  <option value="0" <?= $candidat['elected'] === "0" ? " selected='selected'" : NULL ?>>Non</option>
                </select>
              </div>
              <div class="form-group p-3" style="background-color: rgba(0, 183, 148, 0.3)">
                <label>Visible</label>
                <input name="visible" class="form-control" type="checkbox" <?= $candidat['visible'] ? 'checked': ''?> value="true"></input>
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
  <div class="p-3">
    <h5>Title</h5>
    <p>Sidebar content</p>
  </div>
</aside>
<!-- /.control-sidebar -->
